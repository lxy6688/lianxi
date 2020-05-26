import socket
import socks
import pytube
import time
import os
from urllib.error import HTTPError, URLError
import sys

def get_video_allstreams(video_url):
    youtube = pytube.YouTube(video_url)
    videos = youtube.streams.all()
    return videos
    # print(video)
    # print(video[0])
    # print(type(video))  # <class 'list'>
    # aa = getattr(videos[0],'itag',0)
    # print(aa)
    # print(type(aa))
    # exit()
    # for video in videos:
    #     print(video)
    # exit()
def load(video_url, itag, prefix = None):
    try:
        youtube = pytube.YouTube(video_url)
        video = youtube.streams.get_by_itag(itag)
        if None == video:
            return 'none'
        if prefix == None:
            video.download(path)
        else:
            video.download(path, filename_prefix=prefix)
    except HTTPError as e:
            #return 'Httperror'
            return repr(e)
    except URLError as e:
            #return 'Urlerror'
            return repr(e)
    except KeyError as e:
        #return 'url_encoded_fmt_stream_map'
        return repr(e)
    except Exception as e:
        #return 'invalid'
        return sys.exc_info()[0]
    title = video.title
    file = video.default_filename
    print(title)
    print(file)
    return True,title,file

def time_diff(ticks, file):
    new_ticks = time.time()
    tips_time = new_ticks - ticks
    print('load file: %s  ok,  用时: %d s' % (file, tips_time))

def merge_vcodec_acodec(vcodec, acodec, suffix = 'mp4'):
    if True != vcodec[0] or True != acodec[0]:
        return False
    # vcodec_prefix_file = 'video_'+vcodec[1]+'.'+suffix
    # acodec_prefix_file = 'audio_'+acodec[1]+'.'+suffix
    # new_file = vcodec[1]+'.'+suffix

    vcodec_prefix_file = 'video_' + vcodec[2]
    acodec_prefix_file = 'audio_' + acodec[2]
    new_file = vcodec[2]

    merge_command = "ffmpeg -y -i " + path + vcodec_prefix_file + " -i " + path + acodec_prefix_file + " -vcodec copy -acodec copy " + path + new_file
    os.system(merge_command)
    # 判断文件存在, 则返回成功
    is_new_file = path + new_file
    if True == os.path.isfile(is_new_file):
        return True
    return False
def run():
    data = []
    txt = path+"youtube_urls.txt"
    for line in open(txt, "r"):
        line = line[:-1]
        data.append(line)
    for url in data:
        now = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())
        print('当前时间: %s,     开始下载url video:   %s' % (now, url))
        ticks = time.time()
        # first get all video streams
        streamsRes = get_video_allstreams(url)
        status = 'none'
        for video in streamsRes:
            itagData = getattr(video,'itag',0)
            mimeType = getattr(video,'mime_type',0)
            if itagData == "55":
                # load 720p mp4 includes vcodec and acodec
                loadRes = load(url,"55")
                status = loadRes[0]
                title = loadRes[1]
                break
            elif itagData == "83":
                # load 480p mp4 includes vcodec and acodec
                loadRes = load(url,"83")
                status = loadRes[0]
                title = loadRes[1]
                break
            elif itagData == "137":
                # load 1080p mp4 video vcodec
                loadVcodecRes = load(url, "137", 'video_')
                # load 1080p mp4 video audio
                loadAcodecRes = load(url, "140", 'audio_')

                status = False
                merge_res_1080p = merge_vcodec_acodec(loadVcodecRes, loadAcodecRes)
                if True == merge_res_1080p:
                    status = True
                    title = loadVcodecRes[1]
                else:
                    loadRes = '1080p mp4 merge vdiceo and adiceo false, new video is not exists '
                break
            elif itagData == "135":
                # load 480p mp4 video vcodec
                loadVcodecRes = load(url, "135", 'video_')
                # load 480p mp4 video audio
                loadAcodecRes = load(url, "140", 'audio_')

                status = False
                merge_res_480p = merge_vcodec_acodec(loadVcodecRes, loadAcodecRes)
                if True == merge_res_480p:
                    status = True
                    title = loadVcodecRes[1]
                else:
                    loadRes = '480p mp4 merge vdiceo and adiceo false, new video is not exists '
                break
        if(status == 'none'):
            print('There are no proper res, please choose others.   url: %s' % url)
            continue
        if True == status:
            time_diff(ticks, title)
        else:
            print('load video itag: %s, type: %s false, error: %s , url: %s' % (itagData,mimeType,loadRes, url))
        time.sleep(20)

if __name__ == "__main__":
    socks.set_default_proxy(socks.SOCKS5, "127.0.0.1", 10808)
    socket.socket = socks.socksocket
    #path = '/home/'
    path = os.getcwd() + '/'
    run()