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
	list($kodeperiodepa,$idpegawai) = explode("|",$r_key);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mPa;
		
	$p_tbwidth = "600";
	$p_title = "Data Periode Penilaian";
	$p_aktivitas = 'NILAI';
	$p_listpage = Route::getListPage();
	$p_dbtable = "pa_hasilpenilaian";
	$p_key = "kodeperiodepa,idpegawai";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_input = array();	
	$a_input[] = array('kolom' => 'kodeperiodepa', 'label' => 'Periode Penilaian', 'type' => 'S', 'option' => $p_model::getCPeriode($conn));
	$a_input[] = array('kolom' => 'pegawaidinilai', 'label' => 'Pegawai Dinilai', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'idpegawai', 'type' => 'H');
	
	
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$record['kodeperiodepa'] = $_POST['kodeperiodepa'];
		$key = $record['kodeperiodepa'];
		$r_key = $_POST['kodeperiodepa'].'|'.$_POST['idpegawai'];
		
		$cekAktifPeriodePa = $p_model::cekAktifPeriodePa($conn,$key);
		
		if (empty($cekAktifPeriodePa)){
			if(count($_POST['idpenilai'])>0){
			
				list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
				$jenis = $_POST['kodepajenis'];
				
				foreach($_POST['idpenilai'] as $index=>$penilai){
					$record['idpegawai']= $_POST['idpegawai'];
					$record['idpenilai']= $penilai;
					$record['kodepajenis']= $jenis[$index];
					
					if(empty($p_posterr))
						list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
				}
			}
		}else
			list($p_posterr,$p_postmsg) = array(true,'Periode penilaian lain masih aktif');
		
		if(!$p_posterr){
			$r_key = $_POST['kodeperiodepa'].'|'.$_POST['idpegawai'];
			list($kodeperiodepa,$idpegawai) = explode("|",$r_key);
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	
	$sql = $p_model::getDataSettingPA($kodeperiodepa,$idpegawai);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key,$sql);

	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
		
	$getCJenisPA = $p_model::jenisPenilai($conn);
	$a_penilai = $p_model::getTimPenilai($conn,$kodeperiodepa);
	
	$no=0;
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<script type="text/javascript" src="scripts/forpager.js"></script>
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
							<table width="100%" cellspacing="0" cellpadding="4" class="bottomline">
								<tbody>
									<tr>
										<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodeperiodepa') ?></td>
										<td class="RightColumnBG"><?= Page::getDataInput($row,'kodeperiodepa') ?></td>
									</tr>
									<tr>
										<td class="LeftColumnBG" width="150" style="white-space:nowrap"><strong>Pegawai yang dinilai</strong></td>
										<td class="RightColumnBG">
											<?= Page::getDataInput($row,'pegawaidinilai') ?>
											<?= Page::getDataInput($row,'idpegawai') ?>
											<span id="edit" style="display:none">
												<img id="imgnik_c" src="images/green.gif">
												<img id="imgnik_u" src="images/red.gif" style="display:none">
											</span>
										</td>
									</tr>
									<?if(!empty($r_key)){
										if(count($a_penilai)>0){
											foreach($a_penilai[$idpegawai] as $idpenilai=>$penilai){?>
												<tr>
													<td class="LeftColumnBG" width="150" style="white-space:nowrap"><strong>Penilai</strong></td>
													<td class="RightColumnBG">
														<table width="100%" cellpadding="2" cellspacing="0">
															<tr>
																<td style="border:1px solid white">
																<span id="show">
																	<?= $penilai['penilai']?>
																</span>
																<span id="edit" style="display:none">
																	<?= UI::createTextBox('penilai[]',$penilai['penilai'],'ControlStyle',50,50,$c_edit)?>
																	<input type="hidden" name="idpenilai[]" id="idpenilai" value="<?= $penilai['idpenilai']?>"/>
																	<img id="imgpen_c" src="images/green.gif"><img id="imgpen_u" src="images/red.gif" style="display:none">&nbsp;
																</span>
																</td>
															</tr>
														</table>
													</td>
												</tr>
												<tr>
													<td class="LeftColumnBG" width="150" style="white-space:nowrap">Jenis Penilaian</td>
													<td class="RightColumnBG">
														<span id="show">
															<?= $penilai['namapajenis']?>
														</span>
														<span id="edit" style="display:none">
															<?= UI::createSelect('kodepajenis[]',$getCJenisPA,$penilai['kodepajenis'], 'ControlStyle',$c_edit)?>
														</span>
													</td>
												</tr>
									<?}}}else{?>
									<tr>
										<td class="LeftColumnBG" width="150" style="white-space:nowrap"><strong>Penilai</strong></td>
										<td class="RightColumnBG">
											<table width="100%" cellpadding="2" cellspacing="0">
												
												<tr>
													<td style="border:1px solid white">
													<span id="edit" style="display:none">
														<?= UI::createTextBox('penilai[]','','ControlStyle',50,50,$c_edit)?>
														<input type="hidden" name="idpenilai[]" id="idpenilai"/>
														<img id="imgpen_c" src="images/green.gif"><img id="imgpen_u" src="images/red.gif" style="display:none">&nbsp;
													</span>
													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td class="LeftColumnBG" width="150" style="white-space:nowrap">Jenis Penilaian</td>
										<td class="RightColumnBG">
											<span id="show">
												
											</span>
											<span id="edit" style="display:none">
												<?= UI::createSelect('kodepajenis[]',$getCJenisPA,'', 'ControlStyle',$c_edit)?>
											</span>
										</td>
									</tr>
									<?}?>
									<tr id="tr_tambah">
										<td colspan="2"  style="border:1px solid white">&nbsp;</td>
									</tr>
									<tr id="edit" style="display:none">
										<td style="border:1px solid white"></td>
										<td style="border:1px solid white">
											<input type="button" name="badd" id="badd" value="Tambah Penilai" onClick="goAdd()" />
										</td>
									</tr>
								</tbody>
							</table>
							</div>
							</center>
							<? } ?>
							<input type="hidden" name="act" id="act">
							<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
							<input type="hidden" name="subkey" id="subkey">
						</form>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>

<table style="display:none">
	<tbody id="tb_template">
	<tr>
		<td class="LeftColumnBG" width="150" style="white-space:nowrap"><strong>Penilai</strong></td>
		<td class="RightColumnBG">
			<table width="100%" cellpadding="2" cellspacing="0">
				<tr>
					<td style="border:1px solid white">
						<?= UI::createTextBox('penilai[]','','ControlStyle',50,50,$c_edit)?>
						<input type="hidden" name="idpenilai[]" id="idpenilai"/>
						<img id="imgpen_c" src="images/green.gif"><img id="imgpen_u" src="images/red.gif" style="display:none">&nbsp;
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="LeftColumnBG" width="150" style="white-space:nowrap">Jenis Penilaian</td>
		<td class="RightColumnBG">
			<?= UI::createSelect('kodepajenis[]',$getCJenisPA,'', 'ControlStyle',$c_edit)?>
		</td>
	</tr>
	</tbody>
</table>

<div id="popMenu" class="menubar" style="position:absolute; display:none; top:0px; left:0px;z-index:10000;" onMouseOver="javascript:overpopupmenu=true" onMouseOut="javascript:overpopupmenu=false">
<table width="160" class="menu-body">
    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('key','<?= Route::navAddress('list_papegawaidinilai') ?>')">Atur pegawai yang dinilai</td>
    </tr>
</table>
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
	
	$("#pegawaidinilai").xautox({strpost: "f=acnamapegawai", targetid: "idpegawai", imgchkid: "imgnik", imgavail: true});
	$("input[name='penilai[]']").xautox({strpost: "f=acnamapegawai", targetid: "idpenilai", imgchkid: "imgpen", imgavail: true});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goAdd(){	
	var newtr = $($("#tb_template").html()).insertBefore("#tr_tambah");
	
	newtr.find("input[name='penilai[]']").xautox({strpost: "f=acnamapegawai", targetid: "idpenilai", imgchkid: "imgpen", imgavail: true});
}


function goSaveDet(){
	if($("#idpegawai").val() == ''){
		doHighlight(document.getElementById("pegawai"));
		alert("Silahkan masukkan nama atau NPP Pegawai");
	}else{
		document.getElementById("act").value = "savepenilai";
		goSubmit();
	}	
}

function goDeleteDet(val){
	var hapus = confirm("Apakah anda ingin menghapus data penilai?");
	if(hapus) {
		document.getElementById("act").value = "delpenilai";
		document.getElementById("subkey").value = val.id;
		goSubmit();
	}
}
</script>
</body>
</html>
</html>