<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
      
    // require     
	require_once(Route::getModelPath('laporan'));

	//parameter
	$r_periode    = CStr::removeSpecial($_REQUEST['periode']);
	$r_idseminar = CStr::removeSpecial($_REQUEST['idseminar']);

	//model
	$p_model = mLaporan;
	$p_title = 'KUISIONER SEMINAR';
	$p_tbwidth = 1200;
	
	$a_data = $p_model::getRekapKuisioner($conn,$r_idseminar);
	
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<!DOCTYPE html>
<html>
    <head>
            <title>Kuisioner Seminar</title>   
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
						<td> <strong> <?= $a_data['seminar']['namaseminar']?> </strong></td>
					</tr>
					<tr>
						<td width="30px"><strong>Tgl Kegiatan</strong></td>
						<td width="10px"><strong>:</strong></td>
						<td> <strong><?= CStr::formatDateInd($a_data['seminar']['tglkegiatan']) ?> </strong></td>
					</tr>
					<tr>
						<td width="30px"><strong>Periode</strong></td>
						<td width="10px"><strong>:</strong></td>
						<td> <strong> <?= $r_periode?> </strong></td>
					</tr>
				</table>

                <table width="<?= $p_tbwidth-50 ?>" border=1 cellspacing=0 cellpadding="4" style="border-collapse:collapse">
						<tr align="center">
							<td width="30px"><strong>No</strong></td>
							<td width="100px"><strong>No Peserta</strong></td>
							<td width="85px"><strong>Nama</strong></td>
							<?php foreach ($a_data['pertanyaan'] as $k => $row_p) {?>
							<td width="100px"><strong><?= $row_p['pertanyaan']?> </strong></td>
							<?php } ?>
							<td width="150px"><strong>Saran</strong></td>
							<td width="150px"><strong>Kritik</strong></td>						 
						</tr>
                        <?
                        $no=0;
                        $total = array();
                        foreach ($a_data['peserta'] as $row => $value) {
                        	$no++;
                        
                        ?>
	                        <tr>
								<td> <?= $no ?> </td>
								<td> <?= $value['nopeserta'] ?> </td>
								<td><?= $value['nama'] ?></td>
								<?php 
									foreach ($a_data['pertanyaan'] as $k => $row_p) {
									?>
								<td align="center">
									<?php echo $a_data['jawaban'][$value['nopeserta']][$row_p['idpertanyaankuisseminar']] ?>
								</td>
								<?php $total[$row_p['idpertanyaankuisseminar']][$a_data['jawaban'][$value['nopeserta']][$row_p['idpertanyaankuisseminar']]]++;} ?>
								<td><?= $value['saran'] ?></td>
								<td><?= $value['kritik'] ?></td>								
							</tr>
						<?php } ?>
						<tr>
							<td colspan="3" align="center">TOTAL</td>
							<?php foreach ($a_data['pertanyaan'] as $k => $row_p) {?>
							<td>&nbsp;</td>
							<?php } ?>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
						</tr>
				</table>
				
				<br>	
				
				 <table width="<?= $p_tbwidth-50 ?>" border=1 cellspacing=0 cellpadding="4" style="border-collapse:collapse">
					Detail Jawaban
					<tr>
						<?php foreach ($a_data['pertanyaan'] as $k => $row_p) {?>
						<td width="100px"><strong><?= $row_p['pertanyaan']?> </strong></td>
						<?php } ?>
					</tr>
					<tr>
							<?php foreach ($a_data['pertanyaan'] as $a => $v) {?>
							<td width="100px">
								<?php foreach ($total[$v['idpertanyaankuisseminar']] as $teks => $jumlah) {
									echo ($teks ? $teks : 'Tidak Jawab').' : '.$jumlah.'<br>';
								} ?>
							</td>
							<?php } ?>					
					</tr>
				</table>		
			</div>
		</center>
    </body>
</html>
