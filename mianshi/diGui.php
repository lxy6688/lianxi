<?php

$array = array(
    array('id'=>1,'name'=>'电脑','pid'=>0),
    array('id'=>2,'name'=>'手机','pid'=>0),
    array('id'=>3,'name'=>'笔记本','pid'=>1),
    array('id'=>4,'name'=>'台式机','pid'=>1),
    array('id'=>5,'name'=>'智能机','pid'=>2),
    array('id'=>6,'name'=>'功能机','pid'=>2),
    array('id'=>7,'name'=>'超级本','pid'=>3),
    array('id'=>8,'name'=>'游戏本','pid'=>3),
);

/**
 * @param $arr 数组
 * @param $pid   父类id
 * @param $level  层级
 * @return array
 */
function demo($array,$pid = 0,$level = 0)
{
    $list = [];
    foreach ($array as $value){
        if ($value['pid'] == $pid){
            $value['level']=$level;
            $value['son'] = demo($array,$value['id'],$level+1);
            $list[] = $value;
        }
    }
    return $list;
}

//echo json_encode(demo($array));
print_r(demo($array));