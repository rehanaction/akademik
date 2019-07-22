<?php
	require_once('init.php');
	
	// ambil tagihan yang belum dibayar
	$sql = "select t.nim, j.kodekelompok as kodetagihan from h2h.ke_tagihan t
			join akademik.ms_mahasiswa m on t.nim = m.nim
			join h2h.lv_jenistagihan j on t.jenistagihan = j.jenistagihan
			where coalesce(t.nim,'') <> '' and t.flaglunas in ('BB','BL') and t.tgltagihan <= current_date
			order by t.nim, j.kodetagihan";
	$row = $conn->GetRow($sql);
	
	// ambil kode formulir
	$sql = "select t.kodeformulir from h2h.ke_tariffrm t
			where coalesce(t.kodeformulir,'') <> '' and isaktif = 1
			order by t.periodedaftar desc";
	$kode = $conn->GetOne($sql);
?>
<html>
<div style="float:left">
<form method="post" action="test_inquiry.php">
<h4>NIM</h4>
<input type="text" name="nim" value="<?php echo $row['nim'] ?>">
<h4>Kode Tagihan</h4>
<input type="text" name="kode" value="<?php echo $row['kodetagihan'] ?>">
<br /><br />
<input type="submit" value="Inquiry">
</form>
</div>
<div style="float:left;padding-left:50px">
<form method="post" action="test_inquiry.php">
<h4>Kode Formulir</h4>
<input type="text" name="nim" value="<?php echo $kode ?>">
<input type="hidden" name="kode" value="<?php echo H2H_KODEFORMULIR ?>">
<br /><br />
<input type="submit" value="Inquiry">
</form>
</div>
</html>