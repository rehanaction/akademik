<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$data=Modul::SessionDataFront($conn);
	$data=$data->FetchRow();
	
	// hak akses
	//$a_auth = Modul::getFileAuth();
	if(empty($data['lokasiujian'])){
		$c_update = true;
		$c_upload = true;
	}else{
		$c_update = false;
		$c_upload = false;
	}
	$c_insert = false;
	$c_delete = false;
	
	/*
	// variabel request
	$r_self = (int)$_REQUEST['self'];
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getUserName();
	*/
	$r_key=$data['nopendaftar'];
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	// properti halaman
	$p_title = 'Data Pendaftar';
	$p_tbwidth = 630;
	$p_aktivitas = 'BIODATA';
	$p_listpage = Route::getListPage();
        $p_foto = uForm::getPathImageMahasiswa($conn,$r_key);
	
	$p_model = mPendaftar;
	// var_dump($_SESSION['PENDAFTARAN']['FRONT']['nopendaftar']);
	/*
        // hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
        */        
	// struktur view
        $r_act = $_POST['act'];
        
	if(empty($r_key))
		$p_edit = false;
	else
		$p_edit = true;
	
	// $conn->debug = true;
	$a_input = array();
	$a_input[] = array('kolom' => 'nopendaftar', 'label' => 'No.Pendaftar', 'maxlength' => 12, 'size' => 15, 'readonly' => $p_edit,'notnull' => true);
	$a_input[] = array('kolom' => 'pin', 'label' => 'PIN / Password', 'maxlength' => 12, 'size' => 15, 'notnull' => true);
	$a_input[] = array('kolom' => 'tokenpendaftaran', 'label' => 'Token', 'maxlength' => 10, 'size' => 15, 'readonly' => $p_edit,'notnull' => true);
        
	$a_input[] = array('kolom' => 'gelardepan', 'label' => 'Gelar Depan', 'maxlength' => 10, 'size' => 5);
	$a_input[] = array('kolom' => 'nama', 'label' => 'Nama Pendaftar', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'gelarbelakang', 'label' => 'Gelar Belakang', 'maxlength' => 10, 'size' => 5);
	
	$a_input[] = array('kolom' => 'sex', 'label' => 'Jenis Kelamin', 'type' => 'S', 'option' => mCombo::jenisKelamin(),'empty'=>true, 'notnull' => true);
	$a_input[] = array('kolom' => 'tmplahir', 'label' => 'Tmp & Tgl Lahir', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepropinsilahir', 'label' => 'Propinsi Lahir', 'type' => 'S', 'option' => mCombo::propinsi($conn), 'empty' => true, 'add' => 'onchange="loadKotaLahir()"', 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekotalahir', 'label' => 'Kota Lahir', 'type' => 'S', 'option' => mCombo::getKota(), 'notnull' => true, 'empty'=>true);
	$a_input[] = array('kolom' => 'tgllahir', 'label' => 'Tgl Lahir', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'goldarah', 'label' => 'Gol Darah', 'type' => 'S', 'option' => mCombo::golonganDarah(),'empty'=>true, 'notnull' => true);
	$a_input[] = array('kolom' => 'statusnikah', 'label' => 'Status Nikah', 'type' => 'S', 'option' => mCombo::statusNikah(), 'notnull' => true,'empty'=>true);
	
	$a_input[] = array('kolom' => 'jalan', 'label' => 'Jalan', 'maxlength' => 150, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'rt', 'label' => 'RT', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'rw', 'label' => 'RW', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'kel', 'label' => 'Kelurahan', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kec', 'label' => 'Kecamatan', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepos', 'label' => 'KodePos', 'maxlength' => 150, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekota', 'label' => 'Kota', 'type' => 'S', 'option' => mCombo::getKota(), 'notnull' => true, 'empty'=>true);
        
	$a_input[] = array('kolom' => 'kodepropinsi', 'label' => 'Provinsi', 'type' => 'S', 'notnull' => true, 'option' => mCombo::propinsi($conn),  'add' => 'onchange="loadKota()"','empty'=>true);
	$a_input[] = array('kolom' => 'kodeagama', 'label' => 'Agama', 'type' => 'S', 'notnull' => true, 'option' => mCombo::agama($conn),'empty'=>true);
	$a_input[] = array('kolom' => 'kodewn', 'label' => 'Kewarganegaraan', 'type' => 'S', 'notnull' => true, 'option' => mCombo::wargaNegara(),'empty'=>true);
	$a_input[] = array('kolom' => 'telp', 'label' => 'Telp', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
	$a_input[] = array('kolom' => 'telp2', 'label' => 'Telp(2)', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'hp', 'label' => 'Hp', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
	$a_input[] = array('kolom' => 'hp2', 'label' => 'Hp(2)', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'email', 'label' => 'Email', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'email2', 'label' => 'Email(2)', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'nomorktp', 'label' => 'Nomor KTP', 'maxlength' => 35, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'nomorkk', 'label' => 'Nomor KK', 'maxlength' => 35, 'size' => 50, 'notnull' => true);
	
	$a_input[] = array('kolom' => 'nis', 'label' => 'NIS', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
    $a_input[] = array('kolom' => 'namaayah', 'label' => 'Nama Ayah', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'namaibu', 'label' => 'Nama Ibu', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodeposortu', 'label' => 'Kode Pos', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'telportu', 'label' => 'Telp Ortu', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
	$a_input[] = array('kolom' => 'jalanortu', 'label' => 'Jalan', 'maxlength' => 150, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'rtortu', 'label' => 'RT', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'rwortu', 'label' => 'RW', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'kelortu', 'label' => 'Kelurahan', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kecortu', 'label' => 'Kecamatan', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekotaortu', 'label' => 'Kota Ortu', 'type' => 'S', 'option' =>mCombo::getKota(), 'notnull' => true);
	$a_input[] = array('kolom' => 'kodependapatanortu', 'label' => 'Pendapatan Ortu', 'type' => 'S', 'notnull' => true, 'option' => mCombo::pendapatan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodepekerjaanayah', 'label' => 'Pekerjaan Ayah', 'type' => 'S', 'notnull' => true, 'option' => mCombo::pekerjaan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodepekerjaanibu', 'label' => 'Pekerjaan Ibu', 'type' => 'S', 'notnull' => true, 'option' => mCombo::pekerjaan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodependidikanayah', 'label' => 'Pendidikan Ayah', 'type' => 'S', 'notnull' => true, 'option' => mCombo::pendidikan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodependidikanibu', 'label' => 'Pendidikan Ibu', 'type' => 'S', 'notnull' => true, 'option' => mCombo::pendidikan($conn), 'empty' => true);
	
	// $a_input[] = array('kolom' => 'asalsmu', 'label' => 'Nama Sekolah', 'maxlength' => 50, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'asalsmu', 'label' => 'Nama Sekolah','type' => 'S', 'notnull' => true,'option' => mCombo::getSmuAll(),'add'=>'onChange="getDetailSmu()"');
	$a_input[] = array('kolom' => 'alamatsmu', 'label' => 'Alamat Sekolah', 'maxlength' => 60, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'propinsismu', 'label' => 'Propinsi Sekolah ', 'type' => 'S', 'notnull' => true, 'option' => mCombo::propinsi($conn), 'add' => 'onchange="loadKotaSMU()"','empty'=>true);
	$a_input[] = array('kolom' => 'kodekotasmu', 'label' => 'Kota Sekolah', 'type' => 'S', 'notnull' => true, 'option' =>mCombo::getKota(),'add'=>'onChange="loadSMU()"','maxlength' => 20);
	$a_input[] = array('kolom' => 'telpsmu', 'label' => 'Telp Sekolah', 'maxlength' => 15, 'notnull' => true, 'size' => 15);
	$a_input[] = array('kolom' => 'nemsmu', 'label' => 'NEM Kelulusan', 'type' => 'N,2', 'notnull' => true, 'maxlength' => 6, 'size' => 6);
	$a_input[] = array('kolom' => 'noijasahsmu', 'label' => 'No Ijasah SMU', 'maxlength' => 20, 'notnull' => true, 'size' => 20);
	$a_input[] = array('kolom' => 'jurusansmaasal', 'label' => 'Jurusan', 'maxlength' => 30, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'thnlulussmaasal', 'label' => 'Tahun Lulus', 'maxlength' => 4, 'size' => 5, 'notnull' => true);
		
	$a_input[] = array('kolom' => 'pernahponpes', 'label' => 'Pernah Belajar di Ponpes ?', 'type' => 'R', 'option' => mCombo::pernahPonpes());
	$a_input[] = array('kolom' => 'namaponpes', 'label' => 'Nama Pesantren', 'maxlength' => 50, 'size' => 50);
	$a_input[] = array('kolom' => 'alamatponpes', 'label' => 'Alamat Pesantren', 'maxlength' => 60, 'size' => 50);
	$a_input[] = array('kolom' => 'propinsiponpes', 'label' => 'Propinsi Pesantren ', 'type' => 'S', 'option' => mCombo::propinsi($conn), 'add' => 'onchange="loadKotaPonpes()"', 'empty'=>true);
	$a_input[] = array('kolom' => 'kodekotaponpes', 'label' => 'Kota Pesantren','type' => 'S', 'option' =>mCombo::getKota(),'maxlength' => 20, 'empty'=>true);
	$a_input[] = array('kolom' => 'lamaponpes', 'label' => 'Lama Belajar', 'maxlength' => 5, 'size' => 5);
	
	$a_input[] = array('kolom' => 'mhstransfer', 'label' => 'Mahasiswa Transfer ?', 'type' => 'R', 'option' => mCombo::pernahPonpes());
	$a_input[] = array('kolom' => 'ptasal', 'label' => 'Universitas Asal', 'maxlength' => 50, 'size' => 40);
	$a_input[] = array('kolom' => 'propinsiptasal', 'label' => 'Propinsi universitas ', 'type' => 'S', 'option' => mCombo::propinsi($conn), 'add' => 'onchange="loadKotaPTAsal()"', 'empty'=>true);
	$a_input[] = array('kolom' => 'kodekotapt', 'label' => 'Kota Universitas', 'type' => 'S', 'option' =>"",'empty'=>true);
	$a_input[] = array('kolom' => 'ptjurusan', 'label' => 'Jurusan', 'maxlength' => 50, 'size' => 40);
	$a_input[] = array('kolom' => 'ptipk', 'label' => 'IPK', 'maxlength' => 4, 'size' => 4);
	$a_input[] = array('kolom' => 'ptthnlulus', 'label' => 'Tahun Lulus', 'maxlength' => 4, 'size' => 4);
	$a_input[] = array('kolom' => 'sksasal', 'label' => 'SKS', 'maxlength' => 3, 'size' => 4);
	
	$a_input[] = array('kolom' => 'bhsarab', 'label' => 'Bahasa Arab', 'type' => 'S', 'option' => mCombo::tingkatKeahlian(), 'notnull' => true);
	$a_input[] = array('kolom' => 'bhsinggris', 'label' => 'Bahasa Inggris', 'type' => 'S', 'option' => mCombo::tingkatKeahlian(), 'notnull' => true);
	$a_input[] = array('kolom' => 'pengkomp', 'label' => 'Komputer', 'type' => 'S', 'option' => mCombo::tingkatKeahlian(), 'notnull' => true);
	        
	$a_input[] = array('kolom' => 'pilihan1', 'label' => 'Pilihan 1', 'type' => 'S', 'notnull' => true, 'option' => mCombo::jurusan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'pilihan2', 'label' => 'Pilihan 2', 'type' => 'S', 'option' => mCombo::jurusan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'pilihan3', 'label' => 'Pilihan 3', 'type' => 'S', 'option' => mCombo::jurusan($conn), 'empty' => true);
	
	$a_input[] = array('kolom' => 'kodepropinsiortu', 'label' => 'Propinsi Ortu', 'type' => 'S', 'option' => mCombo::propinsi($conn), 'empty' => true, 'add' => 'onchange="loadKotaOrtu()"', 'notnull' => true);
	
	$a_input[] = array('kolom' => 'kontaknama', 'label' => 'Nama Kontak', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'kontaktelp', 'label' => 'Telp Kontak', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
	$a_input[] = array('kolom' => 'jalankontak', 'label' => 'Jalan', 'maxlength' => 150, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'rtkontak', 'label' => 'RT', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'rwkontak', 'label' => 'RW', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'kelkontak', 'label' => 'Kelurahan', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'keckontak', 'label' => 'Kecamatan', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekotakotak', 'label' => 'Kota Kontak', 'type' => 'S', 'option' =>mCombo::getKota(), 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepropinsikontak', 'label' => 'Propinsi kontak', 'type' => 'S', 'option' => mCombo::propinsi($conn), 'empty' => true, 'add' => 'onchange="loadKotaKontak()"', 'notnull' => true);
       
	$a_input[] = array('kolom' => 'isasing', 'label' => 'Mahasiswa Asing ?', 'type' => 'R', 'option' => mCombo::pernahPonpes());
	$a_input[] = array('kolom' => 'iskartanu', 'label' => 'Mempunyai Kartu NU ?', 'type' => 'R', 'option' => mCombo::punyaKartanu());
	$a_input[] = array('kolom' => 'onedayservice', 'label' => 'One Day Service?', 'type' => 'R', 'option' => mCombo::onedayservice());
	$a_input[] = array('kolom' => 'facebook', 'label' => 'Facebook', 'maxlength' => 50, 'size' => 30, 'notnull' => false);
	$a_input[] = array('kolom' => 'idjadwaldetail', 'label' => 'Jam Ujian', 'type' => 'S', 'option' =>"", 'notnull' => true);
	
	$a_input[] = array('kolom' => 'namapemilikkartanu', 'label' => 'Nama Pemilik Kartanu', 'maxlength' => 100, 'size' => 20, 'notnull' => false);
	$a_input[] = array('kolom' => 'nopemilikkartanu', 'label' => 'No. Kartanu', 'maxlength' => 100, 'size' => 20, 'notnull' => false);
	$a_input[] = array('kolom' => 'hubungankartanu', 'label' => 'Hubungan dengan Pemilik Kartanu?', 'maxlength' => 100, 'size' => 20, 'notnull' => false);
	
	$a_input[] = array('kolom' => 'raport_10_1', 'label' => 'Raport1', 'maxlength' => 100, 'size' => 5, 'notnull' => false,'add'=>'style="border-radius:4px; height:20px;width:50px; border:1px solid #bbb;"');
	$a_input[] = array('kolom' => 'raport_10_2', 'label' => 'Raport2', 'maxlength' => 100, 'size' => 5, 'notnull' => false,'add'=>'style="border-radius:4px; height:20px;width:50px; border:1px solid #bbb;"');
	$a_input[] = array('kolom' => 'raport_11_1', 'label' => 'Raport3', 'maxlength' => 100, 'size' => 5, 'notnull' => false,'add'=>'style="border-radius:4px; height:20px;width:50px; border:1px solid #bbb;"');
	$a_input[] = array('kolom' => 'raport_11_2', 'label' => 'Raport4', 'maxlength' => 100, 'size' => 5, 'notnull' => false,'add'=>'style="border-radius:4px; height:20px;width:50px; border:1px solid #bbb;"');
	$a_input[] = array('kolom' => 'raport_12_1', 'label' => 'Raport5', 'maxlength' => 100, 'size' => 5, 'notnull' => false,'add'=>'style="border-radius:4px; height:20px;width:50px; border:1px solid #bbb;"');
	$a_input[] = array('kolom' => 'raport_12_2', 'label' => 'Raport6', 'maxlength' => 100, 'size' => 5, 'notnull' => false,'add'=>'style="border-radius:4px; height:20px;width:50px; border:1px solid #bbb;"');
	$a_input[] = array('kolom' => 'pendapatanortu', 'label' => 'Pendapatan Ortu', 'maxlength' => 14, 'size' => 20, 'notnull' => false);
	$a_input[] = array('kolom' => 'kotaujian', 'label' => 'Kota Ujian', 'type' => 'S', 'option' => mCombo::getKotaUjian(), 'empty' => true, 'add'=>'onchange="loadTglujian()"', 'notnull' => true);
	
	//Data tambahan
	$a_input[] = array('kolom' => 'anakke', 'label' => 'Anak Ke-', 'maxlength' => 2, 'size' => 5, 'notnull' => false,'add'=>'style="border-radius:4px; height:20px;width:50px; border:1px solid #bbb;"');
	$a_input[] = array('kolom' => 'daribrpsaudara', 'label' => 'dari', 'maxlength' => 2, 'size' => 5, 'notnull' => false,'add'=>'style="border-radius:4px; height:20px;width:50px; border:1px solid #bbb;"');
	$a_input[] = array('kolom' => 'jarakrumah', 'label' => 'Jarak Rumah ke Kampus', 'maxlength' => 10, 'size' => 10, 'notnull' => true);
	$a_input[] = array('kolom' => 'transportasi', 'label' => 'Transportasi yg digunakan', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
	
	$a_input[] = array('kolom' => 'hoby', 'label' => 'Hobi', 'maxlength' => 50, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'cita2', 'label' => 'Cita-cita', 'maxlength' => 50, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'nohpteman', 'label' => 'No. HP Teman', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'ukuranalmamater', 'label' => 'Ukuran Jas Almamater', 'type' => 'S', 'option' => mCombo::ukuranJas(), 'notnull' => true, 'empty'=>true);
    
	$a_input[] = array('kolom' => 'jalandomisili', 'label' => 'Alamat Domisili', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'rtdomisili', 'label' => 'RT Domisili', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'rwdomisili', 'label' => 'RW Domisili', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'keldomisili', 'label' => 'Kelurahan Domisili', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kecdomisili', 'label' => 'Kecamatan Domisili', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepropinsidomisili', 'label' => 'Propinsi Domisili', 'type' => 'S', 'option' => mCombo::propinsi($conn), 'empty' => true, 'add' => 'onchange="loadKotaDomisili()"', 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekotadomisili', 'label' => 'Kota Domisili', 'type' => 'S', 'option' => mCombo::getKota(), 'notnull' => true, 'empty'=>true);
    
	$a_input[] = array('kolom' => 'kodepropinsilahirayah', 'label' => 'Propinsi Lahir Ayah', 'type' => 'S', 'option' => mCombo::propinsi($conn), 'empty' => true, 'add' => 'onchange="loadKotaLahirAyah()"', 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekotalahirayah', 'label' => 'Kota Lahir Ayah', 'type' => 'S', 'option' => mCombo::getKota(), 'notnull' => true, 'empty'=>true);
	$a_input[] = array('kolom' => 'tgllahirayah', 'label' => 'Tgl Lahir Ayah', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'statusayahkandung', 'label' => 'Status', 'type' => 'S', 'option' => mCombo::statusKeluarga(), 'notnull' => true, 'empty'=>true);
	$a_input[] = array('kolom' => 'kodepropinsilahiribu', 'label' => 'Propinsi Lahir Ibu', 'type' => 'S', 'option' => mCombo::propinsi($conn), 'empty' => true, 'add' => 'onchange="loadKotaLahirIbu()"', 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekotalahiribu', 'label' => 'Kota Lahir Ibu', 'type' => 'S', 'option' => mCombo::getKota(), 'notnull' => true, 'empty'=>true);
	$a_input[] = array('kolom' => 'tgllahiribu', 'label' => 'Tgl Lahir Ibu', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'statusibukandung', 'label' => 'Status', 'type' => 'S', 'option' => mCombo::statusKeluarga(), 'notnull' => true, 'empty'=>true);
    
	$a_input[] = array('kolom' => 'tingkatpelatihan', 'label' => 'Tingkat Pelatihan', 'type' => 'S', 'option' => mCombo::tkpelatihan(),'empty'=>true, 'notnull' => false);
    $a_input[] = array('kolom' => 'tingkatakad', 'label' => 'Tingkat Prestasi Akademik', 'type' => 'S', 'option' => mCombo::tkpelatihan(),'empty'=>true, 'notnull' => false);
    $a_input[] = array('kolom' => 'tingkatnonakad', 'label' => 'Tingkat Prestasi Non Akademik', 'type' => 'S', 'option' => mCombo::tkpelatihan(),'empty'=>true, 'notnull' => false);
	
	
        // ada aksi
        
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'savefoto' and $c_upload) {
		if(empty($_FILES['foto']['error'])) {
			$err = Page::createFoto($_FILES['foto']['tmp_name'],$p_foto,200,150);
			
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
        
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
        
	$r_kodekota = Page::getDataValue($row,'kodekota');
	$r_kodekotaortu = Page::getDataValue($row,'kodekotaortu');
	$r_kodekotasmu = Page::getDataValue($row,'kodekotasmu');
	$r_kodekotapt = Page::getDataValue($row,'kodekota_kotak');
	
	//cari nama smu
	$namasmu = mCombo::namaSmu(Page::getDataValue($row,'asalsmu'));
	// $namasmu = "";
	//jadwal detail
	$tglujian = mCombo::getTglJamUjian(Page::getDataValue($row,'idjadwaldetail'));
	$tgltes = $tglujian['tgltes'];
	$jamruang = Date::convertTime($tglujian['jammulai'])." - ".Date::convertTime($tglujian['jamselesai'])."&nbsp;&nbsp;&nbsp;".$tglujian['ruang'];
	
	if(empty($row[0]['value']) and !empty($r_key)) {
		$p_posterr = true;
		$p_fatalerr = true;
		$p_postmsg = 'User ini Tidak Mempunyai Profile';
	}
	
    
	$cap = $conn->GetRow("select periodedaftar,idgelombang,jalurpenerimaan from pendaftaran.pd_pendaftar where nopendaftar='".Page::getDataValue($row,'nopendaftar')."'");
	//cek apakah jalur ini menggunakan nilai raport, -1 = pake raport, 0=tdk pake raport
	$israport = $conn->GetOne("select israport from akademik.lv_jalurpenerimaan where jalurpenerimaan='".$cap['jalurpenerimaan']."'"); 
	
?>
<?php require_once('inc_header.php'); ?>
<div class="container">
  <div class="row">
    <div class="col-md-9">
      <div class="page-header" >
        <h2>Data Pendaftar</h2>
      </div>
      <form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<?php
					
					/*****************/
					/* TOMBOL-TOMBOL */
					/*****************/
					
					if(empty($p_fatalerr))
						require_once('inc_databutton.php');
					
					if(!empty($p_postmsg)) { ?>
                                    <div class="alert <?= $p_posterr ? 'alert-danger' : 'alert-success' ?>">
                                            <?= $p_postmsg ?>
                                    
				<?	}
				?></div>
					<? if(empty($p_fatalerr)) { ?>
					<div class="panel panel-default" style="margin-top:20px;">
                      <div class="panel-heading"><span class="glyphicon glyphicon-user"></span> Data Pendaftar</div>
                      <div class="panel-body">
                      <?	$a_required = array('nama','sex','tmplahir','tgllahir','goldarah','statusnikah','jalan','rt','rw'
												,'kec','kel','kodepos','kodekota','kodepropinsi','kodeagama','kodewn','telp','hp'
												,'email','nomorktp','nomorkk','namaayah','namaibu','kodeposortu','telportu','jalanortu'
												,'rtortu','rwortu','kelortu','kecortu','kodekotaortu','kodependapatanortu','kodepekerjaanayah'
												,'kodepekerjaanibu','kodependidikanayah','kodependidikanibu','asalsmu','alamatsmu'
												,'propinsismu','kodekotasmu','telpsmu','nemsmu','ijasahsmu','bhsarab','bhsinggris','pengkomp'
												,'pilihan1','kodepropinsiortu','kontak_nama','kontak_telp','jalankontak','rtkontak','rwkontak'
												,'kelkontak','keckontak','kodekotakotak','kodepropinsi_kontak'
												); ?>
                        <table width="100%" cellpadding="4" cellspacing="2" class="table table-bordered table-striped">
                        	<tr>
                            	<td style="background:#EBF2F9 !important; vertical-align:middle; text-align:center"><?= Page::getDataLabel($row,'nopendaftar') ?></td>
                                <td colspan="3"><?= Page::getDataInput($row,'nopendaftar') ?></td>
                                <td align="center" valign="top" rowspan="<?= 8+(empty($a_evaluasi) ? 0 : 1) ?>">
								<?= uForm::getImageMahasiswa($conn,$r_key,$c_upload) ?>
                                <br>
								<span style="font-size: 9px">untuk upload / hapus foto klik foto</span>
								<br>
								<button type="button" class="btn btn-primary" onclick="setUpload()"><span class="glyphicon glyphicon-upload"></span> Upload Foto</button>
								<img width="40" height="30" src="images/camera.png" alt="snapshot" title="Capture Foto dari Webcam" style="cursor:pointer" onClick="popup('index.php?page=capture_cam&key=<?= $_SESSION['PENDAFTARAN']['FRONT']['USERID']; ?>&code=a&gel=<?= $cap['idgelombang']?>&jalur=<?= $cap['jalurpenerimaan']?>&period=<?= $cap['periodedaftar']?>',250,360);">
								</td>
                            </tr>
                            <!--<tr>
                            	<td style="background:#EBF2F9 !important; vertical-align:middle; text-align:center"><?//= Page::getDataLabel($row,'tokenpendaftaran') ?></td>
                                <td colspan="3"><?//= Page::getDataInput($row,'tokenpendaftaran') ?></td>
                            </tr>
                            <tr>
                            	<td style="background:#EBF2F9 !important; vertical-align:middle; text-align:center"><?//= Page::getDataLabel($row,'pin') ?></td>
                                <td colspan="3"><?//= Page::getDataInput($row,'pin') ?></td>
                            </tr>-->
                        	<tr>
                            	<td rowspan="3" style="background:#EBF2F9 !important; vertical-align:middle; text-align:center">Nama Lengkap</td>
                                <td><?= Page::getDataLabel($row,'gelardepan')?></td>
                                <td>:</td>
                                <td><?= Page::getDataInput($row,'gelardepan')?></td>
                            </tr>
                            <tr>
                            	<td><?= Page::getDataLabel($row,'nama')?></td>
                                <td>:</td>
                                <td><?= Page::getDataInput($row,'nama')?></td>
                            </tr>   
                            <tr>
                            	<td><?= Page::getDataLabel($row,'gelarbelakang')?></td>
                                <td>:</td>
                                <td><?= Page::getDataInput($row,'gelarbelakang')?></td>
                            </tr>
                            <tr>
                            	<td rowspan="3" style="background:#EBF2F9 !important; vertical-align:middle; text-align:center">Info Pilihan SPMB</td>
                                <td><?= Page::getDataLabel($row,'pilihan1')?></td>
                                <td>:</td>
                                <td><?= Page::getDataInput($row,'pilihan1')?></td>
                            </tr>
                            <tr>
                            	 <td><?= Page::getDataLabel($row,'pilihan2')?></td>
                                <td>:</td>
                                <td><?= Page::getDataInput($row,'pilihan2')?></td>
                            </tr>   
                            <tr>
                            	 <td><?= Page::getDataLabel($row,'pilihan3')?></td>
                                <td>:</td>
                                <td><?= Page::getDataInput($row,'pilihan3')?></td>
                            </tr>                          
						</table>
                      </div>
                    </div>
                    
                    <!--<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span> Harap mengisi kolom inputan pada semua tab</div>-->
                    <ul class="nav nav-tabs" id="myTab">
						<li class="active"><a href="#biodata">Data Pendaftar</a></li>
						<li><a href="#informasi">Data Keluarga</a></li>
						<li><a href="#akademik">Data Sekolah</a></li>
						<li><a href="#jadwal">Jadwal &amp; Upload Berkas</a></li>
						<li><a href="#pendidikan">Data Pendidikan/Prestasi</a></li>
						<li><a href="#lain">Lain-lain</a></li>
                    </ul>
                    
                    <div class="tab-content">
                      <div class="tab-pane active" id="biodata">
                      	<table class="table table-bordered table-striped">
						<?= Page::getDataTR($row,'sex') ?>
						<?//= Page::getDataTR($row,'tmplahir,tgllahir',', ') ?>
						<?= Page::getDataTR($row,'kodepropinsilahir,kodekotalahir,tgllahir',', ') ?>
						<?= Page::getDataTR($row,'goldarah') ?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Alamat</td>
							<td class="RightColumnBG">
								<table>
									<tr>
										<td><?= Page::getDataLabel($row,'jalan')?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'jalan')?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'rt')?> / <?= Page::getDataLabel($row,'rw')?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'rt')?> / <?= Page::getDataInput($row,'rw')?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'kel')?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'kel')?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'kec') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'kec') ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<?= Page::getDataTR($row,'kodepropinsi') ?>
						<?= Page::getDataTR($row,'kodekota') ?>
						<?= Page::getDataTR($row,'kodepos') ?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">No.Telp</td>
							<td class="RightColumnBG">
								<table>
									<tr>
										<td><?= Page::getDataLabel($row,'telp') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'telp') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'telp2') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'telp2') ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">No.Hp</td>
							<td class="RightColumnBG">
								<table>
									<tr>
										<td><?= Page::getDataLabel($row,'hp') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'hp') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'hp2') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'hp2') ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">E-Mail</td>
							<td class="RightColumnBG">
								<table>
									<tr>
										<td><?= Page::getDataLabel($row,'email') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'email') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'email2') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'email2') ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<?= Page::getDataTR($row,'isasing') ?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'statusnikah') ?></td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'statusnikah') ?>
							</td>
						</tr>
						<?= Page::getDataTR($row,'kodeagama') ?>
						<?= Page::getDataTR($row,'kodewn') ?>
						<?= Page::getDataTR($row,'nomorktp') ?>
						<?= Page::getDataTR($row,'nomorkk') ?>
						
						<tr>
							<td colspan="2" class="DataBG">Data Tambahan</td>
						</tr>
						<tr>
							<td>Anak Ke-</td>
							<td><?= Page::getDataInput($row,'anakke') ?>
								dari <?= Page::getDataInput($row,'daribrpsaudara') ?> bersaudara
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Alamat Domisili</td>
							<td class="RightColumnBG">
								<table>
									<tr>
										<td><?= Page::getDataLabel($row,'jalandomisili')?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'jalandomisili')?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'rtdomisili')?> / <?= Page::getDataLabel($row,'rwdomisili')?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'rtdomisili')?> / <?= Page::getDataInput($row,'rwdomisili')?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'keldomisili')?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'keldomisili')?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'kecdomisili') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'kecdomisili') ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<?= Page::getDataTR($row,'jarakrumah') ?>
						<?= Page::getDataTR($row,'transportasi') ?>
						<?= Page::getDataTR($row,'hoby') ?>
						<?= Page::getDataTR($row,'cita2') ?>
						<?= Page::getDataTR($row,'nohpteman') ?>
						<?= Page::getDataTR($row,'ukuranalmamater') ?>
					</table>
                      </div>
                      <!--<div class="tab-pane" id="kemampuan">
                      <table class="table table-bordered table-striped">
						<?//= Page::getDataTR($row,'bhsarab') ?>
						<?//= Page::getDataTR($row,'bhsinggris') ?>
						<?//= Page::getDataTR($row,'pengkomp') ?>
					</table>
                      </div>-->
                      <div class="tab-pane" id="akademik">
                      	<table class="table table-bordered table-striped">
						<tr>
							<td colspan="2" class="DataBG">Asal Sekolah</td>
						</tr>
						<?= Page::getDataTR($row,'propinsismu') ?>
						<?= Page::getDataTR($row,'kodekotasmu') ?>
						<!--<tr>
							<td>Nama Sekolah</td>
							<td><?= $namasmu;?></td>
						</tr>-->
						<tr>
							<td>Nama Sekolah</td>
							<td><?= Page::getDataInput($row,'asalsmu')?>
							&nbsp;<input id="namasma" style="display:none" type="text" size=50 maxlength="100" name="namasmu">
							</td>
						</tr>
						<?= Page::getDataTR($row,'jurusansmaasal') ?>
						<?= Page::getDataTR($row,'thnlulussmaasal') ?>
						<?= Page::getDataTR($row,'alamatsmu') ?>
						<?= Page::getDataTR($row,'telpsmu') ?>
						<?= Page::getDataTR($row,'nis') ?>
						<?= Page::getDataTR($row,'nemsmu') ?>
						<?= Page::getDataTR($row,'noijasahsmu') ?>
						<?if($israport == "-1"){?>
						<tr>
							<td>Nilai Raport * </td>
							<td>
							<table border=0>
								<tr>
									<td align="center" colspan=2>Kelas X</td>
									<td align="center" colspan=2>Kelas XI</td>
									<td align="center" colspan=1>Kelas XII</td>
								</tr>
								<tr>
									<td>Smt. 1</td>
									<td>Smt. 2</td>
									<td>Smt. 1</td>
									<td>Smt. 2</td>
									<td>Smt. 1</td>
									<td>Smt. 2</td>
								</tr>
								<tr>
									<td><?= Page::getDataInput($row,'raport_10_1')?></td>
									<td><?= Page::getDataInput($row,'raport_10_2')?></td>
									<td><?= Page::getDataInput($row,'raport_11_1')?></td>
									<td><?= Page::getDataInput($row,'raport_11_2')?></td>
									<td><?= Page::getDataInput($row,'raport_12_1')?></td>
									<td><?= Page::getDataInput($row,'raport_12_2')?></td>
								</tr>
							</table>
							</td>
						</tr>
						<?}?>
						<tr>
							<td colspan="2" class="DataBG">Informasi Mahasiswa Transfer</td>
						</tr>
						<?= Page::getDataTR($row,'mhstransfer') ?>
						<?= Page::getDataTR($row,'ptasal') ?>
						<?= Page::getDataTR($row,'propinsiptasal') ?>
						<?= Page::getDataTR($row,'kodekotapt') ?>
						<?= Page::getDataTR($row,'ptjurusan') ?>
						<?= Page::getDataTR($row,'ptthnlulus') ?>
						<?= Page::getDataTR($row,'ptipk') ?>
						<?= Page::getDataTR($row,'sksasal') ?>
                                                
						<tr>
							<td colspan="2" class="DataBG">Pondok Pesantren</td>
						</tr>
						<?= Page::getDataTR($row,'pernahponpes') ?>
						<?= Page::getDataTR($row,'namaponpes') ?>
						<?= Page::getDataTR($row,'alamatponpes') ?>
						<?= Page::getDataTR($row,'propinsiponpes') ?>
                                                <?= Page::getDataTR($row,'kodekotaponpes') ?>
						<?= Page::getDataTR($row,'lamaponpes') ?>
					</table>
                      </div>
                      <div class="tab-pane" id="informasi">
                      	<table class="table table-bordered table-striped">
						<tr>
							<td colspan="2" class="DataBG">Wali</td>
						</tr>
						<?= Page::getDataTR($row,'namaayah') ?>
						<?= Page::getDataTR($row,'kodepropinsilahirayah,kodekotalahirayah,tgllahirayah',', ') ?>
						<?= Page::getDataTR($row,'kodepekerjaanayah') ?>
						<?= Page::getDataTR($row,'kodependidikanayah') ?>
						<?= Page::getDataTR($row,'statusayahkandung') ?>
						
						<?= Page::getDataTR($row,'namaibu') ?>
						<?= Page::getDataTR($row,'kodepropinsilahiribu,kodekotalahiribu,tgllahiribu',', ') ?>
						<?= Page::getDataTR($row,'kodepekerjaanibu') ?>
						<?= Page::getDataTR($row,'kodependidikanibu') ?>
						<?= Page::getDataTR($row,'statusayahkandung') ?>
						
						<?= Page::getDataTR($row,'kodepropinsiortu') ?>
						<?= Page::getDataTR($row,'kodekotaortu') ?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Alamat</td>
							<td class="RightColumnBG">
								<table>
									<tr>
										<td><?= Page::getDataLabel($row,'jalanortu')?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'jalanortu')?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'rtortu')?> / <?= Page::getDataLabel($row,'rwortu')?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'rtortu')?> / <?= Page::getDataInput($row,'rwortu')?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'kelortu')?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'kelortu')?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'kecortu') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'kecortu') ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<?= Page::getDataTR($row,'kodeposortu') ?>
						<?= Page::getDataTR($row,'telportu') ?>
						<?//= Page::getDataTR($row,'kodependapatanortu') ?>
						<?= Page::getDataTR($row,'pendapatanortu') ?>
						<!--tr>
							<td>Pendapatan Ortu</td>
							<td>
								<?= Modul::formatNumber(Page::getDataValue($row,'pendapatanortu'),'',true) ?>
							</td>
						</tr>-->
						<tr>
							<td colspan="2" class="DataBG">Kontak Selain Wali</td>
						</tr>
						<?= Page::getDataTR($row,'kontaknama') ?>
						<?= Page::getDataTR($row,'kodepropinsikontak') ?>
						<?= Page::getDataTR($row,'kodekotakotak') ?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Alamat</td>
							<td class="RightColumnBG">
								<table>
									<tr>
										<td><?= Page::getDataLabel($row,'jalankontak')?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'jalankontak')?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'rtkontak')?> / <?= Page::getDataLabel($row,'rwkontak')?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'rtkontak')?> / <?= Page::getDataInput($row,'rwkontak')?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'kelkontak')?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'kelkontak')?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'keckontak') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'keckontak') ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<?= Page::getDataTR($row,'kontaktelp') ?>
						<tr>
							<td colspan="2" class="DataBG">Saudara</td>
						</tr>
						<tr>
							<td colspan=2>
								<table>
									<tr>
										<td>No</td>
										<td>Nama Saudara</td>
										<td>Tempat Tanggal Lahir</td>
										<td>Pendidikan</td>
										<td>Status</td>
									</tr>
									<?
									for($i=1;$i<6;$i++){?>
										<tr>
											<td><?= $i;?></td>
											<td><input type="text" maxlength="100" size="100" name="namasaudara[]" ></td>
											<td>
												<?= Page::getDataInput($row,'kodepropinsilahirayah,kodekotalahirayah,tgllahirayah',', ') ?>
											</td>
											<?= Page::getDataTD($row,'tingkatpelatihan') ?>
											<td><input type="text" maxlength="4" size="4" name="tahunpelatihan[]" onkeypress="return numberOnly(event)"></input></td>
										</tr>
									<?}?>
								</table>
							</td>
						</tr>
					</table>
                      </div>
					   <div class="tab-pane" id="jadwal">
                      	<table class="table table-bordered table-striped">
							<tr>
								<td colspan="2" class="DataBG">Jadwal Ujian</td>
							</tr>
							<?//= Page::getDataTR($row,'onedayservice') ?>
							<?= Page::getDataTR($row,'kotaujian') ?>
							<tr>
								<td>Tanggal Ujian</td>
								<td>
									<select onChange="getKuota();getKuota2()" class="ControlStyle" name="tglujian" id="tglujian"></select>
									<a onClick="window.open('index.php?page=data_jadwalujian', '_blank')" style="cursor:pointer;">Lihat Jadwal Ujian</a>
									<!--<a onClick="show_jadwalujian()" style="cursor:pointer;">Lihat Jadwal Ujian</a>-->
									&nbsp;&nbsp;&nbsp;
									Peserta/Kuota: <span style="font-weight:bold" id="kuota" name="kuota"></span>
								</td>
							</tr>
							<tr>
								<td>Jam Ujian</td>
								<td>
									<select class="ControlStyle" name="idjadwaldetail" id="idjadwaldetail">
									<option value=""></option>
									</select>
								</td>
							</tr>
							<!--<tr>
								<td>Tanggal Ujian</td>
								<td><?= date('d-m-Y',strtotime($tgltes))?></td>
							</tr>
							<tr>
								<td>Jam Ujian</td>
								<td><?= $jamruang;?></td>
							</tr>-->
							
							<tr>
								<td colspan="2" class="DataBG">Upload Berkas</td>
							</tr>
							<tr>
								<td>File Berkas pendaftaran</td>
								<td>
									<a href="../back/uploads/berkas/<?= Page::getDataValue($row,'nopendaftar').'.rar'?>"> <?if(Page::getDataValue($row,'nopendaftar')!='') echo '* '.Page::getDataValue($row,'nopendaftar');?>.rar</a>
								</td>
							</tr>
						</table>
					  </div>
					  
					  <div class="tab-pane" id="pendidikan">
                      	<table class="table table-bordered table-striped">
						<tr>
							<td colspan="2" class="DataBG">Kemampuan Bahasa</td>
						</tr>
						<tr>
							<td>
							<table class="table table-bordered table-striped">
								<?= Page::getDataTR($row,'bhsarab') ?>
								<?= Page::getDataTR($row,'bhsinggris') ?>
								<?= Page::getDataTR($row,'pengkomp') ?>
							</table>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="DataBG">Riwayat Pendidikan Formal</td>
						</tr>
						<tr>
							<td>
								<table>
									<tr>
										<td>No</td>
										<td>Nama Jenis Pendidikan</td>
										<td>Tempat Pendidikan</td>
										<td>Tahun Masuk</td>
										<td>Tahun Lulus</td>
									</tr>
									<?
									$pendidikan = array('SD','SMP','SMA','PERGURUAN TINGGI');
									for($i=1;$i<5;$i++){?>
										<tr>
											<td><?= $i;?></td>
											<td><input type="hidden" name="namapend[]" value="<?= $pendidikan[$i-1];?>"><?= $pendidikan[$i-1];?></td>
											<td><input type="text" size="100" name="tempatpend[]"></input></td>
											<td><input type="text" maxlength="4" size="4" name="tahunmasuk[]" onkeypress="return numberOnly(event)"></input></td>
											<td><input type="text" maxlength="4" size="4" name="tahunlulus[]" onkeypress="return numberOnly(event)"></input></td>
										</tr>
									<?}?>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="DataBG">Riwayat Pendidikan Non Formal/Pelatihan, Kursus yg pernah Diikuti</td>
						</tr>
						<tr>
							<td>
								<table>
									<tr>
										<td>No</td>
										<td>Nama Jenis Pelatihan</td>
										<td>Tingkat</td>
										<td>Tahun</td>
									</tr>
									<?
									for($i=1;$i<6;$i++){?>
										<tr>
											<td><?= $i;?></td>
											<td><input type="text" maxlength="100" size="100" name="namapelatihan[]" ></td>
											<!--<td><input type="text" maxlength="100" size="100" name="tingkatpelatihan[]"></input></td>-->
											<?= Page::getDataTD($row,'tingkatpelatihan') ?>
											<td><input type="text" maxlength="4" size="4" name="tahunpelatihan[]" onkeypress="return numberOnly(event)"></input></td>
										</tr>
									<?}?>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="DataBG">Pengalaman Organisasi</td>
						</tr>
						<tr>
							<td>
								<table>
									<tr>
										<td>No</td>
										<td>Nama Organisasi</td>
										<td>Jabatan</td>
										<td>Tahun</td>
									</tr>
									<?
									for($i=1;$i<6;$i++){?>
										<tr>
											<td><?= $i;?></td>
											<td><input type="text" maxlength="100" size="100" name="namaorganisasi[]" ></td>
											<td><input type="text" maxlength="100" size="100" name="jabatan[]"></input></td>
											<td><input type="text" maxlength="4" size="4" name="tahunorganisasi[]" onkeypress="return numberOnly(event)"></input></td>
										</tr>
									<?}?>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="DataBG">Prestasi Akademik</td>
						</tr>
						<tr>
							<td>
								<table>
									<tr>
										<td>No</td>
										<td>Nama Prestasi Yang Diraih</td>
										<td>Juara</td>
										<td>Tingkat</td>
										<td>Tahun</td>
									</tr>
									<?
									for($i=1;$i<6;$i++){?>
										<tr>
											<td><?= $i;?></td>
											<td><input type="text" maxlength="100" size="100" name="namaprestasi[]" ></td>
											<td><input type="text" maxlength="100" size="100" name="juara[]"></input></td>
											<?= Page::getDataTD($row,'tingkatakad') ?>
											<!--<td><input type="text" maxlength="100" size="100" name="tingkat[]"></input></td>-->
											<td><input type="text" maxlength="4" size="4" name="tahunak[]" onkeypress="return numberOnly(event)"></input></td>
										</tr>
									<?}?>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="DataBG">Prestasi Non Akademik</td>
						</tr>
						<tr>
							<td>
								<table>
									<tr>
										<td>No</td>
										<td>Nama Prestasi Yang Diraih</td>
										<td>Juara</td>
										<td>Tingkat</td>
										<td>Tahun</td>
									</tr>
									<?
									for($i=1;$i<6;$i++){?>
										<tr>
											<td><?= $i;?></td>
											<td><input type="text" maxlength="100" size="100" name="namaprestasinon[]" ></td>
											<td><input type="text" maxlength="100" size="100" name="juaranon[]"></input></td>
											<?= Page::getDataTD($row,'tingkatnonakad') ?>											
											<!--<td><input type="text" maxlength="100" size="100" name="tingkatnon[]"></input></td>-->
											<td><input type="text" maxlength="4" size="4" name="tahunnon[]" onkeypress="return numberOnly(event)"></input></td>
										</tr>
									<?}?>
								</table>
							</td>
						</tr>
						</table>
					</div>
					  
					  <div class="tab-pane" id="lain">
                      	<table class="table table-bordered table-striped">
							<tr>
								<td colspan="2" class="DataBG">Keanggotaan Nahdlatul Ulama
							</tr>
							<!--<tr>
								<td width=50 nowrap >Mempuyai Kartu NU?</td>
								<td ><?//if(Page::getDataValue($row,'iskartanu')=="-1") echo "Ya"; else echo "Tidak";?></td>
							</tr>-->
							
							<?= Page::getDataTR($row,'iskartanu'); ?>
							<?= Page::getDataTR($row,'namapemilikkartanu'); ?>
							<?= Page::getDataTR($row,'nopemilikkartanu'); ?>
							<tr>
								<td>Hubungan dengan Pemilik kartanu?</td>
								<td>
									<?= Page::getDataInput($row,'hubungankartanu'); ?>
								</td>
							</tr>
						</table>
					  </div>
                    </div>
                    
                    <script>
                      $('#myTab a').click(function (e) {
						  e.preventDefault()
						  $(this).tab('show')
						})
                    </script>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				<?	} ?>
			</form>
      
    </div>
    <?php require_once('inc_sidebar.php'); ?>
  </div>
</div>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {

	initEdit(<?= empty($post) ? false : true ?>);
	initTab();
	
	loadKota();
	loadKotaOrtu();
	loadKotaSMU();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function numberOnly(evt) {
	evt = (evt) ? evt : window.event
	var charCode = (evt.which) ? evt.which : evt.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		//status = "This field accepts numbers only."
		// alert("This field accepts numbers only.");
		return false
	}
	//status = ""
	return true
} 

// ajax ganti kota
function loadKota() {
<?php
    $propinsi = mCombo::getPropinsi();
    while ($data = $propinsi->FetchRow())
    {
	$idProp = $data['kodepropinsi'];
	echo "if (document.pageform.kodepropinsi.value == \"".$idProp."\")";
	echo "{";

	$kota = mCombo::kota($idProp);
        //$kota = array_values($kota);
	$content = "document.getElementById('kodekota').innerHTML = \"";
	while($datakota=$kota->FetchRow())
	{
	    $content .= "<option value='".$datakota['kodekota']."'>".$datakota['namakota']."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
    }
?>
}

function loadKotaLahir() {
<?php
    $propinsi = mCombo::getPropinsi();
    while ($data = $propinsi->FetchRow())
    {
	$idProp = $data['kodepropinsi'];
	echo "if (document.pageform.kodepropinsilahir.value == \"".$idProp."\")";
	echo "{";

	$kota = mCombo::kota($idProp);
        //$kota = array_values($kota);
	$content = "document.getElementById('kodekotalahir').innerHTML = \"";
	while($datakota=$kota->FetchRow())
	{
	    $content .= "<option value='".$datakota['kodekota']."'>".$datakota['namakota']."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
    }
?>
}

function loadKotaLahirAyah() {
<?php
    $propinsi = mCombo::getPropinsi();
    while ($data = $propinsi->FetchRow())
    {
	$idProp = $data['kodepropinsi'];
	echo "if (document.pageform.kodepropinsilahirayah.value == \"".$idProp."\")";
	echo "{";

	$kota = mCombo::kota($idProp);
        //$kota = array_values($kota);
	$content = "document.getElementById('kodekotalahirayah').innerHTML = \"";
	while($datakota=$kota->FetchRow())
	{
	    $content .= "<option value='".$datakota['kodekota']."'>".$datakota['namakota']."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
    }
?>
}

function loadKotaLahirIbu() {
<?php
    $propinsi = mCombo::getPropinsi();
    while ($data = $propinsi->FetchRow())
    {
	$idProp = $data['kodepropinsi'];
	echo "if (document.pageform.kodepropinsilahiribu.value == \"".$idProp."\")";
	echo "{";

	$kota = mCombo::kota($idProp);
        //$kota = array_values($kota);
	$content = "document.getElementById('kodekotalahiribu').innerHTML = \"";
	while($datakota=$kota->FetchRow())
	{
	    $content .= "<option value='".$datakota['kodekota']."'>".$datakota['namakota']."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
    }
?>
}

// ajax ganti kota
function loadKotaOrtu() {
<?php
    $propinsi = mCombo::getPropinsi();
    while ($data = $propinsi->FetchRow())
    {
	$idProp = $data['kodepropinsi'];
	echo "if (document.pageform.kodepropinsiortu.value == \"".$idProp."\")";
	echo "{";

	$kota = mCombo::kota($idProp);
        //$kota = array_values($kota);
	$content = "document.getElementById('kodekotaortu').innerHTML = \"";
	while($datakota=$kota->FetchRow())
	{
	    $content .= "<option value='".$datakota['kodekota']."'>".$datakota['namakota']."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
    }
?>
}

function loadKotaDomisili() {
<?php
    $propinsi = mCombo::getPropinsi();
    while ($data = $propinsi->FetchRow())
    {
	$idProp = $data['kodepropinsi'];
	echo "if (document.pageform.kodepropinsidomisili.value == \"".$idProp."\")";
	echo "{";

	$kota = mCombo::kota($idProp);
	$content = "document.getElementById('kodekotadomisili').innerHTML = \"";
	$content .= "<option value=''></option>"; 
	while($datakota=$kota->FetchRow())
	{
	    $content .= "<option value='".$datakota['kodekota']."'>".$datakota['namakota']."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
    }
?>
}

// ajax ganti kota
function loadKotaSMU() {
<?php
    $propinsi = mCombo::getPropinsi();
    while ($data = $propinsi->FetchRow())
    {
	$idProp = $data['kodepropinsi'];
	echo "if (document.pageform.propinsismu.value == \"".$idProp."\")";
	echo "{";

	$kota = mCombo::kota($idProp);
        //$kota = array_values($kota);
	$content = "document.getElementById('kodekotasmu').innerHTML = \"";
	while($datakota=$kota->FetchRow())
	{
	    $content .= "<option value='".$datakota['kodekota']."'>".$datakota['namakota']."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
    }
?>
}
function loadKotaPonpes() {
<?php
    $propinsi = mCombo::getPropinsi();
    while ($data = $propinsi->FetchRow())
    {
	$idProp = $data['kodepropinsi'];
	echo "if (document.pageform.propinsiponpes.value == \"".$idProp."\")";
	echo "{";

	$kota = mCombo::kota($idProp);
        //$kota = array_values($kota);
	$content = "document.getElementById('kodekotaponpes').innerHTML = \"";
	while($datakota=$kota->FetchRow())
	{
	    $content .= "<option value='".$datakota['kodekota']."'>".$datakota['namakota']."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
    }
?>
}
function loadKotaPTAsal() {
<?php
    $propinsi = mCombo::getPropinsi();
    while ($data = $propinsi->FetchRow())
    {
	$idProp = $data['kodepropinsi'];
	echo "if (document.pageform.propinsiptasal.value == \"".$idProp."\")";
	echo "{";

	$kota = mCombo::kota($idProp);
        //$kota = array_values($kota);
	$content = "document.getElementById('kodekotapt').innerHTML = \"";
	while($datakota=$kota->FetchRow())
	{
	    $content .= "<option value='".$datakota['kodekota']."'>".$datakota['namakota']."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
    }
?>
}
function loadKotaKontak(){
<?php
    $propinsi = mCombo::getPropinsi();
    while ($data = $propinsi->FetchRow())
    {
	$idProp = $data['kodepropinsi'];
	echo "if (document.pageform.kodepropinsikontak.value == \"".$idProp."\")";
	echo "{";

	$kota = mCombo::kota($idProp);
        //$kota = array_values($kota);
	$content = "document.getElementById('kodekotakotak').innerHTML = \"";
	while($datakota=$kota->FetchRow())
	{
	    $content .= "<option value='".$datakota['kodekota']."'>".$datakota['namakota']."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
    }
?>
}

function loadSMU() {
<?php
    $kodekota = mCombo::getKotaSmu();
    while ($data = $kodekota->FetchRow())
    {
		$idkota = $data['kodekota'];
		echo "if (document.pageform.kodekotasmu.value == \"".$idkota."\")";
		echo "{";

		$listsmu = mCombo::getSmu($idkota);
		$content = "document.getElementById('asalsmu').innerHTML = \"";
		$content .="<option value=''></option>";
		while($datasmu=$listsmu->FetchRow())
		{
			$content .= "<option value='".$datasmu['idsmu']."'>".$datasmu['namasmu']."</option>";
		}
		$content .= "<option value='*'>Lain-Lain</option>";
		$content .= "\"";
		echo $content;
		echo "}\n";
    }
?>
}

function loadTglujian() {
<?php
    $kota = mCombo::getKotaSmu();
    while ($data = $kota->FetchRow())
    {
	$idkota = $data['kodekota'];
	echo "if (document.pageform.kotaujian.value == \"".$idkota."\")";
	echo "{";

	$tglujain = mCombo::tglUjian($idkota);
        //$kota = array_values($kota);
	$content = "document.getElementById('tglujian').innerHTML = \"";
	$content .="<option value=''></option>";
		
	while($dataujian=$tglujain->FetchRow())
	{
	    $content .= "<option value='".$dataujian['idjadwal']."'>".date('d-m-Y',strtotime($dataujian['tgltes']))."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
    }
?>
}


function getKuota(){
	<?php
		$tglujian = mCombo::getTglUjian();
		while ($data = $tglujian->FetchRow())
		{
			$idjadwal = $data['idjadwal'];
			echo "if (document.pageform.tglujian.value == \"".$idjadwal."\")";
			echo "{";

			$jam = mCombo::getJamUjian($idjadwal);
			$content = "document.getElementById('idjadwaldetail').innerHTML = \"";
			$content .="<option value=''></option>";
			while($datajam=$jam->FetchRow())
			{
				$content .= "<option value='".$datajam['idjadwaldetail']."'>".$datajam['jammulai']." - ".$datajam['jamselesai']."&nbsp;&nbsp;&nbsp;".$datajam['ruang']."</option>";
			}
			$content .= "\"";
			echo $content;
			echo "}\n";
		}
	?>
}

function getKuota2(){
	var posted = "f=getKuota&q[]="+$("#tglujian").val();
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		var text = text.split('#');
		document.getElementById('kuota').innerHTML = text[0]+"/"+text[1];
	});
}

function getDetailSmu(){
	if($("#asalsmu").val() == '*'){
		document.getElementById('namasma').style.display = 'table-row';
		document.getElementById('alamatsmu').value = "";
		document.getElementById('telpsmu').value = "";
	}else{
		document.getElementById('namasma').style.display = 'none';
		var posted = "f=getDetailSmu&q[]="+$("#asalsmu").val();
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var text = text.split('#');
			document.getElementById('alamatsmu').value = text[0];
			document.getElementById('telpsmu').value = text[1];
		});
	}
}

function popup(url,width,height) 
{
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

</script>
<?php require_once('inc_footer.php'); ?>
