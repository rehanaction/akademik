<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('poinkegiatan'));
	require_once(Route::getModelPath('tahapkegiatan'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	
	$r_jenjang =Modul::setRequest($_POST['kodejenjang'],'kodejenjang');
	$a_jenjang = mCombo::programPendidikan($conn);
	
	$r_periode = Modul::setRequest($_POST['periode'],'periode');
	$a_periode = mCombo::periode($conn);
	
	$r_periodeawal = cstr::removeSpecial($_POST['periodeawal']);
	$r_jenjangawal = cstr::removeSpecial($_POST['jenjangawal']);
	
	$r_periodetujuan = cstr::removeSpecial($_POST['periodetujuan']);
	$r_jenjangtujuan = cstr::removeSpecial($_POST['jenjangtujuan']);
		
    if (empty ($r_periode))
	{
		$p_postmsg='Silahkan Pilih Periode';
		$p_posterr=true;
	}
	// properti halaman
	$p_title = 'Setting Poin';
	$p_tbwidth = '700';
	$p_aktivitas = 'Master';
	
	$p_model = mPoinkegiatan;
	$p_key = $p_model::key;
	$p_colnum = count($p_kolom)+1;
	
	$l_periode = uCombo::periode($conn,$r_periode,'periode','onchange="goSubmit()"',true);
	$l_jenjang = uCombo::programPendidikan($conn,$r_jenjang,'kodejenjang','onchange="goSubmit()"',true);
	$a_poin = $p_model::getListArrayPeriode($conn,$r_periode,$r_jenjang);		

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'update' and $c_edit and $r_periode) {
		$r_key = CStr::removeSpecial($_POST['key']);
		$record = array();
		$record['periode'] = $r_periode;
		$record['kodekegiatan'] = $r_key;
		$record['kodejenjang'] = $r_jenjang;
			if(isset($a_poin[$r_periode][$r_key])){
					$record['POINKEGIATAN'] = $_POST['poinkegiatan_'.$r_key];
					$err = $p_model::updateRecord($conn,$record,$r_periode.'|'.$r_key.'|'.$r_jenjang);
			}else{
					$record['POINKEGIATAN'] = $_POST['poinkegiatan_'.$r_key];
					$record['IDTAHAP'] = $i;
					$err = $p_model::insertRecord($conn,$record);
			}
			
			if($err<>'0'){
				$p_posterr = true;
				$p_postmsg = "Gagal update Poin";
			}
			else
			{
				$p_posterr = false;
				$p_postmsg = "berhasil update Poin";
				}
		
		//list($p_posterr,$p_postmsg) = $p_model::insertRec($conn,$a_kolom,$_POST,$r_key);
	}else if ($r_act == 'updateall' and $c_edit and $r_periode){

	}else if ($r_act == 'salinpoin' and $c_edit){
			list($p_posterr,$p_postmsg) = $p_model::copyPoinKegiatan($conn,$r_periodeawal,$r_periodetujuan,$r_jenjangawal,$r_jenjangtujuan);
	} 
	else if($r_act == 'edit' and $c_edit){
		$r_edit = CStr::removeSpecial($_POST['key']);
	}
	$a_poin = $p_model::getListArrayPeriode($conn,$r_periode,$r_jenjang);		
	$a_tahap = mTahap::getArray($conn);

	$arr_data = $p_model::getListData($conn,$r_periode,$r_jenjang);
	
	//var_dump($arr_data);
	foreach($a_poin[$r_periode] as $i => $v){
		$arr_data[$i]['poin'] = $v;
	}

	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Jenjang', 'combo' => $l_jenjang);
	
?>


<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forinplace.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
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
					
					/************************/
					/* COMBO FILTER HALAMAN */
					/************************/
					
					if(!empty($a_filtercombo)) {
				?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<? foreach($a_filtercombo as $t_filter) { ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>		
										</tr>
										<? } ?>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?	} ?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4" border="0">
										<tr>		
											<td>
												<table>
													<tr>
														<td>Periode</td>
														<td><?= UI::createSelect('periodeawal',$a_periode,$r_periodeawal)?></td>
													</tr>
													<tr>
														<td>Jenjang</td>
														<td><?= UI::createSelect('jenjangawal',$a_jenjang,$r_jenjangawal)?></td>
													</tr>
												</table>
											</td>
											<td>salin ke </td>		
											<td>
											<td>
												<table>
													<tr>
														<td>Periode</td>
														<td><?= UI::createSelect('periodetujuan',$a_periode,$r_periodetujuan)?></td>
													</tr>
													<tr>
														<td>Jenjang</td>
														<td><?= UI::createSelect('jenjangtujuan',$a_jenjang,$r_jenjangtujuan)?></td>
													</tr>
												</table>
											</td>
											
											
											</td>		
										</tr>
									</table>
								</td>
							</tr>
										<tr>
											<td align="center" colspan="3"><input type="button" value="salin poin kegiatan" onClick="goCopyPoin()"></td>
										</tr>

						</table>
					</div>
				</center>
				<br>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
				</center>

				<?	/*************/
					/* LIST DATA */
					/*************/
				?>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
						<th width="50">No</th>
						<th width="300">Nama Kegiatan</th>
						<th width="100">Poin</th>
						<?		
						if($c_edit) { 
						?>
						<th width="50">Edit</th>
						<?	} ?>
					</tr>
                    <?                     
					foreach($arr_data as $i => $v){
						$pad = 'style="padding-left:'.(($v['level']*15)+4).'px"';
						$t_key = $v['key'];
						if($t_key == $r_edit and $c_edit and $r_edit) {
							if(empty($v['level'])){
								echo "<tr><td>".$v['no']."</td><td colspan='2'><b>".$v['namakegiatan']."</b></td></tr>";
							}else{
					?>
								<tr>
									<td <?=$pad?> ><?=$v['no']; ?></td>
									<td <?=$pad?> ><?=$v['namakegiatan'] ?></td>
									<td><?=UI::createTextbox('poinkegiatan_'.$t_key,CStr::FormatNumber($v['poin']),'ControlStyle','','',true,'onkeydown="return onlyNumber(event,this,'.true.','.true.')" style="text-align:right"')?></td>
									<td align="center">
										<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onClick="goUpdate(this)" style="cursor:pointer">
									</td>
								</tr>
					 <? 	}
						} else {	
							if(empty($v['level'])){
								$angka = 1;
								echo "<tr><td>".$v['no']."</td><td colspan='3'><b>".$v['namakegiatan']."</b></td></tr>";
							}else{
								if($v['level'] == 1){
									$huruf = 'a';
									$no = $huruf;
								}else{
									$no = $angka;
								}
									
					 ?>
								<tr>
									<td <?=$pad?> ><?=$v['no']; ?></td>
									<?php if($v['level'] == 1){ ?>
										<td colspan="3" <?=$pad?> ><b><?=$v['namakegiatan'] ?></b></td>
									<?php }else { ?>
										<td <?=$pad?> ><?=$v['namakegiatan'] ?></td>
										<td align="center"><?=$v['poin'] ?></td>
									<td align="center">
									<?	if($c_edit and (!empty($r_jenjang) and !empty($r_periode))) { ?>
										<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onClick="goEdit(this)" style="cursor:pointer">
									<?			}
									}
									?>
									</td>
								</tr>
					<?php
							}
							$angka++;
							$huruf++;
						}
				}
				?>
				</table>
				
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
            
           <!-- <div style="clear:both"></div>
				<div>
					<fieldset style="background:#E0FFF3; border:1px solid #CCC;">
                        <legend> Keterangan </legend>
                        
                    </fieldset>
				</div>
		</div>-->
	</div>
</div>
<script type="text/javascript">
	
var insertreq = "<?= @implode(',',$a_insertreq) ?>";
var updatereq = "<?= @implode(',',$a_updatereq) ?>";

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	// handle scrolltop
	$(window).scrollTop($("#scroll").val());
	
	// handle focus
	// $("[id^='i_']:first").focus();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goSetAll(){
	var conf = confirm("Apakah anda yakin Melakukan perubahan Tarif untuk semua Prodi?");
	if(conf) {
		document.getElementById("act").value = "updateall";
		goSubmit();
	}
	
}
function goCopyPoin(){
	var conf = confirm("Apakah anda yakin Melakukan salin poin kegiatan?");
	if(conf) {
		document.getElementById("act").value = "salinpoin";
		goSubmit();
	}
	
}
</script>
</body>
</html>
