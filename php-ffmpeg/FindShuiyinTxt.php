<?php
/** 遍历json文件，找出视频的详情页url，放到txt中
 *
 * User: lxy
 * Date: 2019/12/27
 * Time: 15:45
 */
header("Content-Type: text/html;charset=utf-8");
ini_set("memory_limit", "512M");
set_time_limit(0);
date_default_timezone_set('PRC');

$data_path = '/video/data/bilibili/';
$handle = opendir($data_path);
$i = 0;
if ($handle) {
    while (($json = readdir($handle)) !== false) {
        $jsonPath = $data_path.$json;
        //如果不加  $fl!='.' && $fl != '..'  则会造成把$dir的父级目录也读取出来
        if (!is_dir($jsonPath) && $json!='.' && $json != '..') {
            //判断当前的文件是否是json文件
            if(false === strpos($json, 'json')) {
                continue;
            }
            //判断当前的文件是否是无水印的文件
            $json_string = file_get_contents($jsonPath);
            $result = json_decode($json_string, true);
            if(!$result){
                //echo $jsonPath."json文件解析错误!\n";
                continue;
            }
            $title = $result['titel'];  //视频标题
            if(!preg_match("/凯文先生/i" ,$title)) {
                continue;
            }

            $author = $result['author'];  //作者名称
            $description = $result['description'];  //视频描述
            $tag = $result['tag'];  //视频标签
            $video_review = $result['video_review'];  //播放量
            $authorPic = $result['authorPic'];  //作者头像地址
            $url = $result['url'];  //视频详情页url

            echo $url."\n";
        }
    }
}

closedir($handle);

