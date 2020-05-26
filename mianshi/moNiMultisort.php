<?php
/**
 * 模拟 php的多维数组排序, 写一个函数，可以实现不定个数多字段排序
 *
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/7/14
 * Time: 14:57
 */

class ZiSort {
    public static $option = [
        "desc" => "SORT_DESC",
        "asc"  => "SORT_ASC"
    ];

    /**
     * 二维数组按照多个列进行排序, 类似sql中的order by
     *
     * @param array $array   待排序数组
     * @param $keyname       排序字段 或者 多个排序字段组成数组形式
     * @param int $dir       排序方向
     */
    public static function sortByMultiCols(array $array, $keyname, $dir = "asc"){
        if(is_array($keyname)){
            return self::orderBy($array,$keyname);
        }
        return self::orderBy($array,[$keyname=>$dir]);
    }

    /**
     * 多数组多字段自定义排序方法
     * @param array $array
     * @param array $orders
     * @return bool
     */
    public static function orderBy(array $array, array $orders) {
        if(!is_array($array) || empty($array)){
            return false;
        }

        if(!is_array($orders) || empty($orders)){
            return false;
        }
        $sortRule = '';
        $sortArr = [];
        foreach ($orders as $sortField => $sortDir){
            foreach($array as $rows){
                $sortArr[$sortField][] = $rows[$sortField];
            }

            $sortRule .=  '$sortArr[\'' .$sortField.'\'],'.self::$option[$sortDir].",";
        }
        if(empty($sortRule)){
            return $array;
        }
        eval('array_multisort(' . $sortRule . '$array);');
        return $array;
    }
}


$inputArr = [
    ["score"=>2,"date" => "2015"],
    ["score"=>2,"date" => "2014"],
    ["score"=>1,"date" => "2017"],
    ["score"=>3,"date" => "2014"],
];
$orders = [
    "score" => "asc",
    "date"  => "desc"
];

//$res =  ZiSort::orderBy($inputArr, $orders);
//echo json_encode($res);

//$aa = ZiSort::sortByMultiCols($inputArr, "score",'desc');
$aa = ZiSort::sortByMultiCols($inputArr, $orders);
echo json_encode($aa);