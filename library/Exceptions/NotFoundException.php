<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/26
 * Time: 12:31
 */

namespace Library\Exceptions;
class NotFoundException extends SaiException
{
    protected $code = 404;
}