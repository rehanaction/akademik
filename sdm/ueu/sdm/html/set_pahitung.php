<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pa'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:300px"',false);
		
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nip', 'label' => 'NIP');
	$a_kolom[] = array('kolom' => 'namalengkap', 'label' => 'Nama', 'filter' => 'sdm.f_namalengkap(gelardepan,namatengah,namadepan,namabelakang,gelarbelakang)');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Unit');
	$a_kolom[] = array('kolom' => 'jabatanstruktural', 'label' => 'Struktural');
	$a_kolom[] = array('kolom' => 'nilaiakhir', 'label' => 'Nilai', 'type' => 'N,2', 'width' => '100px', 'align' => 'right');
	
	// properti halaman
	$p_title = 'Perhitungan Akhir Penilaian Kinerja';
	$p_tbwidth = 900;
	$p_aktivitas = 'NILAI';
	$p_detailpage = 'rep_pahasil';//Route::getDetailPage();
	$p_dbtable = 'pa_nilaiakhir';
	$p_key = "idpegawai";
	
	$p_model = mPa;
	$p_colnum = count($a_kolom)+2;
	
	$r_periode = CStr::removeSpecial($_POST['periode']);
	if (empty($r_periode)) $r_periode = $p_model::getLastPeriode($conn);
	
	// ada aksi
	$r_act = CStr::removeSpecial($_POST['act']);
	if($r_act == 'hitung' and $c_edit) {
		$r_kode = $_POST['kode'];
		if (count($r_kode) > 0){
			$conn->StartTrans();
			foreach($r_kode as $idpegawai){
				$is = CStr::cAlphaNum($_POST['check'.$idpegawai]);
				if(!empty($is)){
					$p_model::hitungPA($conn, $r_periode, $idpegawai);
				}
			}
			$conn->CompleteTrans();
		}
		//list($p_posterr,$p_postmsg) = $p_model::saveFirstTim($conn, $r_periode);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if (empty($r_sort)) $r_sort = 'nip';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
		
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);	
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	
	$sql = $p_model::listQueryTim();
					
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	
	$a_blokir = $p_model::cekHitungNilaiPA($conn, $r_periode);
				
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit Kerja', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => UI::createSelect('periode',mPa::getCPeriode($conn), $r_periode, 'ControlStyle',$c_edit,'onChange="goSubmit()"'));
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
				<?	} ?>
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
							<?	if($c_edit) { ?>
							<? /*<div class="right">
								<div class="TDButton" style="padding:7px 0px 7px;width:100px;position:relative;left:-2px;top:5px;" onClick="goHitungAll()">
									<img src="images/calc.png" style="position:relative;left:-70px;top:-7px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hitung All
								</div>
							</div> */ ?>
							<div class="right">
								<div class="TDButton" style="padding:7px 0px 7px;width:120px;position:relative;left:-5px;top:5px;" onClick="goHitung()">
									<img src="images/calc.png" style="position:relative;left:-90px;top:-7px;">
									<span style="position:relative;left:15px">Hitung Centang</span>
								</div>
							</div>
							<? } ?>
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
						<?	} ?>
						<th>Selesai</th>
						<?	if($c_edit) { ?>
						<th width="30"><input type="checkbox" id="checkall" title="Cek semua daftar halaman <?= $r_page?>" onClick="toggle(this)"></th>
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row,$p_key);
							
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?	foreach($rowc as $j => $rowcc) {
								$t_align = $a_kolom[$j]['align'];
								if(!empty($t_align))
									$t_align = ' align="'.$t_align.'"';
								if($a_kolom[$j]['kolom'] == $p_key)
									continue;
						?>
						<td<?= $t_align ?>><?= $rowcc ?></td>
						<?	} ?>
						<td align="center">
							<?= ($row['idpegawai'] != $a_blokir[$row['idpegawai']]) ? '<img src="images/check.png" />' : '' ;?>
						</td>
						<?	if($c_edit) { ?>
						<td align="center">
							<? if ($row['idpegawai'] != $a_blokir[$row['idpegawai']]) { ?>
							<input type="checkbox" id="check" name="check<?= $row[$p_key] ?>" title="Cek untuk validasi per item">
							<input type="hidden" name="kode[]" id="kode[]" value="<?= $row[$p_key] ?>">
							<? } ?>
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
					
						/**********/
						/* FOOTER */
						/**********/
						
						if(!empty($r_page)) { ?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="right" class="FootBG">
						<div style="float:left">
							Record : <?= uCombo::listRowNum($r_row,'onchange="goLimit()"') ?>
						</div>
						<div style="float:right">
							Halaman <?= $r_page ?> / <?= Page::getTheLastPage();?>
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
var reportpage = "<?= Route::navAddress($p_detailpage) ?>";

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

function toggle(elem) {
	var check = elem.checked;
	
	$("[id='check']").attr("checked",check);
}

function goHitung(){
	var hit = confirm("Anda yakin untuk menghitung penilaian terpilih ?");
	if (hit){
		document.getElementById("act").value = "hitung";
		goSubmit();
	}
}

function goHitungAll(){
	document.getElementById("act").value = "hitungall";
	goSubmit();
}

</script>
</body>
</html>
