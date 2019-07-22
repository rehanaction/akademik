<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_idpegawai = CStr::removeSpecial($_REQUEST['idpegawai']);
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tglmulai = CStr::formatDate($_POST['tglmulai']);
	$r_tglselesai = CStr::formatDate($_POST['tglselesai']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('gaji'));
	require_once(Route::getModelPath('presensi'));
	
	// definisi variable halaman	
	$p_tbwidth = 1200;
	$p_col = 13;
	$p_file = 'laporanpotongankehadiran'.(!empty($r_kodeunit) ? '_'.$r_kodeunit : '');
	$p_model = 'mGaji';
	$p_window = 'Laporan Potongan Kehadiran';
	
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
		
    $a_data = $p_model::getLapPotonganPresensi($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$r_idpegawai);
	
	$a_datapeg = $a_data['pegawai'];
	$a_jumpeg = $a_data['terima'];
	
	$arhari = mPresensi::hariAbsensi();
	
	$a_tarifpotkehadiran = $p_model::getTarifPotKehadiran($conn);
	$a_tarifpottransport = $p_model::getTarifPotTransport($conn);
	
	$a_procpotkehadiran = $p_model::getProcPotKehadiran($conn,$r_tglmulai,$r_tglselesai);	
	$prockehadiranpeg_hari = $a_procpotkehadiran['totprochari'];
	
	$a_procpottransport = $p_model::getProcPotTransport($conn,$r_tglmulai,$r_tglselesai);	
	$proctransportpeg_hari = $a_procpottransport['totprochari'];
	
	$p_title = 'Laporan Potongan Kehadiran Karyawan <br />';
	if(!empty($a_data['namaunit']))
		$p_title .= 'Unit '.$a_data['namaunit'].'<br />';
	$p_title .= 'Periode '.CStr::formatDateInd($r_tglmulai).' s/d '.CStr::formatDateInd($r_tglselesai);
	
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
if (count($a_jumpeg) > 0) {
foreach($a_jumpeg as $row):
$total = 0;?>
<div align="center" style="page-break-after:always">
	<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0" bgcolor = "white">
			<tr>
				<td width="100">NIP</td>
				<td width="5">:</td>
				<td><?= $row['nik']?></td>
			</tr>
			<tr>
				<td width="100">NAMA PEGAWAI</td>
				<td width="5">:</td>
				<td><?= $row['namalengkap'] ?></td>
			</tr>
			<tr>
				<td width="100">UNIT KERJA</td>
				<td width="5">:</td>
				<td><?= $row['namaunit']; ?></td>
			</tr>
			<tr>
				<td width="100">JABATAN</td>
				<td width="5">:</td>
				<td><?= $row['jabatanstruktural']; ?></td>
			</tr>
		</table>
		<br>
		
		<table width="<?= $p_tbwidth+200 ?>" border="1" cellpadding="4" cellspacing="0">			
			<tr bgcolor = "gray">
				<th rowspan = "2" width="90"><b style = "color:#FFFFFF">TGL. ABSENSI</b></th>
				<th colspan = "2"><b style = "color:#FFFFFF">JAM KERJA</b></th>
				<th rowspan = "2"><b style = "color:#FFFFFF">JAM MASUK</b></th>
				<th rowspan = "2"><b style = "color:#FFFFFF">JAM KELUAR</b></th>
				<th colspan = "2"><b style = "color:#FFFFFF">JML MENIT DATANG</b></th>
				<th colspan = "2"><b style = "color:#FFFFFF">JML MENIT PULANG</b></th>
				<th rowspan = "2"><b style = "color:#FFFFFF">ALASAN</b></th>
				<th rowspan = "2"><b style = "color:#FFFFFF">JML JAM KERJA</b></th>
				<th colspan = "3"><b style = "color:#FFFFFF">TRANSPORT</b></th>
				<th colspan = "3"><b style = "color:#FFFFFF">KEHADIRAN</b></th>
				<th rowspan = "2"><b style = "color:#FFFFFF">TOTAL POTONGAN</b></th>
			</tr>
			<tr bgcolor ="gray">				
				<th><b style = "color:#FFFFFF">MASUK</b></th>							
				<th><b style = "color:#FFFFFF">PULANG</b></th>
				<th><b style = "color:#FFFFFF">CEPAT</b></th>							
				<th><b style = "color:#FFFFFF">TELAT</b></th>
				<th><b style = "color:#FFFFFF">CEPAT</b></th>							
				<th><b style = "color:#FFFFFF">TELAT</b></th>
				<th><b style = "color:#FFFFFF">NILAI TRANS. (Rp)</b></th>							
				<th><b style = "color:#FFFFFF">POT TRANS. (%)</b></th>
				<th><b style = "color:#FFFFFF">POT TRANS. (Rp)</b></th>							
				<th><b style = "color:#FFFFFF">NILAI HADIR. (Rp)</b></th>
				<th><b style = "color:#FFFFFF">POT HADIR. (%)</b></th>							
				<th><b style = "color:#FFFFFF">POT HADIR. (Rp)</b></th>
			</tr>
			<?php 
			if (count($a_datapeg[$row['idpegawai']]) > 0) {
				$totalpot = 0;
				foreach($a_datapeg[$row['idpegawai']] as $r):
					$jdatangc='';$mdatangc='';
					if($r['menitdatang'] < 0){
						if(abs($r['menitdatang']) > 60){
							$jdatangc = floor(abs($r['menitdatang'])/60) .' jam ';
							$mdatangc = abs($r['menitdatang'])%60 .' menit';
						}else{
							$mdatangc = abs($r['menitdatang']).' menit';
						}
					}
					
					$jdatangt='';$mdatangt='';
					if($r['menitdatang'] > 0){
						if($r['menitdatang'] > 60){
							$jdatangt = floor($r['menitdatang']/60) .' jam ';
							$mdatangt = $r['menitdatang']%60 .' menit';
						}else{
							$mdatangt = $r['menitdatang'].' menit';
						}
					}
					
					$jpulangc='';$mpulangc='';
					if($r['menitpulang'] < 0){
						if(abs($r['menitpulang']) > 60){
							$jpulangc = floor(abs($r['menitpulang'])/60) .' jam ';
							$mpulangc = abs($r['menitpulang'])%60 .' menit';
						}else{
							$mpulangc = abs($r['menitpulang']).' menit';
						}
					}
					
					$jpulangt='';$mpulangt='';
					if($r['menitpulang'] > 0){
						if($r['menitpulang'] > 60){
							$jpulangt = floor($r['menitpulang']/60) .' jam ';
							$mpulangt = $r['menitpulang']%60 .' menit';
						}else{
							$mpulangt = $r['menitpulang'].' menit';
						}
					}
					
					$stgl = strtotime($r['tglpresensi']);
					$elemen=date("w",$stgl);
					
					$procpotkehadiran = $prockehadiranpeg_hari[$row['idpegawai']][$r['tglpresensi']];
					$potkehadiran = $a_tarifpotkehadiran[$row['idjenispegawai']]*($procpotkehadiran/100);
					
					$procpottransport = $proctransportpeg_hari[$row['idpegawai']][$r['tglpresensi']];
					$pottransport = $a_tarifpottransport[$row['idjenispegawai']]*($procpottransport/100);
					
					$totalpot=$pottransport+$potkehadiran;
			?>
			<tr>
				<td><?= CStr::formatDateInd($r['tglpresensi'],false); ?></td>
				<td align="center"><?= $r['sjamdatang2']; ?></td>
				<td align="center"><?= $r['sjampulang2']; ?></td>
				<td align="center"><?= $r['jamdatang2']; ?></td>
				<td align="center"><?= $r['jampulang2']; ?></td>
				<td align="center"><?= $jdatangc.$mdatangc; ?></td>
				<td align="center"><?= $jdatangt.$mdatangt; ?></td>
				<td align="center"><?= $jpulangc.$mpulangc; ?></td>
				<td align="center"><?= $jpulangt.$mpulangt; ?></td>
				<td><?= $r['keterangan'].(($r['kodeabsensi'] != 'H' and $r['kodeabsensi'] != 'HL' and $r['kodeabsensi'] != 'B') ? ' ('.$r['absensi'].')' : ''); ?></td>
				<td align="right" width="50"><?= round($r['jamkerja'],2); ?></td>
				<td align="right"><?= CStr::formatNumber($a_tarifpottransport[$row['idjenispegawai']],0,$dot); ?></td>
				<td align="right"><?= $procpottransport; ?></td>
				<td align="right"><?= CStr::formatNumber($pottransport,0,$dot); ?></td>
				<td align="right"><?= CStr::formatNumber($a_tarifpotkehadiran[$row['idjenispegawai']],0,$dot); ?></td>
				<td align="right"><?= $procpotkehadiran; ?></td>
				<td align="right"><?= CStr::formatNumber($potkehadiran,0,$dot); ?></td>
				<td align="right"><?= CStr::formatNumber($totalpot,0,$dot); ?></td>
			</tr>
			<?php 			
				$total += $totalpot ; 
			endforeach;
			?>
			<tr>
				<td colspan="17" align="center"><b>Total</b>
				<td align="right"><?= CStr::formatNumber($total,0,$dot); ?></td>
			</tr>
			<?
			
				}else{
			?>	
			<tr>
				<td align="center" colspan="18"><strong>Data tidak ditemukan</strong></td>
			</tr>	
			<?}?>
		</table>
	<? include($conf['view_dir'].'inc_footerrep.php');?>
</div>
 <?php 
 endforeach;
 }else{ ?>	
<div align="center" style="page-break-after:always">
	<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<table width="<?= $p_tbwidth?>" border="0" cellpadding="4" cellspacing="0" bgcolor = "white">
			<tr>
				<td width="100">NIP</td>
				<td width="5">:</td>
				<td><?= $row['nik']?></td>
			</tr>
			<tr>
				<td width="100">NAMA PEGAWAI</td>
				<td width="5">:</td>
				<td><?= $row['namalengkap'] ?></td>
			</tr>
			<tr>
				<td width="100">UNIT KERJA</td>
				<td width="5">:</td>
				<td><?= $row['namaunit']; ?></td>
			</tr>
			<tr>
				<td width="100">JABATAN</td>
				<td width="5">:</td>
				<td><?= $row['jabatanfungsional']; ?></td>
			</tr>
		</table>
		<br>
		
		<table width="<?= $p_tbwidth+200 ?>" border="1" cellpadding="4" cellspacing="0">			
			<tr bgcolor = "gray">
				<th rowspan = "2"><b style = "color:#FFFFFF">TGL. ABSENSI</th>
				<th colspan = "2"><b style = "color:#FFFFFF">JAM KERJA</th>
				<th rowspan = "2"><b style = "color:#FFFFFF">JAM MASUK</th>
				<th rowspan = "2"><b style = "color:#FFFFFF">JAM KELUAR</th>
				<th colspan = "2"><b style = "color:#FFFFFF">JML JAM DATANG</th>
				<th colspan = "2"><b style = "color:#FFFFFF">JML JAM PULANG</th>
				<th rowspan = "2"><b style = "color:#FFFFFF">ALASAN</th>
				<th rowspan = "2"><b style = "color:#FFFFFF">JML JAM KERJA</th>
				<th colspan = "3"><b style = "color:#FFFFFF">TRANSPORT</b></th>
				<th colspan = "3"><b style = "color:#FFFFFF">KEHADIRAN</b></th>
				<th rowspan = "2"><b style = "color:#FFFFFF">TOTAL POTONGAN</b></th>
			</tr>
			<tr bgcolor ="gray">				
				<th><b style = "color:#FFFFFF">MASUK</th>							
				<th><b style = "color:#FFFFFF">PULANG</th>
				<th><b style = "color:#FFFFFF">CEPAT</th>							
				<th><b style = "color:#FFFFFF">TELAT</th>
				<th><b style = "color:#FFFFFF">CEPAT</th>							
				<th><b style = "color:#FFFFFF">LEMBUR</th>
				<th><b style = "color:#FFFFFF">Nilai Trans. (Rp)</b></th>							
				<th><b style = "color:#FFFFFF">Pot Trans. (%)</b></th>
				<th><b style = "color:#FFFFFF">Pot Trans. (Rp)</b></th>							
				<th><b style = "color:#FFFFFF">Nilai Hadir. (Rp)</b></th>
				<th><b style = "color:#FFFFFF">Pot Hadir. (%)</b></th>							
				<th><b style = "color:#FFFFFF">Pot Hadir. (Rp)</b></th>
			</tr>
			<tr>
				<td align="center" colspan="18"><strong>Data tidak ditemukan</strong></td>
			</tr>
		</table>
	<? include($conf['view_dir'].'inc_footerrep.php');?>
</div>
 <? } ?>
</body>
 </html>
 
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>
 
