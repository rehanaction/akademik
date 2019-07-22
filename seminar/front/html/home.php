<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// include model
	require_once(Route::getModelPath('seminar','front'));


	// mengambil data
	$rows = mSeminarFront::getListJadwal($conn,5);
	
	
	// include header
	require_once('inc_header.php');
?>
<div class="container bg-white">
	<div class="row">
		<div class="col-md-8">
			<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
				<!-- Wrapper for slides -->
				<div class="carousel-inner" role="listbox">
					<div class="item active"> <img src="images/banner1.jpg" style="width:750px"> </div>
					<div class="item"> <img src="images/banner2.jpg" style="width:750px"> </div>
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

			<div class="col-md-12">
				<div class="page-header">
					<h3>Jadwal Seminar Terbaru</h3>
				</div>
				<hr/><br/>
				<?php foreach($rows as $row) { ?>

				<div class="row">
					<div class="img-seminar col-md-3">
						<img src=
						<?php if (empty($row['filepooster'])) {
							echo "index.php?page=img_datathumb&type=seminar&id=".$row['idseminar'];
						} else {
							echo "images/logo.png";
						}
						?> style="width:180px; max-height:200px">
					</div>
					<div class="col-md-9">
						<a href="<?php echo Route::navAddress('data_seminar') ?>&key=<?php echo $row['idseminar'] ?>">
						  <table width="100%">
							<tr>
								<h4 class="title">
									<b><?php echo $row['namaseminar'] ?></b>
								</h4>
							<tr>
							<tr>
								<td width="20%">Tema</td>
								<td width="5%">:</td>
								<td><?php echo $row['namaseminar'] ?></td>
							</tr>
							<tr>
								<td width="20%">Tanggal / Jam</td>
								<td width="5%">:</td>
								<td><?php echo Cstr::formatDateInd($row['tglkegiatan'])." / ".$row['jammulai']." - ".$row['jamselesai']; ?></td>
							</tr>
							<tr>
								<td width="20%">Pendaftaran</td>
								<td width="5%">:</td>
								<td><?php echo Cstr::formatDateInd($row['tglawaldaftar'])." s.d ".Cstr::formatDateInd($row['tglakhirdaftar']); ?></td>
							</tr>
							 </table>
						</a>
						<?php echo $row['preview'] ?><br/>
						<?php if(!empty($row['namaruang'])) { ?>
						<span class="label label-info"><small class="glyphicon glyphicon-map-marker"></small> <?php echo $row['namaruang'] ?></span>
						<?php } ?>
						<?php if(!empty($row['jam'])) { ?>
						<span class="label label-info"><small class="glyphicon glyphicon-time"></small> <?php echo $row['jam'] ?></span>
						<?php } ?>
						<?php if(!empty($row['typepeserta'])) { ?>
						<span class="label label-<?php echo $row['classtypepeserta'] ?>"><small class="glyphicon glyphicon-user"></small> <?php echo $row['namatypepeserta'] ?></span>
						<?php } ?>
						<?php if(!empty($row['kategori'])) { ?>
						<span class="label label-success">Kategori: <?php echo $row['kategori'] ?></span>
						<?php } ?>
					</div>
				</div>

				<hr class="divider">
				<?php } ?>
				<div align="center">
					<a href="<?php echo Route::navAddress('list_seminar') ?>">
						<div class="btn btn-primary">Lihat Semua Acara</div>
					</a>
				</div>
			</div>
		</div>
		<?php require_once('inc_sidebar.php'); ?>
	</div>
</div>
<?php require_once('inc_footer.php'); ?>
