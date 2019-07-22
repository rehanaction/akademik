<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_other = $a_auth['canother'];	
	$c_validasi = $c_other['V'];
	
	// include
	require_once(Route::getModelPath('rekrutmen'));
	require_once(Route::getUIPath('combo'));

	$p_model = mRekrutmen;
	
	// variabel request
	$r_posisi = Modul::setRequest($_POST['kodeposisi'],'POSISI');
	$r_jenisrekrutmen = Modul::setRequest($_POST['jenisrekrutmen'],'JENISREKRUTMEN');
	$r_jenisrekrutmen = $r_jenisrekrutmen == '' ? 'B' : $r_jenisrekrutmen;
	
	// combo
	$a_posisi1 = array('all' => '-- Semua Posisi --');
	$a_posisi2 = $p_model::getPosisi($conn);
	$a_posisi = $a_posisi1 + $a_posisi2;
	$l_posisi = UI::createSelect('kodeposisi',$a_posisi,$r_posisi,'ControlStyle',true,'onchange="goSubmit()"');
	
	$a_jenisrekrutmen = $p_model::jenisRekrutmen();
	$l_jenisrekrutmen = UI::createSelect('jenisrekrutmen',$a_jenisrekrutmen,$r_jenisrekrutmen,'ControlStyle',true,'onchange="goSubmit()"');
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'tglrekrutmen', 'label' => 'Tgl Permintaan', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'tglterakhir', 'label' => 'Tgl Penutupan', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'namaposisi', 'label' => 'Posisi', 'width' => '125px');
	
	// properti halaman
	$p_title = 'Daftar Permintaan Pegawai';
	$p_aktivitas = 'STRUKTUR';
	$p_detailpage = Route::getDetailPage();	
	$p_colnum = count($a_kolom)+8;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$conn->BeginTrans();

		$r_key = CStr::removeSpecial($_POST['key']);
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'re_prosesseleksi','idrekrutmen');
		
		if(empty($p_posterr))
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'re_mekanisme','idrekrutmen');
		
		if(empty($p_posterr))
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'re_unit','idrekrutmen');
		
		if(empty($p_posterr))
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'re_jurusan','idrekrutmen');
		
		if(empty($p_posterr))
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);

		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
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
	if(!empty($r_posisi)) $a_filter[] = $p_model::getListFilter('kodeposisi',$r_posisi);
	if(!empty($r_jenisrekrutmen)) $a_filter[] = $p_model::getListFilter('jenisrekrutmen',$r_jenisrekrutmen);
		
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	$a_sumkandidat = $p_model::sumKandidat($conn);
	$a_pelamar = $p_model::sumPelamar($conn);

	$a_sumkandidatint = $p_model::sumKandidatInt($conn);

	$a_unit = $p_model::getUnitRek($conn);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Jenis Rekrutmen', 'combo' => $l_jenisrekrutmen);
	$a_filtercombo[] = array('label' => 'Posisi', 'combo' => $l_posisi);
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
		.labelopen {
			background-color: #00CC00;
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
		.labelclose {
			background-color: #660000;
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
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<?	if($c_insert) { ?>
							<div class="right">
								<div class="addButton" onClick="goNew()">+</div>
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
						<th width="250">Unit yang Membutuhkan</th>
						<th width="50">Formasi</th>
						<th width="50">Pelamar</th>
						<th width="50">Kandidat</th>
						<th width="50">Valid</th>
						<th width="50">Status</th>
						<th width="50" align="center">Link</th>
						<th width="50">Aksi</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						if (count($a_data) > 0){
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row,'idrekrutmen');
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center"><?= $rowc[$j++] ?></td>
						<td align="center"><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td>
							<?
								if(count($a_unit[$row['idrekrutmen']])>0){
									foreach ($a_unit[$row['idrekrutmen']] as $idunit => $namaunit) {
										echo $namaunit.'<br>';
									}
								}
							?>
						</td>
						<td align="center"><?= $row['jmldibutuhkan'] ?></td>
						<td align="center"><?= $row['jenisrekrutmen'] == 'B' ? $a_pelamar[$row['idrekrutmen']] : ''; ?></td>
						<td align="center"><?= $row['jenisrekrutmen'] == 'B' ? $a_sumkandidat[$row['idrekrutmen']] : $a_sumkandidatint[$row['idrekrutmen']]; ?></td>
						<td align="center"><?= $row['isvalid'] == 'Y' ? '<img src="images/check.png">' : '' ?></td>
						<td align="center"><?= $row['isclose'] == 'Y' ? '<div class="labelclose">Close</div>' : '<div class="labelopen">Open</div>' ?></td>
						<td align="center">
						<? if ($row['jenisrekrutmen'] == 'B') { ?>
							<img id="<?= $t_key ?>" title="Halaman Pelamar" src="images/link.png" onclick="goPop('popMenuBaru',this,event)" style="cursor:pointer">
						<? }else { ?>
							<img id="<?= $t_key ?>" title="Halaman Pelamar" src="images/link.png" onclick="goPop('popMenu',this,event)" style="cursor:pointer">
						<? } ?>
						</td>
						<td align="center">
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
						<?		
								if(($c_delete and $c_validasi) or ($c_delete and ($row['isvalid'] != 'Y' and !$c_validasi))) { ?>
							<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer">
						<?		} ?>
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


<div id="popMenu" class="menubar" style="position:absolute; display:none; top:0px; left:0px;z-index:10000;" onMouseOver="javascript:overpopupmenu=true" onMouseOut="javascript:overpopupmenu=false">
<table width="130" class="menu-body">
    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('key','<?= Route::navAddress('list_rekandidat') ?>')">Proses Kandidat</td>
    </tr>
    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('key','<?= Route::navAddress('list_prosesseleksiint') ?>')">Proses Seleksi</td>
    </tr>
</table>
</div>

<div id="popMenuBaru" class="menubar" style="position:absolute; display:none; top:0px; left:0px;z-index:10000;" onMouseOver="javascript:overpopupmenu=true" onMouseOut="javascript:overpopupmenu=false">
<table width="130" class="menu-body">
    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('key','<?= Route::navAddress('list_rekandidat') ?>')">Proses Kandidat</td>
    </tr>
    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('key','<?= Route::navAddress('list_prosesseleksi') ?>')">Proses Seleksi</td>
    </tr>
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('key','<?= Route::navAddress('list_hasilseleksi') ?>')">Hasil Seleksi</td>
    </tr>
</table>
</div>


<script type="text/javascript" src="scripts/jquery.balloon.min.js"></script>
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
	
	// handle contact
	$("[id='imgcontact']").balloon();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

</script>
</body>
</html>
