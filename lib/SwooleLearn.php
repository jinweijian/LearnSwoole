<?php


class SwooleLearn
{

    private $servce;

    public function __construct()
    {
        $this->servce = new \swoole_server("0.0.0.0", 9501);
        $this->servce->set([
            'worker_num'      => 10, //开启10个worker进程
            'max_request'     => 10000, //每个worker进程 max_request设置为4次
            'task_worker_num' => 100, //开启4个task进程
            'dispatch_mode'   => 4, //数据包分发策略 - IP分配
            'daemonize'       => false, //守护进程(true/false)
        ]);

        // 监听 - 服务开始
        $this->servce->on('start',[$this,'onStart']);
        // 监听 - 链接成功
        $this->servce->on('connect',[$this,'onConnect']);
        // 监听 - 有请求数据时
        $this->servce->on('receive',[$this,'onReceive']);
        // 监听 - 链接关闭
        $this->servce->on('close',[$this,'onClose']);
        // 监听 - swoole任务处理
        $this->servce->on('task',[$this,'onTask']);
        // 监听 - swoole任务回调
        $this->servce->on('finish',[$this,'onFinish']);
        // 开启swoole
        $this->servce->start();
    }

    public function onStart($service){
        echo "开始我的第一个swoole\n";
        $param = [
            'type' => 'echo',
            'msg' => '这个是一个任务，我想看看他处理了没~'
        ];
//        $service->task(json_encode($param));
    }

    public function onConnect( $service,int $fd, int $from_id ){
        echo "链接成功~这个是个connect\n";
        $param = [
            'type' => 'onConnect',
            'msg' => '这个是一个任务，我想看看他处理了没~',
            'fd' => $fd
        ];
        $service->task(json_encode($param));
    }

    public function onReceive( $service, $fd, $from_id, $data ){
        echo "有请求的数据~这个是个onReceive\n";
        print_r($fd);
        print_r($data);

        $param = [
            'type' => 'onReceive',
            'msg' => '用户有请求的数据',
            'fd' => $fd
        ];
        $service->task(json_encode($param));
    }

    public function onClose( $service,int $fd, int $from_id ){
        echo "链接关闭~onClose\n";
        $param = [
            'type' => 'onClose',
            'msg' => '这个是一个任务，我想看看他处理了没~',
            'fd' => $fd
        ];
        $service->task(json_encode($param));
    }

    public function onTask( $service, int $task_id, int $from_id, string $data){
        echo "开始处理任务~onTask\n";
        echo "#{$task_id} onTask: [{$task_id}]: from_id={$from_id}".PHP_EOL;
        $data = json_decode($data,true);
        var_dump($data);

        $service->send( $data['msg'] , "这个数据来自任务ID： {$task_id}");
        return "这个任务结束，并返回 {$task_id}";
    }

    public function onFinish( $service, int $task_id, string $data){
        echo "任务结束了哦~onFinish\n";
        echo "#{$task_id} onTask: [{$task_id}]".PHP_EOL;
        $data = json_decode($data,true);
        var_dump($data);
        echo "任务结束了 - ID：$task_id";
    }
}
new SwooleLearn();