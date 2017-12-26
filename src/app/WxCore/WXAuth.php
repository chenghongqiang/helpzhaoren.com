<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/25
 * Time: 18:08
 */

namespace App\WxCore;

use App\WxCore\lib\WxPayConfig;
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
     */
    private function jscode2session($code){

        $url = sprintf(self::jscode2sessionURL, WxPayConfig::APPID, WxPayConfig::APPSECRET, $code);
        try{
            $curl = new CUrl(2);

            $rs = $curl->get($url, 3000);
            if($rs == 200){

            }
            \PhalApi\DI()->logger->info(json_encode($rs));
            return $rs;

        }catch (InternalServerErrorException $ex){
            \PhalApi\DI()->logger->error('curl exception: ' . $ex);
        }

    }

    public static function getUserInfo($code, $encryptedData, $iv){
        $jscodeArr = self::jscode2session($code);

        $pc = new WXBizDataCrypt(WxPayConfig::APPID, $jscodeArr['session_key']);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );

        if ($errCode == 0) {
            return $data;
        } else {
            print($errCode . "\n");
        }
    }

}
