#!/usr/bin/python
# coding:utf-8

import re


class htmlUtilities:
    def filter_html_tag(self, originhtml):
        filtered_html = originhtml
        filtered_html = re.sub(r'<[^>]+>', "", filtered_html, flags=re.I)
        filtered_html = re.sub("<br\s*>", "", filtered_html, flags=re.I)
        filtered_html = re.sub("<br\s*/>", "", filtered_html, flags=re.I)
        filtered_html = re.sub("nbsp;", "", filtered_html, flags=re.I)
        filtered_html = re.sub("&nbsp;", "", filtered_html, flags=re.I)
        filtered_html = re.sub("\n", "", filtered_html, flags=re.I)
        filtered_html = re.sub("&", "", filtered_html, flags=re.I)
        filtered_html = re.sub(
            "<a\s+[^<>]+>(?P<aContent>[^<>]+?)</a>", "\g<aContent>", filtered_html, flags=re.I)
        filtered_html = re.sub(
            "<b>(?P<bContent>[^<>]+?)</b>", "\g<bContent>", filtered_html, flags=re.I)
        filtered_html = re.sub(
            "<strong>(?P<strongContent>[^<>]+?)</strong>", "\g<strongContent>", filtered_html, flags=re.I)

        return filtered_html

    def filter_tags(self, htmlstr):
        re_cdata = re.compile('//<!\[CDATA\[[^>]*//\]\]>', re.I)
        re_script = re.compile(
            '<\s*script[^>]*>[^<]*<\s*/\s*script\s*>', re.I)
        re_style = re.compile(
            '<\s*style[^>]*>[^<]*<\s*/\s*style\s*>', re.I)
        re_br = re.compile('<br\s*?/?>')
        re_h = re.compile('</?\w+[^>]*>')
        re_comment = re.compile('<!--[^>]*-->')
        re_stopwords = re.compile('\u3000')
        htmlstr = htmlstr.decode('utf-8')
        s = re_cdata.sub('', htmlstr)
        s = re_script.sub('', s)
        s = re_style.sub('', s)
        s = re_br.sub('\n', s)
        s = re_h.sub('', s)
        s = re_comment.sub('', s)
        s = re_stopwords.sub('', s)
        blank_line = re.compile('\n+')
        s = blank_line.sub('\n', s)
        s = s.replace('\r', '').replace('\n', '').replace('\t', '')
        s = self.replace_char_entity(s)
        return s

    def replace_char_entity(self, htmlstr):
        CHAR_ENTITIES = {'nbsp': ' ', '160': ' ',
                         'lt': '<', '60': '<',
                         'gt': '>', '62': '>',
                         'amp': '&', '38': '&',
                         'quot': '"', '34': '"', }

        re_charEntity = re.compile(r'&#?(?P<name>\w+);')
        sz = re_charEntity.search(htmlstr)
        while sz:
            entity = sz.group()
            key = sz.group('name')
            try:
                htmlstr = re_charEntity.sub(CHAR_ENTITIES[key], htmlstr, 1)
                sz = re_charEntity.search(htmlstr)
            except KeyError:
                htmlstr = re_charEntity.sub('', htmlstr, 1)
                sz = re_charEntity.search(htmlstr)
        return htmlstr
