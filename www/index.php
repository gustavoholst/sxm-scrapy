<!DOCTYPE HTML>


<html>
  <Title>SXM Analysis</Title>
<body>
  <h3>SXM Analysis Dashboard</h3>
  <?php
    #Set variables
    $logDir="D:/Documents/Websites/SXM/sxm-scrapy/sxm/channel_logs/";
    $sortType = array('Recent','Top','Newly Added','Rising');
    $sortDate = array('Week','Hour','Day','Month','Year','All');

    #Create array of log files from directory
    $logFiles = array_filter(scandir($logDir), function($item) {
      return !is_dir($logDir . $item);
    });
  ?>

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
    <br><br>Number of entries to display: <input type="number" name="quantity" min="1" max="100" size="4" value="<?php echo isset($_POST['quantity']) ? $_POST['quantity'] : '10' ?>">

  	<br><br><input type="submit" value="Get Data">
  </form>

  <!--Read log file and echo statistics-->
  <?php
    if (isset($_POST['channel']))
    {
      echo "You chose: ". $_POST['channel']. "<br>";
      $logArray = array();
      $filePath = $logDir.$_POST['channel']."_log.log";
      if (($handle = fopen($filePath, "r")) !== FALSE) 
      {
          $headers = fgetcsv($handle, 1000, ",");
          while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) 
          {
              $logArray[]=array_combine($headers, $data);
          }
          echo "There are ".sizeof($logArray)." log entries for ".$_POST['channel'].".<br>";
          echo "<br> File read from: $filePath <br>";
          fclose($handle);
      }
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
    if (isset($_POST['channel']))
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
        $logFiltered = array_filter($logArray, function ($e) use ($tmin) {return floatval($e['time']) > $tmin;});
        print_r($logFiltered);
      }
    }  
  ?>

  <!--DATA ANLYSIS-->
  <?php
    if (isset($_POST['channel']))
    {
      $now = time();

      if ($_POST['sorttype'] === 'Top') 
      {
        $counted = array_count_values (array_column($logArray, 'title'));
        arsort($counted);
        print_r($counted);
        #TODO change this so that it serializes the data first, preserving only song metadata, then count those values, append count as a column, and remove duplicates, then sort and display
      }
    }
  ?>

  <!--Create table based on sorting/filtering-->
  <?php
    if (isset($_POST['channel']))
    {
      if ($_POST['quantity'] > sizeof($logArray)) 
      {
        $nrows = sizeof($logArray);
      }
      else 
      {
        $nrows = $_POST['quantity']+1;
      }

      $colOrder = array('albumart','title','artist','time');
      $exclude_list = array('channel','');

      //Sort and filter data
      $sorted = array_orderby($logArray, 'time', SORT_DESC);
      $sliced = array_slice($sorted, 0, $nrows);

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
      for ($i=1; $i <= $nrows; $i++) 
      {
        $orderedRow = array_replace(array_flip($colOrder), $sliced[$i]);
        echo "\n  <tr>";
        foreach ($orderedRow as $key => $value) 
        {
          if (!in_array($key, $exclude_list)) 
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
      echo "Last modified: " . date ("d F Y H:i:s.", filemtime('D:/Documents/Websites/SXM/sxm-scrapy/www/index.php'));
    ?>
  </footer>

  <br>
</body>
</html>