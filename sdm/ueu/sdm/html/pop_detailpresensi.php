<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_edit = false;
	$c_delete = false;
	if(Modul::getRole() == 'A' or Modul::getRole() == 'admpres'){
		$c_edit = true;
		$c_delete = true;
	}
	
	// include
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getModelPath('pegawai'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_periode = CStr::removeSpecial($_REQUEST['periode']);
	$peg = mPegawai::getSimplePegawai($conn,$r_key);
	
	// properti halaman
	$p_title = 'Daftar Detail Presensi';
	$p_tbwidth = 900;
	$p_col = 7;
	$p_key = 'tglpresensi,idpegawai';
	$p_dbtable = 'pe_presensidet';
	
	$p_model = mPresensi;
	
	$t_periode = 'Periode '.Date::indoMonth((int) substr($r_periode,4,2),true).' '.substr($r_periode,0,4);
	
	// ada aksi
	$r_act = $_POST['act'];
	$tgl = CStr::removeSpecial($_REQUEST['subkey']);
	if($r_act == 'save' and $c_edit) {
		$jamdatang = !empty($_POST['jamdatang'.$tgl]) ? str_replace(':','',$_POST['jamdatang'.$tgl]) : 'null';
		$jampulang = !empty($_POST['jampulang'.$tgl]) ? str_replace(':','',$_POST['jampulang'.$tgl]) : 'null';
		
		$record['jamdatang'] = $jamdatang;
		$record['jampulang'] = $jampulang;
		
		list($p_posterr,$p_postmsg) = $p_model::saveDetailPresensi($conn, $record,$tgl,$r_key,$p_dbtable,$p_key);
	}
	else if($r_act == 'delete' and $c_delete) {
		$key = $tgl.'|'.$r_key;
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$key,$p_dbtable,$p_key);	
	}
	
	//mendapatkan data dari presensi
	$a_data = $p_model::getPresensi($conn,$r_key,$r_periode);
	if(count($a_data)>0){
		foreach($a_data as $row){
			$data[$row['tglpresensi']]['absensi'] = $row['absensi'];
			$data[$row['tglpresensi']]['kodeabsensi'] = $row['kodeabsensi'];
			$data[$row['tglpresensi']]['color'] = $row['color'];
			$data[$row['tglpresensi']]['tglpresensi'] = $row['tglpresensi'];
			$data[$row['tglpresensi']]['jamdatang'] = $row['jamdatang'];
			$data[$row['tglpresensi']]['jampulang'] = $row['jampulang'];
			$data[$row['tglpresensi']]['keterangan'] = $row['keterangan'];
			$data[$row['tglpresensi']]['nourutcuti'] = $row['nourutcuti'];
		}
	}
	
	//variabel untuk looping
	$tglawal = substr($r_periode,0,4).'-'.substr($r_periode,4,2).'-01';
	$sawal = strtotime($tglawal);
	$tglakhir = date('Y-m-t',$sawal);
	$sakhir = strtotime($tglakhir);
	
	$arhari = $p_model::hariAbsensi();
	
	//mendapatkan hari libur pada periode yang dipilih
	$a_libur = $p_model::getHariLibur($conn,$r_periode);
	if(count($a_libur)>0){
		foreach($a_libur as $rowl){
			$arlibur[$rowl['tgllibur']]['tgllibur'] = $rowl['tgllibur'];
			
			if($data[$rowl['tgllibur']]['kodeabsensi'] != 'D')
				$arlibur[$rowl['tgllibur']]['keterangan'] = $rowl['namaliburan'].(!empty($rowl['keterangan']) ? ' ('.$rowl['keterangan'].')' : '');
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
	<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="scripts/common.js"></script>
	<script type="text/javascript" src="scripts/foredit.js"></script>
</head>
<body>
<center><div class="PagerTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title.'<br>'.$peg['namalengkap'] ?></span></div></center>
<br>
<?if(!empty($p_postmsg)) { ?>
<center>
<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
	<?= $p_postmsg ?>
</div>
</center>
<div class="Break"></div>
<?	}?>

<form name="pageform" id="pageform" method="post">
<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr class="DataBG" height="30px">
		<td align="center" colspan="<?= $p_col?>"><?= $p_title.' '.$t_periode?></td>
	</tr>
		<th width="50">No.</th>
		<th>Jenis Absensi</th>
		<th>Hari dan Tanggal</th>
		<th>Datang</th>
		<th>Pulang</th>
		<th>Keterangan</th>
		<?if($c_edit or $c_delete){?>
		<th width="50px">Aksi</th>
		<?}?>
	</tr>
	<?	
		$i=0;
		while($sawal <= $sakhir){
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
			
			$elemen=date("w",$sawal);
			$tgl = date('Y-m-d',$sawal);
			
			$jenisabsensi = $data[$tgl]['absensi'];
			$hari = $arhari[$elemen];
			$tglpresensi = CStr::formatDate($tgl);
			$jamdatang = !empty($data[$tgl]['jamdatang']) ? substr_replace($data[$tgl]['jamdatang'],':',2,0) : '';
			$jampulang = !empty($data[$tgl]['jampulang']) ? substr_replace($data[$tgl]['jampulang'],':',2,0) : '';
			$keterangan = !empty($arlibur[$tgl]['keterangan']) ? $arlibur[$tgl]['keterangan'] : $data[$tgl]['keterangan'];
			$color = $data[$tgl]['color'];
			
			//pengecekan keterangan
			if($data[$tgl]['tglpresensi'] != $tgl and $arlibur[$tgl]['tgllibur'] != $tgl and $elemen != 0 and $elemen != 6){
				$color = 'red';
				$keterangan = 'Data tidak ada';
			}
									
			//pengecekan apakah lebih besar dari tanggal sekarang
			if($tgl >= date('Y-m-d')){
				$color = 'green';
				$keterangan = '<b>Data absensi belum dimasukkan</b>';
			}
				
			//pengecekan dengan tanggal absensi hadir dan cuti
			if($data[$tgl]['tglpresensi'] == $tgl){
				//cek jam datang
				if(empty($data[$tgl]['jamdatang']) and $elemen != 0 and $elemen != 6 and (empty($data[$tgl]['nourutcuti']) or !empty($data[$tgl]['jampulang'])))
					$jamdatang .= !$c_edit ? '<font color="red">*</font>' : '';
				
				//cek jam pulang
				if(empty($data[$tgl]['jampulang']) and $elemen != 0 and $elemen != 6 and (empty($data[$tgl]['nourutcuti']) or !empty($data[$tgl]['jamdatang'])))
					$jampulang .= !$c_edit ? '<font color="red">*</font>' : '';
			}
			
			if($elemen == 0 or $elemen == 6)
				$color = 'red';
			
			if($tgl <= date('Y-m-d') and $c_edit)
				$a_tgl[] = $tgl;
	?>
	<tr valign="top" class="<?= $rowstyle ?>">
		<td align="center"><?= $i; ?></td><input type="hidden" id="tgl[]" name="tgl[]" value="<?= $tgl?>"></td>
		<td nowrap align="center"><font color="<?= $color?>"><?= $jenisabsensi; ?></font></td>
		<td nowrap align="center"><font color="<?= $color?>"><?= $hari.', '.$tglpresensi; ?></font></td>
		<td nowrap align="center">			
			<font color="<?= $color?>">
				<?= ($tgl <= date('Y-m-d') and $c_edit) ? UI::createTextBox('jamdatang'.$tgl,$jamdatang,'ControlStyle',5,4) : $jamdatang;	?>
			</font>
		</td>
		<td nowrap align="center">
			<font color="<?= $color?>">
				<?= ($tgl <= date('Y-m-d') and $c_edit) ? UI::createTextBox('jampulang'.$tgl,$jampulang,'ControlStyle',5,4) : $jampulang;?>
			</font>
		</td>
		<td><font color="<?= $color?>"><?= $keterangan; ?></font></td>
		<?if($c_edit or $c_delete){?>
		<td align="center">
		<?if($tgl <= date('Y-m-d')){?>
			<img id="<?= $tgl?>" style="cursor:pointer" onclick="goSimpan(this)" src="images/disk.png" title="Simpan Data">
			<img id="<?= $tgl?>" style="cursor:pointer" onclick="goHapus(this)" src="images/delete.png" title="Hapus Data">
		</td>
		<?}}?>
	</tr>
	<?		
			$sawal+=86400;
		}
		if($i == 0) {
	?>
	<tr>
		<td colspan="<?= $p_col?>" align="center">Data kosong</td>
	</tr>
	<?	} ?>
	<tr>
		<td colspan="<?= $p_col?>" align="right" class="FootBG">&nbsp;</td>
	</tr>
</table>
<input type="hidden" name="act" id="act">
<input type="hidden" name="subkey" id="subkey">

</form>
</body>
</html>
<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
<script type="text/javascript">

var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	<?foreach($a_tgl as $tgl){?>
	$("#jamdatang<?= $tgl?>").mask("12:34");
	$("#jampulang<?= $tgl?>").mask("12:34");
	<?}?>
});	

function goHapus(elem){
	var hapus = confirm("Apakah anda yakin akan menghapus data ini?");
	if(hapus) {
		document.getElementById("act").value = "delete";
		document.getElementById("subkey").value = elem.id;
		goSubmit();
	}
}

function goSimpan(elem){
	err = cekJam(elem);
	
	if(!err){
		document.getElementById("act").value = "save";
		document.getElementById("subkey").value = elem.id;
		goSubmit();
	}
}

function cekJam(elem){
	var jawal,jakhir,err=false;
	formatjam = /^[0-1][0-9]|2[0-4]:[0-5][0-9]/;
	
	jawal = $("#jamdatang"+elem.id).val();
	jakhir = $("#jampulang"+elem.id).val();
	jamdatang=jawal.substring(0,2);
	menitawal=jawal.substring(3,5);
	jampulang=jakhir.substring(0,2);
	menitakhir=jakhir.substring(3,5);
	
	if(jamdatang=='24' && menitawal!='00'){
		jawal='';
	}
	else {
		if(jawal.match(formatjam))
			jawal = jawal;
		else
			jawal='';
	}
	if(jampulang=='24' && menitakhir!='00'){
		jakhir='';
	}
	else{
		if(jakhir.match(formatjam))
			jakhir = jakhir;
		else
			jakhir='';
	}
	
	if(jawal == ''){
		doHighlight(document.getElementById("jamdatang"+elem.id));
		alert("Format Jam tidak sesuai");
		err=true;	
	}
	if(jakhir == ''){
		doHighlight(document.getElementById("jampulang"+elem.id));
		alert("Format Jam tidak sesuai");
		err=true;	
	}
	
	return err;
}
</script>

