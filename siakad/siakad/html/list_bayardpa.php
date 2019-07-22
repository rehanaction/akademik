<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('honordpa'));
	require_once(Route::getModelPath('emailhonor'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Setting Pembayaran Honor Dosen Pembimbing Akademik';
	$p_tbwidth = 800;
	$p_aktivitas = 'ABSENSI';
	
	
	
	$p_model = mHonorDpa;
	
	
	// variabel request
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_bulanbayar = Modul::setRequest($_POST['bulanbayar'],'BULANBAYAR');
	$r_tahunbayar = Modul::setRequest($_POST['tahunbayar'],'TAHUN');
	$r_nopembayaran = Modul::setRequest($_POST['nopembayaran'],'NOPEMBAYARAN'); 
	$a_nopengajuan = $_POST['a_nopengajuan'];
	
	$r_periode=$r_tahun.$r_semester;
	$r_periodegaji=$r_tahunbayar.str_pad($r_bulanbayar,2,'0',STR_PAD_LEFT);
	
	$a_check=array(0=>'',-1=>'<img src="images/check.png">');
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'namadepan', 'label' => 'Dosen');
	$a_kolom[] = array('kolom' => 'm.nim', 'label' => 'Mahasiswa');
	$a_kolom[] = array('kolom' => 'honor', 'label' => 'Honor');
	$a_kolom[] = array('kolom' => 'isvalid', 'label' => 'Valid','type'=>'S','option'=>$a_check);
	$a_kolom[] = array('kolom' => 'nopengajuan', 'label' => 'No. Pengajuan');
	$a_kolom[] = array('kolom' => 'nopembayaran', 'label' => 'No. Pembayaran');
	
	$f = 3;
	
	// properti halaman tambahan
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'bayarHonor' and $c_edit) {
		
		list($p_posterr,$p_postmsg) = $p_model::setNoPembayaran($conn,$a_nopengajuan);
	}else if($r_act == 'sendMail' and $c_insert) {
		$a_penerimahonor=$p_model::getPenerimaHonor($conn,$a_nopengajuan);
		
		$conn->BeginTrans();
		foreach($a_penerimahonor as $nodosen=>$nodosen){
			list($p_posterr,$p_postmsg) =mEmailHonor::kirimHonorDpa($conn,$conn_sdm,$nodosen);
		}
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
		
	}
	
	
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('honorunit',$r_unit);
	if(!empty($r_periodegaji)) $a_filter[] = $p_model::getListFilter('periodegaji',$r_periodegaji); 
	if(!empty($r_nopembayaran)) $a_filter[] = $p_model::getListFilter('nopembayaran',$r_nopembayaran); 
	
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// mendapatkan data
	
	/*$l_nopengajuan = uCombo::nopembayaran($conn,$r_nopembayaran,'nopembayaran','onchange="goSubmit()"',true,$r_periode,$r_fakultas,$r_periodegaji);
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Nomor Pembayaran', 'combo' => $l_nopengajuan);
	//$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);*/
	
	$a_input = array();
	
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false).' '.uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false));
	$a_input[] = array('label' => 'Periode Pembayaran', 'input' => uCombo::bulan($r_bulanbayar,'bulanbayar','onchange="goSubmit()"',false).' '.uCombo::tahun($r_tahunbayar,true,'tahunbayar','onchange="goSubmit()"',false));
	$a_input[] = array('label' => 'Program Studi', 'input' => uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false));
	$a_input[] = array('label' => 'Nomor Pengajuan', 'nameid' => 'a_nopengajuan[]', 'type' => 'C', 'option' => $p_model::listNopengajuan($conn,$r_periode,$r_unit,$r_periodegaji,true));
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper" align="center">
		<div class="SideItem" id="SideItem">
			
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
			
				<?	if(!empty($p_postmsg)) { ?>
			 
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				 
				<div class="Break"></div>
				<?	} ?>
				<br>
					<header style="width:<?= $p_tbwidth-200 ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?=$p_title?></h1>
							</div>
							
						</div>
					</header>
				<table width="<?= $p_tbwidth-200 ?>" cellpadding="6" cellspacing="0" class="GridStyle">
					<?	$a_required = array();
						foreach($a_input as $t_row) {
							if($t_row['notnull'])
								$a_required[] = $t_row['id'];
							if(empty($t_row['input']))
								$t_row['input'] = uForm::getInput($t_row,$t_row['value'],true);
					?>
						<tr>
							<td width="150">
								<?= $t_row['label'] ?>
							</td>
							<td>
								<?= $t_row['input'] ?>
							</td>
						</tr>
					<?	} ?>
					<tr>		
						<td width="100">&nbsp;</td>
						<td>
							
							<input type="button" value="Bayarkan Honor" onclick="gobayarHonor()">&nbsp;
							<input type="button" value="Terbitkan Honor" onclick="goSendMail()">
						</td>
					</tr>
				</table><br>
				
				<!-- Filter -->
				<center>
				<?php require_once('inc_listfilter.php'); ?>
				</center>
				<br>
				<div class="DivError" style="display:none"></div>
				<div class="DivSuccess" style="display:none"></div>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<div class="right">
								<?	if(!empty($r_page)) { ?>
								<?php require_once('inc_listnavtop.php'); ?>
								<?	} ?>
							</div>
						</div>
					</header>
				 
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
							
							foreach($a_kolom as $i => $datakolom) {
								if(empty($datakolom['label']))
									continue;
								
								if($t_col == $datakolom['kolom'])
									$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
								else
									$t_sortimg = '';
						?>
						<th id="<?= $datakolom['kolom'] ?>"><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	} ?>
						
						
						
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) { 
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							$j = $f;
							
							
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $row['nipdosen'] ?>-<?= $row['namadosen'] ?></td>
						<td><?= $row['nim'] ?>-<?= $row['nama'] ?></td> 
						<td><?= number_format($row['honor'],0,',','.') ?></td> 
						<td><?= $row['isvalid'] ?></td> 
						<td><font size="1"><?= $row['nopengajuan'] ?></font></td> 
						<td><font size="1"><?= $row['nopembayaran'] ?></font></td> 
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
				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="keyjadwal" id="keyjadwal">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
			
		</div>
	</div>
</div>
<script type="text/javascript">
<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>	



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


function gobayarHonor(){
	document.getElementById("act").value = "bayarHonor";
	goSubmit();
}
function goSendMail(){
	document.getElementById("act").value = "sendMail";
	goSubmit();
}


</script>
</body>
</html>
