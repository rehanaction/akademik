<?php
/*ini_set('display_errors',1);
$data = Array ( 'sistemkuliah' => 'P', 'jalurpenerimaan' => 'Umum', 'gelombang' => '01', 'periode' => '20152','kodeunit' => '2210110', 'ispendaftar' => 1, 'aksescurl' => 1 ,'act' => 'generate', 'nim'=>'15200001');
$_POST = $data;
*/
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('loggenerate'));
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getUIPath('combo'));

	$c_edit = $_POST['aksescurl'];
	$r_act = $_POST['act'];
	$r_sistem = $_POST['sistemkuliah'];
	$r_jalur = $_POST['jalurpenerimaan'];
	$r_gelombang = $_POST['gelombang'];
	$r_kodeunit = $_POST['kodeunit'];
	$r_periode = $_POST['periode'];
	$r_nim = $_POST['nim'];

	if ($r_act == 'generate' and $c_edit) {
	 
		// filter data
		$a_filter = array();
		$a_filter['sistemkuliah'] = $r_sistem;
		$a_filter['jalurpenerimaan'] = $r_jalur;
		$a_filter['gelombang'] = $r_gelombang;
		$a_filter['kodeunit'] = $r_kodeunit;
		$a_filter['ispendaftar'] = true; // hanya pendaftar, tidak termasuk maba
		$a_filter['nim'] = $r_nim;
		
		//print_r($a_filter);
		
		$conn->BeginTrans();
		
		list($err,$msg,$jml) = mTagihan::generateTagihan($conn,$a_filter,$r_periode,null);
		
		// buat log
		if(!$err) {
			$record = array();
			$record['periodetagihan'] = $r_periode;
			$record['bulantahun'] = date('Ym');
			$record['kodeunit'] = CStr::cStrNull($r_kodeunit);
			$record['jml'] = (int)$jml;
			$record['isgen'] = 'G';
			$record['ismahasiswa'] = 0;
			
			$err = mLoggenerate::insertRecord($conn,$record);
		}
		
		$ok = Query::isOK($err);
		$conn->CommitTrans($ok);
		
		$p_posterr = $err;
		$p_postmsg = $msg.' ('.$jml.' data mahasiswa)';
	}
	
	echo $p_posterr.'|'.$p_postmsg;
	
	?>
