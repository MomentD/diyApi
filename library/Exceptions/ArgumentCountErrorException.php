<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/27
 * Time: 14:44
 */

namespace Library\Exceptions;
use Psr\Container\ContainerExceptionInterface;

class ArgumentCountErrorException extends SaiException implements ContainerExceptionInterface
{
    protected $code = 508;
}