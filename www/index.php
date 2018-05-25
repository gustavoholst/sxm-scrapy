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

    <?php
      #Create check boxes for each of the files listed in $logFiles
      foreach ($logFiles as $filename){
          echo "<input type=\"checkbox\" name=\"logfiles\" value=\"$filename\" /> $filename <br>";
      }
    ?>

    <select name="sorttype">
      <option value="top">Top</option>
      <option value="new">New</option>
      <option value="rising">Rising</option>
    </select>
    <select name="sortdate">
      <option value="hour">Hour</option>
      <option value="day">Day</option>
      <option value="week">Week</option>
      <option value="month">Month</option>
      <option value="year">Year</option>
      <option value="all">All</option>
    </select>

  	<br><input type="submit" value="Get Data">
  </form>
</body>
</html>