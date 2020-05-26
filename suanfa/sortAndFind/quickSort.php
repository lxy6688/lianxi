<?php
/**
 * 快速排序
 */

/**
 *递归法实现快速排序
 *
 * @param $arr
 */
function quickSortDigui($arr){
    $count = count($arr);
    if($count <= 1) {
        return $arr;
    }

    $leftArr = $rightArr = [];
    $mid = $arr[0];                       //假设下标为0的值，作为中间值
    for($i = 1; $i < $count; $i++) {      //接着循环，从下标1开始
        if($arr[$i] <= $mid) {
            array_push($leftArr, $arr[$i]);
        }else{
            array_push($rightArr, $arr[$i]);
        }
    }

    $leftArr = quickSortDigui($leftArr);
    $rightArr = quickSortDigui($rightArr);
    return array_merge($leftArr,[$mid],$rightArr);
}

/**
 * 迭代法实现快排
 * https://blog.csdn.net/lingfeng2019/article/details/71124933?utm_source=blogkpcl4
 *
 * @param $arr
 * @return mixed
 */
function quickSortIteration($arr){
    $count = count($arr);
    if($count <= 1) {
        return $arr;
    }

    $low = 0;
    $high = $count-1;

    $stack = [];


    $temp = '';
    $p = $arr[($low+$high)/2];

}

$arr = [2,3,1,11,4,15,21,10,22];
$res = quickSortDigui($arr);
echo json_encode($res);