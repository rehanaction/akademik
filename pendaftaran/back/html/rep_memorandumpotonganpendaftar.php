<?
// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
        require_once($conf['model_dir'].'m_laporan.php');
        $r_kodeunit    	= CStr::removeSpecial($_REQUEST['kodeunit']);
        $r_periode    	= CStr::removeSpecial($_REQUEST['periode']);
        $r_jalur    	= CStr::removeSpecial($_REQUEST['jalur']);
        $r_gelombang    = CStr::removeSpecial($_REQUEST['gelombang']);
        $r_nopendaftar	= CStr::removeSpecial($_REQUEST['nopendaftar']);
        $r_nomor	= CStr::removeSpecial($_REQUEST['nomor']);
		$r_format = CStr::removeSpecial($_REQUEST['format']);
        //model
        $p_model='mPendaftar';        
        $p_namafile = 'memorandum_'.$r_periode.'_'.$r_jalur.'_'.$r_gelombang;
        
		$data = mLaporan::getDataPeserta($conn,$kodeunit,$periode,$jalur,$gelombang,$nopendaftar,true,false);
        
		Page::setHeaderFormat($r_format,$p_namafile);

?>
<!DOCTYPE html>
    <html>
		<head>
				<title>Memorandum Potongan Pendaftar</title>   
				<link rel="icon" type="image/x-icon" href="image/favicon.png">
				<style>
					body {font-family: "Arial"; font-size:12pt; }
					.container{ width:950px;}
					.name{font-size:32px; font-weight:bold}
					.judul{font-weight:bold}
					.justify{text-align:justify; list-style-type: upper-roman}
					
					@media print {
							.footer {page-break-after: always; border-bottom:solid 1px}
						}
						.footer {border-bottom:solid 1px; width:950px;}
					
				</style>
		</head>
		<body>
			<? foreach ($data as $key=> $val) { ?>
			<center>
				<div class="container">
						<table align="left">
							<tr>
								<td colspan="3"><span class="name">MEMORANDUM</span>  <br><span>No <?= $r_nomor?></span> <br><br></td>
							</tr>
							<tr>
								<td>Kepada Yth</td>
								<td> : </td>
								<td> Ka.DKS</td>
							</tr>
							<tr>
								<td>Perihal</td>
								<td> : </td>
								<td> Keringanan biaya kuliah atas nama <?= $val['gelardepan'].' '.$val['nama'].' '.$val['gelarbelakang']?></td>
							</tr>
							<tr>
								<td>Lampiran</td>
								<td> : </td>
								<td> 1 (satu) Berkas</td>
							</tr>
							<tr valign="top">
								<td>Cc.</td>
								<td> : </td>
								<td> 
									<ol>
										<li>Rektor</li>
										<li>Warek II</li>
										<li>Ka. Biro Keuangan Yayasan</li>
										<li>Wadek II FH</li>
										<li>Ybs.</li>
									</ol>
								</td>
							</tr>
						</table>
						<div style="clear:both"></div>
						<hr class="container"> <br>
						<div style="clear:both"></div>
						<div>
							<p align="left" style="text-align:justify;">
								Dengan Hormat, <br><br>
								Menindaklanjuti surat permohonan orang tua sdr <?= $val['gelardepan'].' '.$val['nama'].' '.$val['gelarbelakang']?> (terlampir), berikut ini kami sampaikan bahwa PPMB <?= date('Y').'/'.date('Y')+1?> memberikan Biaya kuliah Semester kepada Ybs. dalam rangka turut mencerdaskan bangsa,
								peningkatan kualitas SDM dan kepedulian universitas terhadap civitas akademika.<br><br>
								Sebagai informasi tambahan, Saudara kandung ybs. (<?= $val['gelardepan'].' '.$val['nama'].' '.$val['gelarbelakang']?>,NIM :<?= $val['nimpendaftar']?>) Juga merupakan mahasiswa Esa Unggul Jurusan <?= $val['namaunit']?>
								
								<br><br>
								Keringanan Biaya Kuliah yang diberikan kepada yb. yaitu: <br><br><br>
								<b>Potongan Biaya Semester I sebesar Rp. <?= number_format($val['potonganbeasiswa'])?></b>
								<br><br><br>
								Adapun biaya selain tersebut diatas (Pendaftaran, Sidang Skripsi, dan wisuda) dikenakan sesuai dengan ketentuan yang berlaku. masa berlaku keringanan ini adalah selama 1 (satu) semester
								pada semester <?= (substr($r_periode,5,1))=='1' ? 'Gasal' : 'Genap'  ?> <?= substr($r_periode,0,4)?>. <br>
								<br>
								Demikianlah, atas perhatian dan kerjasamanya kami ucapkan terimakasih.<br><br>
								
								Jakarta, <?= Date::indoDate(date('Y-m-d')) ?><br>
								Hormat Kami, <br><br><br><br>
								
								<span style="text-decoration:underline">Ir.Jatmiko, MM,MBA</span><br>
								Ka. PPMB <?= date('Y').'/'.date('Y')+1?>
								
							</p>
						</div>
				</div>
				<div class="footer"></div>
			</center>
			
			<? } ?>
		</body>
    </html>
