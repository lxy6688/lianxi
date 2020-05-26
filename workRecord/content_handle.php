<?php
/**
 * 爬虫的content正文数据对p标签的处理
 *
 * Created by PhpStorm.
 * User: yang
 * Date: 2019-08-11
 * Time: 14:27
 */
//$content = "<p>合作详情</p><p><span>这是合作详情</span></p><p>合作费用</p><p><span>100元撒大大大阿达的12220000元</span></p><p>合作支持</p><p><span>支持支持支持支持支持支持支持支持支持</span></p><p>合作流程</p><p><span>流程流程流程流程流程流程流程流程流程</span></p><p>合作详情</p><p><span>这是合作详情这是合作详情这是合作详情这是合作详情</span></p>";

$content = "
<p>合作详情</p><p><span>这是合作<p>合作减半详情</span></p>
<p>合作费用</p><p><span>100元撒大大大阿达的12220000元</span></p>
<p>合作支持</p><p><span>支持阿里烤鱼汤</span></p>
<p>合作流程</p><p><span>先交学费</span></p>
";

/**
 * 写一个新的stripos, $count参数表示字符串在第count次出现的位置
 **/
//function newstripos($str, $find, $count, $offset=0)
//{
//    $pos = strpos($str, $find, $offset);
//    $count--;
//    if ($count > 0 && $pos !== FALSE)
//    {
//        $pos = newstripos($str, $find ,$count, $pos+1);
//    }
//    return $pos;
//}

/**
 * 正则匹配字符串第$count 次匹配的位置, 这些待匹配的字符串不固定,但是有相同的特征
 */
function newmatchs($str, $find, $count)
{
    $offset = 0;
    for($i=0; $i < $count; $i++){
        $pos = preg_match($find,$str,$m,PREG_OFFSET_CAPTURE, $offset);
        if($pos !== 0) {
            $offset = $m[0][1] + 3;
        }
    }
    return (isset($m[0][1]))? $m[0][1] : false;
}


//删除换行符
$content = str_replace(PHP_EOL,'',$content);
$content_arr = [];
$search_field = "/<p>合作[\u{4E00}-\u{9FA5}]{2}<\/p>/u";   //匹配 <p>合作详情</p> 等
//$search_field = "<p>合作";
function content_handle($content,$count){
    global $search_field;
    global $content_arr;
    $num = newmatchs($content,$search_field,$count);
    //echo $num;exit;
    if($num === false){
        $title_key =str_replace("<p>",'',strchr($content, "</p>",true));
        $title_str = strchr($content, "</p>");
        $title_value = substr($title_str, intval(strpos($title_str,"</p>"))+4);
        $content_arr[$title_key] = $title_value;
        return ;
    }
    $content_child = substr($content,0,$num);
    //echo $content_child;exit;
    $title_key =str_replace("<p>",'',strchr($content_child, "</p>",true));
    $title_str = strchr($content_child, "</p>");
    $title_value = substr($title_str, intval(strpos($title_str,"</p>"))+4);
    $content_arr[$title_key] = $title_value;
    $content = substr($content,$num);
    //echo $content;
    content_handle($content,2);
}

content_handle($content,2);
print_r($content_arr);


/*
$num = newstripos($content,$search_field,2);
//echo $num;

$content_one = substr($content,0,$num);
echo $content_one;   //<p>合作详情</p><p><span>这是合作详情</span></p>
$numm = strchr($content_one, "</p>",true);
echo "合作标题: ".$numm."\n";

$neirong = strchr($content_one, "</p>");
echo "合作标题的内容: ".$neirong;


$content = substr($content,$num);
echo "\n";

echo $content;
echo "--------------------\n";


$num = newstripos($content,$search_field,2);
var_dump($num);
$a = substr($content,0,$num);
var_dump($a);
echo "\n";
//$content = substr($content,$num);  //$num 是false,就不必再截取了，这就是最后的一段内容了
var_dump($content);
*/

