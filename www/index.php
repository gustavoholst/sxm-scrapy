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

  <form name="sortForm" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">

    <!--Create list from available channel data-->
    Select a channel: 
    <select name="channel">
    <?php
      #Create check boxes for each of the files listed in $logFiles
      foreach ($logFiles as $filename)
      {
        $channel = explode("_", $filename)[0];
        echo "<option value=\"$channel\">$channel</option>";
        //"<input type=\"checkbox\" name=\"logfiles\" value=\"$channel\" /> $channel <br>";
      }
    ?>
    </select>
    <br><br>
    <!--Select sorting type and range-->
    Sorting:
    <select name="sorttype">
      <?php
        foreach ($sortType as $value) {
          echo '<option value="'.$value.'">'.$value.'</option>';
        }
      ?>
    </select>
    <select name="sortdate">
      <?php
        foreach ($sortDate as $value) {
          echo '<option value="'.$value.'">'.$value.'</option>';
        }
      ?>
    </select>

    <!--Input number of entries to display from 1-100-->
    <br><br>Number of entries to display: <input type="number" name="quantity" min="1" max="100" size="4" value="10">

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
              //array_push($logArray, $data);
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
        $nrows = $_POST['quantity'];
      }

      $order = array('albumart','title','artist','time','channel','');

      //Sort and filter data
      $sorted = array_orderby($logArray, '', SORT_DESC);
      $sliced = array_slice($sorted, 0, $nrows);

      //Create table
      echo '<center><table style="width:80%">';
      
      echo "\n  <tr>";
      
      foreach($order as $val) 
      {
        echo "\n    <th>".$val."</th>";
      }
      echo "\n  </tr>";

      for ($i=1; $i < $nrows; $i++) 
      {
        $orderedRow = array_replace(array_flip($order), $sliced[$i]);
        echo "\n  <tr>";
        foreach ($orderedRow as $key => $value) 
        {
          echo "\n    <td>";
          if ($key === "albumart") 
          {
            echo '<a href="'.$value.'"><img src="'.$value.'" alt="Album Art" style="width:60px;height:60px;border:0;"></a>'; 
          }
          elseif ($key === "time") 
          {
            echo date("D j M Y",$value)."<br>".date("G:i:s",$value);
          }
          else 
          {
            echo $value;
          }          
          echo "</td>";
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