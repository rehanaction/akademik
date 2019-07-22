<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include model
	require_once(Route::getModelPath('pendaftar','front'));
	
	// include ui
	require_once(Route::getUIPath('form'));
	
	// ambil user login
	$r_key = Seminar::getNoPendaftar();
	
	// properti halaman
	$p_title = 'Data Peserta';
	$p_model = mPendaftarFront;
	
	// array input
	$a_input = $p_model::inputColumn($conn);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save') {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		list($err,$msg) = uForm::validateRecord($a_input,$record);
		
		if(empty($err))
			list($err,$t_key) = $p_model::insertRecord($conn,$record);
		
		if(empty($err)) {
			if(empty($r_key)) {
				$a_flash = array();
				$a_flash['posterr'] = false;
				$a_flash['postmsg'] = 'Pendaftaran peserta berhasil. Anda bisa login menggunakan No. Peserta dan Password <strong>'.$t_key.'</strong>. Harap segera ganti Password anda setelah berhasil login.';
				
				Route::setFlashData($a_flash,'login_form');
			}
			else
				$msg = 'Penyimpanan data peserta berhasil';
		}
		else {
			if(!empty($msg)) {
				foreach($msg as $k => $v)
					$msg[$k] = '<li>'.$v.'</li>';
			}
			
			$msg = (empty($r_key) ? 'Pendaftaran' : 'Penyimpanan data').' peserta gagal'.(empty($msg) ? '' : ':<ul style="padding-left:15px">'.implode('',$msg).'</ul>');
		}
	}
	
	// mengambil data
	if(!empty($post))
		$row = $post;
	else if(!empty($r_key))
		$row = $p_model::getData($conn,$r_key);
	
	// include header
	require_once('inc_header.php');
?>
<div class="container">
	<div class="row">
		<div class="col-md-8">
			<div class="page-header">
				<?php if(empty($r_key)) { ?>
				<h3><span class="glyphicon glyphicon-pencil"></span>&nbsp; Daftar Sekarang</h3>
				<?php } else { ?>
				<h3><span class="glyphicon glyphicon-user"></span>&nbsp; Data Peserta</h3>
				<?php } ?>
			</div>
			<?php if(!empty($err)) { ?>
			<div class="alert alert-danger">
				<?php echo $msg ?>
			</div>
			<?php } ?>
			<?php if(empty($r_key)) { ?>
			<p>
				Silahkan isi data berikut ini untuk mendaftar menjadi peserta seminar.
				Jika anda pernah mendaftar sebelumnya atau anda mahasiswa Universitas Esa Unggul, anda bisa login di halaman <a href="<?php echo Route::navAddress('login_form') ?>">Login Peserta</a>.
			</p>
			<?php } ?>
			<form role="form" method="post">
			<p class="custom-font">Keterangan: (<span id="edit">*</span>) harus diisi</p>
			<table width="100%" cellpadding="4" cellspacing="2" class="table table-bordered table-striped custom-form">
				<?php foreach($a_input as $v) { ?>
				<tr>
					<td width="150"><?= Page::getDataLabel($a_input,$v['kolom'])?></td>
					<td class="text-center" width="30">:</td>
					<td><?= Page::getDataInputFront($a_input,$v['kolom'],$row)?></td>
				</tr>
				<?php } ?>
			</table>
			<input type="hidden" name="act" value="save">
			<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-floppy-disk"></span> Simpan Data</button>
			</form>
		</div>
		<?php require_once('inc_sidebar.php'); ?>
	</div>
</div>

<script type="text/javascript">

$(function() {
	$("span[id='edit']").css("color","red").show();
});

</script>

<?php require_once('inc_footer.php'); ?>