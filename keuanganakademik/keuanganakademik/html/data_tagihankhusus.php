<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
	$a_auth = false; Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('jenistagihan'));
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$p_model = mTagihan;
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	
	if($data['flaglunas']=='L'){
			$p_posterr = true;
			$p_postmsg = " Tagihan Telah Lunas, tidak dapat di edit";
			$c_update = false;
			$c_delete = false;
		}
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Tagihan';
	$p_tbwidth = 600;
	$p_aktivitas = 'KEUANGAN';
	$p_listpage = Route::getListPage();
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//option
	$arr_jenis = mJenistagihan::getArray($conn,array('A','B','S','T'));	
	foreach($arr_jenis as $i => $v){
		$arr_jenistagihan[$v['jenistagihan']] = $v['jenistagihan'].' - '.$v['namajenistagihan'];
		$infojt[$v['jenistagihan']] = $v;
		}
	
	$arr_periode = mCombo::periode($conn);
	$arr_bulan = Date::arrayMonth(false);
	for($i = date('Y')-10;$i<= date('Y')+20;$i++ ){
		$arr_tahun[$i] = $i;
		}
	
	if($r_key)
	{
		$readonly = 'readonly';
		}
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idtagihan', 'label' => 'ID Tagihan', 'maxlength' => 20, 'size' => 20,'readonly' => 'readonly');
	$a_input[] = array('kolom' => 'jenistagihan', 'label' => 'Jenis Tagihan','type' => 'S','option' => $arr_jenistagihan,'readonly' => $readonly);
	$a_input[] = array('kolom' => 'periode', 'label' => 'Periode','type' => 'S','option' => $arr_periode,'readonly' => $readonly);
	$a_input[] = array('kolom' => 'bulantahun', 'label' => 'Bulan Tahun','readonly' => $readonly);
	$a_input[] = array('kolom' => 'nominaltagihan', 'label' => 'Jumlah Tagihan', 'maxlength' => 20, 'size' => 20,'type'=> 'N');
	//$a_input[] = array('kolom' => 'potongan', 'label' => 'Potongan','type'=> 'N');
	$a_input[] = array('kolom' => 'tgltagihan', 'label' => 'Tgl Tagihan', 'type' => 'D','default'=> date('Y-m-d'));
	$a_input[] = array('kolom' => 'tgldeadline', 'label' => 'Tgl Deadline', 'type' => 'D');
	//$a_input[] = array('kolom' => 'isangsur', 'label' => 'Angsuran ?', 'type' => 'C','option' => array('1'=>'<em>centang bila tagihan ini dapat di angsur</em>'));
	//$a_input[] = array('kolom' => 'isedit', 'label' => 'Set Generated', 'type' => 'C','option' => array('0'=>'<em>centang bila tagihan ini dianggap generated</em>'));
	$a_input[] = array('kolom' => 'flaglunas', 'label' => 'Status Lunas', 'type' => 'R','option' => array('BB'=>'Belum Bayar','BL'=>'Belum Lunas','L'=>'Lunas', 'S'=>'Suspend', 'F'=>'DiBebaskan'),'default'=>'BB');
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A');
        
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		$record['isedit'] = 'E';
		$kdp = $record['periode'];
		if($infojt[$record['jenistagihan']]['frekuensitagihan']=='B'){
			$record['bulantahun'] = $_POST['tahun'].str_pad($_POST['bulan'],2,'0',STR_PAD_LEFT);
			$kdp = $record['bulantahun'];
			}
		
		$cek = mTagihan::cekTagihan($conn, $_POST['jenistagihan'], $_POST['pilihan'],$_POST['txtnim'], $_POST['periode']);
		if ($cek)
			$no = str_pad($cek+1,2,'0',STR_PAD_LEFT);
		if(empty($r_key))
		{
			$r_pilihan = $_POST['pilihan'];
			$r_cek = $_POST['cek'];
			if($r_cek){
			if($r_pilihan == 'nim'){
				$record['nim'] = $_POST['txtnim'];
				//$panjang = 18-strlen($record['nim']);
				//$record['idtagihan'] = str_pad($infojt[$record['jenistagihan']]['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($kdp,$panjang,'0',STR_PAD_LEFT).$record['nim'];
				$record['idtagihan'] = str_pad($infojt[$record['jenistagihan']]['kodetagihan'],2,'0',STR_PAD_LEFT).$_POST['periode'].$no.str_pad($record['nim'],15,'0',STR_PAD_LEFT);
			}else{
				$record['nopendaftar'] = $_POST['txtnim'];
				//$panjang = 18-strlen($record['nopendaftar']);
				//$record['idtagihan'] = str_pad($infojt[$record['jenistagihan']]['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($kdp,$panjang,'0',STR_PAD_LEFT).$record['nopendaftar'];
				$record['idtagihan'] = str_pad($infojt[$record['jenistagihan']]['kodetagihan'],2,'0',STR_PAD_LEFT).$_POST['periode'].$no.str_pad($record['nim'],15,'0',STR_PAD_LEFT);

				}
			$p_posterr = $p_model::insertRecord($conn,$record,$r_key);
			if (!$p_posterr)
				$p_postmsg = 'Tagihan Berhasil Ditambahkan';
			else
				$p_postmsg = 'Data Tagihan Gagal Ditambahkan';
				 
			}
			else{
				$p_posterr = true;
				$p_postmsg = " Nim / No pendaftar Tidak Terdaftar";
				}
		}
		else
			{
				$p_posterr = $p_model::updateRecord($conn,$record,$r_key);
			if ($p_posterr)
				$p_postmsg = 'Perubahan Data Tagihan Berhasil';
			else
				$p_postmsg = 'Perubahan Data Tagihan Gagal Dilakukan';

			}
		
		if(!$p_posterr) {
			unset($post);
			if(empty($r_key))
				$r_key = $record['idtagihan'];
		
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);	
		
	if($r_key)
	$data = $p_model::getDatadetail($conn,$r_key);
	
	require_once(Route::getViewPath('v_data_tagihan'));
?>
