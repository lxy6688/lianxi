<?php
//判断回文字符串
function isHuiWen($str = ''){
    $len = strlen($str);
    if($len == 0){
        return false;
    }
    $flag = true;

    $k = intdiv($len,2)+1;
    for($i=0;$i<$k;$i++){
        if(substr($str,$i,1) !== substr($str,$len-$i-1,1)){
            $flag = false;
            break;
        }
    }

    if($flag){
        return '是回文';
    }
    return '不是回文';
}

//$aa = 1234321;   也可以判断简单的数字
$aa = 'abba';
echo isHuiWen($aa);