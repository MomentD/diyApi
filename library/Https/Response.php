<?php
/**
 * Created by PhpStorm.
 * User: K0241222
 * Date: 2020/7/26
 * Time: 12:21
 */

namespace Library\Https;

use Library\Components\Base;

class Response extends Base
{
    public $code = 0;

    public $result = [];

    public $msg = "success";

    /**
     * 发给调用方
     */
    public function send()
    {
        header( 'Content-Type:application/json; charset=utf-8' );
        echo json_encode( [
            'code' => $this->code ,
            'msg' => $this->msg ,
            'resultData' => $this->result ,
        ] );
    }

    /**
     * 拼接最后的结果数据
     * @param array|string $data
     * @return $this
     */
    public function json( $data = [] )
    {
        $this->result = is_array( $data ) ? array_merge( $this->result , $data ) : $data;
        return $this;
    }
}