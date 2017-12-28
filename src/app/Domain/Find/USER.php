<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/24
 * Time: 17:35
 */

namespace App\Domain\Find;

use App\Common\Utils\Time;
use App\Common\Utils\Tool;
use App\Model\Find\USER as ModelUSER;
use App\WxCore\WXAuth;
use PhalApi\Exception;
use PhalApi\Exception\InternalServerErrorException;


class USER {

    /**
     * 用户登录态维护
     * @param $code
     * @return array
     * @throws \App\WxCore\lib\WxPayException
     */
    public function userLogin($code){

        $sessionKey = '';
        try{
            //根据code获取openid和session_key
            $wxAuth = new WXAuth();
            $sessionData = $wxAuth->jscode2session($code);
            if(is_array($sessionData)){
                //以3rd_session为key，session_key+openid为value写入session存储
                $sessionKey = $this->thridSession(64);
                //sessionKey有效期1天
                \PhalApi\DI()->redis->set($sessionKey, $sessionData['session_key'].'_' .$sessionData['openid'], Time::DAY);

                \PhalApi\DI()->logger->info(__CLASS__.__FUNCTION__  . ' sessionKey:' . $sessionKey);

                /*$data['openid'] = $sessionData['openid'];
                $data['thirdSessionKey'] = $sessionKey;*/
                return $sessionKey;
            }
        }catch (InternalServerErrorException $ex){
            \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__  . $ex);
            return $sessionKey;
        }

        return $sessionKey;
    }

    public function insertUserInfo($thirdSessionKey, $encryptedData, $iv){

        $userInfo = $this->getUserInfo($thirdSessionKey, $encryptedData, $iv);
        $userInfoDB = $this->getUserInfoFromDB($userInfo->openId);

        if(!empty($userInfoDB)){
            //数据库已存在用户信息
            return 0;
        }
        $data = array(
            'openId' => $userInfo->openId,
            'nickName' => $userInfo->nickName,
            'avatarUrl' => $userInfo->avatarUrl,
        );

        try{
            $model = new ModelUSER();
            return $model->insert($data);
        }catch (\mysqli_sql_exception $e){
            throw new Exception($e, 500);
        }

    }

    /**
     * 解密数据获取用户信息
     * @param $encryptedData
     * @param $iv
     * @return mixed
     * 解密后用户信息如下：
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
    public function getUserInfo($thirdSessionKey, $encryptedData, $iv){
        $wxAuth = new WXAuth();
        $sessionValue = \PhalApi\DI()->redis->get($thirdSessionKey);
        $list = explode('_', $sessionValue);
        $sessionKey = $list[0];

        \PhalApi\DI()->logger->info(__CLASS__.__METHOD__ . '->thirdSessionKey：'.$thirdSessionKey.
            ' encryptedData:'.$encryptedData.' iv:'.$iv);
        $data = $wxAuth->getUserInfo($sessionKey, $encryptedData, $iv);
        $userInfo = json_decode($data);

        return $userInfo;
    }

    public function getUserInfoFromDB($openId){

        $model = new ModelUSER();
        $userInfoDb = $model->getUserByOpenid($openId);

        return $userInfoDb;
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
            //打开文件失败 仍然手动生成随机数并记录日志
            \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__  . 'Can not open /dev/urandom');
            return Tool::getRandom(64);
        }
        //convert from binary to string
        $result = base64_encode($result);
        //remove none url chars
        $result = strtr($result, '+/', '-_');
        return substr($result, 0, $len);

    }

    /**
     * 根据id获取用户信息
     * @param $id
     * @return array
     */
    public function get($id){
        $model = new ModelUSER();
        return $model->get($id);
    }

    /**
     * 根据openid获取用户信息
     * @param $openid
     * @return mixed
     */
    public function getUserByOpenid($openid){
        $model = new ModelUSER();
        return $model->getUserByOpenid($openid);

    }

}