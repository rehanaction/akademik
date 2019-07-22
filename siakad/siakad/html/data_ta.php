<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$c_bimbingan = $a_auth['canother']['B'];
	$c_valbimbingan = $a_auth['canother']['V'];
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('krs'));
	require_once(Route::getModelPath('ta'));
	require_once(Route::getModelPath('unsurnilai'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('kemahasiswaan'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$p_model = mTa;
	
	// variabel request
	if(empty($r_key))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if(Akademik::isMhs()) {
		$r_key = '';
		$r_npm = Modul::getUserName();
		$p_limited=true;
	}
	else
		$r_npm = CStr::removeSpecial($_REQUEST['npm']);
	
	if(Akademik::isDosen()){
		$p_limited=true;
	}
	
	if(empty($r_key) and !empty($r_npm))
		$r_key = $p_model::getTAMahasiswa($conn,$r_npm);
	
	// ambil unsur nilai kelas bila sudah memenuhi prasyarat
	$a_unsurnilai = array();
	if(!empty($r_key)) {
		$a_keykrs = $p_model::getKRSTA($conn,$r_key);
		
		if(!empty($a_keykrs)) {
			$t_keykls = mKelas::getKeyRow($a_keykrs);
			
			$a_unsurnilai = mUnsurNilaiKelas::getDataKelas($conn,$t_keykls);
			if(empty($a_unsurnilai))
				$a_unsurnilai = mUnsurNilaiKelas::insertFromUnsurNilai($conn,$t_keykls);
			
			$t_keykrs = mKRS::getKeyRow($a_keykrs);
			
			$a_nilaimhs = mUnsurNilaiMhs::getDataKelasMhs($conn,$t_keykrs);
			
			$a_nilai = array();
			foreach($a_nilaimhs as $t_nilai)
				$a_nilai[$t_nilai['idunsurnilai']] = $t_nilai['nilaiunsur'];
		}
	}
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Skripsi Mahasiswa';
	$p_tbwidth = 640;
	$p_aktivitas = 'SKRIPSI';
	$p_listpage = Route::getListPage();
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert) {
		if(!empty($r_npm)) {
			$p_posterr = true;
			$p_fatalerr = true;
			$p_postmsg = 'Data skripsi <strong>'.$r_npm.'</strong> belum dimasukkan';
		}
		else
			Route::navigate($p_listpage);
	}

	//struktur view
	$a_dosen = mCombo::dosenLengkap($conn);
	$a_ruang = mCombo::ruang($conn);
	
	$a_input = array();
	$a_input[] = array('kolom' => 'nim', 'label' => 'N I M', 'maxlength' => 11, 'size' => 15, 'notnull' => true);
	
	if(!empty($r_key)) {
		$a_input[] = array('kolom' => 'nama', 'label' => 'Nama Mahasiswa', 'maxlength' => 50, 'size' => 30, 'readonly' => true);
		$a_input[] = array('kolom' => 'kodeunitparent', 'label' => 'Fakultas', 'type' => 'S', 'option' => mCombo::fakultas($conn), 'readonly' => true);
		$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Jurusan', 'type' => 'S', 'option' => mCombo::jurusan($conn), 'readonly' => true);
		$a_input[] = array('kolom' => 'semestermhs', 'label' => 'Semester', 'maxlength' => 2, 'size' => 2, 'readonly' => true);
		
		$a_input[] = array('kolom' => 'juduljurnal', 'label' => 'Judul', 'maxlength' => 1000, 'size' => 50);
		$a_input[] = array('kolom' => 'topik', 'label' => 'Topik', 'maxlength' => 1000, 'size' => 50);
		$a_input[] = array('kolom' => 'namajurnal', 'label' => 'Nama Jurnal', 'maxlength' => 1000, 'size' => 50);
		$a_input[] = array('kolom' => 'edisi', 'label' => 'Edisi', 'maxlength' => 10, 'size' => 4);
		$a_input[] = array('kolom' => 'url', 'label' => 'URL (jika ada)', 'maxlength' => 1000, 'size' => 50);
		$a_input[] = array('kolom' => 'statusvalidasi', 'label' => 'Validasi', 'type' => 'C', 'option' => array('-1' => 'Jurnal telah valid'),'readonly' => $p_limited);
		
		$a_input[] = array('kolom' => 'tglujianproposal', 'label' => 'Tanggal', 'type' => 'D','readonly' => $p_limited);
		$a_input[] = array('kolom' => 'waktumulaiproposal', 'label' => 'Waktu', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam','readonly' => $p_limited);
		$a_input[] = array('kolom' => 'waktuselesaiproposal', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam','readonly' => $p_limited);
		$a_input[] = array('kolom' => 'koderuangproposal', 'label' => 'Ruang', 'type' => 'S', 'option' => $a_ruang, 'empty' => true,'readonly' => $p_limited);
		// $a_input[] = array('kolom' => 'sekretarisproposal', 'label' => 'Sekretaris', 'type' => 'S', 'option' => $a_dosen, 'empty' => true);
		// $a_input[] = array('kolom' => 'penguji1proposal', 'label' => 'Penguji 1', 'type' => 'S', 'option' => $a_dosen, 'empty' => true);
		// $a_input[] = array('kolom' => 'penguji2proposal', 'label' => 'Penguji 2', 'type' => 'S', 'option' => $a_dosen, 'empty' => true);
		$a_input[] = array('kolom' => 'sekretaris', 'label' => 'Sekretaris', 'type' => 'X', 'text' => 'sekretarisproposal_','readonly' => $p_limited);
		$a_input[] = array('kolom' => 'penguji1', 'label' => 'Penguji 1', 'type' => 'X', 'text' => 'penguji1proposal_','readonly' => $p_limited);
		$a_input[] = array('kolom' => 'penguji2', 'label' => 'Penguji 2', 'type' => 'X', 'text' => 'penguji2proposal_','readonly' => $p_limited);
		$a_input[] = array('kolom' => 'nilaiujianproposal', 'label' => 'Nilai', 'type' => 'NP,2', 'maxlength' => 4, 'size' => 3,'readonly' => $p_limited);
		$a_input[] = array('kolom' => 'mengulangproposal', 'label' => 'Keputusan', 'type' => 'S', 'option' => $p_model::mengulang(), 'empty' => true,'readonly' => $p_limited);
		
		$a_input[] = array('kolom' => 'tglujianseminar', 'label' => 'Tanggal', 'type' => 'D');
		$a_input[] = array('kolom' => 'waktumulaiseminar', 'label' => 'Waktu', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam');
		$a_input[] = array('kolom' => 'waktuselesaiseminar', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam');
		$a_input[] = array('kolom' => 'koderuangseminar', 'label' => 'Ruang', 'type' => 'S', 'option' => $a_ruang, 'empty' => true);
		// $a_input[] = array('kolom' => 'sekretarisseminar', 'label' => 'Sekretaris', 'type' => 'S', 'option' => $a_dosen, 'empty' => true);
		// $a_input[] = array('kolom' => 'penguji1seminar', 'label' => 'Penguji 1', 'type' => 'S', 'option' => $a_dosen, 'empty' => true);
		// $a_input[] = array('kolom' => 'penguji2seminar', 'label' => 'Penguji 2', 'type' => 'S', 'option' => $a_dosen, 'empty' => true);
		$a_input[] = array('kolom' => 'sekretarisseminar', 'label' => 'Sekretaris', 'type' => 'X', 'text' => 'textsekretarisseminar');
		$a_input[] = array('kolom' => 'penguji1seminar', 'label' => 'Penguji 1', 'type' => 'X', 'text' => 'textpenguji1seminar');
		$a_input[] = array('kolom' => 'penguji2seminar', 'label' => 'Penguji 2', 'type' => 'X', 'text' => 'textpenguji2seminar');
		$a_input[] = array('kolom' => 'nilaiujianseminar', 'label' => 'Nilai', 'type' => 'NP,2', 'maxlength' => 4, 'size' => 3);
		$a_input[] = array('kolom' => 'mengulangseminar', 'label' => 'Keputusan', 'type' => 'S', 'option' => $p_model::mengulang(), 'empty' => true);
		
		$a_input[] = array('kolom' => 'tglujianakhir', 'label' => 'Tanggal', 'type' => 'D','readonly' => $p_limited);
		$a_input[] = array('kolom' => 'waktumulaiakhir', 'label' => 'Waktu', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam','readonly' => $p_limited);
		$a_input[] = array('kolom' => 'waktuselesaiakhir', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam','readonly' => $p_limited);
		$a_input[] = array('kolom' => 'koderuangakhir', 'label' => 'Ruang', 'type' => 'S', 'option' => $a_ruang, 'empty' => true,'readonly' => $p_limited);
		// $a_input[] = array('kolom' => 'ketuamajelisakhir', 'label' => 'Ketua Majelis', 'type' => 'S', 'option' => $a_dosen, 'empty' => true);
		// $a_input[] = array('kolom' => 'sekretarisakhir', 'label' => 'Sekretaris', 'type' => 'S', 'option' => $a_dosen, 'empty' => true);
		// $a_input[] = array('kolom' => 'penguji1akhir', 'label' => 'Penguji 1', 'type' => 'S', 'option' => $a_dosen, 'empty' => true);
		// $a_input[] = array('kolom' => 'penguji2akhir', 'label' => 'Penguji 2', 'type' => 'S', 'option' => $a_dosen, 'empty' => true);
		$a_input[] = array('kolom' => 'ketuamajelisakhir', 'label' => 'Ketua Majelis', 'type' => 'X', 'text' => 'textketuamajelisakhir','readonly' => $p_limited);
		$a_input[] = array('kolom' => 'sekretarisakhir', 'label' => 'Sekretaris', 'type' => 'X', 'text' => 'textsekretarisakhir' ,'readonly' => $p_limited);
		$a_input[] = array('kolom' => 'penguji1akhir', 'label' => 'Penguji 1', 'type' => 'X', 'text' => 'textpenguji1akhir' ,'readonly' => $p_limited);
		$a_input[] = array('kolom' => 'penguji2akhir', 'label' => 'Penguji 2', 'type' => 'X', 'text' => 'textpenguji2akhir','readonly' => $p_limited);
		$a_input[] = array('kolom' => 'nilaiujianakhir', 'label' => 'Nilai', 'type' => 'NP,2', 'maxlength' => 4, 'size' => 3, 'class' => 'ControlRead','readonly' => $p_limited);
		$a_input[] = array('kolom' => 'mengulangakhir', 'label' => 'Keputusan', 'type' => 'S', 'option' => $p_model::mengulang(), 'empty' => true,'readonly' => $p_limited);
		
		$a_input[] = array('kolom' => 'skslulus', 'label' => 'SKS Lulus', 'readonly' => true);
		$a_input[] = array('kolom' => 'cekpoint', 'label' => 'Poin Mahasiswa', 'readonly' => true,'type' => 'C', 'option' => array('-1' => 'Jumlah poin mencukupi, bisa diajukan untuk sidang'));
		$a_input[] = array('kolom' => 'bebanstudi', 'readonly' => true);
		$a_input[] = array('kolom' => 'cekbimbingan', 'label' => 'Jumlah Bimbingan', 'type' => 'C', 'option' => array('-1' => 'Jumlah bimbingan cukup, bisa diajukan untuk sidang'),'readonly' => $p_limited);
		$a_input[] = array('kolom' => 'cekjurnal', 'label' => 'Jurnal Ilmiah');
		$a_input[] = array('kolom' => 'cektoefl', 'label' => 'Nilai TOEFL', 'type' => 'C', 'option' => array('-1' => 'Nilai TOEFL cukup'),'readonly' => $p_limited);
		//$a_input[] = array('kolom' => 'cektoafl', 'label' => 'Nilai TOAFL', 'type' => 'C', 'option' => array('-1' => 'Nilai TOAFL cukup'));
		$a_input[] = array('kolom' => 'cekkum', 'label' => 'Nilai KUM', 'type' => 'C', 'option' => array('-1' => 'Nilai KUM cukup'),'readonly' => $p_limited);
		$a_input[] = array('kolom' => 'cekprasyarat', 'readonly' => true);
	}
	
	$a_input[] = array('kolom' => 'judulta', 'label' => 'Judul', 'type' => 'A', 'rows' => 4, 'cols' => 50, 'maxlength' => 300);
	$a_input[] = array('kolom' => 'topikta', 'label' => 'Topik', 'maxlength' => 50, 'size' => 58);
	$a_input[] = array('kolom' => 'judultaen', 'label' => 'Judul (EN)', 'type' => 'A', 'rows' => 4, 'cols' => 50, 'maxlength' => 300);
	$a_input[] = array('kolom' => 'topiktaen', 'label' => 'Topik (EN)', 'maxlength' => 50, 'size' => 58);
	$a_input[] = array('kolom' => 'tahapta', 'label' => 'Tahap', 'type' => 'S', 'option' => $p_model::tahapTa($conn));
	$a_input[] = array('kolom' => 'progressta', 'label' => 'Progress (%)', 'type' => 'N', 'maxlength' => 3, 'size' => 5);
	$a_input[] = array('kolom' => 'tglmulai', 'label' => 'Tgl Mulai', 'type' => 'D');
	$a_input[] = array('kolom' => 'tglselesai', 'label' => 'Tgl Selesai', 'type' => 'D');
	$a_input[] = array('kolom' => 'statusta', 'label' => 'Status Skripsi', 'type' => 'S', 'option' => $p_model::statusTa($conn));
	$a_input[] = array('kolom' => 'niputama', 'label' => 'Dosen Pemb.1', 'type' => 'X', 'text' => 'niputama_');
	$a_input[] = array('kolom' => 'nipco', 'label' => 'Dosen Pemb.2', 'type' => 'X', 'text' => 'nipco_');
	$a_input[] = array('kolom' => 'abstrakta', 'label' => 'Abstrak TA', 'type' => 'A', 'rows' => 10, 'cols' => 50, 'maxlength' => 4000);
	
	// mengambil data riwayat
	$a_detail = array();
	
	$t_detail = array();
	$t_detail[] = array('kolom' => 'bimbinganke', 'label' => 'No', 'size' => 2, 'maxlength' => 2);
	// $t_detail[] = array('kolom' => 'nip', 'label' => 'Dosen', 'type' => 'S', 'option' => $p_model::pembimbing($conn,$r_key), 'add' => 'style="width:250px"', 'empty' => true);
	$t_detail[] = array('kolom' => 'nip', 'label' => 'Dosen', 'type' => 'X', 'text' => 'textnip', 'option' => $p_model::pembimbing($conn,$r_key), 'size' => 45);
	$t_detail[] = array('kolom' => 'topikbimbingan', 'label' => 'Topik', 'type' => 'A', 'rows' => 4, 'maxlength' => 30);
	$t_detail[] = array('kolom' => 'tglbimbingan', 'label' => 'Tanggal Bimbingan', 'type' => 'D');
	if(Modul::getRole()=='A' or Modul::getRole()=='D')
		$t_detail[] = array('kolom' => 'disetujui', 'label' => 'Disetujui', 'type' => 'C', 'align' => 'center', 'option' => array('1' => ''));
	
	$a_detail['bimbingan'] = array('key' => $p_model::getDetailInfo('bimbingan','key'), 'data' => $t_detail);
	//die();
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['waktumulaiproposal'] = CStr::cStrNull(str_replace(':','',$_REQUEST['waktumulaiproposal']));
		$record['waktuselesaiproposal'] = CStr::cStrNull(str_replace(':','',$_REQUEST['waktuselesaiproposal']));
		$record['waktumulaiakhir'] = CStr::cStrNull(str_replace(':','',$_REQUEST['waktumulaiakhir']));
		$record['waktuselesaiakhir'] = CStr::cStrNull(str_replace(':','',$_REQUEST['waktuselesaiakhir']));
		$record['tglbimbingan'] = date('Y-m-d',strtotime($_POST['tglbimbingan']));
		
 	// var_dump($record['tglujianproposal']);
		// cek tanggal
		
		if($record['tglujianproposal'] != null and $record['tglujianproposal'] != 'null') {
			$t_istgl = $p_model::isTglSeminarProposal($conn,$record['tglujianproposal']);
			
			if(!$t_istgl) {
				$p_posterr = true;
				$p_postmsg = 'Tanggal seminar proposal berada di luar periode. Untuk memasukkan periode klik <u class="ULink" onclick="goOpen(\'ms_kalender\')">di sini</u>';
			}
		}
		if($_POST['cekprasyarat'] == 1){
	
			if(!$p_posterr and ($record['tglujianakhir'] != null and $record['tglujianakhir'] != 'null')) {
				$t_istgl = $p_model::isTglUjianSkripsi($conn,$record['tglujianakhir']);
				
				if(!$t_istgl) {
					$p_posterr = true;
					$p_postmsg = 'Tanggal ujian skripsi berada di luar periode. Untuk memasukkan periode klik <u class="ULink" onclick="goOpen(\'ms_kalender\')">di sini</u>';
				}
			}
		}
		if(!$p_posterr) {
			if(empty($r_key)) {
				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
			}
			else {
				// validasi jurnal
				$record['cekjurnal'] = $record['statusvalidasi'];
				
				// untuk nilai ujian akhir
				$t_keykrs = mKRS::getKeyRow($a_keykrs);
				foreach($a_unsurnilai as $t_unsur)
					$record['n_'.$t_unsur['idunsurnilai']] = CStr::cStrNull(CStr::cStrDec($_POST['n_'.$t_unsur['idunsurnilai']]));
				
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$t_keykrs,$a_unsurnilai);
			}
		}
		//echo $r_key;die('hentikan');
		/*if(!$p_posterr) {
			$a_flash = array();
			$a_flash['r_key'] = $r_key;
			$a_flash['p_posterr'] = $p_posterr;
			$a_flash['p_postmsg'] = $p_postmsg;
			
			Route::setFlashData($a_flash);
		}*/
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'insertdet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		
		$record = array('idta' => $r_key);
		foreach($a_detail[$r_detail]['data'] as $t_detail) {
			$t_value = $_POST[$r_detail.'_'.CStr::cEmChg($t_detail['nameid'],$t_detail['kolom'])];
			$record[$t_detail['kolom']] = CStr::cStrNull($t_value);
		}
		$record['tglbimbingan'] = date('Y-m-d',strtotime($record['tglbimbingan']));
		list($p_posterr,$p_postmsg) = $p_model::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail);
	}
	else if($r_act == 'deletedet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkey,$r_detail);
	}
	else if($r_act == 'valbimbingan' and $c_valbimbingan) {
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		$err = $p_model::validasiBimbingan($conn,$r_subkey);
		
		$p_posterr = Query::boolErr($err);
		$p_postmsg = 'Validasi data bimbingan '.($err ? 'gagal' : 'berhasil');
	}

	if(!empty($r_key)){

		$data_ta=$p_model::getDataTa($conn,$r_key);
		$a_unit = mUnit::getData($conn,$data_ta['kodeunit']);
		
		$a_pointmhs = mKemahasiswaan::getPointMahasiswa($conn,$data_ta['nim']);
		$a_minimalpoin = $a_unit['jmlminimalpoin'] ? $a_unit['jmlminimalpoin'] : '0' ;
		
		$update = true;
		if ($update){ 
			mTA::updatePoint($conn,$r_key,$a_minimalpoin,$a_pointmhs);
		}

	}

	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post); // get data
	 
	$_SESSION['r_key']=$r_key;
	if(!empty($r_key)) {
		$rowd = array();
		$rowd += $p_model::getBimbingan($conn,$r_key,'bimbingan'); // ,$post);
		
		$n_bimbingan = 0;
		foreach($rowd['bimbingan'] as $t_rowd) {
			if(!empty($t_rowd['disetujui']))
				$n_bimbingan++;
		}
	}
	
	$r_kodeunit = Page::getDataValue($row,'kodeunit');
	$r_skslulus = Page::getDataValue($row,'skslulus');
	$r_bebanstudi = Page::getDataValue($row,'bebanstudi');
	$r_cekbimbingan = Page::getDataValue($row,'cekbimbingan');
	$r_cekjurnal = Page::getDataValue($row,'cekjurnal');
	$r_cekprasyarat = Page::getDataValue($row,'cekprasyarat');
	$r_nim = Page::getDataValue($row,'nim');

	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script src="scripts/jquery-1.7.1.min.js" type="text/javascript" charset="utf-8"></script>	
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
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
			<form name="pageform" id="pageform" method="post">
				<?	/*****************/
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
						
						$a_required = array('nim');
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						
						<?= Page::getDataTR($row,'nim') ?>
						
					<?	if(!empty($r_key)) { ?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Nama Mahasiswa</td>
							<td class="RightColumnBG"><?=$data_ta['nama']?></td>
						</tr>
						
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Info Akademik</td>
							<td class="RightColumnBG">
								<table>
									<tr>
										<td width="50">Fakultas</td>
										<td width="5">:</td>
										<td><?=$data_ta['fakultas']?> - Jurusan : <?=$data_ta['jurusan']?></td>
									</tr>
									<tr>
										<td>Semester</td>
										<td>:</td>
										<td> <?=$data_ta['semestermhs']?></td>
									</tr>
								</table>
							</td>
						</tr>
					<?	} ?>
					</table>
					</div>
				</center>
				<br>
				<center>
				<div class="tabs" style="width:<?= $p_tbwidth+200 ?>px">
					<ul>
						<li><a id="tablink" href="javascript:void(0)">Skripsi</a></li>
						<?	if(!empty($r_key)) { ?>
						<li><a id="tablink" href="javascript:void(0)">Bimbingan</a></li>
						<li><a id="tablink" href="javascript:void(0)">Jurnal Ilmiah</a></li>
						<li><a id="tablink" href="javascript:void(0)">Seminar Proposal</a></li>
						<? /* <li><a id="tablink" href="javascript:void(0)">Seminar Skripsi</a></li> */ ?>
						<li><a id="tablink" href="javascript:void(0)">Syarat Sidang</a></li>
						<?		if(!empty($r_cekprasyarat)) { ?>
						<li><a id="tablink" href="javascript:void(0)">Sidang Skripsi</a></li>
						<?		}
							} ?>
					</ul>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td colspan="2" class="DataBG">Skripsi</td>
						</tr>
						<?= Page::getDataTR($row,'judulta') ?>
						<?= Page::getDataTR($row,'topikta') ?>
						<?= Page::getDataTR($row,'judultaen') ?>
						<?= Page::getDataTR($row,'topiktaen') ?>
						<?= Page::getDataTR($row,'tahapta') ?>
						<?= Page::getDataTR($row,'progressta') ?>
						<?= Page::getDataTR($row,'tglmulai') ?>
						<?= Page::getDataTR($row,'tglselesai') ?>
						<?= Page::getDataTR($row,'statusta') ?>
						<tr>
							<td colspan="2" class="DataBG">
								Dosen Pembimbing &nbsp;
								<a href="<?= Route::navAddress('pop_pembimbing') ?>" rel="facybox">
									<img src="images/magnify.png" title="Cek Pembimbing" style="cursor:pointer">
								</a>
							</td>
						</tr>
						<?= Page::getDataTR($row,'niputama') ?>
						<?= Page::getDataTR($row,'nipco') ?>
						<tr>
							<td colspan="2" class="DataBG">Abstrak</td>
						</tr>
						<?= Page::getDataTR($row,'abstrakta') ?>
					</table>
					</div>
					
					<?	if(!empty($r_key)) { ?>
					<div id="items">
					
					<?	/*************/
						/* BIMBINGAN */
						/*************/
						
						$t_field = 'bimbingan';
						$t_colspan = count($a_detail[$t_field]['data'])+2;
						$t_dkey = $a_detail[$t_field]['key'];
						
						if(!is_array($t_dkey))
							$t_dkey = explode(',',$t_dkey);
					?>
					<table width="100%" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
						<tr>
							<td colspan="<?= $t_colspan ?>" class="DataBG">Bimbingan</td>
						</tr>
						<tr>
						<?	foreach($a_detail[$t_field]['data'] as $datakolom) { ?>
							<th align="center" class="HeaderBG"><?= $datakolom['label'] ?></th>
						<?	}
							if($c_bimbingan) { ?>
							<th align="center" class="HeaderBG" width="30" id="edit" style="display:none">Aksi</th>
						<?	} ?>
						</tr>
						<?	$i = 0;
							if(!empty($rowd[$t_field])) {
								foreach($rowd[$t_field] as $rowdd) {
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
									
									$t_keyrow = array();
									foreach($t_dkey as $t_key)
										$t_keyrow[] = $rowdd[trim($t_key)];
									
									$t_key = implode('|',$t_keyrow);
									$t_disetujui = false;
						?>
						<tr valign="top" class="<?= $rowstyle ?>">
						<?			foreach($a_detail[$t_field]['data'] as $datakolom) {
										// cek disetujui
										$t_kolom = uForm::getLabel($datakolom,$rowdd[$datakolom['kolom']]);
										if($datakolom['kolom'] == 'disetujui') {
											if(!empty($rowdd[$datakolom['kolom']]))
												$t_disetujui = true;
											
											if($c_valbimbingan) {
												$datakolom['add'] = 'onclick="goValBimbingan(\''.$t_key.'\')"';
												
												$t_kolom = uForm::getInput($datakolom,$rowdd[$datakolom['kolom']]);
											}
										}
										
						?>
							<td <?= empty($datakolom['align']) ? '' : ' align="'.$datakolom['align'].'"' ?>><?= $t_kolom ?></td>
						<?			}
									if($c_bimbingan) { ?>
							<td id="edit" align="center" style="display:none">
						<?				if(!$t_disetujui or ($c_bimbingan and $c_valbimbingan)) { ?>
								<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDeleteDetail('<?= $t_field ?>',this)" style="cursor:pointer">
						<?				} ?>
							</td>
						<?			} ?>
						</tr>
						<?		}
							}
							if($i == 0) { ?>
						<tr>
							<td align="center" colspan="<?= $t_colspan ?>">Data kosong</td>
						</tr>
						<?	}
							if($c_bimbingan) { ?>
						<tr valign="top" class="LeftColumnBG" id="edit" style="display:none">
						<?		foreach($a_detail[$t_field]['data'] as $datakolom) {
									$datakolom['nameid'] = $t_field.'_'.CStr::cEmChg($datakolom['nameid'],$datakolom['kolom']);
						?>
							<td<?= empty($datakolom['align']) ? '' : ' align="'.$datakolom['align'].'"' ?>><?= uForm::getInput($datakolom) ?></td>
						<?		} ?>
							<td align="center">
								<img title="Tambah Data" src="images/disk.png" onclick="goInsertDetail('<?= $t_field ?>')" style="cursor:pointer">
							</td>
						</tr>
						<?	} ?>
					</table>
					</div>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'juduljurnal') ?>
						<?= Page::getDataTR($row,'topik') ?>
						<?= Page::getDataTR($row,'namajurnal') ?>
						<?= Page::getDataTR($row,'edisi') ?>
						<?= Page::getDataTR($row,'url') ?>
						<?= Page::getDataTR($row,'statusvalidasi') ?>
					</table>
					</div>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td colspan="2" class="DataBG">Jadwal Ujian</td>
						</tr>
						<?= Page::getDataTR($row,'tglujianproposal') ?>
						<?= Page::getDataTR($row,'waktumulaiproposal,waktuselesaiproposal',' - ') ?>
						<?= Page::getDataTR($row,'koderuangproposal') ?>
						<tr>
							<td colspan="2" class="DataBG">Tim Penguji</td>
						</tr>
						<?= Page::getDataTR($row,'sekretaris') ?>
						<?= Page::getDataTR($row,'penguji1') ?>
						<?= Page::getDataTR($row,'penguji2') ?>
						<tr>
							<td colspan="2" class="DataBG">Hasil Akhir</td>
						</tr>
						<?= Page::getDataTR($row,'nilaiujianproposal') ?>
						<?= Page::getDataTR($row,'mengulangproposal') ?>
					</table>
					</div>
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'cekpoint') ?>
						<!--tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'cekpoint') ?></td>
							<td class="RightColumnBG"> 
							<?	
							if($a_pointmhs >= $a_minimalpoin ) { ?>
								<?= Page::getDataInput($row,'cekpoint') ?>
							<?	} else { ?>
								<span style="color:red">Jumlah Poin (<?= $a_pointmhs ?>) kurang dari syarat minimal poin (<?php echo $a_minimalpoin?>)</span>
							<?	} ?>
							</td>
						</tr-->
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'skslulus') ?></td>
							<td class="RightColumnBG">
								<span style="color:<?= $r_skslulus < $r_bebanstudi ? 'red' : 'black' ?>">
								<?= Page::getDataInput($row,'skslulus') ?>
								(minimal <?= Page::getDataInput($row,'bebanstudi') ?> sks)
								</span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'cekbimbingan') ?></td>
							<td class="RightColumnBG">
							<?	if($n_bimbingan >= 4) { ?>
								<?= Page::getDataInput($row,'cekbimbingan') ?>
							<?	} else { ?>
								<span style="color:red">Jumlah bimbingan disetujui (<?= $n_bimbingan ?> kali) tidak mencukupi (minimal 4 kali)</span>
							<?	} ?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'cekjurnal') ?></td>
							<td class="RightColumnBG">
							<?	if(empty($r_cekjurnal)) { ?>
								<span style="color:red">Mahasiswa belum menulis jurnal ilmiah atau jurnal ilmiah belum disetujui</span>
							<?	} else { ?>
								Jurnal ilmiah mahasiswa telah disetujui
							<?	} ?>
							</td>
						</tr>
						<?= Page::getDataTR($row,'cektoefl') ?>
						<?//= Page::getDataTR($row,'cektoafl') ?>
						<?= Page::getDataTR($row,'cekkum') ?>
					</table>
					</div>
					
					<?		if(!empty($r_cekprasyarat)) { ?>
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td colspan="2" class="DataBG">Jadwal Ujian</td>
						</tr>
						<?= Page::getDataTR($row,'tglujianakhir') ?>
						<?= Page::getDataTR($row,'waktumulaiakhir,waktuselesaiakhir',' - ') ?>
						<?= Page::getDataTR($row,'koderuangakhir') ?>
						<tr>
							<td colspan="2" class="DataBG">Tim Penguji</td>
						</tr>
						<?= Page::getDataTR($row,'ketuamajelisakhir') ?>
						<?= Page::getDataTR($row,'sekretarisakhir') ?>
						<?= Page::getDataTR($row,'penguji1akhir') ?>
						<?= Page::getDataTR($row,'penguji2akhir') ?>
						<tr>
							<td colspan="2" class="DataBG">Hasil Akhir</td>
						</tr>
						<?	foreach($a_unsurnilai as $t_unsur) {
								$t_value = CStr::formatNumber($a_nilai[$t_unsur['idunsurnilai']],2,true);
								$t_input = UI::createTextBox('n_'.$t_unsur['idunsurnilai'],$t_value,'ControlStyle',5,3,$c_edit,'onkeydown="return onlyNumber(event,this,true)" onblur="hitungNilai()"').' &nbsp; '.$t_unsur['prosentasenilai'].' %';
						?>
						<tr>
							<td class="LeftColumnBG"><?= $t_unsur['namaunsurnilai'] ?></td>
							<td class="RightColumnBG"><?= Page::getDataInputWrap($t_value,$t_input) ?></td>
						</tr>
						<?	} ?>
						<?= Page::getDataTR($row,'nilaiujianakhir') ?>
						<?= Page::getDataTR($row,'mengulangakhir') ?>
					</table>
					</div>
					<?		}
						} ?>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="cekprasyarat" id="cekprasyarat" value="<?= $r_cekprasyarat?>">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	<? if(empty($post) and !empty($r_key)){ ?>
		initEdit(false);
	<? }else if(!empty($post) and !empty($r_key)){ ?>
		initEdit(false);
	<? } else {?>
		initEdit(true);
	<? } ?>
	//initEdit(<?= empty($post) or !empty($r_key) ? false : true ?>);
	initTab();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	$('a[rel*=facybox]').facybox();
	
	$("#niputama_").xautox({strpost: "f=acdosen", targetid: "niputama"});
	$("#nipco_").xautox({strpost: "f=acdosen", targetid: "nipco"});
	$("#textnip").xautox({strpost: "f=acpembimbing", targetid: "bimbingan_nip", postid: "key"});
	
	$("#sekretarisproposal_").xautox({strpost: "f=acdosen", targetid: "sekretaris"});
	$("#penguji1proposal_").xautox({strpost: "f=acdosen", targetid: "penguji1"});
	$("#penguji2proposal_").xautox({strpost: "f=acdosen", targetid: "penguji2"});
	
	<? /* $("#textsekretarisseminar").xautox({strpost: "f=acdosen", targetid: "sekretarisseminar"});
	$("#textpenguji1seminar").xautox({strpost: "f=acdosen", targetid: "penguji1seminar"});
	$("#textpenguji2seminar").xautox({strpost: "f=acdosen", targetid: "penguji2seminar"}); */ ?>
	
	$("#textketuamajelisakhir").xautox({strpost: "f=acdosen", targetid: "ketuamajelisakhir"});
	$("#textsekretarisakhir").xautox({strpost: "f=acdosen", targetid: "sekretarisakhir"});
	$("#textpenguji1akhir").xautox({strpost: "f=acdosen", targetid: "penguji1akhir"});
	$("#textpenguji2akhir").xautox({strpost: "f=acdosen", targetid: "penguji2akhir"});
	
	// ketua majelis sidang akhir disamakan seperti dosen pembimbing 1
	if($("#ketuamajelisakhir").val() == "") {
		$("#ketuamajelisakhir").val($("#niputama").val());
		$("#textketuamajelisakhir").val($("#textniputama").val());
	}
});

function hitungNilai(elem) {
	var subnilai
	var nilai = "";
	<?	foreach($a_unsurnilai as $t_unsur) { ?>
	subnilai = jQuery.trim($("#n_<?= $t_unsur['idunsurnilai'] ?>").val());
	if(subnilai != "") {
		if(nilai == "") nilai = 0;
		nilai += (formatNumber(subnilai) * <?= $t_unsur['prosentasenilai'] ?>);
	}
	<?	} ?>
	
	if(nilai != "") {
		nilai = nilai/100;
		if(nilai > 100)
			nilai = 100;
		else
			nilai = Math.round(nilai);
	}
		
	$("#nilaiujianakhir").val(nilai);
}

function goValBimbingan(subkey) {
	document.getElementById("act").value = "valbimbingan";
	document.getElementById("subkey").value = subkey;
	goSubmit();
}

</script>

<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
<script type="text/javascript">
	$(function() {
	$.mask.definitions['~'] = "[+-]";
	$("#waktumulaiproposal").mask("99:99");
	$("#waktuselesaiproposal").mask("99:99"); 
	$("#waktumulaiakhir").mask("99:99");
	$("#waktuselesaiakhir").mask("99:99"); 
	
	
});
</script>
</body>
</html>
