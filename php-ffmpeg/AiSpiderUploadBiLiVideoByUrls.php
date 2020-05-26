<?php
/** B站视频(无水印)根据url上传指定的视频到阿里云平台，并保存视频信息入库脚本
 *
 */
header("Content-Type: text/html;charset=utf-8");
ini_set("memory_limit", "1024M");
set_time_limit(0);

require_once __DIR__  . '/ali_video/voduploadsdk/Autoloader.php';
require_once __DIR__  . '/shell_ffmpeg/vendor/autoload.php';
use vod\Request\V20170321 as vod;   //视频上传、获取播放地址、播放凭证等

date_default_timezone_set('PRC');
define('ACCESSKEYID','');
define('ACCESSKEYSECRET','');
define('SHOTS_TEMPLATE','');
define("JIAMENG_91JM_PROJECT", "");
define("SOURCE", "");
require_once __DIR__ . "/VideoMysqli.php";
$params = [
    "host" => "",
    "user" => "",
    "password" => '',
    "dbName" => ""
];

$errIDArr = [];
$videoMysqli = VideoMysqli::getInstance($params);

//B站视频相关文章json 文件所在位置
$data_path = '/video/data/bilibili/';

//已上传的B站视频本地保存位置
//$uploadBiLiPath = '/data/cron/resources/bili_uploaded/';
$uploadBiLiPath = '/video/bili_uploaded/';

if(!is_dir($data_path)){
    echo $data_path.'不存在!';
    exit;
}

if(!is_dir($uploadBiLiPath)){
    mkdir($uploadBiLiPath,0777,true);
}

$urls_arr = rese_readfile();

echo "脚本开始执行...\n";
$start_time = time();

$handle = opendir($data_path);
$i = 0;
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
            $title = $result['titel'];  //视频标题
            $author = $result['author'];  //作者名称
            $description = $result['description'];  //视频描述
            $description = str_replace("'","\'",$description);
            $tag = $result['tag'];  //视频标签
            $video_review = $result['play'];  //播放量
            $authorPic = $result['authorPic'];  //作者头像地址
            $url = $result['url'];  //视频详情页url
            //判断当前url是否在待上传的url列表中
            if(!in_array($url, $urls_arr)){
                continue;
            }

            $jsonMp4 = str_replace('.json','.mp4',$json);
            if(!file_exists($data_path.$jsonMp4)){
                echo $jsonMp4." 视频不存在\n";
                continue;
            }
            echo "当前时间: ".date("Y-m-d H:i:s")."     开始上传B站无水印视频相关文件:".$jsonMp4."\n";
            $uploadStime = time();
            //上传本地视频
            $jsonMp4Path = $data_path.$jsonMp4;
            $vid = testUploadLocalVideo(ACCESSKEYID, ACCESSKEYSECRET, $jsonMp4Path, $jsonMp4);
            if($vid === false){
                echo $jsonMp4."上传false!\n";
                continue;
            }

            $client = initVodClient(ACCESSKEYID, ACCESSKEYSECRET);
            //视频的截图等基本信息
            $vid_name = str_replace("'","\'",$title);
            $vid_date = '';
            try {
                while(true){
                    $videoInfo = getVideoInfo($client, $vid);
                    $status = $videoInfo->Video->Status;
                    if($status == "Normal"){
                        break;
                    }else{
                        sleep(1);
                    }
                }
                $vid_date =  $videoInfo->Video->CreateTime;
                //$vid_coverpic = $videoInfo->Video->Snapshots->Snapshot;   //获取视频信息里面的是封面截图
            } catch (Exception $e) {
                echo $jsonMp4."获取视频封面截图等信息false    ". $e->getMessage()."\n";
            }

            //进行普通截图
            $vid_normalpic = [];
            try {
                $shotStatus = submitSnapshotJob($client,$vid);
            } catch (Exception $e) {
                echo $jsonMp4."普通截图false    ". $e->getMessage()."\n";
            }

            //获取截图数据
            if(isset($shotStatus->SnapshotJob->JobId)) {
                while(true){
                    try {
                        $normarShotsObj = listSnapshots($client, $vid);
                        if(isset($normarShotsObj->MediaSnapshot->Snapshots->Snapshot)){
                            break;
                        }else{
                            sleep(1);
                        }
                    } catch (Exception $e) {
                        sleep(1);
                        //echo $video."获取普通截图false    ". $e->getMessage()."\n";
                    }
                }
                $snapshotObjArr = $normarShotsObj->MediaSnapshot->Snapshots->Snapshot;
                foreach($snapshotObjArr as $snapshotObj){
                    $vid_normalpic[] = $snapshotObj->Url;
                }
            }

            //获取视频的播放地址
            $vid_address = [];
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
                        $vid_address[] = [
                            'Format' => $obj->Format,
                            'Height' => $obj->Height,
                            'Width' => $obj->Width,
                            'Definition' => $obj->Definition,  // FD流畅 LD标清 SD高清 HD超清 OD原画
                            'PlayURL' => $obj->PlayURL
                        ];
                    }
                    break;
                } catch (Exception $e) {
                    echo $jsonMp4."获取视频地址false    ". $e->getMessage()."\n";
                }
            }


            //insert post to db
            $params=[
                'vid_name' => $vid_name
            ];
            $post_id = 0;
            $insertPostStatus =  $videoMysqli->insertPosts($params);
            if($insertPostStatus['status'] === true){
                $post_id= $insertPostStatus['data'];
            }else{
                echo $jsonMp4."   insert posts false\n";
                echo "false error sql:" . $insertPostStatus['data']."\n";
            }

            //insert into wp_postmeta
            $metaValue = [
                'source' => JIAMENG_91JM_PROJECT,
            ];
            $insertMetaResp = $videoMysqli->insertMetaPosts($post_id, $metaValue);
            if ($insertMetaResp['status'] == false){
                echo $jsonMp4."   insert postmeta false\n";
                echo "false error sql:" . $insertMetaResp['data']."\n";
            }

            //insert videoinfo to db

            $params['vid_date'] = $vid_date;
            $params['create_date'] = date("Y-m-d H:i:s");


            $insertVideoInfoStatus =  $videoMysqli->insertVideoInfo($params);
            if($insertVideoInfoStatus['status'] === true){
                echo "当前时间: ".date("Y-m-d H:i:s")."     视频:".$jsonMp4." vid: ".$vid."   上传完成, 用时".(time()-$uploadStime)."s \n";
            }else{
                echo $jsonMp4."   insert video_info false\n";
                echo "false error sql:" . $insertVideoInfoStatus['data']."\n";
            }

            //上传视频成功后，移动当前视频及文件到指定位置
            $mv_data = str_replace('.json','',$json);
            $command = "mv -f ".$data_path.$mv_data.'* '.$uploadBiLiPath;
            shell_exec($command);
            $i += 1;

//            if($i == 2) {
//                exit;
//            }
        }
    }
}

closedir($handle);
echo '总共上传视频: '.$i.'个';
echo "\n";
echo "\n";
echo "\n";
echo "\n";
echo "更新此次数据用时：". (time()-$start_time) . "s \n";


function initVodClient($accessKeyId, $accessKeySecret) {
    $regionId = 'cn-shanghai';  // 点播服务接入区域
    $profile = DefaultProfile::getProfile($regionId, $accessKeyId, $accessKeySecret);
    return new DefaultAcsClient($profile);
}

/**
 * 上传本地视频
 * @param $accessKeyId
 * @param $accessKeySecret
 * @param $filePath
 */
function testUploadLocalVideo($accessKeyId, $accessKeySecret, $filePath, $videoName){
    try {
        $uploader = new AliyunVodUploader($accessKeyId, $accessKeySecret);
        $uploadVideoRequest = new UploadVideoRequest($filePath, $videoName);
        $res = $uploader->uploadLocalVideo($uploadVideoRequest);
        return $res;
    } catch (Exception $e) {
        printf("testUploadLocalVideo Failed, ErrorMessage: %s\n Location: %s %s\n Trace: %s\n",
            $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
        return false;
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
    //$request->setFormats('m3u8');
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

//加载待上传的urls列表
function rese_readfile(){
    $file = __DIR__ . '/urls.txt';

    $resp = [];
    $handler = fopen($file,'r'); //打开文件
    while(!feof($handler)){
        $lineData = fgets($handler,4096);
        $resp[] = trim($lineData);
    }
    fclose($handler); //关闭文件
    return array_filter($resp);
}