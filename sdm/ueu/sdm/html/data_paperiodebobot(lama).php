<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
		
	require_once(Route::getModelPath('pa'));
	require_once(Route::getUIPath('form'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
			
	// definisi variabel halaman
	$p_title = 'Data Periode Bobot';
	$p_window = '['.$conf['page_title'].'] '.$p_title;
	$p_tbwidth = '860';
	$p_col = 4;
	$p_listpage = Route::getListPage();
	$p_model = mPa;
	
	if (!empty($r_key)){
		$info = $p_model::getInfoBobot($conn,$r_key);
		$p_label ='Periode Bobot '.' ('.$info['namaperiode'].')';
	}else{
		$p_label = "Tambah Periode Bobot Baru";
	}
	
?>
	
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<title><?= $p_window ?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
	<style type="text/css" media="screen">@import "style/basic.css";</style>
	<style type="text/css" media="screen">@import "style/tabs.css";</style>
	<link href="style/style.css" type="text/css" rel="stylesheet">
	<link href="style/pager.css" type="text/css" rel="stylesheet">
	<link href="style/calendar.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<style>
		.EditInfo {
			color:#000;
			font-weight:bolder;
			background: url('images/kate.png');
			background-position:right;
			background-repeat:no-repeat;
		}
		
		.DTitle {
			color: #647287;
			font-size: 18px;
			font-weight: bold;
		}
	</style>
</head>

<body leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0">
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<center>
				<div class="DTitle" style="width:<?= $p_tbwidth ?>px;">
					<table border="0" cellspacing="10" align="left">
						<tr>
							<td valign="bottom" align="left">
								<table border="0" cellspacing="10" align="left">
									<tr>
										<td id="be_list" class="TDButton" onClick="location.href='<?= Route::navAddress($p_listpage); ?>'">
											<img src="images/list.png"> Daftar
										</td>
									</tr>
								</table>
							</td>
							<td valign="bottom" width="600" align="right"><?= $p_label ?></td>
							<td align="right" width="75px">
								<img id="imgfotobio" style="padding:3px;border:1pt solid #ccc;" src="images/aktivitas/CONF.png" width="55" height="75">
							</td>
						</tr>
					</table>
				</div>
				<div style="border-bottom: 1px solid #EE4037;width:<?= $p_tbwidth ?>px;padding-top:100px;"></div>
			</center>
			<br />
				
			<table align="center" width="<?= $p_tbwidth; ?>"><tr><td>
			<div id="header">
			
			<ul id="primary_t" style="display:none;">
				<li><span>Periode</span>
					<ul id="secondary">
						<li id="chosen"><a href="<?= Route::navAddress('ms_paperiodebobot'); ?>" >Tambah Periode</a></li>
					</ul>
				</li>
			</ul>
			<ul id="primary">
				<li><span>Periode</span>
					<ul id="secondary">
						<li id="inshide"><a href="<?= Route::navAddress('ms_paperiodebobot'); ?>" >Periode Bobot</a></li>
						<li><a href="<?= Route::navAddress('list_pabobotsubjobj'); ?>" >% Subjektif Objektif</a></li>
						<li><a href="<?= Route::navAddress('list_pahasilkategori'); ?>" >Kategori Hasil Penilaian</a></li>
					</ul>
				</li>
				<li><span>Bobot</span>
					<ul id="secondary">
						<li><a href="<?= Route::navAddress('list_pabobotsubj'); ?>" >Bobot Nilai Subjektif</a></li>
						<li><a href="<?= Route::navAddress('list_pabobotobj'); ?>" >Bobot Nilai Objektif</a></li>
						<li><a href="<?= Route::navAddress('list_paindeksobj'); ?>" >Indeks Nilai Objektif</a></li>
					</ul>
				</li>
			</ul>
			</div>
			<div id="main">
				<div id="contents"></div>
			</div>
			
			<div id="roller" style="position:absolute;visibility:hidden;left:0px;top:0px;">
				<img src="images/roller.gif">
			</div>
			
			<div id="progressbar" style="position:absolute;visibility:hidden;left:0px;top:0px;">
				<table bgcolor="#FFFFFF" border="1" style="border-collapse:collapse;"><tr><td align="center">
				Mohon tunggu...<br><br><img src="images/progressbar.gif">
				</td></tr></table>
			</div>
		</div>
	</div>
</div>
</body>

<script type="text/javascript" src="scripts/jquery.common.js"></script>
<script type="text/javascript" src="scripts/commonx.js"></script>
<script type="text/javascript" src="scripts/jquery.xtabs.js"></script>
<script type="text/javascript">
	var listpage = "<?= Route::navAddress($p_listpage) ?>";
	var key = "<?= $r_key; ?>";
	var defsent = "key=" + key;
	
	$(function() {
		if(key == "") { // insert mode
			switchTab();
		}
		$("#header").xtabs({sent:defsent,deftab:"0"});
		if(key == "") { // insert mode
			$("#header").showOnlyTab(0);
		}
	});
	
	function showRealTab() {
		$("#header").showrealtab(defsent);
	}
	
	function switchTab() {
		$("#primary").hide();
		$("#primary").attr("id","primary_t");
		$("#primary_t").attr("id","primary");
		$("#primary").show();
	}
</script>
</html>
