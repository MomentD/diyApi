<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/28
 * Time: 12:20
 */
config( [
    //是否开启调试
    'debug' => true ,
    /**
     * 使用redis哪个链接池，读写分离特殊点，其余配置只需要配置一个即可
     * 不过每当新增一个类型时，需要去library/Components/LoadRedisStaticInstance.php文件里面新增一个静态变量
     * 名称规则即  public static $_instance_类型名称
     */
    'redis_driver' => 'default',
    /**
     * redis连接池配置，如果在其他文件配置，则配置的key必须固定为redis
     * 配置的列表为数组，key为上面参数配置的可选值
     */
    'redis' => [
        'read_write' => [
            'write' => [
                'host' => '127.0.0.1' ,
                'port' => 6380 ,
                'auth' => '' ,
                'try' => 5 ,
                'connect_timeout' => 5 ,
                'read_timeout' => 3 ,
                'db' => 0
            ] ,
            'read' => [
                'host' => '127.0.0.1' ,
                'port' => 6381 ,
                'auth' => '' ,
                'try' => 5 ,
                'connect_timeout' => 5 ,
                'read_timeout' => 3 ,
                'db' => 0
            ] ,
        ],
        'default' => [
            'host' => '168.63.65.27' ,
            'port' => 6379 ,
            'auth' => '' ,
            'try' => 5 ,
            'connect_timeout' => 5 ,
            'read_timeout' => 3 ,
            'db' => 0
        ] ,
    ]
] );