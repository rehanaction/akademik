<?
// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
        
	require_once($conf['model_dir'].'m_pendaftar.php');


	//parameter
	$periode    = CStr::removeSpecial($_REQUEST['periode']);
	$jalur      = CStr::removeSpecial($_REQUEST['jalur']);
	$tgltes  = CStr::removeSpecial($_REQUEST['tgltes']);
	$seleksi  = CStr::removeSpecial($_REQUEST['tahapujian']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);

	//model
	$p_model='mPendaftar';
	$p_title='ABSENSI PESERTA TES';
	$p_tbwidth = 800;
	$data=$p_model::getAbsen($conn, $periode,$jalur,$tgltes);
	$p_namafile = 'absensi_'.$periode.'_'.$jalur.'_'.$tgltes;
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<!DOCTYPE html>
    <html>
                <head>
                        <title>Cetak Absesnsi</title>   
                        <link rel="icon" type="image/x-icon" href="image/favicon.png">
                        <link href="style/style.css" rel="stylesheet" type="text/css">
                        
                </head>
                <body style="background:white" onLoad="window.print();">
              
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
                <table width="<?= $p_tbwidth-50 ?>" border=1 cellspacing=0 cellpadding="4">
						<tr align="center">
							<td width="30px"><strong>No</strong></td>
							<td width="150px"><strong>No Pendaftar</strong></td>
							<td width="400px"><strong>Nama</strong></td>
							<td><strong>Tanda Tangan</strong></td>
						</tr>
                        <?
                       
                        $no=0;
                        while($pendaftar = $data->FetchRow()){
                                $no++;
                        ?>
                        <tr>
							<td><?=$no?></td>
							<td><?=$pendaftar['nopendaftar']?></td>
							<td><? echo  $pendaftar['gelardepan'].$pendaftar['nama'].$pendaftar['gelarbelakang'] ?></td>
							<td>&nbsp;</td>
						</tr>
						<?php } ?>
				</table>
				</div>
				</center>
                </body>
    </html>
