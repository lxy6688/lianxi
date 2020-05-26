#!/usr/bin/python
# coding:utf-8
import os
import codecs
import jieba.posseg as posseg

import warnings
warnings.filterwarnings(action='ignore', category=UserWarning, module='gensim')
from gensim import corpora, models, similarities


class SimilarText:
    def __init__(self, stop_words_path):
        if os.path.exists(stop_words_path):
            self.stop_words = codecs.open(
                stop_words_path, 'r', encoding='utf8').readlines()
            self.stop_words = [w.strip() for w in self.stop_words]
        self.stop_flag = ['x', 'c', 'u', 'd', 'p', 't', 'uj', 'm', 'f', 'r']

    def get_words(self, text, is_file=False):
        words = []

        word_list = posseg.cut(text)
        if is_file:
            with open(text, 'r', encoding='utf8') as f:
                content = f.read()
                word_list = posseg.cut(content)

        for word, flag in word_list:
            if flag not in self.stop_flag and word not in self.stop_words:
                words.append(word)
        # print(words)
        return words

    def similarity_text(self, categories, text, matrix_type='lsi', is_file=False):

        corpus = []
        # for each in filenames:
        #    corpus.append(self.tokenization(each))
        self.categories = categories.split(',')
        for category in self.categories:
            corpus.append([category])
        # print(corpus)
        # print(len(corpus))

        dictionary = corpora.Dictionary(corpus)
        # print(dictionary)

        doc_vectors = [dictionary.doc2bow(text) for text in corpus]
        # print(len(doc_vectors))
        # print(doc_vectors)

        tfidf = models.TfidfModel(doc_vectors)
        tfidf_vectors = tfidf[doc_vectors]
        # print(len(tfidf_vectors))
        # print(len(tfidf_vectors[0]))
        switcher = {
            'tfidf': self.similarity_text_tfidf,
            'lsi': self.similarity_text_lsi,
        }
        func = switcher.get(matrix_type)
        return func(text, tfidf_vectors, dictionary, is_file)

    def similarity_text_tfidf(self, text, tfidf_vectors, dictionary, is_file=False):
        # TF-IDF matrix
        query = self.get_words(text)
        query_bow = dictionary.doc2bow(query)
        # print(len(query_bow))

        index = similarities.MatrixSimilarity(tfidf_vectors)
        sims = index[query_bow]
        return sorted(enumerate(sims), key=lambda item: -item[1])

    def similarity_text_lsi(self, text, tfidf_vectors, dictionary, is_file=False):
        # LSI matrix
        lsi = models.LsiModel(tfidf_vectors, id2word=dictionary, num_topics=15)
        #print(lsi.print_topics(num_topics=3, num_words=5))

        lsi_vector = lsi[tfidf_vectors]
        # for vec in lsi_vector:
        # print(vec)
        query = self.get_words(text)
        query_bow = dictionary.doc2bow(query)

        query_lsi = lsi[query_bow]

        index = similarities.MatrixSimilarity(lsi_vector)
        sims = index[query_lsi]
        sorted_sims = sorted(enumerate(sims), key=lambda item: -item[1])

        # print(sorted_sims)
        category_score = []
        for c, score in sorted_sims:
            category_score.append('{0}:{1}'.format(self.categories[c], score))

        return category_score


#if __name__ == '__main__':
    #categories = 'A股,个股,基金,大盘,庄家,技巧,新手,机构,行业,资金'
    #filenames = ['./service/train/个股/个股精确分析.txt','./service/train/基金/这样买基金 比80%的股票还赚钱？！.txt', './service/train/新手/新手想学习股票怎么入手呢？.txt']
    #similar = SimilarText('./extra_dict/stop_words.txt')
    #sims = similar.similarity_text(categories, filenames[1], 'lsi', True)
    #print(sims)
