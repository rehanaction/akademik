<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mMengajar extends mModel {
		const schema = 'akademik';
		const table = 'ak_mengajar';
		const order = 'nohari,jammulai';
		const key = 'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskul,kelompok,nipdosen';
		const label = 'mengajar';
		
		// mendapatkan kueri list
		function listQuery() {
			global $r_key;
			
			$periode = Akademik::getPeriode();
			
			$sql = "select a.thnkurikulum,a.periode,a.kodeunit, c.namaunit ,a.kodemk,a.kelasmk,a.jeniskul,a.kelompok,akademik.f_namahari(b.nohari) AS namahari,
					d.namamk,d.sks,d.semmk,b.jammulai,b.jamselesai,akademik.f_namahari(coalesce(b.nohari2,b.nohari)) AS namahari2,b.jammulai2,
					b.jamselesai2,b.koderuang,coalesce(b.jumlahpeserta,0) as jmlpeserta,b.sistemkuliah, b.tgljadwal1 AS startdate,a.nipdosen,a.tugasmengajar,case b.isonline when -1 then 'Online' else 'Tatap Muka' end as isonline from ".static::table('ak_mengajar')." a
					join ".static::table('ak_kelas')." b on(a.periode=b.periode and a.kodeunit=b.kodeunit and a.thnkurikulum=b.thnkurikulum and a.kodemk=b.kodemk and a.kelasmk=b.kelasmk)
					join gate.ms_unit c on a.kodeunit = c.kodeunit
					join ".static::table('ak_kurikulum')." d on(a.thnkurikulum=d.thnkurikulum and a.kodeunit=d.kodeunit and a.kodemk=d.kodemk)";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'periode': return "a.periode = '$key'";
				case 'nipdosen': return "a.nipdosen = '$key'";
				case 'semmk': return "m.semmk = '$key'";
				case 'tugasmengajar': return "a.tugasmengajar=$key";
				case 'unit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
		}
		function getkelasnila($conn,$r_kodeunit,$r_periode,$r_thnkurikulum,$r_kodemk,$r_kelasmk)
		{
			$sql = "select a.thnkurikulum,a.periode,a.kodeunit, c.namaunit ,a.kodemk,a.kelasmk,a.jeniskul,a.kelompok,akademik.f_namahari(b.nohari) AS namahari, d.namamk,d.sks,d.semmk,b.jammulai,b.jamselesai,akademik.f_namahari(coalesce(b.nohari2,b.nohari)) AS namahari2,b.jammulai2, b.jamselesai2,b.koderuang,coalesce(b.jumlahpeserta,0) as jmlpeserta,b.sistemkuliah, b.tgljadwal1 AS startdate,a.nipdosen,a.tugasmengajar from akademik.ak_mengajar a join akademik.ak_kelas b on(a.periode=b.periode and a.kodeunit=b.kodeunit and a.thnkurikulum=b.thnkurikulum and a.kodemk=b.kodemk and a.kelasmk=b.kelasmk) join gate.ms_unit c on a.kodeunit = c.kodeunit join akademik.ak_kurikulum d on(a.thnkurikulum=d.thnkurikulum and a.kodeunit=d.kodeunit and a.kodemk=d.kodemk) where a.periode = '$r_periode' and a.thnkurikulum='$r_thnkurikulum' and a.kodeunit='$r_kodeunit' and a.kelasmk='$r_kelasmk' and a.kodemk='$r_kodemk' and a.tugasmengajar='-1' order by namamk,kelasmk";

				return $conn->GetOne($sql);
		}
		// cek ajar kelas
		function isAjar($conn,$kelas,$nip='') {
			require_once(Route::getModelPath('kelas'));
			
			if(empty($nip))
				$nip = Modul::getUserName();
			
			$sql = "select 1 from ".static::table()." where ".mKelas::getCondition($kelas);
			$cek = $conn->GetOne($sql);
			
			if(empty($cek))
				return false;
			else
				return true;
		}
		
		// mendapatkan data mengajar
		function getDataAjarMingguan($conn,$a_kolom,$r_sort,$a_filter) {
			/*$sql = "select * from ".static::table('v_jadwalmingguandosen')."
					where nipdosen = '$nip' order by nohari, jammulai";*/
			
			$sql = "SELECT a.thnkurikulum,a.periode,a.nipdosen, k.kodeunit, k.kodemk, m.namamk, k.kelasmk, m.sks, 
			k.koderuang,k.nohari,akademik.f_namahari(k.nohari) AS namahari, k.jammulai, k.jamselesai, 
			k.koderuang2,k.nohari2, akademik.f_namahari(k.nohari2) AS namahari2, k.jammulai2, k.jamselesai2, 
			k.koderuang3,k.nohari3, akademik.f_namahari(k.nohari3) AS namahari3, k.jammulai3, k.jamselesai3, 
			k.koderuang4,k.nohari4, akademik.f_namahari(k.nohari4) AS namahari4, k.jammulai4, k.jamselesai4,
			case k.isonline when -1 then 'Online' else 'Tatap Muka' end as isonline
				   FROM akademik.ak_kelas k
				   JOIN akademik.ak_matakuliah m ON k.thnkurikulum::text = m.thnkurikulum::text AND k.kodemk::text = m.kodemk::text
				   JOIN akademik.ak_mengajar a ON a.thnkurikulum::text = k.thnkurikulum::text AND a.periode::text = k.periode::text AND a.kodeunit::text = k.kodeunit::text AND a.kodemk::text = k.kodemk::text AND a.kelasmk::text = k.kelasmk::text and a.jeniskul='K' and a.tugasmengajar='-1'";
							
			//return $conn->GetArray($sql);
			//print_r($a_filter);
			return static::getListData($conn,$a_kolom,$r_sort,$a_filter,$sql);
		}
		// mendapatkan data mengajar
		function getDataAjarMingguanPrak($conn,$a_kolom,$r_sort,$a_filter) {
			/*$sql = "select * from ".static::table('v_jadwalmingguandosen')."
					where nipdosen = '$nip' order by nohari, jammulai";*/
			
			$sql = "SELECT a.nipdosen, k.kodeunit, k.kodemk, m.namamk, k.kelasmk, m.sks, 
					k.koderuang,k.nohari,akademik.f_namahari(k.nohari) AS namahari, k.jammulai, k.jamselesai,k.kelompok
				   FROM akademik.ak_kelaspraktikum k
				   JOIN akademik.ak_matakuliah m ON k.thnkurikulum::text = m.thnkurikulum::text AND k.kodemk::text = m.kodemk::text
				   JOIN akademik.ak_mengajar a ON a.thnkurikulum::text = k.thnkurikulum::text AND a.periode::text = k.periode::text AND a.kodeunit::text = k.kodeunit::text AND a.kodemk::text = k.kodemk::text AND a.kelasmk::text = k.kelasmk::text and a.jeniskul=k.jeniskul and a.kelompok=k.kelompok";
							
			//return $conn->GetArray($sql);
			return static::getListData($conn,$a_kolom,$r_sort,$a_filter,$sql);
		}
		// mendapatkan data mengajar
		function getDataAjarMingguanSpa($conn,$nip) {
			/*$sql = "select * from ".static::table('v_jadwalmingguandosen')."
					where nipdosen = '$nip' order by nohari, jammulai";*/
			$periode = Akademik::getPeriodeSpa();
			$sql = "SELECT a.nipdosen, k.kodeunit, k.kodemk, m.namamk, k.kelasmk, m.sks, 
			k.koderuang,k.nohari,akademik.f_namahari(k.nohari) AS namahari, k.jammulai, k.jamselesai, 
			k.koderuang2,k.nohari2, akademik.f_namahari(k.nohari2) AS namahari2, k.jammulai2, k.jamselesai2, 
			k.koderuang3,k.nohari3, akademik.f_namahari(k.nohari3) AS namahari3, k.jammulai3, k.jamselesai3, 
			k.koderuang4,k.nohari4, akademik.f_namahari(k.nohari4) AS namahari4, k.jammulai4, k.jamselesai4
				   FROM akademik.ak_kelas k
				   JOIN akademik.ak_matakuliah m ON k.thnkurikulum::text = m.thnkurikulum::text AND k.kodemk::text = m.kodemk::text
				   JOIN akademik.ak_mengajar a ON a.thnkurikulum::text = k.thnkurikulum::text AND a.periode::text = k.periode::text AND a.kodeunit::text = k.kodeunit::text AND a.kodemk::text = k.kodemk::text AND a.kelasmk::text = k.kelasmk::text
				   where a.nipdosen = '$nip' and a.periode='$periode' order by nohari, jammulai";
							
			return $conn->GetArray($sql);
		}
		
		function getDataAjarHarian($conn,$nip) {
			$sql = "select * from ".static::table('v_jadwalhariandosen')."
					where nipjadwal = '$nip' and tugasmengajar='-1' order by tglkuliah, nohari, waktumulai";
			
			return $conn->GetArray($sql);
		}
		function getTugasMengajar($conn,$kolom,$sort,$filter){
			$sql = "select a.periode , a.thnkurikulum , a.kodeunit , a.kodemk , a.kelasmk , a.nipdosen , a.jeniskul , a.kelompok ,a.tugasmengajar,m.namamk,m.sks,m.semmk,
					akademik.f_namalengkap(p.gelardepan, p.namadepan, p.namatengah, p.namabelakang, p.gelarbelakang) as namapengajar,
					a.jeniskul,a.kelompok,a.nipdosen
				   FROM akademik.ak_mengajar a
				   JOIN akademik.ak_kurikulum m ON a.thnkurikulum = m.thnkurikulum AND a.kodemk = m.kodemk and a.kodeunit=m.kodeunit
				   JOIN sdm.ms_pegawai p ON a.nipdosen = p.idpegawai::text
				   join gate.ms_unit u on u.kodeunit=a.kodeunit";
			return static::getListData($conn,$kolom,$sort,$filter,$sql);
		}
		
		function getDosen($conn, $periode, $kodemk, $kelasmk,$kodeunit){
			$sql = "select a.kodemk, a.kelasmk, akademik.f_namalengkap(p.gelardepan, p.namadepan, p.namatengah, p.namabelakang, p.gelarbelakang) as namapengajar from akademik.ak_mengajar a
			left JOIN sdm.ms_pegawai p ON a.nipdosen = p.idpegawai::text where a.periode='$periode' and a.tugasmengajar='-1' and a.kodemk='$kodemk' and a.kelasmk='$kelasmk' and a.kodeunit='$kodeunit' group by namapengajar, a.kodemk, a.kelasmk" ;
			return $conn->GetArray($sql);
		}


		function getSuratTugasMengajar($conn, $nipdosen, $periode, $fakultas){
			$sql = "select case when ee.\"level\"=1 then ee.namaunit else ff.namaunit end as fakultas
						, case when ee.\"level\"=1 then ee.nipketuasementara else ff.nipketuasementara end as nipdekan
						, case when ee.\"level\"=1 then ee.namaketuasementara else ff.namaketuasementara end as namadekan
						, aa.kodemk, bb.namamk, bb.sks, aa.kelasmk, dd.namasistem||' '||dd.tipeprogram AS basis
					from akademik.ak_mengajar aa
						left join akademik.ak_matakuliah bb on aa.thnkurikulum=bb.thnkurikulum and aa.kodemk=bb.kodemk
						left join akademik.ak_kelas cc on aa.periode=cc.periode and aa.thnkurikulum=cc.thnkurikulum and aa.kodeunit=cc.kodeunit and aa.kodemk=cc.kodemk and aa.kelasmk=cc.kelasmk
						left join akademik.ak_sistem dd on cc.sistemkuliah=dd.sistemkuliah
						left join gate.ms_unit ee on aa.kodeunit=ee.kodeunit
						left join gate.ms_unit ff on ee.kodeunitparent=ff.kodeunit
					where aa.nipdosen='$nipdosen' and aa.periode='$periode'
					and case when ee.\"level\"=1 then ee.kodeunit else ff.kodeunit end = '$fakultas' ";

			$rows=$conn->GetArray($sql);	
			$data=array();
			foreach($rows as $row){
					$data[]=array(
					'matakuliah'=>$row['kodemk']." ".$row['namamk'],
					'sks'		=>$row['sks'],
					'seksi'		=>$row['kelasmk'],
					'basis'		=>$row['basis'],
					'fakultas'	=>$row['fakultas'],
					'nipdekan'	=>$row['nipdekan'],
					'namadekan'	=>$row['namadekan']
					);
			}
			return $data;
		}

		function addCourseMoodle($key){
			
			$token = '847895ee848fdb5fb2d43b275705470c';
			$domainname = 'https://elearning.inaba.ac.id';
			$functionname = 'core_course_create_courses';
			$restformat = 'json';
			$data = explode('|', $key);
			
		
				$enddate = strtotime('+16 week',strtotime($data[9]));//menambahkan 3 minggu
				$course = new stdClass();

				$course->fullname=$data[1]."-".$data[8]."-".$data[4]."-".$data[10];	// string,    254, Obrigatorio,          Nome Completo do Curso
				$course->shortname=$data[1]."-".$data[8]."-".$data[4]."-".$data[10];					// string,    100, Obrigatorio,          Nome Curto, evite usar espaço, substitua os espaços por traço baixo (underscore)
				$course->categoryid=self::getCategory($data[2]."|".$data[3]);					// int, 	   10, Obrigatorio, 		 Id da categoria
				$course->idnumber=$data[2]."".$data[3]."".$data[1]."".$data[4];												// deve ser conhecido o id conforme já cadastrado no moodle 
				$course->summaryformat = 1;
				$course->showgrades = 1;
				$course->newsitems = 5;
				$course->maxbytes = 0;
				$course->showreports = 0;
				$course->groupmodeforce = 0;
				$course->defaultgroupingid = 0;
				$course->startdate = strtotime($data[9]);                
				$course->enddate =  $enddate ;
				$course->numsections=16;
				$course->maxbytes=5000;
				//$course->idnumber  = "axo.44d.1x";				// string,    100, Opcional,             Id universal do curso
				$course->summary  = "Mata Kuliah ".$data[8]." Kelas ".$data[4];
																// string,     1K, Obrigatorio, 			 Sumário
				$course->visible  = 1;						// int,         1, Obrigatorio,             1: Disponível para estudante, 0:Não disponível
				$course->groupmode  =  0;						// int,         1, Obrigatorio,             Padrão para "0" //no group, separate, visible
				$course->format  = "weeks";					// string,      1, Obrigatorio,				Padrão para "weeks" //Formato do curso: weeks, topics, social, site,..
				$courses = array( $course);
				$params = array('courses' => $courses);
				
				/// REST CALL
				//header('Content-Type: text/plain');
				$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
				require_once($conf['model_dir'].'m_curl.php');
				$curl = new curl;
				//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
				$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
				if(!empty(self::getCategory($data[3]))){
				$resp = $curl->post($serverurl . $restformat, $params);
				$data = json_decode($resp, true);
					
				return true;
				}else{
					return false;
				}
				
				
				//print_r($resp);
		

		}

		function enrolDosen($conn_moodle,$conn,$key)
		{
			$token = '847895ee848fdb5fb2d43b275705470c';
			$domainname = 'https://elearning.inaba.ac.id';
			$functionname = 'enrol_manual_enrol_users';
			$restformat = 'json';
			$data = explode('|', $key);
			$idnumber = $data[2]."".$data[3]."".$data[1]."".$data[4];
			$uid = self::getUserMoodle($conn,$data[10]);
			$enrolment = new stdClass();
			$enrolment->roleid=3;
			$enrolment->userid =$uid['users'][0]['id'];
			$enrolment->courseid=self::getCourseByPass($conn_moodle,$idnumber);
			$enrolments = array($enrolment);
			$params = array('enrolments' => $enrolments);
			//header('Content-Type: text/plain');
			$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
			require_once($conf['model_dir'].'m_curl.php');
			$curl = new curl;
			//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
			$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
			$resp = $curl->post($serverurl . $restformat, $params);
			//print_r($resp);
			}
			function enrolMahasiswa($conn_moodle,$conn,$key)
			{
				$token = '847895ee848fdb5fb2d43b275705470c';
				$domainname = 'https://elearning.inaba.ac.id';
				$functionname = 'enrol_manual_enrol_users';
				$restformat = 'json';
				$data = explode('|', $key);
				$uid = self::getUserMoodle($conn,$data[1]);
				$enrolment = new stdClass();
				$enrolment->roleid=5;
				$enrolment->userid =$uid['users'][0]['id'];
				$enrolment->courseid=$data[0];
				$enrolments = array($enrolment);
				$params = array('enrolments' => $enrolments);
				//header('Content-Type: text/plain');
				$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
				require_once($conf['model_dir'].'m_curl.php');
				$curl = new curl;
				//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
				$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
				$resp = $curl->post($serverurl . $restformat, $params);
				//print_r($resp);
			}
			function UnEnrolMahasiswa($conn_moodle,$conn,$key){
				$token = '847895ee848fdb5fb2d43b275705470c';
				$domainname = 'https://elearning.inaba.ac.id';
				$functionname = 'enrol_manual_unenrol_users';
				$restformat = 'json';
				$data = explode('|', $key);
				$uid = self::getUserMoodle($conn,$data[1]);
				$enrolment = new stdClass();
				$enrolment->roleid=5;
				$enrolment->userid =$uid['users'][0]['id'];
				$enrolment->courseid=$data[0];
				$enrolments = array($enrolment);
				$params = array('enrolments' => $enrolments);
				//header('Content-Type: text/plain');
				$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
				require_once($conf['model_dir'].'m_curl.php');
				$curl = new curl;
				//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
				$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
				$resp = $curl->post($serverurl . $restformat, $params);
			}
			function getDosenMoodle($conn_moodle,$courseid,$idnumber){
		
				$sql = "select me.id,meu.userid,me.courseid,me.roleid,CONCAT(mu.firstname,' ',mu.lastname) as nama from mdl_enrol me join 
				mdl_user_enrolments meu on me.id=meu.enrolid
				join mdl_user mu on mu.id = meu.userid where mu.idnumber='$idnumber'  and me.courseid='$courseid'";
				$data = $conn_moodle->GetRow($sql);
				return $data['nama'];
	
			}
			function DeleteCourse($coursesid){
				$token = '847895ee848fdb5fb2d43b275705470c';
				$domainname = 'https://elearning.inaba.ac.id';
				$functionname = 'core_course_delete_courses';
				$restformat = 'json';
				$params = array('courseids'=>array($coursesid));
				$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
				require_once($conf['model_dir'].'m_curl.php');
				$curl = new curl;
				//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
				$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
				$resp = $curl->post($serverurl . $restformat, $params);
				$data = json_decode($resp, true);
			
			}

			function getUserMoodle($conn,$idnumber){
			
				$token = '847895ee848fdb5fb2d43b275705470c';
				$domainname = 'https://elearning.inaba.ac.id';
				$functionname = 'core_user_get_users';
				$restformat = 'json';
				$params = array('criteria'=>array(
						array(	
							'key'=>'idnumber',
							'value'=>$idnumber
							)
					)
				  );
				//header('Content-Type: text/plain');
				$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
				require_once($conf['model_dir'].'m_curl.php');
				$curl = new curl;
				//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
				$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
				$resp = $curl->post($serverurl . $restformat, $params);
				$data = json_decode($resp, true);
				return $data;
				//header("Location: http://localhost:8080/akademik2/front/admin/index.php?page=list_syncuser");
				//print_r($resp);
				//self::UpdateSyncMoodle($conn,$data['userid']);
			}
			function inquiryByuserid($conn,$uid){
				$unit = Modul::getLeftRight();
				$left=$unit['LEFT'];
				$right = $unit['RIGHT'];
				
				$sql = "select distinct u.*, ur.koderole
					from gate.sc_user u 
					left join gate.sc_userrole ur on ur.userid = u.userid 
					left join gate.ms_unit un on ur.kodeunit = un.kodeunit 
					and un.infoleft >= '$left' and un.inforight <= '$right' where u.idpegawai='$uid'
					";
				return $conn->GetRow($sql);
			}
			function inquiryByusername($conn,$uid){
				$unit = Modul::getLeftRight();
				$left=$unit['LEFT'];
				$right = $unit['RIGHT'];
				
				$sql = "select distinct u.*, ur.koderole
					from gate.sc_user u 
					left join gate.sc_userrole ur on ur.userid = u.userid 
					left join gate.ms_unit un on ur.kodeunit = un.kodeunit 
					and un.infoleft >= '$left' and un.inforight <= '$right' where u.username='$uid'
					";
				return $conn->GetRow($sql);
			}
			function getNamaDosen($conn,$idpegawai){
				$sql = "select akademik.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as nama from sdm.ms_pegawai where idpegawai='$idpegawai'";
				$data = $conn->GetRow($sql);
				return $data['nama'];
			}
			function syncUserToElearning($conn,$data){
		
				$token = '847895ee848fdb5fb2d43b275705470c';
				$domainname = 'https://elearning.inaba.ac.id';
				$functionname = 'core_user_create_users';
				$restformat = 'json';
				$names = explode(' ', $data['userdesc']);
				$ft = explode('.',$data['username']);
				$lastname='';
				for($i=1;$i<=count($names)-1;$i++){
					$lastname = $lastname.' '.$names[$i];
				}
				$firstname = $names[0];
				$user2 = new stdClass();
				$user2->username = $data['username'];
				if($data['koderole']=='D'){
					$user2->idnumber = $data['idpegawai'];
					$user2->password = '@DsnInaba1984';
				}else{
					$user2->idnumber = $data['username'];
					$user2->password = '@Inaba448';
				}
				$user2->firstname = $firstname;
				$user2->lastname = $lastname;
				$user2->email = $data['username'].'@moodle.com';
				$user2->timezone = 'Asia/Jakarta';
				$user2->city = 'Bandung';
				$user2->country = 'ID';
				$users = array($user2);
				$params = array('users' => $users);
	
				//header('Content-Type: text/plain');
				$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
				require_once($conf['model_dir'].'m_curl.php');
				$curl = new curl;
				//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
				$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
				$resp = $curl->post($serverurl . $restformat, $params);
				//header("Location: http://localhost:8080/akademik2/front/admin/index.php?page=list_syncuser");
				//print_r($resp);
				//self::UpdateSyncMoodle($conn,$data['userid']);
			}
		function getCategory($periode){
			
				$token = '847895ee848fdb5fb2d43b275705470c';
				$domainname = 'https://elearning.inaba.ac.id';
				$functionname = 'core_course_get_categories';
				$restformat = 'json';
				$params = array('criteria'=>array(
						array(	
							'key'=>'idnumber',
							'value'=>$periode
							)
					)
				  );
				//header('Content-Type: text/plain');
				$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
				require_once($conf['model_dir'].'m_curl.php');
				$curl = new curl;
				//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
				$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
				$resp = $curl->post($serverurl . $restformat, $params);
				$data = json_decode($resp, true);
				return $data[0]['id'];
				//header("Location: http://localhost:8080/akademik2/front/admin/index.php?page=list_syncuser");
				//print_r($resp);
				//self::UpdateSyncMoodle($conn,$data['userid']);
		}
		function getCourseByPass($conn_moodle,$key){
			$sql = "select id from mdl_course where idnumber='$key'";
			$data = $conn_moodle->GetRow($sql);
		
			return $data['id'];
			
		}

		function getUsername($conn,$idpegawai){
			$sql = "select username from gate.sc_user where idpegawai='$idpegawai'";
			$data = $conn->GetRow($sql);
			return $data['username'];
		}


		function getCourse($key){
			
			$token = '847895ee848fdb5fb2d43b275705470c';
			$domainname = 'https://elearning.inaba.ac.id';
			$functionname = 'core_course_get_courses';
			$restformat = 'json';
			$key = "AKC005-Ak. Keuangan lanjutan I-1-1066";
			$params = array('options'=>
				array(	
					'ids'=>array($key),
					)
			
		  		);
			//print_r($params['options']['idnumber'][0]);
			//header('Content-Type: text/plain');
			$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
			require_once($conf['model_dir'].'m_curl.php');
			$curl = new curl;
			//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
			$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
			$resp = $curl->post($serverurl . $restformat, $params);
			$data = json_decode($resp, true);
			return $data[0]['id'];
			//header("Location: http://localhost:8080/akademik2/front/admin/index.php?page=list_syncuser");
			//print_r($resp);
			//self::UpdateSyncMoodle($conn,$data['userid']);
	}

	function getDataKuliahOnline($conn)
	{
		global $r_key;
			
			$periode = Akademik::getPeriode();
			
			$sql = "select a.thnkurikulum,a.periode,a.kodeunit, c.namaunit ,a.kodemk,a.kelasmk,a.jeniskul,a.kelompok,akademik.f_namahari(b.nohari) AS namahari,
					d.namamk,d.sks,d.semmk,b.jammulai,b.jamselesai,akademik.f_namahari(coalesce(b.nohari2,b.nohari)) AS namahari2,b.jammulai2,
					b.jamselesai2,b.koderuang,coalesce(b.jumlahpeserta,0) as jmlpeserta,b.sistemkuliah, b.tgljadwal1 AS startdate,a.nipdosen,a.tugasmengajar,case b.isonline when -1 then 'Online' else 'Tatap Muka' end as isonline from ".static::table('ak_mengajar')." a
					join ".static::table('ak_kelas')." b on(a.periode=b.periode and a.kodeunit=b.kodeunit and a.thnkurikulum=b.thnkurikulum and a.kodemk=b.kodemk and a.kelasmk=b.kelasmk)
					join gate.ms_unit c on a.kodeunit = c.kodeunit
					join ".static::table('ak_kurikulum')." d on(a.thnkurikulum=d.thnkurikulum and a.kodeunit=d.kodeunit and a.kodemk=d.kodemk) where b.isonline=-1 and a.periode='$periode'";
			
			return $conn->GetArray($sql);
	}


		

		
	}
?>
