<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_seribarang');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_lokasi = CStr::removeSpecial($_REQUEST['lokasi']);
    $r_showchild = CStr::removeSpecial($_REQUEST['showchild']);

	$r_cabang = CStr::removeSpecial($_REQUEST['cabang']);
	$r_gedung = CStr::removeSpecial($_REQUEST['gedung']);
	$r_lantai = CStr::removeSpecial($_REQUEST['lantai']);
	$r_jenisruang = CStr::removeSpecial($_REQUEST['jenisruang']);
	$r_sumber = CStr::removeSpecial($_REQUEST['sumber']);
	$r_merk = CStr::removeSpecial($_REQUEST['merk']);
	$r_barang = CStr::removeSpecial($_REQUEST['idbarang1']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan1 = Modul::setRequest($_POST['bulan1'],'BULAN1');
	$r_bulan2 = Modul::setRequest($_POST['bulan2'],'BULAN2');

	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));

	// definisi variable halaman
	$p_title = '.: Laporan Daftar Seri Barang :.';	
	$p_tbwidth = 1100;
	$p_ncol = 11;
	$p_namafile = 'daftar_seri_barang_'.$r_unit.'_'.$r_lokasi;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	$a_param = array('idlokasi' => $r_lokasi, 'unit' => $a_unit);
	$a_lokasi = mLaporan::getDataLokasi($conn,$r_lokasi);

	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	if(!empty($r_lokasi))
    	$a_lokasi = mLaporan::getDataLokasi($conn, $r_lokasi);
	if(!empty($r_cabang))
    	$a_cabang = mLaporan::getCabang($conn, $r_cabang);
	if(!empty($r_gedung))
    	$a_gedung = mLaporan::getGedung($conn, $r_gedung);
	if(!empty($r_barang))
    	$a_barang = mLaporan::getDataBarang($conn, $r_barang);

	$a_lantai = mCombo::lantai();
	$a_jenisruang = mCombo::jenislokasi($conn);
	$a_sumber = mCombo::sumberdana($conn);
	$a_merk = mCombo::merk($conn);
	$a_bulan = mCombo::bulan();    
	
	$sql="select 
		case when s.tglperolehan < '2016-06-01' then s.idbarang else s.idbarang1 END AS idbarangaset, 
		case when s.tglperolehan < '2016-06-01' then s.idbarang+' - '+b.namabarang else s.idbarang1+' - '+bb.namabarang END AS namabarang, 
		l.namalokasi as namalokasi, 
		s.noseri, s.idkondisi, s.idstatus, s.merk, 
		s.spesifikasi, s.tglperolehan, s.idlokasi, s.idunit, u.namaunit, p.namalengkap, k.kondisi, st.status
		from aset.as_seri s
		left join aset.ms_barang b on b.idbarang = s.idbarang
		left join aset.ms_barang1 bb on bb.idbarang1 = s.idbarang1
		left join aset.ms_lokasi l on l.idlokasi = s.idlokasi
		left join sdm.v_biodatapegawai p on s.idpegawai = p.idpegawai
		left join aset.ms_kondisi k on k.idkondisi = s.idkondisi
		left join aset.ms_status st on st.idstatus = s.idstatus
		left join aset.ms_gedung g on g.idgedung = l.idgedung
		left join aset.as_perolehandetail pd on pd.iddetperolehan = s.iddetperolehan
		left join aset.as_perolehan pe on pe.idperolehan = pd.idperolehan ";
		
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
        
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight'];
    else
        $sql .= "and s.idunit = '$r_unit' ";
        
    if(!empty($r_lokasi)) 
        $sql .= " and s.idlokasi = '$r_lokasi' ";

    if(!empty($r_cabang)) 
        $sql .= " and g.idcabang = '$r_cabang' ";

    if(!empty($r_gedung)) 
        $sql .= "and g.idgedung = '$r_gedung' ";

    if(!empty($r_lantai)) 
        $sql .= "and substring(s.idlokasi,3,1) = '$r_lantai' ";
        
    if(!empty($r_jenisruang)) 
        $sql .= "and l.idjenislokasi = '$r_jenisruang' ";

    if(!empty($r_sumber)) 
        $sql .= "and pe.idsumberdana = '$r_sumber' ";

    if(!empty($r_merk)) 
        $sql .= "and pe.merk = '$r_merk' ";

    if(!empty($r_barang)) 
        $sql .= "and s.idbarang1 = '$r_barang' ";

    $sql .= " group by s.idbarang,s.idbarang1,b.namabarang,bb.namabarang,l.namalokasi,s.noseri, s.idkondisi, s.idstatus, s.merk,
             s.spesifikasi, s.tglperolehan, s.idlokasi, s.idunit, u.namaunit, p.namalengkap, k.kondisi, st.status
             order by s.idlokasi";
	
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
    Laporan Daftar Seri Barang<br/>
    Universitas Esa Unggul<br/>
	Periode 
	<? if($r_bulan1 == $r_bulan2) { ?>
		<?= $a_bulan[$r_bulan1] ?>
	<? } else { ?>
		<?= $a_bulan[$r_bulan1] ?> - <?= $a_bulan[$r_bulan2] ?> 
	<? } ?>
	<?= $r_tahun ?>
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<tr valign="top">
		<td width="60">Unit</td>
		<td width="5">:</td>
		<td><?= $a_unit['kodeunit'] ?> - <?= $a_unit['namaunit'] ?></td>
	</tr>
    <?  if(!empty($r_lokasi)){ ?>
	<tr valign="top">
		<td width="60">Lokasi</td>
		<td width="5">:</td>
		<td><?= $a_param['idlokasi'] ?> - <?= $a_lokasi['namalokasi'] ?></td>
	</tr>
	<?  } ?>
    <?  if(!empty($r_cabang)){ ?>
	<tr valign="top">
		<td>Cabang</td>
		<td>:</td>
		<td><?= $a_cabang['idcabang'] ?> - <?= $a_cabang['namacabang'] ?></td>
	</tr>
	<?  } ?>
    <?  if(!empty($r_lantai)){ ?>
	<tr valign="top">
		<td>Lantai</td>
		<td>:</td>
		<td><?= $a_lantai[$r_lantai] ?></td>
	</tr>
	<?  } ?>
	<?  if(!empty($r_jenisruang)){ ?>
	<tr valign="top">
		<td>Jenis Ruang</td>
		<td>:</td>
		<td><?= $a_jenisruang[$r_jenisruang] ?></td>
	</tr>
	<?  } ?>
	<?  if(!empty($r_merk)){ ?>
	<tr valign="top">
		<td>Merk</td>
		<td>:</td>
		<td><?= $a_merk[$r_merk] ?></td>
	</tr>
	<?  } ?>
	<?  if(!empty($r_barang)){ ?>
	<tr valign="top">
		<td>Barang</td>
		<td>:</td>
		<td><?= $a_barang['namabarang'] ?></td>
	</tr>
	<?  } ?>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
	    <th width="20">No.</th>
	    <th width="50">ID. Lokasi</th>
   		<th width="150">Nama Unit</th>
 		<th>Pemakai</th>
		<th width="50">No. Seri</th>
		<th>Nama Barang</th>
		<th width="100">Merk</th>
		<th width="150">Spesifikasi</th>
		<th width="75">Tgl. Perolehan</th>
		<th width="50">Kondisi</th>
		<th width="50">Status</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td align="center"><?= $i ?>.</td>
	    <td><?= $row['idlokasi'] ?></td>
 		<td><?= $row['namaunit'] ?></td>
 		<td><?= $row['namalengkap'] ?></td>
		<td align="center"><?= Aset::formatNoSeri($row['noseri']) ?></td>
		<td><?= $row['namabarang'] ?></td>
		<td><?= $row['merk'] ?></td>
		<td><?= $row['spesifikasi'] ?></td>
		<td align="center"><?= CStr::formatDateInd($row['tglperolehan'],false) ?></td>
		<td align="center"><?= $row['kondisi'] ?></td>
		<td align="center"><?= $row['status'] ?></td>
	</tr>
	<?
	}
    if($i == 0) {
	?>
	<tr>
	    <td colspan="<?= $p_ncol ?>" align="center">-- Data tidak ditemukan --</td>
	</tr>
	<? } ?>
</table>
<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<?  if(!empty($r_barang)){ ?>
	<tr>
		<td colspan="3"><span class="highlight">Total Keseluruhan Tipe Barang <?= $a_barang['namabarang'] ?> sejumlah : <?= $i ?></td>
	</tr>
	<?  } ?>
	<?  if(!empty($r_merk)){ ?>
	<tr>
		<td colspan="3"><span class="highlight">Total Keseluruhan <?= $a_barang['namabarang'] ?> dengan Merk <?= $a_merk[$r_merk] ?> sejumlah : <?= $i ?></td>
	</tr>
	<?  } ?>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td>Penanggung Jawab Unit</td>
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
		<td></td>
		<td>Kabag. RT</td>
		<td>Ka. Dept. Umum</td>
	</tr>
</table>
</div>
</body>
</html>
