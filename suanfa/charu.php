<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/3/20
 * Time: 15:07
 */
$starttime = microtime(true);
//生成不重复的随机数
function uniqueRand($min, $max, $num){
    if(($min > $max) || $num <=0){
        return false;
    }

    $count = 0;
    $returnArr = [];
    while($count < $num){
        $i = mt_rand($min,$max);
        array_push($returnArr,$i);
        $returnArr = array_flip(array_flip($returnArr));
        $count = count($returnArr);
    }
    shuffle($returnArr);    //按随机顺序重新排列,重新分配键名
    //$returnArr = array_values($returnArr);
    //$returnArr = array_merge($returnArr);
    return $returnArr;
}

//插入排序
function charu($arr){
    $count = count($arr);
    if($count <= 1){
        return $count;
    }

    for($i=1; $i < $count; ++$i){  //外层循环是未排序区间
        $value = $arr[$i];
        $j = $i-1;
        for(; $j >=0; --$j){   //内层循环是已排序区间和待排序数的依次比较
            if($arr[$j] > $value){
                $arr[$j+1] = $arr[$j];     //数据交换
            }else{
                break;
            }
        }
        $arr[$j+1] = $value;     //插入数据
    }
    return $arr;
}

//$arr = [4,6,5,1,2,3,11];
$arr = uniqueRand(1,1000,200);
//echo json_encode(charu($arr));
charu($arr);
$endtime = microtime(true);
echo "执行时间：",($endtime-$starttime)*1000;