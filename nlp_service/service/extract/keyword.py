#!/usr/bin/python
# coding:utf-8

import os
import re
import nltk
import numpy
import jieba
import codecs

from jieba import analyse


class KeywordText:

    def __init__(self, stop_word_path, suggest_dict_path):
        # Return the top n keywords
        self.TOP_KEYWORDS = 5

        # Used flag
        # self.used_flag = ('n', 'nv', 'nz', 'eng', 'vn', 'nl', 'ng', 'nt', 'nr', 'ns')

        # Stop & suggest words
        self.stop_word_path = stop_word_path

        self.suggest_dict = {}
        self.suggest_dict_path = suggest_dict_path
        if os.path.exists(suggest_dict_path):
            suggest_list = [line.strip() for line in codecs.open(
                suggest_dict_path, 'r', encoding='utf8').readlines()]
            self.suggest_dict = {}.fromkeys(suggest_list)

    def keywords(self, text, analyse_type='tfidf'):
        jieba.analyse.set_stop_words(self.stop_word_path)
        # jieba.set_dictionary('./extra_dict/test_dict.txt')

        for k in self.suggest_dict:
            # jieba.add_word(k)
            # print(k)
            jieba.suggest_freq(k, True)

        switcher = {
            'textrank': self.text_rank_keywords,
            'tfidf': self.tfidf_keywords,
        }
        func = switcher.get(analyse_type)
        return func(text)

    def text_rank_keywords(self, text):
        textrank = analyse.textrank
        keywords = textrank(text, withWeight=False, topK=self.TOP_KEYWORDS)
        # print(keywords)
        # , allowPOS=self.used_flag
        return keywords

    def tfidf_keywords(self, text):
        tfidf = analyse.extract_tags
        keywords = tfidf(text, withWeight=False, topK=self.TOP_KEYWORDS)
        # print(keywords)
        return keywords


if __name__ == '__main__':
    obj = KeywordText('./service/extra_dict/stop_words_jisujie.txt',
                      './service/extra_dict/suggest_words_jisujie.txt')

    txt = 'The information disclosed by the Film Funds Office of the State Administration of Press, Publication, Radio, Film and Television shows that, the total box office in China amounted to nearly 3 billion yuan during the first six days of the lunar year (February 8 - 13), an increase of 67% compared to the 1.797 billion yuan in the Chinese Spring Festival period in 2015, becoming the "Best Chinese Spring Festival Period in History".'
    txt = u'十八大以来的五年，是党和国家发展进程中极不平凡的五年。面对世界经济复苏乏力、局部冲突和动荡频发、全球性问题加剧的外部环境，面对我国经济发展进入新常态等一系列深刻变化，我们坚持稳中求进工作总基调，迎难而上，开拓进取，取得了改革开放和社会主义现代化建设的历史性成就。为贯彻十八大精神，党中央召开七次全会，分别就政府机构改革和职能转变、全面深化改革、全面推进依法治国、制定“十三五”规划、全面从严治党等重大问题作出决定和部署。五年来，我们统筹推进“五位一体”总体布局、协调推进“四个全面”战略布局，“十二五”规划胜利完成，“十三五”规划顺利实施，党和国家事业全面开创新局面。经济建设取得重大成就。坚定不移贯彻新发展理念，坚决端正发展观念、转变发展方式，发展质量和效益不断提升。经济保持中高速增长，在世界主要国家中名列前茅，国内生产总值从五十四万亿元增长到八十万亿元，稳居世界第二，对世界经济增长贡献率超过百分之三十。供给侧结构性改革深入推进，经济结构不断优化，数字经济等新兴产业蓬勃发展，高铁、公路、桥梁、港口、机场等基础设施建设快速推进。农业现代化稳步推进，粮食生产能力达到一万二千亿斤。城镇化率年均提高一点二个百分点，八千多万农业转移人口成为城镇居民。区域发展协调性增强，“一带一路”建设、京津冀协同发展、长江经济带发展成效显著。创新驱动发展战略大力实施，创新型国家建设成果丰硕，天宫、蛟龙、天眼、悟空、墨子、大飞机等重大科技成果相继问世。南海岛礁建设积极推进。开放型经济新体制逐步健全，对外贸易、对外投资、外汇储备稳居世界前列。全面深化改革取得重大突破。蹄疾步稳推进全面深化改革，坚决破除各方面体制机制弊端。改革全面发力、多点突破、纵深推进，着力增强改革系统性、整体性、协同性，压茬拓展改革广度和深度，推出一千五百多项改革举措，重要领域和关键环节改革取得突破性进展，主要领域改革主体框架基本确立。中国特色社会主义制度更加完善，国家治理体系和治理能力现代化水平明显提高，全社会发展活力和创新活力明显增强。'
    txt = u'很多人在找工作的过程中，对于五险一金都是有要求的，当我们每一个月都存入住房公积金的时候，大家也会关注住房公积金有利息吗？这方面的问题看起来很重要，和我们很多的人都有关系，但是很少有人真正的去了解，所以现在就来给大家进一步的计算，到底有没有利息，以及具体的一些方式。有利息，住房公积金有利息吗？这方面是可以肯定的，我们在存入之后会按照人民银行所规定的储蓄方面的利率来进行计算，而且我们每一个月存入这些住房公积金，存入的当年也就会有一些方面的资金，我们在整个的过程中之后，每一个年度都会按照相关的利息来进行计算，而且本息汇每一年进行结转。根据市场的变化而变化，对于住房公积金有利息吗？我们要清楚的知道，这个过程中存在利息，而且也会根据整个市场的变化，然后存在着一定的改变。我们能够进一步的去了解更多，发现了其中的这些改变之后，那么对于自身来说都会有更多的好处，所以希望你可以真正的去了解。不断的去考虑一些具体的内容，关注住房公积金有利息吗，这对于我们来说是很重要的一个部分。大家在认识的过程中，应该不断的去关注各种不同的信息，并且能够学会计算的方法，这样对于整个的过程来说都会很好。每个人在做的过程中，我们都要真正的去关注到了这个方面，然后有了一些恰当的选择之后，那么对于整个过程来说都会很不错。'
    txt = u'什么是逆回购？央行逆回购又指什么？怎么利用逆回购获取盈利？央行是货币政策的裁定者，利用货币政策工具来干预市场，保证市场资金流动性良好，逆回购就是一种调节工具。央行逆回购主要是为了向市场释放流动资金，缓解资金紧张。还有一种是国债逆回购，主要是针对投资者的理财工具。那么，逆回购具体是怎么操作的呢？一般投资者怎么利用逆回购获得收益？央行作为国家货币政策的制定者，其一举一动对国内经济发展特别是金融行业的发展有着重大的影响。央行的货币政策工具多种多样，比如说公开市场操作、借贷便利等等，主要是用来调节市场资金的流动性的工具。其中，公开市场操作又包括正回购和逆回购，今天我们就来看一下什么是央行逆回购。\n\n<img class: \"alignnone size-full wp-image-2178 aligncenter\" src=\"http://static.17kdy.com/wp-content/uploads/23.1-4.jpg\" alt=\"\" width=\"339\" height=\"240\" />\n\n以下是节选自360百科的关于央行逆回购的定义：\n<blockquote>央行逆回购为中国人民银行向一级交易商购买有价证券，并约定在未来特定日期将有价证券卖给一级交易商的交易行为，逆回购为央行向市场上投放流动性的操作，正回购则为央行从市场收回流动性的操作。 简单解释就是主动借出资金，获取债券质押的交易就称为逆回购交易，此时央行扮演投资者，是接受债券质押、借出资金的融出方。</blockquote>\n从这个定义中也可以看出央行逆回购事实上就是往市场里面撒钱，增加基础货币的供给，从而缓解了市场的流动性。\n<h1>这个过程是：</h1>\n央行在正回购的时候在一级市场卖给银行、券商有价证券（可以是国债之类的），银行或是券商想要买这个证券就需要拿钱对吧，拿了钱，这个钱就到了央行的手里，而银行和券商的钱哪来的？只能是市场上流通的货币，这样一来市场上的钱就少了。之后央行在未来的某一天再把自己发行的有价证券回购回去，这样一来也就是银行、券商之前买的央行的有价证券现在又要卖给央行，央行买回来的时候也需要拿钱买，这个时候央行就会与银行或是券商在一级市场交易，把钱给银行或是券商，然后把自己之前发行的有价证券买回来，这样一来银行或券商手里的钱就多了，银行会进行理财或是放贷，把钱流到市场中，而券商会进行各种投资，同样的也会把钱流通到市场中，这样一来市场的资金就多了起来，流通紧张自然也就缓解了，这就是逆回购。\n\n<img class=\"alignnone size-full wp-image-2179 aligncenter\" src=\"http://static.17kdy.com/wp-content/uploads/23.2-4.jpg\" alt=\"\" width=\"362\" height=\"240\" />\n\n这种做法比直接的降准降息对市场的负面影响更小，而且操作起来更加便利，滞后性较短，此外除了逆回购之外，MLF（俗称：麻辣烫）、SLF（俗称：酸辣粉）也是目前央行常用的货币政策工具。\n\n<strong>频繁逆回购发生的时间节点：</strong>\n\n在每月的交易日央行会根据实际情况进行不同程度的逆回购操作，但是通常情况下，没到月末、季度末、节前和年底，央行都会加大逆回购的操作力度。其中，月末和季度末增加逆回购的原因是银行等金融机构资金流动较为紧张，特别是银行每季度的MPA考核，每到这个时候银行就会为了成绩好看点赶紧回笼资金，通常会造成市场特别是股市资金流通过于紧张的局面，央行为了缓解这种局面最常用的做法就是增加逆回购操作力度。而节前、年底资金紧张主要是因为过节大家都会选择持币过节，安全性比较高。\n\n<img class=\"alignnone size-full wp-image-2180 aligncenter\" src=\"http://static.17kdy.com/wp-content/uploads/23.3-3.jpg\" alt=\"\" width=\"431\" height=\"240\" />\n\n<strong>那么我们应该如何利用逆回购获得盈利呢？</strong>\n\n一般来说，我们可以通过国债逆回购这种无风险的套利来获取利润。不过从经验来看，建议10万元之下的投资者可以选择在深市借出自己的闲置资金，而在选择期限的时候，以计息期为主，尽量选择在周五或者周四的时候买入国债逆回购，这样计息期就会大概率包含周六、日两天，能够保证大家获取更多的盈利。'
    print("--------")
    keywords = obj.keywords(txt)
    print("[keywords]: ", "/ ".join(keywords))
