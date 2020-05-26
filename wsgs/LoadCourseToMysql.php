<?php
/** 导入 "我是鼓手" 项目的课件信息
 *
 * User: lxy
 * Date: 2020/2/20
 * Time: 18:49
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
$data_path = __DIR__ . '/course/';

//资源路径前缀
$fixSource = '/course/';

if(!is_dir($data_path)){
    echo $data_path.'不存在!';
    exit;
}

//读取标有水印位置的视频来源url
//$is_shuiyin_arr = rese_readfile();

//册名称对应数组
$forBookArr = [
    '1' => '第一册',
    '2' => '第二册',
    '3' => '第三册',
    '4' => '第四册',
    '5' => '第五册',
    '6' => '第六册',
    '7' => '第七册',
    '8' => '第八册',
    '9' => '第九册',
    '10' => '第十册',
    '11' => '第十一册'
];

//课件名称对应数组
$forCourseArr = [
    '1' => '第1课',
    '2' => '第2课',
    '3' => '第3课',
    '4' => '第4课',
    '5' => '第5课',
    '6' => '第6课',
    '7' => '第7课',
    '8' => '第8课',
    '9' => '第9课',
    '10' => '第10课',
    '11' => '第11课',
    '12' => '第12课',
    '13' => '第13课',
    '14' => '第14课',
    '15' => '第15课',
    '16' => '第16课',
    '17' => '第17课',
    '18' => '第18课',
    '19' => '第19课',
    '20' => '第20课',
    '21' => '第21课',
    '22' => '第22课',
    '23' => '第23课',
    '24' => '第24课',
];

$singleArr = [];

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
            $bookey = $titleArr[0];
            $bookName = $forBookArr[$bookey];

            //课件name
            $coursekey = $titleArr[1];
            $courseName = $forCourseArr[$coursekey];

            $courseArr = [
                'courseName' => $courseName,
                'courseImg'  => '',
                'courseDesc' => ''
            ];
            $screen = $titleArr[2];
            //$courseName = $titleArr[1];
            //添加册和课件数据
            if($i == 1){
                $insertCourseAndBookResp = $wsgsMysqli->insertCourseAndBook($bookName, $courseArr);
                if($insertCourseAndBookResp['status'] == false) {
                    echo "文件：".$json."  对应的课程信息插入false!\n";
                    exit;
                }
                $cid = $insertCourseAndBookResp['data'];
                $i++;
            }


            /**  处理课件详情的json  */
            $vidArr = [];
            $client = initVodClient(ACCESSKEYID, ACCESSKEYSECRET);
            //处理background属性
            if(isset($result['background']['src'])  && !empty($result['background']['src'])) {
                $src = $result['background']['src'];
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
                    $urlInfo = uploadMedia($client,$localFile, $localFileName, $suffix);
                }
                if($urlInfo === false){
                    echo $localFile."上传false!\n";
                    continue;
                }

                array_push($vidArr, $urlInfo['vid']);
                $result['background']['src'] = $urlInfo['url'];
            }

            //处理childs属性
            $returnChilds = [];
            if(isset($result['childs'])  && !empty($result['childs'])) {
                foreach($result['childs'] as $arr) {
                    if(isset($arr['src']) && !empty($arr['src'])) {
                        $localFileName = substr(strrchr($arr['src'], "/"), 1);
                        $suffix = substr(strrchr($arr['src'], "."), 1);
                        $localFile = __DIR__ . $fixSource . $arr['src'];
                        if(!file_exists($localFile)){
                            echo $localFile." 资源不存在false\n";
                            continue;
                        }
                        if($suffix == 'jpg' || $suffix == 'png') {
                            //上传图片到阿里云
                            $urlInfo = uploadImg($localFile, $localFileName);
                        }else{
                            //上传视频、音频到阿里云
                            $urlInfo = uploadMedia($client,$localFile, $localFileName, $suffix);

                        }
                        if($urlInfo === false){
                            echo $localFile."上传false!\n";
                            continue;
                        }

                        array_push($vidArr, $urlInfo['vid']);
                        $arr['src'] = $urlInfo['url'];
                    }
                    $returnChilds[] = $arr;
                }

                $result['childs'] = $returnChilds;
            }

            //insert formal detail
            $insertData = [
                'content' => json_encode($result, JSON_UNESCAPED_UNICODE),
                'screen'  => $screen,
                'vid' => json_encode($vidArr),
                'ageRange' => implode(',',$result['age'])
            ];
            $insertFormalDetailResp =  $wsgsMysqli->insertFormalDetail($cid, $courseName,$insertData);
            if($insertFormalDetailResp['status']){
                echo $json."入库成功!\n";
            }else{
                echo $json."入库false     error sql:  ".$insertFormalDetailResp['data'];
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

