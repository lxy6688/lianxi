<?php
/**
 * 网络图片文字识别：  http://ai.baidu.com/docs#/OCR-PHP-SDK/cf54a57f
 *https://www.jianshu.com/p/7905d3b12104   一篇关于百度ocr的博客
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/31
 * Time: 14:54
 */
require_once 'vendor/AipOcr.php';

// 你的 APPID AK SK
const APP_ID = '16733768';
const API_KEY = 'wTM3i8OFkUIXwUU3tqAtfzMt';
const SECRET_KEY = 'juSjRe4ZUOs0NgxLqXs0RGWXQafeNqqU';

$client = new AipOcr(APP_ID, API_KEY, SECRET_KEY);


$image = file_get_contents('test.png');
// 调用网络图片文字识别, 图片参数为本地图片
$client->webImage($image);

// 如果有可选参数
$options = array();
$options["detect_direction"] = "true";
$options["detect_language"] = "true";

// 带参数调用网络图片文字识别, 图片参数为本地图片
$rest = $client->webImage($image, $options);
var_dump($rest);




//$url = "http://007dir.oss-cn-shanghai.aliyuncs.com/2019-08-01/da2ff8d97bd84666e3cfe78395db588fjpg";
//// 调用网络图片文字识别, 图片参数为远程url图片
//$client->webImageUrl($url);
//// 如果有可选参数
//$options = array();
//$options["detect_direction"] = "true";
//$options["detect_language"] = "true";
//// 带参数调用网络图片文字识别, 图片参数为远程url图片
//$rest = $client->webImageUrl($url, $options);
//print_r($rest);
