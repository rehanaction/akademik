<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('bebandosen'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_edit))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Beban Dosen';
	$p_tbwidth = 650;
	$p_aktivitas = 'NILAI';
	$p_dbtable = 'bd_bebandosen';
	$p_key = 'kodeperiodebd,idpegawaimonev,idpegawai';
	$p_listpage = Route::getListPage();
	
	$p_model = mBebanDosen;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodeperiodebd', 'label' => 'Periode BKD', 'type' => 'S', 'option' => $p_model::getPeriodeBD($conn), 'readonly' => true);
	$a_input[] = array('kolom' => 'pegawai', 'label' => 'Nama Pegawai', 'maxlength' => 100, 'size' => 50, 'readonly' => true);
	$a_input[] = array('kolom' => 'perguruantinggi', 'label' => 'Perguruan Tinggi', 'default' => $conf['univ_name'], 'readonly' => true);
	$a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'default' => $conf['univ_address'], 'readonly' => true);
	$a_input[] = array('kolom' => 'fakultas', 'label' => 'Fakultas/ Departemen', 'readonly' => true);
	$a_input[] = array('kolom' => 'jurusan', 'label' => 'Jurusan/ Prodi', 'readonly' => true);
	$a_input[] = array('kolom' => 'tmplahir', 'label' => 'Tempat, tanggal lahir', 'readonly' => true);
	$a_input[] = array('kolom' => 'tgllahir', 'readonly' => true, 'type' => 'D');
	$a_input[] = array('kolom' => 'pendsarjana', 'label' => 'Pendidikan S1', 'readonly' => true);
	$a_input[] = array('kolom' => 'pendmagister', 'label' => 'Pendidikan S2', 'readonly' => true);
	$a_input[] = array('kolom' => 'penddoktoral', 'label' => 'Pendidikan S3', 'readonly' => true);
	$a_input[] = array('kolom' => 'monev', 'label' => 'Dosen Monev', 'readonly' => true);
	$a_input[] = array('kolom' => 'isfinal', 'label' => 'Status Pengajuan', 'readonly' => true);
	$a_input[] = array('kolom' => 'isfinalreal', 'label' => 'Status Penilaian', 'readonly' => true);
	$a_input[] = array('kolom' => 'nohp', 'label' => 'No. HP', 'readonly' => true);
	$a_input[] = array('kolom' => 'status', 'label' => 'Status Dosen', 'type' => 'R', 'br' => true, 'option' => $p_model::getStatusDosen($conn), 'readonly' => true);
	$a_input[] = array('kolom' => 'nosertifikat', 'label' => 'No. Sertifikat', 'maxlength' => 100, 'size' => 40, 'readonly' => true);
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = CStr::removeSpecial($_POST['actdet']);
	if($r_act == 'ajukan' and $c_edit) {
		$record = array();
		$record['isfinalreal'] = 'Y';
		$record['tglrealisasi'] = date("Y-m-d");
		
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_key,true,$p_dbtable,$p_key);
	}
	else if($r_actdet == 'savedet' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
		
		$a_inputdet = array();
		$a_inputdet[] = array('kolom' => 'tglpenilaian', 'label' => 'Tgl. Penilaian', 'type' => 'D');
		$a_inputdet[] = array('kolom' => 'penilaianmonev', 'label' => 'Penilaian Monev');
		$a_inputdet[] = array('kolom' => 'filepenilaian', 'label' => 'File Penilaian', 'type' => 'U', 'uptype' => 'filepenilaian');
		$a_inputdet[] = array('kolom' => 'skscapaianmonev', 'label' => 'SKS Capaian Monev');
		$a_inputdet[] = array('kolom' => 'capaianmonev');
	
		list($post,$record) = uForm::getPostRecord($a_inputdet,$_POST);
		$where = "nobd";
		
		if(empty($r_subkey)){
			list($record['kodeperiodebd'],$record['idpegawaimonev'],$record['idpegawai']) = explode('|',$r_key);
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_inputdet,$record,$r_subkey,'bd_bebandosenadet',$where,true);
		}else{		
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_inputdet,$record,$r_subkey,'bd_bebandosenadet',$where);
		}
		
		if(!$p_posterr){
			unset($a_inputdet,$post);
		}
	}
	else if($r_actdet == 'deletefile' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
		$r_file = CStr::removeSpecial($_POST['file']);
		$where = "nobd";
		
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_subkey,'bd_bebandosenadet',$r_file,$where);
	}
	
	$sql = $p_model::getDataInputBKD($r_key);	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key,$sql);
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
	
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
	
	//bidang beban dosen
	$a_bidang = $p_model::getBidangBKD($conn);
	
	//mendapatkan data riwayat
	if($r_key)
		$a_data = $p_model::getDataRwtBKD($conn,$r_key);
	
	//cek apakah sudah final
	if(!empty($r_key))
		$isfinal = $p_model::isFinal($conn,$r_key);
		
	if($isfinal){
		$c_edit = false;
		$c_delete = false;
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<style>
		#tbl_det tr:nth-child(2n+2) {background: #F4F4F4}
		#tbl_det tr:nth-child(2n+3) {background: #FFFFFF}
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?					
					if(empty($p_fatalerr)){
				?>
				
				<table border="0" cellspacing="10" align="center">
					<tr>
						<?	if($c_readlist) { ?>
						<td id="be_list" class="TDButton" onclick="goList()">
							<img src="images/list.png"> Daftar
						</td>
						<?	} ?>
						<?if($isfinal){?>						
						<td id="be_print" class="TDButton" onclick="goPrint()">
							<img src="images/small-print.png"> Cetak BKD
						</td>
						<?}?>
					</tr>
				</table>	
					
				<?	} if(!empty($p_postmsg)) { ?>
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
							<td class="LeftColumnBG" width="150" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodeperiodebd') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'kodeperiodebd') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pegawai') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'pegawai') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nohp') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'nohp') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'perguruantinggi') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'perguruantinggi') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'alamat') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'alamat') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'fakultas') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'fakultas') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'jurusan') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'jurusan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tmplahir') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'tmplahir').', '.Page::getDataInput($row,'tgllahir') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pendsarjana') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'pendsarjana') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'pendmagister') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'pendmagister') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'penddoktoral') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'penddoktoral') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isfinal') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'isfinal') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'monev') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'monev') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isfinalreal') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'isfinalreal') ?></td>
						</tr>
						<tr height="30">
							<td colspan="2" class="DataBG">Pelengkap</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'status') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'status') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nosertifikat') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'nosertifikat') ?></td>
						</tr>
					</table>
					</div>
				</center>
				
				<?
					if(!empty($r_key)){
				?>
					<br />
					<?if($c_edit){?>
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td width="60px" class="TDButton" onclick="goAjukan()">
								<img src="images/disk.png"> Ajukan
							</td>
						</tr>
					</table>
					<?}?>
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td align="center" style="color:#3870A8;font-size:14px"><b>Data Beban Kerja Dosen</b></td>
						</tr>
					</table>
					
					<table width="900px" align="center" cellpadding="4">
						<?
							foreach($a_bidang as $bid => $bidval){
								if(!empty($a_data[$bid])){
						?>
						<tr>
							<td>
								<table id="tbl_det" width="100%" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
									<tr>
										<td class="DataBG" colspan="12">Bidang <?= $bidval?></td>
									</tr>
									<tr>
										<th rowspan="3">No</th>
										<th rowspan="3">Kegiatan</th>
										<th colspan="2">Beban Kerja</th>
										<th rowspan="3">Masa Pelaksanaan</th>
										<th colspan="3">Kinerja</th>
										<th colspan="3">Penilaian</th>
										<th rowspan="3">Aksi</th>
									</tr>
									<tr>
										<th rowspan="2">Bukti Penugasan</th>
										<th rowspan="2">SKS</th>
										<th rowspan="2">Bukti Dokumen</th>
										<th colspan="2">Capaian</th>
										<th rowspan="2">Bukti Dokumen</th>
										<th colspan="2">Capaian</th>
									</tr>
									<tr>
										<th>%</th>
										<th>SKS</th>
										<th>%</th>
										<th>SKS</th>
									</tr>
									<? 
												$d=0;
												if($bid != $tempbid)
													$no=0;
												
												foreach($a_data[$bid] as $val => $row){
													$d++;
													$no++;
													$sks[$bid] += $row['sks'];
													$skscapaian[$bid] += $row['skscapaian'];
													$skscapaianmonev[$bid] += $row['skscapaianmonev'];
									?>
									<tr>
										<td align="center"><?= $no ?></td>
										<td><?= $row['kegiatan'] ?></td>
										<td><?= $row['buktipenugasan'] ?></td>
										<td align="center"><?= $row['sks'] ?></td>
										<td><?= $row['waktu'] ?></td>
										<td><?= $row['buktidokumen'] ?></td>
										<td align="center"><?= $row['capaian'] ?></td>
										<td align="center"><?= $row['skscapaian'] ?></td>
										<td><?= $row['penilaianmonev'] ?></td>
										<td align="center"><?= $row['capaianmonev'] ?></td>
										<td align="center"><?= $row['skscapaianmonev'] ?></td>
										<td align="center">
											<img style="cursor:pointer" onclick="openDetail('<?= $r_key ?>','<?= $row['nobd'] ?>')" src="images/edit.png" title="Tampilkan Detail">
										</td>
									</tr>
									<? 
												}
									  ?>
									<tr style="font-weight:bold">
										<td class="FootBG" align="center" colspan="3">Total SKS Beban Kerja</td>
										<td class="FootBG" align="center"><?= $sks[$bid]?></td>
										<td class="FootBG" align="center" colspan="3">Total SKS Kinerja</td>
										<td class="FootBG" align="center"><?= $skscapaian[$bid]?></td>
										<td class="FootBG" align="center" colspan="2">Total SKS Penilaian</td>
										<td class="FootBG" align="center"><?= $skscapaianmonev[$bid]?></td>
										<td class="FootBG">&nbsp;</td>
									</tr>
								</table>
							</td>
						</tr>
						<?
							
											
											$tempbid = $bid;}
						}
						?>
					</table>
				
				<?}?>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var detform = "<?= Route::navAddress('pop_nilaibebandosen') ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
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

function goAjukan(){
	var ajukan = confirm("Anda yakin untuk mengajukan BKD ini ?");
	if (ajukan){
		document.getElementById("act").value = 'ajukan';
		goSubmit();
	}
}

function goPrint() {
	var keys = '<?= $r_key?>';
	keys = keys.split('|');
	window.open("<?= Route::navAddress('rep_bdbebandosen') ?>"+"&periode="+keys[0]+"&idpegawai="+keys[2]+"&format=html","_blank");
}
</script>
</body>
</html>
