<?php
	// fungsi direct redirect
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	class Route {
		// menuju suatu halaman
		function redirect($url) {
			header('Location: '.$url);
			exit;
		}
		
		function navigate($page) {
			header('Location: '.self::navAddress($page));
			exit();
		}
		
		// menuju suatu halaman dengan data
		function setFlashData($data) {
			$url = Route::thisPage();
			
			$_SESSION[SITE_ID]['FLASH'] = $data;
			
			self::navigate($url);
		}
		
		function getFlashData() {
			$data = $_SESSION[SITE_ID]['FLASH'];
			
			unset($_SESSION[SITE_ID]['FLASH']);
			
			return $data;
		}
		
		// seperti flash data
		function setKeyData($key,$data) {
			$_SESSION[SITE_ID]['FLASH'][$key] = $data;
		}
		
		function getKeyData($key) {
			return $_SESSION[SITE_ID]['FLASH'][$key];
		}
		
		// membentuk halaman
		function navAddress($page) {
			if(substr($page,0,9) != 'index.php') {
				if($page[0] != '?')
					$page = '?page='.$page;
				$page = 'index.php'.$page;
			}
			
			return $page;
		}
		
		// nama halaman ini
		function thisPage($src=false) {
			$uri = $_SERVER['REQUEST_URI'];
			list(,$aftpage) = explode('page=',$uri);
			
			$famp = strpos($aftpage,'&');
			if($famp !== false and !$src)
				return substr($aftpage,0,$famp);
			else
				return $aftpage;
		}
		
		// path model
		function getModelPath($name) {
			global $conf;
			
			return $conf['model_dir'].'m_'.strtolower($name).'.php';
		}
		
		// path view
		function getViewPath($name) {
			global $conf;
			
			return $conf['view_dir'].strtolower($name).'.php';
		}
		
		// path ui
		function getUIPath($name) {
			global $conf;
			
			return $conf['ui_dir'].'u_'.strtolower($name).'.php';
		}
		
		// mendapatkan halaman pasangan
		function getPageName($pref='') {
			$thispage = Route::thisPage();
			$uscore = strpos($thispage,'_');
				
			if($uscore !== false)
				$page = $pref.'_'.substr($thispage,$uscore+1);
			else
				$page = $thispage;
			
			return $page;
		}
		
		// mendapatkan halaman list
		function getListPage() {
			return self::getPageName('list');
		}
		
		// mendapatkan halaman detail
		function getDetailPage() {
			return self::getPageName('data');
		}
		
		// mendapatkan halaman laporan
		function getReportPage() {
			return self::getPageName('rep');
		}
	}
?>