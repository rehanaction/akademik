<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=true;

	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('pesertaseminar'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['nopeserta']);
	$r_format = $_REQUEST['format'];
	
	// properti halaman
	$p_title = 'Kuitansi Pembayaran Seminar';
	$p_tbwidth = 700;
	$p_maxrow = 46;
	$p_maxday = 16;
	
	$a_data =  mPesertaSeminar::getBuktiPembayara($conn,$r_key);

	Page::setHeaderFormat($r_format,$p_namafile);
?>

<!DOCTYPE html>
    <html>
                <head>
                        <title>Cetak Absesnsi</title>   
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
								<span style="font-size: 1;">UNIVERSITAS ESA UNGGUL SURABAYA</span><br>
								<span style="font-size: 1;">Jalan Arjuna Utara No.9, Kebon jeruk-Jakarta Barat 11510</span><br>
								<span style="font-size: 1;">021-5674223 (hunting) 021-5682510 (direct) Fax: 021-5674248 Website:www.esaunggul.ac.id, email:info@esaunggul.ac.id</span>

							</td>
						</tr>
					</table>
				<hr>
				<center>
				 <strong>
					 <?=$p_title?>
					 <?=!empty($jalur)?'PROGRAM '.strtoupper($jalur):''?><br>
					 <?=!empty($periode)?'PERIODE '.$periode.'/'.($periode+1):''?><br>
					  <?=!empty($periode)? 'SELEKSI '.strtoupper($seleksi):''?><br>
					
				</strong>     
				</center> 
				<br>
				<div align="left" style="width:<?= $p_tbwidth-50 ?>px">
                 <strong><?=!empty($tgltes)?"Tanggal Tes : ".date('d-m-Y',strtotime($tgltes)):""?></strong>     
				</div>  
                <table width="<?= $p_tbwidth-50 ?>" cellspacing=0 cellpadding="4">
                		<?
                        $no = 0;
                        foreach ($a_data as $row => $rowk) {
                        	$no++ ; 
                        ?>

							<tr>
								<td><strong>Nama</strong></td>
								<td> <?= $rowk['nama']?> </td>
								<td><strong>Nama Seminar</strong></td>
								<td> <?= $rowk['namaseminar']?> </td>
							</tr>

							<tr>
								<td><strong>ID Pendaftar</strong></td>
								<td> <?= $rowk['nopeserta']?> </td>
								<td><strong>Tgl Seminar</strong></td>
								<td> <?= $rowk['tglkegiatan']?> </td>
							</tr>

							<tr>
								<td><strong>Tgl Bayar</strong></td>
								<td> <?= $rowk['waktubayar']?> </td>
								<td><strong>Kasir</strong></td>
								<td> &nbsp; </td>
							</tr>

							<tr>
								<td><strong>Tarif</strong></td>
								<td> 
									<?php
										if (empty($rowk['tarif'])) {
											echo "Gratis";
										} else {
											echo $rowk['tarif'] ;
										}
									?>  
								</td>
							</tr>
	                    
						<?php 
							} 
						?>
				</table>

				<table class="tb_foot" width="<?= $p_tbwidth-50 ?>" cellspacing=0 cellpadding="4">
					<tr align="center">
						<td width="450">&nbsp;</td>
						<td>Mengetahui,</td>
					</tr>
					<tr align="center">
						<td width="450">&nbsp;</td>
						<td class="mark">Jakarta, <?= CStr::formatDateInd(date('Y-m-d')) ?></td>
					</tr>
					<tr align="center">
						<td width="450">&nbsp;</td>
						<td>Kasir</td>
					</tr>
				</table>

				</div>
				</center>
                </body>
    </html>
