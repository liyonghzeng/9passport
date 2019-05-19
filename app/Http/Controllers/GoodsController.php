<?php

namespace App\Http\Controllers;

use App\Order;
use App\Xq;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Redis;
use App\Goods;
use App\Cart;


use Illuminate\Support\Facades\DB;


class GoodsController extends Controller
{
    //
    public function goodsList()
    {
        $goods_name=Goods::all();
        $res=json_encode($goods_name);
        echo $res;
    }
    public function partiGoods()
    {
        $res = $_GET['goods_id'];
        $data=Goods::where(['goods_id'=>$res])->first();
        $json_data=json_encode($data);
        echo $json_data;
    }

    public function addcart()
    {

        $res = $_GET['goods_id'];
        $u_id = $_GET['uid']??"";
//        echo $u_id;die;
        $data=Goods::where(['goods_id'=>$res])->first();
        $where = [
            'goods_id'=>$data->goods_id,
            'goods_name'=>$data->goods_name,
            'goods_price'=>$data->goods_price,
            'cart_num'=>1,
            'u_id'=>$u_id
        ];
        $res=Cart::insertGetId($where);
        if($res){
            $json=[
                'erron'=>0,
                'mag'=>'添加购物车成功',
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



    public function cartlist()
    {
        $goods_id=$_GET['goods_id']??"";
        $u_id = $_GET['uid']??"";
        $res=Cart::where(['u_id'=>$u_id,'zt'=>0])->get();

        if($res){
            $dd=json_encode($res);
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
    public function cartdd()
    {
        $cart_id=$_GET['cart_id']??"";
        $u_id = $_GET['uid']??"";
        $where  = [
             'cart_id'=>$cart_id,
            'u_id'=>$u_id
        ] ;
        $res=Cart::where($where)->first()->toArray();
        $num=$res['goods_price']*$res['cart_num'];
        DB::beginTransaction();
        try{
            $wheres = [
                'order_mun'=>md5(Str::random(16).time()),
                'odergoods_num'=>$res['cart_num'],
                'goods_name'=>$res['goods_name'],
                'order_price'=> $num,
                'order_time'=>time(),
                'zf'=>0
            ];
            Xq::insertGetId($wheres);
            $info= Order::insertGetId($wheres);
             Xq::insertGetId($wheres);
            $res=Cart::where(['cart_id'=>$cart_id,'u_id'=>$u_id])->update(['zt'=>1]);
            DB::commit();
        }catch (\Exception $e) {
            //接收异常处理并回滚
            DB::rollBack();
            $r =[
                'erron'=>50006,
                'mag'=>'失败了奥？！？！？！？！'
            ];
            die(json_encode($r,JSON_UNESCAPED_UNICODE));
        }
        $r =[
            'erron'=>0,
            'mag'=>'生成订单成功'
        ];
        die(json_encode($r,JSON_UNESCAPED_UNICODE));

    }
    public function orderlist()
    {
        $Order_name=Order::all();
        $res=json_encode($Order_name);
        echo $res;
    }
    //支付宝
    public function alipays()
    {
//       ehco 'alipay
    }

//  接口
    public function jk()
    {
        $where = [
            'app_id'=>2016092500595896,
            'method'=>'alipay.trade.wap.pay',
            'format'=>'JSON',
            'charset'=>'utf-8',
            'sign_type'=>'RSA2',
            'version'=>1.0,
//            'timestamp'=>time()
        ];
    }
//REQUEST URL: https://openapi.alipay.com/gateway.do
//REQUEST METHOD: POST
//CONTENT:
//app_id=2014072300007148
//method=alipay.mobile.public.menu.add
//charset=GBK
//sign_type=RSA2
//timestamp=2014-07-24 03:07:50
//biz_content={"button":[{"actionParam":"ZFB_HFCZ","actionType":"out","name":"话费充值"},{"name":"查询","subButton":[{"actionParam":"ZFB_YECX","actionType":"out","name":"余额查询"},{"actionParam":"ZFB_LLCX","actionType":"out","name":"流量查询"},{"actionParam":"ZFB_HFCX","actionType":"out","name":"话费查询"}]},{"actionParam":"http://m.alipay.com","actionType":"link","name":"最新优惠"}]}
//sign=e9zEAe4TTQ4LPLQvETPoLGXTiURcxiAKfMVQ6Hrrsx2hmyIEGvSfAQzbLxHrhyZ48wOJXTsD4FPnt+YGdK57+fP1BCbf9rIVycfjhYCqlFhbTu9pFnZgT55W+xbAFb9y7vL0MyAxwXUXvZtQVqEwW7pURtKilbcBTEW7TAxzgro=
//version=1.0
}
