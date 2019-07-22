<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporan'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_detail = CStr::removeSpecial($_REQUEST['iddetail']);
	$r_format = $_REQUEST['format'];
	
	if(!empty($r_key))
	
	// properti halaman
	$p_title = 'Berita Acara Serah Terima';
	$p_tbwidth = 800;
	$p_ncol = 10;
	$p_namafile = 'bastb_new_perolehan_'.$r_key;
	
    $sql = "select u.kodeunit, u.namaunit
              from aset.as_perolehan p 
              left join aset.ms_unit u on u.idunit = p.idunit 
              where p.idperolehan = '$r_key' ";

	$data =  $conn->GetRow($sql);

	$sql = "select p.idbarang1+' - '+b.namabarang as barang, p.merk, p.spesifikasi, p.tglpo, p.nopo, p.tglperolehan, k.kondisi
			  from aset.as_perolehan p
			  left join aset.ms_barang1 b on b.idbarang1 = p.idbarang1
			  left join aset.ms_kondisi k on k.idkondisi = p.idkondisi
			 where p.idperolehan = '$r_key'
			 order by p.idperolehan ";

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
    Berita Acara Serah Terima (BAST) Perolehan Barang<br/>
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>	
    <tr>
	    <td colspan="3">
	        Pada hari ini <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= Date::indoDay(date('N')) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span>
	        tanggal <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= CStr::formatDateInd(date('Y-m-d')) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span> 
	        telah diserahkan barang di lingkungan Universitas Esa Unggul dari perolehan barang dengan rincian sebagai berikut  :
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
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th width="20">No.</th>
		<th width="175">Nama Barang</th>
		<th width="100">Merk</th>
		<th>Spesifikasi</th>
		<th width="80">Tgl. PO</th>
		<th width="80">No. PO</th>
    	<th width="80">Tgl. Perolehan</th>
		<th width="75">Kondisi</th>
	</tr>
    <?php
        $i = 0;
        while($row = $rs->FetchRow()){
            $i++;
    ?>
	<tr valign="top">
		<td align="center"><?= $i ?>.</td>
		<td><?= $row['barang'] ?></td>
		<td><?= $row['merk'] ?></td>
		<td><?= $row['spesifikasi'] ?></td>
		<td align="center"><?= CStr::formatDateInd($row['tglpo'],false) ?></td>
		<td><?= $row['nopo'] ?></td>
		<td align="center"><?= CStr::formatDateInd($row['tglperolehan'],false) ?></td>
		<td><?= $row['kondisi'] ?></td>
	</tr>
    <?php
        }
        if($i == 0){
    ?>
	<tr>
		<td colspan="10" align="center">-- Data tidak ditemukan --</td>
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
		<td>Biro Pengadaan</td>
		<td>Pengelola Aset</td>
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
	</tr>
</table>
</div>
</body>
</html>
