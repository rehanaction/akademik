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
	list($key,$page) = explode('::',$r_key);
	list($kodeperiodepa,$idpenilai,$idpegawai) = explode('|',$r_key);
	
	// properti halaman
	$p_title = 'Penilaian Kinerja';
	$p_tbwidth = 900;
	$p_aktivitas = 'NILAI';
	$p_listpage = Route::getListPage();
	$p_dbtable = 'pa_hasilpenilaian';
	$p_key = 'kodeperiodepa,idpenilai,idpegawai';
	
	$p_model = mPa;
	
	$a_input = array();	
	$a_input[] = array('kolom' => 'tglpenilaian', 'label' => 'Tanggal Penilaian', 'type' => 'D', 'default' => date('Y-m-d'));	
	$a_input[] = array('kolom' => 'isselesai', 'type' => 'H');
	
	$a_info = array();
	$a_info = $p_model::getDetailPenilaian($conn, $r_key);	
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'simpan' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		$key = $kodeperiodepa .'|'.$idpegawai;
		$pr_key = 'kodeperiodepa,idpegawai';
		
		$conn->StartTrans();
		
		$totalnilai=0;
		//hapus dahulu jawaban yang ada
		list($err,$msg) = $p_model::delete($conn,$key,'pa_penilaian',$pr_key);
		
		if(count($_POST['soal'])>0){
			foreach($_POST['soal'] as $soal => $jwb){
				$nilai = CStr::cStrNull($_POST[$jwb]);
				
				$record = array();
				if ($nilai != 'null'){
					$record['idpegawai'] = $idpegawai;
					$record['kodeperiodepa'] = $a_info['kodeperiodepa'];
					$record['kodeperiodebobot'] = $a_info['kodeperiodebobot'];
					$record['kodesoal'] = $jwb;
					$record['nilai'] = $nilai;
					$totalnilai = $totalnilai + $nilai;
			
					list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,false,'pa_penilaian');
				}
			}
			if(count($totalnilai)>0){
				$record['tglpenilaian'] = CStr::formatDate($_POST['tglpenilaian']);
				$record['nilaiakhir'] = $totalnilai;
				list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn, $record, $r_key, false, $p_dbtable,$p_key);
			}
		}
		$conn->CompleteTrans();
		
		if(!$p_posterr){
			unset($post);
			$a_info = array();
			$a_info = $p_model::getDetailPenilaian($conn, $r_key);	
		}
	}
	else if($r_act == 'selesai' and $c_edit) {
		$conn->StartTrans();
		
		$rec = array();
		$rec['isselesai'] = 'Y';
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn, $rec, $r_key, false, $p_dbtable,$p_key);
		
		$conn->CompleteTrans();
		
		if(!$p_posterr){
			unset($post);
			$a_info = $p_model::getDetailPenilaian($conn, $r_key);
			$c_edit = false;
		}
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key,$sql);
	
	foreach($row as $t_row) {
		if($t_row['id'] = 'isselesai')
			$isselesai = $t_row['value'];
	}
	
	$jmlskala = $p_model::getSkalaSoal($conn, $a_info['kodeperiodebobot']);
	$kodeperiodebobot = $a_info['kodeperiodebobot'];

	$a_data = array();
	$a_data = $p_model::getSoalPenilaian($conn,$kodeperiodebobot,$idpegawai);
	
	$a_radio = array();
	$a_radio[] = 1;	
	
	$cekAktifPeriodePa = $p_model::cekAktifPeriodePa($conn,$kodeperiodepa);
	
	//cek aspek dan skala penilaian
	$infoskala = $p_model::getInfoCekSkala($conn,$kodeperiodebobot);
	
	$cekNilaiTerbawah = $p_model::cekNilaiTerbawah($conn,$kodeperiodebobot,$infoskala['nilaibawah']);
	$cekNilaiTeratas = $p_model::cekNilaiTeratas($conn,$kodeperiodebobot,$infoskala['nilaiatas']);
	
	if($cekNilaiTerbawah)
		list($p_posterr,$p_postmsg) = array(true,"Nilai terbawah Skala Penilaian kurang dari batas nilai bawah, Mohon perikasi kembali");
	if($cekNilaiTeratas)
		list($p_posterr,$p_postmsg) = array(true,"Nilai teratas Skala Penilaian lebih dari batas nilai atas, Mohon perikasi kembali");
	
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
			<form name="pageform" id="pageform" method="post">
				<table border="0" cellspacing="0" align="center">
					<tr>
						<td id="be_list" class="TDButton" onClick="location.href='<?= Route::navAddress($p_listpage); ?>'">
							<img src="images/list.png"> Daftar
						</td>
					</tr>
				</table>
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
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
				</center>
				<center>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
						<table cellspacing="0" cellpadding="4" width="<?= $p_tbwidth-22 ?>" border="0" class="bottomline">
							<tbody>
								<tr>
									<td><strong>Periode Penilaian</strong></td>
									<td><strong>:</strong></td>
									<td><?= $a_info['namaperiodepa']; ?></td>
									<td><strong><?= Page::getDataLabel($row,'tglpenilaian') ?></strong></td>
									<td><strong>:</strong></td>
									<td><?= Page::getDataInput($row,'tglpenilaian') ?></td>
								</tr>
								<tr>
									<td colspan="6">&nbsp;</td>
								</tr>
								<tr valign="top">
									<td width="50%" colspan="3"><strong>Yang dinilai</strong></td>
									<td colspan="3"><strong>Yang menilai</strong></td>
								</tr>
								<tr>
									<td width="30"><strong>Nama</strong></td>
									<td width="2"><strong>:</strong></td>
									<td><?= $a_info['namadinilai']?></td>
									<td ><strong>Nama</strong></td>
									<td><strong>:</strong></td>
									<td><?= $a_info['namapenilai']?></td>
								</tr>	
								<tr>
									<td><strong>Jabatan</strong></td>
									<td><strong>:</strong></td>
									<td><?= $a_info['jabatandinilai']; ?></td>
									<td><strong>Jabatan</strong></td>
									<td><strong>:</strong></td>
									<td><?= $a_info['jabatanpenilai']; ?></td>
								</tr>	
								<tr>
									<td><strong>Unit</strong></td>
									<td><strong>:</strong></td>
									<td><?= $a_info['unitdinilai']; ?></td>
									<td><strong>Unit</strong></td>
									<td><strong>:</strong></td>
									<td><?= $a_info['unitpenilai']; ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				</center>
				<br />
				
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas; ?>.png" onerror="loadDefaultActImg(this)"> <h1>Form <?= $a_info['namaform']; ?></h1>
							</div>
						</div>
					</header>
					<table width="<?= $p_tbwidth?>" cellspacing="0" cellpadding="4" class="GridStyle">
						<tbody>
							<tr>
								<th>No</th>
								<th>Aspek Penilaian</th>
								<? for ($i = 1; $i <= $jmlskala; $i++){
									echo '<th width="40px"> '.$i.' </th>';
								}?>
							</tr>
							<?if (count($a_data) > 0){
									$i = 0;
									foreach($a_data as $data){
										if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							?>
							<tr id="s<?= $data['urutan'] ?>" class="<?= $rowstyle ?>">
									<td align="center"><?= $data['urutan']; ?></td>
									<td><?= $data['namasoal']; ?></td>
									<? for ($x = 1; $x <= $jmlskala; $x++){?>
									<td align="center">
										<? if ($a_info['isselesai'] == 'Y' or $cekAktifPeriodePa){ 
											if ($data['nilai']==$x){?>
												<img src="images/check.png">
										<?}} else{?>
										<input type="radio" name="<?= $data['kodesoal'];?>" id="<?= $data['kodesoal']."_".$x;?>" value="<?=$x ?>" <? if($data['nilai']==$x) echo 'checked';?> />
										<label for="<?= $data['kodesoal']."_".$x?>"><?= $x?></label>
										<?}?>
									</td>
									<?} ?>
									<input type="hidden" name="soal[]" id="soal[]" value="<?= $data['kodesoal']; ?>" />
							</tr>
							<? }?>
							<tr>
								<td colspan="2" align="center"><strong>Total Nilai</strong></td>
								<td colspan="<?= $jmlskala?>" align="center"><strong><?= $a_info['nilaiakhir'] ?></strong></td>
							</tr>
							<?}
							else{ ?>
							<tr>
								<td colspan="<?= $jmlskala+2?>" align="center">Data Kosong</td>
							</tr>
							<? } ?>
						</tbody>
					</table>
					<br />
					<?if (empty($cekAktifPeriodePa) and empty($cekNilaiTerbawah) and empty($cekNilaiTeratas)){?>
					<table cellspacing="0" cellpadding="4">
						<tbody>
						<? if ($c_edit) {?>
							<tr>
								<td id="be_save" class="TDButton" style="" onclick="goSave()">
								<img src="images/disk.png">Simpan
								</td>
								<? if (!empty($a_info['nilaiakhir'])){ ?>
								<td>
								</td>
								<td id="be_save" class="TDButton" onclick="goFinish()">
								<img src="images/disk.png">Finish
								</td>
								<?}?>
							</tr>
						<?  }
						else {?>
							<tr>
								<td id="be_print" class="TDButton" style="" onclick="goPrint()">
								<img src="images/small-print.png">&nbsp;Cetak
								</td>
							</tr>
						<?}?>
						</tbody>
					</table>
					<?}?>
				</center> 
				<br />
				
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
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
	initEdit(<?= (empty($post) and !empty($isselesai)) ? false : true ?>);
	
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
	}else{
		document.getElementById("act").value = 'simpan';	
		goSubmit();
	}
	
}
function goFinish(){	
	var set = confirm("Anda yakin untuk menyimpan penilaian ini ?Penilaian yang sudah disimpan tidak bisa dirubah kembali");
	if (set){
		document.getElementById("act").value = 'selesai';	
		goSubmit();	
	}	
}

function goPrint() {
	var keys = '<?= $key?>';
	window.open("<?= Route::navAddress('rep_painput') ?>"+"&key="+keys+"&format=html","_blank");
}
</script>
</body>
</html>