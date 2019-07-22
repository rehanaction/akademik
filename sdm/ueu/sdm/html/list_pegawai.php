<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getModelPath('integrasi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$p_model = mPegawai;
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_tipepeg = Modul::setRequest($_POST['tipepegawai'],'TIPEPEGAWAI');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:300px"',false);
	$a_tipepeg = $p_model::getCTipePegawaiBaru($conn);
	$l_tipepeg = UI::createSelect('tipepegawai',$a_tipepeg,$r_tipepeg,'ControlStyle',true,'onchange="goSubmit()"');
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'namalengkap', 'label' => 'Nama', 'filter' => 'sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang)');
	$a_kolom[] = array('kolom' => 'nip', 'label' => 'NIP');
	$a_kolom[] = array('kolom' => 'nodosen', 'label' => 'No. Dosen');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Unit');
	$a_kolom[] = array('kolom' => 'tipepeg', 'label' => 'Tipe Pegawai');
	$a_kolom[] = array('kolom' => 'alamat','label' =>'Contact');
	$a_kolom[] = array('kolom' => 'namastatusaktif', 'label' => 'Status');
	
	// properti halaman
	$p_title = 'Daftar Pegawai';
	$p_tbwidth = 1000;
	$p_aktivitas = 'UNIT';
	$p_detailpage = Route::getDetailPage();
	$p_dbtable = 'ms_pegawai';
	$where = 'idpegawai';
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$conn->BeginTrans();
		$r_key = CStr::removeSpecial($_POST['key']);
		
		//hapus integrasi di akademik dulu
		//$p_posterr = mIntegrasi::deleteIntegrasi($connsia,$r_key);
		//if($p_posterr)
		//	$p_postmsg = 'Penghapusan User ke Akademik gagal';
		
		//hapus integrasi di gate dulu
		$p_posterr = mIntegrasi::deleteRoleGate($conn,$r_key);
		if($p_posterr)
			$p_postmsg = 'Penghapusan User Role ke Gate gagal';
		
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'ga_ajardosen',$where);
		
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$where);
		
		if(!$p_posterr){
			$dirfoto = 'fotopeg';
			$p_foto = uForm::getPathImageFoto($conn,$r_key,$dirfoto);
			@unlink($p_foto);
			
			$ok = Query::isOK($p_posterr);
			$conn->CommitTrans($ok);
		}else
			$conn->RollbackTrans();
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'idstatusaktif,namalengkap';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
		
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_tipepeg)) $a_filter[] = $p_model::getListFilter('idtipepeg',$r_tipepeg);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,'',$p_dbtable);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit Kerja', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Tipe Pegawai', 'combo' => $l_tipepeg);
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
	<div id="wrapper" style="width:<?= $p_tbwidth+50 ?>px;">
		<div class="SideItem" id="SideItem" style="width:<?= $p_tbwidth+15 ?>px;">
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
							<?	if($c_insert and Modul::getRole() != 'admpeg') { ?>
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
						<th width="50">Aksi</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						if (count($a_data) > 0){
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row,'idpegawai');
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $rowc[$j++] ?></td>
						<td align="center"><?= $rowc[$j++] ?></td>
						<td align="center"><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td align="center"><?= $rowc[$j++] ?></td>
						<td>
							<? if(empty($row['alamat'])) { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<? } else { ?>
							<img id="imgcontact" src="images/house.png" title="<?= $row['alamat'] ?>">
							<? } if(empty($row['telepon'])) { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<? } else { ?>
							<img id="imgcontact" src="images/telp.png" title="<?= $row['telepon'] ?>">
							<? } if(empty($row['nohp'])) { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<? } else { ?>
							<img id="imgcontact" src="images/hp.png" title="<?= $row['nohp'] ?>">
							<? } if(empty($row['email'])) { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<? } else { ?>
							<img id="imgcontact" src="images/mail.png" title="<?= $row['email'] ?>">
							<? } ?>
						</td>
						<td align="center"><?= $rowc[++$j] ?></td>
						<td align="center">
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
						<?		
								if($c_delete and Modul::getRole() != 'admpeg') { ?>
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
