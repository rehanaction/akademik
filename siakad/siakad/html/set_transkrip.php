<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = false; //$a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('transkrip'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('skalanilai'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	if(Akademik::isMhs())
		$r_key = Modul::getUserName();
	else
		$r_key = CStr::removeSpecial($_REQUEST['npm']);
	
	// redirect
	if(empty($r_key))
		Route::navigate('list_mahasiswa');
	
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	$a_skalanilai = mSkalaNilai::getDataKurikulum($conn,$a_infomhs['kurikulum']);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$r_keytrans = CStr::removeSpecial($_POST['key']);
		
		$record = array();
		$record['nangka'] = CStr::cStrNull($_POST['nhuruf']);
		
		list(,$r_nilaihuruf) = explode(': ',$a_skalanilai[$record['nangka']]);
		$record['nhuruf'] = CStr::cStrNull($r_nilaihuruf);
		
		list($p_posterr,$p_postmsg) = mTranskrip::updateRecord($conn,$record,$r_keytrans,true);
	}
	else if($r_act == 'convert' and $c_edit) {
		$r_keytrans = CStr::removeSpecial($_POST['key']);
		$r_newkey = CStr::removeSpecial($_POST['padanan']);
		
		list($t_thnkurikulum,$t_kodemk,$t_kodeunit) = explode('|',$r_newkey);
		
		$conn->BeginTrans();
		
		// ambil data dulu
		$row = mTranskrip::getData($conn,$r_keytrans);
		
		$err = mTranskrip::delete($conn,$r_keytrans,false);
		if(!$err) {
			$record = $row;
			$record['thnkurikulum'] = $t_thnkurikulum;
			$record['kodemk'] = $t_kodemk;
			$record['kodeunit'] = $t_kodeunit;
			
			unset($record['t_updateact']);
			
			$err = mTranskrip::insertRecord($conn,$record);
		}
		
		$p_posterr = Query::boolErr($err);
		$p_postmsg = 'Pengubahan data transksip '.($err ? 'gagal' : 'berhasil');
		
		$ok = Query::isOK($err);
		$conn->CommitTrans($ok);
	}
	
	// properti halaman
	$p_title = 'Data Nilai Mahasiswa';
	$p_tbwidth = 700;
	$p_aktivitas = 'NILAI';
	$p_headermhs = true;
	
	$p_model = mTranskrip;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label' => 'No.');
	$a_kolom[] = array('kolom' => 'kodemk', 'label' => 'Kode MK');
	$a_kolom[] = array('kolom' => 'namamk', 'label' => 'Nama MK');
	$a_kolom[] = array('kolom' => 'sks', 'label' => 'SKS');
	$a_kolom[] = array('kolom' => 'nangka', 'label' => 'Nilai Angka');
	$a_kolom[] = array('kolom' => 'nhuruf', 'label' => 'Nilai Huruf', 'type' => 'S', 'option' => $a_skalanilai);
	
	$p_colnum = count($a_kolom)+1;
	
	// mendapatkan data
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort);
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
			<div style="float:left; width:18%">
				<?php require_once('inc_headermahasiswa.php'); ?>
			</div>
			<div style="float:left; width:50%">
			<form name="pageform" id="pageform" method="post">
				<center>
				<?php require_once('inc_headermhs_krs.php') ?>
				 
				<br>
				<?	if(!empty($p_postmsg)) { ?>
				 
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				
				<div class="Break"></div>
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
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
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
						<?	list($t_sort) = explode(',',$r_sort);
							trim($t_sort);
							list($t_col,$t_dir) = explode(' ',$t_sort);
							
							foreach($a_kolom as $datakolom) {
								if($t_col == $datakolom['kolom'])
									$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
								else
									$t_sortimg = '';
								
								$t_width = $datakolom['width'];
								if(!empty($t_width))
									$t_width = ' width="'.$t_width.'"';
						?>
						<th id="<?= $datakolom['kolom'] ?>"<?= $t_width ?>><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	}
							if($c_edit) { ?>
						<th width="50">Aksi</th>
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						$t_tsks = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							
							$t_key = $p_model::getKeyRow($row);
							$t_kurikulum = $row['thnkurikulum'];
							
							$t_sks = $row['sks'];
							$t_tsks += $t_sks;
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td align="center"><?= $rowc[$j++] ?></td>
						<td align="center"><?= $rowc[$j++] ?></td>
						<td align="center"><?= $c_edit ? uForm::getInput($a_kolom[$j],$row['nangka']) : $row['real_nhuruf'] ?></td>
						<?	if($c_edit) { ?>
						<td>
							<?	if($t_kurikulum != $a_infomhs['kurikulum']) { ?>
							<img id="<?= $t_key ?>" title="Edit Mata Kuliah" src="images/edit.png" onclick="goShowConvert(this)" style="cursor:pointer; margin-right:9px">
							<?	} else echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;"; ?>
							<img id="<?= $t_key ?>" title="Simpan Nilai" src="images/disk.png" onclick="goSave(this)" style="cursor:pointer">
						</td>
						<?	} ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}
						else { ?>
					<tr>
						<th colspan="3">Jumlah SKS</th>
						<th><?= $t_tsks ?></th>
						<th colspan="3">&nbsp;</th>
					</tr>
					<?	} ?>
				</table>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="npm" id="npm" value="<?= $r_key ?>">
				
				<div id="div_dark" class="Darken" style="display:none"></div>
				<div id="div_light" class="Lighten" align="center" style="display:none">
				<div id="div_content" style="background-color:white;width:615px;height:300px;padding:0 11px 11px 11px;overflow:auto">
				<div id="div_loading" style="position:relative;z-index:3;top:170px">
					<img src="images/loading.gif">
				</div>
				<div id="div_table">
					<table border="0" cellspacing="10" class="nowidth">
						<tr>
							<td class="TDButton" onclick="goConvert()"><img src="images/disk.png"> Simpan</td>
							<td class="TDButton" onclick="goClose()"><img src="images/off.png"> Tutup</td>
						</tr>
					</table>
					<center>
						<header style="width:600px">
							<div class="inner">
								<div class="left title">
									<img id="img_workflow" width="24px" src="images/aktivitas/KULIAH.png" onerror="loadDefaultActImg(this)">
									<h1>Data Padanan Mata Kuliah</h1>
								</div>
							</div>
						</header>
						<table id="tab_padanan" width="600" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
							<tr id="tr_head">
								<th>&nbsp;</th>
								<th>Kurikulum</th>
								<th>Kode MK</th>
								<th>Nama Mata Kuliah</th>
								<th>SKS</th>
							</tr>
							<tr id="tr_empty">
								<td colspan="5" align="center">Padanan Mata Kuliah tidak ditemukan</td>
							</tr>
						</table>
					</center>
				</div>
				</center>
			</form>
			</div>
			</div>
			</div>
			
		</div>
	</div>
</div>

<script type="text/javascript">
	
var keytrans;
	
$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goSave(elem) {
	$("[name='nhuruf']").attr("disabled","disabled");
	$(elem).parents("tr:first").find("[name='nhuruf']").removeAttr("disabled");
	
	document.getElementById("act").value = "save";
	document.getElementById("key").value = elem.id;
	goSubmit();
}

function goConvert() {
	document.getElementById("act").value = "convert";
	document.getElementById("key").value = keytrans;
	goSubmit();
}

function goShowConvert(elem) {
	// atur posisi
	var padtop = $(window).height()/4;
	$("#div_light").css("padding-top",padtop);
	
	$("#div_table").css("visibility","hidden");
	$("#div_loading").show();
	
	$("#div_dark").show();
	$("#div_light").show();
	
	// mengambil padanan
	keytrans = elem.id;
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "trpadanan", q: keytrans }
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
	
	$("#tab_padanan tr:not(#tr_head,#tr_empty)").remove();
}

</script>
</body>
</html>
