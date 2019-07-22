<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug = true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// hak akses manual :D
	$c_edit = true;
	
	// include
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('perwalian'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_angkatan = Modul::setRequest($_POST['angkatan'],'ANGKATAN');
	
	if(Akademik::isDosen())
		$r_key = Modul::getUserIDPegawai();
	
		
	else
		$r_key = CStr::removeSpecial($_REQUEST['idpegawai']);
	
	$r_nama = Akademik::getNamaPegawai($conn,$r_key);
	$r_periode = Akademik::getPeriode();
	
	$r_dosen = $r_nama;
	
	// combo
	$l_angkatan = uCombo::angkatan($conn,$r_angkatan,'angkatan','onchange="goSubmit()"');
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'm.nim', 'label' => 'N I M');
	$a_kolom[] = array('kolom' => 'substring(m.periodemasuk,1,4)', 'alias' => 'angkatan', 'label' => 'Angkatan', 'type' => 'S', 'option' => mCombo::tahun());
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'sistemkuliah', 'label' => 'Basis');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Jurusan');
	$a_kolom[] = array('kolom' => 'semestermhs', 'label' => 'Sem.');
	$a_kolom[] = array('kolom' => 'm.skslulus', 'label' => 'SKS');
	$a_kolom[] = array('kolom' => 'frsterisi', 'label' => 'Isi');
	$a_kolom[] = array('kolom' => 'frsdisetujui', 'label' => 'Approve');
	$a_kolom[] = array('kolom' => 'prasyaratspp', 'label' => 'SPP');
	$a_kolom[] = array('kolom' => 'm.statusmhs', 'label' => 'Status');
	
	// properti halaman
	$p_title = 'Data Mahasiswa Wali';
	$p_tbwidth = 900;
	$p_aktivitas = 'BIODATA';
	
	$p_model = mMahasiswaWali;
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'refresh') {
		Modul::refreshList();
	}
	else if(!empty($r_act) and $c_edit) {
		if($r_act[0] == 'u') // unset
			$t_val = 0;
		else
			$t_val = -1;
		
		if(substr($r_act,-1) == '2')
			$t_col = 'frsterisi';
		else
			$t_col = 'frsdisetujui';
		
		$r_npm = CStr::removeSpecial($_POST['key']);
		
		$record = array();
		$record['nim'] = CStr::cStrNull($r_npm);
		$record['periode'] = $r_periode;
		$record['statusmhs'] = 'A';
		$record['nipdosenwali'] = CStr::cStrNull($r_key);
		$record[$t_col] = $t_val;
		
		list($p_posterr,$p_postmsg) = mPerwalian::saveRecord($conn,$record,$r_npm.'|'.$r_periode,true);
	}
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	$a_filter[] = $p_model::getListFilter('pembimbing',$r_key);
	if(!empty($r_angkatan)) $a_filter[] = $p_model::getListFilter('angkatan',$r_angkatan);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Angkatan', 'combo' => $l_angkatan);
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
				<?php require_once('inc_headerdosen.php') ?>
				</center>
				<br>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<?	if(!empty($r_page)) { ?>
							<div class="right">
								<?php require_once('inc_listnavtop.php'); ?>
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
						<th width="30">Link</th>
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
							
							if(!empty($row['frsdisetujui']))
								$rowstyle .= " GreenBG";
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						
						<td><?= $rowc[$j++] ?></td>

						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<?php/*<td align="center"><input type="checkbox"  readonly id="<?= $t_key ?>"<?= empty($rowc[$j++]) ? '' : ' checked' ?> onclick="goIsi(this)"></td>
						<td align="center"><input type="checkbox" readonly  id="<?= $t_key ?>"<?= empty($rowc[$j++]) ? '' : ' checked' ?> onclick="goApprove(this)"></td>*/?>
						<td align="center"><?= empty($rowc[$j++]) ? '' : '<img src="images/check.png">' ?></td>
						<td align="center"><?= empty($rowc[$j++]) ? '' : '<img src="images/check.png">' ?></td>
						<td align="center"><?= empty($rowc[$j++]) ? '' : '<img src="images/check.png">' ?></td>
						<td align="center"><?= $rowc[$j++] ?></td>
						<td><img id="<?= $t_key ?>" title="Halaman Mahasiswa" src="images/link.png" onclick="goPop('popMenu',this,event)" style="cursor:pointer"></td>
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
				<input type="hidden" name="nip" id="nip" value="<?= $r_key ?>">
			</form>
		</div>
	</div>
</div>
<div id="popMenu" class="menubar" style="position:absolute; display:none; top:0px; left:0px;z-index:10000;" onMouseOver="javascript:overpopupmenu=true" onMouseOut="javascript:overpopupmenu=false">
<table width="130" class="menu-body">
    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
	 
        <td onClick="submitPage('npm','<?= Route::navAddress('set_krs') ?>')">KRS</td>
    </tr>
    <!--
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="submitPage('npm','<?= Route::navAddress('view_khs') ?>')">KHS</td>
    </tr> -->
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="submitPage('npm','<?= Route::navAddress('view_transkrip') ?>')">Transkrip</td>
    </tr>
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="submitPage('npm','<?= Route::navAddress('view_kemajuanbelajar') ?>')">Kemajuan Belajar</td>
    </tr>

</table>
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

function goIsi(elem) {
	document.getElementById("key").value = elem.id;
	document.getElementById("act").value = (elem.checked ? "set" : "unset")+"2";
	goSubmit();
}

function goApprove(elem) {
	document.getElementById("key").value = elem.id;
	document.getElementById("act").value = (elem.checked ? "set" : "unset");
	goSubmit();
}

</script>
</body>
</html>
