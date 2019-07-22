<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request	
	$a_kolom = $_POST['kolom'];
	$a_urutan = $_POST['urutan'];
	$a_kriteria = $_POST['kriteria'];
	$a_paramkriteria = $_POST['paramkriteria'];	
	$judullaporan = $_POST['judullaporan'];	
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('pegawai'));
	
	// definisi variable halaman
	$p_window = 'Daftar Pegawai';
	$p_title = 'Laporan Daftar Pegawai';
	$p_file = 'daftar_pegawai';
	$p_model = mPegawai;
	$p_tbwidth = 800;
	
	//nama kolom		
	$kolom = $p_model::selectField();	
	list($nama_kolom,$nama_tabel) = $p_model::kolomLaporan($a_kolom,$kolom,$a_kriteria,$a_paramkriteria);
	$namakolom = $p_model::namaKolom();		
	$lebarkolom = $p_model::lebarKolom();	
	
	//nama kolom di terjemahkan
	$terjemah_kolom = $p_model::terjemahKolom();	
	$where = $p_model::terjemahKriteria($conn, $a_kriteria,$a_paramkriteria);	
	$terjemah_urutan = $p_model::terjemahUrutan();
	
	//urutan
	$urutan = $p_model::urutanJoin($a_urutan);	
	
	//cek join utk kolom yang banyak diselect, jadi hanya satu kali join			
	$cek = $p_model::varJoin();
	
	// header
	switch($r_format) {
		case 'doc';
			header("Content-Type: application/msword");
			header('Content-Disposition: attachment; filename="'.$p_file.'.doc"');
			break;
		case 'xls' :
			header("Content-Type: application/msexcel");
			header('Content-Disposition: attachment; filename="'.$p_file.'.xls"');
			break;
		default : header("Content-Type: text/html");
	}	
	
	// mendapatkan informasi pegawai 
	$a_data = $p_model::getLaporanPegawai($conn,$nama_tabel,$nama_kolom,$where,$urutan);
		
	$p_col = count($a_kolom)+1;
	
	$a_jeniskelamin = array("L" => "Laki-Laki", "P" => "Perempuan");
	$a_statusnikah = $p_model::statusNikah();
	$a_anak = $p_model::namaAnak($conn);
	$a_tgllahiranak = $p_model::tgllahirAnak($conn);

?>
<html>
<head>
	<title><?= $p_window?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<style>
		table { border-collapse:collapse }
		div,td,th {
			font-family:Verdana, Arial, Helvetica, sans-serif;
			font-size:12px;
		}
		td,th { border:1px solic black }
	</style>
</head>
<body>
	<div align="center">
		<? require_once($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $judullaporan ?></font></strong>
		<br><br>
		
		<table border="1" cellpadding="4" cellspacing="0">
			<tr bgcolor = "gray">
				<?
				echo '<th style = "color:#FFFFFF" align="center"><b>NO.</b></td>';
				for($i=0;$i<count($a_kolom);$i++){
				?>
					<th style = "color:#FFFFFF" width="<?= $lebarkolom[$a_kolom[$i]]; ?>" align="center"><b><?=$namakolom[$a_kolom[$i]]; ?></b></td>						
				<? }?>			
			</tr>
			<?	$n = 1;
			foreach($a_data as $row) { 
			?>
			<tr>
				<?
				echo "<td align='right'>"; echo $n++.'.'; echo "</td>";
				
				for($i=0;$i<count($a_kolom);$i++){	
					if(substr($a_kolom[$i],0,2) == 'mk' && $row[$terjemah_kolom[$a_kolom[$i]]] != ''){
						echo "<td>";		
						echo substr($row[$terjemah_kolom[$a_kolom[$i]]],0,2)." Tahun "; 
						echo substr($row[$terjemah_kolom[$a_kolom[$i]]],2,2). " Bulan";						
						echo "</td>";
					}
					else if(substr($a_kolom[$i],0,3) == 'tgl' or substr($a_kolom[$i],0,3) == 'tmt'){
						echo "<td align='center'>"; 
						echo CStr::formatDate($row[$terjemah_kolom[$a_kolom[$i]]]); 
						echo "</td>";
					}
					else if($a_kolom[$i] == 'gajitotal' or $a_kolom[$i] == 'ratemengajar' or $a_kolom[$i] == 'tunjanganprestasi' or $a_kolom[$i] == 'tunjanganhomebase' or $a_kolom[$i] == 'jamsostek'){
						echo "<td align='right'>"; 
						echo CStr::formatNumber($row[$terjemah_kolom[$a_kolom[$i]]]); 
						echo "</td>";
					}
					else if(substr($a_kolom[$i],0,8) == 'tmplahir'){
						echo "<td>"; 
						echo ucwords($row[$terjemah_kolom[$a_kolom[$i]]]); 
						echo "</td>";
					}
					else if(substr($a_kolom[$i],0,12) == 'jeniskelamin'){
						echo "<td>"; 
						echo $a_jeniskelamin[$row[$terjemah_kolom[$a_kolom[$i]]]]; 
						echo "</td>";
					}
					else if(substr($a_kolom[$i],0,11) == 'statuskawin'){
						echo "<td>"; 
						echo $a_statusnikah[$row[$terjemah_kolom[$a_kolom[$i]]]]; 
						echo "</td>";
					}
					else if(substr($a_kolom[$i],0,8) == 'namaanak'){
						echo "<td>"; 
						echo $a_anak[$row[$terjemah_kolom[$a_kolom[$i]]]]; 
						echo "</td>";
					}
					else if(substr($a_kolom[$i],0,16) == 'tanggallahiranak'){
						echo "<td>"; 
						echo $a_tgllahiranak[$row[$terjemah_kolom[$a_kolom[$i]]]]; 
						echo "</td>";
					}
					else if(substr($a_kolom[$i],0,20) == 'tanggallahirpasangan'){
						echo "<td>"; 
						echo CStr::formatDateInd($row[$terjemah_kolom[$a_kolom[$i]]],false); 
						echo "</td>";
					}
					else{		
						echo "<td>"; 
						echo $row[$terjemah_kolom[$a_kolom[$i]]]."&nbsp;"; 
						echo "</td>";
					}					
				}
				?>	
			</tr>
			<? 					
			} 
			if($n == 1) { ?>
			<tr>
				<td colspan="<?= $p_col ?>" height="30" align="center">Data tidak ditemukan</td>
			</tr>
		<?	} ?>
		</table>
		
		<? require_once($conf['view_dir'].'inc_footerrep.php'); ?>
		
</div>
</body>
</html>
<?	
// cetak ke pdf
if($r_format == 'pdf')
	Page::saveWkPDF($p_file.'.pdf');
?>
