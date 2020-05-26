<?php
/**
 * 输入字符串 123amb456kqa789req， 打印
    _123
    amb_456
    kqa_789
    req
 *
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/7/14
 * Time: 14:54
 */
function test($str){
    $len = strlen($str);
    $res = "_";
    $strtest = '';
    for($i=0;$i<$len;$i++){                //循环每一个字符串去判断
        if(is_numeric($str[$i])){
            $res = $res.$str[$i];
            if($i == ($len-1)){
                echo $res.PHP_EOL;
            }elseif(!is_numeric($str[$i+1])){
                echo $res.PHP_EOL;
                $res = "_";
            }
        }else{
            //处理字母的情况
            $strtest = $strtest.$str[$i];
            if($i == ($len-1)){
                echo $strtest.PHP_EOL;
            }elseif(is_numeric($str[$i+1])){
                $res = $strtest.$res;
                $strtest = '';
            }
        }
    }
}

$str = "123amb456kqa789req";
//$str = "asc";
test($str);