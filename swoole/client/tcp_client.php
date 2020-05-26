<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/3/26
 * Time: 17:08
 */

//连接 swoole 客户端 TCP服务
$client = new swoole_client(SWOOLE_SOCK_TCP);

if(!$client->connect('127.0.0.1',9501)){
    echo "连接失败";
    exit;
}

//php  cli常量 等待用户输入
fwrite(STDOUT,"请输入消息：");
$msg = trim(fgets(STDIN));


if(!empty($msg)){
    //把消息发送给tcp服务端
    $client->send($msg);

    //接收来自server的数据
    $result = $client->recv();
    echo "response:",$result;
}

//linux命令查看启动的工作进程数量
//  ps  aft | grep tcp_server.php


