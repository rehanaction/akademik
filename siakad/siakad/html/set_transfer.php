<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('transfermhs'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getUIPath('form'));

	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_angkatan = Modul::setRequest($_POST['angkatan'],'ANGKATAN');
	/* $r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN'); */
	
	$r_unitbaru = Modul::setRequest($_POST['unitbaru'],'UNITBARU');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false,true);
	$l_angkatan = uCombo::angkatan($conn,$r_angkatan,'angkatan','onchange="goSubmit()"',false);
	/* $l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false); */

	$l_tujuan = uCombo::jurusan($conn,$r_unitbaru,'','unitbaru','',false,true);
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Transfer Mahasiswa Antar Prodi';
	$p_tbwidth = 800;
	$p_aktivitas = 'BIODATA';
	
	$p_model = mTransferMhs;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nim', 'label' => 'NIM');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'sex', 'label' => 'L/P');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Prodi');
	$a_kolom[] = array('kolom' => 'semestermhs', 'label' => 'Sem.');
	$a_kolom[] = array('kolom' => 'nipdosenwali', 'label' => 'Dosen Wali');
	
	
	
	

	
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'transfer' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		$r_newkey = CStr::removeSpecial($_POST['newnim']);
		
		list($p_posterr,$p_postmsg) = $p_model::transfer($conn,$r_key,$r_unitbaru,$r_newkey);
	}
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_angkatan)) $a_filter[] = $p_model::getListFilter('angkatan',$r_angkatan);
	// if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);

	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	print_r('e');
	die();
	
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Prodi', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Angkatan', 'combo' => $l_angkatan);
	// $a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>

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
				<?	if($c_edit) {	 ?>
				<center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td colspan="3" align="center">Transfer Mahasiswa</td>
					</tr>
					<tr valign="top" class="NoHover">
						<td width="60" style="border:none"><strong>Tujuan</strong></td>
						<td width="340" style="border:none"><strong>:</strong> <?= $l_tujuan ?></td>
						<td rowspan="2">
							Masukkan <strong>Tujuan</strong> dan <strong>NIM Baru</strong>, lalu (pilih salah satu):<br>
							<ul style="margin:0 auto;padding:0 15px">
								<li>Masukkan <strong>NIM Lama</strong> dan klik tombol Transfer</li>
								<li>Klik tombol pada kolom <strong>Aksi</strong> di bawah</li>
							</ul>
						</td>
					</tr>
					<tr>
						<td style="border:none"><strong>NIM Baru</strong></td>
						<td style="border:none">
							<strong>:</strong> <?= UI::createTextBox('newnim','','ControlStyle',10,10) ?> &nbsp;
							<strong>NIM Lama &nbsp; :</strong>  <?= UI::createTextBox('oldnim','','ControlStyle',10,10) ?> &nbsp;
							<input type="button" value="Transfer" class="ControlStyle" onclick="goTransferNIM()">
						</td>
					</tr>
				</table>
				</center>
				<br>
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
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?	foreach($rowc as $j => $rowcc) {
								$t_align = $a_kolom[$j]['align'];
								if(!empty($t_width))
									$t_align = ' align="'.$t_align.'"';
						?>
						<td<?= $t_align ?>><?= $rowcc ?></td>
						<?	}
							if($c_edit) { ?>
						<td align="center">
							<img id="<?= $t_key ?>" title="Transfer Mahasiswa" src="images/out.png" onclick="goTransfer(this.id)" style="cursor:pointer">
						</td>
						<?	} ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>

				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	initEdit(<?= empty($post) ? false : true ?>);
	initTab();
	
	loadJurusan();
	loadKota();
	loadKotaPT();
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function ubahTipe() {
	document.getElementById("act").value = "ubahtipe";
	goSubmit();
}

function goTransferEx() {
	document.getElementById("act").value = "transferex";
	goSubmit();
}
//transfer antar jurusan
function goTransferNIM() {
	if(cfHighlight("oldnim"))
		goTransfer(document.getElementById("oldnim").value);
}

function goTransfer(from) {
	if(cfHighlight("newnim")) {
		var transfer = confirm("Apakah anda yakin akan mentransfer '"+from+"' ke '"+document.getElementById("newnim").value+"'?");
		
		if(transfer) {
			document.getElementById("act").value = "transfer";
			document.getElementById("key").value = from;
			goSubmit();
		}
	}
}

//transfer dari luar
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";



// ajax ganti fakultas
function loadJurusan() {
	var param = new Array();
	param[0] = $("#kodefakultas").val();
	param[1] = "<?= $r_kodeunit ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optjurusan", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodeunit").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

// ajax ganti jurusan
function loadBidangStudi() {
	var param = new Array();
	param[0] = $("#kodeunit").val();
	param[1] = "<?= $r_kodebs ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optbidangstudi", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodebs").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

// ajax ganti kota
function loadKota() {
	var param = new Array();
	param[0] = $("#kodepropinsi").val();
	param[1] = "<?= $r_kodekota ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekota").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}




// ajax ganti kota
function loadKotaPT() {
	var param = new Array();
	param[0] = $("#kodepropinsipt").val();
	param[1] = "<?= $r_kodekotapt ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotapt").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

// pilih mahasiswa
function goPick() {
	var temp;
	
	temp = document.getElementById("key").value;
	document.getElementById("key").value = document.getElementById("nimpilih").value;
	document.getElementById("nimpilih").value = temp;
	
	goSubmit();
}
</script>
</body>
</html>
