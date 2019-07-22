<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include
	require_once(Route::getViewPath('class_helper'));
	require_once(Route::getViewPath('class_object'));
	require_once(Route::getViewPath('class_model'));
	
	/**
	 * Class untuk controller akademik aplikasi mobile
	 * @author Sevima
	 * @version 1.0
	 */
	class fAcademic {
		protected $model;
		
		/**
		* Constructor
		*/
		function __construct() {
			$this->model = new mMobile();
		}
		
		/**
		* Menampilkan daftar fakultas (dan prodi) user login
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function faculty($param,$post) {
			// parameter
			$token = $param[0];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_FACULTY);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg,401);
				return cHelper::getJSON($sys);
			}
			
			$username = $this->model->getUsername($row['userid']);
			$rows = $this->model->getListFakultasByUsername($username);
			
			$index = forward_static_call(array('cFaculty','getIndexName'),true);
			$a_obj = array('index' => $index, 'data' => array());
			
			foreach($rows as $row) {
				$obj = new cFaculty();
				$obj->setAttributes($row);
				
				$a_obj['data'][] = $obj;
			}
			
			return cHelper::getJSON($sys,$a_obj);
		}
		
		/**
		* Menampilkan daftar periode user login
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function period($param,$post) {
			// parameter
			$token = $param[0];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_PERIOD);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg);
				return cHelper::getJSON($sys);
			}
			
			$username = $this->model->getUsername($row['userid']);
			$a_periode = $this->model->getListPeriodeByUsername($username);
			
			$index = forward_static_call(array('cPeriod','getIndexName'),true);
			$a_obj = array('index' => $index, 'data' => array());
			
			foreach($a_periode as $periode) {
				$obj = new cPeriod();
				$obj->setAttsFromVal($periode);
				
				$a_obj['data'][] = $obj;
			}
			return cHelper::getJSON($sys,$a_obj);
		}
		
		/**
		* Menampilkan jadwal mahasiswa
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function schedule($param,$post) {
			// parameter
			$token = $param[0];
			$nim = $param[1];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_SCHEDULE);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg);
				return cHelper::getJSON($sys);
			}
			
			$username = $this->model->getUsername($row['userid']);
			
			// cek apa bisa akses mahasiswa
			$cek = $this->model->canUserAksesMhs($username,$nim);
			if(!$cek) {
				$sys->setError(cLang::ERROR_ACCESS_STUDENT.': '.$nim,403);
				return cHelper::getJSON($sys);
			}
			
			$periode = $this->model->getPeriodeSekarang();
			
			$rows = $this->model->getListJadwalMahasiswa($nim,$periode);
			
			$index = forward_static_call(array('cEvent','getIndexName')); // nggak pakai true, singular
			$a_obj = array('index' => $index, 'data' => array());
			
			foreach($rows as $row) {
				$obj = new cEvent();
				$obj->setAttributes($row);
				$obj->setOnly('room',array('id','name'));
				$obj->setOnly('studentGroup',array('name','credit'));
				
				$a_obj['data'][] = $obj;
			}

			// cek kosong
			if(empty($a_obj['data'])) {
				$a_obj = array();
				$sys->setInfos(cLang::getEmptyMsg());
			}
			
			return cHelper::getJSON($sys,$a_obj);
		}
		
		/**
		* Menampilkan rekap presensi mahasiswa pada periode sekarang
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function presence($param,$post) {
			// parameter
			$token = $param[0];
			$nim = $param[1];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_PRESENCE_STUDENT);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg);
				return cHelper::getJSON($sys);
			}
			
			$username = $this->model->getUsername($row['userid']);
			
			// cek apa bisa akses mahasiswa
			$cek = $this->model->canUserAksesMhs($username,$nim);
			if(!$cek) {
				$sys->setError(cLang::ERROR_ACCESS_STUDENT.': '.$nim,403);
				return cHelper::getJSON($sys);
			}
			
			// ambil data mahasiswa
			$student = new cStudent();
			
			$data = $this->model->getDataMahasiswa($nim);
			if(!empty($data))
				$student->setAttributes($data);
			$student->setOnly(null,array('strata','nim','angkatan','id','name'));
			
			$periode = $this->model->getPeriodeSekarang();
			
			$rows = $this->model->getRekapPresensiMahasiswa($nim,$periode);
			
			$index = forward_static_call(array('cPresence','getIndexName'),true);
			$a_obj = array('index' => $index, 'data' => array());
			
			foreach($rows as $row) {
				// dosen
				$lecturer = new cLecturer();
				
				$data = $this->model->getDataDosen($row['nip']);
				if(!empty($data))
					$lecturer->setAttributes($data);
				$lecturer->setOnly(null,array('name'));
				
				// presensi
				$presence = new cPresence();
				$presence->setAttributes($row);
				
				$presence->setOnly(null,array('sumPresence','sumPermitting','sumAbsent','sumMeeting','studentGroup'));
				$presence->setOnly('studentGroup',array('subject'));
				$presence->setOnly('studentGroup.subject',array('name'));
				
				$a_obj['data'][] = array($lecturer,$presence);
			}
			
			// cek kosong
			if(empty($a_obj['data'])) {
				$a_obj = array();
				$sys->setInfos(cLang::getEmptyMsg());
			}
			
			return cHelper::getJSON($sys,$student,$a_obj);
		}
		
		/**
		* Menampilkan kuliah mahasiswa pada periode sekarang
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function course($param,$post) {
			// parameter
			$token = $param[0];
			$nim = $param[1];
			$state = $param[2];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_COURSE);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg);
				return cHelper::getJSON($sys);
			}
			
			$username = $this->model->getUsername($row['userid']);
			
			// cek apa bisa akses mahasiswa
			$cek = $this->model->canUserAksesMhs($username,$nim);
			if(!$cek) {
				$sys->setError(cLang::ERROR_ACCESS_STUDENT.': '.$nim,403);
				return cHelper::getJSON($sys);
			}
			
			// ambil data mahasiswa
			/*$student = new cStudent();
			
			$data = $this->model->getDataMahasiswa($nim);
			
			if(!empty($data))
			$student->setAttributes($data);

		    print_r($student);
			
			$student->setOnly(null,array('strata','nim','angkatan','id','name'));
			*/

			$periode = $this->model->getPeriodeSekarang();
			
			if($state == 'STUDY_KRS' or $state == 'STUDY_KHS' or $state == 'STUDY_TRANSCRIPT') {
				if($state == 'STUDY_KRS') {
					$rows = $this->model->getListKHSMahasiswa($nim,$periode);

					$index = forward_static_call(array('cKRS','getIndexName')); // nggak pakai true, singular
					$a_obj = array('data' => array());
					foreach($rows as $row) {
						$obj = new cKRS();
						$obj->setAttributes($row);
						
						$obj->setOnly(null,array('status','period','studies'));
						$obj->setOnly('period',array('name'));
						
						$a_obj['data'] = $obj;
					}
				}
				else if($state == 'STUDY_KHS') {
					$rows = $this->model->getListKHSMahasiswa($nim);
					
					$index = forward_static_call(array('cKHS','getIndexName')); // nggak pakai true, singular
					$a_obj = array('index' => $index, 'data' => array());
					
					foreach($rows as $row) {
						$obj = new cKHS();
						$obj->setAttributes($row);
						
						$obj->setOnly(null,array('totalSKS','totalIP','status','period','studies'));
						$obj->setOnly('period',array('name'));
						
						$a_obj['data'][] = $obj;
					}
				}
				else if($state == 'STUDY_TRANSCRIPT') {
					$rows = $this->model->getListTranscriptMahasiswa($nim);
					
					$index = forward_static_call(array('cTranscript','getIndexName')); // nggak pakai true, singular
					$a_obj = array('data' => array());
					
					foreach($rows as $row) {
						$obj = new cTranscript();
						$obj->setAttributes($row);
						
						$obj->setOnly(null,array('totalSKS','totalIP','studies'));
						$obj->setOnly('period',array('name'));
						
						$a_obj['data'] = $obj;
					}
				}
				// cek kosong
				if(empty($a_obj['data'])) {
					$a_obj = array();
					$sys->setInfos(cLang::getEmptyMsg());
				}
				if($state == 'STUDY_KHS')
					return cHelper::getJSON($sys,$student,$a_obj);
				else
					return cHelper::getJSON($sys,$student,$a_obj['data']);
			}
			else {
				$sys->setError(cLang::ERROR_COURSE_STATE_NOT_FOUND,400);
				return cHelper::getJSON($sys);
			}
		}
		
		
		/**
		* Menampilkan daftar finance
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function finance($param,$post) {
			// parameter
			$token = $param[0];
			$nim = $param[1];
			$state = $param[2];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_FINANCE);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg);
				return cHelper::getJSON($sys);
			}
			
			$username = $this->model->getUsername($row['userid']);
			
			// cek apa bisa akses mahasiswa
			$cek = $this->model->canUserAksesMhs($username,$nim);
			if(!$cek) {
				$sys->setError(cLang::ERROR_ACCESS_STUDENT.': '.$nim,403);
				return cHelper::getJSON($sys);
			}
			
			$periode = $this->model->getPeriodeSekarang();
			
			if($state == 'FINANCE_BILL' or $state == 'FINANCE_PAYMENT') {
				if($state == 'FINANCE_BILL') {
					$rows = $this->model->getListTagihanMahasiswa($nim);
			
					$index = forward_static_call(array('cBillInfo','getIndexName'),true);
					$a_obj = array('index' => $index, 'data' => array());
					
					foreach($rows as $row) {
						
						$obj = new cBillInfo();
						$obj->setAttributes($row);
						$obj->setOnly(null,array('period','description','bill','fine','status','dueDate'));
						$obj->setOnly('period',array('id','name','year','semester'));
						$a_obj['data'][] = $obj;
					}
				}
				else if($state == 'FINANCE_PAYMENT') {
					$rows = $this->model->getListPembayaranMahasiswa($nim);
			
					$index = forward_static_call(array('cPayment','getIndexName'),true);
					$a_obj = array('index' => $index, 'data' => array());
					
					foreach($rows as $row) {
						
						$obj = new cPayment();
						$obj->setAttributes($row);
						$obj->setOnly(null,array('period','description','payment','fine','status','dueDate'));
						$obj->setOnly('period',array('id','name','year','semester'));
						$a_obj['data'][] = $obj;
					}
				}
				
				// cek kosong
				if(empty($a_obj['data'])) {
					$a_obj = array();
					$sys->setInfos(cLang::getEmptyMsg());
				}
				return cHelper::getJSON($sys,$a_obj);
				
			}
			else {
				$sys->setError(cLang::ERROR_FINANCE_STATE_NOT_FOUND,400);
				return cHelper::getJSON($sys);
			}
			
			
		}

		function calendar($param) {
			// parameter
			$token = $param[0];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_CALENDAR_ACADEMIC);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg);
				return cHelper::getJSON($sys);
			}

			// cek periode
			if(empty($periode))
				$periode = $this->model->getPeriodeSekarang();
			
			$rows = $this->model->getListKalenderAkademik($periode);
			
			$a_obj = array('data' => array());
				
			$obj = new cCalendar();
			$obj->setAttributes($rows);
			$obj->setOnly(null,array('period','events', 'linkDownload'));
			$obj->setOnly('period',array('id','name','year','semester'));
			$a_obj['data'] = $obj;
			//print_r($a_obj);die;
			
			// cek kosong
			if(empty($a_obj['data'])) {
				$a_obj = array();
				$sys->setInfos(cLang::getEmptyMsg(cLang::DATA_CALENDAR_ACADEMIC));
			}
			/*
			*/
			return cHelper::getJSON($sys,$a_obj['data']);

		}
		
		/**
		* Menampilkan daftar anak wali
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function guidance($param,$post) {
			// parameter
			$token = $param[0];
			$cari = $post['search'];

			// set object
			$sys = new cSevimaSystem(cLang::ACT_STUDENT);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg);
				return cHelper::getJSON($sys);
			}

			$user = new cUser();
			$data = $this->model->getDataUser($row['userid']);
			$user->setAttributes($data);
			$user->setOnly(null,array('id','name','username','email','hp','image','facebook','google','userRole'));
			$user->setOnly('userRole',array('id','roles'));
			
			list($ismhs,$isdosen,$isemployee) = $this->model->getRolesByUsername($data['username']);
			
			$lecturer = new cLecturer();
			$data = $this->model->getDataDosen($data['username']);
			
			if($isdosen && isset($data['nip'])){
				$rows = $this->model->getListMahasiswaWali($data['nip'],$cari);
				$index = forward_static_call(array('cStudent','getIndexName'),true);
				$a_obj = array('index' => $index, 'data' => array());
				
				foreach($rows as $row) {
					$obj = new cStudent();
					$obj->setAttributes($row);
					$obj->setOnly(null,array('id','username','name','nim','strata','angkatan','userRole','image'));
					$obj->setOnly('userRole',array('roles'));
					$obj->setOnly('userRole.roles',array('department'));
					$obj->setOnly('userRole.roles.department',array('name'));
					$obj->setOnly('image',array('thumbPath'));
					
					$a_obj['data'][] = $obj;
				}
			}
			
			return cHelper::getJSON($sys,$a_obj);
		}
		
		/**
		* Menampilkan daftar presensi mahasiswa per perkuliahan
		* @param array $param
		* @param array $post
		* @return string json
		*/
		/* function presenceClass($param,$post) {
			// parameter
			$token = $param[0];
			$id = $param[1];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_PRESENCE_CLASS);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg);
				return cHelper::getJSON($sys);
			}
			
			$username = $this->model->getUsername($row['userid']);
			
			// cek apa bisa akses perkuliahan
			$cek = $this->model->canUserAksesKuliah($username,$id);
			if(!$cek) {
				$sys->setError(cLang::ERROR_ACCESS.': '.$id,403);
				return cHelper::getJSON($sys);
			}
			
			$rows = $this->model->getPresensiKuliah($id);
			
			$index = forward_static_call(array('cStudentPresence','getIndexName'),true);
			$a_obj = array('index' => $index, 'data' => array());
			
			foreach($rows as $row) {
				$obj = new cStudentPresence();
				$obj->setAttributes($row);
				
				$obj->setOnly(null,array('student','isAttending'));
				$obj->setOnly('student',array('id','name'));
				
				$a_obj['data'][] = $obj;
			}
			
			return cHelper::getJSON($sys,$a_obj);
		} */
		
		/**
		* Menyimpan presensi mahasiswa di suatu perkuliahan
		* @param array $param
		* @param array $post
		* @return string json
		*/
		/* function presenceSave($param,$post) {
			// parameter
			$token = $param[0];
			$nim = $param[1];
			$id = $param[2];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_PRESENCE_STUDENT);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg);
				return cHelper::getJSON($sys);
			}
			
			$username = $this->model->getUsername($row['userid']);
			
			// hanya untuk dosen
			list(,$isdosen) = $this->model->getRolesByUsername($username);
			if(!$isdosen) {
				$sys->setError(cLang::ERROR_ACCESS_FEATURE,403);
				return cHelper::getJSON($sys);
			}
			
			// cek apa bisa akses perkuliahan
			if(!$sys->isError()) {
				$cek = $this->model->canUserAksesKuliah($username,$id);
				if(!$cek) {
					$sys->setError(cLang::ERROR_ACCESS.': '.$id,403);
					return cHelper::getJSON($sys);
				}
			}
			
			// simpan presensi
			if(!$sys->isError()) {
				// untuk log
				$_SESSION[SITE_ID]['MODUL']['USERNAME'] = $username;
				
				list($err,$msg) = $this->model->savePresensi($nim,$id);
				if($err)
					$sys->setError($msg);
			}
			
			return cHelper::getJSON($sys);
		} */
	}