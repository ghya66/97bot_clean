<?php
namespace bot\api_message;

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
#通用消息
class index  {  
    /**
     * 【参数解答】
     * $message['bot']          =   机器人配置信息
     * $message['msgId']        =   聊天消息唯一ID
     * 
     * $message['chatType']     =   聊天类型 群=supergroup  私聊=private
     * $message['chatId']       =   聊天窗口ID
     * $message['chatUser']     =   聊天窗口用户名(@xxx)
     * $message['chatName']     =   聊天窗口标题 - 群组=群名称 ，用户=用户昵称
     * 
     * $message['formId']       =   发消息的人ID
     * $message['formUser']     =   发消息的人用户名
     * $message['formName']     =   发消息的人昵称
     * $message['fromVip']      =   发消息的人是否是电报VIP 0否 1是
     * $message['fromLang']     =   发消息的人电报客户端语言(多语言需要)
     * 
     * $message['text']         =   消息文本内容
     * $message['time']         =   消息到达-服务器时间戳  
     * $message['tgTime']       =   消息到达-电报官方时间戳 
     * 
     * $message['photo']        =   有图片时为图片数据信息-自己打印查看     没有为0
     * $message['document']     =   有文件时为文件数据信息-自己打印查看     没有为0
     * $message['video']        =   有视频时为视频数据信息-自己打印查看     没有为0
     * $message['gif']          =   有动画图片时为动画数据信息-自己打印查看 没有为0
     * 
     * $message['isHuiFu']       =   是否属回复消息？1是 0否 ：↓ 属于回复消息时才有以下参数 
     *      $message['HuiFu']['msgId']  =   被回复的消息ID   
     *      $message['HuiFu']['isBot']  =   被回复的目标是否为机器人 1是0否
     *      $message['HuiFu']['botUser']=   被回复的目标是机器人时才有效,返回机器人用户名
     *      $message['HuiFu']['toId']   =   被回复消息的人ID
     *      $message['HuiFu']['toUser'] =   被回复消息的人用户名
     *      $message['HuiFu']['toVip']  =   被回复消息的人是否是电报VIP 0否 1是
     *      $message['HuiFu']['text']   =   被回复消息的内容
     * 
     * $ret支持回调参数：sendText(文本消息) sendPhoto(发送照片) sendVideo(发送视频)  anniu(消息按钮)   [ jianpan(回复键盘) && jianpanText(文字消息) || jianpanPhoto(照片消息)]
     * @param $id
     * @return array
     */
     
    
    #默认执行函数 
    public function index($message){   
        $ret['key']=pathinfo(basename(__FILE__), PATHINFO_FILENAME); 
        $ret['level']=100; //优先级 (当存在多个模块都返回了文本消息或按钮时生效)数值大排上面 ，数值小排下面 
        if($message["isHuiFu"]){
            if($message["HuiFu"]["isBot"]){ 
                if($message["HuiFu"]["botUser"] != $message["bot"]["API_BOT"]){
                   return $ret; 
                }
            }
        }
        #-----------------以上核心代码勿动 level 视情况调整改动--------------------------
        
        // 这是一个默认通用的全局免费功能,私聊给机器人发送图片 视频  文件 时返回文件file信息
        
        if($message['chatType'] =="private"){  
            
            
            if(pipei($message['text'],"^发送文件")){
                $file = explode(" ",$message['text']);
                if(count($file) == 2){
                    if(substr($file[1],-4) == ".zip"){
                        $ret['sendText'] = "来自服务器的文件";
                        $ret['sendFile'] = $file[1];
                        return $ret; 
                    }
                     
                }
                return $ret; 
            }
            if($message['formId'] == $message['bot']['Admin']){ 
                if($message['photo']){
                    $count = count($message['photo']);
                    $ret['sendText'] =  "图片File地址：\n<code>".$message['photo'][$count-1]['file_id']."</code>\n\n你可以在机器人后台使用该图片地址用作欢迎消息,定时广告等(<b>该图片地址仅本机器人可用</b>)";  
                }else if($message['video']){ 
                    $ret['sendText'] =  "视频File地址：\n<code>".$message['video']['file_id']."</code>\n\n你可以在机器人后台使用该视频地址用作欢迎消息,定时广告等(<b>该视频地址仅本机器人可用</b>)"; 
                }else if($message['document']){ 
                    $ret['sendText'] =  "文件名称：<b>{$message['document']['file_name']}</b>\n文件类型：<b>{$message['document']['mime_type']}</b>\n文件大小：<b>".round($message['document']['file_size']/1024,2)." </b>Kb\n文件File地址：\n<code>".$message['document']['file_id']."</code>\n\n你可以在机器人消息中使用该文件地址(<b>该文件地址仅本机器人可用</b>)";
                } 
            }
            
        } 
        
 
        
       
          
        return $ret;  
    }
    
     
        
    
 
}