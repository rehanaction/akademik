<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporan'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_keydet = CStr::removeSpecial($_REQUEST['keydet']);
	$r_format = $_REQUEST['format'];
	
	if(!empty($r_key))
	if(!empty($r_keydet))
	
	// properti halaman
	$p_title = 'SK. Tim Penghapusan';
	$p_tbwidth = 800;
	$p_ncol = 12;
	$p_namafile = 'sk_penghapusan_'.$r_key;
	
	/*$sql ="select p.idpenghapusan, u.namaunit, tglpenghapusan, u.kodeunit, s.idlokasi, l.namalokasi
					from aset.as_penghapusan p 
					left join aset.as_penghapusandetail pd on pd.idpenghapusan=p.idpenghapusan
					left join aset.as_seri s on s.idseri=pd.idseri
					left join aset.ms_unit u on u.idunit=p.idunit
					left join aset.ms_lokasi l on l.idlokasi=s.idlokasi
					where p.idpenghapusan = '$r_key'";
	
	$data = $conn->GetRow($sql);
	
	$sql = "select right('000000'+convert(varchar(6), s.noseri), 6) as noseri, s.idbarang,
			b.namabarang, s.spesifikasi, pd.nilaipenghapusan, s.tglperolehan, s.idlokasi, 
			l.namalokasi, pg.namalengkap, k.kondisi
			from aset.as_penghapusan p
			join aset.as_penghapusandetail pd on pd.idpenghapusan = p.idpenghapusan
			left join aset.as_seri s on s.idseri = pd.idseri
			left join aset.ms_barang b on b.idbarang = s.idbarang
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi
			left join aset.ms_kondisi k on k.idkondisi = s.idkondisi
			left join sdm.v_biodatapegawai pg on pg.idpegawai = s.idpegawai
			where p.idpenghapusan = '$r_key' 
			order by pd.iddetpenghapusan ";

	$rs = $conn->Execute($sql);*/

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
    Surat Keputusan Yayasan<br/>
    Nomor : ........../SK-DU/<?= date('m')?>/<?= date('Y')?><br/>
    tentang<br/>
    Tim Penghapusan Barang
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>	
	<tr>
		<td>Menimbang</td>
		<td>:</td>
		<td>a. bahwa untuk Manajemen inventarisasi di lingkungan Universitas Esa Unggul dilakukan menggunakan SIM Aset</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>b. bahwa kegiatan Inventarisasi meliputi Pengadaan, Inventarisasi, Perawatan, Mutasi dan Penghapusan</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>c. bahwa untuk pelaksanaan kegiatan penghapusan yang terdapat dalam point B, perlu dibentuk tim penghapusan barang</td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<tr>
		<td>Mengingat</td>
		<td>:</td>
		<td>a. UU. No. 20 tahun 2003 tentang Sistem Pendidikan Nasional</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>b. Keputusan Ketua Yayasan No. 041/KYK/SK/VIII/2013 tentang Statuta Universitas Indonusa Esa unggul</td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<tr>
		<td>Memperhatikan</td>
		<td>:</td>
		<td>SK Rektor tentang Tim Panitia Pengembangan SIM Aset Universitas Esa Unggul</td>
	</tr>
    <tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<tr>
		<td>Menetapkan</td>
		<td>:</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
	  <td>Pertama</td>
	  <td>:</td>
	  <td>Struktur & Anggota Tim Penghapusan barang adalah:</td>
    </tr>
	<tr>
	  <td colspan="3"><table class="tb_data" width="400" align="left">
        <tr>
          <th width="50">No.</th>
          <th>Nama</th>
          <th width="125">Jabatan</th>
        </tr>
        <tr>
          <td align="center">1.</td>
          <td align="left">Ka. Dept. Keuangan Yayasan</td>
          <td align="left">Penanggung Jawab</td>
        </tr>
        <tr>
          <td align="center">2.</td>
          <td align="left">Kabag. RT</td>
          <td align="left">Ketua</td>
        </tr>
        <tr>
          <td align="center">3.</td>
          <td align="left">Ka. Pengadaan</td>
          <td align="left">Anggota</td>
        </tr>
        <tr>
          <td align="center">4.</td>
          <td align="left">Staf Keuangan</td>
          <td align="left">Anggota</td>
        </tr>
        <tr>
          <td align="center">5.</td>
          <td align="left">Staf RT</td>
          <td align="left">Anggota</td>
        </tr>
      </table></td>
    </tr>
	<tr>
	  <td>Kedua</td>
	  <td>:</td>
	  <td>Tim berkewajiban melaksanakan tugas masing-masing dengan penuh tanggung jawab </td>
    </tr>
	<tr>
	  <td>Ketiga</td>
	  <td>:</td>
	  <td>Tim bertanggung jawab pada yayasan</td>
    </tr>
	<tr>
	  <td valign="top">Keempat</td>
	  <td valign="top">:</td>
		<td valign="top">Keputusan ini berlaku sejak tanggal ditetapkan dan jika terdapat kekeliruan dalam penetapan ini akan diubah dan diperbaiki sebagaimana mestinya</td>
	</tr>
</table>	

<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
	  <td colspan="4">&nbsp;</td>
    </tr>
	<tr>
	  <td width="600">&nbsp;</td>
      <td width="200">Ditetapkan</td>
      <td width="3">:</td>
      <td width="270">Jakarta</td>
	</tr>
	<tr>
	  <td>&nbsp;</td>
      <td>Pada Tanggal</td>
      <td>:</td>
      <td><?= CStr::formatDateInd(date('Y-m-d')) ?></td>
	</tr>
	<tr>
	  <td>&nbsp;</td>
      <td colspan="3">Yayasan Kemala Pendidikan Bangsa</td>
    </tr>
	<tr>
	  <td colspan="4">&nbsp;</td>
    </tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="4">&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	    <td colspan="3"><span class="highlight">Yanuar Ramadhan, SE., AK., MM</span></td>
    </tr>
	<tr>
		<td>&nbsp;</td>
	    <td colspan="3">Ka. Biro Keuangan Yayasan</td>
    </tr>
</table>
</div>
</body>
</html>
