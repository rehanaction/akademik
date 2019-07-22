<?
    defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	//require_once($conf['ui_dir'].'u_form.php');
	require_once(Route::getModelPath('pendaftar'));
	$nopendaftar=$_SESSION['PENDAFTARAN']['FRONT']['USERID'];
	
    $data=Mpendaftar::getDataKtm($conn,$nopendaftar);
	 
?>
<!DOCTYPE html>
    <html>
            <head>
                <title>Cetak KTM Sementara</title>   
                <link rel="icon" type="image/x-icon" href="images/favicon.png">
                
            </head>
            <body style="background: white">
                <center>
					<div style="width:10.5cm;border:1px solid #ccc;padding:5px">
						<div style="width:100%;margin-bottom:0.5cm;">
						<table width="100%">
							<tr align="center">
								<td rowspan="2" width="60"><img src="images/logo.png" width="65"></td>
								<td colspan="2" width="300"><b>BUKTI DAFTAR ULANG / KTM SEMENTARA</b></td>
							</tr>
							<tr align="center">
								<td colspan="2" width="300">MAHASISWA BARU UEU<BR>
								TA <?=$data['periodedaftar']."-".($data['periodedaftar']+1)?><BR>
								PROGRAM <?=strtoupper($data['jalurpenerimaan'])." - ".strtoupper($data['namagelombang'])?>
								</td>
							</tr>
						</table>
						<hr>
						<table width="100%" cellpadding="4">
							<tr>
								<td><div style="width:3cm;height:4cm;border:1px solid;text-align:center;">FOTO 3X4</div></td>
								<td valign="top">
									<div style="width:6cm;height:2cm;border:1px solid;font-weight:bold;" align="center">
										<?=$data['nopendaftar']?><br>
										<?=$data['nama']?>
									</div>
								</td>
							</tr>
						</table>
						</div>
						
						<div style="width:100%;height:3.2cm;border:1px solid #000;margin-bottom:0.5cm">
							<table width="100%">
								<tr>
									<td width="55%">Tanggal Daftar Ulang </td>
									<td>: <?=date('d-m-Y',strtotime($data['ukuranalmamater']))?></td>
								</tr>
								<tr>
									<td width="55%">Ukuran Jas Almamater </td>
									<td>: <?=$data['ukuranalmamater']?></td>
								</tr>
								<tr>
									<td><b>Diterima Di </b></td>
								</tr>
							</table>
							<div align="center"><b><?=!empty($data['jurusan'])?$data['jurusan']:'....'?></b></div>
							<div align="center"><b><?=!empty($data['fakultas'])?$data['fakultas']:'....'?></b></div>
						</div>
						<div style="width:100%;height:3.2cm;border:1px solid #000" align="left">
							<ul>
								<li><i>Bukti Daftar Ulang / KTM sementara ini hanya berlaku samapai KTM asli diterbitkan</i></li>
								<li><i>Bukti Daftar ulang / KTM ini dibawa pada saat pengambilan jas almamater</i></li>
							</ul>
						</div>
					</div>
				</center>
            </body>
    </html>
