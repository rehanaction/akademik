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
	list($key,$page) = explode('::',$r_key);
	
	//konfigurasi halaman
	$p_model = mGaji;
		
	$p_tbwidth = "800";
	$p_title = "Data Slip Gaji";
	$p_aktivitas = 'ANGGARAN';
	$p_listpage = $page;
	$p_dbtable = "ga_gajipeg";
	$p_key = "periodegaji,idpegawai";
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key))
		Route::navigate($p_listpage);
	
	//mendapatkan data gaji
	$a_data = $p_model::getInfoGaji($conn,$key);
	$a_tunj = $p_model::getTunjTetapSlip($conn,$key);
	$a_jtunj = $p_model::getTunjTetapGaji($conn);
	$a_jtunjdet = $p_model::getTunjTetapGajiDet($conn);
	$a_tunja = $p_model::getTunjAwalSlip($conn,$key);
	$a_jtunjawal = $p_model::getTunjTetapAwal($conn);
	$a_jtunjawaldet = $p_model::getTunjTetapAwalDet($conn);
	$a_ttunj = $p_model::getTunjPenyesuaianSlip($conn,$key);
	$a_jttunj = $p_model::getTunjPenyesuaian($conn);
	$a_pot = $p_model::getPotonganSlip($conn,$key);
	$a_jpot = $p_model::getJnsPotongan($conn);
	
	$a_tunjstrukturallain = $p_model::getTunjTetapStrukLain($conn,$key);
	$a_struktural = $p_model::getInfoStruktural($conn);
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
							<td>Periode</td>
							<td>:</td>
							<td colspan="4"><b><?= $a_data['namaperiode'] ?></b></td>
						</tr>
						<tr>
							<td>Nama</td>
							<td>:</td>
							<td colspan="4"><b><?= $a_data['namapegawai'] ?></b></td>
						</tr>
						<tr>
							<td>Jabatan</td>
							<td>:</td>
							<td colspan="4"><?= $a_data['jabatanstruktural'] ?></td>
						</tr>
						<tr>
							<td>Pendidikan</td>
							<td>:</td>
							<td colspan="4"><?= $a_data['namapendidikan'] ?></td>
						</tr>
						<?if($a_data['idtipepeg'] == 'D' or $a_data['idtipepeg'] == 'AD'){?>
						<tr>
							<td>Fungsional</td>
							<td>:</td>
							<td colspan="4"><?= $a_data['fungsional'] ?></td>
						</tr>
						<?}?>
						<tr>
							<td>Masa Kerja</td>
							<td>:</td>
							<td colspan="4"><?= $a_data['mkgaji'] ?></td>
						</tr>
						<tr>
							<td colspan="6">&nbsp;</td>
						</tr>
						<tr>
							<td>Gaji Pokok</td>
							<td>: Rp.</td>
							<td align="right" style="padding-right:30px"><?= CStr::formatNumber($a_data['gapok']) ?></td>
							<td colspan="3">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="3">Tunjangan</td>
							<td colspan="3">Potongan</td>
						</tr>
						<tr style="border:none">
							<td colspan="3" valign="top" style="border:none" width="50%">
								<table width="95%" cellpadding="4" cellspacing="0">
									<?
									if(count($a_jtunj)>0){
										$tunjangantetap = !empty($a_data['gapok']) ? $a_data['gapok'] : 0;
										foreach($a_jtunj as $ikey=>$val){
											if($ikey == $a_jtunjdet[$a_data['idjenispegawai']][$ikey]){
												$tunjangantetap += $a_tunj[$a_data['idpegawai']][$ikey];
									?>
									<tr>
										<td width="50%">- <?= $val?></td>
										<td width="10%">: Rp.</td>
										<td width="35%" align="right"><?= CStr::formatNumber($a_tunj[$a_data['idpegawai']][$ikey]); ?></td>
									</tr>
									<?		
											}
											
											$tunjstrukturallain = $a_tunjstrukturallain[$a_data['idpegawai']][$ikey];					
											if(count($tunjstrukturallain)>0){
												foreach($tunjstrukturallain as $keystruk=>$valstruk){
													$tunjangantetap += $valstruk;
									?>
									<tr>
										<td width="50%">- Tunjangan Struktural Lain (<?= $a_struktural[$keystruk]?>)</td>
										<td width="10%">: Rp.</td>
										<td width="35%" align="right"><?= CStr::formatNumber($valstruk); ?></td>
									</tr>
									<?			}
											}
										}
									}
									?>
									<tr>
										<td colspan="2">&nbsp;</td>
										<td valign="bottom" align="right">________________ +</td>
									</tr>
									<?
										$sistem = $tunjangantetap;
									?>
									<tr style="font-weight:bold">
										<td width="50%" align="right">SISTEM</td>
										<td width="10%">: Rp.</td>
										<td width="35%" align="right"><?= CStr::formatNumber($sistem) ?></td>
									</tr>
								</table>
								<br>
								
								<table width="95%" cellpadding="4" cellspacing="0">
									<?
									if(count($a_jttunj)>0){
										$tunjangantdtetap=0;
										foreach($a_jttunj as $ikey=>$val){
											$tunjangantdtetap += $a_ttunj[$a_data['idpegawai']][$ikey];
									?>
									<tr>
										<td width="50%">- <?= $val?></td>
										<td width="10%">: Rp.</td>
										<td width="35%" align="right"><?= CStr::formatNumber($a_ttunj[$a_data['idpegawai']][$ikey]); ?></td>
									</tr>
									<?}}
									if($a_data['idtipepeg']=='A'){?>
										<tr>
											<td width="50%">- Lembur</td>
											<td width="10%">: Rp.</td>
											<td width="35%" align="right"><?= CStr::formatNumber($a_data['upahlembur']); ?></td>
										</tr>
									<?}
									?>
									<tr>
										<td colspan="2">&nbsp;</td>
										<td valign="bottom" align="right">________________ +</td>
									</tr>
									<?
										$bruto = $sistem + $tunjangantdtetap;
										if($a_data['idtipepeg']=='A')
											$bruto += $a_data['upahlembur'];
									?>
									<tr style="font-weight:bold">
										<td width="50%" align="right">BRUTO</td>
										<td width="10%">: Rp.</td>
										<td width="35%" align="right"><?= CStr::formatNumber($bruto) ?></td>
									</tr>
								</table>
							</td>
							<td colspan="3" valign="top" style="border:none" width="50%">
								<table width="97%" cellpadding="4" cellspacing="0">
									<?
									if(count($a_jpot)>0){
										$potongan = 0;
										foreach($a_jpot as $ikey=>$val){
											$potongan += $a_pot[$a_data['idpegawai']][$ikey];
									?>
									<tr>
										<td width="50%">- <?= $val?></td>
										<td width="10%">: Rp.</td>
										<td width="37%" align="right"><?= CStr::formatNumber($a_pot[$a_data['idpegawai']][$ikey]); ?></td>
									</tr>
									<?}}?>
									<tr>
										<td width="50%">- PPh Ps. 21</td>
										<td width="10%">: Rp.</td>
										<td width="37%" align="right"><?= CStr::formatNumber($a_data['pph']) ?></td>
									</tr>
									<tr>
										<td colspan="2">&nbsp;</td>
										<td valign="bottom" align="right">________________ +</td>
									</tr>
									<?
										$totpotongan = $potongan + $a_data['pph'];
									?>
									<tr>
										<td width="50%">Total Potongan</td>
										<td width="10%">: Rp.</td>
										<td width="37%" align="right"><?= CStr::formatNumber($totpotongan) ?></td>
									</tr>
									<tr>
										<td colspan="3">&nbsp;</td>
									</tr>
									<?
										$netto = $bruto - $totpotongan
									?>
									<tr style="font-weight:bold">
										<td width="50%" align="right">NETTO</td>
										<td width="12%">: Rp.</td>
										<td width="35%" align="right"><?= CStr::formatNumber($netto) ?></td>
									</tr>
									<tr>
										<td colspan="3">&nbsp;</td>
									</tr>
									<tr>
										<td width="50%">Pengembalian PPh Ps. 21</td>
										<td width="10%">: Rp.</td>
										<td width="37%" align="right"><?= CStr::formatNumber($a_data['pph']) ?></td>
									</tr>
									<tr>
										<td colspan="2">&nbsp;</td>
										<td valign="bottom" align="right">________________ +</td>
									</tr>
									<?
										$gajiditerima = $netto + $a_data['pph'];
									?>
									<tr style="font-weight:bold">
										<td width="50%" align="right">Gaji Diterima</td>
										<td width="12%">: Rp.</td>
										<td width="35%" align="right"><?= CStr::formatNumber($gajiditerima) ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="6">&nbsp;</td>
						</tr>
						<?if(count($a_jtunjawaldet)>0){
							foreach($a_jtunjawaldet as $jpeg){
								if($jpeg == $a_data['idjenispegawai']){?>
								<tr>
									<td><b>Tunjangan sudah dibayarkan</b></td>
									<td colspan="5">&nbsp;</td>
								</tr>
						<?
						if(count($a_jtunjawal)>0){
							$gajiawal = 0;
							foreach($a_jtunjawal as $ikey=>$val){
								$gajiawal += $a_tunja[$a_data['idpegawai']][$ikey];
						?>
						<tr>
							<td width="25%">- <?= $val?></td>
							<td width="5%">: Rp.</td>
							<td width="20%" align="right" style="padding-right:30px"><?= CStr::formatNumber($a_tunja[$a_data['idpegawai']][$ikey]); ?></td>
							<td colspan="3">&nbsp;</td>
						</tr>
						<?}}?>
						<tr>
							<td colspan="2">&nbsp;</td>
							<td valign="bottom" align="center">________________ +</td>
							<td colspan="3">&nbsp;</td>
						</tr>
						<tr>
							<td width="25%"><b>Total</b></td>
							<td width="5%">: Rp.</td>
							<td width="20%" align="right" width="20%" style="padding-right:30px"><?= CStr::formatNumber($gajiawal) ?></td>
							<td colspan="3">&nbsp;</td>
						</tr>
						<?}}}?>
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
	var keys = '<?= $key?>';
	keys = keys.split('|');
	window.open("<?= Route::navAddress('rep_gaslipgaji') ?>"+"&periode="+keys[0]+"&idpegawai="+keys[1]+"&format=html","_blank");
}
</script>
</body>
</html>
