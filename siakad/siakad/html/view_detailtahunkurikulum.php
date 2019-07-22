<?php // modal dialog 
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	require_once(Route::getModelPath('thnkurikulum'));
	$p_model = mThnkurikulum;
	
		// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'thnkurikulum', 'label' => 'Kurikulum', 'size' => 5, 'maxlength' => 5, 'notnull' => true);
	
	if (isset ($_POST)) $thn=$_POST['tahun'];
	$a_datadetail=$p_model::allData($conn,$thn);

	$p_aktivitas = 'KULIAH';
?>	
<div id="overlay"> 
 
	<center>
		<table border="0" cellspacing="10" class="nowidth">
			<tr>
				<td class="TDButton" onclick="goClose()"><img src="images/off.png"> Tutup</td>
			</tr>
		</table>
	</center>
	<div style="overflow:scroll; width:100%; height:80%; overflow-x:hidden;">
		
	<div id="wrapper" style="width:650px">
		<div class="SideItem" style="border:none; width:650px; margin:-10px 0 0 -20px">	
			 
					<header style="width:700px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1>Informasi Detail Tahun Kurikulum <?= $thn;?></h1>
							</div>
						</div>
					</header>
				 

				<table width="700" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
 
					?>

					<tr> 
						<th>Unit</th>
						<th>Nama Unit</th>
						<th>Total Mata kuliah Kurikulum</th>
						<th>MataKuliah Jurusan</th>
						<th>MataKuliah Wajib</th>
						<th>MataKuliah Pilihan</th>
						<th>MataKuliah Paket</th> 
						
						
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_datadetail as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle?>">
						<td><?= $row['kodeunit'];?></td>
						<td><?= $row['namaunit'];?></td> 
						<td><?= $row['mk_totalkurikulum']?></td>  
						<td><?= $row['mk_jurusan']?></td>
						<td><?= $row['mk_wajib']?></td>
						<td><?= $row['mk_pilihan']?></td>
						<td><?= $row['mk_paket']?></td>
						
					</tr>
					<?	}
						if($i == 1) {
					?>
					<tr>
						<td colspan="7" align="center">Data kosong</td>
					</tr>
					<?	}
					 
					 ?>
				</table> 
			</div>
		</div>
				
	</div>
</div>
<div id="fade"></div>