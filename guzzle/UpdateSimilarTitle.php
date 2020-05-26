<?php
/**批量更新 相似推荐表 wp_similar_posts 的 title分词字段 (先分词后过滤)
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/20
 * Time: 11:13
 */
header("Content-Type: text/json;charset=utf-8");
ini_set("memory_limit", "1024M");
set_time_limit(0);

require_once "./DaoMysqli.php";
$params = [
    "host" => "127.0.0.1",
    "user" => "",
    "password" => '',
    "dbName" => ""
];

//先获取已发布的项目
$daoMysqli = DaoMysqli::getInstance($params);
$res =  $daoMysqli->getProjectsByPublish();
if(empty($res)) {
    echo '没有已发布的项目';
    exit;
}

$rest = '';
foreach($res as $value){
    $post_title = $value->post_title;
    $ID = $value->ID;

    $title = sp_get_title_terms($post_title);
    $title = filter_stopwords($title,$daoMysqli);
    //分隔处理后的title 更新/插入到simialr_posts表
    $resp = $daoMysqli->updateInsertTitlesToSimilar($ID,$title);
    if($resp === true){
        echo "ID为: ".$ID." success\n";
    }else{
        echo "ID为: ".$ID." false\n";
        echo "sql error: ".$resp."\n";
    }
}


function sp_get_title_terms($text) {
    $wordlist = mb_split("\W+", sp_mb_clean_words($text));
    $words = '';
    foreach ($wordlist as $word) {
        if (!isset($tinywords[$word])) {
            $words .= sp_mb_str_pad($word, 4, '_') . ' ';
        }
    }

    $words = sp_cjk_digrams($words);
    return $words;
}


//过滤掉标题的停止词
function filter_stopwords($title,$daoMysqli){
    $result = $daoMysqli->getTitleStopWords();
    if(empty($result)) {
        return $title;
    }

    $titleArr = [];
    foreach($result as $obj){
        $titleArr[] = $obj->name;
    }

    $titArr = mb_split("\W+", trim($title));
    foreach($titArr as $key => $str){
        $finalStr =  trim(trim($str,'_'));   //删除两边所有的下划线_和空格
        if(in_array($finalStr,$titleArr)){
            unset($titArr[$key]);
        }
    }

    $finalTitle = '';
    foreach($titArr as $value){
        $finalTitle .= $value." ";
    }
    return $finalTitle;
}

function sp_mb_clean_words($text) {
    mb_regex_encoding('UTF-8');
    mb_internal_encoding('UTF-8');
    $text = strip_tags($text);
    $text = mb_strtolower($text);
    $text = str_replace("’", "'", $text); // convert MSWord apostrophe
    $text = preg_replace(array('/\[(.*?)\]/u', '/&[^\s;]+;/u', '/‘|’|—|“|”|–|…/u', "/'\W/u"), ' ', $text); //anything in [..] or any entities
    return 	$text;
}

function sp_mb_str_pad($text, $n, $c) {
    mb_internal_encoding('UTF-8');
    $l = mb_strlen($text);
    if ($l > 0 && $l < $n) {
        $text .= str_repeat($c, $n-$l);
    }
    return $text;
}

function sp_cjk_digrams($string) {
    mb_internal_encoding("UTF-8");
    $strlen = mb_strlen($string);
    $ascii = '';
    $prev = '';
    $result = array();
    for ($i = 0; $i < $strlen; $i++) {
        $c = mb_substr($string, $i, 1);
        // single-byte chars get combined
        if (strlen($c) > 1) {
            if ($ascii) {
                $result[] = $ascii;
                $ascii = '';
                $prev = $c;
            } else {
                $result[] = sp_mb_str_pad($prev.$c, 4, '_');
                $prev = $c;
            }
        } else {
            $ascii .= $c;
        }
    }
    if ($ascii) $result[] = $ascii;
    return implode(' ', $result);
}