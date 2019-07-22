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

$a_data = mLaporanMhs::getDataFeeder($conn,$r_kodeunit,$r_periode);
$p_namafile = "Mahasiswa-".date('YmdHms');
//print_r($a_data);
Page::setHeaderFormat($r_format,$p_namafile);
?>
<html>
<body>
<table border="1">
    <tr>
        <th>NIM</th>
        <th>Nama</th>
        <th>Tempat Lahir</th>
        <th>Tanggal Lahir</th>
        <th>Jenis Kelamin</th>
        <th>Kd Agama</th>
        <th>ID KK</th>
        <th>kd Jenis Pendaftaran</th>
        <th>Tgl Masuk Kuliah</th>
        <th>Mulai semester</th>
        <th>Jalan</th>
        <th>RT</th>
        <th>RW</th>
        <th>Nama Dusun</th>
        <th>Kelurahan</th>
        <th>Kode Pos</th>
        <th>Jenis Tinggal</th>	
        <th>Telp Rumah</th>
        <th>No HP</th>	
        <th>Email</th>	
        <th>Terima KPS</th>
        <th>No KPS</th>	
        <th>Status</th>
        <th>Nama Ayah</th>
        <th>Tgl Lahir Ayah</th>
        <th>Pendidikan Ayah</th>
        <th>Pekerjaan Ayah</th>
        <th>Penghasilan Ayah</th>	
        <th>Nama Ibu</th>	
        <th>Tanggal Lahir Ibu</th>	
        <th>Pendidikan Ibu</th>	
        <th>Pekerjaan Ibu</th>
        <th>Penghasilan Ibu</th>	
        <th>Nama Wali</th>
        <th>Tanggal Lahir wali</th>
        <th>Pendidikan Wali</th>	
        <th>Pekerjaan Wali</th>	
        <th>Penghasilan Wali</th>
        <th>NIK</th>	
        <th>Kode Prodi</th>	
        <th>sks diakui</th>	
        <th>kode PT Asal</th>	
        <th>Prodi Asal</th>
    </tr>
  
    <?php foreach($a_data as $row){ ?>
        <tr>
                    <td><?= $row['nim'] ?></td>
                    <td><?= $row['nama'] ?></td>
                    <td><?= $row['tmplahir'] ?></td>
                    <td><?= $row['tgllahir'] ?></td>
                    <td><?= $row['sex'] ?></td>
                    <td><?= $row['kodeagama'] ?></td>
                    <td>0</td>
                    <td><?= $row['kd_Jenis_Pendaftar'] ?></td>
                    <td><?= $row['tglmasuk'] ?></td>
                    <td><?= $row['Mulai_Semester'] ?></td>
                    <td><?= $row['alamat'] ?></td>
                    <td><?= $row['rt'] ?></td>
                    <td><?= $row['rw'] ?></td>
                    <td><?= $row['kelurahan'] ?></td>
                    <td><?= $row['kecamatan'] ?></td>
                    <td><?= $row['kodepos'] ?></td>
                    <td>-</td>
                    <td><?= $row['telp'] ?></td>
                    <td><?= $row['hp'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td>0</td>
                    <td>-</td>
                    <td><?= $row['statusmhs'] ?></td>
                    <td><?= $row['namaayah'] ?></td>
                    <td></td>
                    <td><?= $row['kodependidikanayah'] ?></td>
                    <td><?= $row['kodepekerjaanayah'] ?></td>
                    <td><?= $row['kodependapatanortu'] ?></td>
                    <td><?= $row['namaibu'] ?></td>
                    <td></td>
                    <td><?= $row['kodependidikanibu'] ?></td>
                    <td><?= $row['kodepekerjaanibu'] ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><?= $row['nik'] ?></td>
                    <td><?= substr($row['kodeunit'],5,10) ?></td>
                    <td><?= $row['sksasal2'] ?></td>
                    <td><?= $row['ptasal'] ?></td>
                    <td><?= $row['ptjurusan'] ?></td>
                    </tr>
    <?php } ?>


</table>
</body>

</html>