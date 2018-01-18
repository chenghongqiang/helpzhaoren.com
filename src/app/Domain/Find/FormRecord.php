<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/28
 * Time: 16:23
 */

namespace App\Domain\Find;

use App\Model\Find\FormRecord as ModelFormRecord;

class FormRecord {

    public function insert($data){
        $model = new ModelFormRecord;
        return $model->insert($data);
    }

    public function get($id){
        $model = new ModelFormRecord();
        return $model->get($id);
    }

    public function update($id, $newData) {
        $model = new ModelFormRecord();
        return $model->update($id, $newData);
    }

    public function getFormRecord($state=1){
        $model = new ModelFormRecord();
        return $model->getFormRecord($state);
    }

    public function getFormIdByOpenId($state=1, $openId){
        $model = new ModelFormRecord();
        return $model->getFormIdByOpenId($state, $openId);
    }

    public function getFormId($openId){
        $model = new ModelFormRecord();
        $record = $model->getFormIdByOpenId(1, $openId);

        if(empty($record)){
            \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__, "未找到供发送模板消息的formId, openId:" . $openId);
        }

        $model->update($record['id'], array('state' => -1));
        return $record['formId'];
    }
}