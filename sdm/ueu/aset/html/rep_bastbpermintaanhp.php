<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporan'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_detail = CStr::removeSpecial($_REQUEST['iddetail']);
	$r_format = $_REQUEST['format'];
	
	if(!empty($r_key))
	
	// properti halaman
	$p_title = 'Berita Acara Serah Terima';
	$p_tbwidth = 650;
	$p_ncol = 5;
	$p_namafile = 'bastb_permintaanhp_'.$r_key;
	
    $sql = "select u.kodeunit, u.namaunit, t.tgltransaksi, p.namalengkap, t.verifyuser
              from aset.as_transhp t 
              left join aset.ms_unit u on u.idunit = t.idunitaju
			  left join sdm.v_biodatapegawai p on p.idpegawai = t.idpegawai
              where t.idtranshp = '$r_key' ";

	$data =  $conn->GetRow($sql);

	$sql = "select iddettranshp,d.idbarang1,b.namabarang,d.idsatuan,qty,qtyaju
			from aset.as_transhpdetail d 
			left join aset.ms_barang1 b on b.idbarang1 = d.idbarang1 
			where d.idtranshp = '$r_key' ";

	$rs = $conn->Execute($sql);

	
	// header
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/stylerep.css" rel="stylesheet" type="text/css">
</head>
<body>
<div align="center">
<?php
    include('inc_headerlap.php');
?>
<div class="div_head">
    Berita Acara Serah Terima (BAST)<br> Pengambilan Barang Habis Pakai / ATK<br/>
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>	
    <tr>
	    <td colspan="3">
	        Pada hari ini <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= Date::indoDay(date('N',strtotime($data['tgltransaksi']))) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span>
	        tanggal <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= CStr::formatDateInd($data['tgltransaksi']) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span> 
	        telah diserahkan Barang Habis Pakai di lingkungan Universitas Esa Unggul dengan rincian sebagai berikut  :
	    </td>
    </tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<tr valign="top">
		<td width="80">Nama</td>
		<td width="5">:</td>
		<td><?= $data['namalengkap'] ?></td>
	</tr>
	<tr valign="top">
		<td>Unit</td>
		<td>:</td>
		<td><?= $data['kodeunit'] ?> - <?= $data['namaunit'] ?></td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th width="20">No.</th>
		<th>Barang</th>
		<th width="90">Jml. Diajukan</th>
		<th width="90">Jml. Disetujui</th>
		<th width="80">Satuan</th>
	</tr>
    <?php
        $i = 0;
        while($row = $rs->FetchRow()){
            $i++;
    ?>
	<tr valign="top">
		<td align="center"><?= $i ?>.</td>
		<td><?= $row['namabarang'] ?></td>
		<td align="right"><?= Cstr::formatNumber($row['qtyaju'],2) ?></td>
		<td align="right"><?= Cstr::formatNumber($row['qty'],2) ?></td>
		<td><?= $row['idsatuan'] ?></td>
	</tr>
    <?php
        }
        if($i == 0){
    ?>
	<tr>
		<td colspan="10" align="center">-- Data tidak ditemukan --</td>
    </tr>
    <?php
        }
    ?>
</table>
<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td>Petugas Aset RT</td>
		<td>Yang Mengambil</td>
		<td>Biro Pengadaan</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td><?= $data['verifyuser'] ?></td>
		<td><?= $data['namalengkap'] ?></td>
		<td></td>
	</tr>
</table>
</div>
</body>
</html>
