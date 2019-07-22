<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_supplier');
	
	// variabel post
	$r_jenissupplier = CStr::removeSpecial($_REQUEST['jenissupplier']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Daftar Supplier :.';	
	$p_tbwidth = 950;
	$p_ncol = 8;
	$p_namafile = 'daftar_kode_supplier';
	
	$sql =" select s.namasupplier, j.jenissupplier, s.alamat, s.notlp, s.nohp, s.email, s.namacp
			from aset.ms_supplier s 
			left join aset.ms_jenissupplier j on j.idjenissupplier = s.idjenissupplier 
			where (1=1) ";

    if(!empty($r_jenissupplier)) 
        $sql .= "and s.idjenissupplier = '$r_jenissupplier' ";

	$sql .=" order by s.idsupplier ";
	
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
    Laporan Daftar Supplier<br/>
    Universitas Esa Unggul<br/>
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="30">No.</th>
	    <th width="150">Nama Supplier</th>
	    <th>Alamat</th>
	    <th width="85">No. Telp</th>
	    <th width="85">No. HP</th>
	    <th width="120">Contact Person</th>
	    <th width="150">Jenis Supplier</th>
    </tr>
	<? 
	$i=0;
	while ($row = $rs->FetchRow ()){
	    $i++;
	?>
	<tr valign="top">
	    <td><?=$i;?>.</td>
	    <td><?=$row['namasupplier']?></td>
	    <td><?=$row['alamat']?></td>
	    <td><?=$row['notlp']?></td>
	    <td><?=$row['nohp']?></td>
	    <td><?=$row['namacp']?></td>
	    <td><?=$row['jenissupplier']?></td>
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
</div>
</body>
</html>
