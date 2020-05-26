<?php
/**
 * 用程序进行两个大数的相加
 *
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/7/14
 * Time: 14:46
 */
function getSum($a,$b){
    if(!is_numeric($a) || !is_numeric($b)){
        return false;
    }
    $lenA = strlen($a);
    $lenB = strlen($b);

    $aa = strrev($a);
    $bb = strrev($b);

    $res = '';
    $flag = 0;

    if($lenA > $lenB){
        $high = $lenA;
        $low = $lenB;

        $rre = $aa;
    }else{
        $high = $lenB;
        $low = $lenA;

        $rre = $bb;
    }

    for($i=0; $i < $high; $i++){
        if($i >= $low){
            $res[$i] = $rre[$i];
            continue;
        }
        $sum = intval($aa[$i])+intval($bb[$i])+$flag;   //这里如果不用intval( )转一下，会报  Warning: A non-numeric value encountered 的错误
        if($sum >= 10){
            $res[$i] = $sum - 10;
            $flag = 1;
        }else{
            $res[$i] = $sum;
            $flag = 0;
        }
    }
    return strrev($res);
}
$a = 1112223232323223;
$b = 222666666666666;

echo getSum($a,$b);