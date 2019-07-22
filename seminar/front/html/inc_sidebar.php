<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	//$conn->debug = true;
	// include model
	require_once(Route::getModelPath('seminar','front'));
	require_once(Route::getModelPath('userguide'));
	
	// data jadwal
	$rowds = mSeminarFront::getListMingguIni($conn,3);
	$ug = mUserGuide::getUserGuide($conn,1);
?>
<div class="col-md-4">
	<?php if(Seminar::isAuthenticated()) { ?>

	<a href="<?php echo Route::navAddress('data_pendaftar') ?>"><div class="box"><span class="glyphicon glyphicon-calendar"></span>&nbsp; Profile</div></a>

	<a href="<?php echo Route::navAddress('list_jadwal') ?>"><div class="box"><span class="glyphicon glyphicon-calendar"></span>&nbsp; List Jadwal</div></a>

	<a href="<?php echo Route::navAddress('list_riwayat') ?>"><div class="box"><span class="glyphicon glyphicon-calendar"></span>&nbsp; Seminar yang Anda Ikuti</div></a>
	<br/>
	<a href="<?php echo Route::navAddress('logout') ?>"><div class="box custom-red"><span class="glyphicon glyphicon-log-out"></span>&nbsp; Logout</div></a>
	<?php } else { ?>
	<a href="<?php echo Route::navAddress('data_input') ?>"><div class="box<?php echo $i_page == 'data_input' ? ' blue' : '' ?>"><span class="glyphicon glyphicon-pencil"></span>&nbsp; Daftar Jadi Peserta Sekarang</div></a>
	<br/>
	<a href="<?php echo Route::navAddress('login_form') ?>"><div class="box<?php echo $i_page == 'login_form' ? ' blue' : '' ?>"><span class="glyphicon glyphicon-user"></span>&nbsp; Login Peserta</div></a>
	<?php } ?>
	<br>
	<?php foreach ($ug as $guide => $down) { ?>
			<!-- <a target="_BLANK" href="<?php //echo '../front/index.php?page=download&type=ug&id='.$down['idguide']; ?>"> --><div class="box" onclick="goGuide(<?php echo $down['idguide']; ?>)"><span class="glyphicon glyphicon-question-sign"></span>&nbsp; Petunjuk</div><!-- </a> -->	<?php } ?>
	<br/>
	<div class="col-md-12">
		<div class="page-header">
			<h4>3 Jadwal Seminar Minggu Ini</h4>
		</div>
		<hr/><br/>
		<?php foreach($rowds as $rowd) { ?>
		<div class="row">
			<div class="col-md-6">
				<img src=
						<?php if (empty($row['filepooster'])) {
							echo "index.php?page=img_datathumb&type=seminar&id=".$rowd['idseminar'];
						} else {
							echo "images/logo.png";
						}
						?> width="100%">
			</div>
			<div class="col-md-6">
				<a href="<?php echo Route::navAddress('data_seminar') ?>&key=<?php echo $rowd['idseminar'] ?>">
					<h4 class="title"><?php echo $rowd['namaseminar'] ?></h4>
					Tanggal/Jam : <br>
						<span class="btn-xs btn-primary"><?php echo Cstr::formatDateInd($rowd['tglkegiatan']) ?>, <?php echo $rowd['jammulai'].' - '.$rowd['jamselesai'] ?></span>
					<br>Pendaftaran : <br>
					<span class="btn-xs btn-info"><?php echo Cstr::formatDateInd($rowd['tglawaldaftar']) ?> s.d <?php echo Cstr::formatDateInd($rowd['tglakhirdaftar']) ?></span>
				</a>
				<?php echo $rowd['preview'] ?><br/>
				<?php if(!empty($rowd['namaruang'])) { ?>
				<span class="label label-info"><small class="glyphicon glyphicon-map-marker"></small> <?php echo $rowd['namaruang'] ?></span>
				<?php } ?>
				<?php if(!empty($rowd['jam'])) { ?>
				<span class="label label-info"><small class="glyphicon glyphicon-time"></small> <?php echo $rowd['jam'] ?></span>
				<?php } ?>
				<?php if(!empty($rowd['typepeserta'])) { ?>
				<span class="label label-<?php echo $rowd['classtypepeserta'] ?>"><small class="glyphicon glyphicon-user"></small> <?php echo $rowd['namatypepeserta'] ?></span>
				<?php } ?>
			</div>
		</div>
		<hr class="divider">
		<?php } ?>
		<div align="right">
			<a href="<?php echo Route::navAddress('list_mingguini') ?>">
				<div class="btn btn-primary btn-sm">Lihat Acara Minggu Ini &raquo;</div>
			</a>
	</div>
	</div>
</div>
<script type="text/javascript">
	function goGuide(elem) {
	window.open("index.php?page=download&type=ug&id="+elem);
}
</script>
