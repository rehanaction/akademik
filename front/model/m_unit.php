<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mUnit extends mModel {
		const schema = 'gate';
		const table = 'ms_unit';
		const order = 'infoleft';
		const key = 'kodeunit';
		const label = 'unit';
		
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
			
			if($record['parentunit'] != 'null') {
				$a_parent = $conn->GetRow("select level, inforight, kodeurutan from ".static::table()." where kodeunit = '".$record['kodeunitparent']."'");
				$t_urutan = $conn->GetOne("select max(kodeurutan) from ".static::table()." where kodeunitparent = '".$record['kodeunitparent']."'");
				
				if($a_parent['level'] == 0)
					$t_urutan = str_pad((int)substr($t_urutan,0,1)+1,1,'0',STR_PAD_LEFT).'00';
				else
					$t_urutan = substr($a_parent['kodeurutan'],0,1).str_pad((int)substr($t_urutan,-2)+1,2,'0',STR_PAD_LEFT);
			}
			else {
				$a_parent = $conn->GetRow("select -1 as level, coalesce(max(inforight),0)+1 as inforight from ".static::table());
				$t_urutan = $conn->GetOne("select max(kodeurutan) from ".static::table());
				
				$t_urutan = str_pad((int)substr($t_urutan,0,1)+1,1,'0',STR_PAD_LEFT).'00';
			}
			
			$record['level'] = $a_parent['level'] + 1;
			$record['infoleft'] = $a_parent['inforight'];
			$record['inforight'] = $record['infoleft']+1;
			$record['kodeurutan'] = $t_urutan;
			
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
	}
?>