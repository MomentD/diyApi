<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/26
 * Time: 11:36
 */

namespace Library\Components;

use ArrayAccess;

/**
 * 基础类
 * Class Base
 * @package Library\Components
 */
class Base implements ArrayAccess
{
    private $_container;

    public function __get( $name )
    {
        if (method_exists( $this , $method = 'get' . ucfirst( $name ) ))
        {
            return $this->$method( $name );
        }
        return null;
    }

    /**
     * @param $name
     * @param $value
     * @return mixed
     */
    public function __set( $name , $value )
    {
        if (method_exists( $this , $method = 'set' . ucfirst( $name ) ))
        {
            return $this->$method( $name , $value );
        }
    }

    public function offsetExists( $offset )
    {
        return isset( $this->_container[$offset] );
    }

    public function offsetGet( $offset )
    {
        return isset( $this->_container[$offset] ) ? $this->_container[$offset] : null;
    }

    public function offsetSet( $offset , $value )
    {
        if (is_null( $offset ))
        {
            $this->_container[] = $value;
        }
        else
        {
            $this->_container[$offset] = $value;
        }
    }

    public function offsetUnset( $offset )
    {
        unset( $this->_container[$offset] );
    }
}