<?php
namespace allinpay\base;

use Exception;

/**
 * 支付异常类
 */
class PayException extends Exception{

	public function errorMessage()
	{
		return $this->getMessage();
	}
}