<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/4/9
 * Time: 17:52
 */
class WebsocketServerOop {
    private $ip;
    private $host;
    public  $server = null;

    function __construct($ip, $host){
        $this->ip = $ip;
        $this->host = $host;

        $this->server = new swoole_websocket_server($this->ip,$this->host);

        //设置静态资源访问
        $this->server->set([
            "enable_static_handler" => true,
            "document_root" => "/home/wwwroot/swoole/public",  //静态资源放置根路径
            "worker_num" => 2,
            "task_worker_num" => 2
        ]);

        $this->server->on('open', [$this,'onOpen']);
        $this->server->on('message', [$this,'onMessage']);
        $this->server->on('close', [$this,'onClose']);
        $this->server->on('task', [$this, 'onTask']);
        $this->server->on('finish', [$this, 'onFinish']);
        $this->server->start();
    }

    /** 监听websocket连接打开事件, open事件不是必须的
     * @param $ws
     * @param $request
     */
    public function onOpen($ws, $request){      //客户端http请求握手信息
        var_dump($request->fd, $request->get, $request->server);
        $ws->push($request->fd, "Hello Wecome!\n");

        $time = swoole_timer_tick(2000, function($timeId){   //每隔2s执行一次
            echo "2s timer: {$timeId}\n";
        });
        echo "time-id:",$time,"\n";    //定时器的id
    }

    public function onMessage($ws, $frame){    //$frame 客户端发来的数据帧消息
        echo "message:",$frame->data,"\n";

        //在push数据到客户端之前, 执行耗时的操作, 投递给task进程
        $data=[
            'id' => 1,
            'name' => 'yang'
        ];
        //$ws->task($data);   //把任务投递给task进程

        //5s之后执行定时器的操作，定时器是异步的
        swoole_timer_after(5000, function() use ($ws, $frame){
           $ws->push($frame->fd, "server-timer_after:");
        });

        $ws->push($frame->fd, "server: {$frame->data}\n");
    }

    /**
     * @param $server
     * @param $taskId
     * @param $workerId
     * @param $data
     * @return string
     */
    public function onTask($server, $taskId, $workerId, $data){
        sleep(10);
        return 'finished!';
    }

    /**
     * @param $server
     * @param $taskId
     * @param $data
     */
    public function onFinish($server, $taskId, $data){
        echo "taskId:",$taskId,"\n";
        echo "return data:",$data,"\n";
    }

    public function onClose($ws, $fd){
        echo "client-",$fd,"is closed\n";
    }
}

new WebsocketServerOop('192.168.136.134', 9502);