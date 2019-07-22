<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_readlist = true;		
	$c_other = $a_auth['canother'];
	$c_kepeg = $c_other['K'];
	$c_valid = $c_other['V'];
	
	// include
	require_once(Route::getModelPath('angkakredit'));
	require_once(Route::getModelPath('integrasi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
		
	// variabel request
	if(SDM::isPegawai()) {
		$r_self = 1;
		$c_kepeg = true;
	}
	
	if($c_kepeg){
		$c_insert = $a_auth['caninsert'];
		$c_edit = $a_auth['canupdate'];
		$c_delete = $a_auth['candelete'];
	}
	
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
	
	$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
	
	// properti halaman
	$p_title = 'Data Bidang IB (Pengajaran)';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mAngkaKredit;
	$p_dbtable = "ak_bidang1b";
	$where = 'nobidangib';
		
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'tahun1', 'label' => 'Periode', 'maxlength' => 4, 'size' => 4, 'add' => 'onkeyup="cekLength()"');
	$a_input[] = array('kolom' => 'tahun2', 'maxlength' => 4, 'size' => 4, 'add' => 'onkeyup="cekLength()"');
	$a_input[] = array('kolom' => 'semester', 'label' => 'Semester', 'type' => 'S', 'option' => $p_model::PeriodeSemester());
	$a_input[] = array('kolom' => 'namakegiatan', 'label' => 'Nama Kegiatan', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255, 'notnull' => true);
	$a_input[] = array('kolom' => 'pada', 'label' => 'Pada', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'tempat', 'label' => 'Tempat', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	
	$a_input[] = array('kolom' => 'sks', 'label' => 'Sks', 'maxlength' => 2, 'size' => 2, 'type' => 'N', 'add' => 'onkeyup="cekSKS()"');
	$a_input[] = array('kolom' => 'ismengajar', 'label' => 'Mengajar?', 'type' => 'C', 'option' => array('Y' => ''), 'add' => 'onclick="cekSKS()" title="Centang bila kegiatan perkuliahan/ pengajaran"');
	$a_input[] = array('kolom' => 'sksdiakui', 'type' => 'H');
	$a_input[] = array('kolom' => 'sksdiakui2', 'type' => 'H');
	$a_input[] = array('kolom' => 'idkegiatan2', 'type' => 'H');
		
	$a_input[] = array('kolom' => 'tglawal', 'label' => 'Tgl. Mulai','type' => 'D', 'maxlength' => 10, 'size' => 10, 'notnull' => true);
	$a_input[] = array('kolom' => 'tglakhir', 'label' => 'Tgl. Selesai','type' => 'D', 'maxlength' => 10, 'size' => 10);
	$a_input[] = array('kolom' => 'nim', 'label' => 'NIM', 'maxlength' => 10, 'size' => 10);
	$a_input[] = array('kolom' => 'namamhs', 'label' => 'Nama Mahasiswa', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'tgllegalitas', 'label' => 'Tgl. Legalitas','type' => 'D', 'maxlength' => 10, 'size' => 10);
	$a_input[] = array('kolom' => 'nolegalitas', 'label' => 'No. legalitas', 'maxlength' => 50, 'size' => 30);	
	$a_input[] = array('kolom' => 'kegiatan', 'label' => 'Indeks Angka Kredit', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255, 'notnull' => true, 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'idkegiatan', 'type' => 'H');
	$a_input[] = array('kolom' => 'kreditmax', 'label' => 'Kredit Max', 'maxlength' => 5, 'size' => 5, 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'nilaikredit', 'label' => 'Kredit Dihitung', 'readonly' => true);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	
	if($c_valid)
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	else
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid(), 'readonly' => true);	
		
	$a_input[] = array('kolom' => 'statusvalidasi', 'label' => 'Status Validasi', 'readonly' => true);
	$a_input[] = array('kolom' => 'tglvalidasi', 'label' => 'Tgl. Validasi','type' => 'D', 'readonly' => true);
	$a_input[] = array('kolom' => 'isfinal', 'label' => 'Final', 'type' => 'R', 'readonly' => true, 'option' => $p_model::isFinal());
	$a_input[] = array('kolom' => 'filebidangsatub', 'label' => 'File Bidang IB', 'type' => 'U', 'uptype' => 'filebidangsatub', 'size' => 40);
		
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		$record['idpegawai'] = $r_key;

		if($record['tahun1'] != 'null' and $record['tahun2'] != 'null')
			$record['thnakademik'] = $record['tahun1'].$record['tahun2'];
		
		$record['stdkredit'] = $record['kreditmax'];
		if($record['sksdiakui'] != 'null' and $record['ismengajar'] == 'Y')
			$record['nilaikredit'] = $p_model::hitungKredit($conn,$record['idkegiatan'],$record['sksdiakui']);
		else
			$record['nilaikredit'] = $record['stdkredit'];
		
		$conn->BeginTrans();
		
		if(empty($r_subkey))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
		
		//simpan pecahan sks
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::savePecahan($conn,$r_subkey,$record['idkegiatan2'],$record['sksdiakui2']);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
			
		if($ok) 
			unset($post);
		else
			Route::setFlashDataPost($post);
		?>
		
		<html>
			<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
			<script type="text/javascript" src="scripts/jquery.common.js"></script>
			<script type="text/javascript" src="scripts/commonx.js"></script>
			<script type="text/javascript" src="scripts/foreditx.js"></script>
			<script type="text/javascript">
				var xlist = "<?= Route::navAddress(Route::thisPage()) ?>";
				var sent = "key=<?= $r_key ?>&subkey=<?= $r_subkey ?>&err=<?= $p_posterr?>&msg=<?= $p_postmsg?>";
				window.parent.parent.$("#contents").divpost({page: xlist, sent: sent});
			</script>
		</html>
		<?php
		exit();
	}
	else if($r_act == 'delete' and $c_delete) {
		$conn->BeginTrans();
		
		//mengembalikan isinput
		list($p_posterr,$p_postmsg) = $p_model::backIsInput($conn,$r_subkey);
		
		//cek pecahan
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::deletePecahan($conn,$r_subkey);
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where,'','filebidangsatub');
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
		
		if($ok) Route::navListpage($p_listpage,$r_key);
	}
	else if($r_act == 'deletefile' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_subkey,$p_dbtable,'filebidangsatub',$where);				
		?>
		
		<html>
			<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
			<script type="text/javascript" src="scripts/jquery.common.js"></script>
			<script type="text/javascript" src="scripts/commonx.js"></script>
			<script type="text/javascript" src="scripts/foreditx.js"></script>
			<script type="text/javascript">
				var xlist = "<?= Route::navAddress(Route::thisPage()) ?>";
				var sent = "key=<?= $r_key ?>&subkey=<?= $r_subkey ?>&err=<?= $p_posterr?>&msg=<?= $p_postmsg?>";
				window.parent.parent.$("#contents").divpost({page: xlist, sent: sent});
			</script>
		</html>
		<?php
		exit();	
	}
		
	$p_postmsg = !empty($_REQUEST['msg']) ? $_REQUEST['msg'] : $p_postmsg;
	$p_posterr = !empty($_REQUEST['err']) ? $_REQUEST['err'] : $p_posterr;
	if($p_posterr)
		$post = Route::getFlashDataPost();
	
	$sql = $p_model::getDataEditBidangIB($r_subkey);	
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$where,$sql);
		
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
			
		//pengecekan hak akses utk pegawai ybs, bila sudah valid
		if($t_row['id'] == 'isvalid'){
			$isvalid = $t_row['value'];
			if($isvalid == 'Ya' and $r_self){
				$c_edit = false;
				$c_delete = false;
			}
		}
		
		//nama kegiatan
		if($t_row['id'] == 'namakegiatan')
			$namakegiatan = $t_row['value'];
		if($t_row['id'] == 'sksdiakui')
			$sksdiakui = $t_row['value'];
			
		//ismengajar
		if($t_row['id'] == 'ismengajar')
			$ismengajar = $t_row['realvalue'];
	}
	
	//cek bila form ini adalah pecahan sks dari lain, maka tidak bisa edit\
	if(!empty($r_subkey)){
		$ispecahan = $p_model::isPecahan($conn,$r_subkey);
		if($ispecahan){
			$c_edit = false;
			$c_delete = false;
		}
	}
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foreditx.js"></script>
</head>
<body>
	<table width="100%">
		<tr>
			<td>
			<form name="pageform" id="pageform" method="post" action="<?= Route::navAddress(Route::thisPage()) ?>" enctype="multipart/form-data">
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
						require_once('inc_databuttonajax.php');
					
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
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<?if($ispecahan){?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Keterangan</td>
							<td class="RightColumnBG" colspan="3"><font color="red"><b>Tidak bisa edit, karena form ini hasil pecahan sks dari kegiatan/ mata kuliah : <?= $namakegiatan?></b></font></td>
						</tr>
						<?}?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tahun1') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tahun1') ?> / <?= Page::getDataInput($row,'tahun2') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'semester') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'semester') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namakegiatan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'namakegiatan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pada') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'pada') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tempat') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'tempat') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'sks') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'sks') ?>
								<?= (!empty($r_subkey) and $ismengajar == 'Y') ? '&nbsp;&nbsp;&nbsp; SKS Diakui : '.$sksdiakui : '';?>
								<?= Page::getDataInput($row,'sksdiakui') ?>
								<?= Page::getDataInput($row,'sksdiakui2') ?>
								<?= Page::getDataInput($row,'idkegiatan2') ?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'ismengajar') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'ismengajar') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglawal') ?></td>
							<td  class="RightColumnBG" width="40%"><?= Page::getDataInput($row,'tglawal') ?></td>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglakhir') ?></td>
							<td  class="RightColumnBG" width="20%"><?= Page::getDataInput($row,'tglakhir') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nim') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'nim') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namamhs') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'namamhs') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tgllegalitas') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tgllegalitas') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nolegalitas') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'nolegalitas') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kegiatan') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'kegiatan') ?>
								<?= Page::getDataInput($row,'idkegiatan') ?>
								<span id="edit" style="display:none;"><img src="images/magnify.png" title="Pilih indeks kegiatan" style="cursor:pointer" onclick="showIndeks()"></span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kreditmax') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'kreditmax') ?>
								<?
									if(!empty($r_subkey)){
										echo '<b>&nbsp;&nbsp;&nbsp;';
										echo Page::getDataLabel($row,'nilaikredit').' : '.Page::getDataInput($row,'nilaikredit').'</b>';
									}
								?>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'keterangan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'keterangan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isvalid') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isvalid') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'statusvalidasi') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'statusvalidasi') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglvalidasi') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tglvalidasi') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isfinal') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isfinal') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'filebidangsatub') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'filebidangsatub') ?></td>
						</tr>
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey" value="<?= $r_subkey; ?>">
				
				<?	} ?>
			</form>
		</td>
	</tr>
</table>

<iframe name="upload_iframe" style="display:none"></iframe>

<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var required = "<?= @implode(',',$a_required) ?>";
var xtdid = "contents";

$(document).ready(function() {	
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goSave(){
	var thn1 = $("#tahun1").val();
	var thn2 = $("#tahun2").val();
	var tgl = $("#tglawal").val();
	tgl = tgl.split('-');
	
	if(tgl[2] == thn1 || tgl[2] == thn2){
		var pass = true;
		if(typeof(required) != "undefined") {
			if(!cfHighlight(required))
				pass = false;
		}		

		if(pass) {
			document.getElementById("pageform").target = "upload_iframe";
			document.getElementById("act").value = "save";
			document.getElementById("pageform").submit();
		}
	}else{
		doHighlight(document.getElementById("tahun1"));
		doHighlight(document.getElementById("tahun2"));
		doHighlight(document.getElementById("tglawal"));
		alert('Tgl mulai tidak sesuai dengan periode');
	}
}

function showIndeks(){
	win = window.open("<?= Route::navAddress('pop_penilaian').'&m=2&b=IB'?>","popup_penilaian","width=650,height=500,scrollbars=1");
	win.focus();
}

function cekSKS(){
	var pass = true;
	var iscek = $("#ismengajar_Y").is(":checked");
	var sks = $("#sks").val();
	var thn = $("#tahun1").val()+$("#tahun2").val();
	var smtr = $("#semester").val();
	$("#sksdiakui").val('');
	$("#idkegiatan").val('');
	$("#kegiatan").val('');
	$("#kreditmax").val('');
	
	$("#sksdiakui2").val('');
	$("#idkegiatan2").val('');
	
	if(iscek){
		if($("#tahun1").val() == ''){
			doHighlight(document.getElementById("tahun1"));
			alert('Isi terlebih dahulu periode');
			pass = false;
		}
		if($("#tahun2").val() == ''){
			doHighlight(document.getElementById("tahun2"));
			alert('Isi terlebih dahulu periode');
			pass = false;
		}
		if(sks == ''){
			doHighlight(document.getElementById("sks"));
			alert('Isi terlebih dahulu jumlah sksnya');
			pass = false;
		}
		
		if(pass){
			var posted = "f=gsksbidang&q[]=<?= $r_key?>&q[]="+sks+"&q[]="+thn+"&q[]="+smtr;
			$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
				var val = text.split('|');
				
				if(val[0] == '0'){
					$("#sksdiakui").val(val[1]);
					$("#idkegiatan").val(val[2]);
					$("#kegiatan").val(val[3]);
					$("#kreditmax").val(val[4]);
					
					$("#sksdiakui2").val(val[5]);
					$("#idkegiatan2").val(val[6]);
				}else{
					alert("Ma'af, sks yang anda inputkan sudah melebihi 12 sks");
					goList();
				}
			});
		}
	}
}

function cekLength(){
	var thn1 = $("#tahun1").val();
	var thn2 = $("#tahun2").val();	

	if(thn1.length == 4 && thn2.length == 4)
		cekSKS();
}
</script>
</body>
</html>
