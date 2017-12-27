<?php
/**
 * User: kewin.cheng
 * Date: 2017/12/26
 * Time: 20:06
 */

namespace App\Component\Filters;


use PhalApi\Filter;

class FindFilter implements Filter{

    public function check(){

        $allParams = \PhalApi\DI()->request->getAll();
        \PhalApi\DI()->logger->info(json_encode($allParams));

        return true;
    }


}