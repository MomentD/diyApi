<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/28
 * Time: 11:25
 */

namespace Library\Components;

use Library\Exceptions\ContainerBadMethodException;
use Library\Exceptions\NotFoundException;
use Library\Tools\RedisService;
use Redis;

class LoadRedis
{
    /**
     * 记录每次链接的redis池类型
     * @var array
     */
    private static $instance = [];

    protected static $defaultRedisClass = 'Library\\Tools\\RedisService';

    /**
     * Singleton instance（获取自己的实例）
     * @param string $func
     * @return Redis
     * @throws ContainerBadMethodException
     * @throws NotFoundException
     */
    public static function getInstance( string $func )
    {
        $func = strtoupper( $func );
        $redisDriver = config( 'redis_driver' );
        if ($redisDriver === 'read_write')
        {
            if (in_array( $func , RedisService::$setFunction ))
            {
                $type = 'write';
            }
            elseif (in_array( $func , RedisService::$getFunction ))
            {
                $type = 'read';
            }
            else
            {
                throw new ContainerBadMethodException( 'function ' . $func . ' not exists' , 10001 );
            }
        }
        else
        {
            $type = $redisDriver;
        }
        $name = '_instance_' . $type;
        if (isset( self::$instance[$type] ))
        {
            return LoadRedisStaticInstance::$$name;
        }
        if (null === LoadRedisStaticInstance::$$name || !isset( self::$instance[$type] ))
        {
            self::loadConfig();
            LoadRedisStaticInstance::$$name = new self();
            return LoadRedisStaticInstance::$$name->loadRedis( $func );
        }
        return LoadRedisStaticInstance::$$name;
    }

    /**
     * @param string $func
     * @return Redis
     * @throws ContainerBadMethodException
     */
    private static function loadRedis( string $func )
    {
        /**
         * @var $class RedisService
         */
        $class = new self::$defaultRedisClass();
        self::$instance[$class->redisType] = 1;
        return $class->getConnectionByFuncName( $func );
    }

    /**
     * 初始化配置信息
     * @throws NotFoundException
     */
    private static function loadConfig()
    {
        $redisDriver = config( 'redis_driver' );
        $config = config( 'redis.' . $redisDriver );
        if (empty( $config ))
        {
            throw new NotFoundException( 'redis配置信息不存在，请配置相关redis配置信息' );
        }
    }
}