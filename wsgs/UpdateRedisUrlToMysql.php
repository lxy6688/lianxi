<?php
/** redis读取本地的视频url，上传至阿里云平台并更新url到数据库
 *
 * User: lxy
 * Date: 2020/3/09
 * Time: 14:47
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
$redis = WsgsMysqli::getRedis();
$listKey = 'videoUrlQueue';
$llen = $redis->lLen($listKey);
if($llen == 0) {
    echo "没有队列数据需要处理";
    exit;
}

$start_time = time();

$client = initVodClient(ACCESSKEYID, ACCESSKEYSECRET);
for ($i=0; $i < $llen; $i++) {
    //上传阿里云平台并更新数据库
    $jsonData = $redis->rPop($listKey);
    $arrData = json_decode($jsonData,true);
    $fileName = $arrData['fileName'];
    $filePath = $arrData['filePath'];
    $fileUrl = $arrData['fileUrl'];

    //学生作业默认是MP4视频
    $urlInfo = uploadMedia($client,$filePath, $fileName);
    if($urlInfo === false){
        echo $filePath."上传false!\n";
        $redis->lPush($listKey,$jsonData);
        continue;
    }

    //更新云视频url到数据库
    $urlInfo['oldUrl'] = $fileUrl;
    $updateResp =  $wsgsMysqli->updateUrl($urlInfo);
    if($updateResp['status']){
        echo $fileUrl."更改成功!\n";
    }else{
        echo $fileUrl."更改false     error sql:  ".$updateResp['data'];
        $redis->lPush($listKey,$jsonData);
    }

}
echo "处理完成, 用时：". (time()-$start_time) . "s \n";

/**
 * 初始化阿里云上传client
 *
 * @param $accessKeyId
 * @param $accessKeySecret
 * @return DefaultAcsClient
 */
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
function uploadMedia($client,$localFile, $localFileName){
    $vid = testUploadLocalVideo(ACCESSKEYID, ACCESSKEYSECRET, $localFile, $localFileName);
    if($vid === false){
        return false;
    }
    //获取视频的播放地址
    while(true){
        try {
            $playInfo = getPlayInfo($client, $vid);
            $objArray = $playInfo->PlayInfoList->PlayInfo;  //数组形式的对象,  PlayURL播放地址
            $isOk = 0;
            foreach ($objArray as $suffix){
                if($suffix->Format == 'mp4'){
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
 * 上传图片 (预留程序)
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
        }
    }

}

/**
 * 返回视频播放地址
 * @param $client
 * @param $videoId
 * @return mixed
 */
function getPlayInfo($client, $videoId) {
    sleep(2);
    $request = new vod\GetPlayInfoRequest();
    $request->setVideoId($videoId);
    $request->setFormats('mp4');
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


