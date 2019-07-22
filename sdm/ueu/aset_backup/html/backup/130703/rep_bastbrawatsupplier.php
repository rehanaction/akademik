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
	$p_title = 'Berita Acara Perawatan Barang Supplier';
	$p_tbwidth = 700;
	$p_ncol = 7;
	$p_namafile = 'bastb_rawat_supplier_'.$r_key;
	
//    $data = mLaporan::getHeaderRawatSupplier($conn,$r_key);
//	$rs = mLaporan::getBASTBRawatSupplier($conn,$r_key);
	
	$sql = " select r.idsupplier, s.namasupplier, s.alamat, s.namacp, s.kota, s.notlp, s.nohp
 			   from aset.as_rawat r
			   left join aset.ms_supplier s on s.idsupplier = r.idsupplier 
			  where r.idrawat = '$r_key' ";

	$data = $conn->GetRow($sql);

	$sql = "select d.iddetrawat,right('000000'+convert(varchar(6), s.noseri), 6) as noseri,d.idseri, 
			s.idbarang,b.namabarang,s.merk,s.spesifikasi,s.idbarang+' - '+b.namabarang as barang,
			r.tglrawat, r.tglkembali, r.biaya
			from aset.as_rawatdetail d 
			join aset.as_rawat r on r.idrawat = d.idrawat
			left join aset.as_seri s on s.idseri = d.idseri 
			left join aset.ms_barang b on b.idbarang = s.idbarang 
			left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
			where d.idrawat = '$r_key' ";

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
	        telah diserahkan barang kepada supplier yang ditunjuk untuk dilakukan perawatan di lingkungan Universitas Esa Unggul, dengan rincian sebagai berikut  :
	    </td>
    </tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<tr valign="top">
		<td width="80">Supplier</td>
		<td width="5">:</td>
		<td><?= $data['idsupplier'] ?> - <?= $data['namasupplier'] ?></td>
	</tr>
	<tr valign="top">
		<td width="80">Contact Person</td>
		<td width="5">:</td>
		<td><?= $data['namacp'] ?></td>
	</tr>
	<tr valign="top">
		<td width="80">Alamat</td>
		<td width="5">:</td>
		<td><?= $data['alamat'] ?> - <?= $data['kota'] ?></td>
	</tr>
	<tr valign="top">
		<td width="80">No. Telp / HP</td>
		<td width="5">:</td>
		<td><?= $data['notlp'] ?> - <?= $data['nohp'] ?></td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th width="20">No.</th>
		<th width="80">No. Seri</th>
		<th width="150">Nama Barang</th>
		<th>Spesifikasi</th>
		<th width="80">Tgl. Perawatan</th>
		<th width="80">Tgl. Kembali</th>
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
		<td align="center"><?= $row['noseri'] ?></td>
		<td><?= $row['namabarang'] ?></td>
		<td><?= $row['spesifikasi'] ?></td>
		<td align="center"><?= $row['tglrawat'] ?></td>
		<td align="center"><?= $row['tglkembali'] ?></td>
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
		<td><?= $data['namasupplier'] ?></td>
		<td>Kabag. RT</td>
		<td>Warek II</td>
	</tr>
</table>
</div>
</body>
</html>
