<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/26
 * Time: 14:32
 */

namespace App\Api\Find;

use App\Component\FindApi;
use App\WxCore\lib\WxPayApi;
use App\WxCore\lib\WxPayConfig;
use App\WxCore\lib\WxPayOrderQuery;
use App\WxCore\lib\WxPayUnifiedOrder;
use App\WxCore\WxPayJsApi;

/**
 * 支付相关接口
 * Class Pay
 * @package App\Api\Find
 */
class Pay extends FindApi{

    public function getRules(){
        return array(
            'prePay' => array(
                'thirdSessionKey' => array('name' => 'thirdSessionKey', 'type' => 'string', 'require' => true, 'desc' => '第三方session'),
                'total_fee' => array('name' => 'total_fee', 'type' => 'int', 'require' => true , 'desc' => '订单金额'),
            )
        );
    }

    /**
     * 预支付
     * @desc 统一下单
     */
    public function prePay(){
        $openId = self::getOpenId($this->thirdSessionKey);

        //统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody("发起找人红包");
        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee($this->total_fee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("HONGBAO");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        $order = WxPayApi::unifiedOrder($input);
        \PhalApi\DI()->logger->info(json_encode($order));
        $tools = new WxPayJsApi();
        $jsApiParameters = $tools->GetJsApiParameters($order);
        return $jsApiParameters;
    }

    /**
     * 下单通知
     * @desc 支付成功后回调地址
     */
    public function notify(){
        $allParams = \PhalApi\DI()->request->getAll();
        \PhalApi\DI()->logger->info(json_encode($allParams));

        return $allParams;
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