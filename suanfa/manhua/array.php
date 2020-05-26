<?php
/**
 * 漫画算法  数组array 相关算法
 */


/**
 * 在数组指定位置插入数据
 */
$arr = [1,2,3,4,5];
function insert($array,$element, $index){
    $size = count($array);
    //判断边界条件
    if($index < 0 || $index > $size) {
        return -1;
    }

    //从右向左循环, 将元素逐个向右挪一位
    for($i=$size-1; $i>=$index; $i--) {
        $array[$i+1] = $array[$i];
    }

    $array[$index] = $element;

    return $array;
}
print_r(insert($arr, 9,3));