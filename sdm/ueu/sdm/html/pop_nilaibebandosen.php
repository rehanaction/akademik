<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('bebandosen'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
	$p_dbtable = "bd_bebandosenadet";
	$where = "kodeperiodebd,idpegawaimonev,idpegawai,nobd";
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Input Beban Dosen';
	$p_tbwidth = 600;
	$p_aktivitas = 'NILAI';
	
	$p_model = mBebanDosen;
	
	//struktur view
	$a_inputdet = array();	
	$a_inputdet[] = array('kolom' => 'kategori', 'label' => 'Kategori', 'type' => 'S', 'option' => $p_model::getCKategoriRubrik($conn, false), 'readonly' => true);
	$a_inputdet[] = array('kolom' => 'kegiatan', 'label' => 'Nama Kegiatan', 'readonly' => true);
	$a_inputdet[] = array('kolom' => 'namakegiatan', 'label' => 'Rubrik', 'readonly' => true);
	$a_inputdet[] = array('kolom' => 'sks', 'label' => 'SKS', 'readonly' => true);
	$a_inputdet[] = array('kolom' => 'peran', 'label' => 'Peran', 'readonly' => true);
	$a_inputdet[] = array('kolom' => 'buktipenugasan', 'label' => 'Bukti Penugasan', 'readonly' => true);
	$a_inputdet[] = array('kolom' => 'filebukti', 'label' => 'File Bukti Penugasan', 'type' => 'U', 'uptype' => 'filebukti', 'size' => 40, 'readonly' => true);
	$a_inputdet[] = array('kolom' => 'waktu', 'label' => 'Alokasi Waktu', 'readonly' => true);
	$a_inputdet[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'readonly' => true);
	$a_inputdet[] = array('kolom' => 'buktidokumen', 'label' => 'Bukti Dokumen', 'readonly' => true);
	$a_inputdet[] = array('kolom' => 'filedokumen', 'label' => 'File Bukti Dokumen', 'type' => 'U', 'uptype' => 'filedokumen', 'size' => 40, 'readonly' => true);
	$a_inputdet[] = array('kolom' => 'skscapaian', 'label' => 'SKS Capaian', 'readonly' => true);
	$a_inputdet[] = array('kolom' => 'capaian', 'readonly' => true);
	$a_inputdet[] = array('kolom' => 'tglpenilaian', 'label' => 'Tgl. Penilaian', 'type' => 'D', 'default' => date('Y-m-d'));
	$a_inputdet[] = array('kolom' => 'penilaianmonev', 'label' => 'Penilaian Monev', 'maxlength' => 100, 'size' => 50);
	$a_inputdet[] = array('kolom' => 'filepenilaian', 'label' => 'File Penilaian', 'type' => 'U', 'uptype' => 'filepenilaian', 'size' => 40);
	$a_inputdet[] = array('kolom' => 'skscapaianmonev', 'label' => 'SKS Capaian Monev', 'maxlength' => 3, 'size' => 3, 'type' => 'N');
	$a_inputdet[] = array('kolom' => 'capaianmonev', 'maxlength' => 5, 'size' => 5, 'type' => 'N');
	
	$sql = $p_model::getDataBKDDet(($r_key.'|'.$r_subkey));
	$row = $p_model::getDataEdit($conn,$a_inputdet,($r_key.'|'.$r_subkey),$post,$p_dbtable,$where,$sql);
		
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
	
	//cek apakah sudah final
	if(!empty($r_key))
		$isfinal = $p_model::isFinal($conn,$r_key);
		
	if($isfinal){
		$c_edit = false;
		$c_delete = false;
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="detail" style="width:<?= $p_tbwidth+30 ?>px;height:600px;overflow:auto">
<form name="pageformdet" id="pageformdet" method="post" enctype="multipart/form-data">
<? require_once('inc_databuttonpop.php'); ?>
<div class="Break"></div>
<table border="0" width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="2" class="GridStyle" align="center">
	<tr>
		<td colspan="2" class="DataBG" style="height:25px;"><?= $p_title ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="150px"><?= Page::getDataLabel($row,'kategori') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'kategori') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'kegiatan') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'kegiatan') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'namakegiatan') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'namakegiatan') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'sks') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'sks') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'peran') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'peran') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'buktipenugasan') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'buktipenugasan') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'filebukti') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'filebukti') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'waktu') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'waktu') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'keterangan') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'keterangan') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'buktidokumen') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'buktidokumen') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'filedokumen') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'filedokumen') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'skscapaian') ?></td>
		<td class="RightColumnBG">
			<?= Page::getDataInput($row,'skscapaian') ?> %&nbsp;&nbsp;
			<?= Page::getDataInput($row,'capaian') ?>
		</td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglpenilaian') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'tglpenilaian') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'penilaianmonev') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'penilaianmonev') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'filepenilaian') ?></td>
		<td class="RightColumnBG"><?= Page::getDataInput($row,'filepenilaian') ?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG"><?= Page::getDataLabel($row,'skscapaianmonev') ?></td>
		<td class="RightColumnBG">
			<?= Page::getDataInput($row,'skscapaianmonev') ?> %&nbsp;&nbsp;
			<?= Page::getDataInput($row,'capaianmonev') ?>
		</td>
	</tr>
</table>

<input type="hidden" name="actdet" id="actdet">
<input type="hidden" name="file" id="file">
<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
<input type="hidden" name="subkey" id="subkey" value="<?= $r_subkey ?>">

</form>
</div>

<script type="text/javascript">
var required = "<?= @implode(',',$a_required) ?>";
var ajaxpage = "<?= Route::navAddress('ajax') ?>";

$(document).ready(function() {
	initEditDet(<?= $c_edit ? true : false ?>);
});

function initEditDet(isedit) {
	if(!isedit)
		isedit = false;
	
	if(isedit)
		goEditDet();
	else if(document.getElementById("subkey"))
		if(document.getElementById("subkey").value == "")
			goEditDet();
}

function goEditDet() {
	$("#pageformdet").find("[id='show']").hide();
	$("#pageformdet").find("[id='edit']").show();
	
	$("#pageformdet").find("#be_editdet").hide();
	$("#pageformdet").find("#be_savedet,#be_undodet").show();
}

function goUndoDet(){
    $('.close').click();    
}

function goSaveDet(){
    var pass = true;
	if(typeof(required) != "undefined") {
		if(!cfHighlight(required))
			pass = false;
	}
	
	if(pass) {
        $("#actdet").val('savedet');
        $('#pageformdet').submit();
    }
}

function showRubrik(){
	var kat = $("#kategori").val();
	win = window.open("<?= Route::navAddress('pop_rubrik').'&k='?>"+kat,"popup_rubrik","width=650,height=500,scrollbars=1");
	win.focus();	
}

function goDownload(url,id) {
	if(!id && document.getElementById("subkey"))
		id = document.getElementById("subkey").value;
	
	window.open(url + "&id="+id,"_blank");
}

function goDeleteFile(elem) {
	var hapus = confirm("Apakah anda yakin akan menghapus file ini?");
	if(hapus) {
		if(document.getElementById("file"))
			document.getElementById("file").value = elem;
        $("#actdet").val('deletefile');
        $('#pageformdet').submit();
	}
}
</script>
</body>
</html>
