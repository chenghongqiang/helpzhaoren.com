<?php
/**
 * User: kewin.cheng
 * Date: 2018/1/4
 * Time: 14:07
 */

namespace App\WxCore;

use App\WxCore\lib\WxPayApi;
use App\WxCore\lib\WxPayNotify;
use App\WxCore\lib\WxPayOrderQuery;

class PayNotifyCallBack extends WxPayNotify{

    //订单查询
    public function QueryOrder($transaction_id){
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($input);

        if(array_key_exists("return_code", $result)
            && array_key_exists("result_code", $result)
            && $result["return_code"] == "SUCCESS"
            && $result["result_code"] == "SUCCESS")
        {
            return true;
        }
        return false;
    }

    //通知处理
    public function NotifyProcess($data, &$msg){

        if(!array_key_exists("transaction_id", $data)){
            $msg = "缺少订单号";
            return false;
        }
        //查询订单是否存在
        if(!$this->QueryOrder($data["transaction_id"])){
            $msg = "订单不存在";
            return false;
        }
        return true;
    }
}