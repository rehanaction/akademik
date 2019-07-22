<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	if(is_array($_REQUEST['kode'])){
		if(count($_REQUEST['kode'])>0)
			$r_kode = implode("','",$_REQUEST['kode']);
	}else
		$r_kode = CStr::removeSpecial($_REQUEST['kode']);
	
	require_once(Route::getModelPath('angkakredit'));
	
	// definisi variable halaman	
	$p_tbwidth = 900;
	$p_col = 8;
	$p_file = 'dupak_'.$r_kodeunit.'_periode_'.$r_tahun;
	$p_model = 'mAngkaKredit';
	$p_window = 'Daftar Usul Penetapan Angka Kredit';
	
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
	
	$a_master = $p_model::getMasterPenilaian($conn);
	
	while($ros = $a_master->FetchRow()) {
		if(($ros['bidangkegiatan'] == 'IA' and strlen($ros['kodeurutan'])<=5) or (($ros['bidangkegiatan'] == 'IB' or $ros['bidangkegiatan'] == 'II' or $ros['bidangkegiatan'] == 'III') and strlen($ros['kodeurutan'])<=7) or ($ros['bidangkegiatan'] == 'IV' and strlen($ros['kodeurutan'])<=3)){
			if(substr($ros['kodeurutan'],0,1) == '1')
				$utama++;
			else
				$pnj++;
		}
		
		$arrow[$ros['idkegiatan']] = $ros;
	}
	
    $a_data = $p_model::getListDupak($conn,$r_kode,$r_kodeunit);
	$rs = $a_data['list'];
	
	//kredit per riwayat
	$kredit1a = $p_model::getRWTKredit($conn,'ak_bidang1a',$r_kodeunit,$r_kode);
	$kredit1b = $p_model::getRWTKredit($conn,'ak_bidang1b',$r_kodeunit,$r_kode);
	$kredit2 = $p_model::getRWTKredit($conn,'ak_bidang2',$r_kodeunit,$r_kode);
	$kredit3 = $p_model::getRWTKredit($conn,'ak_bidang3',$r_kodeunit,$r_kode);
	$kredit4 = $p_model::getRWTKredit($conn,'ak_bidang4',$r_kodeunit,$r_kode);
	
	$p_title = 'Daftar Usul Penetapan Angka Kredit <br />';
	if(!empty($a_data['namaunit']))
		$p_title .= 'Unit '.$a_data['namaunit'].'<br />';
	$p_title .= 'Periode '.$r_tahun;
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
<div align="center">
	<?
		while($row = $rs->FetchRow()){
	?>
		<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<table>
			<tr>
				<td><strong>Tanggal Penilaian : </strong><?= CStr::formatDateInd($row['tglusulan']);?></td>
			</tr>
			<tr>
				<td>
					<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">
						<tr bgcolor = "gray">
							<th style = "color:#FFFFFF" colspan="4">Keterangan Perorangan</th>
						</tr>
						<tr>
							<td align="center">1.</td>
							<td colspan="2">Nama</td>
							<td><?= $row['namalengkap']; ?></td>
						</tr>
						<tr>
							<td align="center">2.</td>
							<td colspan="2">N I K</td>
							<td><?= $row['nik']; ?></td>
						</tr>
						<tr>
							<td align="center">3.</td>
							<td colspan="2">Nomor Seri Karpeg.</td>
							<td> - </td>
						</tr>
						<tr>
							<td align="center">4.</td>
							<td colspan="2">Tempat dan Tanggal Lahir</td>
							<td><?= $row['tmplahir'].', '.CStr::formatDateInd($row['tgllahir']); ?></td>
						</tr>
						<tr>
							<td align="center">5.</td>
							<td colspan="2">Jenis Kelamin</td>
							<td><?= $row['jeniskelamin']; ?></td>
						</tr>
						<tr>
							<td align="center">6.</td>
							<td colspan="2">Pendidikan Tertinggi</td>
							<td><?= $row['namapendidikan']; ?></td>
						</tr>
						<tr>
							<td align="center">7.</td>
							<td colspan="2">Pangkat dan Golongan Ruang/ TMT</td>
							<td><?= $row['namagolongan'].(!empty($row['tmtpangkat']) ? '/ '.CStr::formatDateInd($row['tmtpangkat']) : ''); ?></td>
						</tr>
						<tr>
							<td align="center">8.</td>
							<td colspan="2">Jabatan Fungsional/ TMT</td>
							<td><?= $row['jabatanfungsional'].(!empty($row['tmtmulai']) ? '/ '.CStr::formatDateInd($row['tmtmulai']) : ''); ?></td>
						</tr>
						<tr>
							<td align="center">9.</td>
							<td colspan="2">Fakultas/ Jurusan</td>
							<td><?= $row['namaunit'].(!empty($row['parentunit']) ? '/ '.$row['parentunit'] : ''); ?></td>
						</tr>
							<td align="center" rowspan="2" width="50px">10.</td>
							<td rowspan="2" width="200px">Masa Kerja</td>
							<td width="200px">Lama</td>
							<td><?= $row['masakerjathngol'].' Tahun '.$row['masakerjablngol'].' Bulan'; ?></td>
						</tr>
						<tr>
							<td>Baru</td>
							<td><?= ' Tahun '.' Bulan'; ?></td>
						</tr>
						<tr>
							<td align="center">11.</td>
							<td colspan="2">Unit Kerja</td>
							<td><?= $row['namaunit']; ?></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>
					<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">
						<tr bgcolor = "gray">
							<th colspan="8"><b style = "color:#FFFFFF">Unsur Yang Dinilai</th>
						</tr>
						<tr bgcolor = "gray">
							<th rowspan="3" width="30px"><b style = "color:#FFFFFF">No.</b></th>
							<th rowspan="3"><b style = "color:#FFFFFF">Unsur Dan Sub Unsur</b></th>
							<th colspan="6"><b style = "color:#FFFFFF">Angka Kredit Menurut</b></th>
						</tr>
						<tr bgcolor = "gray">
							<th colspan="3"><b style = "color:#FFFFFF">Perguruan Tinggi/ Kopertis Pengusul</b></th>
							<th colspan="3"><b style = "color:#FFFFFF">Tim Penilai</b></th>
						</tr>
						<tr bgcolor = "gray">
							<th width="50px"><b style = "color:#FFFFFF">Lama</b></th>
							<th width="50px"><b style = "color:#FFFFFF">Baru</b></th>
							<th width="50px"><b style = "color:#FFFFFF">Jumlah</b></th>
							<th width="50px"><b style = "color:#FFFFFF">Lama</b></th>
							<th width="50px"><b style = "color:#FFFFFF">Baru</b></th>
							<th width="50px"><b style = "color:#FFFFFF">Jumlah</b></th>
						</tr>
						
						<?
							$i = 1;$np=0;$bdg='IA';$jum=0;$tjum=0;
							foreach($arrow as $ros){
								if(($ros['bidangkegiatan'] == 'IA' and strlen($ros['kodeurutan'])<=5) or (($ros['bidangkegiatan'] == 'IB' or $ros['bidangkegiatan'] == 'II' or $ros['bidangkegiatan'] == 'III') and strlen($ros['kodeurutan'])<=7) or ($ros['bidangkegiatan'] == 'IV' and strlen($ros['kodeurutan'])<=3)){
									if($bdg != $ros['bidangkegiatan']){?>
						<tr style="font-weight:bold;height:25px;">
							<td>Jumlah</td>
							<td>&nbsp;</td>
							<td align="right"><?= number_format($jum,2);?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<? 
										$jum=0;
									}
						?>
						<tr>
							<?
								if($temp != substr($ros['kodeurutan'],0,1)){
									$np++;
							?>
							<td align="center" valign="top" rowspan="<?= substr($ros['kodeurutan'],0,1) == '1' ? $utama+4 : $pnj+1?>"><?= $ros['kodekegiatan']?></td>
							<?}?>
							<td>
								<table>
									<tr>
										<td valign="top" <?= strlen($ros['kodeurutan']) > 1 ? ' width="22px"' : 'colspan="2"'?>>
											<?
												if(strlen($ros['kodeurutan']) >= 3){
													if($ros['bidangkegiatan'] == 'II' and strlen($ros['kodekegiatan']) > 7)
														$no = substr($ros['kodekegiatan'],strlen(trim($ros['kodekegiatan']))-3,3);
													else if(substr($ros['kodeurutan'],strlen(trim($ros['kodeurutan']))-2,2)<10)
														$no = substr($ros['kodekegiatan'],strlen(trim($ros['kodekegiatan']))-1,1);					
													else
														$no = substr($ros['kodekegiatan'],strlen(trim($ros['kodekegiatan']))-2,2);
												}
												
												echo strlen($ros['kodeurutan']) > 1 ? $no.'. ' : $ros['namakegiatan'];
											?>
										</td>
										<td>
											<?
											  if(($ros['bidangkegiatan'] == 'IB' or $ros['bidangkegiatan'] == 'II' or $ros['bidangkegiatan'] == 'III') and strlen($ros['kodeurutan'])==5){
												echo strtoupper($ros['namakegiatan']);
											  }
											  else
												echo strlen($ros['kodeurutan']) > 1 ? $ros['namakegiatan'] : '';			
											?>
										</td>
									</tr>
								</table>
							</td>
							<td>&nbsp;</td>
							<td align="right">
								<?
									if($ros['bidangkegiatan'] == 'IA')
										echo !empty($kredit1a[$row['idpegawai']][$ros['idkegiatan']]) ? number_format($kredit1a[$row['idpegawai']][$ros['idkegiatan']],2) : '';
									else if($ros['bidangkegiatan'] == 'IB')
										echo !empty($kredit1b[$row['idpegawai']][$ros['idkegiatan']]) ? number_format($kredit1b[$row['idpegawai']][$ros['idkegiatan']],2) : '';
									else if($ros['bidangkegiatan'] == 'II')
										echo !empty($kredit2[$row['idpegawai']][$ros['idkegiatan']]) ? number_format($kredit2[$row['idpegawai']][$ros['idkegiatan']],2) : '';
									else if($ros['bidangkegiatan'] == 'III')
										echo !empty($kredit3[$row['idpegawai']][$ros['idkegiatan']]) ? number_format($kredit3[$row['idpegawai']][$ros['idkegiatan']],2) : '';
									else if($ros['bidangkegiatan'] == 'IV')
										echo !empty($kredit4[$row['idpegawai']][$ros['idkegiatan']]) ? number_format($kredit4[$row['idpegawai']][$ros['idkegiatan']],2) : '';
								?>
							</td>
							<td align="right">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
							<td align="center">&nbsp;</td>
						</tr>
						<?		
									if($ros['bidangkegiatan'] == 'IA'){
										$jum += $kredit1a[$row['idpegawai']][$ros['idkegiatan']];
										$tjum += $kredit1a[$row['idpegawai']][$ros['idkegiatan']];
									}else if($ros['bidangkegiatan'] == 'IB'){
										$jum += $kredit1b[$row['idpegawai']][$ros['idkegiatan']];
										$tjum += $kredit1b[$row['idpegawai']][$ros['idkegiatan']];
									}else if($ros['bidangkegiatan'] == 'II'){
										$jum += $kredit2[$row['idpegawai']][$ros['idkegiatan']];
										$tjum += $kredit2[$row['idpegawai']][$ros['idkegiatan']];
									}else if($ros['bidangkegiatan'] == 'III'){
										$jum += $kredit3[$row['idpegawai']][$ros['idkegiatan']];
										$tjum += $kredit3[$row['idpegawai']][$ros['idkegiatan']];
									}else if($ros['bidangkegiatan'] == 'IV'){
										$jum += $kredit4[$row['idpegawai']][$ros['idkegiatan']];
										$tjum += $kredit4[$row['idpegawai']][$ros['idkegiatan']];
									}
								}
								
								$i++;	
								$temp = substr($ros['kodeurutan'],0,1);
								$bdg = $ros['bidangkegiatan'];
							}
															
							if($i > 1){
						?>
						<tr style="font-weight:bold;height:25px">
							<td>JUMLAH</td>
							<td>&nbsp;</td>
							<td align="right"><?= number_format($jum,2);?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr style="font-weight:bold;height:25px">
							<td colspan="2">TOTAL JUMLAH</td>
							<td>&nbsp;</td>
							<td align="right"><?= number_format($tjum,2);?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
						<tr height="20">
							<td align="center" height="25px" rowspan="2" valign="top">III.</td>
							<td height="25px" colspan="7">BAHAN YANG DINILAI</td>
						</tr>
						<tr height="20">
							<td colspan="8">
								<table width="100%" cellpadding="4" cellspacing="0" border="1" style="border:solid #fff">
									<tr height="20px">
										<td width="25%">Nama</td>
										<td><?= $row['namalengkap']; ?></td>
									</tr>				
									<tr height="20px">
										<td>NIP</td>
										<td><?= $row['nik']; ?></td>
									</tr>					
									<tr height="20px">
										<td>Jabatan/ T.M.T</td>
										<td><?= $row['jabatanfungsional'].'/ '.CStr::formatDateInd($row['tmtmulai'])?></td>
									</tr>					
									<tr height="20px">
										<td>Pangkat/ T.M.T</td>
										<td><?=$row['golongan'].'/ '.CStr::formatDateInd($row['tmtpangkat'])?></td>
									</tr>						
									<tr height="20px">
										<td>Jurusan/ Program Studi</td>
										<td><?= $row['parentunit'].'/ '.$row['namaunit']; ?></td>
									</tr>							
									<tr height="20px">
										<td>Mata Kuliah Yang Dibina</td>
										<td></td>
									</tr>				
								</table>
							</td>
						</tr>
						<?}?>
					</table>
				</td>
			</tr>
		</table>
<? require_once($conf['view_dir'].'inc_footerrep.php'); ?>
	<?}?>
</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>
