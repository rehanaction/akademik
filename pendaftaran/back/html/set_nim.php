<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getUIPath('combo'));
	
	$r_periode 		= Modul::setRequest($_POST['periode'],'PERIODE');
	$r_fakultas 	= Modul::setRequest($_POST['fakultas'],'FAKULTAS');
	$r_npm 			= Modul::setRequest($_POST['npm'],'NPM');
	
	//$periode    =array_values(mCombo::periode($conn));
	$l_periode 	= uCombo::periode($conn,$r_periode,'','periode','onchange="goSubmit()"');
	$l_fakultas 	= uCombo::fakultas($conn,$r_fakultas,'','fakultas','onchange="goSubmit()"', true,'pilih fakultas');
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nopendaftar', 'label' => 'No. Pendaftar');
	$a_kolom[] = array('kolom' => 'p.nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Prodi');
	$a_kolom[] = array('kolom' => 'nimpendaftar', 'label' => 'NIM');
	$a_kolom[] = array('kolom' => 'nim', 'label' => 'export ?', 'nosearch'=>true);
	
	// properti halaman
	$p_title = 'Pembuatan NIM Mahasiswa Baru';
	$p_tbwidth = "100%";
	$p_aktivitas = 'BIODATA';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mPendaftar;
	$p_colnum = count($a_kolom);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	else if ($r_act=='generate'){
		if (empty ($r_periode))
			list($p_posterr, $p_postmsg) = array(true,'silahkan pilih periode');
		else if (empty ($r_fakultas))
			list($p_posterr, $p_postmsg) = array(true,'silahkan pilih fakultas');
		else
			list($p_posterr, $p_postmsg)   = $p_model::setNIM($conn, $r_periode, $r_fakultas);			
        
     }else if ($r_act=='export'){
		if (empty ($r_periode))
			list($p_posterr, $p_postmsg) = array(true,'silahkan pilih periode');
		else if (empty ($r_fakultas))
			list($p_posterr, $p_postmsg) = array(true,'silahkan pilih fakultas');
		else
			list($p_posterr, $p_postmsg)   = $p_model::exportPendaftar($conn, $r_periode, $r_fakultas);	
			 		 
	}else if ($r_act=='transfer'){
		if (empty ($r_periode))
			list($p_posterr, $p_postmsg) = array(true,'silahkan pilih periode');
		else if (empty ($r_fakultas))
			list($p_posterr, $p_postmsg) = array(true,'silahkan pilih fakultas');
		else
			list($p_posterr, $p_postmsg)   = $p_model:: exportPendaftar($conn, $r_periode, $r_fakultas, $r_npm);
			 				 
	}
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	$ceknimsql = $p_model::getSQLnim();
	
	// mendapatkan data
	$a_filter[] = "lulusujian='-1'";
	if(!empty($r_periode)) $a_filter[] 	= $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_fakultas)) $a_filter[] 	= $p_model::getListFilter('fakultas',$r_fakultas);
	
	$fakultas = mcombo::fakultas($conn);
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter, $ceknimsql);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo=array();
    $a_filtercombo[] = array('label'=>'Periode', 'combo'=> $l_periode);
    $a_filtercombo[] = array('label'=>'Fakultas', 'combo'=> $l_fakultas);
	
	if (!empty ($r_periode))
		$pesan = 'Generate NIM akan dilakukan untuk periode '.$r_periode;
	if (!empty ($r_fakultas))
		$pesan .=  ' dan '.$fakultas[$r_fakultas];
		
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
		.export{ cursor:pointer}
		</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<center>
					<div style="width:<?= $p_tbwidth ?>px; border-width:1px; background: #eceaea; border-radius:5px; border-style: solid; border-color:#a09e9e; padding: 10px;">
						Generate NIM massal hanya dapat dilakukan jika informasi jalur penerimaan, fakultas dan jurusan masing-masing pendaftar terisi lengkap
					</div>
				</center>
				<br>
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	} ?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth ?>px;">
						<table width="<?= $p_tbwidth ?>" cellpadding="0" cellspacing="3" align="center" border="0">
						<tr>
							<td valign="top" width="100%">
								<table width="100%" cellspacing="0" cellpadding="4">
									<? foreach($a_filtercombo as $t_filter) { ?>
									<tr>		
										<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
										<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>		
									</tr>
									<? } ?>
								</table>
							</td>
							<td  style="width: 60px; " colspan="2" align="right" >			
							</td>
						</tr>
						</table>						
						<?= '<br><b>'.$pesan.'</b><br>'?><br>
						<div align="right">
							<input name="btnFilter" type="button" value="Generate NIM" onclick="goGenerate()">
						</div>
					</div>
				<br>	
				<div class="filterTable" style="width:<?= $p_tbwidth ?>px;">
				<? if(!empty($r_page)) { ?>
					<table width="100%" cellspacing="0" cellpadding="4">
						<tr>
							<td width="40" style="white-space:nowrap"><strong>Cari :</strong></td>
							<td width="50"><?= uCombo::listColumn($a_kolom) ?></td>
							<td width="210"><input name="tfilter" id="tfilter" class="ControlStyle" size="25" onkeydown="etrFilterCombo(event)" type="text"></td>
							<td width="40"><input type="button" value="Filter" class="ControlStyle" onClick="goFilterCombo()"></td>
							<td><input type="button" value="Refresh" class="ControlStyle" onClick="goRefresh()"></td>
						</tr>
					</table>
					<?	/********************/
						/* INFORMASI FILTER */
						/********************/
						
						if(!empty($a_datafilter)) { ?>
					<table cellpadding="4" cellspacing="0" class="LiteHeaderBG">
					<?	$i = 0;
						foreach($a_datafilter as $t_idx => $t_data) { ?>
						<tr>
							<td width="30" style="white-space:nowrap"><?= $t_data['label'] ?></td>
							<td align="center" width="5">:</td>
							<td><?= $t_data['str'] ?></td>
							<td valign="top" align="right"><u title="Hapus Filter" id="remfilter" style="color:#3300FF;cursor:pointer;text-decoration:none" onclick="goRemoveFilter(<?= $i++ ?>)">x</u></td>
						</tr>
					<?	} ?>
					</table>
					<?	} ?>
				<?	} ?>
				</div>
			
					
					
				</center>
				<br>
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
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							$nim =  $row['nimpendaftar'];
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td align="center"><?= $rowc[$j++] ? '<img src="images/check.png">' : '<img id="'.$nim.'" src="images/out.png" class="export" onclick="goTransfer(this)">' ?></td>
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
							Halaman <?= $r_page ?>
						</div>
						</td>
					</tr>
					<?	} ?>
				</table>
				<? if(!empty($r_page)) { ?>
				<?php require_once('inc_listnav.php'); ?>
				<? } ?>
				<center>
					<div style="width:<?= $p_tbwidth ?>px; border-width:1px; background: #eceaea; border-radius:5px; border-style: solid; border-color:#a09e9e; padding: 10px; margin-top:30px" align="left">
						Pendaftar yang akan di export mejadi mahasiswa adalah
						<ul>
							<li>Telah memiliki NIM</li>
							<li>Telah diterima di prodi tertentu</li>
							<li>Telah menyelesaikan administrasi pemberkasan</li>
							<li>Telah melakukan pembayaran semua tagihan sebagai mahasiswa baru</li> 
							
						</ul>
						setelah selesai proses generate nim silahkan export pendaftar menjadi mahasiswa dengan klik tombol dibawah ini<br><br>

					</div>
					<div style="width:<?= $p_tbwidth ?>px; border-width:1px; background: #eceaea; border-radius:5px; border-style: solid; border-color:#a09e9e; padding: 10px; margin-top:30px">
						Keterangan:<br>
						Jika ada tanda <img src="images/out.png"> pada kolom export menandakan bahwa pendaftar yang bersangkutan telah memiliki nim namun belum ada di data mahasiswa
					</div>

				</center>
				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
					
				<input type="hidden" name="npm" id="npm">

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

function goGenerate(){
	conf = confirm ('apakah anda yakin akan melakukan generate nim ?');
		if (conf){
			document.getElementById('act').value='generate';
			goSubmit();
		}
	}
	
function goExport(){
	conf = confirm ('apakah anda yakin akan melakukan Export data pendaftar ke mahasiswa ?');
		if (conf){
			document.getElementById('act').value='export';
			goSubmit();
		}	
	}
	
function goTransfer(elem){
	conf = confirm ('apakah anda yakin akan melakukan Export data pendaftar ke mahasiswa ?');
		if (conf){
			document.getElementById('npm').value=elem.id;
			document.getElementById('act').value='transfer';
			goSubmit();

		}	
	}
</script>
</body>
</html>
