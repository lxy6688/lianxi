<?php
/**
 * 链表实现一个链式队列
 *
 * 设置头结点: head
 * 设置尾指针：tail
 * 右进左出
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/4/2
 * Time: 11:13
 */
class Node {
    public $val = null;
    public $next = null;


    public function __construct($val = null)
    {
        $this->val = $val;
    }
}


class ListQueue {
    public $head = null;   //头结点, 默认一个空节点
    public $tail = null;   //尾指针，指向最后一个节点


    public function __construct()
    {
        $this->head = $this->tail = new Node(); //创建一个空节点作为头结点，head和tail初始化都指向这个头结点
    }


    //入队列
    public function push($node) {
        $node->next = null;
        $this->tail->next = $node;


        $this->tail = $this->tail->next; //将tail指针后移一位，指向最后一个节点
    }


    //左边出队列
    public function shift(){
        $node = $this->head->next;
        $this->head = $this->head->next;
        return $node;
    }


    public function pop(){
        return $this->tail;  //返回尾指针指向的最后一个节点
    }


}


$obj = new ListQueue();
$obj->push(new Node(1));
$obj->push(new Node(2));
$obj->push(new Node(3));


//出队列
$node = $obj->shift();
var_dump($node->val);


//出栈
$node = $obj->pop();
var_dump($node->val);