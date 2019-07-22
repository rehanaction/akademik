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
$a_data = mLaporanMhs::getDataAktivitasMhsFeeder($conn,$r_periode,$r_kodeunit);
//print_r($a_data);
$p_namafile = "aktivitas_kuliah_mhs";

Page::setHeaderFormat($r_format,$p_namafile);
?>
<html>
<body>
    <table border=1>
        <tr>
            <th>No</th>
            <th>Lap. Semester</th>
            <th>NPM</th>
            <th>Nama</th>
            <th>Status MHS</th>
            <th>IPs</th>
            <th>SKS smt</th>
            <th>IPk</th>
            <th>SKS tot</th>
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
        </tr>
        <?php 
            $no=0;
            foreach($a_data as $row){
                $no++;
        ?>
            <tr>
                <td><?= $no ?></td>
                <td><?= $row['periode'] ?></td>
                <td><?= $row['nim'] ?></td>
                <td><?= $row['nama'] ?></td>
                <td><?= $row['statusmhs'] ?></td>
                <td><?= $row['ips'] ?></td>
                <td><?= $row['sks'] ?></td>
                <td><?= $row['ipk'] ?></td>
                <td></td>
            </tr>
            <?php } ?>
    </table>
</body>
</html>