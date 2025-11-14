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

//后端api文件 GET无需鉴权，POST需jwt鉴权(自动的不用管) 
class 模块名称 { 
   
    public function index(Request $request){
        $name = "模块名称 的 index 控制器方法(index方法 不进行登录权限效验 请勿写设计安全隐私的代码)"; 
        return response("你好这是：" . $name);
    } 
    
    #function 对应后台模块 >设置菜单>子菜单 中的（api方法）
    public function list(Request $request){   
        return viewTP( "/模块名称/list", ["name" => "97bot"]); #说明：views返回视图html，其中的list代表： bot/web/模块名称/list.html
    }
    
    #一个默认的获取对应模块的配置设置视图 
    public function set(Request $request){   
        return viewTP( "/模块名称/set", ["name" => "97bot"]); #说明：views返回视图html，其中的set代表： bot/web/模块名称/set.html
    }
     
    
    //获取：bot_account数据表,数据接口的例子 GET 
    public function api_list(Request $request){  
        $data = $request->get();
        $page = ($data["page"]-1)*$data["limit"]; 
        $jsonVal = array();
        $so =[];  
          
        //前置条件id字段大于0
        array_push($so,"id");
        array_push($so,'>');
        array_push($so,0); 
           
          
        //按状态匹配数据 
        if(!empty($data["state"])){
            if($data["state"] == 1){
              array_push($so,"zt");
              array_push($so,"=");
              array_push($so,1);   
            }else{
              array_push($so,"zt");
              array_push($so,"=");
              array_push($so,0);  
            } 
     
        } 
        //按字段进行模糊搜索数据
        if(!empty($data["keyword"])){
           array_push($so,$data["t"]?$data["t"]:"API_BOT");
           array_push($so,"like");
           array_push($so,"%".$data["keyword"]."%");
        }
          
        //定义事件日期的搜索
        if(!empty($data["timea"])){
           array_push($so,"create_time");
           array_push($so,">");
           array_push($so,substr($data["timea"],0,10));
           array_push($so,"create_time");
           array_push($so,"<");
           array_push($so,substr($data["timeb"],0,10));
        }
          
        $so = array_chunk($so,3);//拆分  
        
        $count = Db::name("bot_account")->where([$so])->count(); 
        $list = Db::name("bot_account")->where([$so])->limit($page,$data["limit"])->order("id desc")->select();  
     
        $jsonVal["count"] = $count;
        $jsonVal["list"] = $list;   
        return json(["code" => 1,"msg"=>"请求成功","data" => $jsonVal ]);  
    }
    
    
    #获取机器人配置 
    public function setget(Request $request){   
        $data = $request->get();  
        if(empty($data['bot'])){
            $模块名称_set =  mdb\模块名称::where('bot', 0)->find();  
        }else{
            if($data['bot'] == "全局设置"){
                $data['bot'] = 0;
            }
            $模块名称_set =  mdb\模块名称::where('bot', $data['bot'])->find();  
        } 
        return json(["code" => 1,  "msg" => "获取成功","data"=>$模块名称_set]);  
    }
    
    
    
    //通用的post新建数据接口
    public function create(Request $request){  
        $data = $request->post();
        return json(["code" => 1, "msg" => "新建成功" ,"data"=>"返回给前端的数据"]);
    }
    
    
    //通用的post数据更新接口
    public function update(Request $request){  
        $data = $request->post();
        if(empty($data['type'])){
            return json(["code" => 0,  "msg" => "缺少更新方法标识：type"]);  
        }
        if($data["data"]['bot'] == "全局设置"){
                $data["data"]['bot'] = 0;
        }
        switch ($data['type']) {
            default:
                return json(["code" => 0,  "msg" => "更新方法：{$data['type']} 未写代码,请在/bot/web/模块名称.php文件的function update 下增加一个case方法"]);  
                break;
                
            case 'set':
                $data["data"]["value"] = str_replace("\n", "|", $data["data"]["value"]);//换行
                $data["data"]["value"] = preg_replace("/\s+/", "", $data["data"]["value"]);//空格 
                $data["data"]["value"] = preg_replace("/\|{2,}/", "|", $data["data"]["value"]);//2个以上替换
                $data["data"]["value"] = preg_replace("/^\|/", "", $data["data"]["value"]);//开始
                $data["data"]["value"] = preg_replace("/\|$/", "", $data["data"]["value"]);//结束 
                
                Cache::delete("jizhang_{$data["data"]['bot']}");
                $模块名称_set =  mdb\模块名称::where('bot', $data["data"]['bot'])->find(); 
                if(empty($模块名称_set)){
                    if(isset($data["data"]['id'])){
                       unset($data["data"]['id']); 
                    } 
                    mdb\模块名称::create($data["data"]);
                }else{
                    $模块名称_set->save($data["data"]); 
                } 
                break; 
        }
        
        return json(["code" => 1, "msg" => "更新成功" ,"data"=>"返回给前端的数据"]);
    }
    
    
    //通用的post删除数据接口
    public function del(Request $request){  
        return json(["code" => 1, "msg" => "删除成功" ,"data"=>"返回给前端的数据"]);
    }
    
    
}