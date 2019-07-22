<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=false;
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_unit = Modul::getRequest('UNIT');
	$r_semester = Modul::getRequest('SEMESTER');
	$r_tahun = Modul::getRequest('TAHUN');
	$r_tahunpelaporan = Modul::getRequest('TAHUNPELAPORAN');
	
	$pilih = $_REQUEST["pilih"];
	if(empty($_REQUEST["pilih"])){
		$pilih = $_SESSION["SESS_PILIH"];	
	}
	if(!$pilih){
		$pilih = "dbf";		
	}
	$_SESSION["SESS_PILIH"]= $pilih;
	
	
	// properti halaman
	$p_title = 'Download Data EPSBED';
	$p_tbwidth = 550;
	$p_aktivitas = 'LAPORAN';
	// $conn->debug = true;
	$a_input = array();
	$a_input[] = array('label' => 'Tahun Pelaporan', 'input' => uCombo::tahun($r_tahunpelaporan,true,'tahunpelaporan','',false));
	$a_input[] = array('label' => 'Prodi', 'nameid' => 'unit', 'type' => 'S', 'option' => mCombo::unit($conn,false), 'default' => $r_unit);
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,false,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	
	$a_laporan = array();
	$a_laporan['rep_jurnal'] = 'Jurnal';
	$a_laporan['rep_absensi'] = 'Absensi';
	$a_laporan['rep_pftgsuts'] = 'Pf-Tgs-UTS';
	$a_laporan['rep_absensiuas&uts=1'] = 'UTS';
	$a_laporan['rep_absensiuas'] = 'UAS';
	$a_laporan['rep_nilai'] = 'Nilai Akhir';
	
	
	if(empty($p_reportpage))
		$p_reportpage = Route::getReportPage();
	// require_once($conf['view_dir'].'inc_repp.php');
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<!--<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">-->
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forreport.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post" target="_blank">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth+22 ?>px">
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
					<div class="box-content" style="width:<?= $p_tbwidth ?>px">
						<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="2" align="center">
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
								<td class="RightColumnBG">
									<select name="pilih" id="pilih" class="ControlStyle" onChange="doFilter()">					
										<option value="dbf" <?= $pilih=="dbf"? "selected" : "";?>>DBF</option>
										<option value="csv" <?= $pilih=="csv"? "selected" : "";?>>CSV</option>
									</select>	
								</td>
							</tr>
							<tr>
								<td class="LeftColumnBG">Data</td>
								<td class="RightColumnBG">			
									<select name="filter" class="ControlStyle" id="filter">	</select>
								</td>
						</table>
						<div class="Break"></div>
						<input type="button" value="Download EPSBED" class="ControlStyle" onclick="goDownload()">
					</div>
				</center>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var reportpage = "<?= Route::navAddress($p_reportpage) ?>";
var required = "<?= @implode(',',$a_required) ?>";

</script>
</body>
</html>

<script type="text/javascript">
	
	$(document).ready(function() {
		doFilter();
	});
	
	function doFilter(){
		var a = document.getElementById('pilih').value;
		if(a == 'dbf'){
			document.getElementById('filter').innerHTML = '<option value="all_dbf">Semua</option><option value="msmhs_dbf">MSMHS (Data mahasiswa, Format DBF)</option><option value="trakm_dbf">TRAKM (Data kuliah mahasiswa, Format DBF)</option><option value="trnlm_dbf">TRNLM (Data nilai semester mahasiswa, Format DBF)</option><option value="trnlp_dbf">TRNLP (Data nilai semester mahasiswa pindahan, Format DBF)</option><option value="trlsm_dbf">TRLSM (Data riwayat status mahasiswa, Format DBF)</option><option value="trakd_dbf">TRAKD (Data aktivitas mengajar dosen, Format DBF)</option><option value="trakd_adm_dbf">TRAKD ADM (Data aktivitas mengajar dosen (Admin), Format DBF)</option><option value="tbkmk_dbf">TBKMK (Data matakuliah, Format DBF)</option>';//<option value="msdos_dbf">MSDOS (Data Dosen, Format DBF)</option><option value="trkap_dbf">TRKAP (Data transaksi Kapasitas Mahasiswa Baru, Format DBF)</option><option value="mspst_dbf">MSPST (Data Program Studi, Format DBF)</option><option value="trlsd_dbf">TRLSD (Data Transaksi Cuti, Format DBF)</option><option value="trpud_dbf">TRPUD (Data Transaksi Publikasi Dosen, Format DBF)</option><option value="trfas_dbf">TRFAS (Data Fasilitas Program Studi, Format DBF)</option>';
		}else{
			document.getElementById('filter').innerHTML = '<option value="all">Semua</option><option value="msmhs">MSMHS (Data mahasiswa, Format CSV)</option><option value="trakm">TRAKM (Data kuliah mahasiswa, Format CSV)</option><option value="trnlm">TRNLM (Data nilai semester mahasiswa, Format CSV)</option><option value="trnlp">TRNLP (Data nilai semester mahasiswa pindahan, Format CSV)</option><option value="trlsm">TRLSM (Data riwayat status mahasiswa, Format CSV)</option><option value="trakd">TRAKD (Data aktivitas mengajar dosen, Format CSV)</option><option value="trakd_adm">TRAKD ADM (Data aktivitas mengajar dosen (Admin), Format CSV)</option><option value="tbkmk">TBKMK (Data matakuliah, Format CSV)</option>';//<option value="msdos">MSDOS (Data Dosen, Format CSV)</option><option value="trkap">TRKAP (Data transaksi Kapasitas Mahasiswa Baru, Format CSV)</option><option value="mspst">MSPST (Data Program Studi, Format CSV)</option><option value="trlsd">TRLSD (Data Transaksi Cuti, Format CSV)</option><option value="trpud">TRPUD (Data Transaksi Publikasi Dosen, Format CSV)</option><option value="trfas">TRFAS (Data Fasilitas Program Studi, Format CSV)</option>';
		}
	}
	
	function goDownload(){	
		var pilih = document.getElementById("pilih").value;		
		if(pilih=="csv"){
		    
			if(document.getElementById("filter").value == "all")
				document.getElementById("pageform").action = getPage('pdpt_all');			
			if(document.getElementById("filter").value == "msmhs")
				document.getElementById("pageform").action = getPage('pdpt_msmhs2');
			if(document.getElementById("filter").value == "trakm")
				document.getElementById("pageform").action = getPage('pdpt_trakm2');	
			if(document.getElementById("filter").value == "trnlm")
				document.getElementById("pageform").action = getPage('pdpt_trnlm2');
			if(document.getElementById("filter").value == "trnlp")
				document.getElementById("pageform").action = getPage('pdpt_trnlp2');
			if(document.getElementById("filter").value == "trlsm")
				document.getElementById("pageform").action = getPage('pdpt_trlsm2');	
			if(document.getElementById("filter").value == "trakd")
				document.getElementById("pageform").action = getPage('pdpt_trakd2');
			if(document.getElementById("filter").value == "trakd_adm")
				document.getElementById("pageform").action = getPage('pdpt_trakd_adm2');
			if(document.getElementById("filter").value == "tbkmk")
				document.getElementById("pageform").action = getPage('pdpt_tbkmk2');
				
			
			// if(document.getElementById("filter").value == "msdos")
				// document.getElementById("pageform").action="";
			// if(document.getElementById("filter").value == "trkap")
				// document.getElementById("pageform").action="";
			// if(document.getElementById("filter").value == "mspst")
				// document.getElementById("pageform").action="";
			// if(document.getElementById("filter").value == "trlsd")
				// document.getElementById("pageform").action="";
			// if(document.getElementById("filter").value == "trpud")
				// document.getElementById("pageform").action="";
			// if(document.getElementById("filter").value == "trfas")
				// document.getElementById("pageform").action="";
			
			goSubmit();
		}else if(pilih=="dbf"){
			if(document.getElementById("filter").value == "all_dbf")
				document.getElementById("pageform").action = getPage('pdpt_all_dbf');
			if(document.getElementById("filter").value == "msmhs_dbf")
				document.getElementById("pageform").action = getPage('pdpt_msmhs_dbf');
			if(document.getElementById("filter").value == "trakm_dbf")
				document.getElementById("pageform").action = getPage('pdpt_trakm_dbf');
			if(document.getElementById("filter").value == "trnlm_dbf")
				document.getElementById("pageform").action = getPage('pdpt_trnlm_dbf');
			if(document.getElementById("filter").value == "trnlp_dbf")
				document.getElementById("pageform").action = getPage('pdpt_trnlp_dbf');
			if(document.getElementById("filter").value == "trlsm_dbf")
				document.getElementById("pageform").action = getPage('pdpt_trlsm_dbf');
			if(document.getElementById("filter").value == "trakd_dbf")
				document.getElementById("pageform").action = getPage('pdpt_trakd_dbf');
			if(document.getElementById("filter").value == "trakd_adm_dbf")
				document.getElementById("pageform").action = getPage('pdpt_trakd_adm_dbf');
			if(document.getElementById("filter").value == "tbkmk_dbf")
				document.getElementById("pageform").action = getPage('pdpt_tbkmk_dbf');
				
			// if(document.getElementById("filter").value == "msdos_dbf")
				// document.getElementById("pageform").action="";
			// if(document.getElementById("filter").value == "trkap_dbf")
				// document.getElementById("pageform").action="";
			// if(document.getElementById("filter").value == "mspst_dbf")
				// document.getElementById("pageform").action="";
			// if(document.getElementById("filter").value == "trlsd_dbf")
				// document.getElementById("pageform").action="";
			// if(document.getElementById("filter").value == "trpud_dbf")
				// document.getElementById("pageform").action="";
			// if(document.getElementById("filter").value == "trfas_dbf")
				// document.getElementById("pageform").action="";
				
			goSubmit();	
		}
	}
</script>