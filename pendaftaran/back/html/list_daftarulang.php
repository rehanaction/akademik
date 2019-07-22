<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_editpass = $c_edit;
	
	// include
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	//$conn->debug=true;
	// variabel request
	$r_periode 	= Modul::setRequest($_POST['periode'],'PERIODE');
	$r_lulus 	= Modul::setRequest($_POST['lulus'],'LULUS');
	$r_tgltes 	= Modul::setRequest($_POST['tgltes'],'TGLTES');
	if(!empty($r_tgltes))
		$r_tgltes=date('Y-m-d',strtotime($r_tgltes));
	//$r_service 	= Modul::setRequest($_POST['onedayservice'],'ONEDAYSERVICE');
	
	
	//combo
	$l_periode 	= uCombo::periode($conn,$r_periode,'','periode','onchange="goSubmit()"');
	$l_lulus 	= uCombo::lulus($conn,$r_lulus,'','lulus','onchange="goSubmit()"');
	$a_input= array('kolom' => 'tgltes', 'label' => 'Tanggal Tes', 'type' => 'D','add' => 'onchange="goSubmit()"');
	$i_tanggal=uForm::getInput($a_input,$r_tgltes);
	//$l_service  =uCombo::getOnedayservice($conn,$r_service,'','onedayservice','onchange="goSubmit()"');
	/*$l_service ='<select name="onedayservice" id="onedayservice" onchange="goSubmit()" class="ControlStyle">';
	$l_service .='<option value="" '.($r_service==''?'selected':'').'>-- Pilih service --</option>';
	$l_service .='<option value="-1" '.($r_service=='-1'?'selected':'').'>One Day Service</option>';
	$l_service .='<option value="0" '.($r_service=='0'?'selected':'').'>Bukan One Day Service</option>';
	$l_service.='</select>';*/
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nopendaftar', 'label' => 'No. Pendaftar');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'periodedaftar', 'label' => 'Periode');
	$a_kolom[] = array('kolom' => 'pilihanditerima', 'label' => 'Jurusan', 'readonly'=>true,'option' => mCombo::jurusan($conn),'empty'=>true);
	$a_kolom[] = array('kolom' => 'isadministrasi','label'=>'Keterangan');
	$a_kolom[] = array('kolom' => 'tgllulusujian','label'=>'Batas Daftar Ulang');
	
	
	// properti halaman
	$p_title = 'Data Deadline Daftar Ulang';
	$p_tbwidth = 900;
	$p_aktivitas = 'BIODATA';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mPendaftar;
	$p_colnum = count($a_kolom)+5;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		//kurangi kuota
		$rs = $conn->getRow("select p.idjadwaldetail,jd.idjadwal,tgltes,jumlahpeserta from pendaftaran.pd_pendaftar p
					left join pendaftaran.pd_jadwaldetail jd on jd.idjadwaldetail = p.idjadwaldetail
					left join pendaftaran.pd_jadwal j on j.idjadwal=jd.idjadwal 
					where nopendaftar='$r_key'");
		$jmlpesertabaru = $rs['jumlahpeserta']-1;
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		$update = $conn->Execute("update pendaftaran.pd_jadwal set jumlahpeserta='$jmlpesertabaru' where idjadwal='".$rs['idjadwal']."'");
		
		//unlink file foto
		$src = "../back/uploads/fotocamaba/".$r_periode."-".$r_jalur."-".$r_gelombang."/".$r_key.".jpg";
		unlink($src);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	
	// mendapatkan data
	if(!empty($r_periode)) $a_filter[] 	= $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_lulus)) $a_filter[] 	= $p_model::getListFilter('lulus',$r_lulus);
	if(!empty($r_tgltes)) $a_filter[] 	= $p_model::getListFilter('tgltes',$r_tgltes);
	//if($r_service!='') $a_filter[] 	= $p_model::getListFilter('onedayservice',$r_service);
	
	$a_data = $p_model::getBatasDu($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Lulus', 'combo' => $l_lulus);
	$a_filtercombo[] = array('label' => 'Tanggal Tes', 'combo' => $i_tanggal);
	//$a_filtercombo[] = array('label' => 'One Day Service', 'combo' => $l_service);
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	 <script type="text/javascript" src="scripts/countdown.js"></script>
	 	 <script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>

</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper" style="width:1100px">
		<div class="SideItem" id="SideItem" style="width:1100px">
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
						<?	if($c_edit or $c_delete) { ?>
						<th width="50">Edit</th>
						<th width="50">Hapus</th>
						<th width="50">Cetak</th>
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$x = 0;
						foreach($a_data as $row) {
							if ($x % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $x++;
							$t_key = $p_model::getKeyRow($row);
							if($row['isadministrasi']==-1){
								$ket='Sudah Diverifikasi';
								$bg='green';
							}else if($row['isadministrasi']==0){
								$ket='Belum Diverifikasi';
								$bg='yellow';
							}
							$j = 0;
							if($row['isdaftarulang'] == '0' and !empty($row['tgllulusujian'])){
								$tgllulus = $row['tgllulusujian'];
								$tglnow = date('Y-m-d');
								
								// memecah bagian-bagian dari tanggal lulus
								$pecahtgldu = explode("-", $tgllulus);

								// membaca bagian-bagian dari $date1
								$thn1 = $pecahtgldu[0];
								$bln1 = $pecahtgldu[1];
								$tgl1 = $pecahtgldu[2];

								// counter looping
								$i = 0;
								// counter untuk jumlah hari minggu
								$sum = 0;

								do
								{
								   // mengenerate tanggal berikutnya
								   $tanggal = date("Y-m-d", mktime(0, 0, 0, $bln1, $tgl1+$i, $thn1));

								   // cek jika harinya sabtu dan minggu, maka counter $sum bertambah satu, lalu tampilkan tanggalnya
								   if (date("w", mktime(0, 0, 0, $bln1, $tgl1+$i, $thn1)) == 0 or date("w", mktime(0, 0, 0, $bln1, $tgl1+$i, $thn1)) == 6 ){
									 $sum++;
								   } 	 
									// increment untuk counter looping
								   $i++;
								}
								while ($tanggal != $tglnow);
								
								
								$hari = (int)$row['lamadaftarulang'] + $sum;
								$tgljatuhtempo=strtotime($tgllulus.' +'.$hari.' days');
								
								//cek jatuh tempo
								$tgljatuhtempokotor = date('Y-m-d',$tgljatuhtempo);
								$thn2 = $pecahtgljtk[0];
								$bln2 = $pecahtgljtk[1];
								$tgl2 = $pecahtgljtk[2];
								if (date("w", mktime(0, 0, 0, $bln2, $tgl2, $thn2)) == 0){ //minggu, nambah 2 hari lagi
									$tgljatuhtempo=strtotime($tgljatuhtempokotor.' +2 days');
								}else if (date("w", mktime(0, 0, 0, $bln2, $tgl2, $thn2)) == 6 ){ //sabtu, nambah 1 hari lagi
									$tgljatuhtempo=strtotime($tgljatuhtempokotor.' +1 days');
								}
								
								if($tgljatuhtempo < strtotime(date('Y-m-d'))){
									$pesan='Batas Daftar Ulang Habis!!';
									
								}else{
									$pesan=date('Y-m-d',$tgljatuhtempo)." 00:00:00 GMT+07:00";
								}
							
							}
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center"><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td align="center"><?= $rowc[$j++] ?></td>
						<td align="center"><?= $rowc[$j++] ?></td>
						<td align="center" bgcolor="<?=$bg?>"><?= $ket?></td>
						<td align="center"><span id="countdown<?=$x?>"><?=$pesan?></span></td>
						<?	if($c_edit or $c_delete) { ?>
						<td align="center">
						<?		if($c_edit) { ?>
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
						<?		}
						?>
						</td>
						<td align="center">
						<?		if($c_delete) { ?>
							<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer">
						<?		} ?>
						</td>
						<td align="center">
							<img id="<?= $t_key ?>" title="Cetak Kartu Ujian" src="images/kartu_ujian.png" onclick="goOpen('rep_kartu&id=<?= $t_key?>')" style="cursor:pointer">
							<img id="<?= $t_key ?>" title="Cetak Formulir Pendaftaran" src="images/formulir.png" onclick="goOpen('rep_formulir&id=<?= $t_key?>')" style="cursor:pointer">
						</td>
						<?	} ?>
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
							Halaman <?= $r_page ?>
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
					
				<input type="hidden" name="npm" id="npm">
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
