<?php 

require_once '../vendor/autoload.php';

header("Content-type: text/html; charset=utf-8");
ini_set('date.timezone','Asia/Shanghai');

use allinpay\AllinPay;

$config = [
	// 微信 appid
	'wechat_appid' => 'wxbd574f3d31234567',
	// 微信 Appsecret
	'wechat_secret' => '45c8e8b7e22512345678911234567890',
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
/**
 * js 支付
 */
function jspay()
{
	global $api;
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
	// 支付回调，写你自己的
	$order["notify_url"] = 'http://172.16.2.46:8080/vo-apidemo/OrderServlet';
	// 发起支付
	$jsApiParameters = $api->WechatJsapiPay($order);
    if(false === $jsApiParameters)
    {
        $this->error = '订单生成失败!';
        return false;            
    }
    // 支付成功
    $success_url = "/success_url.php";
    // 支付失败跳转
    $error_url = "/error_url.php";
    echo <<<EOT
            <html>
            <head>
                <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
                <meta name="viewport" content="width=device-width, initial-scale=1"/> 
                <title>微信支付</title>
            </head>
            <body>
            </body>
            </html>
            <script>
            //调用微信JS api 支付
            function jsApiCall()
            {
                WeixinJSBridge.invoke(
                    'getBrandWCPayRequest',$jsApiParameters,
                    function(res){
                        WeixinJSBridge.log(res.err_msg);
                        if(res.err_msg == "get_brand_wcpay_request:ok" ) {
                            window.location.href = "$success_url";
                        } else {
                            alert('交易取消'+res.err_msg);
                            window.location.href = "$error_url";
                        }
                    }
                );
            }
             
            function callpay()
            {
                if (typeof WeixinJSBridge == "undefined"){
                    if( document.addEventListener ){
                        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                    }else if (document.attachEvent){
                        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
                        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                    }
                }else{
                    jsApiCall();
                }
            }
            callpay();
            </script>
EOT;
die;
}

/** 
 * 支付成功回调
 */
function notify()
{
	global $api;
	// 支付回调
	$res = $api->NotifyCallback(function($data){
		// 调试时可以将数据写入日志 log(json_encode($data))
		// 确认订单成功
		if(true)
		{
			return true;
		}else{
			return false;
		}
	});
}

/**
 * 查询订单
 * @param       string                   $reqsn 自己生成的订单号
 */
function query_order($reqsn)
{
	global $api;
	$api->QueryOrder($reqsn, function($data){
		// 查询结果
		dump($data);
	});
}

function dump($msg)
{
	echo "<pre/>";
	var_dump($msg);
}

dump($_SERVER['REQUEST_URI']);
die;
$baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);


// jspay();
// notify();