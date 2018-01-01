<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/26
 * Time: 19:54
 */

namespace App\Component;

use App\Domain\Common;
use PhalApi\Api;

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

        $commonDomain = new Common();
        $this->openID = $commonDomain->getOpenId($this->thirdSessionKey);

    }



}