<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_other = $a_auth['canother'];
	$c_kepeg = $c_other['K'];
	$c_valid = $c_other['V'];
	
	// include
	require_once(Route::getModelPath('pekerjaan'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
		
	// variabel request
	if(SDM::isPegawai())
		$r_self = 1;
	
	if($c_kepeg){
		$c_insert = false;
		$c_edit = $a_auth['canupdate'];
		$c_delete = $a_auth['candelete'];
	}
		
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
	
	// properti halaman
	$p_title = 'Data Pemberhentian Pegawai';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mPekerjaan;
	$p_dbtable = "pe_pensiun";
	$where = 'idpegawai';
	
	$r_subkey = $p_model::cekPensiun($conn,$r_key);
	
	//mendapatkan data pensiun normal
	$pensiun = $p_model::getPensiun($conn,$r_key);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idpegawai', 'type' => 'H');
	$a_input[] = array('kolom' => 'bulan', 'label' => 'Periode Pemberhentian', 'empty' => true, 'type' => 'S', 'option' => Date::arrayMonth());
	$a_input[] = array('kolom' => 'tahun', 'maxlength' => 4, 'size' => 4);
	$a_input[] = array('kolom' => 'tmtpensiun', 'label' => 'Tgl. Pemberhentian', 'type' => 'D', 'notnull' => true, 'default' => $pensiun['tglpensiun'], 'add' => 'onchange="getMK(this.value)"');
	$a_input[] = array('kolom' => 'masakerjathn', 'label' => 'Masa Kerja', 'maxlength' => 2, 'size' => 2, 'class' => 'ControlRead', 'default' => $pensiun['masakerjathn']);
	$a_input[] = array('kolom' => 'masakerjabln', 'maxlength' => 2, 'size' => 2, 'class' => 'ControlRead', 'default' => $pensiun['masakerjabln']);	
	$a_input[] = array('kolom' => 'idstatusaktif', 'label' => 'Jenis Pemberhentian', 'type' => 'S', 'option' => $p_model::jenisPensiun($conn),'empty' => true,'notnull' => true);
	$a_input[] = array('kolom' => 'skpensiun', 'label' => 'SK Pemberhentian', 'maxlength' => 50, 'size' => 30);
	//$a_input[] = array('kolom' => 'tglbkn', 'label' => 'Tgl. BKN', 'type' => 'D');
	//$a_input[] = array('kolom' => 'nobkn', 'label' => 'No. BKN', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	$a_input[] = array('kolom' => 'filepensiun', 'label' => 'File Pemberhentian', 'type' => 'U', 'uptype' => 'filepensiun', 'size' => 40);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['idpegawai'] = $r_key;
		
		if($record['bulan'] != 'null' and $record['tahun'] != 'null')
			$record['periodepensiun'] = $record['tahun'].str_pad($record['bulan'],2,'0', STR_PAD_LEFT);
		else
			$record['periodepensiun'] = substr($record['tmtpensiun'],0,4).substr($record['tmtpensiun'],5,2);
			
		$conn->BeginTrans();
		
		if(empty($r_subkey))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
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
				
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where);
		if(!$p_posterr){
			$r_subkey = $p_model::cekPensiun($conn,$r_key);
			
			$a_input[] = array('kolom' => 'tmtpensiun', 'label' => 'Tgl. Pensiun', 'type' => 'D', 'notnull' => true, 'default' => $pensiun['tglpensiun'], 'add' => 'onchange="getMK(this.value)"');
			$a_input[] = array('kolom' => 'masakerjathn', 'label' => 'Masa Kerja', 'maxlength' => 2, 'size' => 2, 'class' => 'ControlRead', 'default' => $pensiun['masakerjathn']);
			$a_input[] = array('kolom' => 'masakerjabln', 'maxlength' => 2, 'size' => 2, 'class' => 'ControlRead', 'default' => $pensiun['masakerjabln']);
		}
	}
	else if($r_act == 'deletefile' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_subkey,$p_dbtable,'filepensiun',$where);
				
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
	
	$sql = $p_model::getDataEditPensiun($r_subkey);
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$where,$sql);
	
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
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'idstatusaktif') ?></td>
							<td  class="RightColumnBG" width="40%"><?= Page::getDataInput($row,'idstatusaktif') ?></td>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'bulan') ?></td>
							<td  class="RightColumnBG" width="20%">
								<?= Page::getDataInput($row,'bulan') ?>
								<?= Page::getDataInput($row,'tahun') ?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tmtpensiun') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tmtpensiun') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'skpensiun') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'skpensiun') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'masakerjathn') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'masakerjathn') ?>&nbsp;tahun
								<?= Page::getDataInput($row,'masakerjabln') ?>&nbsp;bulan
							</td>
						</tr>
						<?/*
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglbkn') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tglbkn') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nobkn') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'nobkn') ?></td>
						</tr>
						*/?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isvalid') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isvalid') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'filepensiun') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'filepensiun') ?></td>
						</tr>
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey" value="<?= $r_key ?>">
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
	initEdit(<?= (!empty($r_subkey) or $r_self) ? false : true ?>);
	
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
		document.getElementById("pageform").target = "upload_iframe";
		document.getElementById("act").value = "save";
		document.getElementById("pageform").submit();
	}
}

function getMK(tgl){
	var posted = "f=gmkpensiun&q[]="+$("#key").val()+"&q[]="+tgl;
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		var text = text.split(':');
		
		$("#masakerjathn").val(text[0]);
		$("#masakerjabln").val(text[1]);
	});
}

</script>
</body>
</html>
