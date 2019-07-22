<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('setting'));
	require_once(Route::getModelPath('skalanilai'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('perwalian'));
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$a_global = mSetting::getData($conn,1);
	
	$r_kurikulum = $a_global['thnkurikulumsekarang'];
	$r_periode = $a_global['periodesekarang'];
	$r_periodespa = $a_global['periodespa'];
	$r_tahap = $a_global['tahapfrs'];
	$r_isinilai = $a_global['isinilai'];
	$r_isibiodata = $a_global['biodatamhs'];
	$r_periodenilai = $a_global['periodenilai'];
	$r_periodenilaispa = $a_global['periodenilaispa'];
	$r_parameterdefault = $a_global['isparameternilai'];
	$r_detip = $a_global['detip'];
	$r_skssp = $a_global['skssp'];
	$r_tahun = substr($r_periode,0,4);
	$r_semester = substr($r_periode,-1);
	$r_tahunspa = substr($r_periodespa,0,4);
	$r_semesterspa = substr($r_periodespa,-1);
	$r_tahunnilai = substr($r_periodenilai,0,4);
	$r_semesternilai = substr($r_periodenilai,-1);
	$r_tahunnilaispa = substr($r_periodenilaispa,0,4);
	$r_semesternilaispa = substr($r_periodenilaispa,-1);
  $r_utssatu = $a_global['kartuutssarjana'];
  $r_utsdua = $a_global['kartuutsmagister'];
  $r_uassatu = $a_global['kartuuassarjana'];
  $r_uasdua = $a_global['kartuuasmagister'];
	
	// properti halaman
	$p_title = 'Setting Global';
	$p_tbwidth = 500;
	$p_aktivitas = 'SETTING';
	
	$p_model = mSetting;
	
	$a_inputkrs = array();
	$a_inputkrs[] = array('label' => 'Kurikulum Berlaku', 'input' => uCombo::kurikulum($conn,$r_kurikulum,'thnkurikulumsekarang','',false));
	$a_inputkrs[] = array('label' => 'Periode Semester','input' => uCombo::semester($r_semester,false,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	$a_inputkrs[] = array('label' => 'Periode SPA','input' => uCombo::semester($r_semesterspa,false,'semesterspa','',false).' '.uCombo::tahun($r_tahunspa,true,'tahunspa','',false));
	$a_inputkrs[] = array('label' => 'Pengisian KRS', 'nameid' => 'tahapfrs', 'type' => 'R', 'option' => $p_model::periodeKRS(), 'default' => $r_tahap);
	$a_inputkrs[] = array('label' => 'Pengisian Biodata Mhs', 'nameid' => 'biodatamhs', 'type' => 'R', 'option' => $p_model::isiBiodata(), 'default' => $r_isibiodata);
	$a_inputkrs[] = array('label' => 'Batas SKS Default', 'nameid' => 'batassksdefault', 'type' => 'NP', 'size' => 2, 'maxlength' => 2, 'default' => $a_global['batassksdefault']);
	$a_inputkrs[] = array('label' => 'Prosentase Kehadiran MInimal', 'nameid' => 'pros_kehadiran', 'size' => 3, 'maxlength' => 3, 'default' => $a_global['pros_kehadiran']);
	//$a_inputkrs[] = array('label' => 'Lintas Kurikulum', 'nameid' => 'lintaskurikulum', 'type' => 'C', 'option' => $p_model::lintasKurikulum(), 'default' => $a_global['lintaskurikulum']);
	$a_inputkrs[] = array('label' => 'SKS Maksimal Semester Pendek', 'nameid' => 'skssp', 'size' => 2, 'maxlength' => 2,'default' => $a_global['skssp']);
	$a_inputkrs[] = array('label' => 'Deteksi Ip Ruangan', 'nameid' => 'detip', 'type' => 'R', 'option' => array('0'=>'Off','-1'=>'On'), 'default' => $r_detip);
	$a_inputkrs[] = array('label' => 'Kartu UTS S1', 'nameid' => 'kartuutssarjana', 'type' => 'R', 'option' => array('0'=>'Off','1'=>'On'), 'default' => $r_utssatu);
  $a_inputkrs[] = array('label' => 'Kartu UTS S2', 'nameid' => 'kartuutsmagister', 'type' => 'R', 'option' => array('0'=>'Off','1'=>'On'), 'default' => $r_utsdua);
  $a_inputkrs[] = array('label' => 'Kartu UAS S1', 'nameid' => 'kartuuassarjana', 'type' => 'R', 'option' => array('0'=>'Off','1'=>'On'), 'default' => $r_uassatu);
  $a_inputkrs[] = array('label' => 'Kartu UAS S2', 'nameid' => 'kartuuasmagister', 'type' => 'R', 'option' => array('0'=>'Off','1'=>'On'), 'default' => $r_uasdua);
	//$a_inputkrs[] = array('label' => 'Nama Printer', 'nameid' => 'nama_printer', 'size' => 30, 'maxlength' => 100, 'default' => $a_global['nama_printer'],'add' => 'placeholder="ex: epsonT13"');
	//$a_inputkrs[] = array('nameid' => 'bgktm', 'label' => 'Background KTM', 'type' => 'U', 'uptype' => 'ktm');
	
	$a_inputnilai = array();
	$a_inputnilai[] = array('label' => 'Periode Nilai','input' => uCombo::semester($r_semesternilai,false,'semesternilai','',false).' '.uCombo::tahun($r_tahunnilai,true,'tahunnilai','',false));
	$a_inputnilai[] = array('label' => 'Periode Nilai SPA','input' => uCombo::semester($r_semesternilaispa,false,'semesternilaispa','',false).' '.uCombo::tahun($r_tahunnilaispa,true,'tahunnilaispa','',false));
	$a_inputnilai[] = array('label' => 'Pengisian Nilai', 'nameid' => 'isinilai', 'type' => 'R', 'option' => $p_model::isiNilai(), 'default' => $r_isinilai);
	$a_inputnilai[] = array('label' => 'Gunakan skala Nilai Universitas', 'nameid' => 'isparameternilai', 'type' => 'R', 'option' => $p_model::parameterNilai(), 'default' => $r_parameterdefault);
	$a_inputnilai[] = array('label' => 'Nilai Pemutihan', 'nameid' => 'nangkatutup', 'type' => 'S', 'option' => mSkalaNilai::getDataKurikulum($conn,$r_kurikulum), 'default' => $a_global['nangkatutup']);
	$a_inputnilai[] = array('label' => 'Isi Pesan', 'nameid' => 'pesanpengesahan', 'type' => 'A', 'rows' => 5, 'cols' => 63, 'default' => $a_global['pesanpengesahan']);
	
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodeunit', 'label' => 'Kode Unit');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Nama Unit');
	$a_kolom[] = array('kolom' => 'iskrs','label' => 'Set KRS');
	$a_kolom[] = array('kolom' => 'ispenilaian','label' => 'Set Periode Nilai');
	$a_kolom[] = array('kolom' => 'tglmulaiakhirnilai','label' => 'Tgl Mulai - Tgl Akhir');
	
	// ada submit
	$r_act = $_POST['act'];
	if($r_act == 'setkrs' and $c_edit) {
		$record = CStr::cStrFill($_POST);
		
		$record['periodesekarang'] = $record['tahun'].$record['semester'];
		$record['periodespa'] = $record['tahunspa'].$record['semesterspa'];
		
		/* if(empty($record['lintaskurikulum']))
			$record['lintaskurikulum'] = 0; */
		
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,1,true);
		if(!$p_posterr){
			if($record['tahapfrs']=='KRS')
				$status=1;
			else if($record['tahapfrs']=='KULIAH')
				$status=0;
			$update=$conn->Execute("update gate.ms_unit set iskrs=$status");
		}
		if(!$p_posterr and !empty($_FILES['bgktm']['name'])){
			if(empty($_FILES['bgktm']['error'])) {
				$p_file=$conf['upload_dir'].'ktm/background.png';
				$w=86*3.78;
				$h=54*3.78;
				$err = Page::createFotoPng($_FILES['bgktm']['tmp_name'],$p_file,$w,$h);
				
				switch($err) {
					case -1: $msg = 'file tidak dikenali sebagai gambar'; break;
					case -2: $msg = 'format foto harus JPG, GIF, atau PNG'; break;
					case -3: $msg = 'foto tidak bisa disimpan'; break;
					default: $msg = false;
				}
				if($msg !== false)
					$msg = 'Upload gagal, '.$msg;
				
			}
			else
				$msg = Route::uploadErrorMsg($_FILES['foto']['error']);
				
			$p_postmsg=$p_postmsg.'-'.$msg;
		}
		
		$a_flash = array();
		$a_flash['p_posterr'] = $p_posterr;
		$a_flash['p_postmsg'] = $p_postmsg;
		
		Route::setFlashData($a_flash);
	}
	else if($r_act == 'setnilai' and $c_edit) {
		$record = CStr::cStrFill($_POST);
		$record['isparameternilai']=$_POST['isparameternilai'];
		$record['periodenilai'] = $record['tahunnilai'].$record['semesternilai'];
		$record['periodenilaispa'] = $record['tahunnilaispa'].$record['semesternilaispa'];
		$record['pesanpengesahan'] = htmlentities($record['pesanpengesahan']);
	 
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,1,true);
		
		$a_flash = array();
		$a_flash['p_posterr'] = $p_posterr;
		$a_flash['p_postmsg'] = $p_postmsg;
		
		Route::setFlashData($a_flash);
	}else if($r_act == 'setkrsjur' and $c_edit) {
		foreach($_POST['krs'] as $key=>$value){
			$record=array();
			$record['iskrs']=$value;
			list($p_posterr,$p_postmsg) = mUnit::updateRecord($conn,$record,$key);
		}
		
	}else if($r_act == 'aktifkanmk' and $c_edit) {
		$record=array();
		$record['iskrs']=1;
		list($p_posterr,$p_postmsg) = mUnit::updateRecord($conn,$record,$_POST['kodemk']);
	}else if($r_act == 'matikanmk' and $c_edit) {
		$record=array();
		$record['iskrs']=0;
		list($p_posterr,$p_postmsg) = mUnit::updateRecord($conn,$record,$_POST['kodemk']);
	}else if($r_act == 'genperwalian' and $c_edit) {
		$rec = CStr::cStrFill($_POST);
		$periode=$rec['tahun'].$rec['semester'];
		list($p_posterr,$p_postmsg) = mPerwalian::genPerwalian($conn,$periode);
	}else if($r_act == 'aktifkanmknilai' and $c_edit) {
		$record=array();
		$record['ispenilaian']=1;
		list($p_posterr,$p_postmsg) = mUnit::updateRecord($conn,$record,$_POST['kodemk']);
	}else if($r_act == 'matikanmknilai' and $c_edit) {
		$record=array();
		$record['ispenilaian']=0;
		list($p_posterr,$p_postmsg) = mUnit::updateRecord($conn,$record,$_POST['kodemk']);
	}else if($r_act == 'simpanperiodenilai' and $c_edit) {
		$record=array();
		$record['tglmulainilai']=date('Y-m-d',strtotime($_POST['tglmulainilai_'.$_POST['kodemk']]));
		$record['tglakhirnilai']=date('Y-m-d',strtotime($_POST['tglakhirnilai_'.$_POST['kodemk']]));
		list($p_posterr,$p_postmsg) = mUnit::updateRecord($conn,$record,$_POST['kodemk']);
	}
	else if($r_act == 'kuncinilai' and $c_edit) {
		$p_table='akademik.ak_kelas';
		$periodepenilaian=Akademik::getPeriodeNilai();
		$kodeunit=CStr::cStrNull($_POST['kodeunit']);
		
		$record = array();
		$record['kuncinilai'] = -1;
		$record['userkuncinilai'] = Modul::getUserName();
		$record['tglkuncinilai'] = date('Y-m-d');
		
		$err = Query::recUpdate($conn,$record,$p_table,"periode='$periodepenilaian' and kodeunit='$kodeunit'");
		
		list($p_posterr,$p_postmsg) = array($err,'Proses Kunci Nilai '.($err?'gagal':'berhasil'));
	}	
	
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$a_data = mUnit::getFakJur($conn,$a_kolom,$r_sort,$a_filter);
	
	// pesan untuk input
	/* $a_tooltipkrs = array();
	$a_tooltipkrs[] = 'Tahun kurikulum yang berlaku, terkait dengan mata kuliah yang bisa diprogram untuk sebaran';
	$a_tooltipkrs[] = 'Periode KRS/perkuliahan yang sedang berjalan';
	$a_tooltipkrs[] = 'Menentukan apakah waktu KRS Online sudah dibuka atau tidak';
	$a_tooltipkrs[] = 'Menentukan apakah pengisian data mahasiswa bisa dilakukan atau tidak';
	$a_tooltipkrs[] = 'Batas SKS untuk mahasiswa baru';
	$a_tooltipkrs[] = 'Menentukan apakah bisa mengambil mata kuliah jurusan lain atau tidak saat KRS Online';
	
	$a_tooltipnilai = array();
	$a_tooltipnilai[] = 'Periode penilaian yang sedang berjalan';
	$a_tooltipnilai[] = 'Menentukan apakah pengisian nilai bisa dilakukan atau tidak';
	$a_tooltipnilai[] = 'Nilai default yang diberikan kepada mahasiswa bila nilainya belum diisi saat pengisian nilai ditutup'; */
	
	// var_dump($_SERVER['REMOTE_ADDR']);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/hint.min.css" rel="stylesheet" type="text/css">	
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem" >
			<?	if(!empty($p_postmsg)) { ?>
			<center>
			<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:950px">
				<?= $p_postmsg ?>
			</div>
			</center>
			<div class="Break"></div>
			<?	} ?>
			<center>
			
			<div id="div_setting" style="float:left;">
				<header style="width:<?= $p_tbwidth-50 ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/KULIAH.png" onerror="loadDefaultActImg(this)"> <h1>Setting KRS/Kuliah</h1>
						</div>
					</div>
				</header>
				<?	/********/
					/* DATA */
					/********/
				?>
			<form name="pageformkrs" id="pageformkrs" method="post" enctype="multipart/form-data">
				<div class="box-content" style="width:<?= $p_tbwidth-72 ?>px">
				<table width="<?= $p_tbwidth-72 ?>" cellpadding="4" cellspacing="2" align="center">
				<?	$n_input = count($a_inputkrs);
					
					$a_required = array();
					for($i=0;$i<$n_input;$i++) {
						$t_row = $a_inputkrs[$i];
						
						if($t_row['notnull'])
							$a_required[] = $t_row['id'];
						if(empty($t_row['input']))
							$t_row['input'] = uForm::getInput($t_row);
				?>
					<tr>
						<td class="LeftColumnBG" width="100" style="white-space:nowrap">
							<?= $t_row['label'] ?>
							<?= $t_row['notnull'] ? '<span id="edit" style="display:none">*</span>' : '' ?>
						</td>
						<td class="RightColumnBG">
							<span<? /* class="hint--right" data-hint="<?= $a_tooltipkrs[$i] ?>" */ ?>><?= $t_row['input'] ?></span>
						</td>
					</tr>
				<?	} ?>
				</table>
				<div <? /* class="Break" */ ?>style="height:53px"></div>
				<input type="button" value="Simpan Setting KRS/Kuliah" class="ControlStyle" onclick="goSetKRS()">
				<!--input type="button" value="Generate Perwalian" class="ControlStyle" onclick="goSetPerwalian()"-->
				</div>
				<input type="hidden" name="act" id="act" value="setkrs">
			</form>
			</div>
			
			<div id="div_setting" style="float:left;margin-left:25px">
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/NILAI.png" onerror="loadDefaultActImg(this)"> <h1>Setting Penilaian</h1>
						</div>
					</div>
				</header>
				<?	/********/
					/* DATA */
					/********/
				?>
			<form name="pageformnilai" id="pageformnilai" method="post">
				<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
				<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
				<?	$n_input = count($a_inputnilai);
					
					$a_required = array();
					for($i=0;$i<$n_input-1;$i++) {
						$t_row = $a_inputnilai[$i];
						
						if($t_row['notnull'])
							$a_required[] = $t_row['id'];
						if(empty($t_row['input']))
							$t_row['input'] = uForm::getInput($t_row);
				?>
					<tr>
						<td class="LeftColumnBG" width="100" style="white-space:nowrap">
							<?= $t_row['label'] ?>
							<?= $t_row['notnull'] ? '<span id="edit" style="display:none">*</span>' : '' ?>
						</td>
						<td class="RightColumnBG">
							<span<? /* class="hint--right" data-hint="<?= $a_tooltipnilai[$i] ?>" */ ?>><?= $t_row['input'] ?></span>
						</td>
					</tr>
				<?	} ?>
					<tr>
						<td colspan="2"></td>
					</tr>
					<tr class="DataBG">
						<td colspan="2">Pesan Pengesahan Nilai</td>
					</tr>
					<tr>
						<td colspan="2"><?= uForm::getInput($a_inputnilai[$i]) ?></td>
					</tr>
				</table>
				<div class="Break"></div>
				<input type="button" value="Simpan Setting Penilaian" class="ControlStyle" onclick="goSetNilai()">
				</div>
				<input type="hidden" name="act" id="act" value="setnilai">
			</form>
			</div>
			<div style="clear:both"><br></div>
			
			<!-- untuk setting KRS per jurusan -->
			<div id="div_setting">
				<header style="width:<?= $p_tbwidth+300 ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/NILAI.png" onerror="loadDefaultActImg(this)"> <h1>Setting Pengisian KRS Per Jurusan</h1>
						</div>
					</div>
				</header>
				<?	/********/
					/* DATA */
					/********/
				?>
			<form name="pageformkrsjur" id="pageformkrsjur" method="post">
				<table width="<?= $p_tbwidth+300 ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
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
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							$t_key = $p_model::getKeyRow($row);
							
							$j = 0;
							if ($i % 2)
								$rowstyle = 'NormalBG';
							else
								$rowstyle = 'AlternateBG';
							$i++;
							if($row['level']==2)
								$padding=$row['level']*5;
							else
								$padding=0;
						
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center"><?= $row['kodeunit'] ?></td>
						<td align="left" style="padding-left:<?=$padding?>px"><?= $row['namaunit'] ?></td>
						<td align="center">
							<?php if($row['level']==2) { 
								if($row['iskrs']==1){
									echo '<img src="images/hide.gif" id="'.$row["kodeunit"].'" onclick="matikan(this)" title="Matikan" style="cursor:pointer">';
								}else{
									echo '<img src="images/show.gif" id="'.$row["kodeunit"].'" onclick="aktifkan(this)" title="Aktifkan" style="cursor:pointer">';
								}
							 } 
							 ?>
						</td>
						<td align="center">
							<?php if($row['level']==2) { 
								if($row['ispenilaian']==1){
									echo '<img src="images/hide.gif" id="'.$row["kodeunit"].'" onclick="matikanNilai(this)" title="Matikan" style="cursor:pointer">';
								}else{
									echo '<img src="images/show.gif" id="'.$row["kodeunit"].'" onclick="aktifkanNilai(this)" title="Aktifkan" style="cursor:pointer">';
								}
							 } 
							 ?>
						</td>
						<td nowrap valign="bottom" align="center">
							<?php if($row['level']==2) { 
								if($row['ispenilaian']==1){
							?>
								<input type="text" name="tglmulainilai_<?= $row['kodeunit']?>" id="tglmulainilai_<?= $row['kodeunit']?>" class="ControlStyle" maxlength="10" size="10" value="<?= $row['tglmulainilai']!=''?date('d-m-Y',strtotime($row['tglmulainilai'])):''?>">
								<img src="images/cal.png" id="tglmulainilai_trg_<?= $row['kodeunit']?>" style="cursor:pointer;" title="Pilih Tgl Mulai">
								<script type="text/javascript">
								Calendar.setup({
									inputField     :    "tglmulainilai_<?= $row['kodeunit']?>",
									ifFormat       :    "%d-%m-%Y",
									button         :    "tglmulainilai_trg_<?= $row['kodeunit']?>",
									align          :    "Br",
									singleClick    :    true
								});
								</script>
								&nbsp;-&nbsp;
								<input type="text" name="tglakhirnilai_<?= $row['kodeunit']?>" id="tglakhirnilai_<?= $row['kodeunit']?>" class="ControlStyle" maxlength="10" size="10" value="<?= $row['tglakhirnilai']!=''?date('d-m-Y',strtotime($row['tglakhirnilai'])):''?>">
								<img src="images/cal.png" id="tglakhirnilai_trg_<?= $row['kodeunit']?>" style="cursor:pointer;" title="Pilih Tgl Akhir">
								<script type="text/javascript">
								Calendar.setup({
									inputField     :    "tglakhirnilai_<?= $row['kodeunit']?>",
									ifFormat       :    "%d-%m-%Y",
									button         :    "tglakhirnilai_trg_<?= $row['kodeunit']?>",
									align          :    "Br",
									singleClick    :    true
								});
								</script>
								&nbsp;&nbsp;
								<input type="button" value="Simpan Periode" class="ControlStyle" onclick="goSetPeriode('<?= $row["kodeunit"]?>')">
								<input type="button" value="Kunci Nilai" class="ControlStyle" onclick="goKunciNilai('<?= $row["kodeunit"]?>')">
							<?}} ?>
						</td>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
				<div class="Break"></div>
				<!--input type="button" value="Simpan Setting KRS" class="ControlStyle" onclick="goSetKRSJur()"-->
				</div>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="kodemk" id="kodemk">
				<input type="hidden" name="kodeunit" id="kodeunit">
			</form>
			</div>
			</center>
		</div>
	</div>
</div>

<script type="text/javascript">

$(document).ready(function() {
	$("[id='div_setting']").hover(function() {
		$(this).siblings().fadeTo(0,0.5);
	}, function() {
		$(this).siblings().fadeTo(0,1);
	});
});

function goSetKRS() {
	document.getElementById("pageformkrs").submit();
}
function goSetPerwalian(){
	pageformkrs.act.value='genperwalian';
	document.getElementById("pageformkrs").submit();
}
function goSetNilai() {
	document.getElementById("pageformnilai").submit();
}
function goSetKRSJur() {
	pageformkrsjur.act.value='setkrsjur';
	document.getElementById("pageformkrsjur").submit();
}
function aktifkan(elem) {

	pageformkrsjur.kodemk.value=elem.id;
	pageformkrsjur.act.value='aktifkanmk';
	document.getElementById("pageformkrsjur").submit();
}
function matikan(elem) {
	pageformkrsjur.kodemk.value=elem.id;
	pageformkrsjur.act.value='matikanmk';
	document.getElementById("pageformkrsjur").submit();
}

function aktifkanNilai(elem) {
	pageformkrsjur.kodemk.value=elem.id;
	pageformkrsjur.act.value='aktifkanmknilai';
	document.getElementById("pageformkrsjur").submit();
}
function matikanNilai(elem) {
	pageformkrsjur.kodemk.value=elem.id;
	pageformkrsjur.act.value='matikanmknilai';
	document.getElementById("pageformkrsjur").submit();
}
function goSetPeriode(kodemk) {
	pageformkrsjur.kodemk.value=kodemk;
	pageformkrsjur.act.value='simpanperiodenilai';
	document.getElementById("pageformkrsjur").submit();
}

function goKunciNilai(kodeunit){
	var kunci = confirm("Apakah Anda yakin akan mengunci nilai pada prodi ini? \nJika nilai peserta pada kelas ini ada yang kosong, akan secara otomatis nilai di update menggunakan nilai pemutihan!");
	if(kunci) {
		pageformkrsjur.kodeunit.value=kodeunit;
		pageformkrsjur.act.value='kuncinilai';
		document.getElementById("pageformkrsjur").submit();
	}
}
</script>

</body>
</html>
