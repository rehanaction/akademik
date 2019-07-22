<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('angkakredit'));
	
	// variabel request
	$m = CStr::removeSpecial($_REQUEST['m']);
	$b = CStr::removeSpecial($_REQUEST['b']);
	$id = empty($_REQUEST['id']) ? 'idkegiatan' :CStr::removeSpecial($_REQUEST['id']);
	$nama = empty($_REQUEST['id']) ? 'kegiatan' : CStr::removeSpecial($_REQUEST['nama']);
	
	// properti halaman
	$p_title = 'Daftar Aturan Penilaian Angka Kredit';
	$p_tbwidth = 600;
	
	$p_col = 3;
	$p_model = mAngkaKredit;
	
	$a_data = $p_model::getPenilaian($conn,$b);
	
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
<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr class="DataBG" height="30px">
		<td align="center" colspan="<?= $p_col?>"><?= $p_title?></td>
	</tr>
		<th width="100">Kode</th>
		<th>Nama Kegiatan</th>
		<th width="100">Kredit</th>
	</tr>
	<?							
		$i = 0;
		if(count($a_data)>0){
			foreach($a_data as $row) {
				if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
	?>
	<tr valign="top" class="<?= $rowstyle ?>">
		<td align="center">
			<? if($m == 1) { ?>
			<u onClick="goSend('<?= $row['idkegiatan'] ?>','<?= $row['kodekegiatan'].' - '.preg_replace("/[^a-z0-9_\-\.]/i"," ",$row['namakegiatan']) ?>');" style="cursor:pointer;color:#3300FF;"><?= $row['kodekegiatan']; ?></u>
			<? } else if($m == 2 and $row['stdkredit'] != '') { ?>
			<u onClick="goSendKredit('<?= $row['idkegiatan'] ?>','<?= $row['kodekegiatan'].' - '.preg_replace("/[^a-z0-9_\-\.]/i"," ",$row['namakegiatan']) ?>','<?= number_format($row['stdkredit'],2) ?>');" style="cursor:pointer;color:#3300FF;"><?= $row['kodekegiatan']; ?></u>			
			<? } else { ?>
			<?= $row['kodekegiatan']; ?>
			<? } ?>		
		</td>
		<td style="padding-left:<?= ($row['level']*10)+5 ?>px"><?= $row['namakegiatan']?></td>
		<td align="center"><?= $row['stdkredit']?></td>
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

function goSend(idkegiatan,namakegiatan) {
	window.opener.document.getElementById("parentkegiatan").value = namakegiatan;
	window.opener.document.getElementById("parentidkegiatan").value = idkegiatan;
	window.close();
}

function goSendKredit(idkegiatan,namakegiatan,stdkredit) {
	var b = '<?= $b?>';
	window.opener.document.getElementById("<?= $id?>").value = idkegiatan;
	window.opener.document.getElementById("<?= $nama?>").value = namakegiatan;
	if(b == 'II'){
		if(window.opener.document.getElementById("stdkredit"))
			window.opener.document.getElementById("stdkredit").value = stdkredit;
	}
	else{
		if(window.opener.document.getElementById("kreditmax"))
			window.opener.document.getElementById("kreditmax").value = stdkredit;
	}	
	window.close();
}
</script>
</body>
</html>
