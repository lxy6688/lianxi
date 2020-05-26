<?php
/**
 * wsgs mysqli operate
 *
 * User: lxy
 * Date: 2020/2/20
 * Time: 18:51
 */
final class WsgsMysqli {
    private static $_wsgsMysqli;
    private static $_redis = null;
    private $mySQLi;

    private $host;
    private $user;
    private $password;
    private $dbName;
    private $port;
    private $charset;

    private function __construct($params){
        $this->_initMysqli($params);
    }

    private function _initMysqli($params){
        $this->host = isset($params['host'])? $params['host'] : '';
        $this->user = isset($params['user'])? $params['user'] : '';
        $this->password = isset($params['password'])? $params['password'] : '';
        $this->dbName = isset($params['dbName'])? $params['dbName'] : '';
        $this->port = isset($params['port'])? $params['port'] : 3306;
        $this->charset = isset($params['charset'])? $params['charset'] : 'utf8';

        if( $this->host == '' || $this->user == '' || $this->password == '' || $this->dbName == '' ) {
            die('缺少必要参数');
        }

        $this->mySQLi = new MySQLi($this->host, $this->user, $this->password, $this->dbName, $this->port);
        if($this->mySQLi->connect_error){
            die('连接错误,错误信息: '.$this->mySQLi->connect_error);
        }
        $this->mySQLi -> set_charset($this->charset);
    }

    private function __clone(){}

    public static function getInstance($params){
        if(!(self::$_wsgsMysqli instanceof WsgsMysqli)) {
            self::$_wsgsMysqli = new self($params);
        }
        return self::$_wsgsMysqli;
    }

    public static function getRedis(){
        if(!isset(self::$_redis)) {
            self::$_redis = new Redis();
            self::$_redis->connect('127.0.0.1', 6379);
        }
        return self::$_redis;
    }

    /**
     * 添加册和课件
     *
     * @param $bookName     册名称
     * @param $courseArr    课件数组,包括名称、图片、简介等
     */
    public function insertCourseAndBook($bookName,$courseArr=[]){

        $this->mySQLi-> query('start transaction');

        $selectSql = "select id from book where name='".$bookName."'";
        $selectSqlRes = $this->mySQLi-> query($selectSql);
        if(empty($selectSqlRes)){
            $sql = "INSERT INTO book (`name`) VALUES ('".$bookName."')";
            $insertBookRes = $this->mySQLi-> query($sql);
            $bid = $this->mySQLi->insert_id;
        }else{
            while($rows = $selectSqlRes->fetch_object()){
                $bid = $rows->id;
            }

            $insertBookRes = true;
        }


        //add book data


        //add course data
        $courseName = $courseArr['courseName'];
        $courseImg = $courseArr['courseImg'];
        $courseDesc = $courseArr['courseDesc'];

        $sql = "INSERT INTO course (`name`,`img`,`desc`) VALUES ('".$courseName."','".$courseImg."','".$courseDesc."')";
        $insertCourseRes = $this->mySQLi-> query($sql);
        $cid = $this->mySQLi->insert_id;

        //add book_course_rela
        $sql = "INSERT INTO book_course_rela (`bid`,`cid`) VALUES ('".$bid."','".$cid."')";
        $insertRelaRes = $this->mySQLi-> query($sql);

        if($insertBookRes && $insertCourseRes && $insertRelaRes) {
            $this->mySQLi->commit();
            return [
                'status' => true,
                'data'   => $cid
            ];
        }else{
            $this->mySQLi->rollback();
            return [
                'status' => false,
                'data'   => ''
            ];
        }

    }

    /**
     * 添加上课课件详情
     *
     * @param $cid     课件id
     * @param $name    课件名称
     * @param array $insertData  待添加数组
     */
    public function insertFormalDetail($cid, $name,$insertData = []){
        $content = $insertData['content'];
        $screen = $insertData['screen'];
        $vid = $insertData['vid'];
        $ageRange = $insertData['ageRange'];

        $sql = "INSERT INTO formal_detail (`cid`,`name`,`content`,`screen`,`vid`,`age_range`) VALUES ('".$cid."','".$name."','".$content."',".$screen." ,'".$vid."','".$ageRange."')";
        $insertRes = $this->mySQLi-> query($sql);
        $detailId = $this->mySQLi->insert_id;
        if($insertRes) {
            $sql = "INSERT INTO course_rela (`detail_id`,`cid`,`type`) VALUES (".$detailId.",".$cid.",'formal')";
            $this->mySQLi-> query($sql);
            return [
                'status' => true,
                'data'   => ''
            ];
        }else{
            return [
                'status' => false,
                'data'   => $sql
            ];
        }
    }

    /**
     * 添加备课课件详情
     *
     * @param $cid     课件id
     * @param $name    课件名称
     * @param array $insertData  待添加数组
     */
    public function insertPrepareDetail($cid, $name,$insertData = []){
        $content = $insertData['content'];

        $sql = "INSERT INTO prepare_detail (`cid`,`name`,`content`) VALUES ('".$cid."','".$name."','".$content."')";
        $insertRes = $this->mySQLi-> query($sql);
        $detailId = $this->mySQLi->insert_id;
        if($insertRes) {
            $sql = "INSERT INTO course_rela (`detail_id`,`cid`,`type`) VALUES (".$detailId.",".$cid.",'prepare')";
            $this->mySQLi-> query($sql);
            return [
                'status' => true,
                'data'   => ''
            ];
        }else{
            return [
                'status' => false,
                'data'   => $sql
            ];
        }
    }

    /**
     * 模课、上课desc
     *
     * @param array $insertData  待添加数组
     */
    public function updateDescAndImg($updateData,$id){

        $sql = "update course set `desc` = '".$updateData['desc']."',img = '".$updateData['img']."' where id= ".$id;
        $insertRes = $this->mySQLi-> query($sql);
        if($insertRes) {
            return [
                'status' => true,
                'data'   => ''
            ];
        }else{
            return [
                'status' => false,
                'data'   => $sql
            ];
        }
    }


    /**
     * 学生作业视频url，更改为阿里云上对应的视频url
     *
     * @param $urlInfo   云视频信息，包括 vid 和 url
     */
    public function updateUrl($urlInfo){
        $vid = $urlInfo['vid'];
        $url = $urlInfo['url'];
        $oldUrl = $urlInfo['oldUrl'];

        $sql = "update student_homework_detail set url = '".$url."',vid='".$vid."' where url= '".$oldUrl."'";
        $res = $this->mySQLi-> query($sql);
        if($res) {
            return [
                'status' => true,
                'data'   => ''
            ];
        }else{
            return [
                'status' => false,
                'data'   => $sql
            ];
        }
    }

    /**
     * 添加 预习 课件详情
     *
     * @param array $insertData  待添加数组
     */
    public function insertPreviewDetail($insertData = []){
        $cid = $insertData['cid'];
        $name = $insertData['name'];
        $vid = $insertData['vid'];
        $url = $insertData['url'];
        $tag = $insertData['tag'];
        $type = $insertData['type'];

        $sql = "INSERT INTO preview_detail (`cid`,`name`,`url`,`tag`,`vid`,`type`) VALUES ('".$cid."','".$name."','".$url."','".$tag."' ,'".$vid."','".$type."')";
        $insertRes = $this->mySQLi-> query($sql);
        $detailId = $this->mySQLi->insert_id;
        if($insertRes) {
            $sql = "INSERT INTO course_rela (`detail_id`,`cid`,`type`) VALUES (".$detailId.",".$cid.",'preview')";
            $this->mySQLi-> query($sql);
            return [
                'status' => true,
                'data'   => ''
            ];
        }else{
            return [
                'status' => false,
                'data'   => $sql
            ];
        }
    }




















    /**按发布时间正序获取所有已发布文章的ID
     * @param integer $offset  查询的起始位置
     * @return array
     */
    public function getIDsByPublish($offset){
        $sql = "select ID,post_title from wp_posts where post_status='publish' order by post_date asc";
        $res = $this->mySQLi-> query($sql);
        while($rows = $res->fetch_object()){
            $resp[] = $rows;
        }
        return $resp;
    }

    /**
     * get publish post ID
     */
    public function getPublishId(){
        $sql = "select ID,post_date from wp_posts where post_status='publish'";
        $res = $this->mySQLi-> query($sql);
        while($rows = $res->fetch_object()){
            $resp[] = $rows;
        }
        return $resp;
    }


}