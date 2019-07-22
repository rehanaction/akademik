<?php
    // cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug = true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pendaftar'));
	//require_once(Route::getModelPath('syarat'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Import Data Kunjungan';
	$p_tbwidth = 500;
	$p_aktivitas = 'Kunjungan Mahasiswa';
	
	$p_model = mPeriode;
	//$p_key = $p_model::key;
   // $p_colnum = count($p_kolom)+1;
   $r_act = $_POST['act'];
	if($r_act == 'uploadfile') {
      
        //list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
        require_once($conf['includes_dir'].'phpexcel/excel_reader2.php');
        $target = basename($_FILES['filekunjungan']['name']) ;
        move_uploaded_file($_FILES['filekunjungan']['tmp_name'], $target);   
        // beri permisi agar file xls dapat di baca
        chmod($_FILES['filekunjungan']['name'],0777);
 
        // mengambil isi file xls
        $data = new Spreadsheet_Excel_Reader($_FILES['filekunjungan']['name'],false);
        // menghitung jumlah baris data yang ada
        $jumlah_baris = $data->rowcount($sheet_index=0);
        $berhasil = 0;
        $gagal = 0;
        $record = array();
        for ($i=2; $i<=$jumlah_baris; $i++){
        
            // menangkap data dan memasukkan ke variabel sesuai dengan kolumnya masing-masing
            $record['nama']     = $data->val($i, 1);
            $record['nohp']   = $data->val($i, 2);
            $record['kelas']  = $data->val($i, 3);
            $record['asalsekolah']  = $data->val($i, 4);
            $record['tgldistribusi']  = date("Y-m-d");
            $record['instagram']  = $data->val($i, 5);
            $record['idpegawai']  = mPendaftar::getIdPegawai($conn,$data->val($i, 6));        
            if($record['nama']!= "" && $record['nohp'] != "" && $record['idpegawai'] != "" && $record['asalsekolah']!=""){
				// input data ke database (table data_pegawai)
				
                $ok = mPendaftar::insertDataKunjungan($conn,$record);
                if($ok){
                    $berhasil++;
                }else{
                    $gagal++;
                }
               
            }else{
                $gagal++;
            }
            $totaldata = $jumlah_baris-1;
            $p_postmsg = "Total Data : ".$totaldata." Jumlah Data Berhasil : ".$berhasil." Jumlah Data Gagal : ".$gagal;
            $p_posterr = false;
        }
 
// hapus kembali file .xls yang di upload tadi
        unlink($_FILES['filepegawai']['name']);


       
  
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
	<script type="text/javascript" src="scripts/forinplace.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper" style="width:1225px">
		<div class="SideItem" id="SideItem" style="width:1200px">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	}
					
					/************************/
					/* COMBO FILTER HALAMAN */
					/************************/
					
					if(!empty($a_filtercombo)) {
				?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<? foreach($a_filtercombo as $t_filter) { ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>		
										</tr>
										<? } ?>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>
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
                    <br/>
                <form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
                    <label><b>Pilih File : </b></label> 
                    <input name="filekunjungan" type="file" required="required"> 
                    <input name="upload" type="button" value="Import"  onclick="goUpload()">
            
				</center>
              
		
				
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
function goUpload() {
	document.getElementById("act").value = "uploadfile";

	goSubmit();
}
function goDeletefile() {
	document.getElementById("act").value = "deletefile";
   // alert();
	goSubmit();
}
</script>
</body>
</html>
