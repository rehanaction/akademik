<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];

	$connportal = Query::connect('portal');
	if($_SERVER['REMOTE_ADDR'] == "36.85.91.184" or $_SERVER['REMOTE_ADDR'] == "66.96.234.212") //ip public sevima
		$connportal->debug=true;
	
	// include
	require_once(Route::getModelPath('rekrutmen'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));		
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nopendaftar', 'label' => 'No. Daftar', 'width' => '70px');
	$a_kolom[] = array('kolom' => 'namalengkap', 'label' => 'Nama', 'width' => '250px');
	$a_kolom[] = array('kolom' => 'sex', 'label' => 'L/P', 'width' => '30px');
	$a_kolom[] = array('kolom' => 'alamat','label' =>'Contact', 'width' => '100px');
	$a_kolom[] = array('kolom' => 'namaposisi', 'label' => 'Ambil Posisi', 'width' => '100px');
	$a_kolom[] = array('kolom' => 'namapendidikan', 'label' => 'Pendidikan', 'width' => '150px');
	$a_kolom[] = array('kolom' => 'keahlian', 'label' => 'Keahlian', 'width' => '200px');
	
	// properti halaman
	$p_title = 'Daftar Pelamar';
	$p_tbwidth = 900;
	$p_aktivitas = 'BIODATA';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mRekrutmen;
	$p_dbtable = "re_calon";
	$p_key = 'nopendaftar';
	$p_colnum = count($a_kolom)+2;

	$r_statuslulus = CStr::removeSpecial($_POST['statuslulus']);

	$a_statuslulus = array_merge(array('all' => 'Semua Status'),$p_model::statusLulus());
	$l_statuslulus = UI::createSelect('statuslulus',$a_statuslulus,$r_statuslulus,'ControlStyle',true,'onchange="goSubmit()"');
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		$conn->BeginTrans();

		$connportal->BeginTrans();

		$a_pdd = $p_model::getRPendidikan($conn,$r_key);
		if(count($a_pdd)>0){
			foreach ($a_pdd as $key => $row) {
				list($p_posterr,$p_postmsg) = $p_model::delete($conn,$row['nopendpelamar'],'re_pendpelamar','nopendpelamar','','fileijazahpelamar,filetranskrippelamar');

				//hapus juga di table portal
				if(!$p_posterr){
					if(!empty($row['refnopendpelamar'])){
						list($p_posterrportal,$p_postmsg) = $p_model::delete($connportal,$row['refnopendpelamar'],'re_pendpelamar','nopendpelamar','portal');
						
						if(!$p_posterrportal){
							$p_posterrportal = Route::deleteFilePortal('fileijazahpelamar', $row['refnopendpelamar']);
							if($p_posterrportal){
								$p_postmsg = 'Hapus file ijazah gagal';
								break;
							}
						}
						
						if(!$p_posterrportal){
							$p_posterrportal = Route::deleteFilePortal('filetranskrippelamar', $row['refnopendpelamar']);
							if($p_posterrportal){
								$p_postmsg = 'Hapus file transkrip gagal';
								break;
							}
						}

						if($p_posterrportal)
							break;
					}
				}else
					break;
			}
		}

		if(!$p_posterr and !$p_posterrportal){
			$a_pkj = $p_model::getRPengalamanKerja($conn,$r_key);
			if(count($a_pkj)>0){
				foreach ($a_pkj as $key => $row) {
					list($p_posterr,$p_postmsg) = $p_model::delete($conn,$row['nopengkerjapelamar'],'re_pengkerjapelamar','nopengkerjapelamar');

					//hapus juga di table portal
					if(!$p_posterr){
						if(!empty($row['refnopengkerjapelamar'])){
							list($p_posterrportal,$p_postmsg) = $p_model::delete($connportal,$row['refnopengkerjapelamar'],'re_pengkerjapelamar','nopengkerjapelamar','portal');

							if($p_posterrportal)
								break;
						}
					}else
						break;
				}
			}
		}
		
		//hapus data pelamar di portal
		if(!$p_posterr and !$p_posterrportal){
			$r_refpelamar = $p_model::getRefPelamar($conn,$r_key);
			if(!empty($r_refpelamar)){
				list($p_posterrportal,$p_postmsg) = $p_model::delete($connportal,$r_refpelamar,'re_calon','nopendaftar','portal');

				//untuk file
				if(!$p_posterrportal){
			        $a_file = array('filelamaran' => 'Lamaran','filecv' => 'CV','fileserdos' => 'File Sertifikat Dosen','filejabakademik' => 'File Jabatan Akademik','filepelatihan' => 'File Pelatihan','fileseminar' => 'File Seminar','filesertifikat' => 'File Sertifikat');

			        foreach ($a_file as $key => $value) {
						$p_posterrportal = Route::deleteFilePortal($key, $r_refpelamar);
						if($p_posterrportal){
							$p_postmsg = 'Hapus file '.$value.' gagal';
							break;
						}
			        }
			    }

		        //hapus foto
		        if(!$p_posterrportal){
			        $p_posterrportal = Route::deleteFilePortal('fotopelamar', $r_refpelamar, true);
					if($p_posterrportal)
						$p_postmsg = 'Hapus foto gagal';
				}
			}
		}

		if(!$p_posterr and !$p_posterrportal)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,'re_prosesseleksi',$p_key);

		if(!$p_posterr and !$p_posterrportal)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key,'','filelamaran,filecv');//hapus juga di table portal
		
		
		if(!$p_posterr and !$p_posterrportal){
			$p_foto = uForm::getPathImagePelamar($conn,$r_key);
			@unlink($p_foto);
		
			$ok = Query::isOK($p_posterr);
			$conn->CommitTrans($ok);
			
			$okportal = Query::isOK($p_posterrportal);
			$connportal->CommitTrans($ok);
		}
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if (empty($r_sort))
		$r_sort = 't_updatetime desc';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);

	if(!empty($r_statuslulus)) $a_filter[] = $p_model::getListFilter('statuslulus',$r_statuslulus);
		
	$sql = $p_model::listQueryPelamar($r_key);	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();

	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Status Lulus', 'combo' => $l_statuslulus);	
	
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
			<form name="pageform" id="pageform" method="post">
				<?	/**************/
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
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<?	if($c_insert) { ?>
							<div class="right">
								<div class="addButton" onClick="goNew()">+</div>
							</div>
							<? } ?>
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
						<th width="50">Aksi</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						if (count($a_data) > 0){
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row,'nopendaftar');
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
							$strong = $row['isview'] == '' ? 'style="font-weight: bold;"' : '';
					?>
					<tr valign="top" class="<?= $rowstyle?>" <?= $strong?>">
						<td align="center"><?= $row['nopendaftar'] ?></td>
						<td><?= $row['namalengkap'] ?></td>
						<td align="center"><?= $row['sex'] ?></td>
						<td>
							<? if(empty($row['alamat'])) { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<? } else { ?>
							<img id="imgcontact" src="images/house.png" title="<?= $row['alamat'] ?>">
							<? } if(empty($row['telp'])) { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<? } else { ?>
							<img id="imgcontact" src="images/telp.png" title="<?= $row['telp'] ?>">
							<? } if(empty($row['hp'])) { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<? } else { ?>
							<img id="imgcontact" src="images/hp.png" title="<?= $row['hp'] ?>">
							<? } if(empty($row['email'])) { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<? } else { ?>
							<img id="imgcontact" src="images/mail.png" title="<?= $row['email'] ?>">
							<? } ?>
						</td>
						<td><?= $row['namaposisi'] ?></td>
						<td><?= $row['namapendidikan'] ?></td>
						<td><?= $row['keahlian'] ?></td>
						<td align="center">
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
						<?		
								if($c_delete) { ?>
							<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer">
						<?		} ?>
						</td>
					</tr>
					<?	}}
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
							Record : <?= uCombo::listRowNum($r_row,'onchange="goLimit()"') ?>
						</div>
						<div style="float:right">
							Halaman <?= $r_page ?> / <?= Page::getTheLastPage();?>
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
				<input type="hidden" name="key" id="key">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/jquery.balloon.min.js"></script>
<script type="text/javascript">

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
	
	// handle contact
	$("[id='imgcontact']").balloon();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

</script>
</body>
</html>
