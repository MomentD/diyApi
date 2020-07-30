<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/26
 * Time: 11:15
 */

use Library\Config;

/**
 * 分割字符串公共函数
 */
if (!function_exists( 'split_str_to_array' ))
{
    /**
     * 分割字符串，转为数组
     * @param string $str       [description]
     * @param string $delimiter 分割符
     * @return array            [description]
     */
    function split_str_to_array( $str , $delimiter = ',' )
    {
        return empty( $str ) ? [] : explode( $delimiter , $str );
    }
}
/**
 * 打印公共方法
 */
if (!function_exists( "p" ))
{
    /**
     * @param mixed $var
     */
    function p( $var )
    {
        if (is_bool( $var ))
        {
            var_dump( $var );
        }
        elseif (is_null( $var ))
        {
            var_dump( null );
        }
        else
        {
            die( "<meta charset='utf-8'/>
<pre style='position:relative;
z-index:999;
padding:10px;
border-radius:5px;
background:#f5f5f5;
border:1px solid #aaa;
font-size:14px;
line-height:18px;
opacity:0.8;'>" . print_r( $var , true ) . "</pre>" );
        }
    }
}
/**
 * 数组get某个元素
 */
if (!function_exists( 'array_get' ))
{
    /**
     * @param array  $array
     * @param string $key
     * @param null   $default
     * @return mixed|null
     */
    function array_get( array $array , string $key , $default = null )
    {
        if (is_null( $key ))
        {
            return $array;
        }
        if (isset( $array [$key] ))
        {
            return $array [$key];
        }
        foreach ( explode( '.' , $key ) as $segment )
        {
            if (!is_array( $array ) || !array_key_exists( $segment , $array ))
            {
                return $default;
            }
            $array = $array [$segment];
        }
        return $array;
    }
}
if (!function_exists( 'array_set' ))
{
    /**
     * 设置数组值
     * @param array  $array
     * @param string $key
     * @param mixed  $value
     * @return array|mixed
     */
    function array_set( array &$array , string $key , $value )
    {
        if (is_null( $key ))
        {
            return $array = $value;
        }
        $keys = explode( '.' , $key );
        while (count( $keys ) > 1)
        {
            $key = array_shift( $keys );
            if (!isset( $array [$key] ) || !is_array( $array [$key] ))
            {
                $array [$key] = [];
            }
            $array = &$array [$key];
        }
        $array [array_shift( $keys )] = $value;
        return $array;
    }
}
if (!function_exists( 'array_is_empty' ))
{
    /**
     * @param mixed $arr
     * @return bool
     */
    function array_is_empty( $arr )
    {
        if (is_array( $arr ) && count( $arr ) > 0)
            return false;
        return true;
    }
}
if (!function_exists( 'config' ))
{
    /**
     * 配置某个设置
     * @param null|string|array $key
     * @return array|bool|mixed|void|null
     */
    function config( $key = null )
    {
        if (is_null( $key ))
        {
            return false;
        }
        $Config_instance = Config::getInstance();
        if (is_array( $key ))
        {
            $Config_instance->set( $key );
            return true;
        }
        return $Config_instance->get( $key );
    }
}
if (!function_exists( 'str_parse' ))
{
    /**
     * 解析序列化字符串
     * @param string $str 带解析的字符串
     * @param bool   $needSerialization 是否需要序列化解析内容
     * @return mixed|null
     */
    function str_parse( string $str , $needSerialization = true )
    {
        if ($needSerialization)
        {
            return !is_null( $str ) ? @unserialize( $str ) : null;
        }
        return !is_null( $str ) ? $str : null;
    }
}