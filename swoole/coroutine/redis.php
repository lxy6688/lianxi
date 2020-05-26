<?php
/**
 * 协程Coroutine  redis的使用
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/4/16
 * Time: 10:25
 */
go(function(){      //用go关键字创建一个协程
    $redis = new Swoole\Coroutine\Redis();
    $redis->connect('127.0.0.1', 6379);
    echo $redis->get('aa');
});

//协程并发执行，比原生php同步代码执行效率要高
