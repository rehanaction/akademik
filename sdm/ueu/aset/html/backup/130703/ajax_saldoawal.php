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
        $a_input[] = array('kolom' => 'kodebarang', 'type' => 'H');
        $a_input[] = array('kolom' => 'idunit', 'type' => 'H');
        $a_input[] = array('kolom' => 'idlokasi', 'type' => 'H');
        $a_input[] = array('kolom' => 'idpegawai', 'type' => 'H');


		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$err = mSaldoAwal::updateRecord($conn,$record,$r_key);

        echo $err;
/*
	    $sql = "update aset.rekap_kodebarang set 
	            kodebarang = '{$_POST['kodebarang']}',
	            idunit = '{$_POST['idunit']}',
	            idlokasi = '{$_POST['idlokasi']}',
	            idpegawai = '{$_POST['idpegawai']}' 
            where id = '{$_POST['id']}'";
        $ok = $conn->Execute($sql);
*/
        //$ok = 1;
		//echo $ok;
	}
	else if($f == 'getRow'){
    	$id = $_REQUEST['id'];
	    $sql = "select r.*,b.namabarang as mnamabarang,u.kodeunit,u.namaunit,l.namalokasi,p.nip,p.namalengkap  
	        from aset.rekap_kodebarang r 
	        left join aset.ms_barang1 b on b.idbarang1 = r.kodebarang 
	        left join aset.ms_unit u on u.idunit = r.idunit 
	        left join aset.ms_lokasi l on l.idlokasi = r.idlokasi 
	        left join sdm.v_biodatapegawai p on p.idpegawai = r.idpegawai 
	        where id = '$id'";

    	$row = $conn->GetRow($sql);

?>
		<td><?= $row['id'] ?></td>
		<td><?= $row['kodeunit'].' - '.$row['namaunit'] ?></td>
		<td><?= $row['idlokasi'].' - '.$row['namalokasi'] ?></td>
		<td><?= $row['kodebarang'].' - '.$row['mnamabarang'] ?></td>
		<td><?= $row['nip'].' - '.$row['namalengkap'] ?></td>
		<td><?= $row['ruang'] ?></td>
		<td><?= $row['lantai'] ?></td>
		<td><?= $row['idgedung'] ?></td>
		<td><?= $row['namabarang'] ?></td>
		<td><?= $row['jumlah'] ?></td>
		<td><?= $row['petugas'] ?></td>
		<td><?= $row['doc'] ?></td>
		<td align="center">
			<img title="Tampilkan Detail" src="images/edit.png" onclick="goEdit(<?= $row['id'] ?>)" style="cursor:pointer">
		</td>
<?
	
	}
?>
