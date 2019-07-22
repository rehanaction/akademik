<?
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getModelPath('setting'));

	Modul::getFileAuth();

	//parameter
	$r_periode  	= $_POST['periode'];
	$r_tglawal  	= $_POST['tglawal'] ? $_POST['tglawal'] : date('d-m-Y') ;
	$r_tglakhir  	= $_POST['tglakhir'] ? $_POST['tglakhir'] : date('d-m-Y');

	//model
	$p_model=mLaporan;
	$p_title='Laporan Perbandingan Peserta USM';
	$p_tbwidth = 950;
	
	$data=$p_model::getDataPerbandinganPesertaUSM($conn,$r_periode,$r_tglawal,$r_tglakhir);
	$list_prodi =  mCombo::unitProdi($conn);
	$arrTahun = date::getYear($r_tglawal,$r_tglakhir);
	$namaperiode = Pendaftaran::getNamaPeriode($r_periode);
	$arrData = array();
	foreach ($data as $val){
		$arrData[$val['periodedaftar']][$val['pilihan1']] = $val['jumlah'];
	}
	$p_namafile = 'perbandinganpendaftar_'.$periode;
	Page::setHeaderFormat($r_format,$p_namafile);
	
?>

<html>
	<head>
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	</head>
	
	<body onLoad="window.print();">
		<div class="book">
			<div class="page">
				<div class="subpage">
				<!-- CONTENT HERE -->
				</div>    
			</div>
		</div>
	</body>
</html>



<!DOCTYPE html>
<html>
<head>
	<title>Cetak Perbandingan Pendaftar</title>   
	<link rel="icon" type="image/x-icon" href="image/favicon.png">
	<style>
		body{font-size:12pt;  font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;}
		th {background: #015593; color:#FFF}
	</style>
</head>
	<body style="background:white" onLoad="window.print();">		  
	<center>
		<div style="width:<?=$p_tbwidth.'px'?>;">
		LAPORAN PERBANDINGAN PENDAFTAR
		<table width="100%">
			<tr>
				<td width="100">Semester</td>
				<td width="10">:</td>
				<td><?= $namaperiode?></td>
			</tr>
			<tr>
				<td>Tanggal</td>
				<td>:</td>
				<td><?= date::indoDate(date::indoDateYmd($r_tglawal))?> s/d <?= date::indoDate(date::indoDateYmd($r_tglakhir))?> </td>
			</tr>
		</table>
		<br>
		<table border="1" width="100%" style="border-collapse:collapse;" cellpadding="3">
			<tr>
				<th>No</th>
				<th>Kode Prodi</th>
				<th>Prodi</th>
				<? foreach ($arrTahun as $key => $tahun){?>
				<th align="center"><?= $tahun?></th>
				<?	}?>
			</tr>
			<? $no=0; foreach ($list_prodi as $key => $val){ $no++;?>
			<tr>
				<td><?= $no?></td>
				<td><?= $val['kodenim']?></td>
				<td><?= $val['namaunit']?></td>
				<? foreach ($arrTahun as $keythn => $tahun){?>
				<td align="center"><?= cStr::formatNumber($arrData[$tahun][$key])?></td>
				<?	
				$t_total[$tahun]+= $arrData[$tahun][$key];
				}?>
				
			</tr>
			<?	}?>
			<tr>
				<td colspan="3"></td>
				<? foreach ($arrTahun as $keythn => $tahun){?>
				<td align="center"><b><?= cStr::formatNumber($t_total[$tahun])?></b></td>
				<?	}?>

			</tr>
		</table>
		</div>
	</center>
	</body>
</html>
