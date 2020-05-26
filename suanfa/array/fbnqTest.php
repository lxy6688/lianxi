<?php
/**
 * 斐波那契数列练习
 */

/**
 * 迭代法实现
 * @param $n
 * @return bool
 */
function iteration($n){
    if(intval($n) <= 0) {
        return false;
    }

    $pre = $preRe = 1;
    $current = 0;
    for($i=1;$i<=$n;$i++) {
        if($i == 1 || $i == 2) {
            echo 1;
            continue;
        }

        $currennt = $pre + $preRe;
        echo $currennt;

        $preRe = $pre;
        $pre = $currennt;
    }
}
iteration(50);


//递归法实现
function digui($n){
    if(intval($n) <= 0) {
        return false;
    }
    if($n == 1 || $n ==2){
        return 1;
    }
    return digui($n-1)+digui($n-2);
}
//for($i=1;$i<=5;$i++) {
//    echo digui($i);
//}
//echo  digui(5);