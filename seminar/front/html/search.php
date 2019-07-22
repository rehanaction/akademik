<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn -> debug = false ;
	// include model
	require_once(Route::getModelPath('seminar','front'));
	
	// mengambil data
	$r_q = $_GET['q'];
	list($rows,$n_rows) = mSeminarFront::getListCari($conn,$r_q);
	
	// include header
	require_once('inc_header.php');
?>
<div class="container bg-white">
	<div class="row">
		<div class="col-md-8">
			<div class="col-md-12">
				<div class="page-header">
					<h3>Pencarian Seminar</h3>
				</div>
				<p>Pencarian terhadap <strong><?php echo $r_q ?></strong> <?php echo empty($n_rows) ? 'tidak ' : '' ?>menemukan <?php echo empty($n_rows) ? '' : $n_rows.' ' ?>data</p>
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