<?
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug = false;
	// hak akses
	Modul::getFileAuth();
      
    // require     
	require_once(Route::getModelPath('pesertaseminar'));

	//parameter
	$periode    = CStr::removeSpecial($_REQUEST['periode']);
	$id_seminar = CStr::removeSpecial($_REQUEST['idseminar']);
	$nopeserta = CStr::removeSpecial($_REQUEST['nopeserta']);

	//model
	$p_model = mPesertaSeminar;
	$p_title = 'SERTIFIKAT SEMINAR';
	$p_tbwidth = 1133;
	$a_data = $p_model::getTampilAbsensi($conn,$id_seminar,$periode,$nopeserta);
	
	$periode = substr($periode, 0,4);
	
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<!DOCTYPE html>
<html>
	<!--
	    Logo Posititon 
		background-image:url('images/logo.jpg');
        background-size: 25cm 20cm;background-repeat: no-repeat;background-attachment: fixed;
	-->
    <head>
            <title>Cetak Sertifikat Seminar</title>   
            <link rel="icon" type="image/x-icon" href="image/favicon.png">
            <link href="style/style.css" rel="stylesheet" type="text/css">
            
    </head>
        <body style="background:white;font-family: Arial,arial black;">
        <?php foreach ($a_data as $row => $value) { ?>
		<center>
		<?php	if(empty($value['kritik']) or empty($value['saran'])) { ?>
			<?= $value['nopeserta'] ?> - <?= $value['nama'] ?> belum mengisi kritik dan saran
		<?php
				}
				else {
		?>
        <center>
            <div style="width:<?=$p_tbwidth ?>px;" >
			<center>
			 	 <strong>
					  <h5 style="font-family: Times New Roman; font-size: 20px; margin-top: 250px"><?= $value['nama'] ?></h5>
					  <br><br>
					  <h2 style="font-family: Bell MT; font-size: 43px; "><i>Seminar <br>" <?= $value['namaseminar'] ?> "</i></h2>
					  <h2 style="font-family: Times New Roman; font-size: 17px;">On <?= str_replace(',', '', cstr::formatDateEng($value['tglkegiatan'])) ?> , <?=$value['koderuang']?>, Esa Unggul University</h2>
					  <h2 style="font-family: Times New Roman; font-size: 17px;"> Key Speaker By - <?=$value['namapembicara']?></h2>
					  				 		
				 </strong>

			</center> 	
			
			<table width="<?= $p_tbwidth ?>">
				<tr style="font-family: Times New Roman; font-weight: bold; font-size: 17px">
					<td style="width: 3cm"></td>
					<td style="width: 8cm" align="center">
						<p>Rektor Univ.Esa Unggul</p>
						<br><br><br><br>
						<p style="border-top: thin solid black">( Dr. Arief Kusuma Among Praja )</p>
					</td>
					<td style="width: 10cm"></td>
					<td align="center">
						<p>Key Speaker</p>
						<br><br><br><br>
						<p style="border-top: thin solid black">(<?=ucwords($value['namapembicara'])?>) </p>
					</td>
					<td style="width: 4cm"></td>
				</tr>
			</table>
			</div>
			<!-- <div style="border: solid;border-color: black ;border-width: thin; width: 1366px; height: 768px">
			</div> -->
		<?php	} ?>
			<!-- <div style="page-break-after:always"></div> -->
		</center>
		<?php
			}
		?>
	</body>
</html>