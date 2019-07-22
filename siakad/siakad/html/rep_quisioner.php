<?php
    // cek akses halaman
    defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
    //$conn->debug=true;
    // hak akses
    //Modul::getFileAuth();
  
    // include
    require_once(Route::getModelPath('monitoring'));
    require_once(Route::getModelPath('kelas'));
    
    // variabel request
    $r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
    $r_angkatan = (int)$_REQUEST['angkatan'];
    $r_format = $_REQUEST['format'];
	    if(empty($r_format))
      $r_format='xls';
      
    $p_title = 'Hasil Kuisioner';
    $p_tbwidth = "100%";
    
    // variabel request
    $r_key = CStr::removeSpecial($_REQUEST['key']);
    if(empty($r_key))
        Route::navigate($p_listpage);
    $key=explode('|',$r_key);
    // mendapatkan data

    $a_infokelas = mKelas::getDataSingkat($conn,$r_key,true,$key[5]);
    $a_data=mMonitoring::getHasilQuizAll($conn,$r_key); 

    $a_pilihan=mMonitoring::arrPilihan($conn,$r_key);   
    $p_namafile = 'Quisioner-'.$a_infokelas['namamk']."-".$a_infokelas['pengajar']."- Kelas (".$key[4].")";
    // header
    Page::setHeaderFormat($r_format,$p_namafile);
    //print_r($a_data);die();
    
?>
<html>
<head>
    <title><?= $p_title ?></title>
    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
    <style>
        .tab_header { border-bottom: 1px solid black; margin-bottom: 5px }
        .div_headeritem { float: left }
        .div_preheader, .div_header { font-family: "Times New Roman" }
        .div_preheader { font-size: 10px; font-weight: bold }
        .div_header { font-size: 12px }
        .div_headertext { font-size: 9px; font-style: italic }
        
        .tb_head td, .div_head { font-family: "Times New Roman" }
        .tb_head td { font-size: 10px }
        .div_head { font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 5px }
        
        .tb_cont td { padding: 0; vertical-align: top }
        .tb_data { border: 1px solid black; border-collapse: collapse }
        .tb_data th, .tb_data td { border: 1px solid black; font-family: "Times New Roman"; font-size: 8px; padding: 1px }
        .tb_data th { background-color: #CFC }
        .tb_data .mark { font-family: "Arial Narrow","Arial" }
        
        .tb_foot { font-family: "Times New Roman"; font-size: 10px; font-weight: bold; margin-top: 10px }
        .tb_foot .mark { font-weight: normal }
    
        .pad { padding-left: 100px }
    </style>
</head>
<body>
    
<div align="center">
<?php
//include('inc_headerlap.php');
?>
<style type="text/css">
.tg1  {border-collapse:collapse;border-spacing:0;}
.tg1 td{font-family:Arial, sans-serif;font-size:14px;padding:0px 5px;overflow:hidden;word-break:normal;}
.tg1 th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:0px 5px;overflow:hidden;word-break:normal;}
.tg1 .tg1-1wig{font-weight:bold;text-align:left;vertical-align:top}
.tg1 .tg1-s268{text-align:left}
.tg1 .tg1-5ua9{font-weight:bold;text-align:left}
.tg1 .tg1-0lax{text-align:left;vertical-align:top}
</style>
<table class="tg1" width="100%">
  <tr>
    <th class="tg1-s268">Nama Dosen</th>
    <th class="tg1-s268" width="">:</th>
    <th class="tg1-5ua9"><?= $a_infokelas['pengajar'] ?></th>
    <th class="tg1-s268">Hari/Jurusan/Semester</th>
    <th class="tg1-0lax">:</th>
    <th class="tg1-1wig"><?= $a_infokelas['jadwal'] ?></th>
  </tr>
  <tr>
    <td class="tg1-s268">Mata Kuliah</td>
    <td class="tg1-s268">:</td>
    <td class="tg1-5ua9"><?= $a_infokelas['kodemk'] ?> - <?= $a_infokelas['namamk'] ?></td>
    <td class="tg1-s268">Jumlah Responden</td>
    <td class="tg1-0lax">:</td>
    <td class="tg1-1wig"><?= $a_infokelas['jumlahpeserta'] ?></td>
  </tr>
</table>
<br>
<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
.tg .tg-pgcc{font-family:"Times New Roman", Times, serif !important;;background-color:#9b9b9b;color:#000000;border-color:black;text-align:left;vertical-align:top}
.tg .tg-kb6e{font-family:"Times New Roman", Times, serif !important;;color:#000000;border-color:black;text-align:left;vertical-align:top}
.tg .tg-zuig{font-weight:bold;font-family:"Times New Roman", Times, serif !important;;background-color:#9b9b9b;color:#000000;border-color:black;text-align:center}
.tg .tg-qj9f{font-weight:bold;font-family:"Times New Roman", Times, serif !important;;background-color:#9b9b9b;color:#000000;text-align:center}
.tg .tg-7o7k{font-weight:bold;font-family:"Times New Roman", Times, serif !important;;color:#000000;border-color:black;text-align:center}
.tg .tg-aep7{font-weight:bold;font-family:"Times New Roman", Times, serif !important;;color:#000000;text-align:center}
</style>
<table class="tg" width="100%">
  <tr>
    <th class="tg-zuig" rowspan="3">No.<br>Responden</th>
    <th class="tg-zuig" colspan="5">Bagian I</th>
    <th class="tg-zuig" colspan="17">Bagian II</th>
    <th class="tg-qj9f" rowspan="3">Jml</th>
    <th class="tg-qj9f" rowspan="3">Rata-<br>rata</th>
    <th class="tg-qj9f" colspan="12">Bagian III</th>
    <th class="tg-qj9f" rowspan="3">Jml</th>
    <th class="tg-qj9f" rowspan="3">Rata-<br>rata</th>
  </tr>
  <tr>
    <td class="tg-zuig" colspan="3">1</td>
    <td class="tg-zuig" rowspan="2">2</td>
    <td class="tg-zuig" rowspan="2">3</td>
    <td class="tg-zuig" rowspan="2">1</td>
    <td class="tg-zuig" rowspan="2">2</td>
    <td class="tg-zuig" rowspan="2">3</td>
    <td class="tg-zuig" rowspan="2">4</td>
    <td class="tg-zuig" rowspan="2">5</td>
    <td class="tg-zuig" rowspan="2">6</td>
    <td class="tg-zuig" rowspan="2">7</td>
    <td class="tg-zuig" rowspan="2">8</td>
    <td class="tg-zuig" rowspan="2">9</td>
    <td class="tg-zuig" rowspan="2">10</td>
    <td class="tg-zuig" rowspan="2">11</td>
    <td class="tg-zuig" rowspan="2">12</td>
    <td class="tg-zuig" rowspan="2">13</td>
    <td class="tg-zuig" rowspan="2">14</td>
    <td class="tg-zuig" rowspan="2">15</td>
    <td class="tg-zuig" rowspan="2">16</td>
    <td class="tg-qj9f" rowspan="2">17</td>
    <td class="tg-qj9f" rowspan="2">18</td>
    <td class="tg-qj9f" rowspan="2">19</td>
    <td class="tg-qj9f" rowspan="2">20</td>
    <td class="tg-qj9f" rowspan="2">21</td>
    <td class="tg-qj9f" rowspan="2">22</td>
    <td class="tg-qj9f" rowspan="2">23</td>
    <td class="tg-qj9f" rowspan="2">24</td>
    <td class="tg-qj9f" rowspan="2">25</td>
    <td class="tg-qj9f" rowspan="2">26</td>
    <td class="tg-qj9f" rowspan="2">27</td>
    <td class="tg-qj9f" rowspan="2">28</td>
    <td class="tg-qj9f" rowspan="2">29</td>
  </tr>

  <tr>
    <td class="tg-zuig">a</td>
    <td class="tg-zuig">b</td>
    <td class="tg-zuig">c</td>
  </tr>
  <?php 
    $no=0;
    //menghitung Y
    $bag11a = 0;
    $bag11b = 0;
    $bag11c = 0;
    $bag12 = 0;
    $bag13 = 0;
    
    $jmly = 0;
    foreach($a_data as $hasilquiz){ 
    $bag21 = $hasilquiz['quiz_2_1'] + $bag21;
    $bag22 = $hasilquiz['quiz_2_2'] + $bag22;
    $bag23 = $hasilquiz['quiz_2_3'] + $bag23;
    $bag24 = $hasilquiz['quiz_2_4'] + $bag24;
    $bag25 = $hasilquiz['quiz_2_5'] + $bag25;
    $bag26 = $hasilquiz['quiz_2_6'] + $bag26;
    $bag27 = $hasilquiz['quiz_2_7'] + $bag27;
    $bag28 = $hasilquiz['quiz_2_8'] + $bag28;
    $bag29 = $hasilquiz['quiz_2_9'] + $bag29;
    $bag210 = $hasilquiz['quiz_2_10'] + $bag210;
    $bag211 = $hasilquiz['quiz_2_11'] + $bag211;
    $bag212 = $hasilquiz['quiz_2_12'] + $bag212;
    $bag213 = $hasilquiz['quiz_2_13'] + $bag213;
    $bag214 = $hasilquiz['quiz_2_14'] + $bag214;
    $bag215 = $hasilquiz['quiz_2_15'] + $bag215;
    $bag216 = $hasilquiz['quiz_2_16'] + $bag216;
    $bag217 = $hasilquiz['quiz_2_17'] + $bag217;

    $bag318 = $hasilquiz['quiz_3_18'] + $bag318;
    $bag319 = $hasilquiz['quiz_3_19'] + $bag319;
    $bag320 = $hasilquiz['quiz_3_20'] + $bag320;
    $bag321 = $hasilquiz['quiz_3_21'] + $bag321;
    $bag322 = $hasilquiz['quiz_3_22'] + $bag322;
    $bag323 = $hasilquiz['quiz_3_23'] + $bag323;
    $bag324 = $hasilquiz['quiz_3_24'] + $bag324;
    $bag325 = $hasilquiz['quiz_3_25'] + $bag325;
    $bag326 = $hasilquiz['quiz_3_26'] + $bag326;
    $bag327 = $hasilquiz['quiz_3_27'] + $bag327;
    $bag328 = $hasilquiz['quiz_3_28'] + $bag328;
    $bag329 = $hasilquiz['quiz_3_29'] + $bag329;
    
    if($hasilquiz['quiz_1_1_a'] == "Y"){
      $bag11a = $bag11a + 1;
    };

    if($hasilquiz['quiz_1_1_b'] == "Y"){
      $bag11b = $bag11b + 1;
    };

    if($hasilquiz['quiz_1_1_c'] == "Y"){
      $bag11c = $bag11c + 1;
    };

    if($hasilquiz['quiz_1_2'] == "Y"){
      $bag12 = $bag12 + 1;
    };

    if($hasilquiz['quiz_1_3'] == "Y"){
      $bag13 = $bag13 + 1;
    };

        $no++;      
  ?>
  <tr>
    <td class="tg-aep7"><?= $no; ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_1_1_a'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_1_1_b'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_1_1_c'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_1_2'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_1_3'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_1'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_2'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_3'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_4'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_5'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_6'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_7'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_8'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_9'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_10'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_11'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_12'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_13'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_14'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_15'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_16'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_2_17'] ?></td>
      
    <?php $jmlbagian2 = $hasilquiz['quiz_2_1'] + $hasilquiz['quiz_2_2'] + $hasilquiz['quiz_2_3'] + $hasilquiz['quiz_2_4'] + $hasilquiz['quiz_2_5'] + $hasilquiz['quiz_2_6'] + $hasilquiz['quiz_2_7'] + $hasilquiz['quiz_2_8'] + $hasilquiz['quiz_2_9'] + $hasilquiz['quiz_2_10'] + $hasilquiz['quiz_2_11'] + $hasilquiz['quiz_2_12'] + $hasilquiz['quiz_2_13'] + $hasilquiz['quiz_2_14'] + $hasilquiz['quiz_2_15'] + $hasilquiz['quiz_2_16'] + $hasilquiz['quiz_2_17']; ?>
    <?php $total2 = $jmlbagian2 + $total2; ?>
    <?php $jmlbagian3 = $hasilquiz['quiz_3_18'] + $hasilquiz['quiz_3_19'] + $hasilquiz['quiz_3_20'] + $hasilquiz['quiz_3_21'] + $hasilquiz['quiz_3_22'] + $hasilquiz['quiz_3_23'] + $hasilquiz['quiz_3_24'] + $hasilquiz['quiz_3_25'] + $hasilquiz['quiz_3_26'] + $hasilquiz['quiz_3_27'] + $hasilquiz['quiz_3_28'] + $hasilquiz['quiz_3_29'] ?>
    <?php $total3 = $jmlbagian3 + $total3; ?>
    
    <?php $ratarata = $jmlbagian2 / 17; ?>
    <?php $totalrata2 = $ratarata + $totalrata2; ?>
    <?php $ratarata2 = $jmlbagian3 / 12; ?>
    <?php $totalrata3 = $ratarata2 + $totalrata3; ?>

    <?php $rata21 = $bag21 / $no; ?>
    <?php $rata22 = $bag22 / $no; ?>
    <?php $rata23 = $bag23 / $no; ?>
    <?php $rata24 = $bag24 / $no; ?>
    <?php $rata25 = $bag25 / $no; ?>
    <?php $rata26 = $bag26 / $no; ?>
    <?php $rata27 = $bag27 / $no; ?>
    <?php $rata28 = $bag28 / $no; ?>
    <?php $rata29 = $bag29 / $no; ?>
    <?php $rata210 = $bag210 / $no; ?>
    <?php $rata211 = $bag211 / $no; ?>
    <?php $rata212 = $bag212 / $no; ?>
    <?php $rata213 = $bag213 / $no; ?>
    <?php $rata214 = $bag214 / $no; ?>
    <?php $rata215 = $bag215 / $no; ?>
    <?php $rata216 = $bag216 / $no; ?>
    <?php $rata217 = $bag217 / $no; ?>

    <?php $rata318 = $bag318 / $no; ?>
    <?php $rata319 = $bag319 / $no; ?>
    <?php $rata320 = $bag320 / $no; ?>
    <?php $rata321 = $bag321 / $no; ?>
    <?php $rata322 = $bag322 / $no; ?>
    <?php $rata323 = $bag323 / $no; ?>
    <?php $rata324 = $bag324 / $no; ?>
    <?php $rata325 = $bag325 / $no; ?>
    <?php $rata326 = $bag326 / $no; ?>
    <?php $rata327 = $bag327 / $no; ?>
    <?php $rata328 = $bag328 / $no; ?>
    <?php $rata329 = $bag329 / $no; ?>
    
    <?php $totalratatata2 = $totalrata2 / $no; ?>
    <?php $totalratatata3 = $totalrata3 / $no; ?>
    
    <?php
    if(round($rata21, 2) <= 1.80){
      $ket21 = "SK";
    }elseif(round($rata21, 2) <= 2.60){
      $ket21 = "K";
    }elseif(round($rata21, 2) <= 3.40){
      $ket21 = "C";
    }elseif(round($rata21, 2) <= 4.20){
      $ket21 = "B";
    }elseif(round($rata21, 2) <= 5.00){
      $ket21 = "SB";
    }
    ?>

    <?php
    if(round($rata22, 2) <= 1.80){
      $ket22 = "SK";
    }elseif(round($rata22, 2) <= 2.60){
      $ket22 = "K";
    }elseif(round($rata22, 2) <= 3.40){
      $ket22 = "C";
    }elseif(round($rata22, 2) <= 4.20){
      $ket22 = "B";
    }elseif(round($rata22, 2) <= 5.00){
      $ket22 = "SB";
    }
    ?>

    <?php
    if(round($rata23, 2) <= 1.80){
      $ket23 = "SK";
    }elseif(round($rata23, 2) <= 2.60){
      $ket23 = "K";
    }elseif(round($rata23, 2) <= 3.40){
      $ket23 = "C";
    }elseif(round($rata23, 2) <= 4.20){
      $ket23 = "B";
    }elseif(round($rata23, 2) <= 5.00){
      $ket23 = "SB";
    }
    ?>

    <?php
    if(round($rata24, 2) <= 1.80){
      $ket24 = "SK";
    }elseif(round($rata24, 2) <= 2.60){
      $ket24 = "K";
    }elseif(round($rata24, 2) <= 3.40){
      $ket24 = "C";
    }elseif(round($rata24, 2) <= 4.20){
      $ket24 = "B";
    }elseif(round($rata24, 2) <= 5.00){
      $ket24 = "SB";
    }
    ?>

    <?php
    if(round($rata25, 2) <= 1.80){
      $ket25 = "SK";
    }elseif(round($rata25, 2) <= 2.60){
      $ket25 = "K";
    }elseif(round($rata25, 2) <= 3.40){
      $ket25 = "C";
    }elseif(round($rata25, 2) <= 4.20){
      $ket25 = "B";
    }elseif(round($rata25, 2) <= 5.00){
      $ket25 = "SB";
    }
    ?>

    <?php
    if(round($rata26, 2) <= 1.80){
      $ket26 = "SK";
    }elseif(round($rata26, 2) <= 2.60){
      $ket26 = "K";
    }elseif(round($rata26, 2) <= 3.40){
      $ket26 = "C";
    }elseif(round($rata26, 2) <= 4.20){
      $ket26 = "B";
    }elseif(round($rata26, 2) <= 5.00){
      $ket26 = "SB";
    }
    ?>

    <?php
    if(round($rata27, 2) <= 1.80){
      $ket27 = "SK";
    }elseif(round($rata27, 2) <= 2.60){
      $ket27 = "K";
    }elseif(round($rata27, 2) <= 3.40){
      $ket27 = "C";
    }elseif(round($rata27, 2) <= 4.20){
      $ket27 = "B";
    }elseif(round($rata27, 2) <= 5.00){
      $ket27 = "SB";
    }
    ?>

    <?php
    if(round($rata28, 2) <= 1.80){
      $ket28 = "SK";
    }elseif(round($rata28, 2) <= 2.60){
      $ket28 = "K";
    }elseif(round($rata28, 2) <= 3.40){
      $ket28 = "C";
    }elseif(round($rata28, 2) <= 4.20){
      $ket28 = "B";
    }elseif(round($rata28, 2) <= 5.00){
      $ket28 = "SB";
    }
    ?>

    <?php
    if(round($rata29, 2) <= 1.80){
      $ket29 = "SK";
    }elseif(round($rata29, 2) <= 2.60){
      $ket29 = "K";
    }elseif(round($rata29, 2) <= 3.40){
      $ket29 = "C";
    }elseif(round($rata29, 2) <= 4.20){
      $ket29 = "B";
    }elseif(round($rata29, 2) <= 5.00){
      $ket29 = "SB";
    }
    ?>

    <?php
    if(round($rata210, 2) <= 1.80){
      $ket210 = "SK";
    }elseif(round($rata210, 2) <= 2.60){
      $ket210 = "K";
    }elseif(round($rata210, 2) <= 3.40){
      $ket210 = "C";
    }elseif(round($rata210, 2) <= 4.20){
      $ket210 = "B";
    }elseif(round($rata210, 2) <= 5.00){
      $ket210 = "SB";
    }
    ?>

    <?php
    if(round($rata211, 2) <= 1.80){
      $ket211 = "SK";
    }elseif(round($rata211, 2) <= 2.60){
      $ket211 = "K";
    }elseif(round($rata211, 2) <= 3.40){
      $ket211 = "C";
    }elseif(round($rata211, 2) <= 4.20){
      $ket211 = "B";
    }elseif(round($rata211, 2) <= 5.00){
      $ket211 = "SB";
    }
    ?>

    <?php
    if(round($rata212, 2) <= 1.80){
      $ket212 = "SK";
    }elseif(round($rata212, 2) <= 2.60){
      $ket212 = "K";
    }elseif(round($rata212, 2) <= 3.40){
      $ket212 = "C";
    }elseif(round($rata212, 2) <= 4.20){
      $ket212 = "B";
    }elseif(round($rata212, 2) <= 5.00){
      $ket212 = "SB";
    }
    ?>

    <?php
    if(round($rata213, 2) <= 1.80){
      $ket213 = "SK";
    }elseif(round($rata213, 2) <= 2.60){
      $ket213 = "K";
    }elseif(round($rata213, 2) <= 3.40){
      $ket213 = "C";
    }elseif(round($rata213, 2) <= 4.20){
      $ket213 = "B";
    }elseif(round($rata213, 2) <= 5.00){
      $ket213 = "SB";
    }
    ?>

    <?php
    if(round($rata214, 2) <= 1.80){
      $ket214 = "SK";
    }elseif(round($rata214, 2) <= 2.60){
      $ket214 = "K";
    }elseif(round($rata214, 2) <= 3.40){
      $ket214 = "C";
    }elseif(round($rata214, 2) <= 4.20){
      $ket214 = "B";
    }elseif(round($rata214, 2) <= 5.00){
      $ket214 = "SB";
    }
    ?>

    <?php
    if(round($rata215, 2) <= 1.80){
      $ket215 = "SK";
    }elseif(round($rata215, 2) <= 2.60){
      $ket215 = "K";
    }elseif(round($rata215, 2) <= 3.40){
      $ket215 = "C";
    }elseif(round($rata215, 2) <= 4.20){
      $ket215 = "B";
    }elseif(round($rata215, 2) <= 5.00){
      $ket215 = "SB";
    }
    ?>

    <?php
    if(round($rata216, 2) <= 1.80){
      $ket216 = "SK";
    }elseif(round($rata216, 2) <= 2.60){
      $ket216 = "K";
    }elseif(round($rata216, 2) <= 3.40){
      $ket216 = "C";
    }elseif(round($rata216, 2) <= 4.20){
      $ket216 = "B";
    }elseif(round($rata216, 2) <= 5.00){
      $ket216 = "SB";
    }
    ?>

    <?php
    if(round($rata217, 2) <= 1.80){
      $ket217 = "SK";
    }elseif(round($rata217, 2) <= 2.60){
      $ket217 = "K";
    }elseif(round($rata217, 2) <= 3.40){
      $ket217 = "C";
    }elseif(round($rata217, 2) <= 4.20){
      $ket217 = "B";
    }elseif(round($rata217, 2) <= 5.00){
      $ket217 = "SB";
    }
    ?>

    <?php
    if(round($rata318, 2) <= 1.80){
      $ket318 = "SK";
    }elseif(round($rata318, 2) <= 2.60){
      $ket318 = "K";
    }elseif(round($rata318, 2) <= 3.40){
      $ket318 = "C";
    }elseif(round($rata318, 2) <= 4.20){
      $ket318 = "B";
    }elseif(round($rata318, 2) <= 5.00){
      $ket318 = "SB";
    }
    ?>

    <?php
    if(round($rata319, 2) <= 1.80){
      $ket319 = "SK";
    }elseif(round($rata319, 2) <= 2.60){
      $ket319 = "K";
    }elseif(round($rata319, 2) <= 3.40){
      $ket319 = "C";
    }elseif(round($rata319, 2) <= 4.20){
      $ket319 = "B";
    }elseif(round($rata319, 2) <= 5.00){
      $ket319 = "SB";
    }
    ?>

    <?php
    if(round($rata320, 2) <= 1.80){
      $ket320 = "SK";
    }elseif(round($rata320, 2) <= 2.60){
      $ket320 = "K";
    }elseif(round($rata320, 2) <= 3.40){
      $ket320 = "C";
    }elseif(round($rata320, 2) <= 4.20){
      $ket320 = "B";
    }elseif(round($rata320, 2) <= 5.00){
      $ket320 = "SB";
    }
    ?>

    <?php
    if(round($rata321, 2) <= 1.80){
      $ket321 = "SK";
    }elseif(round($rata321, 2) <= 2.60){
      $ket321 = "K";
    }elseif(round($rata321, 2) <= 3.40){
      $ket321 = "C";
    }elseif(round($rata321, 2) <= 4.20){
      $ket321 = "B";
    }elseif(round($rata321, 2) <= 5.00){
      $ket321 = "SB";
    }
    ?>

    <?php
    if(round($rata322, 2) <= 1.80){
      $ket322 = "SK";
    }elseif(round($rata322, 2) <= 2.60){
      $ket322 = "K";
    }elseif(round($rata322, 2) <= 3.40){
      $ket322 = "C";
    }elseif(round($rata322, 2) <= 4.20){
      $ket322 = "B";
    }elseif(round($rata322, 2) <= 5.00){
      $ket322 = "SB";
    }
    ?>

    <?php
    if(round($rata323, 2) <= 1.80){
      $ket323 = "SK";
    }elseif(round($rata323, 2) <= 2.60){
      $ket323 = "K";
    }elseif(round($rata323, 2) <= 3.40){
      $ket323 = "C";
    }elseif(round($rata323, 2) <= 4.20){
      $ket323 = "B";
    }elseif(round($rata323, 2) <= 5.00){
      $ket323 = "SB";
    }
    ?>

    <?php
    if(round($rata324, 2) <= 1.80){
      $ket324 = "SK";
    }elseif(round($rata324, 2) <= 2.60){
      $ket324 = "K";
    }elseif(round($rata324, 2) <= 3.40){
      $ket324 = "C";
    }elseif(round($rata324, 2) <= 4.20){
      $ket324 = "B";
    }elseif(round($rata324, 2) <= 5.00){
      $ket324 = "SB";
    }
    ?>

    <?php
    if(round($rata325, 2) <= 1.80){
      $ket325 = "SK";
    }elseif(round($rata325, 2) <= 2.60){
      $ket325 = "K";
    }elseif(round($rata325, 2) <= 3.40){
      $ket325 = "C";
    }elseif(round($rata325, 2) <= 4.20){
      $ket325 = "B";
    }elseif(round($rata325, 2) <= 5.00){
      $ket325 = "SB";
    }
    ?>

    <?php
    if(round($rata326, 2) <= 1.80){
      $ket326 = "SK";
    }elseif(round($rata326, 2) <= 2.60){
      $ket326 = "K";
    }elseif(round($rata326, 2) <= 3.40){
      $ket326 = "C";
    }elseif(round($rata326, 2) <= 4.20){
      $ket326 = "B";
    }elseif(round($rata326, 2) <= 5.00){
      $ket326 = "SB";
    }
    ?>

    <?php
    if(round($rata327, 2) <= 1.80){
      $ket327 = "SK";
    }elseif(round($rata327, 2) <= 2.60){
      $ket327 = "K";
    }elseif(round($rata327, 2) <= 3.40){
      $ket327 = "C";
    }elseif(round($rata327, 2) <= 4.20){
      $ket327 = "B";
    }elseif(round($rata327, 2) <= 5.00){
      $ket327 = "SB";
    }
    ?>

    <?php
    if(round($rata328, 2) <= 1.80){
      $ket328 = "SK";
    }elseif(round($rata328, 2) <= 2.60){
      $ket328 = "K";
    }elseif(round($rata328, 2) <= 3.40){
      $ket328 = "C";
    }elseif(round($rata328, 2) <= 4.20){
      $ket328 = "B";
    }elseif(round($rata328, 2) <= 5.00){
      $ket328 = "SB";
    }
    ?>

    <?php
    if(round($rata329, 2) <= 1.80){
      $ket329 = "SK";
    }elseif(round($rata329, 2) <= 2.60){
      $ket329 = "K";
    }elseif(round($rata329, 2) <= 3.40){
      $ket329 = "C";
    }elseif(round($rata329, 2) <= 4.20){
      $ket329 = "B";
    }elseif(round($rata329, 2) <= 5.00){
      $ket329 = "SB";
    }
    ?>
    
    <?php
    if(round($totalratatata2, 2) <= 1.80){
      $ketbagian2 = "SK";
    }elseif(round($totalratatata2, 2) <= 2.60){
      $ketbagian2 = "K";
    }elseif(round($totalratatata2, 2) <= 3.40){
      $ketbagian2 = "C";
    }elseif(round($totalratatata2, 2) <= 4.20){
      $ketbagian2 = "B";
    }elseif(round($totalratatata2, 2) <= 5.00){
      $ketbagian2 = "SB";
    }
    ?>

    <?php
    if(round($totalratatata3, 2) <= 1.80){
      $ketbagian3 = "SK";
    }elseif(round($totalratatata3, 2) <= 2.60){
      $ketbagian3 = "K";
    }elseif(round($totalratatata3, 2) <= 3.40){
      $ketbagian3 = "C";
    }elseif(round($totalratatata3, 2) <= 4.20){
      $ketbagian3 = "B";
    }elseif(round($totalratatata3, 2) <= 5.00){
      $ketbagian3 = "SB";
    }
    ?>

    <td class="tg-aep7"><?= $jmlbagian2; ?></td>
    <td class="tg-aep7"><?= round($ratarata, 2); ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_3_18'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_3_19'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_3_20'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_3_21'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_3_22'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_3_23'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_3_24'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_3_25'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_3_26'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_3_27'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_3_28'] ?></td>
    <td class="tg-aep7"><?= $hasilquiz['quiz_3_29'] ?></td>
    <td class="tg-aep7"><?= $jmlbagian3; ?></td>
    <td class="tg-aep7"><?= round($ratarata2, 2); ?></td>
  </tr>
  <?php }

  ?>
  <tr>
    <td class="tg-pgcc"><b>Jumlah</b></td>
    <td class="tg-qj9f"><?= $bag11a ?></td>
    <td class="tg-qj9f"><?= $bag11b ?></td>
    <td class="tg-qj9f"><?= $bag11c ?></td>
    <td class="tg-qj9f"><?= $bag12 ?></td>
    <td class="tg-qj9f"><?= $bag13 ?></td>
    <td class="tg-qj9f"><?= $bag21 ?></td>
    <td class="tg-qj9f"><?= $bag22 ?></td>
    <td class="tg-qj9f"><?= $bag23 ?></td>
    <td class="tg-qj9f"><?= $bag24 ?></td>
    <td class="tg-qj9f"><?= $bag25 ?></td>
    <td class="tg-qj9f"><?= $bag26 ?></td>
    <td class="tg-qj9f"><?= $bag27 ?></td>
    <td class="tg-qj9f"><?= $bag28 ?></td>
    <td class="tg-qj9f"><?= $bag29 ?></td>
    <td class="tg-qj9f"><?= $bag210 ?></td>
    <td class="tg-qj9f"><?= $bag211 ?></td>
    <td class="tg-qj9f"><?= $bag212 ?></td>
    <td class="tg-qj9f"><?= $bag213 ?></td>
    <td class="tg-qj9f"><?= $bag214 ?></td>
    <td class="tg-qj9f"><?= $bag215 ?></td>
    <td class="tg-qj9f"><?= $bag216 ?></td>
    <td class="tg-qj9f"><?= $bag217 ?></td>
    <td class="tg-qj9f"><?= $total2 ?></td>
    <td class="tg-qj9f"><?= round($totalrata2,2) ?></td>
    <td class="tg-qj9f"><?= $bag318 ?></td>
    <td class="tg-qj9f"><?= $bag319 ?></td>
    <td class="tg-qj9f"><?= $bag320 ?></td>
    <td class="tg-qj9f"><?= $bag321 ?></td>
    <td class="tg-qj9f"><?= $bag322 ?></td>
    <td class="tg-qj9f"><?= $bag323 ?></td>
    <td class="tg-qj9f"><?= $bag324 ?></td>
    <td class="tg-qj9f"><?= $bag325 ?></td>
    <td class="tg-qj9f"><?= $bag326 ?></td>
    <td class="tg-qj9f"><?= $bag327 ?></td>
    <td class="tg-qj9f"><?= $bag328 ?></td>
    <td class="tg-qj9f"><?= $bag329 ?></td>
    <td class="tg-qj9f"><?= $total3 ?></td>
    <td class="tg-qj9f"><?= round($totalrata3,2) ?></td>
  </tr>
  <tr>
    <td class="tg-pgcc"><b>Rata-rata</b></td>
    <td class="tg-qj9f"></td>
    <td class="tg-qj9f"></td>
    <td class="tg-qj9f"></td>
    <td class="tg-qj9f"></td>
    <td class="tg-qj9f"></td>
    <td class="tg-qj9f"><?= $rata21; ?></td>
    <td class="tg-qj9f"><?= $rata22; ?></td>
    <td class="tg-qj9f"><?= $rata23; ?></td>
    <td class="tg-qj9f"><?= $rata24; ?></td>
    <td class="tg-qj9f"><?= $rata25; ?></td>
    <td class="tg-qj9f"><?= $rata26; ?></td>
    <td class="tg-qj9f"><?= $rata27; ?></td>
    <td class="tg-qj9f"><?= $rata28; ?></td>
    <td class="tg-qj9f"><?= $rata29; ?></td>
    <td class="tg-qj9f"><?= $rata210; ?></td>
    <td class="tg-qj9f"><?= $rata211; ?></td>
    <td class="tg-qj9f"><?= $rata212; ?></td>
    <td class="tg-qj9f"><?= $rata213; ?></td>
    <td class="tg-qj9f"><?= $rata214; ?></td>
    <td class="tg-qj9f"><?= $rata215; ?></td>
    <td class="tg-qj9f"><?= $rata216; ?></td>
    <td class="tg-qj9f"><?= $rata217; ?></td>
    <td class="tg-qj9f"></td>
    <td class="tg-qj9f"><?= round($totalratatata2, 2); ?></td>
    <td class="tg-qj9f"><?= $rata318; ?></td>
    <td class="tg-qj9f"><?= $rata319; ?></td>
    <td class="tg-qj9f"><?= $rata320; ?></td>
    <td class="tg-qj9f"><?= $rata321; ?></td>
    <td class="tg-qj9f"><?= $rata322; ?></td>
    <td class="tg-qj9f"><?= $rata323; ?></td>
    <td class="tg-qj9f"><?= $rata324; ?></td>
    <td class="tg-qj9f"><?= $rata325; ?></td>
    <td class="tg-qj9f"><?= $rata326; ?></td>
    <td class="tg-qj9f"><?= $rata327; ?></td>
    <td class="tg-qj9f"><?= $rata328; ?></td>
    <td class="tg-qj9f"><?= $rata329; ?></td>
    <td class="tg-qj9f"></td>
    <td class="tg-qj9f"><?= round($totalratatata3, 2); ?></td>
  </tr>
  <tr>
    <td class="tg-pgcc"><b>Kategori</b></td>
    <td class="tg-qj9f"></td>
    <td class="tg-qj9f"></td>
    <td class="tg-qj9f"></td>
    <td class="tg-qj9f"></td>
    <td class="tg-qj9f"></td>
    <td class="tg-qj9f"><?= $ket21 ?></td>
    <td class="tg-qj9f"><?= $ket22 ?></td>
    <td class="tg-qj9f"><?= $ket23 ?></td>
    <td class="tg-qj9f"><?= $ket24 ?></td>
    <td class="tg-qj9f"><?= $ket25 ?></td>
    <td class="tg-qj9f"><?= $ket26 ?></td>
    <td class="tg-qj9f"><?= $ket27 ?></td>
    <td class="tg-qj9f"><?= $ket28 ?></td>
    <td class="tg-qj9f"><?= $ket29 ?></td>
    <td class="tg-qj9f"><?= $ket210 ?></td>
    <td class="tg-qj9f"><?= $ket211 ?></td>
    <td class="tg-qj9f"><?= $ket212 ?></td>
    <td class="tg-qj9f"><?= $ket213 ?></td>
    <td class="tg-qj9f"><?= $ket214 ?></td>
    <td class="tg-qj9f"><?= $ket215 ?></td>
    <td class="tg-qj9f"><?= $ket216 ?></td>
    <td class="tg-qj9f"><?= $ket217 ?></td>
    <td class="tg-qj9f"></td>
    <td class="tg-qj9f"><?= $ketbagian2; ?></td>
    <td class="tg-qj9f"><?= $ket318 ?></td>
    <td class="tg-qj9f"><?= $ket319 ?></td>
    <td class="tg-qj9f"><?= $ket320 ?></td>
    <td class="tg-qj9f"><?= $ket321 ?></td>
    <td class="tg-qj9f"><?= $ket322 ?></td>
    <td class="tg-qj9f"><?= $ket323 ?></td>
    <td class="tg-qj9f"><?= $ket324 ?></td>
    <td class="tg-qj9f"><?= $ket325 ?></td>
    <td class="tg-qj9f"><?= $ket326 ?></td>
    <td class="tg-qj9f"><?= $ket327 ?></td>
    <td class="tg-qj9f"><?= $ket328 ?></td>
    <td class="tg-qj9f"><?= $ket329 ?></td>
    <td class="tg-qj9f"></td>
    <td class="tg-qj9f"><?= $ketbagian3; ?></td>
  </tr>
</table>

</div>
</body>
</html>
