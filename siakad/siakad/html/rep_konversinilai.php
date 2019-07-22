<?php 
// cek akses halaman
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
// hak akses
Modul::getFileAuth();

// include
require_once(Route::getModelPath('laporanmhs'));
$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
$r_thnkurikulum = CStr::removeSpecial($_REQUEST['tahun']);
$r_periode = CStr::removeSpecial($_REQUEST['tahun'].CStr::removeSpecial($_REQUEST['semester']));
$r_format = $_REQUEST['format'];
$a_data = mLaporanMhs::getDataKonversi($conn,$r_periode,$r_kodeunit);
$p_namafile = "konversi_nilai_mhs".$r_periode;

Page::setHeaderFormat($r_format,$p_namafile);


?>
<html>
<style> .str{ mso-number-format:\@; } </style>
<body>
    <table border=1>
        <tr>
            <th>No</th>
            <th>NIM</th>
            <th>Nama Mahasiswa</th>
            <th>Kode MK</th>
            <th>Mata Kuliah</th>
            <th>Kode MK Asal</th>
            <th>Nama MK Asal</th>
            <th>SKS Asal</th>
            <th>SKS diakui</th>
            <th>Nilai Huruf Asal</th>
            <th>Nilai Huruf Diakui</th>
            <th>Nilai Angka Diakui</th>
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
            $no=0;
            foreach($a_data as $row){
                $no++;
        ?>
             <tr>
                <td><?= $no ?></td>
                <td class='str'><?= $row['nim'] ?></td>
                <td><?= $row['nama'] ?></td>
                <td><?= $row['kodemkbaru'] ?></td>
                <td><?= $row['namamkbaru'] ?></td>
                <td><?= $row['kodemklama'] ?></td>
                <td><?= $row['namamklama'] ?></td>
                <td><?= $row['skslama'] ?></td>
                <td><?= $row['sksbaru'] ?></td>
                <td><?= $row['nhuruflama'] ?></td>
                <td><?= $row['nhurufbaru'] ?></td>
                <td><?= $row['nangkabaru'] ?></td>
            </tr>
            <? } ?>
    </table>
</body>
</html>