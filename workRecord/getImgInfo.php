<?php
/**
 * 下载网络图片到本地, 并获取图片的尺寸等基本信息
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/17
 * Time: 15:50
 */
$dir = "./a.jpg";
$url = "https://cdn.007dir.cn/wp-content/uploads/1571218751.45521215722128.jpg";
file_put_contents($dir , file_get_contents($url));

$size = getimagesize($dir);
unlink($dir);
print_r($size);