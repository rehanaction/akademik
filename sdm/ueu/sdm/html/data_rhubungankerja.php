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
	require_once(Route::getModelPath('riwayat'));
	require_once(Route::getModelPath('mastkepegawaian'));
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
	$p_title = 'Data Hubungan Kerja';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mRiwayat;
	$p_dbtable = "pe_rwthubungankerja";
	$where = 'nourutrwthub';
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'nokontrak', 'label' => 'No. Dokumen', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'tglkontrak', 'label' => 'Tgl. Dokumen', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'idtipepeg', 'label' => 'Tipe Pegawai', 'type' => 'S', 'notnull' => true, 'add' => 'onchange="changeJenis(this.value)"','empty' => true, 'option' => mCombo::tipepegawai($conn));
	$a_input[] = array('kolom' => 'idjenispegawai', 'label' => 'Jenis Pegawai', 'type' => 'S', 'notnull' => true,'empty' => '-- Pilih Jenis Pegawai --', 'option' => mCombo::jenispegawai($conn));
	$a_input[] = array('kolom' => 'idhubkerja', 'label' => 'Hubungan Kerja', 'type' => 'S', 'notnull' => true,'empty' => true, 'option' => mCombo::hubungankerja($conn));
	$a_input[] = array('kolom' => 'idpangkat', 'label' => 'Nama Pangkat', 'type' => 'S','empty' => true, 'option' => $p_model::namaPangkat($conn));
	$a_input[] = array('kolom' => 'idjabatan', 'label' => 'Jenis Jabatan', 'type' => 'S', 'option' => mMastKepegawaian::aJabatan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit Kerja', 'type' => 'S', 'option' => mCombo::unitSave($conn,false), 'empty' => true);
	$a_input[] = array('kolom' => 'masakerjathn', 'label' => 'Masa Kerja', 'maxlength' => 2, 'size' => 2, 'type' => 'N');
	$a_input[] = array('kolom' => 'masakerjabln', 'maxlength' => 2, 'size' => 2, 'type' => 'N');
	$a_input[] = array('kolom' => 'tglefektif', 'label' => 'Tgl. Awal', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'tglberakhir', 'label' => 'Tgl. Akhir', 'type' => 'D');
	$a_input[] = array('kolom' => 'isacuanmk', 'label' => 'Acuan Masa Kerja', 'type' => 'C', 'option' => array('Y' => ''), 'add' => 'title="Centang sebagai acuan perhitungan masa kerja"');
	$a_input[] = array('kolom' => 'pejabatpenetap', 'label' => 'Pejabat Penetap', 'maxlength' => 50, 'size' => 40);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	$a_input[] = array('kolom' => 'filehubungankerja', 'label' => 'File Hubungan Kerja', 'type' => 'U', 'uptype' => 'filehubungankerja', 'size' => 40);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['idpegawai'] = $r_key;
		$record['isefektif'] = $record['tglefektif'] <= date('Y-m-d') ? 'Y' : 'null';
		$conn->BeginTrans();
		
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
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where,'','filehubungankerja');
		
		if(!$p_posterr) Route::navListpage($p_listpage,$r_key);
	}
	else if($r_act == 'deletefile' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_subkey,$p_dbtable,'filehubungankerja',$where);				
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
	
	$sql = $p_model::getDataEditHubunganKerja($r_subkey);
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$where,$sql);
	
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
							<td class="LeftColumnBG" style="white-space:nowrap" width="20%"><?= Page::getDataLabel($row,'nokontrak') ?></td>
							<td  class="RightColumnBG" width="40%"><?= Page::getDataInput($row,'nokontrak') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap" width="20%"><?= Page::getDataLabel($row,'tglkontrak') ?></td>
							<td  class="RightColumnBG" width="20%"><?= Page::getDataInput($row,'tglkontrak') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idtipepeg') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'idtipepeg') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idjenispegawai') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'idjenispegawai') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap" width="200px"><?= Page::getDataLabel($row,'idhubkerja') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'idhubkerja') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idjabatan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'idjabatan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idunit') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'idunit') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idpangkat') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'idpangkat') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'masakerjathn') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'masakerjathn') ?> tahun
								<?= Page::getDataInput($row,'masakerjabln') ?> bulan
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglefektif') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tglefektif') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglberakhir') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tglberakhir') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isacuanmk') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isacuanmk') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pejabatpenetap') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'pejabatpenetap') ?></td>
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
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'filehubungankerja') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'filehubungankerja') ?></td>
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
	changeJenis();
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

function changeJenis() {
	var posted = "f=optjenispegawai&q[]="+$("#idtipepeg").val()+"&q[]="+$("#idjenispegawai").val();
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		$("#idjenispegawai").html(text);
	});
}
</script>
</body>
</html>
