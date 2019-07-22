<?
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	Modul::getFileAuth();// include
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('pembayaranfrm'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	Page::setHeaderFormat($r_format,$p_namafile);
	
	// properti halaman
	$p_title = 'Laporan Pembelian Token (Formulir)';
	$p_tbwidth = 900;
	$p_namafile = 'laporanformulir';
	if($_REQUEST['tglakhir']=='')
		$_REQUEST['tglakhir'] = $_REQUEST['tglmulai'];
	$r_tglmulai = cStr::formatDate($_REQUEST['tglmulai']);
	$r_tglakhir = cStr::formatDate($_REQUEST['tglakhir']);
	$r_jenis = $_REQUEST['jenish2h'];
	$r_periodedaftar = CStr::removeSpecial($_REQUEST['periodedaftar']);
	
	$arr_jenis = mCombo::arrJenish2h();
	
	// header
	Page::setHeaderFormat($r_format,$p_namafile);
	
	if($r_jenis)
		foreach($r_jenis as $i => $v){
			$jenis[] = $arr_jenis[$v];
	}
	
	$data = mPembayaranfrm::laporanbytanggal($conn,$r_tglmulai,$r_tglakhir,$r_jenis,'0',$r_periodedaftar);
                
	
?>
<html>
	<head>
		<title><?= $p_title ?></title>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<link rel="icon" type="image/x-icon" href="images/favicon.png">
		<link href="style/stylerep.css" rel="stylesheet" type="text/css">
	</head>
	<body>
	<div align="center">
		<table width="<?=$p_tbwidth?>" >
			<tr>
				<td colspan="3">
				<? require_once('inc_headrep.php');?>
				</td>
			</tr>
            <tr>
            	<td width="15%"><strong>Periode</strong></td>
            	<td width="1%"><strong>:</strong></td>
                <td><?=$r_periodedaftar?></td>
            </tr>
            <tr>
            	<td width="15%"><strong>Tanggal</strong></td>
            	<td width="1%"><strong>:</strong></td>
                <td><?=cStr::formatDate($r_tglmulai).' s/d '.cStr::formatDate($r_tglakhir)?></td>
            </tr>
            <tr>
            	<td width="15%"><strong>Offline / Online</strong></td>
            	<td width="1%"><strong>:</strong></td>
                <td>
				<?
				if($r_jenis)
					echo implode(' , ',$jenis);
                ?>
                </td>
            </tr>
		</table>
		<table width="<?=$p_tbwidth?>" cellpadding="3" border="1" style="border-collapse:collapse;">
			<tr>
            	<th>No</th>
            	<th>Tanggal</th>
            	<th>Jalur</th>
            	<th>Sistem Kuliah</th>
            	<th>Gelombang</th>
            	<th>Jenjang</th>
            	<th>Pilihan</th>
            	<th>Petugas</th>
            	<th>Token</th>
            	<th>Jumlah</th>
            </tr>
            <?
			$no = 0; 
			if($data)
			foreach($data as $i => $val){$no++;
			$total += $val['jumlahbayar'];
			?>
            <tr>
            	<td><?=$no?></td>
            	<td><?=cStr::formatDate($val['tglbayar'])?></td>
            	<td><?=$val['jalurpenerimaan']?></td>
            	<td><?=$val['namasistem']?></td>
            	<td><?=$val['namagelombang']?></td>
            	<td><?=$val['programpend']?></td>
            	<td><?=$val['jumlahpilihan']?></td>
            	<td><?=$val['nip']?></td>
            	<td><?=$val['notoken']?></td>
            	<td align="right"><?=cStr::FormatNumber($val['jumlahbayar'])?></td>
            </tr>
            <? }?>
            <tr>
            	<td colspan="9" align="right"><strong>TOTAL</strong></td>
            	<td align="right"><strong><?=cStr::FormatNumber($total)?></strong></td>
            </tr>
		</table>
	</div>
	</body>
</html>
