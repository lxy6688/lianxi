<?php
/**
 * 搜索二叉树的遍历
 *
 * Created by PhpStorm.
 * User: yang
 * Date: 2019/4/18
 * Time: 12:16
 */
class Node {
    public $data;
    public $left;
    public $right;

    function __construct($data)
    {
        $this->data = $data;
    }
}

class Tree {
    /**
     * 按层次遍历 (广度优先遍历)   --  迭代法 利用队列
     *
     * @param $root
     */
    public function levelFind($root){
        if(is_null($root)){
            return false;
        }
        $queue = $resp = [];
        //先把根节点入队
        array_unshift($queue, $root);
        //持续输出节点值，直到队列为空
        while(!empty($queue)){
            $node = array_pop($queue);  //弹出队尾元素
            echo $node->data;
            $resp[] = $node->data;

            //左子节点先入队, 然后右子节点再入队
            if(!is_null($node->left)){
                array_unshift($queue,$node->left);
            }
            if(!is_null($node->right)){
                array_unshift($queue,$node->right);
            }
        }
        return $resp;
    }

    /**
     * 前序遍历(迭代法)
     *
     * 节点->左子树->右子树
     * 利用栈，先右子树入栈，再左子树入栈
     *
     * @param $root
     * @return bool
     */
    public function preIteration($root) {
        if(is_null($root)) {
            return false;
        }
        $stack = $resp = [];

        array_push($stack, $root);
        while(!empty($stack)) {
            $node = array_pop($stack);
            echo $node->data . "  ";

            //右子树压栈
            if($node->right != null) {
                array_push($stack,$node->right);
            }

            //左子树压栈
            if($node->left != null) {
                array_push($stack,$node->left);
            }
        }
    }

    /**
     * 前序遍历(递归)
     *
     * 节点-> 左子树 -> 右子树
     *
     * @param $root
     * @return bool
     */
    public function preIterationDigui($root){
        if(is_null($root)) {
            return false;
        }

        echo $root->data."  ";
        $this->preIterationDigui($root->left);
        $this->preIterationDigui($root->right);
    }


    /**
     * 前序遍历(递归2)
     *
     * @param $root
     * @param array $arr
     * @return array|bool
     */
    public function preIterationDiguiTwo($root, &$arr=[]){
        if(is_null($root)) {
            return false;
        }

        $arr[] = $root->data;

        $this->preIterationDiguiTwo($root->left, $arr);
        $this->preIterationDiguiTwo($root->right, $arr);

        return $arr;
    }


    /**
     * ====================================中序遍历===============================================
     * 力扣  遍历二叉树
     * https://leetcode-cn.com/problems/binary-tree-preorder-traversal/solution/leetcodesuan-fa-xiu-lian-dong-hua-yan-shi-xbian-2/
     */
    /**
     * 中序遍历(迭代法)
     * 左子树 -> 节点 -> 右子树
     *
     * 利用栈stack， 左子树不断的入栈，打印节点，  右子树再不断的入栈
     *
     * @param $root
     * @return bool
     */
    public function midIteration($root) {
        if(is_null($root)) {
            return false;
        }

        $stack = [];
        $cur = $root;
        while(!empty($stack) || $cur != null) {
            while($cur != null) {                  //左子树不断的入栈, 类似递归
                array_push($stack, $cur);
                $cur= $cur->left;
            }

            $node = array_pop($stack);    //打印节点
            echo $node->data."  ";

            if($node->right != null) {         // 对右子树进行同样的中序遍历
                $cur = $node->right;
            }
        }

    }

    /**
     * 递归法实现中序遍历
     *
     * @param $root
     * @return bool
     */
    public function midIterationDigui($root){
        if(is_null($root)) {
            return false;
        }

        $this->midIterationDigui($root->left);
        echo $root->data." ";
        $this->midIterationDigui($root->right);
    }


    /**
     * ====================================后序遍历===============================================
     * 力扣  遍历二叉树
     * https://leetcode-cn.com/problems/binary-tree-preorder-traversal/solution/leetcodesuan-fa-xiu-lian-dong-hua-yan-shi-xbian-2/
     */
    /**
     * 后序遍历(迭代法) -------------困难级别
     * 左子树 -> 右子树 -> 节点
     *
     * 利用栈stack， 左子树不断的入栈，  右子树再不断的入栈，最后打印节点
     *
     * @param $root
     * @return bool
     */
    public function afterIteration($root) {    // TODO  这个是错误的，待改正
        if(is_null($root)) {
            return false;
        }

        $stack = [];
        $leftCur = $root;
        $rightCur = $root->right;
        while(!empty($stack) || $leftCur != null || $rightCur != null) {
            while($leftCur != null) {
                array_push($stack,$leftCur);
                $leftCur = $leftCur->left;
            }

            while($rightCur != null) {
                array_push($stack,$rightCur);
                $rightCur = $rightCur->right;
            }

            $node = array_pop($stack);
            echo $node->data."  ";
        }
    }

    /**
     * 递归法实现后序遍历
     *
     * @param $root
     * @return bool
     */
    public function afterIterationDigui($root) {
        if(is_null($root)) {
            return false;
        }

        $this->afterIterationDigui($root->left);
        $this->afterIterationDigui($root->right);

        echo $root->data."  ";
    }

}

/**
 * 构建节点和搜索二叉树
 */
$a = new Node(10);
$b = new Node(8);
$c = new Node(12);
$d = new Node(7);
$e = new Node(9);
$f = new Node(11);
$g = new Node(13);

$a->left = $b;
$a->right = $c;
$b->left = $d;
$b->right = $e;
$c->left = $f;
$c->right = $g;

$tree = new Tree();
//$res = $tree->levelFind($a);
//echo json_encode($res);
//var_dump($a);exit;


//前序遍历
//$tree->preIteration($a);
//$tree->preIterationDigui($a);
//print_r($tree->preIterationDiguiTwo($a)) ;


//中序遍历
//$tree->midIteration($a);
//$tree->midIterationDigui($a);


//后序遍历
$tree->afterIteration($a);
//$tree->afterIterationDigui($a);