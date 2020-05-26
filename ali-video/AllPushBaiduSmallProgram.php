<?php
/**
 * 百度小程序定时推送(按周级和按天级同时分别推送)
 *
 */
ini_set('display_errors', 0);
header("Content-Type: text/json;charset=utf-8");

require_once "DaoMysqli.php";
class PushBaiduSmallProgram {
    private $_appKey = '';
    private $_appSecret = '';
    private $_appToken = null;
    public  $path = '/pages/content/content?id=';

    private $_params = [
        "host" => "",
        "user" => "",
        "password" => '',
        "dbName" => ""
    ];

    public function __construct(){
        $this->daoMysqli = DaoMysqli::getInstance($this->_params);
    }

    /**
     * push to baidu small program
     */
    public function index(){
        echo "\n\n";
        echo "=================================\n";
        echo date("Y-m-d H:i:s")."开始推送数据到百度小程序...\n";


        //先进行周级推送
        $weekSmallPid = file_get_contents('/push_small/week_smallpid');
        $weekSmallPid = intval($weekSmallPid);
        $offset = empty($weekSmallPid)? 0 : $weekSmallPid;

        $weekAllIDsResObj =  $this->daoMysqli->getIDsByPublish($offset);
        if(empty($weekAllIDsResObj)) {
            $this->errorExit('week data is null');
        }

        $pathArr = [];
        $weekIDArr = [];
        foreach( $weekAllIDsResObj as $key => $weekAllIDsRes){
            if($key == 0) {
                echo "当天按周级推送的第一个文章ID是：".$weekAllIDsRes->ID."\n";
            }
            $ID = $weekAllIDsRes->ID;
            array_push($pathArr, $this->path.$ID);
            array_push($weekIDArr, $ID);
        }

        //生成token
        $tokenRes = $this->getToken($this->_appKey, $this->_appSecret);
        if(empty($tokenRes)) {
            $this->errorExit('get token false');
        }
        $tokenObj = json_decode($tokenRes);
        $this->_appToken = $tokenObj->access_token;
        //push
        $this->push($pathArr);
        $weekSmallPid += 500;
        file_put_contents('/push_small/week_smallpid',$weekSmallPid);
        echo date("Y-m-d")."按周级推送成功...\n";
        echo "=================================\n";
    }

    /**
     * push to baidu small program
     * @param $pathArr
     */
    public function push($pathArr, $type = 0){
        $api = 'https://openapi.baidu.com/rest/2.0/smartapp/access/submitsitemap/api?';
        $params = [
            'access_token' => $this->_appToken,
            'type' => $type,
            'url_list' => implode(',',$pathArr),
        ];
        $result = $this->post($api, $params);
        $resObj = json_decode($result);
        if( isset($resObj->errno) && $resObj->errno == 0 ) {
            //echo "push success!\n";
        }else{
            $this->errorExit($resObj->msg);
        }
    }

    /**
     * get token
     * @param $appKey
     * @param $appSecret
     */
    public function getToken($appKey, $appSecret){
        $params = [
            'grant_type' => 'client_credentials',
            'client_id'  => $appKey,
            'client_secret' => $appSecret,
            'scope'      => 'smartapp_snsapi_base'
        ];
        $api = 'https://openapi.baidu.com/oauth/2.0/token?';
        return $this->post($api, $params);
        //var_dump($this->post($api, $params));
    }

    /**
     * post request
     * @param $api
     * @param $data
     */
    public function post($api, $data){
        $ch = curl_init();
        $options =  array(
            CURLOPT_URL => $api,
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_NOBODY => false,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_HTTPHEADER => array('Content-Type: application/x-www-form-urlencoded'),
        );
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        return $result;
        //echo $result;
    }


    public function errorExit($msg){
        echo $msg."\n";
        exit;
    }

}
(new PushBaiduSmallProgram())->index();