# diyApi
个人diy简易api框架

# 说明

> 思路来自laravel社区文章 [PHP DIY 系列](https://learnku.com/articles/40697) ，做了一些改动。
> 此包仅供学习使用

# 目录说明

- `app` 应用目录，主要进行逻辑处理
- `config` 配置信息目录，里面存放一些配置信息，包括路由等等
- `library` 核心类
- `public` 入口文件
- `vendor` composer自动加载公共类

# 配置

> 配置信息全部放在了`config`目录中，以目录扫描（easyswoole中的File类，具体为：`vendor\easyswoole\utility\src\File.php`）方式进行统一处理；

# 路由

> 支持以下类似路由设置，对于参数写在链接里的路由，还请放在 `dynamic_route`这个数组里；具体的配置文件均在 `config`这个文件夹中。
> 注：框架里面 `app\Https\Controller`文件夹中的测试Controller可以全部删除

```
    'route' => [
        'test' => 'demo/test' ,
        'test/api' => 'TestApiController@test' ,
        'test/api2' => 'testApi/test' ,
        'test/api3' => 'TestContainerController@test' ,
        'test/redis' => 'TestRedisController@test_redis' ,
        'test/api5' => 'Activity\TestActivityController@test_activity',
        'test/api6' => 'activity/testActivity/test_activity',
    ] ,
    'dynamic_route' => [
        'test/api4/{id}' => 'TestApiController@test_route' ,
        'test/{id}/name' => 'TestApiController@test_route' ,
        'test/{name}/api4/{id}' => 'TestApiController@test_route2' ,
        '{year}/{month}/{day}/api/test' => 'TestApiController@test_route3' ,
    ],
```

# 支持依赖注入

> 依赖注入核心类文件来自[https://github.com/wazsmwazsm/IOCContainer/tree/master/src/IOC/Container.php](https://github.com/wazsmwazsm/IOCContainer/tree/master/src/IOC/Container.php) ；
> 使用例子如下：

```
    namespace App\Https\Controllers\Activity;
    use Library\Https\Controller;
    use Library\Https\Request;
    
    class TestActivityController extends Controller
    {
        public function test_activity(Request $request)
        {
            $activityId = $request->get('id');
            return $this->response->json('test activity Id:' . $activityId);
        }
    }
```

> 自己做了个简单的redis单例，支持读写分离，如果不需要，可以自己下载redis包 `composer require predis/predis` 。
> mysql没有写，需要的话可以自己写或者参考这篇文章 [在Laravel外独立使用Eloquent](https://www.golaravel.com/post/zai-laravelwai-du-li-shi-yong-eloquent/)