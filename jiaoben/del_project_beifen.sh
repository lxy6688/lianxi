#!/bin/bash
#extract del project beifen
#author: lxy

BACKUP_DIR=/data/beifen/project/ #备份文件存储路径
LOGFILE=/data/beifen/project/del_project_beifen.log #日记文件路径
DATE=`date -d'-3day' +'%Y%m%d'` #日期格式（作为文件名）
DUMPFILE='./*'-$DATE* #备份文件名

#删除备份之前，将备份信息头写入日记文件
echo "———————————————–" >> $LOGFILE
echo "BACKUP DATE:" $(date +"%Y-%m-%d %H:%M:%S") >> $LOGFILE
echo "———————————————– " >> $LOGFILE
 
if [ ! -d $BACKUP_DIR ] ;
then
        echo "no such dir,del false" >> $LOGFILE
        exit
fi
#切换至备份目录
cd $BACKUP_DIR

rm -r $DUMPFILE
#判断备份是否删除成功
if [[ $? == 0 ]]; then
    echo "[$DUMPFILE] beifen project del Successful!" >> $LOGFILE
else
    echo "[$DUMPFILE] beifen project del Fail!" >> $LOGFILE
fi
