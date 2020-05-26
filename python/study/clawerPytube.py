'''
参考： https://blog.csdn.net/fjx1173865548/article/details/45666015   利用Python3爬取YouTube上的视频播放地址

'''

import re
#import urllib
import requests

def getHtml(url):
    #page = urllib.urlopen(url)
    page = requests.get(url)
    html = page.read()
    return html


def getUrl(html):
    reg = r"(?<=a\shref=\"/watch).+?(?=\")"
    urlre = re.compile(reg)
    urllist = re.findall(urlre, html)
    format = "https://www.youtube.com/watch%s\n"
    f = open("/root/output.txt", 'a')
    for url in urllist:
        result = (format % url)
        f.write(result)
    f.close()

pages = 10
for i in range(1, pages):
    html = getHtml("https://www.youtube.com/results?search_query=lion+king&lclk=short&filters=short&page=%s" % i)
    print
    getUrl(html)
    i += 1