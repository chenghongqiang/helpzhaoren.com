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

    //发送模板消息给引荐人和被引荐人
    public function sendModuleMsgToIntro($openId, $record, $wxNickName){

        //收益到账通知
        $dataParam = array(
            'touser' => $openId,
            'template_id' => '2TyJ-pzj0k5QaYE3mlaMOC4CR-pofRwFlhJr0AEOvsE',
            'page' => 'pages/index',
            'form_id' => $this->formId,
            'data' => array(
                'keyword1' => array('value' => $record['money']),
                'keyword2' => array('value' => $wxNickName),
                'keyword3' => array('value' => "想找:" . $record['intro']),
            ),
            'emphasis_keyword' => ''

        );
        $this->sendModuleMsg($dataParam);
    }
}