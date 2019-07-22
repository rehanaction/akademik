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
	require_once(Route::getModelPath('integrasi'));
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
	$p_title = 'Data Riwayat Mutasi';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mRiwayat;
	$p_dbtable = "pe_rwtmutasi";
	$where = 'nourutmutasi';
	
	//cek dari unit dan jenispegawai sekarang
	if(empty($r_subkey)){
		$pegnow = $p_model::getPegawaiNow($conn,$r_key);
	}
	
	if(!empty($r_subkey))
		$c_validmutasi = $p_model::cekValidMutasi($conn,$r_subkey);
	
	//struktur view
	$a_input = array();
	if(empty($r_subkey))
		$a_input[] = array('kolom' => 'unitasal', 'label' => 'Unit Asal', 'type' => 'S', 'notnull' => true, 'empty' => true, 'option' => mCombo::unitSave($conn,false),'default' => $pegnow['idunit']);
	else
		$a_input[] = array('kolom' => 'unitasal', 'label' => 'Unit Asal', 'type' => 'S', 'notnull' => true, 'empty' => true, 'option' => mCombo::unitSave($conn,false));
	
	$a_input[] = array('kolom' => 'unittujuan', 'label' => 'Unit Tujuan', 'type' => 'S', 'option' => mCombo::unitSave($conn,false));
	
	if(empty($r_subkey))
		$a_input[] = array('kolom' => 'jenispegasal', 'label' => 'Jenis Pegawai Asal', 'type' => 'S', 'option' => mCombo::jenispegawai($conn), 'empty' => true, 'notnull' => true,'default' => $pegnow['idjenispegawai']);
	else
		$a_input[] = array('kolom' => 'jenispegasal', 'label' => 'Jenis Pegawai Asal', 'type' => 'S', 'option' => mCombo::jenispegawai($conn), 'empty' => true, 'notnull' => true);
	
	$a_input[] = array('kolom' => 'jenispegtujuan', 'label' => 'Jenis Pegawai Tujuan', 'type' => 'S', 'option' => mCombo::jenispegawai($conn));
	$a_input[] = array('kolom' => 'tmttugas', 'label' => 'Tgl. Mutasi', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'jenismutasi', 'label' => 'Jenis Mutasi', 'type' => 'S', 'option' => $p_model::jenisMutasi());
	$a_input[] = array('kolom' => 'nosk', 'label' => 'No. SK', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'tglsk', 'label' => 'Tgl. SK', 'type' => 'D');
	$a_input[] = array('kolom' => 'nosurattugas', 'label' => 'No. Surat Tugas', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'tglsurattugas', 'label' => 'Tgl. Surat Tugas', 'type' => 'D');
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	$a_input[] = array('kolom' => 'filemutasi', 'label' => 'File Mutasi', 'type' => 'U', 'uptype' => 'filemutasi', 'size' => 40);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['idpegawai'] = $r_key;
		$record['isefektif'] = $record['tmttugas'] <= date('Y-m-d') ? 'Y' : 'null';
		$conn->BeginTrans();
		
		if(empty($r_subkey))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
		
		if(!$p_posterr){
			$p_posterr = mIntegrasi::saveUnitRole($conn,$r_key);
			if($p_posterr)
				$p_postmsg = 'Penyimpanan Unit Role ke Gate gagal';
		}
			
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
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where,'','filemutasi');
		
		if(!$p_posterr){
			$p_posterr = mIntegrasi::saveUnitRole($conn,$r_key);
			if($p_posterr)
				$p_postmsg = 'Penghapusan Unit Role ke Gate gagal';
		}
			
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
		
		if(!$p_posterr) 
			Route::navListpage($p_listpage,$r_key);
	}
	else if($r_act == 'deletefile' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_subkey,$p_dbtable,'filemutasi',$where);				
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
	
	$sql = $p_model::getDataEditMutasi($r_subkey);
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
					
					if(empty($p_fatalerr)){?>
						<table border="0" cellspacing="10" align="center">
							<tr>
								<?	if($c_readlist) { ?>
								<td id="be_list" class="TDButton" onclick="goList()">
									<img src="images/list.png"> Daftar
								</td>
								<?	} if($c_insert) { ?>
								<td id="be_add" class="TDButton" onclick="goNew('<?= $r_key; ?>')">
									<img src="images/add.png"> Data Baru
								</td>
								<?	} if($c_edit) { ?>
							   <td id="be_edit" class="TDButton" onclick="goEdit()">
									<img src="images/edit.png"> Sunting
								</td>
								<td id="be_save" class="TDButton" onclick="goSave()" style="display:none">
									<img src="images/disk.png"> Simpan
								</td>
								<td id="be_undo" class="TDButton" onclick="goUndo()" style="display:none">
									<img src="images/undo.png"> Batal
								</td>
								<?	} if($c_delete and !empty($r_subkey)) { ?>
								<td id="be_delete" class="TDButton" onclick="goDelete()">
									<img src="images/delete.png"> Hapus
								</td>
								<?	} if($c_edit and $c_validmutasi) {?>
								 <td id="be_print" class="TDButton" onclick="goPrint()">
									<img src="images/small-print.png"> Cetak
								</td>
								<? } ?>
							</tr>
						</table>
					<?}	
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
							<td class="LeftColumnBG" style="white-space:nowrap" width="20%"><?= Page::getDataLabel($row,'tmttugas') ?></td>
							<td  class="RightColumnBG" width="40%"><?= Page::getDataInput($row,'tmttugas') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap" width="20%"><?= Page::getDataLabel($row,'jenismutasi') ?></td>
							<td  class="RightColumnBG" width="20%"><?= Page::getDataInput($row,'jenismutasi') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'unitasal') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'unitasal') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'unittujuan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'unittujuan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'jenispegasal') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'jenispegasal') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'jenispegtujuan') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'jenispegtujuan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nosk') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'nosk') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglsk') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tglsk') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nosurattugas') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'nosurattugas') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglsurattugas') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tglsurattugas') ?></td>
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
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'filemutasi') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'filemutasi') ?></td>
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


function goEdit() {
	$("[id='show']").hide();
	$("[id='edit']").show();
	
	$("#be_add,#be_edit,#be_print").hide();
	$("#be_save,#be_undo").show();
}


function goPrint(){
	window.open("<?= Route::navAddress('rep_mutasi') ?>&key=<?= $r_subkey?>&format=html","_blank");
}
</script>
</body>
</html>