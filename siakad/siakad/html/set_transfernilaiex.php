<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mahasiswa'));
    require_once(Route::getModelPath('unit'));
    require_once(Route::getModelPath('transkrip'));
    require_once(Route::getModelPath('kurikulum'));
    require_once(Route::getModelPath('skalanilai'));
    require_once(Route::getModelPath('konversinilai'));
    
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	if(Akademik::isMhs())
		$r_key = Modul::getUserName();
	else
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	// properti halaman
	$p_title = 'Transfer Nilai Mahasiswa';
	$p_tbwidth = 990;
	$p_aktivitas = 'NILAI';
	$p_listpage = 'list_transfer';
	
	$p_model = mTranskrip;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key))
		Route::navigate($p_listpage);
	
	// mendapatkan nim lama
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	
	$a_infounit = mUnit::getData($conn,$a_infomhs['kodeunit']);
	
	$r_kurikulum=Akademik::getKurikulum();
   
    $r_act = $_POST['act'];
    if ( $r_act == 'konversi' and $c_insert) { 
		$record=array();
		$record['thnkurikulumbaru']=CStr::cStrNull($r_kurikulum);
		$record['nimlama']=CStr::cStrNull($a_infomhs['nimlama']);
		$record['nimbaru']=CStr::cStrNull($r_key);
		$record['kodemklama']=CStr::cStrNull($_POST['kodemklama']);
		$record['namamklama']=CStr::cStrNull($_POST['namamklama']);
		$record['skslama']=CStr::cStrNull($_POST['skslama']);
		$record['nangkalama']=CStr::cStrNull($_POST['nangkalama']);
		$record['kodemkbaru']=CStr::cStrNull($_POST['kodemkbaru']);
		$record['nangkabaru']=CStr::cStrNull($_POST['nangkabaru']);
		
		list($p_posterr,$p_postmsg)=mKonversiNilai::insertRecord($conn,$record,true);
    }
     else if ( $r_act == 'delkonversi' and $c_delete) {
		
        if(isset($_POST['delkonv'])){
		   foreach($_POST['delkonv'] as $kodemk=>$thnkurikulumbaru){
				$keykonv=$thnkurikulumbaru.'|'.$a_infounit['kodeunit'].'|'.$kodemk.'|'.$r_key;
				list($p_posterr,$p_postmsg)=mKonversiNilai::delete($conn,$keykonv,true);
		   }
		}
    }
    
    // data
	
	$a_konversi = mKonversiNilai::getHasilKonversi($conn,$a_infomhs['nim']); 
    $a_transkripkonversi = mTranskrip::getTrankskripMhs($conn,$r_key,-1); 
	$a_kurikulum = mKurikulum::mkKurikulumUnit($conn,$r_kurikulum,$a_infounit['kodeunit']);
	$a_skalanilai=mSkalaNilai::getDataKurikulum($conn,$r_kurikulum);
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<style>
		.gray td { color:#555 }
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">

		<div class="SideItem" id="SideItem">
	
			<form name="pageform" id="pageform" method="post">
				<center>
					<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
					<div class="right">
							<img  src="images/print.png" title="Cetak KHS" width="24px" style="cursor:pointer"  onclick="goPrintText()">
					</div>
						<span>
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
							&nbsp;<?= $p_title ?>
						</span>
					</div>
				</center>
				<br>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr valign="top">
								<td width="50%">
						<table width="100%" cellspacing="0" cellpadding="4">
							<tr>		
								<td width="50" style="white-space:nowrap"><strong>Mahasiswa</strong></td>
								<td><strong> : </strong><?= $a_infomhs['nama'] ?> (<?= $a_infomhs['nim'] ?>)</td>		
							</tr>
							<tr>		
								<td style="white-space:nowrap"><strong>Jurusan</strong></td>
								<td><strong> : </strong><?= $a_infomhs['jurusan'] ?></td>		
							</tr>
							<tr>		
								<td style="white-space:nowrap"><strong>Jurusan Asal</strong></td>
								<td><strong> : </strong><?= $a_infomhs['ptjurusan'] ?></td>		
							</tr>
						</table>
								</td>
								<td>
									<strong>Perhatian:</strong>
									<ol style="margin:0px -20px">
										<li>Sebelum transfer nilai, pastikan mata kuliah yang ditransfer ada di kurikulum jurusan baru</li>
										<li>Kurikulum yang diambil adalah kurikulum terbaru di jurusan baru</li>
									</ol>
								</td>
							</tr>
						</table>
					</div>
				
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
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<thead>
						<tr class="DataBG">
							<td colspan="5">Nilai Asal</td>
							<td colspan="2">Konversi dengan Mata Kuliah Kur. <?=$r_kurikulum?></td>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td>No</td>
							<td>Kode MK</td>
							<td>Nama MK</td>
							<td>SKS</td>
							<td>Nilai</td>
							<td>Mata Kuliah Konversi</td>
							<td>Nilai Konversi</td>
							<td>&nbsp;</td>
						</tr>
					</thead>
					<tbody>
					   <?php $no=0;foreach($a_konversi as $row){ $no++;?>
						<tr>
							<td><?=$no?></td>
							<td><?=$row['kodemklama']?></td>
							<td><?=$row['namamklama']?></td>
							<td><?=$row['skslama']?></td>
							<td><?=$row['nangkalama']?></td>
							<td><?=$row['kodemkbaru']?>-<?=$row['namamkbaru']?></td>
							<td><?=$row['nangkabaru']?></td>
							<td>&nbsp;</td>
						</tr>
						<?php } ?>
						<tr>
							<td>&nbsp;</td>
							<td><?=UI::createTextBox('kodemklama','','ControlStyle',10,5)?></td>
							<td><?=UI::createTextBox('namamklama','','ControlStyle',100,25)?></td>
							<td><?=UI::createTextBox('skslama','','ControlStyle',3,1)?></td>
							<td><?=UI::createSelect('nangkalama',$a_skalanilai,$r_nilai,'ControlStyle',true,'');?></td>
							<td><?=UI::createSelect('kodemkbaru',$a_kurikulum,$r_kodemk,'ControlStyle',true,'');?></td>
							<td><?=UI::createSelect('nangkabaru',$a_skalanilai,$r_nilai,'ControlStyle',true,'');?></td>
							<td>
								<?php if($c_insert){ ?>
								<input type="button" class="ControlStyle" value="Transfer" onclick="goTransfer()">
								<?php } ?>
							</td>
						</tr>
					</tbody>
				</table>
				</center>
				<br><br>
				<? if($c_delete) { ?>
				
				<input type="button" class="ControlStyle" value="Hapus Nilai Pilihan" onclick="goDelete()">
				<div class="Break"></div>
				
				<? } ?>
				<table width="<?= $p_tbwidth/2 ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<thead>
						<tr class="DataBG">
							<td colspan="5">Transkrip Hasil Konversi</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>No</td>
							<td>Kode MK</td>
							<td>Nama MK</td>
							<td>Nilai</td>
							
						</tr>
					</thead>
					<tbody>
					   <?php $no=0;foreach($a_transkripkonversi as $rowt){ $no++;?>
						
						<tr>
							<td>
								<?php if($c_delete) { ?>
								<input type="checkbox" name="delkonv[<?=$rowt['kodemk']?>]" value="<?=$rowt['thnkurikulum']?>">
								<?php } ?>
							</td>
							<td><?=$no?></td>
							<td><?=$rowt['kodemk']?></td>
							<td><?=$rowt['namamk']?></td>
							<td><?=$rowt['nhuruf']?></td>
								
						</tr>
					   <?php } ?>
					</tbody>
				</table>
				
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
$(document).ready(function() {
	$("#checklama").click(function() {
		$("[name='checklama[]']").attr("checked",this.checked);
	});
	$("#checkbaru").click(function() {
		$("[name='checkbaru[]']").attr("checked",this.checked);
	});
});
	
function goTransfer() {
	document.getElementById("act").value = "konversi";
	goSubmit();
}

function goDelete() {
	document.getElementById("act").value = "delkonversi";
	goSubmit();
}
function goPrintText() {
	showPage('null','<?= Route::navAddress('rep_trfnilai') ?>');
}

</script>
</body>
</html>
