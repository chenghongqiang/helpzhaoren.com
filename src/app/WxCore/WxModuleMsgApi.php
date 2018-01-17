<?php
/**
 * Created by PhpStorm.
 * User: kewin.cheng
 * Date: 17/12/31
 * Time: PM6:05
 */

namespace App\WxCore;

use App\WxCore\lib\WxPayApi;
use App\WxCore\lib\WxPayConfig;
use PhalApi\CUrl;
use PhalApi\Exception;

/**
 * 微信模板消息API
 * Class WxModuleMsgApi
 * @package App\WxCore
 */
class WxModuleMsgApi{

    /**
     * 发送小程序模板消息
     * @param $accessToken
     * 发送模板消息格式：
     * {
        "touser": "OPENID",
        "template_id": "TEMPLATE_ID",
        "page": "index",
        "form_id": "FORMID",
        "data": {
            "keyword1": {
                "value": "339208499",
                "color": "#173177"
            },
            "keyword2": {
                "value": "2015年01月05日 12:30",
                "color": "#173177"
            },
            "keyword3": {
                "value": "粤海喜来登酒店",
                "color": "#173177"
            } ,
            "keyword4": {
                "value": "广州市天河区天河路208号",
                "color": "#173177"
            }
        },
        "emphasis_keyword": "keyword1.DATA"
        }
     */
    public static function sendModuleMsg($accessToken, $data){

        //发送模板消息接口
        $sendModuleMsgURL = 'https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=%s';
        $url = sprintf($sendModuleMsgURL, $accessToken);

        $curl = new CUrl();
        $curl->setHeader(array('content-type' => 'application/json'));
        $rs = $curl->post($url, http_build_query($data), 6000);
        $data = json_decode($rs, true);

        return $data;
    }


}