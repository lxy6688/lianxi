<?php
/**
 * 获取 相似推荐表 wp_similar_posts的title字段，进行处理一定格式并输出到txt文本中
 * title字段示例：爵___ 爵士__ 士鼓__ 鼓技__ 技术__ 术讲__ 讲解__ 解之__ 之基__ 基本__ 本节__ 节奏__ 奏组__ 组合__
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/9
 * Time: 15:30
 */
header("Content-Type: text/html;charset=utf-8");
ini_set("memory_limit", "1024M");
set_time_limit(0);

require_once "./DaoMysqli.php";
$params = [
    "host" => "",
    "user" => "",
    "password" => '',
    "dbName" => ""
];
$toutuIdArr = $contentIdArr = $allIdArr = [];
$toutuResArr = $contentResArr = $allResArr = [];
$daoMysqli = DaoMysqli::getInstance($params);

$getRes = $daoMysqli->getSimilarTitles();
if(!empty($getRes) && is_array($getRes)) {
    foreach ($getRes as $value) {
        $fenciTitle = $value->title;
        $titArr = mb_split("\W+", trim($fenciTitle));
        foreach($titArr as $str){
            $finalStr =  trim($str,'_');   //删除两边所有的下划线_
            echo $value->ID."   ".$value->post_title."  ".$finalStr."\n";
        }
    }
}