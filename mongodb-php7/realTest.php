<?php
/**
 * php7 mongodb 获取远程的mongodb数据
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/12
 * Time: 10:57
 */

/**
 * 查询list列表数据
 *
 */
function query(){
    //加上指定的数据库名称test ,否则这里会报认证失败的错误
    $manager = new MongoDB\Driver\Manager('mongodb://login:pwd@ip:port/test');

    $filter = [];             //或者叫做where 数组为空，查询集合中所有的文档
    $options = [
        'projection' => ['xmMatches' => 0],    //不查询 _id
        //'sort' => ['x' => -1],        //按照x字段倒叙显示， 如果 值为1 , 则是正序显示
        'limit' =>1,
//        'skip' => 0        //跳过指定数量的数据, 默认是0条
    ];

    // 查询数据
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery('test.seo', $query);

    foreach ($cursor as $document) {
        print_r($document);
        echo "\n";
//        $jianjie = $document->xmJianjieHtm;
//        $fenxi = $document->xmFenxiHtm;
//        $tiaojian = $document->xmTiaojianHtm;
//
//        $content = "<p><img></p><p>合作详情</p>";
//        $content .= $jianjie."<p>合作优势</p>";
//        $content .= $fenxi."<p>合作条件</p>";
//        $content .= $tiaojian;
//
//        echo $content;
        exit;
    }
}
//query();

//带有where条件的查询
function query_where(){
    //加上指定的数据库名称test_crawler ,否则这里会报认证失败的错误
    $manager = new MongoDB\Driver\Manager('mongodb://login:pwd@ip:port/test');

    //或者叫做where 数组为空，查询集合中所有的文档
    $filter = [
        'xmId' => 'sanmiz'    //xmId等于 'sanmiz' 的文档记录
    ];
    $options = [
        'projection' => ['xmMatches' => 0],    //不查询 _id
        //'sort' => ['x' => -1],        //按照x字段倒叙显示， 如果 值为1 , 则是正序显示
        'limit' =>1,
        //'skip' => 0        //跳过指定数量的数据, 默认是0条
    ];

    // 查询数据
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery('test.seo', $query);

    foreach ($cursor as $document) {
        print_r($document);
        echo "\n";
        exit;
    }
}
//query_where();


//限制选择查询的字段
function query_field(){
    //加上指定的数据库名称test_crawler ,否则这里会报认证失败的错误
    $manager = new MongoDB\Driver\Manager();

    //或者叫做where 数组为空，查询集合中所有的文档
    $filter = [
        //'xmId' => 'sanmiz'    //xmId等于 'sanmiz' 的文档记录
    ];
    $options = [
        'projection' => [
            //'xmMatches' => 0,    //不查询 _id
            'cityName'  => 1,
            '_id' => 1
        ],
        //'sort' => ['x' => -1],        //按照x字段倒叙显示， 如果 值为1 , 则是正序显示
        //'limit' =>10,
        //'skip' => 0        //跳过指定数量的数据, 默认是0条
    ];

    // 查询数据
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery('test.seo', $query);

    foreach ($cursor as $document) {
        print_r($document);
        echo "\n";
        exit;
    }
}
query_field();


