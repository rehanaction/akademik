<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pa'));
	require_once(Route::getUIPath('combo'));
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'namalengkap', 'label' => 'Nama Pegawai', 'filter' => 'sdm.f_namalengkap(p.gelardepan,p.namatengah,p.namadepan,p.namabelakang,p.gelarbelakang)');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Unit');
	
	// properti halaman
	$p_title = 'Daftar Pegawai Yang Dinilai';
	$p_tbwidth = 600;
	$p_aktivitas = 'NILAI';
	$p_key = 'kodeperiodepa,idpenilai,idpegawai';
	
	$p_model = mPa;
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_key = Modul::setRequest($_POST['key'],'KEYPEGAWAI');	
		
	$r_act = $_POST['act'];
	if($r_act == 'savepegawai' and $c_edit) {
		$r_pegawai = CStr::removeSpecial($_REQUEST['idpegawai']);
		list($p_posterr,$p_postmsg) = $p_model::savePegawaiDet($conn,$r_key,$r_pegawai);
	}
	else if($r_act == 'delpegawai' and $c_delete) {
		$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,'pa_hasilpenilaian',$p_key);
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
	
	$sql = $p_model::listPAPegawaiDinilai($r_key);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	
	$row = $p_model::getInfoPenilai($conn,$r_key);
	if(empty($row))
		Route::navigate('home');
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
			<form name="pageform" id="pageform" method="post">
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1>Data Penilai</h1>
							</div>
						</div>
					</header>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" width="150px">Periode Penilaian</td>
							<td class="RightColumnBG"><?= $row['namaperiodepa'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG">Nama Penilai</td>
							<td class="RightColumnBG"><?= $row['namalengkap'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG">Jabatan</td>
							<td class="RightColumnBG"><?= $row['jabatanstruktural'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG">Unit</td>
							<td class="RightColumnBG"><?= $row['namaunit'] ?></td>
						</tr>
					</table>
				</center>
				<br>
				<center>
					<?if($c_edit){?>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
					<table width="100%">
						<tr>
							<td><strong>Pegawai yang dinilai</strong></td>
							<td>
								: <?= UI::createTextBox('pegawai', '','ControlStyle',100,40,$c_edit); ?>
								<input type="hidden" name="idpegawai" id="idpegawai" value="" />
								<img id="imgnik_c" src="images/green.gif">
								<img id="imgnik_u" src="images/red.gif" style="display:none">&nbsp;&nbsp;
								<input type="button" value="Simpan" id="be_savedet" class="ControlStyle" onClick="goSaveDet()">
							</td>
						</tr>
					</table>
					</div>
					<br>
					<?}?>
				</center>
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
						<th width="50">Aksi</th>
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
						?>
						<td><?= $t_align ?><?= $rowcc ?></td>
						<?	} ?>
						<td align="center">
							<? if($c_delete) { ?>
							<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDeleteDet(this)" style="cursor:pointer">
						<?		} ?>
						</td>
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
				<input type="hidden" name="key" id="key" value="<?= $r_key?>">
				<input type="hidden" name="subkey" id="subkey">
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
	
	$("input[name='pegawai']").xautox({strpost: "f=acnamapegawai", targetid: "idpegawai", imgchkid: "imgnik", imgavail: true});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goSaveDet(){
	if($("#idpegawai").val() == ''){
		doHighlight(document.getElementById("pegawai"));
		alert("Silahkan masukkan nama atau NPP Pegawai Yang Dinilai");
	}else{
		document.getElementById("act").value = "savepegawai";
		goSubmit();
	}	
}

function goDeleteDet(val){
	document.getElementById("act").value = "delpegawai";
	document.getElementById("subkey").value = val.id;
	goSubmit();	
}

</script>
</body>
</html>
