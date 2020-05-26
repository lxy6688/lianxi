<?php
/**
 * 查找表中guid的字段的值更新到新的字段post_tupian中
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/01
 * Time: 16:30
 */
header("Content-Type: text/html;charset=utf-8");
ini_set("memory_limit", "1024M");
set_time_limit(0);

require_once "./DaoMysqli.php";
$params = [
    "host" => "localhost",
    "user" => "root",
    "password" => '123456',
    "dbName" => "wordpress"
];
$toutuIdArr = $contentIdArr = $allIdArr = [];
$daoMysqli = DaoMysqli::getInstance($params);
$allRes = $daoMysqli->getGuid();
//print_r($allRes);
$i=0;
if(!empty($allRes) && is_array($allRes)) {
    foreach ($allRes as $value) {
        $i++;
        if($value->post_parent == 0){
            echo "当前ID".$value->ID."是父id,不需要更新\n";
            continue;
        }else{
            $parentRes = $daoMysqli->getParentGuid($value->post_parent);
            $pguid = $parentRes['guid'];
            $updateRes = $daoMysqli->updateGuidToTupian($value->ID,$pguid);
            if($updateRes !== true) {
                echo "当前ID".$value->ID."update false\n";
                echo "error sql是".$updateRes."\n";
            }
        }
    }
    echo "更新了".$i."条数据";
}



