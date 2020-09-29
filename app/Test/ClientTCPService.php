<?php


namespace App\Test;


class ClientTCPService
{
    protected $urlInfo = [];

    public function __construct($url)
    {
        // 解析URL
        $this->urlInfo = parse_url($url);
        if ( empty($this->urlInfo) ) {
            exit("{$url} error \n");
        }
    }

    public function __call($name, $arguments)
    {
        $client = stream_socket_client("tcp://{$this->urlInfo['host']}:{$this->urlInfo['port']}",$errno, $errstr);
        if (!$client) {
            exit("{$errno} : {$errstr} \n");
        }
        //传递调用的类名
        $class = basename($this->urlInfo['path']);
        $proto = "Rpc-Class: {$class};" . PHP_EOL;
        //传递调用的方法名
        $proto .= "Rpc-Method: {$name};" . PHP_EOL;
        //传递方法的参数
        $params = json_encode($arguments);
        $proto .= "Rpc-Params: {$params};" . PHP_EOL;
        //向服务端发送我们自定义的协议数据
        fwrite($client, $proto);
        //读取服务端传来的数据
        $data = fread($client, 2048);
        //关闭客户端
        fclose($client);
        return $data;
    }
}

$client = new ClientTCPService('http://127.0.0.1:9501/Test');
echo $client->testFunction() ."\n";
echo $client->testFunction22222(array('name' => 'Test', 'age' => 27))." \n";
