<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('validasi'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','style="width:270px" onchange="goSubmit()"',false);
	
	// properti halaman
	$p_title = 'Daftar Riwayat Pendidikan';
	$p_tbwidth = 950;
	$p_aktivitas = 'DAFTAR';
	$p_key = 'nourutrpen';
	$p_dbtable = 'pe_rwtpendidikan';
	
	$p_model = mValidasi;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nik', 'label' => 'NIP', 'align' => 'center', 'filter' => 'p.nik');
	$a_kolom[] = array('kolom' => 'namalengkap', 'label' => 'Nama Pegawai', 'filter' => 'sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)');
	$a_kolom[] = array('kolom' => 'namapendidikan', 'label' => 'Jenjang');
	$a_kolom[] = array('kolom' => 'namainstitusipend', 'label' => 'Nama Institusi');
	$a_kolom[] = array('kolom' => 'tglijazah', 'label' => 'Tgl. Ijazah', 'type' => 'D', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'noijazah', 'label' => 'No. Ijazah');
	$a_kolom[] = array('kolom' => 'fileijazah', 'label' => 'File Ijazah');
	$a_kolom[] = array('kolom' => $p_key);
	
	$p_colnum = count($a_kolom);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		for($i=0;$i<count($_POST['kode']);$i++){
			$r_ida = CStr::cAlphaNum($_POST['kode'][$i]);
			$is = CStr::cAlphaNum($_POST['check'.$r_ida]);
			if(!empty($is)){
				$rec['isvalid'] = 'Y';
				list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$rec,$r_ida,false,$p_dbtable,$p_key);
			}
		}
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'nik,tglijazah desc';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	
	$sql = $p_model::listQueryValidasiPendidikan();
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	
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
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<input type="text" style="display:none">
				<?php 
				require_once('inc_listfilter.php'); 						
				if(!empty($p_postmsg)) { ?>
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
							<div class="right">
								<div class="TDButton" style="padding:7px 0px 7px;width:95px;position:relative;left:-5px;top:5px;" onClick="goSave()">
									<img src="images/disk.png" style="position:relative;left:-60px;top:-7px;">
									<span style="position:relative;left:15px">Simpan</span>
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
								if($datakolom['kolom'] == $p_key)
									continue;
						?>
						<th id="<?= $datakolom['kolom'] ?>"<?= $t_width ?>><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	}
							if($c_edit) { ?>
						<th width="50"><input type="checkbox" id="checkall" title="Cek semua daftar halaman <?= $r_page?>" onClick="toggle(this)"></th>
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
								if($a_kolom[$j]['kolom'] == 'fileijazah'){
									$rowcc = '<a href="javascript:goDownload(\''.Route::navAddress('download&_auto=1&_ocd=').base64_encode($a_kolom[$j]['kolom']).'\','.$row[$p_key].')" class="ULink">'.$row[$a_kolom[$j]['kolom']].'</a>';
								}
						?>
						<td<?= $t_align ?>><?= $rowcc; ?></td>
						<?}
							if($c_edit) { ?>
						<td align="center">
							<img id="<?= $row['idpegawai'] ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetailData(this)" style="cursor:pointer">
							<input type="checkbox" id="check" name="check<?= $row[$p_key] ?>" title="Cek untuk validasi per item">
							<input type="hidden" name="kode[]" id="kode[]" value="<?= $row[$p_key] ?>">
						</td>
						<?	} ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	
						}
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
				<input type="hidden" name="idx" id="idx" value="1">
				<input type="hidden" name="link" id="link" value="0">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>

$(document).ready(function() {	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function toggle(elem) {
	var check = elem.checked;
	
	$("[id='check']").attr("checked",check);
}

function goDetailData(elem) {
	var form = document.getElementById("pageform");
	
	document.getElementById("key").value = elem.id;
	form.action = "<?= Route::navAddress('data_pegawai') ?>";
	form.target = "_blank";
	
	goSubmit();
	
	form.action = "";
	form.target = "";
}

</script>
</body>
</html>
