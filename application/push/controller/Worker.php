<?php

namespace app\push\controller;

use think\worker\Server;
use Workerman\Lib\Timer;

class Worker extends Server
{
    protected $socket = 'websocket://192.168.1.23:2346';
    public $db;

    const HEARTBEAT_TIME = 25;

    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection, $data)
    {
//        $table=$this->db->name('jobs');
//        $data=$this->db->select();

        foreach($this->worker->connections as $con){
            dump($con->id);
        }
        $connection->lastMessageTime = time();

        $connection->send('我收到你的信息了');
    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {

    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {
        foreach($this->worker->connections as $con){
            dump($con->id);
        }
        $connection->send('您已断开连接！！');
    }

    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg)
    {
        echo "error $code $msg\n";
    }

    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {
        //实例化数据库
        $this->db=DB();
        //
        Timer::add(1, function()use($worker){
            $time_now = time();
            foreach($worker->connections as $connection) {
                // 有可能该connection还没收到过消息，则lastMessageTime设置为当前时间
                if (empty($connection->lastMessageTime)) {
                    echo 'timer';
                    $connection->lastMessageTime = $time_now;
                    continue;
                }
                // 上次通讯时间间隔大于心跳间隔，则认为客户端已经下线，关闭连接
                if ($time_now - $connection->lastMessageTime > self::HEARTBEAT_TIME) {
                    echo 'close';
                    $connection->close();
                }
            }
        });
    }
}