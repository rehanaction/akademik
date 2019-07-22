<?php
	// model combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mCombo {
		function dosen($conn,$unit='') {
			if(!empty($unit)) {
				$info = $conn->GetRow("select infoleft, inforight from gate.ms_unit where kodeunit = '$unit'");
				$sql = "select p.nip, p.nama||' ('||p.nip||')' from akademik.ms_pegawai p join gate.ms_unit u on p.kodeunit = u.kodeunit and
						u.infoleft >= '".$info['infoleft']."' and u.inforight <= '".$info['inforight']."' order by p.nama";
			}
			else
				$sql = "select nip, nama||' ('||nip||')' from akademik.ms_pegawai order by nama";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function dosenKetua($conn,$unit='') {
			if(!empty($unit)) {
				$info = $conn->GetRow("select infoleft, inforight from gate.ms_unit where kodeunit = '$unit'");
				$sql = "select p.idpegawai, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen||' ('||p.idpegawai||')' 
				from sdm.ms_pegawai p join gate.ms_unit u on p.kodeunit = u.kodeunit and
						u.infoleft >= '".$info['infoleft']."' and u.inforight <= '".$info['inforight']."' order by p.namadepan";
			}
			else
				$sql = "select idpegawai, 
				akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) ||' ('||idpegawai||')' 
				from sdm.ms_pegawai p order by namadepan";
			
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
		function rolehusus($conn,$role='') {
			$sql = "select koderole, namarole from gate.sc_role";
			if($role=='DAA' or $role=='DAAN' OR $role=='DAAR' or $role=='PDAAN' or $role=='PDAAR')
				$sql.=" where koderole='M'";
			$sql.=" order by koderole";
			
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
		
		// apakah termasuk unit yang dimunculkan di akademik
		function unitAkad() {
			$data = array('-1' => 'Ya', '0' => 'Tidak');
			
			return $data;
		}
	}
?>
