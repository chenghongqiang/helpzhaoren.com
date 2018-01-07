<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/26
 * Time: 14:32
 */

namespace App\Api\Find;

use App\Domain\Common;
use App\WxCore\lib\WxPayApi;
use App\WxCore\lib\WxPayConfig;
use App\WxCore\lib\WxPayOrderQuery;
use App\WxCore\lib\WxPayUnifiedOrder;
use App\WxCore\PayNotifyCallBack;
use App\WxCore\WxPayJsApi;
use PhalApi\Api;
use App\Domain\Find\OrderRecord as DomainOrderRecord;
use PhalApi\Exception;

/**
 * 支付相关接口
 * Class Pay
 * @package App\Api\Find
 */
class Pay extends Api {

    public function getRules(){
        return array(
            'prePay' => array(
                'thirdSessionKey' => array('name' => 'thirdSessionKey', 'type' => 'string', 'require' => true, 'desc' => '第三方session'),
                'recordId' => array('name' => 'recordId', 'type' => 'int', 'require' => true , 'desc' => '找人记录id'),
                'total_fee' => array('name' => 'total_fee', 'type' => 'int', 'min' => '1', 'require' => true , 'desc' => '订单金额'),
            ),
            'payCheck' => array(
                'thirdSessionKey' => array('name' => 'thirdSessionKey', 'type' => 'string', 'require' => true, 'desc' => '第三方session'),
                'recordId' => array('name' => 'recordId', 'type' => 'int', 'require' => true , 'desc' => '找人记录id'),
            )
        );
    }

    /**
     * 预支付
     * @desc 统一下单
     * @exception 500 订单记录生成失败，请重新发起红包付款
     * @exception 400 参数传递错误
     */
    public function prePay(){
        $commonDomain = new Common();
        $openId = $commonDomain->getOpenId($this->thirdSessionKey);

        $data = array(
            'recordId' => $this->recordId,
            'total_fee' => $this->total_fee * 100, //转换为分
            'openId' => $openId,
            'out_trade_no' => WxPayConfig::MCHID.date("YmdHis").rand(4),
            'trade_type' => 'JSAPI',
            'time_start' => date("YmdHis")
        );
        $domainOrderRecord = new DomainOrderRecord();
        $insertId = $domainOrderRecord->insert($data);
        if($insertId){
            //统一下单
            $input = new WxPayUnifiedOrder();
            $input->SetBody("发起找人红包");
            $input->SetOut_trade_no($data['out_trade_no']);
            $input->SetTotal_fee($data['total_fee']);
            $input->SetTime_start($data['time_start']);
            $input->SetTime_expire(date("YmdHis", time() + 600));
            $input->SetGoods_tag("HONGBAO");
            $input->SetTrade_type($data['trade_type']);
            $input->SetOpenid($openId);
            $order = WxPayApi::unifiedOrder($input);

            $tools = new WxPayJsApi();
            $jsApiParameters = $tools->GetJsApiParameters($order);

            \PhalApi\DI()->logger->info('order:' . json_encode($order). ' jsApiParams:' . json_encode($jsApiParameters));
            return $jsApiParameters;
        }else{
            throw new Exception('订单记录生成失败，请重新发起红包付款', 500);
        }


    }

    /**
     * 下单通知
     * @ignore
     * @desc 支付成功后回调地址
     */
    public function notify(){

        $notify = new PayNotifyCallBack();
        $notify->Handle(false);
    }

    /**
     * 是否支付成功结果通知
     * @desc 根据找人记录id和用户openid查询本地订单记录，确认支付是否成功
     * @return boolean true 支付成功
     * @return boolean false 支付失败
     */
    public function payCheck(){

        $commonDomain = new Common();
        $openID = $commonDomain->getOpenId($this->thirdSessionKey);

        $domainOrderRecord = new DomainOrderRecord();
        $ret = $domainOrderRecord->checkOrderState($this->recordId, $openID);

        if(!$ret){
            //从数据库订单记录中检测订单是否成功，本地检测失败，容错考虑查询微信订单数据
            $record = $domainOrderRecord->getRecordByRecordId($this->recordId, $openID);
            if(!empty($record)){
                \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__. " 数据库订单记录丢失,recordId:" . $this->recordId);
            }else{
                $input = new WxPayOrderQuery();
                $input->SetOut_trade_no($record['out_trade_no']);
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

            return false;

        }else{
            //从订单表里查询到当前用户记录已支付，直接返回成功true
            return true;
        }

    }

    /**
     * 订单查询
     * @desc 订单查询接口
     * @param $transaction_id
     * @return string
     * @throws \App\WxCore\lib\WxPayException
     */
    public function queryOrder($transaction_id){
        $input = new WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = WxPayApi::orderQuery($input);

        \PhalApi\DI()->logger->info("queryOrder:" . json_encode($result));
        return json_encode($result);

    }



}