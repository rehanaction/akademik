<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_paperiodebobot',true);
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pa'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
		
	$r_key = CStr::removeSpecial($_REQUEST['key']);
		
	if($c_insert or $c_edit)
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Periode Bobot';
	$p_tbwidth = 800;
	$p_aktivitas = 'NILAI';
	$p_listpage = Route::getListPage();
	
	$p_model = mPa;
	$p_dbtable = "pa_periodebobot";
	$where = 'kodeperiodebobot';
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodeperiodebobot', 'label' => 'Kode', 'maxlength' => 6, 'size' => 6, 'notnull' => 'true');
	$a_input[] = array('kolom' => 'namaperiode', 'label' => 'Nama Periode', 'maxlength' => 100, 'size' => 30);
	$a_input[] = array('kolom' => 'isaktif', 'label' => 'Aktif ?', 'type' => 'R', 'option' => SDM::getValid());
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
			
		$conn->BeginTrans();
		
		if(empty($r_key)){
			$r_action = "insert";
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$where);
		}else{
			$r_action = "update";
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$where);
		}
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
		
		if ($r_action == 'insert')
			Route::navigate('data_paperiodebobot&key='.$r_key);
		else{
		?>
			<html>
				<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
				<script type="text/javascript" src="scripts/jquery.common.js"></script>
				<script type="text/javascript" src="scripts/commonx.js"></script>
				<script type="text/javascript" src="scripts/foreditx.js"></script>
				<script type="text/javascript">
					var xlist = "<?= Route::navAddress(Route::thisPage()) ?>";
					var sent = "key=<?= $r_key ?>&msg=<?= $p_postmsg?>";
					window.parent.parent.$("#contents").divpost({page: xlist, sent: sent});
				</script>
			</html>
			<?php
			exit();	
		}		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$where);
	}
		
	$sql = $p_model::getDataEditPeriodeBobot($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$where,$sql);
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
		
	$p_postmsg = !empty($_REQUEST['msg']) ? $_REQUEST['msg'] : $p_postmsg;
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foreditx.js"></script>
</head>
<body>
	<table width="100%">
		<tr>
			<td>
			<form name="pageform" id="pageform" method="post" action="<?= Route::navAddress(Route::thisPage()) ?>" enctype="multipart/form-data">
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
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodeperiodebobot') ?></td>
							<td  class="RightColumnBG" width="40%"><?= Page::getDataInput($row,'kodeperiodebobot') ?> <em>Format : yyyymm</em></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namaperiode') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'namaperiode') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isaktif') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isaktif') ?></td>
						</tr>
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<?	} ?>
			</form>
		</td>
	</tr>
</table>

<iframe name="upload_iframe" style="display:none"></iframe>

<script type="text/javascript">
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var required = "<?= @implode(',',$a_required) ?>";
var xtdid = "contents";

$(document).ready(function() {	
	initEdit(<?= !empty($r_subkey) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goSave() {
	var pass = true;
	if(typeof(required) != "undefined") {
		if(!cfHighlight(required))
			pass = false;
	}
	
	if(pass) {
		<? if (!empty($r_key)) {?>
			document.getElementById("pageform").target = "upload_iframe";
		<? } ?>
		document.getElementById("act").value = "save";
		document.getElementById("pageform").submit();
	}
}

</script>
</body>
</html>
