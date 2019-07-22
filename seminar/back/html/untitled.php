<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug = true ;
	 
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('seminar'));
	require_once(Route::getModelPath('jenisseminar'));
	require_once(Route::getModelPath('penyelenggaraseminar'));
	require_once(Route::getModelPath('levelseminar'));
	require_once(Route::getModelPath('jadwalseminar'));
	require_once(Route::getModelPath('seminartopeserta'));
	require_once(Route::getModelPath('pembicaraseminar'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('form'));
	require_once(Route::getModelPath('mahasiswa'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);

	if (isset ($_GET['key']))
		$r_key = CStr::removeSpecial($_GET['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Seminar';
	$p_tbwidth = 600;
	$p_listpage = Route::getListPage();
	
	$p_model = mSeminar;
	$uptype = 'seminar';
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;

	$a_periode = mCombo::periode($conn);
	$a_jenisseminar = mJenisSeminar::getArray($conn);
	$a_ruang = mCombo::ruang($conn);
	$a_penyelenggara = mPenyelenggaraSeminar::getArray($conn);
	$a_level = mLevelSeminar::getArray($conn);
	$a_fakultas = mCombo::fakJur($conn);

	print_r($a_fakultaspeserta);

	$a_peserta = array('M' =>'Mahasiswa','P' =>'Pegawai','U' =>'Umum');
	$a_status = array('Diajuakan' =>'Diajuakan' ,'Disetujui' =>'Disetujui','Tidak Disetujui' =>'Tidak Disetujui');
	$a_wajib = array('W' =>'Wajib' ,'P' =>'Pilihan');	
	$a_type = array('M' =>'Mahasiswa' ,'D' =>'Dosen' , 'L' => 'Lain' );	
	$a_semester = array('1' =>'1' ,'2' =>'2' , '3' => '3' , '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8');					
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'periode', 'label' => 'Periode', 'type' => 'S', 'option' => $a_periode, 'empty' => false);
	$a_input[] = array('kolom' => 'kodejenisseminar', 'label' => 'Jenis Seminar', 'type' => 'S', 'option' => $a_jenisseminar, 'empty' => false);
	$a_input[] = array('kolom' => 'namaseminar', 'label' => 'Nama Seminar', 'maxlength' => 40, 'size' => 50, 'notnull' => !$p_limited, 'readonly' => $p_limited);
	
	$a_input[] = array('kolom' => 'temaseminar', 'label' => 'Tema Seminar', 'size' => 50, 'maxlength' => 40);
	$a_input[] = array('kolom' => 'tglpengajuan', 'label' => 'Tanggal Pengajuan', 'type' => 'D');
	$a_input[] = array('kolom' => 'tglawaldaftar', 'label' => 'Tanggal Awal Daftar', 'type' => 'D');
	$a_input[] = array('kolom' => 'tglakhirdaftar', 'label' => 'Tanggal Akhir Daftar', 'type' => 'D');
	$a_input[] = array('kolom' => 'typepengaju', 'label' => 'Penyelenggara', 'type' => 'S', 'option' => $a_penyelenggara);
	$a_input[] = array('kolom' => 'levelseminar', 'label' => 'Level Seminar', 'type' => 'S', 'option' => $a_level);

	$a_input[] = array('kolom' => 'typepeserta[]', 'label' => 'Peserta', 'type' => 'C', 'option' => $a_peserta);
	$a_input[] = array('kolom' => 'typepeserta', 'label' => 'Peserta');

	$a_input[] = array('kolom' => 'tarifseminarm', 'label' => 'Tarif Mahasiswa','size' => 50);
	$a_input[] = array('kolom' => 'tarifseminarp', 'label' => 'Tarif Pegawai','size' => 50);
	$a_input[] = array('kolom' => 'tarifseminaru', 'label' => 'Tarif Umum','size' => 50);

	$a_input[] = array('kolom' => 'semmhs[]', 'label' => 'MHS Semester', 'type' => 'C', 'option' => $a_semester);
	$a_input[] = array('kolom' => 'semmhs', 'label' => 'MHS Semester');

	$a_input[] = array('kolom' => 'wajibpilihan', 'label' => 'Wajib/Pilihan', 'type' => 'S', 'option' => $a_wajib);
	$a_input[] = array('kolom' => 'pembicara', 'label' => 'Pembicara', 'type' => 'S', 'option' => $a_type,'notnull' => true);
	$a_input[] = array('kolom' => 'pic', 'label' => 'PIC', 'size' => 50, 'maxlength' => 30);
	$a_input[] = array('kolom' => 'nohp', 'label' => 'No Hp', 'size' => 50, 'maxlength' => 30 );
	$a_input[] = array('kolom' => 'koderuang', 'label' => 'Ruang', 'type' => 'S', 'option' => $a_ruang, 'empty' => false);
	$a_input[] = array('kolom' => 'cp', 'label' => 'Contact Person', 'size' => 50, 'maxlength' => 30);
	$a_input[] = array('kolom' => 'tglkegiatan', 'label' => 'Tanggal Kegiatan', 'type' => 'D');
	$a_input[] = array('kolom' => 'jammulai','label' => 'Jam Mulai', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam','class'=>'ControlStyle jam');
	$a_input[] = array('kolom' => 'jamselesai','label' => 'Jam Selesai', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam','class'=>'ControlStyle jam');
	$a_input[] = array('kolom' => 'pagumhs', 'label' => 'Pagu Mhs','size' => 20);
	$a_input[] = array('kolom' => 'paguumum', 'label' => 'Pagu Umum','size' => 20);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Deskripsi', 'type' => 'A', 'rows' => 4, 'cols' => 40);
	$a_input[] = array('kolom' => 'tarifseminar', 'label' => 'Tarif Seminar','size' => 50);
	$a_input[] = array('kolom' => 'status', 'label' => 'Status', 'type' => 'S', 'option' => $a_status);
	$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'C', 'option' => array('1' => ''),'readonly'=>$p_limited,'notnull' => true);
	$a_input[] = array('kolom' => 'isbuka', 'label' => 'Buka Absen', 'type' => 'C', 'option' => array('1' => ''),'readonly'=>$p_limited);

	$a_input[] = array('kolom' => 'fileposter', 'label' => 'File Poster', 'type' => 'U', 'uptype' => $uptype, 'size' => 40,'maxsize'=>'10','arrtype'=>array('png','jpg','gif'));

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);	

		$namaposter = $_FILES['fileposter']['name'] ; 
		$tempposter = $_FILES['fileposter']['tmp_name'] ; 

		// insert peserta
		if (!empty($_POST['typepeserta'])) {
			$peserta = array();
			$peserta = $_POST['typepeserta'];
		}

		$record['typepeserta'] = implode(',', $peserta);

		// insert semester
		if (!empty($_POST['semmhs'])) {
			$semmhs = array();
			$semmhs = $_POST['semmhs'];
		}

		$record['semmhs'] = implode(',', $semmhs);
		$record['fileposter'] = $namaposter;
		
		// insert data seminar
		$isJadwalExist = $p_model::getDataJadwal($conn,$record['tglkegiatan'],$record['koderuang']);
		
		if(empty($r_key)){
			if (!empty($isJadwalExist)) {
				list($p_posterr,$p_postmsg) = array(true , "Jadwal Sudah di Pakai" );
			} else {
				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
			}
		} else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);

		
		// insert fakultas
		if (empty($p_posterr)) {
			if (!empty($_POST['fakultas'])) {			
				$records = array(); 
				$records['idseminar'] = $r_key ;
				
				foreach ($_POST['fakultas'] as $key => $value) {
					$records['kodeunit'] = $value ;
					if(!empty($r_key)){
						$fakultas = mSeminarTopeserta::getDataPeserta($conn,$r_key);
						
						if (!empty($fakultas)) {
							//list($p_posterr,$p_postmsg) = mSeminarTopeserta::deleteFakultas($conn,$r_key);
						} 

						list($p_posterr,$p_postmsg) = mSeminarTopeserta::insertRecord($conn,$records);
					} 
				}				
			}
		}

		// insert pembicara 
		if (!empty($_POST['inpembicara'])) {			
			$records = array(); 			
			$records['idseminar'] = $r_key ;

			foreach ($_POST['inpembicara'] as $key => $value) {
				$records['idpembicara'] = $value ;				
				list($p_posterr,$p_postmsg) = mPembicaraSeminar::insertRecord($conn,$records);
			}				
		}
		

		if(!$p_posterr) unset($post);
	}

	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	$a_fakultaspeserta = mSeminarTopeserta::getDataPeserta($conn);

	$r_mahasiswa = Page::getDataValue($row,'nim');
	$r_pegawai = Page::getDataValue($row,'nip');
	
	if(!empty($r_mahasiswa))
		$r_namamahasiswa = $r_mahasiswa.' - '.$p_model::getNamaMahasiswa($conn,$r_mahasiswa);	


	if(!empty($r_pegawai))
		$r_namapegawai = $r_pegawai.' - '.$p_model::getNamaPegawai($conn,$r_pegawai);
	
	if(empty($p_tbwidth))
		$p_tbwidth = 640;
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
	<script type="text/javascript" src="scripts/forinplace.js"></script>
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
					/***********\*****/
					
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
						<?= Page::getDataTR($row,'periode') ?>
						<?= Page::getDataTR($row,'temaseminar') ?>
						<?= Page::getDataTR($row,'namaseminar') ?>	
						<?= Page::getDataTR($row,'kodejenisseminar') ?>	
						<?= Page::getDataTR($row,'levelseminar') ?>		
						<?= Page::getDataTR($row,'wajibpilihan') ?>	
						<?= Page::getDataTR($row,'pembicara') ?>	
						<?= Page::getDataTR($row,'tglpengajuan') ?>						
						<?= Page::getDataTR($row,'tglawaldaftar') ?>
						<?= Page::getDataTR($row,'tglakhirdaftar') ?>
						<?= Page::getDataTR($row,'typepengaju') ?>

						<!-- Type Peserta -->
						<?	$datapeserta = explode(',',Page::getDataValue($row,'typepeserta'));
							$pesertain = array();
							
							foreach($datapeserta as $key => $val){
								array_push($pesertain,$val);
							}						
						?>	

						<tr>
							<td class="LeftColumnBG">Peserta</td>
							<td class="RightColumnBG">
								<span id="show">
									<?php
										$check = '';
										foreach($a_peserta as $key => $val){
											$check .=  in_array($key,$pesertain)?'<img src="images/check.png">':'<input type="checkbox" readonly="readonly" disabled="disabled">';
											$check .= ' <label for="typepeserta[]_'.$key.'">'.$val.'</label><br>';
										}
										echo $check;

									?>
								</span>
								<span id="edit" style="display:none">
									<?php
										$check = '';
										foreach($a_peserta as $key => $val){
											if(in_array($key,$pesertain))
												$checked = 'checked=""';
											else
												$checked = '';
											$check .=  '<input type="checkbox" onchange=CheckedPeserta(this) name="typepeserta[]" id="typepeserta[]_'.$key.'" value="'.$key.'" '.$checked.'>';
											$check .= ' <label for="typepeserta[]_'.$key.'">'.$val.'</label><br>';
										}
										echo $check;

									?>
								</span>
							</td>
						</tr>

						<?= Page::getDataTR($row,'tarifseminarm') ?>
						<?= Page::getDataTR($row,'tarifseminarp') ?>
						<?= Page::getDataTR($row,'tarifseminaru') ?>		

						<tr>
							<td class="LeftColumnBG">Fakultas Peserta</td>
							<td class="RightColumnBG">
								<span id="show">
									<?php
										$check = '';
										foreach($a_fakultas as $key => $val){
											$check .=  in_array($key,$a_fakultaspeserta)?'<img src="images/check.png">':'<input type="checkbox" readonly="readonly" disabled="disabled">';
											$check .= ' <label for="fakultas[]_'.$key.'">'.$val.'</label><br>';
										}
										echo $check;

									?>
								</span>
								<span id="edit" style="display:none">
									<?php
										$check = '';
										foreach($a_fakultas as $key => $val){
											if(in_array($key,$a_fakultaspeserta))
												$checked = 'checked=""';
											else
												$checked = '';
											$check .=  '<input type="checkbox" name="fakultas[]" id="fakultas[]_'.$key.'" value="'.$key.'" '.$checked.'>';
											$check .= ' <label for="fakultas[]_'.$key.'">'.$val.'</label><br>';
										}
										echo $check;

									?>
								</span>
							</td>
						</tr>						
						
						<!-- Get Semester -->
						<?	$valSem1 = explode(',',Page::getDataValue($row,'semmhs'));
							$valSem = array();

							foreach($valSem1 as $key => $val){
								array_push($valSem,$val);
							}						
						?>

						<tr>
							<td class="LeftColumnBG">Semester</td>
							<td class="RightColumnBG">
								<span id="show">
									<?php
										$check = '';
										foreach($a_semester as $key => $val){
											$check .=  in_array($val,$valSem)?'<img src="images/check.png">':'<input type="checkbox" readonly="readonly" disabled="disabled">';
											$check .= ' <label for="semmhs[]_'.$key.'">'.$val.'</label><br>';
										}
										echo $check;

									?>
								</span>
								<span id="edit" style="display:none">
									<?php
										$check = '';
										foreach($a_semester as $key => $val){
											if(in_array($val,$valSem))
												$checked = 'checked=""';
											else
												$checked = '';
											$check .=  '<input type="checkbox" name="semmhs[]" id="semmhs[]_'.$key.'" value="'.$key.'" '.$checked.'>';
											$check .= ' <label for="semmhs[]_'.$key.'">'.$val.'</label><br>';
										}
										echo $check;

									?>
								</span>
							</td>
						</tr>

						<?= Page::getDataTR($row,'pic') ?>
						<?= Page::getDataTR($row,'nohp') ?>
						<?= Page::getDataTR($row,'koderuang') ?>
						<?= Page::getDataTR($row,'cp') ?>						
						<?= Page::getDataTR($row,'tglkegiatan') ?> 
						<?= Page::getDataTR($row,'jammulai') ?> 
						<?= Page::getDataTR($row,'jamselesai') ?> 
						<?= Page::getDataTR($row,'tarifseminar') ?>	
						<?= Page::getDataTR($row,'pagumhs') ?> 
						<?= Page::getDataTR($row,'paguumum') ?> 
						<?= Page::getDataTR($row,'keterangan') ?>
						<?= Page::getDataTR($row,'fileposter') ?>
						<?= Page::getDataTR($row,'status') ?>
						<?= Page::getDataTR($row,'isvalid') ?>
						<?= Page::getDataTR($row,'isbuka') ?>

						
					</table>
					<? if(!empty($r_key)) { ?>
					<br>
					<?	/**********/
						/* DETAIL */
						/**********/
						$rowd = $p_model::getJadwalSeminar($conn,$r_key);

						$t_field = 'jadwal';
						$t_colspan = count($a_detail[$t_field]['data'])+2;
						$t_dkey = $a_detail[$t_field]['key'];
						/*
						$r_keydetail=$r_key.'|K|1';
									echo '<div class="data_detailjadwalseminar">';
										require_once('data_detailjadwalseminar.php'); 
									echo '</div><br>';
						*/
						if(!is_array($t_dkey))
							$t_dkey = explode(',',$t_dkey);
					?>
					
					<? } ?>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
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
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	// autocomplete
	$("#nim_pengajuseminar").xautox({strpost: "f=acmahasiswa", targetid: "nimpengajuseminar"});
	$("#nip_pengajuseminar").xautox({strpost: "f=acpegawai", targetid: "nippengajuseminar"});

	var type = $("#typepeserta").val();
	if (type == 'M') {
		$("#semestermahasiswa").show();
	} else {
		$("#semestermahasiswa").hide();
	}

	$("#typepeserta").change(function() {
		var type = $("#typepeserta").val();
		
		if (type == 'M') {
			$("#semestermahasiswa").show();
		} else {
			$("#semestermahasiswa").hide();
		}
	});

	var type = $("#typepengaju").val();
	if (type == '3') {
		$("#fakultaspeserta").show();
	} else {
		$("#fakultaspeserta").hide();
	}

	$("#typepengaju").change(function() {
		var type = $("#typepengaju").val();
		
		if (type == '3') {
			$("#fakultaspeserta").show();
		} else {
			$("#fakultaspeserta").hide();
		}
	});

	$("#tfakultas").hide();

	$("#pembicaramhs").hide();
	$("#pembicarapgw").hide();
	$("#pembicaralain").hide();
});

$(function() {
    $.mask.definitions['~'] = "[+-]";
	$("#i_jammulai").mask("99:99");
	$("#i_jamselesai").mask("99:99");
	$(".jam").mask("99:99");
});

function CheckedPeserta(checkbox) {
    if(checkbox.checked == true){
        alert('C');
    }else{
        alert('U');
   }
}


















function addPembicara(){
	var type = $("#pembicara").val();
	
	if (type == 'M') {
		var noid = $('#nimpengajuseminar').val();
		var nama = $('#nim_pengajuseminar').val();
	} else if (type == 'D') {
		var noid = $('#nippengajuseminar').val();
		var nama = $('#nip_pengajuseminar').val();
	} else if (type == 'L') {
		var noid = $('#namapembicaralain').val();
		var nama = $('#namapembicaralain').val();
	}


	var mhs =   '<span id="disini">'+					
					'<a href="javascript:void(0)" id="link_'+noid+'")"><br>'+nama+'\t <b>   X</b></a>'+
					'<input type="hidden" id="list_'+noid+'" name="inpembicara[]" value="'+noid+'" ></input>'
				'</span>' ;

	$("#cetakpembicara").append(mhs);
}

$(function() {	
	$("#pembicara").change(function() {
		var type = $("#pembicara").val();
		
		if (type == 'M') {
			$("#pembicaramhs").show();
			$("#pembicarapgw").hide();
			$("#pembicaralain").hide();
		} else if (type == 'D') {
			$("#pembicaramhs").hide();
			$("#pembicarapgw").show();
			$("#pembicaralain").hide();
		} else if (type == 'L') {
			$("#pembicaramhs").hide();
			$("#pembicarapgw").hide();
			$("#pembicaralain").show();
		}
	});

	
	$("#typepengaju").change(function() {
		var penyelenggara = $("#typepengaju").val();
		var peserta = $("#typepeserta").val();

		if (peserta != 'U') {
			if (penyelenggara == '3') {
				$("#tfakultas").show();
			} else {
				$("#tfakultas").hide();
			}

		}
	});
	
});

</script>
</body>
</html>