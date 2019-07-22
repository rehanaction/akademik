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
	
	// properti halaman
	$p_title = 'Kelengkapan Persyaratan Seleksi';
	$p_tbwidth = 500;
	$p_aktivitas = 'NILAI';
	
	//$conn->debug=true;
	//inisialisasi
	$filter="nopendaftar";
	$filval="0000-0-0-000";
	if(isset($_POST['btnFilter'])){
		$filter=$_POST['filt'];
		$filval=$_POST['filtervalue'];
                
	}
	
	if(isset($_POST['save'])){
		$filter=$_POST['filter'];
		$filval=$_POST['filval'];
		$syarat=mPendaftar::getSyarat($conn, $filter, $filval);
		$row= mPendaftar::getSyarat($conn, $filter, $filval) -> GetRows();
		
		while($syaratpendaftar = $syarat->FetchRow()){
			if($_POST[$syaratpendaftar['idsyaratjalur']]==1){
				$ok=mPendaftar::insertSyaratPendaftar($conn, $filter, $filval, $syaratpendaftar['idsyaratjalur']);
				if($ok){
					$p_postmsg="Persyaratan berhasil dilengkapi";
				}
			}elseif($_POST[$syaratpendaftar['idsyaratjalur']]==0){
				$ok=mPendaftar::deleteSyaratPendaftar($conn, $filter, $filval, $syaratpendaftar['idsyaratjalur']);
				if($ok){
					$p_postmsg="Persyaratan berhasil dibatalkan";
				}
			}			
		}		
		$syaratpendaftar = mPendaftar::cekSyaratPendaftar($conn,$filval);
		$syaratjalur = mPendaftar::cekSyaratJalur($conn,$filval);
		
		if($syaratpendaftar==$syaratjalur){
			$record['isadministrasi'] = '-1';
			$update = Query::recUpdate($conn,$record,"pendaftaran.pd_pendaftar","$filter='$filval'");
		}else{
			$record['isadministrasi'] = '0';
			$update = Query::recUpdate($conn,$record,"pendaftaran.pd_pendaftar","$filter='$filval'");
		}		
	}
	$data = mPendaftar::getDatas($conn, $filter, $filval,'0');
	if($filval != '0000-0-0-000' && empty($data)) $p_postmsgx="Pendaftar tidak ditemukan";
	
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
				
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><img  style=" float: left; margin-top: -5px; margin-right: 10px;" id="img_workflow" width="27px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"><span><?= $p_title ?></span></div></center>
				<br>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<tr>
											<td>
												<select name="filt" class="ControlStyle">
													<option value="nopendaftar">No. Pendaftar</option>
												</select>
											</td>
											<td>
												<input type="text" name="filtervalue" />
											</td>
											<td>
												<input type="submit" value="Filter" name="btnFilter">
											</td>
										</tr>
									</table>
								</td>
								<td>
									<input name="btnRefresh" type="submit" value="Refresh">
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
				<div class="DivSuccess" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<?php
					}
					elseif(isset($p_postmsgx)){
				?>
				<center>
				<div class="DivError" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsgx ?>
				</div>
				</center>
				<?
					}
					if(empty($data)){
					}else{
				?>
				<br>
				<table width="<?= $p_tbwidth-10 ?>" cellpadding="" cellspacing="0" align="center">
					<tr align="center">
						<?
						$data=$data-> FetchRow();
						?>
						<td>
							<div class="filterTable" style="width:<?= $p_tbwidth/2-10 ?>px;">
								<table border=0 width="<?= $p_tbwidth/2-14 ?>" cellpadding="0" cellspacing="0" align="center">
									<tr>
										<td><strong>No Pendaftaran</strong></td>
										<td width=10><strong> : </strong></td>
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
						</td>
					</tr>
					<tr style="height: 10px;">
						
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
								$syarat=mPendaftar::getSyarat($conn, $filter, $filval);	
								
								while($listsyarat = $syarat -> FetchRow()){
									$statussyarat=mPendaftar::getStatusPesertaSyarat($conn, $filter, $filval, $listsyarat['idsyaratjalur'])->GetRows();
									//$statussyarat=mPendaftar::getStatusDaftarUlang($conn, $filter, $filval, $listsyarat['kodesyarat'])->GetRows();
								?>
									<tr>
										<td><?= $listsyarat['namasyarat'] ?></td>
										<td align="center"><input type="checkbox" name="<?= $listsyarat['idsyaratjalur'] ?>" value="1" <?php if(!empty($statussyarat)){ echo 'checked="checked"';}else ?>></td>
									</tr>
								<?php
							}
					
							?>
					
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