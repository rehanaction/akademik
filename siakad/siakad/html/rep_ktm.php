<?php
// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	//ini_set('display_errors',1);
	ob_clean();
	// hak akses
	Modul::getFileAuth();
	
	
	// include
	require_once(Route::getModelPath('laporanmhs'));
	require_once(Route::getModelPath('progpend'));
	require_once(Route::getUIPath('form'));
	//require_once($conf['includes_dir'].'fpdf/tcetak2.php');
	//require_once($conf['includes_dir'].'fpdf/fpdf_ktm.php');
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_angkatan = (int)$_REQUEST['angkatan'];
	$r_npm = $_REQUEST['key'];
	$r_format = $_REQUEST['format'];
	$r_tglberlaku = $_REQUEST['tglberlaku'];
	
	// properti halaman
	$p_title = 'Laporan KHS';
	$p_tbwidth = 720;
	
	$p_namafile = 'ktm_'.$r_kodeunit.'_'.$r_angkatan;
	$a_data = mLaporanMhs::getKtm($conn,$r_kodeunit,$r_angkatan,$r_npm);

	mLaporanMhs::ceklis($conn, $r_npm);

	//$fotomhs = uForm::getPathImageMahasiswa($conn,$row['nim']);
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
	<style type="text/css">
		body, html {
		  height: 100%;
		}

		.bg { 
		  /* The image used */
		  background-image: url("images/backid.jpg");

		  /* Full height */
		  height: 100%; 

		  /* Center and scale the image nicely */
		  background-position: center;
		  background-repeat: no-repeat;
		  background-size: cover;
		}
	</style>
</head>
<body class="bg">

<?php foreach($a_data as $row){ ?>
<?php $fotomhs = uForm::getPathImageMahasiswa($conn,$row['nim']); ?>
<br>
<br>
<br>
<p style="margin-top: 0px;margin-bottom: 4%;"></p>
<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{font-family:Arial, sans-serif;font-size:12px;padding:10px 5px;overflow:hidden;word-break:normal;}
.tg th{font-family:Arial, sans-serif;font-size:12px;font-weight:normal;padding:0px 10px 0px 0px;overflow:hidden;word-break:normal;}
.tg .tg-baqh{text-align:center;vertical-align:top}
.tg .tg-0lax{text-align:left;vertical-align:top}
</style>
<table class="tg" width="100%">
  <tr>
    <th class="tg-baqh" width="80px;" style="padding-left: 7px !important;"><img src="<?= $fotomhs ?>" width="100%" style="border-radius: 3px;"></th>
    <th class="tg-baqh"><span style="font-weight:bold; "><?= $row['nama']; ?></span><p style="margin-top: 5px;margin-bottom: 5px;"><?= $row['nim']; ?></p><p style="margin-top: 5px"><?= $row['jenjang']; ?> <?= strtoupper($row['jurusan']); ?></p></th>
  </tr>
</table>
	
<?php } ?>
</body>
<script type="text/javascript">

window.print();

</script>
</html>


