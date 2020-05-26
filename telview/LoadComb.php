<?php
/**
 * php下载文件
 *
 * 参考： https://blog.csdn.net/change_any_time/article/details/79706772
 * 参考： https://www.jb51.net/article/161010.htm   php下载大文件(分段下载)
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/20
 * Time: 15:19
 */
header("Content-type:text/html;charset=utf-8");
ini_set("memory_limit", "2048M");
$file_name="load.txt";
//用以解决中文不能显示出来的问题
$file_name=iconv("utf-8","gb2312",$file_name);
$file_path='/data/dianpuhaibao/'.$file_name;
//首先要判断给定的文件存在与否
if(!file_exists($file_path)){
    echo "没有该文件";
    return;
}

//以只读和二进制模式打开文件
$file = fopen ( $file_path, "r" );
//告诉浏览器这是一个文件流格式的文件
Header ( "Content-type: application/octet-stream" );
//请求范围的度量单位
Header ( "Accept-Ranges: bytes" );
//Content-Length是指定包含于请求或响应中数据的字节长度
Header ( "Accept-Length: " . filesize ( $file_path ) );
//用来告诉浏览器，文件是可以当做附件被下载，下载后的文件名称为$file_name该变量的值。
Header ( "Content-Disposition: attachment; filename=" . $file_name );

//读取文件内容并直接输出到浏览器
$contents = fread ( $file, filesize ( $file_path ) );
$contents =str_replace("\n","\r\n",$contents );  //保持换行，\n是linux  \r\n是windows下的换行
echo $contents;
fclose ( $file );
exit ();


//$fp=fopen($file_path,"r");
//$file_size=filesize($file_path);
////下载文件需要用到的头
//Header("Content-type: application/octet-stream");
//Header("Accept-Ranges: bytes");
//Header("Accept-Length:".$file_size);
//Header("Content-Length:".$file_size);
//Header("Content-Disposition: attachment; filename=".$file_name);
//
//$buffer=1024;
//$file_count=0;
////向浏览器返回数据
//ob_clean();
//flush();
//
//while(!feof($fp) && $file_count<$file_size){
//    $file_con=fread($fp,$buffer);
//    $file_count+=$buffer;
//    echo $file_con."\r\n";
//}
//fclose($fp);