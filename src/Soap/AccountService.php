<?php

namespace HisInOneProxy\Soap;

use HisInOneProxy\DataModel\Address;
use HisInOneProxy\Log\Log;
use HisInOneProxy\Parser;

class AccountService extends SoapService
{

	/**
	 * AddressService constructor.
	 * @param Log $log
	 * @param SoapServiceRouter $soap_service_router
	 */
	public function __construct($log, $soap_service_router)
	{
		parent::__construct($log, $soap_service_router);
	}

	/**
	 * @param $account_id
	 * @return mixed
	 */
	public function readAccount($account_id)
	{
		$params = array(array('accountId' => $account_id));
		try{
			$response = $this->soap_service_router->getSoapClientAccountService()->__soapCall('readAccount', $params);
			print_r($response);
		}
		catch(\SoapFault $exception)
		{
			$this->log->error($exception->getMessage());
		}
		return null;
	}

}