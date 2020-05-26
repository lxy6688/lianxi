<?php
/**根据不同的策略更新表中的数据
 * update wp_posts post_content field and add  dynamic img
 *
 * User: lxy
 * Date: 2019/8/9
 * Time: 16:19
 */
header("Content-Type: text/html;charset=utf-8");
ini_set("memory_limit", "1024M");
set_time_limit(0);

//存放拼图之后整图的存放路径
const DIANPU_PIC = '/data/dianpuhaibao/';

/* 引入aliyun-oss */
require_once('vendor/aliyun-oss/autoload.php');
use OSS\OssClient;
use OSS\Core\OssException;

require_once "./DaoMysqli.php";
$params = [
    "host" => "localhost",
    "user" => "cdd",
    "password" => 'cdd-seo@123#@!',
    "dbName" => "new_cdd"
];

$errIDArr = [];

$daoMysqli = DaoMysqli::getInstance($params);

/* 文章json 文件所在位置 */
$data_path = '/data/ftp/cdd/';

if(!is_dir($data_path)){
    echo $data_path.'不存在!';
    exit;
}

echo "开始更新数据...\n";
$start_time = time();

$img_houzhui_arr = ['bmp','gif','jpg','png'];
$handle = opendir($data_path);
$i = 0;
$j = 0;
if ($handle) {
    while (($fl = readdir($handle)) !== false) {
        echo "当前时间: ".date("Y-m-d H:i:s")."     开始更新文件:".$fl."\n";
        $direction = $data_path.'/'.$fl;
        //如果不加  $fl!='.' && $fl != '..'  则会造成把$dir的父级目录也读取出来
        if (!is_dir($direction) && $fl!='.' && $fl != '..') {
            $json_string = file_get_contents($direction);
            $result = json_decode($json_string, true);

            if(!$result){
                echo $direction.'json文件解析错误!'; continue;
            }
            foreach ($result as $key=>$item) {
                if (empty($item['xiangmumingcheng'])) {
                    continue;
                }

//                $title = $item['xiangmumingcheng'];
//                $ID =  $daoMysqli->getIdByTitle($title);

                $url = $item['dianpulianjie'];
                $resp = $daoMysqli->getIdByUrl($url);
                $ID =  $resp['ID'];
                $title = $resp['post_title'];
                $touzi = $resp['post_touzi'];
//                if(!empty($errIDArr)) {
//                    if(!in_array($ID,$errIDArr)){
//                        echo "ID为:".$ID."的项目略过, 所在文件位置：".$fl."\n";
//                        continue;
//                    }
//                }else{
//                    if(!empty($touzi)){
//                        if(empty($item['xiangmuzixun'])){
//                            echo "ID为:".$ID."的项目略过, 所在文件位置：".$fl."\n";
//                            continue;
//                        }
//                    }
//
//                    if(!$ID) {
//                        continue;
//                    }
//                }
                if(!$ID) {
                    echo "no such title :".$item['xiangmumingcheng']."\n";
                    continue;
                }

                //处理海报拼图
                $dianpu_pic = '';
                if(!empty($item['dianpuhaibao'])){
                    $old_url = img_puzzle($item['dianpuhaibao']);
                    $dianpu_pic = upload_img(md5(time())."jpg", $old_url);
                    del_path($old_url);
                }

                $item['hezuoxiangqing'] = (isset($item['hezuoxiangqing']) && !empty($item['hezuoxiangqing']))? str_handle($item['hezuoxiangqing']) : '';
                $item['hezuoqianjing'] = (isset($item['hezuoqianjing']) && !empty($item['hezuoqianjing']))? str_handle($item['hezuoqianjing']) : '';
                $item['hezuoyoushi'] = (isset($item['hezuoyoushi']) && !empty($item['hezuoyoushi']))? str_handle($item['hezuoyoushi']) : '';
                $item['hezuotiaojian'] = (isset($item['hezuotiaojian']) && !empty($item['hezuotiaojian']))? str_handle($item['hezuotiaojian']) : '';
                $item['hezuozhichi'] = (isset($item['hezuozhichi']) && !empty($item['hezuozhichi']))? str_handle($item['hezuozhichi']) : '';
                $item['hezuoliucheng'] = (isset($item['hezuoliucheng']) && !empty($item['hezuoliucheng']))? str_handle($item['hezuoliucheng']) : '';

                //项目文章
                $data['content'] = '<p style="text-align: center;"><img src="'.$dianpu_pic.'"></p>';
                if($item['hezuoxiangqing'] != ''){
                    $data['content'] .= '<p>合作详情</p>' . str_replace('\'','',$item['hezuoxiangqing']);
                }
                if($item['hezuoqianjing'] != '') {
                    $data['content'] .= '<p>合作前景</p>' . str_replace('\'','',$item['hezuoqianjing']);
                }
                if($item['hezuoyoushi'] != '') {
                    $data['content'] .= '<p>合作优势</p>' . str_replace('\'','',$item['hezuoyoushi']);
                }
                if($item['hezuotiaojian'] != '') {
                    $data['content'] .= '<p>合作条件</p>' . str_replace('\'','',$item['hezuotiaojian']);
                }
                if($item['hezuozhichi'] != '') {
                    $data['content'] .= '<p>合作支持</p>' . str_replace('\'','',$item['hezuozhichi']);
                }
                if($item['hezuoliucheng'] != '') {
                    $data['content'] .= '<p>合作流程</p>' . str_replace('\'','',$item['hezuoliucheng']);
                }

                $post_touzi = (isset($item['fenlei']['touzijine']))? trim(str_replace(['￥','万'],'',$item['fenlei']['touzijine'])) : 0;
                //$post_join = ($item['hezuofeiyong']) ?: 0;

                $post_toutu = [];
                if(!empty($item['xiangmutoutu'])) {
                    foreach ($item['xiangmutoutu'] as $v) {
                        $image = crab_image($v);
                        $old_url = $image['save_path'];
                        $url = upload_img($image['file_name'], $old_url);
                        del_path($old_url);
                        array_push($post_toutu, $url);
                    }
                }

                $tupian_url = '';
                $img_houzhui = substr(strrchr($item['xiangmutupian'],'.'),1);
                if(!in_array($img_houzhui,$img_houzhui_arr)){
                    echo "xiangmutupian error :".$item['xiangmumingcheng']."\n";
                }else{
                    $image = crab_image($item['xiangmutupian']);
                    $old_url = $image['save_path'];
                    $tupian_url = upload_img($image['file_name'], $old_url);
                    del_path($old_url);
                }

                $post_content = $data['content'];
                $post_address = $item['diqu'];
                $post_money = $item['zhucezijin'];

                $params = [
                    'post_content' => $post_content,
                    'post_touzi'   => $post_touzi,
                    'post_address'   => $post_address,
                    'post_money'   => $post_money,
                    'guid'   => $tupian_url,
                    'post_tupian'   => $tupian_url,
                    //'post_join'    => str_replace("合作费用：",'',$post_join),
                    'post_toutu'   => json_encode($post_toutu)

                ];
                $updateStatus =  $daoMysqli->updateFields($ID,$params);
                if($updateStatus === true){
                    echo "ID为:".$ID.",标题为:".$title."的项目update ok, 所在文件：".$fl."\n";
                }else{
                    echo "ID为:".$ID.",标题为:".$title."的项目update false, 所在文件：".$fl."\n";
                    echo "false error sql:" . $updateStatus."\n";
                }
                //项目资讯
                /**
                if (!empty($item['xiangmuzixun'])) {
                    $post_toutu = [];
                    foreach ($item['xiangmuzixun'] as $value) {
                        $url = '';
                        if (!empty($value['images'])) {
                            foreach ($value['images'] as $v) {
                                $image = crab_image($v);
                                $old_url = $image['save_path'];
                                $url = upload_img($image['file_name'], $old_url);
                                del_path($old_url);
                                array_push($post_toutu, $url);
                            }
                        }
                        $resp =  $daoMysqli->getIdByUrl($value['url']);
                        //$guid = str_replace('http','https',$resp['guid']);
                        $ID = $resp['ID'];
                        $params = [
                            "post_toutu" => json_encode($post_toutu),
                            //"guid" => json_encode($guid),
                        ];
                        $updateStatus =  $daoMysqli->updateArticle($ID,$params);
                        if($updateStatus === true){
                            echo "ID为:".$ID.",标题为:".$value['title']."的文章update ok\n";
                        }else{
                            echo "ID为:".$ID.",标题为:".$value['title']."的文章update false\n";
                            echo "false error sql:" . $updateStatus;
                        }
                        $j += 1;
                    }
                }
                */
                $i += 1;
                echo "当前时间: ".date("Y-m-d H:i:s")."     当前的值是 ：". $i."\n";
//                if($i==6){
//                    exit;
//                }
            }
            echo "当前时间: ".date("Y-m-d H:i:s")."     文件:".$fl."导入完成\n";
        }
    }
}

closedir($handle);
echo '更新项目数据'.$i.'条';
echo "\n";
echo "\n";
echo "\n";
echo "\n";
echo '更新项目资讯数据'.$j."条\n";
echo "更新此次数据用时：". (time()-$start_time) . "s \n";


/**
 * content正文对标签的处理
 *
 * @param $str
 */
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
            if(realpath($old_url)){
                @unlink($old_url);
            }

            return 'src="'.$img_url.'" ';
        }
    },$str);
}

/**
 * @param $img_url
 * @param string $save_dir
 * @param null $file_name
 * @return array|bool
 */
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

/**
 * 获取文件后缀
 * @param $url
 * @return bool|mixed
 */
function get_type($url)
{
    $mimes = ['bmp','gif','jpg','png'];
    $ext = substr(strrchr($url,'.'),1);
    if(in_array($ext,$mimes)) {
        return $ext;
    }
    return false;
}

/**
 * aliyun-oss 上传图片
 */
function upload_img($upload_name, $file_path){
    /* oss配置 */
    $accessKeyId = 'LTAItu6d3D9otJSJ';
    $accessKeySecret = '1LkXel4KnzapGddsXlac4ZTE2etv9Y';
    $endpoint = 'http://oss-cn-shanghai.aliyuncs.com';
    $bucket= '007dir';

    $upload_path = 'wp-content/uploads/' . $upload_name;
    try{
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        $upload_res = $ossClient->uploadFile($bucket, $upload_path, $file_path);
        if ($upload_res['info']['http_code'] == 200) {
            return str_replace("http://007dir.oss-cn-shanghai.aliyuncs.com","https://cdn.007dir.cn", $upload_res['info']['url']);
        }
        return '';
    } catch(OssException $e) {
        printf($e->getMessage() . "\n");
        return;
    }
}

/**
 * 拼成一个完整的图
 * @param array $img_arr
 */
function img_puzzle($img_arr = []){
    list($width, $height, $type, $attr) = getimagesize($img_arr[0]);
    $height = 0;
    // 获取图片基本信息
    foreach ($img_arr as $k=>$v){
        $source[$k]['source'] = Imagecreatefromjpeg($v);
        $res_image[$k] = getimagesize($v);
    }
    //获取新画布的总高度
    foreach ($res_image as $k=>$v){
        $new_height = intval($v[1]*$width/$v[0]);
        $height += $new_height;
        $res_image[$k]['height'] = $new_height;

        //等比例缩放
        $image_p[$k] = imagecreatetruecolor($width, $new_height);
        imagecopyresampled($image_p[$k], $source[$k]['source'], 0, 0, 0, 0, $width, $new_height, $v[0], $v[1]);
    }
    $height = intval($height);
    //创建一个新的画布
    $new_image = imagecreatetruecolor($width,$height);
    //向画布添加图片
    $dst_x = 0;
    $dst_y = 0;
    foreach ($res_image as $k=>$v){
        imagecopy($new_image,$image_p[$k],$dst_x,$dst_y,0,0,$v[0],$v[1]);//参数参照官方文档http://php.net/manual/zh/book.image.php
        $dst_y += $v['height'];
    }
    //添加地址
    $date = date('Ymd', time());
    $dir = DIANPU_PIC . $date;
    if (!is_dir($dir)) {
        mkdir($dir,0777,true);
    }
    $name = uniqid();
    //生成图片
    $res_data = Imagejpeg($new_image, $dir.'/'.$name.'.jpg',75);
    return $dir.'/'.$name.'.jpg';
}

/**
 * rm old  path
 * @param string $url
 */
function del_path($url = ''){
    if(realpath($url)){
        @unlink($url);
    }
}