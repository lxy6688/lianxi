<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/26
 * Time: 15:35
 */
$host = '127.0.0.1'; //主机
$user = 'root'; //用户名
$password = '12345678'; //密码
$db_name = 'wordpress'; //数据库名
$port = '3306'; //端口

//使用面向对象进行数据库的连接，在创建对象的时候就自动的连接数据
$mySQLi = new MySQLi($host, $user, $password, $db_name, $port);
//判断数据库是否连接
if($mySQLi -> connect_errno){
    die('连接错误' . $mySQLi -> connect_error);
}
//设置字符集
$mySQLi -> set_charset('utf8');
//编写sql语句并执行
$sql = "select * from wp_user_info order by post_date desc";

//发送sql语句并执行，如果是select语句，返回的是一个对象，其他的返回来一个boolean.
$res = $mySQLi -> query($sql);

$resp = [
    'code' => -1
];
if(empty($res)){
    $mySQLi -> close();
    echo json_encode($resp);
    exit;
}
$dataArr = [];
while($row = $res -> fetch_object()){
    $dataArr[] = [
        'ID'    => $row->post_id,
        'title' => $row->title,
        'name'  => $row->name,
        'cate'  => $row->cate,
        'zcate'  => $row->zcate,
        'message'  => $row->message,
        'phone'  => $row->phone,
        'post_date'  => $row->post_date,
        'device' => $row->device
    ];

}
$mySQLi -> close();
$resp['code'] = 200;
$resp['data'] = $dataArr;
echo json_encode($resp);
exit;