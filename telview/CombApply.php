<?php
/**
 * 接收ajax上传的文件，读取文件内容进行组合(从m个数中取出n个数，可以取出多少组)，并保存在文件中
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/18
 * Time: 17:51
 */
ini_set('display_errors', 0);
ini_set("memory_limit", "2048M");
set_time_limit(0);
header("Content-Type: text/json;charset=utf-8");

class CombApply {
    public $resp = [
        'code' => 0,
        'data' => '',
        'msg'  => ''
    ];

    public function __construct(){
        $this->uploadFile();
    }

    /**
     * 上传文件
     */
    public function uploadFile(){
        date_default_timezone_set( 'Asia/Shanghai' );
        $num = $_POST['num'] ?: 3;
        if(!is_numeric($num)) {
            $this->resp['msg'] = 'param num is must integer';
            $this->httpResponse($this->resp);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            if (!is_uploaded_file($_FILES["file"]['tmp_name'])) {
                $this->resp['msg'] = 'The post data is empty';
                $this->httpResponse($this->resp);
            }
        }else{
            $this->resp['msg'] = 'request method is not post';
            $this->httpResponse($this->resp);
        }
        $file = $_FILES["file"];
        //var_dump($file);exit;
        $save_dir='/data/dianpuhaibao/';
        if(!is_dir($save_dir)){
            mkdir($save_dir,0777,true);
        }

        $upFileName = 'comb';
        $save_fullpath = $save_dir.$upFileName;

        $filename=$file["tmp_name"];
        $pinfo=pathinfo($file["name"]);
        //var_dump($pinfo);exit;
        $ftype=$pinfo['extension'];
        $destination = $save_fullpath.".".$ftype;
        if(!move_uploaded_file ($filename, $destination)) {
            $this->resp['msg'] = 'move  error, please contact the administrator';
            $this->httpResponse($this->resp);
        }

        //读取comb文件存入数组
        $handler = fopen($destination,'r'); //打开文件
        $combArr = [];
        while(!feof($handler)){
            $lineData = fgets($handler,4096);
            if(empty($lineData)) {
                continue;
            }
            $string = trim($lineData);
            $combArr[] = $string;
        }
        fclose($handler); //关闭文件

        //组合算法
        $combinationResArr = $this->combination($combArr, $num);
        //处理后的数据放入待下载的文件中
        $loadFile = $save_dir.'load.txt';
        unlink($loadFile);
        foreach( $combinationResArr as $combinationArr ) {
            file_put_contents($loadFile, implode(',', $combinationArr)."\n", FILE_APPEND);
        }

        //截取前10组数据用于展示
        //$returnViewRes = array_slice($combinationResArr, 0 ,10);

        $this->resp['code'] = 200;
        $this->resp['data'] = true;
        $this->httpResponse($this->resp);
    }

    public function combination($ar, $num) {
        $ar = array_filter($ar);
        $control = range(0, $num-1);
        $k = false;
        $total = count($ar);
        while($control[0] < $total-($num-1)) {
            $t = array();
            for($i=0; $i <$num; $i++) $t[] = $ar[$control[$i]];
            $r[] = $t;

            for($i=$num-1; $i>=0; $i--) {
                $control[$i]++;
                for($j=$i; $j <$num-1; $j++) $control[$j+1] = $control[$j]+1;
                if($control[$i] < $total-($num-$i-1)) break;
            }
        }
        return $r;
    }

    public function httpResponse($response){
        header("Access-Control-Allow-Origin:*");
        echo json_encode($response);
        exit;
    }
}

$obj = new CombApply();

