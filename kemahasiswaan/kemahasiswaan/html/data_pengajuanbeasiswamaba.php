<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//ini_set("display_errors",true);
	// hak akses
	$a_auth = Modul::getFileAuth();

	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];

//	$conn->debug = true;
	// include
	require_once(Route::getModelPath('pengajuanbeasiswa'));
	require_once(Route::getModelPath('pengajuanbeasiswapendaftar'));
	require_once(Route::getModelPath('beasiswa'));
	require_once(Route::getModelPath('tahapbeasiswa'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('berkasbeasiswamaba'));
	require_once(Route::getModelPath('jenisprestasi'));
	require_once(Route::getModelPath('tingkatprestasi'));
	require_once(Route::getModelPath('kategoriprestasi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));

	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);

	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;

	// properti halaman
	$p_title = 'Data Pengajuan Beasiswa';
	$p_tbwidth = 800;
	$p_aktivitas = 'Asuransi Mahasiswa';
	$p_listpage = Route::getListPage();

	$p_model = mPengajuanBeasiswaPd;

	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);

	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);

	// $a_beasiswa = mBeasiswa::getArrayBeasiswaPendaftar($conn);
	
	if(!empty($r_key))
		$r_idbeasiswa = $p_model::getDataField($conn,$r_key,'idbeasiswa');
	
	$a_beasiswa = mBeasiswa::getArrayBeasiswaPendaftar($conn,true,$r_idbeasiswa);
	
	$a_tahapbeasiswa = mTahapbeasiswa::getArray($conn);

	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idbeasiswa', 'label' => 'Beasiswa', 'type' => 'S', 'option' => $a_beasiswa);
	$a_input[] = array('kolom' => 'nopendaftar', 'label' => 'No Pendaftar');
	$a_input[] = array('kolom' => 'tglpengajuan', 'label' => 'Tgl Pengajuan', 'type' => 'D');
	$a_input[] = array('kolom' => 'nilairapor', 'label' => 'Rata-rata nilai Rapor Semester I s/d V', 'maxlength' => 3,'readonly' => true);
	$a_input[] = array('kolom' => 'nilaiun', 'label' => 'Nilai rata-rata UN', 'maxlength' => 3);
	$a_input[] = array('kolom' => 'penghasilanortu', 'label' => 'Penghasilan Ortu', 'type' => 'N', 'maxlength' => 10, 'notnull' => true);
	//$a_input[] = array('kolom' => 'ipk', 'label' => 'IPK', 'type' => 'N','readonly'=>true);
	//$a_input[] = array('kolom' => 'namastatus', 'label' => 'Status Mahasiswa','readonly'=>true);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'M','readonly'=> Akademik::isMhs());
	$a_input[] = array('kolom' => 'isditerima', 'label' => 'Status Beasiswa', 'type' => 'R', 'option' => array('-1' => 'Lulus', '0' => 'Tidak Lulus'),'readonly'=> Akademik::isMhs());
	$a_input[] = array('kolom' => 'tglterima', 'label' => 'Tgl Ditetapkan', 'type' => 'D','readonly'=> Akademik::isMhs());
	$a_input[] = array('kolom' => 'idtahapbeasiswa', 'label' => 'Tahap', 'type' => 'S', 'option' => $a_tahapbeasiswa);
	$a_input[] = array('kolom' => 'isdiserahkan', 'label' => 'Status Penyerahan', 'type' => 'R', 'option' => array('-1' => 'Sudah Diserahkan', '0' => 'Belum Diserahkan'), 'readonly'=> Akademik::isMhs());
	$a_input[] = array('kolom' => 'tgldiserahkan', 'label' => 'Tgl Diserahkan', 'type' => 'D', 'readonly'=> Akademik::isMhs());

	//input pendaftar
	$a_input[] = array('kolom' => 'nama',	'label' => 'Nama Pendaftar', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekampus', 'label' => 'Kampus', 'type' => 'S','option' => $arrKampus,'empty'=>false,'readonly'=>$readonly,'add'=>'onChange="loadSistemkuliah()"');
	$a_input[] = array('kolom' => 'sistemkuliah', 'label' => 'Sistem Kuliah', 'type' => 'S','option' => $arrSistemkuliah,'empty'=>false,'readonly'=>$readonly,'add'=>'onChange="loadProdiBuka()"');
	$a_input[] = array('kolom' => 'jalan', 	'label' => 'Jalan', 'maxlength' => 150, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'rt', 	'label' => 'RT', 'maxlength' => 5, 'size' => 5,'notnull' => true);
	$a_input[] = array('kolom' => 'rw', 	'label' => 'RW', 'maxlength' => 5, 'size' => 5,'notnull' => true);
	$a_input[] = array('kolom' => 'kel', 	'label' => 'Kelurahan', 'maxlength' => 20, 'size' => 50,'notnull' => true);
	$a_input[] = array('kolom' => 'kec', 	'label' => 'Kecamatan', 'maxlength' => 20, 'size' => 50,'notnull' => true);
	$a_input[] = array('kolom' => 'kodepos','label' => 'Kode Pos', 'maxlength' => 150, 'size' => 50,'notnull' => true);
	$a_input[] = array('kolom' => 'kodekota','label' => 'Kota', 'type' => 'S', 'option' => "", 'notnull' => true, 'empty'=>true,'text'=>'kodekota_text');
	$a_input[] = array('kolom' => 'kodepropinsi', 'label' => 'Propinsi', 'type' => 'S', 'notnull' => true, 'option' => $arrPropinsi,  'add' => 'onchange="loadKota()"','empty'=>true);
	$a_input[] = array('kolom' => 'nomorrumah', 'label' => 'Nomor Rumah', 'maxlength' => 4, 'size' => 4,  'add'=>'onkeypress="return numberOnly(event)"');
	$a_input[] = array('kolom' => 'isbekerja', 'label' => 'Bekerja ?', 'type' => 'S', 'notnull' => true, 'option' => $arrBekerja);
	$a_input[] = array('kolom' => 'negara', 'label' => 'Negara', 'maxlength' => 20, 'size' => 15, 'notnull' => true);
	$a_input[] = array('kolom' => 'iskelainanfisik', 'label' => 'Kelainan Fisik', 'type' => 'S', 'notnull' => false, 'option' => $arrBekerja);
	$a_input[] = array('kolom' => 'keteranganfisik', 'label' => 'Keterangan Kelainan Fisik', 'maxlength' => 255, 'size' => 50, 'notnull' => false, 'type'=>'A');

	$a_input[] = array('kolom' => 'telp', 	'label' => 'Telp', 'maxlength' => 15, 'size' => 15, 'notnull' => false);
	$a_input[] = array('kolom' => 'hp', 	'label' => 'Hp', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
	$a_input[] = array('kolom' => 'hp2', 	'label' => 'Hp2', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'email', 	'label' => 'Email', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodewn', 'label' => 'Kewarganegaraan', 'type' => 'S', 'notnull' => true, 'option' => mCombo::wargaNegara(),'empty'=>false);
	$a_input[] = array('kolom' => 'sex', 	'label' => 'Jenis Kelamin', 'type' => 'S', 'option' => mCombo::jenisKelamin(),'empty'=>false, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodeagama', 'label' => 'Agama', 'type' => 'S', 'notnull' => true, 'option' => $arrAgama,'empty'=>false);
	$a_input[] = array('kolom' => 'statusnikah', 'label' => 'Status Nikah', 'type' => 'S', 'option' => mCombo::statusNikah(),'empty'=>false,'notnull' => true);
	$a_input[] = array('kolom' => 'tgllahir','label' => 'Tgl Lahir', 'type' => 'D', 'notnull' => true,'add'=>'readonly="readonly" class="readonly"');
	$a_input[] = array('kolom' => 'kodepropinsilahir', 'label' => 'TTL Propinsi, kota, tanggal lahir', 'type' => 'S', 'option' => $arrPropinsi, 'empty' => true, 'add' => 'onchange="loadKotaLahir()"', 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekotalahir', 'label' => 'Kota Lahir', 'type' => 'S', 'option' => "", 'notnull' => true, 'empty'=>true,'text'=>'kodekotalahir_text');

	$a_input[] = array('kolom' => 'pilihan1', 'label' => 'Pilihan 1', 'type' => 'S', 'notnull' => true, 'option' => $arrJurusan, 'empty' => true,'add' => 'onchange="loadProdiBuka2()"','readonly'=>$readonly);
	$a_input[] = array('kolom' => 'pilihan2', 'label' => 'Pilihan 2', 'type' => 'S', 'option' => $arrJurusan, 'empty' => true,'readonly'=>$readonly);
	$a_input[] = array('kolom' => 'pilihan3', 'label' => 'Pilihan 3', 'type' => 'S', 'option' => $arrJurusan, 'empty' => true,'readonly'=>$readonly);

	//data sekolah
	$a_input[] = array('kolom' => 'xasalsmu', 'label' => 'Nama Sekolah','type' => 'S', 'notnull' => true,'option'=>'');
	$a_input[] = array('kolom' => 'asalsmu', 'label' => 'Nama Sekolah','type' => 'S','option'=>'');
	$a_input[] = array('kolom' => 'jurusansmaasal', 'label' => 'Jurusan', 'maxlength' => 30, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'alamatsmu', 'label' => 'Alamat Sekolah', 'maxlength' => 60, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekotasmu', 'label' => 'Kota Sekolah', 'type' => 'S', 'notnull' => true, 'option' =>"",'add'=>'onChange="loadSMU()"','maxlength' => 20,'text'=>'kodekotasmu_text');
	$a_input[] = array('kolom' => 'propinsismu', 'label' => 'Propinsi Sekolah ', 'type' => 'S', 'notnull' => true, 'option' => $arrPropinsi, 'add' => 'onchange="loadKotaSMU()"','empty'=>true);
	$a_input[] = array('kolom' => 'telpsmu', 'label' => 'Telp Sekolah', 'maxlength' => 15, 'notnull' => false, 'size' => 15);
	$a_input[] = array('kolom' => 'thnlulussmaasal', 'label' => 'Tahun Lulus', 'maxlength' => 4, 'size' => 5, 'notnull' => true,'add'=>'onkeypress="return numberOnly(event)"');

	$a_input[] = array('kolom' => 'thnmasuksmaasal', 'label' => 'Tahun Masuk', 'maxlength' => 4, 'size' => 4,'notnull' => true,'add'=>'onkeypress="return numberOnly(event)"');
	$a_input[] = array('kolom' => 'nemsmu',	'label' => 'Nilai UN', 'type' => 'N,2', 'notnull' => true, 'maxlength' => 6, 'size' => 6);
	$a_input[] = array('kolom' => 'noijasahsmu', 'label' => 'No Ijasah SMU', 'maxlength' => 20, 'notnull' => true, 'size' => 20);
	$a_input[] = array('kolom' => 'kodesekolah', 'label' => 'Kode Sekolah', 'maxlength' => 10, 'notnull' => true, 'size' => 10);
	$a_input[] = array('kolom' => 'kodepossekolah','label' => 'Kode Pos Sekolah', 'maxlength' => 6, 'size' => 6,'notnull' => false);
	$a_input[] = array('kolom' => 'jenissekolah', 'label' => 'Jenis Sekolah', 'type' => 'S', 'option' => $arrJenissekolah, 'empty' => false);
	$a_input[] = array('kolom' => 'kodeagamasekolah', 'label' => 'Basis Agama Sekolah', 'type' => 'S', 'notnull' => true, 'option' => $arrAgama,'empty'=>false);
	$a_input[] = array('kolom' => 'negarasekolah', 'label' => 'Negara Sekolah', 'maxlength' => 20, 'size' => 15, 'notnull' => true);

	//pekerjaan
	$a_input[] = array('kolom' => 'namaperusahaan', 	'label' => 'nama tempat bekerja', 'maxlength' => 50, 'size' => 15,'notnull' => false);
	$a_input[] = array('kolom' => 'alamatperusahaan', 'label' => 'Alamat Kantor', 'maxlength' => 200, 'size' => 50, 'notnull' => false);
	$a_input[] = array('kolom' => 'nomorkantor', 'label' => 'Nomor Kantor', 'maxlength' => 4, 'size' => 4,  'add'=>'onkeypress="return numberOnly(event)"');
	$a_input[] = array('kolom' => 'rtkantor', 	'label' => 'RT Kantor', 'maxlength' => 5, 'size' => 5,'notnull' => false);
	$a_input[] = array('kolom' => 'rwkantor', 	'label' => 'RW Kantor', 'maxlength' => 5, 'size' => 5,'notnull' => false);
	$a_input[] = array('kolom' => 'kelkantor', 	'label' => 'Kelurahan Kantor', 'maxlength' => 20, 'size' =>20,'notnull' => false);
	$a_input[] = array('kolom' => 'kodekotakantor', 'label' => 'Kota', 'type' => 'S', 'notnull' => false, 'option' =>"",'text'=>'kodekotakantor_text');
	$a_input[] = array('kolom' => 'kodepropinsikantor', 'label' => 'Propinsi ', 'type' => 'S', 'notnull' => false, 'option' => $arrPropinsi, 'empty'=>true,'add' => 'onchange="loadKotaKantor()"');
	$a_input[] = array('kolom' => 'jabatankerja', 	'label' => 'Jabatan', 'maxlength' => 50, 'size' => 15,'notnull' => false);
	$a_input[] = array('kolom' => 'bagian', 	'label' => 'bagian', 'maxlength' => 50, 'size' => 15,'notnull' => false);
	$a_input[] = array('kolom' => 'telpkantor', 	'label' => 'Telp', 'maxlength' => 15, 'size' => 15, 'notnull' => false);
	$a_input[] = array('kolom' => 'hpkantor', 	'label' => 'Hp', 'maxlength' => 15, 'size' => 15, 'notnull' => false);
	$a_input[] = array('kolom' => 'thnmasuk', 'label' => 'Bekerja sejak tahun?', 'maxlength' => 4, 'size' => 4, 'notnull' => false,'add'=>'onkeypress="return numberOnly(event)"');

	//orang tua wali
	$a_input[] = array('kolom' => 'namaayah', 'label' => 'Nama', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepekerjaanayah', 'label' => 'Pekerjaan', 'type' => 'S', 'notnull' => true, 'option' => $arrPekerjaan, 'empty' => true);
	$a_input[] = array('kolom' => 'kodependidikanayah', 'label' => 'Pendidikan', 'type' => 'S', 'notnull' => true, 'option' => $arrPendidikan, 'empty' => true);
	$a_input[] = array('kolom' => 'jeniswali', 'label' => 'Jenis', 'type' => 'S', 'option' => array(1=>'Ayah',2=>'Wali'), 'empty' => false);
	$a_input[] = array('kolom' => 'statuswali', 'label' => 'Status', 'type' => 'S', 'option' => array(1=>'Masih Hidup',2=>'Meninggal'), 'empty' => false);
	$a_input[] = array('kolom' => 'alamatayah', 'label' => 'Alamat', 'maxlength' => 200, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'nomorrumahayah', 'label' => 'Nomor Rumah', 'maxlength' => 4, 'size' => 4,  'add'=>'onkeypress="return numberOnly(event)"');
	$a_input[] = array('kolom' => 'rtayah', 	'label' => 'RT', 'maxlength' => 5, 'size' => 5,'notnull' => true);
	$a_input[] = array('kolom' => 'rwayah', 	'label' => 'RW', 'maxlength' => 5, 'size' => 5,'notnull' => true);
	$a_input[] = array('kolom' => 'kelayah', 	'label' => 'Kelurahan', 'maxlength' => 20, 'size' =>20,'notnull' => true);
	$a_input[] = array('kolom' => 'kecayah', 	'label' => 'Kecamatan', 'maxlength' => 20, 'size' =>20,'notnull' => true);
	$a_input[] = array('kolom' => 'kodekotaayah', 'label' => 'Kota', 'type' => 'S', 'notnull' => true, 'option' =>"",'text'=>'kodekotaayah_text');
	$a_input[] = array('kolom' => 'kodepropinsiayah', 'label' => 'Propinsi ', 'type' => 'S', 'notnull' => true, 'option' => $arrPropinsi, 'empty'=>true,'add' => 'onchange="loadKotaayah()"');
	$a_input[] = array('kolom' => 'telpayah', 	'label' => 'Telp', 'maxlength' => 15, 'size' => 15, 'notnull' => false);
	$a_input[] = array('kolom' => 'hpayah', 	'label' => 'Hp', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
	$a_input[] = array('kolom' => 'emailayah', 	'label' => 'Email', 'maxlength' => 50, 'size' => 30, 'notnull' => false);
	$a_input[] = array('kolom' => 'jabatankerjaayah', 	'label' => 'Jabatan', 'maxlength' => 50, 'size' => 15,'notnull' => false);
	$a_input[] = array('kolom' => 'namaperusahaanayah', 	'label' => 'nama tempat bekerja', 'maxlength' => 50, 'size' => 15,'notnull' => false);

	//nama ibu
	$a_input[] = array('kolom' => 'namaibu', 'label' => 'Nama Ibu', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepekerjaanibu', 'label' => 'Pekerjaan Ibu', 'type' => 'S', 'notnull' => true, 'option' => $arrPekerjaan, 'empty' => true);
	$a_input[] = array('kolom' => 'kodependidikanibu', 'label' => 'Pendidikan Ibu', 'type' => 'S', 'notnull' => true, 'option' => $arrPendidikan, 'empty' => true);
	$a_input[] = array('kolom' => 'statusibu', 'label' => 'Status', 'type' => 'S', 'option' => array(1=>'Masih Hidup',2=>'Meninggal'), 'empty' => false);
	$a_input[] = array('kolom' => 'alamatibu', 'label' => 'Alamat', 'maxlength' => 200, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'nomorrumahibu', 'label' => 'Nomor Rumah', 'maxlength' => 4, 'size' => 4, 'add'=>'onkeypress="return numberOnly(event)"');
	$a_input[] = array('kolom' => 'rtibu', 	'label' => 'RT', 'maxlength' => 5, 'size' => 5,'notnull' => true);
	$a_input[] = array('kolom' => 'rwibu', 	'label' => 'RW', 'maxlength' => 5, 'size' => 5,'notnull' => true);
	$a_input[] = array('kolom' => 'kelibu', 	'label' => 'Kelurahan', 'maxlength' => 20, 'size' =>20,'notnull' => true);
	$a_input[] = array('kolom' => 'kecibu', 	'label' => 'Kecamatan', 'maxlength' => 20, 'size' =>20,'notnull' => true);
	$a_input[] = array('kolom' => 'kodekotaibu', 'label' => 'Kota', 'type' => 'S', 'notnull' => true, 'option' =>"",'text'=>'kodekotaibu_text');
	$a_input[] = array('kolom' => 'kodepropinsiibu', 'label' => 'Propinsi ', 'type' => 'S', 'notnull' => true, 'option' => $arrPropinsi, 'empty'=>true,'add' => 'onchange="loadKotaibu()"');
	$a_input[] = array('kolom' => 'telpibu', 	'label' => 'Telp', 'maxlength' => 15, 'size' => 15, 'notnull' => false);
	$a_input[] = array('kolom' => 'hpibu', 	'label' => 'Hp', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
	$a_input[] = array('kolom' => 'emailibu', 	'label' => 'Email', 'maxlength' => 50, 'size' => 30, 'notnull' => false);
	$a_input[] = array('kolom' => 'jabatankerjaibu', 	'label' => 'Jabatan', 'maxlength' => 50, 'size' => 15,'notnull' => false);
	$a_input[] = array('kolom' => 'namaperusahaanibu', 	'label' => 'Perusahaan', 'maxlength' => 50, 'size' => 15,'notnull' => false);

	$a_input[] = array('kolom' => 'kodependapatanortu', 'label' => 'Pendapatan ortu ', 'type' => 'S', 'notnull' => true, 'option' => $arrPendapatan, 'empty' => true);

	//perguruan tinggi
	$a_input[] = array('kolom' => 'mhstransfer', 'label' => 'Status Asal ?', 'type' => 'R', 'option' => array('0' => 'SMU/SMK dan sejenisnya','-1' => 'D3/Pindahan'), 'add' => 'onChange="disabledMhst();"', 'default'=>'1','notnull'=>true);
	$a_input[] = array('kolom' => 'ptasal', 'label' => 'Universitas Asal', 'maxlength' => 50, 'size' => 40);
	$a_input[] = array('kolom' => 'propinsiptasal', 'label' => 'Propinsi universitas ', 'type' => 'S', 'option' => $arrPropinsi, 'add' => 'onchange="loadKotaPTAsal()"', 'empty'=>true);
	$a_input[] = array('kolom' => 'kodekotapt', 'label' => 'Kota Universitas', 'type' => 'S', 'option' =>"",'empty'=>true,'text'=>'kodekotapt_text');
	$a_input[] = array('kolom' => 'ptipk', 'label' => 'IPK', 'maxlength' => 4, 'size' => 4);
	$a_input[] = array('kolom' => 'ptthnlulus', 'label' => 'Tahun Lulus', 'maxlength' => 4, 'size' => 4,'add'=>'onkeypress="return numberOnly(event)"');
	$a_input[] = array('kolom' => 'ptthnmasuk', 'label' => 'Tahun Masuk', 'maxlength' => 4, 'size' => 4,'add'=>'onkeypress="return numberOnly(event)"');
	$a_input[] = array('kolom' => 'sksasal', 'label' => 'SKS', 'maxlength' => 3, 'size' => 4);
	$a_input[] = array('kolom' => 'semesterkeluar', 'label' => 'Semester Lulus', 'maxlength' => 3, 'size' => 4);
	$a_input[] = array('kolom' => 'negaraptasal', 'label' => 'Negara', 'maxlength' => 20, 'size' => 20);
	$a_input[] = array('kolom' => 'ptfakultas', 'label' => 'Fakultas', 'maxlength' => 20, 'size' => 20);
	$a_input[] = array('kolom' => 'ptjurusan', 'label' => 'Jurusan', 'maxlength' => 50, 'size' => 40);

	$a_input[] = array('kolom' => 'keteranganpotonganbeasiswa', 'label' => 'Keterangan Potongan Beasiswa', 'maxlength' => 200, 'type'=>'A','readonly'=>$beasiswa == '-1' ? true : false);
	$a_input[] = array('kolom' => 'keteranganpotonganregistrasi', 'label' => 'Keterangan Potongan Registrasi', 'maxlength' => 200, 'type'=>'A','readonly'=>$registrasi == '-1' ? true : false);
	$a_input[] = array('kolom' => 'keteranganpotongansemesterpendek', 'label' => 'Keterangan Potongan Semester Pendek', 'maxlength' => 200, 'type'=>'A','readonly'=>$semesterpendek == '-1' ? true : false);
	$a_input[] = array('kolom' => 'isvalidbeasiswa', 'label' => 'Validasi Potongan Beasiswa', 'type'=>'S', 'option'=>array(0=>'Tidak disetujui', -1=>'Disetujui'),'readonly'=>true);
	$a_input[] = array('kolom' => 'isvalidregistrasi', 'label' => 'Validasi Potongan Registrasi', 'type'=>'S', 'option'=>array(0=>'Tidak disetujui', -1=>'Disetujui'),'readonly'=>true);
	$a_input[] = array('kolom' => 'isvalidsemesterpendek', 'label' => 'Validasi Potongan Semester Pendek', 'type'=>'S', 'option'=>array(0=>'Tidak disetujui', -1=>'Disetujui'),'readonly'=>true);

	//alasan memilih UEU
	$a_input[] = array('kolom'=>'raport_10_1', 'label'=>'Raport 10_1','maxlength' => 5, 'size' => 5,'add'=>'style="width:50px"');
	$a_input[] = array('kolom'=>'raport_10_2', 'label'=>'Raport 10_2','maxlength' => 5, 'size' => 5,'add'=>'style="width:50px"');
	$a_input[] = array('kolom'=>'raport_11_1', 'label'=>'Raport 11_1','maxlength' => 5, 'size' => 5,'add'=>'style="width:50px"');
	$a_input[] = array('kolom'=>'raport_11_2', 'label'=>'Raport 11_2','maxlength' => 5, 'size' => 5,'add'=>'style="width:50px"');
	$a_input[] = array('kolom'=>'raport_12_1', 'label'=>'Raport 12_1','maxlength' => 5, 'size' => 5,'add'=>'style="width:50px"');

	// mengambil data pelengkap
	$a_detail = array();

	$t_detail = array();
	$t_detail[] = array('kolom' => 'namasyaratbeasiswa', 'label' => 'Nama Syarat');
	$t_detail[] = array('kolom' => 'qty', 'label' => 'Qty');

	$a_detail['syarat'] = array('key' => mPengajuanBeasiswaPd::getDetailInfo('syarat','key'), 'data' => $t_detail);

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$conn->BeginTrans();

		list($post,$record) = uForm::getPostRecord($a_input,$_POST);

		if(empty($post['isvalid']))
			$record['isvalid'] = 0;

		if(empty($post['isditerima']))
			$record['isditerima'] = 0;

		foreach ($a_input as $key => $value) {
			if($value['type']=='M')
				$record[$value['kolom'].':skip'] = true;
		}

		/* $sisakuota = mBeasiswa::getJumlahPenerimaPndf($conn,$_POST['idbeasiswa']);

		if (!empty($sisakuota['kuota'])) {
			if ($sisakuota['jml'] == $sisakuota['kuota']) {
				$p_posterr = true ;
				$p_postmsg = "Kuota Beasiswa Penuh" ;
			}

		}

		if (!$p_posterr) { */
			if(empty($r_key)) {
				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
			}
			else {
				//delete dulu syarat
				list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_key,'syarat');

				//insert syarat
				if(!empty($_POST['a_syaratmhs'])){
					$recd['idpengajuanbeasiswa'] = $r_key;
					$recd['idbeasiswa'] = $_POST['idbeasiswa'];

					foreach($_POST['a_syaratmhs'] as $val){
						$recd['kodesyaratbeasiswa'] = $val;
						list($p_posterr,$p_postmsg) = $p_model::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$recd,'syarat');

						if(!empty($p_posterr))
							break;
					}
				}
				//insert tabel utama
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
			}
		// }

		// cek kuota, jumlahpenerima diset di trigger
		if(empty($p_posterr) and !empty($record['isditerima'])) {
			$rowb = mBeasiswa::getData($conn,$record['idbeasiswa']);
			if($rowb['jumlahpenerima'] > $rowb['jumlahbeasiswa']) {
				$p_posterr = true;
				$p_postmsg = 'Kuota Beasiswa telah terpenuhi';
			}
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

		$record = array('idasuransi' => $r_key);
		foreach($a_detail[$r_detail]['data'] as $t_detail) {
			$t_value = $_POST[$t_detail['kolom']];
			$record[$t_detail['kolom']] = CStr::cStrNull($t_value);
		}

		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = mSyaratasuransi::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail);
	}
	else if($r_act == 'deletedet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkey = CStr::removeSpecial($_POST['subkey']);

		list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkey,$r_detail);
	}else if($r_act == 'upload' and $c_edit) {
		$tipe=array('image/jpeg','image/jpg','image/gif','image/png','application/pdf');
		$ext=array('image/jpg'=>'jpg','image/jpeg'=>'jpg','image/gif'=>'gif','image/png'=>'png','application/pdf'=>'pdf');
		list($idpengajuanbeasiswa,$kodesyaratbeasiswa,$idbeasiswa) = explode("|",$_POST['subkey']);

		$file_types=$_FILES['fileberkas']['type'];
		$file_nama = $_FILES['fileberkas']['name'];
		$file_ext = pathinfo($file_nama,PATHINFO_EXTENSION);
		if($file_ext != $ext[$file_types])
			$file_nama .= '.'.$ext[$file_types];

		$file_name=str_replace('|',';',$_POST['subkey']).'.'.$ext[$file_types];

		if(in_array($file_types,$tipe) && !empty($tipe)){
			$upload=move_uploaded_file($_FILES['fileberkas']['tmp_name'],'uploads/syaratbeasiswa/'.$file_name);
			if($upload){
				$recordu=array();
				$recordu['idpengajuanbeasiswa']=$idpengajuanbeasiswa;
				$recordu['kodesyaratbeasiswa']=$kodesyaratbeasiswa;
				$recordu['idbeasiswa']=$idbeasiswa;
				$recordu['fileberkas']=$file_nama;

				//delete berkas
				list($p_posterr,$p_postmsg) = mBerkasBeasiswaMaba::delete($conn,$_POST['subkey']);
				//insert berkas
				list($p_posterr,$p_postmsg) = mBerkasBeasiswaMaba::insertRecord($conn,$recordu);

			}else{
				$p_posterr=true;
				$p_postmsg='Upload Gagal';
			}
		}else{
			$p_posterr=true;
			$p_postmsg='Pastikan Tipe File Berupa Gambar/Pdf, Upload Gagal';
		}
	}else if($r_act == 'deletefile' and $c_edit){

		$file = $conf['upload_dir'].'syaratbeasiswa/'.str_replace('|',';',$_POST['subkey']).'.'.$_POST['filetype'];

		$ok = @unlink($file);
		if($ok)
			list($p_posterr,$p_postmsg) = mBerkasBeasiswaMaba::delete($conn,$_POST['subkey']);
		else{
			$p_posterr=true;
			$p_postmsg='File Gagal Dihapus.';
		}
	}



	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);

	if(!empty($r_key)) {
		$r_mahasiswa = Page::getDataValue($row,'nopendaftar');
		$r_namamahasiswa = $r_mahasiswa.' - '.mMahasiswa::getNamaPendaftar($conn,$r_mahasiswa);

		$r_idbeasiswa = Page::getDataValue($row,'idbeasiswa');
		$rowd = $p_model::getSyarat($conn,$r_idbeasiswa,$r_key);
	}

	$arrPropinsi = mCombo::propinsi($conn);
	$arrKota= mCombo::getKota();
	$arrJurusan = mCombo::jurusan_spmb($conn,$r_jalur, $r_periode, $r_gel);
	$arrAgama = mCombo::agama($conn);
	$arrWali = array(1=>'Ayah',2=>'Wali');
	$arrStatusWali = array(1=>'Masih Hidup',2=>'Meninggal');
	$arrPekerjaan = mCombo::pekerjaan($conn);
	$arrPendidikan = mCombo::pendidikan($conn);
	$arrPendapatan = mCombo::pendapatan($conn);

?>

<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
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
			<form name="pageform" id="pageform" method="post"  enctype="multipart/form-data">
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

						// karena tampilan custom
						$a_required = array('penghasilanortu');
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<?php
							if(Akademik::isMhs())
							{
						?>
								<tr>
									<td class="LeftColumnBG">Pendaftar</td>
									<td class="RightColumnBG"><?=$r_namamahasiswa?></td>
								</tr>
								<input type="hidden" name="nim" id="nim" value="<?=$r_mahasiswa?>">

						<?php
					}else{
						?>

						<tr>
							<td class="LeftColumnBG">Pendaftar <span id="edit" style="display:none">*</span></td>
							<td class="RightColumnBG">
								<?= Page::getDataInputWrap($r_namamahasiswa,
									UI::createTextBox('mahasiswa',$r_namamahasiswa,'ControlStyle',30,30)) ?>
								<input type="hidden" name="nopendaftar" id="nopendaftar" value="<?=$r_mahasiswa?>">
							</td>
						</tr>

						<? // Page::getDataTR($row,'nopendaftar') ?>
					<?php } ?>
						<?= Page::getDataTR($row,'idbeasiswa') ?>
						<?php

						 ?>
						<? // Page::getDataTR($row,'nemsmu') ?>
						<? // Page::getDataTR($row,'penghasilanortu') ?>
						<?= Akademik::isMhs()?'':Page::getDataTR($row,'idtahapbeasiswa') ?>
						<?= Page::getDataTR($row,'isditerima') ?>
						<?= Page::getDataTR($row,'tglterima') ?>
						<?= Page::getDataTR($row,'keterangan') ?>
						<?= Page::getDataTR($row,'isdiserahkan') ?>
						<?= Page::getDataTR($row,'tgldiserahkan') ?>
					</table>
					<br />
					<div class="tabs" style="width:<?= $p_tbwidth-22 ?>px">
						<ul>
							<li><a href="#pilihan" id="tablink">Pilihan Jurusan</a></li>
							<li><a href="#biodata" id="tablink">Data Biodata</a></li>
							<li><a href="#informasi" id="tablink">Data Keluarga</a></li>
							<li><a href="#prestasi" id="tablink">Prestasi</a></li>
							<li><a href="#organisasi" id="tablink">Organisasi</a></li>
							<li><a href="#pelatihan" id="tablink">Pelatihan</a></li>
							<li><a href="#kerja" id="tablink">Pengalaman Kerja</a></li>
							<li><a href="#riwayatpendidikan" id="tablink">Riwayat Pendidikan</a></li>
							<li><a href="#potensi" id="tablink">Data Potensi Diri</a></li>
							<li><a href="#syarat" id="tablink">Syarat</a></li>
						</ul>
						<div id="items">
							<div id="v-alasanpd"></div>
						</div>
						<? require_once($conf['view_dir'].'xinc_tabbiodata.php'); ?>
						<? require_once($conf['view_dir'].'xinc_tabinformasi.php'); ?>
						<div id="items">
							<div id="v-prestasi"></div>
						</div>
						<div id="items">
							<div id="v-organisasi"></div>
						</div>
						<div id="items">
							<div id="v-pelatihan"></div>
						</div>
						<div id="items">
							<div id="v-kerja"></div>
						</div>
						<div id="items">
							<div id="v-riwayatpd"></div>
						</div>
						<div id="items">
							<div id="v-potensi"></div>
						</div>
						<div id="items">
							<? if(!empty($r_key)) { ?>
							<br>
							<?	/**********/
								/* DETAIL */
								/**********/

								$t_field = 'syarat';
								$t_colspan = count($a_detail[$t_field]['data'])+3;
								$t_dkey = $a_detail[$t_field]['key'];

								if(!is_array($t_dkey))
									$t_dkey = explode(',',$t_dkey);

							?>
							<table width="100%" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
								<tr>
									<td colspan="<?= $t_colspan ?>" class="DataBG">Daftar Syarat</td>
								</tr>
								<tr>
									<th align="center" class="HeaderBG" width="30">No</th>
								<?	foreach($a_detail[$t_field]['data'] as $datakolom) { ?>
									<th align="center" class="HeaderBG"><?= $datakolom['label'] ?></th>
								<?	} ?>
									<th align="center" class="HeaderBG"> File</th>
									<th align="center" class="HeaderBG" width="30" colspan="2"> Check</th>
								</tr>
								<?	$i = 0;
									if(!empty($rowd)) {
										foreach($rowd as $rowdd) {
											if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;

											$t_keyrow = array();
											foreach($t_dkey as $t_key)
												$t_keyrow[] = $rowdd[trim($t_key)];

											$t_key = implode('|',$t_keyrow);
								?>
								<tr valign="top" class="<?= $rowstyle ?>">
									<td><?= $i ?></td>
								<?		foreach($a_detail[$t_field]['data'] as $datakolom) {
									?>
									<td <?= empty($datakolom['align']) ? '' : ' align="'.$datakolom['align'].'"' ?>><?=$rowdd[$datakolom['kolom']]?></td>
								<?		} ?>
									<td>
										<?php
										if(!empty($rowdd['isberkas'])) {
											if(!empty($rowdd['fileberkas'])) {
										?>
										<a href="<?=$conf['upload_dir'].'syaratbeasiswa/'.$r_key.';'.$rowdd['kodesyaratbeasiswa'].';'.$r_idbeasiswa.'.'.end(explode('.',$rowdd['fileberkas']))?>" target="_blank"><?=$rowdd['fileberkas']?></a>
										<? /*
										<u class="ULink" data-id="<?=$r_key.'|'.$rowdd['kodesyaratbeasiswa'].'|'.$r_idbeasiswa?>" data-type="<?=end(explode('.',$rowdd['fileberkas']))?>" onclick="goDeleteFile(this)">Hapus file</u>
										<?php
										*/
											}
										?>
										<div id="edit" style="display:none;margin-top:5px">
											<input type="file" name="fileberkas" data-id="<?=$r_key.'|'.$rowdd['kodesyaratbeasiswa'].'|'.$r_idbeasiswa?>" size="30" class="ControlStyle">
											<br />(image dan pdf,ukuran maks 2 MB)
											<br /><input type="button" data-id="<?=$r_key.'|'.$rowdd['kodesyaratbeasiswa'].'|'.$r_idbeasiswa?>" value="Upload" onclick="goUpload(this)">
										</div>
										<?php
										} ?>
									</td>
									<td align="center">
										<span id="show">
											<?php
												if(!empty($rowdd['syarat']))
													echo '<img src="images/check.png">';
												else
													echo '';
											?>
										</span>
										<span id="edit" style="display: none;">
										<?php
										if(Akademik::isMhs()){
											if(!empty($rowdd['syarat']))
														echo '<img src="images/check.png">';
													else
														echo '';
											} else { ?>
												<input id="a_syaratmhs[]" name="a_syaratmhs[]" value="<?= $rowdd['kodesyaratbeasiswa'] ?>" type="checkbox"  <?= ($rowdd['syarat'] == 1)? ' checked' : '' ?> >
										<?php } ?>
										</span>

									</td>
								</tr>
								<?
									}
									}
									if($i == 0) { ?>
								<tr>
									<td align="center" colspan="<?= $t_colspan ?>">Data kosong</td>
								</tr>
								<?	} ?>
							</table>
							<? } ?>
						</div>

					</div>
				</center>

				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="filetype" id="filetype">
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
	initTab();
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	// autocomplete
	$("#mahasiswa").xautox({strpost: "f=acpendaftar", targetid: "nopendaftar"});
});

function goSave() {
	var pass = true;
	if(typeof(required) != "undefined") {
		if(!cfHighlight(required))
			pass = false;
	}

	// cek nopendaftar
	var cek = $("#nopendaftar");
	if(cek.length > 0 && cek.val() == "") {
		doHighlight($("#mahasiswa").get(0));

		if(pass) {
			alert("Mohon mengisi isian-isian yang berwana kuning dengan benar terlebih dahulu.");
			pass = false;
		}
	}

	if(pass) {
		document.getElementById("act").value = "save";
		goSubmit();
	}
}
</script>

<script>
<?php
	$r_idpengajuan = $r_key;
	$r_nopendaftar = Page::getDataValue($row,'nopendaftar');
?>

loadRiwayatPd();
loadAlasanPd();
loadPrestasi();
loadOrganisasi();
loadPelatihan();
loadKerja();
loadBiodata();
loadPotensi();

function loadRiwayatPd(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_idpengajuan ?>";
	param[2] = "<?= $r_nopendaftar ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadriwayatpd", q: param }
				});

	jqxhr.done(function(data) {
		$("#v-riwayatpd").html(data);
    });
    /* jqxhr.fail(function(xhr,status) {
		alert(status);
	}); */
}
function loadAlasanPd(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_idpengajuan ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadalasanpd", q: param }
				});

	jqxhr.done(function(data) {
		$("#v-alasanpd").html(data);
    });
    /* jqxhr.fail(function(xhr,status) {
		alert(status);
	}); */
}
function loadPrestasi(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_idpengajuan ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadprestasibsmaba", q: param }
				});

	jqxhr.done(function(data) {
		$("#v-prestasi").html(data);
    });
    /* jqxhr.fail(function(xhr,status) {
		alert(status);
	}); */
}
function loadOrganisasi(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_idpengajuan ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadorganisasi", q: param }
				});

	jqxhr.done(function(data) {
		$("#v-organisasi").html(data);
    });
    /* jqxhr.fail(function(xhr,status) {
		alert(status);
	}); */
}
function loadPelatihan(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_idpengajuan ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadpelatihan", q: param }
				});

	jqxhr.done(function(data) {
		$("#v-pelatihan").html(data);
    });
    /* jqxhr.fail(function(xhr,status) {
		alert(status);
	}); */
}
function loadKerja(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_idpengajuan ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadkerja", q: param }
				});

	jqxhr.done(function(data) {
		$("#v-kerja").html(data);
    });
    /* jqxhr.fail(function(xhr,status) {
		alert(status);
	}); */
}
function loadBiodata(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_idpengajuan ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadbiodata", q: param }
				});

	jqxhr.done(function(data) {
		$("#biodata-beasiswa").html(data);
    });
    /* jqxhr.fail(function(xhr,status) {
		alert(status);
	}); */
}
function loadPotensi(){
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?= $r_idpengajuan ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadpotensi", q: param }
				});

	jqxhr.done(function(data) {
		$("#v-potensi").html(data);
    });
    /* jqxhr.fail(function(xhr,status) {
		alert(status);
	}); */
}
</script>
</body>
</html>
