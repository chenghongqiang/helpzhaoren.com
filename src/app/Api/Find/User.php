<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/24
 * Time: 17:43
 */


namespace App\Api\Find;

use App\Component\FindApi;
use PhalApi\Api;
use App\Domain\Find\USER as DomainUSER;

/**
 * 用户相关接口
 * Class User
 * @package App\Api\Find
 */
class User extends FindApi {

    public function getRules(){
        return array(
            'insertUserInfo' => array(
                'thirdSessionKey' => array('name' => 'thirdSessionKey', 'type' => 'string', 'require' => true, 'desc' => '第三方session'),
                'encryptedData' => array('name' => 'encryptedData', 'type' => 'string', 'require' => true, 'desc' => '目标密文'),
                'iv' => array('name' => 'iv', 'type' => 'string', 'require' => true, 'desc' => '初始向量')
            ),
            'getUserProfile' => array(
                'thirdSessionKey' => array('name' => 'thirdSessionKey', 'type' => 'string', 'require' => true, 'desc' => '第三方session'),
            ),
            'userLogin' => array(
                'code' => array('name' => 'code', 'type' => 'string', 'require' => true, 'desc' => '登录凭证code'),
            )
        );
    }

    /**
     * 用户登录
     * @desc 小程序登录态维护，服务器端生成第三方sessionKey，返回第三方sessionKey给客户端
     * @return string thirdSessionKey 第三方sessionKey
     */
    public function userLogin(){
        $domainUser = new DomainUSER();
        $thirdSessionKey = $domainUser->userLogin($this->code);

        return array('thirdSessionKey' => $thirdSessionKey);
    }

    /**
     * 添加用户
     * @desc 进入小程序判断用户是否已存在，不存在添加新用户
     * @return int id 插入记录id，0表示已存在 -1表示插入失败
     */
    public function insertUserInfo(){
        self::getOpenId($this->thirdSessionKey);
        $domainUser = new DomainUSER();
        $id = $domainUser->insertUserInfo($this->thirdSessionKey, $this->encryptedData, $this->iv);

        return $id;

    }

    /**
     * 获取用户信息
     * @desc 获取用户信息
     * @return string openId 用户openid
     * @return string avatarUrl 用户微信图像
     * @return string nickName 用户微信昵称
     * @return int wallet 用户钱包
     */
    public function getUserProfile(){

        $openId = self::getOpenId($this->thirdSessionKey);
        $domainUser = new DomainUSER();
        $userInfo = $domainUser->getUserInfoFromDB($openId);

        return $userInfo;
    }

}