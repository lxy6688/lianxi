<?php
/**
 * 获取目录下的所有文件
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/1
 * Time: 10:06
 */
$dir = "./readfile";
if (is_dir($dir)){
    if ($dh = opendir($dir)){
        while (($file = readdir($dh)) !== false) {
            $direction = $dir.'/'.$file;
            if ($file != '.' && $file != '..') {
                //echo "filename:" . $file . "<br>";
                $json_string = file_get_contents($direction);
               echo $json_string;

            }
        }
        closedir($dh);
    }
}