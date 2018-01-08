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
use App\Domain\Find\OrderRecord as DomainOrderRecord;

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

        \PhalApi\DI()->logger->info(__CLASS__.__FUNCTION__. " 通知回调,transaction_id:" . $data["transaction_id"]);
        if(!array_key_exists("transaction_id", $data)){
            $msg = "缺少订单号";
            return false;
        }
        //查询订单是否存在
        if(!$this->QueryOrder($data["transaction_id"], $data["out_trade_no"])){
            $msg = "订单查询失败";
            return false;
        }

        $domainOrderRecord = new DomainOrderRecord();
        $record = $domainOrderRecord->getRecordByTradeNo($data["out_trade_no"], $data["openid"]);
        \PhalApi\DI()->logger->info(__CLASS__.__FUNCTION__. " 获取订单数据,out_trade_no:" . json_encode($record));
        if(empty($record)){
            \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__. " 数据库订单记录丢失,out_trade_no:" . $data["out_trade_no"]);
            return false;
        }

        /*
        * 首先判断，订单是否已经更新为ok，因为微信会总共发送8次回调确认
        * 其次，订单已经为ok的，直接返回SUCCESS
        * 最后，订单没有为ok的，更新状态为ok，返回SUCCESS
        */
        if($record['state']==2){
            return true;
        }else{
            $flag = $domainOrderRecord->upate($record['id'], array('state' => 2));
            if($flag){
                return true;
            }else{
                \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__. " 更新数据库订单状态失败,out_trade_no:" . $data["out_trade_no"]);
            }
        }

        return true;
    }
}