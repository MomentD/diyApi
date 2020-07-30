<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/26
 * Time: 19:00
 */

namespace App\Https\Controllers;

use Library\Https\Controller;
use Library\Https\Request;
use Library\Https\Response;

class TestContainerController extends Controller
{
    /**
     * @param Request $request
     * @param A       $a
     * @return Response
     * @throws \RedisException
     */
    public function test(Request $request , A $a)
    {
        $addr = $request->get('a');
        return $this->response->json( [ $a->doSomething() , $addr ] );
    }
}