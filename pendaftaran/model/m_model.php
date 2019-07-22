<?php
	// model induk
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mModel {
		const schema = '';
		const table = '';
		const sequence = '';
		const key = '';
		const order = '';
		const label = '';
		const uptype = '';
		
		// kondisi satu record
		function getCondition($key,$colkey='',$alias='') {
			if(empty($colkey))
				$colkey = static::key;
			
			if(strpos($colkey,',') === false)
				return (empty($alias) ? '' : $alias.'.').$colkey." = '$key'";
			else {
				if(!is_array($key))
					$key = explode('|',$key);
				$pk = explode(',',$colkey);
				
				$cond = array();
				foreach($pk as $i => $t_pk)
					$cond[] = (empty($alias) ? '' : $alias.'.').$t_pk." = '".$key[$i]."'";
				
				return implode(' and ',$cond);
			}
		}
		
		// kondisi banyak record
		function getInCondition($key,$colkey='',$alias='') {
			if(empty($colkey))
				$colkey = static::key;
			
			if(strpos($colkey,',') === false)
				$pk = (empty($alias) ? '' : $alias.'.').$colkey;
			else {
				$pk = explode(',',$colkey);
				if(!empty($alias)) {
					foreach($pk as $i => $t_pk)
						$pk[$i] = $alias.'.'.$t_pk;
				}
				
				$pk = implode("||'|'||",$pk);
			}
			
			return $pk." in ('".implode("','",$key)."')";
		}
		
		// record dari key
		function getKeyRecord($key,$colkey='') {
			if(empty($colkey))
				$colkey = static::key;
			
			$record = array();
			if(strpos($colkey,',') === false)
				$record[$colkey] = $key;
			else {
				if(!is_array($key))
					$key = explode('|',$key);
				$pk = explode(',',$colkey);
				
				foreach($pk as $i => $t_pk)
					$record[$t_pk] = $key[$i];
			}
			
			return $record;
		}
		
		// mendapatkan field recordset key
		function getKeyRow($row,$colkey='') {
			if(empty($colkey))
				$colkey = static::key;
			
			if(strpos($colkey,',') === false)
				$pk = array($colkey);
			else
				$pk = explode(',',$colkey);
				
			$key = array();
			foreach($pk as $t_pk) {
				$t_pk = trim($t_pk);
				if(!empty($row['key_'.$t_pk]))
					$t_pk = 'key_'.$t_pk;
				else if(!empty($row['real_'.$t_pk]))
					$t_pk = 'real_'.$t_pk;
				
				$key[] = trim($row[$t_pk]);
			}
			
			return implode('|',$key);
		}
		
		// key digabung dengan record
		function getRecordKey($key,$record) {
			$reckey = static::getKeyRecord($key);
			foreach($reckey as $k => $v) {
				if(!empty($record[$k]))
					$reckey[$k] = $record[$k];
			}
			
			return implode('|',$reckey);
		}
		
		// table digabung dengan schema
		function table($table='') {
			if(empty($table))
				$table = static::table;
			
			return static::schema.'.'.$table;
		}
		
		// sequence digabung dengan schema
		function sequence() {
			return static::schema.'.'.static::sequence;
		}
		
		// hapus data
		function delete($conn,$key,$status=true) {
			Query::qDelete($conn,static::table(),static::getCondition($key));
			
			if($status)
				return static::deleteStatus($conn);
			else
				return $err;
		}
		
		// delete detail
		function deleteDetail($conn,$key,$detail) {
			$info = static::getDetailInfo($detail);
			
			Query::qDelete($conn,static::table($info['table']),static::getCondition($key,$info['key']));
			
			return static::deleteStatus($conn,$info['label']);
		}
		
		function deleteStatus($conn,$label='') {
			if(empty($label))
				$label = static::label;
			
			list($code) = Query::cekError($conn);
			
			if(!empty($code)) {
				$err = true;
				$msg = 'Penghapusan data '.$label.' gagal';
				if($code == 'REFERENCED')
					$msg .= ', data masih dijadikan referensi';
			}
			else {
				$err = false;
				$msg = 'Penghapusan data '.$label.' berhasil';
			}
			
			return array($err,$msg);
		}
		
		// insert record
		function insertRecord($conn,$record,$status=false) {
			$err = Query::recInsert($conn,$record,static::table());
			
			if($status)
				return static::insertStatus($conn);
			else
				return $err;
		}
		
		function insertInPlace($conn,$kolom,$post) {
			// untuk input
			require_once(Route::getUIPath('form'));
			
			list(,$record) = uForm::getInsertRecord($kolom,$_POST);
			
			return static::insertCRecord($conn,$kolom,$record,$kosong);
		}
		
		function insertCRecord($conn,$kolom,$record,&$key) {
			global $conf;
			
			// unset record
			$upload = array();
			if(!empty($kolom)) {
				foreach($kolom as $datakolom) {
					if($datakolom['readonly']) {
						unset($record[$datakolom['kolom']]);
					}
					else if($datakolom['type'][0] == 'U') {
						$name = empty($datakolom['nameid']) ? $datakolom['kolom'] : $datakolom['nameid'];
						$file = $_FILES[$name];
						
						if(!empty($file) and empty($file['error'])) {
							$record[$datakolom['kolom']] = CStr::cStrNull($file['name']);
							
							$file['uptype'] = $datakolom['uptype'];
							$upload[] = $file;
						}
						else
							unset($record[$datakolom['kolom']]);
					}
				}
			}
			
			$err = Query::recInsert($conn,$record,static::table());
			if(!$err) {
				$seq = static::sequence;
				if(empty($seq))
					$key = static::getRecordKey($key,$record);
				else
					$key = static::getLastValue($conn);
				
				if(!empty($upload)) {
					$ok = true;
					foreach($upload as $file) {
						$ok = Route::uploadFile($file['uptype'],$key,$file['tmp_name']);
						if(!$ok) break;
					}
					
					if(!$ok)
						return array(true,'Upload data '.$label.' gagal');
				}
			}
			
			return static::insertStatus($conn,$kolom);
		}
		
		// insert record detail
		function insertCRecordDetail($conn,$kolom,$record,$detail) {
			$info = static::getDetailInfo($detail);
			
			Query::recInsert($conn,$record,static::table($info['table']));
			
			return static::insertStatus($conn,$kolom,$info['label'],$info['table']);
		}
		
		function insertStatus($conn,$kolom='',$label='',$table='',$schema='') {
			if(empty($label))
				$label = static::label;
			if(empty($table))
				$table = static::table;
			if(empty($schema))
				$schema = static::schema;
			
			list($code,$info) = Query::cekError($conn);
			
			if(!empty($code)) {
				if(!empty($kolom))
					$kolom = Page::getColumnKey($kolom);
				
				$err = true;
				$msg = 'Penambahan data '.$label.' gagal';
				switch($code) {
					case 'DUPLICATE': $msg .= ', data sudah ada'; break;
					case 'NOT NULL': $msg .= ', <strong>'.$kolom[$info]['label'].'</strong> harus diisi'; break;
					case 'TOO LONG':
						$meta = Query::metaColumn($conn,$table,$schema);
						foreach($meta as $t_kolom => $t_meta) {
							if($t_meta['tipe'].':'.$t_meta['panjang'] == $info) {
								$t_msg = ', <strong>'.(empty($kolom) ? $t_kolom : $kolom[$t_kolom]['label']).'</strong> max '.$t_meta['panjang'].' digit';
								break;
							}
						}
						
						if(empty($t_msg))
							$msg .= ', isian terlalu panjang';
						else
							$msg .= $t_msg;
						break;
				}
			}
			else {
				$err = false;
				$msg = 'Penambahan data '.$label.' berhasil';
			}
			
			return array($err,$msg);
		}
		
		// update record
		function updateRecord($conn,$record,$key,$status=false) {
			$err = Query::recUpdate($conn,$record,static::table(),static::getCondition($key));
		
			if($status)
				return static::updateStatus($conn);
			else
				return $err;
		}
		
		function updateInPlace($conn,$kolom,$post,$key) {
			// untuk input
			require_once(Route::getUIPath('form'));
			
			list(,$record) = uForm::getUpdateRecord($kolom,$_POST);
			
			return static::updateCRecord($conn,$kolom,$record,$key);
		}
		
		function updateCRecord($conn,$kolom,$record,&$key) {
			global $conf;
			
			// unset record
			$upload = array();
			if(!empty($kolom)) {
				foreach($kolom as $datakolom) {
					if($datakolom['readonly']) {
						unset($record[$datakolom['kolom']]);
					}
					else if($datakolom['type'][0] == 'U') {
						$name = empty($datakolom['nameid']) ? $datakolom['kolom'] : $datakolom['nameid'];
						$file = $_FILES[$name];
						
						if(!empty($file) and empty($file['error'])) {
							$record[$datakolom['kolom']] = CStr::cStrNull($file['name']);
							
							$file['uptype'] = $datakolom['uptype'];
							$file['arrtype'] = $datakolom['arrtype'];
							$upload[] = $file;
						}
						else
							unset($record[$datakolom['kolom']]);
					}
				}
			}
			
			$err = Query::recUpdate($conn,$record,static::table(),static::getCondition($key));
			if(!$err) {
				$key = static::getRecordKey($key,$record);
				
				if(!empty($upload)) {
					$ok = true;
					foreach($upload as $file) {  
						// array('pdf','png'); 'pdf'; 'from'; 'to';
						$arr_tipe = $file['arrtype'];
						$tipe = $file['type'];
						$from = $file['tmp_name'];
						$to = $conf['upload_dir'].$file['uptype'].'/'.str_replace('|','_',$key);
						//$ok = Route::uploadFile($file['uptype'],$key,$file['tmp_name']);
						$ok = Route::uploadFile($arr_tipe,$tipe,$from,$to);
						if(!$ok) break;
					}
					
					if(!$ok)
						return array(true,'Upload data '.$label.' gagal');
				}
			}
			
			return static::updateStatus($conn,$kolom);
		}
		
		// update record detail
		function updateCRecordDetail($conn,$kolom,$record,$detail,$key) {
			$info = static::getDetailInfo($detail);
			
			Query::recUpdate($conn,$record,static::table($info['table']),static::getCondition($key,$info['key']));
			
			return static::updateStatus($conn,$kolom,$info['label'],$info['table']);
		}
		
		// hapus file
		function deleteFile($conn,$key,$kolom) {
			global $conf;
			
			$conn->BeginTrans();
			
			$record = array();
			$record[$kolom] = 'null';
			
			list($err,$msg) = static::updateRecord($conn,$record,$key,true);
			
			if(!$err) {
				$ok = Route::deleteUploadedFile(static::uptype,$key);
				
				if(!$ok) {
					$err = true;
					$msg = 'Penghapusan file '.static::label.' gagal';
				}
				else
					$msg = 'Penghapusan file '.static::label.' berhasil';
			}
			else
				$ok = false;
			
			$conn->CommitTrans($ok);
			
			return array($err,$msg);
		}
		
		function updateStatus($conn,$kolom='',$label='',$table='',$schema='') {
			if(empty($label))
				$label = static::label;
			if(empty($table))
				$table = static::table;
			if(empty($schema))
				$schema = static::schema;
			
			list($code,$info) = Query::cekError($conn);
			
			if(!empty($code)) {
				if(!empty($kolom))
					$kolom = Page::getColumnKey($kolom);
				
				$err = true;
				$msg = 'Pengubahan data '.$label.' gagal';
				switch($code) {
					case 'DUPLICATE': $msg .= ', data sudah ada'; break;
					case 'NOT NULL': $msg .= ', <strong>'.(empty($kolom) ? $t_kolom : $kolom[$t_kolom]['label']).'</strong> harus diisi'; break;
					case 'TOO LONG':
						$meta = Query::metaColumn($conn,$table,$schema);
						foreach($meta as $t_kolom => $t_meta) {
							if($t_meta['tipe'].':'.$t_meta['panjang'] == $info) {
								$t_msg = ', <strong>'.(empty($kolom) ? $t_kolom : $kolom[$t_kolom]['label']).'</strong> max '.$t_meta['panjang'].' digit';
								break;
							}
						}
						
						if(empty($t_msg))
							$msg .= ', isian terlalu panjang';
						else
							$msg .= $t_msg;
						break;
				}
			}
			else {
				$err = false;
				$msg = 'Pengubahan data '.$label.' berhasil';
			}
			
			return array($err,$msg);
		}
		
		// save record
		function saveRecord($conn,$record,$key,$status=false) {
			// cek
			$sql = "select 1 from ".static::table()." where ".static::getCondition($key);
			$cek = $conn->GetOne($sql);
			
			if(empty($cek))
				return static::insertRecord($conn,$record,$status);
			else
				return static::updateRecord($conn,$record,$key,$status);
		}
		
		function saveCRecord($conn,$kolom,$record,&$key) {
			// cek
			$sql = "select 1 from ".static::table." where ".static::getCondition($key);
			$cek = $conn->GetOne($sql);
			
			if(empty($cek))
				return static::insertCRecord($conn,$kolom,$record,$key);
			else
				return static::updateCRecord($conn,$kolom,$record,$key);
		}
		
		// mendapatkan data terakhir sequence
		function getLastValue($conn) {
			$sql = 'select last_value from '.static::sequence();
			
			return $conn->GetOne($sql);
		}
		
		// mendapatkan daftar field
		function getFields($conn,$record='') {
			$sql = "select * from ".static::table();
			$rs = $conn->SelectLimit($sql,1);
			
			$data = array();
			foreach($rs->fields as $k => $v)
				$data[$k] = $k;
			
			return $data;
		}
		
		// mendapatkan kueri data
		function dataQuery($key) {
			$sql = "select * from ".static::table()." where ".static::getCondition($key);
			
			return $sql;
		}
		
		// mengecek data
		function isDataExist($conn,$key) {
			$sql = "select 1 from ".static::table()." where ".static::getCondition($key);
			$cek = $conn->GetOne($sql);
			
			if(empty($cek))
				return false;
			else
				return true;
		}
		
		// mendapatkan data
		function getData($conn,$key,$sql='') {
			if(empty($sql)) {
				if(empty($key))
					return array();
				else
					$sql = static::dataQuery($key);
			}
			
			$row = $conn->GetRow($sql);
			$row = static::setExtraRow($row);
				
			return $row;
		}
		
		// mendapatkan data tambahan
		function setExtraRow($row) {
			return $row;
		}
		
		// mendapatkan data dari kolom
		function getDataView($conn,$kolom,$key) {
			// untuk input
			require_once(Route::getUIPath('form'));
			
			$row = static::getData($conn,$key);
			if(!empty($post)) {
				foreach($post as $k => $v)
					$row[$k] = $v;
			}
			
			$data = array();
			foreach($kolom as $datakolom) {
				$field = $datakolom['kolom'];
				
				$t_data = array();
				$t_data['id'] = empty($datakolom['nameid']) ? $datakolom['kolom'] : $datakolom['nameid'];
				$t_data['label'] = $datakolom['label'];
				$t_data['realvalue'] = $row[$field];
				$t_data['value'] = uForm::getLabel($datakolom,$row[$field]);
				
				$data[$field] = $t_data;
			}
			
			return $data;
		}
		
		// mendapatkan data beserta input
		function getDataEdit($conn,$kolom,$key,$post='',$sql='') {
			// untuk input
			require_once(Route::getUIPath('form'));
			
			$row = static::getData($conn,$key,$sql);
			if(!empty($post)) {
				foreach($post as $k => $v)
					$row[$k] = $v;
			}
			
			$data = array();
			foreach($kolom as $datakolom) {
				$field = $datakolom['kolom'];
				
				$t_data = array();
				$t_data['id'] = empty($datakolom['nameid']) ? $datakolom['kolom'] : $datakolom['nameid'];
				$t_data['label'] = $datakolom['label'];
				$t_data['text'] = $datakolom['text'];
				
				if(!empty($datakolom['text']) and !empty($row[$datakolom['text']]))
					$datakolom['textvalue'] = $row[$datakolom['text']];
					
				$t_data['realvalue'] = $row[$field];
				$t_data['value'] = uForm::getLabel($datakolom,$row[$field]);
				
				if($datakolom['readonly'])
					$t_data['input'] = $t_data['value'];
				else
					$t_data['input'] = uForm::getInput($datakolom,$row[$field]);
				
				if($datakolom['notnull'])
					$t_data['notnull'] = true;
				if($datakolom['skip'])
					$t_data['skip'] = true;
					
				if($datakolom['textvalue'])
					$t_data['textvalue'] = $datakolom['textvalue'];
				
				if($datakolom['type'] == 'X')
					$t_data['xauto'] = array('kolom' => $field, 'text' => $datakolom['text'], 'param' => $datakolom['param']);
				
				$data[] = $t_data;
			}
			return $data;
		}
		
		// mendapatkan data detail
		function getDetail($conn,$sql,$label='',$post='') {
			if(!empty($post)) {
				if(empty($label))
					$data = $post[$label];
				else
					$data = $post;
			}
			else
				$data = $conn->GetArray($sql);
			
			if(!empty($label)) {
				$tdata = $data;
				$data = array($label => $tdata);
			}
			
			return $data;
		}
		
		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			return array();
		}
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select * from ".static::table();
			
			return $sql;
		}
		
		// mendapatkan kueri list dengan filter dan sort
		function getListQuery(&$sort,$filter='',$sql='',$cond='') {
			// kueri
			if(empty($sql)) {
				$sql = static::listQuery();
				
				// filter
				if(empty($cond))
					$cond = static::listCondition();
			}
			
			if(is_array($filter)) {
				if(!empty($cond))
					array_unshift($filter,$cond);
				
				foreach($filter as $k => $v) {
					if(strlen($v) == 0)
						unset($filter[$k]);
				}
				
				$filter = implode(' and ',$filter);
			}
			else
				$filter = $cond.((!empty($cond) and !empty($filter)) ? ' and ' : '').$filter;
			
			if(!empty($filter))
				$sql .= ' where '.$filter;
			
			// sort
			if(empty($sort))
				$sort = static::order;
			if(!empty($sort))
				$sql .= ' order by '.$sort;
			
			return $sql;
		}
		
		// mendapatkan kondisi kueri list
		function listCondition() {
			return "";
		}
		
		// mendapatkan kolom filter list
		function getArrayListFilterCol() {
			return array();
		}
		
		function getListFilterCol($col) {
			$data = static::getArrayListFilterCol();
			
			if(empty($data[$col]))
				return $col;
			else
				return $data[$col];
		}
		
		// mendapatkan potongan kueri filter list
		function getArrayListFilter() {
			return array();
		}
		
		function getListFilter($col,$key) {
			$data = static::getArrayListFilter();
			
			if(empty($data[$col.':'.$key]))
				return static::getListFilterCol($col)." = '$key'";
			else
				return $data[$col.':'.$key];
		}
		
		// mendapatkan data list sederhana
		function getList($conn,$filter='') {
			// mengambil data
			if(!empty($filter))
				$sql = static::getListQuery($sort,$filter);
			else
				$sql = static::listQuery().' order by '.static::order;
			
			return $conn->GetArray($sql);
		}
		
		// mendapatkan data list
		function getListData($conn,$kolom,&$sort,$filter='',$sql='',$cond='') {
			$page = 1; // karena by reference
			
			return static::getPagerData($conn,$kolom,-1,$page,$sort,$filter,$sql,$cond);
		}
		
		// mendapatkan data pager
		function getPagerData($conn,$kolom,$row,&$page,&$sort,$filter='',$sql='',$cond='') {
			// mengambil data
			$sql = static::getListQuery($sort,$filter,$sql,$cond);
			
			$start = microtime(true);
			
			if($row > -1) {
				// jika halaman terakhir
				// if($page == -1) {
					$sqlc = "select count(*) from (".$sql.") a";
					$rownum = $conn->GetOne($sqlc);
				// }
				
				if($page == -1)	
					$page = ceil($rownum/$row);
				
				$offset = $row*($page-1);
				$rs = $conn->SelectLimit($sql,$row+1,$offset);
			}
			else {
				$rs = $conn->Execute($sql);
				$row = $rs->RecordCount();
				$rownum = $row;
			}
			
			// $end = microtime(true);
			
			// untuk input
			require_once(Route::getUIPath('form'));
			
			$i = 0;
			$a_data = array();
			while($rowdata = $rs->FetchRow() and $i < $row) {
				if(!empty($kolom)) {
					foreach($kolom as $datakolom) {
						if(empty($datakolom['alias']))
							$field = CStr::getLastPart($datakolom['kolom']);
						else
							$field = $datakolom['alias'];
						
						$value = $rowdata[$field];
						
						if($datakolom['type'] == 'D' or $datakolom['type'] == 'DT' or !empty($datakolom['option']) or !empty($datakolom['format'])) {
							$rowdata['real_'.$field] = $value;
							if($datakolom['type'] == 'DT')
								$rowdata['key_'.$field] = strtr($value,array('-'=>'',' '=>'',':'=>''));
						}
						
						$rowdata[$field] = uForm::getLabel($datakolom,$value);
					}
				}
				
				$a_data[$i++] = $rowdata;
			}
			
			if(empty($rowdata))
				$t_lastpage = true;
			else
				$t_lastpage = false;
			
			$end = microtime(true);
			$time = $end-$start;
			
			Page::setLastPage($t_lastpage);
			Page::setListTime($time);
			Page::setRowNum($rownum);
			
			return $a_data;
		}
		
		// array combo
		function arrQuery($conn) {
			$sql = "select ".static::key.", ".static::label." from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
		
		// menemukan data, untuk autocomplete
		function find($conn,$str,$col='',$key='') {
			global $conf;
			
			$str = strtolower($str);
			if(empty($col))
				$col = static::key;
			if(empty($key))
				$key = static::key;
			
			$sql = "select $key, $col as label from ".static::table()."
					where lower($col::varchar) like '%$str%' order by ".static::order;
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
		
		// mendapatkan data unit untuk dicek
		function rideUnit($conn,$key) {
			if(empty($key))
				return true;
			
			$sql = static::dataQuery($key);
			$row = $conn->GetRow($sql);
			
			$unitdata = $row['kodeunit'];
			if(empty($unitdata))
				return true;
			
			$cek = Modul::getLeftRight();
			
			$sql = "select infoleft, inforight from gate.ms_unit
					where kodeunit = '$unitdata'";
			$row = $conn->GetRow($sql);
			
			if($row['infoleft'] >= $cek['LEFT'] and $row['inforight'] <= $cek['RIGHT'])
				return true;
			else
				return false;
		}
		
		function switchUnit($conn,$unit) {
			$unitdef = Modul::getUnit();
			
			if(!isset($unit))
				return $unitdef;
			
			$cek = Modul::getLeftRight();
			
			$sql = "select 1 from gate.ms_unit where kodeunit = '$unit'
					and infoleft >= '".$cek['LEFT']."' and inforight <= '".$cek['RIGHT']."'";
			$cek = $conn->GetOne($sql);
			
			if(empty($cek))
				return $unitdef;
			else
				return $unit;
		}
	}
?>
