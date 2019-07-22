<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include model
	require_once(Route::getModelPath('seminar','front'));
	
	// data jadwal
	$rs = mSeminarFront::getListAktif($conn);
	
	// include header
	require_once('inc_header.php');
?>
<div class="container bg-white">
	<div class="row">
		<div class="col-md-8">
			<div id="carousel-example-generic" class="carousel slide" data-ride="carousel"> 
				<!-- Wrapper for slides -->
				<div class="carousel-inner" role="listbox">
					<div class="item active"> <img src="images/seminar1.jpg" width="100%"> </div>
					<div class="item"> <img src="images/seminar1.jpg" width="100%"> </div>
				</div>
				<!-- Controls --> 
				<a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev"> 
					<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> 
					<span class="sr-only">Previous</span> 
				</a> 
				<a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next"> 
					<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> 
					<span class="sr-only">Next</span> 
				</a> 
			</div>
			<? if(isset($posterr)) { ?>
			<div class="alert alert-<?= empty($posterr) ? 'success' : 'danger' ?>">
				<?= $postmsg ?>
			</div>
			<? } ?>
			<div class="jalur-box">
				<div class="page-header">
					<h3><span class="glyphicon glyphicon-calendar"></span>&nbsp; Jadwal Seminar</h3>
				</div>
				<?php if($rs->EOF) { ?>
				<p>Belum ada jadwal seminar yang dibuka. Jika anda belum pernah mendaftar sebagai peserta, silahkan <a href="<?php echo Route::navAddress('data_input') ?>">Daftar Sekarang</a>.</p>
				<?php } else { ?>
				<p>Untuk mendaftar seminar atau mengetahui informasi lebih lanjut, klik Info pada seminar yang dimaksud.</p>
				<div class="table-responsive">
					<table class="table custom-font">
						<tr style="background:#0165FE; color:#fff;">
							<th width="30">No</th>
							<th width="90">Tanggal</th>
							<th>Tema</th>
							<th width="100">Waktu</th>
							<th>Tempat</th>
							<th>Peserta</th>
							<th width="60">Info</th>
						</tr>
						<?php
							$i = 0;
							while($row = $rs->FetchRow()) {
								// tampilan peserta
								$t_type = $row['namatypepeserta'];
								if($row['typepeserta'] == 'M') {
									$t_peserta = array();
									if(!empty($row['semester'])) {
										$t_semester = explode(',',substr($row['semester'],1,strlen($row['semester'])-2));
										
										$n = count($t_semester);
										switch($n) {
											case 1: $t_semester = $t_semester[0]; break;
											case 2: $t_semester = implode(' dan ',$t_semester); break;
											default: $t_semester[$n-1] = 'dan '.$t_semester[$n-1]; $t_semester = implode(', ',$t_semester); break;
										}
										
										$t_peserta[] = '<li>Semester '.$t_semester.'</li>';
									}
									if(!empty($row['namabasis'])) {
										$t_basis = explode(',',$row['namabasis']);
										$t_wajib = explode(',',$row['iswajib']);
										
										foreach($t_basis as $k => $v)
											$t_basis[$k] = $v.(empty($t_wajib[$k]) ? '' : ': Wajib');
										
										$t_peserta[] = '<li>'.implode('<br />',$t_basis).'</li>';
									}
									
									$t_type .= '<br /><ul>'.implode(PHP_EOL,$t_peserta).'</ul>';
								}
						?>
						<tr>
							<td><?php echo ++$i ?></td>
							<td class="text-center"><?php echo CStr::formatDateInd($row['tgljadwal'],false) ?></td>
							<td><?php echo $row['namaseminar'] ?></td>
							<td><?php echo CStr::formatJam($row['jammulai']).' - '.CStr::formatJam($row['jamselesai']) ?></td>
							<td><?php echo $row['namaruang'] ?></td>
							<td><?php echo $t_type ?></td>
							<td>
								<a href="<?php echo Route::navAddress('data_seminar') ?>&key=<?php echo $row['idseminar'] ?>">
									<button type="button" class="btn btn-primary btn-xs">
										<span class="glyphicon glyphicon-search"></span> Info
									</button>
								</a>
							</td>
						</tr>
						<?php } ?>
					</table>
				</div>
				<?php } ?>
			</div>
		</div>
		<?php require_once('inc_sidebar.php'); ?>
	</div>
</div>
<?php require_once('inc_footer.php'); ?>