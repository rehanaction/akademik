<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Lembur Kolektif';
	$p_tbwidth = 900;
	$p_aktivitas = 'TIME';
	$p_dbtable = 'pe_suratlemburkol';
	$p_key = 'idsuratkol';
	$p_listpage = Route::getListPage();
	
	$p_model = mPresensi;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'tglpenugasan', 'label' => 'Tgl. Penugasan', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit Lembur', 'type' => 'S', 'empty' => true, 'notnull' => true, 'option' => mCombo::unitSave($conn,false));
	$a_input[] = array('kolom' => 'pejabat', 'label' => 'Pejabat Pemberi Tugas', 'maxlength' => 255, 'size' => 100, 'notnull' => true);
	$a_input[] = array('kolom' => 'pejabatatasan', 'type' => 'H');
	$a_input[] = array('kolom' => 'idjstruktural', 'type' => 'H');
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = CStr::removeSpecial($_POST['actdet']);
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key,true);
		else{
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
			
			//update juga detail lemburnya
			$recmast = array();
			$recmast = $p_model::getDataKol($conn,$r_key);
			
			if(count($recmast) > 0 and !$p_posterr){
				$where = "refidkolektif";
				list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$recmast,$r_key,true,'pe_suratlembur',$where);			
			}
		}
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		//hapus dulu detailnya
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'pe_suratlembur','refidkolektif');
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_actdet == 'savedet' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		$recmast = array();
		$recmast = $p_model::getDataKol($conn,$r_key);
		
		$record = array();
		$record['refidkolektif'] = $r_key;
		$record['idpegawai'] = CStr::cStrNull($_POST['idpegawai']);
		$record['tgllembur'] = CStr::formatDate($_POST['tgllembur']);
		$record['jamawal'] = CStr::cStrNull($_POST['jamawal']);
		$record['jamakhir'] = CStr::cStrNull($_POST['jamakhir']);
		$record['lokasi'] = CStr::cStrNull($_POST['lokasi']);
		
		if(count($recmast) > 0)
			$record = array_merge($record,$recmast);
			
		if(empty($r_subkey)){
			list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,'pe_suratlembur');
		}else{
			$where = "idsuratlembur";
			list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_subkey,true,'pe_suratlembur',$where);			
		}
		
		if(!$p_posterr){
			unset($post);
		}
	}
	else if($r_act == 'deletedet' and $c_delete) {
		$r_keydet = CStr::removeSpecial($_POST['keydet']);
		$r_subkey = $r_keydet;
		$where = "idsuratlembur";
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,'pe_suratlembur',$where);
	}
	else if($r_act == 'validasi' and $c_edit) {
		for($i=0;$i<count($_POST['kode']);$i++){
			$record = array();
			$r_subkey = CStr::removeSpecial($_POST['kode'][$i]);
			$record['isvalid'] = CStr::cStrNull($_POST['isvalid'.$r_subkey]);
			
			$where = "idsuratlembur";
			list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_subkey,true,'pe_suratlembur',$where);			
		}
		
		if(!$p_posterr){
			unset($post);
		}
	}
	
	$sql = $p_model::getDataEditLemburKol($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key,$sql);
	
	//select detail lembur
	if(!empty($r_key))
		$a_data = $p_model::getListLemburDetail($conn,$r_key);
		
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
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
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
	<link href="style/jquery.autocomplete.css" rel="stylesheet" type="text/css">
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
				<?					
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
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td width="150px" class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglpenugasan');?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tglpenugasan');?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'idunit');?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'idunit');?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pejabat');?></td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'pejabat');?>
								<?= Page::getDataInput($row,'pejabatatasan');?>
								<?= Page::getDataInput($row,'idjstruktural');?>
							</td>
						</tr>
					</table>
					
					<? if (!empty($r_key)) {?>
					<br />
					<span id="show"></span>
					<span id="edit" style="display:none">
						<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
							<tr>
								<td>
									<input type="button" name="badd" id ="badd" value="Tambah Detail" class="ControlStyle" onClick="openDetail('<?= $r_key ?>','')" />&nbsp;
									<input type="button" name="bval" id ="bval" value="Validasi" class="ControlStyle" onClick="goValidasi()" />
								</td>
							</tr>
						</table>
					</span>
					
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
						<tr>
							<td colspan="6" class="DataBG" width="50" align="center">Detail Lembur</td>
						</tr>
						<tr>
							<th width="50">NIP</td>
							<th>Nama Pegawai</td>
							<th>Tgl. Lembur</td>
							<th>Jam Lembur</td>
							<th>Lokasi</td>
							<th width="80">Aksi</td>
						</tr>
						<? if (count($a_data) > 0 ){
								$I=0;
								foreach($a_data as $col){
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
						?>
						<tr class="<?= $rowstyle ?>">
							<td align="center"><?= $col['nik'] ?></td>
							<td><?= $col['namalengkap'] ?></td>
							<td align="center"><?= CStr::formatDateInd($col['tgllembur']) ?></td>
							<td align="center"><?= $col['jamawal'].' - '.$col['jamakhir'] ?></td>
							<td><?= $col['lokasi'] ?></td>
							<td align="center">
								<span id="show"><?= $col['isvalid'] == 'Y' ? '<img src="images/check.png" title="Sudah valid">' : '';?></span>
								<span id="edit" style="display:none">
									<img id="<?= $col['idsuratlembur']; ?>" style="cursor:pointer" onclick="openDetail('<?= $r_key ?>','<?= $col['idsuratlembur'] ?>')" src="images/edit.png" title="Tampilkan Detail">
									<img id="<?= $col['idsuratlembur']; ?>" style="cursor:pointer" onclick="goDeleteDet('<?= $col['idsuratlembur'] ?>')" src="images/delete.png" title="Hapus Data">
									<input title="Centang untuk validasi" type="checkbox" id="isvalid" value="Y" name="isvalid<?= $col['idsuratlembur']?>" <?= $col['isvalid'] == 'Y' ? 'checked' : ''?>>
									<input type="hidden" id="kode[]" name="kode[]" value="<?= $col['idsuratlembur']?>">
								</span>
							</td>
						</tr>
						<? 
								}
							}else{ ?>
						<tr>
							<td colspan="6" align="center">Data tidak ditemukan</td>
						</tr>
						<? } ?>
					<? } ?>
					</table>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="keydet" id="keydet">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript" src="scripts/jquery.autocomplete.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var detform = "<?= Route::navAddress('pop_lembur') ?>";
var ajaxpage = "<?= Route::navAddress('ajax') ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	// autocomplete
    $('#pejabat').autocomplete(
	    ajaxpage, 
	    {
		    parse: function(data){ 
			    var parsed = [];
			    for (var i=0; i < data.length; i++) {
				    parsed[i] = {
					    data: data[i],
					    value: data[i].pejabat // nama field yang dicari
				    };
			    }
			    return parsed;
		    },
		    formatItem: function(data,i,max){
			    var str = '';
			    str += '<div class="search_content">';
			    str += data.pejabat +'<br>';
			    str += '</div>';
			    return str;
		    },
		    extraParams: {
		        f:'acpejabatatasan'
		    },
		    width: 500,
		    dataType: 'json'
	    })
	    .result(
		    function(event,data,formated){
                $('#pejabat').val(data.pejabat).focus();
                $('#pejabatatasan').val(data.idpegawai);
                $('#idjstruktural').val(data.idjstruktural);
		    }
	    ).focus();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function openDetail(pkey, pkeydet){
    $.ajax({
        url: detform,
        type: "POST",
        data: {key : pkey, subkey : pkeydet},
        success: function(data){
            $.facybox(data);
        }
    });
}

function goDeleteDet(key){
	var hapus = confirm("Anda yakin untuk menghapus ukuran detail lembur ini ?");
	if (hapus){
		document.getElementById("act").value = 'deletedet';
		document.getElementById("keydet").value = key;
		goSubmit();
	}
}

function goValidasi(){
	var yakin = confirm("Anda yakin untuk validasi yang dicentang ?");
	if (yakin){
		document.getElementById("act").value = 'validasi';
		goSubmit();
	}
}

</script>
</body>
</html>
