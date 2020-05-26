<?php
/**php调用python
 *
 */
header("Content-Type: text/html;charset=utf-8");
ini_set("memory_limit", "1024M");
set_time_limit(0);
date_default_timezone_set( 'Asia/Shanghai' );

$file = __DIR__ . '/two_youtube_urls.txt';
$resp = [];
$handler = fopen($file,'r'); //打开文件
while(!feof($handler)){
    $lineData = fgets($handler,4096);
    $resp[] = trim($lineData);
}
fclose($handler); //关闭文件
$loadUrlsArr = array_filter($resp);

foreach($loadUrlsArr as $url) {
    $stime = time();
    echo "当前时间：     ".date("Y-m-d H:i:s",time())."  开始下载 video url：   ".$url."\n";
    //先获取视频的all streams
    $allStreams = allStreamsRequest($url);
    if($allStreams == 'error'){
        continue;
    }
//    if($res[0] == 'None'){
//        //视频url无效
//    }
    $allString = $allStreams[0];
    //load 720p mp4 includes vcodec and acodec
    if(false !== strpos($allString,'itag="18"')){
        $res = succExec($url, 18);
        if($res == "error"){
            continue;
        }
        var_dump($res);exit;


        echo $url."     video load success!\n";
        sleep(20);
        continue;
    }

    //load 480p mp4 includes vcodec and acodec






    echo "下载用时: ". (time()-$stime) . "s\n";
    exit;
}


function succExec($url, $item){
    $command = "python3 ".__DIR__."/PhpToPython.py ".$url." ".$item;
    exec($command, $out, $res);

    if($res !== 0){   //python执行出错
        file_put_contents(__DIR__."/errorYoutubeList.txt", $url.PHP_EOL, FILE_APPEND);
        return 'error';
    }
    return $out;
}

function allStreamsRequest($url) {
    $command = "python3 ".__DIR__."/TotalPhpPython.py ".$url;
    exec($command, $out, $res);
    if($res !== 0){   //python执行出错
        file_put_contents(__DIR__."/errorYoutubeList.txt", $url.PHP_EOL, FILE_APPEND);
        return 'error';
    }
    return $out;
}