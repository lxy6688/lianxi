<?php
/**
 * 抽象的父类,模拟实现类的多态
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/4/22
 * Time: 16:38
 */
abstract class Driver {
    abstract public function set();

    abstract public function get();
}