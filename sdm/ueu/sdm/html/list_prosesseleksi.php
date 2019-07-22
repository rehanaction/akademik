<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('rekrutmen'));	
	require_once(Route::getModelPath('email'));
		
	$r_key = CStr::removeSpecial($_POST['key']);
	
	// properti halaman
	$p_title = 'Proses Seleksi Penerimaan Pegawai';
	$p_aktivitas = 'STRUKTUR';
	$p_detailpage = Route::getDetailPage();
	$p_dbtable = 're_prosesseleksi';
	$where = 'nopendaftar';
	
	$p_model = mRekrutmen;
	
	// mendapatkan data
	$a_infore = array();
	$a_infore = mRekrutmen::getInformasiRE($conn,$r_key);
	
	$r_step = CStr::removeSpecial($_POST['step']);
	if (empty($r_step))
		$r_step = 1;
	
	$r_proses = $p_model::getMekanisme($conn, $r_step, $r_key);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'simpan' and $c_edit) {	
		$a_id = $_POST['id'];
		$where = 'idrekrutmen,nopendaftar,idproses';
		
		foreach($a_id as $id){
			$record = array();
			$record['statusseleksi'] = CStr::cStrNull($_POST['lolos_'.$id]);
			$record['tglproses'] = CStr::formatDate($_POST['tglproses_'.$id]);
			$record['keterangan'] = CStr::cStrNull($_POST['keterangan_'.$id]);
			$p_key = $r_key.'|'.$id.'|'.$r_proses;
			
			list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$p_key,true,$p_dbtable,$where);

			//email ke pelamar yang gagal
			if(!$p_posterr and $record['statusseleksi'] == 'G')
				mEmail::gagalRekrutmen($conn,$id);
		}
	}
	else if($r_act == 'nextstep' and $c_edit) {
		$r_cek = $_POST['cek'];
		
		if (count($r_cek) > 0){
			foreach($r_cek as $r_kandidat){
				$record = array();
				$record['idproses'] = $r_proses;
				$record['idrekrutmen'] = $r_key;
				$record['nopendaftar'] = $r_kandidat;
				
				$isExist = $p_model::cekKandidatProses($conn,$record);
				
				if (!$isExist)
					list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,'',$p_dbtable);
			}
		}
	}
	else if($r_act == 'prevstep' and $c_edit) {
		$where = 'idrekrutmen,nopendaftar,idproses';
		$r_cek = $_POST['cek'];
			
		$r_prevproses = $p_model::getMekanisme($conn, ($r_step+1), $r_key);
		if (count($r_cek) > 0){
			foreach($r_cek as $r_kandidat){
				$p_key = $r_key.'|'.$r_kandidat.'|'.$r_prevproses;
				list($p_posterr,$p_postmsg) = $p_model::delete($conn,$p_key,$p_dbtable,$where);
			}
		}
	}
	
	if(empty($r_proses)){
		$p_posterr = true;
		$p_postmsg = "Silahkan isi terlebih dahulu Proses Seleksi pada Menu Permintaan Pegawai";
		$c_edit = $c_delete = false;
	}

	if ($a_infore['jenisrekrutmen'] == 'B' and !empty($r_proses)){
		$a_lolosproses = $p_model::getLolosProses($conn,$r_key, $r_proses);
		$a_data = $p_model::getProsesSeleksiBaru($conn,$r_key, $r_proses,$a_lolosproses);
	}
	else if ($a_infore['jenisrekrutmen'] != 'B'){
		$a_data = $p_model::getKandidat($conn,$r_key);
	}
	
	$a_unitrek = $p_model::getUnitRek($conn,$r_key);
	$a_jenisrekrutmen = $p_model::jenisRekrutmen();
	$a_proses = array();
	$a_proses = $p_model::getArrProses($conn,$r_key);
	
	$a_lolos = $p_model::statusLulus();
				
	if ($r_step == 1){
		$class_1 = "active-step";
	}
	else if ($r_step == 2){
		$class_1 = "completed-step";
		$class_2 = "active-step";
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/wizard.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
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
				<table cellspacing="0" cellpadding="4" width="100%" border="0">
					<tbody>			
						<tr valign="top">
							<td width="200"><strong>Jenis Rekrutmen</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= $a_jenisrekrutmen[$a_infore['jenisrekrutmen']]; ?></td>
							<td width="200"><strong>Posisi</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= $a_infore['namaposisi']; ?></td>
						</tr>
						<tr valign="top">
							<td><strong>Tgl. Permintaan</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= CStr::formatDateInd($a_infore['tglrekrutmen']); ?></td>
							<td><strong>Tgl. Penutupan</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= CStr::formatDateInd($a_infore['tglterakhir']); ?></td>
						</tr>		
						<tr valign="top">
							<td><strong>Jumlah Dibutuhkan</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= $a_infore['jmldibutuhkan']; ?></td>
							<td><strong>Uraian Tugas</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= $a_infore['tugaskaryawan']; ?></td>
						</tr>				
						<tr valign="top">
							<td><strong>Unit yang Membutuhkan</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td colspan="4">
							<?
								if(count($a_unitrek)){
									foreach ($a_unitrek as $kunit => $vunit)
										echo '- '.$vunit.'<br>';
								}
							?>								
							</td>
						</tr>	
					</tbody>
				</table>
				<br />
				
				<div class="wizard-steps">
					<table cellspacing="0" cellpadding="4">
						<tr>
							<? foreach($a_proses as $inc => $proses) {
								if ($r_step == $proses['urutan']){
									$r_proses = $proses['idproses'];
									$class = "active-step";
								}else{
									if ($r_step > $proses['urutan'])
										$class = "completed-step";
									else
										$class = "";
								}
							?>
								<td align="center">
									<div class="<?= $class ?>"><a href="#" onClick="goStep('<?= $proses['urutan']; ?>')"><span><?= $proses['urutan']; ?></span><?= $proses['namaproses']; ?></a></div>
								</td>
							<? } ?>
						</tr>
					</table>
				</div>
				<br />
				<? if ($c_edit) {?>
				<table>
					<tr><td><input type="button" name="simpan" value="Simpan Proses" class="ControlStyle" style="cursor:pointer" onClick="goSave()"></td></tr>
				</table>
				<br />
				<? } ?>
				<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td colspan="9" align="center">Peserta Seleksi</td>
					</tr>
					<tr>
						<th><?= $a_infore['jenisrekrutmen'] == 'B' ? 'No. Pendaftar' : 'N I P';?></th>
						<th>Nama Peserta Seleksi</th>
						<th>Pendidikan</th>
						<th width="60px">Lolos</th>
						<th>Tgl. Proses</th>
						<th>Keterangan</th>
						<th width="50">Aksi</th>
					</tr>
				<?php
						$i = 0;
						if (count($a_data) >0){
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							$idkandidat = $a_infore['jenisrekrutmen'] == 'B' ? $row['nopendaftar'] : $row['idpegawai'];
				?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center">
							<?= $a_infore['jenisrekrutmen'] == 'B' ? $i : $row['idpegawai'];?>
							<input type="hidden" name="id[]" value="<?= $idkandidat; ?>" />
						</td>
						<td><?= $a_infore['jenisrekrutmen'] == 'B' ? '<a href="#" title="Klik untuk detail pelamar" onclick="showPelamar(\''.$row['nopendaftar'].'\')">'.$row['namalengkap'].'</a>' : $row['namalengkap'] ?></td>
						<td><?= $row['namapendidikan'] ?></td>
						<td><?= UI::createRadio('lolos_'.$idkandidat,$a_lolos,$row['statusseleksi'],$c_edit); ?></td>
						<td><?= UI::createTextBox('tglproses_'.$idkandidat,CStr::formatDate($row['tglproses']),'ControlStyle',10,10,$c_edit) ?>
							<? if ($c_edit){ ?>
							<img src="images/cal.png" id="tglproses_<?= $idkandidat; ?>_trg" style="cursor:pointer;" title="Pilih Proses">
							<script type="text/javascript">
							Calendar.setup({
								inputField     :    "tglproses_<?= $idkandidat; ?>",
								ifFormat       :    "%d-%m-%Y",
								button         :    "tglproses_<?= $idkandidat; ?>_trg",
								align          :    "Br",
								singleClick    :    true
							});
							</script>
							<? } ?>
						</td>
						<td><?= UI::createTextArea('keterangan_'.$idkandidat,$row['keterangan'],'ControlStyle',3,30,$c_edit) ?></td>
						<td align="center">
						<?	if($c_edit and $row['statusseleksi'] != 'G') { ?>
							<input type="checkbox" value="<?= $a_infore['jenisrekrutmen'] == 'B' ? $row['nopendaftar'] : $row['idpegawai'];?>" name="cek[]">
						<?	} ?>
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
				<? if ($c_edit) {?>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" border="0">
					<tr>
						<td align="right">
							<? if ($r_step != 1){?>
							<input type="button" name="bprev" value="<< Proses Sebelumnya" class="ControlStyle" style="cursor:pointer" onClick="goPrevStep('<?= $r_step; ?>')">
							<? }if ($r_step != count($a_proses)) {?>
							<input type="button" name="bnext" value="Proses Selanjutnya >>" class="ControlStyle" style="cursor:pointer" onClick="goNextStep('<?= $r_step; ?>')">
							<? } ?>
						</td>
					</tr>
				</table>
				<? } ?>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key; ?>">
				<input type="hidden" name="step" id="step" value="<?= $r_step; ?>">
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

function goNextStep(step){
    if ($("[name='cek[]']:checked").length > 0) {
		var confirmasi = confirm("Apakah anda yakin akan melanjutkan proses selanjutnya untuk Calon Pegawai tercentang?");
		if(confirmasi) {
			var step = parseInt(step);
			step += 1;
			document.getElementById("act").value = "nextstep";
			document.getElementById("step").value = step;
			goSubmit();
		}
	}else
		alert("Silahkan centang data Calon Pegawai yang ingin diproses terlebih dahulu");
}

function goPrevStep(step){
    if ($("[name='cek[]']:checked").length > 0) {
		var confirmasi = confirm("Apakah anda yakin akan mengembalikan ke proses sebelumnya untuk Calon Pegawai tercentang?");
		if(confirmasi) {
			var step = parseInt(step);
			step -= 1;
			document.getElementById("act").value = "prevstep";
			document.getElementById("step").value = step;
			goSubmit();
		}
	}else
		alert("Silahkan centang data Calon Pegawai yang ingin diproses terlebih dahulu");
}

function goStep(step){
	var step = parseInt(step);
	document.getElementById("step").value = step;
	goSubmit();
}

function showPelamar(nopendaftar){
	win = window.open("<?= Route::navAddress('pop_pelamar').'&key='?>"+nopendaftar,"pop_pelamar","width=950,height=800,scrollbars=1");
	win.focus();
}

</script>
</body>
</html>