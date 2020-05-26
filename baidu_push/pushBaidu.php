<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/9
 * Time: 15:12
 */
function pushbypc(){
    $current_page_url = "https://www.qihanyikao.com/explore/76.html";

    $site_domain = "https://www.qihanyikao.com";

    $token    	 = '';
    $api = 'http://data.zz.baidu.com/urls?site='.$site_domain.'&token='.$token;

    $ch = curl_init();

    $options =  array(
        CURLOPT_URL => $api,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 3,
        CURLOPT_POSTFIELDS => $current_page_url,
        CURLOPT_HTTPHEADER => array('Host: data.zz.baidu.com','Content-Type: text/plain')
    );

    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    $result_obj = json_decode($result);
    var_dump($result_obj);
}
//pushbypc();

function pushbymobile(){
    $domain_m = 'https://m.qihanyikao.com';
    $api = 'http://data.zz.baidu.com/urls?appid=&token=&type=realtime';

    $ch_m = curl_init();

    $current_page_url_m = "https://m.qihanyikao.com/explore/76.html";
    $options_m =  array(
        CURLOPT_URL => $api,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 3,
        CURLOPT_POSTFIELDS => $current_page_url_m,
        CURLOPT_HTTPHEADER => array('Host: data.zz.baidu.com','Content-Type: text/plain')
    );

    curl_setopt_array($ch_m, $options_m);
    $result_m = curl_exec($ch_m);
    $result_obj_m = json_decode($result_m);
    var_dump($result_obj_m);
}
pushbymobile();


