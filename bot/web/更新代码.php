<?php
namespace bot\web;
  

use app\model;                          #模型  
use bot\mdb;                            #模型2 
use think\facade\Db;                    #数据库
use think\facade\Cache;                 #缓存
use support\Redis;                      #redis
use Webman\RedisQueue\Client as RQamsg; #异步队列
use Webman\RedisQueue\Redis as   RQmsg; #同步队列  
use Webman\Push\Api as push;            #push推送
use Tntma\Tntjwt\Auth;                  #jwt 


class update{ 
    
    public static function index($data){  
        $menu = Db::table("sys_menu")->where("authority",'/app/appStore/bot/qunfa')->find();
        if(empty($menu)){
            $sql['parentId'] = 221;
            $sql['auth'] = 0;
            $sql['title'] = '群发消息';
            $sql['menuType'] = 1;
            $sql['authority'] = '/app/appStore/bot/qunfa';
            $sql['tenantId'] = 3; 
            $menu['menuId'] = Db::table('sys_menu')->insertGetId($sql);  
        } 
        
        $roleMenu = Db::table("sys_role_menu")->where("tenantId",3)->where("roleId",9)->find();
        
        if(!strpos($roleMenu['menuText'], "{$menu['menuId']},")){ 
            $roleMenu['menuText'] = str_replace("[", "[{$menu['menuId']}, ", $roleMenu['menuText']);
            Db::table("sys_role_menu")->where("id",$roleMenu['id'])->update(["menuText"=>$roleMenu['menuText']]);
        }
        
        return true;
    }
} 