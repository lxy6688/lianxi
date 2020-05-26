<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/5
 * Time: 14:05
 */
require_once('/home/wwwroot/cdd/spider_cdd/vendor/aliyun-oss/autoload.php');
use OSS\OssClient;
use OSS\Core\OssException;


//$upload_name = "test.jpg";
//$file_path = "./test.jpg";




//$accessKeyId = 'LTAItu6d3D9otJSJ';
//$accessKeySecret = '1LkXel4KnzapGddsXlac4ZTE2etv9Y';
//$endpoint = 'http://oss-cn-shanghai.aliyuncs.com';
////$endpoint = 'http://cdn.007dir.cn';
//$bucket= '007dir';
//
//$upload_path = 'wp-content/uploads/' . $upload_name;
//
//try{
//    $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
//    $upload_res = $ossClient->uploadFile($bucket, $upload_path, $file_path);
//    echo $resp['info']['url'] = str_replace("http://007dir.oss-cn-shanghai.aliyuncs.com","http://cdn.007dir.cn", $upload_res['info']['url']);
//    //var_dump($upload_res);
//} catch(OssException $e) {
//    printf($e->getMessage() . "\n");
//    return;
//}

function crab_image($img_url, $save_dir='/data/websites/cdd/', $file_name=null){
    if(empty($img_url)){
        return false;
    }

    //获取图片信息大小
    //echo $img_url.PHP_EOL;
    $img_size = get_type($img_url);
    if(!$img_size || !in_array($img_size,array('image/jpg', 'image/gif', 'image/png', 'image/jpeg', 'jpg', 'gif', 'png', 'jpeg'),true)){
        return false;
    }

    //获取后缀名
    $_mime = explode('/', $img_size);
    $_ext = '.'.end($_mime);


    if(empty($file_name)){  //生成唯一的文件名
        $file_name = microtime(true).mt_rand().$_ext;
    }

    //开始攫取
    ob_start();
    readfile($img_url);
    $image_info = ob_get_contents();
    ob_end_clean();

    if(!file_exists($save_dir)){
        mkdir($save_dir,0777,true);
    }
    $fp = fopen($save_dir.$file_name, 'a');
    $image_length = strlen($image_info);    //计算图片源码大小
    $_inx = 1024;   //每次写入1k
    $_time = ceil($image_length/$_inx);
    for($i=0; $i<$_time; $i++){
        fwrite($fp,substr($image_info, $i*$_inx, $_inx));
    }
    fclose($fp);
    return array('file_name'=>$file_name,'save_path'=>$save_dir.$file_name,'ext'=>$_ext);
}


function upload_img($upload_name, $file_path){
    /* oss配置 */
    $accessKeyId = 'LTAItu6d3D9otJSJ';
    $accessKeySecret = '1LkXel4KnzapGddsXlac4ZTE2etv9Y';
    $endpoint = 'http://oss-cn-shanghai.aliyuncs.com';
    //$endpoint = 'http://cdn.007dir.cn';
    $bucket= '007dir';

//    $upload_path = date('Y-m-d') . '/' . $upload_name;
    $upload_path = 'wp-content/uploads/' . $upload_name;

    try{
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $upload_res = $ossClient->uploadFile($bucket, $upload_path, $file_path);
        if ($upload_res['info']['http_code'] == 200) {
            return str_replace("http://007dir.oss-cn-shanghai.aliyuncs.com","http://cdn.007dir.cn", $upload_res['info']['url']);
        }
        return '';
    } catch(OssException $e) {
        printf($e->getMessage() . "\n");
        return;
    }
}

function get_type($url)
{

    $mimes = ['bmp','gif','jpg','png'];
    $ext = substr(strrchr($url,'.'),1);
    if(in_array($ext,$mimes)) {
        return $ext;
    }
    return false;
}


function str_handle($str){
    if(empty($str)){
        return $str;
    }
    //去除标签style属性
    $str =  preg_replace("/(\s)*style(\s)*=[\s]*(\"[^\"]+\")/","",$str);

    //替换 img url
    $regx = "/\bsrc\b\s*=\s*[\'\\\"]?([^\'\\\"]*)[\'\\\"]?/i";
    return preg_replace_callback($regx,function($matches){
        if(!empty($matches[1])){
            $xiangmutupian = crab_image($matches[1]);
            $old_url = $xiangmutupian['save_path'];

            $img_url = '';
            $img_url = upload_img($xiangmutupian['file_name'], $old_url);
            /* 上传oss后删除本地 */
//            if(realpath($old_url)){
//                @unlink($old_url);
//            }

            return 'src="'.$img_url.'" ';
        }
    },$str);
}

$str = '<p><img src="https://www.kmway.com/upload/resources/image/2019/05/18/832668_600x600.jpg" ></p>
    <p><img src="https://www.kmway.com/upload/resources/image/2019/05/18/832670_600x600.jpg" ></p>
    <p><img src="https://www.kmway.com/upload/resources/image/2019/05/18/832671_600x600.jpg" ></p>
';

var_dump(str_handle($str));