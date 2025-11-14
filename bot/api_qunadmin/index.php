<?php
namespace bot\api_qunadmin;

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

#群组 频道 有人成为管理员的消息
class index  { 
    /**
     * 【参数解答】
     * $message["bot"]          =   机器人配置信息
     * $message["msgId"]        =   聊天消息唯一ID
     * 
     * $message["chatType"]     =   群组频道类型
     * $message["chatId"]       =   群组ID
     * $message["chatUser"]     =   群组用户名(@xxx)
     * $message["chatName"]     =   群组=群名称
     * 
     * $message["status"]       =   事件类型:admin 升级为管理员  unadmin取消管理员
     * $message["auth"]         =   var_dump($message["auth"])  可以打印查看相关的权限详细数据
     * 
     * $message["formId"]       =   事件成员ID
     * $message["formUser"]     =   事件成员用户名
     * $message["formName"]     =   事件成员昵称
     * $message["fromVip"]      =   事件成员是否是电报VIP 0否 1是
     * $message["fromLang"]     =   事件成员电报客户端语言(多语言需要)
     * 
     * $message["isBot"]        =   是否为机器人
     * 
     * $message["time"]         =   消息到达-服务器时间戳  
     * $message["tgTime"]       =   消息到达-电报官方时间戳 
     * 
     * $ret支持回调参数：sendText(文本消息) sendPhoto(发送照片)  sendVideo(发送视频) sendFile(发送文件)  anniu(消息按钮)   [ jianpan(回复键盘) && jianpanText(文字消息) || jianpanPhoto(照片消息)]
     * 
     * 说明照片 视频 文件 支持填写服务器上的绝对路径比如照片：/etc/xxx.png  文件：/www/xxx/kami.zip  视频:/www/xxx/123.mp4
     * 
     */ 
    public function index($message){ 
        $ret["key"]=pathinfo(basename(__FILE__), PATHINFO_FILENAME);  
        $ret["level"]=100; //优先级 (当存在多个模块都返回了文本消息或按钮时生效)数值大排上面 ，数值小排下面
        #-----------------以上核心代码勿动 level 视情况调整改动--------------------------
         
         
        switch ($message["status"]) {
            default: 
                break;  
                
            case "admin":  
                echo "{$message['formId']} 成为管理员";
                break;  
                
            case "unadmin":  
                echo "{$message['formId']} 被取消管理员";
                break;     
        }
         
        
        return $ret;  
    }
     
    
    
    
 
}