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
	$p_title = 'Berita Acara Mutasi Barang';
	$p_tbwidth = 750;
	$p_ncol = 12;
	$p_namafile = 'bastb_mutasi_'.$r_key;
	
    //$data = mLaporan::getPenghapusan($conn,$r_key);
	//$rs = mLaporan::getBASTBMutasi($conn,$r_key);
	
	$sql = "select u.kodeunit as kdunitasal, u.namaunit as unitasal, m.idlokasiasal, l.namalokasi as lokasiasal,
				   u2.kodeunit as kdunittujuan, u2.namaunit as unittujuan, m.idlokasitujuan, l2.namalokasi as lokasitujuan, 
			       p.nip as niptujuan, p.namalengkap as namapegawai,  p2.nip as nipasal, p2.namalengkap as pegawaiasal
			  from aset.as_mutasi m
			  left join aset.ms_unit u on u.idunit = m.idunitasal
	 		  left join aset.ms_lokasi l on l.idlokasi = m.idlokasiasal
			  left join aset.ms_unit u2 on u2.idunit = m.idunittujuan
	 		  left join aset.ms_lokasi l2 on l2.idlokasi = m.idlokasitujuan
			  left join sdm.v_biodatapegawai p on p.idpegawai = m.idpegawaitujuan
			  left join sdm.v_biodatapegawai p2 on p2.idpegawai = m.idpegawaiasal
	         where m.idmutasi = '$r_key' ";

	$data = $conn->GetRow($sql);

    $sql = "select s.idbarang1,b.namabarang,s.merk,s.spesifikasi, right('000000'+convert(varchar(6), s.noseri), 6) as noseri,
		s.tglperolehan, k.kondisi
        from aset.as_mutasi m
        join aset.as_mutasidetail md on md.idmutasi=m.idmutasi
        left join aset.as_seri s on s.idseri = md.idseri 
        left join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 
		left join aset.ms_kondisi k on k.idkondisi = s.idkondisi
        where m.idmutasi = '$r_key' 
        order by md.iddetmutasi";

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
    Mutasi Barang
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>	
    <tr>
	    <td colspan="3">
	        Pada hari ini <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= Date::indoDay(date('N')) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span>
	        tanggal <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= CStr::formatDateInd(date('Y-m-d')) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span> 
	        telah dilakukan mutasi barang di lingkungan Universitas Esa Unggul, dengan rincian sebagai berikut  :
	    </td>
    </tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
    <tr valign="top">
		<td width="150">RUANGAN ASAL</td>
	</tr>
	<tr valign="top">
		<td>Unit</td>
		<td>:</td>
		<td><?= $data['kdunitasal'] ?> - <?= $data['unitasal'] ?></td>
	</tr>
	<tr valign="top">
		<td>Ruang</td>
		<td>:</td>
		<td><?= $data['idlokasiasal'] ?> - <?= $data['lokasiasal'] ?></td>
	</tr>
	<tr valign="top">
		<td>Pemakai Asal</td>
		<td>:</td>
		<td><?= $data['nipasal'] ?> - <?= $data['pegawaiasal'] ?></td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
    <tr valign="top">
		<td width="150">RUANGAN TUJUAN</td>
	</tr>
	<tr valign="top">
		<td>Unit</td>
		<td>:</td>
		<td><?= $data['kdunittujuan'] ?> - <?= $data['unittujuan'] ?></td>
	</tr>
	<tr valign="top">
		<td>Ruang</td>
		<td>:</td>
		<td><?= $data['idlokasitujuan'] ?> - <?= $data['lokasitujuan'] ?></td>
	</tr>
	<tr valign="top">
		<td>Pemakai</td>
		<td>:</td>
		<td><?= $data['niptujuan'] ?> - <?= $data['namapegawai'] ?></td>
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
		<th width="200">Nama Barang</th>
		<th width="90">Tgl. Perolehan</th>
		<th width="80">Merk</th>
		<th>Spesifikasi</th>
		<th width="60">Kondisi</th>
	</tr>
    <?php
        $i = 0;
        while($row = $rs->FetchRow()){
            $i++;
    ?>
	<tr valign="top">
		<td align="center"><?= $i ?>.</td>
		<td align="center"><?= $row['noseri'] ?></td>
		<td align="center"><?= $row['idbarang1'] ?></td>
		<td><?= $row['namabarang'] ?></td>
		<td align="center"><?= CStr::formatDateInd($row['tglperolehan'],false) ?></td>
		<td><?= $row['merk'] ?></td>
		<td><?= $row['spesifikasi'] ?></td>
		<td><?= $row['kondisi'] ?></td>
	</tr>
    <?php
        }
        if($i == 0){
    ?>
	<tr>
		<td colspan="6" align="center">-- Data tidak ditemukan --</td>
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
		<td>Penanggung Jawab Asal</td>
		<td>Penanggung Jawab Tujuan</td>
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
		<td><?= $data['pegawaiasal'] ?></td>
		<td><?= $data['namapegawai'] ?></td>
		<td>Kabag. RT</td>
		<td>Ka. Dept. Umum</td>
	</tr>
</table>
</div>
</body>
</html>
