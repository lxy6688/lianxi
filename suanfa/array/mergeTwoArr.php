<?php
/**
 * 数组的相关算法练习
 */

/**
 * 实现两个有序数组合并为一个有序数组 (其实就是归并排序的合并数组部分)
 * 时间复杂度 O(n)
 * 空间复杂度 O(n)
 *
 * @param $leftArr
 * @param $rightArr
 * @return array
 */
function mergeArr($leftArr, $rightArr) {
    $mergeArr = [];
    while(true){
        $leftV = current($leftArr);       //当前指针指向的数据
        $rightV = current($rightArr);
        if($leftV <= $rightV) {
            array_push($mergeArr, $leftV);
            $leftSpot = next($leftArr);        //指针指向下一个key，并返回值，当该位没有元素时，返回false，空数组返回false
            if($leftSpot == false) {  //左侧数组先放完毕
                $sKey = key($rightArr);
                $sArray = array_slice($rightArr,$sKey);
                break;
            }
        }else{
            array_push($mergeArr, $rightV);
            $rightSpot = next($rightArr);
            if($rightSpot == false) {  //右侧数组先放完毕
                $sKey = key($leftArr);
                $sArray = array_slice($leftArr,$sKey);
                break;
            }
        }
    }

    array_push($mergeArr, ...$sArray);   //把 $sArray 里面的值依次加入到$mergeArr中
    return $mergeArr;
}

/**
 * 合并两个有序数组，循环小数组，插入排序的思想
 *
 * 时间复杂度 O(n^2)
 * 空间复杂度是O(1)
 */
function mergeArrTwo($leftArr, $rightArr) {
    $leftLen = count($leftArr);

}

/**
 * 合并两个有序数组，循环小数组，二分查找变种的思想(查询小于等于给定值的最大值)
 *
 * 时间复杂度 O(nlogn)
 * 空间复杂度是O(1)
 */
function mergeArrThree($leftArr, $rightArr) {
    $leftLen = count($leftArr);

}


$leftArr = [1,3,5,7,9,20,50];
$rightArr = [2,4,6,12,15,31,100];
$res = mergeArr($leftArr, $rightArr);
echo json_encode($res);








//数组的赋值是一个值赋值，新开辟一块内存空间
$a = ['a'=>1,'b'=>2];
$b = &$a;
$b['b'] = 3;
print_r($a);






$oneArray = ['d', 'e', 'f'];
$anotherArray = ['a', 'b', 'c'];

array_push($anotherArray, ...$oneArray);
//['a', 'b', 'c', 'd', 'e', 'f'];




//  php数组指针函数
//  https://www.cnblogs.com/zxcv123/p/11721865.html



