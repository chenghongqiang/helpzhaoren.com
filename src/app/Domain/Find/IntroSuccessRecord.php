<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/28
 * Time: 16:23
 */

namespace App\Domain\Find;

use App\Model\Find\IntroSuccessRecord as ModelIntroSuccessRecord;
use App\WxCore\WXAuth;
use App\WxCore\WxModuleMsgApi;
use PhalApi\Exception;

class IntroSuccessRecord {

    public function insert($data){
        $model = new ModelIntroSuccessRecord;
        return $model->insert($data);
    }

    public function get($id){
        $model = new ModelIntroSuccessRecord();
        return $model->get($id);
    }

    public function getRecordByRecordId($recordId){
        $model = new ModelIntroSuccessRecord();
        return $model->getRecordByRecordId($recordId);
    }

    public function getRecordIdByOpenId($openId){
        $model = new ModelIntroSuccessRecord();
        return $model->getRecordIdByOpenId($openId);
    }

    public function sendModuleMsg($data){
        $accessToken = WXAuth::getAccessToken();

        //{"errcode": 0, "errmsg": "ok"}
        $data = WxModuleMsgApi::sendModuleMsg($accessToken, $data);
        if($data['errcode']==0){
            //发送模板消息成功
            return true;
        }else{
            throw new Exception($data['errmsg'], $data['errcode']);
        }

    }

    public function getWXQrcode($data){
        $accessToken = WXAuth::getAccessToken();
        $data = WXAuth::getWxQrcode($accessToken, $data);
        return $data;
        if($data['errcode']==0){

            return $data;
        }else{
            throw new Exception($data['errmsg'], $data['errcode']);
        }


    }
}