<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// /$conn->debug=true;
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('detailkelas'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_tgl = Modul::setRequest($_POST['tglpertemuan'],'TGLPERTEMUAN');
	if(!empty($r_tgl))
		$r_tgl=date('Y-m-d',strtotime($r_tgl));
	else
		$r_tgl=date('Y-m-d');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	$l_hari = uCombo::hari($r_hari,true,'nohari','onchange="goSubmit()"',false);
	$a_input= array('kolom' => 'tglpertemuan', 'label' => 'Tanggal Tes', 'type' => 'D','add' => 'onchange="goSubmit()"');
	$i_tanggal=uForm::getInput($a_input,$r_tgl);
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	$arr_hari=Date::arrayDay();
	
	// properti halaman
	$p_title = 'Monitoring Ruang '.Akademik::getNamaPeriode($r_periode).' Tanggal '.Date::indoDate($r_tgl);
	$p_tbwidth = 950;
	$p_aktivitas = 'UNIT';
	$p_waktumin = 700;
	$p_waktumax = 2400;
	$p_colnum = 2; 
	// mendapatkan data
 
	$a_data = mKuliah::getMonitoringRuang($conn,$r_unit, $r_periode,$r_tgl,$p_waktumin,$p_waktumax);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Jurusan', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
	$a_filtercombo[] = array('label' => 'Tanggal Perkuliahan', 'combo' => $i_tanggal);
	
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/hint.min.css" rel="stylesheet" type="text/css">
	<link href="style/tooltipster.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	 <script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<style>
		.GridItem {
			font-size: 9px;
		}
		.GridDiv {
			overflow: hidden;
			white-space: nowrap;
			border: none;
		}
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?php require_once('inc_listfilter.php'); ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
				</center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<tr>
						<th width="120">Ruang</th>
						<?php
							for($i=$p_waktumin;$i<$p_waktumax;$i+=100) {
								$p_colnum++;
						?>
						<th width="59" style="text-align:left"><?= CStr::formatJam($i) ?></th>
						<?php
							}
							$p_time = ($p_colnum-2)*60;
						?>
						<!--th width="11">&nbsp;</th-->
					</tr>
					<tr class="NoHover">
						<td colspan="<?= $p_colnum ?>" style="padding:0">
			<div style="height:450px;overflow-x:hidden">
				<table width="<?= $p_tbwidth ?>" cellpadding="0" class="GridStyle">
					<?php
						foreach($a_data as $t_ruang => $t_data) {
					?>
					<tr height="30" class="NoHover">
						<td style="padding:4px" width="51"><?= $t_ruang ?></td>
						<?php
							$t_mod = 0;
							$t_block = 0;
							
							foreach($t_data as $t_timeline) {
								// mengatur atribut
								$t_lebar = $t_timeline['lebar'];
								if(empty($t_lebar))
									continue;
								
								if(!empty($t_mod)) {
									if($t_mod >= 5)
										$t_lebar -= (10-$t_mod);
									else
										$t_lebar += $t_mod;
								}
								
								$t_mod = $t_lebar%10;
								if($t_mod >= 5)
									$t_lebar += (10-$t_mod);
								else
									$t_lebar -= $t_mod;
								
								$t_colspan = floor($t_lebar/10);
								$bg_td='';
								if($t_timeline['status']) {
									// $t_attrib = ' colspan="'.$t_colspan.'" bgcolor="#D8FEEC" title="'.$t_timeline['keterangan'].'"';
									$t_attrib = ' colspan="'.$t_colspan.'" bgcolor="#D8FEEC"';
									$t_label = $t_timeline['kodemk'].'<br>'.CStr::formatJam($t_timeline['mulai']).' - '.CStr::formatJam($t_timeline['selesai']);
								if ($t_timeline['isonline']==-1) 
									$bg_td='RedBG';
								else if($t_timeline['isonline']==-2) 
									$bg_td='GreenBG';
						?>
						<!--<td class="GridItem" align="center" <?= $t_attrib ?>>
							<span class="hint--top" data-hint="<?= $t_timeline['keterangan'] ?>">
								<div class="GridDiv" align="center" style="width:<?= (7*$t_colspan) ?>px"><?= $t_label ?></div>
							</span>
						</td>-->
						<td class="GridItem tooltip <?=$bg_td?>" align="center" <?= $t_attrib ?> title="<?= $t_timeline['keterangan'] ?>" >
							
							<span>
								<div class="GridDiv" align="center" style="width:<?= (7*$t_colspan) ?>px;cursor:pointer"><?= $t_label ?></div>
							</span>
						</td>
						<?php
								}
								else {
									$t_span = 0;
									$t_modb = 6-$t_block%6;
									$t_cekspan = $t_colspan;
									do {
										if($t_cekspan < $t_modb)
											$t_span = $t_cekspan;
										else
											$t_span = $t_modb;
						?>
						<td colspan="<?= $t_span ?>"></td>
						<?php
										$t_cekspan -= $t_span;
										$t_modb = 6;
									}
									while($t_cekspan > 0);
								}
								
								$t_block += $t_colspan;
							}
						?>
					</tr>
					<?php
						}
					?>
					<tr class="NoHover" bgcolor="#CCC">
						<td style="padding:4px"></td>
						<?php
							for($i=$p_waktumin;$i<$p_waktumax;$i+=100) {
								for($j=0;$j<6;$j++) {
						?>
						<td width="10"></td>
						<?php
								}
							}
						?>
					</tr>
				</table>
			</div>
						</td>
					</tr>
				</table>
				
			</form>
			<br>
			<center>
				<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
					<table width="<?= $p_tbwidth-400 ?>" cellpadding="0" cellspacing="0" align="center">
						<tr>
							<td width="30"><strong>Box</strong></td>
							<td width="40" ><div style="background:#BEFADD;border:1px solid #ccc">&nbsp;</div></td>
							<td width="150">&nbsp; : tatap Muka</td>
							
							<td width="30"><strong>Box</strong></td>
							<td width="40"><div class="RedBG" style="border:1px solid #ccc">&nbsp;</div></td>
							<td width="150">&nbsp; : Online</td>
						</tr>
					</table>
				</div>
			</center>
		</div>
		
	</div>
</div>

<script type="text/javascript" src="scripts/jquery.tooltipster.js"></script>
<script type="text/javascript" src="scripts/jquery.balloon.min.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	$(".GridItem")
		// .balloon({minLifetime:0,showDuration:0,hideDuration:0})
		.hover(function() { $(this).addClass("YellowBG"); },function() { $(this).removeClass("YellowBG"); });
	$('.tooltip').tooltipster();
});

function goPrint() {
	goSubmitBlank('<?= Route::navAddress('rep_rekapruang') ?>');
}

function countWidth(elem) {
	alert($(elem).width());
}

</script>
</body>
</html>
