<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('mengajar'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	if(Akademik::isDosen()){
	
		$r_key =  Modul::getUserIDPegawai();
		$rq_key = CStr::removeSpecial($_REQUEST['key']);
		//$r_dosen = Modul::getUserName() ? Modul::getUserName().' - '.$_SESSION['SIAKAD']['MODUL']['USERDESC'] : $_SESSION['SIAKAD']['MODUL']['USERDESC'];
		
		}
	else
		$r_key = CStr::removeSpecial($_REQUEST['idpegawai']);
	
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	
	
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	$r_nama = Akademik::getNamaPegawai($conn,$r_key);
	$r_dosen=$r_nama;
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Jadwal Mengajar';
	$p_tbwidth = 800;
	$p_aktivitas = 'MENGAJAR';
	
	$p_model = mMengajar;
	
	$r_sort='k.nohari, k.jammulai';
	$a_filter = Page::setFilter($_POST['filter']);
	
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_key)) $a_filter[] = $p_model::getListFilter('nipdosen',$r_key);
	
	// mendapatkan data
	$a_dataminggu = $p_model::getDataAjarMingguan($conn,$a_kolom,$r_sort,$a_filter);
	$a_datamingguprak = $p_model::getDataAjarMingguanPrak($conn,$a_kolom,$r_sort,$a_filter);
	$a_datahari = $p_model::getDataAjarHarian($conn,$r_key);
	
	// array terjemah
	$a_jeniskuliah = mKuliah::jenisKuliah($conn);
	$a_statuskuliah = mKuliah::statusKuliah();
	
	$a_combodosen=array();
	$a_combodosen[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
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
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
		<div style="float:left; width:15%">
				<? //require_once('inc_sidemenudosen.php');?>
			</div>
			<div >
			<center>
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				
			<?php require_once('inc_headerdosen.php') ?>
			<br>
			 
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1>Jadwal Mengajar Perkuliahan</h1>
						</div>
					</div>
				</header>
			 
			<?	/*************/
				/* LIST DATA */
				/*************/
			?>
			<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
				<?	/**********/
					/* HEADER */
					/**********/
				?>
				<tr>
					<th>No.</th>
					<th>Kode</th>
					<th>Mata Kuliah</th>
					<th>Kelas</th>
					<th>Jadwal</th>
					<th>Status</th>
					<th>Detail</th>
					
				</tr>
				<?	/********/
					/* ITEM */
					/********/
					
					$i = 0;
					foreach($a_dataminggu as $row) {
						if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
						$jadwalkey=$row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['periode'].'|'.$row['kelasmk'].'|K|1';
						$a_jadwal = array();
						if(!empty($row['nohari']))
							$a_jadwal[] = $row['namahari'].', '.CStr::formatJam($row['jammulai']).' - '.CStr::formatJam($row['jamselesai']).' <b>Ruang '.$row['koderuang'].'</b>';
						if(!empty($row['nohari2']))
							$a_jadwal[] = $row['namahari2'].', '.CStr::formatJam($row['jammulai2']).' - '.CStr::formatJam($row['jamselesai2']).' <b>Ruang '.$row['koderuang2'].'</b>';
						if(!empty($row['nohari3']))
							$a_jadwal[] = $row['namahari3'].', '.CStr::formatJam($row['jammulai3']).' - '.CStr::formatJam($row['jamselesai3']).' <b>Ruang '.$row['koderuang3'].'</b>';
						if(!empty($row['nohari4']))
							$a_jadwal[] = $row['namahari4'].', '.CStr::formatJam($row['jammulai4']).' - '.CStr::formatJam($row['jamselesai4']).' <b>Ruang '.$row['koderuang4'].'</b>';
				?>
				<tr valign="top" class="<?= $rowstyle ?>">
					<td align="right"><?= $i ?>.</td>
					<td align="center"><?= $row['kodemk'] ?></td>
					<td><?= $row['namamk'] ?></td>
					<td align="center"><?= $row['kelasmk'] ?></td>
					<td><?= implode('<br>',$a_jadwal) ?></td>
					<td align="center">
						<?= $row['isonline']; ?>
					</td>
					<td align="center">
						<img src="images/link.png" id="<?= $jadwalkey ?>" onClick="goOpenpage(this)" style="cursor:pointer" title="Detail Jadwal">
					</td>
					
				</tr>
				<?	}
					if($i == 0) {
				?>
				<tr>
					<td colspan="6" align="center">Data kosong</td>
				</tr>
				<?	} ?>
			</table>
			<br><br>
			<!-- praktikum-->
			<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1>Jadwal Mengajar Praktikum</h1>
						</div>
					</div>
				</header>
			 
			<?	/*************/
				/* LIST DATA */
				/*************/
			?>
			<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
				<?	/**********/
					/* HEADER */
					/**********/
				?>
				<tr>
					<th>No.</th>
					<th>Kode</th>
					<th>Mata Kuliah</th>
					<th>Kelas</th>
					<th>Kelompok</th>
					<th>Jadwal</th>
					
				</tr>
				<?	/********/
					/* ITEM */
					/********/
					
					$i = 0;
					foreach($a_datamingguprak as $row) {
						if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
						
						$a_jadwal = array();
						if(!empty($row['nohari']))
							$a_jadwal = $row['namahari'].', '.CStr::formatJam($row['jammulai']).' - '.CStr::formatJam($row['jamselesai']).' <b>Ruang '.$row['koderuang'].'</b>';
						
				?>
				<tr valign="top" class="<?= $rowstyle ?>">
					<td align="right"><?= $i ?>.</td>
					<td align="center"><?= $row['kodemk'] ?></td>
					<td><?= $row['namamk'] ?></td>
					<td align="center"><?= $row['kelasmk'] ?></td>
					<td align="center"><?= $row['kelompok'] ?></td>
					<td><?= $a_jadwal ?></td>
					
				</tr>
				<?	}
					if($i == 0) {
				?>
				<tr>
					<td colspan="6" align="center">Data kosong</td>
				</tr>
				<?	} ?>
			</table>
			
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $rq_key ?>">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="format" id="format">
			</form>
			</center>
			</div>
		</div>
	</div>
</div>
</body>
</html>
<script>
	function goOpenpage(elem){
		document.getElementById("pageform").action = "<?= Route::navAddress('detail_jadwalblock')?>";
		document.getElementById("key").value = elem.id;
		document.getElementById("pageform").target="_blank";
		goSubmit();
	
		}
</script>
