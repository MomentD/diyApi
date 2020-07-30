<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/28
 * Time: 11:13
 */

namespace Library\Tools;

use Library\Exceptions\ContainerBadMethodException;
use Redis;

class RedisService
{
    /**
     * @var
     */
    private $getObj;

    private $setObj;

    private $name;

    private $host;

    private $port;

    private $auth;

    private $try;

    private $connectTimeout;

    private $db;

    private $readTimeout;

    public static $getFunction = [
        'DUMP' ,
        'GET' ,
        'STRLEN' ,
        'MGET' ,
        'HKEYS' ,
        'HGET' ,
        'HMGET' ,
        'HGETALL' ,
        'HVALS' ,
        'SCAN' ,
        'SSCAN' ,
        'ZSCAN' ,
        'MSSCAN' ,
        'SMEMBERS' ,
        'SISMEMBER' ,
        'SCARD' ,
        'SPOP' ,
        'ZRANGEBYSCORE' ,
        'ZREVRANGEBYSCORE' ,
        'ZSCORE' ,
        'ZCOUNT' ,
        'LRANGE' ,
        'ZRANGE' ,
        'ZREVRANGE' ,
        'ZRANK' ,
        'ZREVRANK' ,
        'ZCARD' ,
        'TTL' ,
        'LLEN' ,
        'GETMULTIPLE' ,
        'EXISTS' ,
        'HEXISTS' ,
    ];

    public static $setFunction = [
        'PEXPIREAT' ,
        'EXPIREAT' ,
        'PEXPIRE' ,
        'SET' ,
        'DEL' ,
        'MSET' ,
        'DELETE' ,
        'MSETNX' ,
        'LREM' ,
        'LTRIM' ,
        'INCR' ,
        'INCRBY' ,
        'DECR' ,
        'DECRBY' ,
        'HSET' ,
        'HDEL' ,
        'LPUSH' ,
        'LMPUSH' ,
        'RPUSH' ,
        'RMPUSH' ,
        'RPOP' ,
        'HMSET' ,
        'SADD' ,
        'SMADD' ,
        'SREM' ,
        'SREMOVE' ,
        'ZADD' ,
        'ZMADD' ,
        'ZMREM' ,
        'ZREM' ,
        'ZDELETE' ,
        'ZINCRBY' ,
        'EXPIRE' ,
        'HINCRBY' ,
        'ZREMRANGEBYSCORE' ,
        'ZREMRANGEBYRANK' ,
    ];

    /**
     * @var array
     */
    private $redisConfig;

    /**
     * @var string
     */
    public $redisType = '';

    /**
     * @var array|bool|mixed|void|null
     */
    private $redisDriver;

    /**
     * RedisService constructor.
     */
    public function __construct()
    {
        $this->redisDriver = config( 'redis_driver' );
        $this->redisConfig = config( 'redis.' . $this->redisDriver );
    }

    /**
     * @param string $func
     * @return Redis
     * @throws ContainerBadMethodException
     */
    public function getConnectionByFuncName( string $func )
    {
        //读写配置特殊处理
        if ($this->redisDriver === 'read_write')
        {
            if (in_array( strtoupper( $func ) , self::$getFunction ))
            {
                $this->redisType = 'read';
                $obj = $this->getReadObj();
            }
            else if (in_array( strtoupper( $func ) , self::$setFunction ))
            {
                $this->redisType = 'write';
                $obj = $this->getWriteObj();
            }
            else
            {
                throw new ContainerBadMethodException( 'function not exists' , 10001 );
            }
        }
        else
        {
            //其余默认均为单库设置
            $allFunction = array_merge( self::$getFunction , self::$setFunction );
            if (in_array( strtoupper( $func ) , $allFunction ))
            {
                $this->redisType = $this->redisDriver;
                $obj = $this->getReadObj();
            }
            else
            {
                throw new ContainerBadMethodException( 'function not exists' , 10001 );
            }
        }
        return $obj;
    }

    /**
     * 初始化redis配置信息
     */
    private function setConfig()
    {
        $config = is_null( $this->name ) ? $this->redisConfig : $this->redisConfig[$this->name];
        $this->host = $config['host'] ?: '127.0.0.1';
        $this->port = $config['port'] ?: 6379;
        $this->auth = $config['auth'] ?: '';
        $this->try = $config['try'] ?: 5;
        $this->connectTimeout = $config['connect_timeout'] ?: 5;
        $this->readTimeout = $config['read_timeout'] ?: 3;
        $this->db = $config['db'] ?: 0;
    }

    /**
     * 获取redis读实例
     * @return Redis
     */
    private function getReadObj()
    {
        $this->name = $this->redisDriver !== 'read_write' ? null : 'read';
        $this->setConfig();
        if (!isset( $this->getObj ) || !is_object( $this->getObj ))
        {
            $this->getObj = new Redis();
            for ( $i = $this->try; $i > 0; $i-- )
            {
                $re_con = $this->getObj->pconnect( $this->host , $this->port , $this->connectTimeout );
                if ($re_con)
                {
                    break;
                }
            }
            $this->getObj->setOption( Redis::OPT_READ_TIMEOUT , $this->readTimeout );
            $this->auth && $this->getObj->auth( $this->auth );
            $this->getObj->select( $this->db );
        }
        return $this->getObj;
    }

    /**
     * 获取redis写实例
     * @return Redis
     */
    private function getWriteObj()
    {
        $this->name = 'write';
        $this->setConfig();
        if (!isset( $this->conn ) || !is_object( $this->setObj ))
        {
            $this->setObj = new Redis();
            for ( $i = $this->try; $i > 0; $i-- )
            {
                $re_con = $this->setObj->pconnect( $this->host , $this->port , $this->connectTimeout );
                if ($re_con)
                {
                    break;
                }
            }
            $this->auth && $this->setObj->auth( $this->auth );
            $this->setObj->select( $this->db );
        }
        return $this->setObj;
    }

    public function __destruct()
    {
    }
}