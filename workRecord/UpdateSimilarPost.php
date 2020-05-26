<?php
/**
 * 更新tags标签到相似推荐库
 *
 */
header("Content-Type: text/json;charset=utf-8");
ini_set("memory_limit", "1024M");
set_time_limit(0);
require_once "./DaoMysqli.php";
$params = [
    "host" => "",
    "user" => "",
    "password" => '',
    "dbName" => ""
];
$daoMysqli = DaoMysqli::getInstance($params);
//已发布文章的id
$res =  $daoMysqli->getProjectsByPublish();
foreach ($res as $value) {
    $tagArr = [];
    $id = $value->ID;
    $tagsObj = $daoMysqli->getTermsTagById($id);
    if(empty($tagsObj)) {
        echo "ID为：".$id."的post 没有terms tag\n";
        continue;
    }
    foreach($tagsObj as $value) {
        $tagArr[] = $value->name;
    }
    $tags = sp_get_tag_terms($tagArr);
    //分隔处理后的tags更新/插入到simialr_posts表
    $resp = $daoMysqli->updateInsertTagsToSimilar($id,$tags);
    if($resp === true){
        echo "ID为: ".$id." success\n";
    }else{
        echo "ID为: ".$id." false\n";
        echo "sql error: ".$resp."\n";
    }
}

function sp_get_tag_terms($tags, $utf8 = true) {
    $respTags = '';
    if ($utf8) {
        mb_internal_encoding('UTF-8');
        foreach ($tags as $tag) {
            $newtags[] = sp_mb_str_pad(mb_strtolower(str_replace('"', "'", $tag)), 4, '_');
        }
    } else {
        foreach ($tags as $tag) {
            $newtags[] = str_pad(strtolower(str_replace('"', "'", $tag)), 4, '_');
        }
    }
    $newtags = str_replace(' ', '_', $newtags);
    return  implode (' ', $newtags);
}

function sp_mb_str_pad($text, $n, $c) {
    mb_internal_encoding('UTF-8');
    $l = mb_strlen($text);
    if ($l > 0 && $l < $n) {
        $text .= str_repeat($c, $n-$l);
    }
    return $text;
}
