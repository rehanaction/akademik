<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include model
	require_once(Route::getModelPath('seminar','front'));
	
	// mengambil data
	//list($rows,$n_rows) = mSeminarFront::getListTerbaru($conn,10,1);
	$rows = mSeminarFront::getListJadwal($conn);

	// include header
	require_once('inc_header.php');
?>
<div class="container bg-white">
	<div class="row">
		<div class="col-md-8">
			<div class="col-md-12">
				<div class="page-header">
					<h3>Jadwal Seminar Terbaru</h3>
				</div>
				<hr/><br/>
				<?php foreach($rows as $row) { ?>
				<div class="row">
					<div class="col-md-3 img-seminar">
						<img src="<?php echo "index.php?page=img_datathumb&type=seminar&id=".$row['idseminar'];?>" width="100%">
					</div>
					<div class="col-md-9">
						<a href="<?php echo Route::navAddress('data_seminar') ?>&key=<?php echo $row['idseminar'] ?>">
							<h4 class="title"><?php echo $row['namaseminar'] ?></h4>
						</a>
						<?php echo $row['keterangan'] ?><br/>
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
			</div>
		</div>
		<?php require_once('inc_sidebar.php'); ?>
	</div>
</div>
<?php require_once('inc_footer.php'); ?>