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
	class fUser {
		protected $model;
		
		/**
		* Constructor
		*/
		function __construct() {
			$this->model = new mMobile();
		}
		
		/**
		* Login ke sistem
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function login($param,$post) {
			// parameter
			$username = $post['username'];
			$password = $post['password'];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_LOGIN);
			$user = new cUser();
			
			// cek login
			$token = cHelper::getNewToken();
			list($row,$msg) = $this->model->logIn($username,$password,$token,$regid);
			
			if($row !== false) {
				$sys->setToken($token);
				$user->setAttributes($row);
				$user->setOnly(null,array('id','name','username','email','hp','sia'));
			}
			else
				$sys->setError($msg);
			
			return cHelper::getJSON($sys,$user);
		}

		function loginfacebook($param,$post) {
			// parameter
			$facebokId = $param['0'];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_LOGIN_FACEBOOK);
			$user = new cUser();
			
			// cek login
			$token = cHelper::getNewToken();
			list($row,$msg) = $this->model->logInFacebook($facebokId,$token,$regid);
			
			if($row !== false) {
				$sys->setToken($token);
				$user->setAttributes($row);
				$user->setOnly(null,array('id','name','username','email','hp','sia'));
			}
			else
				$sys->setError($msg);
			
			return cHelper::getJSON($sys,$user);
		}
		
		/**
		* Logout dari sistem
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function logout($param,$post) {
			// parameter
			$token = $param[0];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_LOGOUT);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			
			if(!$err) {
				$ok = $this->model->logOut($token);
				$err = Query::isErr($ok);
			}
			
			if($err)
				$sys->setError($msg);
			
			return cHelper::getJSON($sys);
		}
		
		/**
		* Permintaan lupa password
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function password($param,$post) {
			// parameter
			$email = $post['email'];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_PASSWORD_FORGET);
			
			// set reset password
			$ret = $this->model->setResetPassword($email);
			$err = $ret['error'];
			$msg = $ret['message'];
			
			// kirim email
			if(!$err)
				list($err,$msg) = cHelper::sendEmailReset($email,$ret['username'],$ret['token']);
			
			if($err)
				$sys->setError($msg);
			
			return cHelper::getJSON($sys);
		}
		
		/**
		 * Ubah password
		 * @param array $param
		 * @param array $post
		 * @return string json
		 */
		function reset($param,$post) {
			// parameter
			$token = $post['token'];
			$passlama = $post['passwordLama'];
			$passbaru = $post['passwordBaru'];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_PASSWORD_RESET);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			
			// ganti password
			if(!$err)
				list($err,$msg) = $this->model->changePassword($row['userid'],$passlama,$passbaru);
			
			if($err)
				$sys->setError($msg);
			
			return cHelper::getJSON($sys);
		}
		
		/**
		* Menyimpan device ke sistem
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function device($param,$post) {
			// parameter
			$token = $param[0];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_DEVICE);
			
				// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg);
				return cHelper::getJSON($sys);
			}
			
			// update login
			if(!$err) {
				$record = $post;
				$record['regid'] = $record['regId'];
				$record['iddevice'] = $record['id'];
				$record['namadevice'] = $record['name'];
				
				unset($record['regId'],$record['id'],$record['name']);
				
				$err = $this->model->setDevice($record,$token);
			}
			
			if($err)
				$sys->setError($msg);
				
			
			$periode = $this->model->getPeriodeSekarang();
			// data user
			$user = new cUser();
			$data = $this->model->getDataUser($row['userid']);
			
			list($ismhs,$isdosen,$isemployee) = $this->model->getRolesByUsername($data['username']);
			
			// mengambil data mahasiswa
			if($ismhs) {
				$student = new cStudent();
				$mhs = $this->model->getDataMahasiswa($data['username']);
				$rows = $this->model->getListCalendar($periode, $row['userid'], $mhs['nim'], null);
			}else if($isdosen) {
				$lecturer = new cLecturer();
				$dosen = $this->model->getDataDosen($data['username']);
				$rows = $this->model->getListCalendar($periode, $row['userid'], null, $dosen['nip']);
			}
			$a_obj = array('data' => array());
				
			$obj = new cCalendarUser();
			$obj->setAttributes($rows);
			$obj->setOnly(null,array('kalenderAkademik','agendaPribadi', 'jadwalRutin', 'jadwalUjian'));
			$a_obj['data'] = $obj;
			
			if(empty($a_obj['data'])) {
				$a_obj = array();
				$sys->setInfos(cLang::getEmptyMsg(cLang::DATA_CALENDAR));
			}
			/*
			*/
			return cHelper::getJSON($sys,$a_obj['data']);
		}
		
		/**
		* Menampilkan data user login
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function me($param,$post) {
			// parameter
			$token = $param[0];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_ME);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg,401);
				return cHelper::getJSON($sys);
			}
			
			// data user
			$user = new cUser();
			$data = $this->model->getDataUser($row['userid']);
			
			list($ismhs,$isdosen,$isemployee) = $this->model->getRolesByUsername($data['username']);
			
			// mengambil data mahasiswa
			if($ismhs) {
				$student = new cStudent();
				$data2 = $this->model->getDataMahasiswa($data['username']);
				$rowd = $data2['hp'];
				$student->setAttributes($data2);
				$student->setOnly(null,array('nim','strata','angkatan'));
			}
			
			// mengambil data dosen
			if($isdosen) {
				$lecturer = new cLecturer();
				$data2 = $this->model->getDataDosen($data['username']);
				$rowd = $data2['hp'];
				$lecturer->setAttributes($data2);
				$lecturer->setOnly(null,array('nip','nidn'));
			}
			
			// mengambil data pegawai, kayak dosen
			if($isemployee) {
				$employee = new cEmployee();
				$data2 = $this->model->getDataDosen($data['username']);
				$rowd = $data2['hp'];
				$employee->setAttributes($data2);
				$employee->setOnly(null,array('nip'));
			}
			$data['hp'] = $rowd;
			$user->setAttributes($data);
			$user->setOnly(null,array('id','name','username','email','hp','image','facebook','google','userRole'));
			$user->setOnly('userRole',array('id','roles'));
			return cHelper::getJSON($sys,$user,$lecturer,$student,$employee);
		}

		function profile($param,$post) {
			return $this->me($param,$post);
		}

		function uploadprofile($param,$post){
			$token = $param[0];
			$hp = $post['hp'];
			$email = $post['email'];
			$file_image = $_FILES['file_image'];
			
			if($post or $_FILES){
				$pass = false;
			}
			if(!$pass){
				// cek token
				list($err,$msg,$row) = $this->model->getLoginByToken($token);

				$user = $this->model->getDataUser($row['userid']);

				// update profile
				if(!$err)
					list($err,$msg) = $this->model->updateprofile($row['userid'],$user['username'],$hp,$email, $file_image);
			}
			return $this->me($param,$post);
		}
		
		function calendar($param) {
			// parameter
			$token = $param[0];
			
			// set object
			$sys = new cSevimaSystem(cLang::ACT_CALENDAR);
			
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg);
				return cHelper::getJSON($sys);
			}
			
			
			$periode = $this->model->getPeriodeSekarang();
			// data user
			$user = new cUser();
			$data = $this->model->getDataUser($row['userid']);
			
			list($ismhs,$isdosen,$isemployee) = $this->model->getRolesByUsername($data['username']);
			
			// mengambil data mahasiswa
			if($ismhs) {
				$student = new cStudent();
				$mhs = $this->model->getDataMahasiswa($data['username']);
				$rows = $this->model->getListCalendar($periode, $row['userid'], $mhs['nim'], null);
			}else if($isdosen) {
				$lecturer = new cLecturer();
				$dosen = $this->model->getDataDosen($data['username']);
				$rows = $this->model->getListCalendar($periode, $row['userid'], null, $dosen['nip']);
			}
			$a_obj = array('data' => array());
				
			$obj = new cCalendarUser();
			$obj->setAttributes($rows);
			$obj->setOnly(null,array('kalenderAkademik','agendaPribadi', 'jadwalRutin', 'jadwalUjian'));
			$a_obj['data'] = $obj;
			
			// cek kosong
			if(empty($a_obj['data'])) {
				$a_obj = array();
				$sys->setInfos(cLang::getEmptyMsg(cLang::DATA_CALENDAR));
			}
			/*
			*/
			return cHelper::getJSON($sys,$a_obj['data']);
		}
		

		function uploadcalendar($param,$post){
			$token = $param[0];
			$datefrom = $post['dateFrom'];
			$dateto = $post['dateTo'];
			$kegiatan = $post['kegiatan'];

			// set object
			$sys = new cSevimaSystem(cLang::ACT_UPLOAD_CALENDAR);
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg);
				return cHelper::getJSON($sys);
			}
			
			if(is_array($kegiatan)&&is_array($datefrom)){
				$this->model->uploadcalendar($row['userid'],$kegiatan, $datefrom, $dateto);
			}
			
			return cHelper::getJSON($sys);
		}
		
		
		
		/**
		* Notification user
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function notification($param,$post) {
			$token = $param[0];
			$lastId	= $param[1];
		
			// set object
			$sys = new cSevimaSystem(cLang::ACT_NOTIFICATION);
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg);
				return cHelper::getJSON($sys);
			}
			
			require_once(Route::getViewPath('class_model_timeline'));
			$this->tModel = new mMobileTimeline();
			
			$a_notification 	= array('index'=>'notifications','data'=>array());	
			
			$data = $this->tModel->getListNotification($row["userid"], $latestId);
			$this->tModel->setReadNotification($row["userid"]);
			
			if(!empty($data)){
				foreach($data as $key => $val){
					$notification = new cNotification();
					$notification->setAttributes($val);
					
					$a_notification['data'][] = $notification;
				}
			}
			return cHelper::getJSON($sys,$a_notification);
		}
		
		
		/**
		* Upload Notification user
		* @param array $param
		* @param array $post
		* @return string json
		*/
		function uploadnotification($param,$post) {
			$token = $param[0];
			$notigicationId	= $post["notigicationId"];
		
			// set object
			$sys = new cSevimaSystem(cLang::ACT_UPLOAD_NOTIFICATION);
			// cek token
			list($err,$msg,$row) = $this->model->getLoginByToken($token);
			if($err) {
				$sys->setError($msg);
				return cHelper::getJSON($sys);
			}
			
			require_once(Route::getViewPath('class_model_timeline'));
			$this->tModel = new mMobileTimeline();
			
			if(!empty($row["userid"])&&strlen($notigicationId)>0)
				$this->tModel->uploadNotification($row["userid"], $notigicationId);
			
			return cHelper::getJSON($sys);
		}
		
	}