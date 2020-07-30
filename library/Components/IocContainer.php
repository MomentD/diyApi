<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/27
 * Time: 9:27
 * Desc: Ioc依赖注入容器公共类
 */

namespace Library\Components;

use ArgumentCountError;
use InvalidArgumentException;
use Library\Exceptions\ArgumentCountErrorException;
use Library\Exceptions\ContainerBadMethodException;
use Library\Exceptions\ContainerNotFoundException;
use ReflectionClass;
use ReflectionException;

/**
 * 依赖注入核心类
 * Class IocContainer
 * @see     https://github.com/wazsmwazsm/IOCContainer/tree/master/src/IOC/Container.php
 * @package Library\Components
 */
class IocContainer
{
    /**
     * singleton instances.
     * 单例集合，以class名称作为key
     * @var array
     */
    protected static $_singleton = [];

    /**
     * create Dependency injection params.
     * 创建依赖的入参并返回入参，假如有入参的话
     * @param array $params
     * @return array
     * @throws ReflectionException
     */
    protected static function _getDiParams( array $params )
    {
        $di_params = [];
        foreach ( $params as $param )
        {
            $class = $param->getClass();
            if ($class)
            {
                // check dependency is a singleton instance or not
                $singleton = self::getSingleton( $class->name );
                $di_params[] = $singleton ? $singleton : self::getInstance( $class->name );
            }
        }
        return $di_params;
    }

    /**
     * set a singleton instance.
     * 设置一个单例
     * @param object $instance
     * @param string $name
     * @return void
     * @throws InvalidArgumentException
     */
    public static function singleton( $instance , $name = NULL )
    {
        if (!is_object( $instance ))
        {
            throw new InvalidArgumentException( "Object need!" );
        }
        $class_name = $name == NULL ? get_class( $instance ) : $name;
        // singleton not exist, create
        if (!array_key_exists( $class_name , self::$_singleton ))
        {
            self::$_singleton[$class_name] = $instance;
        }
    }

    /**
     * get a singleton instance.
     * 获取一个单例
     * @param string $class_name
     * @return mixed object or NULL
     */
    public static function getSingleton( $class_name )
    {
        return array_key_exists( $class_name , self::$_singleton ) ?
            self::$_singleton[$class_name] : NULL;
    }

    /**
     * unset a singleton instance.
     * 销毁单例
     * @param string $class_name
     * @return void
     */
    public static function unsetSingleton( $class_name )
    {
        self::$_singleton[$class_name] = NULL;
    }

    /**
     * register class, instantiate class, set instance to singleton.
     * 注册类单例
     * @param string $abstract abstract class name
     * @param string $concrete concrete class name, if NULL, use abstract class name
     * @return void
     * @throws ReflectionException
     */
    public static function register( $abstract , $concrete = NULL )
    {
        if ($concrete == NULL)
        {
            $instance = self::getInstance( $abstract );
            self::singleton( $instance );
        }
        else
        {
            $instance = self::getInstance( $concrete );
            self::singleton( $instance , $abstract );
        }
    }

    /**
     * get Instance from reflection info.
     * 获取单例
     * @param string $class_name
     * @param array  $params
     * @return object
     * @throws ReflectionException
     */
    public static function getInstance( $class_name , $params = [] )
    {
        // get class reflector
        $reflector = new ReflectionClass( $class_name );
        // get constructor
        $constructor = $reflector->getConstructor();
        // create di params
        $di_params = $constructor ? self::_getDiParams( $constructor->getParameters() ) : [];
        $di_params = array_merge( $di_params , $params );
        // create instance
        return $reflector->newInstanceArgs( $di_params );
    }

    /**
     * get Instance, if instance is not singleton, set it to singleton.
     * 以单例模式获取实例，如果不是单例，自动设置
     * @param string $class_name
     * @param array  $params
     * @return object
     * @throws ReflectionException
     */
    public static function getInstanceWithSingleton( $class_name , $params = [] )
    {
        // is a singleton instance?
        if (NULL != ($instance = self::getSingleton( $class_name )))
        {
            return $instance;
        }
        $instance = self::getInstance( $class_name , $params );
        self::singleton( $instance );
        return $instance;
    }

    /**
     * run class method.
     * 依赖注入运行
     * @param string $class_name
     * @param string $method
     * @param array  $params
     * @param array  $construct_params
     * @return mixed
     * @throws ContainerNotFoundException
     * @throws ContainerBadMethodException
     * @throws ReflectionException
     * @throws ArgumentCountErrorException
     */
    public static function run( $class_name , $method , $params = [] , $construct_params = [] )
    {
        // class exist ?
        if (!class_exists( $class_name ))
        {
            throw new ContainerNotFoundException( "Class {$class_name} is not found!" );
        }
        // method exist ?
        if (!method_exists( $class_name , $method ))
        {
            throw new ContainerBadMethodException( "undefined method {$method} in {$class_name} !" );
        }
        // create instance
        $instance = self::getInstance( $class_name , $construct_params );
        /******* method Dependency injection *******/
        // get class reflector
        $reflector = new ReflectionClass( $class_name );
        // get method
        $reflectorMethod = $reflector->getMethod( $method );
        // create di params
        $di_params = self::_getDiParams( $reflectorMethod->getParameters() );
        // run method
        try
        {
            return call_user_func_array( [ $instance , $method ] , array_merge( $di_params , $params ) );
        }
        catch (ArgumentCountError $e)
        {
            throw new ArgumentCountErrorException( "{$class_name}::{$method}()方法入参不匹配，请添加必须的入参或给对应方法的入参添加默认值！" );
        }
    }
}