<?php
/**
 * 我要找人接口文档 - 自动生成
 *
 * - 对Api_系列的接口，进行罗列
 * - 按service进行字典排序
 * - 支持多级目录扫描
 *
 * <br>使用示例：<br>
 * ```
 * <?php
 * // 左侧菜单说明
 * class Demo extends Api {
 *      /**
 *       * 接口服务名称
 *       * @desc 更多说明
 *       * /
 *      public function index() {
 *      }
 * }
 * ```
 * @author      chenghongqiang 2017-12-23
 */

require_once dirname(__FILE__) . '/init.php';

$projectName = '我要找人接口文档';

if (!empty($_GET['detail'])) {
    $apiDesc = new \PhalApi\Helper\ApiDesc($projectName);
    $apiDesc->render();
} else {
    $apiList = new \PhalApi\Helper\ApiList($projectName);
    $apiList->render();
}

