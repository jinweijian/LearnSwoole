<?php


namespace App\Test;


class RPCService
{
    protected $server = null;
    protected $obj = null;

    public function __construct()
    {
        if ( !($this->obj instanceof self)) {
            $this->obj = new self();
        }
        return $this->obj;
    }

    public function runTCP(){
        // 创建一个TCP服务
        $this->server = new \Swoole\server('127.0.0.1',9501);
        // 监听链接进入事件
        $this->server->on('Connect',function ($server,$fd){
            // 回调事件
            echo "Connect\n";
        });
        $this->server->on('Receive',function ($server, $fd, $from_id, $data){
            // 监听数据接收事件
            echo "Receive\n";
            print_r($data);

        });
        $this->server->on('Close',function ($server,$fd){
            // 监听链接关闭事件
            echo "Close\n";
        });
        $this->server->start();
        echo "run.......";
    }

}

$server = new RPCService();
$server->runTCP();
