<?php
namespace bot\api_query;

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

#这是通用的用户进出群消息  - 都会触发里面的代码  - 你可以新建N个文件干不同的事
class index  { 
    /**
     * 【参数解答】
     * $message["bot"]          =   机器人配置信息
     * 
     * $message["id"]           =   内联查询ID
     * $message["text"]         =   消息内容
     * 
     * $message["chatType"]     =   会话窗口类型  
     * 
     * $message["formId"]       =   事件成员ID
     * $message["formUser"]     =   事件成员用户名
     * $message["formName"]     =   事件成员昵称
     * $message["fromVip"]      =   事件成员是否是电报VIP 0否 1是
     * $message["fromLang"]     =   事件成员电报客户端语言(多语言需要)
     * 
     * $message["isBot"]        =   是否为机器人
     * 
     * $message["location"]     =   用户位置信息(需要请求权限) 
     * 
     * $ret 支持回调参数：name(标题) tips(描述)  sendText(消息文本)   anniu(消息按钮)  
     * 
     * 内联功能返回的函数和其它不一样请注意！
     * 
     */ 
    public function index($message){ 
        $ret["key"]=pathinfo(basename(__FILE__), PATHINFO_FILENAME); //自动定义文件key 用于鉴权机器人是否有权使用该模块 
        $ret["level"]=100; //优先级 (当存在多个模块都返回了文本消息或按钮时生效)数值大排上面 ，数值小排下面
        #-----------------以上核心代码勿动 level 视情况调整改动-------------------------- 
         
        echo "收到内联消息：{$message["text"]}"; 
        
     
        
         
        // $ret["name"] = "{$message["text"]}"; 
        // $ret["tips"] = "搞毛呢兄弟";
        // $ret["sendText"] = "{$message["text"]}";
        // $ret["anniu"] = [
        //             [
        //                 [
        //                     "text" => "小四1",
        //                     "callback_data" => "按钮事件_1"
        //                 ]
        //             ],
        //             [
        //                 [
        //                     "text" => "小四2",
        //                     "callback_data" => "按钮事件_2"
        //                 ]
        //             ],
        //         ];
        return $ret;  
    }
     
    
    
    
 
}