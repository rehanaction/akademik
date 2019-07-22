<?
// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
        require_once($conf['model_dir'].'m_laporan.php');
        $r_kodeunit    	= CStr::removeSpecial($_REQUEST['kodeunit']);
        $r_periode    	= CStr::removeSpecial($_REQUEST['periode']);
        $r_jalur    	= CStr::removeSpecial($_REQUEST['jalur']);
        $r_gelombang    = CStr::removeSpecial($_REQUEST['gelombang']);
        $r_nopendaftar	= CStr::removeSpecial($_REQUEST['nopendaftar']);
		$r_format = CStr::removeSpecial($_REQUEST['format']);
        //model
        $p_model='mPendaftar';
        
        $p_namafile = 'kartu_'.$r_periode.'_'.$r_jalur.'_'.$r_gelombang;
        
		$data = mLaporan::getDataPeserta($conn,$kodeunit,$periode,$jalur,$gelombang,$nopendaftar,true,false);
        
		Page::setHeaderFormat($r_format,$p_namafile);

?>
<!DOCTYPE html>
    <html>
		<head>
				<title>SURAT PERJANJIAN PEMBAYARAN BIAYA KULIAH dan PERNYATAAN TUNDUK pada TATA TERTIB KAMPUS</title>   
				<link rel="icon" type="image/x-icon" href="image/favicon.png">
				<style>
					body {font-family: "Arial"}
					.container{ width:950px;}
					.univ{font-size:12px}
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
					<table align="left" width="100%">
						<tr>
							<td width="100"><img src="images/esaunggul.png"></td>
							<td><span class="univ">U n i v e r s i t a s</span> <br><span class="name">Esa Unggul</span></td>
						</tr>
					</table>
					<div>
					<table align="right">
						<tr>
							<td align="right" class="judul">SURAT PERJANJIAN PEMBAYARAN BIAYA KULIAH<br>dan PERNYATAAN TUNDUK pada TATA TERTIB KAMPUS</td>
						</tr>
					</table>
					
					<table align="left" width="100%">
						<tr>
							<td colspan="6">Saya yang bertanda tangan dibawah ini, <b>Mahasiswa Universitas Esa Unggul :</b></td>
						</tr>
						<tr>
							<td>Nama</td>
							<td>:</td>
							<td><?= $val['gelardepan'].' '.$val['nama'].' '.$val['gelarbelakang']?></td>
							<td>NIM</td>
							<td>:</td>
							<td><?= $val['nimpendaftar']?></td>
						</tr>
						<tr>
							<td>Fakultas</td>
							<td>:</td>
							<td><?= $val['namaunitfakultas']?></td>
							<td>Jurusan</td>
							<td>:</td>
							<td><?= $val['namaunit']?></td>
						</tr>
						<tr>
							<td>Alamat Rumah</td>
							<td>:</td>
							<td colspan="4"><?= 'Jalan: '.$val['Jalan'].', RT/RW: '.$val['rt'].'/'.$val['rw'].', Kecamatan: '.$val['kec'].', Kelurahan: '.$val['kel']?></td>
						</tr>
						<tr>
							<td>Kota</td>
							<td>:</td>
							<td><?= $val['namakota']?></td>
							<td>Kode Pos</td>
							<td>:</td>
							<td><?= $val['kodepos']?></td>
						</tr>
						<tr>
							<td>Telephone</td>
							<td>:</td>
							<td><?= $val['telp']?></td>
							<td>Handphone</td>
							<td>:</td>
							<td><?= $val['hp']?></td>
						</tr>
						<tr>
							<td colspan="6">Dengan ini saya menyatakan bahwa : </td>
						</tr>

					</table>
					<br>
					</div>
					<div style="clear:both"></div>
					<div align="left">
					<ol class="justify">
						<li>Pembayarann Biaya Kuliah
							<ol>
								<li>Saya Bersedia membayar biaya kuliah semester I secara tunai ataupun ansuran, bila diangsur maka pembayaran pada saat daftar ulang pembayaran angsuran ke 2 dan seterusnya dilakukan setiap bulan sesuai jadwal yang telah ditetapkan dalam SPDU (Surat Pemberitahuan Daftar Ulang).</li>
								<li>Saya bersedia menerima pengembalian sebesar 100% dari biaya yang telah saya bayarkan apabila saya mengundurkan diri dengan alasan tidak lulus UAN SMU/K, diterima di perguruan tinggi Negeri (PTN) melalui jalur SPMB Nasional, bukan ujian saringan masuk yang diselenggarakan secara mandiri oleh PTN ybs.</li>
								<li>Saya bersedia menerima pengembalian sebesar 70% dari biaya yang telah kami  ayarkan apabila saya mengundurkan diri dengan alasan diterima di PTN (melalui tes mandiri), dan PTS</li>
								<li>Saya bersedia dan tidak akan menuntut pengembalian semua biaya yang telah saya bayarkan apabila saya mengundurkan diri dengan alasan diluar ketentuan no.2 dan no.3 diatas.</li>
								<li>Saya akan mengajukan pengunduran diri dengan alasan diterima dari PTN dan PTS (butir 2 dan 3) paling lambar 14 (empat belas) hari setelah pengumuman kelulusan, dengan menyerahkan:
									<ol style="list-style-type: lower-alpha;">
										<li>Pengumuman kelulusan SPMB dan fotocopy kartu peserta ujian SPMB Nasional, Mandiri dan PTS</li>
										<li>Bukti pembayaran asli dari Esa Unggul</li>
										<li>Bukti pembayaran biaya kuliah di PTN/PTS yang bari, debagai bukti telah daftar ulang di PTN/PTS lain.</li>
										<li>Form pengembalian biaya kuliah yang telah diisi, den uang dapat diambil paling cepat 2 (dua) minggu setelah diajukan setiap hari kamis.</li>
									</ol>
								</li>
								<li>Jika nama mahasiswa tidak terdaftar dalam website www.forlap.dikti.go.id maka pihak universitas esa unggul tidak dapat memberikan ijazah dan seluruh biaya kuliah maupun uang SPP yang telah dibayar tidak dapat ditarik kembali</li>
								<li>Wajib bagi setiap mahasiswa:
									<ol style="list-style-type: lower-alpha;">
										<li>Membayar biaya kuliah semester ke 2,3,4 dst sampai lulus (berupa biaya paket) sesuai ketentuan yang telah ditetapkan (biaya skripsi, biaya wisuda yang ditentukan kemudian)</li>
										<li>Mengikuti perkuliahan semester pendek (wajib setiap mahasiswa) dengan biaya Rp. 1.800.000,- per semester pendek</li>
									</ol>
								</li>
								<li>Apabila saya tidak dapat melunasi pembayaran yang telah saya nyatakan dalam surat perjanjian ini,maka saya bersedia menerima sanksi administratif maupun sanksi akademik yang berlaku dan dapat pula berakibat saya dikeluarkan dari Universitas Esa Unggul.</li>
								<li>Biaya kuliah semester kedua dan seterusnya dapat diangsur 2(dua) kali, pembayaran pertama 50% pada saat daftar ulang semester yang bersangkutan dan pembayaran kedua 50% paling lambat 2 (dua) minggu sebelum ujian tengah semester(UTS).</li>								
							</ol>
						</li>
						<li>NARKOBA
							<ul style="list-style-type:none">
							<li>Menyatakan bebas dari Narkoba dan sejenisnya, jika terbukti sebagai pemakai/pengedat didalam atau diluar kampus akan diserahkan kepada pihak yang berwajib dan dikeluarkan dari UEU tanpa hormat</li>
							</ul>
						</li>
							
						<li>Tata Tertib
							<ul style="list-style-type:none">
							<li>Sya bersedia mentaati semua ketentuan Administrasi akademik/Keuangan,Disiplin tata tertib, Etika/NormaAkademik mahasiswa Universitas Esa Unggul, serta menerima segenap sanksi yang tertera dalam peraturan UEU apabila saya melanggarnya.</li>
							</ul>
						</li>
					</ol>
					</div>
					<table width="80%" border="0">
						<tr>
							<td width="70%">Universitas Esa Unggul <br><br><br><br> Nama Petugas PPMB</td>
							<td>Jakarta, ...... ........ ...........<br><br><br><br><?= $val['gelardepan'].' '.$val['nama'].' '.$val['gelarbelakang']?></td>
						</tr>
					</table>
				</div>
				<div class="footer"></div>
			</center>
			
			<? } ?>
		</body>
    </html>
