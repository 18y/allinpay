<?php 
namespace allinpay;
use allinpay\base\PayException;
use allinpay\base\AppUtil;
use allinpay\base\WechatJsapi;

class AllinPay
{	
	private $config = array(
		// 接口地址
		'apiurl' => 'https://vsp.allinpay.com/apiweb/unitorder',
		// 接口默认版本
		'version' => 11,
		// 指定不能使用信用卡支付, 不填时不限制
		'limit_pay' => 'no_credit',
		// 签名方式(RSA|MD%)，默认MD5
		'signtype' => 'MD5',
		// 支付有效期，单位：分
		'validtime' => '5'
	);

	public function __construct($config = [])
	{
		$this->config = array_merge($this->config, $config);
		// 接口地址
		if(empty($this->config["apiurl"]))
		{
			throw new PayException("apiurl 参数错误");
		}
		// 商户号
		if(empty($this->config["cusid"]))
		{
			throw new PayException("cusid 参数错误");
		} 
		// 应用ID
		if(empty($this->config["appid"]))
		{
			throw new PayException("appid 参数错误");
		}
		// 微信 appid
		if(empty($this->config["wechat_appid"]))
		{
			throw new PayException("微信 appid 参数错误");
		}
		// 微信 appsecret
		if(empty($this->config["wechat_secret"]))
		{
			throw new PayException("微信 appid 参数错误");
		}
	}
	/**
	 * 微信jsapi支付
	 * @author 18y
	 * @anotherdate 2019-05-15T14:37:31+0800
	 */
	public function WechatJsapiPay($order, $callback = false)
	{
		// 金额
		if(empty($order["trxamt"]) || $order["trxamt"] <= 0)
		{
			throw new PayException("支付金额有误");
		}
		// 订单号
		if(empty($order["reqsn"]))
		{
			throw new PayException("订单号有误");
		}else{
			if(strlen($order["reqsn"]) > 32)
			{
				throw new PayException("订单号长度有误");
			}
		}
		$jsapi = new WechatJsapi;
		$openid = $jsapi->GetOpenid($this->config);
		$trxamt = floatval($order["trxamt"]) * 100;
		$params = array();
		$params["cusid"] = $this->config["cusid"];
	    $params["appid"] = $this->config["appid"];
	    $params["version"] = $this->config["version"];
	    $params["trxamt"] = $trxamt;
	    $params["reqsn"] = $order["reqsn"];//订单号,自行生成
	    // 支付方式，微信JS支付
	    $params["paytype"] = "W02";
	    $params["randomstr"] = AppUtil::GetNonceStr();
	    $params["body"] = !empty($order["body"]) ? $order["body"] : '';
	    $params["remark"] = !empty($order["remark"]) ? $order["remark"] : '';
	    // JS支付时使用，微信支付-用户的微信openid
	    $params["acct"] = $openid;
	    $params["limit_pay"] = $this->config["limit_pay"];
		$params["idno"] = "";
		$params["truename"] = "";
		$params["asinfo"] = "";
        $params["notify_url"] = $this->config["notify_url"];
	    $params["sign"] = AppUtil::SignArray($params, $this->config["appkey"]);//签名
	    $paramsStr = AppUtil::ToUrlParams($params);
	    $url = $this->config["apiurl"] . "/pay";
	    $res = AppUtil::CurlPost($url, $paramsStr, true);
    	// 下单接口请求失败
    	if($res["retcode"] == "FAIL")
    	{
    		// 下单失败记录
			throw new PayException("下单失败：".$res["retmsg"]);
    	}
	    if(AppUtil::validSign($res, $this->config["appkey"])){
	    	if(false !== $callback)
	    	{
	    		call_user_func($callback, $res);
	    	}
	    	// 请求成功
	    	if($res["retcode"] == "SUCCESS")
	    	{
	    		// 交易成功
	    		if($res["trxstatus"] == '0000')
	    		{
	    			// 看看 payinfo 是啥
	    			return true;
	    		}
	    	}
	    	return false;
	    }
	}

	/**
	 * 支付成功通知
	 * @author 18y
	 * @anotherdate 2019-05-16T10:53:17+0800
	 */
	public function NotifyCallback($callback = false)
	{
		if(!empty($_POST))
		{
			$params = array();
			foreach($_POST as $key=>$val) {//动态遍历获取所有收到的参数,此步非常关键,因为收银宝以后可能会加字段,动态获取可以兼容由于收银宝加字段而引起的签名异常
				$params[$key] = $val;
			}
			if(count($params)<1){//如果参数为空,则不进行处理
				echo "error";
				exit();
			}
			if(AppUtil::ValidSign($params, $this->config["appkey"])){//验签成功
				//此处进行业务逻辑处理
	    		$msg = '验证签名成功';
		    	if(false !== $callback)
		    	{
		    		// 确认订单
		    		$res = call_user_func($callback, $params);
		            if($res["code"] == 1)
		            {
		            	$msg = $res["msg"];
		            	echo "success";
		            }
		            if(empty($res) || $res['code'] == 0)
		            {
						$msg = "订单确认失败";
						echo "error";
		            }
		    	}else{
					echo "success";
		    	}
			}
			else{
				// 签名认证错误
				echo "error";
			}
		}

	}


	/**
	 * 订单查询
	 * @author 18y
	 * @anotherdate 2019-05-16T09:57:46+0800
	 * @param       [type]                   $reqsn 商户订单号
	 * @return      [type]                          [description]
	 */
	public function QueryOrder($reqsn, $callback = false)
	{
		// 订单号
		if(empty($reqsn))
		{
			throw new PayException("订单号有误");
		}else{
			if(strlen($reqsn) > 32)
			{
				throw new PayException("订单号长度有误");
			}
		}
		$params = array();
		$params["cusid"] = $this->config["cusid"];
	    $params["appid"] = $this->config["appid"];
	    $params["version"] = $this->config["version"];
	    $params["reqsn"] = $reqsn;
	    $params["randomstr"] = AppUtil::GetNonceStr();//
	    $params["sign"] = AppUtil::SignArray($params,$this->config["appkey"]);//签名
	    $paramsStr = AppUtil::ToUrlParams($params);
	    $url = $this->config["apiurl"] . "/query";
	    $res = AppUtil::CurlPost($url, $paramsStr, true);
	    if(AppUtil::validSign($res, $this->config["appkey"])){
	    	if(false !== $callback)
	    	{
	    		call_user_func($callback, $res);
	    	}
	    	// 请求成功
	    	if($res["retcode"] == "SUCCESS")
	    	{
	    		// 交易成功
	    		if($res["trxstatus"] == '0000')
	    		{
	    			// 看看 payinfo 是啥
	    			return true;
	    		}
	    	}
	    	return false;
	    }
	}
}