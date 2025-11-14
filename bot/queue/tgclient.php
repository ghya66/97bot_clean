<?php
namespace bot\queue;
use Webman\RedisQueue\Consumer;

use app\model;                          #模型  
use bot\mdb;                            #模型2
use think\facade\Db;                    #数据库
use think\facade\Cache;                 #缓存
use support\Redis;                      #redis
use Webman\RedisQueue\Client as RQamsg; #异步队列
use Webman\RedisQueue\Redis as   RQmsg; #同步队列

 

#guzzle 操作
use GuzzleHttp\Pool;
use GuzzleHttp\Client as Guzz_Client;
use GuzzleHttp\Psr7\Request as Guzz_Request; 
use GuzzleHttp\Promise as Guzz_Promise; 

class tgclient implements Consumer{ 
    public $queue = "queue_tgclient";  //消费队列名字：queue_文件名  注：投递队列消息时：RQamsg::send("queue_tgclient","队列消息内容");
    public $connection = "tgbot";   //连接名 固定别修改
    
    #消费代码
    public function consume($data){      
         if(is_mokuai("guanjianci") || is_mokuai("guanjianci2")){
             RQmsg::send("queue_guanjianci",$data);
         }
         return true;
    }
}
