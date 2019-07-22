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
	require_once(Route::getModelPath('pegawai'));
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
	$p_title = 'Data Istri / Suami';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mRiwayat;
	$p_dbtable = "pe_istrisuami";
	$p_key = "nourutist";
	$dirfoto = 'fotopasangan';
	$p_foto = uForm::getPathImageFoto($conn,$r_subkey,$dirfoto);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'namapasangan', 'label' => 'Nama', 'maxlength' => 100, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'jeniskelamin', 'label' => 'Jenis Kelamin', 'type' => 'S', 'option' => mPegawai::jenisKelamin(), 'empty' => true, 'notnull' => true);
	$a_input[] = array('kolom' => 'tmplahir', 'label' => 'Tempat Lahir', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'tgllahir', 'label' => 'Tgl Lahir', 'type' => 'D');
	$a_input[] = array('kolom' => 'ispasangankerja', 'label' => 'Bekerja ?', 'type' => 'R', 'option' => SDM::getValid());
	$a_input[] = array('kolom' => 'pekerjaan', 'label' => 'Pekerjaan', 'maxlength' => 50, 'size' => 50);
	$a_input[] = array('kolom' => 'nippasangan', 'label' => 'NIP', 'maxlength' => 10, 'size' => 10);
	$a_input[] = array('kolom' => 'statuspasangan', 'label' => 'Status Pasangan', 'type' => 'S', 'option' => mPegawai::statusPasangan(), 'empty' => true);
	$a_input[] = array('kolom' => 'tglwafat', 'label' => 'Tgl Wafat', 'type' => 'D');
	
	$a_input[] = array('kolom' => 'tglkawin', 'label' => 'Tgl Nikah', 'type' => 'D');
	$a_input[] = array('kolom' => 'iscerai', 'label' => 'Cerai ?', 'type' => 'R', 'option' => SDM::getValid());
	$a_input[] = array('kolom' => 'noskcerai', 'label' => 'No SK Cerai', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'tglskcerai', 'label' => 'Tgl SK Cerai', 'type' => 'D');
	$a_input[] = array('kolom' => 'tmtskcerai', 'label' => 'TMT SK Cerai', 'type' => 'D');

	if($c_valid)	
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	else
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid(), 'readonly' => true);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['idpegawai'] = $r_key;
		$conn->BeginTrans();
		
		if(empty($r_subkey))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,'',true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$p_key);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$p_key);
		
		if(!$p_posterr){			
			@unlink($p_foto);
			Route::navListpage($p_listpage,$r_key);
		}
	}
	else if($r_act == 'savefoto' and $c_edit) {		
		if(empty($_FILES['foto']['error'])) {
			$err = Page::createFoto($_FILES['foto']['tmp_name'],$p_foto,200,200);
			
			switch($err) {
				case -1:
				case -2: $msg = 'format foto harus JPG, GIF, atau PNG'; break;
				case -3: $msg = 'foto tidak bisa disimpan'; break;
				default: $msg = false;
			}
			if($msg !== false)
				$msg = 'Upload gagal, '.$msg;
		}
		else
			$msg = Route::uploadErrorMsg($_FILES['foto']['error']);
		
		uForm::reloadImageFoto($conn,$r_subkey,$dirfoto,$msg);
	}
	else if($r_act == 'deletefoto' and $c_edit) {
		@unlink($p_foto);
		
		uForm::reloadImageFoto($conn,$r_subkey,$dirfoto);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$p_key);
	
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
	
	//cek apakah tidak bisa insert bila sudah punya istri
	$ispunyaistrisuami = $p_model::cekPunyaIstriSuami($conn,$r_key);
	if($ispunyaistrisuami)
		$c_insert = false;
		
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
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
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
						<tr height="30">
							<td colspan="4" class="DataBG">Informasi Biodata</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namapasangan') ?></td>
							<td  class="RightColumnBG" colspan="2"><?= Page::getDataInput($row,'namapasangan') ?></td>
							<td rowspan="8" width="200" align="center">
								<?= empty($r_subkey) ? '' : uForm::getImageFoto($conn,$r_subkey,$dirfoto,$c_edit); ?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'jeniskelamin') ?></td>
							<td  class="RightColumnBG" colspan="2"><?= Page::getDataInput($row,'jeniskelamin') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tmplahir') ?></td>
							<td  class="RightColumnBG" colspan="2"><?= Page::getDataInput($row,'tmplahir') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tgllahir') ?></td>
							<td  class="RightColumnBG" colspan="2"><?= Page::getDataInput($row,'tgllahir') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'ispasangankerja') ?></td>
							<td  class="RightColumnBG" colspan="2"><?= Page::getDataInput($row,'ispasangankerja') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pekerjaan') ?></td>
							<td  class="RightColumnBG" colspan="2"><?= Page::getDataInput($row,'pekerjaan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nippasangan') ?></td>
							<td  class="RightColumnBG" colspan="2"><?= Page::getDataInput($row,'nippasangan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'statuspasangan') ?></td>
							<td  class="RightColumnBG" colspan="2"><?= Page::getDataInput($row,'statuspasangan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglwafat') ?></td>
							<td  class="RightColumnBG" colspan="2"><?= Page::getDataInput($row,'tglwafat') ?></td>
						</tr>
						<tr height="30">
							<td colspan="4" class="DataBG">Informasi Pernikahan</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglkawin') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'tglkawin') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'iscerai') ?></td>
							<td  class="RightColumnBG" width="40%"><?= Page::getDataInput($row,'iscerai') ?></td>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'tmtskcerai') ?></td>
							<td  class="RightColumnBG" width="20%"><?= Page::getDataInput($row,'tmtskcerai') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'noskcerai') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'noskcerai') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglskcerai') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tglskcerai') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isvalid') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isvalid') ?></td>
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
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";
var xtdid = "contents";

$(document).ready(function() {
	
	initEdit(<?= empty($post) ? false : true ?>);
});
</script>
</body>
</html>
