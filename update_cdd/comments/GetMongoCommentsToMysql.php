<?php
/**
 * 从mongodb获取加盟项目数据,格式化处理后导入mysql
 *
 */
header("Content-Type: text/json;charset=utf-8");
ini_set("display_errors", 0);
ini_set("memory_limit", "1024M");
set_time_limit(0);
date_default_timezone_set( 'Asia/Shanghai' );

require_once "./DaoMysqli.php";
$params = [
    "host" => "",
    "user" => "",
    "password" => '',
    "dbName" => ""
];
$daoMysqli = DaoMysqli::getInstance($params);
/**
 * 获取mongodb的评论数据
 */
$manager = new MongoDB\Driver\Manager('mongodb://login:pwd@ip:port/db');

$filter = [];             //数组为空，查询集合中所有的文档
$options = [
    //'projection' => ['xmMatches' => 0],    //不查询 xmMatches
    'limit' => 10
];
// 查询数据
$query = new MongoDB\Driver\Query($filter, $options);
$cursor = $manager->executeQuery('db.table', $query);

echo "开始导数据...\n";
$start_time = time();
$totalSum =  $tongjiSum = 0;
foreach ($cursor as $document) {
    $id = $document->_id;
    $post_title = str_replace('\'','',$document->xmName);
    $xmId = $document->xmId;

    //过滤正文为空的项目
    if(empty($document->xmJieShaoHtm) && empty($document->xmYouShiHtm) && empty($document->xmTiaoJianHtm)) {
        echo "_id为:".$id."  标题为:".$post_title." 的文档正文为空\n";
        continue;
    }

    $jieshao = $youshi = $tiaojian = $liucheng = '';
    //格式化正文部分
    if(isset($document->xmJieShaoHtm) && !empty($document->xmJieShaoHtm)) {
        $jieshao= formatContent($document->xmJieShaoHtm);
        $patt = $post_title."项目介绍";
        $jieshao = str_replace($patt,'',$jieshao);

        $pat = "/".$post_title."加盟费用分析/i";
        if(preg_match($pat,$jieshao)){
            echo "正文详情含有加盟费用分析,xmID为:    ".$xmId."   文章标题:   ".$post_title."\n";

            $pattern = "/<img.*?src=\"(.*?)\".*?\/?>/i";
            preg_match_all($pattern,$jieshao,$m);
            $imgArr = $m[0];
            $lastImg = end($imgArr);
            $delpat = $post_title."加盟费用分析";
            $jieshao = str_replace([ $lastImg,$delpat.":",$delpat."：" ],'',$jieshao);
            //continue;
        }
    }else{
        echo "正文缺失xmJieShaoHtm,xmID为:    ".$xmId."   文章标题:   ".$post_title."\n";
    }

    if(isset($document->xmYouShiHtm) && !empty($document->xmYouShiHtm)) {
        $youshi= formatContent($document->xmYouShiHtm);
    }else{
        echo "正文缺失xmYouShiHtm,xmID为:    ".$xmId."   文章标题:   ".$post_title."\n";
    }

    if(isset($document->xmTiaoJianHtm) && !empty($document->xmTiaoJianHtm)) {
        $tiaojian= formatContent($document->xmTiaoJianHtm);
    }else{
        echo "正文缺失xmTiaoJianHtm,xmID为:    ".$xmId."   文章标题:   ".$post_title."\n";
    }
    if(isset($document->xmLiuChengHtm) && !empty($document->xmLiuChengHtm)) {
        $liucheng= formatContent($document->xmLiuChengHtm);
    }else{
        echo "正文缺失xmLiuChengHtm,xmID为:    ".$xmId."   文章标题:   ".$post_title."\n";
    }


    $toutuObjArr = $document->xmImgs;
    $toutuArr = [];
    if(!empty($toutuObjArr)) {
        foreach($toutuObjArr as $obj){
            $toutuArr[] = $obj->seoNetAddr;
        }
    }

    $quality = 1;
    //过滤头图数量小于1的项目
    if(count($toutuArr) <= 1) {
        if(count($toutuArr) == 1){
            echo "_id为:".$id."  标题为:".$post_title." 的文档头图数量等于1\n";
        }else{
            echo "_id为:".$id."  标题为:".$post_title." 的文档头图数量为空\n";
            continue;
        }

    }

    //比较数据库的标题,去重
    $titleResp = $daoMysqli->getLikeTitles($post_title);
    if(isset($titleResp) && !empty($titleResp)) {
        echo "_id为:".$id."  标题为:".$post_title." 的文档已有包含的标题\n";
        continue;
    }
    //统计正文中包含**的项目
//    if($document->subContHasStar) {
//        echo "_id为:".$id."  标题为:".$post_title." 的文档正文包含特殊字符\n";
//        ++$tongjiSum;
//        continue;
//    }

    //处理海报
    $hasHaibao = true;
    $dianpuPicArr = (isset($document->xmHaibaoImage) && !empty($document->xmHaibaoImage))? $document->xmHaibaoImage : [];
    if(!empty($dianpuPicArr)) {
        //检测海报是否正常
        $dir = "./check.jpg";
        $url = $dianpuPicArr[0];
        file_put_contents($dir , file_get_contents($url));
        $size = getimagesize($dir);
        unlink($dir);
        if($size){
            if($size[1] > 1000) {
                $dianpuPic = '';
                foreach($dianpuPicArr as $haibaoUrl) {
                    $dianpuPic .= '<img src="'.$haibaoUrl.'">';
                }
            }else{
                //海报尺寸有问题
                $hasHaibao = false;
                $dianpuPic = '<img src="">';
                echo "_id为:".$id."  标题为:".$post_title." 的文档海报图截取尺寸有问题\n";
            }
        }else{
            //不是有效的图片
            $hasHaibao = false;
            $dianpuPic = '<img src="">';
            echo "_id为:".$id."  标题为:".$post_title." 的文档海报图不是有效的图片\n";
        }
    }else{
        $hasHaibao = false;
        $dianpuPic = '<img src="">';
    }

    //处理项目头图
    $post_toutu = json_encode($toutuArr);

    //项目图片,首页和分类图
    $post_tupian = (isset($document->xmHeadImg->seoNetAddr))? $document->xmHeadImg->seoNetAddr : $toutuArr[0];

    //处理正文
    $post_content = '<p style="text-align: center;">'.$dianpuPic.'</p>';
    if(!empty($jieshao)){
        $post_content .= "<p>合作详情</p>".$jieshao;
    }
    if(!empty($tiaojian)) {
        $post_content .= "<p>合作条件</p>".$tiaojian;
    }
    if(!empty($youshi)) {
        $post_content .= "<p>合作优势</p>".$youshi;
    }
    if(!empty($liucheng)) {
        $post_content .= "<p>合作流程</p>".$liucheng;
    }

    //投资金额
    $post_touzi = (!empty($document->xmInve))? trim(str_replace(['￥','万'],'',$document->xmInve)) : 0;
    //公司名称
    $post_company = (!empty($document->xmCompany))? $document->xmCompany : '';
    //公司所在地区
    $post_address = (!empty($document->xmLocation))? $document->xmLocation : '';
    //店铺url
    $post_url = $document->xmId;
    //加盟区域
    $post_area = (!empty($document->xmArea))? $document->xmArea : '';

    //门店数量
    $post_mendian = (isset($document->xmHasUnion))? $document->xmHasUnion : 0;

    //加盟费用
    $post_join = '面议';

    //摘要
    $post_excerpt = '';

    $post_date = date('Y-m-d H:i:s');
    $paramsArr = [
        'post_author' => 3,
        'post_date'   => date('Y-m-d H:i:s'),
    ];
    //insert wp_posts表
    $insertResp = $daoMysqli->insertPosts($paramsArr);
    if($insertResp['status'] == true){
        $postId = $insertResp['data'];
        echo "_id为:".$id."  标题为:".$post_title." 的文档导入mysql  ok!\n";
    }elseif ($insertResp['status'] == false){
        echo "_id为:".$id."  标题为:".$post_title." 的文档导入mysql  false!\n";
        echo "sql语句:".$insertResp['data']."\n";
        continue;
    }
    //insert wp_postmeta
    if($hasHaibao){
        $metaValue = [
            'source' => JIAMENG_91JM_PROJECT,
            'type'   => HAS_91JM_HAIBAO
        ];
    }else{
        $metaValue = [
            'source' => JIAMENG_91JM_PROJECT,
            'type'   => NO_91JM_HAIBAO
        ];
    }
    $insertMetaResp = $daoMysqli->insertMetaPosts($postId, $metaValue);
    if ($insertMetaResp['status'] == false){
        echo "_id为:".$id."  标题为:".$post_title." 的文档导入mysql  false!\n";
        echo "sql语句:".$insertMetaResp['status']."\n";
    }
    ++$totalSum;

    //一级分类和二级分类处理,和我们的分类对应起来
    $formatCate = swapper($document->xmSubFrName);
    if($formatCate['code'] == 0) {
        echo "_id为:".$id."  标题为:".$post_title." 的文档二级分类为空,请手动更改!\n";
        continue;
    }
    if($formatCate['code'] == 400) {
        echo "_id为:".$id."  标题为:".$post_title." 的文档对应cdd系统的一级分类为空,请手动更改! 文档二级分类为：".$document->xmSubFrName."  对应cdd系统二级分类为：".$formatCate['data']."\n";
        continue;
    }
    if($formatCate['code'] == 500) {
        echo "_id为:".$id."  标题为:".$post_title." 的文档分类对应cdd系统的一级分类可能有多个, 请手动更改!\n";
        continue;
    }
    $categoryArr = $formatCate['data'];

    $insertCateResp = $daoMysqli->insertCategory($postId,$categoryArr);
    if ($insertCateResp['status'] == false){
        echo "_id为:".$id."  标题为:".$post_title." 的文档建立分类关系失败,请手动更改!\n";
    }
}

echo "导入数据完成...\n";
echo "总计导入".$totalSum."条数据,正文包含**的数据有：".$tongjiSum."条\n";
echo "导入此次数据用时：". (time()-$start_time) . "s \n";

/**
 * 抓取的分类和我们系统的分类对应
 * @param $category       二级分类
 */
function swapper($category){
    $resp = [
        'code' => 0,
        'data' => []
    ];
    global $cateMatchArr;
    global $daoMysqli;
    $erjifenlei = $cateMatchArr[$category];
    if(empty($erjifenlei)) {
        return $resp;
    }
    //根据二级分类获取一级分类
    $yijifenlei = $daoMysqli->getParentCateByChild($erjifenlei);
    switch ($yijifenlei) {
        case 400 :
            $resp['code'] = 400;
            $resp['data'] = $erjifenlei;
            break;
        case 500 :
            $resp['code'] = 500;
            break;
        default :
            $resp['code'] = 1;
            $resp['data'] = [
                'yijifenlei' => $yijifenlei,
                'erjifenlei' => $erjifenlei
            ];
    }
    return $resp;
}

function formatContent($content){
    $content =str_replace(['\'','<p></p>','<p><strong><span></span></strong></p>','<p><span></span></p>'],'',$content);
    $content =str_replace(['<div>','</div>','<span>','</span>','<br>'],'',$content);
    return $content;
}


