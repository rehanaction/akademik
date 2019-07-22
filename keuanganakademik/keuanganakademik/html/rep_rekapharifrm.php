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
	
	$r_tahun = cStr::removeSpecial($_REQUEST['tahun']);
	$r_bulan = cStr::removeSpecial($_REQUEST['bulan']);
	$r_jenis = $_REQUEST['jenish2h'];
	$r_periodedaftar = CStr::removeSpecial($_REQUEST['periodedaftar']);
	
	$arr_jenis = mCombo::arrJenish2h();
	
	// header
	Page::setHeaderFormat($r_format,$p_namafile);
	
	if($r_jenis)
		foreach($r_jenis as $i => $v){
			$jenis[] = $arr_jenis[$v];
	}
	
	$arr_data = mPembayaranfrm::laporanbybulan($conn,$r_tahun,$r_bulan,$r_jenis,'0',$r_periode);
	
	if($arr_data)
		foreach($arr_data as $i => $v){
				$data[$v['tglbayar']][] = $v;
				$total[$v['tglbayar']] += $v['jumlahbayar'];
			}
                
	
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
            	<td width="15%"><strong>Bulan</strong></td>
            	<td width="1%"><strong>:</strong></td>
                <td><?=Date::indoMonth($r_bulan).' '.$r_tahun?></td>
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
            	<th>Jumlah</th>
            	<th>Total Pembayaran</th>
            </tr>
            <?
			$no = 0; 
			if($data)
			foreach($total as $i => $val){$no++;
			$totalall += $val;
			$totalmhs += count($data[$i]);
			?>
            <tr>
            	<td><?=$no?></td>
            	<td><?=cStr::formatDate($i)?></td>
            	<td align="right"><a href="<?=Route::navAddress('rep_harianfrm&tglmulai='.cStr::formatDate($i).'&kodeunit='.$r_kodeunit.'&jenis='.$r_jenis.'&periodedaftar='.$r_periodedaftar)?>" target="_blank"><?=cStr::FormatNumber(count($data[$i]))?></a></td>
            	<td align="right"><?=cStr::FormatNumber($val)?></td>
            </tr>
            <? }?>
            <tr>
            	<td colspan="2" align="right"><strong>TOTAL</strong></td>
            	<td align="right"><strong><?=cStr::FormatNumber($totalmhs)?></strong></td>
            	<td align="right"><strong><?=cStr::FormatNumber($totalall)?></strong></td>
            </tr>
		</table>
	</div>
	</body>
</html>