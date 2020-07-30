<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/28
 * Time: 12:20
 */
config( [
    'debug' => true ,
    'redis_driver' => 'default',
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