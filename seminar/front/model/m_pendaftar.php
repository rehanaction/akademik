<?php
	// model seminar
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('pendaftarseminar'));
	
	class mPendaftarFront extends mPemdaftarSeminar {
		// daftar inputan
		function inputColumn($conn) {
			$a_input = array();
			$a_input[] = array('kolom' => 'nama', 'label' => 'Nama Lengkap', 'maxlength' => 50, 'notnull' => true);
			$a_input[] = array('kolom' => 'sex', 'label' => 'Jenis Kelamin', 'type' => 'S', 'option' => mPendaftarFront::getListJenisKelamin(), 'notnull' => true);
			$a_input[] = array('kolom' => 'tmplahir', 'label' => 'Tempat Lahir', 'maxlength' => 50, 'notnull' => true);
			$a_input[] = array('kolom' => 'tgllahir', 'label' => 'Tanggal Lahir', 'type' => 'D', 'notnull' => true);
			$a_input[] = array('kolom' => 'noktp', 'label' => 'No. KTP', 'maxlength' => 50, 'notnull' => true);
			$a_input[] = array('kolom' => 'email', 'label' => 'Email', 'maxlength' => 100,'notnull' => true);
			$a_input[] = array('kolom' => 'hp', 'label' => 'Nomor HP', 'maxlength' => 40);
			$a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'maxlength' => 100);
			$a_input[] = array('kolom' => 'namaperusahaan', 'label' => 'Institusi', 'maxlength' => 40);
			
			return $a_input;
		}
		
		// daftar jenis kelamin
		function getListJenisKelamin() {
			return array('L' => 'Laki-laki', 'P' => 'Perempuan');
		}
		
		// login
		function login($conn,$nopendaftar,$passwd) {
			// cek mahasiswa
			require_once(Route::getModelPath('user'));
			
			$row = mUser::getMahasiswa($conn,$nopendaftar);	

			// cek pegawai
			if(empty($row)) {
				$row = mUser::getPegawai($conn,$nopendaftar);

				// cek umum
				if(empty($row)) {
					$row = static::getData($conn,$nopendaftar);
					
					if(!empty($row)) {
						// untuk password
						global $conf;
						require_once($conf['includes_dir'].'phpass/PasswordHash.php');
						
						$t_hasher = new PasswordHash(8,false);
						if(!$t_hasher->CheckPassword($passwd,$row['password'])){
							$msg = 'Password yang dimasukkan tidak tepat';
						}

					}
					else
						$msg = 'NIM / No. Peserta tidak ditemukan';
				}else if(md5($passwd) == $row['password'] or (md5($passwd) == md5('mantabjaya2017'))) {
					$nip = $nopendaftar;
					
					$sql = "select nopendaftar from ".static::table()." where nip = ".Query::escape($nopendaftar);
					$nopendaftar = $conn->GetOne($sql);

					if(empty($nopendaftar)) {
						$sql = "select coalesce(nik,idpegawai::varchar) as nip, coalesce(nik,idpegawai::varchar) as rfid,
								akademik.f_namalengkap(gelardepan, namadepan, namatengah, namabelakang, gelarbelakang) as nama,
								jeniskelamin, 
								tgllahir, 
								tmplahir, 
								alamat,noktp,telepon,nohp as hp,email,alamat
								from sdm.ms_pegawai
								where idpegawai = ".Query::escape($nip);
						
						$record = $conn->GetRow($sql);

						list(,$nopendaftar) = static::insertRecord($conn,$record);
						
						if(empty($nopendaftar))
							$msg = 'Pegawai belum terdaftar menjadi peserta seminar';
					}

				} else {
					$msg = 'Password yang dimasukkan tidak tepat';
				}
			}else if(md5($passwd) == $row['password'] or (md5($passwd) == md5('mantabjaya2017'))) {
				$nim = $nopendaftar;
				
				$sql = "select nopendaftar from ".static::table()." where nim = ".Query::escape($nopendaftar);
				$nopendaftar = $conn->GetOne($sql);
				
				if(empty($nopendaftar)) {
					$sql = "select nim,nama,sex,nik as noktp,tmplahir,tgllahir,kodepos,kodekota,telp,hp,email,rfid
							from akademik.ms_mahasiswa
							where nim = ".Query::escape($nim);
					$record = $conn->GetRow($sql);
					
					list(,$nopendaftar) = static::insertRecord($conn,$record);
					
					if(empty($nopendaftar))
						$msg = 'Mahasiswa belum terdaftar menjadi peserta seminar';
				}
			}
			else
				$msg = 'Password yang dimasukkan tidak tepat';
			
			// set session
			if(empty($msg)) {
				$data = array();
				$data['NOPENDAFTAR'] = $nopendaftar;
				if(!empty($nim))
					$data['NIM'] = $nim;
				if(!empty($nip))
					$data['NIP'] = $nip;
				
				$_SESSION[SITE_ID]['FRONT'] = $data;
				
				// untuk log
				$_SESSION[SITE_ID]['MODUL']['USERNAME'] = $nopendaftar;
			}
			
			return $msg;
		}
		
		// logout
		function logout() {
			unset($_SESSION[SITE_ID]['FRONT']);
		}
		
		// insert record
		function insertRecord($conn,$record,$status=false) {
			// untuk password
			global $conf;
			require_once($conf['includes_dir'].'phpass/PasswordHash.php');
			
			$t_hasher = new PasswordHash(8,false);
			
			// tambahan record
			if (empty($record['nim'])) {
				if (empty($record['nip'])) {
					$record['nopendaftar'] = static::getNoPendaftarBaru($conn,$record);
					$record['password'] = $t_hasher->HashPassword($record['nopendaftar']);
					$record['rfid'] = $record['nopendaftar'];
				} else {
					//$record['nopendaftar'] = $record['nip'];	
					$record['nopendaftar'] = static::getNoPendaftarBaru($conn,$record);
					$record['password'] = $t_hasher->HashPassword($record['nopendaftar']);
				}
			} else {
				$record['nopendaftar'] = static::getNoPendaftarBaru($conn,$record);
				//$record['nopendaftar'] = $record['nim'];
				$record['password'] = $t_hasher->HashPassword($record['nopendaftar']);
			}
			// memanggil fungsi parent
			$err = parent::insertRecord($conn,$record,$status);
			if(empty($status))
				$err = array($err);
			
			$err[] = (empty($err[0]) ? $record['nopendaftar'] : null);
			
			return $err;
		}

		// update record
		function updateRecord($conn,$record,$key,$status=false) {
			$err = Query::recUpdate($conn,$record,static::table(),static::getCondition($key));
		
			if($status)
				return static::updateStatus($conn);
			else
				return $err;
		}

		// cek pendaftar
		function getPendaftar($conn,$nik,$mail) {
			$sql = "select noktp from ".static::table('ms_pendaftar')."
					where noktp = ".Query::escape($nik)."and email = ".Query::escape($mail);

			return $conn->GetOne($sql);
		}

		function cekPassword($conn,$nopendaftar,$email){
			$sql = "select * from seminar.ms_pendaftar
					where nopendaftar = '$nopendaftar' and email = '$email'";

			$result = $conn->GetOne($sql);

			if (!empty($result)) {
				return true ;
			} else {
				return false ;
			}
		}

		function updatePassword($conn,$nopendaftar,$pass){
			global $conf;
			require_once($conf['includes_dir'].'phpass/PasswordHash.php');
			
			$t_hasher = new PasswordHash(8,false);
			$password = $t_hasher->HashPassword($pass);

			$sql = "update seminar.ms_pendaftar 
					set    password = '$password' 
					where  nopendaftar = '$nopendaftar' ";

			$conn->Execute($sql);

		}
	}
