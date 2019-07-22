<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('dinas'));	
	require_once(Route::getUIPath('combo'));
		
	$r_key = CStr::removeSpecial($_POST['key']);
	
	// properti halaman
	$p_title = 'Proses Validasi Tugas Dinas';
	$p_tbwidth = 800;
	$p_aktivitas = 'STRUKTUR';
	$p_detailpage = Route::getDetailPage();
	$p_printpage = 'rep_suratdinas';
	$p_printpagerate = 'rep_ratedinas';
	$p_dbtable = 'pe_rwtdinas';
	$where = 'refid';
	
	$p_model = mDinas;
		
	// mendapatkan data
	$a_info = array();
	$a_info = mDinas::getInformasi($conn,$r_key);
	
	$a_rate = array();
	$a_rate = $p_model::getRate($conn,$a_info['jnsrate']);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'simpan' and $c_edit) {	
		$a_nodinas = $_POST['no'];
		$where = 'nodinas';
		
		foreach($a_nodinas as $r_nodinas){
			$p_key = $r_nodinas;
			$record = array();
			$record['tgldicairkan'] = CStr::formatDate($_POST['tglcair_'.$r_nodinas]);
			$record['jmldicairkan'] = CStr::cStrNull($_POST['jmlcair_'.$r_nodinas]);
			
			list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$p_key,true,$p_dbtable,$where);
		}
	}
	else if ($r_act == 'simpanrate' and $c_edit){
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		$where = 'nodinas,idrate';
		
		if (count($a_rate) > 0){
			foreach($a_rate as $col){
				$p_key = $r_subkey.'|'.$col['idrate'];
				$record = array();
				$record['nodinas'] = $r_subkey;
				$record['idrate'] = $col['idrate'];
				$record['nominal'] = CStr::cStrNull($_POST['biaya_'.$r_subkey.'_'.$col['idrate']]);
				
				$isExist = $p_model::isDataExist($conn,$p_key,'pe_biayadinas',$where);
				
				if ($isExist)
					list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$p_key,true,'pe_biayadinas',$where);
				else{
					if ($record['nominal'] != 'null')
						list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,'pe_biayadinas');
				}
			}
		}
	}	
	else if ($r_act == 'setunit' and $c_edit){
		$record = array();
		$record['idunit'] = CStr::cStrNull($_POST['idunit']);
		$p_key = $r_key;
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$p_key,true,$p_dbtable,$where);
	}	
	
	$a_jenislokasirate = $p_model::jenisRate();
	
	$a_data = $p_model::listQueryPeserta($conn,$r_key);
	
	$a_biayarate = array();
	$a_biayarate = $p_model::getBiayaDinasKol($conn,$a_info['refid']);

?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<script type="text/javascript" src="scripts/foredit.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<center>
					<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
						<span>
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
							&nbsp;<?= $p_title ?>
						</span>
					</div>
				</center>
				<br>
				<center>
				<?php require_once('inc_header.php') ?>
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
							<td width="120"><strong>Anggaran Unit</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td width="40%"><?= UI::createSelect('idunit',mCombo::unitSave($conn,false),$a_info['idunit'],'ControlStyle',$c_edit,'style="width:200px"'); ?>&nbsp
							<input type="button" name="bset" id="bset" onClick="goSet()" class="ControlStyle" value="Set" />
							</td>
							<td width="120"><strong>Instansi</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= $a_info['instansi']; ?></td>
						</tr>					
						<tr valign="top">
							<td><strong>Tugas Dinas</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= $a_info['namakedinasan']; ?></td>
							<td><strong>Alamat</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= $a_info['alamat'].' ('.$a_jenislokasirate[$a_info['jnsrate']].')'; ?></td>
						</tr>		
						<tr valign="top">
							<td><strong>Dalam Rangka</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= $a_info['dalamrangka']; ?></td>
							<td><strong>Tgl. Dinas</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= CStr::formatDateInd($a_info['tglpergi']).' s/d '.CStr::formatDateInd($a_info['tglpulang']); ?></td>
						</tr>	
					</tbody>
				</table>
				<br />
				
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/PERSON.png" onerror="loadDefaultActImg(this)"> <h1>Peserta Kedinasan</h1>
						</div>
					</div>
				</header>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr>
						<th>No Surat</th>
						<th>Nama</th>
						<th width="120">Tgl. Realisasi Biaya</th>
						<th>Realisasi Biaya</th>
						<?	if($c_edit or $c_delete) { ?>
						<th width="100">Aksi</th>
						<?	} ?>
					</tr>
				<?php
						$i = 0;
						if (count($a_data) >0){
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row, 'nodinas');
				?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center"><?= $row['nosurat']; ?><input type="hidden" name="no[]" value="<?= $row['nodinas']; ?>" /></td>
						<td><?= $row['namalengkap'] ?></td>
						<td><?= UI::createTextBox('tglcair_'.$t_key,CStr::formatDate($row['tgldicairkan']),'ControlStyle',10,10,$c_edit); ?>
							<img src="images/cal.png" id="<?= 'tglcair_'.$t_key ?>_trg" style="cursor:pointer;" title="Pilih Tanggal Cair">
							<script type="text/javascript">
							Calendar.setup({
								inputField     :    "<?= 'tglcair_'.$t_key ?>",
								ifFormat       :    "%d-%m-%Y",
								button         :    "<?= 'tglcair_'.$t_key ?>_trg",
								align          :    "Br",
								singleClick    :    true
							});
							</script>
						</td>
						<td align="right"><?= UI::createTextBox('jmlcair_'.$t_key,$row['jmldicairkan'],'ControlStyle',10,10,$c_edit,'onKeyDown="return onlyNumber(event,this,true,true)"'); ?></td>
						<td align="center" valign="top">
							<input type="button" name="bdetail" id="bdetail" onClick="goShowBiaya('<?= $row['nodinas']; ?>')" class="ControlStyle" value="Detail" />&nbsp;
							<img src="images/print.png" onClick="goPrint('<?= $row['nodinas']; ?>')" style="cursor:pointer;width:30px" title="Print Surat Dinas" />
						</td>
					</tr>
					<tr id="template_<?= $row['nodinas'];  ?>" style="display:none">
						<td colspan="6" align="center">
							<br />
							<table width="<?= $p_tbwidth-200; ?>" cellspacing="0" cellpadding="4" class="GridStyle">
								<tr class="DataBG">
									<td valign="top">Biaya Rate Perjalanan</td><td align="right"><img src="images/print.png" onClick="goPrintRt('<?= $row['nodinas']; ?>')" style="cursor:pointer" title="Print Rate Perjalanan" /></td>
								</tr>
								<? if (count($a_rate) >0 ){ 
									foreach($a_rate as $col) {
										$nominal = '';
										$nominal = $a_biayarate[$row['nodinas']][$col['idrate']];
								?>
								<tr>
									<td class="LeftColumnBG" width="200"><?= $col['rateperjalanan']; ?><input type="hidden" name="idrate_<?= $col['idrate']?>"</td>
									<td class="RightColumnBG">Rp. <?= UI::createTextBox('biaya_'.$row['nodinas'].'_'.$col['idrate'],$nominal,'ControlStyle',14,14,$c_edit,'onkeydown="return onlyNumber(event,this,0,true);"'); ?></td>
								</tr>
								<? }} ?>
								<? if ($c_edit) {?>
								<tr>
									<td colspan="2" align="center"><input type="button" name="bsave" value="Simpan Rate" class="ControlStyle" style="cursor:pointer" onClick="goSaveRt('<?= $row['nodinas']; ?>')"></td>
								</tr>
								<? } ?>
							</table>
							<br />
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
				<br />
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" border="0">
					<tr>
						<td align="center">
							<? if ($c_edit) {?>
							<input type="button" name="bsave" value="Simpan Persetujuan" class="ControlStyle" style="cursor:pointer" onClick="goSave()">
							<? } ?>
						</td>
					</tr>
				</table>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key; ?>">
				<input type="hidden" name="subkey" id="subkey">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});
	
function goSave() {
	document.getElementById("act").value = 'simpan';	
	goSubmit();
}

function goSaveRt(subkey) {
	var set = confirm("Anda yakin untuk menyimpan biaya rate perjalanan ini ?");
	if (set){
		document.getElementById("act").value = 'simpanrate';	
		document.getElementById("subkey").value = subkey;	
		goSubmit();
	}
}

function goSet(){
	var set = confirm("Anda yakin untuk memindahkan anggaran ke unit ini ?");
	if (set){
		document.getElementById("act").value = 'setunit';	
		goSubmit();
	}
}

function goShowBiaya(id){
	var tr = "template_"+id;
	$("#template_"+id).fadeToggle("slow");
}

function goPrint(id) {
	goShowPage(id,'<?= Route::navAddress($p_printpage) ?>');
}

function goPrintRt(id) {
	goShowPage(id,'<?= Route::navAddress($p_printpagerate) ?>');
}

</script>
</body>
</html>