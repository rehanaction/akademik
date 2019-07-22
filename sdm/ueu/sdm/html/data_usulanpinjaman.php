<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pinjaman'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	//konfigurasi halaman
	$p_model = mPinjaman;
		
	$p_tbwidth = "800";
	$p_title = "Data Perjanjian Pinjaman";
	$p_aktivitas = 'ANGGARAN';
	$p_listpage = Route::getListPage();
	$p_dbtable = "pe_pinjaman";
	$p_key = "idpinjaman";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_input = array();	
	$a_input[] = array('kolom' => 'kodejnspinjaman', 'label' => 'Jenis Pinjaman', 'type' => 'S', 'option' => $p_model::getCJenisPinjaman($conn), 'notnull' => true);
	$a_input[] = array('kolom' => 'namalengkap', 'label' => 'Nama Pegawai', 'maxlength' => 255, 'size' => 60, 'notnull' => true);
	$a_input[] = array('kolom' => 'idpeminjam', 'type' => 'H');
	$a_input[] = array('kolom' => 'tglpinjaman', 'label' => 'Tgl. Usulan', 'type' => 'D');
	$a_input[] = array('kolom' => 'jmlpinjaman', 'label' => 'Jumlah Pengajuan', 'type' => 'N', 'maxlength' => 14, 'size' => 14);
	$a_input[] = array('kolom' => 'keperluan', 'label' => 'Keperluan', 'type' => 'A', 'rows' => 3, 'cols' => 40, 'maxlength' => 500);
	$a_input[] = array('kolom' => 'jmlcicilan', 'label' => 'Jml. Angsuran', 'type' => 'N', 'maxlength' => 3, 'size' => 3);
	$a_input[] = array('kolom' => 'noperjanjian', 'label' => 'No Perjanjian', 'maxlength' => 50, 'size' => 40, 'notnull' => true);
	$a_input[] = array('kolom' => 'tglperjanjian', 'label' => 'Tgl. Perjanjian', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'tgldisetujui', 'label' => 'Tgl. Disetujui', 'type' => 'D');
	$a_input[] = array('kolom' => 'jmldisetujui', 'label' => 'Jml. Pinjaman disetujui', 'type' => 'N', 'maxlength' => 14, 'size' => 14, 'notnull' => true,'add' => 'onKeyUp="jmlPinjaman();numberFormat(this);"');
	$a_input[] = array('kolom' => 'jmlcicilandisetujui', 'label' => 'Jml. Cicilan disetujui', 'type' => 'N', 'maxlength' => 3, 'size' => 3, 'notnull' => true);
	$a_input[] = array('kolom' => 'biayaadministrasi', 'label' => 'Biaya Administrasi', 'maxlength' => 14, 'size' => 14, 'type' => 'N','add' => 'onKeyUp="jmlPinjaman();numberFormat(this);"');
	$a_input[] = array('kolom' => 'totalpinjaman', 'label' => 'Total Pinjaman', 'maxlength' => 14, 'size' => 14, 'type' => 'N','class' => 'ControlRead');
	$a_input[] = array('kolom' => 'awalpinjam', 'label' => 'Periode Pinjaman', 'type' => 'D');	
	$a_input[] = array('kolom' => 'akhirpinjam', 'label' => 'Tgl. Akhir Pinjam', 'type' => 'D');	
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 3, 'cols' => 40, 'maxlength' => 500);
	
	if (Modul::getRole() != 'gajihrm'){
	$a_input[] = array('kolom' => 'bulan', 'label' => 'Periode Eksekusi', 'empty' => true, 'type' => 'S', 'option' => Date::arrayMonth(), 'notnull' => true);
	$a_input[] = array('kolom' => 'tahun', 'maxlength' => 4, 'size' => 4, 'notnull' => true);
	$a_input[] = array('kolom' => 'nobuktidicairkan', 'label' => 'No Bukti Dicairkan', 'maxlength' => 100, 'size' => 40);
	$a_input[] = array('kolom' => 'tgldicairkan', 'label' => 'Tgl. Dicairkan', 'type' => 'D');
	$a_input[] = array('kolom' => 'isfixpinjam', 'label' => 'Valid', 'type' => 'C', 'option' => SDM::getVerifikasi());
	}	

	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$conn->BeginTrans();
		
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		if($record['bulan'] != 'null' and $record['tahun'] != 'null')
			$record['periodeawal'] = $record['tahun'].str_pad($record['bulan'],2,'0', STR_PAD_LEFT);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr){
			unset($post);
			
			if(!empty($_POST['noawal']) and !empty($_POST['noakhir']) and !empty($_POST['jmlangsuran'])){
				$recangs = array();
				$recangs['noawal'] = CStr::removeSpecial($_POST['noawal']);
				$recangs['noakhir'] = CStr::removeSpecial($_POST['noakhir']);
				$recangs['jmlangsuran'] = CStr::removeSpecial($_POST['jmlangsuran']);
				
				list($p_posterr,$p_postmsg) = $p_model::saveBayarAngsuran($conn,$recangs,$r_key);
			}else{
				//simpan angsuran, apabila tidak ada angsuran detail
				$r_jmlangs = $_POST['jmlangs'];
				if($r_jmlangs == 0)
					$p_posterr = $p_model::saveAngsuran($conn,$record,$r_key);
			}
		
			if(!$p_posterr){
				$ok = Query::isOK($p_posterr);
				$conn->CommitTrans($ok);
			}
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'pe_angsuran',$p_key);
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'pe_bayarpinjaman',$p_key);
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'saveangs' and $c_edit) {
		$recangs = array();
		$recangs['noawal'] = CStr::removeSpecial($_POST['noawal']);
		$recangs['noakhir'] = CStr::removeSpecial($_POST['noakhir']);
		$recangs['jmlangsuran'] = CStr::removeSpecial($_POST['jmlangsuran']);
		
		list($p_posterr,$p_postmsg) = $p_model::saveBayarAngsuran($conn,$recangs,$r_key);
	}
	else if($r_act == 'deleteangs' and $c_edit) {
		$r_subkey = $_POST['subkey'];
		
		list($p_posterr,$p_postmsg) = $p_model::deleteBayarAngsuran($conn,$r_key,$r_subkey);
	}
	
	$sql = $p_model::getDataEditPerjanjian($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key,$sql);
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
	//daftar angsuran
	if(!empty($r_key))
		$rsa = $p_model::getAngsuranPerjanjian($conn,$r_key);
	
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
											<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1>Detail Usulan Pinjaman</h1>
										</div>
									</div>
								</header>
							<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
							<table width="100%" cellspacing="0" cellpadding="4" class="bottomline">
								<tbody>
									<tr height="30">
										<td colspan="3" class="DataBG">Informasi Pengajuan</td>
									</tr>
									<tr>
										<td width="200"><?= Page::getDataLabel($row,'kodejnspinjaman') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'kodejnspinjaman') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'namalengkap') ?></td>
										<td>:</td>
										<td>
											<?= Page::getDataInput($row,'namalengkap') ?>
											<span id="edit" style="display:none">
											&nbsp;&nbsp;<img id="imgid_c" src="images/green.gif">
											<img id="imgid_u" src="images/red.gif" style="display:none">
											</span>
											<?= Page::getDataInput($row,'idpeminjam')?>
										</td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'tglpinjaman') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'tglpinjaman') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'jmlpinjaman') ?></td>
										<td>:</td>
										<td>
											<?= Page::getDataInput($row,'jmlpinjaman') ?>
											&nbsp;&nbsp;&nbsp;Angsuran : <?= Page::getDataInput($row,'jmlcicilan') ?> x
										</td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'keperluan') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'keperluan') ?></td>
									</tr>
									<tr height="30">
										<td colspan="3" class="DataBG">Informasi Perjanjian</td>
									</tr>
									<tr>
										<td width="200"><?= Page::getDataLabel($row,'noperjanjian') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'noperjanjian') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'tglperjanjian') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'tglperjanjian') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'tgldisetujui') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'tgldisetujui') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'jmldisetujui') ?></td>
										<td>:</td>
										<td>
											<?= Page::getDataInput($row,'jmldisetujui') ?>
											&nbsp;&nbsp;&nbsp;Angsuran : <?= Page::getDataInput($row,'jmlcicilandisetujui') ?> x
										</td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'biayaadministrasi') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'biayaadministrasi') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'totalpinjaman') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'totalpinjaman') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'awalpinjam') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'awalpinjam') ?> s/d <?= Page::getDataInput($row,'akhirpinjam') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'keterangan') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'keterangan') ?></td>
									</tr>
									<?if (Modul::getRole() != 'gajihrm'){?>
									<tr>
										<td><?= Page::getDataLabel($row,'tgldicairkan') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'tgldicairkan') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'nobuktidicairkan') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'nobuktidicairkan') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'bulan') ?></td>
										<td>:</td>
										<td>
											<?= Page::getDataInput($row,'bulan') ?>
											<?= Page::getDataInput($row,'tahun') ?>
										</td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'isfixpinjam') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'isfixpinjam') ?></td>
									</tr>
									<?}?>
								</tbody>
							</table>
							</div>
							</center>
							<br>
							<center>
								<?if (Modul::getRole() != 'gajihrm'){?>
								<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;display:none" id="edit">
									<table width="100%" cellspacing="0" cellpadding="4">
										<tr>		
											<td width="100" style="white-space:nowrap"><strong>Ansuran Ke</strong></td>
											<td>
												<?= UI::createTextBox('noawal','','ControlStyle',3,3,$c_edit,'onkeydown="return onlyNumber(event,this,true,true);"');?>
												<strong> s/d </strong>
												<?= UI::createTextBox('noakhir','','ControlStyle',3,3,$c_edit,'onkeydown="return onlyNumber(event,this,true,true);"');?>
											</td>		
											<td width="100" style="white-space:nowrap"><strong>Jml. Angsuran</strong></td>
											<td><?= UI::createTextBox('jmlangsuran','','ControlStyle',14,14,$c_edit,'onkeydown="return onlyNumber(event,this,true,true);"');?></td>
											<td width="100">
											<? if($c_insert and $r_key){?>
												<input type="button" value="Simpan Angsuran" class="ControlStyle" onClick="goSaveAngs()">
											<?}?>
											</td>								
										</tr>
									</table>
								</div>
								<br>
								<?}?>
								<table class="GridStyle" width="<?= $p_tbwidth?>" cellspacing="2" cellpadding="4" align="center">
									<tr>
										<td class="DataBG" colspan="4">Detail Perjanjian Angsuran Pinjaman</td>
									</tr>
									<tr>
										<th class="HeaderBG" align="center">Angsuran Ke</th>
										<th class="HeaderBG" align="center">Besar Angsuran</th>
										<th class="HeaderBG" align="center">Total Angsuran</th>
										<th id="edit" class="HeaderBG" width="50" align="center" style="">Aksi</th>
									</tr>
									<?
										$i = 0;
										if(count($rsa) > 0){
										foreach($rsa as $rowa){
											if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
											$totalangsuran = (($rowa['max']-$rowa['min'])+1) * $rowa['jmlangsuran'];
											$jmlpinjaman += $totalangsuran;
									?>
									<tr valign="top" class="<?= $rowstyle ?>">
										<td align="center"><?= 'Angsuran '.$rowa['min'].' - '.$rowa['max']?></td>
										<td align="right"><?= CStr::formatNumber($rowa['jmlangsuran'])?></td>
										<td align="right"><?= CStr::formatNumber($totalangsuran)?></td>
										<td align="center">
											<span id="edit" style="display:none">
											<? if($c_delete) { ?>
												<img id="<?= $rowa['min'].':'.$rowa['max'] ?>" title="Hapus angsuran <?= $rowa['min'].' - '.$rowa['max']?>" src="images/delete.png" onclick="goDeleteAngs(this)" style="cursor:pointer">
											<? } ?>
											</span>
										</td>
									</tr>
									<?}}?>
									<tr>
										<td align="center" colspan="2"><b>Jumlah</b></td>
										<td align="right"><b><?= CStr::formatNumber($jmlpinjaman)?></b></td>
										<td colspan="3">&nbsp;</td>
									</tr>
								</table>
							</center>
							<? } ?>
							<input type="hidden" name="act" id="act">
							<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
							<input type="hidden" name="subkey" id="subkey">
							<input type="hidden" name="jmlangs" id="jmlangs" value="<?= $i?>">
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
	jmlPinjaman();
	
	$("input[name='namalengkap']").xautox({strpost: "f=acnamapegawaiunit", targetid: "idpeminjam", imgchkid: "imgid", imgavail: true});
});

function goSaveAngs(){
	if(cfHighlight('noawal,noakhir,jmlangsuran')){
		document.getElementById("act").value = "saveangs";
		goSubmit();
	}
}

function goDeleteAngs(elem){
	var hapus = confirm("Anda yakin untuk menghapus angsuran pinjaman?");
	if (hapus){
		document.getElementById("act").value = "deleteangs";
		document.getElementById("subkey").value = elem.id;
		goSubmit();
	}
}

function numberFormat(num) {
	var ret = '';
	var j = 0;
	valnum = num.value;
	
	if(valnum != ''){	
		valnum = String(valnum);
		valnum = valnum.replace(/\./g,'');
		for(i=valnum.length-1;i>=0;i--) {
			if(j == 3) {
				ret = "." + ret;
				j = 0;
			}
			ret = valnum.charAt(i) + ret;
			j++;
		}
		
		num.value = ret;
	}
}

function jmlPinjaman(){
	var posted = "f=gjmlpinjam&q[]="+$("#jmldisetujui").val()+"&q[]="+$("#biayaadministrasi").val();
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		$("#totalpinjaman").val(text);
	});
}

function numberFormat(num) {
	var ret = '';
	var j = 0;
	valnum = num.value;
	
	if(valnum != ''){	
		valnum = String(valnum);
		valnum = valnum.replace(/\./g,'');
		for(i=valnum.length-1;i>=0;i--) {
			if(j == 3) {
				ret = "." + ret;
				j = 0;
			}
			ret = valnum.charAt(i) + ret;
			j++;
		}
		
		num.value = ret;
	}
}
</script>
</body>
</html>
</html>