<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('gaji'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mGaji;
		
	$p_tbwidth = "650";
	$p_title = "Data Tarif Lembur";
	$p_aktivitas = 'ANGGARAN';
	$p_listpage = Route::getListPage();
	$p_dbtable = "ga_tariflembur";
	$p_key = "kodetariflembur,idpendidikan,periodetarif";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_input = array();
	$a_input[] = array('kolom' => 'kodetariflembur', 'label' => 'Kode', 'maxlength' => 3, 'size' => 3, 'notnull' => true);
	$a_input[] = array('kolom' => 'periodetarif', 'label' => 'Periode Tarif', 'type' => 'S', 'option' => $p_model::getCPeriodeTarif($conn));
	$a_input[] = array('kolom' => 'idpendidikan', 'label' => 'Pendidikan', 'type' => 'S', 'option' => $p_model::getCPendidikan($conn));
	$a_input[] = array('kolom' => 'idjenishari', 'label' => 'Jenis Hari', 'type' => 'S', 'option' => $p_model::getJenisHari($conn));
	$a_input[] = array('kolom' => 'isharilibur', 'label' => 'is Hari Libur?', 'type' => 'S', 'option' => $p_model::isLibur($conn), 'empty' => true, 'add'=>'onchange="getDaftarLibur()"');
	$a_input[] = array('kolom' => 'tariflembur', 'label' => 'Tarif Lembur', 'notnull' => true,'maxlength' => 12, 'size' => 15, 'type' => 'N');
	
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){
			list($p_posterr,$p_postmsg) = $p_model::saveTarifLemburDet($conn,$_POST,$r_key);
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'ms_tariflemburdetail',$p_key);
		
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
	$a_data = $p_model::getlistLibur($conn);
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
	
	//pengecekan libur
	if(!empty($r_key))
		$a_cekstatuslibur = $p_model::cekstatuslibur($conn,$r_key);
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
							<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodetariflembur') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'kodetariflembur') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'periodetarif') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'periodetarif') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idpendidikan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'idpendidikan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idjenishari') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'idjenishari') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isharilibur') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'isharilibur') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tariflembur') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tariflembur') ?></td>
						</tr>
					</table>
					<br>
					<table id="tb_Judul" style="display:none;" width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td align="center" style="color:#3870A8;font-size:14px"><b>Daftar Hari Libur</b></td>
						</tr>
					</table>
					<table id="tb_Detail" style="display:none;" width="<?= $p_tbwidth-22 ?>" cellpadding="2" cellspacing="2" align="center" class="GridStyle">
						<tr>
							<td class="DataBG" width="35" align="center">No</td>
							<td class="DataBG" align="center">Nama Hari Libur</td>
							<td class="DataBG" width="120" align="center">Tgl. Mulai</td>
							<td class="DataBG" width="120" align="center">Tgl. Selesai</td>
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
							<td align="left"><?= $col['namaliburan'] ?></td>
							<td align="center"><?= CStr::formatDateInd($col['tglmulai']) ?></td>
							<td align="center"><?= CStr::formatDateInd($col['tglselesai']) ?></td>
							<td align="center">
								<span id="show">
									<?= in_array($col['idliburan'],$a_cekstatuslibur)  ? '<img src="images/check.png">' : '';?>
								</span>
								<span id="edit" style="display:none">
									<input type="checkbox" id="ceklist_<?= $col['idliburan']?>" name="ceklist_<?= $col['idliburan']?>" <?= in_array($col['idliburan'],$a_cekstatuslibur) ? 'checked' : '';?>>									
									<input type="hidden" id="ceklist" name="ceklist[]" value="<?= $col['idliburan']?>">
								</span>
							</td>
						</tr>
						<? }}}else{ ?>
						<tr>
							<td colspan="5" align="center">Data tidak ditemukan</td>
						</tr>
						<? } ?>
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	getDaftarLibur();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function getDaftarLibur(){
	
	if($("#isharilibur").val() == 'Y'){
		$("#tb_Judul").show();
		$("#tb_Detail").show();
	
	}else {
		$("#tb_Judul").hide();
		$("#tb_Detail").hide();
	}
}

</script>
</body>
</html>
