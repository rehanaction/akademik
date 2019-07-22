<?php
  defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
 // $conn->debug = true;
  // hak akses
 // $a_auth = Modul::getFileAuth();
  require_once(Route::getModelPath('mahasiswa'));
  require_once(Route::getModelPath('krs'));
  require_once(Route::getModelPath('perwalian'));
  require_once(Route::getModelPath('mengajar'));
  require_once(Route::getUIPath('form'));
  

  $r_semester = CStr::removeSpecial($_REQUEST['semester']);
  $r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
  $r_periode=Akademik::getPeriode();


  $r_key = CStr::removeSpecial($_REQUEST['key']);
  $p_foto = uForm::getPathImageMahasiswa($conn,$r_key);
  $a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
  $a_tagihan2 = mMahasiswa::getTagihanMhsBB($conn,$r_key);
  $a_dataujian = mKrs::getDataJadwalUjian($conn,$r_key,$a_infomhs,$_REQUEST['jenis']);
?>

<!DOCTYPE html>
<html>
<head>
  <title></title>
  <style type="text/css">
    #imgfoto{
      
      width: 30%;
    }
    #background{
        position:absolute;
        z-index:0;
        display:block;
        min-width:50%;
        margin-top: 130px;
    }

    #content{
        position:absolute;
        z-index:1;
    }

    #bg-text
    {
        color:lightgrey;
        font-size:120px;
        transform:rotate(300deg);
        -webkit-transform:rotate(300deg);
    }
  </style>
</head>
<body id="body" onload="window.print()">
  <div id="background">
  <p id="bg-text">STIE INABA</p>
  </div>
  <div id="content">
    <?php
  
    include('inc_headerlap.php');
?>
      <hr>
    <br>
    <table width="100%">
      <tr>
        <td><center><b>Kartu Tanda Peserta Ujian Akhir Semester (UAS)</b></center></td>
      </tr>
      <tr>
        <td><center><b>T.A. <?=Akademik::getNamaPeriode($r_periode)?></b></center></td>
        <?php /* <td><center><b>T.A. Semester Genap 2018 - 2019</b></center></td> */ ?>
      </tr>
    </table>
    <br>
    <table width="100%" >
      <tr>
        <td width="15%">NIM</td>
        <td width="1%">:</td>
        <td width="30%"><?= $a_infomhs['nim'] ?></td>
        <td width="20%">Angkatan/Kelas</td>
        <td width="1%">:</td>
        <?php
          if ($a_infomhs['sistemkuliah'] == "RS") {
            $kelas = "Reguler Sore";
          }elseif($a_infomhs['sistemkuliah'] == "R") {
            $kelas = "Reguler Pagi";
          }
        ?>
        <td><?= substr($a_infomhs['periodemasuk'], 0, 4) ?>/<?= $kelas ?></td>
      </tr>
      <tr>
        <td>Nama</td>
        <td>:</td>
        <td><?= $a_infomhs['nama'] ?></td>
        <td>Jurusan</td>
        <td>:</td>
        <td><?= $a_infomhs['jenjang'] ?> - <?= $a_infomhs['jurusan'] ?></td>
      </tr>
    </table>
    <br>
    <style type="text/css">
    .tg  {border-collapse:collapse;border-spacing:0;}
    .tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
    .tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
    .tg .tg-hgcj{font-weight:bold;text-align:center}
    .tg .tg-amwm{font-weight:bold;text-align:center;vertical-align:top}
    .tg .tg-s268{text-align:left}
    .tg .tg-0lax{text-align:left;vertical-align:top}
    </style>
    <table class="tg" width="100%">
      <tr>
        <th class="tg-hgcj" width="200px">Hari</th>
        <th class="tg-hgcj">Jam</th>
        <th class="tg-amwm">Mata Kuliah</th>
        <th class="tg-amwm">Nama Dosen</th>
        <th class="tg-amwm">Ruang</th>
        <th class="tg-amwm">Paraf Pengawas</th>
      </tr>
      
      <?php 
        foreach ($a_dataujian as $row) { 
          $a_dosen = mMengajar::getDosen($conn,$r_periode,$row['kodemk'],$row['kelasmk'],$row['kodeunit']);
         
          ?>
        
        <tr>
          <td class="tg-s268"><center><?= Date::IndoDay(date('N',strtotime($row['tglujian']))) ?>, <?= CStr::formatDateInd($row['tglujian']) ?></center></td>
          <td class="tg-s268"><center><?= CStr::formatJam($row['waktumulai']) ?> - <?= CStr::formatJam($row['waktuselesai']) ?></center></td>
          <td class="tg-0lax"><?= $row['kodemk'] ?> - <?= $row['namamk'] ?> (<?= $row['kelasmk'] ?>) </td>
          <?php foreach($a_dosen as $value){ ?>
            <td class="tg-0lax"><center><?= $value['namapengajar'] ?></center></td>
          <?php  } ?>
          <td class="tg-0lax"><center><?= $row['koderuang'] ?></center></td>
          <td class="tg-0lax"></td>
        </tr>
        </center>    

      <?php }
      ?>
      
    </table>
    <br>
    <b>Catatan:</b><i> Kartu ini <b>WAJIB</b> dibawa selama mengikuti ujian</i>
    <br>&nbsp;
    <table width="100%">
      <tr>
        <td align="right" rowspan="3" width="50%"><?= uForm::getImageMahasiswa($conn,$r_key,$c_upload) ?></td>
        <td align="right" width="50%"><b>Wakil Ketua Bidang Akademik,&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</b></td>
      </tr>
      <tr>
        <td align="right"><b>TTD</b>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
      </tr>
      <tr>
        <td align="right"><b>Drs. RIYANDI NUR SUMAWIDJAYA , M.M</b></td>
      </tr>
    </table>

</div>


</body>
</html>




