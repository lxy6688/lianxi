<?php

/**
 * 单链表反转
 * 每次循环，都将当前节点指向他前面的节点，然后 当前节点和前节点后移
 *
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/4/20
 * Time: 11:01
 */
class Node
{
    public $data = null;
    public $next = null;

    public function __construct($data = null)
    {
        $this->data = $data;
    }
}

class ReverseList
{
    public $head;

    public function __construct()
    {
        $this->head = new Node(1);  //设置第一个节点,注意不是头节点; 头节点一般为空节点
    }

    /**
     * 顺序添加节点
     * @param $node
     */
    public function addNode($node)
    {
        $current = $this->head;
        while ($current->next != null) {
            $current = $current->next;
        }
        $node->next = null;
        $current->next = $node;
    }

    /**
     * 反转单链表(非递归方式， 迭代)
     * @return Node|null
     */
    public function reverseList(){
        $current = $this->head;
        $new = null;
        //从左往右循环反转链表
        while($current != null){    //当前头节点，而不是$current->next != null
            $tmp = $current->next;  //临时变量，保存当前节点的下一节点,用于当前节点后移
            $current->next = $new;  //反转指针，把当前节点的下一指针反转，指向前面的节点
            $new = $current;        //前指针后移，到当前节点上，也就是当前节点变成pre
            $current = $tmp;        //当前节点后移，到下一节点，也就是下一节点变成current当前节点
        }
        //return $pre;
        $this->head = $new;       //反转完之后，将反转后的链表赋给 $this->head;
    }


    /**
     * 遍历链表
     */
    public function showList(){
        $current = $this->head;
        while($current != null){
            echo $current->data.'->';
            $current = $current->next;   //一定要将当前指针后移，否则会造成打印死循环
        }
    }
}

$reverse = new ReverseList();
$reverse->addNode(new Node(2));
$reverse->addNode(new Node(3));
$reverse->addNode(new Node(4));
$reverse->addNode(new Node(5));

echo "反转前链表：",$reverse->showList(),PHP_EOL;
$reverse->reverseList();
echo "反转后链表：",$reverse->showList(),PHP_EOL;


/**
 * 反转单链表 递归方式
 * 链表5个节点
 * 4ms 16.8M
 */
function diGuiReverse($head){
    if($head->next == null){
        return $head;
    }
    $new = diGuiReverse($head->next);
    $head->next->next = $head;
    $head->next = null;
    return $new;
}
$head = $reverse->head;
 var_dump(diGuiReverse($head));