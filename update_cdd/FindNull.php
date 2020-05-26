<?php
/**
 * 查找表中 正文内容没有合作详情的、项目头图为空的posts的ID and post_title
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/19
 * Time: 16:30
 */
header("Content-Type: text/html;charset=utf-8");
ini_set("memory_limit", "1024M");
set_time_limit(0);

require_once "./DaoMysqli.php";
$params = [
    "host" => "localhost",
    "user" => "root",
    "password" => '123456',
    "dbName" => "test"
];
$toutuIdArr = $contentIdArr = $allIdArr = [];
$toutuResArr = $contentResArr = $allResArr = [];
$daoMysqli = DaoMysqli::getInstance($params);
//统计头图为空的记录
$field = "ID,post_title,post_toutu,post_url";
$toutuRes = $daoMysqli->getField($field);
//print_r($toutuRes);
if(!empty($toutuRes) && is_array($toutuRes)) {
    foreach ($toutuRes as $value) {
        if(empty(json_decode($value->post_toutu,true))) {
            //array_push($toutuResArr,[$value['ID'],$value['post_title']]);
            array_push($toutuIdArr,$value->ID);
            echo "头图为空的 ID为：".$value->ID,"  标题为：".$value->post_title."  店铺链接：".$value->post_url."\n";
        }
    }
}

echo "\n\n\n";

//统计合作详情为空的记录
$field = "ID,post_title,post_content,post_url";
$contentRes = $daoMysqli->getField($field);
if(!empty($contentRes) && is_array($contentRes)) {
    foreach ($contentRes as $value) {
        if(!preg_match("/合作详情/i",$value->post_content)){
            //array_push($contentResArr,[$value['ID'],$value['post_title']]);
            array_push($contentIdArr,$value->ID);
            echo "合作详情为空的 ID为：".$value->ID,"标题为：".$value->post_title."  店铺链接：".$value->post_url."\n";
        }
    }
}

//统计头图和 合作详情都为空的记录
$allIdArr = array_intersect($toutuIdArr,$contentIdArr);
if(!empty($allIdArr)){
    foreach($allIdArr as $id) {
        $allRes = $daoMysqli->getListById($id);
        echo "头图和合作详情都为空的 ID为：".$id."   标题为：".$allRes['post_title']."   店铺链接：".$allRes['post_url']."\n";
    }
}else{
    echo "头图和合作详情都为空的 ID为 null";
}


