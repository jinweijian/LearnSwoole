<?php

class WebSocket
{
    private $serv;
    private $redis;

    public function __construct() {
        $this->serv = new \swoole_websocket_server("0.0.0.0", 9501);
        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1',9504);
        $this->serv->set([
            'worker_num'      => 2, //开启2个worker进程
            'max_request'     => 4, //每个worker进程 max_request设置为4次
            'task_worker_num' => 4, //开启4个task进程
            'dispatch_mode'   => 4, //数据包分发策略 - IP分配
            'daemonize'       => false, //守护进程(true/false)
        ]);

        $this->serv->on('Start', [$this, 'onStart']);
        $this->serv->on('Connect', [$this, 'onConnect']);
        $this->serv->on('Open', [$this, 'onOpen']);
        $this->serv->on("Message", [$this, 'onMessage']);
        $this->serv->on("Close", [$this, 'onClose']);
        $this->serv->on("Task", [$this, 'onTask']);
        $this->serv->on("Finish", [$this, 'onFinish']);

        $this->serv->start();
    }

    public function onStart($serv) {
        echo "#### onStart ####".PHP_EOL;
        echo "SWOOLE ".SWOOLE_VERSION . " 服务已启动".PHP_EOL;
        echo "master_pid: {$serv->master_pid}".PHP_EOL;
        echo "manager_pid: {$serv->manager_pid}".PHP_EOL;
        echo "########".PHP_EOL.PHP_EOL;
    }

    public function onOpen($serv, $request) {
        echo "#### onOpen ####".PHP_EOL;
        echo "server: handshake success with fd{$request->fd}".PHP_EOL;
//        print_r($request);
        $info = json_decode($request->post,true);

//        $this->redis->zAdd($info['beid'],$info['testId'],$request->fd);

//        $serv->task($info);
        echo "########".PHP_EOL.PHP_EOL;
    }

    public function onConnect( $service, $fd,  $from_id ){

        echo "链接成功~这个是个connect\n";
        $param = [
            'type' => 'onConnect',
            'msg' => '这个是一个任务，我想看看他处理了没~',
            'fd' => $fd,
            'from_id' => $from_id,
        ];
        var_dump($param);
//        $service->task($param);
    }

    public function onTask($serv, $task_id, $from_id, $data) {
        echo "#### onTask ####".PHP_EOL;
        echo "#进程：{$serv->worker_id} 执行任务的信息: [PID={$serv->worker_pid}]: task_id={$task_id}".PHP_EOL;
        $msg = $data['msg'];
        var_dump($data);
        switch ($data['type']) {
            case 'login':
//                $this->redis->zAdd($data['beid'],$data['testId'],$data['fd']);
                $this->redis->zAdd('key',10037,'hhh');
//                $this->redis->zAdd(1049,10037,'aaa');
//                $this->redis->zAdd(1049,10037,'fff');
                break;
            case 'speak':
                break;
            case 'onConnect':
                break;
        }

//        print_r($serv);
//        foreach ($serv->connections as $fd) {
//            $connectionInfo = $serv->connection_info($fd);
//            if ($connectionInfo['websocket_status'] == 3) {
//                echo "这个是fd的数据：$fd";
//                $serv->push($fd, $msg); //长度最大不得超过2M
//            }
//        }
        var_dump($data);
        $info = $this->redis->zRange($data['beid'],$data['testId'],$data['testId']);

        print_r($info);
        foreach ( $info as $item ) {
            print_r($item);
            echo "这个是消息发送的数据：";
//                $serv->push($fd, $msg); //长度最大不得超过2M
        }
        $serv->finish($data);
        echo "########".PHP_EOL.PHP_EOL;
    }

    public function onMessage($serv, $frame) {
        echo "#### onMessage ####".PHP_EOL;

        echo "receive from fd{$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}".PHP_EOL;
        $data = json_decode($frame->data,true);
        $data['fd'] = $frame->fd;
        $serv->task($data);
        echo "########".PHP_EOL.PHP_EOL;
    }

    public function onFinish($serv,$task_id, $data) {
        echo "#### onFinish ####".PHP_EOL;
        echo "#### 任务进程：{$serv->worker_id} ####".PHP_EOL;
        echo "任务 {$task_id} 已完成".PHP_EOL;
        print_r($data);
//        echo "任务数据 {$data} 已完成".PHP_EOL;
        echo "########".PHP_EOL.PHP_EOL;
    }

    public function onClose($serv, $fd) {
        echo "#### onClose ####".PHP_EOL;
        echo "client {$fd} closed".PHP_EOL;
        echo "########".PHP_EOL.PHP_EOL;
    }
}

new WebSocket();