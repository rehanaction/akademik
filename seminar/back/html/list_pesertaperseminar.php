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
	$a_kolom[] = array('kolom' => ':no', 'label' => 'Nomor');
	$a_kolom[] = array('kolom' => 'nopeserta', 'label' => 'Nomor Peserta','readonly' => true);
	$a_kolom[] = array('kolom' => 'namaseminar', 'label' => 'Nama Seminar','readonly' => true);
	$a_kolom[] = array('kolom' => 'namapeserta', 'label' => 'Nama Peserta','readonly' => true);
	$a_kolom[] = array('kolom' => 'waktudaftar', 'label' => 'Waktu Daftar', 'type' => 'D','readonly' => true);
	$a_kolom[] = array('kolom' => 'tarif', 'label' => 'Tarif','readonly' => true);
	$a_kolom[] = array('kolom' => 'islunas', 'label' => 'Lunas', 'type'=>'C' , 'option' => array(1 => ''));
	$a_kolom[] = array('kolom' => 'waktubayar', 'label' => 'Waktu Bayar', 'type' => 'D');
	
	// properti halaman
	$p_title = 'Daftar Peserta Per Seminar';
	$p_tbwidth = 950;
	
	$p_model = mPesertaSeminar;
	$p_key = $p_model::key;

	$p_idseminar = CStr::removeSpecial($_REQUEST['idseminar']);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		/* if(empty($_POST['i_isaktif'])){
			$_POST['i_isaktif'] = '0';
		} */

		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		/* if(empty($_POST['u_isaktif'])){
			$_POST['u_isaktif'] = '0';
		} */

		if (!empty($_POST['u_islunas'])) {
			if(empty($_POST['u_waktubayar']))
				$_POST['u_waktubayar'] = date('d-m-Y');
		} else {
			$_POST['u_waktubayar'] = NULL;
		}

		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	
	// mendapatkan data
	if(!empty($p_idseminar)) 
		$a_filter[] = $p_model::getListFilter('idseminar',$p_idseminar);
	
	$row_seminar = mSeminar::getData($conn,$p_idseminar);
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
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
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	} 
				?>
				<center>
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
						</table> 
					</div>
					<br>	 
				</center>
				<br> 
					<?php if(!empty($p_postmsg)) { ?>
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
						<?	} ?>
						<th width="50">Cetak</th>
						<?	if($c_edit or $c_delete) { ?>
						<th width="50">Edit</th>
						<th width="50">Hapus</th>
						<!--
						<th width="50">Link</th>
						-->
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
					<tr align="center" valign="top" class="<?= $rowstyle ?>">
						
						<td> <?= $no ?> </td>
						<td><?= $row['nopeserta'] ?></td>
						<td><?= $row['namaseminar'] ?></td>
						<td><?= $row['namapeserta'] ?></td>
						<td><?= $row['waktudaftar'] ?></td>
						<td><?= $row['tarif'] ?></td>
						<td><?= $row['islunas'] ?></td>
						<td><?= $row['waktubayar'] ?></td>
						<td>
							<?php if ($row['islunas']) {?>
							<a href="<?php echo  Route::navAddress('rep_bayarseminar&key='.$t_key)?>" target="_BLANK">
								<img src="images/formulir.png" title="cetak bukti pembayaran">
							</a>
						</td>
							<?php } ?>
						<?php
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
						<!--
						<td align="center">
							<img id="<?= $t_key ?>" title="Cetak Bukti Bayar" src="images/link.png" onclick="goPop('popMenu',this,event)" style="cursor:pointer">
						</td>
						-->
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
