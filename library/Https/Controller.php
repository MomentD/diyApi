<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/26
 * Time: 12:23
 */

namespace Library\Https;
class Controller
{
    protected $response;

    protected $code = 200;

    public function __construct( Response $response )
    {
        $this->response = $response;
    }

    /**
     * 默认路由
     * @return Response
     */
    public function index()
    {
        return $this->response->json( [ 'hello' => 'Welcome To Your Diy Api System' ] );
    }
}