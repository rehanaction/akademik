<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	// include
	require_once(Route::getModelPath('angkakredit'));
	require_once(Route::getModelPath('pegawai'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$peg = mPegawai::getSimplePegawai($conn,$r_key);
	
	// properti halaman
	$p_title = 'Daftar Penelitian';
	$p_tbwidth = 900;
	$p_col = 5;
	
	$p_model = mAngkaKredit;
	
	$a_data = $p_model::getPenelitian($conn,$r_key);

?>	
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<center><div class="PagerTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title.'<br>'.$peg['namalengkap'] ?></span></div></center>
<br>
<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr class="DataBG" height="30px">
		<td align="center" colspan="<?= $p_col?>"><?= $p_title ?></td>
	</tr>
		<th width="50">No.</th>
		<th width="100">Tgl. Mulai</th>
		<th width="100">Tgl. Selesai</th>
		<th>Judul Penelitian</th>
		<th>Lokasi Penelitian</th>
	</tr>
	<?							
		$i = 0;
		if(count($a_data)>0){
			foreach($a_data as $row) {
				if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				$send = $row['idpenelitian'].'|'.preg_replace("/[^a-z0-9_\-\.]/i"," ",$row['judulpenelitian']).'|'.$row['outputpenelitian'].'|'.preg_replace("/[^a-z0-9_\-\.]/i"," ",$row['lokasipenelitian']).'|'.CStr::formatDate($row['tglmulai']).'|'.$row['idkegiatan'].'|'.$row['kodekegiatan'].'|'.$row['namakegiatan'].'|'.number_format($row['stdkredit'],2);
	?>
	<tr valign="top" class="<?= $rowstyle ?>">
		<td align="center">
			<u onClick="goSend('<?= $send ?>');" style="cursor:pointer;color:#3300FF;" title="Pilih untuk angka kredit"><?= $i; ?></u>
		</td>
		<td><?= CStr::formatDateInd($row['tglmulai'])?></td>
		<td><?= CStr::formatDateInd($row['tglselesai'])?></td>
		<td><?= $row['judulpenelitian']?></td>
		<td><?= $row['lokasipenelitian']?></td>
	</tr>
	<?		}
		}
		if($i == 0) {
	?>
	<tr>
		<td colspan="<?= $p_col?>" align="center">Data kosong</td>
	</tr>
	<?	} ?>
	<tr>
		<td colspan="<?= $p_col?>" align="right" class="FootBG">&nbsp;</td>
	</tr>
</table>
				
<script type="text/javascript">

function goSend(send) {
	sendto = send.split('|');
	window.opener.document.getElementById("idpenelitian").value = sendto[0];
	window.opener.document.getElementById("judulpenelitian").value = sendto[1];
	window.opener.document.getElementById("outputpenelitian").value = sendto[2];
	window.opener.document.getElementById("lokasi").value = sendto[3];
	window.opener.document.getElementById("tgl").value = sendto[4];
	window.opener.document.getElementById("idkegiatan").value = sendto[5];
	window.opener.document.getElementById("kegiatan").value = sendto[6]+' - '+sendto[7];
	window.opener.document.getElementById("stdkredit").value = sendto[8];
	window.close();
}
</script>
</body>
</html>
