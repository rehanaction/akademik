<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	ini_set("max_execution_time", "1000000");
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$connsia = Query::connect('akad');
	if($_SERVER['REMOTE_ADDR'] == "66.96.234.212") //ip public sevima
		$connsia->debug=true;
	
	// include
	require_once(Route::getModelPath('angkakredit'));
	require_once(Route::getModelPath('integrasi'));
	require_once(Route::getUIPath('combo'));
	
	$p_model = mAngkaKredit;
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODEPROFDOS');
	if (empty($r_periode)) 
		$r_periode = mIntegrasi::getLastPeriodeSia($connsia);

	$a_periode1 = array('all' => '-- Pilih Periode --');
	$a_periode2 = mIntegrasi::getPeriodeSia($connsia);
	$a_periode = $a_periode1+$a_periode2;
	$l_periode = UI::createSelect('periode',$a_periode,$r_periode,'ControlStyle',$c_edit,'onChange="goSubmit()"');
	
	$r_input = Modul::setRequest($_POST['input'],'INPUT1B');
	$a_input = $p_model::getCInput();
	$l_input = UI::createSelect('input',$a_input,$r_input,'ControlStyle',true,'onchange="goSubmit()"');
	
	// properti halaman
	$p_title = 'Penarikan Data Mengajar Dosen';
	$p_tbwidth = 950;
	$p_aktivitas = 'DAFTAR';
	$p_key = 'nobidangibtemp,periode';
	$p_dbtable = 'ak_bidang1btemp';
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'periode', 'label' => 'Periode', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'nik', 'label' => 'NIP', 'filter' => "p.nik");
	$a_kolom[] = array('kolom' => 'nodosen', 'label' => 'No. Dosen', 'filter' => "p.nodosen");
	$a_kolom[] = array('kolom' => 'namalengkap', 'label' => 'Nama Pegawai', 'filter' => "sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)");
	$a_kolom[] = array('kolom' => 'namakegiatan', 'label' => 'Kegiatan Pengajaran/ Mata Kuliah');
	$a_kolom[] = array('kolom' => 'tempat', 'label' => 'Tempat');
	$a_kolom[] = array('kolom' => 'kelasmk', 'label' => 'Sesi');
	$a_kolom[] = array('kolom' => 'sks', 'label' =>'SKS', 'align' => 'center');
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'tarik' and $c_edit) {	
		
		$conn->StartTrans();
		
		list($p_posterr,$p_postmsg) = $p_model::tarikAkademik($conn,$connsia,$r_periode);
		
		$conn->CompleteTrans();
	}
	else if($r_act == 'input' and $c_edit) {	
		$r_key = CStr::removeSpecial($_POST['key']);
		
		$conn->StartTrans();
		
		list($p_posterr,$p_postmsg) = $p_model::saveToBidangIB($conn,$r_key);
		
		$conn->CompleteTrans();
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'namalengkap';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_periode)) $a_filter[] = mIntegrasi::getListFilter('periode',$r_periode);
	if(!empty($r_input)) $a_filter[] = $p_model::getListFilter('input',$r_input);
	
	$sql = $p_model::listQueryBidangIBTemp();
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
		
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode.($c_edit ? '&nbsp;&nbsp;<input type="button" name="btarik" id="btarik" onClick="goTarik()" class="ControlStyle" value="Tarik Data" />' :''));
	$a_filtercombo[] = array('label' => 'Status', 'combo' => $l_input);
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
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
						<th width="50">Status</th>
						<th width="50">Aksi</th>
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
						?>
						<td<?= $t_align ?>><?= $rowcc ?></td>
						<?	} ?>
						<td align="center"><?= $row['isinput'] == 'Y' ? '<img src="images/check.png" title="Sudah dimasukkan ke tabel KUM (Pengajaran)">' : '<img src="images/uncheck.png" title="Belum dimasukkan ke tabel KUM (Pengajaran)">'?></td>
						<? if($c_edit) { ?>
						<td align="center">
							<?if($row['isinput'] == 'Y'){?>
							<img id="<?= $row['idpegawai'] ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetailData(this)" style="cursor:pointer">
							<?}else{?>
							<img id="<?= $t_key.'|'.$row['idpegawai'] ?>" title="Klik untuk memasukkan ke tabel KUM (Pengajaran)" src="images/copy.png" onclick="goInput(this)" style="cursor:pointer">
							<?}?>
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
				<input type="hidden" name="idx" id="idx" value="7">
				<input type="hidden" name="link" id="link" value="1">
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

function goTarik() {
	var tarik = confirm("Apakah anda yakin mau melakukan penarikan data mengajar dari Akademik?");
	if(tarik) {
		document.getElementById("act").value = "tarik";
		goSubmit();
	}
}

function goInput(elem){
	document.getElementById("act").value = "input";
	document.getElementById("key").value = elem.id;
	goSubmit();	
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
