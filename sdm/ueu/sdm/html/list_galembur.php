<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('gaji'));
	require_once(Route::getUIPath('combo'));
		
	$p_model = mGaji;
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODEGAJI');
	$r_bayar = Modul::setRequest($_POST['bayar'],'BAYARLEMBUR');
	
	//periode aktif
	$r_periodenow = $p_model::getLastPeriodeGaji($conn);
	if(empty($r_periode))
		$r_periode = $r_periodenow;
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:300px"',false);
	$a_periode = $p_model::getCPeriodeGaji($conn);
	$l_periode = UI::createSelect('periode',$a_periode,$r_periode,'ControlStyle',true,'onchange="goSubmit()"');
	$a_bayar = $p_model::getCBayar();
	$l_bayar = UI::createSelect('bayar',$a_bayar,$r_bayar,'ControlStyle',true,'onchange="goSubmit()"');
		
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nik', 'label' => 'NIP', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'namapegawai', 'label' => 'Nama Pegawai','filter'=>'sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Nama Unit');
	$a_kolom[] = array('kolom' => 'namajenispegawai', 'label' => 'Jenis Pegawai', 'filter' => "t.tipepeg+' - '+j.jenispegawai");
	$a_kolom[] = array('kolom' => 'namapendidikan', 'label' => 'Pendidikan');
	$a_kolom[] = array('kolom' => 'upahlembur', 'label' => 'Nominal', 'type' => 'N', 'align' => 'right');
	
	// properti halaman
	$p_title = 'Daftar Perhitungan Lembur';
	$p_tbwidth = 1000;
	$p_aktivitas = 'ANGGARAN';
	$p_detailpage = Route::getDetailPage();
	$p_dbtable = "ga_gajipeg";
	$p_key = "periodegaji,idpegawai";
	
	$p_colnum = count($a_kolom)+3;
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	if (empty($r_sort))
		$r_sort = 'namapegawai';
		
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periodegaji',$r_periode);
	if(!empty($r_bayar)) $a_filter[] = $p_model::getListFilter('bayarlembur',$r_bayar);
		
	$sql = $p_model::listQueryHitLembur($r_periode);
	if(count($a_datafilter) > 0)
		$a_sql = $p_model::getListQuery($r_sort,$a_filter,$sql,$table,$r_row);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'hitung' and $c_insert) {		
		list($p_posterr,$p_postmsg) = $p_model::hitungLembur($conn,$r_periode,$a_sql);
	}
	else if($r_act == 'save' and $c_edit) {			
		list($p_posterr,$p_postmsg) = $p_model::saveLembur($conn,$r_periode,$_POST);
		
		$ok = Query::isOK($p_posterr);
	}
	else if($r_act == 'bayar' and $c_insert) {
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg) = $p_model::bayarLembur($conn,$r_periode,$a_sql);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Periode Gaji', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Status', 'combo' => $l_bayar);
	
	//periode aktif
	if($r_periode != $r_periodenow){
		$p_posterr = true;
		$p_postmsg = 'Periode gaji tidak aktif';
		$c_insert = false;
	}
	
	//cek apakah sudah dilakukan penarikan data
	$istarik = $p_model::isTarikData($conn,$r_unit,$r_periode);
	if(!$istarik){
		$p_posterr = true;
		$p_postmsg = 'Belum ada data pegawai yang ditarik, silahkan lakukan penarikan data pegawai terlebih dahulu';
		$c_insert = false;
	}
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
							<?	if($c_insert) { ?>
							<div class="right">
								<div class="TDButton" style="padding:7px 0px 7px;width:95px;position:relative;left:-5px;top:5px;" onClick="goBayar()">
									<img src="images/check.png" style="position:relative;left:-60px;top:-7px;">
									<span style="position:relative;left:15px">Bayarkan</span>
								</div>
							</div>
							<div class="right">
								<div class="TDButton" style="padding:7px 0px 7px;width:95px;position:relative;left:-5px;top:5px;" onClick="goHitung()">
									<img src="images/calc.png" style="position:relative;left:-60px;top:-7px;">
									<span style="position:relative;left:15px">Hitung</span>
								</div>
							</div>
							<?if(count($a_data)>0){?>
							<div class="right">
								<div class="TDButton" style="padding:7px 0px 7px;width:95px;position:relative;left:-5px;top:5px;" onClick="goSimpan()">
									<img src="images/disk.png" style="position:relative;left:-60px;top:-7px;">
									<span style="position:relative;left:15px">Simpan</span>
								</div>
							</div>
							<?}?>
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
						<th width="120px">Kasbon</th>
						<th width="100px">Total Lembur</th>
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
						<?	}?>
						<td align="right">
							<input type="hidden" name="id[]" value="<?= $row['idpegawai']; ?>" />
							<input type="hidden" name="upahlembur_<?= $row['idpegawai']?>" id="upahlembur_<?= $row['idpegawai']?>" value="<?= $row['upahlembur']; ?>" />
							<?= UI::createTextBox('kasbon_'.$row['idpegawai'],CStr::formatNumber($row['kasbon']),'ControlStyle',14,14,$c_edit,'style="text-align:right;" onkeydown="return onlyNumber(event,this,true,true);" onKeyUp="hitungLembur(this);numberFormat(this);"'); ?>
						</td>
						<td align="right"><span id="totallembur_<?= $row['idpegawai']?>"><?= CStr::formatNumber($row['totallembur'])?></span></td>
						<td align="center">
							<img id="<?= $t_key ?>" title="Tampilkan Slip Lembur" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
							<?if($row['isbayar'] == 'Y'){?>
							<img title="Sudah dibayarkan" src="images/check.png">
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
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goSimpan(){
	var simpan = confirm("Anda yakin untuk menyimpan lembur pegawai ?");
	if (simpan){
		document.getElementById("act").value = "save";
		goSubmit();
	}
}

function goHitung() {
	var hitung = confirm("Apakah anda yakin untuk melakukan perhitungan lembur ?");
	if(hitung) {
		document.getElementById("act").value = "hitung";
		goSubmit();
	}
}

function goBayar() {
	var hitung = confirm("Apakah anda yakin untuk membayarkan lembur pegawai?");
	if(hitung) {
		document.getElementById("act").value = "bayar";
		goSubmit();
	}
}

function hitungLembur(elem){
	var id = elem.id.split('_');
	var valnum = $("#upahlembur_"+id[1]).val().replace(/\./g,'') - elem.value.replace(/\./g,'');
	var ret = '';
	var j = 0;
	
	if(valnum != ''){	
		valnum = String(valnum);
		valnum = valnum.replace(/\./g,'');
		for(i=valnum.length-1;i>=0;i--) {
			if(j == 3) {
				ret = "." + ret;
				j = 0;
			}
			ret = valnum.charAt(i) + ret;
			j++;
		}
	}
	
	$("#totallembur_"+id[1]).html(ret);
}

function numberFormat(num) {
	var ret = '';
	var j = 0;
	valnum = num.value;
	
	if(valnum != ''){	
		valnum = String(valnum);
		valnum = valnum.replace(/\./g,'');
		for(i=valnum.length-1;i>=0;i--) {
			if(j == 3) {
				ret = "." + ret;
				j = 0;
			}
			ret = valnum.charAt(i) + ret;
			j++;
		}
		
		num.value = ret;
	}
}
</script>
</body>
</html>