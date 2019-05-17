<?php
namespace allinpay\base;

use allinpay\base\AppUtil;

/**
 * 获取微信openid
 */
class WechatJsapi{

	private $config = array(); 
	/**
	 * 静默获取微信openid
	 * @author 18y
	 * @anotherdate 2019-05-15T16:53:40+0800
	 */
	public function GetOpenid($config)
	{
		$this->config = $config;
		//通过code获得openid
		if (!isset($_GET['code'])){
			//触发微信返回code码
			$baseUrl = urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
			$url = $this->CreateOauthUrlForCode($baseUrl);
			Header("Location: $url");
			exit();
		} else {
			//获取code码，以获取openid
		    $code = $_GET['code'];
		    $url = $this->CreateOauthUrlForOpenid($code);
			$result =  AppUtil::CurlGet($url, true);
			if(isset($result["errcode"]) && isset($result["errcode"]) == 40163)
			{
				return false;
			}
			return $result['openid'];
		}
	}

	/**
	 * 
	 * 构造获取code的url连接
	 * @param string $redirectUrl 微信服务器回跳的url，需要url编码
	 * 
	 * @return 返回构造好的url
	 */
	private function CreateOauthUrlForCode($redirectUrl)
	{
		$urlObj["appid"] = $this->config["wechat_appid"];
		$urlObj["redirect_uri"] = "$redirectUrl";
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = "snsapi_base";
		$urlObj["state"] = "STATE"."#wechat_redirect";
		$bizString = AppUtil::ToUrlParams($urlObj);
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
	}

	/**
	 * 
	 * 构造获取open和access_toke的url地址
	 * @param string $code，微信跳转带回的code
	 * 
	 * @return 请求的url
	 */
	private function CreateOauthUrlForOpenid($code)
	{
		$urlObj["appid"] = $this->config["wechat_appid"];
		$urlObj["secret"] = $this->config["wechat_secret"];
		$urlObj["code"] = $code;
		$urlObj["grant_type"] = "authorization_code";
		$bizString = AppUtil::ToUrlParams($urlObj);
		return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
	}
}