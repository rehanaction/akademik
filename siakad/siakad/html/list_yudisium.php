<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_ijazah = $c_edit;
	
	// include
	require_once(Route::getModelPath('yudisium'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periodewisuda = Modul::setRequest($_POST['periodewisuda'],'PERIODEWISUDA');
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_unitadd = Modul::setRequest($_POST['unitadd']);
	
	$r_periode = $r_tahun.$r_semester;
	
	// combo
	$l_periodewisuda = uCombo::periodeWisuda($conn,$r_periodewisuda,'periodewisuda','onchange="goSubmit()"',false);
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false,true);
	
	$l_semester = uCombo::semester($r_semester,true,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	$l_unitadd = uCombo::unit($conn,$r_unitadd,'unitadd','onchange="goSubmit()"',false,true);
	
	$a_mahasiswa = mCombo::mahasiswa($conn,$r_unitadd,$r_periode);
	$l_mahasiswa = UI::createSelect('npm',$a_mahasiswa,'','ControlStyle',true,'',true,'-- Semua Mahasiswa --');
	
	// properti halaman
	$p_title = 'Data Peserta Yudisium';
	$p_tbwidth = 850;
	$p_aktivitas = 'WISUDA';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mYudisium;
	
	// combo input
	$l_kodemk = UI::createSelect('kodemk',$a_kodemk,'','ControlStyle',true,'style="width:400px"');
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'y.nim', 'label' => 'NIM');
	$a_kolom[] = array('kolom' => 'm.kodeunit', 'label' => 'Prodi');
	$a_kolom[] = array('kolom' => 'm.nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'm.notranskrip', 'label' => 'SK Rektor');
	$a_kolom[] = array('kolom' => 'm.noijasah', 'label' => 'No Ijazah');
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'insertnim' and $c_insert) {
		$record = array();
		$record['idyudisium'] = $r_periodewisuda;
		$record['nim'] = CStr::cStrNull($_POST['nimbaru']);
		$record['nama'] = CStr::cStrNull(Akademik::getNamaMahasiswa($conn,$record['nim']));
		
		list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true);
	}
	if($r_act == 'insertpilih' and $c_insert) {
		$record = array();
		$record['idyudisium'] = $r_periodewisuda;
		
		$r_npm = $_POST['nim'];
		if(empty($r_npm)) {
			$ok = true;
			$conn->BeginTrans();
			
			foreach($a_mahasiswa as $t_npm => $t_label) {
				list($t_nama) = explode(' (',$t_label);
				
				$record['nim'] = $t_npm;
				$record['nama'] = $t_nama;
				
				list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true);
				
				if($p_posterr) {
					$ok = false;
					break;
				}
			}
			
			$conn->CommitTrans($ok);
		}
		else {
			$record['nim'] = CStr::cStrNull($r_npm);
			$record['nama'] = CStr::cStrNull(Akademik::getNamaMahasiswa($conn,$record['nim']));
			
			list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true);
		}
	}
	else if($r_act == 'setnoijazah' and $c_ijazah) {
		$r_noijazah = CStr::removeSpecial($_POST['noijazah']);
		$r_nofakultas = CStr::removeSpecial($_POST['nofakultas']);
		
		list($p_posterr,$p_postmsg) = $p_model::setNoIjazah($conn,$r_periodewisuda,$r_unit,$r_noijazah,$r_nofakultas);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_periodewisuda)) $a_filter[] = $p_model::getListFilter('periodewisuda',$r_periodewisuda);
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periodewisuda);
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
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
				<?	if($c_insert or $c_ijazah) { ?>
				<center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
					<?	if($c_insert) { ?>
						<td colspan="2" align="center">Tambah Peserta Yudisium</td>
					<?	}
						if($c_ijazah) { ?>
						<td align="center">Set No Ijazah</td>
					<?	} ?>
					</tr>
					<tr>
					<?	if($c_insert) { ?>
						<th>Tambah 1 Peserta</th>
						<th>Pilih dari Angkatan</th>
					<?	}
						if($c_ijazah) { ?>
						<th>Masukkan Nomor Mulai</th>
					<?	} ?>
					</tr>
					<tr valign="top" class="NoHover">
					<?	if($c_insert) { ?>
						<td>
							<strong>NIM: </strong><?= UI::createTextBox('nimbaru','','ControlStyle',10,10) ?>
							<div class="Break"></div>
							<input type="button" class="ControlStyle" value="Tambah Peserta" onclick="goAddNIM()">
						</td>
						<td>
							<table border="0" cellpadding="2" cellspacing="0" class="NoGrid">
								<tr class="NoHover">
									<td><strong>Periode Daftar</strong></td>
									<td><strong>:</strong></td>
									<td><?= $l_semester ?> <?= $l_tahun ?></td>
								</tr>
								<tr class="NoHover">
									<td><strong>Prodi</strong></td>
									<td><strong>:</strong></td>
									<td><?= $l_unitadd ?></td>
								</tr>
								<tr class="NoHover">
									<td><strong>Mahasiswa</strong></td>
									<td><strong>:</strong></td>
									<td><?= $l_mahasiswa ?></td>
								</tr>
							</table>
							<div class="Break"></div>
							<input type="button" class="ControlStyle" value="Tambah Peserta" onclick="goAddPilih()">
						</td>
					<?	}
						if($c_ijazah) { ?>
						<td>
							<table border="0" cellpadding="2" cellspacing="0" class="NoGrid">
								<tr class="NoHover">
									<td><strong>Institut</strong></td>
									<td><strong>:</strong></td>
									<td><?= UI::createTextBox('noijazah','','ControlStyle',8,8) ?></td>
								</tr>
								<tr class="NoHover">
									<td><strong>Fakultas</strong></td>
									<td><strong>:</strong></td>
									<td><?= UI::createTextBox('nofakultas','','ControlStyle',8,8) ?></td>
								</tr>
							</table>
							<div class="Break"></div>
							<input type="button" class="ControlStyle" value="Set No Ijazah" onclick="goSetNoIjazah()">
						</td>
					<?	} ?>
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
							<?	if(!empty($r_page)) { ?>
							<div class="right">
								<?php require_once('inc_listnavtop.php'); ?>
							</div>
							<?	} ?>
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
						<th width="30">Edit</th>
						<?	}
							if($c_delete) { ?>
						<th width="30">Hapus</th>
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
						<td align="center"><img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer"></td>
						<?	}
							if($c_delete) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
						<?	} ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}
					
						/**********/
						/* FOOTER */
						/**********/
						
						if(!empty($r_page)) { ?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="right" class="FootBG">
						<div style="float:left">
							Record : <?= uCombo::listRowNum($r_row,'onchange="goSubmit()"') ?>
						</div>
						<div style="float:right">
							Halaman <?= $r_page ?> / <?= $p_pagenum ?>
						</div>
						</td>
					</tr>
					<?	} ?>
				</table>
				<? if(!empty($r_page)) { ?>
				<?php require_once('inc_listnav.php'); ?>
				<? } ?>
				
				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goAddNIM() {
	document.getElementById("act").value = "insertnim";
	goSubmit();
}

function goAddPilih() {
	document.getElementById("act").value = "insertpilih";
	goSubmit();
}

function goSetNoIjazah() {
	document.getElementById("act").value = "setnoijazah";
	goSubmit();
}

</script>
</body>
</html>
