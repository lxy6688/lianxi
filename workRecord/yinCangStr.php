<?php
/**
 * 隐藏部分字符串,可以是隐藏中文，也可以用来做手机号中间部分的隐藏
 *
 * @param $str
 * @param string $replacement
 * @param int $start
 * @param int $length
 * @return string
 */
function func_substr_replace($str, $replacement = '*', $start = 3, $length = 4){
	$len = mb_strlen($str,'utf-8');
	if ($len > intval($start+$length)) {
		$str1 = mb_substr($str,0,$start,'utf-8');
		$str2 = mb_substr($str,intval($start+$length),NULL,'utf-8');
	} else {
		$str1 = mb_substr($str,0,1,'utf-8');
		$str2 = mb_substr($str,$len-1,1,'utf-8');
		$length = $len - 2;
	}
	$new_str = $str1;
	for ($i = 0; $i < $length; $i++) {
		$new_str .= $replacement;
	}
	$new_str .= $str2;
	return $new_str;
}

$a = func_substr_replace('隐藏部分字符串啊哈!');
var_dump($a);
