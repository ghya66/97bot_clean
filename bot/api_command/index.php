<?php
namespace bot\api_command;


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
     * 
     * $ret支持回调参数：sendText(文本消息) sendPhoto(发送照片) gif(发送动图)  anniu(消息按钮)   [ jianpan(回复键盘) && jianpanText(文字消息) || jianpanPhoto(照片消息)]
     * @param $id
     * @return array
     */
     
    
    #默认执行函数 
    public function index($message){ 
        $ret['key']=pathinfo(basename(__FILE__), PATHINFO_FILENAME); 
        $ret['level']=100; //优先级 (当存在多个模块都返回了文本消息或按钮时生效)数值大排上面 ，数值小排下面 
        
        if(preg_match('/\/(\w+)\s*(.*)/i', $message['text'], $com)){
            if(count($com) != 3){ 
                return $ret;
            } 
        }else{
            return $ret;
        }
        $type   = $com[1]; //命令
        $value  = $com[2]; //参数
        
        #多机器人群内同命令时 只处理被@的机器人命令
        if(!empty($value)){
            if($value[0] == "@"){
                if(substr($value, 1) !=  $message['bot']['API_BOT']){
                    return $ret;
                } 
            }
        }
        #判断命令是否机器人admin才能用
        if(is_admin($type)){
            if($message['formId'] != $message['bot']['Admin']){
                return $ret;
            }
            
        }
        #-----------上面代码固定不要修改  level 视情况调整改---------------- 
        
        switch ($type) {
            default: 
                break;
                
                
            case 'start':  
                // Redis::del("backJP1_{$message['chatId']}_{$message['formId']}");
                // Redis::del("backJP2_{$message['chatId']}_{$message['formId']}");
                // $ret['jianpanPhoto'] = "https://bot.tgbot.love/mokuai.png";
                // $ret['jianpanText'] = "本机器人框架正式模块化,集万千功能与一身,你可以在管理后台安装模块,卸载模块<b>每个模块包含了相对的功能</b>(它也可以是一个单独TRX兑换机器人,一个记账机器人,也可以是单独的开会员机器人)当然也可以拥有全部模块功能,<b>支持开发者自己开发功能模块[菜单命令,消息按钮,回复键盘]自动钩子加载完美整合兼容,且不会对现有的功能造成任何影响</b>,目前内测中..尽请期待(上线后我们会出开发模块功能完整教程,菜鸟也能轻易学会开发) - 下方模块钩子参数定义图预览";
                // $ret['jianpan'] = [
                //                     [
                //                         [
                //                             "text" => "💳 余额充值"
                //                         ],
                //                         [
                //                             "text" => "🏧 我的余额"
                //                         ],
                //                     ],
                //                     [
                //                         [
                //                             "text" => "🔗 推广地址"
                //                         ],
                //                         [
                //                             "text" => "💹 查看返利"
                //                         ],
                //                     ],
                //                 ];
                break; 
            
            case 'id': 
                if($message['chatType'] == "supergroup"){
                    $text = "<b>当前电报群ID：</b>"; 
                }
                if($message['chatType'] == "private"){
                    $text = "<b>您的电报ID：</b>"; 
                }
                $ret['sendText'] = "{$text}<code>{$message['chatId']}</code>";
                return $ret; 
                break; 
            
        }
        
         
        
         
        return $ret;  
    }
    
     
        
    
 
}