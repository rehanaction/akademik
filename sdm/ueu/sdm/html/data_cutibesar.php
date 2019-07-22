<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('cuti'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mCuti;
		
	$p_tbwidth = "700";
	$p_title = "Data Pemberitahuan Cuti Besar";
	$p_aktivitas = 'BIODATA';
	$p_listpage = Route::getListPage();
	$p_dbtable = "pe_cutibesar";
	$p_key = "kodeperiode";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_input = array();	
	$a_input[] = array('kolom' => 'kodeperiode', 'label' => 'Kode', 'maxlength' => 4, 'size' => 4, 'notnull' => true, 'default' => date('Y'), 'infoedit' => 'yyyy');
	$a_input[] = array('kolom' => 'namaperiode', 'label' => 'Nama Periode', 'maxlength' => 100, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'tglmulai', 'label' => 'Tgl. Mulai', 'type' => 'D');
	$a_input[] = array('kolom' => 'tglselesai', 'label' => 'Tgl. Selesai', 'type' => 'D');	
	
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key)){
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		}else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){
			$r_key = $record['kodeperiode'];
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'savedet' and $c_edit) {
		$record = array();
		$record['kodeperiode'] = $r_key;
		$record['idpegawai'] = CStr::cStrNull($_POST['idpegawai']);
		$r_subkey = $record['idpegawai'];
		
		$masakerja = $p_model::getMasaKerja($conn,$r_subkey);
		$record['masakerjathn'] = substr($masakerja,0,2);
		$record['masakerjabln'] = substr($masakerja,2,2);
		
		if(empty($r_subkey)){
			list($p_posterr,$p_postmsg) = $p_model::saveRecord($conn,$record,$r_key,true,'pe_cutibesardet');
		}else{
			$r_subkey = $r_key.'|'.$r_subkey;
			$where = "kodeperiode,idpegawai";
			list($p_posterr,$p_postmsg) = $p_model::saveRecord($conn,$record,$r_subkey,true,'pe_cutibesardet',$where);			
		}
		
		if(!$p_posterr){
			$r_key = $record['kodeperiode'];
			unset($post);
		}
	}
	else if($r_act == 'deletedet' and $c_delete) {
		print_r($_POST);
		$r_keydet = CStr::removeSpecial($_REQUEST['keydet']);
		$r_subkey = $r_key.'|'.$r_keydet;
		$where = "kodeperiode,idpegawai";
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,'pe_cutibesardet',$where);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
	
	if (!empty($r_key))
		$a_data = $p_model::getListDetail($conn, $r_key);
			
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
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
	<script type="text/javascript" src="scripts/foredit.js"></script>
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
							<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodeperiode') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'kodeperiode') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namaperiode') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'namaperiode') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglmulai') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tglmulai') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglselesai') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tglselesai') ?></td>
						</tr>
					</table>
					
					<? if (!empty($r_key)) {?>
					<br />
					<span id="show"></span>
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td align="center" style="color:#3870A8;font-size:14px"><b>Daftar Pegawai Cuti Besar</b></td>
						</tr>
					</table>
					<span id="edit" style="display:none">
					<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-35 ?>px;">
						<table width="<?= $p_tbwidth-22 ?>" cellpadding="2" cellspacing="2" align="center">
							<tr>
								<td><strong>Pegawai</strong></td>
									<td>: <?= UI::createTextBox('pegawai','','ControlStyle',60,45,$c_edit); ?>
									<input type="hidden" name="idpegawai" id="idpegawai" />
									<img id="imgnik_c" src="images/green.gif"><img id="imgnik_u" src="images/red.gif" style="display:none">&nbsp;&nbsp;
									<input type="button" name="badd" id ="badd" value="Tambah Detail" class="ControlStyle" onClick="goSaveDet()" />
								</td>
							</tr>
						</table>
					</div>
					</center>
					<br>
					</span>
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="2" cellspacing="2" align="center" class="GridStyle">
						<tr>
							<td class="DataBG" width="35" align="center">No</td>
							<td class="DataBG" align="center">Nama Pegawai</td>
							<td class="DataBG" width="120" align="center">Masa Kerja (Thn)</td>
							<td class="DataBG" width="120" align="center">Masa Kerja (Bln)</td>
							<td class="DataBG" width="35" align="center">Aksi</td>
						</tr>
						<? 	$i = 0;$detail=0;$no = 0;
							if (count($a_data) > 0 ){
								foreach($a_data as $col){
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;$detail++;
								$no++;
						?>
						<tr valign="top" class="<?= $rowstyle ?>"> 
							<td align="center"><?= $no ?></td>
							<td align="left"><?= $col['namalengkap'] ?></td>
							<td align="center"><?= $col['masakerjathn'] ?></td>
							<td align="center"><?= $col['masakerjabln'] ?></td>
							<td align="center">
								<img id="<?= $col['idpegawai']; ?>" style="cursor:pointer" onclick="goDeleteDet('<?= $col['idpegawai'] ?>')" src="images/delete.png" title="Hapus Data">
							</td>
						</tr>
						<? }}else{ ?>
						<tr>
							<td colspan="5" align="center">Data tidak ditemukan</td>
						</tr>
						<? } ?>
					</table>
					<? } ?>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="keydet" id="keydet">
				<?	} ?>
			</form>
		</div>
	</div>
</div>
<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">

var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	// handle contact
	$("input[name='pegawai']").xautox({strpost: "f=acnamapegawai", targetid: "idpegawai", imgchkid: "imgnik", imgavail: true});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goSaveDet(){
	document.getElementById("act").value = 'savedet';
	goSubmit();
}

function goDeleteDet(key){
	var hapus = confirm("Anda yakin untuk menghapus pegawai ini ?");
	if (hapus){
		document.getElementById("act").value = 'deletedet';
		document.getElementById("keydet").value = key;
		goSubmit();
	}
}

</script>
</body>
</html>
