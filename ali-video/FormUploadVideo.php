<?php
/**
 * 单个视频上传
 * 标准表单方式上传视频至阿里云平台，并在本地数据库生成一条上传记录
 * 上传api： http://xxxxx/FormUploadVideo.php
 *
 *
 * php可能会对上传的文件有所限制,必要时修改php.ini中的几个参数：upload_max_filesize、post_max_size
 * max_execution_time、max_input_time、memory_limit
 * client_max_body_size：这是nginx服务器的限制参数
 * 参考： https://blog.csdn.net/luyaran/article/details/80666554    php上传超大文件
 *
 *
 *
 * 参考博客： https://blog.csdn.net/weixin_30699741/article/details/97565908
 * 参考博客： https://blog.csdn.net/qq_37779793/article/details/102573996  小燕雀博客
 * 参考博客： https://q.cnblogs.com/q/99912/   php form表单上传视频
 *
 */
ini_set('display_errors', 0);
header("Content-Type: text/json;charset=utf-8");
require_once  '/voduploadsdk/Autoloader.php';
require_once "/data/cron/VideoMysqli.php";
use vod\Request\V20170321 as vod;

date_default_timezone_set('PRC');
define('ACCESSKEYID','');
define('ACCESSKEYSECRET','');

define('SHOTS_TEMPLATE','');
define("JIAMENG_91JM_PROJECT", "");

class FormUploadVideo {
    private $_accessKeyId = '';
    private $_accessKeySecret = '';
    private $videoMysqli = null;

    public $resp = [
        'code' => 0,
        'data' => '',
        'msg'  => ''
    ];

    public function __construct(){
        $this->_init();
    }

    private function _init() {
        if(!isset($this->videoMysqli)) {
            $params = [
                "host" => "",
                "user" => "",
                "password" => '',
                "dbName" => ""
            ];
            $this->videoMysqli = VideoMysqli::getInstance($params);
        }
        return $this->videoMysqli;
    }

    /**
     * 标准表单视频上传处理
     */
    public function index(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            if (!is_uploaded_file($_FILES["file"]['tmp_name'])) {
                $this->resp['msg'] = 'The post data is empty';
                $this->httpResponse($this->resp);
            }
        }
        $file = $_FILES["file"];
        //var_dump($file);exit;
        $save_dir='/data/cron/resources/';
        if(!file_exists($save_dir)){
            mkdir($save_dir,0777,true);
        }

        $upFileName = microtime(true).mt_rand();
        $save_fullpath = $save_dir.$upFileName;

        $filename=$file["tmp_name"];
        $image_size = getimagesize($filename);
        $pinfo=pathinfo($file["name"]);
        //var_dump($pinfo);exit;
        $ftype=$pinfo['extension'];
        $destination = $save_fullpath.".".$ftype;
        if(!move_uploaded_file ($filename, $destination)) {
            $this->resp['msg'] = 'move img error, please contact the administrator';
            $this->httpResponse($this->resp);
        }

        $lastUpFilename = $upFileName.'.'.$ftype;
        //echo $lastUpFilename;exit;
        $newImgUrlArr = $this->uploadVideo($destination,$lastUpFilename);
        unlink($destination);
        if($newImgUrlArr['status'] == false) {
            $this->resp['msg'] = $newImgUrlArr['data'];
            $this->httpResponse($this->resp);
        }

        $this->resp['code'] = 1;
        $this->resp['data'] = $newImgUrlArr['data'];
        $this->resp['msg'] = '';
        $this->httpResponse($this->resp);
    }

    /**
     * 上传视频
     */
    public function uploadVideo($videoPath,$video){
        if($this->videoMysqli == null){
            $this->_init();
        }

        //上传本地视频
        $vid = $this->testUploadLocalVideo(ACCESSKEYID, ACCESSKEYSECRET, $videoPath, $video);
        if($vid === false){
            return [
                'status' => false,
                'data'   => 'upload false,vid is false!'
            ];
        }

        $client = $this->initVodClient(ACCESSKEYID, ACCESSKEYSECRET);
        //视频的截图、标题等基本信息
        $vid_name = $vid_date = '';
        try {
            while(true){
                $videoInfo = $this->getVideoInfo($client, $vid);
                $status = $videoInfo->Video->Status;
                if($status == "Normal"){
                    break;
                }else{
                    sleep(1);
                }
            }
            $vid_name =  $videoInfo->Video->Title;
            $vid_date =  $videoInfo->Video->CreateTime;
        } catch (Exception $e) {
            return [
                'status' => false,
                'data'   => "get video info false    ". $e->getMessage()
            ];
        }

        //进行普通截图
        $vid_normalpic = [];
        try {
            $shotStatus = $this->submitSnapshotJob($client,$vid);
        } catch (Exception $e) {
            return [
                'status' => false,
                'data'   => "普通截图false    ". $e->getMessage()
            ];
            //echo $video."普通截图false    ". $e->getMessage()."\n";
        }

        //获取截图数据
        if(isset($shotStatus->SnapshotJob->JobId)) {
            while(true){
                try {
                    $normarShotsObj = $this->listSnapshots($client, $vid);
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
        try {
            $playInfo = $this->getPlayInfo($client, $vid);
            $objArray = $playInfo->PlayInfoList->PlayInfo;  //数组形式的对象,  PlayURL播放地址
            foreach($objArray as $obj){
                $vid_address[] = [
                    'Format' => $obj->Format,
                    'Height' => $obj->Height,
                    'Width' => $obj->Width,
                    'Definition' => $obj->Definition,  // FD流畅 LD标清 SD高清 HD超清 OD原画
                    'PlayURL' => $obj->PlayURL
                ];
            }
        } catch (Exception $e) {
            return [
                'status' => false,
                'data'   => "get video address false    ". $e->getMessage()
            ];
        }

        //insert post to db
        $params=[
            'vid_name' => $vid_name
        ];
        $post_id = 0;
        $insertPostStatus =  $this->videoMysqli->insertPosts($params);
        if($insertPostStatus['status'] === true){
            $post_id= $insertPostStatus['data'];
        }else{
            return [
                'status' => false,
                'data'   => "insert db posts false"
            ];
        }

        //insert into wp_postmeta
        $metaValue = [
            'source' => JIAMENG_91JM_PROJECT,
        ];
        $insertMetaResp = $this->videoMysqli->insertMetaPosts($post_id, $metaValue);
        if ($insertMetaResp['status'] == false){
            return [
                'status' => false,
                'data'   => "insert db postmeta false"
            ];
        }

        //insert videoinfo to db
        $params['post_id'] = $post_id;
        $params['vid'] = $vid;
        $params['create_date'] = date("Y-m-d H:i:s");
        $insertVideoInfoStatus =  $this->videoMysqli->insertVideoInfo($params);
        if($insertVideoInfoStatus['status'] === true){
            return [
                'status' => true,
                'data'   => 'upload ok!'
            ];
        }else{
            return [
                'status' => false,
                'data'   => "insert video_info false"
            ];
        }
    }

    public function initVodClient($accessKeyId, $accessKeySecret) {
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
    public function testUploadLocalVideo($accessKeyId, $accessKeySecret, $filePath, $videoName){
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
    public function getPlayInfo($client, $videoId) {
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
    public function getVideoInfo($client, $videoId) {
        $request = new vod\GetVideoInfoRequest();
        $request->setVideoId($videoId);
        $request->setAcceptFormat('JSON');
        return $client->getAcsResponse($request);
    }

    public function httpResponse($response){
        echo json_encode($response);
        exit;
    }

    /**
     * 提交截图(普通截图)
     *
     * @param $client
     * @return mixed
     */
    public function submitSnapshotJob($client, $videoId) {
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

}
(new FormUploadVideo())->index();