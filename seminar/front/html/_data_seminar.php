<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// include model
	require_once(Route::getModelPath('seminar','front'));
	
	// ambil user login
	$r_key = $_REQUEST['key'];
	if(empty($r_key)) {
		$a_flash = array();
		$a_flash['posterr'] = true;
		$a_flash['postmsg'] = 'Harap pilih seminar yang dimaksud terlebih dahulu';
		
		Route::setFlashData($a_flash,(empty($_REQUEST['back']) ? 'home' : 'list_jadwal'));
	}
	
	// properti halaman
	$p_title = 'Informasi Seminar';
	$p_model = mSeminarFront;
	
	// array input
	$a_input = $p_model::inputColumn($conn);
	$a_inputd = $p_model::inputColumnJadwal($conn);
	
	// cek pendaftaran
	if(Seminar::isAuthenticated())
		$isvaliddaftar = $p_model::getValidDaftar($conn,$r_key);
	
	// mengambil data
	$row = $p_model::getData($conn,$r_key);
	$rowds = $p_model::getListJadwal($conn,$r_key);
	$rowbs = $p_model::getListBasis($conn,$r_key);
	
	if(!empty($row['semester'])) {
		$t_semester = explode(',',substr($row['semester'],1,strlen($row['semester'])-2));
		
		$n = count($t_semester);
		switch($n) {
			case 1: $t_semester = $t_semester[0]; break;
			case 2: $t_semester = implode(' dan ',$t_semester); break;
			default: $t_semester[$n-1] = 'dan '.$t_semester[$n-1]; $t_semester = implode(', ',$t_semester); break;
		}
		
		$row['namasemester'] = $t_semester;
	}
	if(!empty($rowbs)) {
		$t_basis = array();
		foreach($rowbs as $rowb)
			$t_basis[] = $rowb['namabasis'].(empty($rowb['iswajib']) ? '' : ': Wajib');
		
		$row['basis'] = implode('<br />',$t_basis);
	}
	
	// cek peserta
	$nim = Seminar::getNIM();
	$nip = Seminar::getNIP();
	if($row['typepeserta'] == 'M' and empty($nim))
		$isnotmhs = true;
	if($row['typepeserta'] == 'P' and empty($nip))
		$isnotpeg = true;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'daftar' and Seminar::isAuthenticated() and !isset($isvaliddaftar) and empty($isnotmhs) and empty($isnotpeg)) {
		$record = array();
		$record['idseminar'] = $r_key;
		
		$err = $p_model::insertRecordJadwal($conn,$record);
		$msg = 'Pendaftaran seminar '.(empty($err) ? 'berhasil' : 'gagal');  
		
		if(empty($err)) {
			$a_flash = array();
			$a_flash['posterr'] = $err;
			$a_flash['postmsg'] = $msg;
			
			Route::setFlashData($a_flash,'list_jadwal');
		}
	}
	
	// include header
	require_once('inc_header.php');
?>
<div class="container bg-white">
	<div class="row">
		<div class="col-md-8">
			<div class="page-header">
				<h3><span class="glyphicon glyphicon-search"></span>&nbsp; Informasi Seminar</h3>
			</div>
			<?php if(!empty($err)) { ?>
			<div class="alert alert-danger">
				<?php echo $msg ?>
			</div>
			<?php } ?>
			<?php if(isset($isvaliddaftar)) { ?>
			<div class="alert alert-info">
				<?php if(empty($isvaliddaftar)) { ?>
				Anda sudah mendaftar seminar ini, hanya saja belum terdaftar sebagai peserta. Harap menunggu proses persetujuan pendaftaran.
				<?php } else { ?>
				Anda sudah terdaftar sebagai peserta seminar ini
				<?php } ?>
			</div>
			<?php }
				else if(Seminar::isAuthenticated()) {
					if($isnotmhs) {
			?>
			<div class="alert alert-warning">
				Seminar ini hanya ditujukan untuk mahasiswa Universitas Esa Unggul.
			</div>
			<?php	} else if($isnotpeg) { ?>
			<div class="alert alert-warning">
				Seminar ini hanya ditujukan untuk pegawai Universitas Esa Unggul.
			</div>
			<?php	}
				}
				else {
			?>
			<p>Untuk mendaftar seminar, silahkan login terlebih dahulu di halaman <a href="<?php echo Route::navAddress('login_form') ?>">Login Peserta</a>.</p>
			<?php } ?>
			<table width="100%" cellpadding="4" cellspacing="2" class="table table-bordered table-striped custom-form">
				<tr>
					<td width="150"><?= Page::getDataLabel($a_input,'namaseminar')?></td>
					<td class="text-center" width="30">:</td>
					<td><?= Page::getDataInputFront($a_input,'namaseminar',$row,false)?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($a_input,'periode')?></td>
					<td class="text-center">:</td>
					<td><?= Page::getDataInputFront($a_input,'periode',$row,false)?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($a_input,'tglawaldaftar')?></td>
					<td class="text-center">:</td>
					<td><?= Page::getDataInputFront($a_input,'tglawaldaftar',$row,false)?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($a_input,'tglakhirdaftar')?></td>
					<td class="text-center">:</td>
					<td><?= Page::getDataInputFront($a_input,'tglakhirdaftar',$row,false)?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($a_input,'tarifseminar')?></td>
					<td class="text-center">:</td>
					<td><?= Page::getDataInputFront($a_input,'tarifseminar',$row,false)?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($a_input,'keterangan')?></td>
					<td class="text-center">:</td>
					<td><?= Page::getDataInputFront($a_input,'keterangan',$row,false)?></td>
				</tr>
			</table>
			<div class="page-header">
				<h3><span class="glyphicon glyphicon-user"></span>&nbsp; Peserta Seminar</h3>
			</div>
			<table width="100%" cellpadding="4" cellspacing="2" class="table table-bordered table-striped custom-form">
				<tr>
					<td width="150"><?= Page::getDataLabel($a_input,'typepeserta')?></td>
					<td class="text-center" width="30">:</td>
					<td><?= Page::getDataInputFront($a_input,'typepeserta',$row,false)?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($a_input,'semester')?></td>
					<td class="text-center">:</td>
					<td><?= $row['namasemester'] // manual ?></td>
				</tr>
				<tr>
					<td>Basis Mahasiswa</td>
					<td class="text-center">:</td>
					<td><?= $row['basis'] // manual ?></td>
				</tr>
			</table>
			<div class="page-header">
				<h3><span class="glyphicon glyphicon-calendar"></span>&nbsp; Jadwal Seminar</h3>
			</div>
			<?php foreach($rowds as $rowd) { ?>
			<table width="100%" cellpadding="4" cellspacing="2" class="table table-bordered table-striped custom-form">
				<tr>
					<td width="150"><?= Page::getDataLabel($a_inputd,'tgljadwal')?></td>
					<td class="text-center" width="30">:</td>
					<td><?= Page::getDataInputFront($a_inputd,'tgljadwal',$rowd,false)?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($a_inputd,'jammulai')?></td>
					<td class="text-center">:</td>
					<td><?= Page::getDataInputFront($a_inputd,'jammulai',$rowd,false)?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($a_inputd,'jamselesai')?></td>
					<td class="text-center">:</td>
					<td><?= Page::getDataInputFront($a_inputd,'jamselesai',$rowd,false)?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($a_inputd,'koderuang')?></td>
					<td class="text-center">:</td>
					<td><?= Page::getDataInputFront($a_inputd,'koderuang',$rowd,false)?></td>
				</tr>
			</table>
			<?php } ?>
			<?php if(Seminar::isAuthenticated() and !isset($isvaliddaftar) and empty($isnotmhs) and empty($isnotpeg)) { ?>
			<form role="form" method="post">
			<input type="hidden" name="act" value="daftar">
			<button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-ok"></span> Daftar Seminar</button>
			</form>
			<?php } ?>
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