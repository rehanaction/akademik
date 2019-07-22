<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=false;
	// hak akses
	Modul::getFileAuth();

	// variabel request
	$r_nim = CStr::removeSpecial($_REQUEST['nim']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);

	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('settingskpi'));
	require_once(Route::getModelPath('settingskpiprodi'));
	require_once(Route::getUIPath('form'));
	require_once(Route::getModelPath('jenispenghargaan'));

	$a_kategori = mJenisPenghargaan::getArray($conn);
	$a_kategorieng = mJenisPenghargaan::getArrayEng($conn);

	// properti halaman
	$p_title = 'Cetak SKPI';
	$p_tbwidth = 800;
	$p_namafile = 'mahasiswa_prestasi'.$r_kodeunit;

	$all_data = mLaporan::getSKPI($conn,$r_nim);

	//untuk mengambil setting global
	$a_setting = mSettingSkpi::getData($conn,'1');

	// header
	Page::setHeaderFormat($r_format,$p_namafile);

	//check nomor dan status
	$i_mhs = mLaporan::getNoSKPI($conn,$r_nim);

if($i_mhs['statusmhs'] == 'L'){
	//jika nomor kosong digeneratekan nomor
	if(empty($i_mhs['nomorskpi']))
	{
		if($i_mhs['countertahun'] == date('Y'))
			$r_nomor = ($i_mhs['counternomor']+1);
		else
			$r_nomor = 1;

		$r_nomorskpi = str_pad($r_nomor,4,'0',STR_PAD_LEFT).'/'.$i_mhs['formatnomor'].'/'.date('Y');

		$ok=true;
		$conn->BeginTrans();
		//update counter nomor jurusan
		$a_settingprodi = mSettingSkpiProdi::getData($conn,$i_mhs['kodeunit']);
		$recno = array();
		$recno['kodeunit'] = $i_mhs['kodeunit'];
		$recno['counternomor'] = $r_nomor;
		$recno['countertahun'] = date('Y');

		//jika setting prodi belum dibuat maka insert, jika tidak maka update
		if(empty($a_settingprodi)){
			$p_posterr = mSettingSkpiProdi::insertRecord($conn,$recno);
		}else{
			$p_posterr = mSettingSkpiProdi::updateRecord($conn,$recno,$i_mhs['kodeunit']);
		}

		//update nomor skpi mahasiswa
		if(empty($p_posterr) and !empty($i_mhs['formatnomor'])){
			$p_posterr = mMahasiswa::updateRecord($conn,array('nomorskpi'=>$r_nomorskpi,'tglskpi'=>date('Y-m-d')),$r_nim);
		}
		$conn->CommitTrans(Query::isOK($p_posterr));
	}else{
		$r_nomorskpi = $i_mhs['nomorskpi'];
	}
?>
<html>
<head>
<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<title>Surat Keterangan Pendamping Ijazah</title>
	<style>
    	#main_wrap{
				width:900px;font-family:Arial, Helvetica,
				sans-serif;font-size:10pt;margin:auto;padding-left:25px;padding-right:25px;
			}
		.title{font-weight:bold;font-size:14pt;text-align:center;padding-bottom:15px;border-bottom:1px dashed;}
		.nomor{text-align:center;padding:11px;}
		.content-id{padding-top:5px;text-align:justify}
		.content-en{padding-top:5px;text-align:justify;font-style:italic;font-size:9pt;}
		.content-title{font-size:11pt;}
		.content-subtitle{font-size:10pt;}
		.border{-webkit-border-radius: 7px;-moz-border-radius: 7px;
				border-radius: 7px;border:1px solid;padding:5px;word-wrap:break-word;
				}
		.en{font-style:italic}
		.grading{font-size:6pt}
		.small-id{font-size:9pt;}
		.small-en{font-size:8pt}
    	.border {-webkit-border-radius: 7px;-moz-border-radius: 7px;border-radius: 7px;border:1px solid;padding:5px;min-height:17px}
		table{font-size:10pt;text-align:justify;}
		table td{vertical-align:top;}
		table.add-content{border-collapse: collapse;}
		table.add-content tr.add-content-title td{background-color:#99CCFF}
		table.add-content td{border:1px solid;padding:5px}
    </style>
</head>
<body>
<?php
foreach($all_data as $a_data) {
	if(empty($a_data['tglskpi']))
		$a_data['tglskpi'] = date('Y-m-d');

	if(empty($i_mhs['formatnomor']))
		echo "<script>alert('Format nomor prodi belum di setting.')</script>";
	else {
?>
<div id="main_wrap">
	<?= str_repeat('<br />', 8) ?>
	<div class="title">SURAT KETERANGAN PENDAMPING IJAZAH (<i>DIPLOMA SUPPLEMENT</i>)</div>
	<div class="nomor">
		Nomor : <?=$r_nomorskpi?>
	</div>
	<div class="content-id">
		<?=$a_setting['pendahuluan']?>
	</div>
	<div class="content-en">
		<?=$a_setting['pendahuluanen']?>
	</div><br>
	<?php require_once('inc_skpidatadiri.php'); ?>
	<?php require_once('inc_skpicapaian3.php'); ?>
  	<?php require_once('inc_skpicapaian4.php'); ?>
        <?php require_once('inc_skpicapaian5.php'); ?>
	<?php require_once('inc_skpiaktivitas1.php'); ?>

	<!--<?php require_once('inc_skpiinfopt.php'); ?>-->
        <?php require_once('inc_skpiinfopt1.php'); ?>
        <?php require_once('inc_skpiinfopt2.php'); ?>
	<!--<?php require_once('inc_skpikkni.php'); ?>-->
	<!--<?php require_once('inc_skpipengesahan.php'); ?>-->
        <?php require_once('inc_skpikknisah.php'); ?>
</div>
<?php
	}
}
?>
 </body>
 </html>
<?php
}
else
{
	echo "Mahasiswa Belum Lulus";
}
?>
