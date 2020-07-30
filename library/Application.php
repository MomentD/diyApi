<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/26
 * Time: 11:15
 */

namespace Library;

use Library\Exceptions\SaiException;
use Library\Https\Request;
use Library\Https\Response;
use ReflectionException;

class Application
{
    private $request;

    /**
     * Application constructor.
     * @param Request $request
     */
    public function __construct( Request $request )
    {
        $this->request = $request;
    }

    /**
     * 运行应用并输出数据
     * @return bool
     * @throws ReflectionException
     */
    public function run()
    {
        try
        {
            $response = $this->handleRequest( $this->request );
            $response->send();
            return true;
        }
        catch (SaiException $e)
        {
            if (config( 'debug' ))
            {
                $e->response( $e->getCode() , [
                    'line' => $e->getLine() ,
                    'msg' => $e->getMessage() ,
                    'code' => $e->getCode() ,
                    'file' => $e->getFile() ,
                ] );
            }
            else
            {
                $e->response( $e->getCode() , [
                    'msg' => $e->getMessage()
                ] );
            }
            return false;
        }
    }

    /**
     * 处理请求
     * @param Request $request
     * @return mixed
     * @throws SaiException|ReflectionException
     */
    public function handleRequest( Request $request )
    {
        $route = $request->resolve( config( 'route' ) );
        $response = $request->runAction( $route , config( 'defaultRoute' ) );
        /**
         * 执行结果赋值给$response->data，并返回给response对象
         */
        if ($response instanceof Response)
        {
            // 返回Response对象
            return $response;
        }
        throw new SaiException( '输出的内容格式错误' );
    }
}