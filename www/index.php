<!DOCTYPE HTML>


<html>
  <Title>SXM Analysis</Title>
<body>
  <p>Select from the following channels:</p>
  <?php
    #Set variables
    $logDir="D:/Documents/Websites/SXM/sxm-scrapy/sxm/channel_logs/";
    $sortType = array('Top','New','Rising');
    $sortDate = array('Hour','Day','Week','Month','Year','All');

    #Create array of log files from directory
    $logFiles = array_filter(scandir($logDir), function($item) {
      return !is_dir($logDir . $item);
    });
  ?>

  <form name="sortForm" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>" method="post">

    <!--Create list from available channel data-->
    <select name="channel">
    <?php
      #Create check boxes for each of the files listed in $logFiles
      foreach ($logFiles as $filename){
        $channel = explode("_", $filename)[0];
        echo "<option value=\"$channel\">$channel</option>";
        //"<input type=\"checkbox\" name=\"logfiles\" value=\"$channel\" /> $channel <br>";
      }
    ?>
    </select>

    <select name="sorttype">
      <option value="top">Top</option>
      <option value="new">New</option>
      <option value="rising">Rising</option>
    </select>
    <select name="sortdate">
      <option value="hour">Hour</option>
      <option value="day">Day</option>
      <option selected value="week">Week</option>
      <option value="month">Month</option>
      <option value="year">Year</option>
      <option value="all">All</option>
    </select>

  	<br><input type="submit" value="Get Data">
  </form>

  <?php
    if (isset($_POST['channel'])){
      echo "You chose: ". $_POST['channel']. "<br>";
      $row = 1;
      $filePath = $logDir.$_POST['channel']."_log.log";
      if (($handle = fopen($filePath, "r")) !== FALSE) {
          while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
              $num = count($data);
              //echo "<p> $num fields in line $row: <br /></p>\n";
              $row++;
              //for ($c=0; $c < $num; $c++) {
                  //echo $data[$c] . "<br />\n";
              //}
          }
          echo "$filePath <br>";
          fclose($handle);
      }
    }
  ?>

  <!--Print date of last update to webpage-->
  <br>
  <br>
  <footer>
    <center>
    <?php
      // outputs e.g. 'Last modified: March 04 1998 20:43:59.'
      echo "Last modified: " . date ("d F Y H:i:s.", filemtime('D:/Documents/Websites/SXM/sxm-scrapy/www/index.php'));
    ?>
    </center>
  </footer>
  <br>


  



</body>
</html>