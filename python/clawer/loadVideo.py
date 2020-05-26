'''
# pip3 install you-get
# you-get https://y.qq.com/n/yqq/mv/v/g0025dltuzc.html   默认下载当前目录

'''


import sys
import you_get
import requests

def download(url, path):
    sys.argv = ['you-get', '-o', path, url]
    you_get.main()

# 最简单的固定链接视频下载
def simple_load():
    print("开始下载")
    url = 'https://pic.ibaotu.com/00/51/34/88a888piCbRB.mp4'
    r = requests.get(url, stream=True)
    with open('test.mp4', "wb") as mp4:
        for chunk in r.iter_content(chunk_size=1024 * 1024):
            if chunk:
                mp4.write(chunk)
    print("下载结束")

if __name__ == '__main__':
    # 视频网站的地址
    #url = 'https://www.bilibili.com/bangumi/play/ep118488?from=search&seid=5050973611974373611'
    url = 'https://www.bilibili.com/video/av70676414?from=search&seid=8105241382808008274'
    # 视频输出的位置
    path = './'
    download(url, path)

    # merge_command = "ffmpeg -y -i "
    # os.system(merge_command)