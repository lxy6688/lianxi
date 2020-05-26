#!/usr/bin/python
# coding:utf-8

import os
import re
import nltk
import numpy
import jieba
import codecs

from jieba import analyse
import jieba.posseg as posseg


class SummaryText:

    def __init__(self, stop_word_path, suggest_dict_path):
        # Top N highest frequency word
        self.n = 100
        # The distance between words
        self.cluster_threshold = 5
        # Return the top n sentences or keywords
        self.top_sentences = 5
        # Stop flag
        self.stop_flag = ['x', 'c', 'u', 'd', 'p', 't', 'uj', 'm', 'f', 'r']

        # Load stop words
        self.stop_words = {}
        self.stop_word_path = stop_word_path
        self.suggest_dict_path = suggest_dict_path
        if os.path.exists(stop_word_path):
            stoplist = [line.strip() for line in codecs.open(
                stop_word_path, 'r', encoding='utf8').readlines()]
            self.stop_words = {}.fromkeys(stoplist)

    def _split_sentences(self, texts):
        '''
        Split the texts into single sentences, these punctuations（.!?。！？） are the opinions of the split.
        :param texts: the text to be processed
        :return: sentence list
        '''
        splitstr = '.!?。！？'  # .decode('utf8')
        start = 0
        index = 0
        sentences = []
        for text in texts:
            if text in splitstr:
                sentences.append(texts[start:index + 1])
                start = index + 1
            index += 1
        if start < len(texts):
            # include no punctuation at the end of the text
            sentences.append(texts[start:])

        return sentences

    def _score_sentences(self, sentences, topn_words):
        '''
        Grade the sentences using keywords
        :param sentences: sentence list
        :param topn_words: keyword list
        :return: score list
        '''
        scores = []
        sentence_idx = -1
        for s in [list(jieba.cut(s)) for s in sentences]:
            sentence_idx += 1
            word_idx = []
            for w in topn_words:
                try:
                    word_idx.append(s.index(w))
                except ValueError:  # w not in the sentence
                    pass
            word_idx.sort()
            if len(word_idx) == 0:
                continue

            # For two consecutive words, use the word position index to calculate the cluster through the distance threshold.
            clusters = []
            cluster = [word_idx[0]]
            i = 1
            while i < len(word_idx):
                if word_idx[i] - word_idx[i - 1] < self.cluster_threshold:
                    cluster.append(word_idx[i])
                else:
                    clusters.append(cluster[:])
                    cluster = [word_idx[i]]
                i += 1
            clusters.append(cluster)
            # Grade each cluster, the maximum score for each cluster is the grading of the sentence.
            max_cluster_score = 0
            for c in clusters:
                significant_words_in_cluster = len(c)
                total_words_in_cluster = c[-1] - c[0] + 1
                score = 1.0 * significant_words_in_cluster * \
                    significant_words_in_cluster / total_words_in_cluster
                if score > max_cluster_score:
                    max_cluster_score = score
            scores.append((sentence_idx, max_cluster_score))

        return scores

    def summary_scored_txt(self, text):
        sentences = self._split_sentences(text)

        words = [w for sentence in sentences for w, f in posseg.cut(
            sentence) if w not in self.stop_words if len(w) > 1 and w != '\t' if f not in self.stop_flag]
        # words = []
        # for sentence in sentences:
        #     for w in jieba.cut(sentence):
        #         if w not in self.stop_words and len(w) > 1 and w != '\t':
        #             words.append(w)

        # Word frequency statistics
        wordfre = nltk.FreqDist(words)

        # Get the first N words with the highest word frequency
        topn_words = [w[0] for w in sorted(
            wordfre.items(), key=lambda d: d[1], reverse=True)][:self.n]

        # Grade the sentences using top keywords
        scored_sentences = self._score_sentences(sentences, topn_words)

        # Filter non-essential sentences using mean & std
        avg = numpy.mean([s[1] for s in scored_sentences])  # mean value
        std = numpy.std([s[1] for s in scored_sentences]
                        )   # standard deviation

        summarySentences = []
        for (sent_idx, score) in scored_sentences:
            if score > (avg + 0.5 * std):
                summarySentences.append(sentences[sent_idx])

        return summarySentences

    def summary_top_n_txt(self, text):
        sentences = self._split_sentences(text)

        words = [w for sentence in sentences for w, f in posseg.cut(sentence) if w not in self.stop_words if
                 len(w) > 1 and w != '\t' if f not in self.stop_flag]
        # print(words)
        # words = []
        # for sentence in sentences:
        #     for w in jieba.cut(sentence):
        #         if w not in self.stop_words and len(w) > 1 and w != '\t':
        #             words.append(w)

        # Word frequency statistics
        wordfre = nltk.FreqDist(words)

        # Get the first N words with the highest word frequency
        topn_words = [w[0] for w in sorted(
            wordfre.items(), key=lambda d: d[1], reverse=True)][:self.n]

        # Grade the sentences using top keywords
        scored_sentences = self._score_sentences(sentences, topn_words)

        top_n_scored = sorted(
            scored_sentences, key=lambda s: s[1])[-self.top_sentences:]
        top_n_scored = sorted(top_n_scored, key=lambda s: s[0])
        summarySentences = []
        for (idx, score) in top_n_scored:
            summarySentences.append(sentences[idx])

        return sentences


# if __name__ == '__main__':
    #obj = SummaryText('./../extra_dict/stop_words.txt','./../extra_dict/suggest_words.txt')

    #txt = 'The information disclosed by the Film Funds Office of the State Administration of Press, Publication, Radio, Film and Television shows that, the total box office in China amounted to nearly 3 billion yuan during the first six days of the lunar year (February 8 - 13), an increase of 67% compared to the 1.797 billion yuan in the Chinese Spring Festival period in 2015, becoming the "Best Chinese Spring Festival Period in History".'
    #txt = u'十八大以来的五年，是党和国家发展进程中极不平凡的五年。面对世界经济复苏乏力、局部冲突和动荡频发、全球性问题加剧的外部环境，面对我国经济发展进入新常态等一系列深刻变化，我们坚持稳中求进工作总基调，迎难而上，开拓进取，取得了改革开放和社会主义现代化建设的历史性成就。为贯彻十八大精神，党中央召开七次全会，分别就政府机构改革和职能转变、全面深化改革、全面推进依法治国、制定“十三五”规划、全面从严治党等重大问题作出决定和部署。五年来，我们统筹推进“五位一体”总体布局、协调推进“四个全面”战略布局，“十二五”规划胜利完成，“十三五”规划顺利实施，党和国家事业全面开创新局面。经济建设取得重大成就。坚定不移贯彻新发展理念，坚决端正发展观念、转变发展方式，发展质量和效益不断提升。经济保持中高速增长，在世界主要国家中名列前茅，国内生产总值从五十四万亿元增长到八十万亿元，稳居世界第二，对世界经济增长贡献率超过百分之三十。供给侧结构性改革深入推进，经济结构不断优化，数字经济等新兴产业蓬勃发展，高铁、公路、桥梁、港口、机场等基础设施建设快速推进。农业现代化稳步推进，粮食生产能力达到一万二千亿斤。城镇化率年均提高一点二个百分点，八千多万农业转移人口成为城镇居民。区域发展协调性增强，“一带一路”建设、京津冀协同发展、长江经济带发展成效显著。创新驱动发展战略大力实施，创新型国家建设成果丰硕，天宫、蛟龙、天眼、悟空、墨子、大飞机等重大科技成果相继问世。南海岛礁建设积极推进。开放型经济新体制逐步健全，对外贸易、对外投资、外汇储备稳居世界前列。全面深化改革取得重大突破。蹄疾步稳推进全面深化改革，坚决破除各方面体制机制弊端。改革全面发力、多点突破、纵深推进，着力增强改革系统性、整体性、协同性，压茬拓展改革广度和深度，推出一千五百多项改革举措，重要领域和关键环节改革取得突破性进展，主要领域改革主体框架基本确立。中国特色社会主义制度更加完善，国家治理体系和治理能力现代化水平明显提高，全社会发展活力和创新活力明显增强。'
    #txt = u'很多人在找工作的过程中，对于五险一金都是有要求的，当我们每一个月都存入住房公积金的时候，大家也会关注住房公积金有利息吗？这方面的问题看起来很重要，和我们很多的人都有关系，但是很少有人真正的去了解，所以现在就来给大家进一步的计算，到底有没有利息，以及具体的一些方式。有利息，住房公积金有利息吗？这方面是可以肯定的，我们在存入之后会按照人民银行所规定的储蓄方面的利率来进行计算，而且我们每一个月存入这些住房公积金，存入的当年也就会有一些方面的资金，我们在整个的过程中之后，每一个年度都会按照相关的利息来进行计算，而且本息汇每一年进行结转。根据市场的变化而变化，对于住房公积金有利息吗？我们要清楚的知道，这个过程中存在利息，而且也会根据整个市场的变化，然后存在着一定的改变。我们能够进一步的去了解更多，发现了其中的这些改变之后，那么对于自身来说都会有更多的好处，所以希望你可以真正的去了解。不断的去考虑一些具体的内容，关注住房公积金有利息吗，这对于我们来说是很重要的一个部分。大家在认识的过程中，应该不断的去关注各种不同的信息，并且能够学会计算的方法，这样对于整个的过程来说都会很好。每个人在做的过程中，我们都要真正的去关注到了这个方面，然后有了一些恰当的选择之后，那么对于整个过程来说都会很不错。'

    # print("--------")

    #summarys = obj.summary_top_n_txt(txt)
    # print(summarys)
