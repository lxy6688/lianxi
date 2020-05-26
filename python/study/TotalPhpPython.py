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

youtube = pytube.YouTube(video_url)
video = youtube.streams.all()
print(video)