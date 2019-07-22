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
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mGaji;
		
	$p_tbwidth = "550";
	$p_title = "Data Jenis Tunjangan";
	$p_aktivitas = 'ANGGARAN';
	$p_listpage = Route::getListPage();
	$p_dbtable = "ms_tunjangan";
	$p_key = "kodetunjangan";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//jumlah hari kerja
	$harikerja = mPresensi::getHariKerja($conn);
	
	$a_input = array();	
	if(empty($r_key))
		$a_input[] = array('kolom' => 'kodetunjangan', 'label' => 'Kode', 'maxlength' => 6, 'size' => 6, 'notnull' => true);
	else
		$a_input[] = array('kolom' => 'kodetunjangan', 'label' => 'Kode', 'maxlength' => 6, 'size' => 6, 'readonly' => true);
	$a_input[] = array('kolom' => 'namatunjangan', 'label' => 'Tunjangan', 'maxlength' => 100, 'size' => 40, 'notnull' => true);
	$a_input[] = array('kolom' => 'urutan', 'label' => 'Urutan', 'maxlength' => 3, 'size' => 3, 'notnull' => true);
	$a_input[] = array('kolom' => 'carahitung', 'label' => 'Cara Hitung', 'type' => 'R', 'option' => $p_model::aCaraHitungTunj());
	$a_input[] = array('kolom' => 'isgajitetap', 'label' => 'Termasuk Gaji Tetap', 'type' => 'C', 'option' => array("Y" => "Gaji Tetap"));
	$a_input[] = array('kolom' => 'isbayargaji', 'label' => 'Dibayarkan bersama gaji?', 'type' => 'R', 'option' => $p_model::iyaTidak(), 'default' => 'Y');
	$a_input[] = array('kolom' => 'iskeldosen', 'label' => 'Dikalikan Jam Kerja?', 'type' => 'R', 'option' => $p_model::iyaTidak(), 'default' => 'T');
	$a_input[] = array('kolom' => 'iskaliharikerja', 'label' => 'Dikalikan Hari Kerja (<b>'.$harikerja.' hari</b>) ?', 'type' => 'R', 'option' => $p_model::iyaTidak(), 'default' => 'T');
	$a_input[] = array('kolom' => 'isaktif', 'label' => 'Aktif?', 'type' => 'R', 'option' => $p_model::iyaTidak(), 'default' => 'Y');
	
	$a_inputdetail = array();	
	$a_inputdetail[] = array('kolom' => 'jenis[]', 'label' => 'Jenis Pegawai', 'type' => 'C', 'br' => true, 'option' => $p_model::getCJenisPegawai($conn));
	
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		$conn->StartTrans();
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){
			if(empty($r_key))
				$r_key = $record['kodetunjangan'];
			
			$p_model::saveTunjHak($conn,$r_key,$_POST['jenis']);			
				
			unset($post);
		}
		
		$conn->CompleteTrans();
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
	
	$col = $p_model::getDataEdit($conn,$a_inputdetail,$r_key,$post,'ms_tunjangandet',$p_key);
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
	
	//jenis pegawai
	$a_jenispegawai = $p_model::aTunjHak($conn, $r_key);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
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
					<?	$a_required = array();
						foreach($row as $t_row) {
							if($t_row['notnull'])
								$a_required[] = $t_row['id'];
					?>
						<tr>
							<td class="LeftColumnBG" width="175px" style="white-space:nowrap">
								<?= $t_row['label'] ?>
								<?= $t_row['notnull'] ? '<span id="edit" style="display:none">*</span>' : '' ?>
							</td>
							<td class="RightColumnBG">
								<span id="show"><?= $t_row['value'] ?></span>
								<span id="edit" style="display:none"><?= $t_row['input'] ?></span>
							</td>
						</tr>
					<?	} ?>
					
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">
								Jenis Pegawai
							</td>
							<td class="RightColumnBG">
								<span id="show">
								<? if (count($a_jenispegawai)>0){
									foreach($a_jenispegawai as $rows){ 
										echo (!empty($rows['kodetunjangan']) ? '<img src="images/check.png">' : '').$rows['jenispegawai'].'<br />';
								}} ?>
								</span>
								<span id="edit" style="display:none">
								<? if (count($a_jenispegawai)>0){
									foreach($a_jenispegawai as $rows){ 
								?>
								<input type="checkbox" id="jenis<?= $rows['idjenispegawai']; ?>" name="jenis[]" value="<?= $rows['idjenispegawai']; ?>" <?= !empty($rows['kodetunjangan']) ? 'checked' : '';?>><label for="jenis<?= $rows['idjenispegawai']; ?>"><?= $rows['jenispegawai']; ?></label> <br />
								<? }} ?>
								</span>
							</td>
						</tr>
					</table>
					</div>							
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<?	} ?>
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
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

</script>
</body>
</html>
