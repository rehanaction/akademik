<?php 
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

// hak akses
$a_auth = Modul::getFileAuth();

$c_insert = $a_auth['caninsert'];
$c_edit = $a_auth['canupdate'];
$c_delete = $a_auth['candelete'];
$p_title = 'Bahan Ajar Matakuliah';
$p_tbwidth = 950;
$p_aktivitas = 'Elearning';
require_once(Route::getModelPath('bahaajar'));
$p_detailpage = 'data_materiOnline';
$key = explode('|',$_REQUEST['key']);
$ispjmk = $key[2];
$a_data = mBahanajar::v_detailbahanajar($conn,$key[0],$key[1]);
$r_act = $_POST['act'];
if($r_act=='hapus'){
	//print_r($_POST);
	$ok = mBahanajar::DeleteBahanajar($conn,$_POST['pk']);
	if($ok){
		$p_postmsg = 'Data Berhasil Di hapus ';
		$p_posterr = false;
	}
}
?>

<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<style>
	.judullaporan{ display:none;font-size:14pt;margin-bottom:10px}	
	@media print{
		.WorkHeader, .sidemenu, .tombol, .filterTable, .inner, .FootBG, .infotime, .action, .pagination{ display:none;}	
		.SideItem{border:none;background:#fff}
		.judullaporan{display:block;}
	}
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	} ?>
				<?	if($p_headermhs) { ?>
				<center>
				<?php require_once('inc_headermhs.php') ?>
				</center>
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
				<?	}
				 ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<?	if(!empty($r_page) or $c_insert or  $ispjmk=='0') { ?>
							<div class="right">
								<?	if(!empty($r_page)) { ?>
								<?php require_once('inc_listnavtop.php'); ?>
								<?	}
							if($c_insert or $ispjmk=='0') { ?>
								<div class="addButton" style="float:left;margin-left:10px" onClick="goBaru('<?php echo $_REQUEST['key'] ?>')">+</div>
							<?	} ?>
							</div>
							<?	}
							 ?>
							<div class="right">
								
							</div>
						</div>
					</header>
				</center>
				<?	/*************/
					/* LIST DATA */
					/*************/
				?>
				<center class="judullaporan">
					Bahan Ajar Elearning<br>
					<?=Akademik::getNamaPeriode($r_periode)?>
				</center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
                        <th>Kode Matakuliah</th>
                        <th>Nama Matakuliah</th>
                        <th>Perkuliah Ke</th>
                        <th>Alamat Video</th>
                        <th>Durasi</th>
                        <th>Alamat Modul</th> 
                        <th>Halaman Modul</th>
						<th>Aksi</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							// cek mengulang
						
							$i++;
							
					
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $row['kodemk'] ?></td>
						<td><?= $row['namamk']?></td>
                        <td><?= $row['perkuliahanke']?></td>
                        <td><?= $row['alamatvideo']?></td>
                        <td><?= $row['durasi']?></td>
                        <td><?= $row['alamatmodul']?></td>
                        <td><?= $row['jumlahhalaman']?></td>
						<td align="center" class="action">
						<?php if($c_delete){ ?>
						<img id="<?= $row['id'] ?>" title="Hapus Data" src="images/delete.png" onclick="goHapus(this)" style="cursor:pointer">
						<?php } 
						if($c_edit){ 
						?>
						
						<img id="<?= $row['id'] ?>" title="Perbaharui Data" src="images/edit.png" onclick="goPerbaharui(this)" style="cursor:pointer"></td>
						<?php } ?>
					</tr>
					<?	}

						if($i == 0) {
					?>
					<tr>
						<td colspan="7" align="center">Data kosong</td>
					</tr>
					<?	}
					
						/**********/
						/* FOOTER */
						/**********/
						
						if(!empty($r_page)) { ?>
					<tr>
						<td colspan="<?= $p_colnum+1 ?>" align="right" class="FootBG">
						<div style="float:left">
							Record : <?= uCombo::listRowNum($r_row,'onchange="goSubmit()"') ?>
						</div>
						<div style="float:right">
							Halaman <?= $r_page ?> / <?= $p_pagenum ?>
						</div>
						</td>
					</tr>
					<?	} ?>
				</table>
				<? if(!empty($r_page)) { ?>
				<?php require_once('inc_listnav.php'); ?>
				<? } ?>
				<br><br>
				
				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="pk" id="pk">
				<input type="hidden" name="key" id="key" value='<?= $_REQUEST['key'] ?>'>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
function goPerbaharui(elem) {
	location.href = "index.php?page=data_materiOnline"+"&key=" + elem.id;
	//window.open(location.href,'_BLANK');
	//goSubmitBlank(location.href);
}
function goBaru($key) {
	//alert($key);
	location.href = detailpage + "&params=" + key.value;
}

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	/* $("#xls").change(function() {
		goUpXLS();
	}); */
});

function goChooseXLS() {
	$("#xls").click();
}

function goHapus(that){
	document.getElementById("act").value = "hapus";
	document.getElementById("pk").value = that.id;
	goSubmit();
}

function goDownXLS() {
	document.getElementById("act").value = "downxls";
	goSubmit();
}

function goUpXLS() {
	var upload = confirm("Apakah anda yakin akan mengupdate data dari format excel?");
	if(upload) {
		document.getElementById("act").value = "upxls";
		goSubmit();
	}
}

function goSalin() {
	var fsemester = $("#csemester option:selected").text();
	var ftahun = $("#ctahun option:selected").text();
	
	var salin = confirm("Apakah anda yakin akan menyalin data "+fsemester+" "+ftahun+"?");
	if(salin) {
		document.getElementById("act").value = "copy";
		goSubmit();
	}
}

function toggleImpor() {
	$("#div_impor").toggle();
}

function goPrint() {
	$('#act').val('');
	goOpen('<?= $p_printpage ?>');
}
</script>
</body>
</html>
