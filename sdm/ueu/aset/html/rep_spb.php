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
    $p_title = 'Surat Permintaan Barang';
    $p_tbwidth =600;
    $p_ncol = 5;
    $p_namafile = 'spb_'.$r_key;
    
    $sql = "select u.kodeunit, u.namaunit, s.insertuser, s.tglspb, s.nospb
              from prcm.pr_spb s 
              left join aset.ms_unit u on u.idunit = s.idunit 
              where s.idspb = '$r_key' ";

    $data =  $conn->GetRow($sql);

    $sql = "select sd.idbarang1, b.namabarang, sd.qtyaju, sd.qtysetuju
              from prcm.pr_spb s
              join prcm.pr_spbdetail sd on sd.idspb = s.idspb
              left join aset.ms_barang1 b on b.idbarang1 = sd.idbarang1
             where s.idspb= '$r_key'
             order by s.idspb ";

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
    Surat Permintaan Barang (SPB)
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>    
    <!--tr>
        <td colspan="3">
            Pada hari ini <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= Date::indoDay(date('N',strtotime($data['tglperolehan']))) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span>
            tanggal <span class="highlight">&nbsp;&nbsp;&nbsp;&nbsp;<?= CStr::formatDateInd($data['tglperolehan']) ?>&nbsp;&nbsp;&nbsp;&nbsp;</span> 
            telah diserahkan barang di lingkungan Universitas Esa Unggul dari perolehan barang dengan rincian sebagai berikut  :
        </td>
    </tr-->
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr valign="top">
        <td width="80">Unit</td>
        <td width="5">:</td>
        <td><?= $data['kodeunit'] ?> - <?= $data['namaunit'] ?></td>
    </tr>
    <tr valign="top">
        <td width="80">Pemohon</td>
        <td width="5">:</td>
        <td><?= $data['insertuser'] ?></td>
    </tr>
    <tr valign="top">
        <td width="80">Tanggal SPB</td>
        <td width="5">:</td>
        <td><?= CStr::formatDateInd($data['tglspb']) ?></td>
    </tr>
    <tr valign="top">
        <td width="80">No. SPB</td>
        <td width="5">:</td>
        <td><?= $data['nospb'] ?></td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th width="20">No.</th>
        <th width="100">Kode Barang</th>
        <th>Barang</th>
        <th width="80">Qty. Diajukan</th>
        <th width="80">Qty. Disetujui</th>
    </tr>
    <?php
        $i = 0;
        while($row = $rs->FetchRow()){
            $i++;
    ?>
    <tr valign="top">
        <td align="center"><?= $i ?>.</td>
        <td><?= $row['idbarang1'] ?></td>
        <td><?= $row['namabarang'] ?></td>
        <td align="right"><?= $row['qtyaju'] ?></td>
        <td align="right"><?= $row['qtysetuju'] ?></td>
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
        <td colspan="3">Demikian pengajuan Surat Permintaan Barang (SPB) ini agar dipertimbangkan.</td>
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
        <td>Pemohon</td>
        <td>Biro Pembelian</td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td><?= $data['insertuser'] ?></td>
        <td>Ka. Biro Pembelian</td>
    </tr>
</table>
</div>
</body>
</html>
