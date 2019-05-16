<?php
namespace allinpay\base;

class AppUtil{
	/**
	 * 将参数数组签名
	 */
	public static function SignArray(array $array,$appkey){
		$array['key'] = $appkey;// 将key放到数组中一起进行排序和组装
		ksort($array);
		$blankStr = self::ToUrlParams($array);
		$sign = md5($blankStr);
		return $sign;
	}
	
	/**
	 * 拼接签名字符串
	 */
	public static function ToUrlParams(array $array)
	{
		$buff = "";
		foreach ($array as $k => $v)
		{
			if($v != "" && !is_array($v)){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	
	/**
	 * 校验签名
	 * @param array 参数
	 * @param unknown_type appkey
	 */
	public static function ValidSign(array $array,$appkey){
		$sign = $array['sign'];
		unset($array['sign']);
		$array['key'] = $appkey;
		$mySign = self::SignArray($array, $appkey);
		return strtolower($sign) == strtolower($mySign);
	}
	
	/**
	 * 
	 * 产生随机字符串，不长于32位
	 * @param int $length
	 * @return 产生的随机字符串
	 */
	public static function GetNonceStr($length = 32) 
	{
		$chars = "abcdefghijklmnopqrstuvwxyz0123456789";  
		$str ="";
		for ( $i = 0; $i < $length; $i++ )  {  
			$str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);  
		} 
		return $str;
	}


	public static function CurlPost($url,$params, $json = false){
		$ch = curl_init();
		$this_header = array("content-type: application/x-www-form-urlencoded;charset=UTF-8");
		curl_setopt($ch,CURLOPT_HTTPHEADER,$this_header);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		 
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//如果不加验证,就设false,商户自行处理
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
		 
		$output = curl_exec($ch);
		curl_close($ch);
		if($json)
		{
			return json_decode($output,true);
		}
		return  $output;
	}

	
	public static function CurlGet($url, $json = false)
	{
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//运行curl，结果以jason形式返回
		$output = curl_exec($ch);
		curl_close($ch);
		if($json)
		{
			return json_decode($output,true);
		}
		return $output;
	}
	
}
?>