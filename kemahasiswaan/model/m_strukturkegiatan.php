<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mStrukturKegiatan extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'ms_strukturkegiatan';
		const order = 'infoleft';
		const key = 'kodekegiatan';
		const label = 'Struktur Kegiatan';
		const sequence = 'ms_strukturkegiatan_kodekegiatan_seq';
		
		// hapus data
		function delete($conn,$key) {
			$conn->BeginTrans();
			
			// atur left right dan urutan
			$row = self::getData($conn,$key);
			
			$err = static::deleteLeaf($conn,$row['infoleft'],$row['kodeurutan']);
			if(!$err)
				$err = Query::qDelete($conn,static::table(),static::getCondition($key));
			
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
			
			return static::deleteStatus($conn);
		}
		
		// delete leaf ($left dari yang didelete)
		function deleteLeaf($conn,$left,$urutan) {
			$sql = "update ".static::table()." set infoleft = infoleft-2 where infoleft > '$left';
					update ".static::table()." set inforight = inforight-2 where inforight > '$left'";
			$ok = $conn->Execute($sql);
			/*
			if($ok) {
				if(substr($urutan,-2) == '00') {
					$sql = "update ".static::table()." set kodeurutan = (substring(kodeurutan,1,1)::int-1)::text||substring(kodeurutan,2,2)
							where kodeurutan > '$urutan'";
				}
				else {
					$sql = "update ".static::table()." set kodeurutan = substring(kodeurutan,1,1)||lpad(substring(kodeurutan,2,2)::int-1,2,'0')
							where substring(kodeurutan,1,1) = '".substr($urutan,0,1)."' and kodeurutan > '$urutan'";
				}
				$ok = $conn->Execute($sql);
			}
			*/
			
			return $conn->ErrorNo();
		}
		
		// insert record
		function insertCRecord($conn,$kolom,$record,&$key) {
			// unset record
			if(!empty($kolom))
				foreach($kolom as $datakolom)
					if($datakolom['readonly'])
						unset($record[$datakolom['kolom']]);
			
			$conn->BeginTrans();
			
			if($record['parentkodekegiatan'] != 'null' and !empty($record['parentkodekegiatan'])) {
				$a_parent = $conn->GetRow("select level, inforight from ".static::table()." where kodekegiatan = '".$record['parentkodekegiatan']."'");
				
			}
			else {
				$a_parent = $conn->GetRow("select -1 as level, coalesce(max(inforight),0)+1 as inforight from ".static::table());
				
			}
			
			$record['level'] = $a_parent['level'] + 1;
			$record['infoleft'] = $a_parent['inforight'];
			$record['inforight'] = $record['infoleft']+1;
						
			$err = static::insertLeaf($conn,$a_parent['inforight']);
			if(!$err) {
				$err = Query::recInsert($conn,$record,static::table());
				if(!$err) {
					$seq = static::sequence;
					if(empty($seq))
						$key = static::getRecordKey($key,$record);
					else
						$key = static::getLastValue($conn);
				}
			}
			
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
			
			return static::insertStatus($conn,$kolom);
		}
		
		// insert child leaf ($right dari parent)
		function insertLeaf($conn,$right) {
			$sql = "update ".static::table()." set infoleft = infoleft+2 where infoleft > '$right';
					update ".static::table()." set inforight = inforight+2 where inforight >= '$right'";
			$conn->Execute($sql);
			
			return $conn->ErrorNo();
		}
		
		function getArray($conn,$key){
			$sql = " select kodekegiatan, namakegiatan from ".static::table();
			
			if(!empty($key))
				$sql .= " where kodekegiatan = '$key' ";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getArrayLevel($conn,$level=''){
			$sql = " select kodekegiatan, namakegiatan from ".static::table();
			
			if(!empty($level))
				$sql .= " where level = '$level' ";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getByParent($conn,$kodekegiatan=''){
			$sql = " select kodekegiatan, namakegiatan from ".static::table();
			
			if(!empty($kodekegiatan))
				$sql .= " where parentkodekegiatan = '$kodekegiatan' ";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getParent($conn,$kodekegiatan=''){
			$sql = " select parentkodekegiatan from ".static::table();
			
			if(!empty($kodekegiatan))
				$sql .= " where kodekegiatan = '$kodekegiatan' ";
			
			return $conn->GetOne($sql);
		}
		
		function getIndukArray($conn){
			$sql = " select kodekegiatan, namakegiatan from ".static::table()." where level = 0 ";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getListCombo($conn,$parent=null) {
			$sql = "select kodekegiatan, namakegiatan, level, nokegiatan from ".static::table();
			if(!empty($parent))
				$sql .= " where kodekegiatan = ".Query::escape($parent);
			$sql .= " order by infoleft";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['kodekegiatan']] = str_repeat('&nbsp;&nbsp;',$row['level']).$row['nokegiatan'].'. '.$row['namakegiatan'];
			
			return $data;
		}
	}
?>
