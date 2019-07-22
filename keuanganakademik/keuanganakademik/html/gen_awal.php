<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
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
	$p_title = 'Generate Tagihan Pendaftar Lulus';
	$p_tbwidth = 450;
	$p_aktivitas = 'TRANSAKSI';
	       
	$a_input = array();
	$a_input['nopendaftar'] = array('kolom' => 'nopendaftar', 'label' => 'nopendaftar');
	$r_nopendaftar = $_POST['nopendaftar'];
	$r_act = $_POST['act'];
	if($r_act == 'generate' and $c_edit){
			$r_nopendaftar = $_POST['nopendaftar'];
			$datapendaftar = mAkademik::getDatapendaftar($conn,$r_nopendaftar);
			list($p_posterr, $p_postmsg) = mTagihan::generateTagihanpendaftar($conn, $r_nopendaftar, $datapendaftar);	
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
				<?	} 
					if(!empty($p_postmsg)) { ?>
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
					<?	/********/
						/* DATA */
						/********/
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
                    	<tr>
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">
								No Pendaftar
							</td>
							<td class="RightColumnBG">
								<?= uForm::getInput($a_input['nopendaftar'], $r_nopendaftar) ?>
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
                        - Tagihan ini untuk mengenerate tagihan daftar ulang
                        <br>
                        - Pendaftar telah lulus namun belum di generate NIM
                        <br>
                        - Tagihan yang akan di generate adalah :
                        <br>
                        1. Tagihan awal perkuliahan
                        <br>
                        2. Tagihan di semester Pertama
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
