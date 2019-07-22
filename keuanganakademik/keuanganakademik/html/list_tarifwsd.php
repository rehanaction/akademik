<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('jenistagihan'));
	require_once(Route::getModelPath('tarif'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	
	$arr_flag = mCombo::arrFlagtagihan();
	$arr_tagihan = mJenistagihan::getArray($conn,'W');
	$r_nominal =Modul::setRequest($_POST['nominal'],'NOMINAL');
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODEWISUDA');
		
         if (empty ($r_periode))
	{
		$p_postmsg='Silahkan Pilih Periode';
		$p_posterr=true;
	}
	// properti halaman
	$p_title = 'Tarif Tagihan Wisuda';
	$p_tbwidth = '600';
	$p_aktivitas = 'Master';
	
	$p_model = mJenistagihan;
	$p_key = $p_model::key;
	$p_colnum = count($p_kolom)+1;
	
	$l_periode = uCombo::periodewisuda($conn,$r_periode,'periode','onchange="goSubmit()"',true);
	
	// daftar jurusan
	$arr_unit = mAkademik::getArrayunit($conn,false,'2');
		
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'update' and $c_edit and $r_periode) {
		$r_key = CStr::removeSpecial($_POST['key']);
		 if($arr_tagihan)
			foreach($arr_tagihan as $b => $vt){
						$record = array();
						$record['periodetarif'] = $r_periode;
						$record['kodeunit'] = $r_key;
						$record['jenistagihan'] = $vt['jenistagihan'];
						$record['jalurpenerimaan'] = 'XXX';
						$record['sistemkuliah'] = 'XX';
						$idtarif = mTarif::getIdtarif($conn,$record);
						$record['nominaltarif'] = cStr::cStrDec($_POST[$vt['jenistagihan']]);
						if(!$idtarif)
							$err = mTarif::insertRecord($conn,$record);
						else
							$err = mTarif::updateRecord($conn,$record,$idtarif); 
							
                        }
						if($err<>'0'){
								$p_posterr = true;
								$p_postmsg = "Gagal update Tarif Wisuda";
							}
						else
							{
								$p_posterr = false;
								$p_postmsg = "berhasil update Tarif Wisuda";
								}
		
		//list($p_posterr,$p_postmsg) = $p_model::insertRec($conn,$a_kolom,$_POST,$r_key);
	}else if ($r_act == 'updateall' and $c_edit and $r_periode){
		$ins = $upd =0;
		foreach ($arr_unit as $i => $v){
			
			if($arr_tagihan)
			foreach($arr_tagihan as $b => $vt){
						$record = array();
						$record['periodetarif'] = $r_periode;
						$record['kodeunit'] = $i;
						$record['jenistagihan'] = 'WSD';
						$record['jalurpenerimaan'] = 'XXX';
						$record['sistemkuliah'] = 'XX';
						$idtarif = mTarif::getIdtarif($conn,$record);
						$record['nominaltarif'] = cStr::cStrDec($r_nominal);
						
						if(!$idtarif){
							$err = mTarif::insertRecord($conn,$record);
							$ins++;
						}
						else{
							$err = mTarif::updateRecord($conn,$record,$idtarif); 
							$upd++; 
						}
			}
			
			if($err<>'0'){
					$p_posterr = true;
					$p_postmsg = "Gagal update Tarif Wisuda";
				}
			else
				{
					$p_posterr = false;
					$p_postmsg = "berhasil update Tarif Wisuda <br>";
					$p_postmsg .= $ins." Data Insert dan ".$upd." Data Terupdate";
					}
			
			
						
			}
	} 
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	if(!empty($r_periode)) {
		$arr_data = mTarif::getTarifwisuda($conn,$r_periode);
		if($arr_data)
			foreach($arr_data as $i => $v){
					$data[$v['periodetarif']][$v['kodeunit']][$v['jenistagihan']] = $v;
				}
	}
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	require_once($conf['view_dir'].'v_list_tarifwsd.php');
?>
