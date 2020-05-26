import socket
import socks
import pytube
import time

from urllib.error import HTTPError, URLError

ticks = time.time()

socks.set_default_proxy(socks.SOCKS5, "127.0.0.1", 10808)
socket.socket = socks.socksocket

#video_url = 'https://www.youtube.com/watch?v=gMoE5IdHXkw'
video_url = 'https://www.youtube.com/watch?v=KNc4LVdIwJ8'

try:
    youtube = pytube.YouTube(video_url)
    #video = youtube.streams.all()
    #video = youtube.streams.filter(progressive=True).filter(subtype='mp4', res='720p').first()
    video = youtube.streams.get_by_itag(22)
    #video.download('/home')
    print(video)
except HTTPError:
    print(11)
except URLError:
    print(22)
except KeyError:
    print(333)

new_ticks = time.time()
tips_time = new_ticks-ticks
print('用时: %d s' % tips_time)

'''
ticks = time.time()
print('当前时间戳： ', ticks)

new_ticks = str(ticks).replace('.', '')   #替换 . 字符为空格
print(new_ticks)
'''
