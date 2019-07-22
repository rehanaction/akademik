<?php  
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('perencanaankuliah'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_pkey = CStr::removeSpecial($_REQUEST['pkey']);
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$ajax_key=!empty($r_pkey)?$r_pkey:$r_key;
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Perencanaan Jurnal Perkuliahan';
	$p_tbwidth = 600;
	$p_aktivitas = 'ABSENSI';
	$p_listpage = 'list_jurnal';
	
	$p_model = mPerencanaanKuliah;
	
	// hak akses tambahan
	$a_authlist = true;
	$p_listpage .= '&key='.$r_pkey;
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_pkey) or (empty($r_key) and !$c_insert))
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'perkuliahanke', 'label' => 'Pertemuan Ke', 'size' => 2, 'maxlength' => 2, 'notnull' => true);
	$a_input[] = array('kolom' => 'tglkuliah', 'label' => 'Tanggal', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'waktumulai', 'label' => 'Jam Mulai', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam');
	$a_input[] = array('kolom' => 'waktuselesai', 'label' => 'Jam Selesai', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam');
	$a_input[] = array('kolom' => 'nohari', 'skip' => true);
	$a_input[] = array('kolom' => 'koderuang', 'label' => 'Ruang Kuliah', 'type' => 'S', 'option' => mCombo::ruang($conn));
	$a_input[] = array('kolom' => 'nipdosen', 'label' => 'Dosen Pengajar', 'type' => 'S', 'option' => mKelas::dosenPengajar($conn,$r_pkey), 'add' => 'style="width:150px"');
	$a_input[] = array('kolom' => 'jeniskuliah', 'label' => 'Jenis Pertemuan', 'type' => 'S', 'option' => $p_model::jenisKuliah($conn),'add'=>'onChange="setKelompok()"');
	$a_input[] = array('kolom' => 'kelompok', 'label'=>'Kelompok','type' => 'S', 'option' =>mCombo::kelompokKelas($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'topikkuliah', 'label' => 'Rencana Materi', 'type' => 'A', 'rows' => 5, 'cols' => 50, 'maxlength' => 200,'notnull'=>true);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Materi/Kegiatan', 'type' => 'A', 'rows' => 5, 'cols' => 50, 'maxlength' => 255,'notnull'=>true);
	$a_input[] = array('kolom' => 'kesandosen', 'label' => 'Kesan Dosen', 'type' => 'A', 'rows' => 5, 'cols' => 50, 'maxlength' => 100);
	$a_input[] = array('kolom' => 'filemateri', 'label' => 'File Materi', 'type' => 'U', 'uptype' => $p_model::uptype, 'size' => 40);

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		$record['waktumulai'] = CStr::cStrNull(str_replace(':','',$_REQUEST['waktumulai']));
		$record['waktuselesai'] = CStr::cStrNull(str_replace(':','',$_REQUEST['waktuselesai']));
		
		$record['nohari'] = date('N',Date::dateToTime($record['tglkuliah']));
		//print_r($record);die();
		if(empty($r_key)) {
			$record += mKelas::getKeyRecord($r_pkey);
			
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		}
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'deletefile' and $c_edit)
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key,'filemateri');
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	$a_infokelas = mKelas::getDataSingkat($conn,$r_pkey);
	// var_dump($row);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
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
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<?	/*****************/
					/* TOMBOL-TOMBOL */
					/*****************/
					
					if(empty($p_fatalerr))
						require_once('inc_databutton.php');
				?>
				<center>
				<?php require_once('inc_headerkelas.php') ?>
				</center>
				<br>
				<?	if(!empty($p_postmsg)) { ?>
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
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
					<?	$a_required = array();
						foreach($row as $t_row) {
							if($t_row['notnull'])
								$a_required[] = $t_row['id'];
							if($t_row['skip'])
								continue;
					?>
						<tr>
							<td class="LeftColumnBG" width="150" style="white-space:nowrap">
								<?= $t_row['label'] ?>
								<?= $t_row['notnull'] ? '<span id="edit" style="display:none">*</span>' : '' ?>
							</td>
							<td class="RightColumnBG">
								<span id="show"><?= $t_row['value'] ?></span>
								<?if($t_row['label']=='Jam Mulai'){?>
									<span id="edit" style="display:none">
										<input type="text" size="3" maxlength="4" class="ControlStyle" value="<?= str_pad($t_row['realvalue'],4,'0',STR_PAD_LEFT)?>" id="waktumulai" name="waktumulai">
									</span>
								<?}else{?>
									<span id="edit" style="display:none"><?= $t_row['input'] ?></span>
								<?}?>
							</td>
						</tr>
					<?	} ?>
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="pkey" id="pkey" value="<?= $r_pkey ?>">
				<?	} ?>
			</form>
		</div>
	</div>
</div>
	<script src="scripts/jquery-1.7.1.min.js" type="text/javascript" charset="utf-8"></script>	
	<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
	<script type="text/javascript">
	    $(function() {
		$("#waktumulai").mask("99:99");
		$("#waktuselesai").mask("99:99");
		
    });
	</script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
		
	$("img").not("[src]").each(function() {
		this.src = "index.php?page=img_datathumb&type="+this.id+"&id="+document.getElementById("key").value;
	});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	setKelompok();
});
function setKelompok(){
	var jenis=$("#jeniskuliah").val();
	var kelompok=$("#kelompok").val();
	 if(jenis=='K'){
		 var posted = "f=setKelompokJ&q[]=<?=$ajax_key?>&q[]=K&q[]="+kelompok;
				$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
					$("#kelompok").html(text);
				});
	}else if(jenis=='P'){
		var posted = "f=setKelompokJ&q[]=<?=$ajax_key?>&q[]=P&q[]="+kelompok;
				$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
					$("#kelompok").html(text);
				});
	}else{
		$(".kelompok").html('');
	}
}
</script>
</body>
</html>
