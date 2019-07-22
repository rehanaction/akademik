<?
    defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once($conf['ui_dir'].'u_form.php');
	require_once(Route::getModelPath('pendaftar'));
       
	//cek session
	if(Modul::pendaftarLogin() == null or Modul::pendaftarLogin() == ''){
		header("Location: index.php");
	}
	$data=Modul::SessionDataFront($conn);
	$data=$data->FetchRow();
        
?>
<!DOCTYPE html>
    <html>
            <head>
                <title>Cetak Kartu Test</title>   
                <link rel="icon" type="image/x-icon" href="images/favicon.png">
                <link href="styles/daftar.css" rel="stylesheet" type="text/css">
                <link href="styles/style.css" rel="stylesheet" type="text/css">
            </head>
            <body style="background: white" onLoad="window.print();">
                <center>
					<div style="border:none; width: 840px; height: 500px;">
						<table><tr><td>
							<table width="100%" cellspacing=0 style="border:1px solid #000000">
								<tr><td>
									<table width="100%" style="border-bottom:1px solid #000000">
										<tr height=10>
											<td align=center colspan=2><h2><strong>TANDA PESERTA SELEKSI PMB <?= $data['periodedaftar']?></strong></h2></td>
										</tr>
										<tr style="background: #ffffff;">
											  <td style="padding:0 20px; width:70px"><img src="images/logo.png" width="70"></td>
											  <td nowrap >
												<p>
													<span style="font-size: 1;">UNIVERSITAS ESA UNGGUL</span><br>
													<span style="font-size: 1;"><i>Jalan Arjuna Utara No. 9, Kebon Jeruk - Jakarta Barat 11510</i></span><br>
													<span style="font-size: 1;"><i>021 - 5674223 (hunting) 021-5682510 (direct) Fax:021-5674248</i></span><br>
													<span style="font-size: 1;">Website: www.esaunggul.ac.id  Email : info@esaunggul.ac.id</span>
												</p>
											  </td>  
										</tr>
									</table>
								</td></tr>
								<tr>
									<td colspan=2 align="center">
										<table border=0 width="100%">
											<tr>
												<td valign="top" nowrap width="100" align="center" valign="middle">
													<?= uForm::getImageMahasiswa($conn,$data['nopendaftar'],true) ?>
												</td>
												<td valign="top" align=center>
													<div align=center style="padding-top:8px;width:230px;height:20px;border:1px solid #000000;margin-top:0px;margin-left:8px"><strong>Untuk Panitia</strong></div>
													<br><div align=center style="padding-top:8px;width:230px;height:30px;border:1px solid #000000;margin-top:0px;margin-left:8px"><font size="4"><strong><?=$data['nopendaftar']?></strong></font></div>
													<br><div align=left style="padding:3px;width:227px;height:80px;border:1px solid #000000;margin-top:0px;margin-left:8px"><u><div align=center>Pilihan Prodi</div></u><br>
														Pilihan 1: <?=mPendaftar::getPilihan($data['pilihan1'])?><br>
														Pilihan 2: <? if(!empty($data['pilihan2'])) echo mPendaftar::getPilihan($data['pilihan2'])?><br>
														Pilihan 3: <? if(!empty($data['pilihan3'])) echo mPendaftar::getPilihan($data['pilihan3'])?><br><br>
													</div>
												</td>
											</tr>
											<!--<tr>
												<td align="center" style="font-family:bar; font-size: 60px;"><?=$data['nopendaftar']?></td>
												<td></td>
											</tr>-->
										</table>
									</td>
								</tr>
								<tr style="background: #fff;">
									<td colspan=2>
										<table border=0 width="100%" cellspacing=0 style="padding:5px;border-top:1px solid #000000">
											<tr>
												<td width=100><strong>Nama Peserta</strong></td>
												<td><strong>:&nbsp;&nbsp;</strong><font size=3><strong><?= strtoupper($data['nama']);?></strong></font></td>
												
											</tr>
											<tr>
												<td colspan=2><strong>&nbsp;</strong></td>
											</tr>
											<tr>
												<td width=100><strong>Jalur Seleksi/Gel</strong></td>
												<td><strong>:&nbsp;&nbsp;</strong><font size=3><strong><?= $data['jalur'];?>/<?= $data['idgelombang']?></strong></font></td>
												
											</tr>
											<? /* 
											<tr>
												<td><strong>Tanggal Seleksi</strong></td>
												<td><strong>:&nbsp;&nbsp;<font size=3><?= date('d-m-Y',strtotime($data['tgltes']))?></font></strong></td>
											</tr>
											<tr>
												<td><strong>Waktu</strong></td>
												<td><strong>:&nbsp;&nbsp;<font size=3><?= Modul::convertTime($data['jammulai']).' - '.Modul::convertTime($data['jamselesai'])?></font></strong></td>
											</tr>
											<tr>
												<td><strong>Lokasi</strong></td>
												<td><strong>:&nbsp;&nbsp;<font size=2><?= $data['koderuang'].' - '.$data['lokasi']?>&nbsp;&nbsp;<?if($data['lantai']!='') echo 'Lantai '.$data['lantai'];?></font></strong></td>
											</tr>
											*/ ?>
										</table>
									</td>
								</tr>
							</table>
						</td>
						<td>&nbsp;</td>
						<td>
							<table width="100%" cellspacing=0 style="border:1px solid #000000">
								<tr><td>
									<table width="100%" style="border-bottom:1px solid #000000">
										<tr height=10>
											<td align=center colspan=2><h2><strong>TANDA PESERTA SELEKSI PMB <?= $data['periodedaftar']?></strong></h2></td>
										</tr>
										<tr style="background: #ffffff;">
											  <td style="padding:0 20px; width:70px"><img src="images/logo.png" width="70"></td>
											  <td nowrap >
												<p>
													<span style="font-size: 1;">UNIVERSITAS ESA UNGGUL</span><br>
													<span style="font-size: 1;"><i>Jalan Arjuna Utara No. 9, Kebon Jeruk-Jakarta Barat 11510</i></span><br>
													<span style="font-size: 1;"><i>021-5674223 (hunting) 021-5682510 (direct) Fax:021-5674248</i></span><br>
													<span style="font-size: 1;"><i>Website: www.esaunggul.ac.id  Email : info@esaunggul.ac.id</i></span>
												</p>
											  </td>  
										</tr>
									</table>
								</td></tr>
								<tr>
									<td colspan=2 align="center">
										<table border=0 width="100%">
											<tr>
												<td valign="top" nowrap width="100" align="center" valign="middle">
													<?= uForm::getImageMahasiswa($conn,$data['nopendaftar'],true) ?>
												</td>
												<td valign="top" align=center>
													<div align=center style="padding-top:8px;width:230px;height:20px;border:1px solid #000000;margin-top:0px;margin-left:8px"><strong>Untuk Peserta</strong></div>
													<br><div align=center style="padding-top:8px;width:230px;height:30px;border:1px solid #000000;margin-top:0px;margin-left:8px"><font size="4"><strong><?=$data['nopendaftar']?></strong></font></div>
													<br><div align=left style="padding:3px;width:227px;height:80px;border:1px solid #000000;margin-top:0px;margin-left:8px"><u><div align=center>Pilihan Prodi</div></u><br>
														Pilihan 1: <?=mPendaftar::getPilihan($data['pilihan1'])?><br>
														Pilihan 2: <? if(!empty($data['pilihan2'])) echo mPendaftar::getPilihan($data['pilihan2'])?><br>
														Pilihan 3: <? if(!empty($data['pilihan3'])) echo mPendaftar::getPilihan($data['pilihan3'])?><br>
													</div>
												</td>
											</tr>
											<!--<tr>
												<td align="center" style="font-family:bar; font-size: 60px;"><?=$data['nopendaftar']?></td>
												<td></td>
											</tr>-->
										</table>
									</td>
								</tr>
								<tr style="background: #fff;">
									<td colspan=2>
										<table border=0 width="100%" cellspacing=0 style="padding:5px;border-top:1px solid #000000">
											<tr>
												<td width=100><strong>Nama Peserta</strong></td>
												<td><strong>:&nbsp;&nbsp;</strong><font size=3><strong><?= strtoupper($data['nama']);?></strong></font></td>
											</tr>
											<tr>
												<td colspan=2><strong>&nbsp;</strong></td>
											</tr>
											<tr>
												<td width=100><strong>Jalur Seleksi/Gel</strong></td>
												<td><strong>:&nbsp;&nbsp;<font size=3><?= $data['jalur'];?>/<?= $data['idgelombang']?></font></strong></td>
											</tr>
											<tr>
												<td><strong>Tanggal Seleksi</strong></td>
												<td><strong>:&nbsp;&nbsp;<font size=3><?= date('d-m-Y',strtotime($data['tgltes']))?></font></strong></td>
											</tr>
											<tr>
												<td><strong>Waktu</strong></td>
												<td><strong>:&nbsp;&nbsp;<font size=3><?= Modul::convertTime($data['jammulai']).' - '.Modul::convertTime($data['jamselesai'])?></font></strong></td>
											</tr>
											<tr>
												<td><strong>Lokasi</strong></td>
												<td><strong>:&nbsp;&nbsp;<font size=2><?= $data['koderuang'].' - '.$data['lokasi']?>&nbsp;&nbsp;<?if($data['lantai']!='') echo 'Lantai '.$data['lantai'];?></font></strong></td>
											</tr>
										</table>
									</td>
								</tr>
							</table>
						</td>
						</tr>
						</table>
					</div>
                </center>
            </body>
    </html>
