<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	$conn->debug = false;
	
	// require tambahan
	require_once(Route::getUIPath('combo'));
	
	// variabel reuqest
	$f = $_REQUEST['f'];
	$q = $_REQUEST['q'];
	
	// filtering
	if(is_array($q)) {
		for($i=0;$i<count($q);$i++)
			$q[$i] = CStr::removeSpecial($q[$i]);
	}
	else
		$q = CStr::removeSpecial($q);
	
	// option jurusan
	if($f == 'savedet') {
		require_once(Route::getModelPath('saldoawal'));
    	require_once(Route::getUIPath('form'));
  
      	$r_key = CStr::removeSpecial($_REQUEST['id']);

        //$conn->debug = true;
    	$a_input = array();
        $a_input[] = array('kolom' => 'idbarang', 'type' => 'H');
        $a_input[] = array('kolom' => 'idunit', 'type' => 'H');
        $a_input[] = array('kolom' => 'idlokasi', 'type' => 'H');
        $a_input[] = array('kolom' => 'idpegawai', 'type' => 'H');


		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$err = mSaldoAwal::updateRecord($conn,$record,$r_key);

        echo $err;

	}
	else if($f == 'getRow'){
    	$id = $_REQUEST['id'];
        $sql = "select s.*,b.idbarang+' - '+b.namabarang as barang,u.namaunit as unit,
            l.idlokasi as lokasi,p.namalengkap as pegawai 
            from aset.aa_saldoawal s 
            left join aset.ms_barang b on b.idbarang = s.idbarang 
            left join aset.ms_unit u on u.idunit = s.idunit 
            left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
            left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
	        where idsaldoawal = '$id'";

    	$row = $conn->GetRow($sql);

?>
		<td><?= $row['idsaldoawal'] ?></td>
		<td><?= $row['lokasi'] ?></td>
		<td><?= $row['unit'] ?></td>
		<td><?= $row['barang'] ?></td>
		<td><?= $row['pegawai'] ?></td>
		<td><?= $row['xnamabarang'] ?></td>
		<td><?= $row['jml'] ?></td>
		<td><?= $row['merk'] ?></td>
		<td><?= $row['spesifikasi'] ?></td>
		<td><?= $row['idkondisi'] ?></td>
		<td><?= $row['xnamapegawai'] ?></td>
		<td><?= $row['doc'].'/'.$row['sheet'] ?></td>
		<td align="center">
			<img title="Tampilkan Detail" src="images/edit.png" onclick="goEdit(<?= $row['idsaldoawal'] ?>)" style="cursor:pointer">
		</td>
<?
	
	}
?>
