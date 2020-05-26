import socket
import socks
import pytube
import time
import sys
import os
from urllib.error import HTTPError, URLError

socks.set_default_proxy(socks.SOCKS5, "127.0.0.1", 10808)
socket.socket = socks.socksocket


video_url = sys.argv[1]
itag = sys.argv[2]

i = 0
prefix = None
path = os.getcwd() + '/'
i = 0
j = 0
while True:
    try:
        youtube = pytube.YouTube(video_url)
        video = youtube.streams.get_by_itag(itag)
        if None == video:
            print('None')
            exit()
        if prefix == None:
            video.download(path)
        else:
            video.download(path, filename_prefix=prefix)
    except HTTPError:
        if i == 4:
            print('Httperror')
        time.sleep(2)
        i = i+1
        a = i+1
        print("Httperror错误,      尝试第%s次连接......" % a)
        continue
    except URLError:
        if j == 4:
            print('Urlerror')
        time.sleep(2)
        j = j+1
        b = j+1
        print("Urlerror错误,      尝试第%d次连接......" % b)
        continue
    except KeyError:
        print('Invalid')

title = video.title
file = video.default_filename
response = (True,title,file)
print(response)
