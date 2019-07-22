<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('gaji'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$a_key = CStr::removeSpecial($_REQUEST['key']);
	list($r_key,$p_listpage) = explode('::',$a_key);
	
	//konfigurasi halaman
	$p_model = mGaji;
		
	$p_tbwidth = "600";
	$p_title = "Penguncian Data Pegawai untuk Perhitungan Gaji";
	$p_aktivitas = 'ANGGARAN';
	$p_listpage = $p_listpage;
	$p_dbtable = "ga_historydatagaji";
	$p_key = "idpeg,gajiperiode";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
		
	$row = $p_model::getDataHistoryGaji($conn,$r_key);
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
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
							<td class="LeftColumnBG" style="white-space:nowrap">Periode Tarif</td>
							<td class="RightColumnBG"><?= $row['namaperiodetarif'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">NIP</td>
							<td class="RightColumnBG"><?= $row['nik'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Nama Pegawai</td>
							<td class="RightColumnBG"><?= $row['namapegawai'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Jenis Kelamin</td>
							<td class="RightColumnBG"><?= $row['jnskelamin'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Status Menikah</td>
							<td class="RightColumnBG"><?= $row['statusnikah'] ?></td>
						</tr>
						<?if($row['jeniskelamin'] == 'P'){?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Suami Bekerja</td>
							<td class="RightColumnBG"><?= $row['pasangankerja'] ?></td>
						</tr>
						<?}?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Jumlah Anak</td>
							<td class="RightColumnBG"><?= $row['jmlanak'] ?></td>
						</tr>
						<tr height="30">
							<td class="DataBG" colspan="4">Informasi Kepegawaian</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Unit Kerja</td>
							<td class="RightColumnBG"><?= $row['namaunit'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Jenis Pegawai</td>
							<td class="RightColumnBG"><?= $row['namajenispegawai'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Hubungan Kerja</td>
							<td class="RightColumnBG"><?= $row['hubkerja'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Status Aktif</td>
							<td class="RightColumnBG"><?= $row['namastatusaktif'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Jam Kerja</td>
							<td class="RightColumnBG"><?= $row['jamkerja'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Golongan</td>
							<td class="RightColumnBG"><?= $row['golongan'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Masa Kerja</td>
							<td class="RightColumnBG"><?= $row['masakerja'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Jabatan Struktural</td>
							<td class="RightColumnBG"><?= $row['jabatanstruktural'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Jenis Pejabat</td>
							<td class="RightColumnBG"><?= $row['jenispejabat'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Jabatan Fungsional</td>
							<td class="RightColumnBG"><?= $row['jabatanfungsional'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Pendidikan Terakhir</td>
							<td class="RightColumnBG"><?= $row['namapendidikan'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">No. Rekening</td>
							<td class="RightColumnBG"><?= $row['norekening'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Atas Nama Rekening</td>
							<td class="RightColumnBG"><?= $row['anrekening'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">NPWP</td>
							<td class="RightColumnBG"><?= $row['npwp'] ?></td>
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
