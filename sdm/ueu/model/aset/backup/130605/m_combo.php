<?php
	// model combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mCombo {
	    function aktif(){
	        return array('1' => 'Aktif', '0' => 'Non Aktif');
	    }
	    
		function cabang($conn) {
			$sql = "select idcabang, namacabang from aset.ms_cabang order by idcabang";
			return Query::arrQuery($conn,$sql);
		}
		
		function coa($conn,$isleaf=true) {
			$sql = "select idcoa, idcoa+' - '+namacoa as coa from aset.ms_coa ";
			if($isleaf)
			    $sql .= "where level = 5 ";
			$sql .= "order by idcoa";
			return Query::arrQuery($conn,$sql);
		}
		
		function gedung($conn) {
			$sql = "select idgedung, namagedung from aset.ms_gedung order by idgedung";
			return Query::arrQuery($conn,$sql);
		}
		
		function barang($conn){
			$sql = "select idbarang1, namabarang from aset.ms_barang1 where level > 3 and isaktif = 1";
			return Query::arrQuery($conn,$sql);
		}
		
		function levelbarang($all=true){
		    if($all) 
		        return array('1' => 'Level 1','2' => 'Level 2','3' => 'Level 3','4' => 'Level 4','5' => 'Level 5','6' => 'Level 6');
            else 
		        return array('1' => 'Level 1','2' => 'Level 2','3' => 'Level 3','4' => 'Level 4','5' => 'Level 5');
		}
		
		function levelcoa(){
	        return array('1' => 'Level 1','2' => 'Level 2','3' => 'Level 3','4' => 'Level 4','5' => 'Level 5');
        }
        
		function lokasi($conn){
			$sql = "select idlokasi, idlokasi+' - '+namalokasi from aset.ms_lokasi order by idlokasi";
			return Query::arrQuery($conn,$sql);
		}
		
		function jenislokasi($conn) {
			$sql = "select idjenislokasi, jenislokasi from aset.ms_jenislokasi order by idjenislokasi";
			return Query::arrQuery($conn,$sql);
		}
		
		function jenispenghapusan($conn) {
			$sql = "select idjenispenghapusan, jenispenghapusan from aset.ms_jenispenghapusan order by idjenispenghapusan";
			return Query::arrQuery($conn,$sql);
		}
		
		function jenispenyusutan($conn) {
			$sql = "select idjenispenyusutan, jenispenyusutan from aset.ms_jenispenyusutan order by idjenispenyusutan";
			return Query::arrQuery($conn,$sql);
		}
		
		function jenisperolehan($conn) {
			$sql = "select idjenisperolehan, jenisperolehan from aset.ms_jenisperolehan order by idjenisperolehan";
			return Query::arrQuery($conn,$sql);
		}
		
		function jenisperolehanhp($conn) {
			$sql = "select idjenistranshp, jenistranshp from aset.ms_jenistranshp where tok = 'T' order by idjenistranshp";
			return Query::arrQuery($conn,$sql);
		}
		
		function jenispengeluaranhp($conn) {
			$sql = "select idjenistranshp, jenistranshp from aset.ms_jenistranshp where tok = 'K' order by idjenistranshp";
			return Query::arrQuery($conn,$sql);
		}
		
		function jenisrawat($conn) {
			$sql = "select idjenisrawat, jenisrawat from aset.ms_jenisrawat order by idjenisrawat";
			return Query::arrQuery($conn,$sql);
		}
		
		function jenissupplier($conn) {
			$sql = "select idjenissupplier, jenissupplier from aset.ms_jenissupplier order by idjenissupplier";
			return Query::arrQuery($conn,$sql);
		}
		
		function kondisi($conn) {
			$sql = "select idkondisi, kondisi from aset.ms_kondisi order by idkondisi";
			return Query::arrQuery($conn,$sql);
		}
		
		function satuan($conn) {
			$sql = "select idsatuan, satuan from aset.ms_satuan order by satuan";
			return Query::arrQuery($conn,$sql);
		}
		
		function satuanTujuan($conn,$key) {
		    if(!empty($key)){
			    $sql = "select idsatuan, satuan 
			        from aset.ms_satuan 
			        where idsatuan not in (select idasal from aset.ms_konversi where idbarang1 = '$key')
			        order by idsatuan";
			    return Query::arrQuery($conn,$sql);
		    }else return array();
		}
		
		function status($conn) {
			$sql = "select idstatus, status from aset.ms_status order by idstatus";
			return Query::arrQuery($conn,$sql);
		}
		
				
		function statusopnamehp(){
		    return array('D' => 'Draft', 'S' => 'Sah');
		}
		
		function statusopname(){
		    return array('D' => 'Draft', 'S' => 'Sah');
		}
		
		function sumberdana($conn) {
			$sql = "select idsumberdana, sumberdana from aset.ms_sumberdana order by idsumberdana";
			return Query::arrQuery($conn,$sql);
		}
		
		function supplier($conn) {
			$sql = "select idsupplier, namasupplier from aset.ms_supplier order by idsupplier";
			return Query::arrQuery($conn,$sql);
		}
		
		function satuanperiode() {
			return array('Jam' => 'Jam', 'Hari' => 'Hari', 'Minggu' => 'Minggu', 'Bulan' => 'Bulan', 'Tahun' => 'Tahun');
		}
		
		// periode
		function tahun() {
			$data = array();
			for($i=date('Y')+1;$i>=date('Y')-10;$i--)
				$data[$i] = $i;
			
			return $data;
		}
		
		function bulan($full=true){
		    $bulan = array();
		    if($full){
			    $bulan['01'] = 'Januari';
			    $bulan['02'] = 'Pebruari';
			    $bulan['03'] = 'Maret';
			    $bulan['04'] = 'April';
			    $bulan['05'] = 'Mei';
			    $bulan['06'] = 'Juni';
			    $bulan['07'] = 'Juli';
			    $bulan['08'] = 'Agustus';
			    $bulan['09'] = 'September';
			    $bulan['10'] = 'Oktober';
			    $bulan['11'] = 'Nopember';
			    $bulan['12'] = 'Desember';
		    }else{
				$bulan['01'] = 'Jan';
				$bulan['02'] = 'Peb';
				$bulan['03'] = 'Mar';
				$bulan['04'] = 'Apr';
				$bulan['05'] = 'Mei';
				$bulan['06'] = 'Jun';
				$bulan['07'] = 'Jul';
				$bulan['08'] = 'Agu';
				$bulan['09'] = 'Sep';
				$bulan['10'] = 'Okt';
				$bulan['11'] = 'Nop';
				$bulan['12'] = 'Des';		    
		    }
			
			return $bulan;
		}
		
		function unitArray($conn) {
			$sql = "select idunit, kodeunit+' - '+namaunit from aset.ms_unit order by infoleft";
			return Query::arrQuery($conn,$sql);
		}
		
		function unit($conn,$dot=true) {
			$cek = Modul::getLeftRight();
			
			$sql = "select idunit, kodeunit, namaunit, level from aset.ms_unit
					where infoleft >= '".$cek['LEFT']."' and inforight <= '".$cek['RIGHT']."'
					order by infoleft";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($dot)
					$pref = '..';
				else
					$pref = '&nbsp;&nbsp;';
				
				$data[$row['idunit']] = str_repeat($pref,$row['level']).$row['kodeunit'].' - '.$row['namaunit'];
			}
			
			return $data;
		}
		
		function unitSave($conn,$dot=true) {
			$cek = Modul::getLeftRight();
			
			$sql = "select kodeunit, namaunit, level,idunit from aset.ms_unit
					where infoleft >= '".$cek['LEFT']."' and inforight <= '".$cek['RIGHT']."'
					order by infoleft";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($dot)
					$pref = '..';
				else
					$pref = '&nbsp;&nbsp;';
				
				$data[$row['idunit']] = str_repeat($pref,$row['level']).$row['namaunit'];
			}
			
			return $data;
		}
		
		function coaSave($conn,$dot=true) {
			$cek = Modul::getLeftRight();
			
			$sql = "select idcoa, namacoa, level from aset.ms_coa order by idcoa, level";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($dot)
					$pref = '..';
				else
					$pref = '&nbsp;&nbsp;';
				
				$data[$row['idcoa']] = str_repeat($pref,$row['level']).$row['namacoa'];
			}
			
			return $data;
		}
		
		function isverify(){
		    return array('0' => 'Belum Diverifikasi', '1' => 'Sudah Diverifikasi');
		}
	}
?>
