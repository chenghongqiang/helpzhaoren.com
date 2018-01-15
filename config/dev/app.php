<?php
/**
 * 请在下面放置任何您需要的应用配置
 *
 * @license     http://www.phalapi.net/license GPL 协议
 * @link        http://www.phalapi.net/
 * @author dogstar <chanzonghuang@gmail.com> 2017-07-13
 */

return array(

    /**
     * 应用接口层的统一参数
     */
    'apiCommonRules' => array(
        //'sign' => array('name' => 'sign', 'require' => true),
    ),

    /**
     * 接口服务白名单，格式：接口服务类名.接口服务方法名
     *
     * 示例：
     * - *.*         通配，全部接口服务，慎用！
     * - Site.*      Api_Default接口类的全部方法
     * - *.Index     全部接口类的Index方法
     * - Site.Index  指定某个接口服务，即Api_Default::Index()
     */
    'service_whitelist' => array(
        'Site.Index',
    ),
    /**
     * 扩展类库 - 快速路由配置
     */
    'FastRoute' => array(
        /**
         * 格式：array($method, $routePattern, $handler)
         *
         * @param string/array $method 允许的HTTP请求方式，可以为：GET/POST/HEAD/DELETE 等
         * @param string $routePattern 路由的正则表达式
         * @param string $handler 对应PhalApi中接口服务名称，即：?service=$handler
         */
        'routes' => array(
            array('POST', '/app/find/pay/notify/{detail:\d+}', 'App.Find_Pay.Notify')
        ),
    ),
    /**
     * 计划任务配置
     */
    'Task' => array(
        //MQ队列设置，可根据使用需要配置
        'mq' => array(
            'redis' => array(
                'host' => '10.1.2.53',
                'port' => 6379,
                'db' => 2,
                'auth' => '',
                'prefix' => 'phal_task',
                'timeout' => 1000
            ),
        )
    ),
);
