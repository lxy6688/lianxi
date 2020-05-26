# nlp service test
# lxy
import json
import requests


class ApplyNlpService:
    NLP_SERVICE_URL = 'http://139.199.30.216:9002/api/v1.0/nlp'
    def __int__(self):
        print('对象类的初始化函数')
    def get_apply(self, post_content, categoryies):
        req_body = {
            'post_content':post_content,
            'categories':categoryies
        }
        headers = {'content-type': 'application/json; charset=utf-8'}
        try:
            response = requests.post(
                self.NLP_SERVICE_URL, data=json.dumps(req_body), headers=headers)
            response = response.json()
            if response and response['data']:
                print(response['data']['keywords'])
        except Exception as e:
            print(e)

if __name__ == '__main__':
    post_content = '雅巴依！Nyango去分泌荷尔蒙！卡通人物上阵，你准备好了吗？在音乐中节奏是打击乐的重点，节奏分为前八后十六，前十六后八，切分音，音符带附点，全分音符节奏，二分音符节奏，四分音符节奏，八分音符节奏，十六分音符节奏等。柱式音程与和弦在五线谱中是为上下结构，简单说垂直上下连接既是同时发生，同时演奏。'
    categoryies = '演奏,Uncategorized,伴奏,初学入门,大师,教学,鼓谱'
    apply = ApplyNlpService()
    apply.get_apply(post_content,categoryies)