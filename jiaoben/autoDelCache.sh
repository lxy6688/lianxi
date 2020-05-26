#!/bin/bash
#extract auto del wp cache
#author:lxy
#sed -i 's/\r$//' build.sh

PC_DETAIL_CACHE=/data/websites/cdd/new/cdd-seo/wp-content/cache/supercache/007dir.cn/  #cdd-pc的缓存路径
M_DETAIL_CACHE=/data/websites/cdd/new/m-cdd-seo/wp-content/cache/supercache/m.007dir.cn/  #cdd-m的缓存路径
echo "开始清理pc端缓存..."

if [ ! -d $PC_DETAIL_CACHE ]; then
    echo "cdd-pc的缓存路径不存在"
    exit
fi

cd $PC_DETAIL_CACHE
if [[ $1 == 'detail' ]]; then
    rm -rf ./projects/*
    echo "详情页缓存清理完成..."
elif [[ $1 == 'cate' ]]; then
    rm -rf ./category/*
    echo "分类页缓存清理完成..."
elif [[ $1 == 'dime' ]]; then
    rm -rf ./dimensions/*
    echo "tag页缓存清理完成"
else
    rm -rf ./projects/*
    rm -rf ./category/*
    rm -rf ./dimensions/*
fi
echo "pc端缓存清理完成..."


echo "====================="
echo "开始清理m端缓存..."

if [ ! -d $M_DETAIL_CACHE ]; then
    echo "cdd-m的缓存路径不存在"
    exit
fi

cd $M_DETAIL_CACHE
if [[ $1 == 'detail' ]]; then
    rm -rf ./projects/*
    echo "详情页缓存清理完成..."
elif [[ $1 == 'cate' ]]; then
    rm -rf ./category/*
    echo "分类页缓存清理完成..."
elif [[ $1 == 'dime' ]]; then
    rm -rf ./dimensions/*
    echo "tag页缓存清理完成"
else
    rm -rf ./projects/*
    rm -rf ./category/*
    rm -rf ./dimensions/*
fi
echo "m端缓存清理完成..."
