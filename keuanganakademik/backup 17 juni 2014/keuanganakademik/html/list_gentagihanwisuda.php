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
	$l_periode = uCombo::periodewisuda($conn,$r_periode,'periode','onchange="goSubmit()"',true);
	$l_unit = uCombo::unit($conn,$r_kodeunit,'kodeunit','onchange="goSubmit()"',true);
		
        
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
			
			$err = mTagihan::deleteperid($conn,$r_idkey);
			
		}
	else
	if($r_act == 'generate' and $c_edit){
			$r_nim = $_POST['nimkey'];
			$r_jenistagihan = $_POST['jenistagihankey'];
			$r_kodeunit = $_POST['kodeunitkey'];
			$r_periodeyudisium = $_POST['periodekey'];
			
			$err = mTagihan::generateTagihanwisuda($conn, $r_nim, $r_kodeunit, $r_periodeyudisium, $r_jenistagihan);
			
		}
	
	
	$arr_tagihan = mJenistagihan::getArray($conn,'W');	
	$data = mTagihan::dataTagihanwisuda($conn,$r_periode,$r_kodeunit);
	$datamhs = mAkademik::getMhsyudisium($conn,$r_periode,$r_kodeunit);
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