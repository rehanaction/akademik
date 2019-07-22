<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=false;
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('soalquiz'));
	require_once(Route::getModelPath('mengajar'));
	require_once(Route::getModelPath('quizadji'));
	require_once(Route::getModelPath('setting'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	if(Akademik::isMhs())
	{
	$r_key = Modul::getUserName();
	$display="none";
	}
	else if(Akademik::isDosen()){
	$r_nip = Modul::getUserName();
	$display="none";
	$r_act = $_POST['act'];
	if($r_act == 'first')
			$r_key = mMahasiswa::getFirstNIM($conn,$r_nim,$r_nip);
		else if($r_act == 'prev')
			$r_key = mMahasiswa::getPrevNIM($conn,$r_nim,$r_nip);
		else if($r_act == 'next')
			$r_key = mMahasiswa::getNextNIM($conn,$r_nim,$r_nip);
		else if($r_act == 'last')
			$r_key = mMahasiswa::getLastNIM($conn,$r_nim,$r_nip);
		else
			$r_key=CStr::removeSpecial($_REQUEST['npm']);
	}
	else
	{
	$r_key = CStr::removeSpecial($_REQUEST['npm']);
	$display="block";
	}
		

	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	//echo $r_semester;
	/*if(!isset($_POST['semester']) or !isset($_POST['tahun'])){
		$prevPeriode=mSetting::getPrevPeriodeaktif($conn);
		$r_tahun=substr($prevPeriode,0,4);
		$r_semester=substr($prevPeriode,4,1);
	}*/
	// combo
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Evaluasi Pelaksanaan Kuliah';
	$p_tbwidth = "100%";
	$p_aktivitas = 'NILAI';
	
	// mendapatkan data
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	
	$a_data = mSoalQuiz::getMkQuiz($conn,$r_key,$r_periode);
	
	$a_hasilquiz=mQuizadji::getDataCek($conn,$r_key,$r_periode);
	//$a_hasilquiz=mHasilQuiz::getArray($conn);
	
	
	//membuat array hasil quiz untuk dicek
	$arr_hasil=array();
	foreach($a_hasilquiz as $rowquiz)
		$arr_hasil[]=$rowquiz['periode']."|".$rowquiz['thnkurikulum']."|". $rowquiz['kodeunit']."|".$rowquiz['kodemk']."|".$rowquiz['kelasmk']."|".$rowquiz['nim']."|".$rowquiz['nipdosen'];
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
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
			<div>
			<?php require_once('inc_headermahasiswa.php'); ?>
			</div>
			<form name="pageform" id="pageform" method="post">
				<?php require_once('inc_headermhs_krs.php') ?>
				<center>
				 <br>
				<div style="float:left; width:100%;">
				<?php require_once('inc_listfilter.php'); ?>
			 
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
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
						<th>No.</th>
						<th>Kode</th>
						<th>Nama Matakuliah</th>
						<th>Kelas</th>
						<th>SKS</th>
						<th>Dosen</th>
						<th>Link</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  
							else $rowstyle = 'AlternateBG'; $i++;
							$t_key = mKelas::getKeyRow($row)."|".$r_key."|".$row['nipdosen'];
							$key_hasil = $row['periode']."|".$row['thnkurikulum']."|". $row['kodeunit']."|".$row['kodemk']."|".$row['kelasmk']."|".$r_key."|".$row['nipdosen'];
							
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="right"><?= $i ?>.</td>
						<td><?= $row['kodemk'] ?></td>
						<td><?= $row['namamk'] ?></td>
						<td align="center"><?= $row['kelasmk'] ?></td>
						<td align="center"><?= $row['sks'] ?></td>
						<td align="center"><?= $row['namadosen']." (".$row['nipdosen'].")" ?></td>
						<?php if(!in_array($key_hasil,$arr_hasil)){?>
						<td><img id="<?= $t_key ?>" title="Isi Quisioner" src="images/link.png" onclick="goPop('popMenu',this,event)" style="cursor:pointer"></td>
						<?php } else {?>
						<td><img title="Selesai Quisioner" src="images/check.png"></td>
						<?php } ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="7" align="center">Data kosong</td>
					</tr>
					<?	} ?>
					
				</table>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="npm" id="npm" value="<?= $r_key ?>">
				<input type="hidden" name="periode" id="periode" value="<?= $r_periode ?>">
				<? if(Akademik::isDosen()) { ?>
				<input type="hidden" name="nip" id="nip" value="<?= Modul::getUserName() ?>">
				<? } ?>
				</div>
				</center>
			</form>
		</div>
	</div>
</div>
<div id="popMenu" class="menubar" style="position:absolute; display:none; top:0px; left:0px;z-index:10000;" onMouseOver="javascript:overpopupmenu=true" onMouseOut="javascript:overpopupmenu=false">
<table width="130" class="menu-body">
    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('key','<?= Route::navAddress('view_soalquiz') ?>')">Isi Quisioner</td>
    </tr>
</table>
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

</script>
</body>
</html>
