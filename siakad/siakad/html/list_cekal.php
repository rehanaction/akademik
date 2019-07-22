<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('perwalian'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getUIPath('combo'));
	//$conn->debug=true;
	// variabel request
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	
	
	// combo
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nim', 'label' => 'N I M');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'semestermhs', 'label' => 'Sem');
	$a_kolom[] = array('kolom' => 'prasyaratspp', 'label' => 'Cekal Keuangan');
	$a_kolom[] = array('kolom' => 'cekalakad', 'label' => 'Cekal Akademik');
	$a_kolom[] = array('kolom' => 'isuts', 'label' => 'Cekal UTS');
	$a_kolom[] = array('kolom' => 'isuas', 'label' => 'Cekal UAS');
	$a_kolom[] = array('kolom' => 'isdispensasi_uts', 'label' => 'Dispensasi UTS');
	$a_kolom[] = array('kolom' => 'isdispensasi_uas', 'label' => 'Dispensasi UAS');
	$a_kolom[] = array('kolom' => 'm.keterangan', 'label' => 'Keterangan');


	
	// properti halaman
	$p_title = 'Daftar Cekal Mahasiswa';
	$p_tbwidth = 750;
	$p_aktivitas = 'BIODATA';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mPerwalian;
	$p_colnum = count($a_kolom)+3;
	$legendstatusmhs=true;
	$legendjkmhs=true;
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	$a_filter[] = $p_model::getListFilter('periode',$r_tahun.$r_semester); 
	$a_filter[] = $p_model::getListFilter('unit',Modul::getUnit());
	
	$a_data = $p_model::listCekal($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	 $a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
	
	
	
	// filter tree
	$a_filtertree = array();
	$a_filtertree['unit'] = array('label' => 'Prodi', 'data' => mCombo::unitTree($conn,true));
	$a_filtertree['angkatan'] = array('label' => 'Angkatan', 'data' => mMahasiswa::angkatan($conn));
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/hint.min.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
<?	if(!empty($a_filtertree)) { ?>
	<link href="style/jquery.treeview.css" rel="stylesheet" type="text/css">
<?	} ?>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?php require_once('inc_listfiltertree.php') ?>
				
				<?	if(!empty($a_filtertree)) { ?>
				<div style="float:left;width:760px">
				<?	}
					
					/**************/
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
				<div class="DivError" style="display:none"></div>
				<div class="DivSuccess" style="display:none"></div>
				<div class="Break"></div>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<?	if(!empty($r_page) or $c_insert) { ?>
							<div class="right">
								<?	if(!empty($r_page)) { ?>
								<?php require_once('inc_listnavtop.php'); ?>
								<?	}
									if($c_insert) { ?>
								<div class="addButton" style="float:left;margin-left:10px" onClick="goNew()">+</div>
								<?	} ?>
							</div>
							<?	} ?>
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
						
					</tr>
					<!--tr>
						<th id="nim">N I M </th>
						<th id="nama">Nama </th>
						<th id="semestermhs">Sem. </th>
						<th id="prasyaratspp">Cekal Keuangan </th>
						<th id="cekalakad">Cekal Akademik </th>
						<th id="m.keterangan">Keterangan </th>
												
					</tr-->
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {	
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td align="center"><?= $rowc[$j++] ?></td>
						<td align="center"><input type="checkbox" id="<?=$t_key?>" <?=($row['prasyaratspp']==0)?'checked':''?> title="Lepas Cekal" onclick="bukaSPP(this)" <?=!$c_edit?'disabled':''?>></td>
						<td align="center"><input type="checkbox" id="<?=$t_key?>" <?=($row['cekalakad']==-1)?'checked':''?> title="Lepas Cekal" onclick="bukaCekal(this)" <?=!$c_edit?'disabled':''?>></td>
						<td align="center"><input type="checkbox" id="<?=$t_key?>" <?=($row['isuts']==0)?'checked':''?> title="Lepas Cekal" onclick="bukaCekalUts(this)" <?=!$c_edit?'disabled':''?>></td>
						<td align="center"><input type="checkbox" id="<?=$t_key?>" <?=($row['isuas']==0)?'checked':''?> title="Lepas Cekal" onclick="bukaCekalUas(this)" <?=!$c_edit?'disabled':''?>></td>
						<td align="center"><input type="checkbox" id="<?=$t_key?>" <?=($row['isdispensasi_uts']==0)?'checked':''?> title="Lepas Cekal" onclick="bukaCekalDispenUts(this)" <?=!$c_edit?'disabled':''?>></td>
						<td align="center"><input type="checkbox" id="<?=$t_key?>" <?=($row['isdispensasi_uas']==0)?'checked':''?> title="Lepas Cekal" onclick="bukaCekalDispenUas(this)" <?=!$c_edit?'disabled':''?>></td>
						<td align="center">
							<?php if($c_edit){?>
								<?= UI::createTextBox('i_keterangan',$row['keterangan'],'ControlStyle',400,25, true, 'key="'.$t_key.'"', '')?>
								<img title="Simpan" src="images/disk.png" onclick="upKeteraangan('<?=$t_key?>')" style="cursor:pointer">
							<?php
							}else{
								echo $row['keterangan'];
							}
							?>
						</td>
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
				<div style="clear:both"></div>
				<div>
					<?// require_once('inc_legendstatusmhs.php')?>
				</div>
				<br>

				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key"> 
				<input type="hidden" name="npm" id="npm">
				<?	if(!empty($a_filtertree)) { ?>
				</div>
				<?	} ?>
			</form>
		</div>
	</div>
</div>


<script type="text/javascript" src="scripts/jquery.balloon.min.js"></script>
<?	if(!empty($a_filtertree)) { ?>
<script type="text/javascript" src="scripts/jquery.cookie.js"></script>
<script type="text/javascript" src="scripts/jquery.treeview.js"></script>
<script type="text/javascript" src="scripts/jquery-ui.js"></script>
<?	} ?>
<script type="text/javascript">

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";
var cookiename = '<?= $i_page ?>.accordion';

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	// handle contact
	// $("[id='imgcontact']").balloon();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	<?	if(!empty($a_filtertree)) { ?>
	initFilterTree();
	<?	} ?>
});
function sukses(msg){
	$(".DivSuccess").html(msg);
	$(".DivSuccess").show();
	//$(".DivSuccess").fadeOut(2000);
}
function gagal(msg){
	$(".DivError").html(msg);
	$(".DivError").show();
	$(".DivError").fadeOut(2000);
}
function bukaSPP(elem){
	if(elem.checked){
		var posted = "f=unsetSPP&q[]="+elem.id;
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var msg=text.split('|');
			if(msg[0]==''){
				sukses(msg[1]);
			}else{
				gagal(msg[1]);
			}
		});
	}else if(!elem.checked){
		var posted = "f=setSPP&q[]="+elem.id;
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
function bukaCekal(elem){
	if(elem.checked){
		var posted = "f=cekal&q[]="+elem.id;
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var msg=text.split('|');
			if(msg[0]==''){
				sukses(msg[1]);
			}else{
				gagal(msg[1]);
			}
		});
	}else if(!elem.checked){
		var posted = "f=bukaCekal&q[]="+elem.id;
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
function bukaCekalUts(elem){
	var value;
	if(elem.checked)
		value=0;
	else
		value=-1;
	
	var posted = "f=cekalUts&q[]="+elem.id+"&q[]="+value;
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		var msg=text.split('|');
		if(msg[0]==''){
			sukses(msg[1]);
		}else{
			gagal(msg[1]);
		}
	});
}
function bukaCekalUas(elem){
	var value;
	if(elem.checked)
		value=0;
	else
		value=-1;
	
	var posted = "f=cekalUas&q[]="+elem.id+"&q[]="+value;
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		var msg=text.split('|');
		if(msg[0]==''){
			sukses(msg[1]);
		}else{
			gagal(msg[1]);
		}
	});
}
function bukaCekalDispenUts(elem){
	var value;
	if(elem.checked)
		value=0;
	else
		value=-1;
	
	var posted = "f=dispenUts&q[]="+elem.id+"&q[]="+value;
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		var msg=text.split('|');
		if(msg[0]==''){
			sukses(msg[1]);
		}else{
			gagal(msg[1]);
		}
	});
}
function bukaCekalDispenUas(elem){
	var value;
	if(elem.checked)
		value=0;
	else
		value=-1;
	
	var posted = "f=dispenUas&q[]="+elem.id+"&q[]="+value;
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		var msg=text.split('|');
		if(msg[0]==''){
			sukses(msg[1]);
		}else{
			gagal(msg[1]);
		}
	});
}
function upKeteraangan(key){
	var keterangan=$("#i_keterangan").val();
	var posted = "f=upKeteraangan&q[]="+key+"&q[]="+keterangan;
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var msg=text.split('|');
			if(msg[0]==''){
				sukses(msg[1]);
			}else{
				gagal(msg[1]);
			}
		});
}
$("#i_keterangan").on('keyup', function(e) {
    if (e.which == 13) {
		var key=$(this).attr('key');
        upKeteraangan(key);
    }
});
</script>
</body>
</html>
