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
	$p_title = 'Daftar Riwayat Pendidikan';
	$p_tbwidth = 900;
	$p_col = 5;
	
	$p_model = mAngkaKredit;
	
	$a_data = $p_model::getPendidikan($conn,$r_key);

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
		<td align="center" colspan="<?= $p_col?>"><?= $p_title?></td>
	</tr>
		<th width="50">No.</th>
		<th>Jenjang Pendidikan</th>
		<th>Nama Institusi</th>
		<th>No. Ijazah</th>
		<th width="100">Tgl. Ijazah</th>
	</tr>
	<?							
		$i = 0;
		if(count($a_data)>0){
			foreach($a_data as $row) {
				if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				$send = $row['nourutrpen'].'|'.$row['idpendidikan'].'|'.$row['namapendidikan'].'|'.$row['namainstitusipend'].'|'.$row['namafakultas'].'|'.$row['namajurusan'].'|'.$row['namabidang'].'|'.$row['noijazah'].'|'.Cstr::formatDate($row['tglijazah']).'|'.$row['noijazahnegara'].'|'.Cstr::formatDate($row['tglijazahnegara']);
	?>
	<tr valign="top" class="<?= $rowstyle ?>">
		<td align="center">
			<u onClick="goSend('<?= $send ?>');" style="cursor:pointer;color:#3300FF;" title="Pilih untuk angka kredit"><?= $i; ?></u>
		</td>
		<td><?= $row['namapendidikan']?></td>
		<td><?= $row['namainstitusipend']?></td>
		<td><?= $row['noijazah']?></td>
		<td align="center"><?= Cstr::formatDateInd($row['tglijazah'])?></td>
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
	window.opener.document.getElementById("nourutrpen").value = sendto[0];
	window.opener.document.getElementById("jenjang").value = sendto[1];
	window.opener.document.getElementById("namapendidikan").value = sendto[2];
	window.opener.document.getElementById("namainstitusi").value = sendto[3];
	window.opener.document.getElementById("fakultas").value = sendto[4];
	window.opener.document.getElementById("jurusan").value = sendto[5];
	window.opener.document.getElementById("bidang").value = sendto[6];
	window.opener.document.getElementById("noijazah").value = sendto[7];
	window.opener.document.getElementById("tglijazah").value = sendto[8];
	window.opener.document.getElementById("noijazahnegara").value = sendto[9];
	window.opener.document.getElementById("tglijazahnegara").value = sendto[10];
	window.close();
}
</script>
</body>
</html>
