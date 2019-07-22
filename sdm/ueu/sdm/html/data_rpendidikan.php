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
	require_once(Route::getModelPath('gaji'));
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
	$p_title = 'Data Riwayat Pendidikan';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mRiwayat;
	$p_dbtable = "pe_rwtpendidikan";
	$where = 'nourutrpen';
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idpendidikan', 'label' => 'Jenjang Pendidikan', 'type' => 'S', 'option' => $p_model::jenjangPendidikan($conn), 'notnull' => true);
	$a_input[] = array('kolom' => 'pt', 'label' => 'Nama Institusi', 'maxlength' => 255, 'size' => 90);
	$a_input[] = array('kolom' => 'kodept', 'type' => 'H');
	$a_input[] = array('kolom' => 'fakultas', 'label' => 'Fakultas', 'maxlength' => 255, 'size' => 40);
	$a_input[] = array('kolom' => 'kodefakultas', 'type' => 'H');
	$a_input[] = array('kolom' => 'jurusan', 'label' => 'Jurusan', 'maxlength' => 100, 'size' => 40);
	$a_input[] = array('kolom' => 'kodejurusan', 'type' => 'H');
	$a_input[] = array('kolom' => 'bidang', 'label' => 'Bidang', 'maxlength' => 100, 'size' => 40);
	$a_input[] = array('kolom' => 'kodebidang', 'type' => 'H');
	$a_input[] = array('kolom' => 'isln', 'label' => 'Lokasi', 'type' => 'S', 'option' => $p_model::lokasiPendidikan());
	$a_input[] = array('kolom' => 'alamatinstitusi', 'label' => 'Alamat Institusi', 'type' => 'A', 'rows' => 3, 'cols' => 40, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'kepalainstitusi', 'label' => 'Kepala Institusi', 'maxlength' => 100, 'size' => 40);
		
	$a_input[] = array('kolom' => 'judulpenelitian', 'label' => 'Judul', 'type' => 'A', 'rows' => 3, 'cols' => 40, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'gelarpanjang', 'label' => 'Gelar', 'maxlength' => 255, 'size' => 40);
	$a_input[] = array('kolom' => 'gelar', 'label' => 'Singkatan Gelar', 'maxlength' => 25, 'size' => 40);
	$a_input[] = array('kolom' => 'letakgelar', 'label' => 'Letak Gelar', 'type' => 'S', 'option' => $p_model::letakGelar());
	$a_input[] = array('kolom' => 'noijazah', 'label' => 'No Ijazah', 'maxlength' => 50, 'size' => 40);
	$a_input[] = array('kolom' => 'tglijazah', 'label' => 'Tgl. Ijazah', 'type' => 'D');
	$a_input[] = array('kolom' => 'noijazahnegara', 'label' => 'No Ijazah Negara', 'maxlength' => 50, 'size' => 40);
	$a_input[] = array('kolom' => 'tglijazahnegara', 'label' => 'Tgl. Ijazah Negara', 'type' => 'D');
	$a_input[] = array('kolom' => 'tahunlulus', 'label' => 'Tahun Lulus', 'maxlength' => 4, 'size' => 5);
	$a_input[] = array('kolom' => 'ipk', 'label' => 'IPK', 'type' => 'N,2', 'maxlength' => 5, 'size' => 5);
	
	if($c_valid){
		$a_input[] = array('kolom' => 'isdiakuiuniv', 'label' => 'Diakui Universitas', 'type' => 'R', 'option' => $p_model::diakuiUniversitas());
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	}else{
		$a_input[] = array('kolom' => 'isdiakuiuniv', 'label' => 'Diakui Universitas', 'type' => 'R', 'option' => $p_model::diakuiUniversitas(), 'readonly' => true);
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid(), 'readonly' => true);		
	}
	$a_input[] = array('kolom' => 'fileijazah', 'label' => 'File Ijazah', 'type' => 'U', 'uptype' => 'fileijazah', 'size' => 40);
	$a_input[] = array('kolom' => 'filetranskrip', 'label' => 'File Transkrip', 'type' => 'U', 'uptype' => 'filetranskrip', 'size' => 40);
	
	//pendidikan terakhir sebelum disimpan
	$pendakhirbs = $p_model::getPendidikanAkhir($conn,$r_key);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['idpegawai'] = $r_key;
		$record['namainstitusi'] = $record['pt'];
		if($record['namainstitusi'] != 'null')
			$record['namainstitusi'] = strtoupper($record['namainstitusi']);
		
		$conn->BeginTrans();
		
		if(empty($r_subkey))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,'',true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
			
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
		
		if($ok) 
			unset($post);
		else
			Route::setFlashDataPost($post);
		
		//update gelar ke ms_pegawai
		if(!$p_posterr)
			$p_posterr = $p_model::setGelar($conn,$r_subkey);

		if(!$p_posterr){
			//pendidikan terakhir setelah disimpan
			$pendakhirss = $p_model::getPendidikanAkhir($conn,$r_key);
			
			//bila ada perubah pendidikan terakhir, maka unvalid dulu tarif honor
			if($pendakhirss != $pendakhirbs){
				$p_posterr = mGaji::unValidRateHonor($conn,$r_key);
				if($p_posterr)
					$p_postmsg = 'Penyimpanan Unvalid honor gagal';
			}
		}
			
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = mGaji::setRateHonor($conn,$r_key);

		if(!$p_posterr){
			$info = mPegawai::getSimplePegawai($conn,$r_key);
			$r_namalengkap = $info['namalengkap'];
			$p_label = $r_namalengkap.(!empty($info['nik']) ? ' | '.$info['nik'] : '');
		?>
		<script type="text/javascript">
			window.parent.$("#labelpeg").html('<?= $p_label?>');
		</script>
		<?php
		}
		
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
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where,'','fileijazah,filetranskrip');
		
		if(!$p_posterr){			
			//pendidikan terakhir setelah dihapus
			$pendakhirss = $p_model::getPendidikanAkhir($conn,$r_key);
			
			//bila ada perubah pendidikan terakhir, maka unvalid dulu tarif honor
			if($pendakhirss != $pendakhirbs){
				$p_posterr = mGaji::unValidRateHonor($conn,$r_key);
				if($p_posterr)
					$p_postmsg = 'Penyimpanan Unvalid honor gagal';
			}
			
			if(!$p_posterr)
				list($p_posterr,$p_postmsg) = mGaji::setRateHonor($conn,$r_key);
		}
		
		if(!$p_posterr) Route::navListpage($p_listpage,$r_key);
	}
	else if($r_act == 'deletefile' and $c_delete) {
		$r_file = CStr::removeSpecial($_POST['file']);

		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_subkey,$p_dbtable,$r_file,$where);			
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
	
	$sql = $p_model::getDataEditPendidikan($r_subkey);
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
						<tr height="30">
							<td colspan="4" class="DataBG">Informasi Pendidikan</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idpendidikan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'idpendidikan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pt') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'pt') ?>
								<?= Page::getDataInput($row,'kodept') ?>	
								<span id="edit" style="display:none">
									<img id="imgpt_c" src="images/green.gif">
									<img id="imgpt_u" src="images/red.gif" style="display:none">
								</span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'fakultas') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'fakultas') ?>
								<?= Page::getDataInput($row,'kodefakultas') ?>	
								<span id="edit" style="display:none">
									<img id="imgfak_c" src="images/green.gif">
									<img id="imgfak_u" src="images/red.gif" style="display:none">
								</span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'jurusan') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'jurusan') ?>
								<?= Page::getDataInput($row,'kodejurusan') ?>	
								<span id="edit" style="display:none">
									<img id="imgjur_c" src="images/green.gif">
									<img id="imgjur_u" src="images/red.gif" style="display:none">
								</span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'bidang') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'bidang') ?>
								<?= Page::getDataInput($row,'kodebidang') ?>	
								<span id="edit" style="display:none">
									<img id="imgbid_c" src="images/green.gif">
									<img id="imgbid_u" src="images/red.gif" style="display:none">
								</span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kepalainstitusi') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'kepalainstitusi') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isln') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isln') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'alamatinstitusi') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'alamatinstitusi') ?></td>
						</tr>
						<tr height="30">
							<td colspan="4" class="DataBG">Informasi Kelulusan</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'judulpenelitian') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'judulpenelitian') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'gelarpanjang') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'gelarpanjang') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'gelar') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'gelar') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'letakgelar') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'letakgelar') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'noijazah') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'noijazah') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglijazah') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tglijazah') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'noijazahnegara') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'noijazahnegara') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglijazahnegara') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tglijazahnegara') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tahunlulus') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'tahunlulus') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'ipk') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'ipk') ?></td>
						</tr>
						<tr height="30">
							<td colspan="4" class="DataBG">Informasi Pendukung</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isdiakuiuniv') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isdiakuiuniv') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isvalid') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isvalid') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'fileijazah') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'fileijazah') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'filetranskrip') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'filetranskrip') ?></td>
						</tr>
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey" value="<?= $r_subkey; ?>">
				<input type="hidden" name="file" id="file">
				<?	} ?>
			</form>
		</td>
	</tr>
</table>

<iframe name="upload_iframe" style="display:none"></iframe>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";
var xtdid = "contents";

$(document).ready(function() {
	
	initEdit(<?= empty($post) ? false : true ?>);
	
	//autocomplete
	$("#pt").xautox({strpost: "f=acpt", targetid: "kodept", imgchkid: "imgpt", imgavail: true});
	$("#fakultas").xautox({strpost: "f=acfakultas", targetid: "kodefakultas", imgchkid: "imgfak", imgavail: true});
	$("#jurusan").xautox({strpost: "f=acjurusan", targetid: "kodejurusan", imgchkid: "imgjur", imgavail: true});
	$("#bidang").xautox({strpost: "f=acbidang", targetid: "kodebidang", imgchkid: "imgbid", imgavail: true});
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
</script>
</body>
</html>
