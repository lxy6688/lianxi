<?php
/**
 * 二分查找类
 *
 * Class ErFenFind
 */
class ErFenFind {

    //迭代法实现二分查找, 标准法(假设数组没有排序)
    public function iteration($arr, $k){
        if( !is_array($arr) || empty($arr)) return false;
        $count = count($arr);
        if($count == 1) {
            return ($arr[0] == $k)? 0 : false;
        }

        $oldArr = $arr;
        sort($arr);
        $low = 0;
        $high = $count-1;
        $flag = false;
        while($low <= $high){
            $mid = intdiv(($low+$high),2);
            if($k == $arr[$mid]) {
                //return $mid;
                $flag = true;
                break;
            }elseif ($k < $arr[$mid]) {
                $high = $mid-1;
            }else{
                $low = $mid +1;
            }
        }

        if($flag) {
            return array_search($k,$oldArr);
        }
        return false;
    }

    //递归法实现二分查找(理想法，假设数组是按升序排好序的索引数组,索引从0开始)
    public function diGui($arr, $k) {
        if( !is_array($arr) || empty($arr)) return false;
        $count = count($arr);
        if($count == 1) {
            return ($arr[0] == $k)? 0 : false;
        }
        $low = 0;
        $high = $count-1;
        return $this->diGuiDetail($arr,$low,$high,$k);
    }

    public function diGuiDetail($arr,$low,$high,$k) {
        $mid = intdiv(($low+$high),2);
        if($k == $arr[$mid]) {
            return $mid;
        }elseif ($k < $arr[$mid]) {
            $high = $mid-1;
            return $this->diGuiDetail($arr,$low,$high,$k);
        }else{
            $low = $mid+1;
            return $this->diGuiDetail($arr,$low,$high,$k);
        }
        return false;
    }


    /**   二分查找的变种  假设都是理想状态的数组 */
    //查找第一个值等于给定值的元素

    //查找最后一个值等于给定值的元素

    //第一个大于等于给定值的元素

    //最后一个小于等于给定值的元素

}

$arr = [1,2,3,4,5,6,7,8,9,99,88,77];
$obj = new ErFenFind();
$site = $obj->iteration($arr, 99);

//$site = $obj->diGui($arr, 9);
var_dump($site);