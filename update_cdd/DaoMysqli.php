<?php
/**
 * update wp_posts post_content field and add  dynamic img
 *
 * User: lxy
 * Date: 2019/8/9
 * Time: 15:46
 */
final class DaoMysqli {
    private static $_daoMysqli;
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
        if(!(self::$_daoMysqli instanceof DaoMysqli)) {
            self::$_daoMysqli = new self($params);
        }
        return self::$_daoMysqli;
    }

    //根据文章标题获得文章ID
    public function getIdByTitle($title = ''){
        if(empty($title)){
            return false;
        }
        $title = str_replace('\'','',$title);

        $sql = "select ID,post_title,post_touzi from wp_posts where post_title = '".$title."'";
        $res = $this->mySQLi-> query($sql);
        $res = $res->fetch_assoc();
        return $res;
    }

    //根据店铺链接获得文章ID
    public function getIdByUrl($url = ''){
        if(empty($url)){
            return false;
        }
        $url = str_replace('\'','',$url);

        $sql = "select ID,post_title,post_touzi from wp_posts where post_url = '".$url."'";
        $res = $this->mySQLi-> query($sql);
        $res = $res->fetch_assoc();      //返回一维数组
        return $res;
    }

    /**
     * wp_posts表 根据文章ID更新 content 内容、加上项目头图(轮播图)
     *
     * @param $params  fields数组
     */
    public function updateFields($id,$params=[]){
        if(empty($params)) {
            return false;
        }

        //$sql = "update wp_posts set post_content='".$params['post_content']."', post_touzi='".$params['post_touzi']."',post_join='".$params['post_join']."', post_toutu='".$params['post_toutu']."' where ID=".$id;
        $sql = "update wp_posts set post_content='".$params['post_content']."', post_touzi='".$params['post_touzi']."',post_address='".$params['post_address']."', post_toutu='".$params['post_toutu']."', post_money='".$params['post_money']."',guid='".$params['guid']."',post_tupian='".$params['post_tupian']."',post_toutu='".$params['post_toutu']."' where ID=".$id;
        $res = $this->mySQLi-> query($sql);
        if($res) {
            return true;
        }else{
            return $sql;
        }
    }

    /**
     * only update field post_toutu
     * @param $id
     * @param array $params
     * @return bool|string
     */
    public function updateArticle($id,$params=[]){
        if(empty($params)) {
            return false;
        }

        $sql = "update wp_posts set post_toutu='".$params['post_toutu']."' where ID=".$id;
        $res = $this->mySQLi-> query($sql);
        if($res) {
            return true;
        }else{
            return $sql;
        }
    }

    /**想查哪些字段查哪些
     * @param string $field
     * @return mixed
     */
    public function getField($field = ''){
        $field = str_replace('\'','',$field);
        $sql = "select ".$field." from wp_posts as p left join wp_postmeta as pe on p.ID = pe.post_id where p.post_parent = 0 and pe.meta_value = 'spider-kuaima-project'";
        $res = $this->mySQLi-> query($sql);
        while($rows = $res->fetch_object()){
            $resp[] = $rows;
        }
        return $resp;     //返回一个数组, 数组元素是对象
    }

    //根据文章标题获得文章ID
    public function getListById($id = ''){
        if(empty($id)){
            return false;
        }
        $id = intval($id);
        $sql = "select post_title,post_url from wp_posts where ID = ".$id;
        $res = $this->mySQLi-> query($sql);
        $res = $res->fetch_assoc();
        return $res;
    }

    //查询ID、guid、parent字段
    public function getGuid(){
        $sql = "select ID,guid,post_parent from wp_posts";
        $res = $this->mySQLi-> query($sql);
        while($rows = $res->fetch_object()){
            $resp[] = $rows;
        }
        return $resp;
    }

    //根据id查询guid、parent字段
    public function getParentGuid($id){
        if(empty($id)){
            return false;
        }
        $sql = "select ID,guid from wp_posts where ID=".$id;
        $res = $this->mySQLi-> query($sql);
        $res = $res->fetch_assoc();
        return $res;
    }

    public function updateGuidToTupian($id,$guid){
        $sql = "update wp_posts set post_tupian='".$guid."' where ID=".$id;
        $res = $this->mySQLi-> query($sql);
        if($res) {
            return true;
        }else{
            return $sql;
        }
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

    //查询ID、post_content字段
    public function getContent(){
        $resp = [];
        $sql = "select ID,post_title,post_content from wp_posts";
        $res = $this->mySQLi-> query($sql);
        while($rows = $res->fetch_object()){
            $resp[] = $rows;
        }
        return $resp;
    }

    public function updateHttpToHttps($id,$params=[]){
        if(empty($params)) {
            return false;
        }
        $sql = "update wp_posts set post_content='".$params['post_content']."' where ID=".$id;

        $res = $this->mySQLi-> query($sql);
        if($res) {
            return true;
        }else{
            return $sql;
        }
    }

    /**
     * 查询已发布的项目的 ID, 标题post_title,正文post_content
     */
    //查询ID、post_content字段
    public function getProjectsByPublish(){
<<<<<<< HEAD
        $sql = "select ID,post_title,post_content from wp_posts where post_status='publish'";
        $res = $this->mySQLi-> query($sql);
        while($rows = $res->fetch_object()){
            $resp[] = $rows;
        }
        return $resp;
    }
=======
    $sql = "select ID,post_title,post_content from wp_posts where post_status='publish'";
    $res = $this->mySQLi-> query($sql);
    while($rows = $res->fetch_object()){
        $resp[] = $rows;
    }
    return $resp;
}
>>>>>>> b0b011766c332cd9ddcd439aff9cede0ce49e0c1

    //wp_tag_statistics post_id对应的记录是否存在
    public function getTagStatisById($id){
        $id = intval($id);
        $sql = "select id from wp_tag_statistics where post_id = ".$id;

        //当前id是否存在对应标签tag
        //$sql = "select wtt.term_taxonomy_id from wp_term_relationships as wte left join wp_term_taxonomy as wtt on wte.term_taxonomy_id = wtt.term_taxonomy_id where wte.object_id=".$id." and wtt.taxonomy = 'post_tag'";

        $res = $this->mySQLi-> query($sql);
        $res = $res->fetch_assoc();
        return $res;
    }

    //del 已经存在的wp_tag_statistics 记录
    public function delTagStatisById($id){
        $id = intval($id);
        $sql = "delete from wp_tag_statistics where post_id=".$id;
        $res = $this->mySQLi-> query($sql);
        if($res) {
            return true;
        }else{
            return $sql;
        }
    }

    //插入新的wp_tag_statistics 记录
    public function insertTagStatisById($id, $params){
        $id = intval($id);
        $sql = "INSERT INTO wp_tag_statistics (post_id, keyword, `count`) VALUES ";
        foreach ($params as $keyword) {
            $sql .= "(".$id.",'".$keyword."',1),";
        }
        $sql = rtrim($sql,',');
        $res = $this->mySQLi-> query($sql);
        return $res;
    }

<<<<<<< HEAD
    /* wp_terms 的tag标签，统一脚本更改 */
    //wp_terms tag object_id 查询对应的tag
    public function getTermsTagById($id){
=======
    /* wp_terms 的tag标签，统一脚本更改  *************************************************/
    //wp_terms tag object_id 查询对应的tag
    public function getTermsTagById($id){
        $resp = [];
>>>>>>> b0b011766c332cd9ddcd439aff9cede0ce49e0c1
        $id = intval($id);
        $sql = "select wpt.term_id,wpt.name from wp_terms as wpt left join wp_term_taxonomy as wtt on wpt.term_id = wtt.term_id left  join wp_term_relationships as wte  on  wtt.term_taxonomy_id=wte.term_taxonomy_id  where wte.object_id=".$id." and wtt.taxonomy = 'post_tag'";
        $res = $this->mySQLi-> query($sql);
        while($rows = $res->fetch_object()){
            $resp[] = $rows;
        }
        return $resp;
    }

    /**
     * @param $id
     * @param $addArr     新加标签 tag
     * @param $deleteArr  删除标签 tag
     */
    public function handleTermsTag($id,$addArr=[],$deleteArr=[]){
        $id = intval($id);
        $this->mySQLi-> query('start transaction');
        if(!empty($addArr) && empty($deleteArr)){
            //只有新加tag即可
<<<<<<< HEAD
        }elseif (!empty($deleteArr) && empty($addArr)) {
            //只有删除标签
=======
            $addRes = $this->addTermsTag($id,$addArr);
            if($addRes) {
                $this->mySQLi->commit();
                return true;
            }else{
                $this->mySQLi->rollback();
                return false;
            }
        }elseif (!empty($deleteArr) && empty($addArr)) {
            //只有删除标签
            $delRes = $this->delTermsTag($id,$deleteArr);
            if($delRes){
                $this->mySQLi->commit();
                return true;
            }else{
                $this->mySQLi->rollback();
                return false;
            }
>>>>>>> b0b011766c332cd9ddcd439aff9cede0ce49e0c1
        }elseif (!empty($deleteArr) && !empty($addArr)){
            //既有删除又有新加
            $addRes = $this->addTermsTag($id,$addArr);
            $delRes = $this->delTermsTag($id,$deleteArr);
            if($addRes && $delRes){
                $this->mySQLi->commit();
                return true;
            }else{
                $this->mySQLi->rollback();
                return false;
            }
        }
    }

<<<<<<< HEAD
=======
    //添加新的terms tag标签
>>>>>>> b0b011766c332cd9ddcd439aff9cede0ce49e0c1
    public function addTermsTag($id,$arr){
        $sql = '';
        //add
        foreach ($arr as $keyword) {
            $sql = "INSERT INTO wp_terms (`name`, slug) VALUES ('".$keyword."','".urlencode($keyword)."')";
            $aRes = $this->mySQLi-> query($sql);
            $termIdArr[] = $this->mySQLi->insert_id;
        }
        foreach($termIdArr as $termId) {
            $sql = "INSERT INTO wp_term_taxonomy (`term_taxonomy_id`, term_id,taxonomy,description) VALUES (".$termId.",".$termId.",'post_tag','')";
            $bRes = $this->mySQLi-> query($sql);
            $ttmIdArr[] = $this->mySQLi->insert_id;
        }
        $sql = "INSERT INTO wp_term_relationships (object_id, term_taxonomy_id) VALUES ";
        foreach ($ttmIdArr as $ttmId) {
            $sql .= "(".$id.",".$ttmId."),";
        }
        $sql = rtrim($sql,',');
        $cRes = $this->mySQLi-> query($sql);
        if($aRes && $bRes && $cRes) {
            return true;
        }
        return false;
    }
<<<<<<< HEAD
=======

    //删除旧的terms tag标签
>>>>>>> b0b011766c332cd9ddcd439aff9cede0ce49e0c1
    public function delTermsTag($id,$arr){
        $sql = "select wpt.term_id,wpt.name from wp_terms as wpt left join wp_term_taxonomy as wtt on wpt.term_id = wtt.term_id left  join wp_term_relationships as wte  on  wtt.term_taxonomy_id=wte.term_taxonomy_id  where wte.object_id=".$id." and wtt.taxonomy = 'post_tag'";
        $res = $this->mySQLi-> query($sql);
        while($rows = $res->fetch_object()){
            $resp[$rows->name] = $rows->term_id;
        }

        //del
        foreach ($arr as $keyword) {
            $termIdArr[] = $resp[$keyword];
        }
        //var_dump($termIdArr);exit;
        $sql = "delete from wp_terms where term_id in (".implode(',',$termIdArr).")";
        $dRes = $this->mySQLi-> query($sql);
        if($dRes) {
            return true;
        }
        return false;
    }
<<<<<<< HEAD
=======

    /**
     * @param $name  白名单名称
     */
    public function insertWhiteTags($name) {
        $sql = "INSERT INTO wp_white_tags_link (`name`) VALUES ('".$name."')";
        return $this->mySQLi-> query($sql);
    }

    /**
     * @param $name  白名单名称
     */
    public function insertWhiteTagsToTerms($name) {
        $slug = urlencode($name);
        $sql = "INSERT INTO wp_terms (`name`,`slug`,`is_extratag`) VALUES ('".$name."','".$slug."',1)";
        return $this->mySQLi-> query($sql);
    }

    /**
     * @param $name  标题停止词(用于相似推荐表的title字段)
     */
    public function insertStopWordsOfTitle($name) {
        $sql = "INSERT INTO wp_titlestop_words (`name`) VALUES ('".$name."')";
        return $this->mySQLi-> query($sql);
    }

    /**
     * 更新/插入文章tags标签到相似推荐表 wp_similar_posts
     *
     * @param $id
     * @param $tags
     */
    public function updateInsertTagsToSimilar($id, $tags){
//        $sql = "SELECT pID FROM wp_similar_posts WHERE pID=".$id." limit 1";
//        $res = $this->mySQLi-> query($sql);
//        $pid = $res->fetch_assoc();

        $sql = "UPDATE wp_similar_posts SET tags='".$tags."' WHERE pID=".$id;
        $res = $this->mySQLi-> query($sql);
        if($res) {
            return true;
        }
        return $sql;
    }

    /**
     * 更新/插入文章tags标签到相似推荐表 wp_similar_posts
     *
     * @param $id
     * @param $tags
     */
    public function updateInsertTitlesToSimilar($id, $titles){
        $sql = "UPDATE wp_similar_posts SET title='".$titles."' WHERE pID=".$id;
        $res = $this->mySQLi-> query($sql);
        if($res) {
            return true;
        }
        return $sql;
    }

    /**
     * 获取 相似推荐表simalar_posts 中的title分词字段
     * @return array
     */
    public function getSimilarTitles(){
        $resp = [];
        $sql = "select wp.post_title,wp.ID,ws.title from wp_similar_posts as ws left join wp_posts as wp on wp.ID=ws.pID where wp.post_status = 'publish'";
        $res = $this->mySQLi-> query($sql);
        while($rows = $res->fetch_object()){
            $resp[] = $rows;
        }
        return $resp;
    }

    /**
     * 获取 相似推荐表simalar_posts 中的title分词字段
     * @return array
     */
    public function getSimilarTags(){
        $resp = [];
        $sql = "select wp.post_title,wp.ID,ws.tags from wp_similar_posts as ws left join wp_posts as wp on wp.ID=ws.pID where wp.post_status = 'publish'";
        $res = $this->mySQLi-> query($sql);
        while($rows = $res->fetch_object()){
            $resp[] = $rows;
        }
        return $resp;
    }

    /**
     * 获取 相似推荐表simalar_posts 中的title 的停止词
     * @return array
     */
    public function getTitleStopWords(){
        $resp = [];
        $sql = "SELECT `name` FROM wp_titlestop_words";
        $res = $this->mySQLi-> query($sql);
        while($rows = $res->fetch_object()){
            $resp[] = $rows;
        }
        return $resp;
    }

    /*******************导入3158平台的数据*************************/

    /**
     * 根据标题进行模糊查询
     * @param string $title 标题
     * @return array
     */
    public function getLikeTitles($title){
        $sql = "SELECT `post_title` FROM wp_posts WHERE post_status in ('publish','draft','pending') and  post_title LIKE '%".$title."%'";
        $res = $this->mySQLi-> query($sql);
        return $res->fetch_assoc();
    }

    /**
     * insert to wp_posts
     * @param $params   待插入参数数组
     * @return array|bool
     */
    public function insertPosts($params = []){
        $sql = "insert ignore into wp_posts (post_author,post_date,post_date_gmt,post_content,post_title,post_excerpt,post_status,comment_status,ping_status,post_modified,post_modified_gmt,post_parent,menu_order,post_mime_type,to_ping,pinged,post_content_filtered,post_address,post_company,post_url,post_mendian,post_join,post_area,post_tupian,post_touzi,post_toutu,post_quality)values('".$params['post_author']."','".$params['post_date']."','".$params['post_date_gmt']."','".$params['post_content']."','".$params['post_title']."','".$params['post_excerpt']."','".$params['post_status']."','".$params['comment_status']."','".$params['ping_status']."','".$params['post_modified']."','".$params['post_modified_gmt']."',".$params['post_parent'].",'".$params['menu_order']."','".$params['post_mime_type']."','".$params['to_ping']."','".$params['pinged']."','".$params['post_content_filtered']."','".$params['post_address']."','".$params['post_company']."','".$params['post_url']."','".$params['post_mendian']."','".$params['post_join']."','".$params['post_area']."','".$params['post_tupian']."','".$params['post_touzi']."','".$params['post_toutu']."',".$params['post_quality'].")";
        $res = $this->mySQLi-> query($sql);

        if($res) {
            return [
                'status' => true,
                'data' => $this->mySQLi->insert_id
            ];
        }else{
            return [
                'status' => false,
                'data'   => $sql
            ];
        }
    }

    /**
     * insert into wp_postmeta
     *
     * @param $postId
     * @param $metaValue
     * @return array
     */
    public function insertMetaPosts($postId, $metaValue){
        $sql = "insert ignore into `wp_postmeta` (`post_id`,`meta_key`,`meta_value`) VALUES ({$postId},'_post_source_type','".$metaValue['source']."')";
        $res = $this->mySQLi-> query($sql);

        $sql = "insert ignore into `wp_postmeta` (`post_id`,`meta_key`,`meta_value`) VALUES ({$postId},'_post_project_type','".$metaValue['type']."')";
        $res = $this->mySQLi-> query($sql);
        if($res) {
            return [
                'status' => true,
                'data' => ''
            ];
        }else{
            return [
                'status' => false,
                'data'   => $sql
            ];
        }
    }

    /**
     * 根据二级分类名称获取对应的一级分类名称
     *
     * @param $erjifenlei
     * @return mixed
     */
    public function getParentCateByChild($erjifenlei){

        $sql = "select name from wp_terms where term_id=(select wtt.parent from wp_term_taxonomy as wtt left join wp_terms as wt on wt.term_id=wtt.term_id where wt.name= '".$erjifenlei."' and wtt.taxonomy = 'category')";
        $res = $this->mySQLi-> query($sql);
        //var_dump($res);   //bool(false)
        if($res !== false){
            $res = $res->fetch_assoc();      //返回一维数组, 报错，可能$res是false，而不是结果集
            return (!empty($res))? $res['name'] : 400;    //400 表示没有查到一级分类
        }else{
            return 500;   //500表示当前分类可能对应多个父级分类
        }

    }

    /**
     * 插入项目和分类的对应关系
     * @param $postId       项目id
     * @param $categoryArr  分类数组
     * @return array
     */
    public function insertCategory($postId,$categoryArr){
        $res = '';
        $sql = "select a.term_id,term_taxonomy_id from wp_terms as a left join wp_term_taxonomy as b on a.term_id = b.term_id  where a.name='". $categoryArr['yijifenlei']."' and b.taxonomy='category'";
        $yiji_result = ($this->mySQLi-> query($sql))->fetch_assoc();
        if($yiji_result){
            //能查到预先创建的分类
            $term_taxonomy_id = $yiji_result['term_taxonomy_id'];
            $insert_sql = "insert into `wp_term_relationships` (`object_id`,`term_taxonomy_id`) VALUES ({$postId},'".$term_taxonomy_id."')";
            $this->mySQLi-> query($insert_sql);

            //erji
            $sql = "select a.term_id,term_taxonomy_id from wp_terms as a left join wp_term_taxonomy as b on a.term_id = b.term_id  where a.name='". $categoryArr['erjifenlei']."' and b.taxonomy='category'";
            $erji_result = ($this->mySQLi-> query($sql))->fetch_assoc();
            if($erji_result){
                $term_taxonomy_id = $erji_result['term_taxonomy_id'];
                $insert_sql = "insert into `wp_term_relationships` (`object_id`,`term_taxonomy_id`) VALUES ({$postId},'".$term_taxonomy_id."')";
                $res = $this->mySQLi-> query($insert_sql);
            }
        }else{
            //如果是新的分类,则用程序去添加
            //TODO
        }
        if($res) {
            return [
                'status' => true,
                'data' => ''
            ];
        }else{
            return [
                'status' => false,
                'data'   => ''
            ];
        }
    }

    /**
     * 查询 publish、draft、pending状态的content
     * @return array
     */
    public function getContentByStatus(){
        $resp = [];
        $sql = "select ID,post_content from wp_posts where post_status in ('publish','draft','pending')";
        $res = $this->mySQLi-> query($sql);
        while($rows = $res->fetch_object()){
            $resp[] = $rows;
        }
        return $resp;
    }

    /**
     * 包含tag的文章数
     *
     * @param string $tag
     * @return int
     */
    public function getCountByTag($tag = ''){
        if(empty($tag)){
            return 0;
        }
        $sql = "select count(*) as count from wp_posts where post_status='publish' and post_content like '%".$tag."%'";
        $res = $this->mySQLi-> query($sql);
        $res = $res->fetch_assoc();
        return (isset($res['count']) && !empty($res['count']))? $res['count'] : 0;
    }


    //插入新的wp_similar_tagpage 记录
    public function insertToTagPage($insertArr){
        $sql = "INSERT INTO wp_similar_tagpage (post_id,tag_name,post_name,site) VALUES ";
        $sql .= implode(',',$insertArr);
        $sql = rtrim($sql,',');
        $res = $this->mySQLi-> query($sql);
        return ($res)? $this->mySQLi->affected_rows : 0;
    }

    //插入新的wp_similar_tagpage 记录
    public function insertToTagPage_Two($insertArr){
        $sql = "INSERT INTO wp_similar_tagpage_two (post_id,tag_name,post_name,site) VALUES ";
        $sql .= implode(',',$insertArr);
        $sql = rtrim($sql,',');
        $res = $this->mySQLi-> query($sql);
        return ($res)? $this->mySQLi->affected_rows : 0;
    }


    //插入新的wp_related_words 记录 (相关热词)
    public function insertTagToRelateWords($insertArr){
        $sql = "INSERT INTO wp_related_words (post_id,tag_name,cate_id,child_cate_id,site) VALUES ";
        $sql .= implode(',',$insertArr);
        $sql = rtrim($sql,',');
        $res = $this->mySQLi-> query($sql);
        //return ($res)? $this->mySQLi->affected_rows : 0;
        return ($res)? [ 'status' => true, 'data' => $this->mySQLi->affected_rows ] : [ 'status' => false , 'data' => $sql];


//        if($res){
//            return [
//                'status' => true,
//                'num' => $this->mySQLi->affected_rows
//            ];
//        }else{
//            return [
//                'status' => false
//            ];
//        }
    }


    /**
     * 根据项目id 查询对应的一级二级分类id
     * @return array
     */
    public function getCategoryIdByPost($ID){
        $resp = [];
        $sql = "select wtr.object_id,wtt.term_id,wtt.parent from wp_term_relationships as wtr left join wp_term_taxonomy as wtt on wtr.term_taxonomy_id=wtt.term_taxonomy_id where wtr.object_id=".$ID." and  wtt.taxonomy='category'";
        $res = $this->mySQLi-> query($sql);
        while($rows = $res->fetch_object()){
            if($rows->parent == 0) {
                $resp['cate_id'] = $rows->term_id;
            }else{
                $resp['child_cate_id'] = $rows->term_id;
            }
        }
        return $resp;
    }

    /**
     * 查询所有项目(回收站和inhert状态的除外)的 ID, 标题post_title
     */
    public function getAllProjects(){
        //查询项目ID，标题、一级分类id
        //$sql = "select wpp.ID,wpp.post_title,wptt.term_id from wp_posts as wpp left join wp_term_relationships as wptr on wpp.ID=wptr.object_id left join wp_term_taxonomy as wptt on wptr.term_taxonomy_id=wptt.term_taxonomy_id where wptt.taxonomy='category' and wpp.post_status in ('publish','draft','pending') limit 100;";
        $sql = "select ID,post_title,post_content from wp_posts where post_status in ('publish','draft','pending')";
        $res = $this->mySQLi-> query($sql);
        while($rows = $res->fetch_object()){
            $resp[] = $rows;
        }
        return $resp;
    }


    /**按发布时间正序获取所有已发布文章的ID
     * @param integer $offset  查询的起始位置
     * @return array
     */
    public function getIDsByPublish($offset, $sort="desc", $limit = 500){
        $sql = "select ID,post_title from wp_posts where post_status='publish' order by post_date ".$sort." limit ".$offset.",".$limit;
        $res = $this->mySQLi-> query($sql);
        $resp = [];
        while($rows = $res->fetch_object()){
            $resp[] = $rows;
        }
        return $resp;
    }

>>>>>>> b0b011766c332cd9ddcd439aff9cede0ce49e0c1
}