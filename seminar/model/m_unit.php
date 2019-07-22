<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mUnit extends mModel {
		const schema = 'gate';
		const table = 'ms_unit';
		const order = 'kodeunit';
		const key = 'kodeunit';
		const label = 'Unit';
		
		const schemaprodi = 'akademik';
		const tableprodi = 'ak_prodi';
		const labelprodi = 'Informasi Prodi';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select u.*, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as nama, up.namaunit as namaparent from ".static::table()." u
					left join ".static::table()." up on u.kodeunitparent = up.kodeunit
					left join sdm.ms_pegawai p on u.ketua = p.nik";
			
			return $sql;
		}


		
		
		
		// mendapatkan nama unit
		function getNamaUnit($conn,$kodeunit) {
			$sql = "select namaunit from ".static::table()." where kodeunit = '$kodeunit'";
			
			return $conn->GetOne($sql);
		}
		
		// mendapatkan nama parent unit
		function getNamaParentUnit($conn,$kodeunit) {
			$sql = "select p.namaunit from ".static::table()." u
					left join ".static::table()." p on p.kodeunit = u.kodeunitparent
					where u.kodeunit = '$kodeunit'";
			
			return $conn->GetOne($sql);
		}
		
		// mendapatkan program pendidikan
		function getProgramPendidikan($conn,$kodeunit) {
			$sql = "select coalesce(kode_jenjang_studi,'S1') from akademik.ak_prodi
					where kodeunit = '$kodeunit'";
			
			return $conn->GetOne($sql);
		}
		
		
		
		// mendapatkan array data (tree)
		function getArrayTree($conn) {
			$cek = Modul::getLeftRight();
			
			$sql = "select kodeunit, namaunit, kodeunitparent, infoleft, inforight from ".static::table()."
					where infoleft >= '".$cek['LEFT']."' and inforight <= '".$cek['RIGHT']."'  and isakad=-1
					order by inforight-infoleft, infoleft";
					
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if(empty($data[$row['kodeunit']]))
					$data[$row['kodeunit']] = $row['namaunit'];
				else
					$data[$row['kodeunit']]['label'] = $row['namaunit'];
				
				$t_kodeparent = strval($row['kodeunitparent']);
				if(strcmp($t_kodeparent,'') != 0) {
					$data[$t_kodeparent]['data'][$row['kodeunit']] = $data[$row['kodeunit']];
					unset($data[$row['kodeunit']]);
				}
				else
					$data[$row['kodeunit']]['label'] = $row['namaunit'];
			}
			
			return $data;
		}
		function listJurusan($conn,$kodeunit=''){
			$sql = "select kodeunit, namaunit from gate.ms_unit where level = 2 and isakad=-1";
			if(!empty($kodeunit)){
				require_once(Route::getModelPath('unit'));
				$unit=mUnit::getData($conn,$kodeunit);
				$left=$unit['infoleft'];
				$right=$unit['inforight'];
				$sql .= " AND (infoleft >= ".(int)$left." and inforight <= ".(int)$right.")";
			}
			$sql .= " order by infoleft";
			
			return Query::arrQuery($conn,$sql);
		}

		function listUniv() {
			$sql = "select u.*, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as nama, up.namaunit as namaparent from ".static::table()." u
					left join ".static::table()." up on u.kodeunitparent = up.kodeunit
					left join sdm.ms_pegawai p on u.ketua = p.nik
					where u.kodeunit='20000000'";
			
			return $sql;
		}
	}
?>
