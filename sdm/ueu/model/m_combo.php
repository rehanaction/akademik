<?php
	// model combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mCombo {
		function tipepegawai($conn) {
			$sql = "select idtipepeg, tipepeg from sdm.ms_tipepeg order by idtipepeg";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function jenispegawai($conn) {
			$sql = "select idjenispegawai, jenispegawai from sdm.ms_jenispeg order by idtipepeg desc,jenispegawai";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function hubungankerja($conn) {
			$sql = "select idhubkerja, hubkerja from sdm.ms_hubkerja order by hubkerja";
			
			return Query::arrQuery($conn,$sql);
		}		
		
		function statusaktif($conn) {
			$sql = "select idstatusaktif, namastatusaktif from sdm.lv_statusaktif order by namastatusaktif";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function unit($conn,$dot=true,$akademik=false) {
			$cek = Modul::getLeftRight();
			
			$sqladd = '';
			if ($akademik)
				$sqladd = " and isakademik='Y'";
			
			$sql = "select kodeunit, namaunit, level,idunit from sdm.ms_unit
					where infoleft >= '".$cek['LEFT']."' and inforight <= '".$cek['RIGHT']."' {$sqladd}
					order by infoleft";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($dot)
					$pref = '..';
				else
					$pref = '&nbsp;&nbsp;';
				
				$data[$row['kodeunit']] = str_repeat($pref,$row['level']).$row['namaunit'];
			}
						
			return $data;
		}
		
		function unitSave($conn,$dot=true,$akademik=false) {
			$cek = Modul::getLeftRight();
						
			$sqladd = '';
			if ($akademik)
				$sqladd = " and isakademik='Y'";
			
			$sql = "select kodeunit, namaunit, level,idunit from sdm.ms_unit
					where infoleft >= '".$cek['LEFT']."' and inforight <= '".$cek['RIGHT']."' {$sqladd}
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
		
		function unitAll($conn,$dot=true,$akademik=false) {
			$cek = Modul::getLeftRight();
						
			$sqladd = '';
			if ($akademik)
				$sqladd = " and isakademik='Y'";
			
			$sql = "select kodeunit, namaunit, level,idunit from sdm.ms_unit
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
		
		function propinsi($conn) {
			$sql = "select idpropinsi, namapropinsi from sdm.lv_propinsi order by namapropinsi";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function kabupaten($conn,$where='') {
			$sql = "select idkabupaten, namakabupaten from sdm.lv_kabupaten {$where} order by namakabupaten";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function kecamatan($conn,$where='') {
			$sql = "select idkecamatan, namakecamatan from sdm.lv_kecamatan {$where} order by namakecamatan";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function sanksi($conn) {
			$sql = "select jenissanksi, keterangan from sdm.lv_sanksi order by keterangan";
			
			return Query::arrQuery($conn,$sql);
		}
				
		function strukturalSave($conn,$dot=true) {			
			$sql = "select idjstruktural, jabatanstruktural, level from sdm.ms_struktural order by infoleft";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($dot)
					$pref = '..';
				else
					$pref = '&nbsp;&nbsp;';
				
				$data[$row['idjstruktural']] = str_repeat($pref,$row['level']).$row['jabatanstruktural'];
			}
			
			return $data;
		}
				
		function tahun($singkat=true,$min=1996) {
			$data = array();
			for($i=date('Y')+1;$i>=$min;$i--)
				$data[$i] = ($singkat ? $i : $i.' - '.($i+1));
			
			return $data;
		}
		
		function isAktif() {
			$data = array('Y' => 'Aktif', 'T' => 'Tidak Aktif');
			
			return $data;
		}
		
		function yaTidak() {
			$data = array('Y' => 'Ya', 'T' => 'Tidak');
			
			return $data;
		}
		
		function bidangSave($conn,$dot=true) {			
			$sql = "select kodebidang, namabidang, level from sdm.ms_bidang order by kodeurutan";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($dot)
					$pref = '..';
				else
					$pref = '&nbsp;&nbsp;';
				
				$data[$row['kodebidang']] = str_repeat($pref,$row['level']).$row['namabidang'];
			}
			
			return $data;
		}
		
		function unitTree($conn) {
			require_once(Route::getModelPath('mastkepegawaian'));
			
			return mMastKepegawaian::getArrayTree($conn);
		}
	}
?>
