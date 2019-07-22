<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	// $r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_tahunpelaporan = Modul::setRequest($_POST['tahunpelaporan'],'TAHUN');
	
	// combo
	// $l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	$l_tahunpelaporan = uCombo::tahun($r_tahunpelaporan,true,'tahunpelaporan','onchange="goSubmit()"',false);
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Transfer Data EPSBED';
	$p_tbwidth = 950;
	$p_aktivitas = 'ABSENSI';
	// $conn->debug = true;
	$p_model = mKelas;
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	
	// $a_data = $p_model::getListDataAbsensi($conn,$a_kolom,$r_sort,$a_filter);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Tahun Pelaporan', 'combo' => $l_tahunpelaporan);
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
	
	if (isset($_REQUEST["act"]))
  	{
		$r_act = $_REQUEST["act"];
		$r_rule = explode(':',$_REQUEST["rule"]);
		$r_unit = $r_rule[1];
		$pathaddress = 'epsbed_file/';

		if ($r_act=="export") 
		{
			if ($r_rule[0] == 'e_msmhs')			
				include("eps_msmhs.php");
			else if ($r_rule[0] == 'e_trakm')
				include("eps_trakm.php");
			else if ($r_rule[0] == 'e_trnlm')
				include("eps_trnlm.php");
			else if ($r_rule[0] == 'e_trnlp')
				include("eps_trnlp.php");
			else if ($r_rule[0] == 'e_trakd')
				include("eps_trakd.php");
			else if ($r_rule[0] == 'e_trlsm')
				include("eps_trlsm.php");
			else if ($r_rule[0] == 'e_tbkmk')
				include("eps_tbkmk.php");
			else if ($r_rule[0] == 'e_trskr')
				include("eps_trskr.php");
			
			//ari
			// else if ($r_rule[0] == 'e_trkap')
				// include("eps_trkap.php");
			// else if ($r_rule[0] == 'e_msdos')
				// include("eps_msdos.php");
			
			// else if ($r_rule[0] == 'e_mspst')
				// include("eps_mspst.php");
			// else if ($r_rule[0] == 'e_trlsd')
				// include("eps_trlsd.php");
			// else if ($r_rule[0] == 'e_trpud')
				// include("eps_trpud.php");
			// else if ($r_rule[0] == 'e_trfas')
				// include("eps_trfas.php");
		}
		else if ($r_act == 'clear' and $c_delete){
			include("eps_cleardata.php");
		}
	}
	
	// $a_all = array('e_msmhs' => 'MSMHS', 'e_tbkmk' => 'TBKMK', 'e_trakd' => 'TRAKD', 'e_trakm' => 'TRAKM', 'e_trlsm' => 'TRLSM', 'e_trnlm' => 'TRNLM', 'e_mspst' => 'MSPST',
					// 'e_msdos' => 'MSDOS', 'e_trkap' => 'TRKAP', 'e_mspst' => 'MSPST', 'e_trlsd' => 'TRLSD', 'e_trpud' => 'TRPUD', 'e_trfas' => 'TRFAS');
	$a_all = array('e_msmhs' => 'MSMHS','e_trakm' => 'TRAKM','e_trnlm' => 'TRNLM','e_trlsm' => 'TRLSM','e_trakd' => 'TRAKD', 'e_trnlp' => 'TRNLP',
					'e_tbkmk' => 'TBKMK','e_trskr'=>'TRSKR');

	$cek = Modul::getLeftRight();
	$sql = "select u.kodeunit, u.namaunit, u.level from gate.ms_unit u join akademik.ak_prodi p on u.kodeunit=p.kodeunit
			where u.infoleft >= '".$cek['LEFT']."' and u.inforight <= '".$cek['RIGHT']."' and u.isakad=-1 and u.level='2'
			order by infoleft";
	$a_data = $conn->Execute($sql);
	
	#query untuk dapatkan log
	$sql_log = "select * from epsbed.ms_transferpdpt where periode='".$r_tahun.$r_semester."' and thnpelaporan='$r_tahunpelaporan' order by idtransferpdpt asc ";
	$rs_log = $conn->Execute($sql_log);
	$arr_log = array();
	while($row = $rs_log->FetchRow()){
		$waktu_exp = explode(' ',$row['t_updatetime']);
		$arr_log[$row['kode_program_studi']][$row['tabelpdpt']] = $row['namapetugas'].'<br>'.Date::indoDate($waktu_exp[0]).'<br>'.$waktu_exp[1];
	}
	
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
	<style>
		.darken {
			background-color: rgb(0, 0, 0);
			opacity: 0.4;
			-moz-opacity: 0.40;
			filter: alpha(opacity=40);
			z-index: 20;
			height: 100%;
			width: 100%;
			background-repeat: repeat;
			position: fixed;
			top: 0px;
			left: 0px;
		}

		.lighten {
			z-index: 50;
			height: 100%;
			width: 100%;
			background-repeat: repeat;
			position: fixed;
			top: 0px;
			left: 0px;
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
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
				</center>
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
						<th width="10%" align="center" >No</th>
						<th width="50%" align="center" >Nama Program Studi</th>
						<?  foreach($a_all as $kode => $namapdpt){?>
							<th width="10%" align="center" ><?= $namapdpt;?></th>
						<?}?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						while($row = $a_data->FetchRow()) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							// $t_key = $p_model::getKeyRow($row);
							
							$j = 0;
							// $rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td width="10" align="center"><?= $i;?>.</td>
						<td><?= $row['kodeunit'].' - '.$row['namaunit']?></td>
						<? foreach($a_all as $kode => $namapdpt){?>
						<td width="50" align="center">
							<img src="images/database_go.png" title="Export to EPSBED <?= $namapdpt?>" style="cursor:pointer" onClick="goExport('<?=$kode.":".$row["kodeunit"]?>')">
							<br>
							<?
								if($kode == 'e_msmhs')
									$tabelpdpt = 'msmhs';
								if($kode == 'e_trakm')
									$tabelpdpt = 'trakm';
								if($kode == 'e_trnlm')
									$tabelpdpt = 'trnlm';
								if($kode == 'e_trlsm')
									$tabelpdpt = 'trlsm';
								if($kode == 'e_trakd')
									$tabelpdpt = 'trakd';
								if($kode == 'e_trnlp')
									$tabelpdpt = 'trnlp';
								if($kode == 'e_tbkmk')
									$tabelpdpt = 'tbkmk';
								if($kode == 'e_trskr')
									$tabelpdpt = 'trskr';
								
								// if($kode == 'e_msdos')
									// $tabelpdpt = 'tmst_dosen';
								// if($kode == 'e_mspst')
									// $tabelpdpt = 'tmst_program_studi';
								// if($kode == 'e_trfas')
									// $tabelpdpt = 'tmst_sarana_pt';
								// if($kode == 'e_trkap')
									// $tabelpdpt = 'tran_daya_tampung';
								// if($kode == 'e_trlsd')
									// $tabelpdpt = 'tran_riwayat_status_dosen';
								// if($kode == 'e_trpud')
									// $tabelpdpt = 'tran_publikasi_dosen_tetap';
									
							echo $arr_log[$row["kodeunit"]][$tabelpdpt];
							?>
						</td>
						<?}?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= count($a_all)+4 ?>" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
				
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="rule" id="rule">
				<input type="hidden" name="key" id="key">
			</form>
		</div>
	</div>
</div>
<div id="div_dark" class="darken" style="display:none"></div>
<div id="div_progressbar" class="lighten" style="display:none" align="center">
		<table height="100%" style="border-collapse:collapse;"><tr>
		<td align="center">
		<table bgcolor="#FFFFFF"><tr><td >
		Mohon tunggu...<br><br><img src="images/progressbar.gif"></td></tr>
		</table>
		</td>
		</tr></table>
</div>
<script type="text/javascript">
$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
});

function goExport(rule)
{	
	$("#div_dark").show();
	$("#div_progressbar").show();
	document.getElementById("act").value = 'export';
	document.getElementById("rule").value = rule;
	goSubmit();
	// document.getElementById("submit").click();
}

</script>
</body>
</html>
