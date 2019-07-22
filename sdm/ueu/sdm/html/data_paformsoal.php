<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pa'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mPa;
		
	$p_tbwidth = "800";
	$p_title = "Data Form Penilaian Subyektif";
	$p_aktivitas = 'NILAI';
	$p_listpage = Route::getListPage();
	$p_dbtable = "pa_formsubyektif";
	$p_key = "kodeformsubyektif";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_input = array();	
	$a_input[] = array('kolom' => 'kodeperiodebobot', 'label' => 'Periode Bobot', 'type' => 'S', 'option' => $p_model::getCPeriodeBobot($conn), 'notnull' => true);
	$a_input[] = array('kolom' => 'namaform', 'label' => 'Nama Form', 'maxlength' => 100, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepajenis', 'label' => 'Jenis Penilai', 'type' => 'S', 'option' => $p_model::getCJenisPenilai($conn), 'notnull' => true);
	$a_input[] = array('kolom' => 'bobotbawah', 'label' => 'Bobot', 'maxlength' => 3, 'size' => 3, 'type' =>'N,0','notnull' => true);
	$a_input[] = array('kolom' => 'bobotatas', 'label' => 'Bobot Atas', 'maxlength' => 3, 'size' => 3, 'type' =>'N,0','notnull' => true);
	$a_input[] = array('kolom' => 'kodeformsubyektif', 'type' => 'H');
	
	
	$r_act = $_POST['act'];
	$r_actdet = CStr::removeSpecial($_POST['actdet']);
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key)){
			$record['kodeformsubyektif'] = substr($record['kodeperiodebobot'],2,5).$record['kodepajenis'];
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key,true);
		}else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){
			$r_key = $record['kodeformsubyektif'];
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_actdet == 'savedet' and $c_edit) {
		$record = array();
		$record['kodeformsubyektif'] = $r_key;
		$record['nouraian'] = $p_model::getKodeFormDet($conn,$r_key);
		$record['uraian'] = CStr::cStrNull($_POST['uraian']);
		$record['isdinilai'] = CStr::cStrNull($_POST['isdinilai']);
		$record['nomor'] = CStr::cStrNull($_POST['nomor']);
		$record['parentnouraian'] = CStr::cStrNull($_POST['parent']);
		
		if(empty($r_subkey)){
			list($p_posterr,$p_postmsg) = $p_model::insertRecordSoalPA($conn,$record,true);
		}else{
			$r_subkey = $r_key.'|'.$r_subkey;
			$where = "kodeformsubyektif,nouraian";
			list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_subkey,true,'pa_formsubyektifdet',$where);			
		}
		
		if(!$p_posterr){
			$r_key = $record['kodeformsubyektif'];
			unset($post);
		}
	}
	else if($r_act == 'deletedet' and $c_delete) {
		$r_keydet = CStr::removeSpecial($_POST['keydet']);
		$r_subkey = $r_key.'|'.$r_keydet;
		$where = "kodeformsubyektif,nouraian";
		
		$conn->BeginTrans();
		$infoleft = $p_model::getInfoLeft($conn,$r_subkey);
		$err = $p_model::deleteLeaf($conn,$r_key,$infoleft);
		if(!$err)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,'pa_formsubyektifdet',$where);
		
		$ok = Query::isOK($err);
		$conn->CommitTrans($ok);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
	
	if (!empty($r_key))
		$a_data = $p_model::getListFormDetail($conn, $r_key);
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
	
	$a_required = array('kodepajenis','kodeperiodebobot','bobotbawah');
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
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
							<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodeperiodebobot') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'kodeperiodebobot') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namaform') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'namaform') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodepajenis') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'kodepajenis') ?><?= Page::getDataInput($row,'kodeformsubyektif') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'bobotbawah') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'bobotbawah') ?> s/d <?= Page::getDataInput($row,'bobotatas') ?></td>
						</tr>
					</table>
					
					<? if (!empty($r_key)) {?>
					<br />
					<span id="show"></span>
					<span id="edit" style="display:none">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr><td><input type="button" name="badd" id ="badd" value="Tambah Detail" class="ControlStyle" onClick="openDetail('<?= $r_key ?>','')" /></td></tr>
					</table>
					</span>
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
						<tr>
							<td class="DataBG" width="50" align="center">Nomor</td>
							<td class="DataBG" align="center">Ukuran Penilaian</td>
							<td class="DataBG" width="80" align="center">Aksi</td>
						</tr>
						<? if (count($a_data) > 0 ){
								foreach($a_data as $col){
						?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap;padding-left:<?= $col['level'] * 20; ?>px"><?= $col['nomor'] ?></td>
							<td class="RightColumnBG" style="padding-left:<?= $col['level'] * 20; ?>px"><?= $col['uraian'] ?></td>
							<td class="RightColumnBG" align="right">
								<span id="show"></span>
								<span id="edit" style="display:none">
									<? if (empty($col['parentnouraian'])) {?>
									<img id="<?= $col['nouraian']; ?>" style="cursor:pointer" onclick="addDetail('<?= $r_key ?>','<?= $col['nouraian'] ?>')" src="images/child.png" title="Tambah Sub Ukuran Penilaian">
									<? } ?>
									<img id="<?= $col['nouraian']; ?>" style="cursor:pointer" onclick="openDetail('<?= $r_key ?>','<?= $col['nouraian'] ?>')" src="images/edit.png" title="Tampilkan Detail">
									<img id="<?= $col['nouraian']; ?>" style="cursor:pointer" onclick="goDeleteDet('<?= $col['nouraian'] ?>')" src="images/delete.png" title="Hapus Data">
								</span>
							</td>
						</tr>
						<? }}else{ ?>
						<tr>
							<td colspan="2" align="center">Data tidak ditemukan</td>
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
var detform = "<?= Route::navAddress('pop_paform') ?>";

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
