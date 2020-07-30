<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/26
 * Time: 15:22
 */

namespace Library\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class ContainerNotFoundException extends SaiException implements NotFoundExceptionInterface
{
    protected $code = '404';
}