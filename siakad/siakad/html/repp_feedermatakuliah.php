<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
    
    
    // hak akses
    Modul::getFileAuth();
    

    // include
	require_once(Route::getUIPath('combo'));
    require_once(Route::getUIPath('form'));
    
    // variabel request
	$r_semester = Modul::getRequest('SEMESTER');
	$r_tahun = Modul::getRequest('TAHUN');
	$r_unit = Modul::getRequest('UNIT');
    $r_angkatan = Modul::getRequest('ANGKATAN');
    
    $p_title = 'Laporan Feeder Mahasiswa';
	$p_tbwidth = 400;
    $p_aktivitas = 'LAPORAN';
    

    $a_input = array();
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,false,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	$a_input[] = array('label' => 'Prodi', 'nameid' => 'unit', 'type' => 'S', 'option' => mCombo::unit($conn,false,false), 'default' => $r_unit);
	$a_input[] = array('label' => 'Angkatan', 'nameid' => 'angkatan', 'type' => 'S', 'option' => mCombo::angkatan($conn), 'default' => $r_angkatan);
	$a_laporan = array();
	$a_laporan['rep_feedermatakuliah'] = 'Tampilkan';





?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forreport.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
					<?	/********/
						/* DATA */
						/********/
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
					<?	$a_required = array();
						foreach($a_input as $t_row) {
							if($t_row['notnull'])
								$a_required[] = $t_row['id'];
							if(empty($t_row['input']))
								$t_row['input'] = uForm::getInput($t_row);
					?>
						<tr>
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">
								<?= $t_row['label'] ?>
								<?= $t_row['notnull'] ? '<span id="edit" style="display:none">*</span>' : '' ?>
							</td>
							<td class="RightColumnBG">
								<?= $t_row['input'] ?>
							</td>
						</tr>
					<?	} ?>
						<tr>
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">Format</td>
							<td class="RightColumnBG"><?= uCombo::format() ?></td>
						</tr>
					</table>
					<div class="Break"></div>
					<?	if(empty($a_laporan)) { ?>
					<input type="button" value="Tampilkan" class="ControlStyle" onclick="goReport()">
					<?	} else {
							foreach($a_laporan as $t_file => $t_label) { ?>
					<input type="button" value="<?= $t_label ?>" class="ControlStyle" onclick="goReport('<?= $t_file ?>')">
					<?		}
						} ?>
					</div>
				</center>
				
				<input type="hidden" id="npm" name="npm">
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	// autocomplete
	$("#mahasiswa").xautox({strpost: "f=acmahasiswa", targetid: "npm"});
});

</script>
</body>
</html>