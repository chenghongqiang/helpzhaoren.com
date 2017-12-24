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
                'openid' => array('name' => 'openid', 'type' => 'string', 'require' => true)
            ),
            'getUserProfile' => array(
                'openid' => array('name' => 'openid', 'type' => 'string', 'require' => true)
            )
        );
    }

    /**
     * 添加用户
     * @desc 进入小程序判断用户是否已存在，不存在添加新用户
     * @return int id 插入记录id，0表示已存在
     */
    public function addUser(){

        $data = array(
            'openid' => $this->openid,
            'headurl' => '12',
            'nickname' => ''
        );

        $domainUser = new DomainUSER();

        $id = $domainUser->insert($data);
        $res['id'] = $id;
        return $res['id'];
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

        $id = $domainUser->get($this->openid);
        $res['id'] = $id;
        return $res['id'];
    }
}