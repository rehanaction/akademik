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
	$p_title = 'Pengumuman Penghapusan Barang';
	$p_tbwidth = 800;
	$p_ncol = 12;
	$p_namafile = 'pengumuman_lelang_'.$r_key;
	
/*	$sql ="select p.idpenghapusan, u.namaunit, tglpenghapusan, u.kodeunit, s.idlokasi, l.namalokasi
					from aset.as_penghapusan p 
					left join aset.as_penghapusandetail pd on pd.idpenghapusan=p.idpenghapusan
					left join aset.as_seri s on s.idseri=pd.idseri
					left join aset.ms_unit u on u.idunit=p.idunit
					left join aset.ms_lokasi l on l.idlokasi=s.idlokasi
					where p.idpenghapusan = '$r_key'";
	
	$data = $conn->GetRow($sql);	*/

	$sql = "select right('000000'+convert(varchar(6), s.noseri), 6) as noseri, s.idbarang,
            b.namabarang, s.spesifikasi, pd.nilaipenghapusan, s.tglperolehan, s.merk, k.tahunrakit, k.nopol
            from aset.as_penghapusan p
            join aset.as_penghapusandetail pd on pd.idpenghapusan = p.idpenghapusan
            left join aset.as_seri s on s.idseri = pd.idseri
            left join aset.ms_barang b on b.idbarang = s.idbarang
            left join aset.as_kibkendaraan k on k.idseri = s.idseri
			where p.idpenghapusan = '$r_key' 
			order by pd.iddetpenghapusan ";

	$data = $conn->GetRow($sql);

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
    "PENGUMUMAN PENGHAPUSAN / LELANG BARANG"<br/>
</div>

<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>	
    <tr>
		<td colspan="5"><span style="font-weight:bold;text-decoration:underline;" >Kendaraan</span></td>
    </tr>
	<tr>
		<td width="100">Merk</td>
		<td width="5">:</td>
		<td><?= $data['merk'] ?></td>
    </tr>
	<tr>
		<td>Type</td>
		<td>:</td>
		<td><?= $data['spesifikasi'] ?></td>
    </tr>
	<tr>
		<td>Model</td>
		<td>:</td>
		<td><?= $data['namabarang'] ?></td>
    </tr>
	<tr>
		<td>Tahun</td>
		<td>:</td>
		<td><?= $data['tahunrakit'] ?></td>
    </tr>
	<tr>
		<td>No. Kendaraan</td>
		<td>:</td>
		<td><?= $data['nopol'] ?></td>
    </tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>	
    <tr>
		<td colspan="5"><span style="font-weight:bold;text-decoration:underline;" >Pendaftaran</span></td>
    </tr>
	<tr>
		<td width="100">Hari, Tanggal</td>
		<td width="5">:</td>
		<td></td>
    </tr>
	<tr>
		<td>Waktu</td>
		<td>:</td>
		<td></td>
    </tr>
	<tr>
		<td style="vertical-align:top">Tempat</td>
		<td style="vertical-align:top">:</td>
		<td>Universitas Esa Unggul</br>
			Bagian Rumah Tangga Lantai 1</br>
			Jl. Arjuna Utara No.9, Kebon Jeruk, Jakarta Barat
		</td>
    </tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>	
    <tr>
		<td colspan="5"><span style="font-weight:bold;text-decoration:underline;" >Pelaksanaan Lelang</span></td>
    </tr>
	<tr>
		<td width="100">Hari, Tanggal</td>
		<td width="5">:</td>
		<td></td>
    </tr>
	<tr>
		<td>Waktu</td>
		<td>:</td>
		<td></td>
    </tr>
	<tr>
		<td style="vertical-align:top">Tempat</td>
		<td style="vertical-align:top">:</td>
		<td>Universitas Esa Unggul Gedung International Ruang 304</br>
			Jl. Arjuna Utara No.9, Kebon Jeruk, Jakarta Barat
		</td>
    </tr>
</table>

<table class="tb_head" width="<?= $p_tbwidth ?>" style="margin-top:15px;">
	<tr>
		<td style="font-weight:bold;text-decoration:underline;" colspan="5">Syarat dan Informasi Lelang</td>
	</tr>
	<tr>
		<td></td>
		<td width="1">1.</td>
		<td>Peserta lelang wajib mengisi formulir pendaftaran</td>
	</tr>
	<tr>
		<td></td>
		<td width="1" style="vertical-align:top">2.</td>
		<td>Peserta lelang wajib menyetorkan uang jaminan sebesar Rp. 5.000.000,- (lima juta rupiah) paling lambat satu hari
			sebelum pelaksanaan lelang di bagian Keuangan Yayasan Pendidikan Kemala Bangsa Gedung International lantai 1.
		</td>
	</tr>
	<tr>
		<td></td>
		<td width="1">3.</td>
		<td>Limit penawaran </td>
	</tr>
	<tr>
		<td></td>
		<td width="1">4.</td>
		<td>Kondisi kendaraan yang dilelang apa adanya (as it is)</td>
	</tr>
	<tr>
		<td></td>
		<td width="1">5.</td>
		<td>Penawaran lelang dilaksanakan secara terbuka, transparan dan bebas KKN yaitu dengan penawaran lisan naik naik</td>
	</tr>
	<tr>
		<td></td>
		<td width="1">6.</td>
		<td>Peserta lelang wajib membawa 2 buah materai @6000</td>
	</tr>
	<tr>
		<td></td>
		<td width="1" style="vertical-align:top">7.</td>
		<td>Pemenang lelang wajib melunasi pembayaran dalam waktu 1 (satu) hari kerja setelah pelaksanaan lelang ke rekening
			BUKOPIN No.1016695013 atas nama YAYASAN KEMALA Cabang S Parman Jakarta Barat
		</td>
	</tr>
	<tr>
		<td></td>
		<td width="1" style="vertical-align:top">8.</td>
		<td>Pelunasan harga lelang sudah diterima paling lambat 1 (satu) hari setelah pelaksanaan lelang, apabila tidak dilunasi
			maka pemenang lelang dianggap wanprestasi dan uang jaminan hangus.
		</td>
	</tr>
	<tr>
		<td></td>
		<td width="1">9.</td>
		<td>Informasi lebih lanjut dapat hubungi (021) 567-4233 ext. 256</td>
	</tr>
</table>
<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td width="35%">Jakarta, &nbsp;&nbsp;<?= CStr::formatDateInd(date('Y-m-d')) ?><?//= str_repeat('.',40) ?></td>
		<td width="35%">Mengetahui,</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td style="font-weight:bold;text-decoration:underline;">Kabag. RT</td>
		<td style="font-weight:bold;text-decoration:underline;">Ka. Biro Keuangan YKPB</td>
	</tr>
</table>
<table width="800">

</table>
</div>
</body>
</html>
