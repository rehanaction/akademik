<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getModelPath('pagu'));
	require_once(Route::getModelPath('syaratdaftarulang'));
	require_once(Route::getModelPath('tagihan'));
	
	

	// properti halaman
	$p_title = 'Kelengkapan Persyaratan Registrasi Ulang';
	$p_tbwidth = 500;
	$p_aktivitas = 'NILAI';
	
	//inisialisasi
	$filter="nopendaftar";
	$nopendaftar = $_POST['nopendaftar'];
	$nama = $_POST['filtervalue'];
	$arrTagihan = mTagihan::getTagihan($conn, $nopendaftar);

	if(isset($_POST['save'])){
		$record = array();
		$almamater = $_POST['ukuranalmamater']; 
		
		$arrSyarat = mSyaratDaftarUlang::getArray($conn);
		
		foreach ($arrSyarat as $key => $val){
			if($_POST[$key]==1){
				$ok=mPendaftar::insertSyaratDaftarUlang($conn, $filter, $nopendaftar, $key);
				if($ok){
					$p_postmsg="Persyaratan berhasil dilengkapi";
				}
			}elseif($_POST[$key]==0){
				$ok=mPendaftar::deleteSyaratDaftarUlang($conn, $filter, $nopendaftar, $key);
				if($ok){
					$p_postmsg="Persyaratan berhasil dibatalkan";
				}
			}
		}
		
		$syaratpendaftar = mPendaftar::cekSyaratDftrUlangPendaftar($conn,$nopendaftar);
		$syaratjalur = mPendaftar::cekSyaratDftrUlangJalur($conn,$nopendaftar);
		
		if($syaratpendaftar==$syaratjalur){
			$record['isdaftarulang'] = '-1';
			$record['tgldaftarulang']=date('Y-m-d');
		}else{
			$record['tgldaftarulang']='';
			$record['isdaftarulang'] = '0';
		}
		$record['ukuranalmamater'] =  (!empty($almamater)) ? $_POST['ukuranalmamater'] : null;

		$update = Query::recUpdate($conn,$record,"pendaftaran.pd_pendaftar","$filter='$nopendaftar'");
		$err = $conn->ErrorNo();
		
		if ($err <> 0)
		list($p_posterr,$p_postmsg) = array(true,'Update Data Gagal');
		else
		list($p_posterr,$p_postmsg) = array(true,'Update Data Berhasil');
		
	}
	if($_POST['form_act']=='generatenim'){
		$nopendaftar=$_POST['nopendaftar'];
		
		list($p_posterr,$p_postmsg)=mPendaftar::generateOneNim($conn,$nopendaftar);
	}
	
	if (!empty ($nopendaftar)){
		$data = mPendaftar::getData($conn, $nopendaftar); //get data pendaftar
		$arrSyarat = mSyaratDaftarUlang::getArray($conn);	
		$sisapagu = mPagu::cekPagu($conn,$data); //cek pagu
		
		$arrSyaratpendaftar = mSyaratDaftarUlang::getSyaratpendaftar($conn, $nopendaftar);
	}

	if(empty($data) and !empty($nopendaftar))
		list($p_posterr,$p_postmsg)=array(true, "Pendaftar tidak ditemukan");
	
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="scripts/foredit.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><img  style=" float: left; margin-top: -5px; margin-right: 10px;" id="img_workflow" width="26px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"><span><?= $p_title ?></span></div></center>
				<br>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<tr>
											<td>No.Pendaftaran</td>
											<td><input type="text" name="filtervalue" class="ControlAuto" id="filtervalue" value="<?= $nama; ?>"/> </td>
											<td><input type="submit" value="Filter" name="btnFilter"></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?php
					
				if(isset($p_postmsg)){
				?>
					<center>
						<div class="DivSuccess" style="width:<?= $p_tbwidth ?>px"><?= $p_postmsg ?></div>
					</center>
				<?php
				}
					if(!empty($data)){
				?>
				<br>
				<table width="<?= $p_tbwidth-10 ?>" cellpadding="" cellspacing="0" align="center">
					<tr align="center">
						<td>
							<div class="filterTable" style="width:<?= $p_tbwidth/2-10 ?>px;">
								<table width="<?= $p_tbwidth/2-14 ?>" cellpadding="0" cellspacing="0" align="center">
									<tr>
										<td><strong>No Pendaftaran</strong></td>
										<td width=10><strong> : </strong></td>
										<td><?= $data['nopendaftar'] ?></td>
									</tr>
									<tr>
										<td><strong>Nama</strong></td>
										<td><strong> : </strong></td>
										<td><?= $data['nama'] ?></td>
									</tr>
									<tr>
										<td><strong>Jenis Kelamin</strong></td>
										<td><strong> : </strong></td>
										<td><?= $data['sex'] == 'L' ? 'Laki-laki' : 'Perempuan'   ?></td>
									</tr>
									<tr>
										<td><strong>Jalur Penerimaan</strong></td>
										<td><strong> : </strong></td>
										<td><?= $data['jalurpenerimaan'] ?></td>
									</tr>
									<tr>
										<td><strong>Sistem Kuliah</strong></td>
										<td><strong> : </strong></td>
										<td><?= $data['namasistem'] ?></td>
									</tr>
									<tr>
										<td><strong>Pilihan diterima</strong></td>
										<td><strong> : </strong></td>
										<td><?= $data['namaunit'] ?></td>
									</tr>
									<? if(!empty($data['nimpendaftar'])){ ?>
									<tr>
										<td><strong>NIM</strong></td>
										<td><strong> : </strong></td>
										<td><?= $data['nimpendaftar'] ?></td>
									</tr>
									<? } ?>
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr valign="top">
						<td>
							<?php
								/****************/
								/* HEADER TABLE */
								/****************/
							?>
							<center>
								<header style="width:<?= $p_tbwidth/2-10 ?>px">
									<div class="inner">
										<div class="left title">
											<img id="img_workflow" width="24px" src="images/aktivitas/ABSENSI.png" onerror="loadDefaultActImg(this)"><h1>Daftar Kelengkapan</h1>
										</div>
									</div>
								</header>
							</center>
								<table width="<?= $p_tbwidth/2-10 ?>" cellpadding="" cellspacing="0" class="GridStyle" align="center">
								<?
								/**************/
								/* ITEM TABLE */
								/**************/
								foreach ($arrSyarat as $key => $val){
								?>
									<tr>
										<td><?= $val ?></td>
										<td align="center"><input type="checkbox" name="<?= $key ?>" value="1" <?= ($arrSyaratpendaftar[$data['nopendaftar']][$key]) ?  'checked="checked"' : '' ?> ></td>
									</tr>
								<?php
								}
								?>
							<tr>
								<td>Ukuran Almamater</td>
								 <td align="center"><?= UI::createTextBox('ukuranalmamater',$data['ukuranalmamater'],'ControlStyle',5,3) ?></td>
							</tr>
							</table>
						</td>
					</tr>
				</table>
				
				<br>
				<center>
					<?php foreach ($arrTagihan as $value) { ?>
						<?php if($value['flaglunas'] == "BB"){ 
							$status = $value['flaglunas'].$status;
						} ?>
					<?php } ?>
					
				<div style="width: <?= $p_tbwidth-10 ?>px; text-align: center;">
					<input type="hidden" name="filter" value="<?= $filter ?>" >
					<input type="hidden" name="filval" value="<?= $filval ?>" >
					<?php if($sisapagu<=0 and !empty($data['nopendaftar']) and !empty($data['lulusujian'])){?>
					<div class="DivError" style="width:<?= $p_tbwidth ?>px">Maaf, Pagu untuk Prodi ini telah terisi penuh</div>
					<?php }else if (empty ($data['lulusujian']) and !empty($data)){ ?>
					<div class="DivError" style="width:<?= $p_tbwidth ?>px">Maaf, Pendaftar belum lulus ujian</div>
						
					<?	} else{ ?>
					<input type="submit" name="save" value="simpan">
					<?php } ?>
					<input type="button" style="padding:6px" value="Cetak Biodata" class="ControlStyle" onclick="goCetakBiodata()">
					<?php if($data['isdaftarulang']== -1 && $status != "BBBBBB"){ ?>
					<input type="button" style="padding:6px" value="Generate NIM" class="ControlStyle" onclick="generateNim()">
						<?php if (!empty($data['nimpendaftar'])){?>
						<input type="button" style="padding:6px" value="Cetak SPDU" class="ControlStyle" onclick="goCetakSPDU()">
					<?php }
					}else{
						echo "<br><br><span style='color: red;'>Generate nim dapat dilakukan setelah pendaftar melunasi tagihan</span>";
					} ?>
				</div>
				</center>
				<?php
					}
				?>
				<input type="hidden" name="form_act" id="form_act">
				<input type="hidden" name="nopendaftar" id="nopendaftar" value="<?= $nopendaftar?>" >
			</form>
		</div>
	</div>
</div>

</body>
</html>
<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>

<script>
 $(document).ready(function() {
	initEdit(true);
	$("#filtervalue").xautox({strpost: "f=acpendaftar", targetid: "nopendaftar"});
	
});
function generateNim(){
	$("#form_act").val('generatenim');
	$("form").submit();
}
function goCetak() {
	var form = document.getElementById("pageform");
	
	form.action ='<?= Route::navAddress('ktm_sementara') ?>';
	form.target = "_blank";
	
	goSubmit();
	
	form.action = "";
	form.target = "";
}
function goCetakSPDU() {
	var form = document.getElementById("pageform");
	
	form.action ='<?= Route::navAddress('rep_spdu') ?>';
	form.target = "_blank";
	
	goSubmit();
	
	form.action = "";
	form.target = "";
}


function goCetakBiodata() {
	var form = document.getElementById("pageform");
	
	form.action ='<?= Route::navAddress('rep_biodata') ?>';
	form.target = "_blank";
	
	goSubmit();
	
	form.action = "";
	form.target = "";
}
</script>
