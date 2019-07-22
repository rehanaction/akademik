<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_delete = $a_auth['candelete'];
	// include
	require_once(Route::getModelPath('dosenwali'));
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getUIPath('combo'));
	//$conn->debug = true;
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_dosen = Modul::setRequest($_POST['dosen'],'DOSEN');
	$r_periode=Akademik::getPeriode();
	
	if(strpos($r_dosen,' - ')){
		$arr=explode(' - ',$r_dosen);
		$r_dosen=$arr[0];
	}
	$r_kelas = Modul::setRequest($_POST['sistemkuliah'],'SISTEMKULIAH');
	$r_jurusan = Modul::setRequest($_POST['jurusan'],'JURUSAN');
	$r_angkatan = Modul::setRequest($_POST['angkatan'],'ANGKATAN');
	
	//variabel dari text autocomplete
	$r_dosen2 = Modul::setRequest($_POST['dosen2'],'DOSEN');
	$r_nama = Modul::setRequest($_POST['dosen2'],'DOSEN');
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	//$l_dosen = uCombo::dosen($conn,$r_dosen,'','dosen','onchange="goSubmit()" style="width:300px"',false);
	$i_dosen=UI::createTextBox('dosen2',$r_dosen2,'ControlStyle', 40, 40);
	$i_dosen.=' <input type="button" value="Tampilkan" onclick="goSubmit()">';
	$i_dosen.='<input type="hidden" name="dosen">';
	
	if(empty($r_dosen))
		$c_delete=false;
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nim', 'label' => 'NIM');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'sistemkuliah', 'label' => 'Basis');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Prodi');
	$a_kolom[] = array('kolom' => 'semestermhs', 'label' => 'Semester');
	$a_kolom[] = array('kolom' => 'f_namalengkap', 'label' => 'Nama Dosen Wali');
	$a_kolom[] = array('kolom' => 'namastatus', 'label' => 'Status');
	
	// properti halaman
	$p_title = 'Data Mahasiswa Wali '.$r_dosen2;
	$p_tbwidth = 800;
	$p_aktivitas = 'ABSENSI';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mDosenwali;
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		$r_nim1 = CStr::removeSpecial($_POST['nim']);
		$r_nim2 = CStr::removeSpecial($_POST['lastnim']);
		//list($p_posterr,$p_postmsg) = $p_model::insert($conn,$r_dosen,$r_nim1,$r_nim2,$r_periode);
		list($p_posterr,$p_postmsg) = $p_model::insertMhs($conn,$r_dosen,$r_nim1,$r_nim2,$r_periode);
	}if($r_act == 'insertperkelas' and $c_insert) {
		list($p_posterr,$p_postmsg) = $p_model::insertPerKelas($conn,$r_dosen,$r_kelas,$r_jurusan,$r_angkatan,$r_periode);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$r_periode);
	}
	else if($r_act == 'generateWali'){
		list($p_posterr,$p_postmsg) = $p_model::generateWali($conn);
	}
	else if($r_act == 'downxls') {
		$a_header = array('NIM');
				
		$a_huruf = CStr::arrayHuruf();
		
		// menghilangkan segala echo
		ob_clean();
		
		header("Content-Type: application/msexcel");
		header('Content-Disposition: attachment; filename="template_dosenwali.xls"');
		
		// pakai phpexcel
		require_once($conf['includes_dir'].'phpexcel/PHPExcel.php');
		
		$xls = new PHPExcel();
		$xls->setActiveSheetIndex(0);
		$sheet = $xls->getActiveSheet();
		
		// header
		$r = 1;
		foreach($a_header as $i => $t_header)
			$sheet->setCellValue($a_huruf[$i].$r,$t_header);
		
		// paskan ukuran
		$n = count($a_header);
		for($i=0;$i<$n;$i++)
			$sheet->getColumnDimension($a_huruf[$i])->setAutoSize(true);
		
		$xlsfile = PHPExcel_IOFactory::createWriter($xls,'Excel5');
		$xlsfile->save('php://output');
		
		exit;
	}	
	else if($r_act == 'uploadxls'){
	
		if(empty($_FILES['xls']['name'])){
			$p_posterr=true;
			$p_postmsg = 'Mohon Pilih File';
		}else if($_FILES['xls']['type']!='application/msexcel'){
			$p_posterr=true;
			$p_postmsg = 'Data Harus format xls, agar lebih pasti Download template yang disediakan';
		}else if($r_dosen == ''){
			$p_posterr=true;
			$p_postmsg = 'Silakan Pilih Dahulu Dosen. Kemudian Klik Tampilkan.';
		}else{
			$r_file = $_FILES['xls']['tmp_name'];
			
			// pakai excel reader
			require_once($conf['includes_dir'].'phpexcel/excel_reader2.php');
			$xls = new Spreadsheet_Excel_Reader($r_file);
			
			$cells = $xls->sheets[0]['cells'];
			$numrow = count($cells);
			
			// jika cells kosong (mungkin bukan merupakan format excel), baca secara csv
			if(empty($numrow)) {
				if(($handle = fopen($r_file, 'r')) !== false) {
					while (($data = fgetcsv($handle, 1000, "\t")) !== false) {
						$numrow++;
						foreach($data as $k => $v)
							$cells[$numrow][$k+1] = $v;
					}
					fclose($handle);
				}
			}
			
			// baris pertama adalah header
			// $conn->BeginTrans();
			$str_nim=array();
			for($r=2;$r<=$numrow;$r++) {
				$data = $cells[$r];
				
				$rowxls = array();
				foreach($cells[1] as $k => $v) {
					$v = strtolower($v);
					$rowxls[$v] = trim($data[$k]);
				}
				
				$t_npm = $rowxls['nim'];
				$str_nim[] = $t_npm;
			}
			// exit;
			//$import = mImporPendaftar::uploadFile($conn,$r_periode,$r_jalur,$r_file);
			list($p_posterr,$p_postmsg) = $p_model::upLoadMhsWali($conn,$r_dosen,$r_file,$str_nim,$r_periode);
			if(!$p_posterr)
				$p_postmsg = 'Impor data dari format excel berhasil';
			else{
				$p_posterr=true;
				$p_postmsg = 'Impor data dari format excel gagal';
			}
		}
		
	}
	else if($r_act == 'refresh'){
		Modul::refreshList();
	}
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_dosen)) $a_filter[] = $p_model::getListFilter('dosen',$r_dosen);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Prodi', 'combo' => $l_unit);
	//$a_filtercombo[] = array('label' => 'Dosen', 'combo' => $l_dosen);
	$a_filtercombo[] = array('label' => 'Dosen', 'combo' => $i_dosen);
	$l_kelas=uCombo::kelas($conn,$r_kelas,'sistemkuliah','',false);
	$l_jurusan=uCombo::jurusan($conn,$r_jurusan,'','jurusan','',false,true);
	$l_angkatan=uCombo::angkatan($conn,$r_angkatan,'angkatan','',false);
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
				<?php $r_dosen=$r_dosen2;require_once('inc_headerdosen.php') ?>
				</center>
				<?	if($c_insert and !empty($r_dosen)) { ?>
				<center>
					
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td colspan="2" align="center">Generate Mahasiswa Wali</td>
					</tr>
					<tr>
						<th>Set Dosen Wali Berdasarkan Kelas / Sistem kuliah</th>
					</tr>
					<tr valign="top" class="NoHover">
						<td align="center">
							<b>Pilih Kelas :</b>&nbsp;<?=$l_jurusan?> <b>-</b> <?=$l_angkatan?> <b>-</b> <?= $l_kelas;?>
							&nbsp;
							<input type="button" class="ControlStyle" value="Tambah Mahasiswa Wali" onclick="goInsertPerkelas()">
							
						</td>
					</tr>
				</table>
				<br>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td colspan="2" align="center">Setting Mahasiswa Wali Secara Manual</td>
					</tr>
					<tr>
						<th>Masukkan Rentang NIM</th>
					</tr>
					<tr valign="top" class="NoHover">
						<td align="center">
							<strong>NIM: </strong> &nbsp; <?= UI::createTextBox('nim','','ControlStyle',12,10) ?> &nbsp;
							s.d. &nbsp; <?= UI::createTextBox('lastnim','','ControlStyle',12,10) ?> &nbsp;
							<input type="button" class="ControlStyle" value="Tambah Mahasiswa Wali" onclick="goInsert()">
							
						</td>
					</tr>
				</table>
				
				
				
				<br>
				<!--table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td align="center" colspan="2">Impor Data Mahasiswa dari Format Excel</td>
						
					</tr>
					<tr class="NoHover NoGrid">		
						<td width="55"> &nbsp; <strong>Upload </strong></td>
						<td>
							<strong> : </strong> <input type="file" name="xls" id="xls" size="50" class="ControlStyle">
							<input type="button" value="Upload" onclick="goUpXLS()"> &nbsp; &nbsp; &nbsp;
							<u class="ULink" onclick="goDownXLS()">Download Template Excel...</u>
						</td>
						
					</tr>
				</table-->
				</center>
				<br>
				<?	} ?>
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
						<?	}
							if($c_edit or $c_delete) { ?>
						<th width="50">Aksi</th>
						<?	} ?>
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
						<?	foreach($rowc as $j => $rowcc) {
								$t_align = $a_kolom[$j]['align'];
								if(!empty($t_width))
									$t_align = ' align="'.$t_align.'"';
						?>
						<td<?= $t_align ?>><?= $rowcc ?></td>
						<?	}
							if($c_edit or $c_delete) { ?>
						<td align="center">
						<?		if($c_edit) { ?>
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
						<?		}
								if($c_delete) { ?>
							<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer">
						<?		} ?>
						</td>
						<?	} ?>
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
				<input type="hidden" name="key" id="key">
				<input type="hidden" id="dosen" name="dosen" value="<?= $r_dosen ?>">
			</form>
		</div>
	</div>
</div>
<?=$r_dosen."day"?>
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
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	$("#dosen2").xautox({strpost: "f=acdosen", targetid: "dosen"});
});
function cekDoli(){
	var nip='<?=$r_dosen?>';
	if(nip==''){
		alert('Mohon Pilih Dosen Wali dan klik Tombol "Tampilkan"');
		return false;
	}else
		return true;
		
}
function goInsertPerkelas() {
	
	if(cekDoli()){
	document.getElementById("act").value = "insertperkelas";
	goSubmit();
	}
	
}
function goInsert() {
	
	if(cekDoli()){
	document.getElementById("act").value = "insert";
	goSubmit();
	}
	
}
function goUpXLS() {
	
	if(cekDoli()){
	document.getElementById("act").value = "uploadxls";
	goSubmit();
	}
}

function goDownXLS() {
	document.getElementById("act").value = "downxls";
	goSubmit();
}
</script>
</body>
</html>
