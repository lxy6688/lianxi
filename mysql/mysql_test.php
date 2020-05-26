<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/30
 * Time: 16:04
 */
/* 数据库参数 */
$host = 'localhost'; //主机
$user = 'root'; //用户名
$password = '123456'; //密码
$db_name = 'wordpress'; //数据库名
$port = '3306'; //端口

/* 连接数据库 */
$link = mysqli_connect($host, $user, $password, $db_name, $port);
mysqli_query($link,"set character set 'utf8'");
mysqli_query($link,"set names 'utf8'");

$sql = "select term_id from wp_terms where name='餐饮'";
$yiji_result = mysqli_query($link,$sql)->fetch_assoc();
echo $yiji_result['term_id'];