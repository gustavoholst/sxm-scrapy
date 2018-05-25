#!/usr/bin/env python3

import time
from os import walk

htmlFormat = """
<html>
  <Title>SXM Analysis</Title>
<body>
  <p>Select from the following channels:</p>
  <form name="userchoice" method="GET">
  	{chanListHTML}
  	<br>
  	{sortTypeHTML}
  	{sortDateHTML}
  	<br>
  	<input type="submit" value="Get Data">
  </form
</body>
</html> """


def genChanList():
	logPath="D:/Documents/Websites/SXM/sxm-scrapy/sxm/channel_logs"
	_, _, filenames = next(walk(logPath), (None, None, [])) # obtains list of log files
	chanListHTML=""
	for i,val in enumerate(filenames):
		i = str(i)
		chanListHTML += '<input checked type="checkbox" name="chanList" value="ch' + i + '" id="ch' + i + '"> <label for="ch' + i + '"> ' + val.split("_")[0] + '</label><br>'
	chanListHTML = "" #Flag to disable individual channel selection
	return filenames, chanListHTML

def genSorting():
	sortType = ['Top','New','Rising']
	sortDate = ['Hour','Day','Week','Month','Year','All']
	
	sortTypeHTML = '<select name="sorttype">'
	for i,val in enumerate(sortType):
		i=str(i)
		sortTypeHTML += '<option value="' + val + '">' + val + '</option>'
	sortTypeHTML += '</select>'

	sortDateHTML = '<select name="sortdate">'
	for j,val2 in enumerate(sortDate):
		j=str(j)
		if val2=="Week":
			sortDateHTML += '<option selected value="' + val2 + '">' + val2 + '</option>'
		else:
			sortDateHTML += '<option value="' + val2 + '">' + val2 + '</option>'
	sortDateHTML += '</select>'

	return sortTypeHTML,sortDateHTML


def getData():
	

	return

def main():
	print("Content-Type: text/html\n\n")  # html markup follows
	chanList, chanListHTML = genChanList()
	sortTypeHTML,sortDateHTML = genSorting()
	print(htmlFormat.format(**locals())) # see embedded %s ^ above

main()