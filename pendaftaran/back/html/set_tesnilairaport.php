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
	require_once(Route::getModelPath('unit'));
    require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$arrUnit = mUnit::listJurusan($conn);
	
	// properti halaman
	$p_title = 'Kelulusan Nilai Raport';
	$p_tbwidth = 900;
	$p_aktivitas = 'DAFTAR';
	$p_model = mPendaftar;
	
	$r_periode 	= Modul::setRequest($_POST['periode'],'PERIODE');
	$r_jalur 	= Modul::setRequest($_POST['jalur'],'JALUR');
	$r_gelombang = Modul::setRequest($_POST['gelombang'],'GELOMBANG');
		
	if(isset($_POST['btnRefresh'])){
		$r_periode 	= '';
		$r_jalur 	= '';
		$r_gelombang ='';
		$r_tgltes 	= '';
	}
	$l_periode 	= uCombo::periode($conn,$r_periode,'','periode');
	$l_jalur 	= uCombo::jalurNilaiRaport($conn,$r_jalur,'','jalur');
	$l_gelombang 	= uCombo::gelombang($conn,$r_gelombang,'','gelombang');
       
      	
	if(isset($_POST['save'])){
	    $result=false;
	    $fjalur=$_POST['jalur'];
	    $fgelombang=$_POST['gelombang'];
	    $fperiode=$_POST['periode'];
	    
	    $pendaftar=mPendaftar::getDataPendaftarLulus($conn, $fperiode, $fjalur, $fgelombang);
	   
	    while($datapendaftar= $pendaftar->FetchRow()){
		$record=array();		
		$lulus		=$_POST["check".$datapendaftar['nopendaftar']];		
		if($lulus==-1)
			$record['lulusnilairaport']		=$lulus;
		else
			$record['lulusnilairaport']		=0;
		
		$ok=mPendaftar::updatekelulusan($conn, $record, $datapendaftar['nopendaftar']);
		
		if($ok){
		    $result=true;
		}else{
		    $result=false;
		}
	    }
	    if($result==false){
		$p_postmsg="gagal disimpan";
	    }else{
		$p_postmsg="berhasil disimpan";
	    }
	    
	}
	
	$data=mPendaftar::getDataPendaftarLulus($conn, $r_periode, $r_jalur, $r_gelombang);	
	
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Jalur', 'combo' => $l_jalur);
	$a_filtercombo[] = array('label' => 'Gelombang', 'combo' => $l_gelombang);
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="scripts/common.js"></script>
    <script type="text/javascript" src="scripts/forpager.js"></script>
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
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="100%">
							<tr>
								<td width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<? foreach($a_filtercombo as $t_filter) { ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>		
										</tr>
										<? } ?> 
									</table>
								</td>
								<td valign="bottom" align="left" width="50%">
									<input name="btnFilter" type="submit" value="Filter" onClick="goSubmit()">
									<input name="btnRefresh" type="submit" value="Refresh" onClick="goSubmit()">
								</td>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?
					if(!empty($p_postmsg)){
				?>
				<center>
				    <div class="<? if($p_postmsg=="berhasil disimpan"){ echo "DivSuccess";}else echo "DivError"; ?>" style="width:<?= $p_tbwidth ?>px">
					    <?= $p_postmsg ?>
				    </div>
				</center>
				<?
					}
				    /****************/
					/* HEADER TABLE */
					/****************/
				?>
				<br>
				<center>
                                    <header style="width:<?= $p_tbwidth ?>px">
                                        <div class="inner">
                                            <div class="left title">
						<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"><h1>Kelulusan Peserta</h1>
                                            </div>
					</div>
                                    </header>
				</center>
				<table width="<?= $p_tbwidth ?>" cellpadding="" cellspacing="0" class="GridStyle" align="center">
                                    <tr align="center" style="height: 30px; font-weight: bold; background: #c5c5c5; color: #4a4949;">
                                        <td>No Pendaftar</td>
                                        <td>Nama</td>                                        
                                        <td>Pilihan</td>
										<td>X Smt 1</td>
										<td>X Smt 2</td>
										<td>XI Smt 1</td>
										<td>XI Smt 2</td>
										<td>XII Smt 1</td>
										<td>XII Smt 2</td>
										<td>Lulus?</td>		
                                        
                                    </tr>
                                    <?
				    if($data->recordCount()==0){
				    ?>
				    <tr>
					<td colspan=5 align="center">Data Kosong</td>
				    </tr>
				    <?
				    } elseif (isset($data)){
						while($pendaftar = $data->FetchRow()){
							$url="index.php?page=set_nilai&&no=".$pendaftar['nopendaftar'];
							?>
							<tr align="center">
								<td><?= $pendaftar['nopendaftar'] ?></td>
								<td nowrap ><?= $pendaftar['nama'] ?></td>
								
								<td align="left">
										<?
										for($index=1; $index<=3; $index++){
											$isi="pilihan".$index;
											if($pendaftar[$isi] != null or $pendaftar[$isi] != ''){
												echo $index.'. '.$arrUnit[$pendaftar[$isi]]."<br>";
											}
										}
										?>
								</td>
								<td><?=$pendaftar['raport_10_1']; ?></td>
								<td><?=$pendaftar['raport_10_2']; ?></td>
								<td><?=$pendaftar['raport_11_1']; ?></td>
								<td><?=$pendaftar['raport_11_2']; ?></td>
								<td><?=$pendaftar['raport_12_1']; ?></td>
								<td><?=$pendaftar['raport_12_2']; ?></td>
								<td><input type="checkbox" id=<?=$pendaftar['lulusnilairaport']==true?"checked":"checklist"?> value="-1" name="<? echo "check".$pendaftar['nopendaftar']; ?>" <? if($pendaftar['lulusnilairaport']==true) echo ' checked="checked" '; ?>/></td>
								
							</tr>
							<?
						}
					}
					?>
					<tr>
						<td colspan="9" align="right"><b><i> Check / Uncheck All </i></b></td>
						<td align="center"><input type="checkbox" id="checkAll" title="Check/Uncheck All"></td>
					</tr>
				</table>						
				<br>
				<center>
				<div style="width: <?= $p_tbwidth-10 ?>px; text-align: center;">
					<input type="submit" name="save" value="simpan">
				</div>
				</center>
			</form>
		</div>
	</div>
</div>

</body>
</html>
<script>
	$(document).ready(function() {
		$("[id='checkAll']").click(function() {
			var checked = $(this).attr("checked");
			if(checked)
				$("[id='checklist']").attr("checked", checked);
			else
				$("[id='checklist']").removeAttr("checked", checked);
		});
	});
</script>
