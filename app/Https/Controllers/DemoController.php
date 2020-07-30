<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/26
 * Time: 13:00
 */

namespace App\Https\Controllers;

use Library\Https\Controller;
use Library\Https\Request;

class DemoController extends Controller
{
    public function welcome()
    {
        return $this->response->json( [ 'hello' => 'welcome' ] );
    }

    public function test( Request $request )
    {
        $data = $request->get( 'demo' );
        return $this->response->json( $data );
    }
}