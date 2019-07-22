<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
        // hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$c_upload = $a_auth['canother']['U'];
	// include
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getModelPath('jadwalujian'));
	require_once(Route::getModelPath('kota'));
	require_once(Route::getModelPath('smu'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	require_once($conf['model_dir'].'m_lokasi.php');
	
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
	$p_title = 'Data Pendaftar';
	$p_tbwidth = 900;
	$p_aktivitas = 'BIODATA';
	$p_listpage = Route::getListPage();
        $p_foto = uForm::getPathImageMahasiswa($conn,$r_key); //membuat debug menjadi false, kalo ada debug tidak bisa upload foto :(
        
	$p_model = mPendaftar;
	if ($r_key)
	list($p_beasiswa,$p_registrasi,$p_semesterpendek) = $p_model::getValidasipotongan($conn,$r_key); 
        // hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
                
	// struktur view
        $r_act = $_POST['act'];
        $r_subkey = CStr::removeSpecial($_POST['subkey']);
	if(empty($r_key))
		$p_edit = false;
	else
		$p_edit = true;
	$lulus=$p_model::cekLulus($conn,$r_key);
	
	$list_kota = array(); //mCombo::getKota();
	$arrJurusan = mCombo::jurusan($conn);
	$arrPropinsi =  mCombo::propinsi($conn);
	$a_input = array();	
	$a_input[] = array('kolom' => 'nopendaftar', 'label' => 'No.Pendaftar', 'maxlength' => 12, 'size' => 15, 'readonly' => $p_edit,'notnull' => true);
	$a_input[] = array('kolom' => 'pin', 'label' => 'PIN', 'maxlength' => 10, 'size' => 15,'notnull' => true);
	$a_input[] = array('kolom' => 'nopesertaspmb', 'label' => 'No.Urut Pendaftar', 'maxlength' => 12, 'size' => 15, 'readonly' => $p_edit,'notnull' => true);
	$a_input[] = array('kolom' => 'tokenpendaftaran', 'label' => 'Token', 'maxlength' => 21, 'size' => 15, 'readonly' => $p_edit,'notnull' => true, 'add' => 'onblur = "isExist()" ');
        
	$a_input[] = array('kolom' => 'gelardepan', 'label' => 'Nama Pendaftar', 'maxlength' => 50, 'size' => 5, 'notnull' => true,'add'=>'placeholder="Gelar Depan"');
	$a_input[] = array('kolom' => 'nama', 'label' => '', 'maxlength' => 50, 'size' => 30, 'notnull' => true,'add'=>'placeholder="Nama Lengkap"');
	$a_input[] = array('kolom' => 'gelarbelakang', 'label' => '', 'maxlength' => 50, 'size' => 5, 'notnull' => true,'add'=>'placeholder="Gelar Belakang"');
	
	$a_input[] = array('kolom' => 'sex', 'label' => 'Jenis Kelamin', 'type' => 'S', 'option' => mCombo::jenisKelamin(),'empty'=>true);
	$a_input[] = array('kolom' => 'kodekotalahir', 'label' => 'Tmp & Tgl Lahir', 'size' => 15);
	$a_input[] = array('kolom' => 'tgllahir', 'label' => 'Tgl Lahir', 'type' => 'D');
	$a_input[] = array('kolom' => 'goldarah', 'label' => 'Gol Darah', 'type' => 'S', 'option' => mCombo::golonganDarah(),'empty'=>true);
	$a_input[] = array('kolom' => 'usia_1sept', 'label' => 'Usia Per 1 September', 'maxlength' => 2, 'size' => 2, 'notnull' => false);
	$a_input[] = array('kolom' => 'statusnikah', 'label' => 'Status Nikah', 'type' => 'S', 'option' => mCombo::statusNikah(),'empty'=>true);
	
	$a_input[] = array('kolom' => 'jalan', 'label' => 'Jalan', 'maxlength' => 150, 'size' => 50);
	$a_input[] = array('kolom' => 'rt', 'label' => 'RT', 'maxlength' => 5, 'size' => 5);
	$a_input[] = array('kolom' => 'rw', 'label' => 'RW', 'maxlength' => 5, 'size' => 5);
	$a_input[] = array('kolom' => 'kel', 'label' => 'Kelurahan', 'maxlength' => 20, 'size' => 50);
	$a_input[] = array('kolom' => 'kec', 'label' => 'Kecamatan', 'maxlength' => 20, 'size' => 50);
	$a_input[] = array('kolom' => 'kodepos', 'label' => 'KodePos', 'maxlength' => 150, 'size' => 50);
	$a_input[] = array('kolom' => 'kodekota', 'label' => 'Kota', 'type' => 'S', 'empty'=>true, 'option'=>'');

	$a_input[] = array('kolom' => 'kodepropinsi', 'label' => 'Provinsi', 'type' => 'S', 'option' =>$arrPropinsi,'empty'=>true,'add' => 'onchange="loadKota()"');
	$a_input[] = array('kolom' => 'kodeagama', 'label' => 'Agama', 'type' => 'S', 'option' => mCombo::agama($conn),'empty'=>true);
	$a_input[] = array('kolom' => 'kodewn', 'label' => 'Kewarganegaraan', 'type' => 'S', 'option' => mCombo::wargaNegara(),'empty'=>true);
	$a_input[] = array('kolom' => 'telp', 'label' => 'Telp', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'telp2', 'label' => 'Telp 2', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'hp', 'label' => 'HP', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'hp2', 'label' => 'HP 2', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'email', 'label' => 'Email', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'email2', 'label' => 'Email 2', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'nomorktp', 'label' => 'Nomor KTP', 'maxlength' => 35, 'size' => 50);
	$a_input[] = array('kolom' => 'nomorkk', 'label' => 'Nomor KK', 'maxlength' => 35, 'size' => 50);

	$a_input[] = array('kolom' => 'nis', 'label' => 'NIS', 'maxlength' => 20, 'size' => 50);

	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'maxlength' => 4000,'rows'=>10, 'cols'=>70, 'type'=>'M');

	$a_input[] = array('kolom' => 'namaayah', 'label' => 'Nama Ayah', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'kodekotalahirayah', 'label' => 'Kota Lahir Ayah', 'type' => 'S', 'option' =>$list_kota, 'notnull' => true, 'empty'=>true);
	$a_input[] = array('kolom' => 'tgllahirayah', 'label' => 'Tgl Lahir Ayah', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'statusayahkandung', 'label' => 'Status', 'type' => 'S', 'option' => mCombo::statusKeluarga(), 'notnull' => true, 'empty'=>true);

	$a_input[] = array('kolom' => 'namaibu', 'label' => 'Nama Ibu', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'kodekotalahiribu', 'label' => 'Kota Lahir Ibu', 'type' => 'S', 'option' => $list_kota, 'notnull' => true, 'empty'=>true);
	$a_input[] = array('kolom' => 'tgllahiribu', 'label' => 'Tgl Lahir Ibu', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'statusibukandung', 'label' => 'Status', 'type' => 'S', 'option' => mCombo::statusKeluarga(), 'notnull' => true, 'empty'=>true);

	$a_input[] = array('kolom' => 'kodeposortu', 'label' => 'Kode Pos', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'telportu', 'label' => 'Telp Ortu', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'jalanortu', 'label' => 'Jalan', 'maxlength' => 150, 'size' => 50);
	$a_input[] = array('kolom' => 'rtortu', 'label' => 'RT', 'maxlength' => 5, 'size' => 5);
	$a_input[] = array('kolom' => 'rwortu', 'label' => 'RW', 'maxlength' => 5, 'size' => 5);
	$a_input[] = array('kolom' => 'kelortu', 'label' => 'Kelurahan', 'maxlength' => 20, 'size' => 50);
	$a_input[] = array('kolom' => 'kecortu', 'label' => 'Kecamatan', 'maxlength' => 20, 'size' => 50);
	$a_input[] = array('kolom' => 'kodekotaortu', 'label' => 'Kota Ortu', 'type' => 'S', 'option' =>$list_kota,'empty'=>true);
	$a_input[] = array('kolom' => 'kodepekerjaanayah', 'label' => 'Pekerjaan Ayah', 'type' => 'S', 'option' => mCombo::pekerjaan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodepekerjaanibu', 'label' => 'Pekerjaan Ibu', 'type' => 'S', 'option' => mCombo::pekerjaan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodependidikanayah', 'label' => 'Pendidikan Ayah', 'type' => 'S', 'option' => mCombo::pendidikan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodependidikanibu', 'label' => 'Pendidikan Ibu', 'type' => 'S', 'option' => mCombo::pendidikan($conn), 'empty' => true);

	$a_input[] = array('kolom' => 'jalurpenerimaan', 'label' => 'Jalur Penerimaan', 'type' => 'S', 'notnull' => true, 'option' => mCombo::jalur($conn),'empty'=>true);
	$a_input[] = array('kolom' => 'periodedaftar', 'label' => 'Periode Daftar', 'type' => 'S', 'notnull' => true, 'option' => mCombo::periode($conn),'empty'=>true);
	$a_input[] = array('kolom' => 'idgelombang', 'label' => 'Gelombang', 'type' => 'S', 'notnull' => true, 'option' => mCombo::gelombang($conn),'empty'=>true);

	$a_input[] = array('kolom' => 'asalsmu', 'label' => 'Nama Sekolah','type' => 'S', 'notnull' => true,'add'=>'onChange="getDetailSmu()" onBlur="getDetailSmu()"','empty'=>true);
	$a_input[] = array('kolom' => 'alamatsmu', 'label' => 'Alamat Sekolah', 'maxlength' => 60, 'size' => 50);
	$a_input[] = array('kolom' => 'propinsismu', 'label' => 'Propinsi Sekolah ', 'type' => 'S', 'option' =>$arrPropinsi, 'add' => 'onchange="loadKotaSMU()"','empty'=>true);
	$a_input[] = array('kolom' => 'kodekotasmu', 'label' => 'Kota Sekolah', 'type' => 'S', 'option' =>$list_kota,'maxlength' => 20,'empty'=>true,'add'=>'onChange="loadSMU()"');
	$a_input[] = array('kolom' => 'telpsmu', 'label' => 'Telp Sekolah', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'nemsmu', 'label' => 'NEM Kelulusan', 'type' => 'N,2', 'maxlength' => 6, 'size' => 6);
	$a_input[] = array('kolom' => 'noijasahsmu', 'label' => 'No Ijasah SMU', 'maxlength' => 20, 'size' => 20);

	$a_input[] = array('kolom' => 'pernahponpes', 'label' => 'Pernah Belajar di Ponpes ?', 'type' => 'R', 'option' => mCombo::pernahPonpes());
	$a_input[] = array('kolom' => 'namaponpes', 'label' => 'Nama Pesantren', 'maxlength' => 50, 'size' => 50);
	$a_input[] = array('kolom' => 'alamatponpes', 'label' => 'Alamat Pesantren', 'maxlength' => 60, 'size' => 50);
	$a_input[] = array('kolom' => 'propinsiponpes', 'label' => 'Propinsi Pesantren ', 'type' => 'S', 'option' => $arrPropinsi, 'add' => 'onchange="loadKotaPonpes()"','empty'=>true);
	$a_input[] = array('kolom' => 'kodekotaponpes', 'label' => 'Kota Pesantren','type' => 'S', 'option' =>$list_kota,'maxlength' => 20,'empty'=>true);
	$a_input[] = array('kolom' => 'lamaponpes', 'label' => 'Lama Belajar', 'maxlength' => 5, 'size' => 5);

	$a_input[] = array('kolom' => 'mhstransfer', 'label' => 'Mahasiswa Transfer ?', 'type' => 'R', 'option' => mCombo::pernahPonpes());
	$a_input[] = array('kolom' => 'ptasal', 'label' => 'Universitas Asal', 'maxlength' => 50, 'size' => 40);
	$a_input[] = array('kolom' => 'propinsiptasal', 'label' => 'Propinsi universitas ', 'type' => 'S', 'option' => $arrPropinsi, 'add' => 'onchange="loadKotaPTAsal()"','empty'=>true);
	$a_input[] = array('kolom' => 'kodekotapt', 'label' => 'Kota Universitas', 'type' => 'S', 'option' =>$list_kota,'empty'=>true);
	$a_input[] = array('kolom' => 'ptjurusan', 'label' => 'Jurusan', 'maxlength' => 50, 'size' => 40);
	$a_input[] = array('kolom' => 'ptipk', 'label' => 'IPK', 'maxlength' => 4, 'size' => 4);
	$a_input[] = array('kolom' => 'ptthnlulus', 'label' => 'Tahun Lulus', 'maxlength' => 4, 'size' => 4);
	$a_input[] = array('kolom' => 'sksasal', 'label' => 'SKS', 'maxlength' => 3, 'size' => 4);

	$a_input[] = array('kolom' => 'bhsarab', 'label' => 'Bahasa Arab', 'type' => 'S', 'option' => mCombo::tingkatKeahlian(),'empty'=>true);
	$a_input[] = array('kolom' => 'bhsinggris', 'label' => 'Bahasa Inggris', 'type' => 'S', 'option' => mCombo::tingkatKeahlian(),'empty'=>true);
	$a_input[] = array('kolom' => 'pengkomp', 'label' => 'Komputer', 'type' => 'S', 'option' => mCombo::tingkatKeahlian(),'empty'=>true);

	$a_input[] = array('kolom' => 'sistemkuliah', 'label' => 'Sistem Kuliah', 'type' => 'S','notnull' => true, 'option' => mCombo::sistemKuliah($conn),'empty'=>true);
	$a_input[] = array('kolom' => 'pilihan1', 'label' => 'Pilihan 1', 'type' => 'S', 'notnull' => true, 'option' => $arrJurusan,'empty'=>true);
	$a_input[] = array('kolom' => 'pilihan2', 'label' => 'Pilihan 2', 'type' => 'S', 'option' => $arrJurusan,'empty'=>true);
	$a_input[] = array('kolom' => 'pilihan3', 'label' => 'Pilihan 3', 'type' => 'S', 'option' => $arrJurusan,'empty'=>true);
	$a_input[] = array('kolom' => 'pilihanditerima', 'label' => 'Pilihan Diterima', 'type'=>'S','readonly'=>false,'option' => $arrJurusan,'empty'=>true);
	$a_input[] = array('kolom' => 'pindahprodi', 'label' => 'Pindah Prodi',  'type' => 'S','option' => $arrJurusan,'empty'=>true);
	$a_input[] = array('kolom' => 'keteranganpindahprodi', 'label' => 'Alasan Pindah', 'maxlength' => 4000,'rows'=>5, 'cols'=>30, 'type'=>'M');
	$a_input[] = array('kolom' => 'kodepropinsiortu', 'label' => 'Propinsi Ortu', 'type' => 'S', 'option' => $arrPropinsi, 'empty' => true, 'add' => 'onchange="loadKotaOrtu()"');
	$a_input[] = array('kolom' => 'kontaknama', 'label' => 'Nama Kontak', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'kontaktelp', 'label' => 'Telp Kontak', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'jalankontak', 'label' => 'Jalan', 'maxlength' => 150, 'size' => 50);
	$a_input[] = array('kolom' => 'rtkontak', 'label' => 'RT', 'maxlength' => 5, 'size' => 5);
	$a_input[] = array('kolom' => 'rwkontak', 'label' => 'RW', 'maxlength' => 5, 'size' => 5);
	$a_input[] = array('kolom' => 'kelkontak', 'label' => 'Kelurahan', 'maxlength' => 20, 'size' => 50);
	$a_input[] = array('kolom' => 'keckontak', 'label' => 'Kecamatan', 'maxlength' => 20, 'size' => 50);
	$a_input[] = array('kolom' => 'kodekotakotak', 'label' => 'Kota Kontak', 'type' => 'S', 'option' =>$list_kota,'empty'=>true);
	$a_input[] = array('kolom' => 'kodepropinsikontak', 'label' => 'Propinsi kontak', 'type' => 'S', 'option' => $arrPropinsi, 'empty' => true, 'add' => 'onchange="loadKotaKontak()"');
	$a_input[] = array('kolom' => 'filepindahprodi', 'label' => 'Dokumen Perpindahan Prodi', 'type' => 'U','uptype' => 'pendaftar','arrtype'=>array('pdf','docx','doc'));        
	$a_input[] = array('kolom' => 'isasing', 'label' => 'Mahasiswa Asing ?', 'type' => 'R', 'option' => mCombo::pernahPonpes());
	$a_input[] = array('kolom' => 'jurusansmaasal', 'label' => 'Jurusan', 'maxlength' => 30, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'thnlulussmaasal', 'label' => 'Tahun Lulus', 'maxlength' => 4, 'size' => 5, 'notnull' => true,'add'=>'onkeypress="return numberOnly(event)"');
	$a_input[] = array('kolom' => 'raport_10_1', 'label' => 'Smt 1', 'maxlength' => 5, 'size' => 5);
	$a_input[] = array('kolom' => 'raport_10_2', 'label' => 'Smt 2', 'maxlength' => 5, 'size' => 5);
	$a_input[] = array('kolom' => 'raport_11_1', 'label' => 'Smt 1', 'maxlength' => 5, 'size' => 5);
	$a_input[] = array('kolom' => 'raport_11_2', 'label' => 'Smt 2', 'maxlength' => 5, 'size' => 5);
	$a_input[] = array('kolom' => 'raport_12_1', 'label' => 'Smt 1', 'maxlength' => 5, 'size' => 5);
	$a_input[] = array('kolom' => 'raport_12_2', 'label' => 'Smt 2', 'maxlength' => 5, 'size' => 5);

	$a_input[] = array('kolom' => 'kotaujian', 'label' => 'Kota Ujian', 'type' => 'S', 'option' => mCombo::getKotaUjian(), 'empty' => true, 'add'=>'onchange="loadTglujian()"');
	$a_input[] = array('kolom' => 'idjadwaldetail', 'label' => 'Jam Ujian', 'type' => 'S', 'option' =>"");

	//data pribadi tambahan
	$a_input[] = array('kolom' => 'anakke', 'label' => 'Anak Ke-', 'maxlength' => 2, 'size' => 5, 'notnull' => false);
	$a_input[] = array('kolom' => 'daribrpsaudara', 'label' => 'dari', 'maxlength' => 2, 'size' => 5, 'notnull' => false);
	$a_input[] = array('kolom' => 'jarakrumah', 'label' => 'Jarak Rumah ke Kampus', 'maxlength' => 10, 'size' => 10, 'notnull' => true);
	$a_input[] = array('kolom' => 'transportasi', 'label' => 'Transportasi yg digunakan', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'jalandomisili', 'label' => 'Alamat Domisili', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'rtdomisili', 'label' => 'RT Domisili', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'rwdomisili', 'label' => 'RW Domisili', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'keldomisili', 'label' => 'Kelurahan Domisili', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kecdomisili', 'label' => 'Kecamatan Domisili', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'hoby', 'label' => 'Hobi', 'maxlength' => 50, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'cita2', 'label' => 'Cita-cita', 'maxlength' => 50, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'nohpteman', 'label' => 'No. HP Teman', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'ukuranalmamater', 'label' => 'Ukuran Jas Almamater', 'type' => 'S', 'option' => mCombo::ukuranJas(), 'notnull' => true, 'empty'=>true);

	$a_input[] = array('kolom' => 'tempatbekerja', 'label' => 'Tempat Bekerja', 'maxlength' => 100, 'size' => 20, 'notnull' => false);
	$a_input[] = array('kolom' => 'biayakuliah', 'label' => 'Biaya dari Instansi/Beasiswa', 'maxlength' => 50, 'size' => 20, 'notnull' => false);
	$a_input[] = array('kolom' => 'password', 'label' => 'Password', 'maxlength' => 20, 'size' => 20);

	$a_input[] = array('kolom' => 'potonganbeasiswa', 'label' => 'Potongan Beasiswa', 'maxlength' => 12, 'size' => 10, 'type'=>'N','readonly'=>$p_beasiswa == '-1' ? true : false);
	$a_input[] = array('kolom' => 'potonganregistrasi', 'label' => 'Potongan Registrasi', 'maxlength' => 12, 'size' => 10, 'type'=>'N','readonly'=>$p_registrasi == '-1' ? true : false);
	$a_input[] = array('kolom' => 'potongansemesterpendek', 'label' => 'Potongan Semester Pendek', 'maxlength' => 12, 'size' => 10, 'type'=>'N','readonly'=>$p_semesterpendek == '-1' ? true : false);

	$a_input[] = array('kolom' => 'keteranganpotonganbeasiswa', 'label' => 'Keterangan Potongan Beasiswa', 'maxlength' => 200, 'type'=>'A','readonly'=>$p_beasiswa == '-1' ? true : false);
	$a_input[] = array('kolom' => 'keteranganpotonganregistrasi', 'label' => 'Keterangan Potongan Registrasi', 'maxlength' => 200, 'type'=>'A','readonly'=>$p_registrasi == '-1' ? true : false);
	$a_input[] = array('kolom' => 'keteranganpotongansemesterpendek', 'label' => 'Keterangan Potongan Semester Pendek', 'maxlength' => 200, 'type'=>'A','readonly'=>$p_semesterpendek == '-1' ? true : false);
	$a_input[] = array('kolom' => 'isvalidbeasiswa', 'label' => 'Validasi Potongan Beasiswa', 'type'=>'S', 'option'=>array(0=>'Tidak disetujui', -1=>'Disetujui'),'readonly'=>true);
	$a_input[] = array('kolom' => 'isvalidregistrasi', 'label' => 'Validasi Potongan Registrasi', 'type'=>'S', 'option'=>array(0=>'Tidak disetujui', -1=>'Disetujui'),'readonly'=>true);
	$a_input[] = array('kolom' => 'isvalidsemesterpendek', 'label' => 'Validasi Potongan Semester Pendek', 'type'=>'S', 'option'=>array(0=>'Tidak disetujui', -1=>'Disetujui'),'readonly'=>true);
		
        //ada aksi
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['nopesertaspmb']=mPendaftar::nopeserta($record['periodedaftar'], $record['idgelombang'], $record['jalurpenerimaan']);
		$record['nopendaftar']  =mPendaftar::nopendaftar($conn,$record['periodedaftar'], $record['idgelombang'], $record['jalurpenerimaan']);
		
		if($_POST['asalsmu'] =='*'){ //lain-lain
			$max_idsmu = $conn->GetOne("select max(idsmu)+1 from pendaftaran.lv_smu");
			$rs_namasekolah = $conn->Execute("insert into pendaftaran.lv_smu (idsmu,namasmu,alamatsmu,telpsmu,kodekota) values ('$max_idsmu','".$_POST['namasmu']."','".$_POST['alamatsmu']."','".$_POST['telpsmu']."','".$_POST['kodekotasmu']."') ");
			$record['asalsmu'] = $max_idsmu;
		 
		}
		$record['kodekotalahir']=$_POST['kodekotalahir'];
		$record['kodepropinsilahir']=mKota::getPropinsi($conn,$record['kodekotalahir']);
		
		if(!empty($record['kodepropinsilahir'])){
			if(empty($r_key)){
				$record['password'] = md5(str_replace('-','',$record['tgllahir']));
				 
				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
			}else
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		}else{
			$p_posterr=true;
			$p_postmsg="Pastikan Kota Lahir Benar";
		}
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'insertdet' and $c_edit and !$p_limited) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		
		foreach($a_detail[$r_detail]['data'] as $t_detail) {
			$t_name = CStr::cEmChg($t_detail['nameid'],$t_detail['kolom']);
			$a_value[$t_name] = $_POST[$r_detail.'_'.$t_name];
		}
		
		list(,$record) = uForm::getPostRecord($a_detail[$r_detail]['data'],$a_value);
		$record['nim'] = $r_key;
		
		list($p_posterr,$p_postmsg) = $p_model::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail);
	}
	else if($r_act == 'deletedet' and $c_edit and !$p_limited) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkey,$r_detail);
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
	else if($r_act == 'deletefile' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key);
		
	}
        
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
        
        $r_jalur=Page::getDataValue($row,'jalurpenerimaan');
        $r_gel=Page::getDataValue($row,'idgelombang');
        $r_periode=Page::getDataValue($row,'periodedaftar');
        
        $r_kodekota = Page::getDataValue($row,'kodekota');
        $r_kodekotaortu = Page::getDataValue($row,'kodekotaortu');
        $r_kodekotasmu = Page::getDataValue($row,'kodekotasmu');
        $r_asalsmu = Page::getDataValue($row,'asalsmu');
        $r_kodekotapt = Page::getDataValue($row,'kodekotakotak');
        $r_kodekotalahir=Page::getDataValue($row,'kodekotalahir');
        $r_kotalahir=$list_kota[Page::getDataValue($row,'kodekotalahir')];
		$r_kodekotalahirayah = Page::getDataValue($row,'kodekotalahirayah');
		$r_kodekotalahiribu = Page::getDataValue($row,'kodekotalahiribu');
		$r_kodekotaortu = Page::getDataValue($row,'kodekotaortu');
		$r_kodekotalahirsaudara1 = Page::getDataValue($row,'kodekotalahirsaudara1');
		$r_kodekotalahirsaudara2 = Page::getDataValue($row,'kodekotalahirsaudara2');
		$r_kodekotalahirsaudara3 = Page::getDataValue($row,'kodekotalahirsaudara3');
		$r_kodekotalahirsaudara4 = Page::getDataValue($row,'kodekotalahirsaudara4');
		$r_kodekotalahirsaudara5 = Page::getDataValue($row,'kodekotalahirsaudara5');
		$r_kodekotaponpes = Page::getDataValue($row,'kodekotaponpes');
		$r_kodekotakotak = Page::getDataValue($row,'kodekotakotak');
		$r_kodekotadomisili = Page::getDataValue($row,'kodekotadomisili');

   
	if(empty($row[0]['value']) and !empty($r_key)) {
		$p_posterr = true;
		$p_fatalerr = true;
		$p_postmsg = 'User ini Tidak Mempunyai Profile';
	}
	if ($r_key){
		list($tarifregistrasi,$totalbiayasemester) = $p_model::getTarifregistrasi($conn,$r_key);
	}
	
    $r_idjadwaldetail=Page::getDataValue($row,'idjadwaldetail');
    
    $r_validasibeasiswa=Page::getDataValue($row,'isvalidbeasiswa');
    $r_validasiregistrasi=Page::getDataValue($row,'isvalidregistrasi');
    $r_validasisemesterpendek=Page::getDataValue($row,'isvalidsemesterpendek');
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
        <script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="scripts/common.js"></script>
        <script type="text/javascript" src="scripts/foredit.js"></script>
        <script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<style>
		#table_evaluasi { border-collapse:collapse }
		#table_evaluasi .td_ev { border:1px solid #666 }
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
        <script type="text/javascript">
$(".subnav").hover(function() {
    $(this.parentNode).addClass("borderbottom");
}, function() {
    $(this.parentNode).removeClass("borderbottom");
});

</script>
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
						</div>
					</header>
					<?	/********/
						/* DATA */
						/********/
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<?	$a_required = array('nama', 'tokenpendaftaran','periodedaftar','jalurpenerimaan','idgelombang','sistemkuliah','pilihan1','tgllahir','jurusansmaasal','thnlulussmaasal'); ?>
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
					<tr>
						<td colspan="3">
							<div style="width:<?= $p_tbwidth-50 ?>px; border-width:1px; background: #eceaea; border-radius:5px; border-style: solid; border-color:#a09e9e; padding: 10px;">
								Token dan PIN didapat dari data host-to-host
							</div>
						</td>
					</tr>
					<tr>
					<? if(!empty($r_key)){?>
						<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nopendaftar') ?></td>
						<td class="RightColumnBG"><?= Page::getDataInput($row,'nopendaftar') ?></td>
						<td align="center" valign="top" rowspan="<?= 8+(empty($a_evaluasi) ? 0 : 1) ?>">
							<?= uForm::getImageMahasiswa($conn,$r_key,$c_upload) ?>
							<br>
							<?php if ($c_upload){?>
							<span style="font-size: 9px">untuk upload / hapus foto klik foto</span><br>
							<input type="button" style="padding:4px" value="Upload" class="ControlStyle" onclick="setUpload()">
							<input type="button" style="padding:4px" value="Hapus" class="ControlStyle" onclick="goHapusFoto()">
							<input type="button" style="padding:4px" value="Capture" class="ControlStyle" onClick="popup('index.php?page=capture_cam&nopendaftar=<?=$r_key?>&jalur=<?=$r_jalur?>&gelombang=<?=$r_gel?>&periode=<?=$r_periode?>',250,360);">
							<?php }?>
						</td>
					<? } ?>
					</tr>
					<?//= Page::getDataTR($row,'nopesertaspmb') ?>
					<tr>
						<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tokenpendaftaran') ?></td>
						<td class="RightColumnBG">
							<?= Page::getDataInput($row,'tokenpendaftaran') ?>
							<div id="checktoken" style="display: none">
								<img src="images/loading.gif"/> sedang mengecek token..
							</div>
						</td>
					</tr>
\                                                        <?= Page::getDataTR($row,'gelardepan,nama,gelarbelakang') ?>
					<tr>
						<td class="LeftColumnBG" style="white-space:nowrap">
							Info Pendaftaran<br>
							<p style="width:50px; font-size: 8px;">Data harus sesuai dengan<br> jalur yang dibuka</p>
						</td>
						<td class="RightColumnBG">
							<table>
								<tr>
									<td><?= Page::getDataLabel($row,'periodedaftar') ?></td>
									<td>:</td>
									<td><?= Page::getDataInput($row,'periodedaftar') ?></td>
								</tr>
								<tr>
									<td><?= Page::getDataLabel($row,'jalurpenerimaan') ?></td>
									<td>:</td>
									<td><?= Page::getDataInput($row,'jalurpenerimaan') ?></td>
								</tr>
								<tr>
									<td><?= Page::getDataLabel($row,'idgelombang') ?></td>
									<td>:</td>
									<td><?= Page::getDataInput($row,'idgelombang') ?></td>
								</tr>
								<tr>
									<td><?= Page::getDataLabel($row,'sistemkuliah') ?></td>
									<td>:</td>
									<td><?= Page::getDataInput($row,'sistemkuliah') ?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="LeftColumnBG" style="white-space:nowrap">Info Pilihan SPMB</td>
						<td class="RightColumnBG">
							<table>
								<tr>
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
								<?
								if(!empty($r_key)){ ?>
								<tr>
									<td><?= Page::getDataLabel($row,'pilihanditerima') ?></td>
									<td>:</td>
									<td><?= Page::getDataInput($row,'pilihanditerima') ?></td>
								</tr>
								<? } ?>
							</table>
						</td>
					</tr>
                    <tr>
						<td class="LeftColumnBG">Tarif Registrasi</td>
						<td class="RightColumnBG">Rp. <?= number_format($tarifregistrasi['total'])?></td>
					</tr>
                    <tr>
						<td class="LeftColumnBG">Biaya Per Semester</td>
						<td class="RightColumnBG">Rp. <?= number_format($totalbiayasemester)?></td>
					</tr>
					
					</table>
					</div>
				</center>
				<br>
				<center>
				<div class="tabs" style="width:<?= $p_tbwidth ?>px">
					<ul>
						<li><a id="tablink" href="javascript:void(0)">Biodata</a></li>
						<li><a id="tablink" href="javascript:void(0)">Akademik</a></li>
                        <li><a id="tablink" href="javascript:void(0)">Informasi Wali</a></li>
                        <li><a id="tablink" href="javascript:void(0)">Beasiswa</a></li>
                        <!--li><a id="tablink" href="javascript:void(0)">Pendidikan / Prestasi</a></li-->
                        <li><a id="tablink" href="javascript:void(0)">Lain-Lain</a></li>
					</ul>
				
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td colspan="2" class="DataBG">Data Pribadi</td>
						</tr>
						<?= Page::getDataTR($row,'sex') ?>
						<tr>
							<td class="LeftColumnBG">Kota Lahir</td>
							<td class="RightColumnBG">
								<span id="show"><?=$r_kotalahir?></span>
								<span id="edit" style="display:none">
								<?= UI::createTextBox('kotalahir',$r_kodekotalahir.' - '.$r_kotalahir,'ControlStyle',30,30) ?>
								<input type="hidden" id="kodekotalahir" name="kodekotalahir" value="<?= $r_kodekotalahir ?>">
								</span>
							</td>
						</tr>
						<?= Page::getDataTR($row,'tgllahir') ?>
						<?= Page::getDataTR($row,'goldarah') ?>
						<?= Page::getDataTR($row,'usia_1sept') ?>
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
						<?= Page::getDataTR($row,'telp,telp2',', ') ?>
						<?= Page::getDataTR($row,'hp,hp2',', ') ?>
						<?= Page::getDataTR($row,'email,email2','<div class="Break"></div>') ?>
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
							<td class="LeftColumnBG">Anak Ke-</td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'anakke') ?>
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
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td colspan="2" class="DataBG">Asal Sekolah</td>
						</tr>
						 <?= Page::getDataTR($row,'propinsismu') ?>
						<?= Page::getDataTR($row,'kodekotasmu') ?>
						<tr>
							<td class="LeftColumnBG"> <?= Page::getDataLabel($row,'asalsmu') ?></td>
							<td class="RightColumnBG"> 
								<span id="show"><?= mSmu::getNamasmu($conn,$r_asalsmu)?></span>
								<span id="edit" style="display:none">
									<?= Page::getDataInput($row,'asalsmu') ?>
									<input id="namasmu" style="display:none" type="text" size=30 maxlength="100" name="namasmu" value="<?= $r_asalsmu?>">
								</span>
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
							<td class="LeftColumnBG">Nilai Raport * </td>
							<td>
							<table border=0>
								<tr>
									<td colspan=2>Kelas X</td>
									<td colspan=2>Kelas XI</td>
									<td colspan=1>Kelas XII</td>
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
									<td><?= Page::getDataInput($row,'raport_10_1') ?></td>
									<td><?= Page::getDataInput($row,'raport_10_2') ?></td>
									<td><?= Page::getDataInput($row,'raport_11_1') ?></td>
									<td><?= Page::getDataInput($row,'raport_11_2') ?></td>
									<td><?= Page::getDataInput($row,'raport_12_1') ?></td>
									<td><?= Page::getDataInput($row,'raport_12_2') ?></td>
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
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td colspan="2" class="DataBG">Wali</td>
						</tr>
						<tr>
							<td class="LeftColumnBG">Kota Lahir Ayah</td>
							<td class="RightColumnBG">
								<span id="show"><?=$list_kota[Page::getDataValue($row,'kodekotalahirayah')];?></span>
								<span id="edit" style="display:none">
								<?= UI::createTextBox('kotalahirayah',Page::getDataValue($row,'kodekotalahirayah').' - '.$list_kota[Page::getDataValue($row,'kodekotalahirayah')],'ControlStyle',30,30) ?>
								<input type="hidden" id="kodekotalahirayah" name="kodekotalahirayah" value="<?= Page::getDataValue($row,'kodekotalahirayah') ?>">
								</span>
							</td>
						</tr>
                        <?= Page::getDataTR($row,'namaayah') ?>
						<?= Page::getDataTR($row,'kodepekerjaanayah') ?>
						<?= Page::getDataTR($row,'kodependidikanayah') ?>
						<?//= Page::getDataTR($row,'pendapatanayah') ?>
						<?= Page::getDataTR($row,'statusayahkandung') ?>
                        <?= Page::getDataTR($row,'namaibu') ?>
                        <tr>
							<td class="LeftColumnBG">Kota Lahir Ibu</td>
							<td class="RightColumnBG">
								<span id="show"><?=$list_kota[Page::getDataValue($row,'kodekotalahiribu')];?></span>
								<span id="edit" style="display:none">
								<?= UI::createTextBox('kotalahiribu',Page::getDataValue($row,'kodekotalahiribu').' - '.$list_kota[Page::getDataValue($row,'kodekotalahiribu')],'ControlStyle',30,30) ?>
								<input type="hidden" id="kodekotalahiribu" name="kodekotalahiribu" value="<?= Page::getDataValue($row,'kodekotalahiribu') ?>">
								</span>
							</td>
						</tr>
						<?= Page::getDataTR($row,'kodepekerjaanibu') ?>
						<?= Page::getDataTR($row,'kodependidikanibu') ?>
						<?//= Page::getDataTR($row,'pendapatanibu') ?>
						<?= Page::getDataTR($row,'statusibukandung') ?>
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
					</table>
					<?php
						echo '<div class="data_saudarakandung">';
							require_once('data_saudarakandung.php'); 
						echo '</div>';
					?>
					</div>              
                    <div id="items">
					<table class="table table-bordered table-striped">
						
						<tr><td colspan="3"><i>*potongan yang telah disetujui oleh petugas KUA (keuangan akademik) tidak bisa di ubah kembali</i><br></td></tr>
						<tr><td><br></td></tr>
						<?= Page::getDataTR($row,'potonganbeasiswa') ?>
						<?= Page::getDataTR($row,'keteranganpotonganbeasiswa') ?>
						<?= Page::getDataTR($row,'isvalidbeasiswa') ?>
						<tr><td><br></td></tr>						
						<?= Page::getDataTR($row,'potonganregistrasi') ?>
						<?= Page::getDataTR($row,'keteranganpotonganregistrasi') ?>
						<?= Page::getDataTR($row,'isvalidregistrasi') ?>
						<tr><td><br></td></tr>
						<?= Page::getDataTR($row,'potongansemesterpendek') ?>
						<?= Page::getDataTR($row,'keteranganpotongansemesterpendek') ?>
						<?= Page::getDataTR($row,'isvalidsemesterpendek') ?>
					</table>
					</div>
                    <div id="items">
						<table class="table table-bordered table-striped">
							<tr>
								<td colspan="2" class="DataBG">Upload Berkas</td>
							</tr>
							<tr>
								<td>File Berkas pendaftaran</td>
								<td>
									<a href="../back/uploads/berkas/<?= $r_key.'.rar'?>"> <?if($r_key!='') echo '* '.$r_key;?>.rar</a>
								</td>
							</tr>
						</table>
					</div>
                    <div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td colspan="2" class="DataBG">Data Kemampuan</td>
						</tr>
						<?= Page::getDataTR($row,'bhsarab') ?>
						<?= Page::getDataTR($row,'bhsinggris') ?>
						<?= Page::getDataTR($row,'pengkomp') ?>
					</table>	
					<? 
					if(!empty($r_key)) { 
						echo '<div class="data_pendformal">';
							require_once('data_pendformal.php'); 
						echo '</div><br>';
						echo '<div class="data_pendnonformal">';
							require_once('data_pendnonformal.php'); 
						echo '</div><br>';
						echo '<div class="data_organisasi">';
							require_once('data_organisasi.php'); 
						echo '</div><br>';
						echo '<div class="data_prestasiakad">';
							require_once('data_prestasiakad.php'); 
						echo '</div><br>';
						echo '<div class="data_prestasinonakad">';
							require_once('data_prestasinonakad.php'); 
						echo '</div><br>';
					}
					?>
					</div>
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<!--tr>
							<td colspan="2" class="DataBG">Data Kartanu</td>
						</tr>
						
						
						<?= Page::getDataTR($row,'iskartanu') ?>
						<?= Page::getDataTR($row,'namapemilikkartanu') ?>
						<?= Page::getDataTR($row,'nopemilikkartanu') ?>
						<?= Page::getDataTR($row,'hubungankartanu') ?>
						-->
						<tr>
							<td colspan="2" class="DataBG">Data Lain Lain</td>
						</tr>
						<?= Page::getDataTR($row,'tempatbekerja') ?>
						<?= Page::getDataTR($row,'biayakuliah') ?>
					</table>
					</div>
                                        
				</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				
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
var ajax = "<?= Route::navAddress("ajax") ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	initTab();
	
	loadKota();
	loadKotaOrtu();
	loadKotaSMU();
	loadKotaPonpes();
	loadKotaPTAsal();
	loadKotaKontak();
	loadSMU();
	
	//getKuota2();
	$("#kotalahir").xautox({strpost: "act=getKotaLahir", targetid: "kodekotalahir"});
	$("#kotalahirayah").xautox({strpost: "act=getKotaLahir", targetid: "kodekotalahirayah"});
	$("#kotalahiribu").xautox({strpost: "act=getKotaLahir", targetid: "kodekotalahiribu"});
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});
function numberOnly(evt) {
    evt = (evt) ? evt : window.event
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false
    }
    return true
} 
function isExist(){
    document.getElementById("checktoken").style.display='block';
    var token = document.getElementById("tokenpendaftaran").value;
    
    $.ajax({
	type: "POST",
	url: "<?= Route::navAddress('ajax') ?>",
	data: "act=token&token="+token,
	timeout: 20000,
        
	success: function(data) {
            datas = JSON.parse(data);
          //  document.getElementById("pin").value=datas.pin;
            document.getElementById("nama").value=datas.nama;
            document.getElementById("periodedaftar").value=datas.periodedaftar;
            document.getElementById("jalurpenerimaan").value=datas.jalurpenerimaan;
            document.getElementById("idgelombang").value=datas.idgelombang;
            document.getElementById("sistemkuliah").value=datas.sistemkuliah;
            document.getElementById("nopendaftar").value=datas.nopendaftar;
            //document.getElementById("nopesertaspmb").value=datas.nopesertaspmb;
	},
        error: function(obj,err) {
	    if(err == "timeout")
		alert("Token tidak dikenali.");
	}
    });
    
    document.getElementById("checktoken").style.display='none';
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
		$("#kodekotasmu").html(data);
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
		$("#asalsmu").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
} 
function getDetailSmu(){
	if (document.getElementById("asalsmu").value != ''){
		var e = document.getElementById("asalsmu");
		var str = e.options[e.selectedIndex].text;

		asalsmu = str[5];
	}
	
	if((asalsmu == '*') || ($("#asalsmu").val() == '*')){
		document.getElementById('namasmu').style.display = 'table-row';
		document.getElementById('alamatsmu').value = "";
		document.getElementById('telpsmu').value = "";
	}else{
		document.getElementById('namasmu').style.display = 'none';
		var posted = "act=getDetailSmu&q[]="+$("#asalsmu").val();
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var text = text.split('#');
			document.getElementById('alamatsmu').value = text[0];
			document.getElementById('telpsmu').value = text[1];
		});
	}
}
/*
<?php if(!$lulus){ ?>
function loadTglujian() {
<?php
    $kota = mCombo::getKotaSmu();
    while ($data = $kota->FetchRow())
    {
	$idkota = $data['kodekota'];
	echo "if (document.pageform.kotaujian.value == \"".$idkota."\")";
	echo "{";

	$tglujain = mCombo::tglUjian($idkota,$r_periode,$r_jalur,$r_gel);
        //$kota = array_values($kota);
	$content = "document.getElementById('tglujian').innerHTML = \"";
	$content .="<option value=''></option>";
		
	while($dataujian=$tglujain->FetchRow())
	{
	    // $content .= "<option value='".$dataujian['idjadwal']."'>".date('d-m-Y',strtotime($dataujian['tgltes']))."</option>";
		 if($dataujian['idjadwal'] == trim($r_idjadwal))
			$content .= "<option selected value='".$dataujian['idjadwal']."'>".date('d-m-Y',strtotime($dataujian['tgltes']))."</option>";
		else	
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
				// $content .= "<option value='".$datajam['idjadwaldetail']."'>".$datajam['jammulai']." - ".$datajam['jamselesai']."&nbsp;&nbsp;&nbsp;".$datajam['ruang']."</option>";
				if($datajam['idjadwaldetail'] == trim(Page::getDataValue($row,'idjadwaldetail')))
					$content .= "<option selected value='".$datajam['idjadwaldetail']."'>".$datajam['jammulai']." - ".$datajam['jamselesai']."&nbsp;&nbsp;&nbsp;".$datajam['ruang']."</option>";
				else	
					$content .= "<option value='".$datajam['idjadwaldetail']."'>".$datajam['jammulai']." - ".$datajam['jamselesai']."&nbsp;&nbsp;&nbsp;".$datajam['ruang']."</option>";
			}
			$content .= "\"";
			echo $content;
			echo "}\n";
		}
	?>
}
<?php }?>
function getKuota2(){
	var posted = "act=getKuota&q[]="+$("#tglujian").val();
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		var text = text.split('#');
		document.getElementById('kuota').innerHTML = text[0]+"/"+text[1];
	});
}
*/
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
</body>
</html>
