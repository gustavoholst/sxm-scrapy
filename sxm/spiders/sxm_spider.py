import scrapy
import json
import time


class QuotesSpider(scrapy.Spider):
    name = "sxm"
    
    sites=[]
    channels = [
        "thebeat",
        "area33",
        "siriushits1"
    ]
    t = time.time()-10
    timestamp=time.strftime("%m-%d-%H:%M:%S", time.gmtime(t))
    for channel in channels:
        sites.append('http://www.siriusxm.com/metadata/pdt/en-us/json/channels/'+channel+'/timestamp/'+timestamp)
    
    start_urls = sites

    def parse(self, response):
        jsonresponse = json.loads(response.body_as_unicode())
        albumart = []
        artist = jsonresponse['channelMetadataResponse']['metaData']["currentEvent"]['artists']['name']
        #print(jsonresponse['channelMetadataResponse']['metaData']['currentEvent']['epgInfo']['program']['episodes']['title']) ##Program 
        title = jsonresponse['channelMetadataResponse']['metaData']['currentEvent']['song']['name']
        channel = jsonresponse['channelMetadataResponse']['metaData']['channelName']
        
        baseurl = jsonresponse['channelMetadataResponse']['metaData']['currentEvent']['baseUrl']
        for item in jsonresponse['channelMetadataResponse']['metaData']['currentEvent']['song']['creativeArts']:
            if item['encrypted'] == False and item['type'] == 'IMAGE' and not item['url'] == '':
                albumart.append(baseurl + item['url'])
        
        yield {
            'artist':artist,
            'title':title,
            'albumart':albumart[len(albumart)-1],
            'channel':channel,
            'time':time.time()
        }