<?php
/**
 * 返回链表的中间节点及其变种
 *
 *https://leetcode-cn.com/problems/lian-biao-zhong-dao-shu-di-kge-jie-dian-lcof/solution/mian-shi-ti-22-lian-biao-zhong-dao-shu-di-kge-j-11/
 */


class Solution {

    /**
     * 方法一：  循环链表，构建散列表，取中间值的索引
     *
     * @param ListNode $head
     * @return ListNode
     */
    function middleNode($head) {
        $arr = [];
        while($head != null){
            array_push($arr,$head);
            $head = $head->next;   //指向下一节点
        }
        $midKey = count($arr)/2;
        return $arr[$midKey];
    }

    /**
     * 方法二： 快慢指针法
     *
     * @param $head
     * @return mixed
     */
    function middleNodeTwo($head) {
        $fast = $slow = $head;
        while($fast != null && $fast->next != null) {
            $slow = $slow->next;
            $fast = $fast->next->next;

        }
        return $slow;
    }


    //=======================变种题==============================
    /**
     * 链表的倒数第k个节点 (暴力法，遍历链表长度为n，倒数第k个节点，就是从头结点往后移动n-k步，指针所指向的节点)
     */
    function getKthFromEnd($head, $k) {
        $len = 0;
        $sumLen = $head;
        while($sumLen != null) {
            $len++;
            $sumLen = $sumLen->next;
        }
        $j = $len-$k;
        //判断边界条件，如果$k 大于 $len
        if($j < 0) {
            return false;
        }

        for($i = 0; $i < $j; $i++) {
            $head = $head->next;
        }

        return $head;
    }

    /**
     * 链表的倒数第k个节点(快慢指针法)
     */
    function getKthFromEndByFastAndSlow($head, $k) {
        $slow = $fast = $head;
        //快指针先移动k步
        for($i = 0; $i < $k; $i++){
            $fast = $fast->next;
        }

        while($fast != null) {
            $fast = $fast->next;
            $slow = $slow->next;
        }

        return $slow;
    }


    //判断链表是否有环  (构建哈希表、快慢双指针)

}

//按摩师问题(动态规划问题)
