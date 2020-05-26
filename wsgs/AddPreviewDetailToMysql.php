<?php
/**  预习/复习 课件详情(detail)信息
 *
 * User: lxy
 * Date: 2020/3/22
 * Time: 11:25
 */
header("Content-Type: text/html;charset=utf-8");
ini_set("memory_limit", "1024M");
ini_set("display_errors",0);
set_time_limit(0);

require_once __DIR__  . '/ali_video/voduploadsdk/Autoloader.php';
use vod\Request\V20170321 as vod;   //视频上传、获取播放地址、播放凭证等

date_default_timezone_set('PRC');
define('ACCESSKEYID','');
define('ACCESSKEYSECRET','');
//define('SHOTS_TEMPLATE',''); 截图的模板id

require_once __DIR__ . "/WsgsMysqli.php";
$params = [
    "host" => "",
    "user" => "",
    "password" => '',
    "dbName" => ""
];

$errIDArr = [];
$wsgsMysqli = WsgsMysqli::getInstance($params);

//课件文件所在位置
$yuxiFile = 'yuxi.txt';
$data_path = __DIR__ . '/preview_course/'.$yuxiFile;

//资源路径前缀(正式课件的路径)
$fixSource = __DIR__. '/course/resources/';

if(!file_exists($data_path)){
    echo $data_path.'不存在!';
    exit;
}


//接收cid参数,  课程id
if(!isset($argv[1])){
    echo 'cid is not null';
    exit;
}
$cid = $argv[1];

echo "脚本开始执行...\n";
$start_time = time();


$resp = [];
$typeArr = [
    "jpg" => 2,
    "png" =>2,
    "mp4" => 0,
    "mp3" => 1
];
$client = initVodClient(ACCESSKEYID, ACCESSKEYSECRET);
$handler = fopen($data_path,'r'); //打开文件
while(!feof($handler)){
    $lineData = fgets($handler,4096);
    if(empty($lineData)) {
        continue;
    }
    $lineDataArr = explode(" ", trim($lineData));
    $tag = trim($lineDataArr[0]);
    $title = trim($lineDataArr[1]);
    $mediaName = trim($lineDataArr[2]);

    //echo $tag." ".$title." ".$mediaName."\n";

    //视频等媒体资源上传阿里云
    $suffix = substr(strrchr($mediaName, "."), 1);

    $localFile =  $fixSource . $mediaName;
    if(!file_exists($localFile)){
        echo $localFile." 资源不存在false\n";
        continue;
    }

    if($suffix == 'jpg' || $suffix == 'png') {
        //上传图片到阿里云
        $urlInfo = uploadImg($localFile, $mediaName);
    }else{
        //上传视频、音频到阿里云
        $urlInfo = uploadMedia($client,$localFile, $mediaName, $suffix);
    }
    if($urlInfo === false){
        echo $localFile."上传false!\n";
        continue;
    }

    //insert data
    $insertData = [
        "cid" => $cid,
        "name" => $title,
        "url"  => $urlInfo['url'],
        "tag"  => $tag,
        "vid"  => $urlInfo['vid'],
        "type" => $typeArr[$suffix]
    ];

    $insertPreviewDetailResp =  $wsgsMysqli->insertPreviewDetail($insertData);
    if($insertPreviewDetailResp['status']){
        echo $mediaName."入库成功!\n";
    }else{
        echo $mediaName."入库false     error sql:  ".$insertPreviewDetailResp['data'];
    }

}
fclose($handler); //关闭文件

echo "添加完成, 用时：". (time()-$start_time) . "s \n";


function initVodClient($accessKeyId, $accessKeySecret) {
    $regionId = 'cn-shanghai';  // 点播服务接入区域
    $profile = DefaultProfile::getProfile($regionId, $accessKeyId, $accessKeySecret);
    return new DefaultAcsClient($profile);
}


/**
 * 上传媒体(音频或视频)
 *
 * @param $localFile
 * @param $localFileName
 */
function uploadMedia($client,$localFile, $localFileName, $suffixPam){
    $vid = testUploadLocalVideo(ACCESSKEYID, ACCESSKEYSECRET, $localFile, $localFileName);
    if($vid === false){
        return false;
    }
    //获取视频的播放地址
    while(true){
        try {
            $playInfo = getPlayInfo($client, $vid, $suffixPam);
            $objArray = $playInfo->PlayInfoList->PlayInfo;  //数组形式的对象,  PlayURL播放地址
            $isOk = 0;
            foreach ($objArray as $suffix){
                if($suffix->Format == $suffixPam){
                    $isOk = 1;
                }
            }
            if($isOk === 0) {
                sleep(1);
                continue;
            }
            foreach($objArray as $obj){
                $url = $obj->PlayURL;
            }
            break;
        } catch (Exception $e) {
            sleep(1);
        }
    }

    return [
        'vid' => $vid,
        'url' => $url
    ];

}


/**
 * 上传图片
 *
 * @param $localFile
 * @param $localFileName
 */
function uploadImg($localFile, $localFileName){
    while(true){
        try {
            $uploader = new AliyunVodUploader(ACCESSKEYID, ACCESSKEYSECRET);
            $uploadImageRequest = new UploadImageRequest($localFile, $localFileName);
            $uploadImageRequest->setCateId(1000009458);
            $res = $uploader->uploadLocalImage($uploadImageRequest);
            break;
        } catch (Exception $e) {
            sleep(1);
        }
    }

    return [
        'vid' => $res['ImageId'],
        'url' => $res['ImageURL']
    ];

}



/**
 * 上传本地视频
 * @param $accessKeyId
 * @param $accessKeySecret
 * @param $filePath
 */
function testUploadLocalVideo($accessKeyId, $accessKeySecret, $filePath, $videoName){
    while(true) {
        try {
            $uploader = new AliyunVodUploader($accessKeyId, $accessKeySecret);
            $uploadVideoRequest = new UploadVideoRequest($filePath, $videoName);
            $res = $uploader->uploadLocalVideo($uploadVideoRequest);
            return $res;
        } catch (Exception $e) {
            sleep(1);
//            printf("testUploadLocalVideo Failed, ErrorMessage: %s\n Location: %s %s\n Trace: %s\n",
//                $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
//            return false;
        }
    }

}

/**
 * 返回视频播放地址
 * @param $client
 * @param $videoId
 * @return mixed
 */
function getPlayInfo($client, $videoId, $suffix) {
    sleep(2);
    $request = new vod\GetPlayInfoRequest();
    $request->setVideoId($videoId);
    $request->setFormats($suffix);
    //$request->setAuthTimeout(300);   //播放地址过期时间
    $request->setAcceptFormat('JSON');
    return $client->getAcsResponse($request);
}

/**
 * 获取视频信息
 * @param client 发送请求客户端
 * @return GetVideoInfoResponse 获取视频信息响应数据
 */
function getVideoInfo($client, $videoId) {
    $request = new vod\GetVideoInfoRequest();
    $request->setVideoId($videoId);
    $request->setAcceptFormat('JSON');
    return $client->getAcsResponse($request);
}


