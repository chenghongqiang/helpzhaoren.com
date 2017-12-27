<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/27
 * Time: 20:04
 */
namespace App\Common\Utils;

class Tool{

    public static function getRandom($len){
        $str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $value = "";
        for($i = 0; $i < $len; $i++) {
            $value .= $str{mt_rand(0,32)};
        }
        return $value;
    }

}