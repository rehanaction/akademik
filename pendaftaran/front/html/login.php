<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	$userid=Modul::getUserIDFront();
	if(empty($userid))
		Route::navigate('home');
	$data=Modul::SessionDataFront($conn);
	$data=$data->FetchRow();
	$_SESSION['PENDAFTARAN']['KODEKOTA'] = trim($data['kotaalamatasal']);
	$_SESSION['PENDAFTARAN']['IDJADWAL'] = $data['idjadwal'];
	$_SESSION['PENDAFTARAN']['IDJADWALDETAIL'] = $data['idjadwaldetail'];
	$_SESSION['PENDAFTARAN']['KODEKOTASMA'] = trim($data['kodekotasmu']);
	$_SESSION['PENDAFTARAN']['IDSMU'] = $data['asalsmu'];
	
	$arrSmu = mCombo::getArrsmu($conn,$data['kodekotasmu']);
	
	$data_token=$conn->GetRow("SELECT t.jumlahpilihan,t.programpend FROM h2h.ke_pembayaranfrm p 
				join h2h.ke_tariffrm t using(idtariffrm)
				WHERE p.notoken='".$data['tokenpendaftaran']."' and t.periodedaftar='".$data['periodedaftar']."' and t.jalurpenerimaan='".$data['jalur']."' and t.idgelombang='".$data['idgelombang']."'");
	
	$r_token=$data['tokenpendaftaran'];
	$r_periode = $data['periodedaftar'];
	$r_gel = $data['idgelombang'];
	$r_jalur = $data['jalur'];	
	$r_jumlahpilihan = $data_token['jumlahpilihan'];	
	$r_programpend = $data_token['programpend'];	
	
	// hak akses
	//$a_auth = Modul::getFileAuth();
	// if(empty($data['lokasiujian'])){
		// $c_update = true;
		// $c_upload = true;
	// }else{
		// $c_update = false;
		// $c_upload = false;
	// }
	$c_insert = false;
	$c_delete = false;
	$c_update = true;
	$c_upload = true;
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
	// $conn->debug = true;
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
	$a_input[] = array('kolom' => 'goldarah', 'label' => 'Gol Darah', 'type' => 'S', 'option' => mCombo::golonganDarah(),'empty'=>true, 'notnull' => false);
	$a_input[] = array('kolom' => 'statusnikah', 'label' => 'Status Nikah', 'type' => 'S', 'option' => mCombo::statusNikah(), 'notnull' => true,'empty'=>true);
	
	$a_input[] = array('kolom' => 'jalan', 'label' => 'Jalan', 'maxlength' => 150, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'rt', 'label' => 'RT', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'rw', 'label' => 'RW', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'kel', 'label' => 'Kelurahan', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kec', 'label' => 'Kecamatan', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepos', 'label' => 'KodePos', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
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
	
	$a_input[] = array('kolom' => 'nis', 'label' => 'NIS', 'maxlength' => 20, 'size' => 50, 'notnull' => false);
    $a_input[] = array('kolom' => 'namaayah', 'label' => 'Nama Ayah', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'namaibu', 'label' => 'Nama Ibu', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodeposortu', 'label' => 'Kode Pos', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'telportu', 'label' => 'Telp Ortu', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
	$a_input[] = array('kolom' => 'jalanortu', 'label' => 'Jalan', 'maxlength' => 150, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'rtortu', 'label' => 'RT', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'rwortu', 'label' => 'RW', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'kelortu', 'label' => 'Kelurahan', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kecortu', 'label' => 'Kecamatan', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekotaortu', 'label' => 'Kota Ortu', 'type' => 'S', 'option' =>mCombo::getKota(), 'notnull' => true);
	//$a_input[] = array('kolom' => 'kodependapatanortu', 'label' => 'Pendapatan Ortu', 'type' => 'S', 'notnull' => true, 'option' => mCombo::pendapatan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodepekerjaanayah', 'label' => 'Pekerjaan Ayah', 'type' => 'S', 'notnull' => true, 'option' => mCombo::pekerjaan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodepekerjaanibu', 'label' => 'Pekerjaan Ibu', 'type' => 'S', 'notnull' => true, 'option' => mCombo::pekerjaan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodependidikanayah', 'label' => 'Pendidikan Ayah', 'type' => 'S', 'notnull' => true, 'option' => mCombo::pendidikan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodependidikanibu', 'label' => 'Pendidikan Ibu', 'type' => 'S', 'notnull' => true, 'option' => mCombo::pendidikan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'biayadari', 'label' => 'Biaya Sekolah dari', 'maxlength' => 100, 'size' => 20, 'notnull' => true);
	
	// $a_input[] = array('kolom' => 'asalsmu', 'label' => 'Nama Sekolah', 'maxlength' => 50, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'asalsmu', 'label' => 'Nama Sekolah','type' => 'S', 'notnull' => true,'option' => $arrSmu,'add'=>'onChange="getDetailSmu()"');
	$a_input[] = array('kolom' => 'alamatsmu', 'label' => 'Alamat Sekolah', 'maxlength' => 60, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'propinsismu', 'label' => 'Propinsi Sekolah ', 'type' => 'S', 'notnull' => true, 'option' => mCombo::propinsi($conn), 'add' => 'onchange="loadKotaSMU()"','empty'=>true);
	$a_input[] = array('kolom' => 'kodekotasmu', 'label' => 'Kota Sekolah', 'type' => 'S', 'notnull' => true, 'option' =>mCombo::getKota(),'add'=>'onChange="loadSMU()"');
	$a_input[] = array('kolom' => 'telpsmu', 'label' => 'Telp Sekolah', 'maxlength' => 15, 'notnull' => true, 'size' => 15);
	$a_input[] = array('kolom' => 'nemsmu', 'label' => 'NEM Kelulusan', 'type' => 'N,2', 'notnull' => false, 'maxlength' => 6, 'size' => 6);
	$a_input[] = array('kolom' => 'noijasahsmu', 'label' => 'No Ijasah SMU', 'maxlength' => 20, 'notnull' => true, 'size' => 20);
	$a_input[] = array('kolom' => 'jurusansmaasal', 'label' => 'Jurusan', 'maxlength' => 30, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'thnmasuksmaasal', 'label' => 'Tahun Masuk', 'maxlength' => 4, 'size' => 5, 'notnull' => true);
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
	
	$a_input[] = array('kolom' => 'pilihan1', 'label' => 'Pilihan 1', 'type' => 'S', 'notnull' => true, 'option' => mCombo::jurusan_spmb($conn,$r_jalur, $r_periode, $r_gel), 'empty' => true);
	$a_input[] = array('kolom' => 'pilihan2', 'label' => 'Pilihan 2', 'type' => 'S', 'option' => mCombo::jurusan_spmb($conn,$r_jalur, $r_periode, $r_gel), 'empty' => true);
	$a_input[] = array('kolom' => 'pilihan3', 'label' => 'Pilihan 3', 'type' => 'S', 'option' => mCombo::jurusan_spmb($conn,$r_jalur, $r_periode, $r_gel), 'empty' => true);
	$a_input[] = array('kolom' => 'kodepropinsiortu', 'label' => 'Propinsi Ortu', 'type' => 'S', 'option' => mCombo::propinsi($conn), 'empty' => true, 'add' => 'onchange="loadKotaOrtu()"', 'notnull' => true);
	
	$a_input[] = array('kolom' => 'kontaknama', 'label' => 'Nama Kontak', 'maxlength' => 50, 'size' => 30, 'notnull' => false);
	$a_input[] = array('kolom' => 'kontaktelp', 'label' => 'Telp Kontak', 'maxlength' => 15, 'size' => 15, 'notnull' => false);
	$a_input[] = array('kolom' => 'jalankontak', 'label' => 'Jalan', 'maxlength' => 150, 'size' => 50, 'notnull' => false);
	$a_input[] = array('kolom' => 'rtkontak', 'label' => 'RT', 'maxlength' => 5, 'size' => 5, 'notnull' => false);
	$a_input[] = array('kolom' => 'rwkontak', 'label' => 'RW', 'maxlength' => 5, 'size' => 5, 'notnull' => false);
	$a_input[] = array('kolom' => 'kelkontak', 'label' => 'Kelurahan', 'maxlength' => 20, 'size' => 50, 'notnull' => false);
	$a_input[] = array('kolom' => 'keckontak', 'label' => 'Kecamatan', 'maxlength' => 20, 'size' => 50, 'notnull' => false);
	$a_input[] = array('kolom' => 'kodekotakotak', 'label' => 'Kota Kontak', 'type' => 'S', 'option' =>mCombo::getKota(), 'notnull' => false);
	$a_input[] = array('kolom' => 'kodepropinsikontak', 'label' => 'Propinsi kontak', 'type' => 'S', 'option' => mCombo::propinsi($conn), 'empty' => true, 'add' => 'onchange="loadKotaKontak()"', 'notnull' => false);
       
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
	// $a_input[] = array('kolom' => 'kotaujian', 'label' => 'Kota Ujian', 'type' => 'S', 'option' => mCombo::getKotaUjian(), 'empty' => true, 'add'=>'onchange="loadTglujian()"', 'notnull' => true);
	//$a_input[] = array('kolom' => 'pendapatanayah', 'label' => 'Pendapatan Ayah', 'maxlength' => 14, 'size' => 20, 'notnull' => false);
	//$a_input[] = array('kolom' => 'pendapatanibu', 'label' => 'Pendapatan Ibu', 'maxlength' => 14, 'size' => 20, 'notnull' => false);
	
	//Data tambahan
	// $a_input[] = array('kolom' => 'usia_1sept', 'label' => 'Usia Per 1 September', 'maxlength' => 2, 'size' => 5, 'notnull' => false,'add'=>'style="border-radius:4px; height:20px;width:50px; border:1px solid #bbb;"');
	$a_input[] = array('kolom' => 'anakke', 'label' => 'Anak Ke-', 'maxlength' => 2, 'size' => 5, 'notnull' => false,'add'=>'style="border-radius:4px; height:20px;width:50px; border:1px solid #bbb;"');
	$a_input[] = array('kolom' => 'daribrpsaudara', 'label' => 'dari', 'maxlength' => 2, 'size' => 5, 'notnull' => false,'add'=>'style="border-radius:4px; height:20px;width:50px; border:1px solid #bbb;"');
	$a_input[] = array('kolom' => 'jarakrumah', 'label' => 'Jarak Rumah ke Kampus', 'maxlength' => 10, 'size' => 10, 'notnull' => true,'add'=>'style="border-radius:4px; height:20px;width:50px; border:1px solid #bbb;"');
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
	
	$a_input[] = array('kolom' => 'kodepropinsilahirsaudara[]', 'label' => 'Propinsi Lahir saudara', 'type' => 'S', 'option' => mCombo::propinsi($conn), 'empty' => true, 'add' => 'onchange="loadKotaLahirSaudara()"', 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekotalahirsaudara[]', 'label' => 'Kota Lahir saudara', 'type' => 'S', 'option' => mCombo::getKota(), 'notnull' => true, 'empty'=>true);
	
	$a_input[] = array('kolom' => 'tgllahirsaudara1', 'label' => 'Tgl Lahir saudara', 'type' => 'D', 'notnull' => true,'add'=>'style="border-radius:4px; height:20px;width:30px; border:1px solid #bbb;"');
	$a_input[] = array('kolom' => 'tgllahirsaudara2', 'label' => 'Tgl Lahir saudara', 'type' => 'D', 'notnull' => false,'add'=>'style="border-radius:4px; height:20px;width:30px; border:1px solid #bbb;"');
	$a_input[] = array('kolom' => 'tgllahirsaudara3', 'label' => 'Tgl Lahir saudara', 'type' => 'D', 'notnull' => false,'add'=>'style="border-radius:4px; height:20px;width:30px; border:1px solid #bbb;"');
	$a_input[] = array('kolom' => 'tgllahirsaudara4', 'label' => 'Tgl Lahir saudara', 'type' => 'D', 'notnull' => false,'add'=>'style="border-radius:4px; height:20px;width:30px; border:1px solid #bbb;"');
	$a_input[] = array('kolom' => 'tgllahirsaudara5', 'label' => 'Tgl Lahir saudara', 'type' => 'D', 'notnull' => false,'add'=>'style="border-radius:4px; height:20px;width:30px; border:1px solid #bbb;"');
	
	$a_input[] = array('kolom' => 'kodependidikansaudara1', 'label' => 'Pendidikan Saudara', 'type' => 'S', 'notnull' => false, 'option' => mCombo::pendidikan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodependidikansaudara2', 'label' => 'Pendidikan Saudara', 'type' => 'S', 'notnull' => false, 'option' => mCombo::pendidikan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodependidikansaudara3', 'label' => 'Pendidikan Saudara', 'type' => 'S', 'notnull' => false, 'option' => mCombo::pendidikan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodependidikansaudara4', 'label' => 'Pendidikan Saudara', 'type' => 'S', 'notnull' => false, 'option' => mCombo::pendidikan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodependidikansaudara5', 'label' => 'Pendidikan Saudara', 'type' => 'S', 'notnull' => false, 'option' => mCombo::pendidikan($conn), 'empty' => true);
	
	$a_input[] = array('kolom' => 'statussaudara1', 'label' => 'Status', 'type' => 'S', 'option' => mCombo::statusKeluarga(), 'notnull' => true, 'empty'=>true);
	$a_input[] = array('kolom' => 'statussaudara2', 'label' => 'Status', 'type' => 'S', 'option' => mCombo::statusKeluarga(), 'notnull' => false, 'empty'=>true);
	$a_input[] = array('kolom' => 'statussaudara3', 'label' => 'Status', 'type' => 'S', 'option' => mCombo::statusKeluarga(), 'notnull' => false, 'empty'=>true);
	$a_input[] = array('kolom' => 'statussaudara4', 'label' => 'Status', 'type' => 'S', 'option' => mCombo::statusKeluarga(), 'notnull' => false, 'empty'=>true);
	$a_input[] = array('kolom' => 'statussaudara5', 'label' => 'Status', 'type' => 'S', 'option' => mCombo::statusKeluarga(), 'notnull' => false, 'empty'=>true);
	
	$a_input[] = array('kolom' => 'tempatbekerja', 'label' => 'Tempat Bekerja', 'maxlength' => 100, 'size' => 20, 'notnull' => false);
	$a_input[] = array('kolom' => 'biayakuliah', 'label' => 'Biaya dari Instansi/Beasiswa', 'maxlength' => 50, 'size' => 20, 'notnull' => false);
	
	
        // ada aksi
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
	 
		$jml_error2=0;
			//cek file berkas 
			if($_FILES['fileberkas']['tmp_name']!=''){
				if($_FILES['fileberkas']['type'] == 'application/rar' or $_FILES['fileberkas']['type'] == 'application/x-rar'  ){

					if($_FILES['fileberkas']['size'] < 1048576){ //1 MB
						$dir = '../back/uploads/berkas/';
						$fileberkas = $dir.$r_key.'.rar';
						
						$okfile = copy($_FILES['fileberkas']['tmp_name'],$fileberkas);
						
						if(!$okfile){
							$p_postmsg = 'Terjadi kesalahan pada penyimpanan fileberkas, coba periksa sekali lagi.';
							$p_posterr = true;
							$p_fatalerr = false;
							$jml_error2=$jml_error2+1;
						}
					}else{
						$p_postmsg = 'Terjadi kesalahan pada penyimpanan fileberkas, file terlalu besar. Maksimal 1 MB.';
						$p_posterr = true;
						$p_fatalerr = false;
						$jml_error2=$jml_error2+1;
					}
				}else{
					$p_postmsg = 'Terjadi kesalahan pada penyimpanan fileberkas, file yang diperbolehkan .rar';
					$p_posterr = true;
					$p_fatalerr = false;
					$jml_error2=$jml_error2+1;
				}
			}
		 
		if ($jml_error2 == 0){
			if(empty($r_key))
				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
			else
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		}
		
		if(!$p_posterr){
			//simpan data riwayat menggunakan delete insert
			$rs_delete = $conn->Execute("delete from pendaftaran.pd_saudarakandung where nopendaftar='".$r_key."' ");
			$rs_delete2 = $conn->Execute("delete from pendaftaran.pd_pendformal where nopendaftar='".$r_key."' ");
			$rs_delete3 = $conn->Execute("delete from pendaftaran.pd_pendnonformal where nopendaftar='".$r_key."' ");
			$rs_delete4 = $conn->Execute("delete from pendaftaran.pd_organisasi where nopendaftar='".$r_key."' ");
			$rs_delete5 = $conn->Execute("delete from pendaftaran.pd_prestasiakad where nopendaftar='".$r_key."' ");
			$rs_delete6 = $conn->Execute("delete from pendaftaran.pd_prestasinonakad where nopendaftar='".$r_key."' ");
				
			for($i=1;$i<6;$i++){
				//insert data saudara
				$record_saudara = array();
				$record_saudara['nopendaftar'] = $r_key;
				$record_saudara['namasaudara'] = $_POST['namasaudara'.$i];
				$record_saudara['kodepropinsisaudara'] = $_POST['kodepropinsilahirsaudara'.$i];
				$record_saudara['kodekotasaudara'] = $_POST['kodekotalahirsaudara'.$i];
				$record_saudara['kodependidikan'] = $_POST['kodependidikansaudara'.$i];
				$record_saudara['status'] = $_POST['statussaudara'.$i];
				$record_saudara['tgllahirsaudara'] = CStr::formatDate($_POST['tgllahirsaudara'.$i]);
				
				if($_POST['namasaudara'.$i] != ''){
					$ok = mPendaftar::insertSaudara($record_saudara,$r_key);
				}
				
				//insert data pend formal
				$record_pend = array();
				$record_pend['nopendaftar'] = $r_key;
				$record_pend['namapend'] = $_POST['namapend'.$i];
				$record_pend['tempatpend'] = $_POST['tempatpend'.$i];
				$record_pend['tahunmasuk'] = $_POST['tahunmasuk'.$i];
				$record_pend['tahunlulus'] = $_POST['tahunlulus'.$i];
				
				if($_POST['namapend'.$i] != ''){
					$ok = mPendaftar::insertPendformal($record_pend,$r_key);
				}
				
				//insert data pend non formal
				$record_nonpend = array();
				$record_nonpend['nopendaftar'] = $r_key;
				$record_nonpend['namapelatihan'] = $_POST['namapelatihan'.$i];
				$record_nonpend['tingkatpelatihan'] = $_POST['tingkatpelatihan'.$i];
				$record_nonpend['tahun'] = $_POST['tahunpelatihan'.$i];
				
				if($_POST['namapelatihan'.$i] != ''){
					$ok = mPendaftar::insertPendnonformal($record_nonpend,$r_key);
				}
				
				//insert data organisasi
				$record_organisasi = array();
				$record_organisasi['nopendaftar'] = $r_key;
				$record_organisasi['namaorganisasi'] = $_POST['namaorganisasi'.$i];
				$record_organisasi['jabatan'] = $_POST['jabatan'.$i];
				$record_organisasi['tahun'] = $_POST['tahunorganisasi'.$i];
				
				if($_POST['namaorganisasi'.$i] != ''){
					$ok = mPendaftar::insertOrganisasi($record_organisasi,$r_key);
				}
				
				//insert data prestasi akad
				$record_prestasiakad = array();
				$record_prestasiakad['nopendaftar'] = $r_key;
				$record_prestasiakad['namaprestasi'] = $_POST['namaprestasi'.$i];
				$record_prestasiakad['juara'] = $_POST['juara'.$i];
				$record_prestasiakad['tingkat'] = $_POST['tingkatakademik'.$i];
				$record_prestasiakad['tahun'] = $_POST['tahunak'.$i];
				
				if($_POST['namaprestasi'.$i] != ''){
					$ok = mPendaftar::insertPrestasiAkad($record_prestasiakad,$r_key);
				}
				
				//insert data prestasi non akad
				$record_nonprestasiakad = array();
				$record_nonprestasiakad['nopendaftar'] = $r_key;
				$record_nonprestasiakad['namaprestasi'] = $_POST['namaprestasinon'.$i];
				$record_nonprestasiakad['juara'] = $_POST['juaranon'.$i];
				$record_nonprestasiakad['tingkat'] = $_POST['tingkatnonakademik'.$i];
				$record_nonprestasiakad['tahun'] = $_POST['tahunnonak'.$i];
				
				if($_POST['namaprestasinon'.$i] != ''){
					$ok = mPendaftar::insertPrestasinonAkad($record_nonprestasiakad,$r_key);
				}
			}
			
			unset($post);
		}
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
	$r_kodekotalahirayah = Page::getDataValue($row,'kodekotalahirayah');
	$r_kodekotalahiribu = Page::getDataValue($row,'kodekotalahiribu');
	$r_kodekotalahir = Page::getDataValue($row,'kodekotalahir');
	$r_kodekotaortu = Page::getDataValue($row,'kodekotaortu');
	$r_kodekotalahirsaudara1 = Page::getDataValue($row,'kodekotalahirsaudara1');
	$r_kodekotalahirsaudara2 = Page::getDataValue($row,'kodekotalahirsaudara2');
	$r_kodekotalahirsaudara3 = Page::getDataValue($row,'kodekotalahirsaudara3');
	$r_kodekotalahirsaudara4 = Page::getDataValue($row,'kodekotalahirsaudara4');
	$r_kodekotalahirsaudara5 = Page::getDataValue($row,'kodekotalahirsaudara5');

	$r_kodekotasmu = Page::getDataValue($row,'kodekotasmu');
	$r_kodekotapt = Page::getDataValue($row,'kodekotapt');
	$r_kodekotaponpes = Page::getDataValue($row,'kodekotaponpes');
	$r_kodekotakotak = Page::getDataValue($row,'kodekotakotak');
	$r_kodekotadomisili = Page::getDataValue($row,'kodekotadomisili');
	$r_asalsmu = Page::getDataValue($row,'asalsmu');
	
	if(empty($row[0]['value']) and !empty($r_key)) {
		$p_posterr = true;
		$p_fatalerr = true;
		$p_postmsg = 'User ini Tidak Mempunyai Profile';
	}
	
	$cap = $conn->GetRow("select nopendaftar,periodedaftar,idgelombang,jalurpenerimaan,* from pendaftaran.pd_pendaftar where nopendaftar='".Page::getDataValue($row,'nopendaftar')."'");
	//cek apakah jalur ini menggunakan nilai raport, -1 = pake raport, 0=tdk pake raport
	$israport = $conn->GetOne("select israport from akademik.lv_jalurpenerimaan where jalurpenerimaan='".$cap['jalurpenerimaan']."'"); 
	
	//cek apakah lulus administrasi
	$rs_cek_administrasi = $conn->GetRow("select p.pilihanditerima,u.namaunit as jurusan, uf.namaunit as fakultas,p.isadministrasi,p.* from pendaftaran.pd_pendaftar p 
					INNER JOIN gate.ms_unit u ON p.pilihanditerima=u.kodeunit
					INNER JOIN gate.ms_unit uf on uf.kodeunit=u.kodeunitparent
					where pilihanditerima is not null and nopendaftar='".$cap['nopendaftar']."'");

	$tingkat = array('1' => 'Internasional', '2' => 'Regional', '3' => 'Nasional', '4' => 'Propinsi', '5' => 'Kabupaten','6' => 'Lain-Lain');
	
	//array prodi
	$prodi = $conn->execute("select kodeunit, namaunit from gate.ms_unit");
	$arr_prodi = array();
	while($row_prodi = $prodi->FetchRow()){
		$arr_prodi[$row_prodi['kodeunit']] = $row_prodi['namaunit'];
	}
	//$arrkota = mCombo::getKota();
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
					
					if(empty($p_fatalerr)){
						require_once('inc_databutton.php');
						if(empty($p_postmsg))
							echo '<div class="alert alert-info">Selamat Datang!</span><br>';
					}
					
					if(!empty($p_postmsg)) { ?>
						<div class="alert <?= $p_posterr ? 'alert-danger' : 'alert-success' ?>">
							<?= $p_postmsg ?>
				<?	}
				?></div>
					<? if(empty($p_fatalerr)) { ?>
					<div class="panel panel-default" style="margin-top:20px;">
                      <div class="panel-heading"><span class="glyphicon glyphicon-user"></span> Data Pendaftar</div>
                      <div class="panel-body">
                      <?	$a_required = array('nama','sex','tmplahir','tgllahir','statusnikah','jalan','rt','rw'
												,'kec','kel','kodepos','kodekota','kodepropinsi','kodeagama','kodewn','telp','hp'
												,'email','nomorktp','nomorkk','namaayah','namaibu','kodeposortu','telportu','jalanortu'
												,'rtortu','rwortu','kelortu','kecortu','kodekotaortu','kodepekerjaanayah'
												,'kodepekerjaanibu','kodependidikanayah','kodependidikanibu','asalsmu','alamatsmu'
												,'propinsismu','kodekotasmu','telpsmu','ijasahsmu','bhsarab','bhsinggris','pengkomp'
												,'pilihan1','kodepropinsiortu','kontak_nama','kontak_telp','kodekotakotak','kodepropinsi_kontak'
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
								<!--<img width="40" height="30" src="images/camera.png" alt="snapshot" title="Capture Foto dari Webcam" style="cursor:pointer" onClick="popup('index.php?page=capture_cam&key=<?= $_SESSION['PENDAFTARAN']['FRONT']['USERID']; ?>&code=a&gel=<?= $cap['idgelombang']?>&jalur=<?= $cap['jalurpenerimaan']?>&period=<?= $cap['periodedaftar']?>',250,360);">-->
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
							<?if($rs_cek_administrasi['lulusujian'] == '-1'){?>
							<tr>
								<td style="background:#EBF2F9 !important; vertical-align:middle; text-align:center" rowspan="<?=$r_jumlahpilihan?>">Pilihan Prodi</td>
								<td>Pilihan 1 *	</td>
								<td>:</td>
								<td>
									<span><?= $arr_prodi[$data['pilihan1']]?></span>
									<input type="hidden" name="pilihan1" value="<?= $data['pilihan1']?>">
								</td>
							</tr>
							 <?php if($r_jumlahpilihan==2 or $r_jumlahpilihan==3){?>
							<tr>
                            	<td>Pilihan 2 *</td>
                                <td>:</td>
                                <td><span ><?= $arr_prodi[$data['pilihan2']]?></span>
								<input type="hidden" name="pilihan2" value="<?= $data['pilihan2']?>">
								</td>
                            </tr> 
                             <?php } ?>
                             <?php if($r_jumlahpilihan==3){?>  
                             <tr>
                            	<td>Pilihan 3 *</td>
                                <td>:</td>
                                <td><span ><?= $arr_prodi[$data['pilihan2']]?></span>
								<input type="hidden" name="pilihan2" value="<?= $data['pilihan2']?>">
							</tr>
							<?php } ?>
							<?}else{?>
							<tr>
                            	<td rowspan="<?=$r_jumlahpilihan?>" style="background:#EBF2F9 !important; vertical-align:middle; text-align:center">Pilihan Prodi</td>
                                <td><?= Page::getDataLabel($row,'pilihan1')?></td>
                                <td>:</td>
                                <td><?= Page::getDataInput($row,'pilihan1')?></td>
                            </tr>
                            <?php if($r_jumlahpilihan==2 or $r_jumlahpilihan==3){?>
                            <tr>
                            	 <td><?= Page::getDataLabel($row,'pilihan2')?></td>
                                <td>:</td>
                                <td><?= Page::getDataInput($row,'pilihan2')?></td>
                            </tr>   
                            <?php } ?>
                             <?php if($r_jumlahpilihan==3){?>
                            <tr>
                            	 <td><?= Page::getDataLabel($row,'pilihan3')?></td>
                                <td>:</td>
                                <td><?= Page::getDataInput($row,'pilihan3')?></td>
                            </tr>
                             <?php } ?> 
							
							<?}?>
						</table>
                      </div>
                    </div>
                    
                    <!--<div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span> Harap mengisi kolom inputan pada semua tab</div>-->
                    <ul class="nav nav-tabs" id="myTab">
						<li class="active"><a href="#biodata">Data Pendaftar</a></li>
						<li><a href="#informasi">Data Keluarga</a></li>
						<li><a href="#akademik">Data Sekolah</a></li>
						<li><a href="#jadwal">Upload Berkas</a></li>
						<li><a href="#pendidikan">Data Pendidikan/Prestasi</a></li>
                    </ul>
                    
                    <div class="tab-content">
                      <div class="tab-pane active" id="biodata">
                      	<table class="table table-bordered table-striped">
						<?= Page::getDataTR($row,'sex') ?> 
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
                   
                      <div class="tab-pane" id="akademik">
                      	<table class="table table-bordered table-striped">
						<tr>
							<td colspan="2" class="DataBG">Asal Sekolah</td>
						</tr>
						<?= Page::getDataTR($row,'propinsismu') ?>
						<?= Page::getDataTR($row,'kodekotasmu') ?>
					 
						<tr>
							<td>Nama Sekolah</td>
							<td><?= Page::getDataInput($row,'asalsmu')?>
							&nbsp;<input id="namasma" style="display:none" type="text" size=50 maxlength="100" name="namasmu">
							</td>
						</tr>
						<?= Page::getDataTR($row,'jurusansmaasal') ?>
						<?//= Page::getDataTR($row,'thnmasuksmaasal') ?>
						<?= Page::getDataTR($row,'thnlulussmaasal') ?>
						<?= Page::getDataTR($row,'alamatsmu') ?>
						<?= Page::getDataTR($row,'telpsmu') ?>
						<?= Page::getDataTR($row,'nis') ?>
						<?= Page::getDataTR($row,'nemsmu') ?>
						<?= Page::getDataTR($row,'noijasahsmu') ?>
						<?if($israport == "-1"){?>
							<?if($rs_cek_administrasi['lulusujian'] == '-1'){?>
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
											<td>
												<span><?= $data['raport_10_1']?></span>
												<input type="hidden" name="raport_10_1" value="<?= $data['raport_10_1']?>">
											</td>
											<td>
												<span><?= $data['raport_10_2']?></span>
												<input type="hidden" name="raport_10_2" value="<?= $data['raport_10_2']?>">
											</td>
											<td>
												<span><?= $data['raport_11_1']?></span>
												<input type="hidden" name="raport_11_1" value="<?= $data['raport_11_1']?>">
											</td>
											<td>
												<span><?= $data['raport_11_2']?></span>
												<input type="hidden" name="raport_11_2" value="<?= $data['raport_11_2']?>">
											</td>
											<td>
												<span><?= $data['raport_12_1']?></span>
												<input type="hidden" name="raport_12_1" value="<?= $data['raport_12_1']?>">
											</td>
											<td>
												<span><?= $data['raport_12_2']?></span>
												<input type="hidden" name="raport_12_2" value="<?= $data['raport_12_2']?>">
											</td>
										</tr>
									</table>
									</td>
								</tr>
							<?}else{?>
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
						<?//= Page::getDataTR($row,'pendapatanayah') ?>
						<?= Page::getDataTR($row,'statusayahkandung') ?>
						
						<?= Page::getDataTR($row,'namaibu') ?>
						<?= Page::getDataTR($row,'kodepropinsilahiribu,kodekotalahiribu,tgllahiribu',', ') ?>
						<?= Page::getDataTR($row,'kodepekerjaanibu') ?>
						<?= Page::getDataTR($row,'kodependidikanibu') ?>
						<?//= Page::getDataTR($row,'pendapatanibu') ?>						
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
						<?//= Page::getDataTR($row,'biayadari') ?>
						<?//= Page::getDataTR($row,'kodependapatanortu') ?>
						<!--tr>
							<td>Pendapatan Ortu</td>
							<td>
								<?//= Modul::formatNumber(Page::getDataValue($row,'pendapatanortu'),'',true) ?>
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
								<span id="show">
									<table>
										<tr>
											<td>No</td>
											<td>Nama Saudara</td>
											<td>Tempat Tanggal Lahir</td>
											<td>Pendidikan</td>
											<td>Status</td>
										</tr>
										
											<? 
											// $saudara = mPendaftaran::getSaudara($conn,Page::getDataValue($row,'nopendaftar'));
											$rs_saudara = $conn->Execute("select s.*,p.namapendidikan,pr.namapropinsi,k.namakota from pendaftaran.pd_saudarakandung s left join akademik.lv_pendidikan p on p.kodependidikan=s.kodependidikan
														left join akademik.ms_propinsi pr on pr.kodepropinsi=s.kodepropinsisaudara left join akademik.ms_kota k on k.kodekota=s.kodekotasaudara
														where nopendaftar='".Page::getDataValue($row,'nopendaftar')."' order by tgllahirsaudara asc");
											$no=1;while($rowa = $rs_saudara->FetchRow()){
											?>
											<tr>
												<td><?= $no;?>.</td>
												<td><?= $rowa['namasaudara']?></td>
												<td><?= $rowa['namakota'].', '.date('d-m-Y',strtotime($rowa['tgllahir']))?></td>
												<td><?= $rowa['namapendidikan']?></td>
												<td><?if($rowa['status']==1) echo 'Kandung'; else if($rowa['status']==2) echo 'Tiri';?></td>
											</tr>
											<?$no++;}
											$rs_saudara->MoveFirst();
											?>
									</table>
								</span>
								<span id="edit" style="display:none">
									<table>
										<tr>
											<td>No</td>
											<td>Nama Saudara</td>
											<td>Tempat Tanggal Lahir</td>
											<td>Pendidikan</td>
											<td>Status</td>
										</tr>
									<?
										$jml_saudara = 0;$no=1;while($row_saudara = $rs_saudara->FetchRow()){
										$_SESSION[SITE_ID]['PENDAFTAR']['kodekotasaudara'][$no]=$row_saudara['kodekotasaudara'];
									?>
										<tr>
											<td><?= $no;?>.</td>
											<td><input type="text" maxlength="100" size="100" name="namasaudara<?= $no?>" value="<?= $row_saudara['namasaudara']?>" ></td>
											<td>
												<select name="kodepropinsilahirsaudara<?= $no?>" id="kodepropinsilahirsaudara<?= $no?>" onchange="loadKotaLahirSaudara<?= $no?>()">
													<option selected value=""></option>
													<?$rs = mCombo::propinsi($conn);
													foreach($rs as $kodeprop => $namaprop){
														if($kodeprop == $row_saudara['kodepropinsisaudara']){
														?>
															<option selected value="<?= $kodeprop?>"><?= $namaprop?></option>
														<?}else{?>
															<option value="<?= $kodeprop?>"><?= $namaprop?></option>
														<?}
													}?>
												</select>, 
												<select name="kodekotalahirsaudara<?= $no?>" id="kodekotalahirsaudara<?= $no?>">
													<?
													/*$kota = mCombo::kota($idProp);
														foreach($kota as $kodekota => $namakota){
															if($kodekota == $row_saudara['kodekotasaudara']){
															?>
																<option selected value="<?= $kodekota?>"><?= $namakota?></option>
															<?}else{?>
																<option value="<?= $kodekota?>"><?= $namakota?></option>
															<?}
														}*/
													?>
												</select>,
												
												<input type="text" name="tgllahirsaudara<?= $no;?>" id="tgllahirsaudara<?= $no;?>" value="<?= date('d-m-Y',strtotime($row_saudara['tgllahirsaudara']))?>">
												<img id="tgllahirsaudara<?= $no;?>_trg" title="Pilih Tgl Lahir saudara" style="cursor:pointer;" src="images/cal.png">
												<script type="text/javascript">
													Calendar.setup({
													inputField : "tgllahirsaudara<?= $no;?>",
													ifFormat : "%d-%m-%Y",
													button : "tgllahirsaudara<?= $no;?>_trg",
													align : "Br",
													singleClick : true
													});
												</script>
											</td>
											<td>
												<select name="kodependidikansaudara<?= $no?>" id="kodependidikansaudara<?= $no?>">
												<option selected value=""></option>
												<?$pendidikan = mCombo::pendidikan($conn);
													foreach($pendidikan as $kodependidikan => $namapendidikan){
														if($kodependidikan == $row_saudara['kodependidikan']){
														?>
															<option selected value="<?= $kodependidikan?>"><?= $namapendidikan?></option>
														<?}else{?>
															<option value="<?= $kodependidikan?>"><?= $namapendidikan?></option>
														<?}
													}
												?>
												</select>
											</td>
											<td>
												<select name="statussaudara<?= $no?>" id="statussaudara<?= $no?>">
												<option selected value=""></option>
												<?$statussaudara = mCombo::statusKeluarga();
													foreach($statussaudara as $kodestatus => $namastatus){
														if($kodestatus == $row_saudara['status']){
														?>
															<option selected value="<?= $kodestatus?>"><?= $namastatus?></option>
														<?}else{?>
															<option value="<?= $kodestatus?>"><?= $namastatus?></option>
														<?}
													}
												?>
												</select>
											</td>
										</tr>
									<?$jml_saudara++;$no++;}?>
									<?for($i=$jml_saudara+1;$i<6;$i++){?>
										<tr>
											<td><?= $i;?>.</td>
											<td><input type="text" maxlength="100" size="100" name="namasaudara<?= $i?>" ></td>
											<td>
												<select name="kodepropinsilahirsaudara<?= $i?>" id="kodepropinsilahirsaudara<?= $i?>" onchange="loadKotaLahirSaudara<?= $i?>()">
													<option selected value=""></option>
													<?$rs = mCombo::propinsi($conn);
													foreach($rs as $kodeprop => $namaprop){
													?>
														<option value="<?= $kodeprop?>"><?= $namaprop?></option>
													<?}?>
												</select>, 
												<select name="kodekotalahirsaudara<?= $i?>" id="kodekotalahirsaudara<?= $i?>"></select>,
												<?= Page::getDataInput($row,'tgllahirsaudara'.$i)?>
											</td>
											<td><?= Page::getDataInput($row,'kodependidikansaudara'.$i)?></td>
											<td><?= Page::getDataInput($row,'statussaudara'.$i)?></td>
										</tr>
									<?}?>
									</table>
								</span>
							</td>
						</tr>
					</table>
                      </div>
					   <div class="tab-pane" id="jadwal">
                      	<table class="table table-bordered table-striped">
							<tr>
								<td colspan="2" class="DataBG">Upload Berkas</td>
							</tr>
							<tr>
								<td>File Berkas pendaftaran</td>
								<td>
									<span id="show">
										<?
										$filename = "../back/uploads/berkas/".Page::getDataValue($row,'nopendaftar').".rar";
										if (file_exists($filename)) {?>
											<a href="../back/uploads/berkas/<?= Page::getDataValue($row,'nopendaftar').'.rar'?>"> <?if(Page::getDataValue($row,'nopendaftar')!='') echo '* '.Page::getDataValue($row,'nopendaftar');?>.rar</a>
										<? }else{
											echo "Tidak ada File";
										} ?>									
									</span>
									<span id="edit" style="display:none">
										<input style="border-radius: 4px;height: 30px;border: 1px solid #bbb;" type="file" name="fileberkas" id="fileberkas" >
										<span>[ Ukuran Maksimal file = 1 MB, type: .rar ]<br>
										<i>* Semua file dijadikan satu dalam bentuk .rar. Lihat pengumuman!</i>
										</span>
									</span>
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
								<span id="show">
									<table>
										<tr>
											<td>No</td>
											<td>Nama Jenis Pendidikan</td>
											<td>Tempat Pendidikan</td>
											<td>Tahun Masuk</td>
											<td>Tahun Lulus</td>
										</tr>
										
											<? 
											$rs_pendformal = $conn->Execute("select * from pendaftaran.pd_pendformal where nopendaftar='".Page::getDataValue($row,'nopendaftar')."' order by tahunmasuk");
											$no=1;while($rowa = $rs_pendformal->FetchRow()){
											?>
											<tr>
												<td><?= $no;?>.</td>
												<td><?= $rowa['namapend']?></td>
												<td><?= $rowa['tempatpend']?></td>
												<td><?= $rowa['tahunmasuk']?></td>
												<td><?= $rowa['tahunlulus']?></td>
											</tr>
											<?$no++;}
											$rs_pendformal->MoveFirst();
											?>
									</table>
								</span>
								<span id="edit" style="display:none">
								<table>
									<tr>
										<td>No</td>
										<td>Nama Jenis Pendidikan</td>
										<td>Tempat Pendidikan</td>
										<td>Tahun Masuk</td>
										<td>Tahun Lulus</td>
									</tr>
									<?
										$pendidikan = array('PERGURUAN TINGGI' => 'PERGURUAN TINGGI', 'SMA' => 'SMA', 'SMP' => 'SMP', 'SD' => 'SD');
										$no=1;while($row_pendformal = $rs_pendformal->FetchRow()){
									?>	
										<tr>
											<td><?= $no;?></td>
											<td>
												<select name="namapend<?= $no?>" id="namapend<?= $no?>">
													<option value=""></option>
													<? foreach($pendidikan as $kodepend => $namapend){
														if($kodepend == $row_pendformal['namapend']){
													?>
														<option selected value="<?= $kodepend?>"><?= $namapend?></option>
													<?}else{?>
														<option value="<?= $kodepend?>"><?= $namapend?></option>
													<?}}?>
												</select>
											</td>
											<td><input type="text" size="100" name="tempatpend<?= $no?>" value="<?= $row_pendformal['tempatpend']?>"></input></td>
											<td><input type="text" maxlength="4" size="4" name="tahunmasuk<?= $no?>" onkeypress="return numberOnly(event)" value="<?= $row_pendformal['tahunmasuk']?>"></input></td>
											<td><input type="text" maxlength="4" size="4" name="tahunlulus<?= $no?>" onkeypress="return numberOnly(event)" value="<?= $row_pendformal['tahunlulus']?>"></input></td>
										</tr>
									<?$no++;}?>
									<?
									$pendidikan = array('PERGURUAN TINGGI' => 'PERGURUAN TINGGI', 'SMA' => 'SMA', 'SMP' => 'SMP', 'SD' => 'SD');
									for($i=$no;$i<5;$i++){?>
										<tr>
											<td><?= $i;?></td>
											<td>
												<select name="namapend<?= $i?>" id="namapend<?= $i?>">
													<option value=""></option>
													<? foreach($pendidikan as $kodepend => $namapend){?>
														<option value="<?= $kodepend?>"><?= $namapend?></option>
													<?}?>
												</select>
											</td>
											<td><input type="text" size="100" name="tempatpend<?= $i?>"></input></td>
											<td><input type="text" maxlength="4" size="4" name="tahunmasuk<?= $i?>" onkeypress="return numberOnly(event)"></input></td>
											<td><input type="text" maxlength="4" size="4" name="tahunlulus<?= $i?>" onkeypress="return numberOnly(event)"></input></td>
										</tr>
									<?}?>
								</table>
								</span>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="DataBG">Riwayat Pendidikan Non Formal/Pelatihan, Kursus yg pernah Diikuti</td>
						</tr>
						<tr>
							<td>
								<span id="show">
									<table>
										<tr>
											<td>No</td>
											<td>Nama Jenis Pelatihan</td>
											<td>Tingkat</td>
											<td>Tahun</td>
										</tr>										
											<? 
											$rs_pendnonformal = $conn->Execute("select * from pendaftaran.pd_pendnonformal where nopendaftar='".Page::getDataValue($row,'nopendaftar')."' order by tahun desc");
											$no=1;while($rowb = $rs_pendnonformal->FetchRow()){
											?>
											<tr>
												<td><?= $no;?>.</td>
												<td><?= $rowb['namapelatihan']?></td>
												<td><?= $tingkat[$rowb['tingkatpelatihan']]?></td>
												<td><?= $rowb['tahun']?></td>
											</tr>
											<?$no++;}
											$rs_pendnonformal->MoveFirst();
											?>
									</table>
								</span>
								<span id="edit" style="display:none">
								<table>
									<tr>
										<td>No</td>
										<td>Nama Jenis Pelatihan</td>
										<td>Tingkat</td>
										<td>Tahun</td>
									</tr>
									<?$no=1;while($row_nonformal = $rs_pendnonformal->FetchRow()){?>
										<tr>
											<td><?= $no;?></td>
											<td><input type="text" maxlength="100" size="100" name="namapelatihan<?= $no?>" value="<?= $row_nonformal['namapelatihan']?>"></td>
											<td>
												<select name="tingkatpelatihan<?= $no?>" id="tingkatpelatihan<?= $no?>">
													<? $tingkat = mCombo::tkpelatihan();
														foreach($tingkat as $kodetk => $namatk){
															if($kodetk == $row_nonformal['tingkatpelatihan']){
														?>
															<option selected value="<?= $kodetk?>"><?= $namatk?></option>
													<?	}else{ ?>
															<option value="<?= $kodetk?>"><?= $namatk?></option>
													<?	}} ?>
												</select>
											</td>
											<td><input type="text" maxlength="4" size="4" name="tahunpelatihan<?= $no?>" onkeypress="return numberOnly(event)" value="<?= $row_nonformal['tahun']?>"></input></td>
										</tr>
									<?$no++;}?>
									<?
									for($i=$no;$i<6;$i++){?>
										<tr>
											<td><?= $i;?></td>
											<td><input type="text" maxlength="100" size="100" name="namapelatihan<?= $i?>" ></td>
											<td>
												<select name="tingkatpelatihan<?= $i?>" id="tingkatpelatihan<?= $i?>">
													<? $tingkat = mCombo::tkpelatihan();
														foreach($tingkat as $kodetk => $namatk){?>
															<option  value="<?= $kodetk?>"><?= $namatk?></option>
													<?	} ?>
												</select>
											</td>
											<td><input type="text" maxlength="4" size="4" name="tahunpelatihan<?= $i?>" onkeypress="return numberOnly(event)" ></input></td>
										</tr>
									<?}?>
								</table>
								</span>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="DataBG">Pengalaman Organisasi</td>
						</tr>
						<tr>
							<td>
								<span id="show">
									<table>
										<tr>
											<td>No</td>
											<td>Nama Organisasi</td>
											<td>Jabatan</td>
											<td>Tahun</td>
										</tr>										
										<?
											$rs_organisasi = $conn->Execute("select * from pendaftaran.pd_organisasi where nopendaftar='".Page::getDataValue($row,'nopendaftar')."' order by tahun desc");
											$no=0;while($row_organ = $rs_organisasi->FetchRow()){
										?>
											<tr>
												<td><?= $no+1;?></td>
												<td><?= $row_organ['namaorganisasi']?></td>
												<td><?= $row_organ['jabatan']?></input></td>
												<td><?= $row_organ['tahun']?></input></td>
											</tr>
										<?$no++;}$rs_organisasi->MoveFirst();?>
									</table>
								</span>
								<span id="edit" style="display:none">
								<table>
									<tr>
										<td>No</td>
										<td>Nama Organisasi</td>
										<td>Jabatan</td>
										<td>Tahun</td>
									</tr>
									<?$no=1;while($row_organ2 = $rs_organisasi->FetchRow()){?>
										<tr>
											<td><?= $no;?></td>
											<td><input type="text" maxlength="100" size="100" name="namaorganisasi<?= $no?>" value="<?= $row_organ2['namaorganisasi']?>"></td>
											<td><input type="text" maxlength="100" size="100" name="jabatan<?= $no?>" value="<?= $row_organ2['jabatan']?>"></input></td>
											<td><input type="text" maxlength="4" size="4" name="tahunorganisasi<?= $no?>" onkeypress="return numberOnly(event)" value="<?= $row_organ2['tahun']?>"></input></td>
										</tr>
									<?$no++;}?>
									<?
									for($i=$no;$i<6;$i++){?>
										<tr>
											<td><?= $i;?></td>
											<td><input type="text" maxlength="100" size="100" name="namaorganisasi<?= $i?>" ></td>
											<td><input type="text" maxlength="100" size="100" name="jabatan<?= $i?>"></input></td>
											<td><input type="text" maxlength="4" size="4" name="tahunorganisasi<?= $i?>" onkeypress="return numberOnly(event)"></input></td>
										</tr>
									<?}?>
								</table>
								</span>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="DataBG">Prestasi Akademik</td>
						</tr>
						<tr>
							<td>
								<span id="show">
									<table>
										<tr>
											<td>No</td>
											<td>Nama Prestasi Yang Diraih</td>
											<td>Juara</td>
											<td>Tingkat</td>
											<td>Tahun</td>
										</tr>									
										<?
											$rs_akademik = $conn->Execute("select * from pendaftaran.pd_prestasiakad where nopendaftar='".Page::getDataValue($row,'nopendaftar')."' order by tahun desc");
											$no=0;while($row_akademik = $rs_akademik->FetchRow()){
										?>
											<tr>
												<td><?= $no+1;?></td>
												<td><?= $row_akademik['namaprestasi']?></td>
												<td><?= $row_akademik['juara']?></td>
												<td><?= $tingkat[$row_akademik['tingkat']]?></td>
												<td><?= $row_akademik['tahun']?></td>
											</tr>
										<?$no++;} $rs_akademik->MoveFirst();?>
									</table>
								</span>
								<span id="edit" style="display:none">
								<table>
									<tr>
										<td>No</td>
										<td>Nama Prestasi Yang Diraih</td>
										<td>Juara</td>
										<td>Tingkat</td>
										<td>Tahun</td>
									</tr>
									<?$no=1;while($row_akademik2 = $rs_akademik->FetchRow()){?>
										<tr>
											<td><?= $no;?></td>
											<td><input type="text" maxlength="100" size="100" name="namaprestasi<?= $no?>" value="<?= $row_akademik2['namaprestasi']?>"></td>
											<td><input type="text" maxlength="100" size="100" name="juara<?= $no?>" value="<?= $row_akademik2['juara']?>"></input></td>
											<td>
												<select name="tingkatakademik<?= $no?>" id="tingkatakademik<?= $no?>">
													<? $tingkat = mCombo::tkpelatihan();
														foreach($tingkat as $kodetk => $namatk){
															if($kodetk == $row_akademik2['tingkat']){
														?>
															<option selected value="<?= $kodetk?>"><?= $namatk?></option>
													<?	}else{ ?>
															<option value="<?= $kodetk?>"><?= $namatk?></option>
													<?	}} ?>
												</select>
											</td>
											<td><input type="text" maxlength="4" size="4" name="tahunak<?= $no?>" onkeypress="return numberOnly(event)" value="<?= $row_akademik2['tahun']?>"></input></td>
										</tr>
									<?$no++;}?>
									<?
									for($i=$no;$i<6;$i++){?>
										<tr>
											<td><?= $i;?></td>
											<td><input type="text" maxlength="100" size="100" name="namaprestasi<?= $i?>" ></td>
											<td><input type="text" maxlength="100" size="100" name="juara<?= $i?>"></input></td>
											<td>
												<select name="tingkatakademik<?= $i?>" id="tingkatakademik<?= $i?>">
													<? $tingkat = mCombo::tkpelatihan();
														foreach($tingkat as $kodetk => $namatk){?>
															<option selected value="<?= $kodetk?>"><?= $namatk?></option>
													<?	} ?>
												</select>
											</td>
											<td><input type="text" maxlength="4" size="4" name="tahunak<?= $i?>" onkeypress="return numberOnly(event)"></input></td>
										</tr>
									<?}?>
								</table>
								</span>
							</td>
						</tr>
						<tr>
							<td colspan="2" class="DataBG">Prestasi Non Akademik</td>
						</tr>
						<tr>
							<td>
								<span id="show">
									<table>
										<tr>
											<td>No</td>
											<td>Nama Prestasi Yang Diraih</td>
											<td>Juara</td>
											<td>Tingkat</td>
											<td>Tahun</td>
										</tr>								
										<?
											$rs_nonakademik = $conn->Execute("select * from pendaftaran.pd_prestasinonakad where nopendaftar='".Page::getDataValue($row,'nopendaftar')."' order by tahun desc");
											$no=0;while($row_nonakademik = $rs_nonakademik->FetchRow()){
										?>
											<tr>
												<td><?= $no+1;?></td>
												<td><?= $row_nonakademik['namaprestasi']?></td>
												<td><?= $row_nonakademik['juara']?></td>
												<td><?= $tingkat[$row_nonakademik['tingkat']]?></td>
												<td><?= $row_nonakademik['tahun']?></td>
											</tr>
										<?$no++;} $rs_nonakademik->MoveFirst();?>
									</table>
								</span>
								<span id="edit" style="display:none">
								<table>
									<tr>
										<td>No</td>
										<td>Nama Prestasi Yang Diraih</td>
										<td>Juara</td>
										<td>Tingkat</td>
										<td>Tahun</td>
									</tr>
									<?$no=1;while($row_nonakademik2 = $rs_nonakademik->FetchRow()){?>
										<tr>
											<td><?= $no;?></td>
											<td><input type="text" maxlength="100" size="100" name="namaprestasinon<?= $no?>" value="<?= $row_nonakademik2['namaprestasi']?>"></td>
											<td><input type="text" maxlength="100" size="100" name="juaranon<?= $no?>" value="<?= $row_nonakademik2['juara']?>"></input></td>
											<td>
												<select name="tingkatnonakademik<?= $no?>" id="tingkatnonakademik<?= $no?>">
													<? $tingkat = mCombo::tkpelatihan();
														foreach($tingkat as $kodetk => $namatk){
															if($kodetk == $row_nonakademik2['tingkat']){
														?>
															<option selected value="<?= $kodetk?>"><?= $namatk?></option>
													<?	}else{ ?>
															<option value="<?= $kodetk?>"><?= $namatk?></option>
													<?	}} ?>
												</select>
											</td>
											<td><input type="text" maxlength="4" size="4" name="tahunnonak<?= $no?>" onkeypress="return numberOnly(event)" value="<?= $row_nonakademik2['tahun']?>"></input></td>
										</tr>
									<?$no++;}?>
									<?
									for($i=$no;$i<6;$i++){?>
										<tr>
											<td><?= $i;?></td>
											<td><input type="text" maxlength="100" size="100" name="namaprestasinon<?= $i?>" ></td>
											<td><input type="text" maxlength="100" size="100" name="juaranon<?= $i?>"></input></td>
											<td>
												<select name="tingkatnonakademik<?= $i?>" id="tingkatnonakademik<?= $i?>">
													<? $tingkat = mCombo::tkpelatihan();
														foreach($tingkat as $kodetk => $namatk){?>
															<option selected value="<?= $kodetk?>"><?= $namatk?></option>
													<?	} ?>
												</select>
											</td>
											<td><input type="text" maxlength="4" size="4" name="tahunnonak<?= $i?>" onkeypress="return numberOnly(event)"></input></td>
										</tr>
									<?}?>
								</table>
								</span>
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
	
	loadKotaSMU();
	loadSMU();
	loadKota();
	loadKotaOrtu();
	loadKotaKontak();
	loadKotaLahirSaudara1();
	loadKotaLahirSaudara2();
	loadKotaLahirSaudara3();
	loadKotaLahirSaudara4();
	loadKotaLahirSaudara5();
	loadKotaLahirIbu();
	loadKotaLahirAyah();
	loadKotaPTAsal();
	loadKotaPonpes();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function numberOnly(evt) {
	evt = (evt) ? evt : window.event
	var charCode = (evt.which) ? evt.which : evt.keyCode
	if (charCode > 31 && (charCode < 48 || charCode > 57)) {
		return false;
	}
	return true;
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

function loadKotaLahir() {
	var param = new Array();
	param[0] = $("#kodepropinsilahir").val();
	param[1] = "<?= $r_kodekotalahir ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotalahir").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

function loadKotaLahirAyah() {
	var param = new Array();
	param[0] = $("#kodepropinsilahirayah").val();
	param[1] = "<?= $r_kodekotalahirayah ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotalahirayah").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

function loadKotaLahirIbu() {
	var param = new Array();
	param[0] = $("#kodepropinsilahiribu").val();
	param[1] = "<?= $r_kodekotalahiribu ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotalahiribu").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

function loadKotaLahirSaudara1() {
	var param = new Array();
	param[0] = $("#kodepropinsilahirsaudara1").val();
	param[1] = "<?= $r_kodekotalahirsaudara1 ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotalahirsaudara1").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

function loadKotaLahirSaudara2() {
	var param = new Array();
	param[0] = $("#kodepropinsilahirsaudara2").val();
	param[1] = "<?= $r_kodekotalahirsaudara2 ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotalahirsaudara2").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function loadKotaLahirSaudara3() {
	var param = new Array();
	param[0] = $("#kodepropinsilahirsaudara3").val();
	param[1] = "<?= $r_kodekotalahirsaudara3 ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotalahirsaudara3").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});

}
function loadKotaLahirSaudara4() {
	var param = new Array();
	param[0] = $("#kodepropinsilahirsaudara4").val();
	param[1] = "<?= $r_kodekotalahirsaudara4 ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotalahirsaudara4").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});

}
function loadKotaLahirSaudara5() {
	var param = new Array();
	param[0] = $("#kodepropinsilahirsaudara5").val();
	param[1] = "<?= $r_kodekotalahirsaudara5 ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotalahirsaudara5").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
 
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


function loadKotaDomisili() {
	var param = new Array();
	param[0] = $("#kodepropinsidomisili").val();
	param[1] = "<?= $r_kodekotadomisili ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotadomisili").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

// ajax ganti kota
function loadKotaSMU() {
	var param = new Array();
	param[0] = $("#propinsismu").val();
	param[1] = "<?= $r_kodekotasmu ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		loadSMU();
		$("#kodekotasmu").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

function loadSMU() {
	var param = new Array();
	param[0] = $("#kodekotasmu").val();
	param[1] = "<?= $r_asalsmu ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optsmu", q: param }
				});
	
	jqxhr.done(function(data) {
		getDetailSmu();
		$("#asalsmu").html(data);
		
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

function loadKotaPonpes() {
	var param = new Array();
	param[0] = $("#propinsiponpes").val();
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
function loadKotaPTAsal() {
	var param = new Array();
	param[0] = $("#propinsiptasal").val();
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
function loadKotaKontak(){
	var param = new Array();
	param[0] = $("#kodepropinsikontak").val();
	param[1] = "<?= $r_kodekotakotak?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotakotak").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
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
