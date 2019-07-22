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
	$p_title = "Data Pembayaran Pinjaman";
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
	$a_input[] = array('kolom' => 'jnspinjaman', 'label' => 'Jenis Pinjaman', 'readonly' => true);
	$a_input[] = array('kolom' => 'namalengkap', 'label' => 'Nama Pegawai', 'readonly' => true);
	$a_input[] = array('kolom' => 'keperluan', 'label' => 'Keperluan', 'readonly' => true);
	$a_input[] = array('kolom' => 'noperjanjian', 'label' => 'No Perjanjian', 'readonly' => true);
	$a_input[] = array('kolom' => 'tglperjanjian', 'label' => 'Tgl. Perjanjian', 'readonly' => true, 'type' => 'D');
	$a_input[] = array('kolom' => 'jmldisetujui', 'label' => 'Jml. Pinjaman disetujui', 'readonly' => true, 'type' => 'N');
	$a_input[] = array('kolom' => 'jmlcicilandisetujui', 'label' => 'Jml. Cicilan disetujui', 'type' => 'N', 'readonly' => true);
	$a_input[] = array('kolom' => 'biayaadministrasi', 'label' => 'Biaya Administrasi', 'type' => 'N', 'readonly' => true);
	$a_input[] = array('kolom' => 'totalpinjaman', 'label' => 'Total Pinjaman', 'type' => 'N', 'readonly' => true);
	$a_input[] = array('kolom' => 'saldo', 'label' => 'Saldo Pinjaman', 'type' => 'N', 'readonly' => true);
	
	$r_act = $_POST['act'];
	if($r_act == 'simpanangs' and $c_edit) {
		$r_subkey = $_POST['subkey'];
		
		$record = array();
		if(!empty($_POST['istunda'.$r_subkey])){
			$record['isdibayar'] = 'T';
			$record['periodegajitunda'] = $_POST['tahun'.$r_subkey].(str_pad($_POST['bulan'.$r_subkey], 2, "0", STR_PAD_LEFT));
			$record['keterangan'] = $_POST['keterangan'.$r_subkey];
		}else{
			$record['isdibayar'] = 'N';
			$record['periodegajitunda'] = 'null';
			$record['keterangan'] = 'null';
		}
		
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn, $record, $r_key.'|'.$r_subkey, true, 'pe_angsuran','idpinjaman,noangsuran');
	}
	
	$sql = $p_model::getDataEditPembayaran($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key,$sql);
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
	
	//daftar angsuran
	$rsa = $p_model::getAngsuranPinj($conn,$r_key);
	$a_status = $p_model::statusBayar();
	$a_bulan = Date::arrayMonth();
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
								
								if(empty($p_fatalerr)){
							?>
									<table border="0" cellspacing="10" align="center">
										<tr>
											<?	if($c_readlist) { ?>
											<td id="be_list" class="TDButton" onclick="goList()">
												<img src="images/list.png"> Daftar
											</td>
											<?	} if($c_edit) { ?>
										   <td id="be_edit" class="TDButton" onclick="goEdit()">
												<img src="images/edit.png"> Sunting
											</td>
											<td id="be_undo" class="TDButton" onclick="goUndo()" style="display:none">
												<img src="images/undo.png"> Batal
											</td>
											<?	} ?>
										</tr>
									</table>
							<?
								}
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
											<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title; ?></h1>
										</div>
									</div>
								</header>
								<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
								<table width="100%" cellspacing="0" cellpadding="4" class="bottomline">
									<tbody>
										<tr>
											<td width="200"><?= Page::getDataLabel($row,'jnspinjaman') ?></td>
											<td>:</td>
											<td><?= Page::getDataValue($row,'jnspinjaman') ?></td>
										</tr>
										<tr>
											<td><?= Page::getDataLabel($row,'namalengkap') ?></td>
											<td>:</td>
											<td><?= Page::getDataValue($row,'namalengkap') ?></td>
										</tr>
										<tr>
											<td><?= Page::getDataLabel($row,'keperluan') ?></td>
											<td>:</td>
											<td><?= Page::getDataInput($row,'keperluan') ?></td>
										</tr>
										<tr>
											<td><?= Page::getDataLabel($row,'noperjanjian') ?></td>
											<td>:</td>
											<td><?= Page::getDataInput($row,'noperjanjian') ?></td>
										</tr>
										<tr>
											<td><?= Page::getDataLabel($row,'tglperjanjian') ?></td>
											<td>:</td>
											<td><?= Page::getDataInput($row,'tglperjanjian') ?></td>
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
											<td><?= Page::getDataLabel($row,'saldo') ?></td>
											<td>:</td>
											<td><?= Page::getDataInput($row,'saldo') ?></td>
										</tr>
									</tbody>
								</table>
								</div>
							</center>
							<br>
							<center>
								<table class="GridStyle" width="<?= $p_tbwidth?>" cellspacing="2" cellpadding="4" align="center">
									<tr>
										<td class="DataBG" colspan="8">Detail Angsuran Pinjaman</td>
									</tr>
									<tr>
										<th class="HeaderBG" align="center">Angsuran Ke</th>
										<th class="HeaderBG" align="center">Jumlah Angsuran</th>
										<th class="HeaderBG" align="center">Status</th>
										<th class="HeaderBG" align="center">Ditunda?</th>
										<th class="HeaderBG" align="center">Periode Gaji Tunda</th>
										<th class="HeaderBG" align="center">Keterangan</th>
										<th class="HeaderBG" align="center">No. Bukti Bayar</th>
										<th id="edit" class="HeaderBG" width="50" align="center" style="">Aksi</th>
									</tr>
									<?
										$i = 0;
										while($rowa = $rsa->FetchROw()){
											if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
											$jmlangsuran += $rowa['jmlangsuran'];
											$no = $rowa['noangsuran'];
									?>
									<tr valign="top" class="<?= $rowstyle ?>">
										<td align="center"><?= $rowa['noangsuran']?></td>
										<td align="right"><?= CStr::formatNumber($rowa['jmlangsuran'])?></td>
										<td align="center" bgcolor="<?= $rowa['isdibayar'] == 'Y' ? 'green' : ($rowa['isdibayar'] == 'T' ? 'yellow' : '')?>">
											<?= $a_status[$rowa['isdibayar']]?>
										</td>
										<td align="center">
											<span id="show"><?= $rowa['isdibayar'] == 'T' ? '<img src="images/check.png">' : ''?></span>
											<span id="edit" style="display:none">
												<input type="checkbox" name="istunda<?=$no?>" id="istunda<?=$no?>" value="<?=$no?>" <?= $rowa['isdibayar'] == 'T' ? 'checked="checked"' : ''?> onClick="toggle(this)">
											</span>
										</td>
										<td align="center">
											<span id="show"><?= $a_bulan[$rowa['bulan']].' '.$rowa['tahun']?></span>
											<span id="edit" style="display:none">
												<span id="edit<?=$no?>" <?= $rowa['isdibayar'] == 'T' ? '' : 'style="display:none"'?>>
												<?= UI::createSelect('bulan'.$no,$a_bulan,$rowa['bulan'],'ControlStyle',$c_edit and $rowa['isdibayar'] != 'Y')?>
												<?= UI::createTextBox('tahun'.$no,$rowa['tahun'],'ControlStyle',4,4,$c_edit and $rowa['isdibayar'] != 'Y');?>
												</span>
											</span>
										</td>
										<td align="center">
											<span id="show"><?= $rowa['keterangan']?></span>
											<span id="edit" style="display:none">
												<span id="edit<?=$no?>" <?= $rowa['isdibayar'] == 'T' ? '' : 'style="display:none"'?>>
												<?= UI::createTextBox('keterangan'.$no,$rowa['keterangan'],'ControlStyle',100,25,$c_edit and $rowa['isdibayar'] != 'Y');?>
												</span>
											</span>
										</td>
										<td align="center"><?= $rowa['nobkm']?></td>
										<td align="center">
											<span id="edit" style="display:none">
											<?if($c_edit and $rowa['isdibayar'] != 'Y'){?>
												<img id="save<?= $no?>" style="cursor:pointer" onclick="goSimpanAngs(<?= $no?>)" src="images/disk.png" title="Simpan Angsuran">
											<?}?>
											</span>
										</td>
									</tr>
									<?}?>
									<tr>
										<td align="center"><b>Jumlah</b></td>
										<td align="right"><b><?= CStr::formatNumber($jmlangsuran)?></b></td>
										<td colspan="6">&nbsp;</td>
									</tr>
								</table>
							</center>
							<? } ?>
							<input type="hidden" name="act" id="act">
							<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
							<input type="hidden" name="detail" id="detail">
							<input type="hidden" name="subkey" id="subkey">
						</form>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";
var detailreq = "<?= @implode(',',$a_detailreq) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
});

function goSimpanAngs(no){
	if(document.getElementById("istunda"+no).checked == true){
		if(cfHighlight('tahun'+no)){
			document.getElementById("act").value = "simpanangs";
			document.getElementById("subkey").value = no;
			goSubmit();
		}
	}else{
		document.getElementById("act").value = "simpanangs";
		document.getElementById("subkey").value = no;
		goSubmit();
	}
}

function toggle(elem) {
	var check = elem.checked;
	var id = elem.value;
	
	if(check == true)
		$("[id='edit"+id+"']").show();
	else
		$("[id='edit"+id+"']").hide();
}
</script>
</body>
</html>
</html>