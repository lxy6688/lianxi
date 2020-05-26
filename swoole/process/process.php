<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/4/11
 * Time: 10:49
 */

//创建子进程并在子进程中创建server
$process = new swoole_process('callback', true);  //true 表示在子进程中输出的内容 不打印到终端

$pid = $process->start();
echo "子进程pid：",$pid.PHP_EOL;

function callback(swoole_process $worker){
    //在子进程中开启http server服务
    $worker->exec('/home/work/soft/php7/bin/php', [__DIR__.'/../server/HttpServerOop.php']);
}

swoole_process::wait();   //回收结束的子进程