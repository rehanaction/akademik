	<table width="100%">
		<tr>
		  <td><b>3.2.</b></td>
		  <td colspan="3"><em><strong>Informasi Tambahan, dengan rincian </strong> </em><strong> / </strong><em>Other information :</em></td>
		  <td>&nbsp;</td>
		  <td colspan="2">&nbsp;</td>
		</tr>
	 </table>
	 <table width="100%">
		 <?
		 /*
		<tr>
			<td width="5%">&nbsp;</td>
			<td width="45%">
				Pemegang  Surat Keterangan Pendamping Ijazah ini memiliki :
			</td>
		  <td width="5%">&nbsp;</td>
		  <td width="45%">
			  <em>
				The holder of this diploma  supplement has:
		   </em>
		   </td>
		</tr>
		*/
		?>
		<tr>
			<td width="5%">&nbsp;</td>
			<td width="45%">
				<!-- <?php
					foreach($a_kategori as $idk => $valk) {
						echo '<strong>'.'3.2.'.$idk.' '.$valk.'</strong><br>';
						if(!empty($a_data['penghargaan'][$idk])){
							echo '<ol style="margin:0;padding-left:1.15em">';
							foreach($a_data['penghargaan'][$idk] as $penghargaan)
								echo '<li>'.$penghargaan['namapenghargaan'].'</li>';
							echo '</ol>';
						}
						echo "<br>";
					}
				?> -->
				<?php
					foreach($a_pengalaman as $pengalaman)
					{
						//Keikutsertaan dalam Organisasi
						if($pengalaman['kodekategori'] == 'O')
						{
							$ikutOrganisasi[] = array(
								"idpenghargaan" => $pengalaman['idpengalaman'],
								"tglpenghargaan" => $pengalaman['tglpengajuan'],
								"namapenghargaan" => $pengalaman['namakegiatan'],
								"namapenghargaanenglish" => $pengalaman['namakegiatanen'],
								"isvalid" => $pengalaman['isvalid'],
								"idjenispenghargaan" => 2
							);
						}
						//Sertifikat Keahlian
						if($pengalaman['kodekategori'] == 'K')
						{
							$keahlian[] = array(
								"idpenghargaan" => $pengalaman['idpengalaman'],
								"tglpenghargaan" => $pengalaman['tglpengajuan'],
								"namapenghargaan" => $pengalaman['namakegiatan'],
								"namapenghargaanenglish" => $pengalaman['namakegiatanen'],
								"isvalid" => $pengalaman['isvalid'],
								"idjenispenghargaan" => 3
							);
						}
						//Keikutsertaan Seminar
						if($pengalaman['kodekategori'] == 'S')
						{
							$seminar[] = array(
								"idpenghargaan" => $pengalaman['idpengalaman'],
								"tglpenghargaan" => $pengalaman['tglpengajuan'],
								"namapenghargaan" => $pengalaman['namakegiatan'],
								"namapenghargaanenglish" => $pengalaman['namakegiatanen'],
								"isvalid" => $pengalaman['isvalid'],
								"idjenispenghargaan" => 6
							);
						}
					}
					foreach($a_kkn as $kkn)
					{
						$kp[] = array(
							"idpenghargaan" => $kkn['idkp'],
							"tglpenghargaan" => "",
							"namapenghargaan" => $kkn['judulkp'],
							"namapenghargaanenglish" => $kkn['judulkpen'],
							"isvalid" => "",
							"idjenispenghargaan" => 4
						);
					}
					foreach($a_skripsi as $skripsi)
					{
						$pa[] = array(
							"idpenghargaan" => $skripsi['idta'],
							"tglpenghargaan" => $skripsi['tglselesai'],
							"namapenghargaan" => $skripsi['judulta'],
							"namapenghargaanenglish" => $skripsi['judultaen'],
							"isvalid" => "",
							"idjenispenghargaan" => 5
						);
					}
					foreach($a_prestasi as $prestasi)
					{
						$prest[] = array(
							"idpenghargaan" => $prestasi['idprestasi'],
							"tglpenghargaan" => $prestasi['tglprestasi'],
							"namapenghargaan" => $prestasi['namaprestasi'],
							"namapenghargaanenglish" => $prestasi['namaprestasien'],
							"isvalid" => $prestasi['namaprestasien'],
							"idjenispenghargaan" => 1
						);
					}
					$a_data['penghargaan'][1] = $prest;
					$a_data['penghargaan'][2] = $ikutOrganisasi;
					$a_data['penghargaan'][3] = $keahlian;
					$a_data['penghargaan'][4] = $kp;
					$a_data['penghargaan'][5] = $pa;
					$a_data['penghargaan'][6] = $seminar;
				?>
				<?php foreach($a_kategori as $idk => $valk): ?>
					<strong>3.2.<?=$idk.' '.$valk?></strong><br>
						<?php if(!empty($a_data['penghargaan'][$idk])):?>
							<ol style="margin:0;padding-left:1.15em">
								<?php foreach($a_data['penghargaan'][$idk] as $penghargaan): ?>
									<li><?=$penghargaan['namapenghargaan']?></li>
								<?php endforeach; ?>
							</ol>
						<?php endif; ?>
					<br>
				<?php endforeach; ?>
			</td>
		  <td width="5%">&nbsp;</td>
		  <td width="45%">
			  <em>
				<?php
					foreach($a_kategorieng as $idk => $valk){
						echo '<strong>'.'3.2.'.$idk.' '.$valk.'</strong><br>';
						if(!empty($a_data['penghargaan'][$idk])){
							echo '<ol style="margin:0;padding-left:1.15em">';
							foreach($a_data['penghargaan'][$idk] as $penghargaan)
								echo '<li>'.$penghargaan['namapenghargaanenglish'].'</li>';
							echo '</ol>';
						}
						echo "<br>";
					}
				?>
			   </em>
		   </td>
		</tr>
	</table>
	<br><br>
	 <table width="100%">
		<tr>
			<td width="5%">&nbsp;</td>
			<td width="45%">
				Catatan :
			</td>
		  <td width="5%">&nbsp;</td>
		  <td width="45%">
			  <em>
				Note :
		   </em>
		   </td>
		</tr>
		<tr>
			<td width="5%">&nbsp;</td>
			<td width="45%">
				Program-program tersebut di atas terdiri atas kegiatan untuk mengembangkan
				soft skills mahasiswa. Daftar kegiatan ko-kurikuler dan ekstra-kurikuler
				 yang diikuti oleh pemegang SKPI ini terlampir.
			</td>
		  <td width="5%">&nbsp;</td>
		  <td width="45%">
			  <em>
				The above-mentioned programs comprise of activities that develop student’s soft skills.
				 A list of co-curricular and extra curricular activities
				taken by the holder of this supplement is attached.
			   </em>
		   </td>
		</tr>
	</table>
	<br><br>
	<?php // <div style="page-break-after:always"></div> ?>