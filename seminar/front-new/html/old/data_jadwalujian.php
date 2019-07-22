<?php 
require_once($conf['model_dir'].'m_combo.php');
require_once('inc_header.php'); 
?>

<div class="container">
  <div class="row">
    <div class="col-md-9">
		<div class="page-header">
			<h2>Jadwal Ujian</h2>
		</div>
		<div class="col-md-9 col-md-offset-0">
			<table width="100%" class="table table-bordered table-striped">
				<tr>
					<td><b>Tanggal Tes</b></td>
					<td><b>Jadwal</b></td>
					<td><b>Peserta/Kapasitas Ruang</b></td>
					<td><b>Kuota Per Tanggal</b></td>
				</tr>
				<? $arr_jadwalujian = mCombo::getJadwal();
					while($row = $arr_jadwalujian->FetchRow()){
					$jmlpeserta = mCombo::getJmlPeserta($row['idjadwaldetail']);
				?>
				<tr>
					<td><?= date('d-m-Y',strtotime($row['tgltes']))?></td>
					<td><?= $row['koderuang']." - ".$row['lokasi']?></td>
					<td><?= $jmlpeserta." / ".$row['kapasitaslokasi']?></td>
					<td><?= $row['kuota']?><?//= $row['idjadwaldetail']?></td>
				</tr>
				<?}?>
			</table>
		</div>
    </div>
    <?php require_once('inc_sidebar.php'); ?>
  </div>
</div>
<?php require_once('inc_footer.php'); ?>
