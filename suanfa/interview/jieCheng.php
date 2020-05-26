<?php
/**
 * 求阶乘 n! 及其变种
 *
 * php的性能分析工具
 * https://www.jb51.net/article/29065.htm
 * https://www.cnblogs.com/yulibostu/articles/9788153.html
 *
 * 5! = 5*4*3*2*1
 */

/**
 * 递归法求n!
 */
function factory($n) {
    if($n == 0 || $n ==1) {
        return 1;
    }
    $take = $n * factory($n-1);
    return $take;
}

//$re = factory(10);
//var_dump($re);


/**
 * 非递归法求n!的阶乘
 */
function noFactory($n) {
    $n = intval($n);
    if($n == 0 || $n ==1) {
        return 1;
    }

    $take = 1;
    for($i=$n; $i > 0; $i--) {
        $take = $take * $i;
    }

    return $take;
}

//$res = noFactory(20);
//var_dump($res);

/***************** 阶乘结果中，尾数0的个数***************************/

/**
 * 求阶乘后,有多少个尾随0
 * 通过计算5的个数，得到0 的个数
 */
function tailZeroNums($n) {
    if($n < 5) {
        return 0;
    }

    $num = 0;
    $k = 5;
    while($k <= $n) {
        $num += $n/$k;
        $k = $k *5;      //5的倍数，也要计算
    }

    return $num;
}

//$zerosNum = tailZeroNums(10);
//var_dump($zerosNum);

function tailZeroNumsTwo($n) {
    $n = intval($n);
    if($n == 0 || $n ==1) {
        return 1;
    }

    $take = 1;
    for($i=$n; $i > 0; $i--) {
        $take = $take * $i;
    }

    //循环int结果，取0的个数，但是不能用foreach，只能用for循环长度，然后substr截取
    $num = 0;
    //$take = strval($take);
    $len = strlen($take);
    for($i = 0; $i < $len; $i++) {
        $intChar = substr($take,$i,1);
        if($intChar == 0) {
            $num++;
        }
    }

    return $num;
}
//$zerosNumTwo = tailZeroNumsTwo(10);
//var_dump($zerosNumTwo);




