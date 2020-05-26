<?php
require_once "Driver.php";
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/4/22
 * Time: 16:38
 */
class Rd extends Driver {
    /**
     * 实现父类抽象类中的set方法
     */
    public function set(){
        echo "redis中的set方法";
    }

    /**
     * 实现父类抽象类中的get方法
     */
    public function get()
    {
        // TODO: Implement get() method.
    }
}