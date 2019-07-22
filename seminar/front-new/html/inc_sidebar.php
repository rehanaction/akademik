<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
?>
<div class="col-md-4">
	<?php if(Seminar::isAuthenticated()) { ?>
	<a href="<?php echo Route::navAddress('list_jadwal') ?>"><div class="box<?php echo $i_page == 'list_jadwal' ? ' blue' : '' ?>"><span class="glyphicon glyphicon-calendar"></span>&nbsp; Jadwal dan Riwayat Seminar</div></a>
	<br/>
	<a href="<?php echo Route::navAddress('logout') ?>"><div class="box custom-red"><span class="glyphicon glyphicon-log-out"></span>&nbsp; Logout</div></a>
	<?php } else { ?>
	<a href="<?php echo Route::navAddress('data_input') ?>"><div class="box<?php echo $i_page == 'data_input' ? ' blue' : '' ?>"><span class="glyphicon glyphicon-pencil"></span>&nbsp; Daftar Sekarang</div></a>
	<br/>
	<a href="<?php echo Route::navAddress('login_form') ?>"><div class="box<?php echo $i_page == 'login_form' ? ' blue' : '' ?>"><span class="glyphicon glyphicon-user"></span>&nbsp; Login Peserta</div></a>
	<?php } ?>
	<br/>
	<?php /* <a href="http://ppmb.esaunggul.ac.id" target="_blank">
		<div class="box"><span class="glyphicon glyphicon-book"></span>&nbsp; Panduan Alur Pendaftaran</div>
	</a> */ ?>
	<?php /* <div class="info-wrapper">
		<div class="page-header">
			<h3><span class="glyphicon glyphicon-bullhorn"></span>&nbsp; Info Penting </h3>
		</div>
		<ul class="pengumuman">
			<? foreach($databerita as $val) { ?>
			<li>
				<strong><?= $val['judul']?></strong>
				<span style="font-size:10px">(<?= $val['creator']?>)</span>
				<hr>
				<?= $val['pengumuman']?>
			</li>
			<? } ?>
		</ul>
	</div> */ ?>
</div>