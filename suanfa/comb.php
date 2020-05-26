<?php
/**
 * 从一组数字当中取出3个组成一组，列出所有组合,如：
 * 有10组数字，10个里随机选3个组成一组，然后列出所有组合
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/18
 * Time: 16:54
 */
ini_set("memory_limit", "2048M");
set_time_limit(0);

function combination($numArr){
    $numArr = array_filter($numArr);
    if(empty($numArr)) {
        return false;
    }
    $len = count($numArr);
    $temp = [];
    for($i=0;$i<$len;$i++) {
        array_push($temp, $numArr[$i]);
        for($j=$i+1;$j<=$len;$j++) {
            array_push($temp, $numArr[$j]);
            if(count($temp) == 3) {
                echo implode(',',$temp)."\n";
                //$a = $temp[1];
                unset($temp[2]);
                array_values($temp);
            }
            if($j == $len-1) {
                $j = $temp[1]+1;
                unset($temp[1]);
                unset($temp[2]);
                //$j = $a+1;
                array_values($temp);
            }
        }

        $temp = [];
    }
}

$arr = [1,2,3,4,5];
combination($arr);


function applyCombination($ar, $num) {
    $control = range(0, $num-1);
    $k = false;
    $total = count($ar);
    while($control[0] < $total-($num-1)) {
        $t = array();
        for($i=0; $i <$num; $i++) $t[] = $ar[$control[$i]];
        //$r[] = $t;
        echo implode('  ',$t)."\n";

        for($i=$num-1; $i>=0; $i--) {
            $control[$i]++;
            for($j=$i; $j <$num-1; $j++) $control[$j+1] = $control[$j]+1;
            if($control[$i] < $total-($num-$i-1)) break;
        }
    }
    //return $r;
}
//print_r(combination(array(1,2,3,4,5), 3));
//applyCombination(array(1,2,3,4,5,6), 5);

