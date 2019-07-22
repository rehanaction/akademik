<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kenaikan'));
	require_once(Route::getModelPath('riwayat'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Kenaikan Pangkat Berkala';
	$p_tbwidth = 650;
	$p_aktivitas = 'DATA';
	$p_dbtable = 'pe_kpb';
	$p_key = 'nokpb';
	$p_listpage = Route::getListPage();
	
	$p_model = mKenaikan;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	if(empty($r_key)){
		$a_input[] = array('kolom' => 'tglkpb', 'label' => 'Tgl. Kenaikan', 'type' => 'D', 'notnull' => true, 'default' => date('Y-m-d'));
		$a_input[] = array('kolom' => 'pegawai', 'label' => 'Nama Pegawai', 'maxlength' => 100, 'size' => 50, 'notnull' => true);
	}else{
		$a_input[] = array('kolom' => 'tglkpb', 'label' => 'Tgl. Kenaikan', 'type' => 'D', 'readonly' => true);
		$a_input[] = array('kolom' => 'pegawai', 'label' => 'Nama Pegawai', 'maxlength' => 100, 'size' => 50, 'readonly' => true);
	}
	
	$a_input[] = array('kolom' => 'idpegawai', 'type' => 'H');
	$a_input[] = array('kolom' => 'namaunit', 'label' => 'Unit Kerja', 'readonly' => true);
	$a_input[] = array('kolom' => 'pangkatlama', 'label' => 'Pangkat Lama', 'readonly' => true);
	$a_input[] = array('kolom' => 'tmtpangkatlama', 'label' => 'TMT. Pangkat Lama', 'type' => 'D', 'readonly' => true);
	$a_input[] = array('kolom' => 'mklama', 'label' => 'Masa Kerja Lama', 'readonly' => true);
	$a_input[] = array('kolom' => 'idpangkat', 'label' => 'Pangkat Baru', 'type' => 'S', 'option' => mRiwayat::namaPangkat($conn));
	$a_input[] = array('kolom' => 'tmtpangkat', 'label' => 'TMT. Pangkat', 'type' => 'D');
	$a_input[] = array('kolom' => 'mkthn', 'label' => 'Masa Kerja Baru', 'maxlength' => 2, 'size' => 2, 'type' => 'N');
	$a_input[] = array('kolom' => 'mkbln', 'maxlength' => 2, 'size' => 2, 'type' => 'N');
	
	if(Modul::getRole() == 'A' or Modul::getRole() == 'admhrm')
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	else
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid(), 'readonly' => true);
	
	//persetujuan
	$a_input[] = array('kolom' => 'issetuju', 'label' => 'Setujui', 'type' => 'R', 'option' => $p_model::isSetuju());
	$a_input[] = array('kolom' => 'tglpersetujuan', 'label' => 'Tgl. Persetujuan', 'type' => 'D');
	$a_input[] = array('kolom' => 'alasan', 'label' => 'Alasan', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$pktold = $p_model::getPKTLama($conn,$record['idpegawai']);
		if(count($pktold) > 0)
			$record = array_merge($record,$pktold);
			
		if($record['mkthn'] != 'null' and $record['mkbln'] != 'null')
			$record['mkg'] = str_pad($record['mkthn'],2,'0',STR_PAD_LEFT).str_pad($record['mkbln'],2,'0',STR_PAD_LEFT);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'ceknaik' and $c_edit) {
		$r_peg = CStr::removeSpecial($_POST['idpegawai']);
		$r_tgl = CStr::removeSpecial($_POST['tglkpb']);
	
		$a_naik = $p_model::isNaikPangkat($conn,$r_peg,$r_tgl);
		
		if(!empty($a_naik['kpb'])){
			$p_posterr = true;
			$p_postmsg = 'Pegawai tersebut sudah dilakukan proses kenaikan pangkat untuk bulan ini';
		}
		if(empty($a_naik['row'])){
			$p_posterr = true;
			$p_postmsg = 'Pegawai tersebut belum waktunya untuk naik pangkat';
		}
		if($a_naik['isnaik']){
			$isnaik = true;
			$sql = $a_naik['sql'];
			$r_key = $r_peg;
		}
	}
	
	if(!$isnaik)
		$sql = $p_model::getDataKPB($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key,$sql);
	
	//pengecekan bila cek apakah naik
	$r_key = $isnaik == true ? '' : $r_key;
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
	
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
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
				<?					
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
						<tr>
							<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglkpb') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tglkpb') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pegawai') ?></td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'pegawai') ?>
								<?= Page::getDataInput($row,'idpegawai') ?>	
								<?if(empty($r_key)){?>
								<span id="edit" style="display:none">
									<img id="imgpeg_c" src="images/green.gif">
									<img id="imgpeg_u" src="images/red.gif" style="display:none">
								</span>
								&nbsp;
								<input type="button" value="Cek Pegawai" class="ControlStyle" onClick="goCek()">
								<?}?>
							</td>
						</tr>
						<?if(!empty($r_key) or $isnaik){?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namaunit') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'namaunit') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pangkatlama') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'pangkatlama') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tmtpangkatlama') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tmtpangkatlama') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'mklama') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'mklama') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tmtpangkat') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tmtpangkat') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idpangkat') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'idpangkat') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'mkthn') ?></td>
							<td  class="RightColumnBG">
								<?= Page::getDataInput($row,'mkthn') ?>&nbsp;tahun
								<?= Page::getDataInput($row,'mkbln') ?>&nbsp;bulan
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isvalid') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'isvalid') ?></td>
						</tr>
						<tr height="30">
							<td colspan="2" class="DataBG">Persetujuan</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'issetuju') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'issetuju') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglpersetujuan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tglpersetujuan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'alasan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'alasan') ?></td>
						</tr>
						<?}?>
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
	
	//autocomplete
	$("#pegawai").xautox({strpost: "f=acnamapegawai", targetid: "idpegawai", imgchkid: "imgpeg", imgavail: true});
});

function goCek(){
	$('#act').val('ceknaik');
	goSubmit();
}
</script>
</body>
</html>
