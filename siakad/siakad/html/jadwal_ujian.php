<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	 
	// hak akses
	Modul::getFileAuth();
	//$conn->debug=true;
 
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('kelaspraktikum'));
	require_once(Route::getModelPath('mengajar'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	if(Akademik::isDosen()){
		
		$r_key = Modul::getUserIDPegawai();
		//$r_dosen = Modul::getUserName() ? Modul::getUserName().' - '.$_SESSION['SIAKAD']['MODUL']['USERDESC'] : $_SESSION['SIAKAD']['MODUL']['USERDESC'];
		}
	else{
		$r_key = CStr::removeSpecial($_REQUEST['idpegawai']);
		//$r_dosen = CStr::removeSpecial($_REQUEST['dosen']);
	}
	$t_mengajar = -1;
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	
	
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	$r_nama = Akademik::getNamaPegawai($conn,$r_key);
	$r_dosen=$r_nama;
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Mengajar Kelas';
	$p_tbwidth = "100%";
	$p_aktivitas = 'MENGAJAR';
	
	$p_model = mMengajar;
	$a_jenis=array('K'=>'Kuliah','P'=>'Praktikum','R'=>'Tutorial');
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label' => 'No.', 'width' =>'10px');
	
	$a_kolom[] = array('kolom' => 'a.kodemk', 'label' => 'Kode Matakuliah', 'width' =>'100px');
	$a_kolom[] = array('kolom' => 'd.namamk', 'label' => 'Nama Matakuliah');	
	$a_kolom[] = array('kolom' => 'c.namaunit', 'label' => 'Prodi', 'width' =>'100px');
	$a_kolom[] = array('kolom' => 'sistemkuliah', 'label' => 'Basis', 'type' => 'S', 'option' => mMahasiswa::sistemKuliah($conn),'readonly'=>true, 'width' =>'100px');

	//$a_kolom[] = array('kolom' => 'd.semmk', 'label' => 'Smt.');
	$a_kolom[] = array('kolom' => 'a.kelasmk', 'label' => 'Kelas', 'width' =>'10px');
	$a_kolom[] = array('kolom' => 'f_namahari(b.nohari)', 'alias' => 'namahari', 'label' => 'Hari');
	$a_kolom[] = array('kolom' => 'b.jammulai', 'label' => 'Mulai', 'format' => 'CStr::formatJam');
	$a_kolom[] = array('kolom' => 'b.jamselesai', 'label' => 'Selesai', 'format' => 'CStr::formatJam');
	$a_kolom[] = array('kolom' => 'b.koderuang', 'label' => 'Ruang');
	$a_kolom[] = array('kolom' => 'a.jeniskul', 'label' => 'Jenis', 'type' => 'S', 'option' =>$a_jenis, 'width' =>'10px');
	// /$a_kolom[] = array('kolom' => 'a.kelompok', 'label' => 'Kel(Prakt)');
	$a_kolom[] = array('kolom' => 'b.jumlahpeserta', 'alias' => 'jmlpeserta', 'label' => 'Jumlah Mahasiswa', 'width' =>'10px');
	
	$p_colnum = count($a_kolom)+1;
	$r_sort='namamk,kelasmk';
	$a_filter = Page::setFilter($_POST['filter']);
	
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_key)) $a_filter[] = $p_model::getListFilter('nipdosen',$r_key);
	if(!empty($t_mengajar)) $a_filter[] = $p_model::getListFilter('tugasmengajar',$t_mengajar);
	
	// mendapatkan data
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	
	
	// membuat filter
	if(empty($r_key))
		$r_dosen = '';
	
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Dosen', 'combo' => UI::createTextBox('dosen',$r_dosen,'ControlStyle',0,60).' <input type="button" value="Tampilkan" onclick="goSubmit()">');
	
	$a_combodosen=array();
	$a_combodosen[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
		<!--div style="float:left; width:15%">
				<?// require_once('inc_sidemenudosen.php');?>
			</div-->
			<div>

			<form name="pageform" id="pageform" method="post">

				<?php require_once('inc_headerdosen.php') ?>
				</center>
				<br>
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
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
						<?	list($t_sort) = explode(',',$r_sort);
							trim($t_sort);
							list($t_col,$t_dir) = explode(' ',$t_sort);
							
							foreach($a_kolom as $datakolom) {
								if($t_col == $datakolom['kolom'])
									$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
								else
									$t_sortimg = '';
								
								$t_width = $datakolom['width'];
								if(!empty($t_width))
									$t_width = ' width="'.$t_width.'"';
						?>
						<th id="<?= $datakolom['kolom'] ?>"<?= $t_width ?>><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	} ?>
						<th width="30">Aksi</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = mKelasPraktikum::getKeyRow($row);
							
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?	foreach($rowc as $j => $rowcc) {
								$t_align = $a_kolom[$j]['align'];
								if(!empty($t_width))
									$t_align = ' align="Center"';
						?>
						<td <?= $t_align ?>><?= $rowcc ?></td>

						<?	} ?>
						<td><img id="<?= $t_key ?>" title="Halaman Kuliah" src="images/link.png" onclick="goPop('<?=$row['jeniskul']=='Kuliah'?"popMenu":"popMenuPrak"?>',this,event)" style="cursor:pointer"></td>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
				
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				
					
				<? if(!Akademik::isDosen()) { ?>
				<input type="hidden" id="nip" name="nip" value="<?= $r_key ?>">
				<? } ?>
			</form>
			<!--/div-->
		</div>
	</div>
</div>

<!-- pop up untuk kuliah -->
<div id="popMenu" class="menubar" style="position:absolute; display:none; top:0px; left:0px;z-index:10000;" onMouseOver="javascript:overpopupmenu=true" onMouseOut="javascript:overpopupmenu=false">
<table width="200" class="menu-body">
    
	
    <?php 
    	if (Akademik::isAdmin() || Akademik::isDosen()) { ?>
    		<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
		        <td onClick="showPage('key','<?= Route::navAddress('set_pesertakelas') ?>')">Peserta</td>
		    </tr>
    		<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
		        <td onClick="showPage('key','<?= Route::navAddress('list_jurnal') ?>')">Rencana Pembelajaran Semester</td>
		    </tr>
		    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
		        <td onClick="showPage('key','<?= Route::navAddress('set_absensi') ?>')">Absensi</td>
		    </tr>
		    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
		        <td onClick="showPage('key','<?= Route::navAddress('set_nilai') ?>')">Isi Nilai</td>
		    </tr>
    <?php } ?>
	
    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('key','<?= Route::navAddress('list_jadwalujian') ?>')">Jadwal Ujian</td>
    </tr>
</table>
</div>

<!-- pop up untuk praktikum -->
<div id="popMenuPrak" class="menubar" style="position:absolute; display:none; top:0px; left:0px;z-index:10000;" onMouseOver="javascript:overpopupmenu=true" onMouseOut="javascript:overpopupmenu=false">
<table width="200" class="menu-body">
    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('key','<?= Route::navAddress('set_pesertakelas') ?>')">Peserta</td>
    </tr>
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('key','<?= Route::navAddress('list_jurnal') ?>')">Rencana Pembelajaran Semester</td>
    </tr>
    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('key','<?= Route::navAddress('set_absensi') ?>')">Absensi</td>
    </tr>
</table>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	$("#dosen").xautox({strpost: "f=acdosen", targetid: "nip"});
});

</script>
</body>
</html>
