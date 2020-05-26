#print("hello")
import  os
import sys
import time

# os.system("cp get-pip.py aaa.py")   # 运行shell命令
# print(1111)

path = os.getcwd()    # 当前文件所在路径
print(path)

# url = sys.argv[1]   #接收第一个参数
# print(url)
# itag_id = sys.argv[2]   # 接收第二个参数
# print(itag_id)

def run():
    return
    print(22)
run()

def test():
    while True:
        print(33)
        return 'aa'

test()

# 获取当前时间
now = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())

# find() 查找字符串，没有返回-1   index()查找没有返回异常
str = '[<Stream: itag="22" mime_type="video/mp4" res="720p" fps="30fps" vcodec="avc1.64001F" acodec="mp4a.40.2">, <Stream: itag="43" mime_type="video/webm" res="360p" fps="30fps" vcodec="vp8.0" acodec="vorbis">, <Stream: itag="18" mime_type="video/mp4" res="360p" fps="30fps" vcodec="avc1.42001E" acodec="mp4a.40.2">, <Stream: itag="137" mime_type="video/mp4" res="1080p" fps="30fps" vcodec="avc1.640028">, <Stream: itag="248" mime_type="video/webm" res="1080p" fps="30fps" vcodec="vp9">, <Stream: itag="136" mime_type="video/mp4" res="720p" fps="30fps" vcodec="avc1.4d4016">, <Stream: itag="247" mime_type="video/webm" res="720p" fps="30fps" vcodec="vp9">, <Stream: itag="135" mime_type="video/mp4" res="480p" fps="30fps" vcodec="avc1.4d4014">, <Stream: itag="244" mime_type="video/webm" res="480p" fps="30fps" vcodec="vp9">, <Stream: itag="134" mime_type="video/mp4" res="360p" fps="30fps" vcodec="avc1.4d401e">, <Stream: itag="243" mime_type="video/webm" res="360p" fps="30fps" vcodec="vp9">, <Stream: itag="133" mime_type="video/mp4" res="240p" fps="30fps" vcodec="avc1.4d400c">, <Stream: itag="242" mime_type="video/webm" res="240p" fps="30fps" vcodec="vp9">, <Stream: itag="160" mime_type="video/mp4" res="144p" fps="30fps" vcodec="avc1.4d400b">, <Stream: itag="278" mime_type="video/webm" res="144p" fps="30fps" vcodec="vp9">, <Stream: itag="140" mime_type="audio/mp4" abr="128kbps" acodec="mp4a.40.2">, <Stream: itag="251" mime_type="audio/webm" abr="160kbps" acodec="opus">]'
a = str.find('itag="23332"')
print(a)

# 异常处理
# https://blog.csdn.net/hiwoshixiaoyu/article/details/89343728#4_except_59    python3异常
# import sys
# try:
#     ages = {'Jim' : 30, 'Pam' : 28 , 'Kevin': 33}
#     ages['aa']                         # 报KeyError
# except Exception as e:                              # 捕获所有的异常
#     print("Unexpected error:", sys.exc_info()[0])   # 打印具体的异常类型
import sys
try:
    ages = {'Jim' : 30, 'Pam' : 28 , 'Kevin': 33}
    ages['aa']                         # 报KeyError
except KeyError as e:
    #print("Unexpected error:", sys.exc_info()[0])   # 打印具体的异常类型
    print(repr(e))
except Exception as e:                 # 捕获其他所有的异常
    print("Unexpected error:", sys.exc_info()[0])    # 打印具体的异常类型


