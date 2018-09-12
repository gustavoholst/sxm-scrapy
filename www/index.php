<!DOCTYPE HTML>


<html>
  <Title>SXM Analysis</Title>
<body>
  <h3>SXM Analysis Dashboard</h3>
  <?php
    #Set variables
    $logDir="D:/Documents/Websites/SXM/sxm-scrapy/sxm/channel_logs/";
    $sortType = array('Recently Played','Top','Newly Added','Rising');
    $sortDate = array('Week','Hour','Day','Month','Year','All'); #TODO replace these date filters to just have TOP:DATE since top is the only one that cares
    ##TODO: Ascending/Descending
    #Create array of log files from directory
    $logFiles = array_filter(scandir($logDir), function($item) {
      return !is_dir($logDir . $item);
    });
  ?>

  <form name="sortForm" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">

    <!--Create list from available channel data-->
    Select a channel: 
    <select name="channel">
      <?php
        fill_select('channel',$logFiles)
      ?>
    </select>
    <br><br>
    <!--Select sorting type and range-->
    Sorting:
    <select name="sorttype">
      <?php
        fill_select('sorttype',$sortType)
      ?>
    </select>
    <select name="sortdate">
      <?php
        fill_select('sortdate',$sortDate)
      ?>
    </select>

    <!--Input number of entries to display from 1-100-->
    <br><br>Number of entries to display: <input type="number" name="quantity" min="1" max="500" size="4" value="<?php echo isset($_POST['quantity']) ? $_POST['quantity'] : '10' ?>">

  	<br><br><input type="submit" value="Get Data">
  </form>

  <?php
    if (isset($_POST['channel'])) 
    {
      $channel = $_POST['channel'];
      $logAll = read_log($logDir);
      $logTrim = time_filter($logAll);
      $logVal = item_filter($logTrim);
      $logCount = count_plays($logVal);
      $logSort = sort_log($logCount);
      echo $_POST['channel']." has played ".sizeof($logVal)." songs this ".strtolower($_POST['sortdate']).".<br>";
      create_table($logSort);
    }
  ?>

  <!--Fill site dropdowns based on listed items-->
  <?php
    function fill_select($name, $items)
    {
      foreach ($items as $value) 
      {
        if ($name === 'channel')
        {
          $value = explode("_", $value)[0];
        }
        if ($value === $_POST[$name]) 
        {
            echo "<option selected='selected' value='$value'>$value</option>";
        }
        else
        {
          echo "<option value='$value'>$value</option>";
        }
      }
    }
  ?>

  <!--Read log file and echo statistics-->
  <?php
    function read_log($logDir)
    {
      //echo "You chose: ". $_POST['channel']. "<br>";
      $logArray = array();
      $filePath = $logDir.$_POST['channel']."_log.log";
      echo "File read from: $filePath <br>";
      if (($handle = fopen($filePath, "r")) !== FALSE) 
      {
          $headers = fgetcsv($handle, 1000, ",");
          while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
          {
              $logArray[]=array_combine($headers, $data);
          }
          echo "There are ".sizeof($logArray)." entries in the ".$_POST['channel']." log.<br>";
          fclose($handle);
      }
      return $logArray;
    }
  ?>

  <!--Function to sort dataset-->
  <?php
    function array_orderby()
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) 
        {
            if (is_string($field)) 
            {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }
  ?>

  <!--Time Filtering-->
  <?php
    function time_filter($log)
    {
      $tmin = 0.0;
      switch ($_POST['sortdate']) {
        case 'Hour':
          $tmin = time() - (60 * 60);
          break;
        case 'Day':
          $tmin = time() - (24 * 60 * 60);
          break;
        case 'Week':
          $tmin = time() - (7* 24 * 60 * 60);
          break;                  
        case 'Month':
          $tmin = time() - (30.4* 24 * 60 * 60);
          break;
        case 'Year':
          $tmin = time() - (365* 24 * 60 * 60);
          break;
        case 'All':
          $tmin = 0; //should actually have this set a thing that makes it skip the filtering step
          break;
        default:
          $tmin = -1; //should actually have this set a thing that makes it skip the filtering step
          break;
      }
      if ($tmin > 0) //checks if data needs to be filtered by timedate and does so if necessary, otherwise $logArray remains untouched
      {
        $logFiltered = array_filter($log, function ($e) use ($tmin) {return floatval($e['time']) > $tmin;});
      }
      else
      {
        $logFiltered = $log;
      }
      return $logFiltered;
    }
  ?>

  <!--Item Filtering (channel tags etc that get mistaken for songs)-->
  <?php
    function item_filter($log)
    {
      $excludeItems = array('#','@','call now to geT on the air','877-MY-HITS-1','fb.com/altnation','Wednesday DL',"Today's Dance Hits+Remixes","alt nation's",'siriusxm.com/hits1',"Today's Biggest",'Dance Hits+Remixes','WeekendCountdown','Advanced Placement');
      $logFiltered = array_filter($log, function ($e) use ($excludeItems) {return (count(array_filter($excludeItems, function ($el) use ($e) {return (strpos($e['title'], $el) !== false);})) == 0 && count(array_filter($excludeItems,function ($el2) use ($e) {return (strpos($e['artist'], $el2) !== false);})) == 0);});
      return $logFiltered;
    }
  ?>


  <!--COUNT PLAYS-->
  <!--Adds count column to data log for all songs, does not remove duplicates-->
  <?php
    function count_plays($log)
    {
      $count_array = [];
      foreach ($log as $value)
      {
        $count_array[$value['']] =  $value['artist'].'---'.$value['title'];
      }
      $counted = array_count_values($count_array);
      foreach ($log as &$song)
      {
        foreach ($counted as $key => $count) 
        {
          if ($song['artist'].'---'.$song['title'] === $key) 
          {
            $song['count'] = $count;
          }
        }
      }
      return $log;
      //print_r(array_orderby($log,'count', SORT_DESC));
    }
  ?>


  <!--DATA ANLYSIS-->
  <?php
    function sort_log($log) #TODO: move time_filter and item_filter to in here
    {
      if ($_POST['sorttype'] === 'Recently Played') 
      {
        return array_orderby($log, 'time', SORT_DESC);
      }

      elseif ($_POST['sorttype'] === 'Newly Added') 
      {
        
      }
      elseif ($_POST['sorttype'] === 'Rising') 
      {
        #TODO time_filter here x times for each of x days and then compare counts over time for each song
      }
      elseif ($_POST['sorttype'] === 'Top') 
      {
        return array_orderby($log, 'count', SORT_DESC);
      }
    }
  ?>


  <?php
    function select_table_rows($log)
    {
      //Set number of rows for table based on inputbox or length of log
      if ($_POST['quantity'] > sizeof($log)) 
      {
        $nrows = sizeof($log);
      }
      else 
      {
        $nrows = $_POST['quantity']+1;
      }
      return array($nrows, array_slice($log, 0, $nrows-1));
    }
  ?>


  <!--Create table based on sorting/filtering-->
  <?php
    function create_table($log)
    {

      //Set column order for table and excludes for table
      $colOrder = array('title','artist','time','count');
      $excludeCols = array('channel','','albumart');

      //Select data for table
      list($nrows, $sliced) = select_table_rows($log);
      
      //Create table
      echo '<center><table>';
      
      echo "\n  <tr>";
      
      //create header row
      foreach($colOrder as $val) 
      {
        echo "\n    <th>".$val."</th>";
      }
      echo "\n  </tr>";

      //create data rows
      for ($i=0; $i <= $nrows; $i++) 
      {
        $orderedRow = array_replace(array_flip($colOrder), $sliced[$i]);
        echo "\n  <tr>";
        foreach ($orderedRow as $key => $value) 
        {
          if (!in_array($key, $excludeCols)) 
          {
            echo "\n    <td>";
            switch ($key)
            {
              case "albumart":
                echo '<a href="'.$value.'"><img src="'.$value.'" alt="Album Art" style="width:60px;height:60px;border:0;"></a>'; 
              case "time":
                echo date("D j M Y",$value)."<br>".date("G:i:s",$value);
                break;
              default:
                echo $value;
              }          
            echo "</td>";
          }
        } 
        echo "\n  </tr>";
      }
      echo "</table></center>";
    }
  ?>
  
  <!--Print date of last update to webpage-->
  <footer>
    <?php
      // outputs e.g. 'Last modified: March 04 1998 20:43:59.'
      echo "Site last modified: " . date ("d F Y H:i:s.", filemtime('D:/Documents/Websites/SXM/sxm-scrapy/www/index.php'));
    ?>
  </footer>

  <br>
</body>
</html>