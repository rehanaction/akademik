<?php
	// model combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mCombo {
		function dosen($conn,$unit='') {
			if(!empty($unit)) {
				$info = $conn->GetRow("select infoleft, inforight from gate.ms_unit where kodeunit = '$unit'");
				$sql = "select p.nip, p.namadepan + ' (' + p.nip + ')' from sdm.ms_pegawai p join gate.ms_unit u on p.kodeunit = u.kodeunit and
						u.infoleft >= '".$info['infoleft']."' and u.inforight <= '".$info['inforight']."' order by p.namadepan";
			}
			else
				$sql = "select nip, namadepan + ' (' + nip + ')' from sdm.ms_pegawai order by namadepan";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function modul($conn) {
			$sql = "select kodemodul, namamodul from gate.sc_modul order by kodemodul";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function role($conn) {
			$sql = "select koderole, namarole from gate.sc_role order by koderole";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function unit($conn,$dot=true) {
			$sql = "select kodeunit, namaunit, level from gate.ms_unit order by infoleft";
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
	}
?>