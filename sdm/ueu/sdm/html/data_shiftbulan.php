<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_readlist = true;
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('presensi'));	
	require_once(Route::getModelPath('pegawai'));	
	require_once(Route::getUIPath('combo'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	$r_month = CStr::removeSpecial($_POST['bulan']);
	if (empty($r_month))
		$r_month = (int)date("m");
		
	$r_tahun = CStr::removeSpecial($_POST['tahun']);
	if (empty($r_tahun))
		$r_tahun = date("Y");
	
	if (!empty($r_key)){
		$r_month = (int)substr($r_key,4,2);
		$r_tahun = substr($r_key,0,4);
	}
				
	// properti halaman
	$p_title = 'Pengaturan Jadwal Per Bulan';
	$p_tbwidth = 700;
	$p_aktivitas = 'TIME';
	$p_listpage = Route::getListPage();
	$p_dbtable = 'pe_rwtshift';
	$p_key = 'kodeshift';
	
	$p_model = mPresensi;
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = $_POST['actdet'];
	if($r_act == 'simpan' and $c_edit) {		
		$record = array();
		$record['namashift'] = CStr::cStrNull($_POST['namashift']);
		
		if(empty($r_key)){
			$record['kodeshift'] = $p_model::setKodeShift($conn,$r_month,$r_tahun);
			list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,$p_dbtable);
		}else
			list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_key,true,$p_dbtable,$p_key);		
		
		if(!$p_posterr){
			if(empty($r_key))
				$r_key = $record['kodeshift'];
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {			
		$conn->StartTrans();
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteShiftBulan($conn,$r_key);
		$conn->CompleteTrans();
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if ($r_act == 'simpandet' and $c_edit){	
		$conn->StartTrans();
		$a_idpegawai = $_POST['idpegawai'];
		foreach($a_idpegawai as $inc => $idpegawai){
			$record = array();
			$record['idpegawai'] = $idpegawai;
			$record['kodeshift'] = $r_key;
			
			list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,'pe_rwtshiftpeg');
		}
		$conn->CompleteTrans();
	}
	else if($r_act == 'deletedet' and $c_delete) {		
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
			
		$conn->StartTrans();
		list($p_posterr,$p_postmsg) = $p_model::deleteDetailShiftBulan($conn,$r_key,$r_subkey);
		$conn->CompleteTrans();
	}
	else if($r_actdet == 'savedet' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		$record = array();
		$record['kodeshift'] = $r_key;
		$record['tglshift'] = $_POST['tglshift'];
		$record['jamdatang'] = CStr::cStrNull($_POST['jamdatang']);
		$record['jampulang'] = CStr::cStrNull($_POST['jampulang']);
		
		if(empty($r_subkey)){
			list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,'pe_rwtshiftdet');
		}else{
			$where = "kodeshift,tglshift";
			list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_subkey.'|'.$record['tglshift'],true,'pe_rwtshiftdet',$where);			
		}
		
		if(!$p_posterr){
			unset($post);
		}
	}
	else if($r_actdet == 'deletedet' and $c_delete) {		
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		$r_date = $_POST['tglshift'];
		$where = "kodeshift,tglshift";
			
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey.'|'.$r_date,'pe_rwtshiftdet',$where);
	}
	
	
	$a_data = $p_model::getDetailShiftBulan($conn, $r_key);
	$a_info = $a_data['info'];
	$a_date = $a_data['data'];
	$a_list = $a_data['list'];
		
	$l_bulan = uCombo::bulan($r_month,true,'bulan','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	$r_tawal = mktime(0,0,0,$r_month,1,$r_tahun);
	$r_takhir = mktime(0,0,0,$r_month+1,0,$r_tahun);
	
	$r_dawal = date('N',$r_tawal);
	$r_nakhir = date('d',$r_takhir);
	
	$t_tgl = 0; $fin = false;
	$a_tanggal = array();
	while(!$fin) {
		for($i=1;$i<=7;$i++) {
			if(!$fin and (!empty($t_tgl) or $i == $r_dawal))
				$t_tgl++;
			
			$a_tanggal[$i][] = $t_tgl;
			
			if($t_tgl >= $r_nakhir) {
				$t_tgl = 0;
				$fin = true;
			}
		}
	}
	
	$a_hari = Date::arrayDay();
	if (!empty($r_key)){
		$a_bulan = Date::arrayMonth();
		$periode = $a_bulan[(int)$a_info['bulan']].' '.$a_info['tahun']; 
	}else
		$periode = $l_bulan.' '.$l_tahun;
	
	$a_required = array("namashift");
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="scripts/foredit.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<center>
					<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
						<span>
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
							&nbsp;<?= $p_title.' '.$a_info['keterangan'] ?>
						</span>
					</div>
					<table border="0" cellspacing="10" align="center">
					<tr>
						<?	if($c_readlist) { ?>
						<td id="be_list" class="TDButton" onclick="goList()">
							<img src="images/list.png"> Daftar
						</td>
						<td id="be_save" class="TDButton" onclick="goSave()">
							<img src="images/disk.png"> Simpan
						</td>
						<?	} if($c_delete and !empty($r_key)) { ?>
						<td id="be_delete" class="TDButton" onclick="goDelete()">
							<img src="images/delete.png"> Hapus
						</td>
						<?	} ?>
					</tr>
				</table>
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
				<table cellspacing="0" cellpadding="4" width="<?= $p_tbwidth ?>" border="0">
					<tbody>
						<tr valign="top">
							<td width="120"><strong>Periode</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td width="40%"><?= $periode;?></td>
							<td width="120"><strong>Nama Shift *</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= UI::createTextBox('namashift',$a_info['namashift'],'ControlStyle',100,30,$c_edit); ?></td>
						</tr>	
					</tbody>
				</table>
				<br />
				<? if (!empty($r_key)){?>
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas; ?>.png" onerror="loadDefaultActImg(this)"> <h1><strong><?= strtoupper(Date::indoMonth($r_month)).' '.$r_tahun ?></strong></h1>
						</div>
					</div>
				</header>
				<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
				<table border="1" cellpadding="4" cellspacing="0" style="border:1px solid #999;border-collapse:collapse">
				<?	foreach($a_hari as $t_nohari => $t_hari) { ?>
					<tr>
						<td width="80"><strong><?= $t_hari ?></strong></td>
						<?	foreach($a_tanggal[$t_nohari] as $t_notgl) {
								$t_tgl = $r_tahun.'-'.str_pad($r_month,2,'0',STR_PAD_LEFT).'-'.str_pad($t_notgl,2,'0',STR_PAD_LEFT);
								
								echo UI::tdKegKalender($a_date[$t_tgl],$t_tgl,false,true,"openDetail('".$r_key."','".$a_date[$t_tgl]['kode']."','".$t_tgl."')",(!empty($t_notgl) and $t_notgl == $r_key));
							} ?>
					</tr>
				<?	} ?>
				</table>
				</div>
				<? } ?>
				<br />
				<? if ($c_edit and !empty($r_key)) {?>
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas; ?>.png" onerror="loadDefaultActImg(this)"> <h1>Entry Jadwal</h1>
						</div>
					</div>
				</header>
				<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
				<table width="<?= $p_tbwidth-22; ?>" cellspacing="0" cellpadding="4" class="bottomline">
					<tbody>
						<tr>
							<td valign="top"><b>Pegawai : </b></td>
							<td>
								<table width="100%" cellpadding="2" cellspacing="0">
									<tr>
										<td>
											<?= UI::createTextBox('pegawai[]','','ControlStyle',60,60,$c_edit)?>
											<input type="hidden" name="idpegawai[]" id="idpegawai"/>
											<img id="imgnik_c" src="images/green.gif"><img id="imgnik_u" src="images/red.gif" style="display:none">&nbsp;
										</td>
									</tr>
									<tr id="tr_tambah">
										<td colspan="2">&nbsp;</td>
									</tr>
									<tr>
										<td>
											<input type="button" name="badd" id="badd" value="Tambah Pegawai" onClick="goAdd()" />&nbsp;
											<input type="button" name="bsave" id="bsave" value="Simpan Jadwal" onClick="goSaveDetail()" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
				</div>
				<? } ?>
				<br />
				
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/PERSON.png" onerror="loadDefaultActImg(this)"> <h1>Daftar Pegawai</h1>
						</div>
					</div>
				</header>
				<table width="<?= $p_tbwidth?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr>
						<th width="50">No</th>
						<th width="100">NIP</th>
						<th>Nama</th>
						<th width="50">Aksi</th>
					</tr>
				<?php
						$i = 0;
						if (count($a_list) > 0){
						foreach($a_list as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="right"><?= $i ?></td>
						<td align="center"><?= $row['nik']; ?></td>
						<td align="left"><?= $row['namalengkap'] ?></td>	
						<td align="center">
							<? if ($c_delete) {?>
							<img src="images/delete.png" onClick="goRemove('<?= $row['idpegawai']; ?>')" style="cursor:pointer" />
							<? } ?>
						</td>
					</tr>
				<?php
						}}else{
				?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td colspan="12" align="center">Data tidak ditemukan</td>
					</tr>
				<? } ?>
				</table>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key; ?>">
				<input type="hidden" name="subkey" id="subkey">
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<? if (!empty($r_key)) { ?>
<table style="display:none">
	<tbody id="tb_template">
	<tr><td style="border:1px solid white">
		<?= UI::createTextBox('pegawai[]','','ControlStyle',60,60) ?>
		<input type="hidden" name="idpegawai[]" id="idpegawai">
		<img id="imgnik_c" src="images/green.gif" style="display:none"><img id="imgnik_u" src="images/red.gif">&nbsp;
	</td></tr>
	</tbody>
</table>
<? } ?>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript">

var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";	
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";
var detform = "<?= Route::navAddress('pop_shift') ?>";

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
		
	$("input[name='pegawai[]']").xautox({strpost: "f=acnamapegawai", targetid: "idpegawai", imgchkid: "imgnik", imgavail: true});
});

function openDetail(pkey, pkeydet, pdate){
    $.ajax({
        url: detform,
        type: "POST",
        data: {key : pkey, subkey : pkeydet, subdate : pdate},
        success: function(data){
            $.facybox(data);
        }
    });
}
	
function goAdd(){	
	var newtr = $($("#tb_template").html()).insertBefore("#tr_tambah");
	
	newtr.find("input[name='pegawai[]']").xautox({strpost: "f=acnamapegawai", targetid: "idpegawai", imgchkid: "imgnik", imgavail: true});
}

function goSave(){		
	var required = "<?= @implode(',',$a_required) ?>";
	var pass = true;
	
	if(typeof(required) != "undefined") {
		if(!cfHighlight(required))
			pass = false;
	}
	
	if(pass) {
		var set = confirm("Anda yakin untuk menyimpan jadwal ini ?");
		if (set){		
			document.getElementById("act").value = 'simpan';	
			goSubmit();	
		}
	}
}

function goSaveDetail(){			
	var set = confirm("Anda yakin untuk menyimpan pegawai ini ?");
	if (set){		
		document.getElementById("act").value = 'simpandet';	
		goSubmit();	
	}
}

function goRemove(key){
	var hapus = confirm("Anda yakin untuk menghapus pegawai ini ?");
	if (hapus){	
		document.getElementById("subkey").value = key;
		document.getElementById("act").value = 'deletedet';	
		goSubmit();
	}
}


</script>
</body>
</html>