<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include
	require_once(Route::getViewPath('class_model'));
	
	/**
	 * Class untuk object mobile
	 * @author Sevima
	 * @version 1.0
	 */
	class cClass {
		protected $id;
		protected $model;
		
		/**
		* Constructor
		*/
		function __construct() {
			$this->model = new mMobile();
		}
		
		/**
		* Mendapatkan atribut obyek yang tidak null
		* @param mixed $elem
		* @return array
		*/
		function getAttributes($elem=null) {
			if(!isset($elem))
				$elem = $this;
			
			$data = array();
			$exclude = array('model');
			foreach($elem as $k => $v) {
				if(in_array($k,$exclude,true))
					continue;
				
				if(isset($v)) {
					if(is_array($v) or is_object($v))
						$data[$k] = self::getAttributes($v);
					else if(empty($v) and strval($v) != '0')
						$data[$k] = null;
					else
						$data[$k] = $v;
				}
			}
			
			if(empty($data))
				return null;
			else
				return $data;
		}
		
		/**
		* Set atribut obyek
		* @param array $row
		* @param boolean $setall jika true, maka mengisi semua atribut, jika tidak ada datanya diisi null
		*/
		function setAttributes($row,$setall=true) {
			$vars = get_class_vars(get_class($this));

			foreach($vars as $key => $val) {
				if($setall)
					$this->$key = ''; // ketika di getAttributes munculnya null

				foreach($row as $k => $v) {
					if(strcasecmp($k,$key) == 0) {
						if(!is_array($v) and !is_object($v))
							$v = strval($v);
						
						$this->$key = $v;
						break;
					}
				}
			}
		}
		
		/**
		* Set atribut dari atribut obyek yang merupakan obyek :D
		* @param string $attrpath
		* @param array $attr
		*/
		function setOnly($attrpath,$attr) {
			// ambil path
			$ptr = $this;
			
			if(!empty($attrpath)) {
				$trc = explode('.',$attrpath);
				
				$n = count($trc);
				for($i=0;$i<$n;$i++)
					$ptr = $ptr->$trc[$i];
			}
			
			// ambil atribut
			foreach($ptr as $k => $v) {
				if(empty($attr) or !in_array($k,$attr))
					unset($ptr->$k);
			}
		}
		
		/**
		* Mendapatkan nama index untuk JSON
		* @param bool $isarray
		* @return string
		*/
		function getIndexName($isarray=false) {
			$class = get_called_class();
			$class = strtolower(substr($class,1));
			
			if($isarray)
				return $class.'s';
			else
				return $class;
		}
	}
	
	class cAccount extends cClass {
		protected $username;
		protected $name;
		protected $email;
		protected $link;
		protected $accessToken;
	}

	class cFacebook extends cClass {
		protected $facebookId;
	}
	
	class cUnit extends cClass {
		protected $code;
		protected $name;
		
		/**
		* Mengisi nilai beberapa atribut dari satu nilai
		* @param string $kodeunit
		* @param bool $issdm
		*/
		function setAttsFromVal($kodeunit=null,$issdm=false) {
			$unit = $this->model->getDataUnit($kodeunit,$issdm);
			
			$this->id = $kodeunit;
			$this->code = $unit['kodeunit'];
			$this->name = $unit['namaunit'];
		}
	}
	
	// class api
	class cPerson extends cClass {
		protected $name;
		protected $gender;
	}
	
	class cSIA extends cClass {
		protected $name;
		protected $username;
		protected $email;
	}
	
	class cUser extends cPerson {
		protected $username;
		protected $email;
		protected $hp;
		protected $image;
		protected $userRole;
		protected $sia;
		protected $facebook;
		protected $google;
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			// untuk atribut obyek
			$image = new cImage();
			$image->setAttsFromVal($row['id']);
			
			$row['image'] = $image;
			
			$sia = new cSIA();
			$sia->setAttributes($row);
			
			$row['sia'] = $sia;
			
			$userrole = new cUserRole();
			if(isset($row['userrole']))
				$userrole->setAttributes($row);
			else
				$userrole->setAttsFromVal($row['username']);
			
			$row['userrole'] = $userrole;
			
			parent::setAttributes($row);
		}
	}
	
	class cStudent extends cUser {
		protected $nim;
		protected $strata;
		protected $entryPeriod;
		protected $angkatan;
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			// untuk atribut obyek
			$periode = new cPeriod();
			$periode->setAttsFromVal($row['angkatan']);
			
			$row['entryPeriod'] = $periode;
			$row['angkatan'] = substr($row['angkatan'],0,4);
			
			// cek jurusan
			/* $unit = new cDepartment();
			$unit->setAttsFromVal($row['kodeunit']);
			
			$row['department'] = $unit; */
			
			parent::setAttributes($row);
		}
	}
	
	class cEmployee extends cUser {
		protected $nip;
	}
	
	class cLecturer extends cEmployee {
		protected $nidn;
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			// cek homebase
			/* $rowu = $this->model->getDataUnit($row['idunit'],true);
			
			if($rowu['isakademik'] == 'Y' and $rowu['level'] == 3) {
				$unit = new cFaculty();
				$unit->setAttsFromVal($row['kodeunit'],true);
				
				$row['homeBaseFaculty'] = $unit;
			}
			else {
				$unit = new cDepartment();
				$unit->setAttsFromVal($row['kodeunit'],true);
				
				$row['homeBaseDepartment'] = $unit;
			} */
			
			parent::setAttributes($row);
		}
		
		/**
		* Mengisi nilai beberapa atribut dari satu nilai
		* @param string $id
		*/
		function setAttsFromVal($id) {
			$row = $this->model->getDataDosen($id);
			
			$this->setAttributes($row);
		}
	}
	
	class cAccountSia extends cAccount {
		protected $password;
	}
	
	class cAccountFb extends cAccount {
	}
	
	class cAccountGoogle extends cAccount {
	}
	
	class cRole extends cClass {
		protected $name;
		public $department; // untuk diambil di cUserRole :D
		public $faculty; // untuk diambil di cUserRole :D
		public $university; // untuk diambil di cUserRole :D
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			// untuk atribut obyek
			$university = new cUniversity();
			$faculty = new cFaculty();
			$department = new cDepartment();
			
			$rowu = $this->model->getDataUnit($row['kodeunit']);
			if($rowu['level'] == 2) {
				$department->setAttsFromVal($row['kodeunit']);
				$faculty->setAttsFromVal($rowu['kodeunitparent']);
			}
			else if($rowu['level'] == 1)
				$faculty->setAttsFromVal($row['kodeunit']);
			
			$row['department'] = $department;
			$row['faculty'] = $faculty;
			$row['university'] = $university;
			
			parent::setAttributes($row);
		}
	}
	
	class cUserRole extends cClass {
		protected $roles;
		protected $department;
		protected $faculty;
		protected $university;
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			if(!empty($row['userrole'])) {
				$roles = array();
				foreach($row['userrole'] as $i => $userrole) {
					$role = new cRole();
					$role->setAttributes($userrole);
					
					// comot satu ah
					if($i == 0) {
						$row['department'] = $role->department;
						$row['faculty'] = $role->faculty;
						$row['university'] = $role->university;
					}
					
					$role->setOnly('department',array('id','name'));
					$role->setOnly('faculty',array('id','name'));
					$role->setOnly('university',array('id','name'));
					
					$roles[] = $role;
				}
				
				$row['roles'] = $roles;
			}
			
			parent::setAttributes($row);
		}
		
		/**
		* Mengisi nilai beberapa atribut dari satu nilai
		* @param string $username
		*/
		function setAttsFromVal($username) {
			$userid = $this->model->getUserID($username);
			$row = $this->model->getDataUser($userid);
			
			$this->setAttributes($row);
		}
	}
	
	class cUniversity extends cUnit {
		protected $address;
		protected $icon;
		
		/**
		* Constructor
		*/
		function __construct() {
			parent::__construct();
			
			$unit = $this->model->getDataUniversitas();
			
			$this->id = $unit['kodeunit'];
			$this->code = $unit['kodeunit'];
			$this->name = $unit['namaunit'];
		}
	}
	
	class cDepartment extends cUnit {
	}
	
	class cFaculty extends cUnit {
		protected $departments;
		
		/**
		* Mendapatkan nama index untuk JSON
		* @param bool $isarray
		* @return string
		*/
		function getIndexName($isarray=false) {
			if($isarray)
				return 'faculties';
			else
				return parent::getIndexName($isarray);
		}
	}
	
	class cPeriod extends cClass {
		protected $id;
		protected $name;
		protected $year;
		protected $semester;
		
		/**
		* Mengisi nilai beberapa atribut dari satu nilai
		* @param string $periode
		*/
		function setAttsFromVal($periode) {
			$a_semester = $this->model->getListSemester();

			$semester = substr($periode,-1);
			$tahun = substr($periode,0,4);
			
			$this->id = $periode;
			$this->name = $a_semester[$semester].' '.$tahun.'/'.($tahun+1);
			$this->year = $semester;
			$this->semester = $tahun;
		}
	}
	
	class cSubject extends cClass {
		protected $name;
		
		/**
		* Mengisi nilai beberapa atribut dari satu nilai
		* @param string $kodemk
		*/
		function setAttsFromVal($kodemk) {
			$this->id = $kodemk;
			$this->name = $this->model->getNamaMataKuliah($kodemk);
		}
	}
	
	class cStudentGroup extends cClass {
		protected $name;
		protected $subject;
		protected $credit;
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			// untuk atribut obyek
			$subject = new cSubject();
			$subject->setAttsFromVal($row['kodemk']);
			
			$row['name'] = $row['kelasmk'];
			$row['subject'] = $subject;
			$row['credit'] = $row['sks'];
			
			parent::setAttributes($row);
		}
	}
	
	class cTeachingLecturer extends cClass {
		protected $studentGroup;
		protected $lecturer;
		protected $status;
	}
	
	class cTeachingClass extends cClass {
		protected $studentGroup;
		protected $meetingNumber;
		protected $meetingType;
		protected $schedule;
		protected $studentPresences;
	}
	
	class cRoom extends cClass {
		protected $name;
		protected $building;
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			$row['id'] = $row['koderuang'];
			$row['name'] = $row['koderuang'];
			
			parent::setAttributes($row);
		}
	}
	
	class cBuilding extends cClass {
		protected $name;
	}
	
	class cSchedule extends cClass {
		protected $noDay;
		protected $date;
		protected $timeFrom;
		protected $timeTo;
		protected $room;
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			// untuk atribut obyek
			$room = new cRoom();
			$room->setAttributes($row);
			
			$row['room'] = $room;
			
			parent::setAttributes($row);
		}
	}
	
	class cJadwal extends cClass {
		protected $dateFrom;
		protected $dateTo;
		protected $name;
		protected $room;
		protected $studentGroup;
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			// untuk atribut obyek
			$room = new cRoom();
			
			$room->setAttributes($row);
			
			$row['room'] = $room;
			
			$class = new cStudentGroup();

			$class->setAttributes($row);
			
			$row['studentGroup'] = $class;
			
			parent::setAttributes($row);
		}
	}

	class Calendar extends cClass{
		protected $AgendaPribadi;
		protected $JadwalRutin;
		protected $JadwalUjian;

		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			// untuk atribut obyek
			$room = new cRoom();
			
			$room->setAttributes($row);
			
			$row['room'] = $room;
			
			$class = new cStudentGroup();

			$class->setAttributes($row);
			
			$row['studentGroup'] = $class;
			
			parent::setAttributes($row);
		}
	}

	class cEvent extends cClass {
		protected $dateFrom;
		protected $dateTo;
		protected $name;
		protected $room;
		protected $studentGroup;
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			// untuk atribut obyek
			$room = new cRoom();
			$room->setAttributes($row);
			
			$row['room'] = $room;
			
			$class = new cStudentGroup();
			$class->setAttributes($row);
			
			$row['studentGroup'] = $class;
			
			parent::setAttributes($row);
		}
	}
	
	class cStudy extends cClass {
		protected $period;
		protected $studentGroup;
		protected $student;
		protected $status;
		protected $grade;
		protected $gradeName;
		
		/**
		* Mendapatkan nama index untuk JSON
		* @param bool $isarray
		* @return string
		*/
		function getIndexName($isarray=false) {
			if($isarray)
				return 'studies';
			else
				return parent::getIndexName($isarray);
		}
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			// simpan id dulu
			$t_id = $row['id'];
			unset($row['id']);
			
			// untuk atribut obyek
			$class = new cStudentGroup();
			$class->setAttributes($row);
			
			$row['studentGroup'] = $class;
			$row['id'] = $t_id;
			
			parent::setAttributes($row);
		}
	}
	
	class cKHS extends cClass {
		protected $totalSKS;
		protected $totalIP;
		protected $status;
		protected $period;
		public $studies;
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			// untuk atribut obyek
			$periode = new cPeriod();
			$periode->setAttsFromVal($row['period']);
			
			$row['period'] = $periode;
			
			// untuk atribut array
			$data = array();
			foreach($row['studies'] as $t_studies) {
				$study = new cStudy();
				$lecturer = new cLecturer();
				$schedule = new cSchedule();
				
				$study->setAttributes($t_studies);
				$lecturer->setAttsFromVal($t_studies['nip']);
				$schedule->setAttributes($t_studies);
				
				// ambil beberapa atribut saja
				$study->setOnly(null,array('id','studentGroup','grade','gradeName'));
				$lecturer->setOnly(null,array('name'));
				$schedule->setOnly(null,array('noDay','timeFrom','timeTo','room'));
				$schedule->setOnly('room',array('name'));
				
				$t_data = array();
				$t_data[$study->getIndexName()] = $study;
				$t_data[$lecturer->getIndexName()] = $lecturer;
				$t_data[$schedule->getIndexName()] = $schedule;
				
				$data[] = $t_data;
			}
			
			$row['studies'] = $data;
			
			parent::setAttributes($row);
		}
	}

	
	class cTranscript extends cClass {
		protected $totalSKS;
		protected $totalIP;
		public $studies;
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			
			// untuk atribut array
			$data = array();
			foreach($row['studies'] as $t_studies) {
				$study = new cStudy();
				$lecturer = new cLecturer();
				$schedule = new cSchedule();
				
				$study->setAttributes($t_studies);
				$lecturer->setAttsFromVal($t_studies['nip']);
				$schedule->setAttributes($t_studies);
				
				// ambil beberapa atribut saja
				$study->setOnly(null,array('id','studentGroup','grade','gradeName'));
				
				$t_data = array();
				$t_data[$study->getIndexName()] = $study;
				
				$data[] = $t_data;
			}
			
			$row['studies'] = $data;
			
			parent::setAttributes($row);
		}
	}

	class cKRS extends cClass {
		protected $status;
		protected $period;
		public $studies;
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			// untuk atribut obyek
			$periode = new cPeriod();
			$periode->setAttsFromVal($row['period']);
			
			$row['period'] = $periode;
			
			// untuk atribut array
			$data = array();
			foreach($row['studies'] as $t_studies) {
				$study = new cStudy();
				$lecturer = new cLecturer();
				$schedule = new cSchedule();
				
				$study->setAttributes($t_studies);
				$lecturer->setAttsFromVal($t_studies['nip']);
				$schedule->setAttributes($t_studies);
				
				// ambil beberapa atribut saja
				$study->setOnly(null,array('id','studentGroup','grade','gradeName'));
				$lecturer->setOnly(null,array('name'));
				$schedule->setOnly(null,array('noDay','timeFrom','timeTo','room'));
				$schedule->setOnly('room',array('name'));
				
				$t_data = array();
				$t_data[$study->getIndexName()] = $study;
				$t_data[$lecturer->getIndexName()] = $lecturer;
				$t_data[$schedule->getIndexName()] = $schedule;
				
				$data[] = $t_data;
			}
			
			$row['studies'] = $data;
			
			parent::setAttributes($row);
		}
	}
	
	class cPresence extends cClass {
		protected $studentGroup;
		protected $sumPresence;
		protected $sumPermitting;
		protected $sumAbsent;
		protected $sumMeeting;

		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			// untuk atribut obyek
			$class = new cStudentGroup();
			$class->setAttributes($row);
			
			$row['studentGroup'] = $class;
			
			parent::setAttributes($row);
		}
	}
	
	class cBillInfo extends cClass {
		protected $period;
		protected $description;
		protected $bill;
		protected $fine;
		protected $status;
		protected $dueDate;
		
		/**
		* Mendapatkan nama index untuk JSON
		* @param bool $isarray
		* @return string
		*/

		function getIndexName($isarray=false) {
			if($isarray)
				return 'bills';
			else
				return 'bill';
		}

		function setAttributes($row){
		    $periode = new cPeriod();
			$periode->setAttsFromVal($row['periode']);
			
			$row['period'] = $periode;

			parent::setAttributes($row);
		}
	}


	class cPayment extends cClass {
		protected $period;
		protected $description;
		protected $payment;
		protected $fine;
		protected $status;
		protected $dueDate;
		
		/**
		* Mendapatkan nama index untuk JSON
		* @param bool $isarray
		* @return string
		*/

		function getIndexName($isarray=false) {
			if($isarray)
				return 'payments';
			else
				return 'payment';
		}

		function setAttributes($row){
		    $periode = new cPeriod();
			$periode->setAttsFromVal($row['periode']);
			
			$row['period'] = $periode;
				
			parent::setAttributes($row);
		}
	}
	
	
	class cCalendar extends cClass {
		protected $events;
		protected $period;
		protected $linkDownload;
		

		function setAttributes($row){
		  
			parent::setAttributes($row);
		}
	}
	
	
	class cCalendarUser extends cClass {
		protected $agendaPribadi;
		protected $kalenderAkademik;
		protected $jadwalUjian;
		protected $jadwalRutin;
		

		function setAttributes($row){
		  
			parent::setAttributes($row);
		}
	}

	class cStudentPresence extends cClass {
		protected $student;
		protected $isAttending;
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			// untuk atribut obyek
			$t_id = $row['id'];
			$row['id'] = $row['nim'];
			
			$student = new cStudent();
			$student->setAttributes($row);
			
			$row['id'] = $t_id;
			$row['student'] = $student;
			
			parent::setAttributes($row);
		}
	}
	
	class cDevice extends cClass {
		protected $name;
		protected $regId;
		protected $brand;
		protected $manufacturer;
		protected $model;
		protected $product;
	}
	
	class cImage extends cClass {
		protected $name;
		protected $ext;
		protected $thumbPath;
		
		/**
		* Mengisi nilai beberapa atribut dari satu nilai
		* @param string $kodeunit
		* @param bool $issdm
		*/
		function setAttsFromVal($userid, $ext = '.png') {
			global $conf;
			
			$this->id = $userid;
			$this->name = $userid;
			$this->ext = $ext;
			$server = $_SERVER['SCRIPT_FILENAME'];
			$cek = str_replace("index.php", "profpic/".$this->name.$this->ext, $server);
			if(file_exists($cek))
				$this->thumbPath = $conf['root'].$conf['url'].'profpic/'.$this->name.$this->ext;
			else
				$this->thumbPath = $conf['root'].$conf['url'].'profpic/default.png';
		}
	}
	
	class cSevimaSystem extends cClass {
		protected $senderId;
		protected $regId;
		protected $token;
		protected $errCode;
		protected $errMessage;
		protected $infos;
		
		protected $func;
		
		/**
		* Constructor, override parent
		* @param string $func
		*/
		function __construct($func=null) {
			if(empty($func))
				$this->func = cLang::ACT;
			else
				$this->func = $func;
			
			$this->errCode = 200;
			$this->errMessage = ''; // string kosong
			$this->infos = array(cLang::getSuccessMsg($this->func));
		}
		
		/**
		* Mendapatkan nama index untuk JSON
		* @return string
		*/
		function getIndexName() {
			return 'system';
		}
		
		/**
		* Mendapatkan atribut obyek yang tidak null
		* @return array
		*/
		function getAttributes() {
			$data = parent::getAttributes();
			
			unset($data['func']);
			
			return $data;
		}
		
		/**
		* Cek apakah error
		* @return integer
		*/
		function isError() {
			// asumsi tidak kosong karena didefault di constructor
			$errcode = strval($this->errCode);
			
			if($errcode[0] == '2')
				return false;
			else
				return true;
		}
		
		/**
		* Set token
		* @param string $token
		*/
		function setToken($token) {
			$this->token = $token;
		}
		
		/**
		* Set data bila error
		* @param array $data
		* @param string $errmsg
		* @param string $errcode
		*/
		function setError($errmsg=null,$errcode=null) {
			if(empty($errmsg))
				$errmsg = cLang::getFailedMsg($this->func);
			if(empty($errcode))
				$errcode = 401;
			
			$this->errCode = $errcode;
			$this->errMessage = $errmsg;
			$this->infos = array();
		}
		
		/**
		* Set informasi
		* @param array $info
		*/
		function setInfos($infos) {
			if(!is_array($infos))
				$infos = array($infos);
			
			$this->infos = $infos;
		}
	}
	
	// class api
	
	class cMember extends cClass{
		protected $id;
		protected $role;
		protected $user;
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row){
			$this->id = $row['id'];
			$this->role = $row['role'];
			
			$imgurl = $_SERVER['SERVER_ADDR'].$_SERVER['SCRIPT_NAME'];
			$pos = strrpos($imgurl,'/');
			$imgurl = substr($imgurl,0,$pos);
			
			
			if(file_exists($conf['root'].$conf['url'].'profpic/'.$row['userid'].'.png'))
				$image = 'http://'.$imgurl.'/profpic/'.$row['userid'].'.png';
			else
				$image = null;
			
			$this->id = $row['id'];
			$this->role = $row['role'];
			$this->user = array(
								'id'=>$row['userid'],
								'name'=>$row['userdesc'],
								'image'=>array('thumbPath'=>$image)
								);
			
			if(file_exists($conf['root'].$conf['url'].'profpic/'.$this->userid.'.png'))
				$image = $conf['root'].$conf['url'].'profpic/'.$this->userid.'.png';
			else
				$image = null;
		}
	}
	
	class cGroup extends cClass{
		protected $name;
		protected $groupMembers;
		protected $rsc;
		protected $image;
		protected $desc;
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row){
			$this->name = $row['name'];
			$this->id = $row['id'];
			$this->rsc = $row['rsc'];
			
			if(!empty($row['namadosen'])){
				$group = array();
				$group[] = array('user'=>array('name'=>$row['namadosen']));
				$this->groupMembers = $group;
			}
			if(!empty($row['desc']))
				$this->desc = $row['desc'];
			$imgurl = $_SERVER['SERVER_ADDR'].$_SERVER['SCRIPT_NAME'];
			$pos = strrpos($imgurl,'/');
			$imgurl = substr($imgurl,0,$pos);
				
			if(strlen($row['image'])==0){
			
				$row['image'] = 'http://'.$imgurl.'/image/ic_group.png';
			}
			$this->image = array('thumbPath' => 'http://'.$imgurl.'/image/'.$row['image']);
			
		}
	}
	
	class cTimeLine extends cClass{
		protected $user;		
		protected $image;
		protected $file;
		protected $video;
		protected $status;		
		protected $timeStamp;
		protected $url;
		protected $group;
		protected $likes;
		protected $isLike;
		protected $isDelete;
		protected $comments;
		
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			
			$imgurl = $_SERVER['SERVER_ADDR'].$_SERVER['SCRIPT_NAME'];
			$pos = strrpos($imgurl,'/');
			$imgurl = substr($imgurl,0,$pos);
			
			
			if(file_exists($conf['root'].$conf['url'].'profpic/'.$row['userid'].'.png'))
				$image = 'http://'.$imgurl.'/profpic/'.$row['userid'].'.png';
			else
				$image = null;
			
			$this->id = $row['timeline_id'];
			if(isset($row['userid']))
			$this->user = array(
								'id'=>$row['userid'],
								'name'=>$row['userdesc'],
								'image'=>array('thumbPath'=>$image)
								);
			
/*			if($row['timeline_photo'])
				$this->image = 'http://'.$imgurl.'/timeline_picture/'.$row['timeline_photo'];

			else if($row['timeline_file'])
				$this->file  = 'http://'.$imgurl.'/timeline_file/'.$row['timeline_file'];

			else if($row['timeline_video'])
				$this->video = 'http://'.$imgurl.'/timeline_video/'.$row['timeline_video'];*/

			/* NEW FEATURE */			
			$this->image = cHelper::generateImage($row['timeline_id'], $row['timeline_photo'], 'timeline_picture');
			$this->file = cHelper::generateFile($row['timeline_id'], $row['timeline_file'], 'timeline_picture');
			$this->video = cHelper::generateFile($row['timeline_id'], $row['timeline_video'], 'timeline_picture');
			

			if($row['timeline_link'])
				$this->url = $row['timeline_link'];
				
			if($row['isDelete'])
				$this->isDelete = $row['isDelete'];
				
			if(isset($row['group_name'])){
				$this->group = array(
										'id'=>$row['group_id'],
										'name'=>$row['group_name']
							);
			}

			$this->status = $row['timeline_status'];
			$this->likes = $row['likes'];
			if(isset($row['isLike']))
				$this->isLike = $row['isLike'];
			
			if(isset($row['comments'])){
				$this->comments = array();
				foreach($row['comments'] as $row2){
					$comment = new cComment();
					$comment->setAttributes($row2);
					$this->comments[] = $comment;
				}
			}
			if(isset($row['timeline_date']))
			$this->timeStamp = strtotime($row['timeline_date'])."000";//milisecond dianggep kosong :P

		}
	}
	
	
	
	class cNotification extends cClass{
		protected $id;		
		protected $description;
		protected $timeStamp;
		protected $state;
		protected $launchId;
		protected $isRead;
		protected $user;
		
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			
			$imgurl = $_SERVER['SERVER_ADDR'].$_SERVER['SCRIPT_NAME'];
			$pos = strrpos($imgurl,'/');
			$imgurl = substr($imgurl,0,$pos);
			
			
			if(file_exists($conf['root'].$conf['url'].'profpic/'.$row['userid'].'.png'))
				$image = 'http://'.$imgurl.'/profpic/'.$row['userid'].'.png';
			else
				$image = null;
			
			$this->id = $row['id'];
			if(isset($row['userid']))
			$this->user = array(
								'id'=>$row['userid'],
								'name'=>$row['userdesc'],
								'image'=>array('thumbPath'=>$image)
								);
			
			if(isset($row['launchid']))
				$this->launchId = $row["launchid"];
				
			if(isset($row['timestamp']))
				$this->timeStamp = strtotime($row['timestamp'])."000";//milisecond dianggep kosong :P
			if(isset($row['description']))
				$this->description = $row['description'];
			if(isset($row['state']))
				$this->state = $row['state'];
			if(isset($row['isread']))
				$this->isRead = $row['isread'];

		}
	}
	
	class cCountNotif extends cClass{
		protected $read;		
		protected $notRead;
		function setAttributes($row) {
			if(isset($row['read'])){
				$this->read = $row['read'];
			}
			if(isset($row['notRead'])){
				$this->notRead = $row['notRead'];
			}
		}
		
	}
	class cPostTimeLine extends cClass{
		protected $user;		
		protected $image;
		protected $file;
		protected $video;
		protected $status;		
		protected $timeStamp;
		protected $url;
		protected $group;
		protected $likes;
		protected $isLike;
		protected $isDelete;
		protected $comments;
		
		
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			
			$imgurl = $_SERVER['SERVER_ADDR'].$_SERVER['SCRIPT_NAME'];
			$pos = strrpos($imgurl,'/');
			$imgurl = substr($imgurl,0,$pos);
			
			
			if(file_exists($conf['root'].$conf['url'].'profpic/'.$row['userid'].'.png'))
				$image = 'http://'.$imgurl.'/profpic/'.$row['userid'].'.png';
			else
				$image = null;
			
			$this->id = $row['timeline_id'];
			if(isset($row['userid']))
			$this->user = array(
								'id'=>$row['userid'],
								'name'=>$row['userdesc'],
								'image'=>array('thumbPath'=>$image)
								);
			
			$this->image = cHelper::generateImage($row['timeline_id'], $row['timeline_photo'], 'timeline_picture');
			$this->file = cHelper::generateFile($row['timeline_id'], $row['timeline_file'], 'timeline_picture');
			$this->video = cHelper::generateFile($row['timeline_id'], $row['timeline_video'], 'timeline_picture');
			

			if($row['timeline_link'])
				$this->url = $row['timeline_link'];

			$this->status = $row['timeline_status'];
			$this->likes = $row['likes'];
			if(isset($row['isDelete']))
				$this->isDelete = $row['isDelete'];
			if(isset($row['isLike']))
				$this->isLike = $row['isLike'];
			
			if(isset($row['comments'])){
				$this->comments = array();
				foreach($row['comments'] as $row2){
					$comment = new cComment();
					$comment->setAttributes($row2);
					$this->comments[] = $comment;
				}
			}
			if(isset($row['timeline_date']))
			$this->timeStamp = strtotime($row['timeline_date'])."000";//milisecond dianggep kosong :P

		}
	}
	
	
	
	class cComment extends cClass{
		protected $id;		
		protected $user;
		protected $text;
		protected $date;
		protected $isDelete;
		
		
		/**
		* Set atribut obyek
		* @param array $row
		*/
		function setAttributes($row) {
			
			$imgurl = $_SERVER['SERVER_ADDR'].$_SERVER['SCRIPT_NAME'];
			$pos = strrpos($imgurl,'/');
			$imgurl = substr($imgurl,0,$pos);
			
			
			if(file_exists($conf['root'].$conf['url'].'profpic/'.$row['userid'].'.png'))
				$image = 'http://'.$imgurl.'/profpic/'.$row['userid'].'.png';
			else
				$image = null;
			
			$this->id = $row['comment_id'];
			$this->text = $row['comment_text'];
			if(isset($row['isDelete']))
				$this->isDelete = $row['isDelete'];
			
			if(isset($row['userid']))
			$this->user = array(
								'id'=>$row['userid'],
								'name'=>$row['userdesc'],
								'image'=>array('thumbPath'=>$image)
								);
			if(isset($row['comment_date']))
			$this->date = strtotime($row['comment_date'])."000";//milisecond dianggep kosong :P

		}
	}
	