<?
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
      
    // require     
	require_once(Route::getModelPath('seminar'));

	//parameter
	$peserta = CStr::removeSpecial($_REQUEST['peserta']);
	$jenis = CStr::removeSpecial($_REQUEST['jenis']);
	$periode    = CStr::removeSpecial($_REQUEST['periode']);
	
	//model
	$p_model = mSeminar;
	$p_title = 'Rekap Seminar Gratis / Berbayar';
	$p_tbwidth = 900;
                                                                      
	$a_data = $p_model::getGratisBayar($conn,$peserta,$jenis,$periode);

	/*print_r($a_data);*/

	if ($periode % 2 == 0) {
		$peri = 'Genap';
	} else {
		$peri = 'Gasal';
	}

	if ($peserta == 'M') {
		$peserta = 'Mahasiswa' ;
	} else if ($peserta == 'P') {
		$peserta = 'Pegawai' ;
	} else {
		$peserta = 'Umum' ;
	} 

	if ($jenis == '0') {
		$jenis = 'Berbayar' ;
	} else {
		$jenis = 'Gratis' ;
	}
	
	$periode = substr($periode, 0,4);
		
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<!DOCTYPE html>
    <html>
                <head>
                        <title>Rekap Seminar Gratis / Berbayar</title>   
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
						<td width="30px"><strong>Peserta</strong></td>
						<td width="10px"><strong>:</strong></td>
						<td> <strong><?= $peserta ?> </strong></td>
					</tr>
					<tr>
						<td width="30px"><strong>Jenis</strong></td>
						<td width="10px"><strong>:</strong></td>
						<td> <strong><?= $jenis ?> </strong></td>
					</tr>
					<tr>
						<td width="30px"><strong>Periode</strong></td>
						<td width="10px"><strong>:</strong></td>
						<td> <strong><?=!empty($periode)? $peri .' '.$periode.'/'.($periode+1):''?> </strong></td>
					</tr>
				</table>
				
				
                <table width="<?= $p_tbwidth-50?>" border=1 cellspacing=0 cellpadding="4">
						<tr align="center">
							<td width="10px"><strong>No</strong></td>
							<td width="150px"><strong>Nama Seminar</strong></td>
							<td width="20px"><strong>Tanggal/Jam</strong></td>
							<td width="80px"><strong>Tempat</strong></td>
							<td width="90px"><strong>Tanggal Pendaftaran</strong></td>
							<td width="70px" style="word-wrap:break-word"><strong>Contact Person</strong></td>
							<td width="50px"><strong>Batas Akhir Pembayaran</strong></td>
							<td width="70px"><strong>Tarif</strong></td>
						</tr>
						
						<? if(!empty($a_data)) {
                        
                            $no=0;
								foreach ($a_data as $row => $value) {
                        	$no++;
                        ?>  
	                        <tr>
								<td><?= $no ?></td>
								<td><?= $value['namaseminar'] ?></td>
								<td><?= CStr::formatDateInd($value['tglkegiatan']).' / '.'</br>'.$value['jammulai'].' - '.$value['jamselesai']  ?></td>
								<td><?= $value['koderuang'] ?></td>
								<td><?= CStr::formatDateInd($value['tglawaldaftar']).' s.d '.'</br>'.CStr::formatDateInd($value['tglakhirdaftar'])?></td>
								<td><?= $value['cp'] ?> </td>
								<td><?= $value['batasbayar'] ?> </td>
								<td><?= empty($value['tarif'])? 'Gratis': 'Rp. '.  number_format($value['tarif'],0,',','.'); ?></td>
							</tr>
							
							     <? } 
									
							} else { 
							?>										
								<tr>
								<td colspan='8' align='center'><b><? echo ('DATA TIDAK DITEMUKAN');?><b></td>
								</tr>
								 
						
								 
						<?php } ?>
				</table>
				</div>
				</center>
                </body>
    </html>
