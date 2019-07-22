<?
// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
    
	require_once($conf['model_dir'].'m_pendaftar.php');
	require_once($conf['model_dir'].'m_laporan.php');
	//parameter
	$kodeunit    = CStr::removeSpecial($_REQUEST['kodeunit']);
	$periode      = CStr::removeSpecial($_REQUEST['periode']);
	$jalur    = CStr::removeSpecial($_REQUEST['jalur']);
	$gelombang      = CStr::removeSpecial($_REQUEST['gelombang']);
	$nopendaftar      = CStr::removeSpecial($_REQUEST['nopendaftar']);
	$conn->debug = true;
	$r_format = CStr::removeSpecial($_REQUEST['format']);

	//model
	$p_model='mPendaftar';
	$p_title='SURAT PEMBERITAHUAN DAFTAR ULANG (SPDU)';
	$p_tbwidth = 950;
	$data=$p_model::getAbsen($conn, $periode,$jalur,$tgltes);
	$p_namafile = 'absensi_'.$periode.'_'.$jalur.'-'.$gelombang;
	
	$data = mLaporan::getDataPeserta($conn,$kodeunit,$periode,$jalur,$gelombang,$nopendaftar);
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<!DOCTYPE html>
    <html>
                <head>
                        <title><?= $p_title ?></title>   
                        <link rel="icon" type="image/x-icon" href="image/favicon.png">
                        <style>
                        @media print {
							.footer {page-break-after: always; border-bottom:solid 1px}
						}
						.footer {border-bottom:solid 1px; width:950px;}

						</style>
                        
                </head>
                <body style="background:white; font-size:12pt;font-family: Arial,Helvetica Neue,Helvetica,sans-serif;" onLoad="window.print();">
					
					<? foreach ($data as $key=> $val) { ?>

									<? 
									if (!empty($val['keuangan']))
									foreach ($val['keuangan'] as $key2 => $val2){
										$total+=$val2['nominaltagihan'];
										} ?>


					<center>
					<div style="width:950px; text-align:left; line-height:25px">
						<div style="text-align:center">
							SURAT PEMBERITAHUAN DAFTAR ULANG (SPDU)<br>
							PESERTA GELOMBANG 1
						</div>
						Kepada Yth<br>
						<?= $val['gelardepan'].' '.$val['nama'].' '.$val['gelarbelakang']?><br>
						No.Test <?= $val['nopendaftar']?>
						<br> 
						<ol>
							<li>Anda Telah dinyatakan LULUS ujian Saringan Masik (USM) Gelombang 1 pada <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Fakultas : <?= $val['namaunitfakultas']?> -  jurusan : <?= $val['namaunit']?> </li>
							<li>Biaya Kuliah semester 1 yang harus diselesaikan adalah Rp.<?= number_format($total)?></li>
							<li>Calon mahasiswa yang telah dinyatakan lulus USM diwajibkan mendaftar ulang dengan membayar Biaya Kuliah semester 1 secara tunai atau angsuran.</li>
							<li>Daftar Ulang dengan pembayaran pola angsuran :
								<table>
									<? 
									if (!empty($val['keuangan']))
									foreach ($val['keuangan'] as $key2 => $val2){?>
									<tr> <td>Angsuran Ke <?= $val2['angsuranke']?> sejumlah Rp. <?= number_format($val2['nominaltagihan'])?> paling lambat tanggal <?= $val2['tanggaldeadline']?> </td> </tr>
									<? } ?>
								</table>
							</li>
							<li> a. Pembayaran tidak lewat 3 hari sejak beli formulir dapat potongan (Time Payment Save) Rp. 500.000<br>
								 b. Pembayaran tidak lewat 1 minggu sejak beli formulir dapat potongan (Time Payment Save) Rp 250.000
							</li>
							<li>Pembayaran Biaya Kuliah Semester 1 Lunas:
								a. Lunas tidak lewat 3 hari sejak oembelian formulir, dapat potongan Rp. 2.500.000<br>
								b. Lunas tidak lewat 1 minggu sejak beli formulir, dapat potongan Rp. 1.000.000<br>
								c. Lunas lewat 1 minggu atau lebih sejak beli formulir, dapat potongan Rp. 500.000<br>
								d. Semua potongan hanya berlaku 1 kali untuk semester pertama saja.
							</li>
							<li>Pembayaran ke 1 disetor langsung ditransfer ke BANK BUKOPIN No Rek. 01.20.02.1035 atas nama Universitas INDONUSA ESA Unggul, Pembayaran ke 2 dan seterusnya wajib disetorkan ke bank BUKOPIN melalui ATM.</li>
							<li>Setelah membayar di BANK, laporkan kepada petugas PPMB/HUMAS untuk mendapatkan pengarahan daftar ulang dan pengambilan kartu rencana studi (KRS), Kartu Studi Mahasiswa (KSM) semester Pendek Awal (SPA) dan semester 1 di Biro Administrasi Akademik.</li>
							<li>Mahasiswa Baru <?= date('Y')?> diwajibkan membuka rekening BANK BUKOPIN cabang Kampus Emas (ATM BUKOPIN berlaku sebagai kartu mahasiswa yang berguna juga sebagai kartu debet pada counter berlogo VISA ELECTRON).</li>
							<li>Semester pendek wajib diikuti oleh mahasiswa dan dikenakan biaya Rp. 1.800.000</li>
							<li>Biaya semester ke 2 dapat diangsur 2 kali, pertama 50% pada saat daftar ulang semester yang bersangkutan dan pembayaran kedua 50% paling lambat dua (2) minggu sebelum Ujian Tengah Semester (UTS).</li>
						</ol>
						<br><br><br>
						Jakarta, <br>
						Panitia Penerimaan Mahasiswa Baru,
						<br><br><br><br><br>
						IR.JATMIKO, MBA
						 
						
					</div>
					<div class="footer"></div>
					</center>

					<? } ?>
                </body>
    </html>
