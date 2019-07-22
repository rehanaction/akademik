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
	$p_title = 'Laporan Detail Pembayaran Rutin';
	$p_tbwidth = 900;
	$p_namafile = 'laporanpembayaranrutindetail';
	
	if($_REQUEST['tglmulai']=='' and $_REQUEST['tglakhir']=='')
		$_REQUEST['tglmulai'] = date('d-m-Y');
	else if($_REQUEST['tglmulai']=='')
		$_REQUEST['tglmulai'] = $_REQUEST['tglakhir'];
	if($_REQUEST['tglakhir']=='')
		$_REQUEST['tglakhir'] = $_REQUEST['tglmulai'];
	
	$r_tglmulai = cStr::formatDate($_REQUEST['tglmulai']);
	$r_tglakhir = cStr::formatDate($_REQUEST['tglakhir']);
	
	$r_jenistagihan = $_REQUEST['jenistagihan'];
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
	
	$data = mPembayaran::laporanbytanggalDetail($conn,$r_tglmulai,$r_tglakhir,$r_jenis,'0',$infounit,$r_sistem,$r_angkatan,$r_bank,$r_jenistagihan);
	$adm1 = 1500;
	$adm2 = 0;
	
?>
<html>
	<head>
		<title><?= $p_title ?></title>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<link rel="icon" type="image/x-icon" href="images/favicon.png">
		<link href="style/stylerep.css" rel="stylesheet" type="text/css">
		<style type="text/css">
				table { page-break-inside:auto }
				tr    { page-break-inside:avoid; page-break-after:auto }
				thead { display:table-header-group }
				tfoot { display:table-footer-group }
		</style>
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
            	<th>ID Tagihan</th>
            	<th>NIM</th>
            	<th>Nama</th>
            	<th>Jenis Tagihan</th>
            	<th>Tanggal Bayar</th>
            	<th>Jumlah Bayar</th>
			<?php if($r_bank=="FINNET") { ?>
				<th>Biaya Adm</th>
			<?php } ?>
            </tr>
            <?
			$no = 0;
			$tadm = 1; 
			
			if($data)
			foreach($data as $i => $val){
			
			$no++;
			$adm = 0;
			$total += $val['nominalbayar'];
			$subtotal_plg += $val['nominalbayar'];
			?>
            <tr>
				<td><?=$no?></td>
            	<td><?=$val['idtagihan']?></td>
				<td><?=$val['nim']?></td>
				<td><?=$val['nama']?></td>
				<td><?=$val['namajenistagihan']."-".Akademik::getNamaPeriode($val['periode'])?></td>
            	<td><?=cStr::formatDateInd($val['tglbayar'])?></td>
            	<td align="right">Rp. <?=cStr::FormatNumber($val['nominalbayar'])?></td>
				<?php 
			
				if($r_bank=="FINNET") { ?>
						<td></td>
			<?php } ?>
            </tr>
			
			<?
				if ($data[$i+1]['idpembayaran'] != $val['idpembayaran'] and $data[$i+1]['nim'] == $val['nim']) {
					$tadm=$tadm+1;
				}
				
				if ($data[$i+1]['nim'] != $val['nim']) { 
					$adm = $tadm*500;
					$adm2 = $adm2+$adm;
				
				?>
			<tr>
						<td align="center" colspan=6><strong>SUB TOTAL<strong></td>
						<td align="right"><strong>Rp.<?= cStr::FormatNumber($subtotal_plg) ?><strong></td>
						<?php if($r_bank=="FINNET") { ?>
						<td align="right"><strong>Rp.<?= cStr::FormatNumber($adm) ?><strong></td>
						<?php } ?>
			</tr>
			<?		
					$tadm=1;
					$subtotal_plg = 0;
				}
		
				}
				
			?>
            <tr>
            	<td colspan="6" align="center"><strong>TOTAL</strong></td>
				<td align="right"><strong>Rp. <?=cStr::FormatNumber($total)?></strong></td>
				<?php if($r_bank=="FINNET") { ?>
					<td align="right"><strong>Rp. <?=cStr::FormatNumber($adm2)?></strong></td>
				<?php } ?>
            </tr>
		</table>
	</div>
	</body>
</html>
