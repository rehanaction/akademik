<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('soalquiz'));
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('pilquiz'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('hasilquiz'));
	require_once(Route::getModelPath('monitoring'));
	require_once(Route::getModelPath('krs'));
	require_once(Route::getUIPath('combo'));
	// variabel request
	// $r_kurikulum = Modul::setRequest($_POST['kurikulum'],'KURIKULUM');
	
	// combo
	// $l_kurikulum = uCombo::kurikulum($conn,$r_kurikulum,'kurikulum','onchange="goSubmit()"',false);
	
	// properti halaman
	$p_title = 'Data Hasil Quisioner';
	$p_tbwidth = "100%";
	$p_aktivitas = 'KULIAH';
	$p_listpage = 'list_mkquiz';
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	if(empty($r_key))
		Route::navigate($p_listpage);
		
	$p_model = mHasilQuiz;
	
	// variabel request
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_periode=$r_tahun.$r_semester;
	// combo
	
	
	// kolom soal
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'soal', 'label' => 'Soal');
	$a_kolom[] = array('kolom' => 'status', 'label' => 'Status Soal');
	
	//kolom pilihan
	$b_kolom = array();
	$b_kolom[] = array('kolom' => 'pilihan', 'label' => 'Kode');
	$b_kolom[] = array('kolom' => 'point', 'label' => 'Nilai Point');
	$b_kolom[] = array('kolom' => 'keterangan', 'label' => 'Keterangan Pilihan');
	
	$r_act = $_REQUEST['act'];
	if(!Akademik::isMhs()) {
		if(isset($_REQUEST['npm'])){
			// cek aksi
			$r_nim = CStr::removeSpecial($_REQUEST['npm']);
			if(Akademik::isDosen()){
				$r_nip = Modul::getUserName();
				$display="none";
				}
			else
				$r_nip = '';
			
			if($r_act == 'first')
				$r_npm = mMahasiswa::getFirstNIM($conn,$r_nim,$r_nip);
			else if($r_act == 'prev')
				$r_npm = mMahasiswa::getPrevNIM($conn,$r_nim,$r_nip);
			else if($r_act == 'next')
				$r_npm = mMahasiswa::getNextNIM($conn,$r_nim,$r_nip);
			else if($r_act == 'last')
				$r_npm = mMahasiswa::getLastNIM($conn,$r_nim,$r_nip);
			else
				$r_npm = $r_nim;
		}else{
			$key=explode("|",$r_key);
			$r_npm=$key[6];
		}
	}
	else if(Akademik::isMhs()){
		$r_npm = Modul::getUserName();
		$display="none";
	}
	
	//mrubah NPM pada $r_key
	$arr_key=explode("|",$r_key);
	$arr_key[6]=$r_npm;
	$r_key=implode("|",$arr_key);
	
	// jika ada aksi
	if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex

	$r_sort = Page::setSort('idsoal');
	$b_sort = Page::setSort('pilihan');
	$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	// if(!empty($r_kurikulum)) $a_filter[] = $p_model::getListFilter('thnkurikulum',$r_kurikulum);
	if(!empty($r_periode)){
		$a_filter[] = $p_model::getListFilter('periode',$r_periode);
		$b_filter[] = mPilQuiz::getListFilter('periode',$r_periode);
	}
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_npm);
	
	//$a_data = mSoalQuiz::getListData($conn,$a_kolom,$r_sort,$a_filter);
	//$a_pilihan = mPilQuiz::getListData($conn,$b_kolom,$b_sort,$b_filter);
	
	
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$a_infokelas = mKelas::getDataSingkat($conn,$r_key,true,$key[5]);
	$a_data=mMonitoring::getHasilQuiz($conn,$r_key,$r_npm);
	
	//require_once(Route::getViewPath('inc_list'));
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<script type="text/javascript" src="scripts/perwalian.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<div style="float:left; width:18%; ">
			<?php //require_once('inc_headermahasiswa.php'); ?>
			</div>
			<form name="pageform" id="pageform" method="post">
				<?php //require_once('inc_headermhs_krs.php') ?>
				<br>
				<center>
				 
				<div>
				<?php //require_once('inc_listfilter.php'); ?>
				<?php require_once('inc_headerkelas_quiz.php') ?>
				<br>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							
						</div>
					</header>
				 
				<?	/*************/
					/* LIST DATA */
					/*************/
				?>
				
				<?php foreach ($a_data as $row) { ?>
				
						<style type="text/css">
						.tg  {border-collapse:collapse;border-spacing:0;}
						.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
						.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
						.tg .tg-88nc{font-weight:bold;border-color:inherit;text-align:center}
						.tg .tg-c3ow{border-color:inherit;text-align:center;vertical-align:top}
						.tg .tg-uys7{border-color:inherit;text-align:center}
						.tg .tg-fymr{font-weight:bold;border-color:inherit;text-align:left;vertical-align:top}
						.tg .tg-7btt{font-weight:bold;border-color:inherit;text-align:center;vertical-align:top}
						.tg .tg-0pky{border-color:inherit;text-align:left;vertical-align:top}
						</style>
						<table class="tg" width="100%">
						  <tr style="background-color: darkgray;">
						    <th class="tg-uys7" colspan="7"><span style="font-weight:bold">BAGIAN I</span></th>
						  </tr>
						  <tr>
						    <td class="tg-fymr" colspan="7"><span style="font-weight:700">Berikan pendapat Anda yang paling sesuai pada kolom pilihan yang tersedia.</span><br><span style="font-weight:700">YA = Dosen memberikan/melaksanakan sesuai daftar pertanyaan.</span><br><span style="font-weight:700">TIDAK = Dosen tidak memberikan/tidak melaksanakan sesuai daftar pertanyaan.</span></td>
						  </tr>
						  <tr style="background-color: darkgray;">
						    <td class="tg-7btt" width="2%">No.</td>
						    <td class="tg-7btt" colspan="4"><span style="font-weight:700">PERTANYAAN TENTANG KINERJA DOSEN MENGAJAR</span></td>
						    <td class="tg-7btt" width="10%">YA</td>
						    <td class="tg-7btt" width="10%">TIDAK</td>
						  </tr>
						  <tr>
						    <td class="tg-88nc" rowspan="4">1.</td>
						    <td class="tg-0pky" colspan="4" style="background-color: darkgray;"><span style="font-weight:700">Pada Tatap Muka pertama, dosen menjelaskan antara lain:</span></td>
						    <td class="tg-0pky"></td>
						    <td class="tg-0pky"></td>
						  </tr>
						  <tr>
						    <td class="tg-0pky" colspan="4"><span style="font-weight:700">a. Rencana Proses Perkuliahan, Silabi, Kompetensi mata kuliah</span></td>
						    <?php if ($row['quiz_1_1_a'] == "Y") { ?>
							    <td class="tg-0pky"><center><img src="images/check.png"></center></td>
							    <td class="tg-0pky"><center></center></td>
						    <?php }else{ ?>
						    	<td class="tg-0pky"><center></center></td>
							    <td class="tg-0pky"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-0pky" colspan="4"><span style="font-weight:700">b. Sistem dan komponen penilaian</span></td>
						    <?php if ($row['quiz_1_1_b'] == "Y") { ?>
							    <td class="tg-0pky"><center><img src="images/check.png"></center></td>
							    <td class="tg-0pky"><center></center></td>
						    <?php }else{ ?>
						    	<td class="tg-0pky"><center></center></td>
							    <td class="tg-0pky"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-0pky" colspan="4"><span style="font-weight:700">c. Buku referensi yang digunakan</span></td>
						    <?php if ($row['quiz_1_1_c'] == "Y") { ?>
							    <td class="tg-0pky"><center><img src="images/check.png"></center></td>
							    <td class="tg-0pky"><center></center></td>
						    <?php }else{ ?>
						    	<td class="tg-0pky"><center></center></td>
							    <td class="tg-0pky"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-7btt">2.</td>
						    <td class="tg-fymr" colspan="4">Tersedianya modul/diktat/handouts untuk mahasiswa<br>(baik melalui fasilitas internet/e-learning atau secara langsung/bentuk fisik)</td>
						    <?php if ($row['quiz_1_2'] == "Y") { ?>
							    <td class="tg-0pky"><center><img src="images/check.png"></center></td>
							    <td class="tg-0pky"><center></center></td>
						    <?php }else{ ?>
						    	<td class="tg-0pky"><center></center></td>
							    <td class="tg-0pky"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-7btt">3.</td>
						    <td class="tg-fymr" colspan="4">Menggunakan fasilitas multimedia dalam proses pembelajaran (komputer,LCD,dll)</td>
						    <?php if ($row['quiz_1_3'] == "Y") { ?>
							    <td class="tg-0pky"><center><img src="images/check.png"></center></td>
							    <td class="tg-0pky"><center></center></td>
						    <?php }else{ ?>
						    	<td class="tg-0pky"><center></center></td>
							    <td class="tg-0pky"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						    
						  </tr>
						</table>
						<br>&nbsp;
						<style type="text/css">
						.tg  {border-collapse:collapse;border-spacing:0;}
						.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
						.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
						.tg .tg-88nc{font-weight:bold;border-color:inherit;text-align:center}
						.tg .tg-kiyi{font-weight:bold;border-color:inherit;text-align:left}
						.tg .tg-uys7{border-color:inherit;text-align:center}
						.tg .tg-xldj{border-color:inherit;text-align:left}
						.tg .tg-7btt{font-weight:bold;border-color:inherit;text-align:center;vertical-align:top}
						.tg .tg-fymr{font-weight:bold;border-color:inherit;text-align:left;vertical-align:top}
						.tg .tg-0pky{border-color:inherit;text-align:left;vertical-align:top}
						</style>
						<table class="tg" width="100%">
						  <tr style="background-color: darkgray;">
						    <th class="tg-uys7" colspan="7"><span style="font-weight:bold;">BAGIAN II</span></th>
						  </tr>
						  <tr>
						    <td class="tg-kiyi" colspan="7">Berikan pendapat Anda yang paling sesuai pada kolom penilaian/pilihan yang tersedia.<br>1 = Sangat Kurang;    2 = Kurang;    3 = Cukup;     4 = Baik;       5 = Sangat Baik</td>
						  </tr>
						  <tr style="background-color: darkgray;">
						    <td class="tg-88nc" width="2%">No.</td>
						    <td class="tg-88nc">PERTANYAAN TENTANG KINERJA DOSEN MENGAJAR</td>
						    <td class="tg-88nc" width="3%">1</td>
						    <td class="tg-88nc" width="3%">2</td>
						    <td class="tg-88nc" width="3%">3</td>
						    <td class="tg-88nc" width="3%">4</td>
						    <td class="tg-88nc" width="3%">5</td>
						  </tr>
						  <tr style="background-color: darkgray;">
						    <td class="tg-88nc">A.</td>
						    <td class="tg-88nc" colspan="6" >KOMPENTENSI PEDAGOGIK</td>
						  </tr>
						  <tr>
						    <td class="tg-88nc">1.</td>
						    <td class="tg-kiyi">Kesiapan memberikan kuliah/praktek/praktikum</td>
						    <?php if ($row['quiz_2_1'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_1'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_1'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_1'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_1'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-88nc">2.</td>
						    <td class="tg-kiyi">Keteraturan/ketertiban penyelenggaraan waktu perkuliahan</td>
						    <?php if ($row['quiz_2_2'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_2'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_2'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_2'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_2'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-88nc">3.</td>
						    <td class="tg-kiyi">Kesesuaian Materi ajar dengan Rencana Pembelajaran Semester (RPS)</td>
						    <?php if ($row['quiz_2_3'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_3'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_3'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_3'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_3'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-7btt">4.</td>
						    <td class="tg-fymr">Kejelasan menyampaikan materi/jawaban atas pertanyaan di kelas</td>
						    <?php if ($row['quiz_2_4'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_4'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_4'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_4'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_4'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-7btt">5.</td>
						    <td class="tg-fymr">Kreativitas melakukan proses pembelajaran agar menarik, memotivasi dan tidak membosankan</td>
						    <?php if ($row['quiz_2_5'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_5'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_5'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_5'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_5'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-7btt">6.</td>
						    <td class="tg-fymr">Melaksanakan partisipasi kelas, memberikan kesempatan bertanya.</td>
						    <?php if ($row['quiz_2_6'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_6'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_6'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_6'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_6'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr style="background-color: darkgray;">
						    <td class="tg-7btt">B.</td>
						    <td class="tg-7btt" colspan="6">KOMPETENSI PROFESIONAL</td>
						  </tr>
						  <tr>
						    <td class="tg-7btt">7.</td>
						    <td class="tg-fymr">Memahami konsep, struktur, materi dan menerapkan pola pikir yang dapat dipahami oleh mahasiswa</td>
						    <?php if ($row['quiz_2_7'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_7'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_7'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_7'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_7'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-7btt">8.</td>
						    <td class="tg-fymr">Mampu menjelaskan pokok bahasan/materi/topik dan memberi contoh yang relevan</td>
						    <?php if ($row['quiz_2_8'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_8'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_8'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_8'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_8'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-7btt">9.</td>
						    <td class="tg-fymr">Mampu menjelaskan keterkaitan bidang/topik yang diajarkan dengan bidang lain, dan isu-isu mutakhir serta mencari alternatif solusinya</td>
						    <?php if ($row['quiz_2_9'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_9'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_9'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_9'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_9'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr style="background-color: darkgray;">
						    <td class="tg-7btt">C.</td>
						    <td class="tg-7btt" colspan="6">KOMPETENSI KEPRIBADIAN</td>
						  </tr>
						  <tr>
						    <td class="tg-7btt">10.</td>
						    <td class="tg-fymr">Berwibawa sebagai pribadi Dosen dan memiliki integritas, menampilkan sikap kepemimpinan</td>
						    <?php if ($row['quiz_2_10'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_10'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_10'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_10'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_10'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-7btt">11.</td>
						    <td class="tg-fymr">Kepantasan dan kerapihan dalam penampilan</td>
						    <?php if ($row['quiz_2_11'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_11'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_11'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_11'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_11'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-7btt">12.</td>
						    <td class="tg-fymr">Menjadi contoh dalam bersikap dan berperilaku sesuai dengan kode etik Dosen</td>
						    <?php if ($row['quiz_2_12'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_12'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_12'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_12'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_12'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-7btt">13.</td>
						    <td class="tg-fymr">Berprilaku kreatif, inovatif, adaptif, produktif dan berorientasi pada pengembangan berkelanjutan</td>
						    <?php if ($row['quiz_2_13'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_13'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_13'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_13'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_13'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr style="background-color: darkgray;">
						    <td class="tg-7btt">D.</td>
						    <td class="tg-7btt" colspan="6">KOMPETENSI SOSIAL</td>
						  </tr>
						  <tr>
						    <td class="tg-7btt">14.</td>
						    <td class="tg-fymr">Kepedulian kepada mahasiswa yang mengikuti kuliahnya</td>
						    <?php if ($row['quiz_2_14'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_14'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_14'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_14'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_14'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-7btt">15.</td>
						    <td class="tg-fymr">Toleransi Dosen terhadap keberagaman mahasiswa</td>
						    <?php if ($row['quiz_2_15'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_15'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_15'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_15'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_15'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-7btt">16.</td>
						    <td class="tg-fymr">Berinteraksi dan berkomunikasi efektif, santun, dan adaptif</td>
						    <?php if ($row['quiz_2_16'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_16'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_16'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_16'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_16'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-7btt">17.</td>
						    <td class="tg-fymr">Bersikap terbuka dan menghargai pendapat, saran dan kritik membangun untuk perbaikan</td>
						    <?php if ($row['quiz_2_17'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_17'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_17'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_17'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_2_17'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						</table>
						<br>&nbsp;
						<style type="text/css">
						.tg  {border-collapse:collapse;border-spacing:0;}
						.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
						.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
						.tg .tg-1wig{font-weight:bold;text-align:left;vertical-align:top}
						.tg .tg-hgcj{font-weight:bold;text-align:center}
						.tg .tg-5ua9{font-weight:bold;text-align:left}
						.tg .tg-s268{text-align:left}
						.tg .tg-amwm{font-weight:bold;text-align:center;vertical-align:top}
						.tg .tg-0lax{text-align:left;vertical-align:top}
						</style>
						<table class="tg" width="100%">
						  <tr style="background-color: darkgray;">
						    <th class="tg-hgcj" colspan="7">BAGIAN III</th>
						  </tr>
						  <tr>
						    <td class="tg-5ua9" colspan="7">Berikan pendapat Anda yang paling sesuai pada kolom penilaian/pilihan yang tersedia.<br>1 = Sangat Tidak Puas;  &nbsp;&nbsp;2 = Kurang Puas;    3 = Cukup Puas;     4 = Puas;       5 = Sangat Puas</td>
						  </tr>
						  <tr style="background-color: darkgray;">
						    <td class="tg-hgcj" width="2%">No.</td>
						    <td class="tg-hgcj">PERTANYAAN TENTANG KEPUASAN MAHASISWA PADA SAAT PROSES PEMBELAJARAN</td>
						    <td class="tg-hgcj" width="3%">1</td>
						    <td class="tg-hgcj" width="3%">2</td>
						    <td class="tg-hgcj" width="3%">3</td>
						    <td class="tg-hgcj" width="3%">4</td>
						    <td class="tg-hgcj" width="3%">5</td>
						  </tr>
						  <tr style="background-color: darkgray;">
						    <td class="tg-hgcj">A.</td>
						    <td class="tg-hgcj" colspan="6">TANGIBLE ( BUKTI LANGSUNG )</td>
						  </tr>
						  <tr>
						    <td class="tg-hgcj">18.</td>
						    <td class="tg-5ua9">Strategi belajar yang disajikan</td>
						    <?php if ($row['quiz_3_18'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_18'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_18'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_18'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_18'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-hgcj">19.</td>
						    <td class="tg-5ua9">Pemanfaatan alat/media yang digunakan</td>
						    <?php if ($row['quiz_3_19'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_19'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_19'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_19'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_19'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr style="background-color: darkgray;">
						    <td class="tg-hgcj">B.</td>
						    <td class="tg-hgcj" colspan="6">REABILITY ( KEHANDALAN )</td>
						  </tr>
						  <tr>
						    <td class="tg-hgcj">20.</td>
						    <td class="tg-5ua9">Mampu menjelaskan materi dengan baik sesuai dengan tujuan pembelajaran</td>
						    <?php if ($row['quiz_3_20'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_20'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_20'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_20'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_20'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-hgcj">21.</td>
						    <td class="tg-5ua9">Mampu meberi tugas yang sesuai dengan materi/SAP sebagai umpan balik</td>
						    <?php if ($row['quiz_3_21'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_21'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_21'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_21'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_21'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-hgcj">22.</td>
						    <td class="tg-5ua9">Kehadiran selalu tepat waktu dan sesuai target tatap muka</td>
						    <?php if ($row['quiz_3_22'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_22'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_22'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_22'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_22'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr style="background-color: darkgray;">
						    <td class="tg-hgcj">C.</td>
						    <td class="tg-hgcj" colspan="6">RESPONSIVENES ( DAYA TANGGAP )</td>
						  </tr>
						  <tr>
						    <td class="tg-hgcj">23.</td>
						    <td class="tg-5ua9">Kesigapan dalam menjawab pertanyaan mahasiswa sesuai harapan</td>
						    <?php if ($row['quiz_3_23'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_23'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_23'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_23'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_23'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-hgcj">24.</td>
						    <td class="tg-5ua9">Kemampuan menumbuhkan munat dan semangat mahasiswa dalam perkuliahan sesuai harapan</td>
						    <?php if ($row['quiz_3_24'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_24'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_24'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_24'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_24'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-hgcj">25.</td>
						    <td class="tg-5ua9">Kemampuan menumbuhkan suasana belajar nyang menyenangkan</td>
						    <?php if ($row['quiz_3_25'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_25'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_25'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_25'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_25'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr style="background-color: darkgray;">
						    <td class="tg-hgcj">D.</td>
						    <td class="tg-hgcj" colspan="6">ASSURANCE ( JAMINAN )</td>
						  </tr>
						  <tr>
						    <td class="tg-hgcj">26.</td>
						    <td class="tg-5ua9">Ketepatan materi dengan SAP dan modul</td>
						    <?php if ($row['quiz_3_26'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_26'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_26'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_26'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_26'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-hgcj">27.</td>
						    <td class="tg-5ua9">Ketepatan waktu dalam memberikan nilai ujian</td>
						    <?php if ($row['quiz_3_27'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_27'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_27'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_27'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_27'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr style="background-color: darkgray;">
						    <td class="tg-hgcj">E.</td>
						    <td class="tg-hgcj" colspan="6">EMPATHY ( EMPATI )</td>
						  </tr>
						  <tr>
						    <td class="tg-amwm">28.</td>
						    <td class="tg-1wig">Perhatian Dosen terhadap kemajuan belajar mahasiswa</td>
						    <?php if ($row['quiz_3_28'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_28'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_28'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_28'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_28'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						  <tr>
						    <td class="tg-amwm">29.</td>
						    <td class="tg-1wig">Masukan dan pujian Dosen terhadap kemampuan mahasiswa menjawab pertanyaan</td>
						    <?php if ($row['quiz_3_29'] == "1") { ?>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_29'] == "2"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_29'] == "3"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_29'] == "4"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
							    <td class="tg-xldj"><center></center></td>
						    <?php }elseif($row['quiz_3_29'] == "5"){ ?>
						    	<td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center></center></td>
							    <td class="tg-xldj"><center><img src="images/check.png"></center></td>
						    <?php } ?>
						  </tr>
						</table>
						<br>&nbsp;
						<style type="text/css">
						.tg  {border-collapse:collapse;border-spacing:0;}
						.tg td{font-family:Arial, sans-serif;font-size:14px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
						.tg th{font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
						.tg .tg-5ua9{font-weight:bold;text-align:left}
						</style>

						<table class="tg" width="100%">
						  <tr style="background-color: darkgray;">
						    <th class="tg-5ua9" colspan="7"><center><span style="font-weight:bold">BAGIAN IV</span></center></th>
						  </tr>
						  <tr>
						    <th class="tg-5ua9">Berikan Saran/Komentar Anda dalam upaya peningkatan kinerja Dosen ini:<br><br>
						    	<?= $row['saran']?></th>
						  </tr>
						</table>
						<input type="hidden" name="act" id="act">
						<input type="hidden" name="npm" id="npm" value="<?= $r_npm ?>">
						<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
						<input type="hidden" name="periode" id="periode" value="<?= $r_periode ?>">
						<? if(Akademik::isDosen()) { ?>
						<input type="hidden" name="nip" id="nip" value="<?= Modul::getUserName() ?>">
						<? } ?>
						</div>
					<?php } ?>
				</center>
			</form>
		</div>
	</div>
</div>
<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
$(document).ready(function() {
	<? if(Akademik::isDosen()) { ?>
	$("#mahasiswa").xautox({strpost: "f=acmhswali", targetid: "npmtemp", postid: "nip"});
	<? } else { ?>
	$("#mahasiswa").xautox({strpost: "f=acmahasiswa", targetid: "npmtemp"});
	<? } ?>
});
	
function goPrint() {
	showPage('null','<?= Route::navAddress('rep_khs') ?>');
}
function goSave(elem) {
	document.getElementById("act").value = "save";
	goSubmit();
}
</script>
</body>
</html>
