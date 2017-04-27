from scrapy.crawler import CrawlerProcess
from scrapy.utils.project import get_project_settings
from settings import FEED_URI

import os
import ast
import pandas


def run_spider():
	"""Run spider for scraping sxm website

	Results of scrape stored to json file (see settings.py)
	"""
	process = CrawlerProcess(get_project_settings())
	process.crawl('sxm')
	process.start()	


def get_now_playing():
	"""Read json file generated by spider and add lines to channel logs"""
	with open(FEED_URI[7:],'r') as f:
		now_playing = ast.literal_eval(f.read())
	print("NOW PLAYING:")
	for song in now_playing:
		print(song['channel']+":",song['artist'],'-',song['title'])
	return now_playing


def clear_json():
	"""Delete json file from previous crawl

	spider needs an empty json file to write to
	"""
	try:
		os.remove(FEED_URI[7:])
	except OSError:
		pass

def read_logs(now_playing):
	for channel in now_playing:
		pandas.

    test_path = Path(path)
    
    if test_path.is_file():
        history = pd.read_csv(path,index_col=0,parse_dates=["First Played", "Last Played"], dtype={"Total Plays": int})
        print(channel, "table loaded from", path)
        return history;
    else:
        history = pd.DataFrame(data=None, columns = ["Artist", "Title", "Album Art URL", "First Played", "Last Played", "Total Plays"], index = None)
        history.to_csv(path)
        print("Blank", channel, "table created @", path)
        return history;

def main():
	"""Execuse scraping activities

	Crawl using srapy spider, read results, write to channel logs
	"""
	clear_json()
	run_spider()
	now_playing = get_now_playing()

if __name__ == "__main__":
    main()

