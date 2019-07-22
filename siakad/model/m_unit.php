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
					left join sdm.ms_pegawai p on u.ketua=p.idpegawai::text";
			
			return $sql;
		}
		
		// mendapatkan list sesua
		function getListFakJur($conn) {
			$cek = Modul::getLeftRight();
			
			$sql = "select kodeunit, namaunit, level, kodeunitparent from ".static::table()."
					where infoleft >= '".$cek['LEFT']."' and inforight <= '".$cek['RIGHT']."'
					order by infoleft";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()) {
				$t_array = array();
				$t_array['namaunit'] = $row['namaunit'];
				
				if($row['level'] == 1) {
					$t_array['child'] = array();
					
					$a_data[$row['kodeunit']] = $t_array;
				}
				else if(isset($a_data[$row['kodeunitparent']]))
					$a_data[$row['kodeunitparent']]['child'][$row['kodeunit']] = $t_array;
			}
			
			return $a_data;
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
		
		// mendapatkan nip dan nama ketua
		function getKetua($conn,$kodeunit) {
			$sql = "select u.ketua as nip, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as nama
					from ".static::table()." u left join kepegawaian.ms_pegawai p on p.nip = u.ketua
					where u.kodeunit = '$kodeunit'";
			
			return $conn->GetRow($sql);
		}
		
		// mendapatkan array data
		function getArray($conn,$dot=true,$skippamu=false,$cekauth=true) {
			$cek = Modul::getLeftRight();
			
			$sql = "select kodeunit, namaunit, level from ".static::table()."
					where isakad=-1";
			if($cekauth)		
				$sql.=" and infoleft >= '".$cek['LEFT']."' and inforight <= '".$cek['RIGHT']."' ";
			if($skippamu)
				$sql.=" and ispamu=0";
			$sql.=" order by infoleft";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($dot)
					$pref = '--';
				else
					$pref = '&nbsp;&nbsp;';
				
				$data[$row['kodeunit']] = str_repeat($pref,$row['level']).$row['namaunit'];
			}
			
			return $data;
		}
		
		// mendapatkan array data (tree)
		function getArrayTree($conn,$skippamu=false) {
			$cek = Modul::getLeftRight();
			
			$sql = "select kodeunit, namaunit, kodeunitparent, infoleft, inforight from ".static::table()."
					where infoleft >= '".$cek['LEFT']."' and inforight <= '".$cek['RIGHT']."'  and isakad=-1";
			if($skippamu)
				$sql.=" and ispamu=0";
			$sql.=" order by inforight-infoleft, infoleft";
					
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
		
		// informasi prodi
		function listQueryProdi() {
			$sql = "select * from ".self::schemaprodi.'.'.self::tableprodi;
			
			return $sql;
		}
		
		function dataQueryProdi($key) {
			$sql = "select * from ".self::schemaprodi.'.'.self::tableprodi." where ".static::getCondition($key);
			
			return $sql;
		}
		
		function deleteProdi($conn,$key) {
			Query::qDelete($conn,self::schemaprodi.'.'.self::tableprodi,static::getCondition($key));
			
			return static::deleteStatus($conn);
		}
		
		function insertCRecordProdi($conn,$kolom,$record,&$key) {
			global $conf;
			
			// unset record
			if(!empty($kolom)) {
				foreach($kolom as $datakolom) {
					if($datakolom['readonly']) {
						unset($record[$datakolom['kolom']]);
					} 
				}
			}
			
			$err = Query::recInsert($conn,$record,static::schemaprodi.'.'.static::tableprodi);
			if(!$err)
				$key = static::getRecordKey($key,$record);
			
			return static::insertStatus($conn,$kolom);
		}
		
		function updateCRecordProdi($conn,$kolom,$record,&$key) {
			global $conf;
			
			// unset record
			if(!empty($kolom)) {
				foreach($kolom as $datakolom) {
					if($datakolom['readonly']) {
						unset($record[$datakolom['kolom']]);
					}
				}
			}
			
			$err = Query::recUpdate($conn,$record,static::schemaprodi.'.'.static::tableprodi,static::getCondition($key));
			if(!$err)
				$key = static::getRecordKey($key,$record);
			
			return static::updateStatus($conn,$kolom);
		}
		
		// pengecekan hak akses
		function passUnit($conn,$kodeunit) {
			$cek = Modul::getLeftRight();
			
			$sql = "select infoleft, inforight from gate.ms_unit
					where kodeunit = '$kodeunit'";
			$row = $conn->GetRow($sql);
			
			if($row['infoleft'] >= $cek['LEFT'] and $row['inforight'] <= $cek['RIGHT'])
				return $kodeunit;
			else
				return null;
		}
		
		// mendapatkan array fakultas
		function fakultas($conn) {
			$cek = Modul::getLeftRight();
			
			$sql = "select kodeunit, namaunit from ".static::table()."
					where level = 1 and isakad=-1 and (
					infoleft < '".$cek['LEFT']."' and inforight > '".$cek['RIGHT']."' or
					infoleft >= '".$cek['LEFT']."' and inforight <= '".$cek['RIGHT']."')
					order by infoleft";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// mendapatkan array jurusan
		function jurusan($conn,$fakultas='',$skippamu=false) {
			$cek = Modul::getLeftRight();
			
			$sql = "select kodeunit, namaunit from ".static::table()."
					where level = 2 and isakad=-1 and infoleft >= '".$cek['LEFT']."' and inforight <= '".$cek['RIGHT']."'";
			if(!empty($fakultas))
				$sql .= " and kodeunitparent = '$fakultas'";
			if($skippamu)
				$sql.=" and ispamu=0";
			$sql .= " order by infoleft";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// nama level
		function namaLevel() {
			$data = array('0'=>'Universitas', '1' => 'Fakultas', '2' => 'Prodi/Jurusan','4'=>'Kepala Bagian');
			
			return $data;
		}
		function updateKodenim($conn,$a_input,$record,$r_key){
			$cek=$conn->GetOne("select 1 from gate.ms_unit where kodeunit='$r_key' and isakad='-1'");
			if($cek>0){
				return static::updateCRecord($conn,$a_input,$record,$r_key);
			}else
				return false;
		}
		function getFakJur($conn,$a_kolom,$r_sort,$a_filter) {
			$sql = "select level,kodeunit, namaunit,iskrs,ispenilaian,tglmulainilai,tglakhirnilai from gate.ms_unit where level >= 1 and isakad=-1";
			$data=static::getListData($conn,$a_kolom,$r_sort,$a_filter,$sql);
			
			return $data;
		}
		// menemukan data dosen, untuk autocomplete
		function findJurusan($conn,$str,$col='',$key='') {
			global $conf;
			
			$str = strtolower($str);
			if(empty($col))
				$col = static::key;
			if(empty($key))
				$key = static::key;
			
			$sql = "select $key, $col as label from ".static::table()."
					where level=2 and lower($col::varchar) like '%$str%' order by ".static::order;
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($key == static::key)
					$t_key = static::getKeyRow($row);
				else
					$t_key = $row[$key];
				
				$data[] = array('key' => $t_key, 'label' => $row['label']);
			}
			
			return $data;
		}
		function getLevelUnit($conn,$kodeunit){
		$data=$conn->GetOne("select level from ".static::table()." where kodeunit='$kodeunit'");
		return $data;
		}
		
		function getComboUnit($conn){
			$sql="select kodeunit,namaunit from ".static::table();
			
			return Query::arrQuery($conn,$sql);
		}
		function InquiryUnit($conn){
			$sql = "select * from ".static::table()." order by level asc";
			return $conn->GetArray($sql);
		}
		
	}
?>
