<?php
/** 定时查询数据库课程作业详情中，未上传到阿里云的视频、音频或图片的数据，上传到阿里云平台并更新数据库中媒体url为阿里云上的url
 *
 * User: lxy
 * Date: 2020/4/19
 * Time: 11:26
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
//define('SHOTS_TEMPLATE','ca99a630c1b76d480d80a3414d09c57f'); 截图的模板id

require_once __DIR__ . "/WsgsMysqli.php";
$params = [
    "host" => "",
    "user" => "",
    "password" => '',
    "dbName" => ""
];

$errIDArr = [];
$wsgsMysqli = WsgsMysqli::getInstance($params);

echo "脚本开始执行...\n";
$start_time = time();
$notUploadRecordsResp = $wsgsMysqli->getNotUploadRecords();
if(empty($notUploadRecordsResp)) {
    echo "没有需要上传的本地媒体资源";
    exit;
}

foreach($notUploadRecordsResp as $notUploadRecords) {
    $id = $notUploadRecords->id;
    $type = $notUploadRecords->type;
    $localPath = $notUploadRecords->local_path;
    if(!file_exists($localPath)){
        echo "id为：".$id."   本地文件：".$localPath."     不存在!\n";
        continue;
    }

    //本地文件上传到阿里云
    $uploadRes = uploadLocalMedia($localPath,$type);
    if($uploadRes['status']) {
        $url = $uploadRes['data']['url'];
        $vid = $uploadRes['data']['vid'];

        $updateResp =  $wsgsMysqli->updateCourseHKDetail($id, $url,$vid);
        if($updateResp['status']){
            echo "id：".$id."    update success!\n";
        }else{
            echo "id：".$id."    update false     error sql:  ".$insertFormalDetailResp['data']."\n";
        }
    }else{
        echo "id为：".$id."   本地文件：".$localPath."     上传false!\n";
    }
}

function uploadLocalMedia($localPath,$type) {
    $localFileName = substr(strrchr($localPath, "/"), 1);

    //初始化阿里云sdk
    $client = initVodClient(ACCESSKEYID, ACCESSKEYSECRET);
    if($type == 2) {
        //上传图片到阿里云
        $urlInfo = uploadImg($localPath, $localFileName);
    }else{
        $suffix = 'mp4';
        if($type == 1) {
            $suffix = 'mp3';
        }
        //上传视频、音频到阿里云
        $urlInfo = uploadMedia($client,$localPath, $localFileName, $suffix);
    }

    $resp = [
        'status' => false,
        'data' => []
    ];
    if($urlInfo === false){
        return $resp;
    }
    $resp['status'] = true;
    $resp['data'] = [
        'vid' => $urlInfo['vid'],
        'url' => $urlInfo['url']
    ];
    return $resp;
}
echo "更新完成, 用时：". (time()-$start_time) . "s \n";


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

/**
 * 提交截图(普通截图)
 *
 * @param $client
 * @return mixed
 */
function submitSnapshotJob($client, $videoId) {
    $request = new vod\SubmitSnapshotJobRequest();
    //需要截图的视频ID(推荐传递截图模板ID)
    $request->setVideoId($videoId);
    //截图模板ID
    $request->setSnapshotTemplateId(SHOTS_TEMPLATE);
    return $client->getAcsResponse($request);
}

/**
 * 获取普通截图数据
 *
 * @param $client
 * @return mixed
 */
function listSnapshots($client, $videoId) {
    $request = new vod\ListSnapshotsRequest();
    $request->setVideoId($videoId);
    ///截图类型
    $request->setSnapshotType("NormalSnapshot");   //CoverSnapshot:封面截图  NormalSnapshot:普通截图
    // 翻页参数
    $request->setPageNo("1");
    $request->setPageSize("20");
    return $client->getAcsResponse($request);
}

