import os
import pandas


def read_log():
	log_path = os.path.dirname(os.path.realpath(__file__)) + '\\channel_logs\\'
	log_keys = ['artist','title','albumart','time']
	song_only = {key: song[key] for key in log_keys}
	if os.path.isfile(log_path):
		log = pandas.read_csv(log_path,index_col=0, encoding = 'utf-8')
		print(song['channel'], 'table loaded from', log_path)
		nrows = len(log.index)
		prev_songs = []
		for i in range(max(-3,-1*nrows),0):
			if log.iloc[nrows+i]['title'] == song['title']:
				match = 1
				break
			else:
				match = 0
		if match == 1:
			print('Song already logged!')
		else:
			log = log.append(song_only,ignore_index = True)
			print('Song Added!')
			log.to_csv(log_path, encoding = 'utf-8')
	else:
		log = pandas.DataFrame(data=None, columns = log_keys, index = None)
		log = log.append(song_only,ignore_index = True)
		log.to_csv(log_path, encoding = 'utf-8')
		print(song['channel'], 'table created @', log_path)

def get_recent(channel, n):
	chan_df = df.loc[df['channel'] == channel]
	
def main():
	"""Execuse scraping activities

	Crawl using srapy spider, read results, write to channel logs
	"""
	update_log(song)

if __name__ == '__main__':
    main()

