<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug = true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	/* $r_angkatan = Modul::setRequest($_POST['angkatan'],'ANGKATAN');
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_status = Modul::setRequest($_POST['statusmhs'],'STATUSMHS'); */
	
	// combo
	/* $l_angkatan = uCombo::angkatan($conn,$r_angkatan,'angkatan','onchange="goSubmit()"');
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	$l_status = uCombo::statusMhs($conn,$r_status,'statusmhs','onchange="goSubmit()"',false); */
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nim', 'label' => 'N I M');
	$a_kolom[] = array('kolom' => 'm.nama', 'label' => 'Nama');
	//$a_kolom[] = array('kolom' => 'm.sex', 'label' => 'L/P');
	// $a_kolom[] = array('kolom' => 'up.namaunit', 'label' => 'Fakultas', 'alias' => 'namafakultas');
	$a_kolom[] = array('kolom' => 'u.namaunit', 'label' => 'Prodi');
	$a_kolom[] = array('kolom' => 'm.semestermhs', 'label' => 'Sem.');
	$a_kolom[] = array('kolom' => 'm.skslulus', 'label' => 'SKS Lulus');  
	$a_kolom[] = array('kolom' => 'm.ipk', 'label' => 'IPK');  
	$a_kolom[] = array('kolom' => 'm.batassks', 'label' => 'Batas SKS');  
	$a_kolom[] = array('kolom' => 'm.statusmhs', 'label' => 'Status');
	$a_kolom[] = array('kolom' => 'm.sistemkuliah', 'label' => 'Basis');
	
	
	// properti halaman
	$p_title = 'Daftar Mahasiswa';
	$p_tbwidth = 750;
	$p_aktivitas = 'BIODATA';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mMahasiswa;
	$p_colnum = count($a_kolom)+3;
	$legendstatusmhs=true;
	$legendjkmhs=true;
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	/* if(!empty($r_angkatan)) $a_filter[] = $p_model::getListFilter('angkatan',$r_angkatan);
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_status)) $a_filter[] = $p_model::getListFilter('statusmhs',$r_status); */
	$a_filter[] = $p_model::getListFilter('unit',Modul::getUnit());
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	//$a_filtercombo = array();
	//$a_filtercombo[] = array('label' => 'Angkatan', 'combo' => $l_angkatan);
	//$a_filtercombo[] = array('label' => 'Jurusan', 'combo' => $l_unit);
	//$a_filtercombo[] = array('label' => 'Status', 'combo' => $l_status);
	//$a_filtercombo[] = array('label' => 'KRS u/ NIM', 'combo' => UI::createTextBox('nimkrs','','ControlStyle').' <input type="button" value="Tampilkan KRS" onclick="showKRS()">');
	
	// filter tree
	$a_filtertree = array();
	$a_filtertree['unit'] = array('label' => 'Prodi', 'data' => mCombo::unitTree($conn,true));
	$a_filtertree['angkatan'] = array('label' => 'Angkatan', 'data' => $p_model::angkatan($conn));
	$a_filtertree['m.statusmhs'] = array('label' => 'Status', 'data' => mCombo::statusMhs($conn));
	$a_filtertree['m.jalurpenerimaan'] = array('label' => 'Jalur', 'data' => mCombo::jalurPenerimaan($conn));
	$a_filtertree['m.sistemkuliah'] = array('label' => 'Basis Mahasiswa', 'data' => mCombo::sistemKuliah($conn));
	$a_filtertree['m.mhstransfer'] = array('label' => 'Status Masuk', 'data' => mCombo::statusMasukMhs());
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/hint.min.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
<?	if(!empty($a_filtertree)) { ?>
	<link href="style/jquery.treeview.css" rel="stylesheet" type="text/css">
<?	} ?>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?php require_once('inc_listfiltertree.php') ?>
				
				<?	if(!empty($a_filtertree)) { ?>
				<div style="float:left;width:760px">
				<?	}
					
					/**************/
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
							<?	if(!empty($r_page) or $c_insert) { ?>
							<div class="right">
								<?	if(!empty($r_page)) { ?>
								<?php require_once('inc_listnavtop.php'); ?>
								<?	} ?>
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
						<?	} ?>
						
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
							$krs = mMahasiswa::cekIsi($conn, $row['nim']);
					?>
					
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center"><?= $row['nim']?></td>
						<td><?= STRTOUPPER($row['nama']) ?></td>
						<?php /* <td align="center"><?= $row['sex'] ?></td> */ ?>
						<td align="center"><?= $row['namaunit'] ?></td>
						<? /* <td><?= $rowc[$j++] ?></td> */ ?>
						<td align="center"><?= $row['semestermhs'] ?></td>
						<td align="center"><?= $row['skslulus'] ?></td> 
						<td align="center"><?= $row['ipk'] ?></td>
						<td align="center"><?= $row['batassks'] ?></td>
						
						<td align="center"><?= $row['statusmhs'] // ++$j] ?></td>
						<td align="center"><?= $row['sistemkuliah'] ?></td>
						
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
				<div style="clear:both"></div>
				<div>
					<? require_once('inc_legendstatusmhs.php')?>
				</div>
				<br>

				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key"> 
				<input type="hidden" name="npm" id="npm">
				<?	if(!empty($a_filtertree)) { ?>
				</div>
				<?	} ?>
			</form>
		</div>
	</div>
</div>
<div id="popMenu" class="menubar" style="position:absolute; display:none; top:0px; left:0px;z-index:10000;" onMouseOver="javascript:overpopupmenu=true" onMouseOut="javascript:overpopupmenu=false">
<table class="menu-body">

	<?php 
		if (Akademik::isAdmin() || Akademik::isWk2() || Akademik::isMagister() || Akademik::isKemahasiswaan()) { ?>
			<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
		        <td onClick="showPage('npm','<?= Route::navAddress('set_krs') ?>')">KRS Sekarang</td>
		    </tr>
	<?php } ?>

	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('npm','<?= Route::navAddress('set_sppmhs') ?>')">Status SPP</td>
    </tr>

    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('npm','<?= Route::navAddress('view_absenmhs') ?>')">Lihat Absensi Mhs</td>
    </tr>

	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('npm','<?= Route::navAddress('view_keuanganmhs') ?>')">Keuangan (SPP)</td>
    </tr>

	

    <?php 
		if (Akademik::isAdmin() || Akademik::isKemahasiswaan() || Akademik::isWk2()) { ?>
			<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
		        <td onClick="showPage('npm','<?= Route::navAddress('list_perwalian') ?>')">Status Semester</td>
		    </tr>
	<?php } ?>

	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('npm','<?= Route::navAddress('view_kemajuanbelajar') ?>')">Kemajuan Belajar</td>
    </tr>
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('npm','<?= Route::navAddress('view_transkrip') ?>')">Transkrip</td>
    </tr>
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('npm','<?= Route::navAddress('view_khs') ?>')">Laporan IPS</td>
    </tr>
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('npm','<?= Route::navAddress('view_mengulang') ?>')">Mengulang</td>
    </tr>
    <?php 
		if (Akademik::isAdmin() || Akademik::isNilai() || Akademik::isMM() || Akademik::isKeuangan()) { ?>
			<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
		        <td onClick="showPage('npm','<?= Route::navAddress('list_krs') ?>')">Edit KRS</td>
		    </tr>
	<?php } ?>
	
    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('npm','<?= Route::navAddress('list_jumpingclass') ?>')">Jumping Clas</td>
    </tr>
</table>
</div>

<script type="text/javascript" src="scripts/jquery.balloon.min.js"></script>
<?	if(!empty($a_filtertree)) { ?>
<script type="text/javascript" src="scripts/jquery.cookie.js"></script>
<script type="text/javascript" src="scripts/jquery.treeview.js"></script>
<script type="text/javascript" src="scripts/jquery-ui.js"></script>
<?	} ?>
<script type="text/javascript">

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";
var cookiename = '<?= $i_page ?>.accordion';

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	// handle contact
	// $("[id='imgcontact']").balloon();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	<?	if(!empty($a_filtertree)) { ?>
	initFilterTree();
	<?	} ?>
});

function showKRS() {
	gParam = $("#nimkrs").val();
	
	if(gParam == "") {
		alert("Mohon isi KRS u/ NIM terlebih dahulu");
		$("#nimkrs").focus();
	}
	else
		showPage('npm','<?= Route::navAddress('set_krs') ?>');
}

</script>
</body>
</html>
