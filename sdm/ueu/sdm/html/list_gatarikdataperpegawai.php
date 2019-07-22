<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug = true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('gaji'));
	require_once(Route::getUIPath('combo'));
		
	$p_model = mGaji;
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODEGAJITARIK');
	
	//periode aktif
	$r_periodenow = $p_model::getLastPeriodeGaji($conn);
	if(empty($r_periode))
		$r_periode = $r_periodenow;
	
	// combo
	$a_periode = $p_model::getCPeriodeGaji($conn);
	$l_periode = UI::createSelect('periode',$a_periode,$r_periode,'ControlStyle',true,'onchange="goSubmit()"');
		
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nik', 'label' => 'NIP', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'namapegawai', 'label' => 'Nama Pegawai','filter'=>'sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Nama Unit');
	$a_kolom[] = array('kolom' => 'namajenispegawai', 'label' => 'Jenis Pegawai', 'filter' => "t.tipepeg+' - '+j.jenispegawai");
	$a_kolom[] = array('kolom' => 'namapendidikan', 'label' => 'Pendidikan');
	$a_kolom[] = array('kolom' => 'mkgaji', 'label' => 'Masa Kerja', 'align' => 'center', 'filter' => "substring(g.masakerja,1,2)+' tahun ' + substring(g.masakerja,3,2)+' bulan'");
	$a_kolom[] = array('kolom' => 'jabatanstruktural', 'label' => 'Jabatan');
	
	// properti halaman
	$p_title = 'Penguncian Data Pegawai untuk Perhitungan Gaji per Pegawai';
	$p_tbwidth = 1000;
	$p_aktivitas = 'ANGGARAN';
	$p_detailpage = 'data_gatarikdata';
	$p_dbtable = "ga_historydatagaji";
	$p_key = "idpeg,gajiperiode";
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'tarik' and $c_insert) {
		
		$r_namapegawai = CStr::removeSpecial($_POST['pegawai']);
		$r_idpegawai = CStr::removeSpecial($_POST['idpegawai']);
		$idpegawai = $p_model::cekTarikPegawai($conn,$r_idpegawai);
		
		if(!empty($idpegawai)){						
			$conn->BeginTrans();
			
			list($p_posterr,$p_postmsg) = $p_model::tarikData($conn,$r_periode,'',$idpegawai);
			
			if(!$p_posterr){				
				$p_model::setPegawaiGajiTetap($idpegawai,'TARIKDATA');
				
				$ok = Query::isOK($p_posterr);
				$conn->CommitTrans($ok);
			}
			
			$r_idpegawai = '';
			$r_namapegawai = '';
		}else{
			$p_posterr = true;
			$p_postmsg = 'Pegawai tersebut tidak dapat dilakukan untuk perhitungan gaji';		
		}
		
	}
	else if($r_act == 'delpegawai' and $c_delete) {	
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($periodegaji,$r_idpegawai) = explode('|',$r_key);
		$p_model::unsetPegawaiGaji('TARIKDATA',$r_idpegawai);
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
		$r_sort = 'namapegawai';
		
	// mendapatkan data
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periodehist',$r_periode);
		
	$sql = $p_model::listQueryHistoryGajiPerPegawai();
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode Gaji', 'combo' => $l_periode);
	
	//periode aktif
	if($r_periode != $r_periodenow){
		$p_posterr = true;
		$p_postmsg = 'Periode gaji tidak aktif';
		$c_insert = false;
	}
	
	//cek apakah sudah dibayarkan
	$a_byrpeg = $p_model::isBayarGajiTunj($conn,$r_periode);
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
				
				<? if ($c_insert) {?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td width="100px"><strong>Pegawai</strong></td>
								<td>
									: <?= UI::createTextBox('pegawai', $r_namapegawai,'ControlStyle',100,50,$c_edit); ?>
									<input type="hidden" name="idpegawai" id="idpegawai" value="<?= $r_idpegawai; ?>" />
									<img id="imgnik_c" src="images/green.gif">
									<img id="imgnik_u" src="images/red.gif" style="display:none">&nbsp;&nbsp;
								</td>
								<td id="be_save" class="TDButton" onclick="goTarik()" width="60px">
									<img src="images/disk.png"> Simpan
								</td>
								<td width="400px">&nbsp;</td>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?}?>
				
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
						<td<?= $t_align ?>><?= $rowcc ?></td>
						<?	} ?>
						<td align="center">
							<img id="<?= $t_key.'::list_gatarikdataperpegawai' ?>" title="Tampilkan Slip Gaji" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
							<?	if($c_delete) { ?>
							<img id="<?= $t_key ?>" title="Hapus dari daftar" src="images/delete.png" onclick="goHapus(this)" style="cursor:pointer">
							<?}?>
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
				<input type="hidden" name="key" id="key">
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

function goTarik() {
	var id = $('#idpegawai').val();
	if(id == ''){
		doHighlight(document.getElementById("pegawai"));
		alert('Silahkan pilih pegawai yang akan dihitung gajinya');
	}else{
		document.getElementById("act").value = "tarik";
		goSubmit();
	}
}

function goHapus(elem){
	document.getElementById("key").value = elem.id;
	document.getElementById("act").value = "delpegawai";
	goSubmit();	
}
</script>
</body>
</html>
