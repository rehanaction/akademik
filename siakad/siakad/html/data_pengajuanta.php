<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('matakuliah'));
	require_once(Route::getModelPath('skripsi'));
	require_once(Route::getModelPath('ta'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	
	// properti halaman
	$p_title = 'Data Pengajuan Skripsi';
	$p_tbwidth = 600;
	$p_aktivitas = 'KULIAH';
	$p_listpage = Route::getListPage();
	
	$p_model = mSkripsi;
	// $p_modelta = mTa;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	//die('ok');
	// cek data
	$a_kurikulum = mCombo::kurikulum($conn);
	
	$r_act = $_POST['act'];
	if(empty($r_key) or $r_act == 'change') {
		$post['thnkurikulum'] = Modul::setRequest($_POST['thnkurikulum'],'KURIKULUM');
		
		$r_kurikulum = $post['thnkurikulum'];
		if(!isset($a_kurikulum[$r_kurikulum]))
			$r_kurikulum = key($a_kurikulum);
	}
	else {
		$a_cek = $p_model::getData($conn,$r_key);
		
		$r_kurikulum = $a_cek['thnkurikulum'];
	}
	$aktif=array('1'=>'Aktif','0'=>'Tidak Aktif');
	
	// struktur view
	$a_input = array();
	// $a_input[] = array('kolom' => 'nim', 'label' => 'NIM', 'maxlength' => 10, 'size' => 10, 'notnull' => true);
	if(Modul::getRole()=='A' or Modul::getRole()=='KTA')
		$a_input[] = array('kolom' => 'nim', 'label' => 'NIM', 'type' => 'X', 'text' => 'nim_mhs', 'param' => 'strpost:"f=acmahasiswa"');
	$a_input[] = array('kolom' => 'judulta', 'label' => 'Judul Skripsi', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'judultaen', 'label' => 'Judul Skripsi (EN)', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'topikta', 'label' => 'Topik Skripsi', 'maxlength' => 50, 'size' => 50);
	$a_input[] = array('kolom' => 'topiktaen', 'label' => 'Topik Skripsi (EN)','maxlength' => 50, 'size' => 50);
	if(Modul::getRole()=='A' or Modul::getRole()=='KTA')
		$a_input[] = array('kolom' => 'statuspengajuanta', 'label' => 'Status Pengajuan', 'type' => 'S', 'option' => $p_model::statuspengajuan());
	$a_input[] = array('kolom' => 'abstrakta', 'label' => 'Abstrak Skripsi', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'pemb1', 'label' => 'Dosen Pembimbing 1', 'type' => 'X', 'text' => 'dosenpembimbing', 'param' => 'strpost:"f=acpegawai"');
	$a_input[] = array('kolom' => 'pemb2', 'label' => 'Dosen Pembimbing 2', 'type' => 'X', 'text' => 'dosenpembimbing2', 'param' => 'strpost:"f=acpegawai"');
	// ada aksi
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		if(Modul::getRole()=='M'){
			// $record = array();
			$record['nim'] = Modul::getUserName();
			$record['statuspengajuanta'] = 'P';
		}
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if($record['statuspengajuanta']=='S'){
			$record2 = array();
			$record2['nim'] = $record['nim'];
			$record2['judulta'] = $record['judulta'];
			$record2['judultaen'] = $record['judultaen'];
			$record2['topikta'] = $record['topikta'];
			$record2['topiktaen'] = $record['topiktaen'];
			$record2['abstrakta'] = $record['abstrakta'];
			$record2['tahapta'] = 'PREPROP';
			$record2['statusta'] = 'A';
			$rs_cek = $conn->GetOne("select count(*) from akademik.ak_ta where nim='".$record['nim']."'");
			if($rs_cek == 0){ //masih 0 diinsert aja
				// list($p_posterr,$p_postmsg) = $p_modelta::insertCRecord($conn,$a_input,$record,$r_key);
				$err = Query::recInsert($conn,$record2,'akademik.ak_ta');
				if(!$err) {
					$tkey = $conn->GetOne("select last_value from akademik.ak_ta_idta_seq");
				}
				
				// pembimbing utama
				if(!$err and $record['pemb1'] != 'null') {
					$recdet = array();
					$recdet['idta'] = $tkey;
					$recdet['nip'] = $record['pemb1'];
					$recdet['tipepembimbing'] = 'U';
					
					$err = Query::recInsert($conn,$recdet,'akademik.ak_pembimbing');
				}
				
				// pembimbing pendamping
				if(!$err and $record['pemb2'] != 'null') {
					$recdet = array();
					$recdet['idta'] = $tkey;
					$recdet['nip'] = $record['pemb2'];
					$recdet['tipepembimbing'] = 'C';
					
					$err = Query::recInsert($conn,$recdet,'akademik.ak_pembimbing');
				}
			}
		}
		
		if(!$p_posterr) unset($post);
		if($r_key == null){
			$r_key = $conn->GetOne("select max(idpengajuanta) from akademik.ak_pengajuanta");
		}
			
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>
