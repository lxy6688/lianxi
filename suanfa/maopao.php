<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/3/20
 * Time: 11:32
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


function  maopao2($arr){
    $count = count($arr);
    if($count <= 1){
        return $arr;
    }

    $num = 0;
    for($i=0; $i<$count; $i++){
        //提前退出冒泡循环的标志位
        $flag = false;
        for($j=$count-1; $j>$i; $j--){
            if($arr[$j] < $arr[$j-1]){
                $tmp = $arr[$j];
                $arr[$j] = $arr[$j-1];
                $arr[$j-1] = $tmp;

                $flag = true;
            }
        }

        //如果没有数据交换，说明已排好序，不再继续循环
        if(!$flag) {
            echo "循环次数: ",$num,"\n";
            break;
        };
        $num++;
    }
    return $arr;
}

//$arr = [4,5,6,3,2,1];
$arr = uniqueRand(1,1000,200);
echo json_encode(maopao2($arr)),"\n";
$endtime = microtime(true);
echo "执行时间：",($endtime-$starttime)*1000;