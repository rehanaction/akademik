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
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mGaji;
		
	$p_tbwidth = "680";
	$p_title = "Data Tunjangan Lain";
	$p_aktivitas = 'ANGGARAN';
	$p_listpage = Route::getListPage();
	$p_dbtable = "ga_pegawaitunjangan";
	$p_key = "notunjangan";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_input = array();	
	$a_input[] = array('kolom' => 'kodetunjangan', 'label' => 'Jenis Tunjangan', 'type' => 'S', 'option' => $p_model::getCTunjManual($conn), 'notnull' => true);
	$a_input[] = array('kolom' => 'namalengkap', 'label' => 'Nama Pegawai', 'maxlength' => 255, 'size' => 60, 'notnull' => true);
	$a_input[] = array('kolom' => 'idpegawai', 'type' => 'H', 'notnull' => true);
	$a_input[] = array('kolom' => 'tmtmulai', 'label' => 'Tgl. Mulai', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'nominal', 'label' => 'Tarif Tunjangan', 'maxlength' => 12, 'size' => 15, 'notnull' => true, 'type' => 'N');
	$a_input[] = array('kolom' => 'isaktif', 'label' => 'Aktif', 'type' => 'C', 'option' => array("Y" => "Aktif"));
	
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$sql = $p_model::getDataEditTunjLain($r_key);
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
											<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1>Detail Tunjangan Lain</h1>
										</div>
									</div>
								</header>
							<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
							<table width="100%" cellspacing="0" cellpadding="4" class="bottomline">
								<tbody>
									<tr>
										<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodetunjangan') ?></td>
										<td class="RightColumnBG"><?= Page::getDataInput($row,'kodetunjangan') ?></td>
									</tr>
									<tr>
										<td class="LeftColumnBG"><?= Page::getDataLabel($row,'namalengkap') ?></td>
										<td class="RightColumnBG">
											<?= Page::getDataInput($row,'namalengkap') ?>
											<?= Page::getDataInput($row,'idpegawai')?>
											<span id="edit" style="display:none">
											&nbsp;&nbsp;<img id="imgid_c" src="images/green.gif">
											<img id="imgid_u" src="images/red.gif" style="display:none">
											</span>
										</td>
									</tr>
									<tr>
										<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tmtmulai') ?></td>
										<td class="RightColumnBG"><?= Page::getDataInput($row,'tmtmulai') ?></td>
									</tr>
									<tr>
										<td class="LeftColumnBG"><?= Page::getDataLabel($row,'nominal') ?></td>
										<td class="RightColumnBG"><?= Page::getDataInput($row,'nominal') ?></td>
									</tr>
									<tr>
										<td class="LeftColumnBG"><?= Page::getDataLabel($row,'isaktif') ?></td>
										<td class="RightColumnBG"><?= Page::getDataInput($row,'isaktif') ?></td>
									</tr>
								</tbody>
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
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	$("input[name='namalengkap']").xautox({strpost: "f=acnamapegawai", targetid: "idpegawai", imgchkid: "imgid", imgavail: true});
});

</script>
</body>
</html>
</html>