<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	//ini_set('display_errors', 1);
	//error_reporting(E_ALL);
	// include
//	$conn->debug=-true;
	require_once(Route::getModelPath('mengajar'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_basis = Modul::setRequest($_POST['sistemkuliah'],'SISTEMKULIAH');
	$r_semmk = Modul::setRequest($_POST['semmk'],'SEMESTERMK');
	
	// combo
	$l_basis = uCombo::sistemkuliah($conn,$r_basis,'sistemkuliah','onchange="goSubmit()"',true);
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	$l_semmk = uCombo::semmk($r_semmk,false,'semmk','onchange="goSubmit()"',true);
	
	
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Data Mengajar Dosen';
	$p_tbwidth = 950;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mMengajar;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'a.thnkurikulum', 'label' => 'Kur.');
	$a_kolom[] = array('kolom' => 'a.kodemk', 'label' => 'Kode');
	$a_kolom[] = array('kolom' => 'namamk', 'label' => 'Nama MK');
	$a_kolom[] = array('kolom' => 'kelasmk', 'label' => 'Sesi');
	$a_kolom[] = array('kolom' => 'sks', 'label' => 'SKS');
	$a_kolom[] = array('kolom' => 'semmk', 'label' => 'Sem.'); 
	$a_kolom[] = array('kolom' => 'jeniskul', 'label' => 'Jenis','option'=>array('K'=>'Kuliah','P'=>'Praktikum')); 
	$a_kolom[] = array('kolom' => 'kelompok', 'label' => 'Kel.'); 
	$a_kolom[] = array('kolom' => 'nipdosen|namadepan|namatengah|namabelakang', 'label' => 'Pengajar');
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if(!$r_sort)
		$r_sort='a.nipdosen';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_semmk)) $a_filter[] = $p_model::getListFilter('semmk',$r_semmk);
	
	$a_data = $p_model::getTugasMengajar($conn,$a_kolom,$r_sort,$a_filter);
 
	
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Prodi Pengelola', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
	$a_filtercombo[] = array('label' => 'Semester Matakuliah', 'combo' => $l_semmk);
	$forcesearch=true;
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
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	} ?>
				<?	if($p_headermhs) { ?>
				<center>
				<?php require_once('inc_headermhs.php') ?>
				</center>
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
				<div class="DivError" style="display:none"></div>
				<div class="DivSuccess" style="display:none"></div>
				<div class="Break"></div>
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
						<?	}
							if($c_edit) { ?>
						<th width="30">Kewajiban Mengajar</th>
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							$t_key = $p_model::getKeyRow($row);
							
							$j = 0;
							
							
							// cek mengulang
							if(!empty($row['mengulang']))
								$rowstyle = 'YellowBG';
							else if ($i % 2)
								$rowstyle = 'NormalBG';
							else
								$rowstyle = 'AlternateBG';
							$i++;
							
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center"><?=$row['thnkurikulum']?></td>
						<td align="center"><?=$row['kodemk']?></td>
						<td><?=$row['namamk']?></td>
						<td align="center"><?=$row['kelasmk']?></td>
						<td align="center"><?=$row['sks']?></td>
						<td align="center"><?=$row['semmk']?></td>
						<td><?=$row['jeniskul']?></td>
						<td><?=$row['kelompok']?></td>
						<td><?=$row['nipdosen']?> - <?=$row['namapengajar']?></td>
						<td align="center">
						<input type="checkbox" id="<?=$t_key?>" <?=($row['tugasmengajar']==-1)?'checked':''?> title="Set/Unset Tugas Mengajar" onclick="setTugasMengajar(this)" <?=!$c_edit?'disabled':''?>>
						</td>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
				<br><br>
				<center>
				<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;" align="left">
					<table width="<?= $p_tbwidth-700 ?>" cellpadding="0" cellspacing="0" align="center">
						<tr>
							<td width="50"><strong>Baris</strong></td>
							<td width="40"><div class="YellowBG" style="border:1px solid #CCC;width:30px">&nbsp;</div></td>
							<td width="150"> : Tugas Mengajar</td>
						</tr>
					</table>
				</div>
				</center>
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
function sukses(msg){
	$(".DivSuccess").html(msg);
	$(".DivSuccess").show();
	$(".DivSuccess").fadeOut(2000);
}
function gagal(msg){
	$(".DivError").html(msg);
	$(".DivError").show();
	$(".DivError").fadeOut(2000);
}
function setTugasMengajar(elem){
	if(elem.checked){
		var posted = "f=setTugas&q[]="+elem.id;
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var msg=text.split('|');
			if(msg[0]==''){
				sukses(msg[1]);
			}else{
				gagal(msg[1]);
			}
		});
	}else if(!elem.checked){
		var posted = "f=unsetTugas&q[]="+elem.id;
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var msg=text.split('|');
			if(msg[0]==''){
				sukses(msg[1]);
			}else{
				gagal(msg[1]);
			}
		});
	}
}


</script>
</body>
</html>
