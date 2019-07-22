<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$p_detailpage = "data_tarifwisuda";
	// include
	require_once(Route::getModelPath('jenistagihan'));
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('tarif'));
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getModelPath('loggenerate'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODEWISUDA');
	$r_kodeunit = Modul::setRequest($_POST['kodeunit'],'JURUSAN');
	
	// combo
	$l_periode = uCombo::periodewisuda($conn,$r_periode,'periode','onchange="goSubmit()"',false);
	$l_unit = uCombo::unit($conn,$r_kodeunit,'kodeunit','onchange="goSubmit()"',false);
		
     if (empty ($r_periode))
			list($p_posterr, $p_postmsg) = array(true, 'Silahkan Pilih Periode');
     if (empty ($r_kodeunit))
			list($p_posterr, $p_postmsg) = array(true, 'Silahkan Pilih Prodi / Unit');
			
	// properti halaman
	$p_title = 'Generate Tagihan Wisuda';
	$p_tbwidth = '100%';
	$p_aktivitas = 'Master';
	
	$p_model = mJenistagihan;
	$p_key = $p_model::key;
	$p_colnum = count($p_kolom)+1;
	
	$r_act = $_POST['act'];
	if($r_act == 'void' and $c_delete){
		$r_idkey = $_POST['idkey'];
			
			$p_posterr = mTagihan::deleteperid($conn,$r_idkey);
			if ($p_posterr)
				$p_postmsg='Pembatalan Tagihan Gagal';
			else
				$p_postmsg='Pembatalan Tagihan Berhasil';
			
		}
	else
	if($r_act == 'generate' and $c_edit){
			$r_nim = $_POST['nimkey'];
			$r_jenistagihan = $_POST['jenistagihankey'];
			$r_kodeunit = $_POST['kodeunitkey'];
			$r_periodeyudisium = $_POST['periodekey'];
			
			$p_posterr = mTagihan::generateTagihanwisuda($conn, $r_nim, $r_kodeunit, $r_periodeyudisium, $r_jenistagihan);
			if ($p_posterr)
				$p_postmsg='Generate Tagihan Gagal';
			else
				$p_postmsg='Generate Tagihan Berhasil';
			
		}else if($r_act == 'generateall' and $c_edit){
			$data = $_POST['check'];
			$err=$sukses=0;
			$nimsukses = $nimgagal=array();
			for($a=0; $a<count($data); $a++){
				list($r_nim, $r_jenistagihan, $r_periodeyudisium, $r_kodeunit) = explode('||', $data[$a]);
					$err = mTagihan::generateTagihanwisuda($conn, $r_nim, $r_kodeunit, $r_periodeyudisium, $r_jenistagihan);
					if ($err){
						$err++;
						$nimgagal[] = $r_nim;
					}else{
						$sukses++;
						$nimsukses[] = $r_nim;
					}
				
				}
			if (count($nimgagal) > 0)
			list($p_posterr, $p_postmsg)= array(true, 'Generate Tagihan Nim ( '.implode(', ', $nimgagal).' ) Gagal');
			
			else if ($sukses > 0)
				$p_postmsg='Generate Tagihan Nim ( '.implode(', ', $nimsukses).' ) Berhasil';
			
		}	
	
	$arr_tagihan = mJenistagihan::getArray($conn,'W');	
	$data = mTagihan::dataTagihanwisuda($conn,$r_periode,$r_kodeunit);
	
	$datamhs = mAkademik::getMhsyudisium($conn,$r_periode,$r_kodeunit);
	//die('test');
	$arr_data = mTarif::getTarifwisuda($conn,$r_periode);
	
	if($arr_data)
		foreach($arr_data as $i => $v){
				$tarif[$v['periodetarif']][$v['kodeunit']][$v['jenistagihan']] = $v;
			}
		
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode Wisuda', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Jurusan', 'combo' => $l_unit);
	
	require_once($conf['view_dir'].'v_list_gentagihanwisuda.php');
?>
