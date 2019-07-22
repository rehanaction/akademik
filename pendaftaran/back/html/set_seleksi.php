<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	global $conn;
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	
	// include
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getModelPath('jalur'));
    require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Setting Kelulusan Final';
	$p_tbwidth = 950;
	$p_aktivitas = 'DAFTAR';
	$p_model = mPendaftar;
	
	$r_periode 	= Modul::setRequest($_POST['periode'],'PERIODE');
	$r_jalur 	= Modul::setRequest($_POST['jalur'],'JALUR');
	$r_gelombang = Modul::setRequest($_POST['gelombang'],'GELOMBANG');

	$l_periode 	= uCombo::periode($conn,$r_periode,'','periode','onchange="goSubmit()"');
	$l_jalur 	= uCombo::jalur($conn,$r_jalur,'','jalur','onchange="goSubmit()"');
	$l_gelombang 	= uCombo::gelombang($conn,$r_gelombang,'','gelombang','onchange="goSubmit()"');

	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nopendaftar', 'label' => 'No. Pendaftar');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama');
	$p_colnum = count($a_kolom)+15;
	
	if(isset($_POST['save'])){
	    $result=false;
	    //$pendaftar=mPendaftar::getDataPendaftar($conn, $r_periode, $r_jalur, $r_gelombang, $r_tgltes);
	   $pendaftar = $_POST['nopendaftar'];
	    foreach ($pendaftar as $key => $val){

		$pilihan	=$_POST["s".$val];
		$lulus		=$_POST["check".$val];
		$tanggal = date('Y-m-d');
		$record=array();				
		$record['lulusujian']		=$lulus;
		$record['pilihanditerima'] = !empty($record['lulusujian']) ? $pilihan : '';
		$record['tgllulusujian'] = !empty($record['lulusujian']) ? $tanggal : null;		
			
		list($p_posterr,$p_postmsg) = mPendaftar::updateRecord($conn,$record,$val,true);
		
		}
		
		$err = $conn->ErrorNo();
		
		if ($err <> 0){
			list($p_posterr,$p_postmsg) = array(true,'Update kelulusan Gagal');
		}else{
			list($p_posterr,$p_postmsg) = array(false,'Update kelulusan Berhasil');			
		}
	    
	}
	//$data=mPendaftar::getDataPendaftarLulus($conn, $r_periode, $r_jalur, $r_gelombang, $r_tgltes);	
	$cek=mPendaftar::cekJalurPenerimaan($conn,$r_jalur);
	$arrProdi = mCombo::jurusan($conn);
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);

	if(!empty($r_periode)) $a_filter[] 	= $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_jalur)) $a_filter[] 	= $p_model::getListFilter('jalur',$r_jalur);
	if(!empty($r_gelombang)) $a_filter[] 	= $p_model::getListFilter('gelombang',$r_gelombang);
	$a_filter[] = $p_model::getListFilter('isadministrasi',true);

	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Jalur', 'combo' => $l_jalur);
	$a_filtercombo[] = array('label' => 'Gelombang', 'combo' => $l_gelombang);
	
	//$list_jalur=Mjalur::getJalur($conn);
	
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css"> 
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
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
						<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px"><?= $p_postmsg ?></div>
					</center>
				<div class="Break"></div>
				<?	} ?>
				
				<center>
					<i style="color:blue">Keterangan : Data yang ditampilkan adalah pendaftar yang telah lulus kelengkapan seleksi dan sesuai filter periode, jalur dan gelombang</i><br><br>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"><h1>Kelulusan Peserta</h1>
							</div>
						</div>
					</header>
				</center>
				
				<?	/*************/
					/* LIST DATA */
					/*************/
				?>
				<table width="<?= $p_tbwidth ?>" cellpadding="" cellspacing="0" class="GridStyle" align="center">
					<tr align="center" style="height: 30px; font-weight: bold; background: #c5c5c5; color: #4a4949;">
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
					
						<?php $cols=2?>
						<? if($cek['israport']==-1){$cols++;?>
						<th>Lulus Nilai Raport?</th>
						<?} if($cek['istpa']==-1){$cols++;?>
						<th>Lulus TPA?</th>
						<?} if($cek['iskesehatan']==-1){$cols++;?>
						<th>Lulus Tes Kesehatan?</th>
						<?} if($cek['ismatpel']){$cols++;?>
						<th>Lulus Tes Bidang?</th>
						<?} if($cek['iskompetensi']==-1){$cols++;?>
						<th>Lulus Kompetensi?</th>
						<?} if($cek['iswawancara']==-1){$cols++;?>
						<th>Lulus Wawancara?</td>
						<?}?>
						<th width="300">Lulus?</th>
						<th>Pilihan</th>                                                                            
					</tr>
					<?$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							$url="index.php?page=set_nilai&&no=".$t_key;
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
							?>
							<input type="hidden" name="nopendaftar[]" value="<?= $t_key?>">
							<tr valign="top" class="<?= $rowstyle ?>">
								<td><?= $row['nopendaftar'] ?></td>
								<td><?= $row['nama'] ?></td>
								<? if($cek['israport']==-1){?>
								<td><?= $row['lulusnilairaport']=='-1' ? '<img src="images/check.png">' : '';?></td>
								<?} if($cek['istpa']==-1){?>
								<td><?= $row['lulustpa']=='-1' ? '<img src="images/check.png">' : '';?></td>
								<?} if($cek['iskesehatan']==-1){?>
								<td><?= $row['lulusteskesehatan']=='-1' ? '<img src="images/check.png">' : '';?></td>
								<?} if($cek['ismatpel']){?>
								<td><?= $row['lulustespelajaran']=='-1' ? '<img src="images/check.png">' : '';?></td>
								<?} if($cek['iskompetensi']==-1){?>
								<td><?= $row['luluskompetensi']=='-1' ? '<img src="images/check.png">' : '';?></td>
								<?} if($cek['iswawancara']==-1){?>
								<td><?= $row['luluswawancara']=='-1' ? '<img src="images/check.png">' : '';?></td>
								<?}?>
								<td>
									<input type="radio" value="-1" id=<?=$row['lulusujian']==-1?"checked":"checklist"?> name="<?="check".$row['nopendaftar'];?>" <?=($row['lulusujian']==-1)?"checked":""?>>Lulus
									<input type="radio" value="0" name="<?="check".$row['nopendaftar'];?>" <?=($row['lulusujian'] <> -1)?"checked":""?>>Tidak
								</td>
								<td align="left">
									<select name="<? echo "s".$row['nopendaftar']; ?>">
										<?
										for($index=1; $index<=3; $index++){
											$isi="pilihan".$index;
											if($row[$isi] != null or $row[$isi] != ''){
											?>
											<option value="<?= $row[$isi] ?>" <? if($row['pilihanditerima']==$row[$isi]){ echo ' selected="selected" ';} ?> > <?=  $arrProdi[$row[$isi]]; ?></option>
											<?
										}}
										?>
									</select>
								</td>	 						
							</tr>
							<?
						}if($i == 0) { ?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>	

					<?	} ?> <? /*
					<tr>
						<td colspan="<?=$cols?>" align="right"> <b><i>Check / Uncheck All </i></b></td>
						<td align="center"><input type="checkbox" id="checkAll" title="Check/Uncheck All"></td>
						<td>&nbsp;</td>
					</tr> 
					
						*//**********/
						/* FOOTER */
						/**********/ ?>
					<? if(!empty($r_page)) { ?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="right" class="FootBG">
							<div style="float:left"> Record : <?= uCombo::listRowNum($r_row,'onchange="goSubmit()"') ?> </div>
							<div style="float:right"> Halaman <?= $r_page ?> </div>
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
				
				<br>
				
				<center>
				<div style="width: <?= $p_tbwidth-10 ?>px; text-align: center;">
					<input type="submit" name="save" value="simpan">
				</div>
				</center>
			</form>
		</div>
	</div>
</div>

</body>
</html>
<script type="text/javascript">
<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>	
	$(document).ready(function() {
		$("th[id]").css("cursor","pointer").click(function() {
				$("#sort").val(this.id);
				goSubmit();
			});

		$("[id='checkAll']").click(function() {
			var checked = $(this).attr("checked");
			if(checked)
				$("[id='checklist']").attr("checked", checked);
			else
				$("[id='checklist']").removeAttr("checked", checked);
		});
	});
</script>
