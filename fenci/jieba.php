<?php
/**
 * jieba-php的分词算法, 功能1: 分词
 * composer安装： composer require fukuball/jieba-php:dev-master 或者直接git clone
 * git clone https://github.com/fukuball/jieba-php.git
 *
 * github链接: https://github.com/fukuball/jieba-php
 * python版本的：  https://github.com/fxsjy/jieba
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

//Jieba::loadUserDict("/home/fenci/vendor/fukuball/jieba-php/src/dict/user_dict.txt"); 加载自定义词典,作为默认词典的补充,提高纠错能力

$seg_list = Jieba::cut("怜香惜玉也得要看对象啊！");
var_dump($seg_list);

$seg_list = Jieba::cut("我来到北京清华大学", true);
var_dump($seg_list); #全模式

$seg_list = Jieba::cut("我来到北京清华大学", false);
var_dump($seg_list); #默認精確模式

$seg_list = Jieba::cut("他来到了网易杭研大厦");
var_dump($seg_list);

$seg_list = Jieba::cutForSearch("小明硕士毕业于中国科学院计算所，后在日本京都大学深造"); #搜索引擎模式
var_dump($seg_list);