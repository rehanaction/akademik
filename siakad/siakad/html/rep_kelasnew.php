<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$koderuang = array();
	// hak akses
	$a_auth = Modul::getFileAuth();
	//$conn->debug=true;
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getModelPath('kuliah'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_basis = Modul::setRequest($_POST['sistemkuliah'],'SISTEMKULIAH');
	
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Jadwal Perkuliahan';
	$p_tbwidth = 950;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mkelas;
	
	
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	
	if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);

	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_basis)) $a_filter[] = $p_model::getListFilter('sistemkuliah',$r_basis);
	$a_data = $p_model::getReportKelas($conn,$a_kolom,$r_sort,$a_filter);
	$a_databaru = $p_model::getReportKelasNew($conn,$r_periode);
	
	$a_sistemkuliah = mCombo::sistemkuliah($conn);
	$a_unit = mUnit::getComboUnit($conn);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<style>
		.tab_header { border-bottom: 1px solid black; margin-bottom: 5px }
		.div_headeritem { float: left }
		.div_preheader, .div_header { font-family: "Times New Roman" }
		.div_preheader { font-size: 12px; font-weight: bold }
		.div_header { font-size: 12px }
		.div_headertext { font-size: 12px; font-style: italic }
		
		.tb_head td, .div_head, .div_subhead { font-family: "Times New Roman" }
		.tb_head { border-bottom: 1px solid black }
		.tb_head td { font-size: 12px }
		.tb_head .mark { font-size: 12px }
		.div_head { font-size: 16px; text-decoration: underline }
		.div_subhead { font-size: 14px; margin-bottom: 5px }
		.div_head, .div_subhead { font-weight: bold }
		
		.tb_data { border: 1px solid black; border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-family: "Times New Roman"; padding: 1px }
		.tb_data th { background-color: #CFC; font-size: 12px }
		.tb_data td { font-size: 10px }
		.tb_data .noborder th { border-left: none; border-right: none }
		
		.tb_subfoot, .tb_foot { font-family: "Times New Roman" }
		.tb_subfoot { font-size: 12px; border-top: 1px solid black }
		.tb_foot { font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot .mark { font-size: 11px; font-weight: normal }
		.tb_foot .pad { padding-left: 30px }
	</style>
</head>
<body>
<div align="center">
	<?php echo $p_title?><br>
	<?=Akademik::getNamaPeriode($r_periode)?>
	<br>
</div>
<div align="left">
	HARI : <select name="multi_search_filter" id="multi_search_filter" style="width:150px;" onchange="myFunction()">
			    <option value="senin">--SENIN--</option>
			    <option value="selasa">--SELASA--</option>
			    <option value="rabu">--RABU--</option>
			    <option value="kamis">--KAMIS--</option>
			    <option value="jumat">--JUMAT--</option>
			    <option value="sabtu">--SABTU </option>
			    <option value="minggu">--MINGGU--</option>
			</select>
</div>
<div align="center">
	<table class="tb_data" width="100%" align="center" id="repdata">

		<thead>
			<tr>
				<th colspan="2">Waktu</th>
				<th colspan="25">Ruangan</th>
			</tr>
		<tr>
			<th>Hari</th>
			<th>Jam</th>
			<th>A12</th>
			<th>A14</th>
			<th>A21</th>
			<th>A22</th>
			<th>A23</th>
			<th>B21</th>
			<th>B22</th>
			<th>B23</th>
			<th>B24</th>
			<th>B25</th>
			<th>C11</th>
			<th>C12</th>
			<th>C13</th>
			<th>C21</th>
			<th>C22</th>
			<th>C23</th>
			<!--<th>C24</th>
			<th>C25</th>
			<th>C26</th>
			<th>C31</th>
			<th>C32</th>-->
			<th>C34</th>
			<th>C35</th>
			<th>C36</th>
			<th>LABKOM</th>
		</tr>
		</thead>
	 <?php //print_r($a_databaru); ?>
	 <tbody>
	 		<?php 
	 		$i = 0;
	 		foreach ($a_databaru as $key => $row) { 
	 		$i++;
	 		?>
	 		<tr>
			<td><?=$row['hari']?></td>
			<td><?php
			 if($row['jam']=='09')
			 { 
			 	echo $row['jam'].":30-12:00"; 
			 }elseif($row['jam']=='15'){ 
			 	echo $row['jam'].":30-18:00";
			 }elseif($row['jam']=='07'){
			 	echo $row['jam'].":00-09:30";
			 }elseif($row['jam']=='13'){
			 	echo $row['jam'].":00-15:30";
			 }elseif($row['jam']=='18'){
			 	echo $row['jam'].":00-20:00";
			 }elseif($row['jam']=='20'){
			 	echo $row['jam'].":00-22:00";
			 }elseif($row['jam']=='16'){
			 	echo $row['jam'].":40-18:00";
			 }else{
			 	echo $row['jam'].":00";
			 }
			?></td>
			<td><?=$row['a12']?></td>
			<td><?=$row['a14']?></td>
			<td><?=$row['a21']?></td>
			<td><?=$row['a22']?></td>
			<td><?=$row['a23']?></td>
			<td><?=$row['b21']?></td>
			<td><?=$row['b22']?></td>
			<td><?=$row['b23']?></td>
			<td><?=$row['b24']?></td>
			<td><?=$row['b25']?></td>
			<td><?=$row['c11']?></td>
			<td><?=$row['c12']?></td>
			<td><?=$row['c13']?></td>
			<td><?=$row['c21']?></td>
			<td><?=$row['c22']?></td>
			<td><?=$row['c23']?></td>
<?php /*
			<td><?=$row['c24']?></td>
			<td><?=$row['c25']?></td>
			<td><?=$row['c26']?></td>
			<td><?=$row['c31']?></td>
			<td><?=$row['c32']?></td>
			*/ ?>
			<td><?=$row['c34']?></td>
			<td><?=$row['c35']?></td>
			<td><?=$row['c36']?></td>
			<td><?=$row['labkom']?></td>
			
		</tr>
<?	}
			if($i == 0) {
		?>
		<tr>
			<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
		</tr>
		<?	} ?>
		</tbody>
	 </table>
					
</div>
<script>
function myFunction() {
  // Declare variables 
  var input, filter, table, tr, td, i;
  input = document.getElementById("multi_search_filter");
  filter = input.value.toUpperCase();
  table = document.getElementById("repdata");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    } 
  }
}
</script>

</body>
</html>