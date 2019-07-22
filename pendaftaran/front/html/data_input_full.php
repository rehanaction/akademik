<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	        
        require_once($conf['model_dir'].'m_token.php');
        require_once($conf['model_dir'].'m_combo.php');
        require_once($conf['model_dir'].'m_pendaftar.php');
        require_once ($conf['helpers_dir'].'route.class.php');
        require_once ($conf['helpers_dir'].'pendaftaran.class.php');
        require_once ($conf['helpers_dir'].'date.class.php');
        
	// include
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$c_upload = false;
	$c_insert = false;
	$c_delete = false;
	$c_readlist = true;
	// $conn->debug = true;
	/*
	// variabel request
	$r_self = (int)$_REQUEST['self'];
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getUserName();
	*/
	//$r_key=$_SESSION[SITE_ID]['PENDAFTAR']['tokenpendaftaran'];
        $r_token=$_SESSION[SITE_ID]['PENDAFTAR']['tokenpendaftaran'];
	if((empty($r_key)) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Pendaftar';
	$p_tbwidth = 550;
	$p_aktivitas = 'bio';
	$p_listpage = Route::getListPage();
	
	$p_model = mPendaftar;
	/*
        // hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
        */        
	// struktur view
    $r_act = $_POST['act'];
	$r_periode = $_REQUEST['periode'];
	$r_gel = $_REQUEST['gel'];
	$r_jalur = $_REQUEST['jalur'];
        
	if(empty($r_key))
		$p_edit = false;
	else
		$p_edit = true;
	
	$a_input = array();
	
    $a_input[] = array('kolom' => 'gelardepan', 'label' => 'Gelar Depan', 'maxlength' => 10, 'size' => 5);
	$a_input[] = array('kolom' => 'nama', 'label' => 'Nama Pendaftar', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
    $a_input[] = array('kolom' => 'gelarbelakang', 'label' => 'Gelar Belakang', 'maxlength' => 10, 'size' => 5);
	
    $a_input[] = array('kolom' => 'sex', 'label' => 'Jenis Kelamin', 'type' => 'S', 'option' => mCombo::jenisKelamin(),'empty'=>true, 'notnull' => true);
    $a_input[] = array('kolom' => 'tingkatpelatihan', 'label' => 'Tingkat Pelatihan', 'type' => 'S', 'option' => mCombo::tkpelatihan(),'empty'=>true, 'notnull' => false);
    $a_input[] = array('kolom' => 'tingkatakad', 'label' => 'Tingkat Prestasi Akademik', 'type' => 'S', 'option' => mCombo::tkpelatihan(),'empty'=>true, 'notnull' => false);
    $a_input[] = array('kolom' => 'tingkatnonakad', 'label' => 'Tingkat Prestasi Non Akademik', 'type' => 'S', 'option' => mCombo::tkpelatihan(),'empty'=>true, 'notnull' => false);
	// $a_input[] = array('kolom' => 'tmplahir', 'label' => 'Tmp & Tgl Lahir', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepropinsilahir', 'label' => 'Propinsi Lahir', 'type' => 'S', 'option' => mCombo::propinsi($conn), 'empty' => true, 'add' => 'onchange="loadKotaLahir()"', 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekotalahir', 'label' => 'Kota Lahir', 'type' => 'S', 'option' => "", 'notnull' => true, 'empty'=>true);

	$a_input[] = array('kolom' => 'tgllahir', 'label' => 'Tgl Lahir', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'goldarah', 'label' => 'Gol Darah', 'type' => 'S', 'option' => mCombo::golonganDarah(),'empty'=>true, 'notnull' => false);
	$a_input[] = array('kolom' => 'usia', 'label' => 'Usia Per 1 Sept '.date('Y'), 'maxlength' => '2', 'size'=>'5', 'empty'=>true, 'notnull' => false);
	$a_input[] = array('kolom' => 'statusnikah', 'label' => 'Status Nikah', 'type' => 'S', 'option' => mCombo::statusNikah(), 'notnull' => true,'empty'=>true);
	
	$a_input[] = array('kolom' => 'jalan', 'label' => 'Jalan', 'maxlength' => 150, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'rt', 'label' => 'RT', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'rw', 'label' => 'RW', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'kel', 'label' => 'Kelurahan', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kec', 'label' => 'Kecamatan', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepos', 'label' => 'Kode Pos', 'maxlength' => 150, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekota', 'label' => 'Kota', 'type' => 'S', 'option' => "", 'notnull' => true, 'empty'=>true);
        
	$a_input[] = array('kolom' => 'kodepropinsi', 'label' => 'Provinsi', 'type' => 'S', 'notnull' => true, 'option' => mCombo::propinsi($conn),  'add' => 'onchange="loadKota()"','empty'=>true);
	$a_input[] = array('kolom' => 'kodeagama', 'label' => 'Agama', 'type' => 'S', 'notnull' => true, 'option' => mCombo::agama($conn),'empty'=>true);
	$a_input[] = array('kolom' => 'kodewn', 'label' => 'Kewarganegaraan', 'type' => 'S', 'notnull' => true, 'option' => mCombo::wargaNegara(),'empty'=>true);
	$a_input[] = array('kolom' => 'telp', 'label' => 'Telp', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
	$a_input[] = array('kolom' => 'telp2', 'label' => 'Telp (2)', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'hp', 'label' => 'Hp', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
	$a_input[] = array('kolom' => 'hp2', 'label' => 'Hp (2)', 'maxlength' => 15, 'size' => 15);
	$a_input[] = array('kolom' => 'email', 'label' => 'Email', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'email2', 'label' => 'Email (2)', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'nomorktp', 'label' => 'Nomor KTP', 'maxlength' => 35, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'nomorkk', 'label' => 'Nomor KK', 'maxlength' => 35, 'size' => 50, 'notnull' => true);
	
	$a_input[] = array('kolom' => 'nis', 'label' => 'NIS', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
        
        //$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'maxlength' => 4000,'rows'=>10, 'cols'=>70, 'type'=>'M');
        
	$a_input[] = array('kolom' => 'namaayah', 'label' => 'Nama Ayah', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'namaibu', 'label' => 'Nama Ibu', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodeposortu', 'label' => 'Kode Pos', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'telportu', 'label' => 'Telp Ortu', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
	$a_input[] = array('kolom' => 'jalanortu', 'label' => 'Jalan', 'maxlength' => 150, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'rtortu', 'label' => 'RT', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'rwortu', 'label' => 'RW', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	$a_input[] = array('kolom' => 'kelortu', 'label' => 'Kelurahan', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kecortu', 'label' => 'Kecamatan', 'maxlength' => 20, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodekotaortu', 'label' => 'Kota Ortu', 'type' => 'S', 'option' =>"", 'notnull' => true);
	$a_input[] = array('kolom' => 'kodependapatanortu', 'label' => 'Pendapatan Ortu', 'type' => 'S', 'notnull' => true, 'option' => mCombo::pendapatan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodepekerjaanayah', 'label' => 'Pekerjaan Ayah', 'type' => 'S', 'notnull' => true, 'option' => mCombo::pekerjaan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodepekerjaanibu', 'label' => 'Pekerjaan Ibu', 'type' => 'S', 'notnull' => true, 'option' => mCombo::pekerjaan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodependidikanayah', 'label' => 'Pendidikan Ayah', 'type' => 'S', 'notnull' => true, 'option' => mCombo::pendidikan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'kodependidikanibu', 'label' => 'Pendidikan Ibu', 'type' => 'S', 'notnull' => true, 'option' => mCombo::pendidikan($conn), 'empty' => true);
	
	$a_input[] = array('kolom' => 'asalsmu', 'label' => 'Nama Sekolah', 'maxlength' => 50, 'size' => 50, 'notnull' => true0);
	$a_input[] = array('kolom' => 'alamatsmu', 'label' => 'Alamat Sekolah', 'maxlength' => 60, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'propinsismu', 'label' => 'Propinsi Sekolah ', 'type' => 'S', 'notnull' => true, 'option' => mCombo::propinsi($conn), 'add' => 'onchange="loadKotaSMU()"','empty'=>true);
	$a_input[] = array('kolom' => 'kodekotasmu', 'label' => 'Kota Sekolah', 'type' => 'S', 'notnull' => true, 'option' =>"",'maxlength' => 20);
	$a_input[] = array('kolom' => 'telpsmu', 'label' => 'Telp Sekolah', 'maxlength' => 15, 'notnull' => true, 'size' => 15);
	$a_input[] = array('kolom' => 'nemsmu', 'label' => 'NEM Kelulusan', 'type' => 'N,2', 'notnull' => true, 'maxlength' => 6, 'size' => 6);
	$a_input[] = array('kolom' => 'noijasahsmu', 'label' => 'No Ijasah SMU', 'maxlength' => 20, 'notnull' => true, 'size' => 20);
	
	$a_input[] = array('kolom' => 'pernahponpes', 'label' => 'Pernah Belajar di Ponpes ?', 'type' => 'R', 'option' => mCombo::pernahPonpes(),'add'=>'onchange="disabledPonpes();"');
	$a_input[] = array('kolom' => 'namaponpes', 'label' => 'Nama Pesantren', 'maxlength' => 50, 'size' => 50);
	$a_input[] = array('kolom' => 'alamatponpes', 'label' => 'Alamat Pesantren', 'maxlength' => 60, 'size' => 50);
	$a_input[] = array('kolom' => 'propinsiponpes', 'label' => 'Propinsi Pesantren ', 'type' => 'S', 'option' => mCombo::propinsi($conn), 'add' => 'onchange="loadKotaPonpes()"', 'empty'=>true);
	$a_input[] = array('kolom' => 'kodekotaponpes', 'label' => 'Kota Pesantren','type' => 'S', 'option' =>"",'maxlength' => 20, 'empty'=>true);
	$a_input[] = array('kolom' => 'lamaponpes', 'label' => 'Lama Belajar', 'maxlength' => 5, 'size' => 5);
	
	$a_input[] = array('kolom' => 'mhstransfer', 'label' => 'Mahasiswa Transfer ?', 'type' => 'R', 'option' => mCombo::pernahPonpes(), 'add' => 'onChange="disabledMhst();"');
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
	$a_input[] = array('kolom' => 'kodekotakotak', 'label' => 'Kota Kontak', 'type' => 'S', 'option' =>"", 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepropinsikontak', 'label' => 'Propinsi kontak', 'type' => 'S', 'option' => mCombo::propinsi($conn), 'empty' => true, 'add' => 'onchange="loadKotaKontak()"', 'notnull' => true);
       
	$a_input[] = array('kolom' => 'isasing', 'label' => 'Mahasiswa Asing ?', 'type' => 'R', 'option' => mCombo::pernahPonpes());
        
        // ada aksi
	if($r_act == 'save' and $c_edit) {
		// $periode=$_SESSION[SITE_ID]['PENDAFTAR']['periodedaftar'];
		// $gelombang=$_SESSION[SITE_ID]['PENDAFTAR']['idgelombang'];
		// $jalur = $_SESSION[SITE_ID]['PENDAFTAR']['jalurpenerimaan'];
		var_dump($_POST);exit;
		$periode=$r_periode;
		$gelombang=$r_gel;
		$jalur = $r_jalur;
		
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		$record['nopesertaspmb']=mPendaftar::nopeserta($periode, $gelombang, $jalur);
		$record['nopendaftar']  =mPendaftar::nopendaftar($conn,$periode, $gelombang, $jalur);
		$record['jalurpenerimaan'] = $jalur;
		$record['idgelombang'] = $gelombang;
		$record['periodedaftar'] = $periode;
		
		$password_acak = Date::RandomCode(6);
		$record['pswd'] = md5($password_acak);
		$record['password'] = $password_acak;
		Pendaftaran::setDataPribadi($record);
		$inputrecord=$_SESSION[SITE_ID]['PENDAFTAR'];
		
		$ok=mPendaftar::insertPeserta($inputrecord);
		if($ok) {
			Route::navigate('data_end');
		}  
		/*if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);*/
	}
	
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
        
        $row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
        
        $r_kodekota = Page::getDataValue($row,'kodekota');
        $r_kodekotaortu = Page::getDataValue($row,'kodekotaortu');
        $r_kodekotasmu = Page::getDataValue($row,'kodekotasmu');
        $r_kodekotapt = Page::getDataValue($row,'kodekota_kotak');
        
	if(empty($row[0]['value']) and !empty($r_key)) {
		$p_posterr = true;
		$p_fatalerr = true;
		$p_postmsg = 'User ini Tidak Mempunyai Profile';
	}
	
?>
<?php require_once('inc_header.php'); ?>
<div class="container">
  <div class="row">
    <div class="col-md-9">
      <div class="page-header">
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
                                    
				<?	} ?>
                </div>
                <?php
		
					if(empty($p_fatalerr)) { ?>
					<div class="panel panel-default" style="margin-top:20px;">
                      <div class="panel-heading"><span class="glyphicon glyphicon-user"></span> Data Pendaftar</div>
                      <div class="panel-body">
                      <?	$a_required = array('nama','sex','kodepropinsilahir','kodekotalahir','tgllahir','goldarah','statusnikah','jalan','rt','rw'
					,'kec','kel','kodepos','kodekota','kodepropinsi','kodeagama','kodewn','telp','hp'
					,'email','nomorktp','nomorkk','namaayah','namaibu','kodeposortu','telportu','jalanortu'
					,'rtortu','rwortu','kelortu','kecortu','kodekotaortu','kodependapatanortu','kodepekerjaanayah'
					,'kodepekerjaanibu','kodependidikanayah','kodependidikanibu','asalsmu','alamatsmu'
					,'propinsismu','kodekotasmu','telpsmu','nemsmu','ijasahsmu','bhsarab','bhsinggris','pengkomp'
					,'pilihan1','kodepropinsiortu','kontaknama','kontaktelp','jalankontak','rtkontak','rwkontak'
					,'kelkontak','keckontak','kodekotakotak','kodepropinsi_kontak'
					); ?>
																	
                        <table width="100%" cellpadding="4" cellspacing="2" class="table table-bordered table-striped">
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
                    
                    <div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span> Harap mengisi kolom inputan pada semua tab</div>
                    <ul class="nav nav-tabs" id="myTab">
						<li class="active"><a href="#biodata">Data Pendaftar</a></li>
						<li><a href="#akademik">Data Sekolah</a></li>
						<li><a href="#informasi">Data Keluarga</a></li>
						<li><a href="#pendidikan">Data Pendidikan/Prestasi</a></li>
						<li><a href="#kemampuan">Jadwal &amp; Upload Berkas</a></li>
                    </ul>
                    
                    <div class="tab-content">
                      <div class="tab-pane active" id="biodata">
                      	<table class="table table-bordered table-striped">
						<?= Page::getDataTR($row,'sex') ?>
						<?= Page::getDataTR($row,'kodepropinsilahir,kodekotalahir,tgllahir',', ') ?>
						<?= Page::getDataTR($row,'usia') ?>
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
					</table>
                      </div>
                      <?/*<div class="tab-pane" id="kemampuan">
                      <table class="table table-bordered table-striped">
						<?= Page::getDataTR($row,'bhsarab') ?>
						<?= Page::getDataTR($row,'bhsinggris') ?>
						<?= Page::getDataTR($row,'pengkomp') ?>
					</table>
                      </div>*/?>
                      <div class="tab-pane" id="akademik">
                      	<table class="table table-bordered table-striped">
						<tr>
							<td colspan="2" class="DataBG">Asal Sekolah</td>
						</tr>
                                                <?= Page::getDataTR($row,'asalsmu') ?>
						<?= Page::getDataTR($row,'alamatsmu') ?>
                                                <?= Page::getDataTR($row,'propinsismu') ?>
						<?= Page::getDataTR($row,'kodekotasmu') ?>
						<?= Page::getDataTR($row,'telpsmu') ?>
                                                <?= Page::getDataTR($row,'nis') ?>
						<?= Page::getDataTR($row,'nemsmu') ?>
                                                <?= Page::getDataTR($row,'noijasahsmu') ?>
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
						<?= Page::getDataTR($row,'kodepekerjaanayah') ?>
						<?= Page::getDataTR($row,'kodependidikanayah') ?>
                                                <?= Page::getDataTR($row,'namaibu') ?>
						<?= Page::getDataTR($row,'kodepekerjaanibu') ?>
						<?= Page::getDataTR($row,'kodependidikanibu') ?>
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
						<?= Page::getDataTR($row,'kodependapatanortu') ?>
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
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#tmplahir").xautox({strpost: "f=kotalahir", targetid: "npmtemp", postid: "nip"});
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

var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	initTab();
	
	loadKota();
	loadKotaOrtu();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

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

function disabledMhst(){
	if($('input[name="mhstransfer"]:checked').val() == 0){
		document.getElementById("ptasal").disabled=true;
		document.getElementById("propinsiptasal").disabled=true;
		document.getElementById("kodekotapt").disabled=true;
		document.getElementById("ptjurusan").disabled=true;
		document.getElementById("ptthnlulus").disabled=true;
		document.getElementById("ptipk").disabled=true;
		document.getElementById("sksasal").disabled=true;
	}else{
		document.getElementById("ptasal").disabled=false;
		document.getElementById("propinsiptasal").disabled=false;
		document.getElementById("kodekotapt").disabled=false;
		document.getElementById("ptjurusan").disabled=false;
		document.getElementById("ptthnlulus").disabled=false;
		document.getElementById("ptipk").disabled=false;
		document.getElementById("sksasal").disabled=false;

	}
}
function disabledPonpes(){
	if($('input[name="pernahponpes"]:checked').val() == 0){
		document.getElementById("namaponpes").disabled=true;
		document.getElementById("alamatponpes").disabled=true;
		document.getElementById("propinsiponpes").disabled=true;
		document.getElementById("kodekotaponpes").disabled=true;
		document.getElementById("lamaponpes").disabled=true;
	}else{
		document.getElementById("namaponpes").disabled=false;
		document.getElementById("alamatponpes").disabled=false;
		document.getElementById("propinsiponpes").disabled=false;
		document.getElementById("kodekotaponpes").disabled=false;
		document.getElementById("lamaponpes").disabled=false;
	}
}
</script>
<?php require_once('inc_footer.php'); ?>

