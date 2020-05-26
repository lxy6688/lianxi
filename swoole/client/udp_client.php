<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/3/26
 * Time: 18:10
 */

//创建udp客户端
$client = new swoole_client(SWOOLE_SOCK_UDP);

$client->sendto('127.0.0.1', 9502, 'hello');

$result = $client->recv();
echo $result;