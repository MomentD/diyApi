<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/28
 * Time: 12:21
 */
config( [
    /**
     * 静态路由，如果在其他文件配置，则配置的key必须固定为route
     */
    'route' => [
        'test' => 'demo/test' ,
        'test/api' => 'TestApiController@test' ,
        'test/api2' => 'testApi/test' ,
        'test/api3' => 'TestContainerController@test' ,
        'test/redis' => 'TestRedisController@test_redis' ,
        'test/api5' => 'Activity\TestActivityController@test_activity',
        'test/api6' => 'activity/testActivity/test_activity',
    ] ,
    /**
     * 动态配置路由设置，如果在其他文件配置，则配置的key必须固定为dynamic_route
     */
    'dynamic_route' => [
        'test/api4/{id}' => 'TestApiController@test_route' ,
        'test/{id}/name' => 'TestApiController@test_route' ,
        'test/{name}/api4/{id}' => 'TestApiController@test_route2' ,
        '{year}/{month}/{day}/api/test' => 'TestApiController@test_route3' ,
    ],
    /**
     * 是否开启默认的首页路由，如果开启，则当调用不存在的路由时，不会抛异常，而是会自动调转到默认的路由
     * 具体设置在 library/Https/Controller.php文件的index方法
     */
    'defaultRoute' => false ,
] );