<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_edit = false;
	$r_nim = $_POST['nim'];
	
	if($r_nim[1] == ':')
		list(,$r_nim) = explode(':',$r_nim);
	
	if(!empty($r_nim)) {
		// cek unit
		$rowm = mAkademik::getDataMhsPendaftar($conn,$r_nim);
		if(!mAkademik::unitRide($conn,Modul::getUnit(),$rowm['kodeunit']))
			unset($r_nim);
	}
	
	if(!empty($r_nim)) {
		$r_nama = $r_nim.' - '.$rowm['nama'];
		
		// cek post
		$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
		if (empty ($r_periode))
			list($p_posterr, $p_postmsg) = array(true, 'Silahkan pilih Periode');
		else
			$r_edit = true;
		
		$r_jenistagihan = Modul::setRequest($_POST['jenistagihan'],'JENISTAGIHAN');
	}
	else
		list($p_posterr, $p_postmsg) = array(true, 'Silahkan pilih (Calon) Mahasiswa');
	
	// properti halaman
	$p_title = 'Tagihan Mahasiswa';
	$p_tbwidth = '100%';
	$p_aktivitas = 'SPP';
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_key = $_POST['key'];
	if ($r_act == 'generate' and $c_edit) {
		$conn->BeginTrans();
		
		$a_filter = array();
		$a_filter['sistemkuliah'] = $rowm['sistemkuliah'];
		$a_filter['kodeunit'] = $rowm['kodeunit'];
		$a_filter['nim'] = $r_nim;
		
		// jenis tagihan
		if(empty($r_key))
			$a_jenis = null;
		else
			$a_jenis = array($r_key => $r_key);
		
		list($p_posterr,$p_postmsg) = mTagihan::generateTagihan($conn,$a_filter,$r_periode,$a_jenis);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'join' and $c_edit) {
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg) = mTagihan::joinTagihan($conn,$_POST['check_'.$r_key]);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'split' and $c_edit) {
		$conn->BeginTrans();
		
		$r_tgltagihan = CStr::formatDate($_POST['tgltagihan']);
		$r_tgldeadline = CStr::formatDate($_POST['tgldeadline']);
		
		list($r_key,$r_jumlah) = explode(':',$r_key);
		list($p_posterr,$p_postmsg) = mTagihan::splitTagihan($conn,$r_key,$r_jumlah,$r_tgltagihan,$r_tgldeadline);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
	
	// ambil data
	$rows = mTagihan::getListTagihanPeriode($conn,$r_nim,$r_periode,$r_jenistagihan);
	
	// combo
	if(!empty($r_nim)) {
		$l_periode = uCombo::periode($conn,$r_periode,'periode','onchange="goSubmit()"',true);
		$l_jenistagihan = uCombo::jenistagihan($conn,$r_jenistagihan,'jenistagihan','onchange="goSubmit()"',true);
	}
	
	// membuat filter
	$a_filtercombo = array();
	if(!empty($r_nim)) {
		$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
		$a_filtercombo[] = array('label' => 'Jenis Tagihan', 'combo' => $l_jenistagihan);
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
	<script type="text/javascript" src="scripts/foredit.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?	/************************/
					/* COMBO FILTER HALAMAN */
					/************************/
				?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>">
						<table width="<?= $p_tbwidth ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<tr>		
											<td width="50" style="white-space:nowrap"><strong>(Calon) Mahasiswa</strong></td>
											<td>
												<strong> : </strong>
												<?= UI::createTextBox('nama',$r_nama,'ControlStyle','',50) ?>
												<input type="hidden" id="nimpilih" value="<?= $r_nim ?>">
												<input type="button" value="Pilih" onclick="goPilih()">
											</td>		
										</tr>
										<?	foreach($a_filtercombo as $t_filter) { ?>
										<tr>		
											<td style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>		
										</tr>
										<?	}
											if($c_edit and !empty($r_periode) and !empty($r_nim)) { ?>
										<tr>
											<td colspan="2">
												<input type="button" value="Generate Tagihan" onclick="goGenerate()">
											</td>
										</tr>
										<?	} ?>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>">
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
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle Pointerless" align="center">
					<?	/********/
						/* ITEM */
						/********/
						$n = count($rows);
						for($c=0;$c<$n;$c++) {
							$row = $rows[$c];
							if($row['jenistagihan'] != $t_jenis) {
								$i = 0;
								$t_totaltagihan = 0;
								$t_totaldeposit = 0;
								$t_totalbayar = 0;
								$t_jenis = $row['jenistagihan'];
					?>
					<tr>
						<th colspan="10">
							<div style="float:left"><?= $row['namajenistagihan'] ?></div>
							<? if($c_edit) { ?>
							<div style="float:right">
								<input type="button" value="Generate" onclick="goGenerateJenis(this,'<?= $t_jenis ?>')">
								<input type="button" value="Gabung" onclick="goJoinJenis(this,'<?= $t_jenis ?>')">
							</div>
							<? } ?>
						</th>
					</tr>
					<tr>
						<th style="width:10px">
							<? if($c_edit) { ?>
							<input type="checkbox" id="check_<?= $t_jenis ?>_all">
							<? } ?>
						</th>
						<th>ID Tagihan</th>
						<th>Tgl Tagihan</th>
						<th>Tgl Deadline</th>
						<th>Nominal</th>
						<th>Voucher</th>
						<th>Bayar</th>
						<th>Status</th>
						<th>Tgl Lunas</th>
						<? if($c_edit) { ?>
						<th style="width:60px">Aksi</th>
						<? } ?>
					</tr>
					<?		}
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							
							$t_key = $row['idtagihan'];
							$t_tagihan = (float)$row['nominaltagihan'];
							$t_deposit = (float)$row['nominaldeposit'];
							$t_bayar = (float)$row['nominalbayar']-(float)$row['nominalpakai'];
							
							$t_totaltagihan += $t_tagihan;
							$t_totaldeposit += $t_deposit;
							$t_totalbayar += $t_bayar;
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td>
							<? if($c_edit and $row['flaglunas'] == 'BB' and $row['nominalbayar'] == 0) { ?>
							<input type="checkbox" name="check_<?= $t_jenis ?>[]" value="<?= $t_key ?>">
							<? } else { ?>
							<?= $i ?>.
							<? } ?>
						</td>
                        <td><a href="<?= Route::navAddress('data_tagihan','key='.$t_key) ?>" class="ALink" target="_blank"><?= $row['idtagihan'] ?></a></td>
						<td align="center"><?= CStr::formatDate($row['tgltagihan']) ?></td>
						<td align="center"><?= CStr::formatDate($row['tgldeadline']) ?></td>
						<td align="right"><?= CStr::formatNumber($t_tagihan) ?></td>
						<td align="right"><?= CStr::formatNumber($t_deposit) ?></td>
						<td align="right"><?= CStr::formatNumber($t_bayar) ?></td>
						<td align="center">
							<strong>
							<?	switch($row['flaglunas']) {
									case 'L': echo '<span style="color:seagreen">Lunas</span>'; break;
									case 'S': echo '<span style="color:orange">Suspend</span>'; break;
									default: echo '<span style="color:red">Belum Lunas</span>';
								}
							?>
							</strong>
						</td>
						<td align="center"><?= CStr::formatDate($row['tgllunas']) ?></td>
						<? if($c_edit) { ?>
						<td align="center">
							<? if($row['flaglunas'] == 'BB' and $row['nominalbayar'] == 0) { ?>
							<input type="button" value="Split" onclick="goSplit('<?= $t_key ?>',<?= (float)$row['nominaltagihan'] ?>)">
							<? } ?>
						</td>
						<? } ?>
					</tr>
					<?		$rown = $rows[$c+1];
							if($rown['jenistagihan'] != $t_jenis) {
					?>
					<tr>
						<th colspan="4">&nbsp;</th>
						<th style="text-align:right"><?= CStr::formatNumber($t_totaltagihan) ?></th>
						<th style="text-align:right"><?= CStr::formatNumber($t_totaldeposit) ?></th>
						<th style="text-align:right"><?= CStr::formatNumber($t_totalbayar) ?></th>
						<th colspan="3">&nbsp;</th>
					</tr>
					<?		}
						}
						if($n == 0) {
					?>
					<tr>
						<td colspan="10" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
				<input type="hidden" name="nim" id="nim" value="<?= $r_nim ?>">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="tgltagihan" id="tgltagihan">
				<input type="hidden" name="tgldeadline" id="tgldeadline">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
		</div>
	</div>
</div>
<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript" src="scripts/jquery.number.min.js"></script>
<script type="text/javascript">

var ajaxpage = "<?= Route::navAddress('ajax') ?>";

$(document).ready(function() {
	$("#nama").xautox({strpost: "f=acmhspendaftarunit", targetid: "nimpilih", minlength: 2});
	
	$("[id$=_all]").click(function() {
		id = $(this).attr("id");
		cek = id.substr(0,id.length-4);
		
		$("[name='" + cek + "[]']").prop("checked",$(this).prop("checked"));
	});
});

function goPilih() {
	var nim = $("#nimpilih").val();
	if(nim) {
		$("#nim").val(nim);
		goSubmit();
	}
	else
		alert("Cari data (Calon) Mahasiswa terlebih dahulu");
}

<? if($c_edit) { ?>

function goGenerate() {
	var nama;
	var pilih = $("#jenistagihan option:selected");
	
	if(pilih.val() == "")
		nama = "";
	else
		nama = " " + pilih.text();
	
	var yakin = confirm("Apakah anda yakin akan generate ulang tagihan" + nama + " (calon) mahasiswa?");
	if(yakin) {
		$("#act").val("generate");
		$("#key").val(pilih.val());
		
		goSubmit();
	}
}

function goGenerateJenis(elem,key) {
	var namatagihan = $(elem).parents("th:eq(0)").find("div:eq(0)").text();
	
	var yakin = confirm("Apakah anda yakin akan generate ulang tagihan " + namatagihan + "(calon) mahasiswa?");
	if(yakin) {
		$("#act").val("generate");
		$("#key").val(key);
		
		goSubmit();
	}
}

function goJoinJenis(elem,key) {
	var namatagihan = $(elem).parents("th:eq(0)").find("div:eq(0)").text();
	
	var yakin = confirm("Apakah anda yakin akan menggabungkan tagihan " + namatagihan + " (calon) mahasiswa yang ter-CENTANG?");
	if(yakin) {
		$("#act").val("join");
		$("#key").val(key);
		
		goSubmit();
	}
}

function goSplit(key,nominal) {
	var nilai = prompt("Masukkan nominal tagihan sekarang");
	if(nilai) {
		nilai = parseFloat(nilai);
		var total = parseFloat(nominal);
		
		if(nilai > 0 && nilai < total) {
			var sisa = total-nilai;
			
			var tgl = prompt("Masukkan tanggal tagihan sisanya (dd-mm-yyyy)");
			if(tgl)
				$("#tgltagihan").val(tgl);
			tgl = prompt("Masukkan tanggal deadline tagihan tersebut (dd-mm-yyyy)");
			if(tgl)
				$("#tgldeadline").val(tgl);
			
			var yakin = confirm("Tagihan akan dibelah menjadi 2 dengan nilai masing-masing sejumlah " + $.number(nilai,0,',','.') + " dan " + $.number(sisa,0,',','.') + ", apakah anda yakin?");
			if(yakin) {
				$("#act").val("split");
				$("#key").val(key + ":" + nilai);
				
				goSubmit();
			}
		}
		else
			alert("Tagihan tidak bisa dibelah");
	}
}

<? } ?>

</script>
</body>
</html>