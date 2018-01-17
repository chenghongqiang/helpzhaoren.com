<?php
/**
 * Created by PhpStorm.
 * User: kewin.cheng
 * Date: 17/12/30
 * Time: PM5:47
 */

namespace App\Api\Find;

use App\Component\FindApi;
use App\Domain\Find\RECORD as DomainRECORD;
use App\Domain\Find\IntroRecord as DomainIntroRecord;
use App\Domain\Find\WalletIncomeRecord as DomainWalletIncomeRecord;
use App\Domain\Find\IntroSuccessRecord as DomainIntroSuccessRecord;
use App\Domain\Find\USER as DomainUSER;
use PhalApi\Exception;

/**
 * 被引荐人提交数据接口
 * Class IntroSuccessRecord
 * @package App\Api\Find
 */
class IntroSuccessRecord extends FindApi{

    public function getRules(){

        return array_merge(parent::getRules(),array(

            'intro' => array(
                'record_id' => array('name' => 'record_id', 'type' => 'int', 'require' => true , 'desc' => '找人记录id'),
                'intro_user_id' => array('name' => 'intro_user_id', 'type' => 'int', 'require' => true , 'desc' => '引荐人id'),
                'wx_introducered_code' => array('name' => 'wx_introducered_code', 'type' => 'string', 'require' => true, 'desc' => '被引荐人微信号'),
            ),
            'sendModuleMsg' => array(
                'recordId' => array('name' => 'recordId', 'type' => 'int', 'require' => true , 'desc' => '找人记录id'),
                'formId' => array('name' => 'formId', 'type' => 'string', 'require' => true , 'desc' => 'formId'),
            ),
            'getWxQrcode' => array(
                'page' => array('name' => 'page', 'type' => 'string', 'require' => true , 'desc' => '已经发布的小程序页面'),
                'width' => array('name' => 'width', 'type' => 'string', 'desc' => '二维码宽度', 'default' => '430'),
            )
        ));
    }

    /**
     * 被引荐人提交数据
     * @desc 被引荐人填入微信号，引荐找人成功
     * @return int intro_success_id 引荐成功记录id
     */
    public function intro(){

        //获取引荐人记录
        $domainIntroRecord = new DomainIntroRecord();
        $introRecord = $domainIntroRecord->get($this->intro_user_id);

        $data = array(
            'recordId' => $this->record_id,
            'introducererOpenId' => $introRecord['openId'],
            'wx_introducer_code' => $introRecord['wx_introducer_code'],
            'introduceredOpenId' => $this->openID,
            'wx_introducered_code' => $this->wx_introducered_code
        );

        try{
            //开启事务，当成功推荐记录表数据写入成功后更新找人记录状态失败时回滚
            \PhalApi\DI()->notorm->beginTransaction('db_master');

            $ret = \PhalApi\DI()->notorm->intro_success_record->insert($data);
            if($ret){
                $domainRecord = new DomainRECORD();
                $flag = $domainRecord->upate($this->record_id, array('oper_state' => 3));
                if($flag){
                    \PhalApi\DI()->notorm->commit('db_master');
                    //被引荐人提交数据成功后平分发起人金额 收取10%交易费用
                    $record = $domainRecord->get($this->record_id);
                    if(!empty($record)){
                        $this->updateRecord($record, $ret);
                    }

                }else{
                    \PhalApi\DI()->notorm->rollback('db_master');
                    throw new Exception('更新记录状态失败', 500);
                }
            }

            return $ret;
        }catch (\Exception $e){
            throw new Exception($e->getMessage(), 500);
        }

    }

    /**
     * 写入各入账记录表，更新推荐人和被推荐人钱包金额
     * @param $record
     * @param $ret
     */
    private function updateRecord($record, $ret){
        $rate = \PhalApi\DI()->config->get('params.rate');
        $averageMoney = ($record['money']-$record['money']*$rate)/2;
        //更新推荐人和被推荐人钱包金额，并且写入各入账记录表
        $domainIntroSuccessRecord = new DomainIntroSuccessRecord();
        $introSuccescRecord = $domainIntroSuccessRecord->get($ret);
        if(!empty($introSuccescRecord)){
            unset($data);
            $data = array(
                'recordId' => $record['id'],
                'introducererOpenId0' => $introSuccescRecord['introducererOpenId'],
                'money0' => $averageMoney,
                'money1' => $averageMoney,
                'introducererOpenId1' => $introSuccescRecord['introduceredOpenId'],
            );

            $walletIncomeRecord = new DomainWalletIncomeRecord();
            $walletIncomeRecord->insert($data);

            //更新推荐人和被推荐人钱包
            $domainUSER = new DomainUSER();
            $domainUSER->updateWallet($introSuccescRecord['introducererOpenId'], $averageMoney);
            $domainUSER->updateWallet($introSuccescRecord['introduceredOpenId'], $averageMoney);

        }else{
            \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__. " 未找到推荐成功相关记录");
        }
    }

    /**
     * 发送模板消息
     * @desc 被推荐人提交数据城后，给发起人、引荐人、被引荐人发送模板消息
     * @return boolean flag 1.成功 0.失败
     */
    public function sendModuleMsg(){
        $recordId = $this->recordId;
        $domainRecord = new DomainRECORD();
        $record = $domainRecord->get($recordId);

        if(!empty($record)){
            $domainIntroSuccessRecord = new DomainIntroSuccessRecord();
            $introSuccescRecord = $domainIntroSuccessRecord->getRecordByRecordId($recordId);
            if(empty($introSuccescRecord)){
                \PhalApi\DI()->logger->error(__CLASS__.__FUNCTION__. " 未找到推荐成功相关记录，id:" . $recordId);
            }

            //服务进度通知
            $data = array(
                'touser' => $record['openId'],
                'template_id' => 'DdQxT0RfqPy4AGOPVVHz7a9vK09W7MVsORTwfVMsHHw',
                'page' => 'pages/index',
                'form_id' => $this->formId,
                'data' => array(
                    'keyword1' => array('value' => '找到啦，赶紧去联系他（她）吧'),
                    'keyword2' => array('value' => $introSuccescRecord['wx_introducer_code']),
                    'keyword3' => array('value' => $introSuccescRecord['wx_introducered_code']),
                ),
                'emphasis_keyword' => ''

            );

            //发送模板消息给发起人
            $this->sendModuleMsgFunc($data);

            $domainUSER = new DomainUSER();
            $introducererInfo = $domainUSER->getUserByOpenid($introSuccescRecord['introducererOpenId']);
            $introduceredInfo = $domainUSER->getUserByOpenid($introSuccescRecord['introduceredOpenId']);

            $domainIntroSuccessRecord->sendModuleMsgToIntro($introSuccescRecord['introduceredOpenId'], $record, $introduceredInfo['nickName']);
            $domainIntroSuccessRecord->sendModuleMsgToIntro($introSuccescRecord['introducererOpenId'], $record, $introducererInfo['nickName']);
        }

    }

    private function sendModuleMsgFunc($data){

        $domainIntroSuccessRecord = new DomainIntroSuccessRecord();
        return $domainIntroSuccessRecord->sendModuleMsg($data);
    }

}