<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$koderuang = array();
    $a_auth = Modul::getFileAuth();
	//$conn->debug=true;
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	require_once(Route::getModelPath('kuliah'));

    $key=$_GET['key'];
    $param = explode('|',$key);
    //$conn->debug=true;
    $p_title = 'DAFTAR HADIR DOSEN DAN BERITA ACARA PERKULIAHAN';
    $p_tbwidth = "100%";
    $p_count = mKuliah::getJumlahMengajar($conn,'1013');
    $a_data = mKuliah::getKehadiran($conn,$param[1]."".$param[0]);
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=kehadirandosen.xls");
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
		.div_preheader { font-size: 10px; font-weight: bold }
		.div_header { font-size: 12px }
		.div_headertext { font-size: 9px; font-style: italic }
		
		.tb_head td, .div_head, .div_subhead { font-family: "Times New Roman" }
		.tb_head td, .div_head { font-size: 12px }
		.div_subhead { font-size: 13px; margin-bottom: 5px }
		.div_head { font-size: 14px; font-weight: bold; text-decoration: underline }
		
		.tb_data { border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-size: 10px; padding: 1px }
		.tb_data th { background-color: #CFC; font-family: Arial; font-weight: bold }
		.tb_data td { font-family: Arial; height: 50px; padding:5px }

		.tb_data2 { border-collapse: collapse }
		.tb_data2 th, .tb_data2 td { border: 0px solid black; font-size: 13px; padding: 1px }
		.tb_data2 th { background-color: #CFC; font-family: Arial; font-weight: bold }
		.tb_data2 td { font-family: Arial; height: 50px; padding:5px }
	</style>
</head>
<body>
<div align="center">
<div class="div_head">DAFTAR HADIR DOSEN DAN BERITA ACARA PERKULIAHAN</div>
<div class="div_subhead"><?= Akademik::getNamaPeriode($param[1]."".$param[0]) ?></div>
</div>
<?php //var_dump($a_data); ?>
<table class="tb_data" width="100%" align="center" id="repdata">
<thead>
			<tr>
				<th>Dosen</th>
				<th>Matakuliah</th>
                <th>SKS</th>
                <th>Hari</th>
                <th>Waktu</th>
                <th>Ruang</th>
                <th>Jurusan</th>
                <th colspan="16">Pertemuan</th>
			</tr>
		</thead>
<?php foreach($a_data as $key=>$row){ ?>

    <tr>
        <td><?=$row['namadosen']?></td>
        <td><?=$row['namamk']?></td>
            <td><?=$row['sks']?></td>
            <td><?=CStr::getNamaHari($row['nohari'])?></td>
            <td><?=CStr::formatJam($row['waktumulai'])."-".CStr::formatJam($row['waktuselesai'])?></td>
            <td><?=$row['koderuang']?></td>
            <td><?=$row['jurusan']?></td>
            <td><?=$row['pertemuan1']?></td>
            <td><?=$row['pertemuan2']?></td>
            <td><?=$row['pertemuan3']?></td>
            <td><?=$row['pertemuan4']?></td>
            <td><?=$row['pertemuan5']?></td>
            <td><?=$row['pertemuan6']?></td>
            <td><?=$row['pertemuan7']?></td>
            <td><?=$row['pertemuan8']?></td>
            <td><?=$row['pertemuan9']?></td>
            <td><?=$row['pertemuan10']?></td>
            <td><?=$row['pertemuan11']?></td>
            <td><?=$row['pertemuan12']?></td>
            <td><?=$row['pertemuan13']?></td>
            <td><?=$row['pertemuan14']?></td>
            <td><?=$row['pertemuan15']?></td>
            <td><?=$row['pertemuan16']?></td>
        
    </tr>

<?php } ?>
</table>
</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script>
$(document).ready(function() {
   var span = 1;
   var prevTD = "";
   var prevTDVal = "";
   $("#repdata tr td:first-child").each(function() { //for each first td in every tr
      var $this = $(this);
      if ($this.text() == prevTDVal) { // check value of previous td text
         span++;
         if (prevTD != "") {
            prevTD.attr("rowspan", span); // add attribute to previous td
            $this.remove(); // remove current td
         }
      } else {
         prevTD     = $this; // store current td 
         prevTDVal  = $this.text();
         span       = 1;
      }
   });
});
</script>

