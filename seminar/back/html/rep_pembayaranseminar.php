<?	
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
      
    // require     
	require_once(Route::getModelPath('pesertaseminar'));

	//parameter
	$idseminar = CStr::removeSpecial($_REQUEST['idseminar']);
	$status = CStr::removeSpecial($_REQUEST['status']);

	//model
	$p_model = mPesertaSeminar;
	$p_title = 'Rekap Data Pembayaran';
	$p_tbwidth = 800;

	$a_data = $p_model::getTampilStatusbayar($conn,$idseminar,$status);
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<!DOCTYPE html>
    <html>
                <head>
                        <title>Rekap Data Pembayaran</title>   
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
								   
								<span style="font-size: 1;">KEMENTRIAN PENDIDIKAN NASIONAL DAN KEBUDAYAAN</span><br>
								<span style="font-size: 1;">UNIVERSITAS ESA UNGGUL JAKARTA</span><br>
								<span style="font-size: 1;">Jalan Arjuna Utara No.9, Kebon jeruk-Jakarta Barat 11510</span><br>
								<span style="font-size: 1;">021-5674223 (hunting) 021-5682510 (direct) Fax: 021-5674248 Website:www.esaunggul.ac.id, email:info@esaunggul.ac.id</span>

							</td>
						</tr>
					</table>
				<hr>
				<center>
				 <strong>
					 <?=$p_title?>
					 <?=!empty($jalur)?'PROGRAM '.strtoupper($jalur):''?><br><br>
				</strong>     
				</center> 
				<br>
				<table width="<?= $p_tbwidth-50?>">
					<tr>
						<td width="100px"><strong>Nama Seminar</strong></td>
						<td width="10px"><strong>:</strong></td>
						<td> <strong> <?= $a_data[0]['namaseminar']?> </strong></td>
					</tr>
					<tr>
						<td width="100px" style="white-space:nowrap;"><strong>Batas Akhir Pembayaran</strong></td>
						<td width="10px"><strong>:</strong></td>
						<td> <strong> <?= CStr::formatDateInd($a_data[0]['batasbayar'])?> </strong></td>
					</tr>
				</table>
                <table width="<?= $p_tbwidth-50 ?>" border=1 cellspacing=0 cellpadding="4">
						<tr align="center">
							<td width="30px"><strong>No</strong></td>
							<td width="120px"><strong>No Peserta</strong></td>
							<td width="350px"><strong>Nama</strong></td>
							<td width="350px"><strong>Waktu Daftar</strong></td>
							<td width="350px"><strong>Biaya Seminar</strong></td>
							<td width="350px"><strong>Status Pembayaran</strong></td>
						</tr>
						
                        <?php
						if(!empty($a_data)){
                        $no=0;
						$total=0;
                        foreach ($a_data as $row => $value) {
                        	$no++;                        
                        ?>
	                        <tr>
								<td> <?= $no ?> </td>
								<td> <?= $value['nopeserta'] ?> </td>
								<td><?= $value['namapeserta'] ?></td>
								<td><?= $value['waktudaftar'] ?></td>
								<td><?= 'Rp. '.number_format($value['tarif'],0,',','.' )?></td>
								<td><?= $value['lunas'] ?></td>
							</tr>
							
							
							<? 
								$total=$total+$value['tarif'];
								
							} ?>
							
							<tr>
								<td colspan="4"><strong>Total<strong></td>
								<td colspan="2"><strong>Rp. <?  echo (number_format($total,0,',','.'));?><strong></td>
								
							</tr>
							
							<? } else { ?>
								<tr>
									<td colspan="8" align="center"><b><? echo ('DATA TIDAK DITEMUKAN'); ?></b></td>
								</tr>
						<?php } ?>
				</table>
				</div>
				</center>
                </body>
    </html>
