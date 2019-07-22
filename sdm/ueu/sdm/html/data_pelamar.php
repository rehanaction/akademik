<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];

	$connportal = Query::connect('portal');
	if($_SERVER['REMOTE_ADDR'] == "36.85.91.184" or $_SERVER['REMOTE_ADDR'] == "66.96.234.212") //ip public sevima
		$connportal->debug=true;
	
	// include
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getModelPath('rekrutmen'));
	require_once(Route::getModelPath('riwayat'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_self = (int)$_REQUEST['self'];
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getUserName();
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Pelamar';
	$p_tbwidth = 900;
	$p_aktivitas = 'BIODATA';
	$p_listpage = Route::getListPage();
	$p_foto = uForm::getPathImagePelamar($conn,$r_key);
		
	$p_model = mRekrutmen;
	$p_dbtable = 're_calon';
	$where = 'nopendaftar';
	$p_model::updateView($conn,$r_key);
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	// struktur view
	if(empty($r_key))
		$p_edit = false;
	else
		$p_edit = true;
	
	$a_input = array();
	$a_input[] = array('kolom' => 'nopendaftar', 'label' => 'No Daftar (Auto)', 'maxlength' => 15, 'size' => 30, 'readonly' => true);
	$a_input[] = array('kolom' => 'gelardepan', 'maxlength' => 25, 'size' => 15);
	$a_input[] = array('kolom' => 'gelarbelakang', 'maxlength' => 25, 'size' => 15);
	$a_input[] = array('kolom' => 'namadepan', 'label' => 'Depan', 'maxlength' => 100, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'namatengah', 'label' => 'Tengah', 'maxlength' => 100, 'size' => 30);
	$a_input[] = array('kolom' => 'namabelakang', 'label' => 'Belakang', 'maxlength' => 100, 'size' => 30);
	$a_input[] = array('kolom' => 'sex', 'label' => 'Jenis Kelamin', 'type' => 'S', 'option' => mPegawai::jenisKelamin($conn), 'empty' => true, 'notnull' => true);
	$a_input[] = array('kolom' => 'idagama', 'label' => 'Agama', 'type' => 'S', 'option' => mPegawai::agama($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'tmplahir', 'label' => 'Tempat Lahir', 'maxlength' => 100, 'size' => 30);
	$a_input[] = array('kolom' => 'tgllahir', 'label' => 'Tgl Lahir', 'type' => 'D');
	$a_input[] = array('kolom' => 'statusnikah', 'label' => 'Status Nikah', 'type' => 'S', 'option' => mPegawai::statusNikah());
		
	$a_input[] = array('kolom' => 'telp', 'label' => 'No. Telepon', 'maxlength' => 30, 'size' => 30);
	$a_input[] = array('kolom' => 'hp', 'label' => 'No Ponsel', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'email', 'label' => 'Email', 'maxlength' => 100, 'size' => 30);
	$a_input[] = array('kolom' => 'sukubangsa', 'label' => 'Suku Bangsa', 'maxlength' => 25, 'size' => 30);
		
	$a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'maxlength' => 150, 'size' => 60);
	$a_input[] = array('kolom' => 'kelurahan', 'label' => 'Kelurahan', 'maxlength' => 150, 'size' => 80);
	$a_input[] = array('kolom' => 'idkelurahan', 'type' => 'H');
	$a_input[] = array('kolom' => 'kodepos', 'label' => 'Kode Pos', 'maxlength' => 5, 'size' => 6);
	
	$a_input[] = array('kolom' => 'noktp', 'label' => 'No. KTP', 'maxlength' => 150, 'size' => 60);
	$a_input[] = array('kolom' => 'tglktp', 'label' => 'Tanggal KTP', 'type' => 'D');
	$a_input[] = array('kolom' => 'tglhabisktp', 'label' => 'Tanggal KTP', 'type' => 'D');
	$a_input[] = array('kolom' => 'alamatktp', 'label' => 'Alamat KTP', 'maxlength' => 150, 'size' => 60);
	$a_input[] = array('kolom' => 'kelurahanktp', 'label' => 'Kelurahan', 'maxlength' => 150, 'size' => 80);
	$a_input[] = array('kolom' => 'idkelurahanktp', 'type' => 'H');
	$a_input[] = array('kolom' => 'kodeposktp', 'label' => 'Kode Pos KTP', 'maxlength' => 5, 'size' => 6);
	
	$a_input[] = array('kolom' => 'noberkas', 'label' => 'No Berkas', 'maxlength' => 30, 'size' => 30);
	$a_input[] = array('kolom' => 'nidn', 'label' => 'NIDN', 'maxlength' => 25, 'size' => 30);	
	$a_input[] = array('kolom' => 'kodeposisi', 'label' => 'Ambil Posisi', 'type' => 'S', 'option' => mRekrutmen::getPosisi($conn));
	$a_input[] = array('kolom' => 'idjfungsional', 'label' => 'Jabatan Akademik', 'type' => 'S', 'option' => mRiwayat::jabatanFungsional($conn));
	$a_input[] = array('kolom' => 'tmtjabatan', 'label' => 'TMT. Jabatan Akademik', 'type' => 'D');
	$a_input[] = array('kolom' => 'nilaitoefl', 'label' => 'Toefl', 'type' => 'N,2', 'size' => 5, 'maxlength' => 5);
	$a_input[] = array('kolom' => 'tglterimaberkas', 'label' => 'Tanggal Terima Berkas', 'type' => 'D');
	$a_input[] = array('kolom' => 'filelamaran', 'label' => 'File Lamaran', 'type' => 'U', 'uptype' => 'filelamaran', 'size' => 30);
	$a_input[] = array('kolom' => 'filecv', 'label' => 'File CV', 'type' => 'U', 'uptype' => 'filecv', 'size' => 30);
	$a_input[] = array('kolom' => 'filejabakademik', 'label' => 'File Jabatan Akademik', 'type' => 'U', 'uptype' => 'filejabakademik', 'size' => 30);
	$a_input[] = array('kolom' => 'fileserdos', 'label' => 'File Sertifikat Dosen', 'type' => 'U', 'uptype' => 'fileserdos', 'size' => 30);
	$a_input[] = array('kolom' => 'filepelatihan', 'label' => 'File Pelatihan', 'type' => 'U', 'uptype' => 'filepelatihan', 'size' => 30);
	$a_input[] = array('kolom' => 'fileseminar', 'label' => 'File Seminar', 'type' => 'U', 'uptype' => 'fileseminar', 'size' => 30);
	$a_input[] = array('kolom' => 'filesertifikat', 'label' => 'File Sertifikat', 'type' => 'U', 'uptype' => 'filesertifikat', 'size' => 30);
	$a_input[] = array('kolom' => 'prestasiakademik', 'label' => 'Prestasi Akademik', 'type' => 'A', 'maxlength' => 1000, 'cols' => 30, 'rows' => '3');
	$a_input[] = array('kolom' => 'keahlian', 'label' => 'Keahlian', 'type' => 'A', 'maxlength' => 1000, 'cols' => 30, 'rows' => '3');
	$a_input[] = array('kolom' => 'alasanmelamar', 'label' => 'Alasan Melamar', 'type' => 'A', 'maxlength' => 1000, 'cols' => 30, 'rows' => '3');
	$a_input[] = array('kolom' => 'penghasilandiharapkan', 'label' => 'Penghasilan Diharapkan', 'maxlength' => 14, 'size' => 14, 'type' => 'N');
	
	$a_input[] = array('kolom' => 'statuslulus', 'label' => 'Status Lulus', 'type' => 'S', 'option' => mRekrutmen::statusLulus(), 'empty' => true, 'readonly' => true);
	$a_input[] = array('kolom' => 'suratperjanjian', 'label' => 'No Surat Perjanjian', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'tglditerimapegawai', 'label' => 'Tgl. Diterima Pegawai', 'type' => 'D', 'readonly' => true);
	$a_input[] = array('kolom' => 'tglsurat', 'label' => 'Tgl. Surat Perjanjian', 'type' => 'D');
	$a_input[] = array('kolom' => 'tglaktif', 'label' => 'Tgl. Aktif Bekerja', 'type' => 'D');
	$a_input[] = array('kolom' => 'deskripsi', 'label' => 'Catatan', 'type' => 'A', 'maxlength' => 255, 'cols' => 30, 'rows' => '3');
	
	// mengambil data riwayat
	$a_detail = array();
	
	$t_detailpend = array();
	$t_detailpend[] = array('kolom' => 'tahunlulus', 'label' => 'Tahun Lulus', 'size' => 4, 'maxlength' => 4, 'align' => 'center');
	$t_detailpend[] = array('kolom' => 'idpendidikan', 'label' => 'Jenjang Pendidikan', 'type' => 'S', 'option' => mPegawai::pendidikan($conn));
	$t_detailpend[] = array('kolom' => 'namainstitusi', 'label' => 'Instansi', 'size' => 15, 'maxlength' => 255);
	$t_detailpend[] = array('kolom' => 'jurusan', 'label' => 'Jurusan', 'size' => 15, 'maxlength' => 100);
	$t_detailpend[] = array('kolom' => 'ipk', 'label' => 'IPK', 'type' => 'N,2', 'size' => 4, 'maxlength' => 4);
	$t_detailpend[] = array('kolom' => 'noijazah', 'label' => 'No Ijazah', 'size' => 15, 'maxlength' => 50);
	$t_detailpend[] = array('kolom' => 'tglijazah', 'label' => 'Tgl. Ijazah', 'type' => 'D');
	$t_detailpend[] = array('kolom' => 'fileijazahpelamar', 'label' => 'File Ijazah', 'type' => 'U', 'uptype' => 'fileijazahpelamar', 'size' => 10);
	$t_detailpend[] = array('kolom' => 'filetranskrippelamar', 'label' => 'File Transkrip', 'type' => 'U', 'uptype' => 'filetranskrippelamar', 'size' => 10);

	//pengalaman kerja	
	$t_detailpengkerja = array();
	$t_detailpengkerja[] = array('kolom' => 'periodekerja', 'label' => 'Periode Kerja', 'size' => 10, 'maxlength' => 25);
	$t_detailpengkerja[] = array('kolom' => 'namainstansi', 'label' => 'Instansi', 'size' => 15, 'maxlength' => 100);
	$t_detailpengkerja[] = array('kolom' => 'alamatinstansi', 'label' => 'Alamat', 'type' => 'A', 'maxlength' => 255, 'cols' => 30);
	$t_detailpengkerja[] = array('kolom' => 'jabatan', 'label' => 'Jabatan', 'size' => 15, 'maxlength' => 100);
	$t_detailpengkerja[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'maxlength' => 255, 'cols' => 30);	
		
	$a_detail['pendidikan'] = array('key' => $p_model::getDetailInfo('pendidikan','key'), 'data' => $t_detailpend);
	$a_detail['pengalamankerja'] = array('key' => $p_model::getDetailInfo('pengalamankerja','key'), 'data' => $t_detailpengkerja);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key)){
			$record['nopendaftar'] = $p_model::setNoPendaftar($conn)+1;
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable);
			$r_key = $record['nopendaftar'];
		}else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$where);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {	
		$conn->BeginTrans();

		$connportal->BeginTrans();

		$a_pdd = $p_model::getRPendidikan($conn,$r_key);
		if(count($a_pdd)>0){
			foreach ($a_pdd as $key => $row) {
				list($p_posterr,$p_postmsg) = $p_model::delete($conn,$row['nopendpelamar'],'re_pendpelamar','nopendpelamar','','fileijazahpelamar,filetranskrippelamar');

				//hapus juga di table portal
				if(!$p_posterr){
					if(!empty($row['refnopendpelamar'])){
						list($p_posterrportal,$p_postmsg) = $p_model::delete($connportal,$row['refnopendpelamar'],'re_pendpelamar','nopendpelamar','portal');
						
						if(!$p_posterrportal){
							$p_posterrportal = Route::deleteFilePortal('fileijazahpelamar', $row['refnopendpelamar']);
							if($p_posterrportal){
								$p_postmsg = 'Hapus file ijazah gagal';
								break;
							}
						}
						
						if(!$p_posterrportal){
							$p_posterrportal = Route::deleteFilePortal('filetranskrippelamar', $row['refnopendpelamar']);
							if($p_posterrportal){
								$p_postmsg = 'Hapus file transkrip gagal';
								break;
							}
						}

						if($p_posterrportal)
							break;
					}
				}else
					break;
			}
		}

		if(!$p_posterr and !$p_posterrportal){
			$a_pkj = $p_model::getRPengalamanKerja($conn,$r_key);
			if(count($a_pkj)>0){
				foreach ($a_pkj as $key => $row) {
					list($p_posterr,$p_postmsg) = $p_model::delete($conn,$row['nopengkerjapelamar'],'re_pengkerjapelamar','nopengkerjapelamar');

					//hapus juga di table portal
					if(!$p_posterr){
						if(!empty($row['refnopengkerjapelamar'])){
							list($p_posterrportal,$p_postmsg) = $p_model::delete($connportal,$row['refnopengkerjapelamar'],'re_pengkerjapelamar','nopengkerjapelamar','portal');

							if($p_posterrportal)
								break;
						}
					}else
						break;
				}
			}
		}
		
		//hapus data pelamar di portal
		if(!$p_posterr and !$p_posterrportal){
			$r_refpelamar = $p_model::getRefPelamar($conn,$r_key);
			if(!empty($r_refpelamar)){
				list($p_posterrportal,$p_postmsg) = $p_model::delete($connportal,$r_refpelamar,'re_calon','nopendaftar','portal');

				//untuk file
				if(!$p_posterrportal){
			        $a_file = array('filelamaran' => 'Lamaran','filecv' => 'CV','fileserdos' => 'File Sertifikat Dosen','filejabakademik' => 'File Jabatan Akademik','filepelatihan' => 'File Pelatihan','fileseminar' => 'File Seminar','filesertifikat' => 'File Sertifikat');

			        foreach ($a_file as $key => $value) {
						$p_posterrportal = Route::deleteFilePortal($key, $r_refpelamar);
						if($p_posterrportal){
							$p_postmsg = 'Hapus file '.$value.' gagal';
							break;
						}
			        }
			    }

		        //hapus foto
		        if(!$p_posterrportal){
			        $p_posterrportal = Route::deleteFilePortal('fotopelamar', $r_refpelamar, true);
					if($p_posterrportal)
						$p_postmsg = 'Hapus foto gagal';
				}
			}
		}

		if(!$p_posterr and !$p_posterrportal)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'re_prosesseleksi',$where);

		if(!$p_posterr and !$p_posterrportal)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$where,'','filelamaran,filecv');

		if(!$p_posterr and !$p_posterrportal){
			@unlink($p_foto);
		
			$ok = Query::isOK($p_posterr);
			$conn->CommitTrans($ok);

			$okportal = Query::isOK($p_posterrportal);
			$connportal->CommitTrans($ok);
			Route::navigate($p_listpage);
		}
	}
	else if($r_act == 'deletefile' and $c_delete) {
		$r_file = CStr::removeSpecial($_POST['file']);

		$r_refpelamar = $p_model::getRefPelamar($conn,$r_key);
		if(!empty($r_refpelamar)){
			$p_posterrportal = Route::deleteFilePortal($r_file, $r_refpelamar);
			if($p_posterrportal){
				$p_postmsg = 'Hapus file '.$value.' gagal';
				break;
			}
		}		
		
		if(!$p_posterrportal)
			list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key,$p_dbtable,$r_file,$where);	
	}
	else if($r_act == 'insertdet' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$conn->BeginTrans();

		if(empty($r_key)){
			$record['nopendaftar'] = $p_model::setNoPendaftar($conn)+1;
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable);
			$r_key = $record['nopendaftar'];
		}

		if(!$p_posterr){
			unset($record,$post);
			$r_detail = CStr::removeSpecial($_POST['detail']);
			
			$record = array('nopendaftar' => $r_key);
			foreach($a_detail[$r_detail]['data'] as $t_detail) {
				$t_value = $_POST[$r_detail.'_'.CStr::cEmChg($t_detail['nameid'],$t_detail['kolom'])];
				if ($t_detail['type'] == 'D')
					$record[$t_detail['kolom']] = CStr::formatDate($t_value);				
				else if(substr($t_detail['type'],0,1) == 'N') {
					list(,$dec) = explode(',',$t_detail['type']);
					$record[$t_detail['kolom']] = CStr::cStrDec($t_value,(int)$dec);
				}
				else
					$record[$t_detail['kolom']] = CStr::cStrNull($t_value);
			}
			
			list($p_posterr,$p_postmsg) = $p_model::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail,true);
		}

		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'deletedet' and $c_edit) {
		$conn->BeginTrans();
		$connportal->BeginTrans();

		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		if($r_detail == 'pendidikan'){
			$keypp = $p_model::getRefPendidikan($conn,$r_subkey);
			if(!empty($keypp)){
				list($p_posterrportal,$p_postmsg) = $p_model::delete($connportal,$keypp,'re_pendpelamar','nopendpelamar','portal');
						
				if(!$p_posterrportal){
					$p_posterrportal = Route::deleteFilePortal('fileijazahpelamar', $keypp);
					if($p_posterrportal)
						$p_postmsg = 'Hapus file ijazah gagal';
				}

				if(!$p_posterrportal){
					$p_posterrportal = Route::deleteFilePortal('filetranskrippelamar', $keypp);
					if($p_posterrportal)
						$p_postmsg = 'Hapus file transkrip gagal';
				}
			}

			if(!$p_posterrportal)
				list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkey,$r_detail,'','fileijazahpelamar,filetranskrippelamar');
		}
		else{
			if(!empty($keypp)){
				$keypp = $p_model::getRefPengalamanKerja($conn,$r_subkey);
				list($p_posterrportal,$p_postmsg) = $p_model::delete($connportal,$keypp,'re_pengkerjapelamar','nopengkerjapelamar','portal');
			}

			if(!$p_posterrportal)
				list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkey,$r_detail);
		}
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);		
		$okportal = Query::isOK($p_posterrportal);
		$connportal->CommitTrans($ok);
	}
	else if($r_act == 'savefoto' and $c_edit) {
		if(empty($_FILES['foto']['error'])) {
			$err = Page::createFoto($_FILES['foto']['tmp_name'],$p_foto,200,200);
			
			switch($err) {
				case -1:
				case -2: $msg = 'format foto harus JPG, GIF, atau PNG'; break;
				case -3: $msg = 'foto tidak bisa disimpan'; break;
				default: $msg = false;
			}
			if($msg !== false)
				$msg = 'Upload gagal, '.$msg;
		}
		else
			$msg = Route::uploadErrorMsg($_FILES['foto']['error']);
		
		uForm::reloadImagePelamar($conn,$r_key,$msg);
	}
	else if($r_act == 'deletefoto' and $c_edit) {
		$r_refpelamar = $p_model::getRefPelamar($conn,$r_key);
		if(!empty($r_refpelamar)){
			$p_posterrportal = Route::deleteFilePortal('fotopelamar', $r_refpelamar, true);
			if($p_posterrportal)
				$p_postmsg = 'Hapus foto gagal';
		}

		@unlink($p_foto);
		
		uForm::reloadImagePelamar($conn,$r_key);
	}
	else if ($r_act == 'download') {
        $r_key = $_REQUEST['file'];
        list($id,$folder,$namafile) = explode('::', $r_key);

        $t_path = $conf['uploads_portal'].$folder.'/'.$id;
        $t_mime = Route::getMIME($t_path);

        ob_clean();
        header("Content-Type: $t_mime");
        header('Content-Disposition: attachment; filename="' . $namafile . '"');

        echo file_get_contents($t_path);
        exit;
    }
	
	if(!empty($r_key)) {
		$rowdpdd = array();
		$rowdpdd += $p_model::getPendidikan($conn,$r_key,'pendidikan',$post);
		
		$rowdpk = array();
		$rowdpk += $p_model::getPengalamanKerja($conn,$r_key,'pengalamankerja',$post);

		$r_refpelamar = $p_model::getRefPelamar($conn,$r_key);
	}
	
	$sql = mRekrutmen::getDataEditPelamar($r_key);
	$row = mRekrutmen::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$where,$sql,$r_refpelamar);
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	}?>
				<table border="0" cellspacing="10" align="center">
					<tr>
						<?	if($c_readlist) { ?>
						<td id="be_list" class="TDButton" onclick="goList()">
							<img src="images/list.png"> Daftar
						</td>
						<?	} if($c_insert) { ?>
						<td id="be_add" class="TDButton" onclick="goNew()">
							<img src="images/add.png"> Data Baru
						</td>
						<?	} if($c_edit) { ?>
					   <td id="be_edit" class="TDButton" onclick="goEdit()">
							<img src="images/edit.png"> Sunting
						</td>
						<td id="be_save" class="TDButton" onclick="goSave()" style="display:none">
							<img src="images/disk.png"> Simpan
						</td>
						<td id="be_undo" class="TDButton" onclick="goUndo()" style="display:none">
							<img src="images/undo.png"> Batal
						</td>
						<?	} if($c_delete and !empty($r_key)) { ?>
						<td id="be_delete" class="TDButton" onclick="goDelete()">
							<img src="images/delete.png"> Hapus
						</td>
						<?	} if (!empty($r_key)) {?>
						<td id="be_print" class="TDButton" onclick="goPrint()">
							<img src="images/small-print.png"> Print
						</td>
						<?}?>
					</tr>
				</table>
				<? if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	}
				
					if(empty($p_fatalerr)) { ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
					<?	/********/
						/* DATA */
						/********/
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nopendaftar') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'nopendaftar') ?></td>
							<td align="right" valign="top" rowspan="7">
								<?= empty($r_key) ? '' : uForm::getImagePelamar($conn,$r_key,true,$r_refpelamar) ?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG">Gelar</td>
							<td class="RightColumnBG">
								<table>
									<tr><td>Depan<td><td>:</td><td><?= Page::getDataInput($row,'gelardepan') ?></td></tr>
									<tr><td>Belakang<td><td>:</td><td><?= Page::getDataInput($row,'gelarbelakang') ?></td></tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG">Nama</td>
							<td class="RightColumnBG">
								<table>
									<tr><td><?= Page::getDataLabel($row,'namadepan') ?><td><td>:</td><td><?= Page::getDataInput($row,'namadepan') ?></td></tr>
									<tr><td><?= Page::getDataLabel($row,'namatengah') ?><td><td>:</td><td><?= Page::getDataInput($row,'namatengah') ?></td></tr>
									<tr><td><?= Page::getDataLabel($row,'namabelakang') ?><td><td>:</td><td><?= Page::getDataInput($row,'namabelakang') ?></td></tr>
									</table>
								</td>
						</tr>
						<?= Page::getDataTR($row,'sex') ?>
						<?= Page::getDataTR($row,'idagama') ?>
						<?= Page::getDataTR($row,'tmplahir') ?>
						<?= Page::getDataTR($row,'tgllahir') ?>
					</table>
					</div>
				</center>
				<br>
				<center>
				<div class="tabs" style="width:<?= $p_tbwidth ?>px">
					<ul>
						<li><a id="tablink" href="javascript:void(0)">Biodata</a></li>
						<li><a id="tablink" href="javascript:void(0)">Info Pelamar</a></li>
						<li><a id="tablink" href="javascript:void(0)">Pendidikan</a></li>
						<li><a id="tablink" href="javascript:void(0)">Pengalaman Kerja</a></li>
						<li><a id="tablink" href="javascript:void(0)">Info Calon Pegawai</a></li>
					</ul>
				
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'statusnikah') ?>
						<?= Page::getDataTR($row,'telp') ?>
						<?= Page::getDataTR($row,'hp') ?>
						<?= Page::getDataTR($row,'email') ?>
						<?= Page::getDataTR($row,'sukubangsa') ?>
						<tr height="30">
							<td class="DataBG" colspan="2">Alamat Sekarang</td>
						</tr>
						<?= Page::getDataTR($row,'alamat') ?>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'kelurahan') ?></td>
							<td>
								<?= Page::getDataInput($row,'kelurahan') ?>
								<?= Page::getDataInput($row,'idkelurahan') ?>	
								<span id="edit" style="display:none">
									<img id="imgkel_c" src="images/green.gif">
									<img id="imgkel_u" src="images/red.gif" style="display:none">
								</span>
							</td>
						</tr>
						<?= Page::getDataTR($row,'kodepos') ?>
						<tr height="30">
							<td class="DataBG" colspan="2">Alamat KTP</td>
						</tr>
						<?= Page::getDataTR($row,'noktp') ?>
						<?= Page::getDataTR($row,'tglktp') ?>
						<?= Page::getDataTR($row,'alamatktp') ?>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'kelurahanktp') ?></td>
							<td>
								<?= Page::getDataInput($row,'kelurahanktp') ?>
								<?= Page::getDataInput($row,'idkelurahanktp') ?>	
								<span id="edit" style="display:none">
									<img id="imgkelktp_c" src="images/green.gif">
									<img id="imgkelktp_u" src="images/red.gif" style="display:none">
								</span>
							</td>
						</tr>
						<?= Page::getDataTR($row,'kodeposktp') ?>
					</table>
					</div>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'noberkas') ?></td>
							<td class="RightColumnBG" <?php echo $row['idtipepeg'] != 'TK' ? '' : 'colspan="3"'?>><?= Page::getDataInput($row,'noberkas') ?></td>
                        	<?php if($row['idtipepeg'] != 'TK'){?>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'nidn') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'nidn') ?></td>
							<?php }?>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'kodeposisi') ?></td>
							<td class="RightColumnBG" <?php echo $row['idtipepeg'] != 'TK' ? '' : 'colspan="3"'?>><?= Page::getDataInput($row,'kodeposisi') ?></td>
                        	<?php if($row['idtipepeg'] != 'TK'){?>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'idjfungsional') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'idjfungsional') ?></td>
							<?php }?>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglterimaberkas') ?></td>
							<td class="RightColumnBG" <?php echo $row['idtipepeg'] != 'TK' ? '' : 'colspan="3"'?>><?= Page::getDataInput($row,'tglterimaberkas') ?></td>
                        	<?php if($row['idtipepeg'] != 'TK'){?>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tmtjabatan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tmtjabatan') ?></td>
							<?php }?>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'filelamaran') ?></td>
							<td class="RightColumnBG" <?php echo $row['idtipepeg'] != 'TK' ? '' : 'colspan="3"'?>><?= Page::getDataInput($row,'filelamaran') ?></td>
                        	<?php if($row['idtipepeg'] != 'TK'){?>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'filejabakademik') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'filejabakademik') ?></td>
							<?php }?>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'filecv') ?></td>
							<td class="RightColumnBG" <?php echo $row['idtipepeg'] != 'TK' ? '' : 'colspan="3"'?>><?= Page::getDataInput($row,'filecv') ?></td>
                        	<?php if($row['idtipepeg'] != 'TK'){?>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'fileserdos') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'fileserdos') ?></td>
							<?php }?>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'prestasiakademik') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'prestasiakademik') ?></td>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'nilaitoefl') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'nilaitoefl') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="15%"><?= Page::getDataLabel($row,'filepelatihan') ?></td>
							<td class="RightColumnBG" width="35%"><?= Page::getDataInput($row,'filepelatihan') ?></td>
							<td class="LeftColumnBG" width="15%"><?= Page::getDataLabel($row,'keahlian') ?></td>
							<td class="RightColumnBG" width="35%"><?= Page::getDataInput($row,'keahlian') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'fileseminar') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'fileseminar') ?></td>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'alasanmelamar') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'alasanmelamar') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'filesertifikat') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'filesertifikat') ?></td>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'penghasilandiharapkan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'penghasilandiharapkan') ?></td>
						</tr>
					</table>
					</div>

					<div id="items" style="overflow-x: scroll">
					<?= Page::getDetailTable($rowdpdd,$a_detail,'pendidikan','Pendidikan',true,$c_edit,$c_delete,'refnopendpelamar','nopendpelamar') ?>
					</div>
					<div id="items">
					<?= Page::getDetailTable($rowdpk,$a_detail,'pengalamankerja','Pengalaman Kerja',true,$c_edit,$c_delete,'refnopengkerjapelamar','nopengkerjapelamar') ?>
					</div>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'statuslulus') ?>
						<?= Page::getDataTR($row,'suratperjanjian') ?>
						<?= Page::getDataTR($row,'tglditerimapegawai') ?>
						<?= Page::getDataTR($row,'tglsurat') ?>
						<?= Page::getDataTR($row,'tglaktif') ?>
						<?= Page::getDataTR($row,'deskripsi') ?>
					</table>
					</div>
				</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="idx" id="idx">
				<input type="hidden" id="file" name="file">
				<?	} ?>
			</form>
		</div>
	</div>
</div>
	<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
		<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
	</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	initTab(<?= !empty($_POST['idx']) ? $_POST['idx'] : '0'?>);
	
	loadKota();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
    
	$(".fileportal").click(function() {
		$("#pageform").attr("target","_blank");
        $("#act").val('download');
        $("#file").val($(this).attr("id"));
        $("#pageform").submit();
        $("#pageform").removeAttr("target");
    });	
});

// ajax ganti kota
function loadKota() {
	var param = new Array();
	param[0] = $("#kodepropinsi").val();
	param[1] = "<?= $r_kodekota ?>";
	
	
	$("#kelurahan").xautox({strpost: "f=acnamakelurahan", targetid: "idkelurahan", imgchkid: "imgkel", imgavail: true});
	$("#kelurahanktp").xautox({strpost: "f=acnamakelurahan", targetid: "idkelurahanktp", imgchkid: "imgkelktp", imgavail: true});
	$("#jurusan").xautox({strpost: "f=acjurusan", targetid: "kodejurusan", imgchkid: "imgjur", imgavail: true});
}

function goPrint() {
	window.open("<?= Route::navAddress('rep_pelamar') ?>"+"&key=<?= $r_key?>&format=html","_blank");
}
</script>
</body>
</html>
