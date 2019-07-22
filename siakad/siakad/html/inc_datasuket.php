	<div class="border">
		<table width="100%">
			<tr>
				<td width="30" style="vertical-align:top"><b>I.</b></td>
				<td>
					<div class="content-title"><b>INFORMASI TENTANG IDENTITAS DIRI PEMEGANG SKPI</b></div>
					<div class="content-subtitle en">Information Identifying the holder of diploma supplement</div>
			  </td>
			</tr>
		</table>
	</div>
	<table width="100%">
		<tr>
			<td width="5%"><b>&nbsp;</b></td>
			<td width="45%"><b>Nama Lengkap </b><br><i> Full Name</i></td>
			<td width="5%"><b>&nbsp;</b></td>
			<td width="45%"><b>Tanggal lulus </b><br><i> Date of  graduation</i></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td><div class="border"><?= $a_data['nama']?></div></td>
		  <td>&nbsp;</td>
		  <td><div class="border"><?= CStr::FormatDateInd($a_data['tgltranskrip'])?></div></td>
		</tr>
		<tr>
		  <td><b>&nbsp;</b></td>
		  <td><strong>Tempat, tanggal lahir </strong> <br> <em>Place  and date of birth</em></td>
		  <td><b>&nbsp;</b></td>
		  <td><strong>Nomor Ijazah </strong> <br><em>Diploma  Serial Number</em></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td>
			<div class="border">
				<?= $a_data['tmplahir']?>, <?= CStr::formatDateInd($a_data['tgllahir']) ?><br>
				<em><?= $a_data['tmplahir']?>,  <?= date('F jS, Y', strtotime($a_data['tgllahir']))?></em>
			</div>
		  </td>
		  <td>&nbsp;</td>
		  <td><div class="border"><?= $a_data['noijasah']?></div></td>
		</tr>
		<tr>
		  <td><strong>&nbsp;</strong>.</td>
		  <td><strong>Nomor Induk Mahasiswa </strong><br><em> Student Identification Number</em></td>
		  <td><b>&nbsp;</b></td>
		  <td><strong>Gelar Akademik </strong><br><em> Title of Academic  Degree</em></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td><div class="border"><?= $a_data['nim']?></div></td>
		  <td>&nbsp;</td>
		  <td>
			  <div class="border"><?= $a_data['gelar'].' ('.$a_data['gelarsingkat'].')'?> 
				<br><em><?= $a_data['gelaren']?></em></div>
		  </td>
		</tr>
		<tr>
		  <td><strong>&nbsp;</strong>.</td>
		  <td><strong>Tanggal masuk </strong><br><em> Date of Entry</em></td>
		  <td><b>&nbsp;</b></td>
		  <td><strong>&nbsp;</strong><br><em> &nbsp;</em></td>
		</tr>
		<tr>
		  <td>&nbsp;</td>
		  <td>
			  <div class="border"><?= CStr::formatDateInd($a_data['tglregistrasi'])?></div>
		  </td>
		  <td>&nbsp;</td>
		  <td>&nbsp;
			  <? /*<div class="border"><?= $a_data['STATUSLULUS']?></div>*/?>
		  </td>
		</tr>
	</table>
	<br>
	<div class="border">
		<table width="100%">
			<tr>
				<td width="30" style="vertical-align:top"><b>II.</b></td>
				<td>
					<div class="content-title"><strong>INFORMASI TENTANG IDENTITAS PENYELENGGARA PROGRAM</strong></div>
					<div class="content-subtitle en">Information <em>Identifying the Awarding  Institution</em></div>
			  </td>
			</tr>
		</table>
	</div>
	<table width="100%">
		<tr>
			<td width="5%"><b>&nbsp;</b></td>
			<td width="45%">
			  <strong>SK Pendirian Perguruan Tinggi</strong>
				<br><em> Awarding  Institution Lisence</em>
			</td>
			<td width="5%"><b>&nbsp;</b></td>
			<td width="45%">
			  <strong>Prasyaratan Penerimaan</strong>
				<br><em> Entry Requirements</em>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<div class="border">
					<?= $a_data['no_sk_dikti']?>, <?= $a_data['tgl_sk_dikti']?><br>
					<em><?= $a_data['no_sk_dikti']?>, <?= $a_data['tgl_sk_dikti']?></em>
				</div>
			</td>
			<td>&nbsp;</td>
			<td>
				<div class="border">
					<?= $a_data['syaratpenerimaan']?><br>
					<em><?= $a_data['syaratpenerimaanen']?></em>
				</div>
			</td>
		</tr>
		<tr>
			<td><b>&nbsp;</b></td>
			<td>
			  <strong>Nama Perguruan Tinggi</strong>
				<br><em> Awarding  Institution</em>
			</td>
			<td><b>&nbsp;</b></td>
			<td>
			  <strong>Bahasa Pengantar Kuliah</strong>
				<br><em> Language of Instruction</em>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<div class="border">
					Universitas Es Unggul<br>
					<em>Esa Unggul University</em>
				</div>
			</td>
			<td>&nbsp;</td>
			<td>
				<div class="border">
					<?=$a_data['bahasapengantar']?><br>
					<em><?=$a_data['bahasapengantaren']?></em>
				</div>
			</td>
		</tr>
		<tr>
			<td><b>&nbsp;</b></td>
			<td>
			  <strong>Program Studi</strong>
				<br><em> Major</em>
			</td>
			<td><b>&nbsp;</b></td>
			<td>
			  <strong>Sistem Penilaian</strong>
				<br><em> Grading System</em>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<div class="border">
					<?= $a_data['namaunit']?><br>
					<em><?= $a_data['namauniten']?></em>
				</div>
			</td>
			<td>&nbsp;</td>
			<td>
				<div class="border">
					Skala 1-4; <br><?= $a_data['skala']?><br>
					<em>Scale 1-4; <br><?= $a_data['skala']?></em>
				</div>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<div class="border">
					<?php  
						if ($a_data['mhstransfer'] == '0') {
							$mhstransfer = 'Lulusan SMA' ;
							$mhstransferen = 'Senior High School Graduated' ;
						} elseif ($a_data['mhstransfer'] == '1') {
							$mhstransfer = 'Lulusan SMK' ;
							$mhstransferen = 'Vocational High School Graduated' ;
						} else {
							$mhstransfer = 'Tranfer' ;
						}
					?>

					Status : <?= $mhstransfer?> <br>
					<em> Status : <?= $mhstransferen?> </em>
				</div>
			</td>
			<td>&nbsp;</td>
			<td>
				<strong>Lama Studi Reguler</strong>
				<br><em> Reguler Length of Study</em>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<div class="border">
					Program : <?= $a_data['namaunit']?><br>
					<em> Program : <?= $a_data['namauniten']?></em>
				</div>
			</td>
			<td>&nbsp;</td>
			<td>
				<div class="border">
					 <?= $a_data['lamastudi']?> <br>
					
				</div>
			</td>
		</tr>
		<tr>
			<td><b>&nbsp;</b></td>
			<td>
			  <strong>Jenis & Jenjang Pendidikan</strong>
				<br><em> Type & Bachelor Degree</em>
			</td>
			<td><b>&nbsp;</b></td>
			<td>
			  <strong>Jenis Dan Jenjang Pendidikan Lanjutan</strong>
				<br><em> Access to Further Study</em>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<div class="border">
					<?= $a_data['jenispendidikan']?> & <?= $a_data['jenjangpendidikan']?><br>
					<em><?= $a_data['jenispendidikanen']?> & <?= $a_data['jenjangpendidikanen']?></em>
				</div>
			</td>
			<td>&nbsp;</td>
			<td>
				<div class="border">
					<?= $a_data['jenjangpendidikanlanjut']?><br>
					<em><?= $a_data['jenjangpendidikanlanjuten']?></em>
				</div>
			</td>
		</tr>		
		<tr>
			<td><b>&nbsp;</b></td>
			<td>
			  <strong>Jenjang Kualifikasi Sesuai KKNI</strong>
				<br><em> Level of Qualification in the National Qualification Framework</em>
			</td>
			<td><b>&nbsp;</b></td>
			<td>
			  <strong>Status Profesi (Bila Ada)</strong>
				<br><em> Proffesional Status (If Applicable)</em>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td>
				<div class="border">
					<?= $a_data['jenjangkkni']?><br>
				</div>
			</td>
			<td>&nbsp;</td>
			<td>
				<div class="border">
					<?= (empty($a_data['statusprofesi']))?'-':$a_data['statusprofesi'] ?><br>
				</div>
			</td>
		</tr>		
	</table>
	<div style="page-break-after:always"></div>
	<br>
	
