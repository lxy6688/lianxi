#!/bin/bash
#extract del mysql beifen
#author: lxy

BACKUP_DIR=/data/beifen/db/ #备份文件存储路径
LOGFILE=/data/beifen/db/del_db_beifen.log #日记文件路径
DATE=`date -d'-3day' +'%Y%m%d'` #日期格式（作为文件名）
DUMPFILE='./cdd-seo'-$DATE* #备份文件名

#开始备份之前，将备份信息头写入日记文件
echo " " >> $LOGFILE
echo " " >> $LOGFILE
echo "———————————————–" >> $LOGFILE
echo "BACKUP DATE:" $(date +"%Y-%m-%d %H:%M:%S") >> $LOGFILE
echo "———————————————– " >> $LOGFILE
 
if [ ! -d $BACKUP_DIR ] ;
then
        mkdir -p "$BACKUP_DIR"
fi
#切换至备份目录
cd $BACKUP_DIR
#使用mysqldump 命令备份制定数据库，并以格式化的时间戳命名备份文件
rm -r $DUMPFILE
#判断数据库备份是否成功
if [[ $? == 0 ]]; then
    echo "[$DUMPFILE] beifen sql del Successful!" >> $LOGFILE
else
    echo "[$DUMPFILE] beifen sql del Fail!" >> $LOGFILE
fi
