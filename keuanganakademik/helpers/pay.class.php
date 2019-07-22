<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class BasePay {
		// setting sevimapay
		const PAY_URL_DEV = 'http://pay.sevima.com/dev/api/';
		const PAY_URL_PROD = 'https://sevimapay.sevima.com/api/';
		const PAY_CLIENT_ID = null;
		const PAY_SECRET_ID = null;
		const PAY_ADMIN_AMOUNT = 0;
		const PAY_DEVELOPMENT = true; // harap di-override di child class
		
		// setting lain-lain
		const TIME_DIFF_LIMIT = 300; // 5 menit
		const VA_LENGTH = 16;
		
		// status/kode error
		const ERROR_OK = '000';
		const ERROR_PAID = '100';
		const ERROR_NOTFOUND = '101';
		const ERROR_EXPIRED = '103';
		const ERROR_CANCELLED = '104';
		const ERROR_INTERNAL = '999';
		
		// cek selisih waktu
		protected static function tsDiff($ts) {
			return abs($ts - time()) <= static::TIME_DIFF_LIMIT;
		}
		
		// metode enkripsi
		protected static function encrypt($string, $key) {
			$result = '';
			$strls = strlen($string);
			$strlk = strlen($key);
			for($i = 0; $i < $strls; $i++) {
				$char = substr($string, $i, 1);
				$keychar = substr($key, ($i % $strlk) - 1, 1);
				$char = chr((ord($char) + ord($keychar)) % 128);
				$result .= $char;
			}
			return $result;
		}
		
		// metode dekripsi
		protected static function decrypt($string, $key) {
			$result = '';
			$strls = strlen($string);
			$strlk = strlen($key);
			for($i = 0; $i < $strls; $i++) {
				$char = substr($string, $i, 1);
				$keychar = substr($key, ($i % $strlk) - 1, 1);
				$char = chr(((ord($char) - ord($keychar)) + 256) % 128);
				$result .= $char;
			}
			return $result;
		}
		
		// enkripsi data
		protected static function hashData($json_data, $secret) {
			$result = static::encrypt(strrev(time()) . '.' . json_encode($json_data), $secret);
			
			return strtr(rtrim(base64_encode($result), '='), '+/', '-_');
		}
		
		// dekripsi data
		protected static function parseData($hashed_string, $secret) {
			$result = base64_decode(strtr(str_pad($hashed_string, ceil(strlen($hashed_string) / 4) * 4, '=', STR_PAD_RIGHT), '-_', '+/'));
			
			$parsed_string = static::decrypt($result, $secret);
			list($timestamp, $data) = array_pad(explode('.', $parsed_string, 2), 2, null);
			
			if (static::tsDiff(strrev($timestamp)) === true) {
				return json_decode($data, true);
			}
			return null;
		}
		
		// request dengan curl
		protected static function getContent($url, $request) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
			$response = curl_exec($ch);
			curl_close($ch);
			
			return $response;
		}
		
		// mengirim data
		protected static function requestData($data) {
			// request
			$hash = static::hashData($data,static::PAY_SECRET_ID);
			$request = array('client_id' => static::PAY_CLIENT_ID,'data' => $hash);
			$requestjson = json_encode($request);
			
			// response
			if(static::PAY_DEVELOPMENT)
				$url = static::PAY_URL_DEV;
			else
				$url = static::PAY_URL_PROD;
			
			$responsejson = static::getContent($url,$requestjson);
			$response = json_decode($responsejson,true);
			
			// dekripsi data response
			if(!empty($response['data']))
				$response['data'] = static::parseData($response['data'],static::PAY_SECRET_ID);
			
			return $response;
		}
		
		// default datetime expired
		protected static function getDefaultExpired() {
			return date('c',mktime(0,0,0,date('n'),date('j')+1,date('Y'))); // besok sudah kadaluarsa
		}
		
		// create billing
		public static function createBilling($data) {
			// tanggal kadaluarsa
			if(!isset($data['datetime_expired']))
				$expired = static::getDefaultExpired();
			else if(!empty($data['datetime_expired']))
				$expired = date('c',date_timestamp_get(date_create($data['datetime_expired'])));
			else
				$expired = null;
			
			$request = array(
				'type'=> 'createbilling',
				'client_id' => static::PAY_CLIENT_ID,
				'trx_id' => $data['trx_id'],
				'trx_amount' => $data['trx_amount'],
				'billing_type' => 'c',
				'customer_id' => $data['customer_id'],
				'customer_name' => $data['customer_name'],
				'customer_email' => $data['customer_email'],
				'customer_phone' => $data['customer_phone'],
				'datetime_expired' => $expired,
				'description' => $data['description'],
				'update_exists' => 1
			);
			
			if(!empty($data['virtual_account']))
				$request['virtual_account'] = $data['virtual_account'];
			
			if(static::PAY_DEVELOPMENT) {
				$request['customer_email'] = 'sevimapay12@gmail.com';
				$request['customer_phone'] = null;
			}
			
			return static::requestData($request);
		}
		
		// inquiry billing
		public static function inquiryBilling($data) {
			$request = array(
				'type'=> 'inquirybilling',
				'client_id' => static::PAY_CLIENT_ID,
				'trx_id' => $data['trx_id']
			);
			
			if(!empty($data['get_payment']))
				$request['get_payment'] = 1;
			
			return static::requestData($request);
		}
		
		// cancel billing, hanya update datetime_expired
		public static function cancelBilling($trx_id) {
			$request = array(
				'type'=> 'updatebilling',
				'client_id' => static::PAY_CLIENT_ID,
				'trx_id' => $trx_id,
				'datetime_expired' => date('c',mktime(0,0,0,date('n'),date('j'),date('Y')))
			);
			
			$resp = static::requestData($request);
			
			// cek error yang sejalan
			if($resp['status'] == static::ERROR_EXPIRED or $resp['status'] == static::ERROR_CANCELLED)
				$resp['status'] = static::ERROR_OK;
			
			return $resp;
		}
		
		// payment notification
		public static function getPaymentNotification() {
			$datajson = file_get_contents('php://input');
			$data = json_decode($datajson,true);
			
			// cek client id
			if($data['client_id'] == static::PAY_CLIENT_ID)
				return static::parseData($data['data'],static::PAY_SECRET_ID);
			else
				return false;
		}
	}
?>