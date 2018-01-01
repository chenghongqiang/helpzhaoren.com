<?php
/**
 * Created by PhpStorm.
 * User: kewin.cheng
 * Date: 18/1/1
 * Time: PM8:44
 */

namespace App\Domain;

use PhalApi\Exception;

class Common{

    public function getOpenId($thirdSessionKey){
        //根据接口请求thirdSessionKey,从redis中取出thirdSessionKey = $session_key_$openid
        $sessionValue = \PhalApi\DI()->redis->get($thirdSessionKey);
        if(empty($sessionValue)){
            //缓存过期或者不存在
            throw new Exception('session已过期', -10000);
        }

        $list = explode('%%', $sessionValue);
        return $list[1];
    }
}