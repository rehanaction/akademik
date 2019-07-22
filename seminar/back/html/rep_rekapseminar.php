<?
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
      
    // require     
	require_once(Route::getModelPath('seminar'));

	//parameter
	$tglawal = CStr::formatDate($_REQUEST['tglawaldaftar']);
	$tglakhir = CStr::formatDate($_REQUEST['tglakhirdaftar']);
	$periode = CStr::removeSpecial($_REQUEST['periode']);	

	//model
	$p_model = mSeminar;
	$p_title = 'DAFTAR REKAP SEMINAR';
	$p_tbwidth = 900;
                                                                      
	$a_data = $p_model::getTampilRekap($conn,$tglawal,$tglakhir,$periode);

	if ($periode % 2 == 0) {
		$peri = 'Genap';
	} else {
		$peri = 'Gasal';
	}

	$periode = substr($periode, 0,4);
		
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<!DOCTYPE html>
    <html>
                <head>
                        <title>Cetak Rekap Seminar</title>   
                        <link rel="icon" type="image/x-icon" href="image/favicon.png">
                        <link href="style/style.css" rel="stylesheet" type="text/css">
                        
                </head>
                <body style="background:white">
              
                        <center>
                        <div style="border: solid; border-width: thin; border-color: #c5c5c5; width:<?=$p_tbwidth ?>px;" >
                        
                        <table width="<?=$p_tbwidth ?>px">
							<tr>
							<td align="center" width="100"><img src="images/logo.jpg" height="70"></td>
							<td align="left">								   
								<span style="font-size: 1;">KEMENTERIAN PENDIDIKAN NASIONAL DAN KEBUDAYAAN</span><br>
								<span style="font-size: 1;">UNIVERSITAS ESA UNGGUL JAKARTA</span><br>
								<span style="font-size: 1;">Jalan Arjuna Utara No.9, Kebon jeruk-Jakarta Barat 11510</span><br>
								<span style="font-size: 1;">021-5674223 (hunting) 021-5682510 (direct) Fax: 021-5674248 <br>Website:www.esaunggul.ac.id, email:info@esaunggul.ac.id</span>

							</td>
						</tr>
					</table>
				<hr>

				<center>
					 <strong>
						 <?=$p_title?>
						 	<br>
						 <br>
					</strong>     
				</center> 

				<table width="<?= $p_tbwidth-50?>">
					<tr>
						<td width="30px"><strong>Periode</strong></td>
						<td width="10px"><strong>:</strong></td>
						<td> <strong><?=!empty($periode)? $peri .' '.$periode.'/'.($periode+1):''?> </strong></td>
					</tr>
					<tr>
						<td width="30px"><strong>Tanggal</strong></td>
						<td width="10px"><strong>:</strong></td>
						<td> <strong><?= CStr::formatDateInd($tglawal) .' s.d '. CStr::formatDateInd($tglakhir) ?> </strong></td>
					</tr>
				</table>
				
				
                <table width="<?= $p_tbwidth-50?>" border=1 cellspacing=0 cellpadding="4">
						<tr align="center">
							<td width="30px"><strong>No</strong></td>
							<td width="120px"><strong>Nama Seminar</strong></td>
							<td width="100px"><strong>Tanggal Kegiatan</strong></td>
							<td width="20px"><strong>Ruang</strong></td>
							<td width="80px"><strong>Total Peserta</strong></td>
							<td width="80px"><strong>Jumlah Hadir</strong></td>
							<td width="80px"><strong>Contact Person</strong></td>
							<td width="80px"><strong>Deskripsi</strong></td>
						</tr>
                        <?
                       
                        $no=0;
                        foreach ($a_data as $row => $value) {

                        	$no++;
                        
                        ?>
	                        <tr>
								<td> <?= $no ?> </td>
								<td> <?= $value['namaseminar'] ?> </td>
								<td><?= CStr::formatDateInd($value['tglkegiatan']) ?></td>
								<td><?= $value['koderuang'] ?></td>
								<td><?= $value['jumlahpeserta'] ?></td>
								<td><?= $value['hadir'] ?></td>
								<td> <?= $value['cp'] ?> </td>
								<td><?= $value['keterangan'] ?></td>
								
							</tr>
						<?php } ?>
				</table>
				</div>
				</center>
                </body>
    </html>
