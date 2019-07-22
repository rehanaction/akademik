<?php 
// cek akses halaman
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

// hak akses
Modul::getFileAuth();

// include
require_once(Route::getModelPath('laporanmhs'));

$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
$r_angkatan = (int)$_REQUEST['angkatan'];
$r_periode = CStr::removeSpecial($_REQUEST['tahun'].CStr::removeSpecial($_REQUEST['semester']));
$r_format = $_REQUEST['format'];

$a_data = mLaporanMhs::getDataKelasMkFeeder($conn,$r_kodeunit,$r_periode);

$p_namafile = "kelasmkfeeder-".date('YmdHms');
Page::setHeaderFormat($r_format,$p_namafile);

?>
<html>
<body>
<table border="1">
    <tr>
        <th>NO</th>
        <th>Kode Prodi</th>
        <th>Semester</th>
        <th>Kode MK</th>
        <th>Mata Kuliah</th>
        <th>Kelas</th>
        <th>SKS MK</th>
        <th>SKS TM</th>
        <th>SKS PRAK</th>
        <th>SKS PRAK LAP</th>
        <th>SKS SIM</th>
        <th>BAHASAN CASE</th>
        <th>a SELENGGARA PDITT</th>
        <th>a PENGGUNA PDITT</th>
        <th>KUOTA PDITT</th>
        <th>TGL MULAI EFEKTIF</th>
        <th>TGL AKHIR EFEKTIF</th>
        <th>ID MOU</th>
        <th>ID KELAS PDITT</th>
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
        <th>13</th>
        <th>14</th>
        <th>15</th>
        <th>16</th>
        <th>17</th>
        <th>18</th>
        <th>19</th>
    </tr>

    <?php 
        $no = 0;
        foreach($a_data as $row) {

            $no++;
            ?>
<tr>
        <td><?=$no?></td>
        <td><?= substr($row['kodeunit'],5,10) ?></td>
        <td><?= $row['periode'] ?></td>
        <td><?= $row['kodemk'] ?></td>
        <td><?= $row['namamk'] ?></td>
        <td><?= $row['kelasmk'] ?></td>
        <td><?= $row['sks'] ?></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>

        </tr>

        <?php } ?>


</table>