<?php
require_once "Rd.php";
require_once "Mc.php";
/**
 * 引入文件,多个子类继承一个父类时,如果用require, 会报错：类不能重复声明,解决办法：
 * require  改成 require_once 或者 加if判断，如：
 * if(!class_exists('class'){
 *      require class.php
 * }
 */

/**
 * 看thinkphp5 的源码cache部分时,发现缓存的调用流程使用了面向对象的多态特性(当然封装、继承特性都有,
 * 只不过多态特性相对要稍复杂一些), 故简写了一下tp框架中缓存部分的多态使用。
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/4/22
 * Time: 16:30
 */

class Cache {
    /**
     * 操作句柄
     * @var null
     */
    public static $handler = null;

    /**
     * 模拟读取默认的缓存类
     * @var
     */
    public static $class = "Rd";  //tp框架中，默认的缓存类是从配置文件读取的

    /**
     * 初始化Cache
     * @return mixed
     */
    public static function init(){
        return self::$handler = new self::$class();
    }

    public function set(){
        self::$handler->set();
    }

    public static function store($class = ''){
        if(!empty($class)) {
            self::$class = ucwords($class);
        }
        return self::init();
    }
}

Cache::init()->set();
//Cache::store('mc')->set();