<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/28
 * Time: 12:21
 */
config( [
    'route' => [
        'test' => 'demo/test' ,
        'test/api' => 'TestApiController@test' ,
        'test/api2' => 'testApi/test' ,
        'test/api3' => 'TestContainerController@test' ,
        'test/redis' => 'TestRedisController@test_redis' ,
    ] ,
    'dynamic_route' => [
        'test/api4/{id}' => 'TestApiController@test_route' ,
        'test/{id}/name' => 'TestApiController@test_route' ,
        'test/{name}/api4/{id}' => 'TestApiController@test_route2' ,
        '{year}/{month}/{day}/api/test' => 'TestApiController@test_route3' ,
    ],
    'defaultRoute' => false ,
] );