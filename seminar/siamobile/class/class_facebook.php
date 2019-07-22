<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include
	require_once(Route::getViewPath('class_helper'));
	require_once(Route::getViewPath('class_object'));
	require_once(Route::getViewPath('class_model'));
	
	/**
	 * Class untuk controller user aplikasi mobile
	 * @author Sevima
	 * @version 1.0
	 */
	class fFacebook {
		protected $model;
		
		/**
		* Constructor
		*/
		function __construct() {
			$this->model = new mMobile();
		}
		
		function link($param,$post) {
			// parameter
			$token = $param[0];
			$facebookId = $param[1];

			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg);
				return cHelper::getJSON($sys);
			}
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_FACEBOOK_LINK);
			
			$facebook = new cFacebook();
			
			list($row,$msg) = $this->model->facebookLinked($row['userid'],$facebookId);
			
			return cHelper::getJSON($sys,$facebook);
		}
		
		function unlink($param,$post) {
			// parameter
			$token = $param[0];
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg);
				return cHelper::getJSON($sys);
			}
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_FACEBOOK_UNLINK);
			
			$facebook = new cFacebook();
			
			list($row,$msg) = $this->model->facebookLinked($row['userid']);
			
			return cHelper::getJSON($sys,$facebook);
		}
		
				
	}