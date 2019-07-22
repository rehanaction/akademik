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
	/*}else{
		$c_unit = $_SESS["unit"];	
		$c_periode = $_SESS["periode"];	
	}*/
	
	if($filter<>'all') {
			header("Content-Type: application/msexcel");
			header('Content-Disposition: attachment; filename="MSMHS.csv"');	
	}
  
	$tableTitle = ".: Data Mahasiswa :.";
	$columnCount = 33;
	
	$strSQL = "select * from msmhs where 1=1 and tahun_masuk='$c_periode'";	
	if($c_unit<>'0')
		$strSQL .= " and kode_program_studi='$c_unit'";
	$rs = $connp->Execute($strSQL);	
?>
<html>
<head>
<title>SIAKAD : Data Mahasiswa</title>
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
						<td align="center" class="SubHeaderBG">Kode_perguruan_tinggi</td> 
						<td align="center" class="SubHeaderBG">Kode_program_studi</td>
						<td align="center" class="SubHeaderBG">NIM</td>
						<td align="center" class="SubHeaderBG">Nama_mahasiswa</td>
						<td align="center" class="SubHeaderBG">Tempat_lahir</td>
						<td align="center" class="SubHeaderBG">Kode_jenjang_pendidikan</td>						
						<td align="center" class="SubHeaderBG">Tanggal_lahir</td> 
						<td align="center" class="SubHeaderBG">Jenis_kelamin</td>
						<td align="center" class="SubHeaderBG">Status_mahasiswa</td>
						<td align="center" class="SubHeaderBG">Tahun_masuk</td>
						<td align="center" class="SubHeaderBG">Batas_studi</td>
						<td align="center" class="SubHeaderBG">Tanggal_masuk</td>						
						<td align="center" class="SubHeaderBG">Tanggal_lulus</td> 
						<td align="center" class="SubHeaderBG">Ipk_akhir</td>
						<td align="center" class="SubHeaderBG">Status_awal_mahasiswa</td>
						<td align="center" class="SubHeaderBG">Sks_diakui</td>
						<td align="center" class="SubHeaderBG">Kode_perguruan_tinggi_asal</td>
						<td align="center" class="SubHeaderBG">Kode_program_studi_asal</td>
						<td align="center" class="SubHeaderBG">Kode_jenjang_pendidikan_sblm</td>
						<td align="center" class="SubHeaderBG">Nim_asal</td>						
						<td align="center" class="SubHeaderBG">Kode_biaya_studi</td> 
						<td align="center" class="SubHeaderBG">Kode_pekerjaan</td>
						<td align="center" class="SubHeaderBG">Nama_tempat_kerja</td>
						<td align="center" class="SubHeaderBG">Kode_pt_bekerja</td>
						<td align="center" class="SubHeaderBG">Kode_ps_bekerja</td>
						<td align="center" class="SubHeaderBG">Nidn_promotor</td>						
						<td align="center" class="SubHeaderBG">Nidnkopromotor1</td> 
						<td align="center" class="SubHeaderBG">Nidnkopromotor2</td>
						<td align="center" class="SubHeaderBG">Nidnkopromotor3</td>
						<td align="center" class="SubHeaderBG">Nidnkopromotor4</td>
						<td align="center" class="SubHeaderBG">Judul_skripsi</td>
						<td align="center" class="SubHeaderBG">Bulan_awal_bimbingan</td>
						<td align="center" class="SubHeaderBG">Bulan_akhir_bimbingan</td>
				  </tr>					
				  <?			
						while (!$rs->EOF) 
						{							
							$i++;
							
					//================================== START LOOP ========================================
				?>
				  <tr class="<?= $rowStyle ?>" valign="top"> 
					<td align="center"> 
				      <?= $rs->fields["kode_perguruan_tinggi"]; ?>
				     </td>
					 <td align="center"> 
				      <?= $rs->fields["kode_program_studi"]; ?>
				    </td>
				    <td align="center"> 
				      <?= $rs->fields["nim"]; ?>
				    </td>
					<td align="left"> 
				      <?= $rs->fields["nama_mahasiswa"]; ?>
				    </td>
					<td align="left"> 
				      <?= $rs->fields["tempat_lahir"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["kode_jenjang_pendidikan"]; ?>
				    </td>
					<td align="left"> 
				      <?= $rs->fields["tanggal_lahir"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["jenis_kelamin"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["status_mahasiswa"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["tahun_masuk"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["batas_studi"]; ?>
				    </td>
					<td align="left"> 
				      <?= $rs->fields["tanggal_masuk"]; ?>
				    </td>
					<td align="left"> 
				      <?= $rs->fields["tanggal_lulus"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["ipk_akhir"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["status_awal_mahasiswa"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["sks_diakui"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["kode_perguruan_tinggi_asal"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["kode_program_studi_asal"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["kode_jenjang_pendidikan_sblm"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["nim_asal"]; ?>
				    </td>
					<td align="right"> 
				      <?= $rs->fields["kode_biaya_studi"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["kode_pekerjaan"]; ?>
				    </td>
					<td align="left"> 
				      <?= $rs->fields["nama_tempat_kerja"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["kode_pt_bekerja"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["kode_ps_bekerja"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["nidn_promotor"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["nidnkopromotor1"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["nidnkopromotor2"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["nidnkopromotor3"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["nidnkopromotor4"]; ?>
				    </td>
					<td align="left"> 
				      <?= $rs->fields["judul_skripsi"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["bulan_awal_bimbingan"]; ?>
				    </td>
					<td align="center"> 
				      <?= $rs->fields["bulan_akhir_bimbingan"]; ?>
				    </td>
				  </tr>	
				  <? 
					$rs->MoveNext();
				  } ?>				  
				  <tr valign=center> 
				    <td  class="HeaderBG" align="right" colspan="33">&nbsp;</td>
				  </tr>
				</table>
			
</body>
</html>

<? 
	if($filter=='all') {
		$file = 'MSMHS.csv';
		@unlink($file);
		file_put_contents($file,ob_get_contents());
		ob_clean();
	}else{
		ob_end_flush();
	}
?>