#!/bin/bash
#extract qihanyikao beifen
#author: lxy

BACKUP_DIR=/data/beifen/project/ #备份文件存储路径
LOGFILE=/data/beifen/project/data_project.log #日记文件路径
DATE=`date '+%Y%m%d-%H%M'` #日期格式（作为文件名）

MAIN_SITE_DIR=/home/wwwroot/     #启瀚艺考主站的项目路径
MAIN_ARCHIVE='all-qwqihan'-$DATE.tgz #压缩文件名

SEO_SITE_DIR=/data/website/qihanvideo/seo/     #seo频道的项目路径
SEO_ARCHIVE='all-website-explore'-$DATE.tgz    #seo频道压缩文件名


#判断备份文件存储目录是否存在，否则创建该目录
if [ ! -d $BACKUP_DIR ] ;
then
        mkdir -p "$BACKUP_DIR"
fi

#开始备份之前，将备份信息头写入日记文件
echo "———————————————–" >> $LOGFILE
echo "BACKUP DATE:" $(date +"%y-%m-%d %H:%M:%S") >> $LOGFILE
echo "———————————————– " >> $LOGFILE

# copy www.qihanyikao.com
cd $BACKUP_DIR        //进入到备份文件存放的目录
QWQIHAN=qwqihan/
M_QWQIHAN=m-qwqihan/
tar -zcvf $MAIN_ARCHIVE $MAIN_SITE_DIR$QWQIHAN $MAIN_SITE_DIRM_QWQIHAN >/dev/null 2>&1
if [[ $? == 0 ]]; then
    echo "[$MAIN_ARCHIVE] Backup Successful!" >> $LOGFILE
else
    echo "main website backup fail" >> $LOGFILE
fi


# copy explore seo
WEBSITE_SEO=website/
M_WEBSITE_SEO=m-website/
tar -zcvf $SEO_ARCHIVE $SEO_SITE_DIR$WEBSITE_SEO $SEO_SITE_DIR$M_WEBSITE_SEO >/dev/null 2>&1
if [[ $? == 0 ]]; then
    echo "[$SEO_ARCHIVE] Backup Successful!" >> $LOGFILE
else
    echo "main website backup fail" >> $LOGFILE
fi

