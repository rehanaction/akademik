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
	$p_tbwidth = 750;
	$p_ncol = 10;
	$p_namafile = 'bastb_new_perolehan_'.$r_key;
	
    $sql = "select u.kodeunit, u.namaunit, p.tglperolehan, p.nopo, p.tglpo
              from aset.as_perolehanheader p 
              left join aset.ms_unit u on u.idunit = p.idunit 
              where p.idperolehanheader = '$r_key' ";

	$data =  $conn->GetRow($sql);

	$sql = "select 
			--pd.idbarang1+' - '+b.namabarang as barang, 
			case when pd.tglperolehan < '2016-06-01' then pd.idbarang+' - '+b.namabarang else pd.idbarang1+' - '+bb.namabarang END AS barang,
			pd.merk, pd.spesifikasi, pd.qty, k.kondisi, pd.total
			from aset.as_perolehan pd
			left join aset.ms_barang b on b.idbarang = pd.idbarang
			left join aset.ms_barang1 bb on bb.idbarang1 = pd.idbarang1
			left join aset.ms_kondisi k on k.idkondisi = pd.idkondisi
			where pd.idperolehanheader = '$r_key'
			order by pd.idperolehanheader ";

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
    Berita Acara Serah Terima (BAST)<br> Perolehan Barang<br/>
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>	
    <tr>
	    <td colspan="3">
	        Pada hari ini <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= Date::indoDay(date('N',strtotime($data['tglperolehan']))) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span>
	        tanggal <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= CStr::formatDateInd($data['tglperolehan']) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span> 
	        telah diserahkan barang di lingkungan Universitas Esa Unggul dari perolehan barang dengan rincian sebagai berikut  :
	    </td>
    </tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<tr valign="top">
		<td width="100">Unit</td>
		<td width="5">:</td>
		<td><?= $data['kodeunit'] ?> - <?= $data['namaunit'] ?></td>
	</tr>
	<tr valign="top">
		<td>Tgl Perolehan</td>
		<td>:</td>
		<td><?= Cstr::formatDateInd($data['tglperolehan']) ?></td>
	</tr>
	<tr valign="top">
		<td>Tgl. PO / No. PO</td>
		<td>:</td>
		<td><?= Cstr::formatDateInd($data['tglpo']) ?> / <?= $data['nopo'] ?></td>
	</tr>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
	<tr>
		<th width="20">No.</th>
		<th width="200">Nama Barang</th>
		<th width="100">Merk</th>
		<th>Spesifikasi</th>
		<th width="75">Kondisi</th>
		<th width="60">Jumlah</th>
		<th width="100">Total</th>
	</tr>
    <?php
        $i = 0;
        while($row = $rs->FetchRow()){
            $i++;
			$total += (float)$row['total'];
    ?>
	<tr valign="top">
		<td align="center"><?= $i ?>.</td>
		<td><?= $row['barang'] ?></td>
		<td><?= $row['merk'] ?></td>
		<td><?= nl2br($row['spesifikasi']) ?></td>
		<td><?= $row['kondisi'] ?></td>
		<td align="right"><?= Cstr::formatNumber($row['qty'],2) ?></td>
		<td align="right"><?= Cstr::formatNumber($row['total'],2) ?></td>
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
	<tr>
		<td colspan="6" align="right"><b>Grand Total</b>&nbsp;&nbsp;</td>
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
		<td>Biro Pembelian</td>
		<td>Pengelola Aset</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td>Ka. Biro Pembelian</td>
		<td>Ka. Bag. RT</td>
	</tr>
</table>
</div>
</body>
</html>
