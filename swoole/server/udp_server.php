<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/3/26
 * Time: 18:03
 */

//创建UDP服务器
$serv = new swoole_server('127.0.0.1', 9502, SWOOLE_PROCESS, SWOOLE_SOCK_UDP);

//不用建立连接, 直接监听数据接收
$serv->on("packet", function($serv, $data, $clientInfo){
    //发送给客户端的消息
    $serv->sendto($clientInfo['address'], $clientInfo['port'], "server:".$data);
    var_dump($clientInfo);
});

$serv->start();