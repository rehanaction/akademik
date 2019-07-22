<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	global $conn;
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	
	
	// include
	require_once(Route::getModelPath('pendaftar'));
	
	// properti halaman
	$p_title = 'Hasil Ujian Peserta';
	$p_tbwidth = 300;
	$p_aktivitas = 'NILAI';
	
	
	//inisialisasi
	$filter="nopendaftar";
	$filval=$_GET['no'];
	
	$materi=mPendaftar::getMateri($conn);
	$row= mPendaftar::getMateri($conn) -> GetRows();
	$length=mPendaftar::getMateri($conn)->RecordCount();
	
	if(isset($_POST['save'])){
		$filter=$_POST['filter'];
		$filval=$_POST['filval'];
		$record=array();
		$record['nilaiujian']=$_POST['nilaiujian'];
		mPendaftar::updateKelulusan($conn, $record, $filval);
		while($materipendaftar = $materi->FetchRow()){
			$nilai=$_POST[$materipendaftar['kodemateri']];
			if($nilai!=0){
				mPendaftar::insertMateriPendaftar($conn, $filter, $filval, $materipendaftar['kodemateri'], $nilai);
			}elseif($nilai==0){
				mPendaftar::deleteMateriPendaftar($conn, $filter, $filval, $materipendaftar['kodemateri']);
			}
		}
	}else if(isset($_POST['kembali'])){
			Route::navigate('set_tesmapel');
		}

	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<?
							$data = mPendaftar::getDatas($conn, $filter, $filval,'')-> FetchRow();
							?>
							<tr>
								<td><strong>No Pendaftaran</strong></td>
								<td><strong> : </strong></td>
								<td><?= $data['nopendaftar'] ?></td>
							</tr>
							<tr>
								<td><strong>Nama</strong></td>
								<td><strong> : </strong></td>
								<td><?= $data['gelardepan'].$data['nama'].$data['gelarbelakang'] ?></td>
							</tr>
							<tr>
								<td><strong>Jenis Kelamin</strong></td>
								<td><strong> : </strong></td>
								<td><?= $data['sex'] ?></td>
							</tr>
							<tr>
								<td><strong>Jalur Penerimaan</strong></td>
								<td><strong> : </strong></td>
								<td><?= $data['jalurpenerimaan'] ?></td>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?php
					if(empty($row) && $filval!='0000-0-0-000'){
						$p_postmsg="Unfound search result";
				?>
				<center>
				<div class="DivError" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<?php
					}else{
						$p_postmsg=$filval;
				?>
				<table width="<?= $p_tbwidth-10 ?>" cellpadding="" cellspacing="0" align="center">
					<tr valign="top">
						<td>
							<?php
								/****************/
								/* HEADER TABLE */
								/****************/
							?>
							<center>
								<header style="width:<?= $p_tbwidth-10 ?>px">
									<div class="inner">
										<div class="left title">
											<img id="img_workflow" width="24px" src="images/aktivitas/NILAI.png" onerror="loadDefaultActImg(this)"><h1>Daftar Nilai Materi</h1>
										</div>
									</div>
								</header>
							</center>
							<table width="<?= $p_tbwidth-10 ?>" cellpadding="" cellspacing="0" class="GridStyle" align="center">
								<tr style=" font-weight: bold; background:#c5c5c5; color: #3d3d3b;" align="center">
									<td>MATERI</td>
									<td>NILAI</td>
									
								</tr>
								<?
								/**************/
								/* ITEM TABLE */
								/**************/
								$materi=mPendaftar::getMateri($conn);
								$totalscore=0;
								$rata=0;
								while($listmateri = $materi -> FetchRow()){
									$statusnilaimateri=mPendaftar::getNilaiPesertaMateri($conn, $filter, $filval, $listmateri['kodemateri'])->GetRows();
									$nilaimateri=mPendaftar::getNilaiPesertaMateri($conn, $filter, $filval, $listmateri['kodemateri'])->FetchRow();
									
								?>
									<tr>
										<td align="center"><?= $listmateri['namamateri'] ?></td>
										<td align="center"><input maxlength=5 type="text" name="<?= $listmateri['kodemateri'] ?>" value="<? if(empty($statusnilaimateri)){ echo 0;}else{ echo $nilaimateri['nilai']; } ?>" style="width: 50px;"  ></td>
										
									</tr>
								<?php
								$totalscore=$totalscore+$nilaimateri['nilai'];
								$rata=$rata+$listmateri['nillaiminimum'];
								}
								?>
								<tr align="center" style=" font-weight: bold; background:#c5c5c5; color: #3d3d3b;">
									<td><strong>TOTAL</strong></td>
									<td><input maxlength=5 type="text" name="nilaiujian" value="<? $totalscore=$totalscore/$length; echo $totalscore ?>" style="width: 50px;"  ></td>
									
								</tr>
							</table>
						</td>
					</tr>
				</table>
				<br>
				<center>
				<div style="width: <?= $p_tbwidth-10 ?>px; text-align: center;">
					<input type="hidden" name="filter" value="<?= $filter ?>" >
					<input type="hidden" name="filval" value="<?= $filval ?>" >
					<input type="submit" name="save" value="simpan">
					<input type="submit" name="kembali" value="Kembali">
				</div>
				</center>
				<?php
					}
				?>
			</form>
		</div>
	</div>
</div>
</body>
</html>
