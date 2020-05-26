<?php
/**
 * php输出xml格式数据
 * get publish post id
 * 拼装成xml格式的 sitemap文件，按月总结, 输出到指定文件
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/12
 * Time: 15:12
 */
header("Content-Type: text/xml;charset=utf-8");
//ini_set("memory_limit", "1024M");
//set_time_limit(0);



define("HOME_DIR","/root/");
define("PROFIX","sitemap-");

require_once "./DaoMysqli.php";

$params = [
    "host" => "127.0.0.1",
    "user" => "root",
    "password" => '12345678',
    "dbName" => "wordpress"
];
$toutuIdArr = $contentIdArr = $allIdArr = [];
$daoMysqli = DaoMysqli::getInstance($params);
$allRes = $daoMysqli->getPublishId();
//print_r($allRes);
$i=0;
$dateArr = [];
if(empty($allRes)) {
    echo '数据为空!';
    exit;
}

    foreach ($allRes as $value) {
        $date = date("Ym",strtotime($value->post_date));
        if(isset($dateArr[$date])) {
            array_push($dateArr[$date],$value);
        }else{
            $dateArr[$date][] = $value;
        }
    }

$tempXmlArr = [];
foreach($dateArr as $kDate => $vDataArr){
    $xml = '<?xml version="1.0" encoding="utf-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    foreach($vDataArr as $vData){
        $xmldate = date("Y-m-d",strtotime($vData->post_date));
        $xml .= '
            <url>
                <loc>https://007dir.cn/projects/'.$vData->ID.'.html</loc>
                <lastmod>'.$xmldate.'</lastmod>
                <changefreq>daily</changefreq>
                <priority>0.9</priority>
             </url>
        ';
    }
    $tempXmlArr[$kDate] = $xml;
    $xml = '';
}

$yearArr = [
    2019 => 'a',
    2020 => 'b',
    2021 => 'c',
    2022 => 'd',
    2023 => 'e',
    2024 => 'f',
    2025 => 'g'
];
$monthArr = [
    '01' => 'January',
    '02' => 'February',
    '03' => 'March',
    '04' => 'April',
    '05' => 'May',
    '06' => 'June',
    '07' => 'July',
    '08' => 'August',
    '09' => 'September',
    '10' => 'October',
    '11' => 'November',
    '12' => 'December'
];
foreach($tempXmlArr as $kDate => $vxml){
    $vxml .= '</urlset>';
    $yearKey = intval(substr($kDate,0,4));
    $year = $yearArr[$yearKey];

    $monthKey = strval(substr($kDate,4));
    $month = $monthArr[$monthKey];

    $file = HOME_DIR.PROFIX.$year.'-'.$month.".xml";
    if(file_exists($file)) {
        unlink($file);

    }
    file_put_contents($file,$vxml);
}





