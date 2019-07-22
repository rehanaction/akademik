<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kelaspraktikum'));
	require_once(Route::getModelPath('detailkelas'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('jeniskuliah'));
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('asistenajar'));
	require_once(Route::getModelPath('mengajar'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Kelas Praktikum';
	$p_tbwidth = 800;
	$p_aktivitas = 'Kelas';
	$p_listpage = Route::getListPage();
	
	$p_model = mKelasPraktikum;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	// cek data
	$a_kodeunit = mCombo::unit($conn,false);
	$a_kurikulum = mCombo::kurikulum($conn);
	$a_ruang = mCombo::ruang($conn);
	
	$r_act = $_POST['act'];
	if(empty($r_key) or $r_act == 'change') {
		$post['kodeunit'] = Modul::setRequest($_POST['kodeunit'],'UNIT');
		$post['thnkurikulum'] = Modul::setRequest($_POST['thnkurikulum'],'KURIKULUM');
		$post['kodemk'] = Modul::setRequest($_POST['kodemk'],'KODEMK');
		$tahun=Modul::setRequest($_POST['tahun'],'TAHUN');
		$semester=Modul::setRequest($_POST['semester'],'SEMESTER');
		$post['periode'] =$tahun.$semester; 
		
		$r_kodeunit = $post['kodeunit'];
		if(!isset($a_kodeunit[$r_kodeunit]))
			$r_kodeunit = key($a_kodeunit);
		
		$r_periode = $post['periode'];
		
			
		$r_kurikulum = $post['thnkurikulum'];
		if(!isset($a_kurikulum[$r_kurikulum]))
			$r_kurikulum = key($a_kurikulum);
			
		$a_kodemk=$p_model::mkKelas($conn,$r_periode,$r_kurikulum,$r_kodeunit);
		$r_kodemk = $post['kodemk'];
		
		/*if(!isset($a_kodemk[$r_kodemk]))
			$r_kodemk = key($a_kodemk);*/
		
	}
	else {
		$a_cek = $p_model::getData($conn,$r_key);
		
		$r_kodeunit = $a_cek['kodeunit'];
		$r_kurikulum = $a_cek['thnkurikulum'];
		$r_periode = $a_cek['periode'];
		$r_kodemk = $a_cek['kodemk'];
	}
	$rowprodi=mUnit::jurusan($conn);
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'semester', 'label' => 'Periode', 'type' => 'S', 'option' => mCombo::semester(), 'add' => 'onchange="goChange()"', 'request' => 'SEMESTER');
	$a_input[] = array('kolom' => 'tahun', 'type' => 'S', 'option' => mCombo::tahun(), 'add' => 'onchange="goChange()"', 'request' => 'TAHUN');
	$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Pengelola', 'type' => 'S', 'option' => $a_kodeunit, 'add' => 'onchange="goChange()"', 'request' => 'UNIT');
	$a_input[] = array('kolom' => 'thnkurikulum', 'label' => 'Kurikulum', 'type' => 'S', 'option' => $a_kurikulum, 'add' => 'onchange="goChange()"', 'request' => 'KURIKULUM');
	$a_input[] = array('kolom' => 'kodemk','label' => 'Mata Kuliah', 'type' => 'S', 'option' => $p_model::mkKelas($conn,$r_kurikulum,$r_periode,$r_kodeunit), 'add' => 'onchange="goChange()"', 'bold' => true,'empty'=>'Pilih Mata Kuliah');
	$a_input[] = array('kolom' => 'kelasmk', 'label' => 'Sesi', 'type' => 'S', 'option' => $p_model::kelas($conn,$r_kurikulum,$r_periode,$r_kodeunit,$r_kodemk), 'notnull' => true, 'bold' => true);
	//$a_input[] = array('kolom' => 'jeniskul', 'label' => 'Jenis Pertemuan', 'type' => 'S', 'option' => mJeniskuliah::getArray($conn));
	$a_input[] = array('kolom' => 'kelompok', 'label'=>'Kelompok','type' => 'S', 'option' =>$p_model::getKelompok($conn), 'empty' => false);
	
	//jadwal 1
	$a_input[] = array('kolom' => 'tglawalkuliah', 'label' => 'Tgl Mulai Kuliah','type' => 'D','add'=>'onchange="setHari1(this.value)"');
	$a_input[] = array('kolom' => 'nohari', 'type' => 'S', 'option' => Date::arrayDay(), 'empty' => true,'add'=>'readonly');
	$a_input[] = array('kolom' => 'jammulai', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam');
	$a_input[] = array('kolom' => 'jamselesai', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam');
	$a_input[] = array('kolom' => 'koderuang', 'label' => 'Ruang', 'type' => 'S', 'option' => $a_ruang, 'empty' => true);
	$a_input[] = array('kolom' => 'kapasitas', 'label' => 'Kapasitas', 'maxlength' => 3, 'size' => 3);
	
	
	// ada aksi
	if($r_act == 'save' and $c_edit) { 
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['jeniskul'] = 'P';
		$record['periode'] = $record['tahun'].$record['semester']; 
		$record['jammulai'] = CStr::cStrNull(str_replace(':','',$_REQUEST['jammulai']));
		$record['jamselesai'] = CStr::cStrNull(str_replace(':','',$_REQUEST['jamselesai']));

		
		if(empty($r_key)){
			$ok=true;
			$conn->BeginTrans();
			list($p_posterr,$p_postmsg)=mKelas::cekKapasitas($conn,$r_key,$record,$record['kapasitas']);
			if(!$p_posterr)
				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
			if(!$p_posterr)
				list($p_posterr,$p_postmsg)=mDetailKelas::insertDetailPraktikum($conn,$a_input,$record,$r_key);
			
			if($p_posterr){
				$ok=false;
				$r_key='';
			}
			$conn->CommitTrans($ok);
		}else{
			
			$ok=true;
			$conn->BeginTrans();
			
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
			if(!$p_posterr and mDetailKelas::ubahDetail($record,$_POST)){
				list($p_posterr,$p_postmsg)=mDetailKelas:: deleteBlock($conn,$r_key);
				list($p_posterr,$p_postmsg)=mDetailKelas::insertDetailPraktikum($conn,$a_input,$record,$r_key);
			}	
			if($p_posterr){
				$ok=false;
			}
			$conn->CommitTrans($ok);
		}
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'insertajar' and $c_edit) {
		$record = array();
		$record['nipdosen'] = CStr::cStrNull($_POST['nip']);
		$record['ispjmk'] = $_POST['ispjmk'];
		
		list($p_posterr,$p_postmsg) = $p_model::insertRecordMengajar($conn,$record,$r_key);
	}
	else if($r_act == 'updateajar' and $c_edit) {
		$record = array();
		$record['nipdosen'] = CStr::cStrNull($_POST['u_nip']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateRecordMengajar($conn,$record,$r_key);
	}
	else if($r_act == 'deleteajar' and $c_delete) {
		$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteMengajar($conn,$r_key,$r_subkey);
	}else if($r_act == 'insertasisten' and $c_edit) {
		$reckey = $p_model::getKeyRecord($r_key);
		$record=array();
		$record['nipasisten'] = CStr::cStrNull($_POST['nipasisten']);
		$record += $reckey;
		
		list($p_posterr,$p_postmsg) = mAsistenAjar::insertRecord($conn,$record);
	}
	else if($r_act == 'deleteasisten' and $c_delete) {
		$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
		$a_key=$r_key.'|'.$r_subkey;
		list($p_posterr,$p_postmsg) = mAsistenAjar::delete($conn,$a_key);
	}else if($r_act == 'setpjmk' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
		$t_key = $r_key.'|'.$r_subkey;
		$record = array();
		
		$record['ispjmk'] = CStr::removeSpecial($_REQUEST['u_ispjmk']);
		
		list($p_posterr,$p_postmsg) = mMengajar::updateRecord($conn,$record,$t_key,true);
		
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	$rowp=array();
	if(!empty($r_key)){
		$rowp = $p_model::getDosenPengajar($conn,$r_key);
		$a_asisten = mAsistenAjar::getAsistenPengajar($conn,$r_key);
	}
	
	$a_pengajar = array();
	foreach($rowp as $t_row){
		if($t_row['ispjmk'] == '1'){
			$a_pengajar[] = $t_row['nama'].' ('.$t_row['nipdosen'].') => <b>Koordinator</b>';
			$pjmk=true;
		}else	
			$a_pengajar[] = $t_row['nama'].' ('.$t_row['nipdosen'].')';
	}
	
	
		
	$kelompok=mJeniskuliah::flagKelompok($conn);
	//print_r($row);
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
	<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>

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
						<?= Page::getDataTR($row,'semester,tahun') ?>
						<?= Page::getDataTR($row,'kodeunit') ?>
						<?= Page::getDataTR($row,'thnkurikulum') ?>
						<tr>
						<td class="LeftColumnBG" style="white-space:nowrap">Mata Kuliah</td>
						<td class="RightColumnBG">
						<?= Page::getDataInput($row,'kodemk') ?>
						</td>
						</tr>
						
						<?= Page::getDataTR($row,'kelasmk') ?>
						
						<?= Page::getDataTR($row,'kelompok') ?>
						<?= Page::getDataTR($row,'kapasitas') ?>
						
						
						
						
						<tr id="jadwal">
							<td class="DataBG" colspan="2">Jadwal dan Pengajar</td>
						</tr>
						<!--jadwal 1 -->
						<tr id="jadwal">
							<td class="LeftColumnBG">
								<?= Page::getDataLabel($row,'tglawalkuliah') ?>
							</td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'tglawalkuliah') ?>
								<input type="hidden" name="old_tglawalkuliah" value="<?= Page::getDataValue($row,'tglawalkuliah') ?>">
							</td>
						</tr>
						<tr id="jadwal">
							<td class="LeftColumnBG" style="white-space:nowrap">Jadwal</td>
							<td class="RightColumnBG">
								<?= Page::getDataInput($row,'nohari') ?>
								, <?= Page::getDataInput($row,'jammulai') ?> - <?= Page::getDataInput($row,'jamselesai') ?>
								&nbsp; &nbsp; &nbsp; Ruang: <?= Page::getDataInput($row,'koderuang') ?>
								<input type="hidden" name="old_jammulai" value="<?= Page::getDataValue($row,'jammulai') ?>">
								<input type="hidden" name="old_jamselesai" value="<?= Page::getDataValue($row,'jamselesai') ?>">
								<input type="hidden" name="old_koderuang" value="<?= Page::getDataValue($row,'koderuang') ?>">
							</td>
						</tr>
						
						
						
						<? if(!empty($r_key)) { ?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Dosen Pengajar</td>
							<td class="RightColumnBG">
								<span id="show">
								<?= implode('<br>',$a_pengajar) ?>
								</span>
								<span id="edit" style="display:none">
								<? if($c_edit) { ?>
								<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
								<?	
									if(!empty($rowp)){
									$i = 0; $jml_pjmk=0;
									foreach($rowp as $t_row) {
										if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
										
								?>
									<tr valign="top" class="<?= $rowstyle ?>" id="l_<?=$t_row['idpegawai']?>">
										<td><?= $t_row['nama'].' ('.$t_row['nipdosen'].')' ?><?= $t_row['ispjmk']=='1' ? '=> <b>Koordinator</b>':''?></td>
										<td>
											<input title="Set/Unset Koordinator" type="checkbox" value="<?= $t_row['idpegawai'] ?>" onclick="goSetPjmk(this)"<?= empty($t_row['ispjmk']) ? '' : ' checked' ?> <?=(empty($t_row['ispjmk']) and $pjmk) ?'disabled':''?>>
											
										</td>
										<td>
											<img id="<?= $t_row['idpegawai'] ?>" title="Hapus Data" src="images/delete.png" onclick="goDeletePengajar(this)" style="cursor:pointer">
										</td>
									</tr>
								<?	} }?>
									<tr class="LeftColumnBG NoHover">
										<td width="100" nowrap><?= UI::createTextBox('dosen',$r_dosen,'ControlStyle',0,60) ?></td>
										<td>
											<input title="Koordinator" type="checkbox" value="1" name="ispjmk" <?=($pjmk) ?'disabled':''?>> Koordinator?
										</td>
										<td>
											<img title="Tambah Data" src="images/disk.png" onclick="goInsertPengajar()" style="cursor:pointer">
										</td>
									</tr>
								</table>
								<? } else { ?>
								<?= implode('<br>',$a_pengajar) ?>
								<? } ?>
								</span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Asisten</td>
							<td class="RightColumnBG">
								<span id="show">
								<?= implode('<br>',$a_asisten) ?>
								</span>
								<span id="edit" style="display:none">
								<? if($c_edit) { ?>
								<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
								<?	
									
									$i = 0; $jml_pjmk=0;
									foreach($a_asisten as $nipasisten=>$namaasisten) {
										if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
										
								?>
									<tr valign="top" class="<?= $rowstyle ?>">
										<td colspan=2 ><?= $namaasisten?></td>
										<td><img id="<?= $nipasisten ?>" title="Hapus Data" src="images/delete.png" onclick="goDeleteAsisten(this)" style="cursor:pointer"></td>
									</tr>
								<?	} ?>
									<tr class="LeftColumnBG NoHover">
										<td width="100" nowrap><?= UI::createTextBox('asisten','','ControlStyle',0,60) ?></td>
										
										<td><img title="Tambah Data" src="images/disk.png" onclick="goInsertAsisten()" style="cursor:pointer"></td>
									</tr>
								</table>
								<? } else { ?>
								<?= implode('<br>',$a_asisten) ?>
								<? } ?>
								</span>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<?php 
									echo '<div class="data_detailkelas">';
										require_once('data_detailkelaspraktikum.php'); 
									echo '</div><br>';
								?>
							</td>
						</tr>
						<? } ?>
						
						</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="matkul" id="matkul">
				<input type="hidden" id="nip" name="nip" value="<?= $r_key ?>">
				<input type="hidden" id="nipasisten" name="nipasisten">
			
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="subkeymku" id="subkeymku">
				<input type="hidden" name="u_ispjmk" id="u_ispjmk">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

	<script src="scripts/jquery-1.7.1.min.js" type="text/javascript" charset="utf-8"></script>	
	<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
	<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
	<script type="text/javascript">
	    $(function() {
        $.mask.definitions['~'] = "[+-]";
		$("#jammulai").mask("99:99");
		$("#jamselesai").mask("99:99");
		
		
    });

var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);

	$("#dosen").xautox({strpost: "f=acdosen", targetid: "nip"});
	$("#asisten").xautox({strpost: "f=acpegawaipenunjang", targetid: "nipasisten"});
	$("#u_dosen").xautox({strpost: "f=acdosen", targetid: "u_nip"});
	$("#kodemk").xautox({strpost: "f=acmatkul", targetid: "matkul"});
	$("#l_unitmku").xautox({strpost: "f=acjurusan", targetid: "unitmku"});
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	var block = $("#isblock_-1").attr("checked");
	var nonblock = $("#isblock_0").attr("checked");
	if(block){
		$("[id='jadwal']").hide();
		
	}else if(nonblock){
		$("[id='jadwal']").show();
		
	}
	 $("#isblock_-1").click(function(){
		 $("[id='jadwal']").hide();
		 
	 });
	 $("#isblock_0").click(function(){
		 $("[id='jadwal']").show();
		 
	 });
	 $("[id='checkAll']").click(function() {
		var checked = $(this).attr("checked");
		if(checked)
			$("[id='checklist']").attr("checked", checked);
		else
			$("[id='checklist']").removeAttr("checked", checked);
	});
});

function goDeletePengajar(elem) {
	document.getElementById("act").value = "deleteajar";
	document.getElementById("subkey").value = elem.id;
	if(confirm("Yakin akan hapus pengajar ?"))
		goSubmit();
}

function goInsertPengajar() {
	document.getElementById("act").value = "insertajar";
	goSubmit();
}
function goDeleteMku(elem) {
	document.getElementById("act").value = "deletemku";
	document.getElementById("subkeymku").value = elem.id;
	if(confirm('Yakin Akan Menghapus ?'))
		goSubmit();
}

function goInsertMku() {
	document.getElementById("act").value = "insertmku";
	goSubmit();
}
function goUpdatePengajar() {
	document.getElementById("act").value = "updateajar";
	goSubmit();
}
function hari(val){
	switch(val){
		case 1 : return 'Senin'; break;
		case 2 : return 'Selasa'; break;
		case 3 : return 'Rabu'; break;
		case 4 : return 'Kamis'; break;
		case 5 : return 'Jumat'; break;
		case 6 : return 'Sabtu'; break;
		case 7 : return 'Minggu'; break;
		default: return 'Pilih Tanggal';
	}
}
function setHari1(val){
	var arr_date=val.split('-');
	var date = new Date(arr_date[2]+' '+arr_date[1]+' '+arr_date[0]);
	var day = date.getDay();
	if(day==0)
		day=7;
	document.getElementById("nohari").value=day;
	//$('#nohari').prop("disabled",true);
	//document.getElementById("l_day").innerHTML=hari(day);
}
function goDeleteAsisten(elem) {
	document.getElementById("act").value = "deleteasisten";
	document.getElementById("subkey").value = elem.id;
	if(confirm("Yakin akan hapus Asisten ?"))
		goSubmit();
}

function goInsertAsisten() {
	document.getElementById("act").value = "insertasisten";
	goSubmit();
}
function goSetPjmk(elem) {
	document.getElementById("u_ispjmk").value = (elem.checked ? '1' : '0');
	document.getElementById("act").value = 'setpjmk';
	document.getElementById("subkey").value = elem.value;
	
	goSubmit();
}
</script>
</body>
</html>
