<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Generate Tagihan Mahasiswa Wisuda';
	$p_tbwidth = 450;
	$p_aktivitas = 'TRANSAKSI';
	       
	$a_input = array();
	$a_input['periodeyudisium'] = array('kolom' => 'periodeyudisium', 'label' => 'Periode Yudisium', 'type' => 'S', 'option' => mAkademik::getArrayperiodeyudisium($conn));
	$a_input['nim'] = array('kolom' => 'nim', 'label' => 'NIM');
	
	$r_act = $_POST['act'];
	if($r_act == 'generate' and $c_edit){
			$r_nim = $_POST['nim'];
			$datamhs = mAkademik::getDatamhs($conn,$r_nim);
			$r_kodeunit = $datamhs['kodeunit'];
			$r_periodeyudisium = $_POST['periodeyudisium'];
			
			$err = mTagihan::generateTagihanwisuda($conn, $r_nim, $r_kodeunit, $r_periodeyudisium, '','0');
			
		}
	

?>

<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forreport.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	} ?>
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
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">
								Periode
							</td>
							<td class="RightColumnBG">
								<?= uForm::getInput($a_input['periodeyudisium']) ?>
							</td>
						</tr><tr>
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">
								NIM
							</td>
							<td class="RightColumnBG">
								<?= uForm::getInput($a_input['nim']) ?>
							</td>
						</tr>
					</table>
					<div class="Break"></div>
                    <input type="hidden" name="act" id="act">
                    <input type="button" value="Generate" class="ControlStyle" onClick="goGenerate()">
					</div>
				</center>
			</form>
            <div style="clear:both"></div>
				<div>
					<fieldset style="background:#E0FFF3; border:1px solid #CCC;">
                        <legend> Keterangan </legend>
                        Tagihan yang di generate sesuai dengan nim dan periode yang anda inputkan, tanpa pengecekan ke daftar yudisium
                    </fieldset>
				</div>
		</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	
function goGenerate(){
	var txt = confirm("Apakah anda yakin akan melakukan generate tagihan ini?");
	if(txt) {
		document.getElementById("act").value = "generate";
		
		goSubmit();
	}
	}

</script>
</body>
</html>
