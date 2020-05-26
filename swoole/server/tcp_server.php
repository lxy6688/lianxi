<?php
//创建swoole对象，监听ip和端口
$serv = new swoole_server('127.0.0.1', 9501);

$serv->set([
    'worker_num' => 4,
    'daemonize'  => false
]);

//监听连接，进入事件
$serv->on("connect", function ($serv, $fd, $from_id) {
    echo "Client: {$fd}-connected-{$from_id}";
});

//监听数据接收事件
$serv->on("receive", function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "Server:" . $data);
});

//监听关闭事件
$serv->on("close", function ($serv, $fd, $from_id) {
    echo "Client: {$fd}-closed-{$from_id}";
});

//启动服务器
$serv->start();