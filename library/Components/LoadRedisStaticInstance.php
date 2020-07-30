<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/29
 * Time: 11:09
 */

namespace Library\Components;
/**
 * redis静态实例字段配置，需要与config.php文件中的redis进行匹配
 * Class LoadRedisStaticInstance
 * @package Library\Components
 */
class LoadRedisStaticInstance
{
    /**
     * 读实例
     * @var null
     */
    public static $_instance_read = null;

    /**
     * 写实例
     * @var null
     */
    public static $_instance_write = null;

    /**
     * 默认实例
     * @var null
     */
    public static $_instance_default = null;
}