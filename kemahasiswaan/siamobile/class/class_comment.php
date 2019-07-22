<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include
	require_once(Route::getViewPath('class_helper'));
	require_once(Route::getViewPath('class_object'));
	require_once(Route::getViewPath('class_model_timeline'));
	
	/**
	 * Class untuk controller user aplikasi mobile fitur timeline
	 * @author Sevima
	 * @version 1.0
	 */
	 
	class fComment {
		protected $model;
		
		/**
		* Constructor
		*/
		function __construct() {
			$this->model = new mMobileTimeline();
		}
		
		/**
		* Upload Comment
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function upload($param,$post) {
		}
	}

?>