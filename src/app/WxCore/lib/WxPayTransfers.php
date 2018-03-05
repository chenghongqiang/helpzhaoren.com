<?php
/**
 * User: kewin.cheng
 * Date: 2018/3/5
 * Time: 14:07
 */

namespace App\WxCore\lib;

/**
 *
 * 企业付款给用户输入对象
 * @author kewin.cheng
 *
 */
class WxPayTransfers extends WxPayDataBase
{
    /**
     * 设置商户账号appid
     * @param string $value
     **/
    public function SetMchAppid($value)
    {
        $this->values['mch_appid'] = $value;
    }
    /**
     * 获取商户账号appid
     * @return 值
     **/
    public function GetMchAppid()
    {
        return $this->values['mch_appid'];
    }
    /**
     * 判断商户账号appid是否存在
     * @return true 或 false
     **/
    public function IsMchAppidSet()
    {
        return array_key_exists('mch_appid', $this->values);
    }

    /**
     * 设置微信支付分配的商户号
     * @param string $value
     **/
    public function SetMchId($value)
    {
        $this->values['mchid'] = $value;
    }
    /**
     * 获取微信支付分配的商户号的值
     * @return 值
     **/
    public function GetMchId()
    {
        return $this->values['mchid'];
    }
    /**
     * 判断微信支付分配的商户号是否存在
     * @return true 或 false
     **/
    public function IsMchIdSet()
    {
        return array_key_exists('mchid', $this->values);
    }

    /**
     * 设置微信支付分配的终端设备号，与下单一致
     * @param string $value
     **/
    public function SetDevice_info($value)
    {
        $this->values['device_info'] = $value;
    }
    /**
     * 获取微信支付分配的终端设备号，与下单一致的值
     * @return 值
     **/
    public function GetDevice_info()
    {
        return $this->values['device_info'];
    }
    /**
     * 判断微信支付分配的终端设备号，与下单一致是否存在
     * @return true 或 false
     **/
    public function IsDevice_infoSet()
    {
        return array_key_exists('device_info', $this->values);
    }


    /**
     * 设置随机字符串，不长于32位。推荐随机数生成算法
     * @param string $value
     **/
    public function SetNonce_str($value)
    {
        $this->values['nonce_str'] = $value;
    }
    /**
     * 获取随机字符串，不长于32位。推荐随机数生成算法的值
     * @return 值
     **/
    public function GetNonce_str()
    {
        return $this->values['nonce_str'];
    }
    /**
     * 判断随机字符串，不长于32位。推荐随机数生成算法是否存在
     * @return true 或 false
     **/
    public function IsNonce_strSet()
    {
        return array_key_exists('nonce_str', $this->values);
    }

    /**
     * 设置trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。
     * @param string $value
     **/
    public function SetOpenid($value)
    {
        $this->values['openid'] = $value;
    }
    /**
     * 获取trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。 的值
     * @return 值
     **/
    public function GetOpenid()
    {
        return $this->values['openid'];
    }
    /**
     * 判断trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识。下单前需要调用【网页授权获取用户信息】接口获取到用户的Openid。 是否存在
     * @return true 或 false
     **/
    public function IsOpenidSet()
    {
        return array_key_exists('openid', $this->values);
    }

    /**
     * 设置微信订单号
     * @param string $value
     **/
    public function SetPartnerTradeNo($value)
    {
        $this->values['partner_trade_no'] = $value;
    }
    /**
     * 获取微信订单号的值
     * @return 值
     **/
    public function GetPartnerTradeNo()
    {
        return $this->values['partner_trade_no'];
    }
    /**
     * 判断微信订单号是否存在
     * @return true 或 false
     **/
    public function IsPartnerTradeNoSet()
    {
        return array_key_exists('partner_trade_no', $this->values);
    }

    public function SetCheckName($value)
    {
        $this->values['check_name'] = $value;
    }

    public function GetCheckName()
    {
        return $this->values['check_name'];
    }

    public function IsCheckNameSet()
    {
        return array_key_exists('check_name', $this->values);
    }

    public function SetReUserName($value)
    {
        $this->values['re_user_name'] = $value;
    }

    public function GetReUserName()
    {
        return $this->values['re_user_name'];
    }

    public function IsReUserNameSet()
    {
        return array_key_exists('re_user_name', $this->values);
    }

    public function SetAmount($value)
    {
        $this->values['amount'] = $value;
    }

    public function GetAmount()
    {
        return $this->values['amount'];
    }

    public function IsAmountSet()
    {
        return array_key_exists('amount', $this->values);
    }

    public function SetDesc($value)
    {
        $this->values['desc'] = $value;
    }

    public function GetDesc()
    {
        return $this->values['desc'];
    }

    public function IsDescSet()
    {
        return array_key_exists('desc', $this->values);
    }

    public function SetSpbillCreateIp($value)
    {
        $this->values['spbill_create_ip'] = $value;
    }

    public function GetSpbillCreateIp()
    {
        return $this->values['spbill_create_ip'];
    }

    public function IsSpbillCreateIpSet()
    {
        return array_key_exists('spbill_create_ip', $this->values);
    }
}