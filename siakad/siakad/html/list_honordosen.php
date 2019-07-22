<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('honordosen'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getModelPath('emailhonor'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Setting Honor Mengajar';
	$p_tbwidth = 900;
	$p_aktivitas = 'ABSENSI';
	$p_model = mHonorDOsen;
	$a_addcolfilter=array('namadepan','namatengah','namabelakang','namamk');
	
	
	// variabel request
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_sistemkuliah	= Modul::setRequest($_POST['sistemkuliah'],'SISTEMKULIAH');
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_bulanbayar = Modul::setRequest($_POST['bulanbayar'],'BULANBAYAR');
	$r_tahunbayar = Modul::setRequest($_POST['tahunbayar'],'TAHUN');
	$r_bulanpengajuan = Modul::setRequest($_POST['bulanpengajuan'],'BULANPENGAJUAN'); 
	$r_tahunpengajuan = Modul::setRequest($_POST['tahunpengajuan'],'TAHUN'); 
	$r_nopengajuan = Modul::setRequest($_POST['nopengajuan'],'NOPENGAJUAN'); 
	
	$r_periode=$r_tahun.$r_semester;
	$r_periodegaji=$r_tahunbayar.str_pad($r_bulanbayar,2,'0',STR_PAD_LEFT);
	$r_periodepengajuan=$r_tahunpengajuan.str_pad($r_bulanpengajuan,2,'0',STR_PAD_LEFT);
	
	$status_kuliah=array('0'=>'Tatap Muka','-1'=>'Online');	
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'g.nipdosenrealisasi', 'label' => 'Nama Dosen');
	$a_kolom[] = array('kolom' => 'kodemk', 'label' => 'Nama Mata Kuliah');
	$a_kolom[] = array('kolom' => 'g.skshonor', 'label' => 'SKS');
	$a_kolom[] = array('kolom' => 'kelasmk','label' => 'Sesi');
	$a_kolom[] = array('kolom' => 'kl.sistemkuliah','label' => 'Basis');
	$a_kolom[] = array('kolom' => 'tglkuliahrealisasi', 'label' => 'Tanggal', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'jeniskuliah', 'label' => 'Aktifitas','type' => 'S', 'option' => mKuliah::jenisKuliah($conn));
	$a_kolom[] = array('kolom' => 'k.isonline', 'label' => 'Jenis','type' => 'S', 'option' => $status_kuliah);
	$a_kolom[] = array('kolom' => 'perkuliahanke', 'label' => 'Perkuliahan Ke');
	$a_kolom[] = array('kolom' => 'honordosen', 'label' => 'Honor');
	$a_kolom[] = array('kolom' => 'nopengajuan', 'label' => 'Nomor Pengajuan');
	$a_kolom[] = array('kolom' => 'g.keterangan', 'label' => 'Keterangan');
	
	$f = 3;
	
	// properti halaman tambahan
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		if(!$p_posterr){
			$unset=mKuliah::unsetHonor($conn,$r_key);
		}
	}else if($r_act == 'genGaji' and $c_insert) {
	
// 	if( $_SERVER['REMOTE_ADDR']=='172.16.88.105' || $_SERVER['REMOTE_ADDR']=='172.16.137.14' || $_SERVER['REMOTE_ADDR']=='172.16.137.13'  )
// 	{
// 		$conn->debug=true;
// 		require_once('/var/www/Debug.php');
// 		Zend_Debug::dump($r_periodepengajuan,'$r_periodepengajuan',true);
// 	}
// 	else
// 		$conn->debug=false;

		list($p_posterr,$p_postmsg) = $p_model::genGaji($conn,$conn_sdm,$r_unit,$r_periode,$r_periodegaji,$r_periodepengajuan,$r_sistemkuliah);

//	$conn->debug=false;

	}else if($r_act == 'delPerNomor' and $c_delete) {
		if(empty($r_nopengajuan))
			list($p_posterr,$p_postmsg)=array(true,'Mohon pilih Nomor pengajuan');
		else
			list($p_posterr,$p_postmsg) = $p_model::delPerNomor($conn,$r_nopengajuan);
	}
	
	
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_sistemkuliah))	$a_filter[] = $p_model::getListFilter('sistemkuliah',$r_sistemkuliah);
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_periodegaji)) $a_filter[] = $p_model::getListFilter('periodegaji',$r_periodegaji); 
	if(!empty($r_nopengajuan)) $a_filter[] = $p_model::getListFilter('nopengajuan',$r_nopengajuan); 
	
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// mendapatkan data
	$l_nopengajuan = uCombo::nopengajuan($conn,$p_model,$r_nopengajuan,'nopengajuan','onchange="goSubmit()"',true,$r_periode,$r_unit,$r_periodegaji);
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Nomor Pengajuan', 'combo' => $l_nopengajuan,'delete'=>true,'function'=>'goDeletePerNomor(this)');
	
	

?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper" align="center">
		<div class="SideItem" id="SideItem">
			
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
			
				<?	if(!empty($p_postmsg)) { ?>
			 
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				 
				<div class="Break"></div>
				<?	} ?>
				<br>
					<header style="width:<?= $p_tbwidth-200 ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?=$p_title?></h1>
							</div>
							
						</div>
					</header>
				<table width="<?= $p_tbwidth-200 ?>" cellpadding="6" cellspacing="0" class="GridStyle">
					<tr>		
						<td width="160"> &nbsp; <strong>Pilih Periode Akademik</strong></td>
						<td>
							<?=uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false).' '.uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);?>
							
						</td>
					</tr>
					<tr>		
						<td width="160"> &nbsp; <strong>Pilih Sistem Kuliah</strong></td>
						<td>
							<?= uCombo::sistemkuliah($conn,$r_sistemkuliah,'sistemkuliah','onchange="goSubmit()"',false);?>
						</td>
					</tr>
					<tr>		
						<td width="100"> &nbsp; <strong>Pilih Prodi </strong></td>
						<td>
							<?=uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);?>
							
						</td>
					</tr>
					<tr>		
						<td width="100"> &nbsp; <strong>Pilih Periode Pembayaran </strong></td>
						<td>
							<?=uCombo::bulan($r_bulanbayar,'bulanbayar','onchange="goSubmit()"',false).' '.uCombo::tahun($r_tahunbayar,true,'tahunbayar','onchange="goSubmit()"',false);?>
						</td>
					</tr>
					<tr>		
						<td width="100"> &nbsp; <strong>Pilih Bulan pengajuan </strong></td>
						<td>
							<?=uCombo::bulan($r_bulanpengajuan,'bulanpengajuan','',false).' '.uCombo::tahun($r_tahunpengajuan,true,'tahunpengajuan','',false);?>
						</td>
					</tr>
					<tr>		
						<td width="100">&nbsp;</td>
						<td>
							
							<input type="button" value="Generate Honor Mengajar" onclick="goGenGaji()">
						</td>
					</tr>
				</table><br>
				
				<?php require_once('inc_listfilterhonor.php'); ?>
				<br>
				<div class="DivError" style="display:none"></div>
				<div class="DivSuccess" style="display:none"></div>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<div class="right">
								<?	if(!empty($r_page)) { ?>
								<?php require_once('inc_listnavtop.php'); ?>
								<?	} ?>
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
						<?	list($t_sort) = explode(',',$r_sort);
							trim($t_sort);
							list($t_col,$t_dir) = explode(' ',$t_sort);
							
							foreach($a_kolom as $i => $datakolom) {
								if(empty($datakolom['label']))
									continue;
								
								if($t_col == $datakolom['kolom'])
									$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
								else
									$t_sortimg = '';
						?>
						<th id="<?= $datakolom['kolom'] ?>"><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	} ?>
						
						<?	if($c_edit) { ?>
						<th width="30">Validasi</th>
						<?	} if($c_delete) { ?>
						<th width="30">Hapus</th>
						<?	} ?>
						
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) { 
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							$j = $f;
							
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $row['nipdosenrealisasi'] ?>-<?= $row['namadosen'] ?></td>
						<td><?= $row['kodemk'] ?> - <?= $row['namamk'] ?></td> 
						<td><?= $row['sks'] ?></td>
						<td><?= $row['kelasmk'] ?></td>
						<td><?= $row['basis'] ?></td>
						<td><?= $row['tglkuliahrealisasi'] ?></td> 
						<td><?= $row['jeniskuliah'] ?></td> 
						<td><?= $row['isonline'] ?></td> 
						<td><?= $row['perkuliahanke'] ?></td> 
						<td><?= number_format($row['honordosen'],0,',','.') ?></td> 
						<td><?= $row['nopengajuan'] ?></td> 
						<td><?= $row['keterangan'] ?></td> 
						<?php if($c_edit){ ?>
						<td align="center"><input type="checkbox" id="<?=$t_key?>" <?=($row['validhonor']==-1)?'checked':''?> title="Validasi honor" onclick="validasi(this)" <?=!$c_edit?'disabled':''?>></td>
						<? } if($c_delete) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
						<?		} ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	} 
					/**********/
						/* FOOTER */
						/**********/
						
						if(!empty($r_page)) { ?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="right" class="FootBG">
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
				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="keyjadwal" id="keyjadwal">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
			
		</div>
	</div>
</div>
<script type="text/javascript">
<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>	

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});


function goGenGaji(){
	document.getElementById("act").value = "genGaji";
	goSubmit();
}
function sukses(msg){
	$(".DivSuccess").html(msg);
	$(".DivSuccess").show();
	$(".DivSuccess").fadeOut(2000);
}
function gagal(msg){
	$(".DivError").html(msg);
	$(".DivError").show();
	$(".DivError").fadeOut(2000);
}
function validasi(elem){
	if(elem.checked){
		var posted = "f=valid&q[]="+elem.id;
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var msg=text.split('|');
			if(msg[0]==''){
				sukses(msg[1]);
			}else{
				gagal(msg[1]);
			}
		});
	}else if(!elem.checked){
		var posted = "f=unvalid&q[]="+elem.id;
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var msg=text.split('|');
			if(msg[0]==''){
				sukses(msg[1]);
			}else{
				gagal(msg[1]);
			}
		});
	}
}
function goDeletePerNomor(elem){
	document.getElementById("act").value = "delPerNomor";
	if(confirm('Yakin Menghapus Data ?'))
		goSubmit();
}
function goSendMail(){
	document.getElementById("act").value = "sendMail";
	goSubmit();
}
</script>
</body>
</html>
