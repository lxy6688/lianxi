<?php
/**
 * jieba-php的分词算法, 功能3: 提取文章关键词
 * composer安装： composer require fukuball/jieba-php:dev-master 或者直接git clone
 * git clone https://github.com/fukuball/jieba-php.git
 *
 * github链接: https://github.com/fukuball/jieba-php
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/10
 * Time: 12:07
 */
ini_set('memory_limit', '1024M');
//require_once "/home/composer/vendor/autoload.php";

require_once "/home/fenci/vendor/fukuball/jieba-php/src/vendor/multi-array/MultiArray.php";
require_once "/home/fenci/vendor/fukuball/jieba-php/src/vendor/multi-array/Factory/MultiArrayFactory.php";
require_once "/home/fenci/vendor/fukuball/jieba-php/src/class/Jieba.php";
require_once "/home/fenci/vendor/fukuball/jieba-php/src/class/Finalseg.php";
require_once "/home/fenci/vendor/fukuball/jieba-php/src/class/JiebaAnalyse.php";

use Fukuball\Jieba\Jieba;
use Fukuball\Jieba\Finalseg;
use Fukuball\Jieba\JiebaAnalyse;
Jieba::init(array('mode'=>'test','dict'=>'small'));
Finalseg::init();
JiebaAnalyse::init();

$top_k = 10;    //返回的关键词数量,默认是20
$content = file_get_contents("/home/fenci/content.txt", "r");
$tags = JiebaAnalyse::extractTags($content, $top_k);
var_dump($tags);


//还可以增加自定义停止词
JiebaAnalyse::setStopWords('/home/fenci/stop_words.txt');
$tags = JiebaAnalyse::extractTags($content, $top_k);
var_dump($tags);

