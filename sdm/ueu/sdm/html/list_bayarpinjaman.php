<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pinjaman'));
	require_once(Route::getUIPath('combo'));
		
	$p_model = mPinjaman;
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_jnspinjaman = Modul::setRequest($_POST['jnspinjaman'],'JENISPINJAMAN');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUNPINJAMAN');
	if(empty($r_tahun))
		$r_tahun = $p_model::getLastTahun($conn);
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:300px"',false);
	$a_tahun = $p_model::getTahun($conn);
	$l_tahun = UI::createSelect('tahun',$a_tahun,$r_tahun,'ControlStyle',true,'onchange="goSubmit()"');
	$a_jnspinjaman = $p_model::getJnsPinjaman($conn);
	$l_jnspinjaman = UI::createSelect('jnspinjaman',$a_jnspinjaman,$r_jnspinjaman,'ControlStyle',true,'onchange="goSubmit()"');
		
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'tglperjanjian', 'label' => 'Tgl. Perjanjian', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'nobuktidicairkan', 'label' => 'No. Bukti Transaksi');
	$a_kolom[] = array('kolom' => 'jnspinjaman', 'label' => 'Jenis Pinjaman');
	$a_kolom[] = array('kolom' => 'nik', 'label' => 'NIP');
	$a_kolom[] = array('kolom' => 'namalengkap', 'label' => 'Nama Pegawai','filter'=>"sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang)");
	$a_kolom[] = array('kolom' => 'totalpinjaman', 'label' => 'Jumlah Pinjaman', 'type' => 'N');
	$a_kolom[] = array('kolom' => 'angsuran', 'label' => 'Angsuran Pinjaman', 'type' => 'N');
	$a_kolom[] = array('kolom' => 'saldo', 'label' => 'Saldo Pinjaman', 'type' => 'N');
	
	// properti halaman
	$p_title = 'Daftar Pembayaran Pinjaman';
	$p_tbwidth = 1000;
	$p_aktivitas = 'ANGGARAN';
	$p_detailpage = Route::getDetailPage();
	$p_dbtable = "pe_pinjaman";
	$p_key = "idpinjaman";
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	if (empty($r_sort))
		$r_sort = 'tglperjanjian';
		
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_jnspinjaman)) $a_filter[] = $p_model::getListFilter('jnspinjaman',$r_jnspinjaman);
		
	$sql = $p_model::listQueryPerjanjian($r_tahun);
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Tahun', 'combo' => $l_tahun);
	$a_filtercombo[] = array('label' => 'Jenis Pinjaman', 'combo' => $l_jnspinjaman);
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
	<div id="wrapper" style="width:<?= $p_tbwidth+50 ?>px">
		<div class="SideItem" id="SideItem" style="width:<?= $p_tbwidth+10 ?>px">
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
						<th width="50">Lunas</th>
						<th width="50">Aksi</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						if (count($a_data) > 0){
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row,'idpinjaman');
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center"><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td align="center"><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td align="right"><?= $rowc[$j++] ?></td>
						<td align="right"><?= $rowc[$j++] ?></td>
						<td align="right"><?= $rowc[$j++] ?></td>
						<td align="center"><?= $row['islunas'] == 'Y' ? '<img src="images/check.png">' : ''; ?></td>
						<td align="center">
							<img id="<?= $t_key; ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
							<img id="<?= $t_key ?>" title="Pembayaran Manual" src="images/link.png" onclick="goPop('popMenu',this,event)" style="cursor:pointer">
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
        <td onClick="showPage('key','<?= Route::navAddress('list_bayarpinjmanual') ?>')">Pembayaran Manual</td>
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