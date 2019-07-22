<?php 

// cek akses halaman
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
// hak akses
Modul::getFileAuth();

// include
require_once(Route::getModelPath('tagihan'));
require_once(Route::getModelPath('bank'));
require_once(Route::getModelPath('akademik'));
require_once(Route::getModelPath('unit'));

//print_r($_POST);
$p_tbwidth = 800;
$r_periode =  CStr::removeSpecial($_POST['periode']);
$a_data = mTagihan::getDataPiutang($conn, $r_periode);
$p_title = "Laporan Piutang Semester";
$r_format = $_REQUEST['format'];
$p_namafile = "Piutang-".$r_periode;
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
		<table width="<?=$p_tbwidth?>" >
			<tr>
				<td colspan="6">
				<?php require_once('inc_headrep.php') ?>
				</td>
			</tr>
            <tr>
            	<td width="10%"><strong>Periode</strong></td>
            	<td width="1%"><strong>:</strong></td>
                <td width="39%"><?= $r_periode ?></td>
				<td width="10%"><strong>Jumlah Mahasiswa Aktif</strong></td>
            	<td width="1%"><strong>:</strong></td>
                <td><?= $a_data[0]['jumlahmahasiswa'] ?></td>
            </tr>
        </table>
        <table width="<?= $p_tbwidth ?>" cellpadding="3" border="1" style="border-collapse:collapse;">
			<tr>
            	<th>&nbsp;</th>
            	<th>JENIS TAGIHAN</th>
            	<th>TOTAL</th>
            	
            </tr>
            <?php $i=0; 
                $total=0;
                foreach($a_data as $row){ $i++; 
                $total=$total+$row['totaltagihan'];
                
                ?>
                <tr>
                    <td><?= $i ?></td>
                    <td><?= $row['namajenistagihan'] ?></td>
                    <td align="left"><?= CStr::formatNumberRep($r_format,$row['totaltagihan'],0,false,true) ?></td>
                </tr>

            <?php } ?>
            <tr>
                    <th colspan="2">Total</th>
                    <th align="left"><?= CStr::formatNumberRep($r_format,$total,0,false,true) ?></th>
             </tr>
        </table>
    </div>
    </body>
</html>