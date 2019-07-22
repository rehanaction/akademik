<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include model
	require_once(Route::getModelPath('seminar','front'));
	
	// data jadwal
	$rsa = mSeminarFront::getListJadwal($conn);
	
	//$rsb = mSeminarFront::getListRiwayat($conn);
	
	// include header
	require_once('inc_header.php');
?>
<div class="container bg-white">
	<div class="row">
		<div class="col-md-8">
			<div class="jalur-box">
				<div class="page-header">
					<h3><span class="glyphicon glyphicon-calendar"></span>&nbsp; Jadwal Seminar</h3>
				</div>
				<? if(isset($posterr)) { ?>
				<div class="alert alert-<?= empty($posterr) ? 'success' : 'danger' ?>">
					<h3><?= $postmsg ?></h3>
				</div>
				<? } ?>
				<?php if($rsa->EOF) { ?>
				<p>Anda tidak mendaftar pada seminar yang akan datang. Silahkan melihat jadwal seminar yang bisa anda ikuti di halaman <a href="<?php echo Route::navAddress() ?>">Beranda</a>.</p>
				<?php } else { ?>
				<p>Anda dapat mendaftar pada seminar yang akan datang di bawah ini. Klik Info untuk melihat informasi lebih lanjut.</p>
				<div class="table-responsive">
					<table class="table custom-font">
						<tr style="background:#0165FE; color:#fff;">
							<th width="30">No</th>
							<th width="90">Tanggal</th>
							<th>Tema</th>
							<th width="100">Waktu</th>
							<th>Tempat</th>
							<th width="60">Info</th>
						</tr>
						<?php
							$i = 0;
							foreach ($rsa as $row => $value) {	
						?>
						<tr>
							<td><?php echo ++$i ?></td>
							<td class="text-center"><?php echo CStr::formatDateInd($value['tglkegiatan'],false) ?></td>
							<td><?php echo $value['namaseminar'] ?></td>
							<td><?php echo CStr::formatJam($value['jammulai']).' - '.CStr::formatJam($value['jamselesai']) ?></td>
							<td><?php echo $value['koderuang'] ?></td>
							<td>
								<a href="<?php echo Route::navAddress('data_seminar') ?>&key=<?php echo $value['idseminar'] ?>">
									<button type="button" class="btn btn-primary btn-xs">
										<span class="glyphicon glyphicon-search"></span> Info
									</button>
								</a>
							</td>
						</tr>
						<?php  
							}
						?>
					</table>
				</div>
				<?php } ?>
			</div>
			<br />
		</div>
		<?php require_once('inc_sidebar.php'); ?>
	</div>
</div>