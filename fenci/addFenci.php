<?php
/**
 * jieba-php的分词算法, 功能2: 加载自定义的词典,作为默认词典的补充,提高纠错辨认能力
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
use Fukuball\Jieba\Jieba;
use Fukuball\Jieba\Finalseg;
Jieba::init();
Finalseg::init();


Jieba::loadUserDict("/home/fenci/dict.txt"); # file_name 為自定義詞典的絕對路徑

/**
 * 下面再进行分词, 比如： 贡禧茶饮
 */

