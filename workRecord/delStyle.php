<?php
/**
 * 过滤标签中的style属性
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/30
 * Time: 14:36
 */
$str = "<img src=
\"https://www.kmway.com/upge/2019/08/05/8324.jpg\" suffix=\"jpg\"><p style=\"white-space: normal; line-height: 1.6em; text-indent: 2em; text-align: justify;\"><span style=\"font-size: 
16px;\">'1、品牌运营与管理：拥有完善的连锁运营支持体为合作商提供专业的运营支持。</span></p>\n<p style=\"white-space: normal; line-height: 
1.6em; text-indent: 2em; text-align: justify;\"><span style=\"font-size: 16px;\">2、品牌营销：拥有高素质的营销及管理团队，为合作商特供强力有效
的营销方案。</span></p>\n<p style=\"white-space: normal; line-height: 1.6em; text-indent: 2em; text-align: justify;\"><span style=\"font-size: 
16px;\">3、连锁加盟体系：拥有完善的加盟政策与流程及专业的管理团队。</span></p>\n<p style=\"white-space: normal; line-height: 1.6em; text-
indent: 2em; text-align: justify;\"><span style=\"font-size: 16px;\">4、产品研发：拥有强劲的后续研发能力以及持续性的市场运作能力，使门店产品更
具市场竞争力。</span></p>\n<p style=\"white-space: normal; line-height: 1.6em; text-indent: 2em; text-align: justify;\"><span style=\"font-
size: 16px;\">5、'产品供'应链：拥有完善的产品供应链，实力保证门店的日常运作，科学管理为合作商提供原料及食材的一站式配送服务。</span></p>\n<p 
style=\"white-space: normal; line-height: 1.6em; text-indent: 2em; text-align: justify;\"><span style=\"font-size: 16px;\"><br></span></p>
\n<p><span style=\"font-size: 16px;\"></span></p>\n<p style=\"text-align:center;\"><img src=
\"https://www.kmway.com/upload/resources/image/2019/07/15/847824_600x600.jpg\" suffix=\"jpg\"></p>\n<p><br><img src=
\"https://www.kmway.com/upge/2019/07/15/84.jpg\" suffix=\"jpg\"></p>";

//$ss = "<p style=\"white-space: normal; line-height: 1.6em; text-indent: 2em; text-align: justify;\"><span style=\"font-size:
//16px;\">1、'品牌运营与管理：拥有完善的连锁运营支持体为合作商提供专业的运营支持。</span></p>\n<p style=\"white-space: normal; line-height:
//1.6em; text-indent: 2em; text-align: justify;\"><span style=\"font-size: 16px;\">2、品牌营销：拥有高素质的营销及管理团队，为合作商特供强力有效
//的营销方案。</span></p>";


//str=str.replace(/[ \t]*style[ \t]*=[ \t]*("[^"]+")|('[^']+')/ig,"");   //删除style属性
$str=preg_replace("/(\s)*style(\s)*=[\s]*(\"[^\"]+\")/","",$str);
//var_dump($str);exit;


//$count = preg_match_all("/\bsrc\b\s*=\s*[\'\\\"]?([^\'\\\"]*)[\'\\\"]?/i",$str,$array2);
//print_r($array2);

$regx = "/\bsrc\b\s*=\s*[\'\\\"]?([^\'\\\"]*)[\'\\\"]?/i";
echo preg_replace_callback($regx,function($matches){
    //print_r($matches);
    if(!empty($matches[1])){
        //$aa = $matches[1].'ssss';
        $aa = str_replace('https://','http://',$matches[1]);
//        return '<a href="'.$matches[0].'">'.$matches[0].'</a>';
        return 'src="'.$aa.'"';
    }
},$str);

//echo str_replace('\'','',$str);

//$regx = "/\bsrc\b\s*=\s*[\'\\\"]?([^\'\\\"]*)[\'\\\"]?/i";
//$count = preg_match_all($regx,$str,$array2);
//$data_arr = $array2[1];
////print_r($array2);
//
////echo $count;   //匹配出2个，所以结果是2
//foreach($data_arr as $turl){
//    //$regx = "/".$turl."/";
//    $regx = "/https://www.kmway.com/upge/2019/07/15/84.jpg/";
//     preg_replace_callback($regx,function($matches) use ($turl) {
//         print_r($matches);
////        if(!empty($matches[1])){
//////            $xiangmutupian = crab_image($matches[1]);
//////            $old_url = $xiangmutupian['save_path'];
//////
//////            $img_url = '';
//////            $img_url = upload_img(md5(time()).$xiangmutupian['ext'], $old_url);
//////            /* 上传oss后删除本地 */
//////            if(realpath($old_url)){
//////                @unlink($old_url);
//////            }
////            $img_url = $turl."1234567";
////
////            return 'src="'.$img_url.'" ';
////        }
//    },$str);
//}

//echo $str;












/**
 * js方法 过滤style、width、height等属性
 * str=str.replace(/[ \t]*style[ \t]*=[ \t]*("[^"]+")|('[^']+')/ig,"");
 * str=str.replace(/[ \t]*width[ \t]*=[ \t]*("[^"]+")|('[^']+')/ig,"");
 * str=str.replace(/[ \t]*height[ \t]*=[ \t]*("[^"]+")|('[^']+')/ig,"");
 * str=str.replace(/[ \t]*width[ \t]*=[ \t]*[^ \t]+/ig,"");
 * str=str.replace(/[ \t]*height[ \t]*=[ \t]*[^ \t]+/ig,"");
 */