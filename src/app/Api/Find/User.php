<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/24
 * Time: 17:43
 */


namespace App\Api\Find;

use PhalApi\Api;
use App\Domain\Find\USER as DomainUSER;

/**
 * 用户相关接口
 * Class User
 * @package App\Api\Find
 */
class User extends Api{

    public function getRules(){
        return array(
            'addUser' => array(
                'openid' => array('name' => 'openid', 'type' => 'string', 'require' => true , 'desc' => '用户openid'),
                'code' => array('name' => 'code', 'type' => 'string', 'require' => true, 'desc' => '登录凭证code'),
                'encryptedData' => array('name' => 'encryptedData', 'type' => 'string', 'require' => true, 'desc' => '目标密文'),
                'iv' => array('name' => 'iv', 'type' => 'string', 'require' => true, 'desc' => '初始向量')
            ),
            'getUserProfile' => array(
                'openid' => array('name' => 'openid', 'type' => 'string', 'require' => true , 'desc' => '用户openid')
            )
        );
    }

    /**
     * 添加用户
     * @desc 进入小程序判断用户是否已存在，不存在添加新用户
     * @return int id 插入记录id，0表示已存在
     */
    public function addUser(){

        $params = array(
            'openid' => $this->openid,
            'code' => $this->code,
            'encryptedData' => $this->encryptedData,
            'iv' => $this->iv
        );

        $domainUser = new DomainUSER();
        $user = $domainUser->getUserByOpenid($this->openid);
        if(empty($user)){
            $id = $domainUser->insert($params);
            $res['id'] = $id;
            return $res['id'];
        }

        return 0;
    }

    /**
     * 获取用户信息包括钱包
     * @desc 根据用户openid获取用户信息
     * @return string openid 用户openid
     * @return string headurl 用户微信图像
     * @return string nickname 用户微信昵称
     * @return int wallet 用户钱包
     */
    public function getUserProfile(){
        $domainUser = new DomainUSER();

        $user = $domainUser->getUserByOpenid($this->openid);
        return $user;
    }


}