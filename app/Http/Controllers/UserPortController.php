<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use App\Zcc;

class UserPortController extends Controller
{
    //
    public function index()
    {

        $res= file_get_contents('php://input');
        $json_data=json_decode($res);
//        dd($json_data);
        $where = [
            'user_name'=>$json_data->user_name,
            'password'=> $json_data->password
        ];
        $res=Zcc::insertGetId($where);
        if($res){
            $json=[
                'erron'=>0,
                'mag'=>'注册成功成功',
            ];
            $dd=json_encode($json);
            echo $dd;
        }else{
            $json=[
                'erron'=>50001,
                'mag'=>'出现异常'
            ];
            $dd=json_encode($json);
            echo $dd;
        }
    }
    public function useradd()
    {
        $res= file_get_contents('php://input');
        $json_data=json_decode($res);
        $name=$json_data->user_name;
        $pwd=$json_data->password;
        $where1=[
            'user_name'=>$name
        ];
        $res=Zcc::where($where1)->first();
  
        $pwds=$res->password;
        if($res){
            $new_data= base64_decode($pwds);
            $public_key=openssl_pkey_get_public("file://".storage_path("key/public.pem"));
            openssl_public_decrypt($new_data,$ii,$public_key);
            if($ii == $pwd){
                $token=md5(Str::random(15).'lyz'.time());
                $ksy_token='login_token:uid'.$res->u_id;
                Redis::set($ksy_token,$token);
                Redis::expire($ksy_token,259200);
                $json=[
                    'erron'=>0,
                    'mag'=>'登录成功',
                    'token'=>$token,
                    'uid'=>$res->u_id
                ];
                $dd=json_encode($json);
                return $dd;
            }else{
                $json=[
                    'erron'=>50005,
                    'mag'=>'密码或账号出现错误'
                ];
                $dd=json_encode($json);
                return $dd;
            }
        }else{
            $json=[
                'erron'=>50002,
                'mag'=>'密码或账号出现错误'
            ];
            $dd=json_encode($json);
            return $dd;
        }
    }
}
