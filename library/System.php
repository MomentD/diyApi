<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/26
 * Time: 11:15
 */
// debug默认开启
defined( 'SF_DEBUG' ) or define( 'SF_DEBUG' , true );
// 框架开始运行时间
defined( 'SF_START_TIME' ) or define( 'SF_START_TIME' , microtime( true ) );
//配置目录
defined( 'SF_CONFIG_PATH' ) or define( 'SF_CONFIG_PATH' , __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR );
// 核心文件目录
defined( 'SF_LIBRARY_PATH' ) or define( 'SF_LIBRARY_PATH' , __DIR__ . DIRECTORY_SEPARATOR );
// 应用目录
defined( 'SF_APP_PATH' ) or define( 'SF_APP_PATH' , __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR );
// 入口目录
defined( 'SF_PUBLIC_PATH' ) or define( 'SF_PUBLIC_PATH' , __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR );