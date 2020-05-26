<?php
/**
 * swoole开启多个子进程，同时处理多任务
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/4/11
 * Time: 15:19
 */
class ProcessApply {
    private $urls = [
        "http://www.baidu.com",
        "http://www.sina.com.cn",
        "http://www.sohu.com",
        "http://www.163.com",
        "http://www.qq.com",
        "http://www.1688.com"
    ];

    public $workers = [];

    /**
     * 原生php以同步的方式访问url
     */
    public function sync(){
        $startTime = microtime(true);
        echo "同步开始时间：".$startTime.PHP_EOL;
        foreach($this->urls as $url){
            $params = file_get_contents($url);
        }
        $endtime = microtime(true);
        echo "同步处理结束时间：".$endtime.PHP_EOL;
        echo "同步耗时: ".(($endtime-$startTime)*1000).PHP_EOL;   //大约300ms
    }

    /**
     * swoole异步处理访问url
     */
    public function async(){
        $startTime = microtime(true);
        echo "异步开始时间：".$startTime.PHP_EOL;
        //开启六个子进程
        for($i=0; $i<6; $i++){
            $process = new swoole_process(function(swoole_process $worker) use($i) {
                //TODO 处理逻辑
                $content = file_get_contents($this->urls[$i]);
                echo $content.PHP_EOL;   //不会打印到终端，而是会进入进程间通道
                $worker->write($content.PHP_EOL);   //和echo的效果一样，这是标准的写法
            },true);
            $pid = $process->start();
            $this->workers[$pid] = $process;   //将进程id 和 进程内容关联在一起
        }
        $endtime = microtime(true);
        echo "异步处理结束时间：".$endtime.PHP_EOL;
        echo "异步耗时: ".(($endtime-$startTime)*1000).PHP_EOL;   // 大约 60ms

        //将进程处理的信息打印到终端
        foreach ($this->workers as $process){
            echo $process->read();    //读出进程管道间的数据，打印到终端
        }
    }
}

(new ProcessApply())->sync();
(new ProcessApply())->async();
