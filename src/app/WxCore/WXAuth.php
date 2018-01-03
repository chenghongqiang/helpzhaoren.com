<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/25
 * Time: 18:08
 */

namespace App\WxCore;

use App\WxCore\lib\WxPayApi;
use App\WxCore\lib\WxPayConfig;
use App\WxCore\lib\WxPayException;
use PhalApi\CUrl;
use PhalApi\Exception;
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
            $curl = new CUrl();
            $rs = $curl->get($url, 6000);
            $data = json_decode($rs, true);
            if(!empty($data['errcode'])){
                //记录错误
                \PhalApi\DI()->logger->error(json_encode($rs));
                throw new Exception("jscode2session exception:" . $rs, $data['errcode']);
            }

            return $data;

        }catch (InternalServerErrorException $ex){
            throw new Exception("curl exception: :" . $ex, 500);
        }

    }

    /**
     * 解密数据获取用户信息
     * @param $sessionKey
     * @param $encryptedData
     * @param $iv
     * @return mixed
     * @throws Exception
     */
    public function getUserInfo($sessionKey, $encryptedData, $iv){

        $pc = new WxBizDataCrypt(WxPayConfig::APPID, $sessionKey);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );

        if ($errCode == 0) {
            return $data;
        } else {
            \PhalApi\DI()->logger->error(__CLASS__.__METHOD__ . ' errCode:' . $errCode);
            throw new Exception("decryptData failed", $errCode);
        }
    }

    /**
     * 获取小程序access_token
     * 正常情况 返回{"access_token": "ACCESS_TOKEN", "expires_in": 7200}
     * 错误情况 返回{"errcode": 40013, "errmsg": "invalid appid"}
     */
    public static function getAccessToken(){
        $accessToken = \PhalApi\DI()->redis->get("accessToken");

        if(empty($accessToken)){
            //获取access_token
            $getAccessTokenURL = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
            $url = sprintf($getAccessTokenURL, WxPayConfig::APPID, WxPayConfig::APPSECRET);

            $curl = new CUrl();
            $rs = $curl->get($url, 6000);
            $data = json_decode($rs, true);
            if(!empty($data['errcode'])){
                //记录错误
                \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__ . json_encode($rs));
                throw new Exception("getAccessToken exception:" . $rs, $data['errcode']);
            }

            $accessToken = $data['access_token'];
            //存储acessToken 7000s过期
            \PhalApi\DI()->redis->set("accessToken", $accessToken, intval($data['expires_in'])-200);
        }

        return $accessToken;

    }

}
