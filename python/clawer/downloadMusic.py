# baidu vip music

import requests
import re


class BaiduMusic(object):
    def __init__(self):
        self.url = 'http://musicapi.taihe.com/v1/restserver/ting?method=baidu.ting.song.playAAC&format=jsonp&songid={}'
        self.headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.20 Safari/537.36'
        }
        self.song_id = []  # 保存歌曲id
        self.song_url_data = []  # 保存歌曲url

    # 1.获取vip歌曲的id,以周杰伦歌单为列
    def vipsong_id(self):
        url_id = 'http://music.taihe.com/artist/7994'
        response_id = requests.get(url_id, headers=self.headers)
        html = response_id.text
        self.song_id = set(re.findall('a href="/song/(\d*?)"', html))

    # 2. 获取全部歌曲id的url
    def song_url(self):
        for i in self.song_id:
            url = self.url.format(i)
            self.song_url_data.append(url)

    # 3.发送歌曲请求开启下载
    def response_down(self):
        self.vipsong_id()  # 获取第1步中的歌曲id
        self.song_url()  # 获取歌曲url
        for url in self.song_url_data:
            response = requests.get(url, headers=self.headers)
            data = response.json()
            file_link = data['bitrate']['file_link']
            title = data['songinfo']['title']
            print(file_link, title)  # 测试

            response_download = requests.get(file_link, headers=self.headers)
            with open(title + '.m4a', mode='wb')as f:
                f.write(response_download.content)


if __name__ == '__main__':
    BaiduMusic().response_down()