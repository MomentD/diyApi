<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/28
 * Time: 12:12
 */

namespace App\Https\Controllers;

use Library\Components\RedisManager;
use Library\Exceptions\ContainerBadMethodException;
use Library\Exceptions\NotFoundException;
use Library\Https\Controller;
use Library\Https\Request;
use Library\Components\LoadRedis;
use Library\Https\Response;
use Redis;

class TestRedisController extends Controller
{
    /**
     * @var RedisManager|Redis
     */
    private $redis;

    public function __construct( Response $response )
    {
        parent::__construct( $response );
        $this->redis = new RedisManager();
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function test_redis( Request $request )
    {
        $value = [
            [
                'mobile' => '136****0010' ,
                'prize' => '恭喜136****0010获得1000乐米'
            ] ,
            [
                'mobile' => '136****0011' ,
                'prize' => '恭喜136****0011获得400乐米'
            ] ,
            [
                'mobile' => '136****0012' ,
                'prize' => '恭喜136****0020获得300乐米'
            ] ,
            [
                'mobile' => '136****0013' ,
                'prize' => '恭喜136****0013获得1个月Level2券'
            ] ,
            [
                'mobile' => '136****0014' ,
                'prize' => '恭喜136****0014获得10元京东卡'
            ] ,
            [
                'mobile' => '136****0015' ,
                'prize' => '恭喜136****0015获得6666乐米'
            ] ,
        ];
        $key = $request->get( 'key' );
        $value = $request->get( 'value' );
        //$this->redis->hSet( 'test_user' , $key , $value );
        $data = $this->redis->hGet( 'test_user' , $key );
        return $this->response->json( $data );
    }
}