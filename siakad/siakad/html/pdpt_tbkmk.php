<?php
	ob_start();

	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// pengecekan tipe session user
	$a_auth = Helper::checkRoleAuth($conng,false);

	// otorisasi user
	$c_readlist = $a_auth['canlist'];
	$c_add = $a_auth['cancreate'];
	$c_edit = $a_auth['canedit'];
	$c_delete = $a_auth['candelete'];
	$c_other = $a_auth['canother'];

	//include("function.inc.php");
	
	$filter = $_POST["filter"];
	//if(!empty($filter)) {
		$c_unit = $_POST["unit"];	
		$c_periode = substr($_POST["periode"],0,4);	
		$c_kurikulum = $_POST["thnkurikulum"];
	/*}else{
		$c_unit = $_SESS["unit"];	
		$c_periode = $_SESS["periode"];	
		$c_kurikulum = $_SESS["thnkurikulum"];
	}*/
	
	if($filter<>'all') {
			header("Content-Type: application/msexcel");
			header('Content-Disposition: attachment; filename="TBKMK.csv"');
	}
		
	$tableTitle = ".: Data Mata Kuliah :.";
	$columnCount = 19;
	
	
	$strSQL = "select * from tmst_matakuliah where 1=1 
			   and kode_kurikulum='$c_kurikulum' and semester_pelaporan='$c_periode'";		
	if($c_unit<>'0')
		$strSQL .= " and kode_program_studi='$c_unit'";
	$rs = $connp->Execute($strSQL);	
?>
<html>
<head>
<title>SIAKAD : Data Mata Kuliah</title>
<style>
.GridStyle 
{
	border:1px solid #666;
	border-collapse: collapse;
	font-size:11px;
}

.HeaderBG {
	color: #FFFFFF;
	font-weight: bold;
	background-color: #000;
}

.SubHeaderBG {
	color: #FFFFFF;
	font-weight: bold;
	background-color : #000;
}
</style>
</head>

<body onLoad="window.print()">
				<table width="720" border=1 cellpadding="4" cellspacing=0 class="GridStyle">
				  <tr height=20> 
				    <td class="HeaderBG" colspan=<?= $columnCount ?> align="center"> 
				      <?= $tableTitle ?>
				    </td>
				  </tr>
				  <tr bgcolor="#33CCFF" height=20>
						<td align="center" class="SubHeaderBG">Kode_mata_kuliah</td> 
						<td align="center" class="SubHeaderBG">Tahun_pelaporan</td>
						<td align="center" class="SubHeaderBG">Semester_pelaporan</td>
						<td align="center" class="SubHeaderBG">Kode_perguruan_tinggi</td>
						<td align="center" class="SubHeaderBG">Kode_program_studi</td>
						<td align="center" class="SubHeaderBG">Kode_jenjang_pendidikan</td>						
						<td align="center" class="SubHeaderBG">Nama_mata_kuliah</td> 
						<td align="center" class="SubHeaderBG">Semester</td>
						<td align="center" class="SubHeaderBG">Kelompok_mata_kuliah</td>
						<td align="center" class="SubHeaderBG">Sks_mata_kuliah</td>	
						<td align="center" class="SubHeaderBG">Sks_tatap_muka</td> 
						<td align="center" class="SubHeaderBG">Sks_praktikum</td>
						<td align="center" class="SubHeaderBG">Sks_praktek_lapangan</td>
						<td align="center" class="SubHeaderBG">Kode_kurikulum</td>
						<td align="center" class="SubHeaderBG">Sap</td>
						<td align="center" class="SubHeaderBG">Silabus</td>						
						<td align="center" class="SubHeaderBG">Bahan_ajar</td> 
						<td align="center" class="SubHeaderBG">Diktat</td>
						<td align="center" class="SubHeaderBG">Nidn</td>						
				  </tr>					
				  <?			
						while (!$rs->EOF) 
						{							
							$i++;
							
					//================================== START LOOP ========================================
				?>
				  <tr class="<?= $rowStyle ?>" valign="top"> 
					<td align="center"> 
				      <?= $rs->fields["kode_mata_kuliah"]; ?>
				     </td>
					 <td align="center"> 
				      <?= $rs->fields["tahun_pelaporan"]; ?>
				    </td>
				    <td align="center"> 
				      <?= $rs->fields["semester_pelaporan"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["kode_perguruan_tinggi"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["kode_program_studi"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["kode_jenjang_pendidikan"]; ?>
				    </td>
					<td align="left"> 
				      <?= $rs->fields["nama_mata_kuliah"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["semester"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["kelompok_mata_kuliah"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["sks_mata_kuliah"]; ?>
				    </td>	
					<td align="center"> 
				      <?= $rs->fields["sks_tatap_muka"]; ?>
				     </td>
					 <td align="center"> 
				      <?= $rs->fields["sks_praktikum"]; ?>
				    </td>
				    <td align="center"> 
				      <?= $rs->fields["sks_praktek_lapangan"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["kode_kurikulum"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["sap"]; ?>
				    </td>
					<td align="left"> 
				      <?= $rs->fields["silabus"]; ?>
				    </td>
					<td align="left"> 
				      <?= $rs->fields["bahan_ajar"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["diktat"]; ?>
				    </td>
					<td align="left"> 
				      <?= $rs->fields["nidn"]; ?>
				    </td>
				  </tr>	
				  <? 
					$rs->MoveNext();
				  } ?>				  
				  <tr valign=center> 
				    <td  class="HeaderBG" align="right" colspan="19">&nbsp;</td>
				  </tr>
				</table>
			
</body>
</html>

<? 
	if($filter=='all') {
		$file = 'TBKMK.csv';
		@unlink($file);
		file_put_contents($file,ob_get_contents());
		ob_clean();
	}else{
		ob_end_flush();
	}
?>