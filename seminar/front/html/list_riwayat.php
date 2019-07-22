<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// include model
	require_once(Route::getModelPath('seminar','front'));
	require_once(Route::getModelPath('kuisioner'));
	require_once(Route::getModelPath('pertanyaankuisseminar'));
	require_once(Route::getModelPath('pesertaseminar'));
	require_once(Route::getModelPath('jawabankuispeserta'));
	require_once(Route::getModelPath('pendaftarseminar'));
	require_once(Route::getModelPath('pengalaman','kemahasiswaan'));
	require_once(Route::getUIPath('form'));

	$nopendaftar = Seminar::getNoPendaftar();
	$a_data = mSeminarFront::getListRiwayat($conn,$nopendaftar);
	
	$a_idseminar = array();
	foreach ($a_data as $v)
		$a_idseminar[] = $v['idseminar'];

	$r_act = $_POST['act'];
	if ($r_act == 'kuisioner') { 
		$conn->beginTrans();
		
		$nopeserta = cStr::removeSpecial($_POST['nopeserta']);
		$idseminar = cStr::removeSpecial($_POST['idseminar']);
		
		$jawaban = $_POST['jawaban'];
		
		// update peserta
		$record = array();
		$record['kritik'] = cStr::removeSpecial($_POST['kritik']);
		$record['saran'] = cStr::removeSpecial($_POST['saran']);
		if (!empty ($jawaban))
		$record['isisikuis'] = 1;
		
		$err = mPesertaSeminar::updateRecord($conn,$record,$nopeserta);
		
		// simpan jawaban kuisioner
		if(empty($err)) {
			$err = mJawabanKuisPeserta::deleteJawaban($conn,$nopeserta,$idseminar);
			
			if(empty($err)) {
				foreach ($jawaban as $idpertanyaan => $kodejawaban){
					// konsep delete insert;
					$recordkuis = array();
					$recordkuis['idpertanyaankuisseminar'] = $idpertanyaan;
					$recordkuis['nopeserta'] = $nopeserta;
					$recordkuis['kodejawaban'] = $kodejawaban;
					
					$err = mJawabanKuisPeserta::insertRecord($conn,$recordkuis);
					if(!empty($err))
						break;
				}
			}
		}

		// masukkan ke kemahasiswaan
		if(empty($err) and $record['isisikuis'] == 1) {
			$row = mPesertaSeminar::getData($conn,$nopeserta);
			$row = mPemdaftarSeminar::getData($conn,$row['nopendaftar']);

			$nim = $row['nim'];

			if($row['isvalid'] == 1 and !empty($nim)) {
				$row = mSeminar::getData($conn,$idseminar);

				if(!empty($row['kodekegiatan'])) {
					$recordmhs = array();
					$recordmhs['periode'] = $row['periode'];
					$recordmhs['nim'] = $nim;
					$recordmhs['kodekegiatan'] = $row['kodekegiatan'];
					$recordmhs['tglkegiatan'] = $row['tglkegiatan'];
					$recordmhs['namakegiatan'] = $row['namaseminar'];
					$recordmhs['nopeserta'] = $nopeserta;

					$idpengalaman = mPengalaman::getIDBySeminar($conn,$nim,$nopeserta);

					if(empty($idpengalaman)) {
						$recordmhs['tglpengajuan'] = date('Y-m-d');
						$recordmhs['isvalid'] = -1;
						$recordmhs['tglvalidasi'] = date('Y-m-d');
						$recordmhs['kodekategori'] = 'S';
						$recordmhs['jenisaktivitas'] = 'I';

						$err = mPengalaman::insertRecord($conn,$recordmhs);
					}
					else
						$err = mPengalaman::updateRecord($conn,$recordmhs,$idpengalaman);
				}
			}
		}

		$ok = Query::isOK($err);
		$conn->commitTrans($ok);

		$p_posterr = $err;
		$p_postmsg = 'Pengisian kuisioner '.(empty($err) ? 'berhasil' : 'gagal');
	}
	else if($r_act == 'cancel') {
		$r_key = $_POST['nopeserta'];
		
		// cek data
		$t_found = false;
		foreach($a_data as $v) {
			if($v['nopeserta'] == $r_key and $v['isopen']) {
				$t_found = true;
				break;
			}
		}
		
		if(empty($t_found))
			$p_posterr = true;
		
		if(empty($p_posterr)) {
			$conn->BeginTrans();
			
			list($p_posterr) = mPesertaSeminar::delete($conn,$r_key);
			$ok = Query::isOK($p_posterr);
			
			$conn->CommitTrans($ok);
		}
		
		$p_postmsg = 'Pembatalan peserta seminar '.($p_posterr ? 'gagal' : 'berhasil');
	}
	
	if (!empty ($a_idseminar))
		$a_pertanyaan = mPertanyaanKuisSeminar::getPertanyaan($conn,$a_idseminar,true);

	$a_data = mSeminarFront::getListRiwayat($conn,$nopendaftar);
		
	// include header
	require_once('inc_header.php');
?>
<div class="container bg-white">
	<div class="row">
		<div class="col-md-8">
			<div class="jalur-box">
				<div class="page-header">
					<h3><span class="glyphicon glyphicon-hourglass"></span>&nbsp; Riwayat Seminar</h3>
				</div>
				<p>Berikut ini adalah daftar seminar yang pernah anda ikuti. Klik Info untuk melihat informasi lebih lanjut.</p>
				<?php if(isset($p_posterr)) { ?>
				<div class="alert alert-<?php echo ($p_posterr === 'warning' ? 'warning' : ($p_posterr ? 'danger' : 'success')) ?> alert-dismissable">
					<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
					<?php echo $p_postmsg ?>
				</div>
				<?php } ?>
				<div class="table-responsive">
					<table class="table custom-font">
						<tr style="background:#0165FE; color:#fff;">
							<th width="30">No</th>
							<th width="30">No Peserta</th>
							<th width="90">Tanggal</th>
							<th>Tema</th>
							<th width="100">Waktu</th>
							<th>Tempat</th>
							<th width="60">Info</th>
							<th width="60">Kuisioner</th>
							<th width="60">Batal</th>
						</tr>
						<?php
							$i = 0;
							foreach ($a_data as $row => $rows) {
						?>
							<tr>
								<td><?php echo ++$i ?></td>
								<td><?php echo $rows['nopeserta'] ?></td>
								<td class="text-center"><?php echo CStr::formatDateInd($rows['tglkegiatan'],false) ?></td>
								<td><?php echo $rows['namaseminar'] ?></td>
								<td><?php echo CStr::formatJam($rows['jammulai']).' - '.CStr::formatJam($rows['jamselesai']) ?></td>
								<td><?php echo $rows['koderuang'] ?></td>
								<td class="text-center">
									<a href="<?php echo Route::navAddress('data_seminar') ?>&key=<?php echo $rows['idseminar'] ?>">
										<button type="button" class="btn btn-primary btn-xs">
											<span class="glyphicon glyphicon-search"></span> Info
										</button>
									</a>
								</td>
								<td class="text-center">
								<?php if (!empty($rows['isisikuis'])) { ?>
									<button type="button" class="btn btn-info btn-xs disabled">
										Terisi
									</button>
								<?php } else {  ?>
									<button type="button" class="btn btn-success btn-xs" data-toggle="modal" data-target="#kuisioner_<?=$rows['idseminar'];?>">
										<span class="glyphicon glyphicon-pencil"></span> Kuisioner
									</button>
								<?php } ?>
								</td>
								<td class="text-center">
									<?php if($rows['isopen']) { ?>
									<form role="form" method="post" data-type="cancel">
										<button type="submit" class="btn btn-danger btn-xs">
											<span class="glyphicon glyphicon-remove"></span> Batal
										</button>
										<input type="hidden" name="act" value="cancel">
										<input type="hidden" name="nopeserta" value="<?php echo $rows['nopeserta'] ?>">
									</form>
									<?php } else { ?>
									<button type="button" class="btn btn-danger btn-xs disabled">
										Tidak Bisa
									</button>
									<?php } ?>
								</td>
							</tr>

							<!--modal-->
							
							<div class="modal fade" id="kuisioner_<?=$rows['idseminar']?>" role="dialog">
								
								    <div class="modal-dialog" style="width: 900px;">								    
								      <!-- Modal content-->
								        <div class="modal-content">
									        <div class="modal-header">
									          <button type="button" class="close" data-dismiss="modal">&times;</button>
									          <h4 class="modal-title">Silahkan isi kuisioner berikut</h4>
									        </div>
									        <form role="form" method="post">						        
										        <div class="modal-body">
													<?php
													$a_soal = $a_pertanyaan[$rows['idseminar']]; 
													$no = 0;
													foreach ($a_soal as $val){ $no++;  ?>
														<h4><?= $val['nomor']?>. <?= $val['pertanyaan']?></h4>
														<?php foreach ($val['jawaban'] as $kode => $detail){ ?>
															<input type="radio"  name="<?= 'jawaban['.$val['idpertanyaankuisseminar'].']'?>" id="<?= 'jawaban['.$val['idpertanyaankuisseminar'].']'?>" style="width:50px;" value="<?= $kode?>"> <?= $detail['teksjawaban']?>
														<?php }?>
														
													<?php 
													} ?>

										          	<br>
										          	<h4>Saran</h4> 
										          	<textarea class="form-control" rows="3" id="saran" name="saran"></textarea>

										          	<br>
										          	<h4>Kritik</h4> 
										          	<textarea class="form-control" rows="3" id="kritik" name="kritik"></textarea>
										          	
										          	<input type="hidden" name="act" value="kuisioner">
										          	<input type="hidden" name="idseminar" value="<?php echo $rows['idseminar'];?>">	
										          	<input type="hidden" name="nopeserta" value="<?php echo $rows['nopeserta'];?>">	
										          	
										        </div>

										        <div class="modal-footer">
										          <button type="submit" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-ok"></span> Submit</button>
										        </div>
										    </form>
								        </div>
								    </div>
							    
							</div>
						<?php 
							} 
						?>
					</table>
				</div>
			</div>
		</div>
	   <?php require_once('inc_sidebar.php'); ?>
	</div>
</div>

<script type="text/javascript">

$("[data-type='cancel']").submit(function() {
	var ok = confirm("Apakah anda yakin akan batal mengikuti seminar tersebut?");
	if(!ok)
		return false;
});

</script>