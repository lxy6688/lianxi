<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/31
 * Time: 11:29
 */
require_once 'vendor/AipOcr.php';

// 你的 APPID AK SK
const APP_ID = '16733768';
const API_KEY = 'wTM3i8OFkUIXwUU3tqAtfzMt';
const SECRET_KEY = 'juSjRe4ZUOs0NgxLqXs0RGWXQafeNqqU';
//$dianpuhaibao = [
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p0.jpg?1556348826000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p1.jpg?1556348827000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p2.jpg?1556348828000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p3.jpg?1556348829000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p4.jpg?1556348830000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p5.jpg?1556348832000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p6.jpg?1556348833000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p7.jpg?1556348834000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p8.jpg?1556348835000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p9.jpg?1556348836000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p10.jpg?1556348837000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p11.jpg?1556348838000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p12.jpg?1556348839000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p13.jpg?1556348840000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p14.jpg?1556348841000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p15.jpg?1556348844000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p16.jpg?1556348845000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p17.jpg?1556348846000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p18.jpg?1556348847000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p19.jpg?1556348848000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p20.jpg?1556348850000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p21.jpg?1556348851000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p22.jpg?1556348852000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p23.jpg?1556348853000",
//    "https://www.kmway.com/upload/merchantsposter/819504/819504_p24.jpg?1556348854000"
//];

$dianpuhaibao = [
    "https://www.kmway.com/upload/merchantsposter/835461/835461_p0.jpg?1562915988000",
    "https://www.kmway.com/upload/merchantsposter/835461/835461_p1.jpg?1562915988000",
    "https://www.kmway.com/upload/merchantsposter/835461/835461_p2.jpg?1562915989000",
    "https://www.kmway.com/upload/merchantsposter/835461/835461_p3.jpg?1562915990000",
    "https://www.kmway.com/upload/merchantsposter/835461/835461_p4.jpg?1562915991000",
    ];

/**
 * 拼成一个完整的图
 * @param array $img_arr
 */
function img_puzzle($img_arr = []){
//    //先获取图片的宽度
    list($width, $height, $type, $attr) = getimagesize($img_arr[0]);
//    //echo $width;
//    $height = PicALLHeight($img_arr, $width);
//    echo $height;

    $height = 0;
    // 获取图片基本信息
    foreach ($img_arr as $k=>$v){
        $source[$k]['source'] = Imagecreatefromjpeg($v);
        $res_image[$k] = getimagesize($v);
    }
    //获取新画布的总高度
    foreach ($res_image as $k=>$v){
        $new_height = $v[1]*$width/$v[0];###我的新画布固定宽度为1000
        $height += $new_height;###计算新画布的高度
        $res_image[$k]['height'] = $new_height;

        //等比例缩放
        $image_p[$k] = imagecreatetruecolor($width, $new_height);
        imagecopyresampled($image_p[$k], $source[$k]['source'], 0, 0, 0, 0, $width, $new_height, $v[0], $v[1]);
    }
    //创建一个新的画布
    $new_image = imagecreatetruecolor($width,$height);
    //向画布添加图片
    $dst_x = 0;
    $dst_y = 0;
    foreach ($res_image as $k=>$v){
        imagecopy($new_image,$image_p[$k],$dst_x,$dst_y,0,0,$v[0],$v[1]);###参数挺多可以参照官方文档呦http://php.net/manual/zh/book.image.php
        $dst_y += $v['height'];
    }
    //添加地址
    $date = date('Ymd', time());
    $dir = './' . $date;
    if (!is_dir($dir)) {
        @mkdir("./$date");
    }
    $name = uniqid();
    //生成图片
    $res_data = Imagejpeg($new_image, $dir.'/'.$name.'.jpg',80);
    return $dir.'/'.$name.'.jpg';
    //echo $res_data;

}

//function PicALLHeight($arr,$width){
//    $height = 0;
//
//    if(count($arr) == count($arr,1)){  //一位数组的计算
//        foreach ($arr as $key => $value) {
//            $info = getimagesize($value);
//            $height += $width/$info[0]*$info[1];
//        }
//    }else{
//        foreach ($arr as $key => $value) {  //二维数组的计算
//
//            foreach ($value as $k => $v) {
//                $info = getimagesize($v);
//                $height += $width/$info[0]*$info[1];
//            }
//        }
//    }
//    return $height;
//}
//img_puzzle($dianpuhaibao);

function get_excerpt_proxy($img_arr = [], $img_url = ''){
    $len = count($img_arr);
//    if($len <= 10){   //拼图的每个图片高度是400px,baidu-ocr api要求上传图片不超过4096
//        return get_excerpt_byimg($img_url);
//    }

    $excerpt = '';
    $offset = 0;
    $for_num = intdiv($len,10)+1;
    for($i = 0; $i<$for_num; $i++) {
        $img_arr_child = array_slice($img_arr, $offset,10);
        $img_url = img_puzzle($img_arr_child);
        $excerpt .= get_excerpt_byimg($img_url);
        $offset += 10;
    }
    return $excerpt;
}

/**调用baidu-ocr 识别图片文字
 *
 * @param $img_url  本地图片路径
 * @return string
 */
function get_excerpt_byimg($img_url){
    $zhaiyao = '';
    $client = new AipOcr(APP_ID, API_KEY, SECRET_KEY);
    $image = file_get_contents($img_url);
    $client->webImage($image);
    $options = array();
    $options["detect_direction"] = "true";
    $options["detect_language"] = "true";
    $rest = $client->webImage($image, $options);
    if(!empty($rest['words_result']) && is_array($rest['words_result'])){
        foreach($rest['words_result'] as $wordsArr){
            $zhaiyao .= $wordsArr['words']."\n";
        }
    }
    return $zhaiyao;
}

echo get_excerpt_proxy($dianpuhaibao);
