<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/24
 * Time: 17:35
 */

namespace App\Domain\Find;

use App\Common\Utils\Time;
use App\Model\Find\USER as ModelUSER;
use App\WxCore\WXAuth;
use PhalApi\Exception\InternalServerErrorException;


class USER {

    /**
     * 用户登录态维护
     * @param $code
     * @return null|string
     * @throws \App\WxCore\lib\WxPayException
     */
    public function userLogin($code){

        try{
            //根据code获取openid和session_key
            $wxAuth = new WXAuth();
            $sessionData = $wxAuth->jscode2session($code);
            if(is_array($sessionData)){
                //以3rd_session为key，session_key+openid为value写入session存储
                $sessionKey = $this->thridSession(64);
                //sessionKey有效期1天
                \PhalApi\DI()->redis->set($sessionKey, $sessionData['session_key'].'_' .$sessionData['openid'], Time::DAY);

                return $sessionKey;
            }
        }catch (InternalServerErrorException $ex){
            return null;
        }

        return null;
    }

    /**
     * 解密数据获取用户信息
     * @param $encryptedData
     * @param $iv
     * @return mixed
     * {
            "openId": "OPENID",
            "nickName": "NICKNAME",
            "gender": GENDER,
            "city": "CITY",
            "province": "PROVINCE",
            "country": "COUNTRY",
            "avatarUrl": "AVATARURL",
            "unionId": "UNIONID",
            "watermark":
            {
            "appid":"APPID",
            "timestamp":TIMESTAMP
            }
        }
     */
    public function getUserInfo($encryptedData, $iv){
        $wxAuth = new WXAuth();
        $sessionValue = \PhalApi\DI()->redis->get($this->thirdSessionKey);
        $list = explode('_', $sessionValue);
        $sessionKey = $list[0];
        $userInfo = $wxAuth->getUserInfo($sessionKey, $encryptedData, $iv);

        return $userInfo;
    }

    public function insertUserInfo($encryptedData, $iv){

        $userInfo = $this->getUserInfo($encryptedData, $iv);
        if(empty($userInfo)){
            return -1;
        }
        $data = array(
            'openid' => $userInfo['openId'],
            'nickName' => $userInfo['nickName'],
            'avatarUrl' => $userInfo['avatarUrl'],
            'unionId' => $userInfo['unionId'],
        );

        $model = new ModelUSER();
        return $model->insert($data);

    }

    /**
     * 读取/dev/urandom获取随机数机制
     * @param $len
     * @return string
     */
    private function thridSession($len) {

        $fp = @fopen('/dev/urandom','rb');
        $result = '';

        if($fp !== FALSE) {
            $result .= @fread($fp, $len);
            @fclose($fp);
        } else {
            trigger_error('Can not open /dev/urandom.');
        }

        //convert from binary to string
        $result = base64_encode($result);
        //remove none url chars
        $result = strtr($result, '+/', '-_');
        return substr($result, 0, $len);

    }

    public function get($id){
        $model = new ModelUSER();
        return $model->get($id);
    }

    public function getUserByOpenid($openid){
        $model = new ModelUSER();
        return $model->getUserByOpenid($openid);

    }

}