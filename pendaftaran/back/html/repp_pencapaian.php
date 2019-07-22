<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_editpass = $c_edit;
	
	// include
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getModelPath('sistemkuliah'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
//	$conn->debug=true;
    // variabel request
    
    // properti halaman
	$p_title = 'Report Pencapaian';
	$p_tbwidth = 1100;
	$p_aktivitas = 'BIODATA';
    $p_detailpage = Route::getDetailPage();
    $r_key = Modul::getUserName();
    $p_model = mPendaftar;
    $c_header = $p_model::getPeriodeDaftar($conn);
    $a_data = $p_model::getPencapaian($conn);
	$a_detai = $p_model::getPencapaianDetail($conn);
	$a_semester = $p_model::getPencapaianSemester($conn);
	$a_ptahun = $p_model::getPencapaianTahun($conn);
 	//print_r($a_ptahun);
?>


<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	
	<script type="text/javascript" src="scripts/forpager.js"></script>
	 <script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper" style="width:1100px">
		<div class="SideItem" id="SideItem" style="width:1100px">
			<form name="pageform" id="pageform" method="post">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	} ?>
				<?php require_once('inc_listfilter.php'); ?>
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
                <?	/*************/
					/* LIST DATA */
					/*************/
				?>
                	<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
                        <tr>
                            <th Rowspan='2'>Nama Marketing</th>
							<?php $colspanheader=1; 
								  $thnarr = array();
								  $i=0;
								  foreach($c_header as $kolom){
									 
									  if(Pendaftaran::getTahun($kolom['periodedaftar'])==Pendaftaran::getTahun($c_header[$i+1]['periodedaftar'])){
										$colspanheader++;  
										$thnarr[Pendaftaran::getTahun($c_header[$i+1]['periodedaftar'])]=$colspanheader; 
									  }else{
										$colspanheader++; 
										$thnarr[Pendaftaran::getTahun($c_header[$i+1]['periodedaftar'])]=$colspanheader; 
									  }
									  $i++;
									  $colspanheader=0;
								  }
								 ?>
								 <?php foreach($thnarr as $key=>$value){
									 if($key!=0){ ?>

									 <th colspan=<?= $value ?>> <?= $key ?> </th>
									 <?php }
								 }?>
                            
                        </tr>
						<tr>
						<?php foreach($c_header as $kolom){ ?>
                            <th><?= Pendaftaran::getNamaPeriode($kolom['periodedaftar']) ?></th>
                            <?php } ?>
						</tr>
				


                  
                                <?php 
                                //$total = 0;
                                foreach($a_data as $row){ ?>
                                <tr>
                                    <td><?=$row['nama']?></td>
                                    <?php foreach($c_header as $kolom){ ?>

                                        <td align='center'><?= $a_detai[$kolom['periodedaftar'].$row['idpegawai']] ?> </td>

                                    <?php } ?>
                                    
                                </tr>

                                <?php } ?>
                            
                                <tfoot>
                                    <tr>
                                        <td rowspan='2' align="center"><b>Total</b></td>
										<?php foreach($thnarr as $key=>$value){
									 if($key!=0){ ?>

									 <td align="center" colspan=<?= $value ?>><b> <?= $a_ptahun[$key] ?> </b></th>
									 <?php }
									 }?>
                                
                                    </tr>

									<tr>
									<?php foreach($c_header as $kolom){ ?>

											<td align='center'><?= $a_semester[$kolom['periodedaftar']] ?></td>

											<?php } ?>


									</tr>
									

                                </tfoot>

                    </table>