<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/26
 * Time: 15:21
 */

namespace Library\Exceptions;

use Psr\Container\ContainerExceptionInterface;

class ContainerException extends SaiException implements ContainerExceptionInterface
{
    protected $code = 424;
}