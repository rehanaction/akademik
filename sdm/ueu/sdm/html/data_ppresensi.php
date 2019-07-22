<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_readlist = true;		
	$c_other = $a_auth['canother'];
	$c_kepeg = $c_other['K'];
	$c_valid = $c_other['V'];
	
	// include
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
		
	// variabel request
	if(SDM::isPegawai())
		$r_self = 1;
	
	if($c_kepeg){
		$c_insert = $a_auth['caninsert'];
		$c_edit = $a_auth['canupdate'];
		$c_delete = $a_auth['candelete'];
	}
		
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
			
	$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
	
	// properti halaman
	$p_title = 'Data Input Presensi';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mPresensi;
	$p_dbtable = "pe_presensiinput";
	$where = 'nopresensiinput';
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'tglmulai', 'label' => 'Tgl. Mulai','type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'tglselesai', 'label' => 'Tgl. Selesai','type' => 'D');
	$a_input[] = array('kolom' => 'kodeabsensi', 'label' => 'Jenis Absensi', 'type' => 'S', 'option' => $p_model::jenisAbsensi($conn), 'add' => 'onchange="changeJenis()"');		
	$a_input[] = array('kolom' => 'jamdatang', 'label' => 'Jam Datang', 'maxlength' => 4, 'size' => 4, 'infoedit' => 'Contoh : 0800');
	$a_input[] = array('kolom' => 'jampulang', 'label' => 'Jam Pulang', 'maxlength' => 4, 'size' => 4, 'infoedit' => 'Contoh : 1700');
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['idpegawai'] = $r_key;
		$conn->BeginTrans();
		
		$record['jamdatang'] = str_replace(':','',$record['jamdatang']);
		$record['jampulang'] = str_replace(':','',$record['jampulang']);
		if ($record['kodeabsensi'] == 'S' or $record['kodeabsensi'] == 'ST' or $record['kodeabsensi'] == 'I' or $record['kodeabsensi'] == 'TB'){
			unset($record['jamdatang']);
			unset($record['jampulang']);
		}
		
		if(empty($r_subkey)){
			$record['tglpemasukan'] = date('Y-m-d');
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where,true);
		}else{		
			list($p_posterr,$p_postmsg) = $p_model::deletePresensiDetail($conn,$r_subkey);
			
			if(!$p_posterr)
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
		}
		
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::savePresensiDetail($conn,$r_subkey);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
				
		if(!$p_posterr){
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {	
		list($p_posterr,$p_postmsg) = $p_model::deletePresensiDetail($conn,$r_subkey);
		
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where);
		
		if(!$p_posterr) Route::navListpage($p_listpage,$r_key);
	}
	
	$sql = $p_model::getDataInputPresensi($r_subkey);	
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$where,$sql);
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
		
		//setting jam		
		if($t_row['id'] == 'jamdatang')
			$jamdatang = $t_row['value'];	
		if($t_row['id'] == 'jampulang')
			$jampulang = $t_row['value'];
		if($t_row['id'] == 'kodeabsensi')
			$kodeabsensi = $t_row['realvalue'];
	}
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();

?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foreditx.js"></script>
</head>
<body>
	<table width="100%">
		<tr>
			<td>
			<form name="pageform" id="pageform" method="post">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	}
					
					/*****************/
					/* TOMBOL-TOMBOL */
					/*****************/
					
					if(empty($p_fatalerr))
						require_once('inc_databuttonajax.php');
					
					if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	}
				
					if(empty($p_fatalerr)) { ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
					<?	/********/
						/* DATA */
						/********/
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td width="200px" class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglmulai') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'tglmulai') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglselesai') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'tglselesai') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodeabsensi') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'kodeabsensi') ?></td>
						</tr>
						<tr id="tr_jamdatang">
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'jamdatang') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'jamdatang') ?></td>
						</tr>
						<tr id="tr_jampulang">
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'jampulang') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'jampulang') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'keterangan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'keterangan') ?></td>
						</tr>						
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey" value="<?= $r_subkey; ?>">
				<?	} ?>
			</form>
		</td>
	</tr>
</table>
<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";
var xtdid = "contents";

$(document).ready(function() {

	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	$("#jamdatang").mask("12:34");
	$("#jampulang").mask("12:34");
	
	changeJenis();
});

function changeJenis(){
	if($("#kodeabsensi").val() == 'H' || $("#kodeabsensi").val() == 'HL' || '<?= $kodeabsensi?>' == 'H' || '<?= $kodeabsensi?>' == 'HL'){
		$("#tr_jamdatang").show();
		$("#tr_jampulang").show();
		$("#jamdatang").val('<?= $jamdatang?>');
		$("#jampulang").val('<?= $jampulang?>');
	}else{
		$("#tr_jamdatang").hide();
		$("#tr_jampulang").hide();
		$("#jamdatang").val('');
		$("#jampulang").val('');
	}
}

function cekJam(){
	var jawal,jakhir,err=false;
	formatjam = /^[0-1][0-9]|2[0-4]:[0-5][0-9]/;
	
	jawal=$("#jamdatang").val();
	jakhir=$("#jampulang").val();
	
	jamdatang=jawal.substring(0,2);
	menitawal=jawal.substring(3,5);
	jampulang=jakhir.substring(0,2);
	menitakhir=jakhir.substring(3,5);
	
	if(jamdatang=='24' && menitawal!='00'){
		jawal='';
	}
	else {
		if(jawal.match(formatjam))
			jawal = jawal;
		else
			jawal='';
	}
	if(jampulang=='24' && menitakhir!='00'){
		jakhir='';
	}
	else{
		if(jakhir.match(formatjam))
			jakhir = jakhir;
		else
			jakhir='';
	}
	
	if(jawal == ''){
		doHighlight(document.getElementById("jamdatang"));
		alert("Format Jam tidak sesuai");
		err=true;	
	}
	if(jakhir == ''){
		doHighlight(document.getElementById("jampulang"));
		alert("Format Jam tidak sesuai");
		err=true;	
	}
	
	return err;
}

function goSave() {
	var err;
	if($("#kodeabsensi").val() == 'H' || $("#kodeabsensi").val() == 'HL' || '<?= $kodeabsensi?>' == 'H' || '<?= $kodeabsensi?>' == 'HL')
		err = cekJam();

	var pass = true;
	if(typeof(required) != "undefined") {
		if(!cfHighlight(required))
			pass = false;
	}
	
	if(pass && !err) {
		document.getElementById("act").value = "save";
		goSubmit();
	}
}
</script>
</body>
</html>
