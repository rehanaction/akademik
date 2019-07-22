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
			exit;
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
		
		// upload file
		function uploadFile($from,$to) {
			//if(!isdir($to))
				//mkdir($conf['docuploads_dir'].$to,0777);
			return move_uploaded_file($from,$to);
		}
		
		// pesan error upload
		function uploadErrorMsg($err) {
			$msg = 'Upload gagal';
			
			switch($err) {
				case 1:
				case 2: return $msg.', ukuran file melebihi batas';
				case 4: return $msg.', file tidak ditemukan';
				case 3:
				case 6:
				case 7: return $msg.', server tidak bisa menerima file';
				default: return $msg.', mohon cek aturan upload terlebih dulu';
			}
		}

		function navListpage($page,$sent='') {
			if(substr($page,0,9) != 'index.php') {
				if($page[0] != '?')
					$page = '?page='.$page;
				if(!empty($sent))
					$page .= '&key='.$sent;
				$page = 'index.php'.$page;
			}
			
			header('Location: '.self::navAddress($page));
			exit;
		}
	
		//simpan yang dipost ke session
		function setFlashDataPost() {
			if(func_num_args() == 1)
				$_SESSION[SITE_ID]['FDATA'] = func_get_arg(0);
			else
				$_SESSION[SITE_ID]['FDATA'] = func_get_args();
		}
		
		// dapatkan variabel dari fungsi diatas
		function getFlashDataPost() {
			$var = $_SESSION[SITE_ID]['FDATA'];
			unset($_SESSION[SITE_ID]['FDATA']);
			
			return $var;
		}
		
	    function getMIME($path) {
	        $finfo = finfo_open(FILEINFO_MIME_TYPE);
	        $mime = finfo_file($finfo, $path);
	        finfo_close($finfo);

	        return $mime;
	    }

	    function deleteFilePortal($path, $id, $isimage = false){
			global $conf;

			$url = $conf['delete_file_portal'].$path;

			$post = array();
			$post['token'] = $conf['token_portal'];
			$post['id'] = $id;
			$post['isimage'] = $isimage;

		    $curl = curl_init();
		    curl_setopt_array($curl, array(
		    	CURLOPT_RETURNTRANSFER => 1,
		    	CURLOPT_URL => $url,
		    	CURLOPT_USERAGENT => '',
		    	CURLOPT_POST => 1,
		    	CURLOPT_POSTFIELDS => $post
		    ));

		    $response = curl_exec($curl);
		    curl_close($curl);

		    return $response;
		}

		function isUrlFileExist($url){
		    $ch = curl_init($url);    
		    curl_setopt($ch, CURLOPT_NOBODY, true);
		    curl_exec($ch);
		    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		    if($code == 200){
		       $status = true;
		    }else{
		      $status = false;
		    }
		    
		    curl_close($ch);

		   	return $status;
		}
	}
?>
