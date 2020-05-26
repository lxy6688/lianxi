<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/16
 * Time: 17:36
 *
 * gitee:   https://gitee.com/kuotong/IP2Location-PHP-Module
 * github:  https://github.com/shakenetwork/ip2region
 */

 function getIPLoc_qq($queryIP) {
    $url = 'http://ip.qq.com/cgi-bin/searchip?searchip1='.$queryIP;
    $ch = curl_init($url);
    curl_setopt($ch,CURLOPT_ENCODING ,'gb2312');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
    $result = curl_exec($ch);
    $result = mb_convert_encoding($result, "utf-8", "gb2312"); // 编码转换，否则乱码
    curl_close($ch);
    //preg_match("@<span>(.*)</span></p>@iU",$result,$ipArray);
    //$loc = $ipArray[1];
    return $result;
}

//$ip = getIPLoc_qq('123.125.114.144');
//var_dump($ip);



function getIPLocation($queryIP){
$url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$queryIP;

//如果是新浪，这里的URL是：'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$queryIP; 
$ch = curl_init($url);
curl_setopt($ch,CURLOPT_ENCODING ,'gb2312');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回 
$result = curl_exec($ch);
$result = mb_convert_encoding($result, "utf-8", "gb2312"); // 编码转换，否则乱码 

curl_close($ch);
//preg_match("@<span>(.*)</span></p>@iU",$result,$ipArray); //匹配标签，抓取查询到的ip地址(以数组的形式返回)
//$location = $ipArray[0];
return $result;
}

//$ip = getIPLocation('111.186.116.208');//将ip传入进来
//print_r($ip);//打印结果



function getCity($ip)
{
    $url="http://ip.taobao.com/service/getIpInfo.php?ip=".$ip;
    $ipinfo=json_decode(file_get_contents($url));
    if($ipinfo->code=='1'){
    return false;
    }
    $city = $ipinfo->data->region.$ipinfo->data->city;
    return $city;
}
// example
print_r(getCity("111.186.116.208"));

