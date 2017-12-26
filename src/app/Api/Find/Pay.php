<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/26
 * Time: 14:32
 */

namespace App\Api\Find;

use App\WxCore\JsApiPay;
use App\WxCore\WXAuth;
use PhalApi\Api;

/**
 * 支付相关接口
 * Class Pay
 * @package App\Api\Find
 */
class Pay extends Api{

    public function getRules(){
        return array(
            'prePay' => array(
                'openID' => array('name' => 'openID', 'type' => 'string', 'require' => true , 'desc' => '用户openID'),
                'total_fee' => array('name' => 'total_fee', 'type' => 'int', 'require' => true , 'desc' => '订单金额'),
            )
        );
    }

    /**
     * 预支付
     */
    public function prePay(){
        $auth = new WXAuth();

        //获取用户openid
        $tools = new JsApiPay();
        $openId = $tools->GetOpenid();

        //②、统一下单
        $input = new WxPayUnifiedOrder();
        $input->SetBody("test");
        $input->SetAttach("test");
        $input->SetOut_trade_no(WxPayConfig::MCHID.date("YmdHis"));
        $input->SetTotal_fee($this->total_fee);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire(date("YmdHis", time() + 600));
        $input->SetGoods_tag("test");
        $input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($this->openID);
        $order = WxPayApi::unifiedOrder($input);

        printf_info($order);
        $jsApiParameters = $tools->GetJsApiParameters($order);
        printf_info($jsApiParameters);
    }

}