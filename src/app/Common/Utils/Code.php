<?php
/**
 * User: kewin.cheng
 * Date: 2018/1/9
 * Time: 10:58
 */

namespace App\Common\Utils;

class Code{

    //正常
    const OK  					=  10000;			//正常
    //公用错误
    const ERROR					= -10000;			//未知错误类型
    const VERIFY_FAIL			= -10001;           //验证错误
    const PARAM_ERR				= -10002;           //参数错误
    const NO_RECORD				= -10003;           //找不到记录
    const CONF_MISS				= -10004;			//配置缺失
    const DB_ERROR              = -10009;           //数据库错误


}