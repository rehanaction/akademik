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
	require_once(Route::getModelPath('angkakredit'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
		
	// variabel request
	if(SDM::isPegawai()) {
		$r_self = 1;
		$c_kepeg = true;
	}
	
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
	$p_title = 'Data Bidang II (Penelitian)';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mAngkaKredit;
	$p_dbtable = "ak_bidang2";
	$where = 'nobidangii';
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'judulpenelitian', 'label' => 'Penelitian', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'notnull' => true, 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'idpenelitian', 'type' => 'H');
	$a_input[] = array('kolom' => 'outputpenelitian', 'label' => 'Output Penelitian', 'maxlength' => 100, 'size' => 70, 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'lokasi', 'label' => 'Lokasi', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'tgl', 'label' => 'Tgl. Penelitian','type' => 'D', 'maxlength' => 10, 'size' => 10, 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'kegiatan', 'label' => 'Indeks Angka Kredit', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255, 'notnull' => true, 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'idkegiatan', 'type' => 'H');
	$a_input[] = array('kolom' => 'stdkredit', 'label' => 'Kredit Max', 'maxlength' => 5, 'size' => 5, 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'nilaikredit', 'label' => 'Kredit Dihitung', 'readonly' => true);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	
	if($c_valid)
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	else
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid(), 'readonly' => true);	
		
	$a_input[] = array('kolom' => 'statusvalidasiak', 'label' => 'Status Validasi', 'readonly' => true);
	$a_input[] = array('kolom' => 'tglvalidasi', 'label' => 'Tgl. Validasi','type' => 'D', 'readonly' => true);
	$a_input[] = array('kolom' => 'isfinal', 'label' => 'Final', 'type' => 'R', 'readonly' => true, 'option' => $p_model::isFinal());
	$a_input[] = array('kolom' => 'filebidangdua', 'label' => 'File Bidang II', 'type' => 'U', 'uptype' => 'filebidangdua', 'size' => 40);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['idpegawai'] = $r_key;
		$conn->BeginTrans();
		
		//perhitungan nilai kredit bila peneltian oleh tim
		$nilaikredit = $p_model::getAKPenelitian($conn,$record['idpenelitian'],$record['idkegiatan']);
		$record['stdkredit'] = !empty($nilaikredit) ? $nilaikredit : $record['nilaikredit'];
		$record['nilaikredit'] = $record['stdkredit'];
		
		if(empty($r_subkey))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
			
		if($ok) 
			unset($post);
		else
			Route::setFlashDataPost($post);
		?>
		
		<html>
			<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
			<script type="text/javascript" src="scripts/jquery.common.js"></script>
			<script type="text/javascript" src="scripts/commonx.js"></script>
			<script type="text/javascript" src="scripts/foreditx.js"></script>
			<script type="text/javascript">
				var xlist = "<?= Route::navAddress(Route::thisPage()) ?>";
				var sent = "key=<?= $r_key ?>&subkey=<?= $r_subkey ?>&err=<?= $p_posterr?>&msg=<?= $p_postmsg?>";
				window.parent.parent.$("#contents").divpost({page: xlist, sent: sent});
			</script>
		</html>
		<?php
		exit();
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where,'','filebidangdua');
		
		if(!$p_posterr) Route::navListpage($p_listpage,$r_key);
	}
	else if($r_act == 'deletefile' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_subkey,$p_dbtable,'filebidangdua',$where);				
		?>
		
		<html>
			<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
			<script type="text/javascript" src="scripts/jquery.common.js"></script>
			<script type="text/javascript" src="scripts/commonx.js"></script>
			<script type="text/javascript" src="scripts/foreditx.js"></script>
			<script type="text/javascript">
				var xlist = "<?= Route::navAddress(Route::thisPage()) ?>";
				var sent = "key=<?= $r_key ?>&subkey=<?= $r_subkey ?>&err=<?= $p_posterr?>&msg=<?= $p_postmsg?>";
				window.parent.parent.$("#contents").divpost({page: xlist, sent: sent});
			</script>
		</html>
		<?php
		exit();	
	}
		
	$p_postmsg = !empty($_REQUEST['msg']) ? $_REQUEST['msg'] : $p_postmsg;
	$p_posterr = !empty($_REQUEST['err']) ? $_REQUEST['err'] : $p_posterr;
	if($p_posterr)
		$post = Route::getFlashDataPost();
	
	$sql = $p_model::getDataEditBidang2($r_subkey);	
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$where,$sql);
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
			
		//pengecekan hak akses utk pegawai ybs, bila sudah valid
		if($t_row['id'] == 'isvalid'){
			$isvalid = $t_row['value'];
			if($isvalid == 'Ya' and $r_self){
				$c_edit = false;
				$c_delete = false;
			}
		}
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
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'judulpenelitian') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'judulpenelitian') ?>
								<?= Page::getDataInput($row,'idpenelitian') ?>
								<span id="edit" style="display:none;"><img src="images/magnify.png" title="Pilih dari daftar penelitian" style="cursor:pointer" onclick="showPenelitian()"></span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'outputpenelitian') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'outputpenelitian') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'lokasi') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'lokasi') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tgl') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'tgl') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kegiatan') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'kegiatan') ?>
								<?= Page::getDataInput($row,'idkegiatan') ?>
								<span id="edit" style="display:none;"><img src="images/magnify.png" title="Pilih indeks kegiatan" style="cursor:pointer" onclick="showIndeks()"></span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'stdkredit') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'stdkredit') ?>
								<?
									if(!empty($r_subkey)){
										echo '<b>&nbsp;&nbsp;&nbsp;';
										echo Page::getDataLabel($row,'nilaikredit').' : '.Page::getDataInput($row,'nilaikredit').'</b>';
									}
								?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'keterangan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'keterangan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isvalid') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isvalid') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'statusvalidasiak') ?></td>
							<td  class="RightColumnBG" width="40%"><?= Page::getDataInput($row,'statusvalidasiak') ?></td>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglvalidasi') ?></td>
							<td  class="RightColumnBG" width="20%"><?= Page::getDataInput($row,'tglvalidasi') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isfinal') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isfinal') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'filebidangdua') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'filebidangdua') ?></td>
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

<iframe name="upload_iframe" style="display:none"></iframe>

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

function showPenelitian(){
	win = window.open("<?= Route::navAddress('pop_penelitian').'&key='.$r_key?>","popup_penelitian","width=950,height=600,scrollbars=1");
	win.focus();
}

function showIndeks(){
	win = window.open("<?= Route::navAddress('pop_penilaian').'&m=2&b=II'?>","popup_penilaian","width=650,height=500,scrollbars=1");
	win.focus();
}
</script>
</body>
</html>
