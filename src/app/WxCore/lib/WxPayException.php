<?php
/**
 * 
 * 微信支付API异常类
 * @author kewin.cheng
 *
 */
namespace App\WxCore\lib;

class WxPayException extends \Exception {

	public function errorMessage(){
		return $this->getMessage();
	}
}
