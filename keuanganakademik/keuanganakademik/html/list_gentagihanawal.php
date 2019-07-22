<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	//$conn->debug=-true;
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('loggenerate'));
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
	$r_sistem = Modul::setRequest($_POST['sistemkuliah'],'SISTEMKULIAH');
	$r_jalur = Modul::setRequest($_POST['jalurpenerimaan'],'JALUR');
	$r_gelombang = Modul::setRequest($_POST['gelombang'],'GELOMBANG');
	
	// jenis tagihan
	$a_jenis = mTagihan::getListJenisTagihanGenerate($conn,$r_periode,$r_sistem,true); // hanya pendaftar, tidak termasuk maba
	
	// cek post
	if (empty ($r_periode))
		list($p_posterr, $p_postmsg) = array(true, 'Silahkan pilih Periode');
	else if (empty ($r_sistem))
		list($p_posterr, $p_postmsg) = array(true, 'Silahkan pilih Sistem Kuliah');
	else if (empty ($r_jalur))
		list($p_posterr, $p_postmsg) = array(true, 'Silahkan pilih Jalur Penerimaan');
	else if (empty ($r_gelombang))
		list($p_posterr, $p_postmsg) = array(true, 'Silahkan pilih Gelombang');
	
	// properti halaman
	$p_title = 'Generate Tagihan Awal &nbsp; <span style="color:yellow">(Khusus Pendaftar yang Belum Menjadi Mahasiswa)</span>';
	$p_tbwidth = '100%';
	$p_aktivitas = 'Master';
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_key = $_POST['key'];
	if (($r_act == 'generate' or $r_act == 'void') and $c_edit) {
		// filter data
		$a_filter = array();
		$a_filter['sistemkuliah'] = $r_sistem;
		$a_filter['jalurpenerimaan'] = $r_jalur;
		$a_filter['gelombang'] = $r_gelombang;
		$a_filter['kodeunit'] = $r_key;
		$a_filter['ispendaftar'] = true; // hanya pendaftar, tidak termasuk maba
		
		$conn->BeginTrans();
		
		if ($r_act == 'generate')
			list($err,$msg,$jml) = mTagihan::generateTagihan($conn,$a_filter,$r_periode,$a_jenis);
		else if ($r_act == 'void')
			list($err,$msg,$jml) = mTagihan::voidTagihan($conn,$a_filter,$r_periode,$a_jenis);
		
		// buat log
		if(!$err) {
			$record = array();
			$record['periodetagihan'] = $r_periode;
			$record['bulantahun'] = date('Ym');
			$record['kodeunit'] = CStr::cStrNull($r_key);
			$record['jml'] = (int)$jml;
			$record['isgen'] = ($r_act == 'void' ? 'V' : 'G');
			$record['ismahasiswa'] = 0;
			
			$err = mLoggenerate::insertRecord($conn,$record);
		}
		
		$ok = Query::isOK($err);
		$conn->CommitTrans($ok);
		
		$p_posterr = $err;
		$p_postmsg = $msg.' ('.$jml.' data mahasiswa)';
	}
	
	// combo
	$l_periode = uCombo::periode($conn,$r_periode,'periode','onchange="goSubmit()"',true);
	$l_sistem = uCombo::sistemkuliah($conn,$r_sistem,'sistemkuliah','onchange="goSubmit()"',true);
	$l_jalur = uCombo::jalur($conn,$r_jalur,'jalurpenerimaan','onchange="goSubmit()"',true);
	$l_gelombang = uCombo::gelombang($conn,$r_gelombang,'gelombang','onchange="goSubmit()"',true);
	
	// daftar jurusan
	$arr_unit = mAkademik::getArrayunit($conn,false,'2');
	
	// filter data
	$a_filter = array();
	$a_filter['sistemkuliah'] = $r_sistem;
	$a_filter['jalurpenerimaan'] = $r_jalur;
	$a_filter['gelombang'] = $r_gelombang;
	$a_filter['ispendaftar'] = true; // hanya pendaftar, tidak termasuk maba
	
	// ambil tagihan
	$data = mTagihan::getListTagihanGenerate($conn,$a_filter,$r_periode,$a_jenis);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Sistem Kuliah', 'combo' => $l_sistem);
	$a_filtercombo[] = array('label' => 'Jalur Penerimaan', 'combo' => $l_jalur);
	$a_filtercombo[] = array('label' => 'Gelombang', 'combo' => $l_gelombang);
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
					
					/************************/
					/* COMBO FILTER HALAMAN */
					/************************/
					
					if(!empty($a_filtercombo)) {
				?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>">
						<table width="<?= $p_tbwidth ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<?	foreach($a_filtercombo as $t_filter) { ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>		
										</tr>
										<?	}
											if($c_edit and !empty($r_periode) and !empty($r_sistem) and !empty($r_jalur) and !empty($r_gelombang)) { ?>
										<tr>
											<td colspan="2">
												<input type="button" value="Generate Tagihan" onclick="goGenerate()">
												<input type="button" value="Hapus Tagihan" onclick="goVoid()">
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
				<?	}
					if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	}
					if(!empty($r_periode) and !empty($r_sistem) and !empty($r_jalur) and !empty($r_gelombang)) { ?>
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
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
						<th rowspan="2">Jurusan</th>
						<? foreach($a_jenis as $k => $v) { ?>
						<th colspan="2"><?= $v ?></th>
						<? } ?>
						<th rowspan="2" style="width:60px">Aksi</th>
					</tr>
					<tr>
						<? foreach($a_jenis as $k => $v) { ?>
						<th style="width:80px">Tagihan</th>
						<th style="width:80px">Potongan</th>
						<? } ?>
					</tr>
                    <?	/********/
						/* ITEM */
						/********/
						if($arr_unit)
							$i = 0;
							foreach($arr_unit as $kodeunit => $namaunit) {
								if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
                        <td><?=$namaunit?></td>
						<? foreach($a_jenis as $k => $v) { ?>
						<td align="right"><?= CStr::formatNumber($data[$kodeunit][$k]['tagihan']) ?></td>
						<td align="right"><?= CStr::formatNumber($data[$kodeunit][$k]['potongan']) ?></td>
						<? } ?>
						<td align="center">
							<img id="<?= $kodeunit ?>" title="Generate Tagihan" src="images/disk.png" onClick="goGenerate(this)" style="cursor:pointer">
							<img id="<?= $kodeunit ?>" title="Hapus Tagihan" src="images/delete.png" onClick="goVoid(this)" style="cursor:pointer">
						</td>
					</tr>
					<?		} ?>
				</table>
				<?	} ?>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var insertreq = "<?= @implode(',',$a_insertreq) ?>";
var updatereq = "<?= @implode(',',$a_updatereq) ?>";
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	// handle scrolltop
	$(window).scrollTop($("#scroll").val());
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

<? if($c_edit) { ?>

function goGenerate(elem) {
	var key;
	if(typeof(elem) == "undefined")
		key = "";
	else
		key = elem.id;
	
	var txt = confirm("Apakah anda yakin akan melakukan Generate Tagihan?");
	if(txt) {
		document.getElementById("act").value = "generate";
		document.getElementById("key").value = key;
		goSubmit();
	}
}

function goVoid(elem) {
	var key;
	if(typeof(elem) == "undefined")
		key = "";
	else
		key = elem.id;
	
	var txt = confirm("Apakah anda yakin akan melakukan Hapus Tagihan?");
	if(txt) {
		document.getElementById("act").value = "void";
		document.getElementById("key").value = key;
		goSubmit();
	}
}

<? } ?>

</script>
</body>
</html>