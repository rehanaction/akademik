<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('transkrip'));
	
	// variabel request
	if(Akademik::isMhs())
	{
		$r_key = Modul::getUserName();
		$display="none";
	}	
	else if(Akademik::isDosen()){
	$display="none";
	$r_key = CStr::removeSpecial($_REQUEST['npm']);
	}	
	else
	{
		$r_key = CStr::removeSpecial($_REQUEST['npm']);
		$display="block";
	}
		
	
	// properti halaman
	$p_title = 'Daftar Nilai Mahasiswa';
	$p_tbwidth = "100%";
	$p_aktivitas = 'NILAI';
	
	// mendapatkan data
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	$a_data = mTranskrip::getDataResume($conn,$r_key);
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
		<div style="float:left; width:100%;  ">
			<?php require_once('inc_headermahasiswa.php'); ?>
		</div>
		<div style="float:left; width:100%;">
		 
		
			
			<center>
				<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
					<span>
						<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
						&nbsp;<?= $p_title ?>
						<div class="right"><img title="Cetak Transkrip" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()"></div>
					</span>
				</div>
			 
			<br>
			<?php require_once('inc_headermhs.php') ?>
			<br>
			 
<?php
	$t = 0;
	$n_data = count($a_data);
	
	$t_ttsks = 0;
	$t_ttbobot = 0;
	foreach($a_data as $t_kompetensi => $t_data) {
		$t++;
?>
<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
	<tr class="DataBG">
		<td colspan="10" align="center"><?= $t_data[0]['namajenis'] ?></td>
	</tr>
	<tr>
		<th>No.</th>
		<th>Kurikulum</th>
		<th>Kode</th>
		<th>Nama MataKuliah</th>
		<th>Nilai</th>
		<th>SKS</th>
		<th>NK</th>  
		<th>SMT</th>
	</tr>
<?php
    $kdkmk = $row['kodemk'];
		$i = 0;
		$t_tsks = 0;
		$t_tbobot = 0;
		foreach($t_data as $row) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
			
			$t_sks = (int)$row['sks'];
			$t_bobot = $t_sks * (float)$row['nangka'];
			
			$t_nh = $row['nhuruf'];
			$a_nhuruf[$t_nh]++;
			
			if(empty($t_bobot))
				$t_nh = '<span style="color:red">'.$t_nh.'</span>';
			
			$t_tsks += $t_sks;
			$t_tbobot += $t_bobot;
?>
	<tr valign="top" class="<?= $rowstyle ?>">
		<td align="center"><?= $i ?>.</td>  
		<td align="center"><?= $row['thnkurikulum'] ?></td>
		<td align="center"><?= $row['kodemk'] ?></td>  
		<!--<td align="center"><?= $kdkmk ?></td>-->
		<td><?= $row['namamk'] ?></td>
		<td align="center"><?= $t_nh ?></td>
		<td align="center"><?= $t_sks ?></td>
		<td align="center"><?= $t_bobot ?></td>   
		<td align="center"><?= $row['periode'] ?></td>
	</tr>
<?php
		}
		
		$t_ttsks += $t_tsks;
		$t_ttbobot += $t_tbobot;
?>
  <tr>
		<th align="right" colspan="4">J U M L A H</th>
    <th><?= $huruf   ?></th>
		<th><?= $t_tsks ?></th>
		<th><?= $t_tbobot ?></th> 
    <th><?= $huruf   ?></th>
	</tr> 
  <?php $t_ipk=round($t_tbobot/$t_tsks,2); ?>  
  <tr>
		<th colspan="4">IPK</th>
    <th><?= $huruf   ?></th>
		<th><?= $huruf ?></th>
		<th><?= $huruf ?></th> 
    <th><?= $t_ipk   ?></th>
	</tr>
<?php
		if($t == $n_data) {
?>
	<tr>
		<!--<th colspan="4">T O T A L</th>-->
		<!--<th><?= $t_ttsks ?></th>-->
		<!--<th><?= $t_ttbobot ?></th>-->
	</tr>
<?php
		}
?>
</table>
<?php
		if($t < $n_data) {
?>
<br>
<?php
		}
	}
?>
			</center>
		</div>
			<form name="pageform" id="pageform" method="post">
				<input type="hidden" name="npm" id="npm" value="<?= $r_key ?>">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

function goPrint() {
<?	if($a_infomhs['statusmhs'] == 'A') { ?>
	showPage('null','<?= Route::navAddress('rep_resumesmt') ?>');
<?	} else { ?>
	showPage('null','<?= Route::navAddress('rep_resumesmt') ?>');
<?	} ?>
}

</script>
</body>
</html>