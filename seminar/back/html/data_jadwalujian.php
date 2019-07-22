<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('jadwalujian'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	//$conn->debug=false;
	// properti halaman
	$p_title = 'Data Jadwal Ujian Seleksi';
	$p_tbwidth = 700;
	$p_aktivitas = 'Kelas';
	$p_listpage = Route::getListPage();
	
	$p_model = mJadwalUjian;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$ruang=$p_model::getRuang($conn);
	$kota=$p_model::getKota($conn);
	$jalur=mCombo::jalur($conn);
	$r_act = $_POST['act'];

	$aktif=array('-1'=>'Aktif','0'=>'Tidak Aktif');
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'tgltes', 'label' => 'Tanggal Ujian', 'type' => 'D','notnull' => true);
	$a_input[] = array('kolom' => 'kuota', 'label' => 'Total Kuota','notnull' => true);
	$a_input[] = array('kolom' => 'isaktif', 'label' => 'Status', 'type' => 'S', 'option' => $aktif);
	//kolom detail
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodekota', 'label' => 'Kota', 'type'=>'S', 'option' => $kota,'add' => 'onchange="loadRuang()"','empty'=>true);
	$a_kolom[] = array('kolom' => 'koderuang', 'label' => 'Ruang', 'type'=>'S', 'option' => $ruang,'empty'=>true);
	$a_kolom[] = array('kolom' => 'jalurpenerimaan', 'label' => 'Jalur', 'type'=>'S', 'option' => $jalur);
	$a_kolom[] = array('kolom' => 'jammulai','label' => 'Jam Mulai', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam');
	$a_kolom[] = array('kolom' => 'jamselesai', 'label' => 'Jam Selesai','maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam');
	//$a_kolom[] = array('kolom' => 'jammulai', 'label' => 'Jam Mulai', 'size' => 10, 'maxlength' => 10,'format' => 'CStr::formatJam');
	//$a_kolom[] = array('kolom' => 'jamselesai', 'label' => 'Jam Selesai', 'size' => 10, 'maxlength' => 10,'format' => 'CStr::formatJam');
	// ada aksi
	if($r_act == 'save' and $c_edit) { 
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		

		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'indetail' and $c_edit) {
		//print_r($_POST);die();
		$record=array();
		$record['koderuang']=CStr::cStrNull($_POST['i_koderuang']);
		$record['kodekota']=CStr::cStrNull($_POST['i_kodekota']);
		$record['jalurpenerimaan']=CStr::cStrNull($_POST['i_jalurpenerimaan']);
		$record['jammulai']=CStr::cStrNull(str_replace(':','',$_POST['i_jammulai']));
		$record['jamselesai']=CStr::cStrNull(str_replace(':','',$_POST['i_jamselesai']));
		$record['idjadwal']=$r_key;
		
		list($p_posterr,$p_postmsg) = $p_model::insertJadwal($conn,$record);
	}
	else if($r_act == 'deletedetail' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteJadwal($conn,$r_subkey);
	}
	else if($r_act == 'editdetail' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
		$r_edit=$r_subkey;
		//list($p_posterr,$p_postmsg) = $p_model::deleteJadwal($conn,$r_subkey);
	}
	else if($r_act == 'updatedetail' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
		$record=array();
		$record['koderuang']=CStr::cStrNull($_POST['u_koderuang']);
		$record['kodekota']=CStr::cStrNull($_POST['u_kodekota']);
		$record['jalurpenerimaan']=CStr::cStrNull($_POST['u_jalurpenerimaan']);
		$record['jammulai']=CStr::cStrNull(str_replace(':','',$_POST['u_jammulai']));
		$record['jamselesai']=CStr::cStrNull(str_replace(':','',$_POST['u_jamselesai']));
		list($p_posterr,$p_postmsg) = $p_model::updateJadwal($conn,$record,$r_subkey);
	}
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	if(!empty($r_key))
		$a_data=$p_model::getDetailJadwal($conn,$r_key);
		//$a_data = $p_model::getListData($conn,$a_kolom,$r_sort);
	
	//print_r($a_data);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	 <script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="scripts/common.js"></script>
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>

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
				<?	}
					
					/*****************/
					/* TOMBOL-TOMBOL */
					/*****************/
					
					if(empty($p_fatalerr))
						require_once('inc_databutton.php');
					
					if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	}
				
					if(empty($p_fatalerr)) { ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
					<?	/********/
						/* DATA */
						/********/
						
						$a_required = array('kodemk');
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG">Tanggal Tes</td>
							<td class="RightColumnBG">
							<?= Page::getDataInput($row,'tgltes') ?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG">Total Kuota</td>
							<td class="RightColumnBG">
							<?= Page::getDataInput($row,'kuota') ?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG">Status</td>
							<td class="RightColumnBG">
							<?= Page::getDataInput($row,'isaktif') ?>
							</td>
						</tr>
						<tr>
							<td class="DataBG" colspan="2">Detail Jadwal</td>
						</tr>
						
						<? if(!empty($r_key)) { ?>
						<tr>
							<td colspan="2">
								<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
									<?	/**********/
										/* HEADER */
										/**********/
									?>
									<tr>
										
										<th id="koderuang">Kota</th>
										<th id="koderuang">Ruang</th>
										<th id="koderuang">Jalur</th>
										<th id="jammulai">Jam Mulai</th>
										<th id="jamselesai">Jam Selsai</th>
										<?	
											if($c_edit or $c_delete) { ?>
										<th width="50">Edit</th>
										<th width="50">Hapus</th>
										<?	} ?>
									</tr>
									<?	/********/
										/* ITEM */
										/********/
										
										$i = 0;
										foreach($a_data as $row) {
											if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
											$t_key = $row['idjadwaldetail'];
											
											if($t_key == $r_edit and $c_edit) {
												$rowc = Page::getColumnEdit($a_kolom,'u_','onkeydown="etrUpdate(event)"',$row);
												
												$a_updatereq = array();
									?>
									<tr align="center" valign="top" class="AlternateBG2">
										<?		foreach($rowc as $rowcc) {
													if($rowcc['notnull'])
														$a_updatereq[] = $rowcc['id'];
										?>					
										<td><?= $rowcc['input'] ?></td>
										<?		} ?>
										<td align="center" colspan=2>
											<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onclick="goUpdateDetail(this)" style="cursor:pointer">
										</td>
									</tr>
									<?		}
											else {
												$rowc = Page::getColumnRow($a_kolom,$row);
									?>
									<tr align="center" valign="top" class="<?= $rowstyle ?>">
										<?		foreach($rowc as $rowcc) { ?>					
										<td><?= $rowcc ?></td>
										<?		}
												if($c_edit or $c_delete) { ?>
										<td align="center">
										<?			if($c_edit) { ?>
											<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goEditDetail(this)" style="cursor:pointer">
										<?			}
										?>
										</td>
										<td align="center">
										<?			if($c_delete) { ?>
											<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDeleteDetail(this)" style="cursor:pointer">
										<?			} ?>
										</td>
										<?		} ?>
									</tr>
									<?		}
										}
										if($i == 0) {
									?>
									<tr>
										<td colspan="5" align="center">Data kosong</td>
									</tr>
									<?	}
										if($c_insert) { ?>
									<tr align="center" valign="top" class="LeftColumnBG NoHover">
										<?	$rowc = Page::getColumnEdit($a_kolom,'i_','onkeydown="etrInsert(event)"');
											
											$a_insertreq = array();
											foreach($rowc as $rowcc) {
												if($rowcc['notnull'])
													$a_insertreq[] = $rowcc['id'];
										?>					
										<td><?= $rowcc['input'] ?></td>
										<?	} ?>
										<td align="center" colspan=2>
											<img title="Tambah Data" src="images/disk.png" onclick="goInDetail()" style="cursor:pointer">
										</td>
									</tr>
									<?	} ?>
								</table>
							</td>
						</tr>
						<? }?>
						</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey">
				<?	} ?>
			</form>
		</div>
	</div>
</div>
<script src="scripts/jquery-1.7.1.min.js" type="text/javascript" charset="utf-8"></script>	
<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
<script>
 
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var ajax = "<?= Route::navAddress("ajax") ?>";
var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	//initEdit(false);
	
	loadRuang();
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
		
});
$(function() {
        $.mask.definitions['~'] = "[+-]";
		$("#i_jammulai").mask("99:99");
		$("#u_jammulai").mask("99:99");
		$("#i_jamselesai").mask("99:99");
		$("#u_jamselesai").mask("99:99");
		
		
    });
function goDeleteDetail(elem) {
	if (confirm("yakin akan menghapus data ?")){
	document.getElementById("act").value = "deletedetail";
	document.getElementById("subkey").value = elem.id;
	goSubmit();
	}
}
function goEditDetail(elem) {
	document.getElementById("act").value = "editdetail";
	document.getElementById("subkey").value = elem.id;
	goSubmit();
	
}
function goUpdateDetail(elem) {
	document.getElementById("act").value = "updatedetail";
	document.getElementById("subkey").value = elem.id;
	goSubmit();
	
}
function goInDetail() {
	document.getElementById("act").value = "indetail";
	goSubmit();
}
// ajax ganti ruang
function loadRuang() {
<?php
	$conn->debug=false;
    $kota = $p_model::Kota($conn);
    while ($data = $kota->FetchRow())
    {
	$idKota = $data['kodekota'];
	
	echo "if (document.pageform.i_kodekota.value == \"".$idKota."\")";
	echo "{";

	$ruang = $p_model::Ruang($conn,$idKota);
	   // $kota = array_values($kota);
	$content = "document.getElementById('i_koderuang').innerHTML = \"";
	while($dataruang= $ruang->FetchRow())
	{
		$content .= "<option value='".$dataruang['koderuang']."'>".$dataruang['ruang']."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
	if(!empty($r_edit)){
		echo "if (document.pageform.u_kodekota.value == \"".$idKota."\")";
		echo "{";
		
		$ruang = $p_model::Ruang($conn,$idKota);
		   // $kota = array_values($kota);
		$content = "document.getElementById('u_koderuang').innerHTML = \"";
		while($dataruang= $ruang->FetchRow())
		{
			$content .= "<option value='".$dataruang['koderuang']."'>".$dataruang['ruang']."</option>";
		}
		$content .= "\"";
		echo $content;
		echo "}\n";
	}
    }
?>
}
</script>
</body>
</html>
