<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_other = $a_auth['canother'];
	$c_kepeg = $c_other['K'];
	$c_valid = $c_other['V'];
	
	//koneksi dengan akademik = sdm.ms_pegawai
	//$connsia = Query::connect('akad');
	//if($_SERVER['REMOTE_ADDR'] == "36.85.91.184") //ip public sevima
	//	$connsia->debug=true;
	
	// include
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getModelPath('integrasi'));
	require_once(Route::getModelPath('gaji'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	if(SDM::isPegawai()) {
		$r_self = 1;
		$c_kepeg = true;
	}
	
	if($c_kepeg){
		$c_edit = $a_auth['canupdate'];
		$c_delete = $a_auth['candelete'];
	}
	
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
	
	//konfigurasi halaman
	print_r(Modul::getIDPegawai());
	$p_model = mPegawai;
	$p_tbwidth = "800";
	$p_title = "Biodata Pegawai";
	$p_aktivitas = "BIODATA";
	$dirfoto = 'fotopeg';
	$p_foto = uForm::getPathImageFoto($conn,$r_key,$dirfoto);
	
	//mendapatkan informasi pegawai
	$col = $p_model::getInfoPegawai($conn,$r_key);
	
	
	$a_input = array();
	$a_input[] = array('kolom' => 'namadepan', 'label' => 'Depan', 'maxlength' => 100, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'namatengah', 'label' => 'Tengah', 'maxlength' => 100, 'size' => 30);
	$a_input[] = array('kolom' => 'namabelakang', 'label' => 'Belakang', 'maxlength' => 100, 'size' => 30);
	$a_input[] = array('kolom' => 'jeniskelamin', 'label' => 'Jenis Kelamin', 'type' => 'S', 'option' => $p_model::jenisKelamin($conn), 'empty' => true, 'notnull' => true);
	$a_input[] = array('kolom' => 'idagama', 'label' => 'Agama', 'type' => 'S', 'option' => $p_model::agama($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'tmplahir', 'label' => 'Tempat Lahir', 'maxlength' => 100, 'size' => 30);
	$a_input[] = array('kolom' => 'tgllahir', 'label' => 'Tgl. Lahir', 'type' => 'D','notnull' => true);
	$a_input[] = array('kolom' => 'statusnikah', 'label' => 'Status Nikah', 'type' => 'S', 'option' => $p_model::statusNikah());
	$a_input[] = array('kolom' => 'nohandkey', 'label' => 'No. Handkey', 'maxlength' => 10, 'size' => 10, 'notnull' => true);
	$a_input[] = array('kolom' => 'idfinger', 'label' => 'ID Finger', 'maxlength' => 10, 'size' => 10);
	$a_input[] = array('kolom' => 'telepon', 'label' => 'No. Telepon', 'maxlength' => 25, 'size' => 30);
	$a_input[] = array('kolom' => 'teleponkantor', 'label' => 'No. Telepon Kantor', 'maxlength' => 25, 'size' => 30);
	$a_input[] = array('kolom' => 'nohp', 'label' => 'No Ponsel', 'maxlength' => 25, 'size' => 30);
	$a_input[] = array('kolom' => 'email', 'label' => 'Email Esa Unggul', 'maxlength' => 100, 'size' => 50, 'infoedit' => 'Email Esa Unggul', 'notnull' => true);
	$a_input[] = array('kolom' => 'emailpribadi', 'label' => 'Email selain Esa Unggul', 'maxlength' => 100, 'size' => 50, 'infoedit' => 'Email selain Esa Unggul');
	$a_input[] = array('kolom' => 'sukubangsa', 'label' => 'Suku Bangsa', 'maxlength' => 25, 'size' => 30);
	$a_input[] = array('kolom' => 'idkewarganegaraan', 'label' => 'Warga Negara', 'type' => 'S', 'option' => $p_model::warganegara($conn), 'empty' => '-- Pilih Warga Negara --', 'notnull' => true);
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => mCombo::unitSave($conn,false), 'notnull' => true);
		
	$a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'maxlength' => 150, 'size' => 60);
	$a_input[] = array('kolom' => 'kelurahan', 'label' => 'Kelurahan', 'maxlength' => 150, 'size' => 80);
	$a_input[] = array('kolom' => 'idkelurahan', 'type' => 'H');
	$a_input[] = array('kolom' => 'kodepos', 'label' => 'Kode Pos', 'maxlength' => 5, 'size' => 6);
	$a_input[] = array('kolom' => 'jarakrumah', 'label' => 'Jarak Rumah', 'maxlength' => 6, 'size' => 6);
	
	$a_input[] = array('kolom' => 'noktp', 'label' => 'No. KTP', 'maxlength' => 150, 'size' => 60);
	$a_input[] = array('kolom' => 'tglktp', 'label' => 'Tanggal KTP', 'type' => 'D');
	$a_input[] = array('kolom' => 'tglktphabis', 'label' => 'Tanggal KTP Habis', 'type' => 'D');
	$a_input[] = array('kolom' => 'alamatktp', 'label' => 'Alamat', 'maxlength' => 150, 'size' => 60);
	$a_input[] = array('kolom' => 'kelurahanktp', 'label' => 'Kelurahan', 'maxlength' => 150, 'size' => 80);
	$a_input[] = array('kolom' => 'idkelurahanktp', 'type' => 'H');
	$a_input[] = array('kolom' => 'kodeposktp', 'label' => 'Kode Pos', 'maxlength' => 5, 'size' => 6);
	
	$a_input[] = array('kolom' => 'goldarah', 'label' => 'Golongan Darah', 'type' => 'S', 'empty' => true, 'option' => $p_model::golDarah());
	$a_input[] = array('kolom' => 'tinggi', 'label' => 'Tinggi Badan', 'type' => 'N','maxlength' => 4, 'size' => 6, 'infoedit' => 'centimeter');
	$a_input[] = array('kolom' => 'beratbadan', 'label' => 'Berat Badan', 'type' => 'N', 'maxlength' => 4, 'size' => 6, 'infoedit' => 'kilogram');
	$a_input[] = array('kolom' => 'ukuranbaju', 'label' => 'Ukuran Baju', 'type' => 'S', 'empty' => true, 'option' => $p_model::ukuranBaju());
	$a_input[] = array('kolom' => 'ukurankacamata', 'label' => 'Ukuran Kacamata', 'maxlength' => 4, 'size' => 6);
	
	$r_act = $_POST['act'];
	$tipepeg = $p_model::getTipePegawai($conn,$r_key);
	if($r_act == 'save' and $c_edit) {
		
		$conn->BeginTrans();
		
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		if($record['namadepan'] != 'null')
			$record['namadepan'] = strtoupper($record['namadepan']);
		if($record['namatengah'] != 'null')
			$record['namatengah'] = strtoupper($record['namatengah']);
		if($record['namabelakang'] != 'null')
			$record['namabelakang'] = strtoupper($record['namabelakang']);
		if($record['tmplahir'] != 'null')
			$record['tmplahir'] = strtoupper($record['tmplahir']);
		if($record['alamat'] != 'null')
			$record['alamat'] = strtoupper($record['alamat']);
		if($record['alamatktp'] != 'null')
			$record['alamatktp'] = strtoupper($record['alamatktp']);
		
		if(empty($r_key)){
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		}else{
			//print_r($a_input);
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		}
		
		if(!$p_posterr){
			$p_posterr = mIntegrasi::saveRoleGate($conn,$r_key);
			if($p_posterr)
				$p_postmsg = 'Penyimpanan User Role ke Gate gagal';
		}
		/*
		if(!$p_posterr){
			if($record['idtipepeg'] == 'D' or $record['idtipepeg'] == 'AD'){
				$p_posterr = mIntegrasi::saveDosenSyncAkad($conn,$connsia,$r_key);
			}else{
				if($tipepeg == 'D' or $tipepeg == 'AD'){
					$p_posterr = mIntegrasi::saveDosenSyncAkad($conn,$connsia,$r_key);
				}
				//$p_posterr = mIntegrasi::saveNonDosenSyncAkad($conn,$connsia,$r_key);
			}
			if($p_posterr)
				$p_postmsg = 'Penyimpanan User ke Akademik Berhasil';
		}
		*/
		
		if(!$p_posterr){
			$r_periodegaji = mGaji::getLastPeriodeGaji($conn);
			mGaji::tarikData($conn,$r_periodegaji,'',$r_key);
		}
		
		$ok = Query::isOK($p_posterr);
		if($ok){
			$conn->CommitTrans($ok);				
			unset($post);
		}else
			$conn->RollbackTrans();
	}
	else if($r_act == 'savefoto' and $c_edit) {		
		if(empty($_FILES['foto']['error'])) {
			$err = Page::createFoto($_FILES['foto']['tmp_name'],$p_foto,200,200);
			
			switch($err) {
				case -1:
				case -2: $msg = 'format foto harus JPG, GIF, atau PNG'; break;
				case -3: $msg = 'foto tidak bisa disimpan'; break;
				default: $msg = false;
			}
			if($msg !== false)
				$msg = 'Upload gagal, '.$msg;
		}
		else
			$msg = Route::uploadErrorMsg($_FILES['foto']['error']);
		
		uForm::reloadImageFoto($conn,$r_key,$dirfoto,$msg);
	}
	else if($r_act == 'deletefoto' and $c_edit) {
		@unlink($p_foto);
		
		uForm::reloadImageFoto($conn,$r_key,$dirfoto);
	}
	
	$sql = $p_model::getDataEditBiodata($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,'','',$sql);
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foreditx.js"></script>
	<style>
		.bottomline td{
			border-bottom:1px solid #eaeaea;
		}
	</style>
</head>
<body>
	<table width="100%">
		<tr>
			<td>
				<form name="pageform" id="pageform" method="post" action="<?= Route::navAddress(Route::thisPage()) ?>" enctype="multipart/form-data">
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
							require_once('inc_databuttonajax.php');
						
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
						
						<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
						<table width="100%" cellspacing="0" cellpadding="4" class="bottomline">
							<tr>
								<td width="180">ID System</td>
								<td width="10">:</td>
								<td><?= $col['idpegawai'] ?></td>
								<td rowspan="8" width="200" align="center" valign="top">
									<?= uForm::getImageFoto($conn,$r_key,$dirfoto,$c_edit) ?>
								</td>
							</tr>
							<tr>
								<td>NIP</td>
								<td>:</td>
								<td><?= $col['nip']; ?></td>
							</tr>
							<tr>
								<td>Nama Lengkap</td>
								<td>:</td>
								<td><?= $col['namalengkap']; ?></td>
							</tr>
							<tr>
								<td>Nama</td>
								<td>:</td>
								<td>
									<table cellspacing="0" cellpadding="4" c>
										<tr><td><?= Page::getDataLabel($row,'namadepan') ?><td><td>:</td><td><?= Page::getDataInput($row,'namadepan') ?></td></tr>
										<tr><td><?= Page::getDataLabel($row,'namatengah') ?><td><td>:</td><td><?= Page::getDataInput($row,'namatengah') ?></td></tr>
										<tr><td><?= Page::getDataLabel($row,'namabelakang') ?><td><td>:</td><td><?= Page::getDataInput($row,'namabelakang') ?></td></tr>
									</table>
								</td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'jeniskelamin') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'jeniskelamin') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'idagama') ?></td>
								<td>:</td>
								<td><?= Page::getDataInput($row,'idagama') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'tmplahir') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'tmplahir') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'tgllahir') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'tgllahir') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'statusnikah') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'statusnikah') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'nohandkey') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'nohandkey') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'idfinger') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'idfinger') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'telepon') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'telepon') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'teleponkantor') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'teleponkantor') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'nohp') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'nohp') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'email') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'email') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'emailpribadi') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'emailpribadi') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'sukubangsa') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'sukubangsa') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'idkewarganegaraan') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'idkewarganegaraan') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'idunit') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'idunit') ?></td>
							</tr>
						</table>
						</div>
					</center>
					<br>
					<center>
					<div class="tabs" style="width:<?= $p_tbwidth ?>px">
						<ul>
							<li><a href="javascript:void(0)">Alamat Domisili</a></li>
							<li><a href="javascript:void(0)">Alamat KTP</a></li>
							<li><a href="javascript:void(0)">Identitas Fisik</a></li>
						</ul>
						
						<div id="items">
						<table cellpadding="4" cellspacing="0" align="center" class="bottomline">
							<tr>
								<td width="180"><?= Page::getDataLabel($row,'alamat') ?></td>
								<td width="10">:</td>
								<td colspan="2"><?= Page::getDataInput($row,'alamat') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'kelurahan') ?></td>
								<td>:</td>
								<td colspan="2">
									<?= Page::getDataInput($row,'kelurahan') ?>
									<?= Page::getDataInput($row,'idkelurahan') ?>	
									<span id="edit" style="display:none">
										<img id="imgkel_c" src="images/green.gif">
										<img id="imgkel_u" src="images/red.gif" style="display:none">
									</span>
								</td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'kodepos') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'kodepos') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'jarakrumah') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'jarakrumah') ?></td>
							</tr>
						</table>
						</div>
						
						<div id="items">
						<table cellpadding="4" cellspacing="0" align="center" class="bottomline">
							<tr>
								<td width="180"><?= Page::getDataLabel($row,'noktp') ?></td>
								<td width="10">:</td>
								<td colspan="2"><?= Page::getDataInput($row,'noktp') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'tglktp') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'tglktp') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'tglktphabis') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'tglktphabis') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'alamatktp') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'alamatktp') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'kelurahanktp') ?></td>
								<td>:</td>
								<td colspan="2">
									<?= Page::getDataInput($row,'kelurahanktp') ?>
									<?= Page::getDataInput($row,'idkelurahanktp') ?>	
									<span id="edit" style="display:none">
										<img id="imgkelktp_c" src="images/green.gif">
										<img id="imgkelktp_u" src="images/red.gif" style="display:none">
									</span>
								</td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'kodeposktp') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'kodeposktp') ?></td>
							</tr>
						</table>
						</div>
						
						<div id="items">
						<table cellpadding="4" cellspacing="0" align="center" class="bottomline">
							<tr>
								<td width="180"><?= Page::getDataLabel($row,'goldarah') ?></td>
								<td width="10">:</td>
								<td colspan="2"><?= Page::getDataInput($row,'goldarah') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'tinggi') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'tinggi') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'beratbadan') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'beratbadan') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'ukuranbaju') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'ukuranbaju') ?></td>
							</tr>
							<tr>
								<td><?= Page::getDataLabel($row,'ukurankacamata') ?></td>
								<td>:</td>
								<td colspan="2"><?= Page::getDataInput($row,'ukurankacamata') ?></td>
							</tr>
						</table>
						</div>
					</div>
					</center>
				
					<? } ?>
					<input type="hidden" name="act" id="act">
					<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
					<input type="hidden" name="detail" id="detail">
				</form>
			</td>
		</tr>
	</table>

	<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
		<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
	</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	initTab();
	
	//autocomplete
	$("#kelurahan").xautox({strpost: "f=acnamakelurahan", targetid: "idkelurahan", imgchkid: "imgkel", imgavail: true});
	$("#kelurahanktp").xautox({strpost: "f=acnamakelurahan", targetid: "idkelurahanktp", imgchkid: "imgkelktp", imgavail: true});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

</script>
</body>
</html>
