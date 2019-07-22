<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug = true;
	// include
	require_once(Route::getModelPath('laporanmhs'));
	require_once(Route::getModelPath('progpend'));
	require_once(Route::getUIPath('form'));
	//require_once($conf['includes_dir'].'fpdf/tcetak2.php');
	require_once($conf['includes_dir'].'fpdf/fpdf_ktm.php');
	require_once($conf['includes_dir'].'PhpWord/PhpWord/Autoloader.php');
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_angkatan = (int)$_REQUEST['angkatan'];
	$r_npm = $_REQUEST['npm'];
	$r_format = $_REQUEST['format'];
	$r_tglberlaku = $_REQUEST['tglberlaku'];
	
	// properti halaman
	$p_title = 'Laporan KHS';
	$p_tbwidth = 720;
		
	// $p_namafile = 'ktm_'.$r_kodeunit.'_'.$r_angkatan;
	$p_namafile = 'ijazah_'.$r_npm.'.docx';

	$a_data = mLaporanMhs::getIjazahMhs($conn,'L',$r_kodeunit,$r_npm,$r_angkatan);


	\PhpOffice\PhpWord\Autoloader::register();
	
	$tpr = new \PhpOffice\PhpWord\TemplateProcessor($conf['includes_dir'].'PhpWord/PhpWord/template/ijazah.docx');
	foreach ($a_data as $key => $row) {
		//bagian depan
		$tpr->setValue('nmmhs',ucwords($row['nama']));
		$tpr->setValue('tlahir',ucwords($row['tmplahir']));
		$tpr->setValue('tglahir',cstr::FormatdateInd($row['tgllahir']));
		$tpr->setValue('namagelar',$row['deskgelar']." (".$row['gelar'].")");
		$tpr->setValue('prodi',$row['jurusan']);
		$tpr->setValue('fakultas',$row['fakultas']);
		$tpr->setValue('tglijazah',cstr::formatdateInd($row['ijazah_tgl']));
		$tpr->setValue('namadekan',$row['namadekan']);
		$tpr->setValue('skbanpt_no',$row['skbanpt_no']);
		
		//bagian belakang
		$tpr->setValue('tgllulus',cstr::formatdateInd($row['tgl_lulus']));
		$tpr->setValue('skrektor',$row['skrektor_no']);
		$tpr->setValue('nm',$row['nim']);
		$tpr->setValue('noijasah',$row['ijazah_no']);
		$tpr->setValue('pin',$row['ijazah_nopin']);
	}
	$temp = '/tmp/temp.docx'; // $temp = $conf['includes_dir'].'PhpWord/PhpWord/temp/temp.docx';
	$tpr->saveAs($temp);

	ob_clean();
	
	header('Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
	header('Content-Disposition: attachment; filename="'.$p_namafile.'"');
	
	readfile($temp);
	@unlink($temp);
?>
