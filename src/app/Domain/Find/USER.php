<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/24
 * Time: 17:35
 */

namespace App\Domain\Find;

use App\Model\Find\USER as ModelUSER;
use App\WxCore\WXAuth;


class USER {

    public function insert($params){

        //根据code获取session_key，对加密数据解密
        $userInfo = WXAuth::getUserInfo($params['code'], $params['encryptedData'], $params['iv']);
        $data = array(
            'openid' => $userInfo['openId'],
            'code' => $this->code,
            'encryptedData' => $this->encryptedData,
            'iv' => $this->iv
        );

        $model = new ModelUSER();
        return $model->insert($data);
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