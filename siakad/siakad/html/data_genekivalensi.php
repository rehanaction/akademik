<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kurikulum'));
	require_once(Route::getModelPath('ekivaturan'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_kurikulumlama = Modul::setRequest($_POST['kurikulumlama'],'KURIKULUMLAMA');
	$r_kurikulumbaru = Modul::setRequest($_POST['kurikulumbaru'],'KURIKULUM');
	$r_angkatan = Modul::setRequest($_POST['angkatan'],'ANGKATAN');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	$l_kurikulumlama = uCombo::kurikulum($conn,$r_kurikulumlama,'kurikulumlama','onchange="goSubmit()"',false);
	$l_kurikulumbaru = uCombo::kurikulum($conn,$r_kurikulumbaru,'kurikulumbaru','onchange="goSubmit()"',false);
	$l_angkatan = uCombo::angkatan($conn,$r_angkatan,'angkatan','',false);
	
	// struktur view
	$a_kolom = array();
	// $a_kolom[] = array('kolom' => 'thnkurikulum', 'label' => 'Kur. Lama');
	$a_kolom[] = array('kolom' => 'kodemklama', 'label' => 'Kode MK', 'type' => 'S', 'option' => mKurikulum::mkKurikulumUnit($conn,$r_kurikulumlama,$r_unit));
	$a_kolom[] = array('kolom' => 'namamklama', 'label' => 'Nama MK');
	$a_kolom[] = array('kolom' => 'skslama', 'label' => 'SKS');
	
	// $a_kolom[] = array('kolom' => 'tahunkurikulumbaru', 'label' => 'Kur. Baru');
	$a_kolom[] = array('kolom' => 'kodemkbaru', 'label' => 'Kode MK', 'type' => 'S', 'option' => mKurikulum::mkKurikulumUnit($conn,$r_kurikulumbaru,$r_unit));
	$a_kolom[] = array('kolom' => 'namamkbaru', 'label' => 'Nama MK');
	$a_kolom[] = array('kolom' => 'sksbaru', 'label' => 'SKS');
	
	// properti halaman
	$p_title = 'Data Aturan Ekivalensi Mata Kuliah';
	$p_tbwidth = 900;
	$p_aktivitas = 'KULIAH';
	
	$p_model = mEkivAturan;
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		// tambahan unit dan kurikulum
		$a_kolom[] = array('kolom' => 'kodeunitlama', 'value' => $r_unit);
		$a_kolom[] = array('kolom' => 'kodeunitbaru', 'value' => $r_unit);
		$a_kolom[] = array('kolom' => 'thnkurikulum', 'value' => $r_kurikulumlama);
		$a_kolom[] = array('kolom' => 'tahunkurikulumbaru', 'value' => $r_kurikulumbaru);
		$a_kolom[] = array('kolom' => 'relasi', 'value' => 'O');
		
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
		
		// buang lagi unit dan kurikulum
		array_pop($a_kolom);
		array_pop($a_kolom);
		array_pop($a_kolom);
		array_pop($a_kolom);
		array_pop($a_kolom);
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	} /* else if($r_act == 'genekiv' and $c_edit) {
		
		//cek mahasiswa periode daftar = angkatan
		$rs_mhs = $conn->Execute("select nim from akademik.ms_mahasiswa where kodeunit = '".$_POST['unit']."' 
                          and (statusmhs = 'A' or statusmhs = 'C' or statusmhs = 'T') 
                          and periodemasuk like ('".$_POST['angkatan']."%')");

		$arrNim = array();
		while($row = $rs_mhs->fetchRow()){
			$arrNim[] = $row['nim'];
		}
		if(count($arrNim)>0)
			$nim = implode("','",$arrNim);
		
		for($i=0;$i<count($arrNim);$i++){
			// echo "<br>".$i;
			//matkul ekiv tdk lulus
			$strTL = "select kodemk,nim,kodemkbaru from akademik.ak_transkrip k join akademik.ak_ekivaturan e on e.kodemklama = k.kodemk and e.tahunkurikulumbaru = '".$_POST['kurikulumbaru']."' and e.thnkurikulum = k.thnkurikulum 
					and e.kodeunitbaru = k.kodeunit where nim in ('".$arrNim[$i]."') and lulus=0 and 
					kodemklama not in (select kodemk from akademik.ak_ekivmhs where nim ='".$arrNim[$i]."' and thnkurikulum=e.thnkurikulum)
					";
			// }
			$rsTL = $conn->Execute($strTL);
			while($rowTL = $rsTL->fetchRow()){
				$kodemkTL[$rowTL['nim']][] = $rowTL['kodemkbaru'];
			}
			$mkTL = '';
			if(count($kodemkTL[$arrNim[$i]])>0)
				$mkTL = implode("','",$kodemkTL[$arrNim[$i]]);
			
			//matkul ekiv lulus
			$strL = "select distinct(kodemkbaru) as kodemkbaru from akademik.ak_transkrip k 
					join akademik.ak_ekivaturan e on e.kodemklama = k.kodemk 
                                        and e.tahunkurikulumbaru = '".$_POST['kurikulumbaru']."' 
                                        and e.thnkurikulum = k.thnkurikulum
					where nim in ('".$arrNim[$i]."') and lulus<>0 and e.kodemkbaru not in ('".$mkTL."')";
			$rsL = $conn->Execute($strL);
			while($rowL = $rsL->fetchRow())
      
      {
				$record['kodemk'] = $rowL['kodemkbaru'];
				$record['kodeunit'] = $r_unit;
				$record['thnkurikulum'] = $r_kurikulumbaru;
				$record['nim'] = $arrNim[$i];
        
				$cek = $conn->GetOne("select 1 from ak_ekivmhs where nim = '".$record['nim']."' and kodeunit = '".$record['kodeunit']."'
									and thnkurikulum = '".$record['thnkurikulum']."' and kodemk='".$record['kodemk']."'");
                  
				if($cek!=1){
					$err = Query::recInsert($conn,$record,'akademik.ak_ekivmhs');
					$nimm[] = $record['nim'];
				}
			}
		}
		if(count($nimm)>0)
			$nimall = implode("','",$nimm);
		$jumnim = $conn->GetOne("select count(*) from akademik.ms_mahasiswa where nim in ('".$nimall."')");
		$p_posterr = false;
		$p_postmsg = $jumnim." mahasiswa telah diproses ekivalensi";
		
	} */
	else if($r_act == 'genekiv' and $c_edit) {
		//cek mahasiswa periode daftar = angkatan yang belum diekivalensi
		$sql = "select m.nim from akademik.ms_mahasiswa m
				left join akademik.ak_ekivmhs e on e.nim = m.nim and e.thnkurikulum = ".Query::escape($r_kurikulumbaru)."
				where m.kodeunit = ".Query::escape($r_unit)." and m.statusmhs in ('A','C','T')
				and substr(m.periodemasuk,1,".strlen($r_angkatan).") = ".Query::escape($r_angkatan)."
				and e.nim is null";
		$rs_mhs = $conn->Execute($sql);
		
		$jumnim = $jumnimgagal = 0;
		while($row = $rs_mhs->FetchRow()) {
			// mulai transaksi
			$conn->BeginTrans();
			
			// mengambil mata kuliah baru
			$strL = "insert into akademik.ak_ekivmhs (kodemk,kodeunit,thnkurikulum,nim,t_updateuser,t_updatetime,t_updateip,t_updateact)
					select e.kodemkbaru,e.kodeunitbaru,e.tahunkurikulumbaru,'".$row['nim']."',".Query::logInsert().",'i-{$i_page}'
					from akademik.ak_ekivaturan e
					left join akademik.ak_ekivmhs m on m.kodemk = e.kodemklama and m.thnkurikulum = e.thnkurikulum and m.kodeunit = e.kodeunitlama
						and m.nim = ".Query::escape($row['nim'])."
					left join akademik.ak_transkrip t on t.kodemk = e.kodemklama and t.thnkurikulum = e.thnkurikulum /*and t.kodeunit = e.kodeunitlama*/
						and t.lulus <> 0 and t.nim = ".Query::escape($row['nim'])."
					where e.tahunkurikulumbaru = ".Query::escape($r_kurikulumbaru)." and e.thnkurikulum = ".Query::escape($r_kurikulumlama)."
						and e.kodeunitbaru = ".Query::escape($r_unit)." and e.kodeunitlama = ".Query::escape($r_unit)."
					group by e.kodemkbaru,e.kodeunitbaru,e.tahunkurikulumbaru
					having min(case when m.kodemk is null and t.kodemk is null then 0 else 1 end) = 1";
			$conn->Execute($strL);
			$err = $conn->ErrorNo();
			
			// selesai transaksi
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
			
			if($ok)
				$jumnim++;
			else
				$jumnimgagal++;
		}
		
		$p_posterr = false;
		$p_postmsg = $jumnim." mahasiswa berhasil dan ".$jumnimgagal." gagal diproses ekivalensi";
	}
	else if($r_act == 'delekiv' and $c_delete) {
		$sql = "delete from akademik.ak_ekivmhs e where e.thnkurikulum = ".Query::escape($r_kurikulumbaru)."
				and e.kodeunit = ".Query::escape($r_unit)." and exists
				(select 1 from akademik.ms_mahasiswa m where m.nim = e.nim and substr(periodemasuk,1,".strlen($r_angkatan).") = ".Query::escape($r_angkatan).")";
		$conn->Execute($sql);
		
		$p_posterr = $conn->ErrorNo();
		$p_postmsg = 'Pembatalan proses ekivalensi mahasiswa '.(empty($p_posterr) ? 'berhasil' : 'gagal');
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	
	// mendapatkan data
	$a_filter[] = $p_model::getListFilter('thnkurikulumlama',$r_kurikulumlama);
	$a_filter[] = $p_model::getListFilter('kodeunitlama',$r_unit);
	$a_filter[] = $p_model::getListFilter('thnkurikulumbaru',$r_kurikulumbaru);
	$a_filter[] = $p_model::getListFilter('kodeunitbaru',$r_unit);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Prodi', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Kurikulum Lama', 'combo' => $l_kurikulumlama);
	$a_filtercombo[] = array('label' => 'Kurikulum Baru', 'combo' => $l_kurikulumbaru);
	$a_filtercombo[] = array('label' => 'Angkatan', 'combo' => $l_angkatan);
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
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?	/************************/
					/* COMBO FILTER HALAMAN */
					/************************/
					
					if(!empty($a_filtercombo)) {
				?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth ?>px;box-sizing:border-box">
						<table width="100%" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">

<tr>

<td style="white=space:nowrap"><strong>Kurikulum Lama</strong></td>
<td> : <?= $a_filtercombo[1]['combo'] ?></td>
</tr>

										<tr>		
<td style="white-space:nowrap"><strong>Kurikulum Baru</strong></td>		
											
<td> : <?= $a_filtercombo[2]['combo'] ?></td>		
										</tr>



										<tr>		
											<td width="100" style="white-space:nowrap"><strong>Angkatan</strong></td>		
											<td> : <?= $a_filtercombo[3]['combo'] ?></td>		
										</tr>	
										<? $t_filter = $a_filtercombo[0]; ?>
										<tr>		
											<td style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>
										</tr>
										<?php if($c_edit or $c_delete) { ?>
										<tr>
											<td colspan="2">
												<?php if($c_edit) { ?>
												<input type="button" value="Proses Ekiv. Mahasiswa" class="ControlStyle" onclick="goEkivalensi()">
												<?php } if($c_delete) { ?>
												<input type="button" value="Batalkan Ekiv. Mahasiswa" class="ControlStyle" onclick="goHapusEkiv()">
												<?php } ?>
											</td>
										</tr>
										<?php } ?>
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
						<th colspan="3">Kurikulum <?= $r_kurikulumlama ?></th>
						<th colspan="3">Kurikulum <?= $r_kurikulumbaru ?></th>
					</tr>
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
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						$t_skslama = $t_sksbaru = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							// total sks
							$t_skslama += $row['skslama'];
							$t_sksbaru += $row['sksbaru'];
							
							/* if($t_key == $r_edit and $c_edit) {
								$rowc = Page::getColumnEdit($a_kolom,'u_','onkeydown="etrUpdate(event)"',$row);
								
								$a_updatereq = array();
								foreach($rowc as $rowcc) {
									if($rowcc['notnull'])
										$a_updatereq[] = $rowcc['id'];
								}
					?>
					<tr valign="top" class="AlternateBG2">
						<td colspan="3"><?= Page::getDataInputOnly($rowc,'u_kodemklama') ?></td>
						<td colspan="3"><?= Page::getDataInputOnly($rowc,'u_kodemkbaru') ?></td>
						<td align="center" colspan="2">
							<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onclick="goUpdate(this)" style="cursor:pointer">
						</td>
					</tr>
					<?		}
							else { */
								$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?		foreach($rowc as $rowcc) {
									list($rowcc) = explode(' - ',$rowcc);
						?>					
						<td><?= $rowcc ?></td>
						<?		}?>
					</tr>
					<?		// }
						}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}?>
					<tr>
						<th colspan="2">Total SKS Kurikulum <?= $r_kurikulumlama ?></th>
						<th><?php echo $t_skslama ?></th>
						<th colspan="2">Total SKS Kurikulum <?= $r_kurikulumbaru ?></th>
						<th><?php echo $t_sksbaru ?></th>
					</tr>
				</table>
				
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
		</div>
	</div>
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
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goSalin() {
	document.getElementById("act").value = "copy";
	goSubmit();
}


function goEkivalensi(kodeunit){
	var ekiv = confirm("Pastikan Aturan Ekivalensi telah benar,sebelum melakukan proses ekivalen ini..\nLanjutkan Proses Ekivalensi Mahasiswa?");
	if(ekiv) {
		document.getElementById("act").value = "genekiv";
		goSubmit();
	}
}

function goHapusEkiv(){
	var ekiv = confirm("Apakah anda yakin akan membatalkan proses ekivalensi mahasiswa terkait?");
	if(ekiv) {
		document.getElementById("act").value = "delekiv";
		goSubmit();
	}
}

</script>
</body>
</html>
