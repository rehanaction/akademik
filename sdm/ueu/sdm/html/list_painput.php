<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// variabel request		
	if(SDM::isPegawai())
		$r_self = 1;
			
	// include
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getModelPath('pa'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_periode = CStr::removeSpecial($_POST['periode']);
	$r_status = CStr::removeSpecial($_POST['status']);
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:300px"',false);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nik', 'label' => 'NPP');
	$a_kolom[] = array('kolom' => 'namadinilai', 'label' => 'Nama Pegawai Dinilai', 'filter' => 'sdm.f_namalengkap(gelardepan,namatengah,namadepan,namabelakang,gelarbelakang)');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Unit Kerja');
	$a_kolom[] = array('kolom' => 'pendidikan', 'label' => 'Pendidikan');
	$a_kolom[] = array('kolom' => 'jabatanstruktural', 'label' => 'Struktural');
	
	// properti halaman
	$p_title = 'Daftar Pegawai yang dinilai';
	$p_tbwidth = 900;
	$p_aktivitas = 'NILAI';
	$p_detailpage = Route::getDetailPage();
	$p_dbtable = 'pa_hasilpenilaian';
	$p_key = 'kodeperiodepa,idpenilai,idpegawai';
	
	$p_model = mPa;
	$p_colnum = count($a_kolom)+2;
	
	if ($r_self){
		$r_idpenilai = Modul::getIDPegawai();
		$r_penilai = $p_model::getNamaPenilai($conn, $r_idpenilai);
	}else{
		$r_idpenilai = CStr::removeSpecial($_POST['idpenilai']);
		$r_penilai = CStr::removeSpecial($_POST['penilai']);
	}
	if (empty($r_periode)) $r_periode = $p_model::getLastPeriode($conn);
	
	// ada aksi
	$r_act = CStr::removeSpecial($_POST['act']);
	if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if (empty($r_sort)) $r_sort = 'nik';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
		
	// mendapatkan data
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periodetim',$r_periode);
	if(!empty($r_status)) $a_filter[] = $p_model::getListFilter('status',$r_status);
	if (empty($r_idpenilai)){
		if (!empty($_SESSION[SITE_ID]['PA_IDPENILAI'])){
			$r_idpenilai = $_SESSION[SITE_ID]['PA_IDPENILAI'];
			$r_penilai = $_SESSION[SITE_ID]['PA_PENILAI'];
		}
	}
	if(!empty($r_idpenilai)){ 
		$a_filter[] = $p_model::getListFilter('penilai',$r_idpenilai);
		$_SESSION[SITE_ID]['PA_IDPENILAI'] = $r_idpenilai;
		$_SESSION[SITE_ID]['PA_PENILAI'] = $r_penilai;
	}
	
	$sql = $p_model::listQueryTimPenilai();
	
	if (!empty($r_idpenilai)){
		$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	}
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode Penilaian', 'combo' => UI::createSelect('periode',mPa::getCPeriode($conn), $r_periode, 'ControlStyle',$c_edit,'onChange="goSubmit()"'));
	$a_filtercombo[] = array('label' => 'Status', 'combo' => UI::createSelect('status',mPa::getCStatus(), $r_status, 'ControlStyle',$c_edit,'onChange="goSubmit()"'));
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
	<style>
		#labelstatus{
			border-radius: 3px 3px 3px 3px;
			color: #FFFFFF;
			display: block;
			float: left;
			font-size: 11.05px;
			font-weight: bold;
			margin: 0 2px 2px 0;
			padding: 2px 4px 3px;
			text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
		}			
		.statusY {	
			background-color: #00CC00;
		}
		.statusT {	
			background-color: #FF0000;
		}
	</style>
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
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table cellpadding="5" cellspacing="0" width="<?= $p_tbwidth-12 ?>px";>
							<tr>	
								<td><strong>Penilai</strong></td>
								<td>: <?= ($r_self) ? $r_penilai : UI::createTextBox('penilai', $r_penilai,'ControlStyle',60,45,$c_edit); ?>
								<input type="hidden" name="idpenilai" id="idpenilai" value="<?= $r_idpenilai; ?>" />
								<? if (!$r_self) {?>
								<img id="imgnik_c" src="images/green.gif"><img id="imgnik_u" src="images/red.gif" style="display:none">&nbsp;&nbsp;
								<input type="button" name="bcari" id="bcari" value="Tampilkan" class="ControlStyle" onClick="goSubmit()" />
								<? } ?>
								</td>
							</tr>
						</table>
					</div>
					<br />
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
						<?	} ?>
						<th width="70">Status</th>
						<th width="50">Aksi</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						if (count($a_data) > 0){
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row,$p_key);
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
							
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td align="center"><div id="labelstatus" class="<?= $row['isselesai'] == 'Y' ? 'statusY' : 'statusT' ?>"><?= $row['isselesai'] == 'Y' ? 'Selesai' : 'Belum Selesai' ?></td>
						<td align="center">
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
						</td>
					</tr>
					<?	}}
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

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
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
	
	<? if (!$r_self) {?>
	// handle contact
	$("input[name='penilai']").xautox({strpost: "f=acnamapenilai", targetid: "idpenilai", imgchkid: "imgnik", imgavail: true});
	<? } ?>
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

</script>
</body>
</html>