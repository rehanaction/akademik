<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('kurikulum'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_kurikulum = Modul::setRequest($_POST['kurikulum'],'KURIKULUM');
	
	// combo
	$l_kurikulum = uCombo::kurikulum($conn,$r_kurikulum,'kurikulum','onchange="goSubmit()"',false);
	
	// tambahan
	$r_periode = Akademik::getPeriode();
	$g_namaperiode = Akademik::getNamaPeriode();
	
	// properti halaman
	$p_title = 'Pengambilan Mata Kuliah Paket';
	$p_tbwidth = 700;
	$p_aktivitas = 'KULIAH';
	
	$p_model = mKurikulum;
	
	// ada aksi
	$r_act = $_POST['act'];
	if(!empty($r_act)) {
		$r_unit = CStr::removeSpecial($_POST['kodeunit']);
		$r_semmk = CStr::removeSpecial($_POST['semmk']);
		$r_angkatan = CStr::removeSpecial($_POST['angkatan']);
		
		if($r_act == 'ambilpaket' and $c_edit) {
			$r_aturan = CStr::removeSpecial($_POST['aturan']);
			// $r_jmlkelas = CStr::removeSpecial($_POST['jmlkelas']);
			
			list($p_posterr,$p_postmsg) = $p_model::setPaketAturan($conn,$r_kurikulum,$r_unit,$r_semmk,$r_angkatan,$r_aturan,$r_jmlkelas);
		}
		else if($r_act == 'setpaket' and $c_edit) {
			//die('kena');
			$r_kodejur = CStr::removeSpecial($_POST['kodejur']);
			$r_nim = CStr::removeSpecial($_POST['nim']);
			$r_lastnim = CStr::removeSpecial($_POST['lastnim']);
			$r_kelas =  CStr::removeSpecial($_POST['kls']);
			
			list($p_posterr,$p_postmsg) = $p_model::setPaket($conn,$r_kurikulum,$r_unit,$r_semmk,$r_angkatan,$r_kodejur,$r_nim,$r_lastnim,$r_kelas);
		}
	}
	
	// mendapatkan data
	$a_data = $p_model::getListUnitPaket($conn,$r_periode,$r_kurikulum);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $g_namaperiode);
	$a_filtercombo[] = array('label' => 'Kurikulum', 'combo' => $l_kurikulum);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?php require_once('inc_listfilter.php'); ?>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<div class="right" style="padding-top:8px;padding-right:10px">
								<div style="float:left;background-color:#FF0;width:20px;height:20px;border:1px solid #CCC"></div>
								<div style="float:left;color:#FFF"> &nbsp; Tidak semua MK ada kelasnya</div>
							</div>
						</div>
					</header>
				</center>
				<?	/*************/
					/* LIST DATA */
					/*************/
				?>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
						<th>Fakultas</th>
						<th>Prodi</th>
						<th>Smt</th>
						<th>Angkatan</th>
						<th>MK / Kelas</th>
						<th>KRS?</th>
						<th>Set</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $row['kodeunit'].'|'.$row['semmk'];
							
							// cek kelas
							if($row['jumlahkelas'] < $row['jumlahmk'])
								$t_bgtd = '#FF0';
							else
								$t_bgtd = '';
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $row['fakultas'] ?></td>
						<td><?= $row['namaunit'] ?></td>
						<td align="center"><?= $row['semmk'] ?></td>
						<td align="center"><?= $row['angkatan'] ?></td>
						<td align="center"<?= empty($t_bgtd) ? '' : ' bgcolor="'.$t_bgtd.'"' ?>><?= $row['jumlahmk'] ?> / <?= $row['jumlahkelas'] ?></td>
						<td align="center"><?= empty($row['adakrs']) ? '' : '<img src="images/check.png">' ?></td>
						<td align="center"><img id="<?= $t_key ?>" title="Set Paket" src="images/list.png" onclick="goShowPaket(this)" style="cursor:pointer"></td>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="7" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
					
				<div id="div_dark" class="Darken" style="display:none"></div>
				<div id="div_light" class="Lighten" align="center" style="display:none">
				<div id="div_content" style="background-color:white;width:665px;height:520px;padding:0 11px 11px 11px;overflow:auto">
				<div id="div_loading" style="position:relative;z-index:3;top:300px">
					<img src="images/loading.gif">
				</div>
				<div id="div_table">
					<table border="0" cellspacing="10" class="nowidth">
						<tr>
							<td class="TDButton" onclick="goPaket()"><img src="images/disk.png"> Simpan</td>
							<td class="TDButton" onclick="goClose()"><img src="images/off.png"> Tutup</td>
						</tr>
					</table>
					<center>
						<div class="filterTable" style="width:638px;">
							<table width="638" cellpadding="2" cellspacing="0" align="center">
								<tr>
									<td width="60"><strong>Fakultas</strong></td>
									<td width="20" align="center"><strong>:</strong></td>
									<td width="130"><span id="span_fakultas"></span></td>
									<td width="60"><strong>Prodi</strong></td>
									<td width="20" align="center"><strong>:</strong></td>
									<td><span id="span_namaunit"></span></td>
								</tr>
								<tr>
									<td><strong>Semester</strong></td>
									<td align="center"><strong>:</strong></td>
									<td><span id="span_semester"></span></td>
									<td><strong>Angkatan</strong></td>
									<td align="center"><strong>:</strong></td>
									<td><span id="span_angkatan"></span></td>
								</tr>
							</table>
						</div>
						<br>
						<div class="filterTable" style="width:638px;">
							<table width="638" cellpadding="2" cellspacing="0" align="center">
								<tr>
									<td>
										<input type="radio" name="pilih" value="1" id="pilih_1" checked>
										<!--label for="pilih_1"-->
											Distribusikan Mata Kuliah ke setiap Mahasiswa dengan aturan
											<input type="radio" name="aturan" id="aturan_urut" value="URUT" checked> <label for="aturan_urut">Urut NIM</label>
											<input type="radio" name="aturan" id="aturan_zz" value="ZIGZAG"> <label for="aturan_zz">Zig Zag</label>
										<!--/label-->
									</td>
								</tr>
								<tr>
									<td>
										<input type="radio" name="pilih" value="2" id="pilih_2">
										<!--label for="pilih_2"-->
											Atau mahasiswa dengan 4 digit awal NIM
											
											<?= UI::createTextBox('kodejur','','ControlStyle',4,4) ?>
											dan 3 digit akhir NIM
											<?= UI::createTextBox('nim','','ControlStyle',3,3) ?>
											s.d.
											<?= UI::createTextBox('lastnim','','ControlStyle',3,3) ?>
											di kelas
											<?= UI::createTextBox('kls','','ControlStyle',5,4) ?>
											
											
										<!--/label-->
									</td>
								</tr>
							</table>
						</div>
						<br>
						<header style="width:650px">
							<div class="inner">
								<div class="left title">
									<img id="img_workflow" width="24px" src="images/aktivitas/KULIAH.png" onerror="loadDefaultActImg(this)">
									<h1><?= $p_title ?></h1>
								</div>
							</div>
						</header>
						<table id="tab_paket" width="650" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
							<tr id="tr_head">
								<th>No</th>
								<th>Kode MK</th>
								<th>Nama Mata Kuliah</th>
								<th>SKS</th>
								<th>Kelas</th>
							</tr>
							<tr id="tr_empty">
								<td colspan="5" align="center">Mata Kuliah Paket tidak ditemukan</td>
							</tr>
						</table>
					</center>
				</div>
				
				<input type="hidden" name="kodeunit" id="kodeunit">
				<input type="hidden" name="semmk" id="semmk">
				<input type="hidden" name="angkatan" id="angkatan">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goShowPaket(elem) {
	// atur posisi
	var padtop = $(window).height()/4;
	$("#div_light").css("padding-top",padtop);
	
	$("#div_table").css("visibility","hidden");
	$("#div_loading").show();
	
	// set info
	var td = $(elem).parents("tr:eq(0)").children();
	
	$("#span_fakultas").html(td.eq(0).html());
	$("#span_namaunit").html(td.eq(1).html());
	$("#span_semester").html(td.eq(2).html());
	$("#span_angkatan").html(td.eq(3).html());
	
	var arrid = elem.id.split("|");
	
	$("#kodeunit").val(arrid[0]);
	$("#semmk").val(arrid[1]);
	$("#angkatan").val(td.eq(3).html());
	
	$("#div_dark").show();
	$("#div_light").show();
	
	// mengambil padanan
	keytrans = "<?= $r_periode ?>|<?= $r_kurikulum ?>|" + elem.id;
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "trpaket", q: keytrans }
				});
	
	jqxhr.done(function(data) {
		$("#div_loading").hide();
		$("#div_table").css("visibility","visible");
		
		if(data != "") {
			$("#tr_head").after(data);
			$("#tr_empty").hide();
		}
		else
			$("#tr_empty").show();
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
		goClose();
	});
}

function goClose() {
	$("#div_light").hide();
	$("#div_dark").hide();
	
	$("#tab_paket tr:not(#tr_head,#tr_empty)").remove();
}

function goPaket() {
	var pilih = $("[name='pilih']:checked").val();
	
	if(pilih == "2")
		document.getElementById("act").value = "setpaket";
	else
		document.getElementById("act").value = "ambilpaket";
		
	goSubmit();
}

</script>
</body>
</html>
