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
		
	$p_tbwidth = "800";
	$p_title = "Data Slip Lembur Gaji";
	$p_aktivitas = 'ANGGARAN';
	$p_listpage = Route::getListPage();
	$p_dbtable = "ga_upahlembur";
	$p_key = "periodegaji,idpegawai";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key))
		Route::navigate($p_listpage);
			
	//mendapatkan data lembur
	$a_lembur = $p_model::getInfoLembur($conn,$r_key);
	$a_info = $a_lembur['info'];
	$a_data = $a_lembur['data'];
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<style>
		.bottomline td{
			border-bottom:1px solid #eaeaea;
		}
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<table border="0" cellspacing="10" align="center">
					<tr>
						<?	if($c_readlist) { ?>
						<td id="be_list" class="TDButton" onclick="goList()">
							<img src="images/list.png"> Daftar
						</td>
						<?	} ?>
						<td id="be_print" class="TDButton" onclick="goPrint()">
							<img src="images/small-print.png"> Slip Gaji
						</td>
						<td id="be_printlembur" class="TDButton" onclick="goPrintLembur()">
							<img src="images/small-print.png"> Slip Lembur
						</td>
					</tr>
				</table>
				<div class="Break"></div>
				<?					
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
						<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="0" align="center" class="bottomline">
							<tr>
								<td width="150px">Nama</td>
								<td width="10px">:</td>
								<td><b><?= $a_info['namalengkap']; ?></b></td>
							</tr>
							<tr>
								<td>Unit </td>
								<td>:</td>
								<td><?= $a_info['namaunit'] ?></td>
							</tr>
						</table>
						<br>
						
						<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
							<tr class="DataBG" height="30">
								<td colspan="5">Lembur di Hari Kerja</td>
							</tr>
							<tr>
								<th>No.</th>
								<th>Tanggal</th>
								<th>Jam Datang</th>
								<th>Jam Pulang</th>
								<th>Jam Diakui</th>
							</tr>
							<? 
								if (count($a_data['H']['tanggal']) > 0){
									$i = 0;
									foreach($a_data['H']['tanggal'] as $inc => $date){
										if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG';
							?>
							<tr class="<?= $rowstyle ?>">
								<td align="right"><?= ++$i; ?></td>
								<td align="center"><?= CStr::formatDateInd($date); ?></td>
								<td align="center"><?= CStr::formatJam($a_data['H']['jamdatang'][$inc]); ?></td>
								<td align="center"><?= CStr::formatJam($a_data['H']['jampulang'][$inc]); ?></td>
								<td align="center"><?= number_format($a_data['H']['totlembur'][$inc],2); ?></td>
							</tr>
							<? }} ?>
						</table>
						<br>
						
						<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
							<tr class="DataBG" height="30">
								<td colspan="5">Lembur di Hari Libur</td>
							</tr>
							<tr>
								<th>No.</th>
								<th>Tanggal</th>
								<th>Jam Datang</th>
								<th>Jam Pulang</th>
								<th>Jam Diakui</th>
							</tr>
							<? 
								if (count($a_data['HL']['tanggal']) > 0){
									$i = 0;
									foreach($a_data['HL']['tanggal'] as $inc => $date){
										if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG';
							?>
							<tr class="<?= $rowstyle ?>">
								<td align="right"><?= ++$i; ?></td>
								<td align="center"><?= CStr::formatDateInd($date); ?></td>
								<td align="center"><?= CStr::formatJam($a_data['HL']['jamdatang'][$inc]); ?></td>
								<td align="center"><?= CStr::formatJam($a_data['HL']['jampulang'][$inc]); ?></td>
								<td align="center"><?= number_format($a_data['HL']['totlembur'][$inc],2); ?></td>
							</tr>
							<? }}else{ ?>
							<tr>
								<td colspan="5" align="center">Data tidak ditemukan</td>
							</tr>
							<? } ?>
						</table>
						<br>
												
						<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="0" align="center" class="bottomline">
							<tr>
								<td width="150px"><b>Upah Lembur<b></td>
								<td width="50px"><b>: Rp.</b></td>
								<td><b><?= CStr::formatNumber($a_info['upahlembur']) ?></b></td>
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

function goPrint() {
	var keys = '<?= $r_key?>';
	keys = keys.split('|');
	window.open("<?= Route::navAddress('rep_gaslipgaji') ?>"+"&periode="+keys[0]+"&idpegawai="+keys[1]+"&format=html","_blank");
}

function goPrintLembur() {
	var keys = '<?= $r_key?>';
	keys = keys.split('|');
	window.open("<?= Route::navAddress('rep_gasliplembur') ?>"+"&periode="+keys[0]+"&idpegawai="+keys[1]+"&format=html","_blank");
}
</script>
</body>
</html>
