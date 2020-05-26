#!usr/bin/python
# coding:utf-8

from flask import Flask, jsonify, request, json, make_response, abort
from utilities import htmlUtilities
from extract import keyword
from extract import summary
from similar import similar

app = Flask(__name__)


@app.route('/api/v1.0/nlp/cdd', methods=['POST'])
def get_nlp_cdd_info():
    data = request.get_data()
    json_data = json.loads(data)

    post_content = categories = post_title = ''
    if 'post_content' in json_data:
        post_content = json_data['post_content']

    if 'categories' in json_data:
        categories = json_data['categories']

    if 'post_title' in json_data:
        post_title = json_data['post_title']

    if not post_content and not post_title:
        abort(400)

    if post_title and post_content:
        post_content = ''   #如果标题和正文都存在,那就按照标题去切词
    
    stop_words_path = './extra_dict/stop_words_cdd.txt'
    if post_content:
        suggest_words_path = './extra_dict/suggest_words_cdd.txt'
    if post_title:
        suggest_words_path = './extra_dict/title_suggest_words_cdd.txt'
        post_content = post_title
    allow_origin = 'http://007dir.cn'

    return get_nlp_result(stop_words_path, suggest_words_path, allow_origin, post_content, categories)


def get_nlp_result(stop_words_path, suggest_words_path, allow_origin, post_content, categories):
    hu = htmlUtilities.htmlUtilities()
    texts = post_content.strip().encode('utf-8', 'ignore')
    texts = hu.filter_tags(texts)

    summary_obj = summary.SummaryText(stop_words_path, suggest_words_path)
    keyword_obj = keyword.KeywordText(stop_words_path, suggest_words_path)
    similar_obj = similar.SimilarText(stop_words_path)

    # summary_result = summary_obj.summary_top_n_txt(texts)
    keywords_result = keyword_obj.keywords(texts)
    category_scores_result = similar_obj.similarity_text(
        categories, texts, 'lsi')

    response = make_response(
        jsonify({'code': 200, 'data': {'keywords': keywords_result, 'category_scores': category_scores_result}}))

    # response.headers['Access-Control-Allow-Origin'] = allow_origin
    # response.headers['Access-Control-Allow-Methods'] = 'POST'
    # response.headers['Access-Control-Allow-Headers'] = 'x-requested-with,content-type'

    return response


if __name__ == '__main__':
    # app.run(host='0.0.0.0',port=8081)  # port=8080
    from werkzeug.contrib.fixers import ProxyFix
    app.wsgi_app = ProxyFix(app.wsgi_app)
