<?php
/**
 * composer require guzzlehttp/guzzle //用composer安装最新guzzle，当前是6.3版
 *
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/4/11 * Time: 23:00
 */
require_once 'vendor/autoload.php';
//use GuzzleHttp\Client;

//初始化客户端
$client = new GuzzleHttp\Client(); //初始化客户端

$response = $client->get("http://httpbin.org/get",[
    'query' => [
        'a' => '参数a的值',
        'b' => '参数b的值'
    ],
    'timeout' => 3.14
]);

$body = $response->getBody();   //获取响应体，对象
$bodyStr = (string)$body;       //对象转字符串，请求的结果
echo $bodyStr;