 

  <?= str_repeat('<br />', 8)?>
	<table width="100%">
		<tr>
		  <td><b>3.2.</b></td>
		  <td colspan="3"><em><strong>Prestasi/penghargaan</strong> </em><strong> / </strong><em>Award :</em></td>
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
				<?php
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
				?>
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
	<div style="page-break-after:always"></div>

