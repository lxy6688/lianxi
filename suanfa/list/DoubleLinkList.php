<?php
/**
 * 双向链表的添加和删除
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/4/15
 * Time: 17:39
 */
//error_reporting(0);
class Node {
    public $key;     //节点的位置key
    public $val;     //节点的值
    public $pre;     //前继指针
    public $next;    //后继指针

    public function __construct($key = null, $val = null)
    {
        $this->key = $key;
        $this->val = $val;
        $this->pre = null;
        $this->next = null;
    }
}

class DoubleLinkList {
    private static $head;  //定义一个头结点

    public function __construct()
    {
        self::$head = new Node();
    }

    /**
     * 顺序添加结点
     * @param $node
     */
    public static function addNode($node){
        $current = self::$head;
        while($current->next != null){
            $current = $current->next;
        }
        $node->next = null;  //这里也可以写成 $node->next = $current->next,但是会报notice: 访问不存在的对象属性警告
        $node->pre = $current;
        $current->next = $node;
    }

    /**
     * 在指定位置后面添加节点
     * @param $key   指定位置
     * @param $node  新节点
     */
    public static function addNodeByKey($key, $node){
        $current = self::$head;
        while($current->next != null){
            if($current->next->key == $key){
                if($current->next->next == null){   //表示已经是最后一个节点
                    return self::addNode($node);
                }
                break;
            }
            $current = $current->next;
        }
        $cur = $current->next;  //$cur表示当前节点

        $node->next = $cur->next;
        $node->pre = $cur;
        $cur->next->pre = $node;
        $cur->next = $node;
    }

    /**
     * 删除指定位置的节点
     * @param $key
     */
    public static function delNode($key){
        $current = self::$head;
        $flag = false;
        while($current->next != null){
            if($current->next->key == $key){
                if($current->next->next == null){  //尾节点
                    $current->next->pre->next = null;
                    //$current->next->pre = null;
                    return ;
                }
                $flag = true;
                break;
            }
            $current = $current->next;
        }

        if($flag){
            $cur = $current->next;   //当前节点
            $cur->pre->next = $cur->next;
            $cur->next->pre = $cur->pre;
        }else{
            echo "要删除的节点位置不存在";
        }

    }

    /**
     * 正向遍历双向链表
     */
    public static function showLinkList(){
        $current = self::$head;
        if($current->next == null){
            echo "链表为空！";
            exit;
        }

        while($current->next != null){
            echo "当前结点值是：".$current->next->val."-他的前节点是：".$current->next->pre->val."-下一节点的值是：".$current->next->next->val.PHP_EOL;
            $current = $current->next;
        }
    }

}

$doubleList = new DoubleLinkList();
DoubleLinkList::addNode(new Node(1,'a'));
DoubleLinkList::addNode(new Node(2,'b'));
DoubleLinkList::addNode(new Node(3,'c'));
DoubleLinkList::addNode(new Node(4,'d'));

DoubleLinkList::addNodeByKey(2,new Node(5,'ff'));
DoubleLinkList::delNode(40);   //节点位置不存在

DoubleLinkList::showLinkList();