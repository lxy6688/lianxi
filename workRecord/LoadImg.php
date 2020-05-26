<?php
/**
 * 接收参数 img url，从url下载图片到本地，处理后再上传到阿里云oss服务器,并返回给调用者新的img url
 *
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
     * 上传图片到ali-oss
     */
    public function index(){
        $imgUrl = $_GET['imgurl'];
        if(!isset($imgUrl) || empty($imgUrl)){
            $this->resp['msg'] = 'img url is null';
            $this->httpResponse($this->resp);
        }
        //先将图片下载到本地
        //$imgUrl = base64_decode($imgUrl);
        $imageArr = $this->crabImage($imgUrl);
        $oldUrl = $imageArr['save_path'];
        $newImgUrlArr = $this->uploadImg($imageArr['file_name'], $oldUrl);
        unlink($oldUrl);
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
     * @param $img_url
     * @param string $save_dir
     * @param null $file_name
     * @return array|bool
     */
    public function crabImage($img_url, $save_dir='/data/dianpuhaibao/', $file_name=null){
        //获取后缀名
        $img_size = substr(strrchr($img_url,'.'),1);
        if(!$img_size){
            $this->resp['msg'] = '没有获取后缀名,请上传正确的图片格式';
            $this->httpResponse($this->resp);
        }
        $_ext = '.'.$img_size;

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