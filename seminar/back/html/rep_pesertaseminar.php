<?
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
      
    // require     
	require_once(Route::getModelPath('pesertaseminar'));

	//parameter
	$periode    = CStr::removeSpecial($_REQUEST['periode']);
	$id_seminar = CStr::removeSpecial($_REQUEST['idseminar']);

		
	//model
	$p_model = mPesertaSeminar;
	$p_title = 'DAFTAR PESERTA SEMINAR';
	$p_tbwidth = 1200;
	$a_data = $p_model::getTampilPeserta($conn,$id_seminar,$periode);
	
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
                        <title>Cetak Daftar Peserta Seminar</title>   
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
				<table width="<?= $p_tbwidth-50?>">
					<tr>
						<td width="100px"><strong>Nama Seminar</strong></td>
						<td width="10px"><strong>:</strong></td>
						<td> <strong> <?= $a_data[0]['namaseminar']?> </strong></td>
					</tr>
					<tr>
						<td width="30px"><strong>Tgl Kegiatan</strong></td>
						<td width="10px"><strong>:</strong></td>
						<td> <strong><?= CStr::formatDateInd($a_data[0]['tglkegiatan']) ?> </strong></td>
					</tr>
					<tr>
						<td width="30px"><strong>Periode</strong></td>
						<td width="10px"><strong>:</strong></td>
						<td> <strong> <?= $peri .' '.$periode .'/'.($periode+1) ?> </strong></td>
					</tr>
				</table>

                <table width="<?= $p_tbwidth-50?>" border=1 cellspacing=0 cellpadding="4">
						<tr align="center">
							<td width="30px"><strong>No</strong></td>
							<td><strong>No Peserta</strong></td>
							<td><strong>Nama</strong></td>
							<td width="25px"><strong>L/P</strong></td>
							<td><strong>Fakultas</strong></td>
							<td><strong>Jurusan</strong></td>
							<td><strong>No KTP</strong></td>
							<td><strong>Telp</strong></td>
							<td><strong>Email</strong></td>
							<td><strong>Tempat, Tgl lahir</strong></td>
							<td><strong>Alamat</strong></td>
							<td><strong>Kode Pos</strong></td>
							<td width="50px"><strong>Bekerja</strong></td>
							<td><strong>Institusi</strong></td>
							<td width="70px"><strong>Keterangan</strong></td>
						</tr>
                        <?
                       
                        $no=0;
                        foreach ($a_data as $row => $value) {

                        	$no++;
                        
                        ?>
	                        <tr>
								<td> <?= $no ?> </td>
								<td> <?= $value['nopeserta'] ?> </td>
								<td><?= $value['nama'] ?></td>
								<td><?= $value['sex'] ?></td>
								<td><?= $value['fakultas'] ?></td>
								<td><?= $value['namaunit'] ?></td>
								<td><?= $value['noktp'] ?></td>
								<td><?= $value['hp'] ?></td>
								<td><?= $value['email'] ?></td>
								<td><?= $value['tmplahir'] .', '.CStr::formatDateInd($value['tgllahir']) ?></td>
								<td><?= $value['alamat'].' '.$value['namakota'] ?></td>
								<td><?= $value['kodepos'] ?></td>
								

								<?php 
									if ($value['iskerja'] == '1') {
										$status = 'Ya';
									} else $status = 'Tidak';
								?>

								<td> <?= $status ?></td>
								<td><?= $value['namaperusahaan'] ?></td>

								<?php  
									if (empty($value['nim'])) {
										if (empty($value['nip'])) {
											$a = 'Umum' ;
										} else {
											$a = 'Pegawai' ;
										}
									} else {
										$a = 'Mahasiswa' ;
									}
								?>
								<td><? echo $a ;?></td>
							</tr>
						<?php } ?>
				</table>
				</div>
				</center>
                </body>
    </html>
