<?
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	Modul::getFileAuth();
	
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('bank'));
	require_once(Route::getModelPath('pembayaran'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	Page::setHeaderFormat($r_format,$p_namafile);
	
	// properti halaman
	$p_title = 'Laporan Pembayaran Rutin';
	$p_tbwidth = 900;
	$p_namafile = 'laporanpembayaranrutin';
	
	if($_REQUEST['tglmulai']=='' and $_REQUEST['tglakhir']=='')
		$_REQUEST['tglmulai'] = date('d-m-Y');
	else if($_REQUEST['tglmulai']=='')
		$_REQUEST['tglmulai'] = $_REQUEST['tglakhir'];
	if($_REQUEST['tglakhir']=='')
		$_REQUEST['tglakhir'] = $_REQUEST['tglmulai'];
	
	$r_tglmulai = cStr::formatDate($_REQUEST['tglmulai']);
	$r_tglakhir = cStr::formatDate($_REQUEST['tglakhir']);
	
	$r_jenis = $_REQUEST['jenish2h'];
	$r_kodeunit = $_REQUEST['kodeunit'];
	$infounit = mAkademik::infoUnit($conn,$r_kodeunit);
	
	$arr_jenis = mCombo::arrJenish2h();
	
	$r_sistem = $_REQUEST['sistemkuliah'];
	$r_angkatan = $_REQUEST['angkatan'];
	$r_bank = $_REQUEST['bank'];
	
	if(!empty($r_sistem)) {
		$r_namasistem = current(mAkademik::getArraysistemkuliah($conn,$r_sistem));
		$r_namasistem = $r_namasistem['namasistem'].' '.$r_namasistem['tipeprogram'];
	}
	else
		$r_namasistem = 'Semua';
	
	if(!empty($r_bank))
		$r_namabank = mBank::getNamaBank($conn,$r_bank);
	else
		$r_namabank = 'Semua';
	
	$r_namaunit = mUnit::getNamaUnit($conn,$r_kodeunit);
	
	// header
	Page::setHeaderFormat($r_format,$p_namafile);
	
	if($r_jenis)
		foreach($r_jenis as $i => $v){
			$jenis[] = $arr_jenis[$v];
	}
	
	$data = mPembayaran::laporanbytanggal($conn,$r_tglmulai,$r_tglakhir,$r_jenis,'0',$infounit,$r_sistem,$r_angkatan,$r_bank);
	$adm1 = 0;
	$adm2 = 500;
	
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
		<table width="<?=$p_tbwidth?>">
			<tr>
				<td colspan="6">
				<? require_once('inc_headrep.php');?>
				</td>
			</tr>
			
			<tr>
                <td colspan="4"><br><center><b><?=cStr::formatDateInd($r_tglmulai).' s/d '.cStr::formatDateInd($r_tglakhir)?></b></center><br></td>
            </tr>
		</table>
		<table width="<?=$p_tbwidth?>" cellpadding="3" border="1" style="border-collapse:collapse;">
			<tr>
				<th>No</th>
				<th>No Nota</th>
            	<th>Tanggal</th>
            	<th>NIM</th>
            	<th>Nama</th>
            	<th>Jurusan</th>
            	<th>Petugas</th>
            	<th>Jumlah Bayar</th>
				<?php if($r_bank=='FINNET'){ ?>
					<th>Adm 1</th>
            		<th>Adm 2</th>
					<th>Jumlah Bayar+Adm</th>
				<?php } ?>

            </tr>
            <?
			$no = 0; 
			if($data)
			foreach($data as $i => $val){$no++;
			$total += $val['jumlahuang'];
			$tadm1 += $adm1;
			$tadm2 += $adm2;
			?>
            <tr>
				<td><?=$no?></td>
            	<td><?=$val['refno']?></td>
            	<td><?=cStr::formatDateInd($val['tglbayar'])?></td>
            	<td><?=$val['nimpendaftar']?></td>
            	<td><?=$val['nama']?></td>
            	<td><?=$val['namaunit']?></td>
            	<td><?if(!empty($val['nip'])){echo $val['nip']; }else{echo $val['companycode']; }?></td>
            	
            	<td align="right">Rp. <?=cStr::FormatNumber($val['jumlahuang'])?></td>
				<?php if($r_bank=='FINNET'){ ?>
					<td align="right">Rp. <?=cStr::FormatNumber($adm1)?></td>
					<td align="right">Rp. <?=cStr::FormatNumber($adm2)?></td>
					<td align="right">Rp. <?=cStr::FormatNumber($val['jumlahuang']+$adm1+$adm2)?></td>
				<?php } ?>
            </tr>
            <? }?>
            <tr>
            	<td colspan="7" align="right"><strong>TOTAL</strong></td>
				<td align="right"><strong><?=cStr::FormatNumber($total)?></strong></td>
				<?php if($r_bank=='FINNET'){ ?>
            	
				<td align="right"><strong><?=cStr::FormatNumber($tadm1)?></strong></td>
				<td align="right"><strong><?=cStr::FormatNumber($tadm2)?></strong></td>
				<td align="right"><strong><?=cStr::FormatNumber($total+$tadm1+$tadm2)?></strong></td>
				<?php }  ?>
            </tr>
		</table>
	</div>
	</body>
</html>
