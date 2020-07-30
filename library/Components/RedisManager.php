<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/28
 * Time: 16:26
 */

namespace Library\Components;

use Library\Exceptions\ContainerBadMethodException;
use Library\Exceptions\NotFoundException;
use Library\Tools\RedisService;
use Redis;

class RedisManager
{
    /**
     * redis对象
     * @var Redis
     */
    public $redisObj;

    /**
     * @param string $func
     * @param array  $params
     * @return RedisService|Redis
     * @throws ContainerBadMethodException
     * @throws NotFoundException
     */
    public function __call( string $func , $params )
    {
        $this->resetInstance( $func );
        $res = null;
        if (method_exists( $this , $func ))
        {
            $obj = $this;
        }
        else
        {
            $obj = $this->redisObj;
        }
        $paramsCount = count( $params );
        //一般redis的方法入参不会超过4个
        switch ($paramsCount)
        {
            case 1:
                $res = $obj->$func( $params[0] );
                break;
            case 2:
                $res = $obj->$func( $params[0] , $params[1] );
                break;
            case 3:
                $res = $obj->$func( $params[0] , $params[1] , $params[2] );
                break;
            case 4:
                $res = $obj->$func( $params[0] , $params[1] , $params[2] , $params[4] );
                break;
        }
        return $res;
    }

    /**
     * 获取缓存内容
     * @param string $key
     * @param bool   $needSerialization
     * @return bool|mixed|string
     * @throws ContainerBadMethodException
     * @throws NotFoundException
     */
    public function get( string $key , $needSerialization = true )
    {
        $this->resetInstance( 'get' );
        $value = $this->redisObj->get( $key );
        return str_parse( $value , $needSerialization );
    }

    /**
     * 设置缓存
     * @param string       $key   缓存key
     * @param string|array $value 缓存内容
     * @param int|null     $ttl   缓存时间
     * @return bool
     * @throws ContainerBadMethodException
     * @throws NotFoundException
     */
    public function set( string $key , $value , $ttl = null )
    {
        $this->resetInstance( 'set' );
        if (is_array( $value ))
        {
            //如果是数组，则进行序列化操作
            $value = serialize( $value );
        }
        return $this->redisObj->set( $key , $value , $ttl );
    }

    /**
     * @param string       $key     redis key，可以当做表明
     * @param string       $hashKey hash key，可以当做表的字段名称
     * @param string|array $value 内容
     * @param int          $ttl 缓存时间
     * @return bool|int
     * @throws ContainerBadMethodException
     * @throws NotFoundException
     */
    public function hSet( string $key , string $hashKey , $value , $ttl = 0 )
    {
        $this->resetInstance( 'hSet' );
        if (is_array( $value ))
        {
            //如果是数组，则进行序列化操作
            $value = serialize( $value );
        }
        $res = $this->redisObj->hSet( $key , $hashKey , $value );
        if ($res && $ttl > 0)
        {
            $this->expire( $key , $ttl );
        }
        return $res;
    }

    /**
     * 设置缓存时间
     * @param string $key
     * @param int    $ttl
     * @return bool
     * @throws ContainerBadMethodException
     * @throws NotFoundException
     */
    public function expire( string $key , int $ttl )
    {
        $this->resetInstance( 'expire' );
        return $this->redisObj->expire( $key , $ttl );
    }

    /**
     * 删除key
     * @param string $key
     * @return int
     * @throws ContainerBadMethodException
     * @throws NotFoundException
     */
    public function del( string $key )
    {
        $this->resetInstance( 'del' );
        return $this->redisObj->del( $key );
    }

    /**
     * 事务执行
     * @return void
     * @throws ContainerBadMethodException
     * @throws NotFoundException
     */
    public function exec()
    {
        $this->resetInstance( 'exec' );
        return $this->redisObj->exec();
    }

    /**
     * @param string $func
     * @throws ContainerBadMethodException
     * @throws NotFoundException
     */
    private function resetInstance( string $func )
    {
        $this->redisObj = LoadRedis::getInstance( $func );
    }
}