<?php
/** 面向对象的方式创建 http server
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/4/9
 * Time: 17:14
 */
class HttpServerOop {
    private $ip;
    private $host;
    public  $server = null;

    function __construct($ip, $host){
        $this->ip = $ip;
        $this->host = $host;

        $this->server = new swoole_http_server($this->ip,$this->host);

        //设置静态资源访问
        $this->server->set([
            "enable_static_handler" => true,
            "document_root" => "/home/wwwroot/swoole/public",  //静态资源放置根路径
            "worker_num" => 2,
            "task_worker_num" => 2
        ]);

        $this->server->on('request', [$this,'onRequest']);
        $this->server->on('task', [$this, 'onTask']);
        $this->server->on('finish', [$this, 'onFinish']);
        $this->server->on('close', [$this, 'onClose']);
        $this->server->start();
    }

    public function onRequest($request, $response){
        //设置cookie
        $response->cookie("singwa", "xssss", time()+3600);

        //异步处理task任务进程，异步执行耗时的操作,比如是10s
        $data=[
          'id' => 1,
          'name' => 'yang'
        ];
        $this->server->task($data);  //当前worker进程把这个耗时的操作投递过task进程，worker进程继续往下执行

        //$response->end()输出内容到浏览器,end中参数必须是字符串,end()只会执行一次
        $response->end("hello as");

        //打印请求参数
        //print_r($_GET);  这样是获取不到了，因为$_GET已经封装到$request对象里了
        print_r($request->get);
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

    public function onClose($server, $fd){
        echo "client-",$fd,"is closed\n";
    }

}

new HttpServerOop('192.168.136.134', 8811);