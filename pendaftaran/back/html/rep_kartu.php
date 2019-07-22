<?
    defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once($conf['ui_dir'].'u_form.php');
	require_once(Route::getModelPath('pendaftar'));
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	$data=mPendaftar::getData($conn,$_GET['id']);
	
	$p_namafile='kartu_'.$_GET['id'];
	
	
   Page::setHeaderFormat($r_format,$p_namafile);     
?>
<!DOCTYPE html>
    <html>
            <head>
                <title>Cetak Kartu Test</title>   
                <link rel="icon" type="image/x-icon" href="images/favicon.png">
                <link href="../front/styles/daftar.css" rel="stylesheet" type="text/css">
                <link href="../front/styles/style.css" rel="stylesheet" type="text/css">
            </head>
            <body style="background: white" onLoad="window.print();">
                <center>
					<div style="border:none; width: 840px; height: 500px;">
						<table><tr><td>
							<table width="100%" cellspacing=0 style="border:1px solid #000000">
								<tr><td>
									<table width="100%" style="border-bottom:1px solid #000000">
										<tr height=10>
											<td align=center colspan=2><h2 style="margin-bottom: 0"><strong>TANDA PESERTA SELEKSI PMB <?= $data['periodedaftar']?></strong></h2></td>
										</tr>
										<tr style="background: #ffffff;">
											  <td style="padding:0 20px; width:70px"><img src="../front/images/logo.png" width="80"></td>
											  <td nowrap>
												<p>
													<span style="font-size: 1;"><b>STIE INABA</b></span><br>
													<span style="font-size: 1;"><i>Jl. Soekarno Hatta No.448 Bandung 40266</i></span><br>
													<span style="font-size: 1;"><i>(022) 7563919</i></span><br>
													<span style="font-size: 1;">Website: www.inaba.ac.id <br>Email : info@inaba.ac.id</span>
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
													<br><div align=center style="padding-top:8px;width:230px;height:30px;border:1px solid #000000;margin-top:0px;margin-left:8px"><font size="4"><strong><?=$data['nopendaftar']?></strong></font></div>
													<br><div align=left style="padding:3px;width:227px;height:70px;border:1px solid #000000;margin-top:0px;margin-left:8px"><u><div align=center>Pilihan Prodi</div></u><br>
														<center><b><?=mPendaftar::getPilihan($data['pilihan1'])?></b></center>
														
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
												<td width=100><strong>Jalur/Gel</strong></td>
												<td><strong>:&nbsp;&nbsp;</strong><font size=3><strong><?= $data['jalurpenerimaan'];?>/<?= $data['idgelombang']?></strong></font></td>
												
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
											*/  ?>
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
