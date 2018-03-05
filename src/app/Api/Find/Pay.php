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
use App\WxCore\lib\WxPayRefund;
use App\WxCore\lib\WxPayTransfers;
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
                'total_fee' => array('name' => 'total_fee', 'type' => 'int', 'min' => '1', 'require' => true , 'desc' => '订单金额'),
            ),
            'payCheck' => array(
                'thirdSessionKey' => array('name' => 'thirdSessionKey', 'type' => 'string', 'require' => true, 'desc' => '第三方session'),
                'out_trade_no' => array('name' => 'out_trade_no', 'type' => 'string', 'require' => true , 'desc' => '商户订单号，预支付接口返回'),
            ),
            'refund' => array(
                'thirdSessionKey' => array('name' => 'thirdSessionKey', 'type' => 'string', 'require' => true, 'desc' => '第三方session'),
                'total_fee' => array('name' => 'total_fee', 'type' => 'int', 'min' => '1', 'require' => true , 'desc' => '订单金额'),
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
            'total_fee' => $this->total_fee * 100, //转换为分
            'openId' => $openId,
            'out_trade_no' => WxPayConfig::MCHID.date("YmdHis").rand(1000,9999),
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
            $input->SetNotify_url("https://service.helpzhaoren.com/app/find/pay/notify/1");
            $order = WxPayApi::unifiedOrder($input);

            $tools = new WxPayJsApi();
            $jsApiParameters = $tools->GetJsApiParameters($order);
            $jsApiParameters['out_trade_no'] = $data['out_trade_no'];

            \PhalApi\DI()->logger->info('order:' . json_encode($order). ' jsApiParams:' . json_encode($jsApiParameters));
            return $jsApiParameters;
        }else{
            throw new Exception('订单记录生成失败，请重新发起红包付款', 500);
        }


    }

    /**
     * 下单通知
     * @desc 支付成功后回调地址
     */
    public function notify(){

        \PhalApi\DI()->logger->info(__CLASS__.__FUNCTION__);
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
        $ret = $domainOrderRecord->checkOrderState($this->out_trade_no, $openID);

        if(!$ret){
            //从数据库订单记录中检测订单是否成功，本地检测失败，容错考虑查询微信订单数据
            $record = $domainOrderRecord->getRecordByTradeNo($this->out_trade_no, $openID);
            if(empty($record)){
                \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__. " 数据库订单记录丢失,out_trade_no:" . $this->out_trade_no);
            }else{
                $input = new WxPayOrderQuery();
                $input->SetOut_trade_no($record['out_trade_no']);
                $result = WxPayApi::orderQuery($input);

                \PhalApi\DI()->logger->info(__CLASS__.__FUNCTION__. " 查询订单状态,out_trade_no:" . $record['out_trade_no'].
                    " queryOrder:" . json_encode($result));
                if(array_key_exists("return_code", $result)
                    && array_key_exists("result_code", $result)
                    && $result["return_code"] == "SUCCESS"
                    && $result["result_code"] == "SUCCESS") {
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

    /**
     * 申请退款
     * @desc 申请退款接口
     */
    public function refund(){
        $commonDomain = new Common();
        $openId = $commonDomain->getOpenId($this->thirdSessionKey);

        $data = array(
            'total_fee' => $this->total_fee * 100, //转换为分
            'refund_fee' => $this->total_fee * 100, //转换为分
            'openId' => $openId,
            'out_trade_no' => WxPayConfig::MCHID.date("YmdHis").rand(1000,9999),
            'out_refund_no' => WxPayConfig::MCHID.date("YmdHis").rand(1000,9999),
            'create_time' => date("YmdHis")
        );
        $domainOrderRecord = new DomainOrderRecord();
        $insertId = $domainOrderRecord->insert($data);
        if($insertId){
            //退款操作
            $input = new WxPayRefund();
            $input->SetOut_trade_no($data['out_trade_no']); //商户订单号
            $input->SetTotal_fee($data['total_fee']); //订单金额
            $input->SetOut_refund_no($data['out_refund_no']); //商户退款单号
            $input->SetRefund_fee($data['refund_fee']);//退款金额

            $refund = WxPayApi::refund($input);

            $tools = new WxPayJsApi();
            $jsApiParameters = $tools->GetJsApiParameters($refund);
            $jsApiParameters['out_trade_no'] = $data['out_trade_no'];

            \PhalApi\DI()->logger->info('refund:' . json_encode($refund). ' jsApiParams:' . json_encode($jsApiParameters));
            return $jsApiParameters;
        }else{
            throw new Exception('申请退款失败', 500);
        }
    }

    /**
     * 企业付款给用户
     * @desc 企业付款给用户
     * @param $transaction_id
     * @return string
     * @throws \App\WxCore\lib\WxPayException
     */
    public function transfers(){
        $commonDomain = new Common();
        $openId = $commonDomain->getOpenId($this->thirdSessionKey);

        $data = array(
            'amount' => $this->total_fee * 100, //转换为分
            'openid' => $openId,
            'partner_trade_no' => WxPayConfig::MCHID.date("YmdHis").rand(1000,9999),
            'create_time' => date("YmdHis")
        );
        $domainOrderRecord = new DomainOrderRecord();
        $insertId = $domainOrderRecord->insert($data);
        if($insertId){
            //退款操作
            $input = new WxPayTransfers();
            $input->SetPartnerTradeNo($data['out_trade_no']); //商户订单号
            $input->SetOpenid($data['openid']);
            $input->SetCheckName("NO_CHECK");
            $input->SetAmount($data['amount']);
            $input->SetDesc("钱包体现");

            $refund = WxPayApi::transfers($input);

            $tools = new WxPayJsApi();
            $jsApiParameters = $tools->GetJsApiParameters($refund);

            \PhalApi\DI()->logger->info('transfers:' . json_encode($refund). ' jsApiParams:' . json_encode($jsApiParameters));
            return $jsApiParameters;
        }else{
            throw new Exception('付款给用户失败', 500);
        }
    }

}