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
	$p_title = 'Berita Acara ';
	$p_tbwidth = 700;
	$p_ncol = 7;
	$p_namafile = 'ba_hapus_'.$r_key;
		
	$sql ="select p.idpenghapusan, p.tglpembukuan, 105, pd.idseri, 
					b.namabarang, pd.nilaipenghapusan, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, s.merk, s.spesifikasi,
					b.idbarang, u.namaunit, tglpenghapusan, u.kodeunit,
					count(case when s.idkondisi = 'B' then 1 else 0 end) as b,
					count(case when s.idkondisi = 'RB' then 1 else 0 end) as rb,
					count(case when s.idkondisi = 'RR' then 1 else 0 end) as rr,
					count(case when s.idkondisi = 'TB' then 1 else 0 end) as tb
					from aset.as_penghapusan p 
					left join aset.as_penghapusandetail pd on pd.idpenghapusan=p.idpenghapusan
					left join aset.as_seri s on s.idseri=pd.idseri
					left join aset.ms_barang b on s.idbarang=b.idbarang
					left join aset.ms_unit u on u.idunit=p.idunit
					where p.idpenghapusan = '$r_key'
					group by p.idpenghapusan, p.tglpembukuan, pd.idseri, b.namabarang, pd.nilaipenghapusan, 
					s.noseri, s.merk, s.spesifikasi, b.idbarang, p.tglpenghapusan, u.namaunit, u.kodeunit";
	
	$data = $conn->GetRow($sql);

	$sql = "select right('000000' + cast(s.noseri as varchar(6)), 6) noseri, s.idbarang, b.namabarang, s.merk, s.spesifikasi, p.nilaipenghapusan
		from aset.as_penghapusandetail p
		left join aset.as_seri s on s.idseri=p.idseri
		left join aset.ms_barang b on b.idbarang=s.idbarang
        where p.idpenghapusan = '$r_key' 
        order by p.iddetpenghapusan";

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
    Berita Acara<br/>
    Penghapusan Barang
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>	
    <tr>
	    <td colspan="3">
	        Pada hari ini <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= Date::indoDay(date('N')) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span>
	        tanggal <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= CStr::formatDateInd(date('Y-m-d')) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span> 
	        telah dilakukan pemusnahan barang di lingkungan Universitas Esa Unggul, dengan rincian sebagai berikut  :
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
		<td>Tgl. Penghapusan</td>
		<td width="8">:</td>
		<td><?= CStr::formatDateInd($data['tglpenghapusan']) ?></td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th width="20">No.</th>
		<th width="60">No. Seri</th>
		<th width="80">Kode Barang</th>
		<th width="150">Nama Barang</th>
		<th width="80">Merk</th>
		<th>Spesifikasi</th>
		<th width="80">Nilai Hapus</th>
	</tr>
    <?php
        $i = 0;
        while($row = $rs->FetchRow()){
            $i++;
            $nilai = (float)$row['nilaipenghapusan'];
            $total += $nilai;
    ?>
	<tr valign="top">
		<td align="center"><?= $i ?>.</td>
		<td align="center"><?= $row['noseri'] ?></td>
		<td align="center"><?= $row['idbarang'] ?></td>
		<td><?= $row['namabarang'] ?></td>
		<td><?= $row['merk'] ?></td>
		<td><?= $row['spesifikasi'] ?></td>
		<td align="right"><?= CStr::formatNumberRep($r_format,$row['nilaipenghapusan'],2) ?></td>
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
