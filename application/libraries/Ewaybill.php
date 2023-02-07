<?php defined('BASEPATH') OR exit('No direct script access allowed');
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Client;

class Ewaybill {
	
	private static $_auth_url;
	private static $_ci;
	private static $_eway_url;
	private static $_get;
	private static $_client;
	private static $_settings;
	private static $_credential;
	private static $_crd = [];
	private static $_session;
	private static $_kaabar;
	private static $_vehicle;
	private static $_count;
	private static $_company;
	
	public function __construct() 
	{
		self::$_ci    =& get_instance();

		self::$_client = new Client( array( 
			'curl'   => array( CURLOPT_SSL_VERIFYPEER => false ),
			'verify' => false
		));

		self::$_count = 0;
		if(self::$_ci->session->credential)
			self::$_company = self::$_ci->session->credential['company_id'];
		self::$_credential = self::$_ci->settings->getEwbCredential();

		foreach (self::$_credential as $key => $value) {
			$name = str_replace('_test', '', $value['name']);
			self::$_crd[$name] = $value['value'];
		}

		self::$_auth_url = self::$_crd['eway_auth_url'];
		self::$_eway_url = self::$_crd['eway_ewaybill_url'];
	}

	/**
	* API To Generate EwayBill Token
	*/
	public static function generateToken($crd = null) {
		
		ini_set('memory_limit', '-1');
		
		if($crd){
			self::$_crd = $crd;
			self::$_auth_url = $crd['eway_auth_url'];

		}
		
		self::$_get['action'] = 'ACCESSTOKEN';
		self::$_get['aspid'] = self::$_crd['eway_asp_id'];
		self::$_get['password'] = self::$_crd['eway_password'];
		self::$_get['Gstin'] = self::$_crd['eway_gstin'];
		self::$_get['user_name'] = self::$_crd['eway_username'];
		self::$_get['eInvPwd'] = self::$_crd['eway_ewbpwd'];

		//$gstCall = self::$_ci->kaabar->getField('companies', self::$_company, 'id', 'gst_call');
		
		try {

			$response = self::$_client->get(self::$_auth_url.http_build_query(self::$_get));
			/// Update GST CALL
			//self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

			$result = $response->getBody()->read(1024);
			$result = json_decode($result, true);
			self::$_ci->session->set_userdata('authtoken', $result['authtoken']);

			return $result['authtoken']; 

		}catch (GuzzleHttp\Exception\BadResponseException $e) {

			/// Update GST CALL
			//self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

			if ($e->hasResponse()){
		    	$response = $e->getResponse();
		    	$errors = json_decode($response->getBody())->error->error_cd;
		    	$errorsCodes = preg_replace("/[^0-9]/", "", explode(', ', $errors));
		    	$result = ['code' => $response->getStatusCode(), 'errors' => $errorsCodes];
		    	return $result;
		    }
		}
	}

	/**
	* API To Fetch EwayBill Details Using EWB NO
	*/
	public static function generate($data = null) {
		ini_set('memory_limit', '-1');

		$token = self::$_ci->session->userdata('authtoken');

		if(! isset($token)){
			$token = self::generateToken();
		}

		self::$_get['action'] = 'GetEwayBill';
		self::$_get['aspid'] = self::$_crd['eway_asp_id'];
		self::$_get['password'] = self::$_crd['eway_password'];
		self::$_get['gstin'] = self::$_crd['eway_gstin'];
		self::$_get['username'] = self::$_crd['eway_username'];
		self::$_get['authtoken'] = $token;
		self::$_get['ewbNo'] = $data;

		//self::$_count;
		//$gstCall = self::$_ci->kaabar->getField('companies', self::$_company, 'id', 'gst_call');
		
		return self::$_get;
		exit;

		try {

			$response = self::$_client->get(self::$_eway_url.http_build_query(self::$_get));	
			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

            $value = json_decode($response->getBody()->read(1024*1024*2), true);

            $ewayBillDate = str_replace("/", "-", $value['ewayBillDate']);
			$docDate = str_replace("/", "-", $value['docDate']);
			$validUpto = str_replace("/", "-", $value['validUpto']);

			$ewbVehicles = $value['VehiclListDetails'];

			$value['itemList'] = serialize($value['itemList']);
			$value['VehiclListDetails'] = serialize($value['VehiclListDetails']);

			$value['ewayBillDate'] = date('Y-m-d H:i:s', strtotime($ewayBillDate));
			$value['docDate'] = date('Y-m-d', strtotime($docDate));
			$value['validUpto'] = date('Y-m-d H:i:s', strtotime($validUpto));

			$value['transitDays'] = get_days_kms($value['actualDist']);
			$value['remainDist'] = $value['actualDist'];
			$value['remainDays'] = get_days_kms($value['actualDist']);
			
			$found = self::$_ci->kaabar->getField('ewaybills', $value['ewbNo'], 'ewbNo', 'id');	

			if(!$found){
				$id = self::$_ci->kaabar->save('ewaybills', $value);

				foreach ($ewbVehicles as $k => $v) {
					
					$eDate = str_replace("/", "-", $v['enteredDate']);
					$enteredDate = date('Y-m-d H:i:s', strtotime($eDate));
					
					$tDocDate = str_replace("/", "-", $v['transDocDate']);
					$transDocDate = date('Y-m-d H:i:s', strtotime($tDocDate));

					$ewbmaster = [
						'ewb_id' => $id,
						'ewbNo' => $value['ewbNo'],
						'ewayBillDate' => $value['ewayBillDate'],
						'genMode' => $value['genMode'],
						'userGstin' => $value['userGstin'],
						'supplyType' => $value['supplyType'],
						'subSupplyType' => $value['subSupplyType'],
						'docType' => $value['docType'],
						'docNo' => $value['docNo'],
						'docDate' => $value['docDate'],
						'fromGstin' => $value['fromGstin'],
						'fromTrdName' => $value['fromTrdName'],
						'fromAddr1' => $value['fromAddr1'],
						'fromAddr2' => $value['fromAddr2'],
						'fromPlace' => $value['fromPlace'],
						'fromPincode' => $value['fromPincode'],
						'fromStateCode' => $value['fromStateCode'],
						'toGstin' => $value['toGstin'],
						'toTrdName' => $value['toTrdName'],
						'toAddr1' => $value['toAddr1'],
						'toAddr2' => $value['toAddr2'],
						'toPlace' => $value['toPlace'],
						'toPincode' => $value['toPincode'],
						'toStateCode' => $value['toStateCode'],
						'totalValue' => $value['totalValue'],
						'totInvValue' => $value['totInvValue'],
						'cgstValue' => $value['cgstValue'],
						'sgstValue' => $value['sgstValue'],
						'igstValue' => $value['igstValue'],
						'cessValue' => $value['cessValue'],
						'transporterId' => $value['transporterId'],
						'transporterName' => $value['transporterName'],
						'status' => $value['status'],
						'actualDist' => $value['actualDist'],
						'noValidDays' => $value['noValidDays'],
						'transitDays' => $value['transitDays'],
						'remainDist' => $value['remainDist'],
						'remainDays' => $value['remainDays'],
						'validUpto' => $value['validUpto'],
						'extendedTimes' => $value['extendedTimes'],
						'rejectStatus' => $value['rejectStatus'],
						'vehicleType' => $value['vehicleType'],
						'actFromStateCode' => $value['actFromStateCode'],
						'actToStateCode' => $value['actToStateCode'],
						'transactionType' => $value['transactionType'],
						'otherValue' => $value['otherValue'],
						'cessNonAdvolValue' => $value['cessNonAdvolValue'],
						'child_updMode' => $v['updMode'],
						'child_vehicleNo' => $v['vehicleNo'],
						'child_fromPlace' => $v['fromPlace'],
						'child_fromState' => $v['fromState'],
						'child_tripshtNo' => $v['tripshtNo'],
						'child_userGSTINTransin' => $v['userGSTINTransin'],
						'child_enteredDate' => $enteredDate,
						'child_transMode' => $v['transMode'],
						'child_transDocNo' => $v['transDocNo'],
						'child_transDocDate' => $transDocDate,
						'child_groupNo' => $v['groupNo'],
					];
					self::$_ci->kaabar->save('ewbmasters', $ewbmaster);
				}

			}
			else
			{
				self::$_ci->kaabar->save('ewaybills', $value, ['id' => $found]);
			}
			
			/////////////////////////// UPDATE VEHICLES ////////////////////////////////
			$result = ['code' => $response->getStatusCode(), 'result' => $value];
			return $result;

			/////////////////////////// END UPDATE VEHICLES /////////////////////////////
			//return true;

		}catch (GuzzleHttp\Exception\BadResponseException $e) {

			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);
            	
            if ($e->hasResponse()){
		    	$response = $e->getResponse();
		    	$errors = json_decode($response->getBody())->error->error_cd;
		    	$errorsCodes = preg_replace("/[^0-9]/", "", explode(', ', $errors));
		    	$result = ['code' => $response->getStatusCode(), 'errors' => $errorsCodes];
		    	return $result;
		    }
		}
	}

	/**
	* API To Fetch EwayBill Details Using EWB NO
	*/
	public static function GetEwayBill($ewbNo = null) {
		ini_set('memory_limit', '-1');

		$token = self::$_ci->session->userdata('authtoken');

		if(! isset($token)){
			$token = self::generateToken();
		}

		self::$_get['action'] = 'GetEwayBill';
		self::$_get['aspid'] = self::$_crd['eway_asp_id'];
		self::$_get['password'] = self::$_crd['eway_password'];
		self::$_get['gstin'] = self::$_crd['eway_gstin'];
		self::$_get['username'] = self::$_crd['eway_username'];
		self::$_get['authtoken'] = $token;
		self::$_get['ewbNo'] = $ewbNo;

		//self::$_count;
		$gstCall = self::$_ci->kaabar->getField('companies', self::$_company, 'id', 'gst_call');
		
		try {

			$response = self::$_client->get(self::$_eway_url.http_build_query(self::$_get));	
			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

            $value = json_decode($response->getBody()->read(1024*1024*2), true);

            $ewayBillDate = str_replace("/", "-", $value['ewayBillDate']);
			$docDate = str_replace("/", "-", $value['docDate']);
			$validUpto = str_replace("/", "-", $value['validUpto']);

			$ewbVehicles = $value['VehiclListDetails'];

			$value['itemList'] = serialize($value['itemList']);
			$value['VehiclListDetails'] = serialize($value['VehiclListDetails']);

			$value['ewayBillDate'] = date('Y-m-d H:i:s', strtotime($ewayBillDate));
			$value['docDate'] = date('Y-m-d', strtotime($docDate));
			$value['validUpto'] = date('Y-m-d H:i:s', strtotime($validUpto));

			$value['transitDays'] = get_days_kms($value['actualDist']);
			$value['remainDist'] = $value['actualDist'];
			$value['remainDays'] = get_days_kms($value['actualDist']);
			
			$found = self::$_ci->kaabar->getField('ewaybills', $value['ewbNo'], 'ewbNo', 'id');	

			if(!$found){
				$id = self::$_ci->kaabar->save('ewaybills', $value);

				foreach ($ewbVehicles as $k => $v) {
					
					$eDate = str_replace("/", "-", $v['enteredDate']);
					$enteredDate = date('Y-m-d H:i:s', strtotime($eDate));
					
					$tDocDate = str_replace("/", "-", $v['transDocDate']);
					$transDocDate = date('Y-m-d H:i:s', strtotime($tDocDate));

					$ewbmaster = [
						'ewb_id' => $id,
						'ewbNo' => $value['ewbNo'],
						'ewayBillDate' => $value['ewayBillDate'],
						'genMode' => $value['genMode'],
						'userGstin' => $value['userGstin'],
						'supplyType' => $value['supplyType'],
						'subSupplyType' => $value['subSupplyType'],
						'docType' => $value['docType'],
						'docNo' => $value['docNo'],
						'docDate' => $value['docDate'],
						'fromGstin' => $value['fromGstin'],
						'fromTrdName' => $value['fromTrdName'],
						'fromAddr1' => $value['fromAddr1'],
						'fromAddr2' => $value['fromAddr2'],
						'fromPlace' => $value['fromPlace'],
						'fromPincode' => $value['fromPincode'],
						'fromStateCode' => $value['fromStateCode'],
						'toGstin' => $value['toGstin'],
						'toTrdName' => $value['toTrdName'],
						'toAddr1' => $value['toAddr1'],
						'toAddr2' => $value['toAddr2'],
						'toPlace' => $value['toPlace'],
						'toPincode' => $value['toPincode'],
						'toStateCode' => $value['toStateCode'],
						'totalValue' => $value['totalValue'],
						'totInvValue' => $value['totInvValue'],
						'cgstValue' => $value['cgstValue'],
						'sgstValue' => $value['sgstValue'],
						'igstValue' => $value['igstValue'],
						'cessValue' => $value['cessValue'],
						'transporterId' => $value['transporterId'],
						'transporterName' => $value['transporterName'],
						'status' => $value['status'],
						'actualDist' => $value['actualDist'],
						'noValidDays' => $value['noValidDays'],
						'transitDays' => $value['transitDays'],
						'remainDist' => $value['remainDist'],
						'remainDays' => $value['remainDays'],
						'validUpto' => $value['validUpto'],
						'extendedTimes' => $value['extendedTimes'],
						'rejectStatus' => $value['rejectStatus'],
						'vehicleType' => $value['vehicleType'],
						'actFromStateCode' => $value['actFromStateCode'],
						'actToStateCode' => $value['actToStateCode'],
						'transactionType' => $value['transactionType'],
						'otherValue' => $value['otherValue'],
						'cessNonAdvolValue' => $value['cessNonAdvolValue'],
						'child_updMode' => $v['updMode'],
						'child_vehicleNo' => $v['vehicleNo'],
						'child_fromPlace' => $v['fromPlace'],
						'child_fromState' => $v['fromState'],
						'child_tripshtNo' => $v['tripshtNo'],
						'child_userGSTINTransin' => $v['userGSTINTransin'],
						'child_enteredDate' => $enteredDate,
						'child_transMode' => $v['transMode'],
						'child_transDocNo' => $v['transDocNo'],
						'child_transDocDate' => $transDocDate,
						'child_groupNo' => $v['groupNo'],
					];
					self::$_ci->kaabar->save('ewbmasters', $ewbmaster);
				}

			}
			else
			{
				self::$_ci->kaabar->save('ewaybills', $value, ['id' => $found]);
			}
			
			/////////////////////////// UPDATE VEHICLES ////////////////////////////////
			$result = ['code' => $response->getStatusCode(), 'result' => $value];
			return $result;

			/////////////////////////// END UPDATE VEHICLES /////////////////////////////
			//return true;

		}catch (GuzzleHttp\Exception\BadResponseException $e) {

			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);
            	
            if ($e->hasResponse()){
		    	$response = $e->getResponse();
		    	$errors = json_decode($response->getBody())->error->error_cd;
		    	$errorsCodes = preg_replace("/[^0-9]/", "", explode(', ', $errors));
		    	$result = ['code' => $response->getStatusCode(), 'errors' => $errorsCodes];
		    	return $result;
		    }
		}
	}

	/**
	* API To Fetch All Ewaybills By Date
	*/
	public static function GetEwayBillsByDate($date = null) {
		
		ini_set('memory_limit', '-1');

		$token = self::$_ci->session->userdata('authtoken');
		if(!$token){
			$token = self::generateToken();
		}
			
		self::$_get['action'] = 'GetEwayBillsByDate';
		self::$_get['aspid'] = self::$_crd['eway_asp_id'];
		self::$_get['password'] = self::$_crd['eway_password'];
		self::$_get['gstin'] = self::$_crd['eway_gstin'];
		self::$_get['username'] = self::$_crd['eway_username'];
		self::$_get['authtoken'] = $token;
		self::$_get['date'] = $date;

		$gstCall = self::$_ci->kaabar->getField('companies', self::$_company, 'id', 'gst_call');


		try {

			$response = self::$_client->get(self::$_eway_url.http_build_query(self::$_get));	
			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

            $rows = json_decode($response->getBody()->read(1024*1024*2), true);

            foreach ($rows as $key => $value) {

            	$ewbDate = str_replace("/", "-", $value['ewbDate']);
				$docDate = str_replace("/", "-", $value['docDate']);
				$validUpto = str_replace("/", "-", $value['validUpto']);

				$value['ewbDate'] = date('Y-m-d H:i:s', strtotime($ewbDate));
				$value['docDate'] = date('Y-m-d', strtotime($docDate));
				$value['validUpto'] = date('Y-m-d H:i:s', strtotime($validUpto));

				$found = self::$_ci->kaabar->getField('ewaybillsbydates', $value['ewbNo'], 'ewbNo', 'id');
				$ewbCheck = self::$_ci->kaabar->getField('ewaybills', $value['ewbNo'], 'ewbNo', 'id');	

				if($found && $ewbCheck)
					continue;

				if(!$found)
					self::$_ci->kaabar->save('ewaybillsbydates', $value);
				else
					self::$_ci->kaabar->save('ewaybillsbydates', $value, ['id' => $found]);
				
				Ewaybill::GetEwayBill($value['ewbNo']);
				
			}

			$result = ['code' => $response->getStatusCode(), 'result' => $value];
			return $result;
			
		}catch (GuzzleHttp\Exception\BadResponseException $e) {

			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

			if ($e->hasResponse()){
		    	$response = $e->getResponse();
		    	$errors = json_decode($response->getBody())->error->error_cd;
		    	$errorsCodes = preg_replace("/[^0-9]/", "", explode(', ', $errors));
		    	$result = ['code' => $response->getStatusCode(), 'errors' => $errorsCodes];
		    	return $result;
		    }
		}
	}

	/**
	* API To Fetch All Ewaybills By Date
	*/
	public static function GetEwayBillsofOtherParty($date = null) {
		
		ini_set('memory_limit', '-1');

		$token = self::$_session->get('authtoken');
		if(!$token){
			$token = self::generateToken();
		}
			
		self::$_get['action'] = self::$_router->methodName();
		self::$_get['aspid'] = self::$_crd['eway_asp_id'];
		self::$_get['password'] = self::$_crd['eway_password'];
		self::$_get['gstin'] = self::$_crd['eway_gstin'];
		self::$_get['username'] = self::$_crd['eway_username'];
		self::$_get['authtoken'] = $token;
		self::$_get['date'] = $date;

		$gstCall = self::$_kaabar->getField('companies', self::$_company, 'id', 'gst_call');
		
		try {

			$response = self::$_client->get(self::$_eway_url.http_build_query(self::$_get));	
			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

            $rows = json_decode($response->getBody()->read(1024*1024*2), true);

            foreach ($rows as $key => $value) {

            	$ewbDate = str_replace("/", "-", $value['ewbDate']);
				$docDate = str_replace("/", "-", $value['docDate']);
				$validUpto = str_replace("/", "-", $value['validUpto']);

				$value['ewbDate'] = date('Y-m-d H:i:s', strtotime($ewbDate));
				$value['docDate'] = date('Y-m-d', strtotime($docDate));
				$value['validUpto'] = date('Y-m-d H:i:s', strtotime($validUpto));

				$found = self::$_kaabar->getField('ewaybillsbydates', $value['ewbNo'], 'ewbNo', 'id');
				$ewbCheck = self::$_kaabar->getField('ewaybills', $value['ewbNo'], 'ewbNo', 'id');	

				if($found && $ewbCheck)
					continue;

				if(!$found)
					self::$_ci->kaabar->save('ewaybillsbydates', $value);
				else
					self::$_ci->kaabar->save('ewaybillsbydates', $value, ['id' => $found]);
				
				Ewaybill::GetEwayBill($value['ewbNo']);
				
			}
			$result = ['code' => $response->getStatusCode(), 'result' => $value];
			return $result;
			

		}catch (GuzzleHttp\Exception\BadResponseException $e) {

			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

			if ($e->hasResponse()){
		    	$response = $e->getResponse();
		    	$errors = json_decode($response->getBody())->error->error_cd;
		    	$errorsCodes = preg_replace("/[^0-9]/", "", explode(', ', $errors));
		    	$result = ['code' => $response->getStatusCode(), 'errors' => $errorsCodes];
		    	return $result;
		    }
		}
	}


	/**
	* API To Fetch All Ewaybills By Transporter
	*/
	public static function GetEwayBillsForTransporter($date = null, $crd = null) {
		
		ini_set('memory_limit', '-1');

		$token = self::$_session->get('authtoken');
		if(!$token){
			$token = self::generateToken();
		}
			
		self::$_get['action'] = self::$_router->methodName();
		self::$_get['aspid'] = self::$_crd['eway_asp_id'];
		self::$_get['password'] = self::$_crd['eway_password'];
		self::$_get['gstin'] = self::$_crd['eway_gstin'];
		self::$_get['username'] = self::$_crd['eway_username'];
		self::$_get['authtoken'] = $token;
		self::$_get['date'] = $date;

		$gstCall = self::$_kaabar->getField('companies', self::$_company, 'id', 'gst_call');
		
		try {

			$response = self::$_client->get(self::$_eway_url.http_build_query(self::$_get));	
			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

            $rows = json_decode($response->getBody()->read(1024*1024*2), true);

            foreach ($rows as $key => $value) {

            	$ewbDate = str_replace("/", "-", $value['ewbDate']);
				$docDate = str_replace("/", "-", $value['docDate']);
				$validUpto = str_replace("/", "-", $value['validUpto']);

				$value['ewbDate'] = date('Y-m-d H:i:s', strtotime($ewbDate));
				$value['docDate'] = date('Y-m-d', strtotime($docDate));
				$value['validUpto'] = date('Y-m-d H:i:s', strtotime($validUpto));

				$found = self::$_kaabar->getField('ewaybillsbydates', $value['ewbNo'], 'ewbNo', 'id');
				$ewbCheck = self::$_kaabar->getField('ewaybills', $value['ewbNo'], 'ewbNo', 'id');	

				if($found && $ewbCheck)
					continue;

				if(!$found)
					self::$_ci->kaabar->save('ewaybillsbydates', $value);
				else
					self::$_ci->kaabar->kbrsave('ewaybillsbydates', $value, ['id' => $found]);
				
				Ewaybill::GetEwayBill($value['ewbNo']);
				
			}
			$result = ['code' => $response->getStatusCode(), 'result' => $value];
			return $result;
			

		}catch (GuzzleHttp\Exception\BadResponseException $e) {

			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

			if ($e->hasResponse()){
		    	$response = $e->getResponse();
		    	$errors = json_decode($response->getBody())->error->error_cd;
		    	$errorsCodes = preg_replace("/[^0-9]/", "", explode(', ', $errors));
		    	$result = ['code' => $response->getStatusCode(), 'errors' => $errorsCodes];
		    	return $result;
		    }
		}
	}

	/**
	* API To Fetch All Ewaybills By Transporter
	*/
	public static function GetEwayBillsForTransporterByState($date = null, $crd = null) {
	    
	    
	    ini_set('memory_limit', '-1');

		$token = self::$_ci->session->userdata('authtoken');
		if(!$token){
			$token = self::generateToken();
		}	
		
		//

		self::$_get['action'] = 'GetEwayBillsForTransporterByState';
		self::$_get['aspid'] = self::$_crd['eway_asp_id'];
		self::$_get['password'] = self::$_crd['eway_password'];
		self::$_get['gstin'] = self::$_crd['eway_gstin'];
		self::$_get['username'] = self::$_crd['eway_username'];
		self::$_get['authtoken'] = $token;
		self::$_get['date'] = $date;

		

		/////////////SAVE GST CALL
		$gstCall = self::$_ci->kaabar->getField('companies', self::$_company, 'id', 'gst_call');

		$statecodeList = self::$_ci->kaabar->getRows('states', $search = NULL, $search_field = 'id', $fields = 'gst', $order_by = 'gst');


		foreach ($statecodeList as $statesKey => $stateValue) {

			try {

				self::$_get['stateCode'] = $stateValue['gst'];
				$response = self::$_client->get(self::$_eway_url.http_build_query(self::$_get));

				/// Update GST CALL
				self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

				$rows[$statesKey]['result'] = ['code' => $response->getStatusCode(), 'result' => $response];
				$rows[$statesKey]['data'] = json_decode($response->getBody()->read(1024*1024*2), true);

			}catch (GuzzleHttp\Exception\BadResponseException $e) {
			
			    /// Update GST CALL
				self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

				if ($e->hasResponse()){
			    	$response = $e->getResponse();
			    	$errors = json_decode($response->getBody())->error->error_cd;
			    	$errorsCodes = preg_replace("/[^0-9]/", "", explode(', ', $errors));
			    	$rows[$statesKey]['result'] = ['code' => $response->getStatusCode(), 'errors' => $errorsCodes];
			    	$rows[$statesKey]['data'] = [];
			    }
			}
		}

		
		
		
		foreach ($rows as $k => $v) {
			$finalRows[] = $v['data'];
			$responseData[] = $v['result'];
		}
		$finalRows = custom_filter(array_filter($finalRows));

		function super_unique($array)
		{
		  $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

		  foreach ($result as $key => $value)
		  {
		    if ( is_array($value) )
		    {
		      $result[$key] = super_unique($value);
		    }
		  }

		  return $result;
		}

		if($finalRows){

			foreach ($finalRows as $key => $value) {

				$ewbDate = str_replace("/", "-", $value['ewbDate']);
				$docDate = str_replace("/", "-", $value['docDate']);
				$validUpto = str_replace("/", "-", $value['validUpto']);

				$value['ewbDate'] = date('Y-m-d H:i:s', strtotime($ewbDate));
				$value['docDate'] = date('Y-m-d', strtotime($docDate));
				$value['validUpto'] = date('Y-m-d H:i:s', strtotime($validUpto));

				$found = self::$_ci->kaabar->getField('ewaybillsbydates', $value['ewbNo'], 'ewbNo', 'id');
				$ewbCheck = self::$_ci->kaabar->getField('ewaybills', $value['ewbNo'], 'ewbNo', 'id');	

				if($found && $ewbCheck)
					continue;

				if(!$found){
					self::$_ci->kaabar->save('ewaybillsbydates', $value);
					Ewaybill::GetEwayBill($value['ewbNo']);
				}
			}

		}
		
		$finalResult = array_merge(custom_filter_key(super_unique($finalRows)), custom_filter_key(super_unique($responseData)));

		return $finalResult;
		
	}

	/**
	* API To Fetch All Ewaybills By Transporter
	*/
	public static function GetEwayBillsForTransporterByGstin($gstNo = null, $date = null, $crd = null) {
		
		ini_set('memory_limit', '-1');

		$token = self::$_session->get('authtoken');
		if(!$token){
			$token = self::generateToken();
		}
			
		self::$_get['action'] = self::$_router->methodName();
		self::$_get['aspid'] = self::$_crd['eway_asp_id'];
		self::$_get['password'] = self::$_crd['eway_password'];
		self::$_get['gstin'] = self::$_crd['eway_gstin'];
		self::$_get['username'] = self::$_crd['eway_username'];
		self::$_get['authtoken'] = $token;
		self::$_get['Gen_gstin'] = $gstNo;
		self::$_get['date'] = $date;

		$gstCall = self::$_kaabar->getField('companies', self::$_company, 'id', 'gst_call');
		
		try {

			$response = self::$_client->get(self::$_eway_url.http_build_query(self::$_get));	
			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

            $rows = json_decode($response->getBody()->read(1024*1024*2), true);

            foreach ($rows as $key => $value) {

            	$ewbDate = str_replace("/", "-", $value['ewbDate']);
				$docDate = str_replace("/", "-", $value['docDate']);
				$validUpto = str_replace("/", "-", $value['validUpto']);

				$value['ewbDate'] = date('Y-m-d H:i:s', strtotime($ewbDate));
				$value['docDate'] = date('Y-m-d', strtotime($docDate));
				$value['validUpto'] = date('Y-m-d H:i:s', strtotime($validUpto));

				$found = self::$_kaabar->getField('ewaybillsbydates', $value['ewbNo'], 'ewbNo', 'id');
				$ewbCheck = self::$_kaabar->getField('ewaybills', $value['ewbNo'], 'ewbNo', 'id');	

				if($found && $ewbCheck)
					continue;

				if(!$found)
					self::$_ci->kaabar->save('ewaybillsbydates', $value);
				else
					self::$_ci->kaabar->save('ewaybillsbydates', $value, ['id' => $found]);
				
				Ewaybill::GetEwayBill($value['ewbNo']);
				
			}
			$result = ['code' => $response->getStatusCode(), 'result' => $value];
			return $result;
			

		}catch (GuzzleHttp\Exception\BadResponseException $e) {

			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

			if ($e->hasResponse()){
		    	$response = $e->getResponse();
		    	$errors = json_decode($response->getBody())->error->error_cd;
		    	$errorsCodes = preg_replace("/[^0-9]/", "", explode(', ', $errors));
		    	$result = ['code' => $response->getStatusCode(), 'errors' => $errorsCodes];
		    	return $result;
		    }
		}
	}

	/**
	* API To Fetch EwayBill Details Using EWB NO
	*/
	public static function extendEWBValidaty($param = null) {
		ini_set('memory_limit', '-1');
		
		$token = self::$_session->get('authtoken');
		if(! isset($authtoken)){
			$token = self::generateToken();
		}

		if(!$param)
		{
			setSessionError('Ewaybill Data Required');
			return redirect()->to('ewaybill');
		}

		if($param['transMode'] == 5){
			$consignmentStatus = 'T';
			$transitType = $param['transitType'];
		}
		else
		{
			$consignmentStatus = 'M';
			$transitType = '';
		}

		$form_params = [
			'ewbNo' 			=> $param['ewbNo'],
	        'vehicleNo' 		=> $param['vehicleNo'],
	        'fromPlace' 		=> $param['fromPlace'],
	        'fromState' 		=> $param['fromStateCode'],
	        'fromPincode' 		=> $param['fromPincode'],
	        'remainingDistance' => preg_replace("/[^0-9]/", "", $param['remainingDistance']),
	        'transDocNo' 		=> $param['transDocNo'],
	        'transDocDate' 		=> $param['transDocDate'],
	        'transMode' 		=> $param['transMode'],
	        'extnRsnCode' 		=> $param['extnRsnCode'],
	        'extnRemarks' 		=> $param['extnRemarks'],
	        'consignmentStatus' => $consignmentStatus,
	        'transitType' 		=> $transitType,
	        'addressLine1' 		=> $param['addressLine1'],
	        'addressLine2' 		=> $param['addressLine2'],
	        'addressLine3' 		=> $param['addressLine3'],
		];

		
		$data = json_encode($form_params);
		self::$_get['action'] = 'EXTENDVALIDITY';
		self::$_get['aspid'] = self::$_crd['eway_asp_id'];
		self::$_get['password'] = self::$_crd['eway_password'];
		self::$_get['gstin'] = self::$_crd['eway_gstin'];
		self::$_get['username'] = self::$_crd['eway_username'];
		self::$_get['authtoken'] = $token;
		unset(self::$_get['ewbpwd']);

		$gstCall = self::$_kaabar->getField('companies', self::$_company, 'id', 'gst_call');
		
		try {
			$response = self::$_client->post(self::$_eway_url.http_build_query(self::$_get), ['body' => $data]);
			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

			$value = json_decode($response->getBody()->read(1024), true);
			$result = ['code' => $response->getStatusCode(), 'result' => $value];
			return $result;
		}catch (GuzzleHttp\Exception\BadResponseException $e) {

			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

			if ($e->hasResponse()){
		    	$response = $e->getResponse();
		    	$errors = json_decode($response->getBody())->error->error_cd;
		    	$errorsCodes = preg_replace("/[^0-9]/", "", explode(', ', $errors));
		    	$result = ['code' => $response->getStatusCode(), 'errors' => $errorsCodes];
		    	return $result;
		    }
		}
	}

	/**
	* API To UPDATE EWB Part B Vehicle No 
	*/
	public static function updateEWBVehicle($param = null) {
		ini_set('memory_limit', '-1');
		
		$token = self::$_session->get('authtoken');
		if(! isset($authtoken)){
			$token = self::generateToken();
		}

		if(!$param)
		{
			setSessionError('Ewaybill Data Required');
			return redirect()->to('ewaybill');
		}

		if($param['transMode'] == 5){
			$consignmentStatus = 'T';
			$transitType = $param['transitType'];
		}
		else
		{
			$consignmentStatus = 'M';
			$transitType = '';
		}

		$form_params = [
			'ewbNo' 			=> $param['ewbNo'],
	        'vehicleNo' 		=> $param['vehicleNo'],
	        'fromPlace' 		=> $param['fromPlace'],
	        'fromState' 		=> $param['fromStateCode'],
	        'fromPincode' 		=> $param['fromPincode'],
	        'remainingDistance' => preg_replace("/[^0-9]/", "", $param['remainingDistance']),
	        'transDocNo' 		=> $param['transDocNo'],
	        'transDocDate' 		=> $param['transDocDate'],
	        'transMode' 		=> $param['transMode'],
	        'extnRsnCode' 		=> $param['extnRsnCode'],
	        'extnRemarks' 		=> $param['extnRemarks'],
	        'consignmentStatus' => $consignmentStatus,
	        'transitType' 		=> $transitType,
	        'addressLine1' 		=> $param['addressLine1'],
	        'addressLine2' 		=> $param['addressLine2'],
	        'addressLine3' 		=> $param['addressLine3'],
		];

		
		$data = json_encode($form_params);
		self::$_get['action'] = 'EXTENDVALIDITY';
		self::$_get['aspid'] = self::$_crd['eway_asp_id'];
		self::$_get['password'] = self::$_crd['eway_password'];
		self::$_get['gstin'] = self::$_crd['eway_gstin'];
		self::$_get['username'] = self::$_crd['eway_username'];
		self::$_get['authtoken'] = $token;
		unset(self::$_get['ewbpwd']);

		$gstCall = self::$_kaabar->getField('companies', self::$_company, 'id', 'gst_call');
		
		try {
			$response = self::$_client->post(self::$_eway_url.http_build_query(self::$_get), ['body' => $data]);
			/// Update GST CALL
			self::$_ci->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);

			$value = json_decode($response->getBody()->read(1024), true);
			$result = ['code' => $response->getStatusCode(), 'result' => $value];
			return $result;
		}catch (GuzzleHttp\Exception\BadResponseException $e) {

			/// Update GST CALL
			self::$_CI->kaabar->save('companies', ['gst_call' => ($gstCall-1)], ['id' => self::$_company]);
			
			if ($e->hasResponse()){
		    	$response = $e->getResponse();
		    	$errors = json_decode($response->getBody())->error->error_cd;
		    	$errorsCodes = preg_replace("/[^0-9]/", "", explode(', ', $errors));
		    	$result = ['code' => $response->getStatusCode(), 'errors' => $errorsCodes];
		    	return $result;
		    }
		}
	}

	/**
	* API To Fetch Distance Beetween two PINCODES
	*/
	public static function getDistance($param = null) {
		
		self::$_client = new Client( array
			( 
         		'curl'   => array( CURLOPT_SSL_VERIFYPEER => false ),
         		'verify' => false
       		)
		);
		self::$_kaabar = new KaabarModel();

		ini_set('memory_limit', '-1');
		//URL ===== https://maps.googleapis.com/maps/api/distancematrix/json?origins=370201&destinations=382350&key=AIzaSyCNFeNmvBIkNtLwFXeNjw1EEK4ZeUr-tBI
		$savedata = [];
		$url = 'https://maps.googleapis.com/maps/api/distancematrix/json?';
		$api = 'AIzaSyCNFeNmvBIkNtLwFXeNjw1EEK4ZeUr-tBI';
		$frmPincode = $param['frmPincode'];
		$toPincode = $param['toPincode'];
		$param = array(
			'origins' => $frmPincode,
			'destinations' => $toPincode,
			'key' => $api
		);
		
		try {
            $response = self::$_client->get($url.http_build_query($param));
            $value = json_decode($response->getBody()->read(5000), true);
            
            
            if($value['rows'][0]['elements'][0]['status'] == 'NOT_FOUND')
               return '0';
            
			$distance = $value['rows'][0]['elements'][0]['distance']['text'];
			if($distance){
        		self::$_ci->kaabar->save('distances', ['origins' => $frmPincode, 'destinations' => $toPincode, 'distance' => $distance]);
			}

        	return $distance;
            
		}catch (GuzzleHttp\Exception\BadResponseException $e) {
			if ($e->hasResponse()){
		    	$response = $e->getResponse();
		    	$errors = json_decode($response->getBody())->error->error_cd;
		    	$errorsCodes = preg_replace("/[^0-9]/", "", explode(', ', $errors));
		    	$result = ['code' => $response->getStatusCode(), 'errors' => $errorsCodes];
		    	return $result;
		    }
		}
	}

	/**
	* API To Fetch Distance Beetween two PINCODES
	*/
	public static function getPincode($param = null) {
		
		ini_set('memory_limit', '-1');

		//URL ===== https://maps.googleapis.com/maps/api/geocode/json?address=500030&sensor=true&key=AIzaSyBaEczifsBfN_QP0DAUkQVtYA7hF7OT-Tc

		$url = 'https://maps.googleapis.com/maps/api/geocode/json?';
		$api = 'AIzaSyCNFeNmvBIkNtLwFXeNjw1EEK4ZeUr-tBI';
		$param = array(
			'address' => $param,
			'sensor' => 'true',
			'key' => $api
		);

		try {
			$response = self::$_client->get($url.http_build_query($param));
			$value = json_decode($response->getBody()->read(1024), true);
        	$distance = $value['rows'][0]['elements'][0]['distance']['text'];
        	if($distance)
        		self::$_ci->kaabar->save('distances', ['origins' => $frmPincode, 'destinations' => $toPincode, 'distance' => $distance]);

        	return $distance;
            
		}catch (GuzzleHttp\Exception\BadResponseException $e) {
			if ($e->hasResponse()){
		    	$response = $e->getResponse();
		    	$errors = json_decode($response->getBody())->error->error_cd;
		    	$errorsCodes = preg_replace("/[^0-9]/", "", explode(', ', $errors));
		    	$result = ['code' => $response->getStatusCode(), 'errors' => $errorsCodes];
		    	return $result;
		    }
		}
	}

	
	
}
