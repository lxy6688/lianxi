<?php
/**
 * 安装php-ffmpeg (预先安装好ffmpeg)
 * # composer require php-ffmpeg/php-ffmpeg
 *
 * 参考博客：php-ffmpeg操作
 * https://blog.csdn.net/heart24kking/article/details/101426972
 * https://blog.csdn.net/a9925/article/details/80334700
 * https://www.jianshu.com/p/c72cac56513c
 * https://php.ctolib.com/PHP-FFmpeg.html#articleHeader9
 *
 * github:  https://github.com/PHP-FFMpeg/PHP-FFMpeg
 * ffmpeg官网： http://ffmpeg.org/download.html#repositories
 *
 * linux 下ffmpeg命令行操作去除水印: https://blog.csdn.net/sinat_14826983/article/details/82670058
 *
 * https://blog.csdn.net/kingvon_liwei/article/details/79271361  ffmpeg控制profile
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/24
 * Time: 14:30
 */
require __DIR__.'/vendor/autoload.php';
$ffmpeg = FFMpeg\FFMpeg::create([
    'ffmpeg.binaries' => '/usr/local/ffmpeg/bin/ffmpeg',//安装的ffmpeg服务绝对地址
    'ffprobe.binaries' => '/usr/local/ffmpeg/bin/ffprobe',//安装的ffprobe服务绝对地址
    //'timeout' => 3600, // The timeout for the underlying process
    //'ffmpeg.threads' => 12,   // The number of threads that FFMpeg should use
]);
$video = $ffmpeg->open('./test.mp4');

//视频信息
$video_info = $video->getStreams()->videos()->first()->all();
$video->getStreams()->audios()->first()->set('profile', 'high');  //设置视频的画质
var_dump($video_info);
//echo $video_info['width']."\n";
//echo $video_info['height']."\n";

//单独获取某一个
$profile = $video->getStreams()->videos()->first()->get('width');   //视频的尺寸宽度
//echo $profile."\n";
//echo $profile * (2/3);

//获取视频尺寸有单独的方法
//$dimensions=$video->getStreams()->videos()->first()->getDimensions();

//视频的音频信息
$audio_info_rate = $video->getStreams()->audios()->first()->all();
//单独获取某一个
$sample_rate = $video->getStreams()->audios()->first()->get('sample_rate');




//$video->filters()
//    ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
//    ->synchronize();
//$video->save(new FFMpeg\Format\Video\X264(), 'export-x264.mp4');



