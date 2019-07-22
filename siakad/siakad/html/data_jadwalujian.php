<?php  
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	//hak akses manual
	if(Akademik::isDosen()){
		$c_update = false;
		$c_delete = false;
	}
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('jadwalujian'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_pkey = CStr::removeSpecial($_REQUEST['pkey']);
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	
	
	$ajax_key=!empty($r_pkey)?$r_pkey:$r_key;
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Jadwal Ujian';
	$p_tbwidth = 600;
	$p_aktivitas = 'ABSENSI';
	$p_listpage = Route::getListPage();
	
	$p_model = mJadwalUjian;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	$p_listpage .= '&key='.$r_pkey;
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_pkey) or (empty($r_key) and !$c_insert))
		Route::navigate($p_listpage);
	
	list(,,,,,$jeniskuliah,$kelompok)=explode('|',$r_pkey);
	
		
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'jeniskuliah', 'label' => 'Jenis Perkuliahan', 'type' => 'S', 'option' => mKuliah::jenisKuliah($conn),'default'=>$jeniskuliah,'readonly'=>true);
	$a_input[] = array('kolom' => 'kelompokkul', 'label' => 'Kelompok perkuliahan','default'=>$kelompok,'readonly'=>true);
	$a_input[] = array('kolom' => 'jenisujian', 'label' => 'Jenis Ujian', 'type' => 'S', 'option' => mCombo::getJenisUjian());
	$a_input[] = array('kolom' => 'kelompok', 'label' => 'Kelompok Ujian', 'type' => 'S', 'option' => $p_model::getKelompok());
	$a_input[] = array('kolom' => 'tglujian', 'label' => 'Tanggal Ujian', 'type' => 'D');
	$a_input[] = array('kolom' => 'waktumulai', 'label' => 'Jam Mulai', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam');
	$a_input[] = array('kolom' => 'waktuselesai', 'label' => 'Jam Selesai', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam');
	$a_input[] = array('kolom' => 'koderuang', 'label' => 'Ruang Ujian', 'type' => 'S', 'option' => mCombo::ruang($conn));
	$a_input[] = array('kolom' => 'nippengawas1', 'label' => 'Pengawas 1', 'type' => 'X', 'text' => 'pengawas1','param'=>'strpost:"f=acpengawas"');
	$a_input[] = array('kolom' => 'nippengawas2', 'label' => 'Pengawas 2', 'type' => 'X', 'text' => 'pengawas2','param'=>'strpost:"f=acpengawas"');
	$a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan ', 'type' => 'A', 'rows' => 5, 'cols' => 50, 'maxlength' => 100);
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		$record['waktumulai'] = CStr::cStrNull(str_replace(':','',$_REQUEST['waktumulai']));
		$record['waktuselesai'] = CStr::cStrNull(str_replace(':','',$_REQUEST['waktuselesai']));
		if(empty($r_key)) {
			list(,,,,,$jeniskuliah,$kelompok)=explode('|',$r_pkey);
			
			$record += mKelas::getKeyRecord($r_pkey);
			$record['jeniskuliah']=$jeniskuliah;
			$record['kelompokkul']=$kelompok;
			//echo $r_pkey;	
			
			list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true);
			$r_key = $p_model::getLastValue($conn);
		}
		else
			list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_key,true);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	//$a_infokelas = mKelas::getDataSingkat($conn,$r_pkey);
	require_once(Route::getViewPath('inc_data'));
?>

<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
<script type="text/javascript">
	$(function() {
	$("#waktumulai").mask("99:99");
	$("#waktuselesai").mask("99:99");
	
	
});
</script>

