<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_linkedit=true;
	
	// include
	require_once(Route::getModelPath('thnkurikulum'));
	require_once(Route::getModelPath('matakuliah'));
	require_once(Route::getUIPath('combo'));
	
	
	// properti halaman
	$p_title = 'Informasi Tahun Kurikulum';
	$p_tbwidth = 700;
	$p_aktivitas = 'KULIAH';
	
	$p_model = mThnkurikulum;
	$p_key = $p_model::key;
	
	
	// struktur view
	
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'thnkurikulum', 'label' => 'Kurikulum', 'size' => 5, 'maxlength' => 5, 'notnull' => true);
 
	
	// ada aksi
	$r_act = $_POST['act']; 
	if($r_act == 'insert' and $c_insert) {
		$record['thnkurikulum'] = $_REQUEST['thnkurikulum'];
		list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true);
	}
	else if($r_act == 'edit' and $c_edit){
		$r_edit = $_REQUEST['thnkurikulum'];
		
	}	
	else if($r_act == 'delete' and $c_delete) {
		$r_key = $_REQUEST['thnkurikulum'];
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);	
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	 
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	$m_model = mMahasiswa;
	
	$p_colnum = count($a_kolom)+2;
	
	if(empty($p_detailpage))
		$p_detailpage = Route::getDetailPage();
		
	$a_filtertree = array();
	$a_filtertree['thnkurikulum'] = array('label' => 'Kurikulum', 'data' => mCombo::kurikulum($conn)); 
	
	$expld=explode("|",$_POST['filter']);
	$expld2=explode(":", $expld[2]);
	$tahun=$expld2[1];
	
	if (!empty($tahun))
	$thn=$tahun;
	else
	$thn=date('Y');
	
	$a_data=$p_model::allData($conn,$thn);
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
	<link href="style/jquery.treeview.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem"> 
		
			<!---center>
			<div class="filterTable" style="width:400px; margin-bottom:10px">
			<b>Tambah Tahun Kurikulum</b>
			<br>
			<br>
			<input type="text" name="tahunkurikulum">
			<input type="submit" value="tambah">
			</div>
			</center--->
			<form name="pageform" id="pageform" method="post"> 
			<?php require_once('inc_listfiltertree.php') ?>
		<div style="float:left;width:760px">
				<?	 
					
					/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	}
					 ?>
				  
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
				<div class="filterTable" style="width:500px; margin-bottom:10px">
					 
						TAHUN KURIKULUM :
						  
						<?= UI::createTextBox('thnkurikulum',$thn,'ControlStyle',10,10) ?>
						<? if (!empty($tahun)){?>
						
						<input type="button" name="tahun" value="edit" class="ControlStyle" onclick="goEdit()">
						<input type="button" name="tahun" value="hapus" class="ControlStyle" onclick="goDelete()">
						
						<? } else {?>
						<input type="button" name="tahun" value="tambah" class="ControlStyle" onclick="goSimpan()"> 
						<?}?>
				 
				</div>
				</center>
				<center>
					<header style="width:<?= $p_tbwidth ?>px;display:table">
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
						<th>Kurikulum</th>
						<th>Unit</th>
						<th>Nama Unit</th>
						<th>Total Mata kuliah Kurikulu</th>
						<th>Jenjang study</th>
						<th>MataKuliah Jurusan</th>
						<th>MataKuliah Wajib</th>
						<th>MataKuliah Pilihan</th>
						<th>MataKuliah Paket</th> 
						
						
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
						<td><?= $row['thnkurikulum']?></td>
						<td><?= $row['kodeunit'];?></td>
						<td><?= $row['namaunit'];?></td> 
						<td><?= $row['mk_totalkurikulum']?></td> 
						<td><?= $row['kode_jenjang_studi']?></td>
						<td><?= $row['mk_jurusan']?></td>
						<td><?= $row['mk_wajib']?></td>
						<td><?= $row['mk_pilihan']?></td>
						<td><?= $row['mk_paket']?></td>
						
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}
						else if(!empty($a_total)) {
							$n_kolom = count($a_kolom);
							if($c_edit) $n_kolom++;
							if($c_delete) $n_kolom++;
					?>
					<tr>
						<th colspan="<?= $a_total['index'] ?>"><?= $a_total['label'] ?></th>
						<th><?= $t_total ?></th>
						<th colspan="<?= $n_kolom-$a_total['index']+1 ?>">&nbsp;</th>
					</tr>
					<?	}
						
						/**********/
						/* FOOTER */
						/**********/
						
					 ?>
				</table> 
				<?	if(!empty($r_page)) { ?>
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
<script type="text/javascript" src="scripts/jquery.cookie.js"></script>
<script type="text/javascript" src="scripts/jquery.treeview.js"></script>
<script type="text/javascript" src="scripts/jquery-ui.js"></script>

<script type="text/javascript">

var detailpage = "<?= Route::navAddress($p_detailpage) ?>";
var cookiename = '<?= $i_page ?>.accordion';

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	}); 
	initFilterTree();
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?> 
});

function goSimpan() {
	document.getElementById("act").value = "insert";
	goSubmit();
}
function goDelete() {
	document.getElementById("act").value = "delete";
	goSubmit();
}

function goEdit() {
	document.getElementById("act").value = "edit";
	goSubmit();
}


 
</script>
 
</body>
</html>