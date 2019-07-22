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
	$p_title = 'Berita Acara Perawatan Barang Unit';
	$p_tbwidth = 700;
	$p_ncol = 7;
	$p_namafile = 'bastb_rawat_'.$r_key;
	
    //$data = mLaporan::getHeaderRawatUnit($conn,$r_key);
	//$rs = mLaporan::getBASTBRawat($conn,$r_key);

	$sql = " select u.kodeunit, u.namaunit, r.idlokasi, l.namalokasi, p.namalengkap as pemakai
 			   from aset.as_rawat r
			   join aset.as_rawatdetail rd on rd.idrawat = r.idrawat
			   left join aset.ms_unit u on u.idunit = r.idunit 
			   left join aset.ms_lokasi l on l.idlokasi = r.idlokasi
			   left join aset.as_seri s on s.idseri = rd.idseri
			   left join sdm.v_biodatapegawai p on p.idpegawai = l.idpetugas
			  where r.idrawat = '$r_key' ";

	$data = $conn->GetRow($sql);

    $sql = "select d.iddetrawat,right('000000'+convert(varchar(6), s.noseri), 6) as noseri,d.idseri, 
			s.idbarang1,b.namabarang,s.merk,s.spesifikasi,s.idbarang1+' - '+b.namabarang as barang, p.namalengkap as pegawai,
			s.tglperolehan, s.tglgaransi 
			from aset.as_rawatdetail d 
			left join aset.as_seri s on s.idseri = d.idseri 
			left join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 
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
    Berita Acara Serah Terima (BAST) Unit<br/>
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
			telah diserahkan barang untuk dilakukan perbaikan dari:
	    </td>
    </tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<tr valign="top">
		<td width="80">Unit</td>
		<td width="5">:</td>
		<td><?= $data['kodeunit'] ?> - <?= $data['namaunit'] ?></td>
	</tr>
	<tr valign="top">
		<td width="80">Lokasi</td>
		<td width="5">:</td>
		<td><?= $data['idlokasi'] ?> - <?= $data['namalokasi'] ?></td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th width="20">No.</th>
		<th width="50">No. Seri</th>
		<th width="150">Nama Barang</th>
		<th>Spesifikasi</th>
		<th width="80">Tgl. Perolehan</th>
		<th width="80">Tgl. Garansi</th>
		<th width="120">Pemakai</th>
	</tr>
    <?php
        $i = 0;
        while($row = $rs->FetchRow()){
            $i++;
    ?>
	<tr valign="top">
		<td align="center"><?= $i ?>.</td>
		<td align="center"><?= $row['noseri'] ?></td>
		<td><?= $row['namabarang'] ?></td>
		<td><?= $row['spesifikasi'] ?></td>
		<td align="center"><?= $row['tglperolehan'] ?></td>
		<td align="center"><?= $row['tglgaransihabis'] ?></td>
		<td><?= $row['pegawai'] ?></td>
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
		<td>Penanggung Jawab Unit </td>
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
		<td><?= $data['pemakai'] ?></td>
		<td>Kabag. RT</td>
		<td>Ka. Dept. Umum</td>
	</tr>
</table>
</div>
</body>
</html>
