<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pa'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$p_dbtable = "bd_monevdet";
	$where = "kodeperiodebd,idpegawaimonev,idpegawai";
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	$r_act = $_POST['act'];
	if($r_act == 'savedet' and $c_edit) {
		$r_pegawai = CStr::removeSpecial($_REQUEST['idpegawaidet']);
		list($p_posterr,$p_postmsg) = $p_model::saveMonev($conn,$r_key,$r_pegawai);
	}
	else if($r_act == 'deldet' and $c_delete) {
		$r_keydet = CStr::removeSpecial($_REQUEST['keydet']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,'bd_monev','kodeperiodebd,idpegawaimonev');
	}
	
	// properti halaman
	$p_title = 'Data Dosen Yang Dinilai';
	$p_tbwidth = 600;
	$p_aktivitas = 'NILAI';
	
	$p_model = mPa;
	
	$row = $p_model::getInfoMonev($conn,$r_key);
	$a_data = $p_model::getDosenDinilai($conn,$r_key);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="detail" style="width:<?= $p_tbwidth+30 ?>px;height:500px;overflow:auto">
<form name="pageformdet" id="pageformdet" method="post" action="<?= Route::navAddress(Route::thisPage()) ?>">
<table border="0" width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="2" align="center">
	<tr>
		<td>
			<table border="0" width="100%" cellpadding="4" cellspacing="2" class="GridStyle" align="center">
				<tr>
					<td colspan="4" class="DataBG" style="height:25px;border-top-left-radius:4px;border-top-right-radius:4px;border:none;"><?= $p_title ?></td>
				</tr>
				<tr>
					<td class="LeftColumnBG">Nama Periode</td>
					<td class="RightColumnBG"><?= $row['periodebd'] ?></td>
				</tr>
				<tr>
					<td class="LeftColumnBG">Tgl. Penilaian</td>
					<td class="RightColumnBG"><?= CStr::formatDateInd($row['tglawal']).' s/d '.CStr::formatDateInd($row['tglawal']) ?></td>
				</tr>
				<tr>
					<td class="LeftColumnBG">Nama Monev</td>
					<td class="RightColumnBG"><?= $row['namalengkap'] ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="center">
			&nbsp;
			<?if($c_edit){?>
			<div class="filterTable" style="width:<?= $p_tbwidth ?>px;">
			<table width="100%">
				<tr>
					<td><strong>Pegawai</strong></td>
					<td>
						: <?= UI::createTextBox('pegawaidet', '','ControlStyle',100,40,$c_edit); ?>
						<input type="hidden" name="idpegawaidet" id="idpegawai" value="" />
						<img id="imgnik_c" src="images/green.gif">
						<img id="imgnik_u" src="images/red.gif" style="display:none">&nbsp;&nbsp;
						<input type="button" value="Simpan" id="be_savedet" class="ControlStyle" onClick="goSaveDet()">
					</td>
				</tr>
			</table>
			</div>	
			<?}?>
		</td>
	</tr>
	<tr>
		<td>
			<table cellpadding="4" cellspacing="0" class="GridStyle" width="100%">
				<tr class="DataBG">
					<td align="center">Dosen Yang Dinilai</td>
					<td align="center" width="50px">Aksi</td>
				</tr>
				<?
				if(count($a_data)>0){
					foreach($a_data as $key){
				?>
				<tr>
					<td><?= $key['namalengkap']?></td>
					<td align="center" width="50px">
						<img id="<?= $key['kodeperiodebd'].'|'.$key['idpegawaimonev'] ?>" title="Hapus Data" src="images/delete.png" onclick="goDeleteDet(this)" style="cursor:pointer">
						<img title="Halaman Dosen yang dinilai" src="images/link.png" onClick="openDetail('<?= $key['kodeperiodebd'].'|'.$key['idpegawaimonev'] ?>')" style="cursor:pointer">
					</td>
				</tr>
				<?
					}
				}
				?>
			</table>
		</td>
	</tr>
</table>

<input type="hidden" name="actdet" id="actdet">
<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
<input type="hidden" name="keydet" id="keydet">

</form>
</div>

<script type="text/javascript">

$(document).ready(function() {	
	//autocomplete
	$("#pegawaidet").xautox({strpost: "f=acnamapegawai", targetid: "idpegawaidet", imgchkid: "imgpeg", imgavail: true});
});

function goUndoDet(){
    $('.close').click();    
}

function goSaveDet(){
	if($("#idpegawaidet").val() == ''){
		doHighlight(document.getElementById("pegawaidet"));
		alert("Silahkan masukkan nama atau NPP Pegawai yang dinilai");
	}else{
		document.getElementById("actdet").value = "savedet";
		$('#pageformdet').submit();
	}	
}

function goDeleteDet(val){
	document.getElementById("actdet").value = "deldet";
	document.getElementById("keydet").value = val.id;
    $('#pageformdet').submit();
}
</script>
</body>
</html>
