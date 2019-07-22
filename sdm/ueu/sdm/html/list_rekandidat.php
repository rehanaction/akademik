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
		
	$r_key = CStr::removeSpecial($_POST['key']);
	
	// properti halaman
	$p_title = 'Daftar Kandidat';
	$p_aktivitas = 'STRUKTUR';
	$p_detailpage = Route::getDetailPage();
	$p_dbtable = 're_calon';
	$where = 'nopendaftar';
	
	$p_model = mRekrutmen;
	
	// mendapatkan data
	$a_infore = array();
	$a_infore = mRekrutmen::getInformasiRE($conn,$r_key);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'set' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		$record = array();
		
		if ($a_infore['jenisrekrutmen'] == 'B'){
			$record['idrekrutmen'] = $r_key;
			
			list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_subkey,true,$p_dbtable,$where);
						
			$record['nopendaftar'] = $r_subkey;
			$record['idrekrutmen'] = $r_key;
			$proses = mRekrutmen::getArrProses($conn,$r_key);
			$record['idproses'] = $proses[0]['idproses'];
			
			list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,'re_prosesseleksi');
		}else{
			$record['idpegawai'] = $r_subkey;
			$record['idrekrutmen'] = $r_key;
			
			list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,'re_kandidat');
		}
	}
	else if($r_act == 'unset' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		if ($a_infore['jenisrekrutmen'] == 'B'){			
			$proses = mRekrutmen::getArrProses($conn,$r_key);
			$idproses = $proses[0]['idproses'];
			$where = 'idrekrutmen,nopendaftar,idproses';
			$p_key = $r_key.'|'.$r_subkey.'|'.$idproses;
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$p_key,'re_prosesseleksi',$where);
		}else{
			$where = 'idrekrutmen,idpegawai';
			$p_key = $r_key.'|'.$r_subkey;
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$p_key,'re_kandidat',$where);
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		if ($a_infore['jenisrekrutmen'] == 'B'){			
			$proses = mRekrutmen::getArrProses($conn,$r_key);
			$idproses = $proses[0]['idproses'];
			$where = 'idrekrutmen,nopendaftar,idproses';
			$p_key = $r_key.'|'.$r_subkey.'|'.$idproses;
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$p_key,'re_prosesseleksi',$where);
		}else{
			$where = 'idrekrutmen,idpegawai';
			$p_key = $r_key.'|'.$r_subkey;
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$p_key,'re_kandidat',$where);
		}
	}
	
	if ($a_infore['jenisrekrutmen'] == 'B'){
		$a_data = mRekrutmen::getKandidatBaru($conn,$r_key);
		$a_calon = mRekrutmen::listKandidatBaru($conn, $a_infore);
		$a_kandidat = mRekrutmen::getSudahKandidat($conn, $r_key);
		$a_sudahproses = mRekrutmen::getKandidatSudahProses($conn,$r_key);
	}else{
		$a_data = mRekrutmen::getKandidat($conn,$r_key);
		$a_calon = mRekrutmen::listKandidat($conn, $a_infore);
	}
	
	$a_unitrek = $p_model::getUnitRek($conn,$r_key);
	$a_jenisrekrutmen = mRekrutmen::jenisRekrutmen();

?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
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
				<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td colspan="9" align="center">Daftar Kandidat</td>
					</tr>
					<tr>
						<th><?= $a_infore['jenisrekrutmen'] == 'B' ? 'No. Pendaftar' : 'N I P';?></th>
						<th>Nama Kandidat</th>
						<th>Umur</th>
						<th>Pendidikan Terakhir</th>
						<th>Universitas</th>
						<th>IPK</th>
						<th width="50">Aksi</th>
					</tr>
				<?php
						$i = 0;
						if (count($a_data) >0){
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);

							$c_deletekandidat = $c_edit;
							if($a_infore['jenisrekrutmen'] == 'B' and in_array($row['nopendaftar'], $a_sudahproses))
								$c_deletekandidat = false;
				?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center"><?= $a_infore['jenisrekrutmen'] == 'B' ? $i : $row['idpegawai'];?></td>
						<td><?= $a_infore['jenisrekrutmen'] == 'B' ? '<a href="#" title="Klik untuk detail pelamar" onclick="showPelamar(\''.$row['nopendaftar'].'\')">'.$row['namalengkap'].'</a>' : $row['namalengkap'] ?></td>
						<td><?= $row['umurth'].' Th '.$row['umurmonth'].' Bln' ?></td>
						<td><?= $row['namapendidikan']. (!empty($row['jurusan']) ? ' ('.$row['jurusan'].')' : '')?></td>
						<td><?= $row['namainstitusi'] ?></td>
						<td><?= $row['ipk'] ?></td>
						<td align="center">
						<?	if($c_deletekandidat) { ?>
							<img id="<?= $a_infore['jenisrekrutmen'] == 'B' ? $row['nopendaftar'] : $row['idpegawai'];?>" style="cursor:pointer" onclick="goRemoveKandidat(this)" src="images/delete.png" title="Hapus Data">
						<?	} ?>
						</td>
					</tr>
				<?php
						}}else{
				?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td colspan="7" align="center">Data tidak ditemukan</td>
					</tr>
				<? } ?>
				</table>
				<br />
				<br />
				<br />
				<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td colspan="9" align="center">Daftar Calon Kandidat</td>
					</tr>
					<tr>
						<th><?= $a_infore['jenisrekrutmen'] == 'B' ? 'No. Pendaftar' : 'N I P';?></th>
						<th>Nama Calon Kandidat</th>
						<th>Umur</th>
						<th>Pendidikan Terakhir</th>
						<th>Universitas</th>
						<th>IPK</th>
						<th width="50">Aksi</th>
					</tr>
				<?php
						$i = 0;
						if (count($a_calon) >0){
						foreach($a_calon as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);

							if($a_infore['jenisrekrutmen'] == 'B' and in_array($row['nopendaftar'], $a_kandidat))
								$checked = 'checked="checked"';
							else
								$checked = '';

							$c_editcalon = $c_edit;
							if($a_infore['jenisrekrutmen'] == 'B' and in_array($row['nopendaftar'], $a_sudahproses))
								$c_editcalon = false;
				?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center"><?= $a_infore['jenisrekrutmen'] == 'B' ? $i : $row['idpegawai'];?></td>
						<td><?= $a_infore['jenisrekrutmen'] == 'B' ? '<a href="#" title="Klik untuk detail pelamar" onclick="showPelamar(\''.$row['nopendaftar'].'\')">'.$row['namalengkap'].'</a>' : $row['namalengkap'] ?></td>
						<td><?= $row['umurth'].' Th '.$row['umurmonth'].' Bln' ?></td>
						<td><?= $row['namapendidikan']. (!empty($row['jurusan']) ? '('.$row['jurusan'].')' : '') ?></td>
						<td><?= $row['namainstitusi'] ?></td>
						<td><?= $row['ipk'] ?></td>
						<td align="center">
						<?	if($c_editcalon) { ?>
							<input type="checkbox" value="<?= $a_infore['jenisrekrutmen'] == 'B' ? $row['nopendaftar'] : $row['idpegawai'];?>" onclick="goEntry(this)" <?= $checked ?>>
						<?	} else if(!empty($row['idrekrutmen'])) { ?>
							<img src="images/check.png">
						<?	} ?>
						</td>
					</tr>
				<?php
						}}else{
				?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td colspan="7" align="center">Data tidak ditemukan</td>
					</tr>
				<? } ?>
				</table>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
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
	
function goEntry(elem) {
	document.getElementById("act").value = (elem.checked ? 'set' : 'unset');
	document.getElementById("subkey").value = elem.value;
	
	goSubmit();
}

function goRemoveKandidat(elem) {
	var hapus = confirm("Apakah anda yakin akan menghapus data kandidat ini?");
	if(hapus) {
		document.getElementById("act").value = "delete";
		document.getElementById("subkey").value = elem.id;
		goSubmit();
	}
}

function showPelamar(nopendaftar){
	win = window.open("<?= Route::navAddress('pop_pelamar').'&key='?>"+nopendaftar,"pop_pelamar","width=950,height=800,scrollbars=1");
	win.focus();
}

</script>
</body>
</html>