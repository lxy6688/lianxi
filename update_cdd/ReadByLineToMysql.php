<?php
/**
 * 逐行读取txt文本内容,并插入到数据库中
 *
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/19
 * Time: 16:50
 */
require_once "./DaoMysqli.php";

class ReadByLineToMysql {
    public $params = [
        "host" => "127.0.0.1",
        "user" => "",
        "password" => '',
        "dbName" => ""
    ];
    public $file = '/data/cron/update_cdd/white_tags_link.txt';
    public $daoMysqli = null;

    public function __construct(){
        $this->daoMysqli = DaoMysqli::getInstance($this->params);
    }

    /**
     * tag页面缓存 (有链接的tag导入wp_terms表中，使wp缓存tag页面)
     */
    public function loadTagsToTerms(){
        $handler = fopen($this->file,'r'); //打开文件
        while(!feof($handler)){
            $lineData = fgets($handler,4096);
            $title = trim($lineData);
            $res =  $this->daoMysqli->insertWhiteTagsToTerms($title);
            if(!$res) {
                echo $title."插入false\n";
            }

        }
        fclose($handler); //关闭文件
    }
}
$obj = new ReadByLineToMysql();
$obj->loadTagsToTerms();





/*
$file = '/data/cron/update_cdd/white_tags_link.txt';

//$file = '/data/cron/update_cdd/title_stop_words_cdd.txt';
$handler = fopen($file,'r'); //打开文件

while(!feof($handler)){
    $lineData = fgets($handler,4096);
    $title = trim($lineData);
    $res =  $daoMysqli->insertWhiteTags($title);
    //$res =  $daoMysqli->insertStopWordsOfTitle($title);
    if(!$res) {
        echo $title."插入false\n";
    }

}
fclose($handler); //关闭文件
*/
