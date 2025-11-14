<?php
namespace bot\api_userqun;

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
     * $message['bot']          =   机器人配置信息
     * $message['msgId']        =   聊天消息唯一ID
     * 
     * $message['chatType']     =   群组频道类型
     * $message['chatId']       =   群组ID
     * $message['chatUser']     =   群组用户名(@xxx)
     * $message['chatName']     =   群组=群名称
     * 
     * $message['status']       =   事件类型:new 新入群  exit退出群
     * 
     * $message['formId']       =   事件成员ID
     * $message['formUser']     =   事件成员用户名
     * $message['formName']     =   事件成员昵称
     * $message['fromVip']      =   事件成员是否是电报VIP 0否 1是
     * $message['fromLang']     =   事件成员电报客户端语言(多语言需要)
     * 
     * $message['isBot']        =   是否为机器人
     * 
     * $message['time']         =   消息到达-服务器时间戳  
     * $message['tgTime']       =   消息到达-电报官方时间戳 
     * 
     * $ret支持回调参数：sendText(文本消息) sendPhoto(发送照片)  sendVideo(发送视频)  anniu(消息按钮)   [ jianpan(回复键盘) && jianpanText(文字消息) || jianpanPhoto(照片消息)]
     */ 
    public function index($message){ 
        $ret['key']=pathinfo(basename(__FILE__), PATHINFO_FILENAME); //自动定义文件key 用于鉴权机器人是否有权使用该模块 
        $ret['level']=100; //优先级 (当存在多个模块都返回了文本消息或按钮时生效)数值大排上面 ，数值小排下面
        #-----------------以上核心代码勿动 level 视情况调整改动--------------------------
         
         
        switch ($message['status']) {
            default: 
                echo "其它用户群事件：{$message['status']}";
                break;  
                
            case 'exit':  
                echo "用户退出群事件：{$message['status']}";
                break;
            case 'new':  
                echo "用户加入群事件：{$message['status']}";
                break;    
        }
         
        
        return $ret;  
    }
     
    
    
    
 
}