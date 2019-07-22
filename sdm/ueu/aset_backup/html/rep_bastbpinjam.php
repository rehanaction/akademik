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
	$p_title = 'Berita Acara Peminjaman Barang';
	$p_tbwidth = 800;
	$p_ncol = 7;
	$p_namafile = 'bastb_pinjam_'.$r_key;
	
    //$data = mLaporan::getPenghapusan($conn,$r_key);
	//$rs = mLaporan::getBASTBPinjam($conn,$r_key);
	
	$sql = " select u1.kodeunit as kodeunitasal, u1.namaunit as unitasal, pg1.namalengkap as pemilik, pg1.alamat as alamatpemilik, pg1.nip as nippemilik,
			   u2.kodeunit as kodeunitpinjam, u2.namaunit as unitpeminjam, pg.namalengkap as namapeminjam, pg.alamat as alamatpinjam, pg.nip as nippinjam 
 			   from aset.as_pinjam p
			   join aset.as_pinjamdetail pd on pd.idpinjam = p.idpinjam
			   left join aset.as_seri s on s.idseri = pd.idseri
			   left join aset.ms_unit u1 on u1.idunit = p.idunitasal
               left join aset.ms_unit u2 on u2.idunit = p.idunitpeminjam
               left join sdm.v_biodatapegawai pg on pg.idpegawai = p.idpeminjam
			   left join sdm.v_biodatapegawai pg1 on pg1.idpegawai = s.idpegawai
			  where p.idpinjam = '$r_key' ";

	$data = $conn->GetRow($sql);

	$sql = "select s.idbarang,b.namabarang,s.merk,s.spesifikasi, p.catatan,
		p.tglpinjam,p.tglkembali,s.tglperolehan
        from aset.as_pinjam p
        join aset.as_pinjamdetail pd on pd.idpinjam=p.idpinjam
        left join aset.as_seri s on s.idseri = pd.idseri 
        left join aset.ms_barang b on b.idbarang = s.idbarang 
        where p.idpinjam = '$r_key' 
        order by pd.iddetpinjam";

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
    Peminjaman Barang
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>	
    <tr>
	    <td colspan="3">
	        Pada hari ini <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= Date::indoDay(date('N')) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span>
	        tanggal <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= CStr::formatDateInd(date('Y-m-d')) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span> 
	        telah diserahkan barang pinjaman di lingkungan Universitas Esa Unggul, dengan data sebagai berikut  :
	    </td>
    </tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
    <tr valign="top">
		<td width="80">PEMINJAM</td>
	</tr>
	<tr valign="top">
		<td width="80">Unit</td>
		<td width="5">:</td>
		<td><?= $data['kodeunitpinjam'] ?> - <?= $data['unitpeminjam'] ?></td>
	</tr>
	<tr valign="top">
		<td width="80">Nama</td>
		<td width="5">:</td>
		<td><?= $data['nippinjam'] ?> - <?= $data['namapeminjam'] ?></td>
	</tr>
	<tr valign="top">
		<td width="80">Alamat</td>
		<td width="5">:</td>
		<td><?= $data['alamatpinjam'] ?></td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
    <tr valign="top">
		<td width="200">PEMBERI PINJAMAN</td>
	</tr>
	<tr valign="top">
		<td width="80">Unit</td>
		<td width="5">:</td>
		<td><?= $data['kodeunitasal'] ?> - <?= $data['unitasal'] ?></td>
	</tr>
	<tr valign="top">
		<td width="80">Nama</td>
		<td width="5">:</td>
		<td><?= $data['nippemilik'] ?> - <?= $data['pemilik'] ?></td>
	</tr>
	<tr valign="top">
		<td width="80">Alamat</td>
		<td width="5">:</td>
		<td><?= $data['alamatpemilik'] ?></td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<tr>
	    <td colspan="3">
	        dengan rincian sebagai berikut  :
	    </td>
    </tr>
    <tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th rowspan="2" width="20">No.</th>
		<th rowspan="2" width="80">Kode Barang</th>
		<th rowspan="2" width="150">Nama Barang</th>
		<!--th rowspan="2" width="80">Merk</th-->
		<th rowspan="2">Spesifikasi</th>
		<th rowspan="2" width="75">Tgl. Perolehan</th>
		<th colspan="3" width="150">Ketentuan</th>
	</tr>
	<tr>
		<th width="75">Tgl. Pinjam</th>
		<th width="75">Tgl. Kembali</th>
		<th width="75">Keterangan</th>
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
		<!--td><?= $row['merk'] ?></td-->
		<td><?= $row['spesifikasi'] ?></td>
		<td align="center"><?= CStr::formatDateInd($row['tglperolehan'],false) ?></td>
		<td align="center"><?= CStr::formatDateInd($row['tglpinjam'],false) ?></td>
		<td align="center"><?= CStr::formatDateInd($row['tglkembali'],false) ?></td>
		<td align="left"><?= $row['catatan'] ?></td>
	</tr>
    <?php
        }
        if($i == 0){
    ?>
	<tr>
		<td colspan="9" align="center">-- Data tidak ditemukan --</td>
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
		<td width="35%">Jakarta, &nbsp;&nbsp;<?= CStr::formatDateInd(date('Y-m-d')) ?><?//= str_repeat('.',40) ?></td>
		<td width="35%">&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>Peminjam Barang</td>
		<td>Pemberi Pinjaman</td>
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
		<td><?= $data['namapeminjam'] ?></td>
		<td><?= $data['pemilik'] ?></td>
		<td>Kabag. RT</td>
		<td>Ka. Dept. Umum</td>
	</tr>
</table>
</div>
</body>
</html>
