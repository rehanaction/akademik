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
$a_data = mLaporanMhs::getDataKrsFeeder($conn,$r_kodeunit,$r_periode);
//print_r($a_data);
$p_namafile = "krs_mhs";

Page::setHeaderFormat($r_format,$p_namafile);
?>
<html>
<body>
    <table border=1>
        <tr>
            <th>NO</th>
            <th>NIM</th>
            <th>Nama Mahasiswa</th>
            <th>Kode MK</th>
            <th>Mata Kuliah</th>
            <th>Semester</th>
            <th>Kelas</th>
            <th>Nilai Huruf</th>
            <th>Nilai Indeks</th>
            <th>Nilai Angka</th>
            <th>kode prodi</th>
        </tr>

        <?php 
            $no = 0;
        
            foreach($a_data as $row){
                $no++;

        ?>
        <tr>
            <td><?= $no ?></td>
            <td><?= $row['nim'] ?></td>
            <td><?= $row['nama'] ?></td>
            <td><?= $row['kodemk'] ?></td>
            <td></td>
            <td><?= $row['periode'] ?></td>
            <td><?= $row['kelasmk'] ?></td>
            <td><?= $row['nhuruf'] ?></td>
            <td><?= $row['nangka'] ?></td>
            <td></td>
            <td><?= substr($row['kodeunit'],5,10) ?></td>


        </tr>

            <?php } ?>
    </table>

</body>

</html>