<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/3/29
 * Time: 15:43
 */
//创建http server
$http = new swoole_http_server('192.168.136.134',8811);

//设置静态资源访问
$http->set([
    "enable_static_handler" => true,  //如果访问静态资源存在，直接输出,就不会再走下面的response,如果不存在,继续往下执行response
    "document_root" => "/home/wwwroot/swoole/public"  //静态资源放置根路径
]);

$http->on('request', function($request, $response){
    //设置cookie
    $response->cookie("singwa", "xssss", time()+3600);

    //$response->end()输出内容到浏览器,end中参数必须是字符串,end()只会执行一次
    $response->end("hello as");

    //打印请求参数
    //print_r($_GET);  这样是获取不到了，因为$_GET已经封装到$request对象里了
    print_r($request->get);
});

$http->start();