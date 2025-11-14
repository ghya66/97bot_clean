<?php
namespace bot\web;

use app\model;                          #模型  
use bot\mdb;                          #模型2
use think\facade\Db;                    #数据库
use think\facade\Cache;                 #缓存
use support\Redis;                      #redis
use Webman\RedisQueue\Client as RQamsg; #异步队列
use Webman\RedisQueue\Redis as   RQmsg; #同步队列

use GuzzleHttp\Pool;
use GuzzleHttp\Client as Guzz_Client;
use GuzzleHttp\Psr7\Request as Guzz_Request; 
use GuzzleHttp\Promise as Guzz_Promise; 

use support\Request; 

class index{ 
   
    public function index(Request $request){
        $tips = "控制器：index.php 的index方法 这个文件为公共文件,所有方法不需要登录鉴权就可以访问"; 
        return response($tips);
    } 
    
    
    public function testjson(Request $request){ 
        $text = "控制器：index.php 的hi方法,这里演示了 如何返回json数据格式"; 
        return json(["code" => 1, "msg" => "请求成功" ,"data"=>$text]);
    }
    
    
    public function testhtml(Request $request){ 
        $text = "控制器：index.php 的test2方法,这里演示了返回渲染html 视图模板以及传参"; 
        
        return views( '/index/h123', ['name' => '97bot']); #说明：其中的index/h123 代表对应模板文件： /index/h123.html
    }
    
    
}