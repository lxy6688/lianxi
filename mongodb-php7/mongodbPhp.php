<?php
/**
 * php7 mongodb 简单用法 - 增删改查
 *
 * 参考： 菜鸟教程 https://www.runoob.com/mongodb/php7-mongdb-tutorial.html
 * 参考： 博客 https://www.cnblogs.com/wujuntian/p/8352586.html  php操作mongodb
 *
 * 参考： https://blog.csdn.net/cjs5202001/article/details/81139477    php7的mongodb基本用法
 * 参考： http://blog.daozys.com/goods_104.html    mongodb的控制台管理
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/10/12
 * Time: 10:57
 */

/**
 * 插入数据
 */
function insert(){
    $bulk = new MongoDB\Driver\BulkWrite;  //默认有序的,串行执行
    //$bulk = new MongoDB\Driver\BulkWrite(['ordered' => flase]);//如果要改成无序操作则加flase，并行执行
    $document = ['_id' => new MongoDB\BSON\ObjectID, 'name' => '菜鸟教程2'];
    $_id= $bulk->insert($document);

    var_dump($_id);

    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    $writeConcern = new MongoDB\Driver\WriteConcern(MongoDB\Driver\WriteConcern::MAJORITY, 1000);
    $result = $manager->executeBulkWrite('test.runoob', $bulk, $writeConcern);
    print_r($result);
}
//insert();

/**
 * 查询数据
 */
function query(){
    $manager = new MongoDB\Driver\Manager("mongodb://localhost:27017");
    /**
    // 插入数据
    $bulk = new MongoDB\Driver\BulkWrite;
    $bulk->insert(['x' => 1, 'name'=>'菜鸟教程', 'url' => 'http://www.runoob.com']);
    $bulk->insert(['x' => 2, 'name'=>'Google', 'url' => 'http://www.google.com']);
    $bulk->insert(['x' => 3, 'name'=>'taobao', 'url' => 'http://www.taobao.com']);
    $manager->executeBulkWrite('test.sites', $bulk);
     */

    //$filter = ['x' => 2];     //这个只查询 x=2的文档
    //$filter = ['x' => ['$gt' => 1]];   //查询x>1 的文档
    $filter = [];             //数组为空，查询集合中所有的文档
    $options = [
        'projection' => ['_id' => 0],    //不输出 _id 字段
        'sort' => ['x' => -1],        //按照x字段倒叙显示， 如果 值为1 , 则是升序显示
        'limit' =>1,
        //'skip' => 0        //跳过指定数量的数据, 默认是0条
    ];

    // 查询数据
    $query = new MongoDB\Driver\Query($filter, $options);
    $cursor = $manager->executeQuery('test.sites', $query);

    foreach ($cursor as $document) {
        echo $document->name."\n";
    }
}
query();


/**
 * update
 */
function update(){
    //链接mongodb
    $manager = new MongoDB\Driver\Manager('mongodb://login:pwd@ip:27017');

    $bulk = new MongoDB\Driver\BulkWrite; //默认是有序的，串行执行
    //$bulk = new MongoDB\Driver\BulkWrite(['ordered' => flase]);//如果要改成无序操作则加flase，并行执行
    $bulk->update(
            ['user_id' => 2],
            ['$set'=>['real_name'=>'中国国']]
    );
    //$set相当于mysql的 set，这里和mysql有两个不同的地方，
    //1：字段不存在会添加一个字段;
    //2：mongodb默认如果条件不成立，新增加数据，相当于insert


    //如果条件不存在不新增加，可以通过设置upsert
    //db.collectionName.update(query, obj, upsert, multi);

    $bulk->update(
        ['user_id' => 5],
        [
            '$set'=>['fff'=>'中国国']
        ],
        ['multi' => true, 'upsert' => false]
    //multi为true,则满足条件的全部修改,默认为true，如果改为false，则只修改满足条件的第一条
    //upsert为 treu：表示不存在就新增
    );
    $manager->executeBulkWrite('location.box', $bulk); //执行写入 location数据库下的box集合
}

/**
 * ordered 设置
 *
 * 默认是ture，按照顺序执行插入更新数据，如果出错，停止执行后面的，mongo官方叫串行
 * 如果是false，mongo并发的方式插入更新数据，中间出现错误，不影响后续操作无影响，mongo官方叫并行
 *
 */



/**
 * delete
 */
function delete(){
    //链接mongodb
    $manager = new MongoDB\Driver\Manager('mongodb://login:pwd@ip:27017');

    $bulk = new MongoDB\Driver\BulkWrite; //默认是有序的，串行执行
    //$bulk = new MongoDB\Driver\BulkWrite(['ordered' => flase]);//如果要改成无序操作则加flase，并行执行
    $bulk->delete(['user_id'=>5]);//删除user_id为5的字段
    $manager->executeBulkWrite('location.box', $bulk); //执行写入 location数据库下的box集合

}
