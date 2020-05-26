<?php
/** 单独添加  备课课件详情(detail)信息
 *
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
$data_path = __DIR__ . '/prepare_course/';

//资源路径前缀
$fixSource = '/prepare_course/';

if(!is_dir($data_path)){
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

$handle = opendir($data_path);
$i = 1;
if ($handle) {
    while (($json = readdir($handle)) !== false) {
        $jsonPath = $data_path.$json;
        //如果不加  $fl!='.' && $fl != '..'  则会造成把$dir的父级目录也读取出来
        if (!is_dir($jsonPath) && $json!='.' && $json != '..') {
            //判断当前的文件是否是json文件
            if(false === strpos($json, 'json')) {
                continue;
            }
            $json_string = file_get_contents($jsonPath);
            $result = json_decode($json_string, true);
            if(!$result){
                echo $jsonPath."json文件解析错误!\n";
                continue;
            }
            $newJson = str_replace(['ce','ke'],['-',''],$json);
            $fileName = str_replace('.json','',$newJson);

            $titleArr = array_values(explode('-', $fileName));
            $courseName = $titleArr[1];

            /**  处理课件详情的json  */
            $vidArr = [];
            $client = initVodClient(ACCESSKEYID, ACCESSKEYSECRET);


            foreach($result as $k => $waiValue) {
                $dataRes = $waiValue['data'];
                //媒体信息处理
                $waiValue['data'] = handleMedia($dataRes);
                $result[$k] = $waiValue;
            }

            //insert formal detail
            $insertData = [
                'content' => json_encode($result, JSON_UNESCAPED_UNICODE)
            ];
            $insertPrepareDetailResp =  $wsgsMysqli->insertPrepareDetail($cid, $courseName,$insertData);
            if($insertPrepareDetailResp['status']){
                echo $json."入库成功!\n";
            }else{
                echo $json."入库false     error sql:  ".$insertPrepareDetailResp['data'];
            }

        }
    }
}

closedir($handle);
echo "添加完成, 用时：". (time()-$start_time) . "s \n";


function initVodClient($accessKeyId, $accessKeySecret) {
    $regionId = 'cn-shanghai';  // 点播服务接入区域
    $profile = DefaultProfile::getProfile($regionId, $accessKeyId, $accessKeySecret);
    return new DefaultAcsClient($profile);
}

/**
 * 媒体信息的处理
 *
 * @param $dataRes
 */
function handleMedia($dataRes) {
    foreach($dataRes as $k => $valueArr) {
        if(isset($valueArr['linkTalk']) && !empty($valueArr['linkTalk'])) {
            $valueArr['linkTalk'] = handleMediaTwo($valueArr['linkTalk']);
        }

        if(isset($valueArr['accompany']) && !empty($valueArr['accompany'])) {
            $valueArr['accompany'] = handleMediaTwo($valueArr['accompany']);
        }

        if(isset($valueArr['demoVideos']) && !empty($valueArr['demoVideos'])) {
            $valueArr['demoVideos'] = handleMediaTwo($valueArr['demoVideos']);
        }

        if(isset($valueArr['teachingTalk']) && !empty($valueArr['teachingTalk'])) {
            $valueArr['teachingTalk'] = handleMediaTwo($valueArr['teachingTalk']);
        }

        $dataRes[$k] = $valueArr;
    }

    return $dataRes;
}

function handleMediaTwo($arr){
    global $fixSource;
    global $client;
    foreach($arr as $k => $v){
        if(isset($v['src']) && !empty($v['src'])) {
            $src = $v['src'];
            $localFileName = substr(strrchr($src, "/"), 1);
            $suffix = substr(strrchr($src, "."), 1);
            $localFile = __DIR__ . $fixSource . $src;
            if(!file_exists($localFile)){
                echo $localFile." 资源不存在false\n";
                continue;
            }

            if($suffix == 'jpg' || $suffix == 'png') {
                //上传图片到阿里云
                $urlInfo = uploadImg($localFile, $localFileName);
            }else{
                //上传视频、音频到阿里云
                $urlInfo = uploadMedia($client,$localFile, $localFileName,$suffix);
            }
            if($urlInfo === false){
                echo $localFile."上传false!\n";
            }else{
                $v['src'] = $urlInfo['url'];
            }

            $arr[$k] = $v;
        }
    }
    return $arr;
}


/**
 * 上传媒体(音频或视频)
 *
 * @param $localFile
 * @param $localFileName
 */
function uploadMedia($client,$localFile, $localFileName,$suffixPam){
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
function getPlayInfo($client, $videoId, $suffixPam) {
    sleep(2);
    $request = new vod\GetPlayInfoRequest();
    $request->setVideoId($videoId);
    $request->setFormats($suffixPam);
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


