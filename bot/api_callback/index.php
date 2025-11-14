<?php
namespace bot\api_callback;

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
 
class index  {  
    /**
     * 【参数解答】
     * $message["bot"]          =   机器人配置信息
     * $message["callId"]       =   点击唯一ID
     * $message['msgId']        =   消息ID
     * 
     * $message["chatType"]     =   聊天类型 群=supergroup  私聊=private
     * $message["chatId"]       =   聊天窗口ID
     * $message["chatUser"]     =   聊天窗口用户名(@xxx)
     * $message["chatName"]     =   聊天窗口标题 - 群组=群名称 ，用户=用户昵称
     * 
     * $message["formId"]       =   发消息的人ID
     * $message["formUser"]     =   发消息的人用户名
     * $message["formName"]     =   发消息的人昵称
     * $message["fromVip"]      =   发消息的人是否是电报VIP 0否 1是
     * $message["fromLang"]     =   发消息的人电报客户端语言(多语言需要)
     * 
     * $message["isHuiFu"]       =   是否属回复消息？1是 0否 ：↓ 属于回复消息时才有以下参数 
     *      $message["HuiFu"]["msgId"]  =   被回复的消息ID   
     *      $message["HuiFu"]["isBot"]  =   被回复的目标是否为机器人 1是0否
     *      $message["HuiFu"]["toId"]   =   被回复消息的人ID
     *      $message["HuiFu"]["toUser"] =   被回复消息的人用户名
     *      $message["HuiFu"]["toVip"]  =   被回复消息的人是否是电报VIP 0否 1是
     *   
     * $message["time"]         =   消息到达-服务器时间戳
     * $message["msgTime"]      =   消息发布时间戳  电报官方时间
     * $message["editTime"]     =   消息最后编辑时间戳 0未编辑过
     * 
     * $message["btnData"]      =   消息按钮对应 消息事件
     * 
     * $message["gamaId"]       =   游戏标识ID   
     * $message["gameName"]     =   游戏唯一标识 游戏才有
     * 
     *  ■■ $ret ■■ 返回参数说明：
     *  delMessage=1 代表点击按钮后删除原消息
     *  alert (最高优先级 - 下面参数无效) back=0 代表编辑按钮时禁止增加返回按钮
     *  jianpan(键盘按钮) + jianpanText(文本消息) 或 jianpanPhoto(发送照片) （第2优先级 - 下面参数无效）
     *  sendText(发送文本消息),sendPhoto(发送照片),sendVideo(发送视频)  ||  editText(编辑消息),editPhoto(编辑照片(文本消息时无效)) ||  anniu(消息按钮)
     * 
     * @param $id
     * @return array
     */
     
    
    #默认执行函数 
    public function index($message){ 
        $ret['key']=pathinfo(basename(__FILE__), PATHINFO_FILENAME); 
        $ret['level']=100; //优先级 (当存在多个模块都返回了文本消息或按钮时生效)数值大排上面 ，数值小排下面
        #-----------------以上核心代码勿动 level 视情况调整改动--------------------------
        
      
         
        #按钮例子
        if($message['btnData'] == "取消操作"){
            $ret['nextdel']=1;
            $ret['delMessage'] =1;
            return $ret;
        }
        // $ret['anniu'] = [
        //             [
        //                 [
        //                     "text" => "按钮名称1",
        //                     "callback_data" => "按钮事件_1"
        //                 ]
        //             ],
        //             [
        //                 [
        //                     "text" => "按钮名称2",
        //                     "callback_data" => "按钮事件_2"
        //                 ]
        //             ],
        //         ]; 
 
 
        return $ret;
    }
}