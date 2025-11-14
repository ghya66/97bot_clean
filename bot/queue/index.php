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

#tron 钱包操作
use TNTma\TronWeb\Address;
use TNTma\TronWeb\Account;
use TNTma\TronWeb\Tron; 

#guzzle 操作
use GuzzleHttp\Pool;
use GuzzleHttp\Client as Guzz_Client;
use GuzzleHttp\Psr7\Request as Guzz_Request; 
use GuzzleHttp\Promise as Guzz_Promise; 

class index implements Consumer{ 
    public $queue = "queue_index";  //消费队列名字：queue_文件名  注：投递队列消息时：RQamsg::send("queue_index","队列消息内容");
    public $connection = "tgbot";   //连接名 固定别修改
    
    #消费代码
    public function consume($data){    
        
        var_dump("bot/queue/index.php 收到队列消费消息",$data);
        
    }
}