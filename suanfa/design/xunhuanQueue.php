<?php
/**
 * 设计循环队列(环形缓冲器)
 * leetcode(622题)
 * https://leetcode-cn.com/problems/design-circular-queue/
 *
 *设计思路：
 *
 * 一个循环队列应该满足以下基本的操作：
 * MyCircularQueue(k): 构造器，设置队列长度为 k
 * Front: 从队首获取元素。如果队列为空，返回 -1
 * Rear: 获取队尾元素。如果队列为空，返回 -1
 * enQueue(value): 向循环队列插入一个元素。如果成功插入则返回真。
 * deQueue(): 从循环队列中删除一个元素。如果成功删除则返回真。
 * isEmpty(): 检查循环队列是否为空。
 * isFull(): 检查循环队列是否已满。
 *
 * 我们用数组定义一个循环队列，数据从左边弹出，右边插入，那么左边定义为队首，右边定义为队尾
 * 定义两个指针：
 * front：队列中头部第一个有效数据的索引位置
 * rear：队列尾部(最后一个有效数据)的下一个索引位置 (
    如果只给出队首font,那么根据公式(front+count-1) % cap也可以算出队尾指针位置rear
 *  count:指队列中元素的个数
 * )
 *
 * 为了避免队列满和队列空的判定条件冲突，我们至少要保留一个位置不存放数据，那么判定条件就是：
 * 队列为空：front == rear
 * 队列为满：(rear+1) % cap == front   这里的cap指数组的长度
 *
 * 队首指针front向后移一位：  ( front+1 ) % cap
 * 队尾指针rear向后移一位：  ( rear+1 ) % cap
 *
 * 求队尾的索引位置：
 * (front+count-1) % cap
 * (rear-1+cap) % cap
 */
class MyCircularQueue {
    public $font = 0;
    public $rear = 0;
    public $cap = 0;
    public $dataArr;

    public function __construct($k)
    {
        $this->cap = $k+1;
    }

    //获取队首元素，不弹出
    public function front(){
        if($this->isEmpty()) {
            return false;
        }
        $param = $this->dataArr[$this->font];
        return $param;
    }

    //获取队尾元素,不弹出
    public function rear(){
        if($this->isEmpty()) {
            return false;
        }
        $param = $this->dataArr[($this->rear -1 + $this->cap)%$this->cap];  //队尾的索引位置 (rear-1+cap)%cap
        return $param;
    }

    //向队列插入一个元素
    public function enQueue($value){
        if($this->isFull()) {
            return false;
        }

        $this->dataArr[$this->rear] = $value;
        $this->rear = ($this->rear +1) % $this->cap;
        return true;
    }

    //弹出队列的一个元素
    public function deQueue() {
        if($this->isEmpty()) {
            return false;
        }

        $param = $this->dataArr[$this->font];
        $this->font = ($this->font +1) % $this->cap;
        return $param;
    }

    //判断队列是否为空
    public function isEmpty(){
        return $this->font == $this->rear;
    }

    //判断队列是否已满
    public function isFull(){
        return $this->font == ( ($this->rear +1) % $this->cap );
    }
}

$obj = new MyCircularQueue(3);
$obj->enQueue(1);
$obj->enQueue(2);
$obj->enQueue(3);
//var_dump($obj->enQueue(4));
var_dump($obj->front());
var_dump($obj->rear());

var_dump($obj->isFull());
var_dump($obj->isEmpty());

var_dump($obj->deQueue());