<?php
/**
 * 数组中的第k个大元素
 *
 * 在未排序的数组中找到第 k 个最大的元素。请注意，你需要找的是数组排序后的第 k 个最大的元素，而不是第 k 个不同的元素。
 * 输入: [3,2,1,5,6,4] 和 k = 2
 * 输出: 5
 */

//第一种方法，暴力法 (数组排序，取n-k的索引值即可)
function findKthLargest($nums, $k) {
    if(empty($nums)) {
        return false;
    }
    $count = count($nums);
    $len = $count-$k;
    sort($nums);
    return $nums[$len];
}


//第二种方法， 构建一个k大小的小顶堆，遍历数组，维护小顶堆，最后取堆顶元素就是第k大元素
function findKthLargestTwo($nums, $k) {
    if(empty($nums)) {
        return false;
    }
    $count = count($nums);
    //构建k个元素的小顶堆
    $heap =new SplMinHeap();
    for($i =0; $i < $k; $i++) {
        $heap->insert($nums[$i]);
    }

    //遍历原数组,比较堆顶元素,维护这个小顶堆
    for($j = $k; $j < $count; $j++) {
        $top = $heap->top();
        if($nums[$j] > $top) {
            $heap->extract();   //移除堆顶元素
            $heap->insert($nums[$j]);   //插入新元素,维护最小堆
        }
    }
    return $heap->top();
}

$heap = new SplMinHeap();   //构建一个小顶堆
$heap->insert(4);
$heap->insert(8);
$heap->insert(7);
$heap->insert(5);
$heap->insert(1);

//$heap->extract();
//echo $heap->top();


//构建一个大顶堆
$maxheap = new SplMaxHeap();
$maxheap->insert(4);
$maxheap->insert(8);
$maxheap->insert(7);
$maxheap->insert(5);
$maxheap->insert(1);

$maxheap->extract();
//echo $maxheap->top();


//继承SplHeap类，自定义实现最大/小堆
class JupilerLeague extends SplHeap
{
    /**
     * We modify the abstract method compare so we can sort our
     * rankings using the values of a given array
     */
    public function compare($array1, $array2)
    {
        $values1 = array_values($array1);
        $values2 = array_values($array2);
        if ($values1[0] === $values2[0]) return 0;
        return $values1[0] < $values2[0] ? -1 : 1;
    }
}

// Let's populate our heap here (data of 2009)
$heap = new JupilerLeague();
$heap->insert(array ('AA Gent' => 15));
$heap->insert(array ('Anderlecht' => 20));
$heap->insert(array ('Cercle Brugge' => 11));
$heap->insert(array ('Charleroi' => 12));
$heap->insert(array ('Club Brugge' => 21));
$heap->insert(array ('G. Beerschot' => 15));
$heap->insert(array ('Kortrijk' => 10));
$heap->insert(array ('KV Mechelen' => 18));
$heap->insert(array ('Lokeren' => 10));
$heap->insert(array ('Moeskroen' => 7));
$heap->insert(array ('Racing Genk' => 11));
$heap->insert(array ('Roeselare' => 6));
$heap->insert(array ('Standard' => 20));
$heap->insert(array ('STVV' => 17));
$heap->insert(array ('Westerlo' => 10));
$heap->insert(array ('Zulte Waregem' => 15));

// For displaying the ranking we move up to the first node
var_dump($heap->top());


/*操作堆的方法

abstract SplHeap implements Iterator , Countable {
    // 创建一个空堆
public __construct ( void )
    // 比较两个节点的大小
abstract protected int compare ( mixed $value1 , mixed $value2 )
    // 返回堆节点数
public int count ( void )
    // 返回迭代指针指向的节点
public mixed current ( void )
    // 从堆顶部提取一个节点并重建堆
public mixed extract ( void )
    // 向堆中添加一个节点并重建堆
public void insert ( mixed $value )
    // 判断是否为空堆
public bool isEmpty ( void )
    // 返回迭代指针指向的节点的键
public mixed key ( void )
    // 迭代指针指向下一节点
public void next ( void )
    // 恢复堆
public void recoverFromCorruption ( void )
    // 重置迭代指针
public void rewind ( void )
    // 返回堆的顶部节点
public mixed top ( void )
    // 判断迭代指针指向的节点是否存在
public bool valid ( void )
}
*/

/**
 * 参考：
 * https://blog.csdn.net/wuxing26jiayou/article/details/51899811
 *
 */


//===============优先级队列====================







