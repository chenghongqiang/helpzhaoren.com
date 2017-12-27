<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/25
 * Time: 18:08
 */

namespace App\WxCore;

use App\WxCore\lib\WxPayConfig;
use App\WxCore\lib\WxPayException;
use PhalApi\CUrl;
use PhalApi\Exception\InternalServerErrorException;
use PhalApi\Logger;


class WXAuth {

    //登录凭证code获取session_key和openid
    const jscode2sessionURL = 'https://api.weixin.qq.com/sns/jscode2session?appid=%s&secret=%s&js_code=%s&grant_type=authorization_code';


    /**
     * 使用登录凭证code获取session_key和openid
     * @param $code
     * @return  openid	用户唯一标识
                session_key	会话密钥
                unionid	用户在开放平台的唯一标识符。本字段在满足一定条件的情况下才返回
     *
     * //正常返回的JSON数据包
        {
            "openid": "OPENID",
            "session_key": "SESSIONKEY",
            "unionid": "UNIONID"
        }
        //错误时返回JSON数据包(示例为Code无效)
        {
            "errcode": 40029,
            "errmsg": "invalid code"
        }
     */
    public function jscode2session($code){

        $url = sprintf(self::jscode2sessionURL, WxPayConfig::APPID, WxPayConfig::APPSECRET, $code);
        try{
            $curl = new CUrl(2);
            $rs = $curl->get($url, 1000);
            $data = json_decode($rs, true);
            if(!empty($data['errcode'])){
                //记录错误
                \PhalApi\DI()->logger->error(json_encode($rs));
                throw new WxPayException("jscode2session exception:" . $rs);
            }

            return $data;

        }catch (InternalServerErrorException $ex){
            throw new WxPayException("curl exception: :" . $ex, 500);
        }

    }

    public function getUserInfo($sessionKey, $encryptedData, $iv){

        $pc = new WXBizDataCrypt(WxPayConfig::APPID, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );

        if ($errCode == 0) {
            return $data;
        } else {
            print($errCode . "\n");
        }
    }

}
