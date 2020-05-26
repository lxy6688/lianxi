<?php
/**php的http端guzzle获取nlp服务器的数据(guzzle的用法)
<<<<<<< HEAD
=======
 * guzzle 参考： https://www.jianshu.com/p/392f966fc4d1  推荐一个 PHP 网络请求插件 Guzzle
 *
>>>>>>> b0b011766c332cd9ddcd439aff9cede0ce49e0c1
 * mysqli 事务更新多个表
 *
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/9/20
 * Time: 11:13
 */
header("Content-Type: text/json;charset=utf-8");
ini_set("memory_limit", "1024M");
set_time_limit(0);

require_once "./DaoMysqli.php";
require_once '/home/wwwroot/update_cdd/vendor/guzzle-http/vendor/autoload.php';
//require_once '/data/cron/update_cdd/vendor/guzzle-http/vendor/autoload.php';

define('NLP_SERVICE_URL','http://localhost:8081/api/v1.0/nlp/cdd');
$params = [
<<<<<<< HEAD
    "host" => "127.0.0.1",
    "user" => "root",
    "password" => '123456',
    "dbName" => "cdd_test"
=======
    "host" => "localhost",
    "user" => "*",
    "password" => '*',
    "dbName" => "*"
>>>>>>> b0b011766c332cd9ddcd439aff9cede0ce49e0c1
];

//先获取已发布的项目
$daoMysqli = DaoMysqli::getInstance($params);
$res =  $daoMysqli->getProjectsByPublish();
if(empty($res)) {
    echo '没有已发布的项目';
    exit;
}

<<<<<<< HEAD
=======
$rest = '';
>>>>>>> b0b011766c332cd9ddcd439aff9cede0ce49e0c1
foreach($res as $value){
    $post_content = $value->post_content;
    $post_title = $value->post_title;
    $ID = $value->ID;
    $returnRes = requestNlp($post_content);
    if($returnRes === false){
        echo 'ID为：'.$ID.',标题为:'.$post_title."的项目获取nlp服务false\n";
    }else{
        //根据ID查询wp_terms tag
        $isTagSucc =  $daoMysqli->getTermsTagById($ID);
        if($isTagSucc) {
            $tagNameArr = $syTagArr = [];
            foreach($isTagSucc as $tagValue) {
                $tagNameArr[] = $tagValue->name;
                $syTagArr[$tagValue->name] = $tagValue->term_id;
            }
            $add = array_diff($returnRes,$tagNameArr);
            $delete = array_diff($tagNameArr,$returnRes);
            if(!empty($add) && empty($delete)){
                //只有新加tag即可
<<<<<<< HEAD
            }elseif (!empty($delete) && empty($add)) {
                //只有删除标签
            }elseif (!empty($delete) && !empty($add)){
                //既有删除又有新加
                $rest = $daoMysqli->handleTermsTag($ID,$add,$delete);
            }
            if(!$rest){
                echo 'handle error : ID为：'.$ID.',标题为:'.$post_title."\n";
            }
            exit;
        }else{
            //插入新记录
        }


//        $insertStatus =  $daoMysqli->insertTagStatisById($ID, $returnRes);
//        if(!$insertStatus) {
//            echo 'insert error : ID为：'.$ID.',标题为:'.$post_title."插入失败！\n";
//        }
=======
                $rest = $daoMysqli->handleTermsTag($ID,$add);
            }elseif (!empty($delete) && empty($add)) {
                //只有删除标签
                $rest = $daoMysqli->handleTermsTag($ID,$add,$delete);
            }elseif (!empty($delete) && !empty($add)){
                //既有删除又有新加
                $rest = $daoMysqli->handleTermsTag($ID,$add,$delete);
            }else{
                echo 'newst ID为：'.$ID.',标题为:'.$post_title."的标签是最新,无须重建\n";
            }
            if($rest === false){
                echo 'handle error : ID为：'.$ID.',标题为:'.$post_title."\n";
            }
        }else{
            //插入新记录
            $rest = $daoMysqli->handleTermsTag($ID,$returnRes);
            if($rest === false){
                echo 'new insert error : ID为：'.$ID.',标题为:'.$post_title."\n";
            }
        }
>>>>>>> b0b011766c332cd9ddcd439aff9cede0ce49e0c1
    }
}

//调用nlp接口
function requestNlp($post_content) {
    $category = '餐饮,火锅,烧烤,炸鸡,鱼火锅,蟹煲,奶茶饮品,牛排杯,冒菜,果汁果饮,快餐,中餐,烤鱼,小吃,酸菜鱼,西餐,美蛙鱼头,蛋糕,甜品,酸奶,饺子,串串香,麻辣烫,卤味熟食,汉堡,外卖便当,冰淇凌,猪蹄,牛肉汤,贡茶,米粉米线,咖啡,面馆,黄焖鸡米饭,豆浆,韩国料理,铁板烧,花甲,小龙虾,砂锅,煲仔饭,包子,寿司,臭豆腐,麻辣香锅,焖锅,日本料理,酸辣粉,饭团,馄饨,凉皮,披萨,肉夹馍,生煎,豆腐脑,煎饼,牛杂,馅饼,粥,羊肉汤,面包,卤肉卷,螺蛳粉,烤鹅烤鸭,干锅,胡辣汤,烧饼,石锅鱼,旋转火锅,锅贴,啵啵鱼,牛肉火锅,泰国菜,炒饭,重庆小面,米粉,米线,烤肉,牛排,冷锅串串,海鲜,荷叶饭,羊肉火锅,茶餐厅,湘菜馆,地锅鸡,拌饭,自助餐,餐饮培训,跷脚牛肉,卤肉饭,锅盔,酒馆,鸡公煲,海鲜火锅,市井火锅,母婴,婴儿游泳,儿童乐园,母婴用品,月子中心,儿童玩具,儿童摄影,儿童手工,小儿推拿,产后恢复,零售,便利店,白酒,零食店,渔具,啤酒,茶叶,零售百货,超市,成人用品,红酒,药店,水果店,文具店,书店,眼镜,牛奶,佛教用品,无人售货,炒货,办公用品,生鲜,电子烟,服装,女装,内衣,鞋,品牌服装,男士专属,精品童装,箱包,休闲,户外,皮草,婚纱礼服,服装定制,教育,早教,幼儿园,教育培训,英语培训,潜能培训,IT培训,出国留学,学习辅导,作文培训,机器人教育,舞蹈培训,艺术教育,少儿编程,绘本馆,书法,国学馆,美术教育,数学培训,冬夏令营,在线教育,心理教育,建材,油漆涂料,地板,门窗栏杆,家居日用,灯饰,墙纸,家具,硅藻泥,服务,干洗,家政,汽车服务,摄影,家电清洗,宠物店,新奇特,房产中介,民宿,充电站,婚庆服务,游乐,VR体验馆,游乐,酒吧,KTV,网咖,影院,游戏,密室逃脱,珠宝礼品,饰品,礼品,首饰,珠宝,翡翠玉石,美容保健,汗蒸,美甲纹绣,养生,美容,化妆品,减肥瘦身,护肤品,健身房,视力保健,足浴足疗,美发,养发,皮肤管理,理疗,艾灸,推拿按摩,口腔牙科,整形美容,假发,家居,建材厨卫,装饰装修,家具饰品,窗帘,家居用品,集成墙饰,卫浴,家纺,电暖画,智能家居,智能锁,橱柜,集成灶,环保,空气净化,家电,汽车用品,电动车,数码用品,车衣,室内环保,平衡车,太阳能,新能源汽车,净水器,酒店,快捷酒店,商务酒店,民宿,旅馆,主题酒店,精品酒店,冰淇淋,';
    $client = new GuzzleHttp\Client();
    $options = [
        'post_content' => $post_content,
        'categories' => $category
    ];
    $data = [
        'body' => json_encode($options),
        'headers' => ['content-type'=> 'application/json', 'charset'=>'utf-8']
    ];
    $response = $client->post(NLP_SERVICE_URL,$data);
    if($response->getStatusCode() == '200') {
        $jsonData = $response->getBody()->getContents();
        $objData = json_decode($jsonData);
        $keywords = $objData->data->keywords;
        return $keywords;
        //var_dump($keywords);exit;
    }
    return false;
}
