<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug = true;
// 	if ( $_SERVER['REMOTE_ADDR']=='172.16.88.105' )
// 		$conn->debug= true;
//     else
//     	$conn->debug= false;
	
	// hak akses
	$a_auth = Modul::getFileAuth();

	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];

	$c_upload = $a_auth['canother']['U'];
	$c_status = $a_auth['canother']['S'];
	$c_ktm = $a_auth['canother']['K'];

	//untuk link beasiswa
	if ($c_insert)
	$link=true;
	$ubahNim = false;
	// include
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('laporanmhs'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('beasiswa'));
	require_once(Route::getModelPath('evaluasi'));
	require_once(Route::getModelPath('smu'));
	require_once(Route::getModelPath('penghargaan'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));

	// variabel request

	if(Akademik::isMhs()) {
		$p_limited = true;
		$r_self = 1;
		$display="none";
	}
	else {
		$p_limited = false;
		$r_self = (int)$_REQUEST['self'];
		$display="block";
	}

	if(empty($r_self)){
		if (isset ($_POST['npm']))
		$r_key=CStr::removeSpecial($_REQUEST['npm']);
		else
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	}

	else
		$r_key = Modul::getUserName();

	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	$user=Modul::getUserID();
	if($user=='PMB' and !empty($r_key)){
		$periode=$conn->GetOne("select substr(periodemasuk,4,1) from akademik.ms_mahasiswa where nim='$r_key'");
		if($periode=='2014')
			$c_edit=true;
		else
			$c_edit=false;
	}
	// properti halaman
	$p_title = 'Data Mahasiswa';
	$p_tbwidth = 900;
	$p_aktivitas = 'BIODATA';
	$p_listpage = Route::getListPage();
	$cetakKtm = "rep_ktm";
	//print_r($p_listpage);die();
	$p_foto = uForm::getPathImageMahasiswa($conn,$r_key);

	$p_model = mMahasiswa;

	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	$c_readlist = empty($a_authlist) ? false : true;
	$angkatan = Akademik::getAngkatanMahasiswa($conn,$r_key);
	$statusceklis = mLaporanMhs::statusceklis($conn, $r_key);
	// cek unit
	if(Akademik::cekUnit()) {
		$t_cek = $p_model::rideUnit($conn,$r_key);
		if(!$t_cek and $c_readlist) {
			$r_key = CStr::removeSpecial($_REQUEST['nimpilih']);
			if(!empty($r_key))
				$t_cek = $p_model::rideUnit($conn,$r_key);
		}
		if(!$t_cek)
			$r_key = '';
	}

	// cek data
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);

	// cek periode
	if(!Akademik::getIsiBiodataMhs()) {
		$c_edit = false;
		$c_delete = false;
	}

	if(Akademik::isAdmin()) {
		$ubahNim = true;
	}

	// struktur view
	$r_act = $_POST['act'];

	if(empty($r_key))
		$p_edit = false;
	else
		if(Akademik::isMhs()||Akademik::isHumas())
			$p_edit=true;
		else
			$p_edit = false;

			

	$a_propinsi = mCombo::propinsi($conn);
	$a_kota = mCombo::kota($conn);
	$a_keahlian = $p_model::tingkatKeahlian();
	$a_pendidikan = $p_model::pendidikan($conn);
	$a_pekerjaan = $p_model::pekerjaan($conn);
	$a_periode = mCombo::periode($conn);
	$list_smu=mSmu::getArray($conn);

	$a_input = array();
	if (Akademik::isAdmin()) {
		$a_input[] = array('kolom' => 'nim', 'label' => 'N I M', 'maxlength' => 100, 'size' => 15, 'readonly' => false);
		$a_input[] = array('kolom' => 'kodefakultas', 'label' => 'Fakultas', 'type' => 'S', 'option' => mCombo::fakultas($conn), 'add' => 'onchange="loadJurusan()"', 'readonly' => false);
		$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Prodi', 'type' => 'S', 'option' => mCombo::jurusan($conn), 'add' => 'onchange="loadBidangStudi()"', 'readonly' => false);
		//$a_input[] = array('kolom' => 'kodebs', 'label' => 'Bidang Studi', 'type' => 'S', 'option' => mCombo::bidangStudi($conn,), 'empty' => '-- Pilih Bidang Studi --', 'readonly' => $p_limited);
		$a_input[] = array('kolom' => 'semesterdaftar', 'label' => 'Periode Daftar', 'type' => 'S', 'option' => mCombo::semester(), 'readonly' => false);
		$a_input[] = array('kolom' => 'tahundaftar', 'label' => 'Periode Daftar', 'type' => 'S', 'option' => mCombo::tahun(), 'readonly' => false);
		$a_input[] = array('kolom' => 'thnkurikulum', 'label' => 'Kurikulum Mahasiswa', 'type' => 'S', 'option' => mCombo::kurikulum($conn), 'notnull' => !$p_limited, 'readonly' => false);
	}elseif(Akademik::isKeuangan()){
		$a_input[] = array('kolom' => 'nim', 'label' => 'N I M', 'maxlength' => 100, 'size' => 15, 'readonly' => true);
		$a_input[] = array('kolom' => 'kodefakultas', 'label' => 'Fakultas', 'type' => 'S', 'option' => mCombo::fakultas($conn), 'add' => 'onchange="loadJurusan()"', 'readonly' => true);
		$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Prodi', 'type' => 'S', 'option' => mCombo::jurusan($conn), 'add' => 'onchange="loadBidangStudi()"', 'readonly' => true);
		//$a_input[] = array('kolom' => 'kodebs', 'label' => 'Bidang Studi', 'type' => 'S', 'option' => mCombo::bidangStudi($conn,), 'empty' => '-- Pilih Bidang Studi --', 'readonly' => $p_limited);
		$a_input[] = array('kolom' => 'semesterdaftar', 'label' => 'Periode Daftar', 'type' => 'S', 'option' => mCombo::semester(), 'readonly' => false);
		$a_input[] = array('kolom' => 'tahundaftar', 'label' => 'Periode Daftar', 'type' => 'S', 'option' => mCombo::tahun(), 'readonly' => false);
		$a_input[] = array('kolom' => 'thnkurikulum', 'label' => 'Kurikulum Mahasiswa', 'type' => 'S', 'option' => mCombo::kurikulum($conn), 'notnull' => !$p_limited, 'readonly' => true);
	}else{
		$a_input[] = array('kolom' => 'nim', 'label' => 'N I M', 'maxlength' => 100, 'size' => 15, 'readonly' => true);
		$a_input[] = array('kolom' => 'kodefakultas', 'label' => 'Fakultas', 'type' => 'S', 'option' => mCombo::fakultas($conn), 'add' => 'onchange="loadJurusan()"', 'readonly' => true);
		$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Prodi', 'type' => 'S', 'option' => mCombo::jurusan($conn), 'add' => 'onchange="loadBidangStudi()"', 'readonly' => true);
		//$a_input[] = array('kolom' => 'kodebs', 'label' => 'Bidang Studi', 'type' => 'S', 'option' => mCombo::bidangStudi($conn,), 'empty' => '-- Pilih Bidang Studi --', 'readonly' => $p_limited);
		$a_input[] = array('kolom' => 'semesterdaftar', 'label' => 'Periode Daftar', 'type' => 'S', 'option' => mCombo::semester(), 'readonly' => true);
		$a_input[] = array('kolom' => 'tahundaftar', 'label' => 'Periode Daftar', 'type' => 'S', 'option' => mCombo::tahun(), 'readonly' => true);
		$a_input[] = array('kolom' => 'thnkurikulum', 'label' => 'Kurikulum Mahasiswa', 'type' => 'S', 'option' => mCombo::kurikulum($conn), 'notnull' => !$p_limited, 'readonly' => true);
	}
	
	if(!Akademik::isMhs()||!Akademik::isHumas())
		
	$a_input[] = array('kolom' => 'nama', 'label' => 'Nama Mahasiswa', 'maxlength' => 50, 'size' => 30, 'notnull' => !$p_limited, 'readonly' => $p_limited);
	
	$a_input[] = array('kolom' => 'noblankoijasah', 'label' => 'No Blanko Ijasah', 'size' => 50, 'maxlength' => 100);

	// agak kompleks :D
	$t_input = array('kolom' => 'statusmhs', 'label' => 'Status Mahasiswa', 'type' => 'S', 'option' => mCombo::statusMhs($conn), 'default' => 'A');

	if(!(empty($r_key) and $r_act == 'save')) {
		if(!$c_status or empty($r_key))
			$t_input['readonly'] = true;
	}

	$a_input[] = $t_input;

	// tidak ada inputan, tapi masukkan saja
	$a_input[] = array('kolom' => 'semestermhs', 'label' => 'Semester', 'readonly' => true);
	$a_input[] = array('kolom' => 'ipk', 'label' => 'IPK', 'readonly' => true);
	$a_input[] = array('kolom' => 'skslulus', 'label' => 'SKS Lulus', 'readonly' => true);
	$a_input[] = array('kolom' => 'ipslalu', 'label' => 'IPS Lalu', 'readonly' => true);
	$a_input[] = array('kolom' => 'batassks', 'label' => 'Batas SKS', 'readonly' => true);
	$a_input[] = array('kolom' => 'cuti', 'label' => 'Cuti', 'readonly' => true);


	$a_input[] = array('kolom' => 'sex', 'label' => 'Jenis Kelamin', 'type' => 'S', 'option' => $p_model::jenisKelamin());
	
	$a_input[] = array('kolom' => 'kodeagama', 'label' => 'Agama', 'type' => 'S', 'option' => $p_model::agama($conn));
	$a_input[] = array('kolom' => 'tmplahir', 'label' => 'Tmp dan Tanggal Lahir', 'maxlength' => 25, 'size' => 25, 'notnull' => true);
	$a_input[] = array('kolom' => 'tgllahir', 'label' => 'Tgl Lahir', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'goldarah', 'label' => 'Gol Darah', 'type' => 'S', 'option' => $p_model::golonganDarah(), 'empty' => true);
	$a_input[] = array('kolom' => 'alamat', 'label' => 'Jalan', 'maxlength' => 150, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'rt', 'label' => 'RT', 'type' => 'NP', 'maxlength' => 2, 'size' => 2, 'notnull' => true);
	$a_input[] = array('kolom' => 'rw', 'label' => 'RW', 'type' => 'NP', 'maxlength' => 2, 'size' => 2, 'notnull' => true);
	$a_input[] = array('kolom' => 'kelurahan', 'label' => 'Kelurahan', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kecamatan', 'label' => 'Kecamatan', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepropinsi', 'label' => 'Propinsi', 'type' => 'S', 'option' => $a_propinsi, 'add' => 'onchange="loadKota()"', 'empty' => '-- Pilih Propinsi --','notnull'=>true);
	$a_input[] = array('kolom' => 'kodekota', 'label' => 'Kota', 'type' => 'S', 'option' => $a_kota, 'empty' => '-- Pilih Kota --', 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepos', 'label' => 'Kode Pos', 'type' => 'NP', 'maxlength' => 5, 'size' => 5,'notnull'=>true);
	$a_input[] = array('kolom' => 'telp', 'label' => 'Telp', 'maxlength' => 15, 'size' => 15, 'notnull' => false);
	//$a_input[] = array('kolom' => 'telp2', 'label' => 'Telp 2', 'maxlength' => 15, 'size' => 15);
  if (Akademik::isAdmin() || Akademik::isKemahasiswaan() || Akademik::isBAA()) {
	 $a_input[] = array('kolom' => 'hp', 'label' => 'HP', 'maxlength' => 15, 'size' => 15);
	 $a_input[] = array('kolom' => 'tgllulus', 'label' => 'Tanggal Lulus', 'type'=>'D', 'readonly' => false);
   $a_input[] = array('kolom' => 'email', 'label' => 'Email', 'maxlength' => 50, 'size' => 30);
   $a_input[] = array('kolom' => 'nik', 'label' => 'No Induk Kependudukan', 'size' => 20, 'maxlength' => 50);
  }else{
  	$a_input[] = array('kolom' => 'tgllulus', 'label' => 'Tanggal Lulus', 'readonly' => true);
    $a_input[] = array('kolom' => 'hp', 'label' => 'HP', 'maxlength' => 15, 'size' => 15, 'readonly' => true);
    $a_input[] = array('kolom' => 'email', 'label' => 'Email', 'maxlength' => 50, 'size' => 30, 'readonly' => true);
    $a_input[] = array('kolom' => 'nik', 'label' => 'No Induk Kependudukan', 'size' => 20, 'maxlength' => 50, 'readonly' => true);
  }
	//$a_input[] = array('kolom' => 'hp2', 'label' => 'HP 2', 'maxlength' => 15, 'size' => 15);
	
	$a_input[] = array('kolom' => 'email2', 'label' => 'Email 2', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'statusnikah', 'label' => 'Status Nikah', 'type' => 'S', 'option' => $p_model::statusNikah($conn));
	$a_input[] = array('kolom' => 'kodewn', 'label' => 'Kewarganegaraan', 'type' => 'S', 'option' => $p_model::wargaNegara($conn), 'empty' => '-- Pilih Kewarganegaraan --');
	if (Akademik::isAdmin() || Akademik::isKeuangan() || Akademik::isWk2() || Akademik::isKemahasiswaan() ) {
		$a_input[] = array('kolom' => 'sistemkuliah', 'label' => 'Sistem', 'type' => 'S', 'option' => $p_model::sistemKuliah($conn), 'empty' => false, 'readonly' => false);
		$a_input[] = array('kolom' => 'jalurpenerimaan', 'label' => 'Jalur', 'type' => 'S', 'option' => $p_model::jalurPenerimaan($conn), 'empty' => false, 'default'=>'Umum', 'readonly' => false);
		$a_input[] = array('kolom' => 'gelombang', 'label' => 'Gelombang', 'type' => 'S', 'option' => $p_model::getGelombangDaftar($conn), 'readonly' => false, 'required' => true);
	}else{
		$a_input[] = array('kolom' => 'sistemkuliah', 'label' => 'Sistem', 'type' => 'S', 'option' => $p_model::sistemKuliah($conn), 'empty' => false, 'readonly' => true);
		$a_input[] = array('kolom' => 'jalurpenerimaan', 'label' => 'Jalur', 'type' => 'S', 'option' => $p_model::jalurPenerimaan($conn), 'empty' => false, 'default'=>'Umum', 'readonly' => true);
		$a_input[] = array('kolom' => 'gelombang', 'label' => 'Gelombang', 'type' => 'S', 'option' => $p_model::getGelombangDaftar($conn), 'readonly' => true, 'required' => true);
	}
	
	$a_input[] = array('kolom' => 'mhstransfer', 'label' => 'Pindahan', 'type'=>'S', 'option'=> $p_model::getPindahan(),'empty'=>false,'readonly'=>$p_limited);
	$a_input[] = array('kolom' => 'gelombang', 'label' => 'Gelombang', 'type' => 'S', 'option' => $p_model::getGelombangDaftar($conn), 'readonly' => $p_limited, 'required' => true);

	$a_input[] = array('kolom' => 'nipdosenwali', 'label' => 'Dosen Wali', 'type' => 'S', 'option' => mCombo::dosen($conn), 'readonly' => true);
	//$a_input[] = array('kolom' => 'bhsarab', 'label' => 'Bahasa Arab', 'type' => 'S', 'option' => $a_keahlian, 'empty' => true);
	$a_input[] = array('kolom' => 'bhsinggris', 'label' => 'Bahasa Inggris', 'type' => 'S', 'option' => $a_keahlian, 'empty' => true);
	$a_input[] = array('kolom' => 'pengkomp', 'label' => 'Komputer', 'type' => 'S', 'option' => $a_keahlian, 'empty' => true);

	$a_input[] = array('kolom' => 'asalsmu', 'label' => 'Nama Sekolah', 'maxlength' => 50, 'size' => 50);
	$a_input[] = array('kolom' => 'alamatsmu', 'label' => 'Alamat Sekolah', 'maxlength' => 60, 'size' => 50,'notnull'=>true);
	$a_input[] = array('kolom' => 'telpsmu', 'label' => 'Telp Sekolah', 'maxlength' => 15, 'size' => 15,'notnull'=>true);
	//$a_input[] = array('kolom' => 'kodepropinsismu', 'label' => 'Propinsi', 'type' => 'S', 'option' => $a_propinsi, 'add' => 'onchange="loadKotaSMU()"', 'empty' => '-- Pilih Propinsi --','notnull'=>true);
	//$a_input[] = array('kolom' => 'kodekotasmu', 'label' => 'Kota', 'type' => 'S', 'option' => $a_kota, 'empty' => true, 'empty' => '-- Pilih Kota --','notnull'=>true);
	$a_input[] = array('kolom' => 'nemsmu', 'label' => 'NEM Kelulusan', 'maxlength' => 6, 'size' => 6);
	$a_input[] = array('kolom' => 'noijasahsmu', 'label' => 'No. Ijasah', 'maxlength' => 50, 'size' => 50,'notnull'=>true);
	$a_input[] = array('kolom' => 'nisn', 'label' => 'NISN', 'maxlength' => 10, 'size' => 10);

	$a_input[] = array('kolom' => 'ptasal', 'label' => 'Universitas Asal', 'maxlength' => 50, 'size' => 40);
	$a_input[] = array('kolom' => 'kodepropinsipt', 'label' => 'Propinsi', 'type' => 'S', 'option' => $a_propinsi, 'add' => 'onchange="loadKotaPT()"', 'empty' => '-- Pilih Propinsi --');
	$a_input[] = array('kolom' => 'kodekotapt', 'label' => 'Kota', 'type' => 'S', 'option' => $a_kota, 'empty' => true, 'empty' => '-- Pilih Kota --');
	$a_input[] = array('kolom' => 'ptjurusan', 'label' => 'Prodi', 'maxlength' => 50, 'size' => 40);
	$a_input[] = array('kolom' => 'ptthnlulus', 'label' => 'Tahun Lulus', 'maxlength' => 4, 'size' => 4);
	$a_input[] = array('kolom' => 'ptipk', 'label' => 'IPK', 'maxlength' => 4, 'size' => 4);
	$a_input[] = array('kolom' => 'nimlama', 'label' => 'NIM', 'maxlength' => 20, 'size' => 20);

	$a_input[] = array('kolom' => 'pernahponpes', 'label' => 'Pernah Belajar', 'type' => 'R', 'option' => $p_model::pernahPonpes());
	$a_input[] = array('kolom' => 'namaponpes', 'label' => 'Nama Pesantren', 'maxlength' => 50, 'size' => 50);
	$a_input[] = array('kolom' => 'alamatponpes', 'label' => 'Alamat Pesantren', 'maxlength' => 60, 'size' => 50);
	$a_input[] = array('kolom' => 'kodepropinsiponpes', 'label' => 'Propinsi', 'type' => 'S', 'option' => $a_propinsi, 'add' => 'onchange="loadKotaPonpes()"', 'empty' => '-- Pilih Propinsi --');
	$a_input[] = array('kolom' => 'kodekotaponpes', 'label' => 'Kota', 'type' => 'S', 'option' => $a_kota, 'empty' => true, 'empty' => '-- Pilih Kota --');
	$a_input[] = array('kolom' => 'lamaponpes', 'label' => 'Lama Belajar', 'maxlength' => 5, 'size' => 5);

	$a_input[] = array('kolom' => 'namaayah', 'label' => 'Nama Ayah', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'namaibu', 'label' => 'Nama Ibu', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'alamatortu', 'label' => 'Jalan', 'maxlength' => 150, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'rtortu', 'label' => 'RT', 'type' => 'NP', 'maxlength' => 2, 'size' => 2, 'notnull' => true);
	$a_input[] = array('kolom' => 'rwortu', 'label' => 'RW', 'type' => 'NP', 'maxlength' => 2, 'size' => 2, 'notnull' => true);
	$a_input[] = array('kolom' => 'kelurahanortu', 'label' => 'Kelurahan', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kecamatanortu', 'label' => 'Kecamatan', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepropinsiortu', 'label' => 'Propinsi', 'type' => 'S', 'option' => $a_propinsi, 'add' => 'onchange="loadKotaOrtu()"', 'empty' => '-- Pilih Propinsi --','notnull'=>true);
	$a_input[] = array('kolom' => 'kodekotaortu', 'label' => 'Kota', 'type' => 'S', 'option' => $a_kota, 'empty' => '-- Pilih Kota --', 'notnull' => true);
	$a_input[] = array('kolom' => 'kodeposortu', 'label' => 'Kode Pos', 'type' => 'NP', 'maxlength' => 5, 'size' => 5,'notnull'=>true);
	$a_input[] = array('kolom' => 'telportu', 'label' => 'Telp Ortu', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodependapatanortu', 'label' => 'Pendapatan', 'type' => 'S', 'option' => $p_model::pendapatan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodependidikanayah', 'label' => 'Pendidikan Ayah', 'type' => 'S', 'option' => $a_pendidikan, 'empty' => true, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodependidikanibu', 'label' => 'Pendidikan Ibu', 'type' => 'S', 'option' => $a_pendidikan, 'empty' => true, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepekerjaanayah', 'label' => 'Pekerjaan Ayah', 'type' => 'S', 'option' => $a_pekerjaan, 'empty' => true, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepekerjaanibu', 'label' => 'Pekerjaan Ibu', 'type' => 'S', 'option' => $a_pekerjaan, 'empty' => true, 'notnull' => true);
	$a_input[] = array('kolom' => 'namacpdarurat', 'label' => 'Nama Kontak', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'telpcpdarurat', 'label' => 'Telp Kontak', 'maxlength' => 30, 'size' => 15, 'notnull' => true);

	$a_input[] = array('kolom' => 'statuskerja', 'label' => 'Status Kerja', 'type' => 'S', 'option' => $p_model::statusKerja());
	$a_input[] = array('kolom' => 'pekerjaan', 'label' => 'Pekerjaan', 'maxlength' => 30, 'size' => 30);
	$a_input[] = array('kolom' => 'namaperusahaan', 'label' => 'Perusahaan', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'jenisinstansi', 'label' => 'Jenis Instansi', 'type' => 'S', 'option' => $p_model::jenisInstansi(), 'empty' => true);
	$a_input[] = array('kolom' => 'alamatperusahaan', 'label' => 'Alamat Perusahaan', 'maxlength' => 60, 'size' => 50);
	$a_input[] = array('kolom' => 'kodepropinsiperusahaan', 'label' => 'Propinsi', 'type' => 'S', 'option' => $a_propinsi, 'add' => 'onchange="loadKotaPerusahaan()"', 'empty' => '-- Pilih Propinsi --');
	$a_input[] = array('kolom' => 'kodekotaperusahaan', 'label' => 'Kota', 'type' => 'S', 'option' => $a_kota, 'empty' => true, 'empty' => '-- Pilih Kota --');
	$a_input[] = array('kolom' => 'telpperusahaan', 'label' => 'Telp Perusahaan', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'jabatan', 'label' => 'Jabatan', 'maxlength' => 30, 'size' => 30);
	$a_input[] = array('kolom' => 'penanggungdana', 'label' => 'Penanggung Dana', 'maxlength' => 10, 'size' => 30);


	// upload berkas
	$a_input[] = array('kolom' => 'filektp', 'label' => 'File KTP','type' => 'U', 'uptype' => 'ktp', 'size' => 40,'maxsize'=>'3','arrtype'=>array('jpg','jpeg','png','doc','pdf','ppt','xls','rar','zip'));
	$a_input[] = array('kolom' => 'fileijasah', 'label' => 'File Ijasah', 'type' => 'U', 'uptype' => 'ijasah', 'size' => 40,'maxsize'=>'3','arrtype'=>array('jpg','jpeg','png','doc','pdf','ppt','xls','rar','zip'));
	$a_input[] = array('kolom' => 'filekk', 'label' => 'File KK', 'type' => 'U', 'uptype' => 'kk', 'size' => 40,'maxsize'=>'3','arrtype'=>array('jpg','jpeg','png','doc','pdf','ppt','xls','rar','zip'));

	// mengambil data riwayat
	$a_detail = array();

	$t_detail = array();
	$t_detail[] = array('kolom' => 'kodesumber', 'label' => 'Sumber', 'type' => 'S', 'option' => mSumberBeasiswa::getArray($conn));
	$t_detail[] = array('kolom' => 'periodeawal', 'label' => 'Mulai', 'type' => 'S', 'option' => $a_periode);
	$t_detail[] = array('kolom' => 'periodeakhir', 'label' => 'Selesai', 'type' => 'S', 'option' => $a_periode);
	$t_detail[] = array('kolom' => 'jumlahperperiode', 'label' => 'Jumlah per Periode', 'type' => 'N', 'size' => 10, 'maxlength' => 10);

	$a_detail['beasiswa'] = array('key' => $p_model::getDetailInfo('beasiswa','key'), 'data' => $t_detail);

	/*
	 * Dipisah dengan ajax - adhi
	 *
	$t_detail = array();
	$t_detail[] = array('kolom' => 'tglpenghargaan', 'label' => 'Tanggal', 'type' => 'D');
	$t_d etail[] = array('kolom' => 'namapenghargaan', 'label' => 'Nama Penghargaan', 'size' => 25, 'maxlength' => 255);
	$t_detail[] = array('kolom' => 'namapenghargaanenglish', 'label' => 'Nama Penghargaan English', 'size' => 25, 'maxlength' => 255);
	$t_detail[] = array('kolom' => 'isvalid', 'label' => 'Valid?', 'type' => 'C', 'option' => array('-1' => ''),'readonly'=>$p_limited);

	$a_detail['penghargaan'] = array('key' => $p_model::getDetailInfo('penghargaan','key'), 'data' => $t_detail);
	*/
	$t_detail = array();
	$t_detail[] = array('kolom' => 'periodeawal', 'label' => 'Mulai', 'type' => 'S', 'option' => $a_periode);
	$t_detail[] = array('kolom' => 'periodeakhir', 'label' => 'Selesai', 'type' => 'S', 'option' => $a_periode);
	$t_detail[] = array('kolom' => 'alasanskors', 'label' => 'Alasan', 'type' => 'A', 'rows' => 3, 'cols' => 15, 'maxlength' => 255);
	$t_detail[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 3, 'cols' => 15, 'maxlength' => 4000);

	$a_detail['skors'] = array('key' => $p_model::getDetailInfo('skors','key'), 'data' => $t_detail);
	
	


if ( $_SERVER['REMOTE_ADDR']=='172.16.88.105' ){
	$conn->debug= true;
	require_once('/home/ngadimin/Debug.php');
	Zend_Debug::dump($p_foto,'$p_foto',true);
	Zend_Debug::dump($r_act,'$r_act',true);
}
else
	$conn->debug= false;

	// ada aksi
	if($r_act == 'save' and $c_edit) {
		$conn->BeginTrans();

		list($post,$record) = uForm::getPostRecord($a_input,$_POST);

		$periodedaftar=$record['tahundaftar'].$record['semesterdaftar'];
		if(!empty($periodedaftar))
			$record['periodemasuk'] = $periodedaftar;
		
		// cek unit
		if(Akademik::cekUnit())
			$record['kodeunit'] = $p_model::switchUnit($conn,$record['kodeunit']);
		if(!empty($record['asalsmu']) or $record['asalsmu']!='null'){
// 			$datasmu=mSmu::getData($conn,$record['asalsmu']);
// 			$record['kodepropinsismu']=CStr::cStrNull(substr($datasmu['kodekota'],0,2));
// 			$record['kodekotasmu']=CStr::cStrNull($datasmu['kodekota']);
			list($p_posterr,$p_postmsg)=array(false,'');
		}else{
			list($p_posterr,$p_postmsg)=array(true,'Pastikan pilihan SMA benar, jika SMA anda tidak tersedia silahkan hubungi admin');
		}

		// simpan data (+ upload file)
		if(empty($p_posterr)) {
			if(empty($r_key)) {
				$record['statusmhs'] = 'A';
				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
			}else
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		}

		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);

		if(!$p_posterr) unset($post);
	}

	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);

		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'insertdet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);

		foreach($a_detail[$r_detail]['data'] as $t_detail) {
			$t_name = CStr::cEmChg($t_detail['nameid'],$t_detail['kolom']);
			$a_value[$t_name] = $_POST[$r_detail.'_'.$t_name];
		}

		list(,$record) = uForm::getPostRecord($a_detail[$r_detail]['data'],$a_value);
		$record['nim'] = $r_key;

		list($p_posterr,$p_postmsg) = $p_model::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail);
	}
	else if($r_act == 'deletedet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkey = CStr::removeSpecial($_POST['subkey']);

		list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkey,$r_detail);

	}
	else if($r_act == 'savefoto' and $c_upload) {
		if(empty($_FILES['foto']['error'])) {
			$err = Page::createFoto($_FILES['foto']['tmp_name'],$p_foto,400,600);

// 	if ( $_SERVER['REMOTE_ADDR']=='172.16.88.105' )
// 		$conn->debug= true;
//     else
//     	$conn->debug= false;

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

		uForm::reloadImageMahasiswa($conn,$r_key,$msg);
	}
	else if($r_act == 'deletefoto' and $c_upload) {
		@unlink($p_foto);

		uForm::reloadImageMahasiswa($conn,$r_key);
	}
	else if($r_act == 'uploadpenghargaan' and $c_edit) {
		$t_file = $_FILES['filepenghargaan'];
		if($t_file['error'] == UPLOAD_ERR_OK) {
			$t_key = (int)$_POST['subkey'].'|'.$r_key;

			$conn->BeginTrans();

			// update nama file
			$record = array();
			$record['filesertifikat'] = $t_file['name'];

			$err = mPenghargaan::updateRecord($conn,$record,$t_key);
			$ok = Query::isOK($err);

			// upload
			if($ok)
				$ok = Route::uploadFile('penghargaan',$t_key,$t_file['tmp_name']);

			$conn->CommitTrans($ok);

			if(empty($ok))
				@unlink($t_file['tmp_name']);
		}
		else
			$ok = false;

		$p_posterr = ($ok ? false : true);
		$p_postmsg = 'Upload file sertifikat penghargaan mahasiswa '.($ok ? 'berhasil' : 'gagal');
		$p_opentab = 6;
	}
	else if($r_act == 'deletefilepenghargaan' and $c_edit) {
		$t_key = (int)$_POST['subkey'].'|'.$r_key;

		$conn->BeginTrans();

		// update nama file
		$record = array();
		$record['filesertifikat'] = null;

		$err = mPenghargaan::updateRecord($conn,$record,$t_key);
		$ok = Query::isOK($err);

		if($ok)
			$ok = unlink(Route::getUploadedFile('penghargaan',$t_key));

		$conn->CommitTrans($ok);

		$p_posterr = ($ok ? false : true);
		$p_postmsg = 'Hapus file sertifikat penghargaan mahasiswa '.($ok ? 'berhasil' : 'gagal');
		$p_opentab = 6;
	}
	else if($r_act == 'deletefile' and $c_edit)
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key,$_POST['subkey'],substr($_POST['subkey'],4));

	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);

	if(!empty($r_key)) {

		$r_nim = Page::getDataValue($row,'nim');

		$a_tagihan = $p_model::getTagihanMhs($conn,$r_nim);
		//die($r_key);
		if(empty($r_nim) and !empty($r_key)) {
			$p_posterr = true;
			$p_fatalerr = true;
			$p_postmsg = 'User ini Tidak Mempunyai Profile<br>Termasuk Login Khusus yang Tidak Terdaftar Di Tabel Mahasiswa';
		}
		else {
			$r_kodeunit = Page::getDataValue($row,'kodeunit');
			//$r_kodebs = Page::getDataValue($row,'kodebs');
			$r_periodemasuk = Page::getDataValue($row,'tahundaftar').Page::getDataValue($row,'semesterdaftar');
			$r_semester = Page::getDataValue($row,'semestermhs');

			$r_kodekota = Page::getDataValue($row,'kodekota');
			$r_kodekotaortu = Page::getDataValue($row,'kodekotaortu');
			$r_kodekotaperusahaan = Page::getDataValue($row,'kodekotaperusahaan');
			$r_kodekotaponpes = Page::getDataValue($row,'kodekotaponpes');
			$r_kodekotasmu = Page::getDataValue($row,'kodekotasmu');
			$r_kodekotapt = Page::getDataValue($row,'kodekotapt');
			$r_kurikulum = Page::getDataValue($row,'thnkurikulum');
			$r_asalsmu = Page::getDataValue($row,'asalsmu');
			$r_smu=$list_smu[$r_asalsmu];
			$v_smu=!empty($r_smu)?$r_asalsmu.' - '.$r_smu:'';
			// cek evaluasi
			if(empty($r_periodemasuk))
				$r_periodemasuk = $p_model::getPeriodeMasukNIM($r_key);
			if (empty ($r_kurikulum))
			$r_kurikulum = $p_model::getKurikulum($conn,$r_periodemasuk,$r_kodeunit);

			$r_progpend = mUnit::getProgramPendidikan($conn,$r_kodeunit);
			//echo $r_kurikulum.'kurikulum zonk';
			$r_ipk = Page::getDataValue($row,'ipk');
			$r_skslulus = Page::getDataValue($row,'skslulus');

			$a_evaluasi = mEvaluasi::getDataSemester($conn,$r_kurikulum,$r_progpend,$r_semester);

			//die('ok');
			$rowd = array();
			$rowd += $p_model::getBeasiswa($conn,$r_key,'beasiswa',$post);
			$rowd += $p_model::getPenghargaan($conn,$r_key,'penghargaan',$post);
			$rowd += $p_model::getSkors($conn,$r_key,'skors',$post);
			$jenjang=$p_model::getJenjang($conn,$r_kodeunit);
		}
	}
	if(isset($_POST['kodesmu'])){
		$v_smu=$_POST['kodesmu'];
	}

	//print_r($list_smu);
?>

<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
	<link href="style/modal.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<script type="text/javascript" src="scripts/forreport.js"></script>
	<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
	<style>
		#table_evaluasi { border-collapse:collapse }
		#table_evaluasi .td_ev { border:1px solid #666 }
		#imgfoto{
			width: 50%;
		}
	</style>
	
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
		<?php if (!empty ($_GET['key'])) require_once('inc_headermahasiswa.php'); ?>
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<?	/**************/
					/* JUDUL LIST */
					/**************/

					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	}

					/*****************/
					/* TOMBOL-TOMBOL */
					/*****************/

					if(empty($p_fatalerr))
						require_once('inc_databutton.php');

					if(!empty($p_postmsg)) { ?>
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
						<?	if($c_readlist) { ?>
							<div class="right">
								<div class="addButton" style="padding:0 0 3px 7px" title="Tampilkan data mahasiswa" onClick="goPick()">
									<img src="images/search.png" style="float:none;padding:0px 0px 2px;margin-right:6px">
								</div>
							</div>
							<div class="right" style="padding-top:7px">

								<?= UI::createTextBox('mahasiswa','','ControlStyle',50,40, true, '', 'Cari Mahasiswa') ?>
								<input type="hidden" id="nimpilih" name="nimpilih">&nbsp;
							</div>
						<?	} ?>
						</div>

					</header>
					<?	/********/
						/* DATA */
						/********/
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<?
						$a_required = array();
						foreach($a_input as $t_input) {
							if($t_input['notnull'] === true)
								$a_required[] = CStr::cEmChg($t_input['nameid'],$t_input['kolom']);
						}
						$a_required[]='kodesmu';

					?>
					
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nim') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'nim') ?></td>
							<?php /* <td align="center" valign="top" rowspan="<?= 8+(empty($a_evaluasi) ? 0 : 1) ?>">
							
							</td> */ ?>
							<td rowspan="9" align="center">
								<?	if(!empty($r_key)) { ?>
								<?= uForm::getImageMahasiswa($conn,$r_key,$c_upload) ?>
								<?php if($c_upload){ ?>
								<br><span>klik gambar untuk upload/hapus Photo</span>
								<br>Pas Foto formal<br>Ukuran 4x6 (berwarna)
								<br>
								<br>
								&nbsp;
									<?php if ($c_ktm) { ?>
										<input type="button" style="padding:4px" value="Capture" class="ControlStyle" onClick="popup('index.php?page=capture_cam&nim=<?=$r_key?>&angkatan=<?=$angkatan?>',420,670);">
										<input type="button" value="Cetak KTM" class="ControlStyle" onclick="goCetakktm(this)" style="padding:4px">
										
									<?php } ?>
								<?php } ?>

							<?	} ?>
							</td>
						</tr>
						<?= Page::getDataTR($row,'nama') ?>
						<?= Page::getDataTR($row,'nipdosenwali') ?>
						<?= Page::getDataTR($row,'kodefakultas') ?>
						<?= Page::getDataTR($row,'kodeunit') ?>
						<?= Page::getDataTR($row,'semesterdaftar,tahundaftar') ?>
						<?= Akademik::isMhs() ? '' : Page::getDataTR($row,'thnkurikulum') ?>
						<?= Page::getDataTR($row,'statusmhs') ?>
						<?= Page::getDataTR($row,'tgllulus') ?>
						<?= Page::getDataTR($row,'noblankoijasah') ?>
						<!--tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Info Akademik <?=$jenjang?></td>
							<td class="RightColumnBG">
								<table>
									<tr>
										<td><?= Page::getDataLabel($row,'semestermhs') ?></td>
										<td>: <?= Page::getDataInput($row,'semestermhs') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'batassks') ?></td>
										<td>: <?= Page::getDataInput($row,'batassks') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'skslulus') ?></td>
										<td>: <?= Page::getDataInput($row,'skslulus') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'ipk') ?></td>
										<td>: <?= Page::getDataInput($row,'ipk') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'ipslalu') ?></td>
										<td>: <?= Page::getDataInput($row,'ipslalu') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'cuti') ?></td>
										<td>: <?= Page::getDataInput($row,'cuti') ?></td>
									</tr>
								</table>
								<?/*= Page::getDataLabel($row,'semestermhs') ?> : <?= Page::getDataInput($row,'semestermhs') ?>,<br>
								<?= Page::getDataLabel($row,'ipk') ?> : <?= Page::getDataInput($row,'ipk') ?>,
								<?= Page::getDataLabel($row,'skslulus') ?> : <?= Page::getDataInput($row,'skslulus') ?><br>
								<?= Page::getDataLabel($row,'ipslalu') ?> : <?= Page::getDataInput($row,'ipslalu') ?>,<br>
								<?= Page::getDataLabel($row,'batassks') ?> : <?= Page::getDataInput($row,'batassks') ?>,<br>
								<?= Page::getDataLabel($row,'cuti') ?> : <?= Page::getDataInput($row,'cuti') */?>
							</td>
						</tr-->
						<? //if(!empty($a_evaluasi)) { ?>
						<? if(!Akademik::isMhs()) { ?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">
								Evaluasi <?= $a_evaluasi['evaluasike'] ?><br>
								(Semester <?= $r_semester ?>)
							</td>
							<td class="RightColumnBG">
					<table id="table_evaluasi" border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td width="60">&nbsp;</td>
							<td width="60" align="center" class="td_ev">IPK</td>
							<td width="60" align="center" class="td_ev">SKS Lulus</td>
						</tr>
						<tr>
							<td class="td_ev">Minimal</td>
							<td align="center" class="td_ev"><?= $a_evaluasi['batasip'] ?></td>
							<td align="center" class="td_ev"><?= $a_evaluasi['batassks'] ?></td>
						</tr>
						<tr>
							<td class="td_ev">Tercapai</td>
							<td align="center" class="td_ev <?= $r_ipk >= $a_evaluasi['batasip'] ? 'GreenBG' : 'YellowBG' ?>"><?= $r_ipk ?></td>
							<td align="center" class="td_ev <?= $r_skslulus >= $a_evaluasi['batassks'] ? 'GreenBG' : 'YellowBG' ?>"><?= $r_skslulus ?></td>
						</tr>
					</table>
							</td>
							<?php
								if(!Akademik::isMhs()){
									if ($statusceklis == "1") {
										$warna = "background-color: chartreuse;";
										$cetak =  "Sudah Cetak KTM";
									}else{
										$warna = "background-color: gold;";
										$cetak =  "Belum Cetak KTM";
									}
								}else{
									echo "nbsp;";
								}

							?>
							<td align="center" style="<?= $warna ?>">
								
								<b><?= $cetak ?></b>
											
							</td>
						</tr>

						<? } ?>
					</table>
					</div>
				</center>
				<br>
				<center>

				<font size="5" color="red">Pastikan tab BIODATA, PENDIDIKAN dan KELUARGA sudah terisi!</font>
				<div class="tabs" style="width:<?= $p_tbwidth ?>px">
					<ul>
						<li><a id="tablink" href="javascript:void(0)">Biodata</a></li>
						<li><a id="tablink" href="javascript:void(0)">Akademik</a></li>
						<li><a id="tablink" href="javascript:void(0)">Pendidikan</a></li>
						<li><a id="tablink" href="javascript:void(0)">Keluarga</a></li>
						<li><a id="tablink" href="javascript:void(0)">Pekerjaan</a></li>
						<? if(!empty($r_key)) { ?>
						<li><a id="tablink" href="javascript:void(0)">Skors</a></li>
						<li><a id="tablink" href="javascript:void(0)">Riwayat Keuangan</a></li>
						<? } ?>
						<li><a id="tablink" href="javascript:void(0)">Berkas</a></li>
					</ul>

					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'nik') ?>
						<?= Page::getDataTR($row,'sex') ?>
						<?= Page::getDataTR($row,'kodeagama') ?>
						<?= Page::getDataTR($row,'goldarah') ?>
						<?= Page::getDataTR($row,'tmplahir,tgllahir',', ') ?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Alamat</td>
							<td class="RightColumnBG">
					<table>
						<tr>
							<td width="80"><?= Page::getDataLabel($row,'alamat') ?></td>
							<td width="5">:</td>
							<td><?= Page::getDataInput($row,'alamat') ?></td>
						</tr>
						<tr>
							<td><?= Page::getDataLabel($row,'rt') ?>/<?= Page::getDataLabel($row,'rw') ?></td>
							<td>:</td>
							<td><?= Page::getDataInput($row,'rt') ?>/<?= Page::getDataInput($row,'rw') ?></td>
						</tr>
						<tr>
							<td><?= Page::getDataLabel($row,'kelurahan') ?></td>
							<td>:</td>
							<td><?= Page::getDataInput($row,'kelurahan') ?></td>
						</tr>
						<tr>
							<td><?= Page::getDataLabel($row,'kecamatan') ?></td>
							<td>:</td>
							<td><?= Page::getDataInput($row,'kecamatan') ?></td>
						</tr>
					</table>
							</td>
						</tr>
						<?= Page::getDataTR($row,'kodepropinsi') ?>
						<?= Page::getDataTR($row,'kodekota') ?>
						<?= Page::getDataTR($row,'kodepos') ?>
						<?= Page::getDataTR($row,'telp') ?>
						<?= Page::getDataTR($row,'hp') ?>
						<?= Page::getDataTR($row,'email') ?>
						<?= Page::getDataTR($row,'statusnikah') ?>
						<?= Page::getDataTR($row,'kodewn') ?>
					</table>
					</div>

					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'sistemkuliah') ?>
						<?= Page::getDataTR($row,'jalurpenerimaan') ?>
						<?= Page::getDataTR($row,'gelombang') ?>
						<?= Page::getDataTR($row,'mhstransfer')?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Kemampuan</td>
							<td class="RightColumnBG">
					<table>
						<!--tr>
							<td width="90"><?= Page::getDataLabel($row,'bhsarab') ?></td>
							<td width="5">:</td>
							<td><?= Page::getDataInput($row,'bhsarab') ?></td>
						</tr-->
						<tr>
							<td><?= Page::getDataLabel($row,'bhsinggris') ?></td>
							<td>:</td>
							<td><?= Page::getDataInput($row,'bhsinggris') ?></td>
						</tr>
						<tr>
							<td><?= Page::getDataLabel($row,'pengkomp') ?></td>
							<td>:</td>
							<td><?= Page::getDataInput($row,'pengkomp') ?></td>
						</tr>
					</table>
							</td>
						</tr>
					</table>
					</div>

					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td colspan="2" class="DataBG">Asal Sekolah</td>
						</tr>
						<?= Page::getDataTR($row,'asalsmu') ?>
            <!--emanshp>
						<tr>
							<td class="LeftColumnBG">Nama Sekolah</td>
							<td class="RightColumnBG">
								<span id="show"><?=$r_smu?></span>
								<span id="edit" style="display:none">
								<?= UI::createTextBox('kodesmu',$v_smu,'ControlStyle',30,30,true, '', 'Cari Berdasarkan Nama') ?>
								<input type="hidden" id="asalsmu" name="asalsmu" value="<?= $r_asalsmu ?>">
								</span>
<? if($p_limited){?><br>(Note: Jika pilihan sekolah tidak ada, ketikkan "KOSONG" dan pilih "20249 - SMA KOSONG")<? }?>
							</td>
						</tr> -->
						<?= Page::getDataTR($row,'alamatsmu') ?>
						<?//= Page::getDataTR($row,'kodepropinsismu') ?>
						<?//= Page::getDataTR($row,'kodekotasmu') ?>
						<?= Page::getDataTR($row,'telpsmu') ?>
						<?= Page::getDataTR($row,'nemsmu') ?>
						<?= Page::getDataTR($row,'nisn') ?>
						<?= Page::getDataTR($row,'noijasahsmu') ?>
						<tr>
							<td colspan="2" class="DataBG">Asal Perguruan Tinggi</td>
						</tr>
						<?= Page::getDataTR($row,'ptasal') ?>
						<?= Page::getDataTR($row,'kodepropinsipt') ?>
						<?= Page::getDataTR($row,'kodekotapt') ?>
						<?= Page::getDataTR($row,'ptjurusan') ?>
						<?= Page::getDataTR($row,'nimlama') ?>
						<?= Page::getDataTR($row,'ptthnlulus') ?>
						<?= Page::getDataTR($row,'ptipk') ?>
						<tr>
<!--
							<td colspan="2" class="DataBG">Pondok Pesantren</td>
						</tr>
						<?= Page::getDataTR($row,'pernahponpes') ?>
						<?= Page::getDataTR($row,'namaponpes') ?>
						<?= Page::getDataTR($row,'alamatponpes') ?>
						<?= Page::getDataTR($row,'kodepropinsiponpes') ?>
						<?= Page::getDataTR($row,'kodekotaponpes') ?>
						<?= Page::getDataTR($row,'lamaponpes') ?>
-->
					</table>
					</div>

					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td colspan="2" class="DataBG">Orang Tua</td>
						</tr>
						<?= Page::getDataTR($row,'namaayah') ?>
						<?= Page::getDataTR($row,'namaibu') ?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Alamat</td>
							<td class="RightColumnBG">
					<table>
						<tr>
							<td width="80"><?= Page::getDataLabel($row,'alamatortu') ?></td>
							<td width="5">:</td>
							<td><?= Page::getDataInput($row,'alamatortu') ?></td>
						</tr>
						<tr>
							<td><?= Page::getDataLabel($row,'rtortu') ?>/<?= Page::getDataLabel($row,'rwortu') ?></td>
							<td>:</td>
							<td><?= Page::getDataInput($row,'rtortu') ?>/<?= Page::getDataInput($row,'rwortu') ?></td>
						</tr>
						<tr>
							<td><?= Page::getDataLabel($row,'kelurahanortu') ?></td>
							<td>:</td>
							<td><?= Page::getDataInput($row,'kelurahanortu') ?></td>
						</tr>
						<tr>
							<td><?= Page::getDataLabel($row,'kecamatanortu') ?></td>
							<td>:</td>
							<td><?= Page::getDataInput($row,'kecamatanortu') ?></td>
						</tr>
					</table>
							</td>
						</tr>
						<?= Page::getDataTR($row,'kodepropinsiortu') ?>
						<?= Page::getDataTR($row,'kodekotaortu') ?>
						<?= Page::getDataTR($row,'kodeposortu') ?>
						<?= Page::getDataTR($row,'telportu') ?>

						<?= Page::getDataTR($row,'kodependidikanayah') ?>
						<?= Page::getDataTR($row,'kodependidikanibu') ?>
						<?= Page::getDataTR($row,'kodepekerjaanayah') ?>
						<?= Page::getDataTR($row,'kodepekerjaanibu') ?>
<!--
						<?= Page::getDataTR($row,'kodependapatanortu') ?>
-->
						<tr>
							<td colspan="2" class="DataBG">Kontak yang Bisa Dihubungi Saat Darurat</td>
						</tr>
						<?= Page::getDataTR($row,'namacpdarurat') ?>
						<?= Page::getDataTR($row,'telpcpdarurat') ?>
					</table>

					<div class="data_kontakteman">
						<?php require_once('data_kontakteman.php');?>
					</div>

					</div>

					<div id="items">
						<table cellpadding="4" cellspacing="2" align="center">
							<?= Page::getDataTR($row,'statuskerja') ?>
							<?= Page::getDataTR($row,'pekerjaan') ?>
							<?= Page::getDataTR($row,'namaperusahaan') ?>
							<?= Page::getDataTR($row,'jenisinstansi') ?>
							<?= Page::getDataTR($row,'alamatperusahaan') ?>
							<?= Page::getDataTR($row,'kodepropinsiperusahaan') ?>
							<?= Page::getDataTR($row,'kodekotaperusahaan') ?>
							<?= Page::getDataTR($row,'telpperusahaan') ?>
							<?= Page::getDataTR($row,'jabatan') ?>
							<?= Page::getDataTR($row,'penanggungdana') ?>
						</table>
					</div>

					<? if(!empty($r_key)) { ?>
					



					<div id="items">
					<? //Page::getDetailTable($rowd,$a_detail,'skors','Skors',true,!$p_limited) ?>
					</div>

					<div id="items">
						<table width="100%" cellspacing="2" cellpadding="4" align="center" class="GridStyle">
							<tr>
								<td class="DataBG" colspan="8">Riwayat Keuangan Mahasiswa</td>
							</tr>
							<tr>
								<th width="30" align="center" class="HeaderBG">No</th>
								<th align="center" class="HeaderBG">Periode</th>
								<th align="center" class="HeaderBG">Jenis Tagihan</th>
								
								<th align="center" class="HeaderBG">Nominal Tagihan</th>
								<th align="center" class="HeaderBG">Dibayar</th>
								<th align="center" class="HeaderBG">Sisa</th>
								<th align="center" class="HeaderBG">Status</th>
								<th align="center" class="HeaderBG">Tgl Lunas</th>
							</tr>
							<?php
								$i = 0;
								
								$totaltagihan = array(0,0);
								$sks = 0;
								foreach($a_tagihan as $rowh){
									$total += $rowh['nominaltagihan'];
									$totalbayar += $rowh['nominalbayar'];
									$totalsisa = $rowh['nominaltagihan'] - ($rowh['nominalbayar']+$rowh['potongan']);
									if($rowh['namajenistagihan']=="SKS"){
										$sks+=$rowh['nominaltagihan'];
									}
								?>
								<tr>
									<td><?=++$i?></td>
									<td align="center"><?php echo Akademik::getNamaPeriode($rowh['periode']) ?></td>
									<td><?=$rowh['namajenistagihan']?></td>
									<td align="right">Rp. <?=cStr::formatNumber($rowh['nominaltagihan'])?></td>
									<td align="right">Rp. <?=cStr::formatNumber($rowh['nominalbayar'])?></td>
									<td align="right">Rp. <?=cStr::formatNumber($totalsisa)?></td>
									
									<?php 
											if ($totalsisa  == $rowh['nominaltagihan'] and $totalsisa>0 ) {
												$statusnya = "Belum Bayar";
												$stylenya = "background-color: red; color: white;";
											}elseif ($totalsisa > 0 and $totalsisa<$rowh['nominaltagihan']) {
												$statusnya = "Belum Lunas";
												$stylenya = "background-color: yellow; color: black;";
											}elseif ($totalsisa == 0) {
												$statusnya = "Lunas";
												$stylenya = "background-color: green; color: white;";
											}

										?>
									<td align="center" style="<?= $stylenya; ?>"><b><?= $statusnya ?></b></td>
									<td align="center"><?=$rowh['flaglunas'] == 'L' ? CStr::formatDateInd($rowh['tgllunas'],false) : ''?></td>
								</tr>
							<?php } ?>
							<tr>
								<td colspan="3" align="right"><strong>Total</strong></td>
								<td align="right">Rp. <?= cStr::formatNumber($total) ?></td>
								<td align="right">Rp. <?= cStr::formatNumber($totalbayar) ?></td>
								<td align="right">Rp. <?= cStr::formatNumber($total - $totalbayar) ?></td>
							</tr>
						</table>
					</div>
					<? } ?>

					<div id="items">
						<table cellpadding="4" cellspacing="2" align="center">
							<tr>
								<td class="LeftColumnBG" width="120" style="white-space:nowrap">
									<?= Page::getDataLabel($row,'filektp'); ?>
									<span style="color: red ;">
										<br> max size 0.5 MB
										<br> png,doc,pdf,jpg
										<br> ppt,xls,rar,zip
									</span>
								</td>
								<td class="RightColumnBG">
									<?= Page::getDataInput($row,'filektp'); ?>
								</td>
							</tr>

							<tr>
								<td class="LeftColumnBG" width="120" style="white-space:nowrap">
									<?= Page::getDataLabel($row,'fileijasah'); ?>
									<span style="color: red ;">
										<br> max size 0.5 MB
										<br> png,doc,pdf,jpg
										<br> ppt,xls,rar,zip
									</span>
								</td>
								<td class="RightColumnBG">
									<?= Page::getDataInput($row,'fileijasah'); ?>
								</td>
							</tr>

							<tr>
								<td class="LeftColumnBG" width="120" style="white-space:nowrap">
									<?= Page::getDataLabel($row,'filekk'); ?>
									<span style="color: red ;">
										<br> max size 0.5 MB
										<br> png,doc,pdf,jpg
										<br> ppt,xls,rar,zip
									</span>
								</td>
								<td class="RightColumnBG">
									<?= Page::getDataInput($row,'filekk'); ?>
								</td>
							</tr>
						</table>
					</div>

				</div>
				</center>

				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="npm" id="npm" value="<?= $r_key ?>">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">

</div>
<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
$(function() {
        $.mask.definitions['~'] = "[+-]";
		$("#nemsmu").mask("999,99");
		
    });

var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	initTab(<?= $p_opentab ?>);

	loadJurusan();
	loadBidangStudi();
	loadKota();
	loadKotaOrtu();
	loadKotaPerusahaan();
	loadKotaPonpes();
	loadKotaSMU();
	loadKotaPT();
	loadPenghargaan();

	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>

	// autocomplete
	$("#mahasiswa").xautox({strpost: "f=acmahasiswa", targetid: "nimpilih"});
	$("#kodesmu").xautox({strpost: "f=acsmu", targetid: "asalsmu"});

	$("#rt").change(function(){
		$("#rtortu").val($("#rt").val());
	});
	$("#rs").change(function(){
		$("#rwortu").val($("#rw").val());
	});
	$("#alamat").change(function(){
		$("#alamatortu").val($("#alamat").val());
	});
	$("#kelurahan").change(function(){
		$("#kelurahanortu").val($("#kelurahan").val());
	});
	$("#kecamatan").change(function(){
		$("#kecamatanortu").val($("#kecamatan").val());
	});
	$("#kodepropinsi").change(function(){
		$("#kodepropinsiortu").val($("#kodepropinsi").val());
	});
	$("#kodekota").change(function(){
		$("#kodekotaortu").val($("#kodekota").val());
	});
	$("#kodepos").change(function(){
		$("#kodeposortu").val($("#kodepos").val());
	});

});

function popup(url,width,height){
	var left   = (screen.width  - width)/2;
	var top    = (screen.height - height)/2;
	var params = 'width='+width+', height='+height;
	params += ', top='+top+', left='+left;
	params += ', directories=no';
	params += ', location=no';
	params += ', menubar=no';
	params += ', resizable=no';
	params += ', scrollbars=yes';
	params += ', status=no';
	params += ', toolbar=no';
	newwin=window.open(url,'windowname5', params);
	
	if (window.focus) {newwin.focus()}
	return false;
}

// ajax ganti fakultas
function loadJurusan() {
	var param = new Array();
	param[0] = $("#kodefakultas").val();
	param[1] = "<?= $r_kodeunit ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optjurusan", q: param }
				});

	jqxhr.done(function(data) {
		$("#kodeunit").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

// ajax ganti jurusan
function loadBidangStudi() {
	var param = new Array();
	param[0] = $("#kodeunit").val();
	param[1] = "<?= $r_kodebs ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optbidangstudi", q: param }
				});

	jqxhr.done(function(data) {
		$("#kodebs").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

// ajax ganti kota
function loadKota() {
	var param = new Array();
	param[0] = $("#kodepropinsi").val();
	param[1] = "<?= $r_kodekota ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});

	jqxhr.done(function(data) {
		$("#kodekota").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

// ajax ganti kota
function loadKotaOrtu() {
	var param = new Array();
	param[0] = $("#kodepropinsiortu").val();
	param[1] = "<?= $r_kodekotaortu ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});

	jqxhr.done(function(data) {
		$("#kodekotaortu").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

// ajax ganti kota
function loadKotaPerusahaan() {
	var param = new Array();
	param[0] = $("#kodepropinsiperusahaan").val();
	param[1] = "<?= $r_kodekotaperusahaan ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});

	jqxhr.done(function(data) {
		$("#kodekotaperusahaan").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

// ajax ganti kota
function loadKotaPonpes() {
	var param = new Array();
	param[0] = $("#kodepropinsiponpes").val();
	param[1] = "<?= $r_kodekotaponpes ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});

	jqxhr.done(function(data) {
		$("#kodekotaponpes").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

// ajax ganti kota
function loadKotaSMU() {
	var param = new Array();
	param[0] = $("#kodepropinsismu").val();
	param[1] = "<?= $r_kodekotasmu ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});

	jqxhr.done(function(data) {
		$("#kodekotasmu").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

// ajax ganti kota
function loadKotaPT() {
	var param = new Array();
	param[0] = $("#kodepropinsipt").val();
	param[1] = "<?= $r_kodekotapt ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});

	jqxhr.done(function(data) {
		$("#kodekotapt").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

// pilih mahasiswa
function goPick() {
	var temp;

	temp = document.getElementById("key").value;
	document.getElementById("key").value = document.getElementById("nimpilih").value;
	document.getElementById("nimpilih").value = temp;

	goSubmit();
}

//valid penghargaan
function validpenghargaan(elem){
	if(elem.checked){
		var posted = "f=validpenghargaan&q[]="+elem.id;
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var msg=text.split('|');
			if(msg[0]==''){
				sukses(msg[1]);
			}else{
				gagal(msg[1]);
			}
			//location.reload();
			loadPenghargaan();
		});
	}else if(!elem.checked){
		var posted = "f=unvalidpenghargaan&q[]="+elem.id;
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var msg=text.split('|');
			if(msg[0]==''){
				sukses(msg[1]);
			}else{
				gagal(msg[1]);
			}
			//location.reload();
			loadPenghargaan();
		});
	}
}

function sukses(msg){
	$(".DivSuccess").html(msg);
	$(".DivSuccess").show();
	$(".DivSuccess").fadeOut(2000);
}
function gagal(msg){
	$(".DivError").html(msg);
	$(".DivError").show();
	$(".DivError").fadeOut(2000);
}

function goCetakktm() {
	goOpen('<?= $cetakKtm ?>&key=' + document.getElementById("key").value);

	
}

function loadPenghargaan(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_nim ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadpenghargaan", q: param }
				});

	jqxhr.done(function(data) {
		$("#item-penghargaan").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}


</script>
</body>
</html>
