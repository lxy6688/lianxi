<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/4/10
 * Time: 11:51
 */

swoole_timer_tick(5000, function($timeId){
    echo "after 500ms \n";
    swoole_timer_after(10000, function(){
        echo "after 10000ms \n";
    });
//    sleep(5);
//    echo "after 5000ms \n";
});