<?php
// cek akses halaman
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
// hak akses
Modul::getFileAuth();

// include
require_once(Route::getModelPath('laporanmhs'));
//$conn->debug=true;
// variabel request
$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
$r_angkatan = (int)$_REQUEST['angkatan'];
$r_periode = CStr::removeSpecial($_REQUEST['tahun'].CStr::removeSpecial($_REQUEST['semester']));
$r_format = $_REQUEST['format'];

$a_data = mLaporanMhs::getDataAjarDosenFeeder($conn,$r_kodeunit,$r_periode);
$p_namafile = "ajardosen-".date('YmdHms');
//print_r($a_data);
Page::setHeaderFormat($r_format,$p_namafile);
?>
<html>

<body>
<table border=1>
<tr>
    <th>No</th>
    <th>NIDN</th>
    <th>Smt</th>
    <th>Kode MK</th>
    <th>nm_mk</th>
    <th>Kelas</th>
    <th>sks_subst_tot</th>
    <th>sks_tm_subst</th>
    <th>sks_prak_subst</th>
    <th>sks_prak_lap_subst</th>
    <th>sks_sim_subst</th>
    <th>jml_tm_renc</th>
</tr>
<tr>
    <th>1</th>
    <th>2</th>
    <th>3</th>
    <th>4</th>
    <th>5</th>
    <th>6</th>
    <th>7</th>
    <th>8</th>
    <th>9</th>
    <th>10</th>
    <th>11</th>
    <th>12</th>
</tr>
<?php 
    $no = 0;
    foreach($a_data as $row) { 
    $no++;
    ?>
<tr>
    <td><?=$no?></td>
    <td><?=$row['nidn']?></td>
    <td><?=$row['periode']?></td>
    <td><?=$row['kodemk']?></td>
    <td><?=$row['namamk']?></td>
    <td><?=$row['kelasmk']?></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td>16</td>
</tr>
<?php } ?>
</table>
</body>

</html>