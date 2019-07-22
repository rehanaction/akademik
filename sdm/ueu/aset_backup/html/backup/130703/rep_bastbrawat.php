<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporan'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_format = $_REQUEST['format'];
	
	if(!empty($r_key))
	
	// properti halaman
	$p_title = 'Berita Acara Perawatan Barang';
	$p_tbwidth = 700;
	$p_ncol = 7;
	$p_namafile = 'bastb_rawat_'.$r_key;
	
    //$data = mLaporan::getPenghapusan($conn,$r_key);
	$rs = mLaporan::getBASTBRawat($conn,$r_key);
	
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
    Berita Acara Serah Terima (BAST)<br/>
    Perawatan Barang
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>	
    <tr>
	    <td colspan="3">
	        Pada hari ini <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= Date::indoDay(date('N')) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span>
	        tanggal <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= CStr::formatDateInd(date('Y-m-d')) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span> 
	        telah dilakukan perawatan barang di lingkungan Universitas Esa Unggul, dengan rincian sebagai berikut  :
	    </td>
    </tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th width="20">No.</th>
		<th width="80">Kode Barang</th>
		<th width="150">Nama Barang</th>
		<th width="80">Merk</th>
		<th>Spesifikasi</th>
		<th width="80">Tgl. Perawatan</th>
		<th width="80">Biaya</th>
	</tr>
    <?php
        $i = 0;
        while($row = $rs->FetchRow()){
            $i++;
            $nilai = (float)$row['biaya'];
            $total += $nilai;
    ?>
	<tr valign="top">
		<td align="center"><?= $i ?>.</td>
		<td align="center"><?= $row['idbarang'] ?></td>
		<td><?= $row['namabarang'] ?></td>
		<td><?= $row['merk'] ?></td>
		<td><?= $row['spesifikasi'] ?></td>
		<td align="center"><?= $row['tglrawat'] ?></td>
		<td align="right"><?= CStr::formatNumberRep($r_format,$row['biaya'],2) ?></td>
	</tr>
    <?php
        }
        if($i == 0){
    ?>
	<tr>
		<td colspan="7" align="center">-- Data tidak ditemukan --</td>
    </tr>
    <?php
        }
    ?>
    <tr>
		<td colspan="6" align="right"><b>Total</b>&nbsp;&nbsp;</td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$total,2) ?></b></td>
    </tr>
</table>
<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">Demikian berita acara ini dibuat dengan sebenar - benarnya dan digunakan sebagaimana mestinya.</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td width="35%">Jakarta, &nbsp;&nbsp;<?= CStr::formatDateInd(date('Y-m-d')) ?><?//= str_repeat('.',40) ?></td>
		<td width="35%">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>Penanggung Jawab</td>
		<td>Pengelola Aset</td>
		<td>Mengetahui</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td>Ruang / Pemakai</td>
		<td>Kabag. RT</td>
		<td>Warek II</td>
	</tr>
</table>
</div>
</body>
</html>
