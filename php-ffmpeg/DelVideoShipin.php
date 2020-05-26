<?php
/**
 * 脚本去除视频的水印(示例左上或右上的水印)
 *
 * linux ffmpeg命令行去除水印:
 * # ffmpeg -y -i test.mp4 -vf "delogo=x=564:y=15:w=270:h=25:show=0" -c:a copy delogo.mp4
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/24
 * Time: 16:43
 */
require __DIR__.'/vendor/autoload.php';
$ffmpeg = FFMpeg\FFMpeg::create([
    'ffmpeg.binaries' => '/usr/local/ffmpeg/bin/ffmpeg',//安装的ffmpeg服务绝对地址
    'ffprobe.binaries' => '/usr/local/ffmpeg/bin/ffprobe',//安装的ffprobe服务绝对地址
    'timeout' => 3600, // The timeout for the underlying process
    'ffmpeg.threads' => 12,   // The number of threads that FFMpeg should use
]);
$video = $ffmpeg->open('./test.mp4');
$video->getStreams()->audios()->first()->set('profile', 'high');

//去除右上位置的水印
function delRightShuiyin($video){
    //视频信息
    $width = $video->getStreams()->videos()->first()->get('width');   //视频的尺寸宽度
    //右上角水印
    $xRight = $width * (2/3);
    $yRight = 15;
    $wRight = $width - $xRight - 10;
    $hRight = 120;

    $command = "ffmpeg -y -i test.mp4 -vf 'delogo=x=".$xRight.":y=".$yRight.":w=".$wRight.":h=".$hRight.":show=0' -c:a copy -q 0 r_delogo.mp4 &";
    echo "Starting ffmpeg...\n\n";
    echo shell_exec($command);      //  shell_exec()需要在php.ini当中删除掉此函数
    echo "Done.\n";
}
//delRightShuiyin($video);

//去除左上位置的水印
function delLeftShuiyin($video){
    //视频信息
    $width = $video->getStreams()->videos()->first()->get('width');   //视频的尺寸宽度
    //上角水印
    $xRight = 10;
    $yRight = 15;
    $wRight = $width * (1/3);
    //$wRight = ($width) * (9/10);
    $hRight = 120;

    $command = "ffmpeg -y -i test.mp4 -vf 'delogo=x=".$xRight.":y=".$yRight.":w=".$wRight.":h=".$hRight.":show=0' -c:a copy -q 0 l_delogo.mp4 &";
    echo "Starting ffmpeg...\n\n";
    echo shell_exec($command);      //  shell_exec()需要在php.ini当中删除掉此函数
    echo "Done.\n";
}
delLeftShuiyin($video);
