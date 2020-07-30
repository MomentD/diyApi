<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/26
 * Time: 12:47
 */
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . 'System.php';
// composer自动加载
require __DIR__ . '/../vendor/autoload.php';
// 加载配置
Library\Config::loadConf( SF_CONFIG_PATH );
if (config( 'debug' ))
{
    ini_set( "display_errors" , "On" );
    error_reporting( E_ALL );
}
// 实例化应用并运行
$app = new Library\Application( new Library\Https\Request() );
try
{
    $app->run();
}
catch (ReflectionException $e)
{
    $response = new Library\Https\Response();
    $response->code = 502;
    $response->msg = $e->getMessage();
    $data = null;
    if (config( 'debug' ))
    {
        $data = [
            'line' => $e->getLine() ,
            'code' => $e->getCode() ,
            'file' => $e->getFile() ,
        ];
    }
    return $response->json( $data )->send();
}