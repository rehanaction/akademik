<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_idpegawai = CStr::removeSpecial($_REQUEST['idpegawai']);
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_periode = CStr::removeSpecial($_REQUEST['periode']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('gaji'));
	
	// definisi variable halaman	
	$p_tbwidth = 900;
	$p_file = 'slipgaji_'.$r_kodeunit;
	$p_model = 'mGaji';
	$p_window = 'Slip Gaji Pegawai';
	
	// header
	switch($r_format) {
		case 'doc';
			header("Content-Type: application/msword");
			header('Content-Disposition: attachment; filename="'.$p_file.'.doc"');
			break;
		case 'xls' :
			header("Content-Type: application/msexcel");
			header('Content-Disposition: attachment; filename="'.$p_file.'.xls"');
			break;
		default : header("Content-Type: text/html");
	}
	
	//bila diunduh bentuk doc atau excel, koma rupiah dihilangkan
	$dot = true;
	if($r_format == 'doc' or $r_format == 'xls')
		$dot = false;
		
	//mendapatkan data gaji
    $a_data = $p_model::repSlipGaji($conn,$r_periode,$r_kodeunit,$r_idpegawai);
	$rs = $a_data['list'];
	$key = $r_periode.'|'.$r_idpegawai;
	$a_tunj = $p_model::getTunjTetapSlip($conn,$key);
	$a_jtunj = $p_model::getTunjTetapGaji($conn);
	$a_jtunjdet = $p_model::getTunjTetapGajiDet($conn);
	$a_tunja = $p_model::getTunjAwalSlip($conn,$key);
	$a_jtunjawal = $p_model::getTunjTetapAwal($conn);
	$a_jtunjawaldet = $p_model::getTunjTetapAwalDet($conn);
	$a_ttunj = $p_model::getTunjPenyesuaianSlip($conn,$key);
	$a_jttunj = $p_model::getTunjPenyesuaian($conn);
	$a_pot = $p_model::getPotonganSlip($conn,$key);
	$a_jpot = $p_model::getJnsPotongan($conn);
	
	$a_tunjstrukturallain = $p_model::getTunjTetapStrukLain($conn,$key);
	$a_struktural = $p_model::getInfoStruktural($conn);
	
	$p_title = 'Slip Gaji Pegawai <br />';
	if(!empty($a_data['namaunit']))
		$p_title .= 'Unit '.$a_data['namaunit'].'<br />';
	
	$p_title .= 'Periode '.$a_data['namaperiode'];
	
	
?>
<html>
<head>
	<title><?= $p_window; ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<style>
	table { border-collapse:collapse }
	div,td,th {
	font-family:Verdana, Arial, Helvetica, sans-serif;
	font-size:12px;
	}
	td,th { border:1px solic black }
	</style>
</head>
<body>
<? 
	$no=0;
	while($row = $rs->FetchRow()){
		$no++;
?>
	<div align="center" style="page-break-after:always">
		<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<?	/********/
			/* DATA */
			/********/
		?>
		<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
		<table width="<?= $p_tbwidth-22 ?>" style="border:1px solid;" cellpadding="4" cellspacing="0" align="center">
			<tr>
				<td>Periode</td>
				<td>:</td>
				<td colspan="4"><b><?= $a_data['namaperiode'] ?></b></td>
			</tr>
			<tr>
				<td>Nama</td>
				<td>:</td>
				<td colspan="4"><b><?= $row['namapegawai'] ?></b></td>
			</tr>
			<tr>
				<td>Jabatan</td>
				<td>:</td>
				<td colspan="4"><?= $row['jabatanstruktural'] ?></td>
			</tr>
			<tr>
				<td>Pendidikan</td>
				<td>:</td>
				<td colspan="4"><?= $row['namapendidikan'] ?></td>
			</tr>
			<?if($row['idtipepeg'] == 'D' or $row['idtipepeg'] == 'AD'){?>
			<tr>
				<td>Fungsional</td>
				<td>:</td>
				<td colspan="4"><?= $row['fungsional'] ?></td>
			</tr>
			<?}?>
			<tr>
				<td>Masa Kerja</td>
				<td>:</td>
				<td colspan="4"><?= $row['mkgaji'] ?></td>
			</tr>
			<tr>
				<td colspan="6">&nbsp;</td>
			</tr>
			<tr>
				<td>Gaji Pokok</td>
				<td>: Rp.</td>
				<td align="right" style="padding-right:30px"><?= CStr::formatNumber($row['gapok'],0,$dot) ?></td>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3">Tunjangan</td>
				<td colspan="3">Potongan</td>
			</tr>
			<tr style="border:none">
				<td colspan="3" valign="top" style="border:none" width="50%">
					<table width="95%" cellpadding="4" cellspacing="0">
						<?
						if(count($a_jtunj)>0){
							$tunjangantetap = !empty($row['gapok']) ? $row['gapok'] : 0;
							foreach($a_jtunj as $ikey=>$val){
								if($ikey == $a_jtunjdet[$row['idjenispegawai']][$ikey]){
									$tunjangantetap += $a_tunj[$row['idpegawai']][$ikey];
						?>
						<tr>
							<td width="50%">- <?= $val?></td>
							<td width="10%">: Rp.</td>
							<td width="35%" align="right"><?= CStr::formatNumber($a_tunj[$row['idpegawai']][$ikey],0,$dot); ?></td>
						</tr>
						<?
								}
								
								$tunjstrukturallain = $a_tunjstrukturallain[$row['idpegawai']][$ikey];						
								if(count($tunjstrukturallain)>0){
									foreach($tunjstrukturallain as $keystruk=>$valstruk){
										$tunjangantetap += $valstruk;
						?>
						<tr>
							<td width="50%">- Tunjangan Struktural Lain (<?= $a_struktural[$keystruk]?>)</td>
							<td width="10%">: Rp.</td>
							<td width="35%" align="right"><?= CStr::formatNumber($valstruk,0,$dot); ?></td>
						</tr>
						<?			}
								}						
							}
						}
						?>
						<tr>
							<td colspan="2">&nbsp;</td>
							<td valign="bottom" align="right">________________ +</td>
						</tr>
						<?
							$sistem = $tunjangantetap;
						?>
						<tr style="font-weight:bold">
							<td width="50%" align="right">SISTEM</td>
							<td width="10%">: Rp.</td>
							<td width="35%" align="right"><?= CStr::formatNumber($sistem,0,$dot) ?></td>
						</tr>
					</table>
					<br>
					
					<table width="95%" cellpadding="4" cellspacing="0">
						<?
						if(count($a_jttunj)>0){
							$tunjangantdtetap=0;
							foreach($a_jttunj as $ikey=>$val){
								$tunjangantdtetap += $a_ttunj[$row['idpegawai']][$ikey];
						?>
						<tr>
							<td width="50%">- <?= $val?></td>
							<td width="10%">: Rp.</td>
							<td width="35%" align="right"><?= CStr::formatNumber($a_ttunj[$row['idpegawai']][$ikey],0,$dot); ?></td>
						</tr>
						<?	}
						}
						if($row['idtipepeg']=='A'){?>
							<tr>
								<td width="50%">- Lembur</td>
								<td width="10%">: Rp.</td>
								<td width="35%" align="right"><?= CStr::formatNumber($row['upahlembur'],0,$dot); ?></td>
							</tr>
						<?}	?>
						<tr>
							<td colspan="2">&nbsp;</td>
							<td valign="bottom" align="right">________________ +</td>
						</tr>
						<?
							$bruto = $sistem + $tunjangantdtetap;
							if($row['idtipepeg']=='A')
								$bruto += $row['upahlembur'];
						?>
						<tr style="font-weight:bold">
							<td width="50%" align="right">BRUTO</td>
							<td width="10%">: Rp.</td>
							<td width="35%" align="right"><?= CStr::formatNumber($bruto,0,$dot) ?></td>
						</tr>
					</table>
				</td>
				<td colspan="3" valign="top" style="border:none" width="50%">
					<table width="97%" cellpadding="4" cellspacing="0">
						<?
						if(count($a_jpot)>0){
							$potongan = 0;
							foreach($a_jpot as $ikey=>$val){
								$potongan += $a_pot[$row['idpegawai']][$ikey];
						?>
						<tr>
							<td width="50%">- <?= $val?></td>
							<td width="10%">: Rp.</td>
							<td width="37%" align="right"><?= CStr::formatNumber($a_pot[$row['idpegawai']][$ikey],0,$dot); ?></td>
						</tr>
						<?}}?>
						<tr>
							<td width="50%">- PPh Ps. 21</td>
							<td width="10%">: Rp.</td>
							<td width="37%" align="right"><?= CStr::formatNumber($row['pph'],0,$dot) ?></td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
							<td valign="bottom" align="right">________________ +</td>
						</tr>
						<?
							$totpotongan = $potongan + $row['pph'];
						?>
						<tr>
							<td width="50%">Total Potongan</td>
							<td width="10%">: Rp.</td>
							<td width="37%" align="right"><?= CStr::formatNumber($totpotongan,0,$dot) ?></td>
						</tr>
						<tr>
							<td colspan="3">&nbsp;</td>
						</tr>
						<?
							$netto = $bruto - $totpotongan
						?>
						<tr style="font-weight:bold">
							<td width="50%" align="right">NETTO</td>
							<td width="12%">: Rp.</td>
							<td width="35%" align="right"><?= CStr::formatNumber($netto,0,$dot) ?></td>
						</tr>
						<tr>
							<td colspan="3">&nbsp;</td>
						</tr>
						<tr>
							<td width="50%">Pengembalian PPh Ps. 21</td>
							<td width="10%">: Rp.</td>
							<td width="37%" align="right"><?= CStr::formatNumber($row['pph'],0,$dot) ?></td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
							<td valign="bottom" align="right">________________ +</td>
						</tr>
						<?
							$gajiditerima = $netto + $row['pph'];
						?>
						<tr style="font-weight:bold">
							<td width="50%" align="right">Gaji Diterima</td>
							<td width="12%">: Rp.</td>
							<td width="35%" align="right"><?= CStr::formatNumber($gajiditerima,0,$dot) ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td colspan="6">&nbsp;</td>
			</tr>
			<?if(count($a_jtunjawaldet)>0){
				foreach($a_jtunjawaldet as $jpeg){
					if($jpeg == $row['idjenispegawai']){?>
			<tr>
				<td><b>Tunjangan sudah dibayarkan</b></td>
				<td colspan="5">&nbsp;</td>
			</tr>
			<?
			if(count($a_jtunjawal)>0){
				$gajiawal = 0;
				foreach($a_jtunjawal as $ikey=>$val){
					$gajiawal += $a_tunja[$row['idpegawai']][$ikey];
			?>
			<tr>
				<td width="25%">- <?= $val?></td>
				<td width="5%">: Rp.</td>
				<td width="20%" align="right" style="padding-right:30px"><?= CStr::formatNumber($a_tunja[$row['idpegawai']][$ikey],0,$dot); ?></td>
				<td colspan="3">&nbsp;</td>
			</tr>
			<?}}?>
			<tr>
				<td colspan="2">&nbsp;</td>
				<td valign="bottom" align="center">________________ +</td>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td width="25%"><b>Total</b></td>
				<td width="5%">: Rp.</td>
				<td width="20%" align="right" width="20%" style="padding-right:30px"><?= CStr::formatNumber($gajiawal,0,$dot) ?></td>
				<td colspan="3">&nbsp;</td>
			</tr>
			<?}}}?>
		</table>
	<? include($conf['view_dir'].'inc_footerrep.php'); ?>
	</div>
<?}?>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>
