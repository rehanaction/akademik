<?
    defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	//require_once($conf['ui_dir'].'u_form.php');
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getUIPath('form'));
	$nopendaftar=$_POST['nopendaftar'];
    $data=Mpendaftar::getDataKtm($conn,$nopendaftar);
	 
?>
<!DOCTYPE html>
    <html>
            <head>
                <title>Bukti Daftar Ulang</title>   
                <link rel="icon" type="image/x-icon" href="images/favicon.png">
                   <!--style type="text/css" media="print">
					  .page {
					height: 750px;
					width: 800px;
					filter: progid:D/XImageTransform.Microsoft.BasicImage(Rotation=3);}
					</style-->
				<style type="text/css">
				body{ font-size:10pt;}
				</style>
            </head>
            <body style="background: white;">
                <center>
					<div style="width:10.5cm;border:1px solid #ccc;padding:5px;float:left;margin-right:20px">
						<div style="width:100%;margin-bottom:0.2cm;">
						<table width="100%">
							<tr align="center">
								<td rowspan="2" width="60"><img src="images/logo.jpg" width="65"></td>
								<td colspan="2" width="300"><b>BUKTI DAFTAR ULANG</b></td>
							</tr>
							<tr align="center">
								<td colspan="2" width="300">MAHASISWA BARU ESA UNGGUL<BR>
								TA <?=$data['periodedaftar']."-".($data['periodedaftar']+1)?><BR>
								PROGRAM <?=strtoupper($data['jalurpenerimaan'])." - ".strtoupper($data['namagelombang'])?>
								</td>
							</tr>
						</table>
						<hr>
						<table width="100%" cellpadding="4" >
							<tr>
								<td><div style="width:2.6cm;height:3.6cm;border:1px solid;text-align:center;"><?= uForm::getImageMahasiswa3x4($conn,$nopendaftar)?></div></td>
								<td valign="top">
									<div style="width:6cm;height:2cm;border:1px solid;font-weight:bold;" align="center">
										<?=$data['nopendaftar']?><br>
										<?=$data['nama']?>
									</div>
								</td>
							</tr>
						</table>
						</div>

						<div style="width:100%;height:2.7cm;border:1px solid #000;margin-bottom:0.2cm;">
							<table width="100%">
								<tr>
									<td width="55%">Tanggal Daftar Ulang </td>
									<td>: <?=date('d-m-Y',strtotime($data['tgldaftarulang']))?></td>
								</tr>
								<tr>
									<td width="55%">Ukuran Jas Almamater </td>
									<td>: <?=$data['ukuranalmamater']?></td>
								</tr>
								<tr>
									<td><b>Diterima Di </b></td>
									<td><b>: <?= $data['jurusan']?></b></td>
								</tr>
							</table>
						</div>
						<div style="width:100%;height:2cm;border:1px solid #000" align="left">
							<ul style="margin:3px">								
								<li><i>Bukti Daftar ulang ini dibawa pada saat pengambilan jas almamater</i></li>
								<li><i>Bukti Daftar Ulang ini dapat ditukarkan dengan KTM setelah pengukuhan Mahasiswa Baru</i></li>
							</ul>
						</div>
					</div>
				</center>
            </body>
    </html>
