<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('gaji'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	//konfigurasi halaman
	$p_model = mGaji;
		
	$p_tbwidth = "600";
	$p_title = "Penarikan Data Mengajar Dosen untuk Perhitungan Gaji";
	$p_aktivitas = 'ANGGARAN';
	$p_listpage = Route::getListPage();
	$p_dbtable = "ga_mengajarlog";
	$p_key = "tglkuliah,perkuliahanke,periode,thnkurikulum,kodeunit,kodemk,kelasmk";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
		
	$row = $p_model::getDataHistoryMengajar($conn,$r_key);
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
	
	//jenis kuliah
	$a_jnskuliah = $p_model::jenisKuliah();
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	}
					
					/*****************/
					/* TOMBOL-TOMBOL */
					/*****************/
					
					if(empty($p_fatalerr))
						require_once('inc_databutton.php');
					
					if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	}
				
					if(empty($p_fatalerr)) { ?>
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
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap" width="150px">Periode Gaji</td>
							<td class="RightColumnBG"><?= $row['namaperiodegaji'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">NPP</td>
							<td class="RightColumnBG"><?= $row['nik'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Nama Pegawai</td>
							<td class="RightColumnBG"><?= $row['namapegawai'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Tgl. Kuliah</td>
							<td class="RightColumnBG"><?= CStr::formatDateInd($row['tglkuliah']) ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Perkuliahan Ke</td>
							<td class="RightColumnBG"><?= $row['perkuliahanke'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Periode</td>
							<td class="RightColumnBG"><?= $row['periode'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Tahun Kurikulim</td>
							<td class="RightColumnBG"><?= $row['thnkurikulum'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Mengajar Unit</td>
							<td class="RightColumnBG"><?= $row['namaunit'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Matakuliah</td>
							<td class="RightColumnBG"><?= $row['kodemk'].' - '.$row['namamk'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Ruang</td>
							<td class="RightColumnBG"><?= $row['koderuang'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">SKS</td>
							<td class="RightColumnBG"><?= $row['sks'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Jenis Kuliah</td>
							<td class="RightColumnBG"><?= $a_jnskuliah[$row['jeniskuliah']] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Waktu Kuliah</td>
							<td class="RightColumnBG"><?= CStr::formatJam($row['waktumulai']).' - '.CStr::formatJam($row['waktuselesai']) ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Jumlah Jam</td>
							<td class="RightColumnBG"><?= $row['jmljam'] ?></td>
						</tr>
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<?	} ?>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

</script>
</body>
</html>
