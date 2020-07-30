<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/26
 * Time: 12:12
 */

namespace Library\Https;

use Exception;
use Library\Components\Base;
use Library\Components\IocContainer;
use Library\Exceptions\ArgumentCountErrorException;
use Library\Exceptions\ContainerBadMethodException;
use Library\Exceptions\ContainerNotFoundException;
use ReflectionException;

class Request extends Base
{
    /**
     * get参数数组
     */
    private $queryParams = [];

    private $method;

    // app应用控制器命名空间
    private $controllerNameSpace = 'App\\Https\\Controllers\\';

    // 之前定义的基类控制器
    private $baseController = 'Library\\Https\\Controller';

    private $pathUrl;

    /**
     * @var array|null
     */
    protected $route;

    /**
     * 获取请求方法
     * @return string
     */
    public function getMethod()
    {
        if (isset( $_SERVER['REQUEST_METHOD'] ))
        {
            return strtoupper( $_SERVER['REQUEST_METHOD'] );
        }
        return 'GET';
    }

    /**
     * 请求头
     * @param string $name
     * @param null   $defaultValue
     * @return mixed|null
     */
    public function getHeader( string $name , $defaultValue = null )
    {
        $name = ucfirst( $name );
        if (function_exists( 'apache_request_headers' ))
        {
            $headers = apache_request_headers();
            return $headers[$name] ?? $defaultValue;
        }
        // $_SERVER使用下划线
        $name = strtoupper( str_replace( '-' , '_' , $name ) );
        // 部分自定义参数需要加上HTTP_
        return $_SERVER[$name] ?? ($_SERVER['HTTP_' . $name] ?? $defaultValue);
    }

    /**
     * 获取url某个入参，默认获取全部入参
     * @param null $name
     * @param null $defaultValue
     * @return mixed
     */
    public function get( $name = null , $defaultValue = null )
    {
        if ($name === null)
        {
            return $this->getQueryParams();
        }
        return $this->getQueryParam( $name , $defaultValue );
    }

    /**
     * 获取url某个入参
     * @param string $name
     * @param null   $defaultValue
     * @return mixed|null
     */
    public function getQueryParam( string $name , $defaultValue = null )
    {
        $params = $this->getQueryParams();
        return isset( $params[$name] ) ? $params[$name] : $defaultValue;
    }

    /**
     * 批量获取所有get入参，接口链接?后面的部分
     * @return array
     */
    public function getQueryParams()
    {
        if (empty( $this->queryParams ))
        {
            return $this->queryParams = $_GET;
        }
        return $this->queryParams;
    }

    /**
     * 获取post请求某个参数，默认获取全部
     * @param null $name
     * @param null $defaultValue
     * @return array|mixed|null
     */
    public function post( $name = null , $defaultValue = null )
    {
        if ($name === null)
        {
            return $this->getBodyParams();
        }
        return $this->getBodyParam( $name , $defaultValue );
    }

    /**
     * 获取某个值的入参
     * @param string $name
     * @param null   $defaultValue
     * @return mixed|null
     */
    public function getBodyParam( string $name , $defaultValue = null )
    {
        $params = $this->getBodyParams();
        if (is_object( $params ))
        {
            try
            {
                return $params->{$name};
            }
            catch (Exception $e)
            {
                return $defaultValue;
            }
        }
        return isset( $params[$name] ) ? $params[$name] : $defaultValue;
    }

    /**
     * 批量获取所有post请求的入参
     * @return array|mixed
     */
    public function getBodyParams()
    {
        $contentType = strtolower( $this->getHeader( 'Content-Type' ) );
        if (strpos( $contentType , 'multipart/form-data' ) === false)
        {
            $bodyParams = json_decode( file_get_contents( "php://input" ) , true );
        }
        else
        {
            $bodyParams = $_POST;
        }
        return $bodyParams ?? [];
    }

    /**
     * 控制器处理
     * @param string $urlRoute
     * @param bool   $defaultRoute
     * @return mixed
     * @throws ReflectionException
     * @throws ContainerBadMethodException|ContainerNotFoundException
     * @throws ArgumentCountErrorException
     */
    public function runAction( string $urlRoute , bool $defaultRoute )
    {
        $params = [];
        if (array_key_exists( $urlRoute , $this->route ))
        {
            $configControllerRoute = $this->route[$urlRoute];
        }
        else
        {
            //不存在时，判断是否是动态接口
            $configControllerRoute = $this->parseDynamicRoute( $urlRoute , $params );
            if (empty( $configControllerRoute ) && !$defaultRoute)
            {
                throw new ContainerNotFoundException( "route not found:" . $urlRoute );
            }
        }
        if (strpos( $configControllerRoute , '@' ))
        {
            $controllerNameArr = split_str_to_array( $configControllerRoute , '@' );
            $controller = $this->createController( $controllerNameArr[0] );
            $action = $controllerNameArr[1];
            if (!method_exists( $controller , $action ))
            {
                throw new ContainerNotFoundException( "method not found:" . $action );
            }
        }
        else
        {
            $match = explode( '/' , $configControllerRoute );
            $match = array_filter( $match );
            // 处理$route=/
            if (empty( $match ))
            {
                $match = [ 'index' ];
                $controller = $this->createController( $match );
                $action = 'index';
                // 处理$route=index
            }
            elseif (count( $match ) < 2)
            {
                $controller = $this->createController( $match );
                $action = 'index';
            }
            else
            {
                $action = array_pop( $match );
                $controller = $this->createController( $match );
                if (!method_exists( $controller , $action ))
                {
                    throw new ContainerNotFoundException( "method not found:" . $action );
                }
            }
        }
        // 将get和post注入控制器方法中
        $params = array_merge( $params , $this->getQueryParams() , $this->getBodyParams() );
        //以依赖注入的方式进行调用
        return IocContainer::run( $controller , $action , $params );
    }

    /**
     * 解析是否是动态路由
     * @param string $route  待解析的url路由
     * @param array  $params 动态路由部分的入参，{}部分的设置规则，统一转为对应名称的变量，如{id}，解析为id=xx
     * @return false|int|mixed|string
     */
    public function parseDynamicRoute( string $route , &$params )
    {
        $parse_route = '';
        $dynamic_route = config( 'dynamic_route' );
        $currentRoute = split_str_to_array( $route , '/' );
        $countRoute = count( $currentRoute );
        if (!array_is_empty( $dynamic_route ))
        {
            $configRoutes = array_keys( $dynamic_route );
            $newConfigRoutes = [];
            foreach ( $configRoutes as $key => $configRoute )
            {
                $needRoute = split_str_to_array( $configRoute , '/' );
                $needCountRoute = count( $needRoute );
                if ($countRoute > 0 && $countRoute == $needCountRoute)
                {
                    $newConfigRouteStr = '';
                    for ( $i = 0; $i < $needCountRoute; $i++ )
                    {
                        if (strpos( 'need_' . $needRoute[$i] , '{' ))
                        {
                            $needRoute[$i] = $currentRoute[$i];
                        }
                        $newConfigRouteStr .= $needRoute[$i] . '/';
                    }
                    $newConfigRoutes[$key] = rtrim( $newConfigRouteStr , '/' );
                }
            }
            $routeIndex = array_search( $route , $newConfigRoutes );
            if (is_int( $routeIndex ))
            {
                $dynamicRoute = $configRoutes[$routeIndex];
                $dynamicRouteArr = split_str_to_array( $dynamicRoute , '/' );
                foreach ( $dynamicRouteArr as $k => $item )
                {
                    if (strpos( 'need_' . $item , '{' ))
                    {
                        $dynamicParam = substr( $item , 1 , strlen( $item ) - 2 );
                        //匹配到动态标记{}，记下入参
                        $params[$dynamicParam] = $currentRoute[$k];
                    }
                }
                $dynamicRouteControllers = array_values( $dynamic_route );
                $parse_route = $dynamicRouteControllers[$routeIndex];
            }
        }
        return $parse_route;
    }

    /**
     * 创建控制器
     * @param array|string $match
     * @return mixed
     * @throws ContainerNotFoundException
     */
    public function createController( $match )
    {
        $controllerName = $this->controllerNameSpace;
        if (is_string( $match ))
        {
            $controllerName = $controllerName . $match;
        }
        elseif (is_array( $match ))
        {
            foreach ( $match as $namespace )
            {
                $controllerName .= ucfirst( $namespace ) . '\\';
            }
            $controllerName = rtrim( $controllerName , '\\' ) . 'Controller';
        }
        if (!class_exists( $controllerName ))
        {
            if ($controllerName == $this->controllerNameSpace . 'IndexController')
            {
                return $this->baseController;
            }
            throw new ContainerNotFoundException( "controller not found:" . $controllerName );
        }
        return $controllerName;
    }

    /**
     * 返回不含参数的REQUEST_URI地址
     * @param array $route
     * @return bool|mixed|string
     */
    public function resolve( $route = [] )
    {
        $this->route = $route;  // 自定义路由
        return $this->getPathUrl();
    }

    /**
     * 获取请求地址
     * @return bool|mixed|string
     */
    public function getPathUrl()
    {
        if (is_null( $this->pathUrl ))
        {
            $url = trim( $_SERVER['REQUEST_URI'] , '/' );
            $index = strpos( $url , '?' );
            $this->pathUrl = ($index > -1) ? substr( $url , 0 , $index ) : $url;
        }
        return $this->pathUrl;
    }
}