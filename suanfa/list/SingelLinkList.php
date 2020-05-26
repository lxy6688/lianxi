<?php
/**
 * 实现单链表的基本功能
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/4/15
 * Time: 11:42
 */
class Node {
    public $key;  //节点的key
    public $val;  //节点val
    public $next; //节点的后继指针

    public function __construct($key = null, $val = null)
    {
        $this->key = $key;
        $this->val = $val;
        $this->next = null;
    }
}

//单链表类
class SingelLinkList {
    private $head;   //定义一个头节点

    public function __construct()
    {
        $this->head = new Node();    //生成一个头结点(空节点)
    }

    /**
     * 获取链表长度, 不包括头结点
     * @return int
     */
    public function getLinkLength(){
        $i=0;
        $current = $this->head;
        while($current->next != null){
            $i++;
            $current = $current->next;
        }
        return $i;
    }

    /**
     * 顺序添加节点
     * @param $node 节点数据
     */
    public function addNode($node){
        $current = $this->head;   //从头结点开始判断操作
        //已添加过节点
        $flag = false;
        while($current->next != null){
            if($current->next->key == $node->key){
                $flag = true;
                break;
            }

            $current = $current->next;   //假如不考虑key是否重复等判断,我只添加数据,只需要这一行代码即可
        }
        if($flag){
            return "节点key重复";
        }
        //添加节点操作
        $node->next = $current->next;
        $current->next = $node;
    }

    /**
     * 在指定结点位置后面添加结点
     * @param $key    指定的结点位置
     * @param $node   待添加的结点
     */
    public function addNodeByKey($key, $node) {
        $current = $this->head;
        while($current->next != null){
            if($current->next->key == $key){
                break;
            }
            $current = $current->next;
        }
        $node->next = $current->next->next;
        $current->next->next = $node;
    }

    /**
     * 删除节点
     * @param $key   节点的key
     * @return string
     */
    public function delNode($key) {
        $current = $this->head;
        $flag = false;
        while($current->next != null){
            if($current->next->key == $key){
                $val = $current->next->val;
                $flag = true;
                break;
            }else{
                $current = $current->next;
            }
        }
        if($flag){
            $current->next = $current->next->next;
            return '删除的节点值为:'.$val;
        }else{
            return '未找到key='.$key.'的节点'.PHP_EOL;
        }

    }

    /**
     * 判断链表是否为空
     * @return bool
     */
    public function isEmpty(){
        return $this->head == null;
    }

    /**
     * 清空链表
     */
    public function clear(){
        $this->head = null;
    }

    /**
     * 遍历链表值
     */
    public function getLinkList(){
        $current = $this->head;
        if($current->next == null){
            echo "链表为空";
            return;
        }
        while($current->next != null){
            echo '节点的值：',$current->next->val,PHP_EOL;
            $current = $current->next;
        }
    }

    /**
     * 获取节点的值
     * @param $key
     * @return mixed
     */
    public function getVal($key){
        $current = $this->head;
        while($current->next != null){
            if($current->next->key == $key){
                return $current->next->val;
            }else{
                $current = $current->next;
            }
        }
        return '节点：'.$key.'不存在'.PHP_EOL;
    }

    /**
     * 更新节点的值
     * @param $key
     * @param $val
     */
    public function updateVal($key,$val){
        $current = $this->head;
        while($current->next != null){
            if($current->next->key == $key){
                $current->next->val = $val;
                break;
            }else{
                $current = $current->next;
            }
        }
    }
}

$singLinkList = new SingelLinkList();
$singLinkList->addNode(new Node(1,'aa'));
$singLinkList->addNode(new Node(2,'bb'));
$singLinkList->addNode(new Node(3,'cc'));
$singLinkList->addNode(new Node(4,'dd'));
$singLinkList->addNode(new Node(5,'ee'));
echo '链表长度：',$singLinkList->getLinkLength(),PHP_EOL;
$singLinkList->getLinkList();

echo '删除节点：',$singLinkList->delNode(5),PHP_EOL;
echo '获取节点值：',$singLinkList->getVal(4).PHP_EOL;
$singLinkList->getLinkList();

echo $singLinkList->addNode(new Node(4,'ee'));   //节点key重复
$singLinkList->updateVal(3,'cc33');

$singLinkList->addNodeByKey(2,new Node(6,'ff'));  //在key=2的结点后面插入新的结点
$singLinkList->getLinkList();