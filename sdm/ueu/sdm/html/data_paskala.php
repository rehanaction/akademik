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
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mPa;
		
	$p_tbwidth = 500;
	$p_title = "Data Skala Penilaian";
	$p_aktivitas = 'NILAI';
	$p_listpage = Route::getListPage();
	$p_dbtable = "pa_skala";
	$p_key = "kodeskala";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_input = array();
	$a_input[] = array('kolom' => 'kodeperiodebobot', 'label' => 'Periode Bobot', 'type' => 'S', 'option' => $p_model::getCPeriodeBobot($conn), 'add' => 'onchange="getSkalaSoal(this.value)"');
	$a_input[] = array('kolom' => 'kodeskala', 'label' => 'Kode', 'maxlength' => 3, 'size' => 3, 'notnull' => true);
	$a_input[] = array('kolom' => 'nilaibawah', 'label' => 'Nilai Bawah - Atas', 'maxlength' => 3, 'size' => 4, 'notnull' => true, 'type' => 'N');
	$a_input[] = array('kolom' => 'nilaiatas', 'label' => 'Nilai Atas', 'maxlength' => 3, 'size' => 4, 'notnull' => true, 'type' => 'N');
	$a_input[] = array('kolom' => 'nilaihuruf', 'label' => 'Nilai Huruf', 'maxlength' => 1, 'size' => 2, 'notnull' => true);
	$a_input[] = array('kolom' => 'predikat', 'label' => 'Predikat', 'maxlength' => 100, 'size' => 30);
	
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$r_kodeperiodebobot = $record['kodeperiodebobot']; 
		$r_nilaibawah = $record['nilaibawah'];
		$r_nilaiatas = $record['nilaiatas'] ;
		
		$cekNilaiTerbawah = $p_model::cekNilaiTerbawah($conn,$r_kodeperiodebobot,$r_nilaibawah);
		$cekNilaiTeratas = $p_model::cekNilaiTeratas($conn,$r_kodeperiodebobot,$r_nilaiatas);
		$cekSkala = $p_model::cekSkala($conn,$r_nilaibawah,$r_nilaiatas);
		
		if(!empty($cekNilaiTerbawah)){
			list($p_posterr,$p_postmsg) = array(true,"Nilai terbawah kurang dari batas nilai bawah");
		}
		else if(!empty($cekNilaiTeratas)){
			list($p_posterr,$p_postmsg) = array(true,"Nilai teratas lebih dari batas nilai atas");
		}
		else if(!empty($cekSkala)){
			list($p_posterr,$p_postmsg) = array(true,"Nilai bawah tidak boleh lebih dari nilai atas");
		}
		else{
			if(empty($r_key))
				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
			else
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		}
		
		if(!$p_posterr){
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$sql = $p_model::getDataSkala($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key,$sql);
			
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
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<link href="style/calendar.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<style>
		.bottomline td{
			border-bottom:1px solid #eaeaea;
		}
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<table width="100%">
				<tr>
					<td>
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
							<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
							<table width="100%" cellspacing="2" cellpadding="4" align="center">
								<tbody>
									<tr>
										<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodeperiodebobot') ?></td>
										<td class="RightColumnBG"><?= Page::getDataInput($row,'kodeperiodebobot') ?></td>
									</tr>
									<tr>
										<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodeskala') ?></td>
										<td class="RightColumnBG"><?= Page::getDataInput($row,'kodeskala') ?></td>
									</tr>
									<tr>
										<td class="LeftColumnBG"><?= Page::getDataLabel($row,'nilaibawah') ?></td>
										<td class="RightColumnBG">
											<?= Page::getDataInput($row,'nilaibawah') ?> - <?= Page::getDataInput($row,'nilaiatas') ?>
										</td>
									</tr>
									<tr>
										<td class="LeftColumnBG"><?= Page::getDataLabel($row,'nilaihuruf') ?></td>
										<td class="RightColumnBG"><?= Page::getDataInput($row,'nilaihuruf') ?></td>
									</tr>
									<tr>
										<td class="LeftColumnBG"><?= Page::getDataLabel($row,'predikat') ?></td>
										<td class="RightColumnBG"><?= Page::getDataInput($row,'predikat') ?></td>
									</tr>
								</tbody>
							</table>
							</div>
							<br>
							<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px; height:70px;">
								<table cellspacing="0" cellpadding="0" align="left">
									<tr>
										<td colspan="2"><strong>Keterangan Batas Nilai :</strong></td>
									</tr>
									<tr>
										<td width="100px">Jumlah Skala</td>
										<td>: <span id="skala"></span></td>
									</tr>
									<tr>
										<td width="100px">Jumlah Soal</td>
										<td>: <span id="soal"></span></td>
									</tr>
									<tr>
										<td width="100px">Nilai Terbawah</td>
										<td>: Jumlah skala x 1</td>
									</tr>
									<tr>
										<td width="100px">Nilai Teratas</td>
										<td>: Jumlah skala x Jumlah Soal</td>
									</tr>
								</table>
							</div>
							</center>
							<? } ?>
							<input type="hidden" name="act" id="act">
							<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
						</form>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	getSkalaSoal($("#kodeperiodebobot").val());
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	$("input[name='namalengkap']").xautox({strpost: "f=acnamapegawai", targetid: "idpegawai", imgchkid: "imgid", imgavail: true});
});

function getSkalaSoal(periode){
	var posted = "f=gpaskalasoal&q="+periode;
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		var text = text.split(':');
		
		$("#skala").html(text[0]);
		$("#soal").html(text[1]);
	});
}
</script>
</body>
</html>
</html>