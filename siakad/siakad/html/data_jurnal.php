<?php  
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];

	$c_bypass = false;
	$valid_mhs = true;

	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('user'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getModelPath('ruang'));
	require_once(Route::getModelPath('asistenajar'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_pkey = CStr::removeSpecial($_REQUEST['pkey']);
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_jenis = 'P';
	$r_jenis = CStr::removeSpecial($_REQUEST['jenis']);
	$r_open = CStr::removeSpecial($_REQUEST['open']);
	
	if ($r_jenis=='R')
	$read = 'true';
	else
	$read2='true';
	
	
	$ajax_key=!empty($r_pkey)?$r_pkey:$r_key;
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Jurnal Perkuliahan';
	$p_tbwidth = "100%";
	$p_aktivitas = 'ABSENSI';
	$p_listpage = Route::getListPage();
	
	$p_model = mKuliah;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	$p_listpage .= '&key='.$r_pkey;
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_pkey) or (empty($r_key) and !$c_insert))
		Route::navigate($p_listpage);
	if(Akademik::isDosen() or Akademik::isPPA())
		$p_dosen=true;
	$status=array('0'=>'Tatap Muka','-1'=>'Online');	
	$kelompokkelas=mCombo::kelompokKelas($conn);
	
	//admin ingin update status pertemuan (online/offline) walaupun sudah selesai realisasi, permintaan pak Budi (Septyan)
	if(!$p_dosen)
		$read_isonline=false;
	else
		$read_isonline=$read;
		
		
	//struktur view 
	$a_input = array();
	$a_input[] = array('kolom' => 'perkuliahanke', 'label' => 'Pertemuan Ke', 'size' => 2, 'maxlength' => 2, 'notnull' => true, 'readonly'=>$read);
	$a_input[] = array('kolom' => 'koderuang', 'label' => '(Perencanaan) <b>Ruang Kuliah</b>', 'type' => 'S', 'option' => mCombo::ruang($conn), 'readonly'=>$read,'empty'=>true);
	$a_input[] = array('kolom' => 'tglkuliah', 'label' => '(Perencanaan) <b>Tanggal</b>', 'type' => 'D', 'notnull' => true, 'readonly'=>$read);
	$a_input[] = array('kolom' => 'waktumulai', 'label' => '(Perencanaan) <b>Jam Mulai</b>', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam', 'readonly'=>$read);
	$a_input[] = array('kolom' => 'waktuselesai', 'label' => '(Perencanaan) <b>Jam Selesai</b>', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam', 'readonly'=>$read);
	//$a_input[] = array('kolom' => 'nohari', 'skip'=>true);
	
	$a_input[] = array('kolom' => 'nipdosen', 'label' => '(Perencanaan) <b>Dosen Pengajar</b>', 'type' => 'S', 'option' => mKelas::dosenPengajar($conn,$r_pkey), 'add' => 'style="width:150px"', 'readonly'=>$read);
	$a_input[] = array('kolom' => 'jeniskuliah', 'label' => 'Jenis Pertemuan', 'type' => 'S', 'option' => $p_model::jenisKuliah($conn),'add'=>'onChange="setKelompok()"', 'readonly'=>$read);
	$a_input[] = array('kolom' => 'kelompok', 'label'=>'Kelompok','type' => 'S', 'option' =>$kelompokkelas, 'empty' => false, 'readonly'=>$read);
	$a_input[] = array('kolom' => 'topikkuliah', 'label' => 'Rencana Materi', 'type' => 'A', 'rows' => 5, 'cols' => 50, 'notnull'=>true);
	$a_input[] = array('kolom' => 'isonline', 'label' => 'Pelaksanaan Kuliah', 'type' => 'S', 'option' => $status,'readonly'=>$read_isonline);
	$a_input[] = array('kolom' => 'koderuangrealisasi', 'label' => '(Realisasi) <b>Ruang Kuliah</b>', 'type' => 'S', 'option' => mCombo::ruang($conn),'readonly'=>$p_dosen);
	
	
		$a_input[] = array('kolom' => 'tglkuliahrealisasi', 'label' => '(Realisasi) <b>Tanggal</b>', 'type' => 'D');
		$a_input[] = array('kolom' => 'waktumulairealisasi', 'label' => '(Realisasi) <b>Jam Mulai</b>', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam');
		$a_input[] = array('kolom' => 'waktuselesairealisasi', 'label' => '(Realisasi) <b>Jam Selesai</b>', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam');
		//$a_input[] = array('kolom' => 'noharirealisasi', 'skip'=>true);
		
		
		$a_input[] = array('kolom' => 'nipdosenrealisasi', 'label' => '(Realisasi) <b>Dosen Pengajar</b>', 'type' => 'S', 'option' => mKelas::dosenPengajar($conn,$r_pkey), 'add' => 'style="width:150px"','readonly'=>Akademik::isPPA());
		//$a_input[] = array('kolom' => 'nipasisten', 'label' => '(Realisasi) <b>Asisten</b>', 'type' => 'S', 'option' => mAsistenAjar::getAsistenPengajar($conn,$r_pkey),'empty'=>true, 'add' => 'style="width:150px"');
		$a_input[] = array('kolom' => 'keterangan', 'label' => '(Realisasi) Materi/Kegiatan', 'type' => 'A', 'rows' => 5, 'cols' => 50, 'notnull'=> false);
		$a_input[] = array('kolom' => 'kesandosen', 'label' => '(Realisasi) Catatan Dosen', 'type' => 'A', 'rows' => 5, 'cols' => 50, 'maxlength' => 1000);
	
	//$a_input[] = array('kolom' => '(Realisasi) filemateri', 'label' => 'File Materi', 'type' => 'U', 'uptype' => $p_model::uptype, 'size' => 40);

	$a_input[] = array('kolom' => 'statusperkuliahan', 'label' => 'Status', 'type' => 'S', 'option' => $p_model::statusKuliah($r_jenis), ($r_jenis=='R' and !empty($r_open)) ? '' : 'readonly'=>true,'readonly'=>Akademik::isPPA());

	//$a_input[] = array('kolom'=>'validmhs','label'=>'Validasi Mahasiswa','type'=>'C','option'=>array(t=>'Validasi Sudah dilakukan'));
	//$a_input[] = array('kolom'=>'nim','label'=>'Validator','readonly'=>true,'option'=>'S','option'=>$a_mahasiswa);

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {

		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['waktumulai'] = CStr::cStrNull(str_replace(':','',$record['waktumulai']));
		$record['waktuselesai'] = CStr::cStrNull(str_replace(':','',$record['waktuselesai']));
		if(isset($record['waktumulairealisasi']))
			$record['waktumulairealisasi'] = CStr::cStrNull(str_replace(':','',$record['waktumulairealisasi']));
		if(isset($record['waktuselesairealisasi']))
			$record['waktuselesairealisasi'] = CStr::cStrNull(str_replace(':','',$record['waktuselesairealisasi']));
		
		if(isset($record['tglkuliah']))
			$record['nohari'] = date('N',strtotime($record['tglkuliah']));
		if(isset($record['tglkuliahrealisasi']))
			$record['noharirealisasi'] = date('N',strtotime($record['tglkuliahrealisasi']));
		
		if(empty($r_key)) {
			$record += mKelas::getKeyRecord($r_pkey);
				if ($r_jenis <> 'R')
			$record['statusperkuliahan']='J';
			
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
			$r_key = $record['thnkurikulum'].'|'.$record['kodemk'].'|'.$record['kodeunit'].'|'.$record['periode'].'|'.$record['kelasmk']
										   .'|'.$record['perkuliahanke'].'|'.$record['tglkuliah'].'|'.$record['jeniskuliah'].'|'.$record['kelompok'];
		}
		else{
			$conn->beginTrans();
			$a_data = $p_model::getData($conn,$r_key);

			if($a_data['statusperkuliahan'] != 'S' and mUser::cekUserPassMahasiswa($conn,Cstr::removeSpecial($_POST['nim']),cstr::removeSpecial($_POST['password']))){
				$recordm['nim'] = Cstr::removeSpecial($_POST['nim']);
				list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$recordm,$r_key);
				if(!$p_posterr){
					$valid_mhs = true;
				}
			}

			if($p_dosen){
				if(Akademik::detIp() and !empty($r_open) and ($_POST['isonline']==0 or empty($_POST['isonline'])))
					list($p_posterr,$p_postmsg) = true;
					
				if(!$p_posterr and empty($r_open))
					list($p_posterr,$p_postmsg) = $p_model::cektgl($conn,$r_key);
					
				if(!$p_posterr and empty($r_open) and ($_POST['isonline']==0 or empty($_POST['isonline'])))
					list($p_posterr,$p_postmsg) = $p_model::cekWaktu($conn,$r_key);
			}

			if($record['statusperkuliahan'] == 'S' and !$c_bypass and !$valid_mhs and ($_POST['isonline']==0 or empty($_POST['isonline']))){
					$p_postmsg = " Validasi Mahasiswa dibutuhkan untuk menyelesaikan perkuliahan";
					$p_posterr = true;
			}
			
			if(!$p_posterr or ($p_dosen and $a_data['statusperkuliahan']=='S'))
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
			
			$conn->commitTrans(!$p_posterr);
			
		}
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'deletefile' and $c_edit)
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key,'filemateri');
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);

	$a_mahasiswa = mMahasiswa::getNama($conn,Page::getDataValue($row,'nim'));
	
	$a_infokelas = mKelas::getDataSingkat($conn,$r_pkey);
	// var_dump($row);
	$online=Page::getDataValue($row,'isonline');
	$statusperkuliahan=Page::getDataValue($row,'statusperkuliahan');

	//if(Akademik::detIp() and $p_dosen and $statusperkuliahan!='S' and ($online==0 or empty($online))){
        if ($p_dosen and $statusperkuliahan!='S' and ($online==0 or empty($online))){
		list($p_posterr,$p_postmsg) = $p_model::cekTgl($conn,$r_key);

                if(!$p_posterr)
                   list($p_posterr,$p_postmsg) = true;

                if(!$p_posterr and ($_POST['isonline']==0 or empty($_POST['isonline'])))
                   list($p_posterr,$p_postmsg) = $p_model::cekWaktu($conn,$r_key);
	        }
        if ($p_dosen and $statusperkuliahan!='S' and ($online==-1))
        {
                list($p_posterr,$p_postmsg) = $p_model::cekTglol($conn,$r_key);
        }
        if(($online==-1)){
	        unset($row[20]);
	        $temp = array_values($row);
	        $row = $temp;
    	}
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
					
					if(empty($p_fatalerr)){
					// data button di custom sehingga diikutkan aja disini
					?>
					<table border="0" cellspacing="10" align="center">
						<tr>
							<?	if($c_readlist) { ?>
							<td id="be_list" class="TDButton" onclick="goList()">
								<img src="images/list.png"> Daftar
							</td>
							<?	} if(!$p_posterr) { ?>
						   <td id="be_edit" class="TDButton" onclick="goEdit()">
								<img src="images/edit.png"> Sunting
							</td>
							<td id="be_save" class="TDButton" onclick="goSave()" style="display:none">
								<img src="images/disk.png"> Simpan
							</td>
							<td id="be_undo" class="TDButton" onclick="goUndo()" style="display:none">
								<img src="images/undo.png"> Batal
							</td>
							<?	} if($c_delete and !empty($r_key)) { ?>
							<td id="be_delete" class="TDButton" onclick="goDelete()">
								<img src="images/delete.png"> Hapus
							</td>
							<?	} ?>
						</tr>
					</table>
					<?
						}
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
					<div class="box-content" style="width:<?= $p_tbwidth ?>px">
					<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="2" align="center">
					<?	$a_required = array();
						foreach($row as $t_row) {
							if($t_row['notnull'])
								$a_required[] = $t_row['id'];
							if($t_row['skip'])
								continue;
					?>
						<tr id="tr_<?= str_replace(" ", "", $t_row['label']) ?>">
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
								<? } else if ($t_row['label'] == 'Validator') { ?>
									<span id="edit" style="display:none"><?= $t_row['input'] ?></span>-<?= $a_mahasiswa ?>
								<? }else{?>
									<span id="edit" style="display:none"><?= $t_row['input'] ?></span>
								<?}?>
							</td>
						</tr>
					<?	} ?>
					<tr class="tr_hidden" style="display: none;">
						
						<td class="LeftColumnBG" width="150" style="white-space:nowrap;">
								Username *
							</td>
							<td class="RightColumnBG">
								<span id="show"><?= $t_row['value'] ?></span>
									<span id="edit" style="display:none">
										<input type="text" class="ControlStyle" id="username" name="username">
										<input type="hidden" id="nim" name="nim" value="<?= $r_nim ?>">
									</span>
							</td>
					</tr>
					<tr class="tr_hidden" style="display: none">
						<td class="LeftColumnBG" width="150" style="white-space:nowrap;">
								Password *
							</td>
							<td class="RightColumnBG">
								<span id="show"><?= $t_row['value'] ?></span>
									<span id="edit" style="display:none">
										<input type="password" class="ControlStyle" id="password" name="password">
									</span>
							</td>
					</tr>

					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="pkey" id="pkey" value="<?= $r_pkey ?>">
					<?php if($p_dosen){?>
						<input type="hidden" name="isonline" id="isonline" value="<?= Page::getDataValue($row,'isonline') ?>">
					<?php } ?>
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
		$("#waktumulairealisasi").mask("99:99");
		$("#waktuselesairealisasi").mask("99:99");
		
    });
	</script>

	<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";



$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
		
	$("img").not("[src]").each(function() {
		this.src = "index.php?page=img_datathumb&type="+this.id+"&id="+document.getElementById("key").value;
	});
	
	$('#validmhs_t').click(function() {
        if($(this).is(':checked'))
            $(".tr_hidden").show();
        else{
            $(".tr_hidden").hide();
        }
    });

    $("#username").xautox({strpost: "f=acmahasiswakelas", targetid: "nim",postid:"key"});

	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	//setKelompok();
});
function setKelompok(){
	var jenis=$("#jeniskuliah").val();
	var kelompok=$("#kelompok").val();
	 if(jenis=='R'){
		 var posted = "f=setKelompokJ&q[]=<?=$ajax_key?>&q[]=R&q[]="+kelompok;
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
