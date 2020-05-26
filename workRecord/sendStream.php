<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/15
 * Time: 11:17
 */
/** php 发送流文件, 本地图片转成二进制流，再发送
 * @param  String  $url  接收的路径
 * @param  String  $file 要发送的文件
 * @return boolean
 */
//这个貌似有问题,再研究吧
//require_once '/data/cron/update_cdd/vendor/guzzle-http/vendor/autoload.php';
//$client = new GuzzleHttp\Client(); //初始化客户端
//
//$stream = '/root/711679.jpg';
//$body = \GuzzleHttp\Psr7\stream_for($stream);
//$response = $client->request('POST', 'http://192.168.62.128/UploadImg.php', [
//    'body' => $body,
//    'headers' => ['content-type'=> 'application/octet-stream', 'charset'=>'utf-8']
//    ]);
//$body = $response->getBody();
//echo $body;




function sendStreamFile($url, $file){
    if(file_exists($file)){
        $opts = array(
            'http' => array(
                'method' => 'POST',
                //'header' => 'content-type:application/x-www-form-urlencoded',
                'header' => 'content-type:multipart/form-data',
                'content' => file_get_contents($file)
            )
        );

        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);
        var_dump($response);
        //$ret = json_decode($response, true);
    }else{
        return false;
    }

}
sendStreamFile('http://192.168.62.128/UploadImg.php', '/root/711679.jpg');


/**
 * 标准表单方式上传图片
 * upload.htm
 *
 *  <form action="http://47.103.116.147/UploadImg.php" method="post" enctype="multipart/form-data">
    <label for="file">文件名：</label>
    <input type="file" name="file" value="test.png"><br>
    <input type="submit" name="submit" value="提交">
    </form>
 */

