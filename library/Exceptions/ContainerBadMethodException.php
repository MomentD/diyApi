<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/27
 * Time: 9:59
 */

namespace Library\Exceptions;
use Psr\Container\ContainerExceptionInterface;

class ContainerBadMethodException extends SaiException implements ContainerExceptionInterface
{
    protected $code = 405;
}