<?php
namespace bot\api_botqun;

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

#这是通用的机器人加入频道 群组 退出等专属消息 
class index  { 
    /**
     * 【参数解答】
     * $message["bot"]          =   机器人配置信息 
     * 
     * $message["chatType"]     =   聊天类型 群=supergroup  频道=channel
     * $message["chatId"]       =   聊天窗口ID
     * $message["chatUser"]     =   聊天窗口用户名(@xxx)
     * $message["chatName"]     =   聊天窗口标题 - 群组=群名称 ，用户=用户昵称
     * 
     * $message["formId"]       =   操作的人ID
     * $message["formUser"]     =   操作的人用户名
     * $message["formName"]     =   操作的人昵称
     * $message["fromVip"]      =   操作的人是否是电报VIP 0否 1是
     * $message["fromLang"]     =   操作的人电报客户端语言(多语言需要)
     * $message["formIsBot"]    =   操作的人是否是机器人
     * 
     * $message["isBot"]        =   对象是否为机器人
     * $message["botId"]        =   机器人ID
     * $message["botUser"]      =   机器人用户名
     * $message["botName"]      =   机器人昵称
     * $message["botVip"]       =   机器人是否是电报VIP 0否 1是
     * $message["botLang"]      =   机器人电报客户端语言(多语言需要) 
     * 
     * $message["time"]         =   消息到达-服务器时间戳  
     * $message["tgTime"]       =   消息到达-电报官方时间戳 
     * 
     * $message["status"]       =   member入群  left出群  admin成为管理员 unadmin被取消管理员(同时：$message["auth"] 可以打印管理员权限详情)
     * $message["auth"]         =   admin 和 unadmin 时才有该参数,机器人新的权限详情
     * 
     * $ret支持回调参数：sendText(文本消息) sendPhoto(发送照片)  sendVideo(发送视频) sendFile(发送文件)  anniu(消息按钮)  
     * 
     * 说明照片 视频 文件 支持填写服务器上的绝对路径比如照片：/etc/xxx.png  文件：/www/xxx/kami.zip  视频:/www/xxx/123.mp4
     * 
     */ 
    public function index($message){ 
        $ret["key"]=pathinfo(basename(__FILE__), PATHINFO_FILENAME); //自动定义文件key 用于鉴权机器人是否有权使用该模块 
        $ret["level"]=100; //优先级 (当存在多个模块都返回了文本消息或按钮时生效)数值大排上面 ，数值小排下面
        #-----------------以上核心代码勿动 level 视情况调整改动-------------------------- 
        
        if($message["chatType"] == "supergroup"){ //群组
            switch ($message["status"]) {
                default:
                    echo "未知机器人群组事件类型{$message['status']}\n";
                    break;
                
                case "kicked":
                    echo "机器人被踢出群事件 {$message['botUser']}\n";
                    break;    
                    
                case "member":
                    echo "机器人入群事件 {$message['botUser']}\n";
                    break;
                    
                    
                case "left": 
                    echo "机器人退出群事件 {$message['botUser']}\n";
                    break; 
                    
                
                case "admin":
                    echo "机器人成为管理员 {$message['botUser']}\n";
                    #$ret["sendText"] = "😊<b>很高兴,能成为本群管理员，我将尽心竭力为您提供服务！</b>"; 
                    break;     
                
                case "unadmin":
                    echo "机器人被取消管理员 {$message['botUser']}\n";
                    #$ret["sendText"] = "☹️<b>失去了管理员资格的我，已经不能再为您提供服务！</b>";
                    break;  
                 
            } 
        }else if($message["chatType"] == "channel"){//频道
            switch ($message["status"]) {
                default:
                    echo "未知机器人频道事件类型{$message['status']}\n";
                    break;
                
                case "kicked":
                    echo "机器人被踢出频道事件 {$message['botUser']}\n";
                    break;    
                    
                case "member":
                    echo "机器人加入频道事件 {$message['botUser']}\n";
                    break;
                    
                    
                case "left": 
                    echo "机器人退出频道事件 {$message['botUser']}\n";
                    break;  
                 
            } 
        }
        return $ret;  
    }
     
    
    
    
 
}