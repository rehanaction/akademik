<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('bebandosen'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mBebanDosen;
		
	$p_tbwidth = "500";
	$p_title = "Data Periode BKD";
	$p_aktivitas = 'NILAI';
	$p_listpage = Route::getListPage();
	$p_dbtable = "ms_periodebd";
	$p_key = "kodeperiodebd";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_input = array();	
	$a_input[] = array('kolom' => 'tahun', 'label' => 'Kode', 'maxlength' => 4, 'size' => 4, 'notnull' => true, 'default' => date('Y'));
	$a_input[] = array('kolom' => 'semester', 'type' => 'S', 'option' => $p_model::aSemester());
	$a_input[] = array('kolom' => 'periodebd', 'label' => 'Nama Periode', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'tglawal', 'label' => 'Tgl. Mulai', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'tglakhir', 'label' => 'Tgl. Selesai', 'type' => 'D', 'notnull' => true);
	
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['kodeperiodebd'] = $record['tahun'].$record['semester'];
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){
			$r_key = $record['kodeperiodebd'];
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {				
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'bd_monev','kodeperiodebd');
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'savemonev' and $c_edit) {
		$r_pegawai = CStr::removeSpecial($_REQUEST['idpegawai']);
		list($p_posterr,$p_postmsg) = $p_model::saveMonev($conn,$r_key,$r_pegawai);
	}
	else if($r_act == 'delmonev' and $c_delete) {
		$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,'bd_monev','kodeperiodebd,idpegawaimonev');
	}
	
	$sql = $p_model::getDataPeriodeBKD($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key,$sql);
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
	
	//mendapatkan daftar data dosen monev
	$a_monev = $p_model::getDataMonev($conn,$r_key);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
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
						require_once('inc_databutton.php');
					
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
							<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'tahun') ?></td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'tahun') ?>&nbsp;
								<?= Page::getDataInput($row,'semester') ?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'periodebd') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'periodebd') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglawal') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tglawal') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglakhir') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tglakhir') ?></td>
						</tr>
						<?if(!empty($r_key)){?>
						<tr height="5px">
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr height="30" id="edit" style="display:none">
							<td colspan="2">
								<div class="filterTable" style="width:<?= $p_tbwidth-42 ?>px;">
								<table width="100%">
									<tr>
										<td><strong>Pegawai</strong></td>
										<td>
											: <?= UI::createTextBox('pegawai', '','ControlStyle',100,40,$c_edit); ?>
											<input type="hidden" name="idpegawai" id="idpegawai" value="" />
											<img id="imgnik_c" src="images/green.gif">
											<img id="imgnik_u" src="images/red.gif" style="display:none">&nbsp;&nbsp;
											<span id="edit" style="display:none">
												<input type="button" value="Simpan" id="be_savedet" class="ControlStyle" onClick="goSaveDet()">
											</span>
										</td>
									</tr>
								</table>
								</div>						
							</td>
						</tr>
						<tr>
							<td colspan="2">								
								<table cellpadding="4" cellspacing="0" class="GridStyle" width="100%">
									<tr class="DataBG">
										<td align="center">Dosen Monev</td>
										<td align="center" width="50px">Aksi</td>
									</tr>
									<?
									if(count($a_monev)>0){
										foreach($a_monev as $key){
									?>
									<tr>
										<td><?= $key['namalengkap']?></td>
										<td align="center" width="50px">
											<img id="<?= $key['kodeperiodebd'].'|'.$key['idpegawaimonev'] ?>" title="Hapus Data" src="images/delete.png" onClick="goDeleteDet(this)" style="cursor:pointer">
											<img id="<?= $key['kodeperiodebd'].'|'.$key['idpegawaimonev'] ?>" title="Halaman Dosen yang dinilai" src="images/link.png" onClick="goPop('popMenu',this,event)" style="cursor:pointer">
										</td>
									</tr>
									<?
										}
									}
									?>
								</table>
							</td>
						</tr>
						<?}?>
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<div id="popMenu" class="menubar" style="position:absolute; display:none; top:0px; left:0px;z-index:10000;" onMouseOver="javascript:overpopupmenu=true" onMouseOut="javascript:overpopupmenu=false">
<table width="130" class="menu-body">
    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('key','<?= Route::navAddress('list_bdpegawaidinilai') ?>')">Set Pegawai</td>
    </tr>
</table>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.balloon.min.js"></script>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	$("input[name='pegawai']").xautox({strpost: "f=acnamapegawai", targetid: "idpegawai", imgchkid: "imgnik", imgavail: true});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goSaveDet(){
	if($("#idpegawai").val() == ''){
		doHighlight(document.getElementById("pegawai"));
		alert("Silahkan masukkan nama atau NPP Pegawai");
	}else{
		document.getElementById("act").value = "savemonev";
		goSubmit();
	}	
}

function goDeleteDet(val){
	document.getElementById("act").value = "delmonev";
	document.getElementById("subkey").value = val.id;
	goSubmit();	
}
</script>
</body>
</html>
