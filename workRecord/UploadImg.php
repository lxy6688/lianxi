<?php
/**
 * 接收参数 img url，上传到阿里云oss服务器,并返回给调用者新的img url
 * 标准表单上传图片方式，服务端处理图片，保存到本地，再调用阿里云oss接口，上传到云平台
 *
 * 参考博客： https://blog.csdn.net/weixin_30699741/article/details/97565908
 * 参考博客： https://blog.csdn.net/qq_37779793/article/details/102573996  小燕雀博客
 *
 * Created by PhpStorm.
 * User: lxy
 * Date: 2019/10/14
 * Time: 17:06
 */
ini_set('display_errors', 0);
header("Content-Type: text/json;charset=utf-8");
require_once('autoload.php');
use OSS\OssClient;
use OSS\Core\OssException;

class UploadImg {
    private $_accessKeyId = '';
    private $_accessKeySecret = '';
    private $_endpoint = '';
    private $_ossClient = null;

    public $resp = [
        'code' => 0,
        'data' => '',
        'msg'  => ''
    ];

    public function __construct(){
        $this->_init();
    }

    private function _init() {
        if(!isset($this->_ossClient)) {
            $this->_ossClient = new OssClient($this->_accessKeyId, $this->_accessKeySecret, $this->_endpoint);
        }
        return $this->_ossClient;
    }

    /**
     * 标准表单图片上传处理
     */
    public function index(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            if (!is_uploaded_file($_FILES["file"]['tmp_name'])) {
                $this->resp['msg'] = 'The post data is empty';
                $this->httpResponse($this->resp);
            }
        }
        $file = $_FILES["file"];
        $save_dir='/data/dianpuhaibao/';
        if(!file_exists($save_dir)){
            mkdir($save_dir,0777,true);
        }

        $upFileName = microtime(true).mt_rand();
        $save_fullpath = $save_dir.$upFileName;

        $filename=$file["tmp_name"];
        $image_size = getimagesize($filename);
        $pinfo=pathinfo($file["name"]);
        $ftype=$pinfo['extension'];
        $destination = $save_fullpath.".".$ftype;
        if(!move_uploaded_file ($filename, $destination)) {
            $this->resp['msg'] = 'move img error, please contact the administrator';
            $this->httpResponse($this->resp);
        }

        $lastUpFilename = $upFileName.'.'.$ftype;
        $newImgUrlArr = $this->uploadImg($lastUpFilename, $destination);
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

    //二进制数据流接收方式
    // 参考： https://www.jb51.net/article/102575.htm
//    public function index(){
//        //$streamData = file_get_contents( 'php://input' );
//        $streamData = $_POST;
//        if(empty($streamData)){
//            $this->resp['msg'] = 'The post data is empty';
//            $this->httpResponse($this->resp);
//        }
//
//        //先将图片下载到本地
//        $imageArr = $this->crabImage($streamData);
//        //echo json_encode($imageArr);exit;
//        $oldUrl = $imageArr['save_path'];
//        $newImgUrlArr = $this->uploadImg($imageArr['file_name'], $oldUrl);
//        unlink($oldUrl);
//        if($newImgUrlArr['status'] == false) {
//            $this->resp['msg'] = $newImgUrlArr['data'];
//            $this->httpResponse($this->resp);
//        }
//
//        $this->resp['code'] = 1;
//        $this->resp['data'] = $newImgUrlArr['data'];
//        $this->resp['msg'] = '';
//        $this->httpResponse($this->resp);
//    }

    /**
     * @param $img_url
     * @param string $save_dir
     * @param null $file_name
     * @return array|bool
     */
    public function crabImage($stream, $save_dir='/data/dianpuhaibao/'){
        //生成唯一的文件名
        $_ext = '.jpg';
        $file_name = microtime(true).mt_rand().$_ext;

        if(!file_exists($save_dir)){
            mkdir($save_dir,0777,true);
        }
        $save_fullpath = $save_dir.$file_name;
        file_put_contents($save_fullpath, $stream, true);
        return array('file_name'=>$file_name,'save_path'=>$save_dir.$file_name,'ext'=>$_ext);
    }

    /**
     * aliyun-oss 上传图片
     */
    public function uploadImg($upload_name, $file_path){
        if($this->_ossClient == null){
            $this->_init();
        }
        $bucket= '007dir';
        $upload_path = 'wp-content/uploads/' . $upload_name;
        try{
            $upload_res = $this->_ossClient->uploadFile($bucket, $upload_path, $file_path);
            if ($upload_res['info']['http_code'] == 200) {
                $str =  str_replace("http://xxx.com","https://xxx.cn", $upload_res['info']['url']);
                return [
                    'status' => true,
                    'data'   => $str
                ];
            }
            return [
                'status' => false,
                'data'   => 'https上传ali-oss服务失败!'
            ];
        } catch(OssException $e) {
            return [
                'status' => false,
                'data'   => $e->getMessage()
            ];

        }
    }

    public function httpResponse($response){
        echo json_encode($response);
        exit;
    }
}
(new UploadImg())->index();