<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/3/30
 * Time: 11:33
 */

//创建websocket服务器
$ws = new swoole_websocket_server('192.168.136.134',9502);

//配置可以处理静态文件
$ws->set([
    "enable_static_handler" => true,
    "document_root" => "/home/wwwroot/swoole/public"
]);

//监听websocket连接打开事件, open事件不是必须的
$ws->on('open','onOpen');   //这里不用匿名函数，可以写一个函数名
function onOpen($ws, $request){      //客户端http请求握手信息
    var_dump($request->fd, $request->get, $request->server);
    $ws->push($request->fd, "Hello Wecome!\n");
}

//监听websocket消息事件，message事件是必须的
$ws->on('message', function($ws, $frame){   //$frame 客户端发来的数据帧消息
    echo "message:",$frame->data,"\n";
    $ws->push($frame->fd, "server: {$frame->data}\n");
});

//设置onRequest回调，可以处理http请求信息
$ws->on('request', function(Swoole\Http\Request $request, Swoole\Http\Response $response){
    global $ws;
    //$ws->connections遍历所有websocket连接用户的fd，给所有用户推送
//    foreach($ws->connections as $fd){
//        $ws->push($fd, $request->get['message']);
//    }
});


//监听websocket连接关闭事件
$ws->on('close', function($ws, $fd){
    echo "client-",$fd,"is closed\n";
});

$ws->start();


//以上的代码是面向过程的代码，一般来说，我们通过面向对象的方式来优化代码