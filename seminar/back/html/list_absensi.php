<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	//$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pesertaseminar'));
	require_once(Route::getModelPath('seminar'));
	require_once(Route::getUIPath('combo'));
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label' => 'No.');
	// $a_kolom[] = array('kolom' => 'namaseminar', 'label' => 'Nama Seminar','readonly'=>true);
	$a_kolom[] = array('kolom' => 'nopeserta', 'label' => 'Nomor Peserta','readonly'=>true);
	$a_kolom[] = array('kolom' => 'nopendaftar', 'label' => 'Nomor Pendaftar','readonly'=>true);
	$a_kolom[] = array('kolom' => 'namapeserta', 'label' => 'Nama','readonly'=>true);
	// $a_kolom[] = array('kolom' => 'rfid', 'label' => 'RFID','readonly'=>true);
	$a_kolom[] = array('kolom' => 'waktucheckin', 'label' => 'Waktu In', 'maxlength' => 5, 'size' => 4, 'format' => 'CStr::formatJam','class'=>'ControlStyle jam');
	$a_kolom[] = array('kolom' => 'waktucheckout', 'label' => 'Waktu Out', 'maxlength' => 5, 'size' => 4, 'format' => 'CStr::formatJam','class'=>'ControlStyle jam');

	// properti halaman
	$p_title = 'Absensi Peserta';
	$p_tbwidth = 800;
	
	$p_model = mPesertaSeminar;
	$p_key = $p_model::key;

	$p_idseminar = CStr::removeSpecial($_REQUEST['idseminar']);
	if(empty($p_idseminar)) {
		$a_flash = array();
		$a_flash['p_posterr'] = true;
		$a_flash['p_postmsg'] = 'Silahkan pilih seminar terlebih dahulu';
		
		Route::setFlashData($a_flash,'list_seminar');
	}
	
	$row_seminar = mSeminar::getData($conn,$p_idseminar);
	
	$datenow = date('Y-m-d');
	
	$absen = true;
	if ($datenow > $row_seminar['tglkegiatan']) {
		$absen = false;
		$p_postabsen = 'tanggal kegiatan sudah terlewati';
	}
	else if (empty($row_seminar['isbuka'])) {
		$absen = false;
		$p_postabsen = 'absensi ditutup';
	}
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		/* if(empty($_POST['i_isaktif'])) {
			$_POST['i_isaktif'] = '0';
		}
		
		$_POST['waktubayar'] = date('Y-m-d H:i:s'); */
		
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		/* if(empty($_POST['u_isaktif'])) {
			$_POST['u_isaktif'] = '0';
		}
		
		$_POST['waktubayar'] = date('Y-m-d H:i:s'); */
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);

	// rfid 
	if (!empty($_POST['rfid']) and $absen) {
		$rfid = $_POST['rfid'];
		list($row,$p_postmsg) = $p_model::getRfid($conn,$p_idseminar,$rfid,true);
		
		if(empty($p_postmsg)) {
			$r_key = $row['peserta']['nopeserta'] ; 
			$r_bukaabsen =  $row['seminar']['isbuka']; 
			$r_checkin =  $row['peserta']['waktucheckin'];
			$r_checkout =  $row['peserta']['waktucheckout'];  
			
			if (empty ($row['peserta']['islunas'])) {
				// cek tarif
				if(!empty($row['peserta']['nim']))
					$t_tarif = (float)$row_seminar['tarifseminarm'];
				else if(!empty($row['peserta']['nip']))
					$t_tarif = (float)$row_seminar['tarifseminarp'];
				else
					$t_tarif = (float)$row_seminar['tarifseminaru'];
				
				if(!empty($t_tarif))
					list($p_posterr,$p_postmsg) = array(true," Pendaftaran belum melakukan pembayaran");
			}
		}
		else
			$p_posterr = true;
		
		if(empty($p_posterr)) {
			if ($r_key and (empty($r_checkin) or empty ($r_checkout)) ){
				if (empty ($r_checkin))
					$record['waktucheckin']  =  date('H:i');
				else if (empty ($r_checkout)) {
					// cek waktu selesai seminar
					$t_akhir = $row_seminar['tglkegiatan'].' '.$row_seminar['jamselesai'];
					if(date('Y-m-d H:i') < $t_akhir)
						list($p_posterr,$p_postmsg) = array(true,'Peserta tidak bisa checkout sebelum seminar selesai');
					else
						$record['waktucheckout']  =  date('H:i');
				}
				
				if(!$p_posterr)
					list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
			}
			else
				$p_postmsg = 'Absensi untuk nomor peserta '.$row['peserta']['nopeserta'].' dengan kode RFID '.$row['peserta']['rfid'].' telah selesai dilakukan';
		}
	}
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	
	// mendapatkan data
	if(!empty($p_idseminar)) 
		$a_filter[] = $p_model::getListFilter('idseminar',$p_idseminar);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	
	if(empty($absen)) {
		$p_posterr = true;
		$p_postmsg = 'Absensi tidak bisa dilakukan<br>tidak bisa hapus peserta<br></r>'.$p_postabsen;
		
		$c_edit = false;
		$c_delete = false;
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
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<style type="text/css">
		.menu-body {
			color: #fff;
			margin: 0;
			padding: 0;
			overflow: hidden;
			border: 6px solid transparent;
			cursor: default;
			background-color: #383838;
			border-radius: 2px;
			box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.3);
		}
	</style>
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
				<center>
					<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
						<span><?= $p_title ?></span>
					</div>
				</center>


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
					<?php if ($p_idseminar){?>
					<div class="filterTable" style="width:<?= $p_tbwidth ?>px;">
						<table width="100%">
							<tr>
								<td width="100">Nama Seminar</td>
								<td width="10">:</td>
								<td width="250"><?= $row_seminar['namaseminar']?></td>
								<td width="10">&nbsp;</td>
								<td width="100">Periode</td>
								<td width="10">:</td>
								<td width="250"><?= $row_seminar['periode']?></td>
							</tr>
							<tr>
								<td>Jenis Seminar</td>
								<td>:</td>
								<td><?= $row_seminar['namajenisseminar']?></td>
								<td>&nbsp;</td>
								<td>Level Seminar</td>
								<td>:</td>
								<td><?= $row_seminar['namalevelseminar']?></td>
							</tr>
							<tr>
								<td>Penyelenggara</td>
								<td>:</td>
								<td><?= $row_seminar['namapenyelenggara']?></td>
								<td>&nbsp;</td>
								<td>Ruang</td>
								<td>:</td>
								<td><?= $row_seminar['koderuang']?></td>
							</tr>
							<tr>
								<td>PIC</td>
								<td>:</td>
								<td><?= $row_seminar['pic']?></td>
								<td>&nbsp;</td>
								<td>Nomor PIC</td>
								<td>:</td>
								<td><?= $row_seminar['nohp']?></td>
							</tr>
							<tr>
								<td>Tanggal Kegiatan</td>
								<td>:</td>
								<td><?= CStr::formatDateInd($row_seminar['tglkegiatan'])?></td>
								<td>&nbsp;</td>
								<td>Jam Kegiatan</td>
								<td>:</td>
								<td><?= $row_seminar['jammulai']?> - <?= $row_seminar['jamselesai']?></td>
							</tr>
							
						</table>
						<?php if ($absen) {?>
						<hr>
						<b>Absen Peserta Seminar</b><br> Silahkan ketikkan Nomor Pendaftar lalu tekan [Enter] pada input dibawah ini <br> atau scan menggunakan RFID <br><br>
						<input type="text" name="rfid" autofocus placeholder="Nomor Pendaftar" style="padding:7px; font-size:13pt; font-weight:bold; text-align:center">
						<?php } ?>
					</div>
					<br>
					<?php } ?>
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
						?>
						<th id="<?= $datakolom['kolom'] ?>"><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	}
							if($c_edit or $c_delete) { ?>
						<th width="50">Edit</th>
						<th width="50">Hapus</th>
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							if($t_key == $r_edit and $c_edit) {
								$rowc = Page::getColumnEdit($a_kolom,'u_','onkeydown="etrUpdate(event)"',$row);
								
								$a_updatereq = array();
					?>
					<tr align="center" valign="top" class="AlternateBG2">
						<?		foreach($rowc as $rowcc) {
									if($rowcc['notnull'])
										$a_updatereq[] = $rowcc['id'];
						?>					
						<td><?= $rowcc['input'] ?></td>
						<?		} ?>
						<td align="center" colspan=3>
							<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onclick="goUpdate(this)" style="cursor:pointer">
						</td>
					</tr>
					<?		}
							else {
								$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td> <?= $no ?> </td>
						<?php /* <td><?= $row['namaseminar'] ?></td> */ ?>
						<td align="center"><?= $row['nopeserta'] ?></td>
						<td align="center"><?= $row['nopendaftar'] ?></td>
						<td><?= $row['namapeserta'] ?></td>
						<?php /* <td align="center"><?= $row['rfid'] ?></td> */ ?>
						<td align="center"><?= $row['waktucheckin'] ?></td>
						<td align="center"><?= $row['waktucheckout'] ?></td>
						<?	
						if (empty($row['isbuka'])) {
							$c_edit = false ; 
						}
								if($c_edit or $c_delete) { ?>
						<td align="center">
						<?			if($c_edit) { ?>
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goEdit(this)" style="cursor:pointer">
						<?			}
						?>
						</td>
						<td align="center">
						<?			if($c_delete) { ?>
							<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer">
						<?			} ?>
						</td>
						<?		} ?>
					</tr>
					<?		}
						}
						if($i == 0) {
					?>
					<tr>
						<td colspan="11" align="center">Data kosong</td>
					</tr>
					<?	}
						if($c_insert) { ?>
					<tr align="center" valign="top" class="LeftColumnBG NoHover">
						<?	$rowc = Page::getColumnEdit($a_kolom,'i_','onkeydown="etrInsert(event)"');
							
							$a_insertreq = array();
							foreach($rowc as $rowcc) {
								if($rowcc['notnull'])
									$a_insertreq[] = $rowcc['id'];
						?>
						<td><?= $rowcc['input'] ?></td>
						<?	} ?>
						<td align="center" colspan=3>
							<img title="Tambah Data" src="images/disk.png" onclick="goInsert()" style="cursor:pointer">
						</td>
					</tr>
					<?	} ?> 
				</table>
				
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
				<input type="hidden" name="nopeserta" id="nopeserta" value="<?= $t_key ?>">
				<input type="hidden" name="idseminar" id="idseminar" value="<?= $p_idseminar ?>">
			</form>
		</div>
	</div>
</div>

<div id="popMenu" class="menubar" style="position:absolute; display:none; top:0px; left:0px;z-index:10000;" onMouseOver="javascript:overpopupmenu=true" onMouseOut="javascript:overpopupmenu=false">
	<table width="130" class="menu-body">
	    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
	        <td onClick="showPage('nopeserta','<?= Route::navAddress('rep_buktibayarseminar') ?>')">Cetak Bukti Bayar</td>
	    </tr>
	</table>
</div>

<script type="text/javascript">
	
var insertreq = "<?= @implode(',',$a_insertreq) ?>";
var updatereq = "<?= @implode(',',$a_updatereq) ?>";

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	// handle scrolltop
	$(window).scrollTop($("#scroll").val());
	
	// handle focus
	// $("[id^='i_']:first").focus();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goUpload() {
	document.getElementById("act").value = "uploadfile";
	goSubmit();
}
function goDeletefile() {
	document.getElementById("act").value = "deletefile";
	goSubmit();
}
</script>
</body>
</html>
