<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug = true;
	// hak akses
	$a_auth = Modul::getFileAuth();
		// properti halaman
		$p_title = 'Aktivitas Perkuliahan Online';
		$p_aktivitas = 'Aktivitas Perkuliahan';
		$p_tbwidth = "100%";
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_open = $a_auth['canother']['O'];
    // include
    require_once(Route::getModelPath('moodle'));
    $p_model = mMoodle;
    $r_key = CStr::removeSpecial(Akademik::base64url_decode($_REQUEST['key']));
    $key = explode('|',$r_key);
    $idnumber=$key[2].$key[3].$key[1].$key[4];
    $a_data = $p_model::ReportActivityQuiz($conn_moodle,$idnumber);
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
	<link href="style/modal.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<script type="text/javascript" src="scripts/forreport.js"></script>
	<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
    <div id="wrapper">
		<div class="SideItem" id="SideItem">
        <br>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							
						</div>
					</header>
				<?	/*************/
					/* LIST DATA */
					/*************/
				?>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
                    <tr>
                        <th>No</th>
                        <th>MataKuliah</th>
                        <th>Relaisasi</th>
						<th>Jumlah Soal</th>
                        <th>Pertemuan Ke</th>
                    </tr>
                    <?php 
                    $no = 0;
					foreach($a_data as $row){ $no++; 
					?>
                    <tr>
                        <td><?= $no ?></td>
                        <td><?= $row['namakelas']; ?></td>
                        <td><?=$row['realisasi']; ?></td>
						<td><?= $row['soal']; ?></td>
                        <td><?= $row['pertemuanke']+1; ?></td>
                    </tr>
					<?php
					
				
				$section = $row['section'];
				
				} ?>
                </table>
        </div>
    </div>
</div>
</body>
</html>