import socket
import socks
import pytube
import time
import os
from urllib.error import HTTPError, URLError

def load_detail(video_url, itag, prefix = None):
    i = 0
    j = 0
    while True:
        try:
            youtube = pytube.YouTube(video_url)
            video = youtube.streams.get_by_itag(itag)
            if None == video:
                return 'none'
            if prefix == None:
                video.download(path)
            else:
                video.download(path, filename_prefix=prefix)
        except HTTPError:
            if i == 4:
                return 'Httperror'
            time.sleep(2)
            i = i+1
            print("Httperror错误,      尝试第%s次连接......" % i+1)
            continue
        except URLError:
            if j == 4:
                return 'Urlerror'
            time.sleep(2)
            j = j+1
            b = j+1
            print("Urlerror错误,      尝试第%d次连接......" % b)
            continue
        except KeyError:
            return 'invalid'

    title = video.title
    file = video.default_filename
    return True,title,file

def load(video_url):
    now = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
    print('当前时间: %s,     开始下载url video:   %s' % (now, video_url))
    ticks = time.time()
    socks.set_default_proxy(socks.SOCKS5, "127.0.0.1", 10808)
    socket.socket = socks.socksocket

    # first load 720p mp4 includes vcodec and acodec
    res_mp4_720p = load_detail(video_url, 22)
    #print(res_mp4_720p)
    if True == res_mp4_720p[0]:
        time_diff(ticks, res_mp4_720p[2])
        return
    if 'none' == res_mp4_720p:
        # load 720p webm includes vcodec and acodec
        res_webm_720p = load_detail(video_url, 45)
        if True == res_webm_720p[0]:
            # 转换 webm格式为 MP4
            command = "ffmpeg -i " + path + res_webm_720p[2] + " " + path + res_webm_720p[1] + ".mp4"
            os.system(command)
            time_diff(ticks, res_webm_720p[2])
            return
    else:
        print('load url 720p mp4 video false, error: %s , url: %s' % (res_mp4_720p,video_url))
        return

    if 'none' == res_webm_720p:
        # load 480p mp4 includes vcodec and acodec
        res_mp4_480p = load_detail(video_url, 83)
        if True == res_mp4_480p[0]:
            time_diff(ticks, res_mp4_480p[2])
            return
    else:
        print('load url 720 webm video false, error: %s , url: %s' % (res_webm_720p, video_url))
        return

    if 'none' == res_mp4_480p:
        # load 480p webm includes vcodec and acodec
        res_webm_480p = load_detail(video_url, 44)
        if True == res_webm_480p[0]:
            # 转换 webm格式为 MP4
            command = "ffmpeg -y -i " + path + res_webm_480p[2] + " " + path + res_webm_480p[1] + ".mp4"
            os.system(command)
            time_diff(ticks, res_webm_480p[2])
            return
    else:
        print('load url 480p mp4 video false, error: %s , url: %s' % (res_mp4_480p,video_url))
        return

    # load 1080p video vcodec and acodec
    res_mp4_1080p_vcodec = load_detail(video_url, 137, 'video_')
    res_mp4_1080p_acodec = load_detail(video_url, 140, 'audio_')
    # merge vcodec and acodec
    merge_res_1080p = merge_vcodec_acodec(res_mp4_1080p_vcodec, res_mp4_1080p_acodec)
    if True == merge_res_1080p:
        time_diff(ticks, res_mp4_1080p_vcodec[2])
        return

    # load 480p video vcodec and acodec
    res_mp4_480p_vcodec = load_detail(video_url, 135, 'video_')
    res_mp4_480p_acodec = load_detail(video_url, 140, 'audio_')
    merge_res_480p = merge_vcodec_acodec(res_mp4_480p_vcodec, res_mp4_480p_acodec)
    if True == merge_res_480p:
        time_diff(ticks, res_mp4_480p_vcodec[2])
        return

    print('There are no proper res, please choose others')
def time_diff(ticks, file):
    new_ticks = time.time()
    tips_time = new_ticks - ticks
    print('load file: %s   用时: %d s' % (file, tips_time))

def merge_vcodec_acodec(vcodec, acodec):
    if True != vcodec[0] or True != acodec[0]:
        return False
    vcodec_prefix_file = 'video_'+vcodec[2]
    acodec_prefix_file = 'audio_'+acodec[2]
    merge_command = "ffmpeg -y -i " + path + vcodec_prefix_file + " -i " + path + acodec_prefix_file + "–vcodec copy –acodec copy " + path + vcodec[2]
    os.system(merge_command)
    return True
def run():
    data = []
    txt = path+"youtube_urls.txt"
    for line in open(txt, "r"):
        line = line[:-1]
        data.append(line)
    for url in data:
        load(url)
        time.sleep(20)
if __name__ == "__main__":
    #path = '/home/'
    path = os.getcwd() + '/'
    run()