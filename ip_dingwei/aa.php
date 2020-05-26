<?php
/**
 * Ip2Region php client test script
 *
 * 参考git: https://github.com/shakenetwork/ip2region
 *
 * @author    chenxin<chenxin619315@gmail.com>
*/


$dbFile     = './ip2region.db';
$method     = 'btreeSearch';
$algorithm  = 'B-tree';

//require dirname(__FILE__) . '/Ip2Region.class.php';
require './Ip2Region.class.php';
$ip2regionObj = new Ip2Region($dbFile);

//$data   = $ip2regionObj->{$method}('183.136.168.78');
$data   = $ip2regionObj->{$method}('222.18.108.19');


var_dump($data);   //固定格式：城市Id|国家|区域|省份|城市|ISP   没有的返回0


//printf("%s|%s in %.5f millseconds\n", $data['city_id'], $data['region'], $c_time);
?>
