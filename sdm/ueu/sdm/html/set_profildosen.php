<?
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$connsia = Query::connect('akad');
	if($_SERVER['REMOTE_ADDR'] == "36.85.91.184") //ip public sevima
		$connsia->debug=true;
	
	// include
	require_once(Route::getModelPath('integrasi'));
	require_once(Route::getUIPath('combo'));
		
	$p_model = mIntegrasi;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nodosen', 'label' => 'No Dosen','align' => 'center');
	$a_kolom[] = array('kolom' => 'namalengkap', 'label' => 'Nama Dosen', 'filter' => 'sdm.f_namalengkap(gelardepan,namatengah,namadepan,namabelakang,gelarbelakang)');
	$a_kolom[] = array('kolom' => 'unithomebase', 'label' => 'Unit Homebase','filter' => 'uh.namaunit');
	$a_kolom[] = array('kolom' => 'unitmatkul', 'label' => 'Unit Matakuliah','filter' => 'u.namaunit');
	$a_kolom[] = array('kolom' => 'namapendidikan', 'label' => 'Pendidikan');
	$a_kolom[] = array('kolom' => 'jabatanfungsional', 'label' => 'Jab. Akademik');
	$a_kolom[] = array('kolom' => 'irpd', 'label' => 'LBP', 'type' => 'N');
	$a_kolom[] = array('kolom' => 'irkd', 'label' => 'KA', 'type' => 'N');
	
	// properti halaman
	$p_title = 'Sinkronisasi Profil Dosen';
	$p_tbwidth = 900;
	$p_aktivitas = 'UNIT';
	$p_dbtable = "pe_profildosen";
	$p_key = "periode,idunit";
	$p_colnum = count($a_kolom)+2;
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODEPROFDOS');
	if (empty($r_periode)) $r_periode = $p_model::getLastPeriodeSia($connsia);
	
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:300px"',false,true);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'set' and $c_edit) {
		$r_unitsdm = $p_model::getMapGateSDM($conn, $r_unit);
		$ar_mapunit = $p_model::getMapUnit($conn, $r_unitsdm);
		
		$a_dosen = array();
		$a_dosen = $p_model::getDataAjarSia($connsia, $r_periode, $ar_mapunit);
		
		$a_pegawai = array();
		$a_pegawai = $p_model::getCompPegawai($conn);
		
		$conn->StartTrans();
		if (count($a_dosen) > 0){
			$where = "periode,idunit";
			$t_key = $r_periode.'|'.$r_unitsdm;
			$p_model::delete($conn,$t_key,$p_dbtable,$where);
			
			foreach($a_dosen as $dosen){
				if ($a_pegawai['nodosen'][$dosen['nipdosen']] == $dosen['nipdosen']){
					$record = array();
					$record['periode'] = $r_periode;
					$record['idunit'] = $r_unitsdm;
					$record['kodeunitsia'] = $dosen['kodeunit'];
					$record['idpegawai'] = $a_pegawai['idpegawai'][$dosen['nipdosen']];
					$record['idpendidikan'] = $a_pegawai['idpendidikan'][$dosen['nipdosen']];
					$record['idpangkat'] = $a_pegawai['idpangkat'][$dosen['nipdosen']];
					$record['idjfungsional'] = $a_pegawai['idjfungsional'][$dosen['nipdosen']];
					$record['idjenispegawai'] = $a_pegawai['idjenispegawai'][$dosen['nipdosen']];
					
					list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,$p_dbtable);
				}
			}
		}else{
			$p_postmsg = "Data tidak ditemukan";
			$p_posterr = 1;
		}	
		
		$conn->CompleteTrans();
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if (empty($r_sort)) $r_sort = 'nodosen';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
		
	$sql = $p_model::listQueryProfilDosen();
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	
	$p_lastpage = Page::getLastPage();	
	
	$l_periode = UI::createSelect('periode',$p_model::getPeriodeSia($connsia),$r_periode,'ControlStyle',$c_edit,'onChange="goSubmit()"');
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode.'&nbsp;&nbsp;<input type="button" name="bsinkron" id="bsinkron" onClick="goSinkronisasi()" class="ControlStyle" value="Sinkronisasi" />');
	
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
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
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
						if (count($a_data) > 0){
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row,'idtim');
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
							
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center"><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td align="right"><?= $rowc[$j++] ?></td>
						<td align="right"><?= $rowc[$j++] ?></td>
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

function goSinkronisasi(){
	var set = confirm("Apakah anda yakin untuk sinkronisasi Periode " + $("#periode option:selected").text() + " ? ");
	if (set){
		document.getElementById("act").value = "set";
		goSubmit();
	}
}
</script>
</body>
</html>
