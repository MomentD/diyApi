<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/26
 * Time: 19:28
 */

namespace App\Https\Controllers;
class A
{
    /**
     * @var B
     */
    private $bbb;

    public function __construct( B $b)
    {
        $this->bbb = $b;
    }

    /**
     * @return string
     * @throws \RedisException
     */
    public function doSomething()
    {
        return $this->bbb->doSomething();
    }
}