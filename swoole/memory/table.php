<?php
/**
 * swoole_table  共享内存表
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/4/16
 * Time: 9:36
 */
//创建内存表
$table = new swoole_table(1024);

//内存表增加列
$table->column('id',$table::TYPE_INT,4);
$table->column('name',$table::TYPE_STRING,64);
$table->column('age',$table::TYPE_INT,3);

//创建内存表
$table->create();

//插入内存表数据，类似redis的方式去set
$table->set("name",['id'=>1, 'name' => 'yang', 'age' => 30]);

//table表中的column自增
$table->incr("name", "age", 2);   //age自增2   自减是decr()

//删除数据
//$table->del("name");

//以get的方式去获取
print_r($table->get("name")).PHP_EOL;

//获取表中的行数
echo "行数：",$table->count(),PHP_EOL;
