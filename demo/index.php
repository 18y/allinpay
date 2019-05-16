<?php 

require_once '../vendor/autoload.php';

header("Content-type: text/html; charset=utf-8");
ini_set('date.timezone','Asia/Shanghai');

use allinpay\AllinPay;

$config = [
	// 微信 appid
	'wechat_appid' => 'wxbd574f3d32b5687c',
	// 微信 Appsecret
	'wechat_secret' => '45c8e8b7e2256a3ce7d937f212a5bc4a',
	// 商户号
    'cusid' => '990440153996000',
    // 平台分配的APPID
    'appid' => "00000000",
    // 密钥
    'appkey' => '43df939f1e7f5c6909b3f4b63f893994',
    // 接收微信支付异步通知回调地址，通知url必须为直接可访问的url，不能携带参数
    'notify_url' => 'http://172.16.2.46:8080/vo-apidemo/OrderServlet'
];

$api = new AllinPay($config);
// 支付
$order = array();
// 交易金额，单位：分
$order["trxamt"] = "1";
// 订单号
$order["reqsn"] = "100";
// 支付标题，最大100个字节(50个中文字符)
$order["body"] = '商品名称';
// 支付备注，最大160个字节(80个中文字符)
$order["remark"] = '支付备注';
// $result = $api->WechatJsapiPay($order, function($data){
	// dump($data);
// });

// 查询订单
// $res = $api->QueryOrder(100, function($data){
// 	dump($data);
// });

// 支付回调
$res = $api->NotifyCallback(function($data){
	// return 
});

dump($res);
function dump($msg)
{
	echo "<pre/>";
	var_dump($msg);
}