<?php
	ob_clean();
	require_once(Route::getModelPath('unit'));
	//$conn->debug=true;
	$filter = $_POST["filter"];
	$format = $_POST["pilih"];
	$f_unit = $_POST["unit"];
		
	$c_thnpelaporan = $_POST["tahunpelaporan"];	
	$c_periode = $_POST["tahun"].$_POST['semester'];	
	
	$unit = mUnit::getData($conn,$f_unit);
	$sqlu = "select kodeunit from gate.ms_unit where infoleft >= '".$unit['infoleft']."' and inforight <= '".$unit['inforight']."' and isakad=-1 and level in(1,2)";
	$a_unit=$conn->GetArray($sqlu);
	foreach($a_unit as $row_unit){
		$c_unit=$row_unit['kodeunit'];
		$_POST["unit"]=$row_unit['kodeunit'];
		include('pdpt_msmhs2.php');
		include('pdpt_trakm2.php');
		include('pdpt_trnlm2.php');
		include('pdpt_trlsm2.php');
		include('pdpt_trakd2.php');
		
		// include('pdpt_tbkmk2.php');	
		// include('pdpt_msdos2.php');
		// include('pdpt_trkap2.php');
		// include('pdpt_mspst2.php');
		// include('pdpt_trlsd2.php');
		// include('pdpt_trpud2.php');
		// include('pdpt_trfas2.php');
		
		$a = 'pdpt/MSMHS.csv';
		$b = 'pdpt/TRAKM.csv';
		$c = 'pdpt/TRNLM.csv';
		$d = 'pdpt/TRLSM.csv';
		$e = 'pdpt/TRAKD.csv';
		
		// $b = 'pdpt/TBKMK.csv';
		// $g = 'pdpt/MSDOS.csv';
		// $h = 'pdpt/TRKAP.csv';
		// $i = 'pdpt/MSPST.csv';
		// $j = 'pdpt/TRLSD.csv';
		// $k = 'pdpt/TRPUD.csv';
		// $l = 'pdpt/TRFAS.csv';
		
		// $add = array($a, $b, $c, $d, $e, $f, $g, $h, $i, $j, $k, $l);
		$add = array($a, $b, $c, $d, $e);
		
		$zip = new ZipArchive;
		$zipname = 'PDPT_'.$c_unit.'.zip';
		@unlink($zipname);
		if($zip->open($zipname, ZipArchive::CREATE) === true) {
			foreach($add as $fadd)
				$zip->addFile($fadd);
			$zip->close();
		}
		
		//kumpulkan zipnya
		$arr_zip[]=$zipname;
		//ambil kodeepsbedprodi
		$rs_kode = $conn->GetOne("select epskodeprodi from akademik.ak_prodi where kodeunit='$c_unit'");
		
		$rec = array();
		$rec['kode_program_studi'] = $c_unit;
		$rec['kode_program_studi_eps'] = $rs_kode;
		$rec['periode'] = $c_periode;
		$rec['thnpelaporan'] = $c_thnpelaporan;
		$rec['tabelpdpt'] = 'pdpt_all';
		$rec['nip'] = Modul::getUserName();
		$rec['namapetugas'] = Modul::getUserDesc();
		$rec['t_updatetime'] = date("Y-m-d H:i:s");
		$rec['t_ipaddress'] = $_SERVER['REMOTE_ADDR']; 
		$rec['format'] = $format; 
		$ok = Query::recInsert($conn,$rec,'epsbed.ms_downloadpdpt');
		
		
	}
	$all_zip = new ZipArchive;
	$all_zipname='pdpt/PDPT_all.zip';
	@unlink($all_zipname);
	if($all_zip->open($all_zipname, ZipArchive::CREATE) === true) {
		foreach($arr_zip as $fadd_all)
			$all_zip->addFile($fadd_all);
		$all_zip->close();
	}
	//die();
	header('Content-Type: application/zip');
	header('Content-disposition: attachment; filename='.$all_zipname);
	header('Content-Length: '.filesize($all_zipname));
	readfile($all_zipname);
?>
