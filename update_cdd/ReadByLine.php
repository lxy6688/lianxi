<?php
/**
 * 逐行读取txt文本内容
 * 【参考】https://www.cnblogs.com/scriptlift/archive/2014/06/16/3790986.html
 * 【参考】https://blog.csdn.net/sinat_35861727/article/details/78056357
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/19
 * Time: 16:50
 */

require_once "./DaoMysqli.php";
$params = [
    "host" => "127.0.0.1",
    "user" => "root",
    "password" => '12345678',
    "dbName" => "wordpress_two"
];
$daoMysqli = DaoMysqli::getInstance($params);
$file = '/home/wwwroot/python/nlp_service/service/extra_dict/suggest_words_cdd.txt';
$handler = fopen($file,'r'); //打开文件

while(!feof($handler)){
    //$m[] = fgets($handler,4096); //fgets逐行读取，4096最大长度，默认为1024
    $lineData = fgets($handler,4096);
    $title = trim($lineData);
    $res =  $daoMysqli->getIdByTitle($title);
    if(!$res) {
        echo $title."\n";
    }

}

fclose($handler); //关闭文件

//输出文件
//print_r($m);