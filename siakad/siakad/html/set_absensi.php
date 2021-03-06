<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=false;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('absensikuliah'));
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getModelPath('krs'));
	require_once(Route::getUIPath('combo'));
	// variabel request
	$r_key = CStr::removeSpecial(Akademik::base64url_decode($_REQUEST['key']));
	//echo $r_key;
	if(empty($r_key))
		Route::navigate($p_listpage);
	list($thnkurikulum,$kodemk,$kodeunit,$periode,$kelasmk,$jeniskul,$kelompok)=explode('|',$r_key);	
	$a_jeniskul=array('K'=>'Kuliah','P'=>'Praktikum','R'=>'Tutorial');
	// properti halaman
	$p_title = 'Pengisian Absensi '.$a_jeniskul[$jeniskul].' Kelompok '.$kelompok;
	$p_tbwidth = 900;
	$p_aktivitas = 'ABSENSI';
	$p_listpage = 'list_absensi';
	$p_printpage = 'rep_isiabsensi';

	$p_model = mAbsensiKuliah;
	
	
	
	// mendapatkan data
	$a_infokelas = mKelas::getDataSingkat($conn,$r_key);
	if($jeniskul=='P')
		$kelprak=$kelompok;
	$a_data = mKelas::getDataPeserta($conn,$r_key,$kelprak);
	$a_kuliah = mKuliah::getListPerKelas($conn,$r_key,true);
	
	$p_kulnum = count($a_kuliah);
	$p_colnum = 3 + $p_kulnum;
	
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		//print_r ($_POST);
		//$conn->debug=true;
		$conn->BeginTrans();
		$a_nim=array();
		foreach($a_kuliah as $t_kuliah) {
			$a_absen = array();
			foreach($a_data as $t_data) {
				
				if(isset($_POST['check_'.$t_kuliah['perkuliahanke'].'_'.$t_kuliah['ftglkuliah'].'_'.$t_data['nim']])){
					$a_absen[$t_data['nim']] = $_POST['check_'.$t_kuliah['perkuliahanke'].'_'.$t_kuliah['ftglkuliah'].'_'.$t_data['nim']];
					$a_nim[$t_data['nim']] = $_POST['check_'.$t_kuliah['perkuliahanke'].'_'.$t_kuliah['ftglkuliah'].'_'.$t_data['nim']];
				}
			}
			
			if(!empty($a_absen)){
				$err = $p_model::savePerPertemuan($conn,$r_key.'|'.$t_kuliah['perkuliahanke'].'|'.$t_kuliah['tglkuliah'],$a_absen);
				if($err) break;
			}
		}
		//die();
		$err = $p_model::updateIjin($conn,$r_key,$a_nim);
		$ok = Query::isOK($err);
		
		$conn->CommitTrans($ok);
		//$conn->debug=false;
		$p_posterr = Query::boolErr($err);
		$p_postmsg = 'Penyimpanan '.$p_model::label.' '.($p_posterr ? 'gagal' : 'berhasil');
	}else if($r_act == 'upload' and $c_edit) {
		$tipe=array('image/jpeg','image/jpg','image/gif','image/png');
		$ext=array('image/jpg'=>'jpg','image/jpeg'=>'jpeg','image/gif'=>'gif','image/png'=>'png');
		$file_types=$_FILES['fileabsen']['type'];
		$file_name=str_replace('|',';',$r_key).'.'.$ext[$file_types];
		if(in_array($file_types,$tipe) && !empty($tipe)){
			$upload=move_uploaded_file($_FILES['fileabsen']['tmp_name'],'uploads/berita/'.$file_name);
			if($upload){
				$record=array();
				$record['fileabsen']=$file_name;
				list($p_posterr,$p_postmsg) = mKelas::updateCRecord($conn,"",$record,$r_key);	
			}else{
				$p_posterr=true;
				$p_postmsg='Upload Gagal';
			}
		}else{
			$p_posterr=true;
			$p_postmsg='Pastikan Tipe Gambar, Upload Gagal';
		}
	}
	
	// mendapatkan data
	$a_absen = $p_model::getListPerKelas($conn,$r_key);
	//print_r($a_absen);
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<center>
				<?php require_once('inc_headerkelas.php') ?>
				</center>
				<br>
				<table width="<?= $p_tbwidth ?>" cellpadding="0" cellspacing="0" align="center">
					
					<tr>
						<td align="center">
							<p style="font-size: 20px;"><b>Jika absensi tidak muncul, diharuskan mengisi Rencana Materi Perkuliahan pada pertemuan yang diinginkan.
							Untuk mengisi Rencana Materi Perkuliahan klik <u class="ULink" onclick="goSubmitBlank('<?= Route::navAddress('list_jurnal') ?>')">di sini</u>.</b></p>
						</td>
					</tr>
				</table>
				<br>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<?	if(isset($p_posterr)) { ?>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				<?	} else { ?>
				<div style="width:<?= $p_tbwidth ?>px">
					<strong><?= $p_postmsg ?></strong>
				</div>
				<?	} ?>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<br>
				<center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<?	if($c_edit) { ?>
						<td align="center" colspan="2">Upload File Scan Absensi Sebagai Bukti Absen Manual</td>
						<? } ?>
					</tr>
					<tr class="NoHover NoGrid">
						<?	if($c_edit) { ?>
						<td width="55"> &nbsp;
							<strong>Upload </strong>
						</td>
						<td>
							<strong> : </strong> <input type="file" name="fileabsen" id="fileabsen" size="30" class="ControlStyle">
							<input type="button" value="Upload" onclick="goUpload()">
						</td>
						<?	}?>
					</tr>
				</table>
				</center>
				<br>
				<center>
					<table style="background:#E0FFF3; border:1px solid #CCC;width:<?= $p_tbwidth ?>px">
						<tr>
							<td colspan="9"><b>Keterangan :</b></td>
						</tr>
						<tr>
							<td>H = HADIR&nbsp;&nbsp;&nbsp;&nbsp;I = IZIN&nbsp;&nbsp;&nbsp;&nbsp;S = SAKIT&nbsp;&nbsp;&nbsp;&nbsp;A = ABSEN </td>
						</tr>
						<tr>
							<td colspan="6"></td>
						</tr>
					</table>
					<br>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
								<h1><?= $p_title ?></h1>
							</div>
							<div class="right">
								<img title="Cetak Absensi" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">
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
						
						$t_rowspan = ($c_edit ? 4 : 3);
					?>
					<tr>
						<th rowspan="<?= $t_rowspan ?>" width="25">No.</th>
						<th rowspan="<?= $t_rowspan ?>" width="80">NIM</th>
						<th rowspan="<?= $t_rowspan ?>">Nama</th>
						<? if(!empty($p_kulnum)) { ?>
						<th colspan="<?= $p_kulnum ?>" style="white-space:nowrap">Tatap Muka</th>
						<? } ?>
					</tr>
					<tr>
						<?	foreach($a_kuliah as $t_kuliah) { ?>
						<th width="120">Pertemuan ke-<?= $t_kuliah['perkuliahanke'] ?></th>
						<?	} ?>
					</tr>
					<tr>
						<?	foreach($a_kuliah as $t_kuliah) { ?>
						<th>
							<? /* <sup><?= (int)substr($t_kuliah['ftglkuliah'],-2) ?></sup>&frasl;<sub><?= (int)substr($t_kuliah['ftglkuliah'],-4,2) ?></sub> */ ?>
							<?= substr($t_kuliah['ftglkuliah'],-2) ?> <?= Date::indoMonth(substr($t_kuliah['ftglkuliah'],-4,2),TRUE) ?>
						</th>
						<?	} ?>
					</tr>
					<?	if($c_edit) { ?>
					<tr>
						<?	foreach($a_kuliah as $t_kuliah) { ?>
						<th><input type="checkbox" id="check_<?= $t_kuliah['perkuliahanke'] ?>_<?= $t_kuliah['ftglkuliah'] ?>"></th>
						<?	} ?>
					</tr>
					<?	}
						
						/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = trim($row['nim']);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $i ?>.</td>
						<td align="center"><?= $row['nim'] ?></td>
						<td><?= STRTOUPPER($row['nama']) ?></td>
						<?	foreach($a_kuliah as $t_kuliah) {
            
                if (empty($a_absen[$t_kuliah['perkuliahanke']][$t_kuliah['tglkuliah']][$t_key][$t_kuliah['jeniskuliah']])){
                $t_selected = 'A';
                }else{
								$t_selected = $a_absen[$t_kuliah['perkuliahanke']][$t_kuliah['tglkuliah']][$t_key][$t_kuliah['jeniskuliah']];
                }
						?>
						<td align="center">
						<?	if($c_edit) { ?>
							<!--input type="checkbox" name="check_<?= $t_kuliah['perkuliahanke'] ?>_<?= $t_kuliah['ftglkuliah'] ?>_<?= $t_key ?>"<?= $t_checked ?>-->
              
              <? if($t_selected=="H")  { ?>
						<select name="check_<?= $t_kuliah['perkuliahanke'] ?>_<?= $t_kuliah['ftglkuliah'] ?>_<?= $t_key ?>">
	                		<option <?=$t_selected=='H'?'selected':''?>><?=$t_selected?></option>
							<option <?=$t_selected=='I'?'selected':''?>>I</option>  
							<option <?=$t_selected=='S'?'selected':''?>>S</option>  
							<option <?=$t_selected=='A'?'selected':''?>>A</option>  
						</select>
              
              <?  } else { ?>  
              
							<select name="check_<?= $t_kuliah['perkuliahanke'] ?>_<?= $t_kuliah['ftglkuliah'] ?>_<?= $t_key ?>">
               					<option <?=$t_selected=='A'?'selected':''?>>A</option>
            					<option <?=$t_selected=='H'?'selected':''?>>H</option>
								<option <?=$t_selected=='I'?'selected':''?>>I</option>
								<option <?=$t_selected=='S'?'selected':''?>>S</option>  
							</select>
              
              <?  } ?>  
                
                
                
						<?	}
							else { ?>
								<?=$t_selected?>
						<?	} ?>
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
						if($c_edit) { ?>
					<tr class="LeftColumnBG">
						<td colspan="<?= $p_colnum ?>" align="center">
							<input type="button" value="Simpan" onclick="goSave()" style="font-size:14px">
						</td>
					</tr>
					<?	} ?>
				</table>
				<?php //echo $r_key.'day';?>
				<input type="hidden" name="act" id="act" >
				<input type="hidden" name="key" id="key" value="<?= Akademik::base64url_encode($r_key) ?>" >
				<input type="hidden" name="subkey" id="subkey" >
				<input type="hidden" name="format" id="format" >
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	$("[id^='check_']").click(function() {
		var checked=$(this).attr('checked');
		if(checked){
			$("[name^='"+this.id+"_']").val("H");
			$("[name^='"+this.id+"_']").addClass("changed");
		}else{
			$("[name^='"+this.id+"_']").val("A");
			$("[name^='"+this.id+"_']").addClass("changed");
		}
	});
	$("select").change(function(){
		$(this).addClass("changed");
	});
});

function goSave(elem) {
	//if($("select").val()=='A')
	//$("select:has(option[value='A']:selected)").attr("disabled", "disabled");
	$("select:not(.changed)").attr("disabled", "disabled");
	document.getElementById("act").value = "save";
	goSubmit();
}
function goUpload(elem) {
	document.getElementById("act").value = "upload";
	goSubmit();
}
function goPrint() {
	goOpen('<?= $p_printpage ?>&key=' + document.getElementById("key").value);
}

</script>
</body>
</html>
