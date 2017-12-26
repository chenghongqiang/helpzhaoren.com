<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/25
 * Time: 18:08
 */

namespace App\WxCore;

use PhalApi\CUrl;
use PhalApi\Exception\InternalServerErrorException;
use PhalApi\Logger;


class WXAuth {

    //登录凭证code获取session_key和openid
    const jscode2sessionURL = 'https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code';

    private static $AppID = 'wxd035cd5461feb811';
    private static $AppSecret = '336a27a52c1f55e7abed35a8de4e3cdd';

    /**
     * 使用登录凭证code获取session_key和openid
     * @param $code
     * @return  openid	用户唯一标识
                session_key	会话密钥
                unionid	用户在开放平台的唯一标识符。本字段在满足一定条件的情况下才返回
     */
    private function jscode2session($code){

        try{
            $curl = new CUrl(2);
            $url = sprintf(self::jscode2sessionURL, static::$AppID, static::$AppSecret, $code);

            $rs = $curl->get($url, 3000);
            if($rs == 200){

            }
            \PhalApi\DI()->logger->info(json_encode($rs));
            return $rs;
        }catch (InternalServerErrorException $ex){
            \PhalApi\DI()->logger->error('curl exception: ' . self::jscode2sessionURL);
        }

    }

    public static function getUserInfo($code, $encryptedData, $iv){
        $jscodeArr = self::jscode2session($code);

        $encryptedData="CiyLU1Aw2KjvrjMdj8YKliAjtP4gsMZM
                QmRzooG2xrDcvSnxIMXFufNstNGTyaGS
                9uT5geRa0W4oTOb1WT7fJlAC+oNPdbB+
                3hVbJSRgv+4lGOETKUQz6OYStslQ142d
                NCuabNPGBzlooOmB231qMM85d2/fV6Ch
                evvXvQP8Hkue1poOFtnEtpyxVLW1zAo6
                /1Xx1COxFvrc2d7UL/lmHInNlxuacJXw
                u0fjpXfz/YqYzBIBzD6WUfTIF9GRHpOn
                /Hz7saL8xz+W//FRAUid1OksQaQx4CMs
                8LOddcQhULW4ucetDf96JcR3g0gfRK4P
                C7E/r7Z6xNrXd2UIeorGj5Ef7b1pJAYB
                6Y5anaHqZ9J6nKEBvB4DnNLIVWSgARns
                /8wR2SiRS7MNACwTyrGvt9ts8p12PKFd
                lqYTopNHR1Vf7XjfhQlVsAJdNiKdYmYV
                oKlaRv85IfVunYzO0IKXsyl7JCUjCpoG
                20f0a04COwfneQAGGwd5oa+T8yO5hzuy
                Db/XcxxmK01EpqOyuxINew==";

        $iv = 'r7BXXKkLb8qrSNn05n0qiA==';

        //
        $pc = new WXBizDataCrypt(static::$AppID, $jscodeArr['session_key']);
        $errCode = $pc->decryptData($encryptedData, $iv, $data );

        if ($errCode == 0) {
            return $data;
        } else {
            print($errCode . "\n");
        }
    }

}
