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
	$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mGaji;
		
	$p_tbwidth = "800";
	$p_title = "Data Pajak";
	$p_aktivitas = 'ANGGARAN';
	$p_listpage = Route::getListPage();
	$p_dbtable = "ms_pajak";
	$p_key = "idpajak";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_input = array();	
	$a_input[] = array('kolom' => 'idpajak', 'label' => 'Kode', 'maxlength' => 3, 'size' => 3, 'notnull' => true);
	$a_input[] = array('kolom' => 'prosentasepotongan', 'label' => 'Prosentase Potongan', 'maxlength' => 3, 'size' => 3, 'type' => 'N', 'notnull' => true);
	$a_input[] = array('kolom' => 'maxpotongan', 'label' => 'Maksimal Potongan', 'maxlength' => 12, 'size' => 15, 'type' => 'N');
	$a_input[] = array('kolom' => 'ptkppribadi', 'label' => 'PTKP Pribadi', 'maxlength' => 12, 'size' => 15, 'type' => 'N');
	$a_input[] = array('kolom' => 'ptkpkawin', 'label' => 'PTKP Menikah', 'maxlength' => 12, 'size' => 15, 'type' => 'N');
	$a_input[] = array('kolom' => 'ptkpanak', 'label' => 'PTKP Anak', 'maxlength' => 12, 'size' => 15, 'type' => 'N');
	$a_input[] = array('kolom' => 'maxanak', 'label' => 'Maksimal Anak', 'maxlength' => 2, 'size' => 2, 'type' => 'N');
	$a_input[] = array('kolom' => 'skpajak', 'label' => 'SK Pajak', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'isaktif', 'label' => 'Aktif', 'type' => 'C', 'option' => array("Y" => "Aktif"));
	
	
	$r_act = $_POST['act'];
	$r_actdet = CStr::removeSpecial($_POST['actdet']);
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key)){
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		}else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){
			$r_key = $record['idpajak'];
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'ms_pajakdet',$p_key);
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
		
		if($ok) Route::navigate($p_listpage);
	}
	else if($r_actdet == 'savedet' and $c_edit) {
		$record = array();
		$record['idpajak'] = $r_key;
		$record['batasbawah'] = CStr::cStrNull($_POST['batasbawah']);
		$record['batasatas'] = CStr::cStrNull($_POST['batasatas']);
		$record['prosentase'] = CStr::cStrNull($_POST['prosentase']);
		$record['nonnpwp'] = CStr::cStrNull($_POST['nonnpwp']);
		
		if(empty($r_subkey)){
			list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,'ms_pajakdet');
		}else{
			$r_subkey = $r_key.'|'.$r_subkey;
			$where = "idpajak,idpph";
			list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_subkey,true,'ms_pajakdet',$where);
		}
		
		if(!$p_posterr){
			$r_key = $record['idpajak'];
		}
	}
	else if($r_act == 'deletedet' and $c_delete) {
		$r_keydet = CStr::removeSpecial($_POST['keydet']);
		$r_subkey = $r_key.'|'.$r_keydet;
		$where = "idpajak,idpph";
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,'ms_pajakdet',$where);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
	
	if (!empty($r_key))
		$a_data = $p_model::getListPajakDet($conn,$r_key);
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
		
	//not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<style>
		#tbl_det tr:nth-child(2n+2) {background: #F4F4F4}
		#tbl_det tr:nth-child(2n+3) {background: #FFFFFF}
	</style>
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
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idpajak') ?></td>
							<td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'idpajak') ?></td>
						</tr>
						<tr>
							<td width="20%" class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'prosentasepotongan') ?></td>
							<td width="40%" class="RightColumnBG"><?= Page::getDataInput($row,'prosentasepotongan') ?> %</td>
							<td width="20%" class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'maxpotongan') ?></td>
							<td width="40%" class="RightColumnBG"><?= Page::getDataInput($row,'maxpotongan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'ptkppribadi') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'ptkppribadi') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'ptkpkawin') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'ptkpkawin') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'ptkpanak') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'ptkpanak') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'maxanak') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'maxanak') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'skpajak') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'skpajak') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isaktif') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'isaktif') ?></td>
						</tr>
					</table>
					
					<? if (!empty($r_key)) {?>
					<br />
										
					<span id="edit" style="display:none">
						<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
							<tr>
								<td>
									<input type="button" name="badd" id ="badd" value="Tambah Detail" class="ControlStyle" onClick="openDetail('<?= $r_key ?>','')" />
								</td>
							</tr>
						</table>
					</span>
					
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td align="center" style="color:#3870A8;font-size:14px"><b>Prosentase Perhitungan PPH</b></td>
						</tr>
					</table>
					
					<table id="tbl_det" width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
						<tr>
							<td class="DataBG" align="center">Batas Bawah</td>
							<td class="DataBG" align="center">Batas Atas</td>
							<td class="DataBG" align="center">NPWP (%)</td>
							<td class="DataBG" align="center">Non NPWP (%)</td>
							<td class="DataBG" width="100" align="center">Aksi</td>
						</tr>
						<? 
							$d=0;
							while($col = $a_data->FetchRow()){$d++;
						?>
						<tr>
							<td align="center"><?= CStr::formatNumber($col['batasbawah']) ?></td>
							<td align="center"><?= CStr::formatNumber($col['batasatas']) ?></td>
							<td align="center"><?= !empty($col['prosentase']) ? CStr::formatNumber($col['prosentase']).' %' : '' ?></td>
							<td align="center"><?= !empty($col['nonnpwp']) ? CStr::formatNumber($col['nonnpwp']).' %' : '' ?></td>
							<td align="center">
								<span id="show"></span>
								<span id="edit" style="display:none">
									<img id="<?= $col['idpph']; ?>" style="cursor:pointer" onclick="openDetail('<?= $r_key ?>','<?= $col['idpph'] ?>')" src="images/edit.png" title="Tampilkan Detail">
									<img id="<?= $col['idpph']; ?>" style="cursor:pointer" onclick="goDeleteDet('<?= $col['idpph'] ?>')" src="images/delete.png" title="Hapus Data">
								</span>
							</td>
						</tr>
						<? }if($d==0){ ?>
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
<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var detform = "<?= Route::navAddress('pop_gapajakdet') ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function openDetail(pkey, pkeydet){
    $.ajax({
        url: detform,
        type: "POST",
        data: {key : pkey, subkey : pkeydet},
        success: function(data){
            $.facybox(data);
        }
    });
}

function addDetail(pkey, pkeydet){
    $.ajax({
        url: detform,
        type: "POST",
        data: {key : pkey, parent : pkeydet},
        success: function(data){
            $.facybox(data);
        }
    });
}

function goDeleteDet(key){
	var hapus = confirm("Anda yakin untuk menghapus ukuran detail penilaian ini ?");
	if (hapus){
		document.getElementById("act").value = 'deletedet';
		document.getElementById("keydet").value = key;
		goSubmit();
	}
}

</script>
</body>
</html>
