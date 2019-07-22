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
	require_once(Route::getModelPath('integrasi'));	
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getModelPath('email'));	
	require_once(Route::getUIPath('combo'));
		
	$r_key = CStr::removeSpecial($_POST['key']);
	
	// properti halaman
	$p_title = 'Daftar Hasil Seleksi';
	$p_aktivitas = 'STRUKTUR';
	$p_detailpage = Route::getDetailPage();
	$p_dbtable = 're_prosesseleksi';
	$where = 'nopendaftar';
	
	$p_model = mRekrutmen;
	
	// mendapatkan data
	$a_infore = array();
	$a_infore = mRekrutmen::getInformasiRE($conn,$r_key);

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'generatenip' and $c_edit) {
		$conn->BeginTrans();

		$r_subkey = CStr::removeSpecial($_POST['subkey']);

		if ($a_infore['jenisrekrutmen'] == 'B'){
			global $conf;
			
			/****************** start of biodata ********************/
			$a_col = array();
			$a_col = $p_model::getPelamarBaru($conn, $r_subkey);

			if($a_col['kodeposisi'] == 'DT')
				$unit = mRekrutmen::unitDosen($conn,$r_key);
			else
				$unit = mRekrutmen::getIDUnit($conn,$_POST['unit_'.$r_subkey]);
		
			$record = array();
			$record = $a_col;
			$record['nip'] = mPegawai::createNIP($conn,$a_infore['idmilikpeg']);
			$record['jeniskelamin'] = $a_col['sex'];
			$record['idunit'] = $unit;
			$record['idtipepeg'] = $a_infore['idtipepeg'];
			$record['idjenispegawai'] = $a_infore['idjenispeg'];
			$record['idkelompok'] = $a_infore['idkelompok'];
			$record['idhubkerja'] = 'HK';
			$record['idstatusaktif'] = 'AA';
			
			list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,'ms_pegawai','','',true,$idpegawai);

			//copy file
			if(!$p_posterr){
				$source = $conf['uploads_portal'].'fotopelamar/'.$a_col['refpelamar'].'.jpg';
				$dest = $conf['uploads_dir'].'fotopeg/'.$idpegawai.'.jpg';
				if(Route::isUrlFileExist($source))
					copy($source, $dest);
			}
			
			if(!$p_posterr){
				$recpelamar = array();
				$recpelamar['refidpeg'] = $idpegawai;
				$recpelamar['tglditerimapegawai'] = date("Y-m-d");
				$recpelamar['statuslulus'] = 'L';
				$recpelamar['idunitlamar'] = $unit;

				$where = 'idrekrutmen,nopendaftar';
				$p_key = $r_key.'|'.$r_subkey;
				
				list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$recpelamar,$p_key,true,'re_calon',$where);
			}

			if (!$p_posterr){
				$p_posterr = mIntegrasi::saveRoleGate($conn,$idpegawai);
				if($p_posterr)
					$p_postmsg = 'Penyimpanan User Role ke Gate gagal';
			}

			/****************** end of biodata ********************/

			//jabatan akademik
			if (!$p_posterr and $a_col['kodeposisi'] == 'DT' and !empty($a_col['tmtjabatan']) and !empty($a_col['idjfungsional'])){
				$recordakad = array();
				$recordakad['idpegawai'] = $idpegawai;
				$recordakad['tmtmulai'] = $a_col['tmtjabatan'];
				$recordakad['idjfungsional'] = $a_col['idjfungsional'];
				$recordakad['filefungsional'] = $a_col['filejabakademik'];
				$recordakad['jenisjabatan'] = 'L';
				$recordakad['isvalid'] = 'Y';

				list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$recordakad,true,'pe_rwtfungsional','','',true,$keyakad);

				//copy file
				if(!$p_posterr){
					$source = $conf['uploads_portal'].'filejabakademik/'.$a_col['refpelamar'];
					$dest = $conf['uploads_dir'].'d0c/filejabakademik/'.$keyakad;
					if(Route::isUrlFileExist($source))
						copy($source, $dest);
				}
			}

			if (!$p_posterr){
				$a_colpend = array();
				$a_colpend = $p_model::getRPendidikan($conn, $r_subkey);
				if (count($a_colpend) > 0){
					foreach ($a_colpend as $row){
						$recordpend = array();
						$recordpend = $row;
						$recordpend['idpegawai'] = $idpegawai;
						$recordpend['fileijazah'] = $row['fileijazahpelamar'];
						$recordpend['filetranskrip'] = $row['filetranskrippelamar'];
						$recordpend['isvalid'] = 'Y';

						list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$recordpend,true,'pe_rwtpendidikan','','',true,$keypend);
						if($p_posterr)
							break;
						else{
							//copy file
							$source = $conf['uploads_portal'].'fileijazahpelamar/'.$row['refnopendpelamar'];
							$dest = $conf['uploads_dir'].'d0c/fileijazah/'.$keypend;
							if(Route::isUrlFileExist($source))
								copy($source, $dest);

							$source = $conf['uploads_portal'].'filetranskrippelamar/'.$row['refnopendpelamar'];
							$dest = $conf['uploads_dir'].'d0c/filetranskrip/'.$keypend;
							if(Route::isUrlFileExist($source))
								copy($source, $dest);
						}
					}				
				}
			}

			if (!$p_posterr){
				$a_colpkj = array();
				$a_colpkj = $p_model::getRPengalamanKerja($conn, $r_subkey);
				if (count($a_colpkj) > 0){
					foreach ($a_colpkj as $row){
						$recordpkj = array();
						$recordpkj = $row;
						$recordpkj['lokasiinstansi'] = $row['alamatinstansi'];
						$recordpkj['idpegawai'] = $recpelamar['refidpeg'];
						$recordpkj['isvalid'] = 'Y';

						list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$recordpkj,true,'pe_pengalamankerja');
						if($p_posterr)
							break;
					}				
				}
			}

			//email ke pelamar yang berhasil
			if(!$p_posterr)
				mEmail::berhasilRekrutmen($conn,$r_subkey);
		}
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}else if($r_act == 'batalpeg' and $c_edit) {
		$conn->BeginTrans();

		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		if ($a_infore['jenisrekrutmen'] == 'B'){
			$a_col = $p_model::getPelamarBaru($conn, $r_subkey);
			
			$where = 'idpegawai';
			$p_key = $a_col['refidpeg'];

			$a_pend = mRekrutmen::getRWTPendidikan($conn,$a_col['refidpeg']);
			if(count($a_pend)>0){
				foreach ($a_pend as $nourutrpen) {
					list($p_posterr,$p_postmsg) = $p_model::delete($conn,$nourutrpen,'pe_rwtpendidikan','nourutrpen','','fileijazah,filetranskrip');

					if($p_posterr)
						break;
				}
			}

			if(!$p_posterr){
				$a_pkj = mRekrutmen::getRWTPengalamanKerja($conn,$a_col['refidpeg']);
				if(count($a_pkj)>0){
					foreach ($a_pkj as $nourutpk) {
						list($p_posterr,$p_postmsg) = $p_model::delete($conn,$nourutpk,'pe_pengalamankerja','nourutpk');

						if($p_posterr)
							break;
					}
				}
			}

			if(!$p_posterr){
				$a_akad = mRekrutmen::getRWTJabAkademik($conn,$a_col['refidpeg']);
				if(count($a_akad)>0){
					foreach ($a_akad as $nourutjf) {
						list($p_posterr,$p_postmsg) = $p_model::delete($conn,$nourutjf,'pe_rwtfungsional','nourutjf','','filefungsional');

						if($p_posterr)
							break;
					}
				}
			}

			if(!$p_posterr){
				$p_posterr = mIntegrasi::deleteRoleGate($conn,$a_col['refidpeg']);
				if($p_posterr)
					$p_postmsg = 'Penghapusan User Role ke Gate gagal';
			}
			
			if(!$p_posterr){
				$where = 'idpegawai';
				$p_key = $a_col['refidpeg'];

				list($p_posterr,$p_postmsg) = $p_model::delete($conn,$p_key,'ms_pegawai',$where);
			}

			if(!$p_posterr){
				$recpelamar = array();
				$recpelamar['refidpeg'] = 'null';
				$recpelamar['tglditerimapegawai'] = 'null';
				$recpelamar['statuslulus'] = 'null';
				$recpelamar['idunitlamar'] = 'null';

				$where = 'idrekrutmen,nopendaftar';
				$p_key = $r_key.'|'.$r_subkey;
				
				list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$recpelamar,$p_key,true,'re_calon',$where);
			}
		}

		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
	
	
	$a_proses = array();
	$a_proses = $p_model::getArrProses($conn,$r_key);	
	
	$r_proses = $p_model::getMekanisme($conn, count($a_proses),$r_key);
	if ($a_infore['jenisrekrutmen'] == 'B'){
		$a_data = $p_model::getLolosSeleksiBaru($conn,$r_key, $r_proses);
	}else{
		$a_data = $p_model::getKandidat($conn,$r_key);
	}
	
	$a_unitrek = $p_model::getUnitRek($conn,$r_key);
	$a_jenisrekrutmen = $p_model::jenisRekrutmen();
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/wizard.css" rel="stylesheet" type="text/css">
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
						<td colspan="9" align="center">Daftar Peserta Lolos Seleksi</td>
					</tr>
					<tr>
						<th><?= $a_infore['jenisrekrutmen'] == 'B' ? 'No. Pendaftar' : 'N I P';?></th>
						<th>Nama Calon Pegawai</th>
						<th>Pendidikan</th>
						<? if($a_infore['kodeposisi'] != 'DT'){?>
						<th>Ditempatkan di Unit</th>
						<? }?>
						<th width="300">Aksi</th>
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
						<? if($a_infore['kodeposisi'] != 'DT'){?>
						<td><?= uCombo::unit($conn,$r_unit,'unit_'.$idkandidat,'style="width:300px"',false);?></th>
						<? }?>
						<td align="center">
						<?	if($c_edit) { 
								if (!empty($row['refidpeg'])){ ?>
									<input type="button" name="simpan" value="Data Pegawai" class="ControlStyle" onClick="goDetailPegawai('<?= $row['refidpeg']; ?>')" style="cursor:pointer" title="Klik untuk Biodata Pegawai">
						<?		}	?>
							<input type="button" name="simpan" value="<?= empty($row['refidpeg']) ? 'Jadikan Pegawai >>' : '<< Batalkan Pegawai'; ?>" class="ControlStyle" style="cursor:pointer" onClick="<?= empty($row['refidpeg']) ? "goAddPegawai('".$idkandidat."')" : "goRemovePegawai('".$idkandidat."')"; ?>">
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
				<div id="container_peserta" style="width:<?= $p_tbwidth; ?>px;height:300px"></div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key; ?>">
				<input type="hidden" name="subkey" id="subkey">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/highcharts/highcharts.js"></script>
<script type="text/javascript" src="scripts/highcharts/modules/exporting.js"></script>
<script type="text/javascript">
	
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

function goAddPegawai(idkandidat) {
	var confirmasi = confirm("Apakah anda yakin akan menjadikan pegawai ?");
	if(confirmasi) {
		document.getElementById("act").value = 'generatenip';	
		document.getElementById("subkey").value = idkandidat;	
		goSubmit();
	}
}

function goRemovePegawai(idkandidat) {
	var confirmasi = confirm("Apakah anda yakin akan membatalkan pegawai ?");
	if(confirmasi) {
		document.getElementById("act").value = 'batalpeg';	
		document.getElementById("subkey").value = idkandidat;	
		goSubmit();
	}
}

function showPelamar(nopendaftar){
	win = window.open("<?= Route::navAddress('pop_pelamar').'&key='?>"+nopendaftar,"pop_pelamar","width=950,height=800,scrollbars=1");
	win.focus();
}

function goDetailPegawai(id){
	window.open("<?= Route::navAddress('data_pegawai') ?>"+"&key="+ id,"_blank");
}
	
</script>

</script>
</body>
</html>