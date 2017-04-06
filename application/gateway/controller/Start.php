<?php
namespace app\gateway\controller;

use think\Request;

class Start
{

    public function index()
    {
        $url=url('/bind');
        return view(':index',[
            'url'=>$url,
        ]);
    }

    public function bind(Request $request)
    {
        $client_id = $request->param('client_id');
        vendor('workerman.gateway.Gateway');
        \GatewayClient\Gateway::$registerAddress = '127.0.0.1:1238';
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值
//        \GatewayClient\Gateway::$registerAddress = '127.0.0.1:1236';

        // 假设用户已经登录，用户uid和群组id在session中
        $uid      = 1;
        $group_id = 'group';
        // client_id与uid绑定
        \GatewayClient\Gateway::bindUid($client_id, $uid);
        // 加入某个群组（可调用多次加入多个群组）
        \GatewayClient\Gateway::joinGroup($client_id, $group_id);
    }


    public function send_message()
    {
        $uid      = 1;
        $message    =json_encode(array(
            'data' => "我已收到消息"
        ));
        vendor('workerman.gateway.Gateway');
        // 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值
        \GatewayClient\Gateway::$registerAddress = '127.0.0.1:1238';

        // 向任意uid的网站页面发送数据
        \GatewayClient\Gateway::sendToUid($uid, $message);
        // 向任意群组的网站页面发送数据
//        \GatewayClient\Gateway::sendToGroup($group, $message);
    }
}