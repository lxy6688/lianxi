#!/bin/bash
#extract spider log
#author: lxy

LOG_FILE="/data/logs/web/test1.007dir.cn.access.log"
WEBSITE_LOG_PATH="/data/websites/cdd/new/cdd-seo/wp-spider-log-pg0IrK104HSMDAfK"

today=`date +"%d\/%b\/%Y"`
yesterday=`date -d"-1day" +"%d\/%b\/%Y"`
yesterday_short=`date -d"-1day" +"%Y-%m-%d"`

sed -n "/$yesterday/,/$today/p;/bot\|spider/!d" $LOG_FILE | grep -i "bot\|spider" > $WEBSITE_LOG_PATH/robots_full_log_$yesterday_short.txt
