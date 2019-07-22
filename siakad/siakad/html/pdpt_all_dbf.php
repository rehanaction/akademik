<?php
	ob_clean();
	//die('cek');
	$filter = $_POST["filter"];
	$format = $_POST["pilih"];
	$c_unit = $_POST["unit"];	
	
	$c_thnpelaporan = $_POST["tahunpelaporan"];	
	$c_periode = $_POST["tahun"].$_POST['semester'];
	$conn->debug = false;
	include('pdpt_msmhs_dbf.php');
	include('pdpt_trakm_dbf.php');
	include('pdpt_trnlm_dbf.php');
	include('pdpt_trlsm_dbf.php');
	include('pdpt_trakd_dbf.php');
	
	// include('pdpt_tbkmk_dbf.php');
	// include('pdpt_msdos_dbf.php');
	// include('pdpt_trkap_dbf.php');
	// include('pdpt_mspst_dbf.php');
	// include('pdpt_trlsd_dbf.php');
	// include('pdpt_trpud_dbf.php');
	// include('pdpt_trfas_dbf.php');
	
	$a = 'file_dbf/copy/MSMHS.DBF';
	$b = 'file_dbf/copy/TRAKM.DBF';
	$c = 'file_dbf/copy/TRNLM.DBF';
	$d = 'file_dbf/copy/TRLSM.DBF';
	$e = 'file_dbf/copy/TRAKD.DBF';
	
	
	// $a = '/var/www/poltekes/www/poltekesakad/file_dbf/copy/MSMHS.DBF';
	// $b = '/var/www/poltekes/www/poltekesakad/file_dbf/copy/TBKMK.DBF';
	// $c = '/var/www/poltekes/www/poltekesakad/file_dbf/copy/TRAKD.DBF';
	// $d = '/var/www/poltekes/www/poltekesakad/file_dbf/copy/TRAKM.DBF';
	// $e = '/var/www/poltekes/www/poltekesakad/file_dbf/copy/TRLSM.DBF';
	// $f = '/var/www/poltekes/www/poltekesakad/file_dbf/copy/TRNLM.DBF';
	
	// $g = '/var/www/poltekes/www/poltekesakad/file_dbf/copy/MSDOS.DBF';
	// $h = '/var/www/poltekes/www/poltekesakad/file_dbf/copy/TRKAP.DBF';
	// $i = '/var/www/poltekes/www/poltekesakad/file_dbf/copy/MSPST.DBF';
	// $j = '/var/www/poltekes/www/poltekesakad/file_dbf/copy/TRLSD.DBF';
	// $k = '/var/www/poltekes/www/poltekesakad/file_dbf/copy/TRPUD.DBF';
	// $l = '/var/www/poltekes/www/poltekesakad/file_dbf/copy/TRFAS.DBF';
	
	// $add = array($a, $b, $c, $d, $e, $f, $g, $h, $i, $j, $k, $l);
	$add = array($a, $b, $c, $d, $e);
	//print_r($add);
	$zip = new ZipArchive;
	$zipname = 'file_dbf/copy/PDPT_dbf.zip';
	@unlink($zipname);
	if($zip->open($zipname, ZipArchive::CREATE) === true) {
		foreach($add as $fadd)
			$zip->addFile($fadd);
			//echo $fadd."<br>";
		$zip->close();
	}
	//die('hentikan');
	//ambil kodeepsbedprodi
	$rs_kode = $conn->GetOne("select epskodeprodi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	$rec = array();
	$rec['kode_program_studi'] = $c_unit;
	$rec['kode_program_studi_eps'] = $rs_kode;
	$rec['periode'] = $c_periode;
	$rec['thnpelaporan'] = $c_thnpelaporan;
	$rec['tabelpdpt'] = 'pdpt_all_dbf';
	$rec['nip'] = Modul::getUserName();
	$rec['namapetugas'] = Modul::getUserDesc();
	$rec['t_updatetime'] = date("Y-m-d H:i:s");
	$rec['t_ipaddress'] = $_SERVER['REMOTE_ADDR']; 
	$rec['format'] = $format; 
	$ok = Query::recInsert($conn,$rec,'epsbed.ms_downloadpdpt');	
	
	header('Content-Type: application/zip');
	header('Content-disposition: attachment; filename=PDPT_dbf.zip');
	header('Content-Length: '.filesize($zipname));
	readfile($zipname);
?>
