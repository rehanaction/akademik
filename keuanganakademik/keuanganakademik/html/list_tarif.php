<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$p_detailpage = "data_tarif";
	// include
	require_once(Route::getModelPath('jenistagihan'));
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('tarif'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
	$r_jalur = Modul::setRequest($_POST['jalurpenerimaan'],'JALUR');
	$r_gelombang = Modul::setRequest($_POST['gelombang'],'GELOMBANG');
	$r_unit = Modul::setRequest($_POST['kodeunit'],'UNIT');
	$r_jenis = Modul::setRequest($_POST['jenis'],'JENIS');
	
	$r_periodesalin = Modul::setRequest($_POST['periodesalin'],'PERIODESALIN');
	$r_jalursalin = Modul::setRequest($_POST['jalurpenerimaansalin'],'JALURPENERIMAANSALIN');
	
	$arr_flag = mCombo::arrFlagtagihan();
	// combo
	$l_periode = uCombo::periode($conn,$r_periode,'periode','onchange="goSubmit()"',true);
	$l_jalur = uCombo::jalur($conn,$r_jalur,'jalurpenerimaan','onchange="goSubmit()"',true);
	$l_gelombang = uCombo::gelombang($conn,$r_gelombang,'gelombang','onchange="goSubmit()"',true);
	$l_periodesalin = uCombo::periode($conn,$r_periodesalin,'periodesalin','',true);
	$l_jalursalin = uCombo::jalur($conn,$r_jalursalin,'jalurpenerimaansalin','',true);
	$l_unit = uCombo::unit($conn,$r_unit,'kodeunit','onchange="goSubmit()"',true);
	$l_jenis = uCombo::sistemkuliah($conn,$r_jenis,'jenis','onchange="goSubmit()"');
	//$l_jenis = uCombo::jenis($r_jenis,'onchange="goSubmit()"');
	$jumlahgelombang = mAkademik::getJumlahGelombang($conn);
	
	
         if (empty ($r_periode))
	{
		$p_postmsg='Silahkan Pilih Periode';
		$p_posterr=true;
	}
         if (empty ($r_jalur))
	{
		$p_postmsg='Silahkan Pilih Jalur Penerimaan';
		$p_posterr=true;
	}
		if (empty ($r_gelombang))
	{
		$p_postmsg='Silahkan Pilih Gelombang';
		$p_posterr=true;
	}
	
	$r_act = $_POST['act'];
	if ($r_act == 'salin'){
		list ($p_posterr, $p_postmsg) = mTarif::salinTagihan($conn, $r_jalur, $r_periode, $r_jalursalin, $r_periodesalin);		
			if (!$p_posterr){
				$r_periode = $r_periodesalin;
				$r_jalur = $r_jalursalin;
			}
		}
	// properti halaman
	$p_title = 'Tarif Pembayaran Rutin';
	$p_tbwidth = '100%';
	$p_aktivitas = 'Master';
	
	$p_model = mJenistagihan;
	$p_key = $p_model::key;
	$p_colnum = count($p_kolom)+1;
	
	
	//daftar jenis tagihan
	$arr_jenistagihan = mJenistagihan::getArrayTagRutin($conn,array('A','S')); // tampilkan yang sederhana
	
	foreach($arr_jenistagihan as $k => $v) {
		if(isset($r_jenis)) {
			if(($r_jenis == 'P' and empty($v['isparalel'])) or ($r_jenis == 'R' and empty($v['isreguler'])))
				unset($arr_jenistagihan[$k]);
		}
	}
		
	// daftar jurusan
		$arr_unit = mAkademik::getArrayunit($conn,false,'2',$r_unit);
 
	//daftar sistem kuliah 
		$arr_sistemkuliah = mAkademik::getArraysistemkuliah($conn,$r_jenis);
	//data tarif
	if (!empty($r_periode) and !empty($r_jalur) and !empty($r_gelombang)) {
		$arr_tarif = mTarif::getArraytarif($conn,$r_periode,$r_jalur,$r_unit,'','', $r_gelombang);
		
		if($arr_tarif)
		foreach($arr_tarif as $i => $val){
				$data[$val['kodeunit']][$val['sistemkuliah']][$val['jenistagihan']] = $val['nominaltarif'];
			}
	}
		
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Semester Daftar', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Jalur Penerimaan', 'combo' => $l_jalur);
	$a_filtercombo[] = array('label' => 'Gelombang', 'combo' => $l_gelombang);
	$a_filtercombo[] = array('label' => 'Jurusan', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Basis Kuliah', 'combo' => $l_jenis);
        //$a_filtercombo[] = array['label' => 'Gelombang', 'combo' => $l_gelomban);
	
	require_once($conf['view_dir'].'v_list_tarif.php');
?>
