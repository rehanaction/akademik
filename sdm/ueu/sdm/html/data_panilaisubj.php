<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_other = $a_auth['canother'];
	$c_readonly = $c_other['B'];
	
	// include
	require_once(Route::getModelPath('pa'));	
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
		
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	// properti halaman
	$p_title = 'Penilaian Kinerja';
	$p_tbwidth = 900;
	$p_aktivitas = 'NILAI';
	$p_listpage = Route::getListPage();
	$p_dbtable = 'pa_hasilsubyektif';
	$p_key = 'idtim';
	
	$p_model = mPa;
	
	$a_info = array();
	$a_info = $p_model::getDetailPenilaian($conn, $r_key);	
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'simpan' and $c_edit) {
		//list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$conn->StartTrans();
		$a_nilai = $p_model::isExistNilai($conn, $r_key);
		$a_id = $_POST['id'];
		
		foreach($a_id as $id){
			$nilai = CStr::cStrNull($_POST['nilai_'.$id]);
			if ($nilai != 'null'){
				$record = array();
				$record['idtim'] = $r_key;
				$record['kodeformsubyektif'] = $a_info['kodeformsubyektif'];
				$record['nouraian'] = $id;
				$record['nilai'] = $nilai;
				
				if (!in_array($id, $a_nilai))
					list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,$p_dbtable);
				else{
					$r_subkey = $r_key.'|'.$id;
					$where = "idtim,nouraian";
					list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn, $record, $r_subkey, false, $p_dbtable,$where);
				}
			}
		}
		
		$rectim = array();
		$rectim['isselesai'] = 'Y';
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn, $rectim, $r_key, false, 'pa_timsubyektif','idtim');
		$conn->CompleteTrans();
		
		if(!$p_posterr){
			unset($post);
			$a_info = array();
			$a_info = $p_model::getDetailPenilaian($conn, $r_key);	
		}
	}
		
	$a_data = array();
	$a_data = $p_model::listSoalPenilain($conn, $a_info['kodeformsubyektif'], $r_key);
	
	$a_radio = array();
	$i = $a_info['bobotbawah'];
	
	for($i;$i<=$a_info['bobotatas'];$i++)
		$a_radio[$i] = $i;
		
	
	if ($a_info['isselesai'] == 'Y' or ($c_readonly))
		$c_edit = false;
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/wizard.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<link href="style/calendar.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<style>
		.bottomline td{
			border-bottom:1px solid #eaeaea;
		}
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<center>
				<div class="DTitle" style="width:<?= $p_tbwidth ?>px;">
					<table border="0" cellspacing="0" align="left">
						<tr>
							<td valign="bottom" align="left" width="200">
								<table border="0" cellspacing="0" align="left">
									<tr>
										<td id="be_list" class="TDButton" onClick="location.href='<?= Route::navAddress($p_listpage); ?>'">
											<img src="images/list.png"> Daftar
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
			</center>
				<br />
				<br />
				<br />
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<center>
					<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
						<span>
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
							&nbsp;<?= $p_title.' '.$a_info['keterangan'] ?>
						</span>
					</div>
				</center>
				<br>
				<center>
				<?php require_once('inc_header.php') ?>
				</center>
				<br>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
				<table cellspacing="0" cellpadding="4" width="<?= $p_tbwidth ?>" border="0">
					<tbody>
						<tr valign="top">
							<td width="120"><strong>Nama Dinilai</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td width="30%"><?= $a_info['nipdinilai'].' - '.$a_info['namadinilai']?></td>
							<td width="120"><strong>Unit Kerja</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= $a_info['unitdinilai']?></td>
						</tr>	
						<tr>
							<td><strong>Nama Penilai</strong></td>
							<td><strong>:</strong></td>
							<td><?= $a_info['nippenilai'].' - '.$a_info['namapenilai']; ?></td>
							<td><strong>Unit Kerja</strong></td>
							<td><strong>:</strong></td>
							<td><?= $a_info['unitpenilai']; ?></td>
						</tr>	
						<tr>
							<td><strong>Periode</strong></td>
							<td><strong>:</strong></td>
							<td><?= $a_info['namaperiode']; ?></td>
							<td><strong>Form</strong></td>
							<td><strong>:</strong></td>
							<td><?= $a_info['namaform']; ?></td>
						</tr>
					</tbody>
				</table>
				<br />
				
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas; ?>.png" onerror="loadDefaultActImg(this)"> <h1>Form <?= $a_info['namaform']; ?></h1>
						</div>
					</div>
				</header>
				<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
				<table width="<?= $p_tbwidth-22; ?>" cellspacing="0" cellpadding="4" class="GridStyle">
					<tbody>
						<tr>
							<th>No</th>
							<th>Ukuran Penilaian</th>
							<th>Jawaban</th>
						</tr>
						<? if (count($a_data) > 0){ 
								foreach($a_data as $row){
								
									$iscanempty = '';
									if ($row['isdinilai']=='T')
										$iscanempty = '_blank';
						?>
						<tr id="s<?= $row['nouraian'].$iscanempty ?>">
							<? if ($row['isdinilai']=='T') {?>
								<td style="padding-left:<?= $row['level']*20 ?>px"><strong><?= $row['nomor']; ?></strong></td>
								<td colspan="2"><strong><?= $row['uraian']; ?></strong></td>
							<? }else{ ?>
								<td style="padding-left:<?= $row['level']*20 ?>px"><?= $row['nomor']; ?></td>
								<td><?= $row['uraian']; ?></td>
								<td align="center" width="350">
									<?= UI::createRadio('nilai_'.$row['nouraian'],$a_radio,$row['nilai'],$c_edit);  ?>
									<input type="hidden" name="id[]" value="<?= $row['nouraian']; ?>" />
								</td>
							<? } ?>
						</tr>
						<? }}else{ ?>
						<tr>
							<td colspan="3" align="center">Data Kosong</td>
						</tr>
						<? } ?>
					</tbody>
				</table>
				<br />
				<br />
				<? if ($c_edit) {?>
				<table cellspacing="0" cellpadding="4">
					<tbody>
						<tr>
							<td id="be_save" class="TDButton" style="" onclick="goSave()">
							<img src="images/disk.png">Simpan
							</td>
						</tr>
					</tbody>
				</table>
				<?  } ?>
				</div> 
				<br />
				
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key; ?>">
				<input type="hidden" name="subkey" id="subkey">
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});	

function goSave(){		
	var cname = '';
	var checkempty = true;
	var splitname = '';	
	$("tr[id]").each(function() {
		cname = this.id.substr(1);
		tr = $(this);
		splitname = this.id.split('_');
		
		if(tr.find("[type='radio']").length == 0)
			return true;
			
			alert[splitname[1]];
		
		if(tr.find("[type='radio']:checked").length == 0 && splitname[1] != 'blank') {
			tr.addClass("YellowBG");
			checkempty = false;
		}
		else
			tr.removeClass("YellowBG");
	});
	
	if(!checkempty){
		alert ("Maaf, ada pertanyaan yang belum anda jawab. Mohon menjawab semua pertanyaan yang berwarna kuning!");
		return; 
	}
	
	var set = confirm("Anda yakin untuk menyimpan penilaian ini ?Penilaian yang sudah disimpan tidak bisa dirubah kembali");
	if (set){		
		document.getElementById("act").value = 'simpan';	
		goSubmit();	
	}
}


</script>
</body>
</html>