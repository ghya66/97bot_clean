<?php
namespace bot\api_game;

use app\model;                          #模型  
use bot\mdb;                            #模型2
use think\facade\Db;                    #数据库
use think\facade\Cache;                 #缓存
use support\Redis;                      #redis
use Webman\RedisQueue\Client as RQamsg; #异步队列
use Webman\RedisQueue\Redis as   RQmsg; #同步队列
 

//对应类文件
use plugin\tgbot\app\controller\Base;
use plugin\tgbot\app\controller\Template;


use GuzzleHttp\Pool;
use GuzzleHttp\Client as Guzz_Client;
use GuzzleHttp\Psr7\Request as Guzz_Request; 
use GuzzleHttp\Promise as Guzz_Promise; 

#游戏game按钮点击事件
class index  { 
    
    #这是通用的订阅事件 - 只要机器人收到任何游戏按钮点击 - 都会触发里面的代码  - 你可以新建N个文件干不同的事
    public function index($game){ 
          
        return false;  
    }
    
    
    
    
    #这是自定义的消息事件
    public function diy($game){
        
       var_dump("diy 事件");
         
      return false;  
    }
    
    
    
    
 
}