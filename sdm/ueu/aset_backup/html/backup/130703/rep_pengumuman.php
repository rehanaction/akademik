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
	$p_title = 'Berita Acara Lelang';
	$p_tbwidth = 900;
	$p_ncol = 12;
	$p_namafile = 'pengumuman_lelang_'.$r_key;
	
    //$data = mLaporan::getPenghapusan($conn,$r_key);
	//$rs = mLaporan::getBASTBRawat($conn,$r_key);
	
	$sql = "select d.iddetrawat,right('000000'+convert(varchar(6), s.noseri), 6) as noseri,d.idseri, 
			s.idbarang,b.namabarang,s.merk,s.spesifikasi,s.idbarang+' - '+b.namabarang as barang, p.namalengkap as pegawai,
			s.tglperolehan, s.tglgaransihabis 
			from aset.as_rawatdetail d 
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
    Berita Acara Lelang<br/>
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>	
    <tr>
	    <td colspan="3">
	        Pada hari ini <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= Date::indoDay(date('N')) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span>
	        tanggal <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= CStr::formatDateInd(date('Y-m-d')) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span> 
	        telah dilakukan lelang barang di lingkungan Universitas Esa Unggul, dengan rincian sebagai berikut  :
	    </td>
    </tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th rowspan="2" width="20">No.</th>
		<th rowspan="2" width="60">Kode Barang</th>
		<th rowspan="2" width="120">Nama Barang</th>
		<th rowspan="2" width="80">Merk</th>
		<th rowspan="2" >Tipe</th>
		<th rowspan="2" width="50">Jumlah</th>
		<th rowspan="2" width="80">Harga Satuan</th>
		<th rowspan="2" width="80">Total</th>
		<th colspan="4" width="80">Kondisi Barang</th>
	</tr>
	<tr>
		<th width="25">B</th>
		<th width="25">RB</th>
		<th width="25">RR</th>
		<th width="25">TB</th>
	</tr>
    <?php
        $i = 0;
        while($row = $rs->FetchRow()){
            $i++;
            $nilai = (float)$row['biaya'];
            $total += $nilai;
    ?>
	<tr valign="top">
		<td><?= $i ?>.</td>
		<td align="center"><?= $row['idbarang'] ?></td>
		<td><?= $row['namabarang'] ?></td>
		<td><?= $row['merk'] ?></td>
		<td><?= $row['spesifikasi'] ?></td>
		<td align="right"><?= CStr::formatNumberRep($r_format,$row['qty'],2,true) ?></td>
		<td align="right"></td>
		<td align="right"></td>
		<td align="center"><?= $row['b'] ?></td>
		<td align="center"><?= $row['rb'] ?></td>
		<td align="center"><?= $row['rr'] ?></td>
		<td align="center"><?= $row['tb'] ?></td>
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
		<td colspan="3">Demikian berita acara lelang ini dibuat dengan sebaik-baiknya dan dapat dipergunakan sebagaimana mestinya.</td>
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
		<td colspan="3">
			Tim Panitia Lelang SK <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
		</td>
	</tr>
</table>
<table class="tb_data" width="500">
	<tr>
		<th width="50">No.</th>
		<th>Nama</th>
		<th width="125">Jabatan</th>
		<th width="125">Tanda Tangan</th>
	</tr>
	<tr>
		<td align="center">1.</td>
		<td align="left">Ka. Dept. Keuangan Yayasan</td>
		<td align="left">PJ</td>
		<td align="left"></td>
	</tr>
	<tr>
		<td align="center">2.</td>
		<td align="left">Kabag. RT</td>
		<td align="left">Ketua</td>
		<td align="left"></td>
	</tr>
	<tr>
		<td align="center">3.</td>
		<td align="left">Ka. Pengadaan</td>
		<td align="left">Anggota</td>
		<td align="left"></td>
	</tr>
	<tr>
		<td align="center">4.</td>
		<td align="left">Staf Keuangan</td>
		<td align="left">Anggota</td>
		<td align="left"></td>
	</tr>
	<tr>
		<td align="center">5.</td>
		<td align="left">Staf RT</td>
		<td align="left">Anggota</td>
		<td align="left"></td>
	</tr>
</table>
</div>
</body>
</html>
