<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include model
	require_once(Route::getModelPath('pendaftar','front'));
	
	// properti halaman
	$p_title = 'Login Peserta';
	$p_model = mPendaftarFront;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'login') {
		$msg = $p_model::login($conn,$_POST['nopendaftar'],$_POST['password']);
		
		if(empty($msg))
			Route::navigate('list_jadwal');
	}
	
	// include header
	require_once('inc_header.php');
?>
<div class="container">
	<div class="row">
		<div class="col-md-8">
			<div class="page-header">
				<h3><span class="glyphicon glyphicon-user"></span>&nbsp; Login Peserta</h3>
			</div>
			<? if(isset($posterr)) { ?>
			<div class="alert alert-<?= empty($posterr) ? 'success' : 'danger' ?>">
				<?= $postmsg ?>
			</div>
			<? } ?>
			<div class="well">
				<p>
					Anda perlu login untuk mendaftar seminar atau melihat jadwal dan riwayat seminar anda.
					Jika anda mahasiswa Universitas Esa Unggul, anda bisa login menggunakan NIM dan password SIM Akademik anda.
					Jika bukan dan belum pernah mendaftar sebagai peserta, silahkan <a href="<?php echo Route::navAddress('data_input') ?>">Daftar Sekarang</a>.
				</p>
				<p>
					<form role="form" method="post">
						<? if(!empty($msg)) { ?>
						<div class="alert alert-danger"><?= $msg ?></div>
						<? } ?>
						<div class="form-group">
							<label>NIM / No. Peserta</label>
							<input type="text" class="form-control" name="nopendaftar" placeholder="No. Pendaftar" value="<?php echo $_POST['nopendaftar'] ?>">
						</div>
						<div class="form-group">
							<label>Password</label>
							<input type="password" class="form-control" name="password" placeholder="Password">
						</div>
						<button type="submit" class="btn btn-success" name="login" id="btnSave">
							<span class="glyphicon glyphicon-log-in"></span>&nbsp; Login
						</button>
						<input type="hidden" name="act" value="login">
					</form>
				</p>
				<a href="<?php echo Route::navAddress('data_forgotpass') ?>">Lupa password?</a>
			</div>
		</div>
		<?php require_once('inc_sidebar.php'); ?>
	</div>
</div>
<?php require_once('inc_footer.php'); ?>