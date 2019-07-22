<?php 
		// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('detailkelas'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('jeniskuliah'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('mengajar'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	require_once(Route::getModelPath('bahaajar'));
	
	$a_kodeunit = mCombo::unit($conn,false);
	$a_semester =  mCombo::semester();
	$a_kurikulum =array();
	$p_title = 'Materi Elearning';
	$p_tbwidth = 800;
	$p_aktivitas = 'Elearning';
	$p_listpage = Route::getListPage();
	$pk = $_GET['key'];
	//print_r($pk);
	if(!empty($pk))
	{
		$a_kurikulum = mCombo::tahun();
		$data = mBahanajar::v_detailbahanajarbyid($conn,$pk);
		$a_matakuliah = mKelas::mkKurikulum($conn,$data['thnkurikulum'],$data['kodeunit']);
		print_r($data);
		$selectUnitOption =$data['kodeunit'];
		$selectthnOption = $data['thnkurikulum'];
		$selectMkOption = $data['kodemk'];
		$selectsmsOption = substr($data['periode'],4,1);
	}else{
		$dis="";

	}

	$r_act = $_POST['act'];
	if($r_act == 'change') {
		$selectUnitOption = $_POST['kodeunit'];
		$selectthnOption = $_POST['thnkurikulum'];
		if(!empty($_POST['kodeunit'])){
			$r_kodeunit = $_POST['kodeunit'];
			$a_kurikulum = mCombo::tahun();
		}else{
			$r_kodeunit = '';
		}
		if(!empty($_POST['thnkurikulum'])){
			$r_thnkurikulum = $_POST['thnkurikulum'];
		}else{
			$r_thnkurikulum = '';
		}

	if(!empty($r_thnkurikulum) and !empty($r_kodeunit)){
		$a_matakuliah = mKelas::mkKurikulum($conn,$r_thnkurikulum,$r_kodeunit);
	}
	}elseif($r_act='simpan' and empty($pk)){
		if(empty($_POST['kodeunit'])){
			$p_postmsg = 'Pengelola Harus Diisi !';
			$p_posterr = true;
		}
		if(empty($_POST['thnkurikulum'])){
			$p_postmsg = 'Tahun Kurikulum Harus Diisi !';
			$p_posterr = true;
		}
		if(empty($_POST['kodemk'])){
			$p_postmsg = 'Matakuliah Harus Diis !';
			$p_posterr = true;
		}
		if(empty($_POST['semester']) || empty($_POST['tahun']) ){
			$p_postmsg = 'Semester Harus Diisi !';
			$p_posterr = true;
		}
		if(empty($p_posterr)){
			$data = array();
			$data['periode'] = $_POST['tahun'].$_POST['semester'];
			$data['thnkurikulum'] = $_POST['thnkurikulum'];
			$data['kodemk'] = $_POST['kodemk'];
			$data['alamatvideo'] = $_POST['alamatvideo'];
			$data['durasi'] = $_POST['durasi'];
			$data['alamatmodul'] = $_POST['alamatmodul'];
			$data['jumlahhalaman'] = $_POST['jumlahhalaman'];
			$data['perkuliahanke'] = $_POST['perkuliahanke'];
			$data['kodeunit']=$_POST['kodeunit'];

			$ok = mBahanajar::insertBahanajar($conn,$data);
			if($ok){
				$r_key = '|'.$data['kodemk'].'||'.$data['periode'].'|';
				header("Location: https://siakad.inaba.ac.id/siakad/siakad/index.php?page=detail_bahanajar&key=".$r_key);
			}
			//die();
			
		}
		
	}elseif($r_act='simpan' and !empty($pk)){
		if(empty($_POST['kodeunit'])){
			$p_postmsg = 'Pengelola Harus Diisi !';
			$p_posterr = true;
		}
		if(empty($_POST['thnkurikulum'])){
			$p_postmsg = 'Tahun Kurikulum Harus Diisi !';
			$p_posterr = true;
		}
		if(empty($_POST['kodemk'])){
			$p_postmsg = 'Matakuliah Harus Diis !';
			$p_posterr = true;
		}
		if(empty($_POST['semester']) || empty($_POST['tahun']) ){
			$p_postmsg = 'Semester Harus Diisi !';
			$p_posterr = true;
		}
		if(empty($p_posterr)){
			$data = array();
			$data['periode'] = $_POST['tahun'].$_POST['semester'];
			$data['thnkurikulum'] = $_POST['thnkurikulum'];
			$data['kodemk'] = $_POST['kodemk'];
			$data['alamatvideo'] = $_POST['alamatvideo'];
			$data['durasi'] = $_POST['durasi'];
			$data['alamatmodul'] = $_POST['alamatmodul'];
			$data['jumlahhalaman'] = $_POST['jumlahhalaman'];
			$data['perkuliahanke'] = $_POST['perkuliahanke'];
			$data['kodeunit']=$_POST['kodeunit'];

			$ok = mBahanajar::updateBahanajar($conn,$data,$pk);
			if($ok){
				$r_key = '|'.$data['kodemk'].'||'.$data['periode'].'|';
				header("Location: https://siakad.inaba.ac.id/siakad/siakad/index.php?page=detail_bahanajar&key=".$r_key);
			}
			//die();
			
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
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
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
				<?	}
					
					/*****************/
					/* TOMBOL-TOMBOL */
					/*****************/
					
					if(empty($p_fatalerr))
					
					
					if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	}
				
					if(empty($p_fatalerr)) { ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
					<?	/********/
						/* DATA */
						/********/
						
						//$a_required = array('kodemk');
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
					<tr>
						<td class="LeftColumnBG" style="white-space:nowrap">Pengelola</td>
						<td class="RightColumnBG">
						<select name='kodeunit' id='kodeunit'  onChange="setChange(this)">
						<option value=''>Pilih</option>
						<?php foreach($a_kodeunit as $kd => $key) {
							
							if($selectUnitOption==$kd){
							?>
								<option value=<?=$kd?> selected><?= $key ?></option>
							<?php }else{ ?>
						
								<option value=<?=$kd?>><?= $key ?></option>
						
							<?php 
							}
							
							
							
							} ?>
							
						</select>
					
						</td>
						</tr>
					<tr>
						<td class="LeftColumnBG" style="white-space:nowrap">Tahun Kurikulum</td>
						<td class="RightColumnBG">
						<select name='thnkurikulum' id='thnkurikulum'  onChange="setChange(this)">
						<option value=''>Pilih</option>
							<?php foreach($a_kurikulum as $kr) { 
									if($selectthnOption==$kr){
								
								?>
									<option value=<?=$kr?> selected><?= $kr ?></option>
									<?php }else{ ?>
									<option value=<?=$kr?>><?= $kr ?></option>
								<?php	}
										} ?>
							
						</select>
					
						</td>
						</tr>
						<tr>
						<td class="LeftColumnBG" style="white-space:nowrap">Mata Kuliah</td>
						<td class="RightColumnBG">
										<select name='kodemk' id='kodemk'>
											<option value=''>Pilih</option>
											<?php foreach($a_matakuliah as $mk => $val) { 
												if($selectMkOption==$mk){
												?>
												<option value=<?=$mk?> selected><?= $val ?></option>
												<?php }else{ ?>
												<option value=<?=$mk?>><?= $val ?></option>
												<?php }
										} ?>
										</select>
						</td>
						</tr>
						<tr>
						<td class="LeftColumnBG" style="white-space:nowrap">Semester</td>
						<td class="RightColumnBG">
								<select name='semester' id='semester'>
									<?php foreach($a_semester as $sms => $val) { 
											if($sms == $selectsmsOption){
										?>
											<option value=<?=$sms?> selected><?= $val ?></option>
											<?php }else{ ?>
												<option value=<?=$sms?>><?= $val ?></option>
									<?php }} ?>
								</select>

								<select name='tahun' id='tahun'>
								<option value=''>Pilih</option>
									<?php foreach($a_kurikulum as $kr) { 
											if($selectthnOption==$kr){
										
										?>
											<option value=<?=$kr?> selected><?= $kr ?></option>
											<?php }else{ ?>
											<option value=<?=$kr?>><?= $kr ?></option>
										<?php	}
												} ?>
									
								</select>
						</td>
						</tr>
						<tr>
						<td class="LeftColumnBG" style="white-space:nowrap">Alamat Video</td>
						<td class="RightColumnBG">
						<input type="text" name="alamatvideo" value=<?=$data['alamatvideo']?>>
						</td>
						</tr>
						<tr>
						<td class="LeftColumnBG" style="white-space:nowrap">Durasi Video</td>
						<td class="RightColumnBG">
						<input type="time" name="durasi" value=<?=$data['durasi'] ?>>
						</td>
						</tr>
						<tr>
						<td class="LeftColumnBG" style="white-space:nowrap">Alamat Modul</td>
						<td class="RightColumnBG">
						<input type="text" name="alamatmodul" value=<?=$data['alamatmodul'] ?>>
						</td>
						</tr>
						<tr>
						<td class="LeftColumnBG" style="white-space:nowrap">Jumlah Halaman Modul</td>
						<td class="RightColumnBG">
							<input type="number" name="jumlahhalaman" maxlength="2" value=<?= $data['jumlahhalaman']?>>
						</td>
						</tr>
						<td class="LeftColumnBG" style="white-space:nowrap">Perkuliahan Ke - </td>
						<td class="RightColumnBG">
							<select name='perkuliahanke' id='perkuliahanke'>
								<option value='1'>1</option>
								<option value='2'>2</option>
								<option value='3'>3</option>
								<option value='4'>5</option>
								<option value='6'>6</option>
								<option value='7'>7</option>
								<option value='9'>9</option>
								<option value='10'>10</option>
								<option value='11'>11</option>
								<option value='12'>12</option>
								<option value='13'>13</option>
								<option value='14'>14</option>
								<option value='15'>15</option>
							</select>
						</td>
						</tr>
					
						</table>
						<table border="0" cellspacing="10" align="center">
	<tr>
		
		<td>
			<button type="submit" onClick='goSimpan()' value='simpan'>Simpan</button>
		</td>
	</tr>
</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="matkul" id="matkul">
				<input type="hidden" id="nip" name="nip" value="<?= $r_key ?>">
			
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="subkeymku" id="subkeymku">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

	<script src="scripts/jquery-1.7.1.min.js" type="text/javascript" charset="utf-8"></script>	
	<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
	<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
	<script type="text/javascript">

var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";
function setChange(that){
	document.getElementById(that.id).value = that.value;
	document.getElementById("act").value = "change";
	goSubmit();
}
function goSimpan(){
	document.getElementById("act").value = "simpan";
	goSubmit();
}
$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);

	$("#dosen").xautox({strpost: "f=acdosen", targetid: "nip"});
	$("#u_dosen").xautox({strpost: "f=acdosen", targetid: "u_nip"});
	$("#kodemk").xautox({strpost: "f=acmatkul", targetid: "matkul"});
	$("#l_unitmku").xautox({strpost: "f=acjurusan", targetid: "unitmku"});
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	var block = $("#isblock_-1").attr("checked");
	var nonblock = $("#isblock_0").attr("checked");
	if(block){
		$("[id='jadwal']").hide();
		
	}else if(nonblock){
		$("[id='jadwal']").show();
		
	}
	 $("#isblock_-1").click(function(){
		 $("[id='jadwal']").hide();
		 
	 });
	 $("#isblock_0").click(function(){
		 $("[id='jadwal']").show();
		 
	 });
	 $("[id='checkAll']").click(function() {
		var checked = $(this).attr("checked");
		if(checked)
			$("[id='checklist']").attr("checked", checked);
		else
			$("[id='checklist']").removeAttr("checked", checked);
	});
});

function goDeletePengajar(elem) {
	document.getElementById("act").value = "deleteajar";
	document.getElementById("subkey").value = elem.id;
	goSubmit();
}

function goInsertPengajar() {
	document.getElementById("act").value = "insertajar";
	goSubmit();
}
function goDeleteMku(elem) {
	document.getElementById("act").value = "deletemku";
	document.getElementById("subkeymku").value = elem.id;
	if(confirm('Yakin Akan Menghapus ?'))
		goSubmit();
}

function goInsertMku() {
	document.getElementById("act").value = "insertmku";
	goSubmit();
}
function goUpdatePengajar() {
	document.getElementById("act").value = "updateajar";
	goSubmit();
}
function setHari1(val){
	var arr_date=val.split('-');
	var date = new Date(arr_date[2]+' '+arr_date[1]+' '+arr_date[0]);
	var day = date.getDay();
	if(day==0)
		day=7;
	document.getElementById("nohari").value=day;
}
function setHari2(val){
	var arr_date=val.split('-');
	var date = new Date(arr_date[2]+' '+arr_date[1]+' '+arr_date[0]);
	var day = date.getDay();
	if(day==0)
		day=7;
	document.getElementById("nohari2").value=day;
}
function setHari3(val){
	var arr_date=val.split('-');
	var date = new Date(arr_date[2]+' '+arr_date[1]+' '+arr_date[0]);
	var day = date.getDay();
	if(day==0)
		day=7;
	document.getElementById("nohari3").value=day;
}
function setHari4(val){
	var arr_date=val.split('-');
	var date = new Date(arr_date[2]+' '+arr_date[1]+' '+arr_date[0]);
	var day = date.getDay();
	if(day==0)
		day=7;
	document.getElementById("nohari4").value=day;
}
function editPjmk(elem) {
	$("#l_"+elem.id).hide();
	$("#u_"+elem.id).show();
	//alert(elem.id);
}
function goSetPjmk(elem) {
	document.getElementById("act").value = (elem.checked ? 'set' : 'unset');
	document.getElementById("subkey").value = elem.value;
	
	goSubmit();
}

function goSetMengajar(elem) {
	document.getElementById("act").value = (elem.checked ? 'setajar' : 'unsetajar');
	document.getElementById("subkey").value = elem.value;
	
	goSubmit();
}
</script>
</body>
</html>
