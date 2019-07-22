<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	/**
	 * Model untuk interface mobile
	 * @author Sevima
	 * @version 1.0
	 */
	class mMobile {
		/**
		* Cek login ke sistem
		* @param string $userid username sebetulnya
		* @param string $passwd
		* @param string $token
		* @param string $regid
		* @return mixed
		*/
		function logIn($userid,$passwd,$token,$regid=null) {
			global $conn;
			
			$conn->BeginTrans();
			
			// cek data user
			$sql = "select password, userid as id, userdesc as name, username, email, idpegawai
					from gate.sc_user where username = ".Query::escape($userid);
			$row = $conn->GetRow($sql);
			
			// proses
			$ok = false;
			if(!empty($row)) {
				// cek password
				if($passwd !== false) {
					$passmd5 = md5($passwd);
					if($passmd5 == $row['password'] or (strlen($passwd) == 0 and strlen($row['password']) == 0))
						$ok = true;
				}
				else
					$ok = true;
				
				if($ok) {
					// masukkan data login
					$sql = "delete from gate.sc_loginmobile where userid = ".$row['id'];
					$ok = $conn->Execute($sql);
					
					if($ok) {
						$record = array();
						$record['userid'] = $row['id'];
						$record['token'] = $token;
						$record['regid'] = CStr::cStrNull($regid);
						
						$err = Query::recInsert($conn,$record,'gate.sc_loginmobile');
						$ok = Query::isOK($err);
					}
					
					// ambil biodata
					list($ismhs,$isdosen) = $this->getRolesByUsername($row['username']);
					if($ismhs)
						$rowd = $this->getDataMahasiswa($row['username']);
					else
						$rowd = $this->getDataDosen($row['username']);
					
					$row['hp'] = $rowd['hp'];
				}
				else
					$msg = cLang::ERROR_LOGIN_PASSWORD_INCORRECT;
			}
			else
				$msg = cLang::ERROR_LOGIN_USER_NOT_FOUND;
			
			$conn->CommitTrans($ok);
			
			if($ok)
				return array($row);
			else
				return array(false,$msg);
		}

		function logInFacebook($facebookId,$token,$regid=null) {
			global $conn;
			
			$conn->BeginTrans();
			
			// cek data user
			$sql = "select userid as id, userdesc as name, username, email, idpegawai
					from gate.sc_user a 
					where fbid = '$facebookId'";
			$row = $conn->GetRow($sql);
			
			// proses
			$ok = false;
			if(!empty($row)) {
					// masukkan data login
					$sql = "delete from gate.sc_loginmobile where userid = ".$row['id'];
					$ok = $conn->Execute($sql);
					
					if($ok) {
						$record = array();
						$record['userid'] = $row['id'];
						$record['token'] = $token;
						$record['regid'] = CStr::cStrNull($regid);
						
						$err = Query::recInsert($conn,$record,'gate.sc_loginmobile');
						$ok = Query::isOK($err);
					}
					
					// ambil biodata
					list($ismhs,$isdosen) = $this->getRolesByUsername($row['username']);
					if($ismhs)
						$rowd = $this->getDataMahasiswa($row['username']);
					else
						$rowd = $this->getDataDosen($row['username']);
					
					$row['hp'] = $rowd['hp'];
			}
			else
				$msg = cLang::ERROR_LOGIN_USER_NOT_FOUND;
			
			$conn->CommitTrans($ok);
			
			if($ok)
				return array($row);
			else
				return array(false,$msg);
		}
		
		/**
		* Logout dari sistem
		* @param string $token
		* @return bool
		*/
		function logOut($token) {
			global $conn;
			
			$sql = "delete from gate.sc_loginmobile where token = ".Query::escape($token);
			$ok = $conn->Execute($sql);
			
			return ($ok ? true : false);
		}
		
		/**
		* Mendapatkan username dari userid
		* @param string $userid
		* @return string
		*/
		function getUsername($userid) {
			global $conn;
			
			$sql = "select username from gate.sc_user where userid = ".Query::escape($userid);
			
			return $conn->GetOne($sql);
		}
		
		/**
		* Mendapatkan userid dari username
		* @param string $username
		* @return string
		*/
		function getUserID($username) {
			global $conn;
			
			$sql = "select userid from gate.sc_user where username = ".Query::escape($username);
			
			return $conn->GetOne($sql);
		}
		
		/**
		* Mendapatkan roles dari username
		* @param string $username
		* @return array
		*/
		function getRolesByUsername($username) {
			global $conn;
			
			$sql = "select r.koderole from gate.sc_userrole r
					join gate.sc_user u on u.userid = r.userid
					where u.username = ".Query::escape($username);
			$rs = $conn->Execute($sql);
			
			$ismhs = false;
			$isdosen = false;
			$isemployee = false;
			while($row = $rs->FetchRow()) {
				if($row['koderole'] == 'M')
					$ismhs = true;
				else if($row['koderole'] == 'D')
					$isdosen = true;
				else
					$isemployee = true;
			}
			
			return array($ismhs,$isdosen,$isemployee);
		}
		
		/**
		* Mendapatkan data login dari token
		* @param string $token
		* @return array
		*/
		function getLoginByToken($token) {
			global $conn;
			
			$sql = "select * from gate.sc_loginmobile where token = ".Query::escape($token);
			$row = $conn->GetRow($sql);
			
			if(empty($row)) {
				$err = true;
				$msg = cLang::ERROR_INVALID_TOKEN;
			}
			else
				$err = false;
			
			return array($err,$msg,$row);
		}
		
		/**
		* Mendapatkan data login dari username
		* @param string $username
		* @return array
		*/
		function getLoginByUsername($username) {
			global $conn;
			
			$sql = "select l.* from gate.sc_loginmobile l
					join gate.sc_user u on u.userid = l.userid
					where u.username = ".Query::escape($username);
			
			return $conn->GetRow($sql);
		}
		
		/**
		* Cek apakah user bisa mengakses data mahasiswa
		* @param string $username
		* @param string $nim
		* @return bool
		*/
		function canUserAksesMhs($username,$nim) {
			global $conn;
			
			list($ismhs,$isdosen) = $this->getRolesByUsername($username);
			
			if($ismhs and $nim == $username) {
				return true;
			}
			else if($isdosen) {
				$sql = "select 1 from akademik.ak_perwalian where nipdosenwali = ".Query::escape($username)." and nim = ".Query::escape($nim);
				$cek = $conn->GetOne($sql);
				
				if(empty($cek))
					return false;
				else
					return true;
			}
			else
				return false;
		}
		
		/**
		* Cek apakah user bisa mengakses data perkuliahan
		* @param string $username
		* @param string $id
		* @return bool
		*/
		function canUserAksesKuliah($username,$id) {
			global $conn;
			
			list($ismhs,$isdosen) = $this->getRolesByUsername($username);
			
			// ambil kueri
			$a_id = explode('|',$id);
			
			if($ismhs) {
				$sql = "select 1 from akademik.ak_krs u
						where u.nim = ".Query::escape($username)." and u.periode = ".Query::escape($a_id[2])."
						and u.thnkurikulum = ".Query::escape($a_id[3])." and u.kodeunit = ".Query::escape($a_id[4])."
						and u.kodemk = ".Query::escape($a_id[5])." and u.kelasmk = ".Query::escape($a_id[6]);
			}
			else if($isdosen) {
				$sql = "select 1 from akademik.ak_mengajar u
						where u.nipdosen = ".Query::escape($username)." and u.periode = ".Query::escape($a_id[2])."
						and u.thnkurikulum = ".Query::escape($a_id[3])." and u.kodeunit = ".Query::escape($a_id[4])."
						and u.kodemk = ".Query::escape($a_id[5])." and u.kelasmk = ".Query::escape($a_id[6]);
			}
			else
				return false;
			
			$cek = $conn->GetOne($sql);
			
			if(empty($cek))
				return false;
			else
				return true;
		}
		
		/**
		* Mendapatkan data user dari userid
		* @param int $userid
		* @return mixed
		*/
		function getDataUser($userid) {
			global $conn;
			
			$sql = "select u.userid as id, u.username, u.userdesc as name, u.email
					from gate.sc_user u where u.userid = ".Query::escape($userid);
			$row = $conn->GetRow($sql);
			
			if(!empty($row)) {
				$sql = "select r.koderole as id, r.namarole as name, u.kodeunit
						from gate.sc_userrole u
						join gate.sc_role r on u.koderole = r.koderole
						where u.userid = ".Query::escape($userid);
				
				$row['userrole'] = $conn->GetArray($sql);
			}
			
			if(empty($row))
				return false;
			else
				return $row;
		}
		
		/**
		* Mendapatkan data dosen
		* @param string $id
		* @return mixed
		*/
		function getDataDosen($id) {
			global $conn;
			
			$sql = "select p.idpegawai as id, p.nik as nip, p.nidn, p.nohp as hp, p.email, p.idunit,
					trim(sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)) as name
					from sdm.ms_pegawai p where p.idpegawai = ".Query::escape($id);
			$row = $conn->GetRow($sql);
			
			if(empty($row))
				return false;
			else
				return $row;
		}
		
		/**
		* Mendapatkan data mahasiswa
		* @param string $id
		* @return array
		*/
		function getDataMahasiswa($id) {
			global $conn;
			
			$sql = "select p.nim, p.sex as gender, p.hp, p.kodeunit as department, u.programpend as strata, p.periodemasuk as angkatan,
					trim(akademik.f_namalengkap(p.gelardepan,p.nama,null,null,p.gelarbelakang)) as name, p.nim as id
					from akademik.ms_mahasiswa p
					join gate.ms_unit u on p.kodeunit = u.kodeunit
					where p.nim = ".Query::escape($id);
			$row = $conn->GetRow($sql);
			
			if(empty($row))
				return false;
			else
				return $row;
		}
		
		/**
		* Mendapatkan data unit
		* @param string $kodeunit
		* @param bool $issdm
		* @return array
		*/
		function getDataUnit($kodeunit,$issdm=true) {
			global $conn;
			
			/* if($issdm)
				$sql = "select * from sdm.ms_unit where idunit = ".(int)$kodeunit;
			else */
				$sql = "select * from gate.ms_unit where kodeunit = ".Query::escape($kodeunit);
				
			return $conn->GetRow($sql);
		}
		
		/**
		* Mendapatkan data universitas (gate)
		* @return array
		*/
		function getDataUniversitas() {
			global $conn;
			
			$sql = "select * from gate.ms_unit where level = 0";
				
			return $conn->GetRow($sql);
		}
		
		/**
		* Mendapatkan nama matakuliah
		* @param string $kodemk
		* @return string
		*/
		function getNamaMataKuliah($kodemk) {
			global $conn;
			
			$sql = "select namamk from akademik.ak_matakuliah where kodemk = ".Query::escape($kodemk);
			
			return $conn->GetOne($sql);
		}
		
		/**
		* Mendapatkan array nama semester
		* @return array
		*/
		function getListSemester() {
			return Akademik::semester();
		}
		
		/**
		* Mendapatkan unit dari username
		* @param string $username
		* @return array
		*/
		function getListFakultasByUsername($username) {
			global $conn;
			
			list($ismhs,$isdosen) = $this->getRolesByUsername($username);
			
			if($ismhs)
				$sql = "select kodeunit from akademik.ms_mahasiswa where nim = ".Query::escape($username);
			else if($isdosen)
				$sql = "select idunit from sdm.ms_pegawai where idpegawai = ".Query::escape($username);
			else
				return array();
			
			$kodeunit = $conn->GetOne($sql);
			
			// ambil unit bawahan
			$sql = "select c.kodeunit as code, c.namaunit as name, c.kodeunitparent, c.level
					from gate.ms_unit u
					join gate.ms_unit c on c.infoleft >= u.infoleft and c.inforight <= u.inforight
					where u.kodeunit = ".Query::escape($kodeunit)." and c.level in (1,2) and c.isakad = -1
					order by c.infoleft";
			$rows = $conn->GetArray($sql);
			
			// jika cuma 1 ambil parent
			if(count($rows) == 1 and !empty($rows[0]['kodeunitparent'])) {
				$sql = "select c.kodeunit as code, c.namaunit as name, c.kodeunitparent, c.level
						from gate.ms_unit c
						where c.kodeunit = ".Query::escape($rows[0]['kodeunitparent'])." and c.level in (1,2) and c.isakad = -1";
				$rowp = $conn->GetRow($sql);
				
				if(!empty($rowp))
					array_unshift($rows,$rowp);
			}
			
			$data = array();
			foreach($rows as $row) {
				$t_level = $row['level'];
				$t_parent = $row['kodeunitparent'];
				
				$row['id'] = $row['code'];
				unset($row['level'],$row['kodeunitparent']);
				
				if($t_level == 1) {
					$row['departments'] = array();
					$data[$row['code']] = $row;
				}
				else
					$data[$t_parent]['departments'][] = $row;
			}
			
			return $data;
		}
		
		/**
		* Mendapatkan periode dari username
		* @param string $username
		* @return array
		*/
		function getListPeriodeByUsername($username) {
			global $conn;
			
			list($ismhs,$isdosen) = $this->getRolesByUsername($username);
			
			if($ismhs)
				$sql = "select periode from akademik.ak_perwalian where nim = ".Query::escape($username)." order by periode";
			else if($isdosen)
				$sql = "select distinct periode from akademik.ak_mengajar where nipdosen = ".Query::escape($username)." order by periode";
			else
				return array();
			
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[] = $row['periode'];
			
			return $data;
		}
		
		
		function getPeriodeById($id) {
			global $conn;
			
			$sql = "select * from akademik.ms_periode where periode = ".Query::escape($id)." ";
			
			
			$rs = $conn->Execute($sql);
			
			return $rs->FetchRow();
		}
		
		/**
		* Mendapatkan dosen mahasiswa per periode
		* @param string $nim
		* @param string $periode
		* @return object
		*/
		function getListPengajarMhs($nim,$periode) {
			global $conn;
			
			// ambil dosen
			$sql = "select a.thnkurikulum, a.kodemk, a.kodeunit, a.periode, a.kelasmk, min(a.nipdosen) as nip
					from akademik.ak_mengajar a
					join akademik.ak_krs k using (thnkurikulum,kodemk,kodeunit,periode,kelasmk)
					where k.nim = ".Query::escape($nim)." and k.periode = ".Query::escape($periode)."
					group by a.thnkurikulum, a.kodemk, a.kodeunit, a.periode, a.kelasmk";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['periode'].'|'.$row['kelasmk'];
				$data[$t_key] = $row['nip'];
			}
			
			return $data;
		}
		
		/**
		* Mendapatkan khs mahasiswa
		* @param string $nim
		* @param string $periode
		* @return array
		*/
		function getListKHSMahasiswa($nim,$periode=null) {
			global $conn;
			
			// ambil krs dan kuliah
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, r.sks,
					c.nohari as noday, c.jammulai as timefrom, c.jamselesai as timeto, c.koderuang,
					k.nangka as grade, k.nhuruf as gradename, abs(k.nilaimasuk) as status, abs(k.dipakai) as dipakai
					from akademik.ak_krs k
					join akademik.ak_kelas c using (thnkurikulum,kodemk,kodeunit,periode,kelasmk)
					join akademik.ak_kurikulum r using (thnkurikulum,kodemk,kodeunit)
					where k.nim = ".Query::escape($nim).(empty($periode) ? '' : " and k.periode = ".Query::escape($periode))."
					order by k.periode, k.kodemk";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($t_periode != $row['periode']) {
					$t_sks = 0;
					$t_xsks = 0; // sks yang digunakan untuk ips
					$t_nsks = 0;
					$t_final = 1;
					$t_studies = array();
					
					$t_periode = $row['periode'];
				}
				
				// format jam
				$row['timefrom'] = CStr::formatJam($row['timefrom']);
				$row['timeto'] = CStr::formatJam($row['timeto']);
				
				$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['periode'].'|'.$row['kelasmk'];
				$row['id'] = $t_key.'|'.$nim;
				
				$t_studies[$t_key] = $row;
				
				// cek nilai masuk semua
				if(empty($row['status']))
					$t_final = 0;
				
				// untuk ips
				$t_sks += $row['sks'];
				if(!empty($row['status']) and !empty($row['dipakai'])) {
					$t_xsks += $row['sks'];
					$t_nsks += $row['sks']*$row['grade'];
				}
				
				if($rs->EOF or $row['periode'] != $rs->fields['periode']) {
					$t_data = array();
					$t_data['id'] = $nim.'|'.$t_periode;
					$t_data['totalSKS'] = $t_sks;
					$t_data['totalIP'] = (empty($t_xsks) ? 0 : round($t_nsks/$t_xsks,2));
					$t_data['status'] = $t_final;
					$t_data['period'] = $t_periode;
					
					$a_dosen = $this->getListPengajarMhs($nim,$t_periode);
					foreach($a_dosen as $t_key => $t_nip)
						$t_studies[$t_key]['nip'] = $t_nip;
					
					$t_data['studies'] = $t_studies;
					
					$data[] = $t_data;
				}
			}
			
			return $data;
		}
		/**
		* Mendapatkan transcript mahasiswa
		* @param string $nim
		* @return array
		*/
		function getListTranscriptMahasiswa($nim) {
			global $conn;
			
			// ambil transcript dan kuliah
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, r.sks,
					c.nohari as noday, c.jammulai as timefrom, c.jamselesai as timeto, c.koderuang,
					k.nangka as grade, k.nhuruf as gradename, abs(k.nilaimasuk) as status, abs(k.dipakai) as dipakai
					from akademik.ak_krs k
					join akademik.ak_kelas c using (thnkurikulum,kodemk,kodeunit,periode,kelasmk)
					join akademik.ak_kurikulum r using (thnkurikulum,kodemk,kodeunit)
					where k.nim = ".Query::escape($nim)."order by k.periode, k.kodemk";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				
				$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['periode'].'|'.$row['kelasmk'];
				$row['id'] = $t_key.'|'.$nim;
				
				$t_studies[$t_key] = $row;
				
				// cek nilai masuk semua
				if(empty($row['status']))
					$t_final = 0;
				
				// untuk ips
				$t_sks += $row['sks'];
				if(!empty($row['status']) and !empty($row['dipakai'])) {
					$t_xsks += $row['sks'];
					$t_nsks += $row['sks']*$row['grade'];
				}
				
				if($rs->EOF or $row['periode'] != $rs->fields['periode']) {
					$t_data = array();
					$t_data['id'] = $nim.'|'.$t_periode;
					$t_data['totalSKS'] = $t_sks;
					$t_data['totalIP'] = (empty($t_xsks) ? 0 : round($t_nsks/$t_xsks,2));
					$t_data['status'] = $t_final;
					
					$a_dosen = $this->getListPengajarMhs($nim,$t_periode);
					foreach($a_dosen as $t_key => $t_nip)
						$t_studies[$t_key]['nip'] = $t_nip;
					
					$t_data['studies'] = $t_studies;
					
					$data[] = $t_data;
				}
			}
			
			return $data;
		}
		
		/**
		* Mendapatkan daftar tagihan mahasiswa
		* @param string $nim
		* @return array
		*/
		function getListTagihanMahasiswa($nim) {
			global $conn;
			
			$sql = "select t.idtagihan as id, j.namajenistagihan, t.periode, t.nominaltagihan-t.potongan as bill, t.denda as fine,
					0 as status, to_char(t.tgldeadline,'DD-MM-YYYY') as duedate
					from h2h.ke_tagihan t
					join h2h.lv_jenistagihan j on t.jenistagihan = j.jenistagihan
					where t.nim = ".Query::escape($nim)." and flaglunas <> 'L'
					order by j.jenistagihan, t.periode, t.angsuranke";
			$rs = $conn->Execute($sql);
			
			$a_semester = Akademik::semester(true);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				$row['description'] = $row['namajenistagihan'];
				
				$data[] = $row;
			}
			
			return $data;
		}

		/**
		* Mendapatkan daftar tagihan yang telah dibayar
		* @param string $nim
		* @return array
		*/
		function getListPembayaranMahasiswa($nim) {
			global $conn;
			
			$sql = "select t.idtagihan as id, j.namajenistagihan, t.periode, t.nominaltagihan-t.potongan as payment, t.denda as fine,
					1 as status, to_char(t.tgldeadline,'DD-MM-YYYY') as duedate
					from h2h.ke_tagihan t
					join h2h.lv_jenistagihan j on t.jenistagihan = j.jenistagihan
					where t.nim = ".Query::escape($nim)." and flaglunas = 'L'
					order by j.jenistagihan, t.periode, t.angsuranke";
			$rs = $conn->Execute($sql);
			
			$a_semester = Akademik::semester(true);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				$row['description'] = $row['namajenistagihan'];
				
				$data[] = $row;
			}
			
			return $data;
		}
		
		

		/**
		* Mendapatkan daftar kalender akademik
		* @param string $periode
		* @return array
		*/
		function getListKalenderAkademik($periode) {
			global $conn;
			
			$sql = "
			select kalenderakademik_id, diskripsi, tahun, periode_ganjil, periode_genap, link_download from akademik.ms_kalenderakademik where periode_ganjil = ".Query::escape($periode)." or periode_genap = ".Query::escape($periode)." limit 1";
			
			$rs = $conn->Execute($sql);
			$data = array();
			while($row = $rs->FetchRow()) {
				$data = $row;
			}
			if($data!=null){
				if(is_array($data)){
				$sql = "select kalenderakademiklist_id as id, to_char(tanggal_mulai,'DD-MM-YYYY HH24:MI:SS') as dateFrom, to_char(tanggal_akhir,'DD-MM-YYYY HH24:MI:SS') as dateTo, kegiatan as name from akademik.ms_kalenderakademiklist where kalenderakademik_id = ".Query::escape($data['kalenderakademik_id'])." ";
				$rs = $conn->Execute($sql);
				$data = array('events'=>array(), "period"=>array(), "linkDownload"=>$data['link_download']);
				$a_semester = $this->getListSemester();
				$semester = substr($periode,-1);
				$tahun = substr($periode,0,4);
					$data['period']["id"] = $periode;
					$data['period']["name"] = $a_semester[$semester].' '.$tahun.'/'.($tahun+1);
					$data['period']["year"] = $semester;
					$data['period']["semester"] = $tahun;
					while($row = $rs->FetchRow()) {
						$data['events'][] = $row;
					}
				}
			}
			return $data;
		}
		
		

		/**
		* Mendapatkan daftar kalender akademik
		* @param string $periode
		* @return array
		*/
		function getListCalendar($periode, $userid, $nim, $nip) {
			global $conn;
			
			$sql = "
			select kalenderakademik_id, diskripsi, tahun, periode_ganjil, periode_genap, link_download from akademik.ms_kalenderakademik where periode_ganjil = ".Query::escape($periode)." or periode_genap = ".Query::escape($periode)." limit 1";
			
			$rs = $conn->Execute($sql);
			$data = array();
			while($row = $rs->FetchRow()) {
				$data = $row;
			}
			if($data!=null){
				if(is_array($data)){
					$sql = "select kalenderakademiklist_id as id, to_char(tanggal_mulai,'DD-MM-YYYY HH24:MI:SS') as dateFrom, to_char(tanggal_akhir,'DD-MM-YYYY HH24:MI:SS') as dateTo, kegiatan as name from akademik.ms_kalenderakademiklist where kalenderakademik_id = ".Query::escape($data['kalenderakademik_id'])." ";
					$rs = $conn->Execute($sql);
					
					$data = array('kalenderAkademik'=>array('events'=>array(), "period"=>array()), 'agendaPribadi'=>array('events'=>array()), 'jadwalRutin'=>array('events'=>array()), 'jadwalUjian'=>array('events'=>array()));
					$a_semester = $this->getListSemester();
					$semester = substr($periode,-1);
					$tahun = substr($periode,0,4);
					$data['kalenderAkademik']['period']["id"] = $periode;
					$data['kalenderAkademik']['period']["name"] = $a_semester[$semester].' '.$tahun.'/'.($tahun+1);
					$data['kalenderAkademik']['period']["year"] = $semester;
					$data['kalenderAkademik']['period']["semester"] = $tahun;
					$kalender = array();
					
					while($row = $rs->FetchRow()) {
						if(!isset($kalender[$row['id']])){
							$kalender[$row['id']] = true;
							$data['kalenderAkademik']['events'][] = $row;				
						}
					}
					
					$sql = "select agenda_id as id, to_char(tanggal_mulai,'DD-MM-YYYY HH24:MI:SS') as dateFrom, to_char(tanggal_akhir,'DD-MM-YYYY HH24:MI:SS') as dateTo, kegiatan as name from mobile.ms_agenda where user_id = ".Query::escape($userid)." ";
					$rs = $conn->Execute($sql);
					
					$kalender = array();
					while($row = $rs->FetchRow()) {
						if(!isset($kalender[$row['id']])){
							$kalender[$row['id']] = true;
							$data['agendaPribadi']['events'][] = $row;
						}
					}
					
					if($nim!=null){
						$sql = "select d.iddetailkelas as id, d.pertemuan, to_char(d.tglpertemuan,'DD-MM-YYYY') as tglpertemuan,
						d.jammulai, d.jamselesai, d.koderuang,d.kodemk, r.namamk as kelasmk, r.sks from akademik.ak_krs k
						join akademik.ak_kurikulum r using (thnkurikulum,kodemk,kodeunit)
						join akademik.ak_detailkelas d using (thnkurikulum,kodemk,kodeunit,periode,kelasmk)
						where (k.nim = ".Query::escape($nim).") and k.periode = ".Query::escape($periode)."
						order by d.tglpertemuan, d.jammulai";
						$rs = $conn->Execute($sql);
						
						$kalender = array();
						while($row = $rs->FetchRow()) {
							if(!isset($kalender[$row['id']])){
								$kalender[$row['id']] = true;
								$row['name'] = 'Pertemuan ke-'.$row['pertemuan'];
								$row['dateFrom'] = $row['tglpertemuan'].' '.CStr::formatJam($row['jammulai']).':00';
								$row['dateTo'] = $row['tglpertemuan'].' '.CStr::formatJam($row['jamselesai']).':00';
								$data['jadwalRutin']['events'][] = array("id"=>$row['id'], "name"=>$row['kelasmk']." Pertemuan ke-".$row['pertemuan'], "dateFrom"=>$row['dateFrom'], "dateTo"=>$row['dateTo']);
							}
						}
					}else if($nip!=null){
						
					}
				}
			}
			return $data;
		}
		
		/**
		* Mendapatkan daftar mahasiswa wali
		* @param string $nip
		* @return array
		*/
		function getListMahasiswaWali($nip,$cari='') {
			global $conn;
			
			$sql = "select p.nim, p.sex as gender, p.kodeunit as department, coalesce(u.programpend) as strata, p.periodemasuk as angkatan,
					akademik.f_namalengkap(p.gelardepan,p.nama,null,null,p.gelarbelakang) as name, p.nim as id, p.nim as username
					from akademik.ms_mahasiswa p
					join gate.ms_unit u on p.kodeunit = u.kodeunit
					join akademik.ak_perwalian w on w.nim = p.nim
					join akademik.ms_setting s on s.idsetting = 1 and w.periode = s.periodesekarang
					where w.nipdosenwali = ".Query::escape($nip)." and akademik.f_namalengkap(p.gelardepan,p.nama,null,null,p.gelarbelakang) ilike '%$cari%'
					order by p.nim limit 20";
			
			return $conn->GetArray($sql);
		}
		
		/**
		* Mendapatkan jadwal mahasiswa
		* @param string $nim
		* @param string $periode
		* @return array
		*/
		function getListJadwalMahasiswa($nim,$periode) {
			global $conn;
			
			// ambil krs dan kuliah
			$sql = "select d.iddetailkelas as id, d.pertemuan, to_char(d.tglpertemuan,'DD-MM-YYYY') as tglpertemuan,
					d.jammulai, d.jamselesai, d.koderuang,d.kodemk, r.namamk as kelasmk, r.sks from akademik.ak_krs k
					join akademik.ak_kurikulum r using (thnkurikulum,kodemk,kodeunit)
					join akademik.ak_detailkelas d using (thnkurikulum,kodemk,kodeunit,periode,kelasmk)
					where k.nim = ".Query::escape($nim)." and k.periode = ".Query::escape($periode)."
					order by d.tglpertemuan, d.jammulai";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				$t_key = $row['kodemk'];
				$row['name'] = 'Pertemuan ke-'.$row['pertemuan'];
				$row['dateFrom'] = $row['tglpertemuan'].' '.CStr::formatJam($row['jammulai']);
				$row['dateTo'] = $row['tglpertemuan'].' '.CStr::formatJam($row['jamselesai']);
				
				$data[$t_key] = $row;
			}
			
			return $data;
		}
		
		/**
		* Mendapatkan absensi per pertemuan
		* @param string $id
		* @return array
		*/
		/* function getPresensiKuliah($id) {
			global $conn;
			
			$a_id = explode('|',$id);
			
			$sql = "select k.nim, m.nama as name, case when u.nim is null then 0 else 1 end as isattending from akademik.ak_krs k
					join akademik.ms_mahasiswa m on k.nim = m.nim
					left join akademik.ak_absensikuliah u on u.nim = k.nim and u.periode = k.periode
						and u.thnkurikulum = k.thnkurikulum and u.kodeunit = k.kodeunit
						and u.kodemk = k.kodemk and u.kelasmk = k.kelasmk
						and to_char(u.tglkuliah,'YYYY-MM-DD') = ".Query::escape($a_id[0])." and u.perkuliahanke = ".Query::escape($a_id[1])."
						and u.jeniskuliah = ".Query::escape($a_id[7])." and u.kelompok  = ".Query::escape($a_id[8])."
					where k.periode = ".Query::escape($a_id[2])." and k.thnkurikulum = ".Query::escape($a_id[3])."
						and k.kodeunit = ".Query::escape($a_id[4])." and k.kodemk = ".Query::escape($a_id[5])."
						and k.kelasmk = ".Query::escape($a_id[6])."
					order by k.nim";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				$row['id'] = $id.'|'.$row['nim'];
				
				$data[] = $row;
			}
			
			return $data;
		} */
		
		/**
		* Mendapatkan rekap absensi mahasiswa
		* @param string $nim
		* @param string $periode
		* @return array
		*/
		function getRekapPresensiMahasiswa($nim,$periode) {
			global $conn;
			
			// ambil krs dan kuliah
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, r.sks,
					count(u.perkuliahanke) as pertemuan from akademik.ak_krs k
					join akademik.ak_kurikulum r using (thnkurikulum,kodemk,kodeunit)
					left join akademik.ak_kuliah u using (thnkurikulum,kodemk,kodeunit,periode,kelasmk)
					where k.nim = ".Query::escape($nim)." and k.periode = ".Query::escape($periode)."
					group by k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, r.sks
					order by k.kodemk";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['periode'].'|'.$row['kelasmk'];
				
				$row['id'] = $t_key;
				$row['presensi'] = 0;
				
				$data[$t_key] = $row;
			}

			if(count($data) > 0){
				// ambil dosen
				$a_dosen = $this->getListPengajarMhs($nim,$periode);
				foreach($a_dosen as $t_key => $t_nip)
					$data[$t_key]['nip'] = $t_nip;
				
				// ambil absensi
				$sql = "select a.thnkurikulum, a.kodemk, a.kodeunit, a.periode, a.kelasmk,a.absen,
						count(a.perkuliahanke) as jumlah from akademik.ak_absensikuliah a
						where a.nim = ".Query::escape($nim)." and a.periode = ".Query::escape($periode)."
						group by a.thnkurikulum, a.kodemk, a.kodeunit, a.periode, a.kelasmk,a.absen";
				$rs = $conn->Execute($sql);
				
				while($row = $rs->FetchRow()) {
					$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['periode'].'|'.$row['kelasmk'];
					
					if(!isset($data[$t_key]['sumMeeting']))
						$data[$t_key]['sumMeeting'] = 0;
					if($row['absen'] == 'S'){
						$data[$t_key]['sumPermitting'] += $row['jumlah'];
						$data[$t_key]['sumMeeting'] += $row['jumlah'];
					}else if($row['absen'] == 'I'){
						$data[$t_key]['sumPermitting'] += $row['jumlah'];
						$data[$t_key]['sumMeeting'] += $row['jumlah'];
					}else if($row['absen'] == 'H'){
						$data[$t_key]['sumPresence'] = $row['jumlah'];
						$data[$t_key]['sumMeeting'] += $row['jumlah'];
					}
						$data[$t_key]['sumAbsent']=0;

				}
			}
			//print_r($data);die();
			return $data;
		}
		
		/**
		* Mengambil periode akademik aktif
		* @return string
		*/
		function getPeriodeSekarang() {
			global $conn;
			
			// menggunakan model akademik
			require_once(Route::getModelPath('setting'));
			
			$row = forward_static_call(array('mSetting','getDataSession'),$conn);
			
			return $row['PERIODE'];
		}
		
		/**
		* Ganti password berdasarkan token
		* @param string $userid
		* @param string $passlama
		* @param string $passbaru
		* @return array
		*/
		function changePassword($userid,$passlama,$passbaru) {
			global $conn;
			
			// menggunakan model akademik
			require_once(Route::getModelPath('user'));
			
			$ok = forward_static_call(array('mUser','cekUserPass'),$conn,$userid,$passlama);
			if(!$ok)
				return array(true,cLang::ERROR_PASSWORD_RESET_PASSWORD_INCORRECT);
			
			$rec = array();
			$rec['password'] = md5($passbaru);
			
			$err = Query::recUpdate($conn,$record,'gate.sc_user','userid = '.Query::escape($userid));
			
			return array($err);
		}
		
		/**
		* Set device login dan daftarkan
		* @param array $record
		* @param string $token
		* @return int
		*/
		function setDevice($record,$token) {
			global $conn;
			
			$conn->BeginTrans();
		
			
			$err = Query::recUpdate($conn,$record,'gate.sc_loginmobile','token = '.Query::escape($token));
			if(!$err)
				$err = Query::recSave($conn,$record,'gate.sc_device','regid = '.Query::escape($record['regid']));
			
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
			
			return $err;
		}

		function facebookLinked($userid,$facebookId) {
			global $conn;
			
			$conn->BeginTrans();
			
			$record = array();
			$record['fbid'] = $facebookId;

			
			$err = Query::recUpdate($conn,$record,'gate.sc_user','userid = '.Query::escape($userid));
			
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
			
			return $err;
		}

		function updateprofile($userid,$username,$hp,$email, $file_image) {
			global $conn;
			
			$conn->BeginTrans();
			
			$record = array();
			$record['email'] = $email;
			$recordm['hp'] = $hp;
			$recordp['nohp'] = $hp;
			
			list($ismhs,$isdosen,$isemployee) = $this->getRolesByUsername($username);
			
			if($hp!=null && $hp!="" && strlen($hp)>0){
				if($ismhs){
					$err = Query::recUpdate($conn,$recordm,'akademik.ms_mahasiswa','nim = '.Query::escape($username));
				}
				else if($isdosen or $isemployee){
					$err = Query::recUpdate($conn,$recordp,'sdm.ms_pegawai','idpegawai = '.Query::escape($username));
				}
			}

			if($email!=null && $email!="" && strlen($email)>0){
				$err = Query::recUpdate($conn,$record,'gate.sc_user','userid = '.Query::escape($userid));
			}
			if(isset($_FILES["file_image"])){
				
				$server = $_SERVER['SCRIPT_FILENAME'];
				
				$cek = str_replace("index.php", "", $server);

				$target_dir = $cek."profpic/";

				$target_file = $target_dir.$userid.".png";
				
				move_uploaded_file($_FILES["file_image"]["tmp_name"], $target_file);

				list($width, $height, $type) = getimagesize($target_file);
				$newwidth = $maxwidth = 360;
				$newheight = round($maxwidth / $width * $height);

				$thumb = imagecreatetruecolor($newwidth, $newheight);
				switch ($type) {
			    	case IMAGETYPE_GIF:
			            $source = imagecreatefromgif($target_file);
			            break;
			        case IMAGETYPE_JPEG:
			            $source = imagecreatefromjpeg($target_file);
			            break;
			        case IMAGETYPE_PNG:
			            $source = imagecreatefrompng($target_file);
			            break;
			    }

				$resize = imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

				$upload = imagejpeg($thumb, $target_file, 70);
			}
			
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
			
			
			return $err;
		}
		
		function uploadcalendar($userid, $kegiatan, $datefrom, $dateto) {
			global $conn;
			
			$sql = "delete from mobile.ms_agenda where user_id = ".$userid;
			$ok = $conn->Execute($sql);
			
			$conn->BeginTrans();
			foreach($kegiatan as $key=>$row){
				$record = array();
				$record['user_id'] = $userid;
				$record['kegiatan'] = $kegiatan[$key];
				$record['tanggal_mulai'] = $datefrom[$key];
				$record['tanggal_akhir'] = $dateto[$key];
				$err = Query::recInsert($conn,$record,'mobile.ms_agenda');
				$ok = Query::isOK($err);
				$conn->CommitTrans($ok);
			}
				
			
			
			return $err;
		}
		
		/**
		* Set reset password
		* @param array $record
		* @param string $token
		* @return array
		*/
		function setResetPassword($email) {
			global $conn;
			
			// ambil userid
			$sql = "select userid, username from gate.sc_user where email = ".Query::escape($email);
			$row = $conn->GetRow($sql);
			
			if(empty($row['userid'])) {
				$ret = array();
				$ret['error'] = true;
				$ret['message'] = cLang::ERROR_PASSWORD_FORGET_EMAIL_NOT_FOUND;
				
				return $ret;
			}
			
			$token = cHelper::getNewToken();
			
			$record = array();
			$record['tokenreset'] = $token;
			
			$err = Query::recUpdate($conn,$record,'gate.sc_user','userid = '.Query::escape($row['userid']));
			
			$ret = array();
			$ret['error'] = $err;
			$ret['token'] = $token;
			$ret['username'] = $row['username'];
			
			return $ret;
		}
		
		/**
		* Simpan presensi mahasiswa per perkuliahan
		* @param string $nim
		* @param string $id
		* @return array
		*/
		function savePresensi($nim,$id) {
			global $conn;
			
			// menggunakan model akademik
			require_once(Route::getModelPath('absensikuliah'));
			
			// membentuk ulang key sesuai model absensi kuliah
			$a_id = explode('|',$id);
			$a_idm = array($a_id[3],$a_id[5],$a_id[4],$a_id[2],$a_id[6],$a_id[0],$a_id[1],$nim,$a_id[7],$a_id[8]);
			
			// cek ada tidaknya data
			$cek = forward_static_call(array('mAbsensiKuliah','isDataExist'),$conn,$a_idm);
			if($cek)
				return array(true,cLang::ERROR_EXISTS);
			
			// tambahkan presensi
			$record = array();
			$record['tglkuliah'] = $a_id[0];
			$record['perkuliahanke'] = $a_id[1];
			$record['periode'] = $a_id[2];
			$record['thnkurikulum'] = $a_id[3];
			$record['kodeunit'] = $a_id[4];
			$record['kodemk'] = $a_id[5];
			$record['kelasmk'] = $a_id[6];
			$record['jeniskuliah'] = $a_id[7];
			$record['kelompok'] = $a_id[8];
			$record['nim'] = $nim;
			$record['absen'] = 'H';
			$record['t_updateact'] = 'presence/save';
			
			$err = forward_static_call(array('mAbsensiKuliah','insertRecord'),$conn,$record);
			
			return array($err);
		}
	}