<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=false;
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('krs'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('laporanmhs'));
	
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
	
	// combo
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Kartu Hasil Studi Mahasiswa';
	$p_tbwidth = "100%";
	$p_aktivitas = 'NILAI';
	
	// mendapatkan data
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	$a_data = mKRS::getDataPeriode($conn,$r_key,$r_periode);
	$hehe = mKRS::getNilaiMasuk($conn,$r_key,$r_periode);
	$a_nilaiunsur=mKRS::getNilaiUnsur($conn,$r_key,$r_periode);
	$periodeaktif = mAkademik::getPeriodeSekarang($conn);
	
	$a_nilaiunsur=mKRS::getNilaiUnsur($conn,$r_key,$r_periode);
	if(Akademik::isMhs()){
		if(!empty($_POST['tahun']) or !empty($_POST['semester']) ){
			$r_periode = $_POST['tahun'].$_POST['semester'];
			$cekal = mMahasiswa::cekCekalMhs($conn,$r_key,$r_periode);
			if ($r_periode>20172) {

				if(mKrs::cekQuisioner($conn,$r_key,$r_periode )){
					$p_posterr = true;
					$p_postmsg = "Quisioner ".Akademik::getNamaPeriode($r_periode)." belum diisi lengkap , untuk mengisi klik <u class='ULink' onclick=submitPage('npm','".Route::navAddress('list_mkquiz')."')>di sini</u>";
				}else{
					if($periodeaktif == $r_periode and $cekal['isuts']=='0' and $cekal['isuas']=='0'){
						$p_posterr = true;
						$p_postmsg = "Tidak Dapat Melihat Nilai, Anda Memiliki Tunggakan ".Akademik::getNamaPeriode($r_periode)."";
					}
				}
			}
		}else{
			$cekal = mMahasiswa::cekCekalMhs($conn,$r_key,$r_periode);
			if(mKrs::cekQuisioner($conn,$r_key,$r_periode)){
				$p_posterr = true;
				$p_postmsg = "Quisioner ".Akademik::getNamaPeriode($r_periode)." belum diisi lengkap, untuk mengisi klik <u class='ULink' onclick=submitPage('npm','".Route::navAddress('list_mkquiz')."')>di sini</u>";
			}else{
				if($periodeaktif == $r_periode and $cekal['isuts']=='0' and $cekal['isuas']=='0'){
					$p_posterr = true;
					$p_postmsg = "Tidak Dapat Melihat Nilai, Anda Memiliki Tunggakan ".Akademik::getNamaPeriode($r_periode)."";
				}
			}
		}
	}
	$cekdata = mLaporanMhs::getValidateInputNilai($conn,$r_key,$r_periode);
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
			<div style="float:left; width:100%; ">
			<?php require_once('inc_headermahasiswa.php'); ?>
			</div>
			<form name="pageform" id="pageform" method="post">
				<?php require_once('inc_headermhs_krs.php') ?>
				<center>
				 <br>
				<div style="width:100%;">
				<?php require_once('inc_listfilter.php'); ?>
				<center>
					<div class="<?= $p_posterr ? 'DivError' : '' ?>" style="width:<?= $p_lwidth ?>px">
						<?= $p_postmsg ?>
					</div>
				</center>
				<?php if(!$p_posterr) { ?>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							
							<div class="right">
								<!--<img title="Cetak KHS" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">-->
								<!--<img title="Lihat Detail Nilai" width="24px" src="images/magnify.png" style="cursor:pointer" onclick="toggleDetail()"-->
							</div>
							
							<div class="right">
								<?php //if($cekdata['dipakai']==$cekdata['jmlmk'] and $cekdata['nmasuk']==$cekdata['jmlmk']){ ?>
								<img title="Cetak KHS" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrintText()">
								<?php //} ?>
							</div-->
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
						<th>Nilai</th>
						<th>Bobot</th>
						<th>SKS x N</th>
						<th></th>
						<!--<th>Lulus</th>-->
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						$t_tsks = 0;
						$t_sksn = 0;
						$t_tbobot = 0;
						foreach($a_data as $row) {
							
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_sks = (int)$row['sks'];
							$t_tsks += $t_sks;
							
							//$t_sksn += $t_sksn;
							/*if(empty($row['nhuruf']) or empty($row['dipakai']))
								continue;
							
							$t_sks = (int)$row['sks'];
							$t_tsks += $t_sks;*/
							
							if(!empty($row['nilaimasuk'])) {
								$t_nhuruf = $row['nhuruf'];
								
								$t_bobot = $t_sks*$row['nangka'];
								$t_tbobot += $t_bobot;
							}
							else{
								$t_nhuruf = '&nbsp;';
							}
						$t_sksn += $row['sks']*$row['nangka'];
					?>
					<tr valign="top" class="<?= $rowstyle ?><?= empty($row['prasyaratspp']) ? '' : ' GreenBG' ?>">
						<td align="center"><?= $i ?>.</td>
						<td><?= $row['kodemk'] ?></td>
						<td><?= $row['namamk'] ?></td>
						<td align="center"><?= $row['kelasmk'] ?></td>
						<td align="center"><?= $row['sks'] ?></td>
						<td align="center"><?php
					
						$cek = mLaporanMhs::cekInputanNilai($conn,$r_periode,$row['kodemk'],$r_key);
						$cek2 =  mLaporanMhs::cekInputanNilaiLengkap($conn,$r_periode,$row['kodemk'],$r_key);
						if($r_periode>=20181){	
							if($cek==0 and $cek2==3)
								echo $row['nhuruf'];	
							else
								if($row['kodemk']=='LU25' or $row['kodemk']=='INA028' or $row['kodemk']=='INA029')
									echo $row['nhuruf'];
								else
									echo "T";
						}else{
							echo $row['nhuruf'];
						}
							
							
							?>
							
							
							</td>
						<!--<td align="center"><?php
						/*if(!empty($row['nhuruf'])){
								echo $row['nhuruf']=='E' ? 'Tidak Lulus' : 'Lulus';
						}else{
								echo "Tidak Lulus";
						}*/
						
						
						?>
						
						
						
						
						
					</td>-->
					<td align="center"><?= $row['nangka'] ?></td>
					<td align="center"><?= $row['sks']*$row['nangka'] ?></td>
					<td align="center"><?php if($row['dipakai']==-1){ ?>
						<img title="Selesai Quisioner" src="images/check.png">
					<?php } ?>
					</td>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="7" align="center">Data kosong</td>
					</tr>
					<?	} ?>
					<tr>
						<th align="right" class="HeaderBG" colspan="5">Total</th>
						<th><?= $t_tsks ?></th>
						<th></th>
						<th><?= $t_tbobot ?></th>
						<th><?= $t_sksn ?></th>
						<!--<th class="HeaderBG">&nbsp;</th>-->
					</tr>
					<tr>
						<th colspan="9">Indeks Prestasi Semester = <?= empty($t_tsks) ? 0 : round($t_sksn/$t_tsks,2) ?></th>
					</tr>
				</table>
				
				
				<br><br>
				<div class="detail_nilai" >

				<table width="<?= $p_tbwidth ?>">
					
				<?php
					$i=0;
					
					foreach($a_nilaiunsur as $matakuliah=>$rowunsur){
						$i++;
				?>
					<tr>
						<td align="center"><?= $i ?></td>
						<td colspan="4"><?= $matakuliah ?></td>
					</tr>
					<?php foreach($rowunsur as $idunsur=>$rowu){ ?>
					<tr>
						<td width="30" align="center">&nbsp;</td>
						<td width="80"><?= $rowu['namaunsurnilai'] ?></td>
						<td width="30"><?= $rowu['prosentasenilai'] ?>%</td>
						<td width="30"><?= $rowu['nilaiunsur'] ?></td>
						<td>&nbsp;</td>
					</tr>
				<?php } ?>
					<tr>
						<td width="30" align="center">&nbsp;</td>
						<td colspan="2">Nilai Akhir</td>
						<td width="30"><?=$rowunsur[$idunsur]['nnumerik'] ?></td>
						<td>&nbsp;</td>
					</tr>
				<?php }?> 
				<?php } ?>
				</table>
				</div>
				<input type="hidden" name="act" id="act">
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
function goPrintDm() {
	showPage('null','<?= Route::navAddress('rep_khstext') ?>');
}
function goPrintText() {
	showPage('null','<?= Route::navAddress('rep_khs') ?>');
}
function toggleDetail() {
	$(".detail_nilai").toggle();
}
</script>
</body>
</html>
