<?php
/**
 * 获取前三个p标签的内容
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/8
 * Time: 15:13
 */

$text = '
<p><img src="http://xxxxx">sad撒大大
多多撒</p>
<p><p><span>11刘晓阳1</span></p>
<p><span>222</span></p>
<p>333</p>
<p>444</p>
<p><img src="http://xxxsfdsfdsfsdfsxx"></p>
<p>666</p>
';

//$pattern = '/(<p>[A-z0-9]+<\/p>)/i';
$pattern = '/(<p>.*?<\/p>)/s';
//$pattern = '/((<p>)*(<span>)*[A-z0-9]+<\/span>*<\/p>)/i';

//$pattern = "/\bsrc\b\s*=\s*[\'\\\"]?([^\'\\\"]*)[\'\\\"]?/i";
/*$pattern = "/<img.*?src=\"(.*?)\".*?\/?>/i";    //匹配img标签*/

var_dump(array_slice(preg_get($pattern , $text),0,3));


function preg_get($pattern , $text)
{
    $out = array();
    preg_match_all( $pattern , $text, $out );
    return $out[1];
}



//$content = "hello world测试 三生三世测试";
//if(preg_match($pattern,$text,$m)){
//    echo $m[0]."\n";       //$m[0]表示 匹配出的全部内容, 即测试
//}



//删除第一个p标签
//$s='<p>第一段内容</p> dfg <p>第二段内容</p> ghi';
//$i=strpos($s,'<p>');
//$j=strpos($s,'</p>',$i);
//$s=substr($s,$j+4);
//echo $s;