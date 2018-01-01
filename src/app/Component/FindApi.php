<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/26
 * Time: 19:54
 */

namespace App\Component;

use PhalApi\Api;
use PhalApi\Exception;
use PhalApi\Filter;

/**
 * 找人中间层Api
 * Class FindApi
 * @package App\Component
 */
class FindApi extends Api{

    public $openID;

    public function init(){
        parent::init();
    }

    public function getRules() {
        return array(
            '*' => array(
                'thirdSessionKey' => array('name' => 'thirdSessionKey', 'type' => 'string', 'require' => true, 'desc' => '第三方session'),
            ),
        );
    }

    /**
     * 默认自动调用
     */
    protected function userCheck(){

        $this->openID = $this->getOpenId($this->thirdSessionKey);
    }

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