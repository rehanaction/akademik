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
$r_thnkurikulum = CStr::removeSpecial($_REQUEST['tahun']);
$r_angkatan = (int)$_REQUEST['angkatan'];
$r_periode = CStr::removeSpecial($_REQUEST['tahun'].CStr::removeSpecial($_REQUEST['semester']));
$r_format = $_REQUEST['format'];
$a_data = mLaporanMhs::getDataMatakuliahFeeder($conn,$r_kodeunit,$r_thnkurikulum);
//print_r($a_data);
$p_namafile = "matkul_sample";

Page::setHeaderFormat($r_format,$p_namafile);
?>
<html>

<body>
    <table border=1>
        <tr>
            <th>No</th>
            <th>Kode Prodi</th>
            <th>Kode MK</th>
            <th>Nama MK</th>
            <th>Jenjang Pendidikan</th>
            <th>Jenis MK</th>
            <th>sks_tm</th>
            <th>sks_prak</th>
            <th>sks_prak_lap</th>
            <th>sks_sim</th>
            <th>a_sap</th>
            <th>a_silabus</th>
            <th>a_bahan_ajar</th>
            <th>acara_praka_diktat</th>
            <th>Semester</th>
        </tr>
        <?php $no=0;

            foreach($a_data as $row){
                $no++;
            ?>
        <tr>
            <td><?=$no?></td>
            <td><?= substr($row['kodeunit'],5,10) ?></td>
            <td><?= $row['kodemk'] ?></td>
            <td><?= $row['namamk'] ?></td>
            <td></td>
            <td><?= $row['jenismk'] ?></td>
            <td><?= $row['skstm'] ?></td>
            <td>0</td>
            <td>0</td>
            <td>0</td>
            <td>1</td>
            <td>1</td>
            <td>1</td>
            <td>1</td>
            <td></td>

        </tr>
            <?php } ?>
    </table>

</body>
</html>
