<?
 
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	Modul::getFileAuth();// include
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('pembayaran'));
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	
	Page::setHeaderFormat($r_format,$p_namafile);
	
	// properti halaman
	$p_title = 'Tagihan';
	$p_tbwidth = 900;
	$p_namafile = 'tagihan';
	
	$r_kodeunit = $_REQUEST['kodeunit'];
	$r_jenis =  'SPP';//$_REQUEST['jenistagihan'];
	$r_nomor = $_REQUEST['nomor'];
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	$infounit = mAkademik::infoUnit($conn,$r_kodeunit);
	$pendaftar = mAkademik::getNama($conn, $r_nomor);
	$tagihan = 	mTagihan::getInquiry($conn,$r_nomor,'',$r_jenis);
	// header
	Page::setHeaderFormat($r_format,$p_namafile);
	
?>
<html>
	<head>
		<title><?= $p_title ?></title>
		<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
		<link rel="icon" type="image/x-icon" href="images/favicon.png">
		<link href="style/stylerep.css" rel="stylesheet" type="text/css">
	</head>
	<body>
	<div align="center">
		<table width="<?=$p_tbwidth?>" style="border:solid 10px;">
			<tr>
				<td width="90"><img src="images/undana.png"></td>
				<td><span style="font-size:25px; font-weight:bold">KUITANSI SPP</span> <br> <span style="font-weight:bold; font-size:14px">UNIVERSITAS NUSA CENDANA</span></td>
				<td>Logo Bank NTT</td>
			</tr>				
		</table>
		<br>
		<table width="<?=$p_tbwidth?>" border="0" style="font-size:14px" cellspacing="5px">
			<tbody>
				<tr>
					<td colspan="4">Terima Dari</td>
				</tr>
				<tr>
					<td width="220">Nama Wajib Bayar</td>
					<td>:</td>
					<td colspan="2"><?= $pendaftar['nama']?></td>				
				</tr>
				
				<tr>
					<td>Nomor Registrasi</td>
					<td>:</td>
					<td colspan="2"><?= $r_nomor?></td>				
				</tr>
				<tr>
					<td>Semester</td>
					<td>:</td>
					<td colspan="2"> <?= $pendaftar['semestermhs']?>
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					Strata: <?= $pendaftar['strata']?></td>				
				</tr>			
				<tr>
					<td>Fak.Jur / Prog.Studi</td>
					<td>:</td>
					<td colspan="2"><?= $pendaftar['fakultas']?> / <?= $pendaftar['namaunit']?></td>				
				</tr>			
				<tr>
					<td>Untuk Rekening Giro</td>
					<td>:</td>
					<td colspan="2">No. 0039-01-001585-30-5 
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					An. Bendahara Penerima Undana</td>				
				</tr>
				<tr>
					<td>Untuk Pembayaran SPP bulan</td>
					<td>:</td>
					<td colspan="2">.................. s/d..........</td>				
				</tr>
				<tr>
					<td>Jumlah Uang</td>
					<td>:</td>
					<td colspan="2">Rp. <?= number_format($tagihan[0]['nominaltagihan']);?> </td>				
				</tr>
				<tr>
					<td>Terbilang</td>
					<td>:</td>
					<td colspan="2"><?= CStr::terbilang($tagihan[0]['nominaltagihan'])?></td>				
				</tr>
				<tr colspan="4">
					<td><br></td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2" style="padding-bottom:50px">Diterima Oleh Bank NTT</td>
					<td colspan="2" align="right" style="padding-right:40px; padding-bottom:50px">Kupang, <?= date('d-m-Y')?></td>				
				</tr>
				
				<tr>
					<td colspan="2">Tanda Tangan</td>
					<td colspan="2" align="right">Nama Dan Tanda Tangan Penyetor</td>				
				</tr>
			</tfoot>
		
		</table>
	</div>
	</body>
</html>
