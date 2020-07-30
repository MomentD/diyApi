<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/30
 * Time: 11:27
 */

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