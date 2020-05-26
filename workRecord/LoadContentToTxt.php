<?php
/**导出content内容,每10个为一组，写入不同的txt文本中
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/22
 * Time: 10:40
 */
header("Content-Type: text/json;charset=utf-8");
//ini_set("display_errors", 0);
ini_set("memory_limit", "1024M");
set_time_limit(0);
date_default_timezone_set( 'Asia/Shanghai' );

require_once "./DaoMysqli.php";
$params = [
    "host" => "127.0.0.1",
    "user" => "root",
    "password" => '12345678',
    "dbName" => "wordpress"
];
$txt = 1;
$i = 0;
$handle = false;
$daoMysqli = DaoMysqli::getInstance($params);
$contentResObj = $daoMysqli->getContentByStatus();
if(!empty($contentResObj)) {
    foreach ($contentResObj as $contentObj){
        $postContent = $contentObj->post_content;
        if($i == 10){     //每10条记录打印到一个txt文本中
            $i=0;
            fclose($handle);
            $txt++;
        }

        $dir = "./test/".$txt.'.txt';
        $handle = fopen($dir,'a+');         //以读写追加的方式打开
        chmod($dir,0777);
        fwrite($handle,$postContent."\n");
        $i++;
    }
}