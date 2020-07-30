<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/26
 * Time: 14:03
 */

namespace App\Https\Controllers;

use Library\Https\Controller;
use Library\Https\Request;

class TestApiController extends Controller
{
    public function test( Request $request )
    {
        $id = $request->get( 'id' );
        return $this->response->json( [ 'test api id:' . $id ] );
    }

    public function test_route( Request $request , $id )
    {
        $id1 = $request->get( 'id1' );
        return $this->response->json( [ "test route api id:{$id},other param : {$id1}" ] );
    }

    public function test_route2( Request $request , $name , $id )
    {
        $id1 = $request->get( 'id1' );
        return $this->response->json( [ "test route api name:{$name} id:{$id},other param : {$id1}" ] );
    }

    public function test_route3( Request $request , $year , $month , $day )
    {
        $id1 = $request->get( 'id1' );
        return $this->response->json( "test route api year:{$year} month:{$month} day:{$day},other param : {$id1}" );
    }
}