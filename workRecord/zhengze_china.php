<?php
$content = "
<p>合作详情</p><p><span>这是合作<p>合作减半详情</span></p>
<p>合作费用</p><p><span>100元撒大大大阿达的12220000元</span></p>
<p>合作支持</p><p><span>支持阿里烤鱼汤</span></p>
<p>合作流程</p><p><span>先交学费</span></p>
";

$content = str_replace(PHP_EOL,'',$content);


$search_field = "/<p>合作[\u{4E00}-\u{9FA5}]{2}<\/p>/u";  //匹配合作详情等
//$search_field = "/[\u{4E00}-\u{9FA5}]+/u";  匹配中文
$a = preg_match($search_field,$content,$m);
//var_dump($m);




$search_field = "/<p>合作[\u{4E00}-\u{9FA5}]{2}<\/p>/u";
//$search_field = "/[\u{4E00}-\u{9FA5}]+/u";
$a = preg_match_all($search_field,$content,$m);
//var_dump($a);
print_r($m);
