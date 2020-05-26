<?php
/**
 * 两个有序链表合并为一个有序链表
 */

//方法一
// 还是利用合并排序的合并数组的那部分，双指针法
class Node {
    public $val = null;
    public $next = null;

    public function __construct($val = null)
    {
        $this->val = $val;
    }
}

class LinkedList {
    public $head = null;

    public function __construct($head = null)
    {
        $this->head = $head;
    }

    // 顺序添加节点，构建链表
    public function addNode($node){
        $current = $this->head;
        while($current->next != null) {
            $current = $current->next;
        }
        $node->next = null;
        $current->next = $node;
    }

    //返回构建完成的链表
    public function getLinkedList(){
        return $this->head;
    }
}

//构建链表1
$ob1 = new LinkedList( new Node(1) );
$ob1->addNode(new Node(3));
$ob1->addNode(new Node(5));
$oneList = $ob1->getLinkedList();
//var_dump($oneList);exit;

//构建链表2
$ob2 = new LinkedList( new Node(2) );
$ob2->addNode(new Node(4));
$ob2->addNode(new Node(6));
$twoList = $ob2->getLinkedList();

//两个有序链表合并为一个有序链表(迭代法、双指针)
function mergeList($oneList, $twoList){

    $newList = new LinkedList( new Node() );   //创建一个头结点(空节点)

    while(true) {
        $leftVal = $oneList->val;
        $rightVal = $twoList->val;
        if($leftVal <= $rightVal) {
            $newList->addNode(new Node($leftVal));
            $oneList = $oneList->next;       //指到下一节点
            if($oneList == null) {
                $current = $newList->head;        //67-70行，是找出新链表的最后一个节点
                while($current->next != null){
                    $current = $current->next;
                }
                $current->next = $twoList;   //把 $twoList 剩余的节点追加到 新链表newList的最后一个节点的后面
                break;
            }
        }else{
            $newList->addNode(new Node($rightVal));
            $twoList = $twoList->next;
            if($twoList == null) {
                $current = $newList->head;
                while($current->next != null){
                    $current = $current->next;
                }
                $current->next = $oneList;
                break;
            }
        }
    }

    return $newList;
}

$newList = mergeList($oneList, $twoList);
//var_dump($newList->head);exit;

$current = $newList->head;
while($current->next != null) {
    echo $current->next->val,"\n";
    $current = $current->next;
}


//=======================leetcode上的解法========================================
//https://leetcode-cn.com/problems/merge-two-sorted-lists/solution/php-jie-fa-by-zzpwestlife-19/

/**
 * 递归法实现两个链表的合并
 */
function mergeTwoLists($l1, $l2) {
    if($l1 ==null) {
        return $l2;
    }
    if($l2 == null) {
        return $l1;
    }
    if($l1->val < $l2->val) {
        $l1->next = mergeTwoLists($l1->next, $l2);
        return $l1;
    }else{
        $l2->next = mergeTwoLists($l1,$l2->next);
        return $l2;
    }
}


/**
 * 迭代法实现两个链表的合并( leetcode)
 */
function mergeTwoListsTwo($l1, $l2) {
    if($l1 ==null) {
        return $l2;
    }
    if($l2 == null) {
        return $l1;
    }

    $node = new ListNode(-1);
    $pre = $node;
    while($l1 != null && $l2 != null) {
        if($l1->val < $l2->val) {
            $pre->next = $l1;
            $l1 = $l1->next;
        }else{
            $pre->next = $l2;
            $l2 = $l2->next;
        }
        $pre = $pre->next;
    }

    $pre->next = ($l1 == null)? $l2 : $l1;
    return $node->next;
}

