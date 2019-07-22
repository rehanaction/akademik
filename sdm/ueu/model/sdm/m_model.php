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
				return $row[$colkey];
			else {
				$pk = explode(',',$colkey);
				
				$key = array();
				foreach($pk as $t_pk) {
					$t_pk = trim($t_pk);
					if(empty($row['real_'.$t_pk]))
						$key[] = trim($row[$t_pk]);
					else
						$key[] = trim($row['real_'.$t_pk]);
				}
				
				return implode('|',$key);
			}
		}
		
		// key digabung dengan record
		function getRecordKey($key,$record,$colkey='') {
			$reckey = static::getKeyRecord($key,$colkey);
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
		
		function schema($schema='') {
			if(empty($schema))
				$schema = static::schema;
			
			return $schema.'.';
		}
		
		// sequence digabung dengan schema
		function sequence() {
			return static::schema.'.'.static::sequence;
		}
		
		//delete file
		function deleteFile($conn,$r_key,$p_dbtable,$kolom,$colkey){
			global $conf;
			
			$record = array();
			$record[$kolom] = 'null';
			
			list($err,$msg) = static::updateRecord($conn, $record, $r_key, true, $p_dbtable,$colkey);
			
			if(!$err) {
				$ok = true;
				$path = $conf['docuploads_dir'].$kolom.'/'.$r_key;
				if(file_exists($path))
					$ok = unlink($path);
				
				if(!$ok) {
					$err = true;
					$msg = 'Penghapusan file '.static::label.' gagal';
				}
				else
					$msg = 'Penghapusan file '.static::label.' berhasil';
			}
			
			return array($err,$msg);
		}
		
		// hapus data
		function delete($conn,$key,$table='',$colkey='',$schema='',$colfile='') {
			global $conf;
			
			if (empty($table))
				$table = static::table();
			else{
				//bila dari schema modul lain
				if(!empty($schema))
					$table = $schema.'.'.$table;
				else
					$table = static::schema.'.'.$table;
			}
				
			$err = Query::qDelete($conn,$table,static::getCondition($key,$colkey));
			
			//hapus file misal ada file yang diupload
			if(!$err and !empty($colfile)){
				if(strpos($key,'|') === false)
					$r_key = $key;
				else
					list(,$r_key) = explode('|',$key);
				
				$a_colfile = array();
				$a_colfile = explode(',',$colfile);
				foreach($a_colfile as $cfile){
					$path = $conf['docuploads_dir'].$cfile.'/'.$r_key;
					@unlink($path);
				}			
			}
			
			return static::deleteStatus($conn);
		}
		
		// delete detail
		function deleteDetail($conn,$key,$detail,$table='',$colfile='') {
			$info = static::getDetailInfo($detail);

			if (empty($table))
				$table = static::table($info['table']);
			else
				$table = static::schema.'.'.$table;				
			
			$err = Query::qDelete($conn,$table,static::getCondition($key,$info['key']));

			global $conf;
			
			//hapus file misal ada file yang diupload
			if(!$err and !empty($colfile)){
				if(strpos($key,'|') === false)
					$r_key = $key;
				else
					list(,$r_key) = explode('|',$key);
				
				$a_colfile = array();
				$a_colfile = explode(',',$colfile);
				foreach($a_colfile as $cfile){
					$path = $conf['docuploads_dir'].$cfile.'/'.$r_key;
					@unlink($path);
				}			
			}
			
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
		function insertRecord($conn,$record,$status=false,$table='',$schema='',$colkey='',$isseq=false,&$key='') {
			if (empty($table))
				$table = static::table();
			else{
				//bila dari schema modul lain
				if(!empty($schema))
					$table = $schema.$table;
				else
					$table = static::schema.'.'.$table;
			}
				
			$err = Query::recInsert($conn,$record,$table);
			
			if(!$err) {
				$seq = static::sequence;
				if(!empty($seq))
					$key = static::getLastValue($conn);
				else{
					if($isseq)
						$key = static::getLastValue($conn);
					else
						$key = static::getRecordKey($key,$record,$colkey);
				}
			}
			
			if($status)
				return static::insertStatus($conn);
			else
				return $err;
		}
		
		function insertInPlace($conn,$kolom,$post,$table='',&$key='',$colkey='',$isseq=false) {
			// untuk input
			require_once(Route::getUIPath('form'));
			
			list(,$record) = uForm::getInsertRecord($kolom,$_POST);
			
			return static::insertCRecord($conn,$kolom,$record,$key,$table,$colkey,$isseq);
		}
		
		function insertCRecord($conn,$kolom,$record,&$key,$table='',$colkey='',$isseq=false) {
			global $conf;

			// unset record
			$upload = array();
			if(!empty($kolom)) {
				foreach($kolom as $datakolom) {
					if($datakolom['readonly'] and (empty($record[$datakolom['kolom']]) or $record[$datakolom['kolom']] == 'null')) {
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
			
			if (empty($table))
				$table = static::table();
			else
				$table = static::schema.'.'.$table;
				
			$err = Query::recInsert($conn,$record,$table);
			if(!$err) {
				$seq = static::sequence;
				if(!empty($seq))
					$key = static::getLastValue($conn);
				else{
					if($isseq)
						$key = static::getLastValue($conn);
					else
						$key = static::getRecordKey($key,$record,$colkey);
				}
				
				if(!empty($upload)) {
					$ok = true;
					foreach($upload as $file) {
						$file['name'] = $conf['docuploads_dir'].$file['uptype'].'/'.$key;
						
						$ok = Route::uploadFile($file['tmp_name'],$file['name']);
						if(!$ok) break;
					}
					
					if(!$ok)
						return array(true,'Upload data '.$label.' gagal');
				}

			}
			
			return static::insertStatus($conn,$kolom);
		}
		
		// insert record detail
		function insertCRecordDetail($conn,$kolom,$record,$detail,$isseq=false) {
			global $conf;
			
			// unset record
			$upload = array();
			if(!empty($kolom)) {
				foreach($kolom as $datakolom) {
					if($datakolom['readonly'] and (empty($record[$datakolom['kolom']]) or $record[$datakolom['kolom']] == 'null')) {
						unset($record[$datakolom['kolom']]);
					}
					else if($datakolom['type'] == 'U') {
						$name = empty($datakolom['nameid']) ? $datakolom['kolom'] : $datakolom['nameid'];
						$file = $_FILES[$detail.'_'.$name];
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
			$info = static::getDetailInfo($detail);
			
			$err = Query::recInsert($conn,$record,static::table($info['table']));

			if(!$err) {
				$seq = static::sequence;
				if(!empty($seq))
					$key = static::getLastValue($conn);
				else{
					if($isseq)
						$key = static::getLastValue($conn);
					else
						$key = static::getRecordKey($key,$record,$colkey);
				}

				if(!empty($upload)) {
					$ok = true;
					foreach($upload as $file) {
						$file['name'] = $conf['docuploads_dir'].$file['uptype'].'/'.$key;
						
						$ok = Route::uploadFile($file['tmp_name'],$file['name']);
						if(!$ok) break;
					}
					
					if(!$ok)
						return array(true,'Upload data '.$label.' gagal');
				}
			}
			
			return static::insertStatus($conn,$kolom,$info['label'],$info['table']);
		}
		
		function insertStatus($conn,$kolom='',$label='',$table='') {
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
		function updateRecord($conn,$record,$key,$status=false,$table='',$colkey='',$schema='') {
			if (empty($table))
				$table = static::table();
			else{
				//bila dari schema modul lain
				if(!empty($schema))
					$table = $schema.$table;
				else
					$table = static::schema.'.'.$table;
			}
				
			$err = Query::recUpdate($conn,$record,$table,static::getCondition($key,$colkey));
		
			if($status)
				return static::updateStatus($conn);
			else
				return $err;
		}
		
		function updateInPlace($conn,$kolom,$post,$key,$table='',$colkey='') {
			// untuk input
			require_once(Route::getUIPath('form'));
			
			list(,$record) = uForm::getUpdateRecord($kolom,$_POST);
			
			return static::updateCRecord($conn,$kolom,$record,$key,$table,$colkey);
		}
		
		function updateCRecord($conn,$kolom,$record,&$key,$table='',$colkey='') {
			global $conf;

			// unset record
			$upload = array();
			if(!empty($kolom)) {
				foreach($kolom as $datakolom) {
					if($datakolom['readonly'] and (empty($record[$datakolom['kolom']]) or $record[$datakolom['kolom']] == 'null')) {
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
			
			if (empty($table))
				$table = static::table();
			else
				$table = static::schema.'.'.$table;
								
			$err = Query::recUpdate($conn,$record,$table,static::getCondition($key,$colkey));
			if(!$err){
				//bila update kode dan bukan identity
				if($record[$colkey] != $key)
					$key = static::getRecordKey($key,$record,$colkey);
					
				if (empty($colkey))
					$key = static::getRecordKey($key,$record,$colkey);					
				
				if(!empty($upload)) {
					$ok = true;
					foreach($upload as $file) {										
						$file['name'] = $conf['docuploads_dir'].$file['uptype'].'/'.$key;
						
						$ok = Route::uploadFile($file['tmp_name'],$file['name']);
						if(!$ok) break;
					}
					
					if(!$ok)
						return array(true,'Upload data '.$label.' gagal');
				}
			}
			
			return static::updateStatus($conn,$kolom);
		}
		
		function updateStatus($conn,$kolom='',$label='',$table='') {
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
		function saveRecord($conn,$record,$key,$status=false,$table='',$colkey='') {
			// cek
			if (empty($table))
				$table = static::table();
			else
				$table = static::schema.'.'.$table;
				
			$sql = "select 1 from ".static::table." where ".static::getCondition($key,$colkey);
			$cek = $conn->GetOne($sql);
			
			if(empty($cek))
				return static::insertRecord($conn,$record,$status);
			else
				return static::updateRecord($conn,$record,$key,$status);
		}
		
		function saveCRecord($conn,$kolom,$record,&$key,$table='',$colkey='') {
			if (empty($table))
				$table = static::table();
			else
				$table = static::schema.'.'.$table;
				
			// cek
			$sql = "select 1 from ".$table." where ".static::getCondition($key,$colkey);
			$cek = $conn->GetOne($sql);
			
			if(empty($cek))
				return static::insertCRecord($conn,$kolom,$record,$key);
			else
				return static::updateCRecord($conn,$kolom,$record,$key);
		}
		
		function saveStatus($conn,$kolom='',$label='',$table='') {
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
				$msg = 'Penyimpanan data '.$label.' gagal';
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
				$msg = 'Penyimpanan data '.$label.' berhasil';
			}
			
			return array($err,$msg);
		}
		
		// mendapatkan data terakhir sequence
		function getLastValue($conn) {
			//$sql = 'select last_value from '.static::sequence();
			//return $conn->GetOne($sql);
			//sql server and mysql
			return $conn->Insert_ID();
		}
		
		// mendapatkan daftar field
		function getFields($conn,$record='',$table='') {
			if (empty($table))
				$table = static::table();
			else
				$table = static::schema.'.'.$table;
				
			$sql = "select * from ".static::table();
			$rs = $conn->SelectLimit($sql,1);
			
			$data = array();
			foreach($rs->fields as $k => $v)
				$data[$k] = $k;
			
			return $data;
		}
		
		// mendapatkan kueri data
		function dataQuery($key,$table='',$colkey='') {
			if (empty($table))
				$table = static::table();
			else
				$table = static::schema.'.'.$table;
				
			$sql = "select * from ".$table." where ".static::getCondition($key,$colkey);
			
			return $sql;
		}
		
		// mengecek data
		function isDataExist($conn,$key,$table='',$colkey='') {
			
			if (empty($table))
				$table = static::table();
			else
				$table = static::schema.'.'.$table;
			
			$sql = "select 1 from ".$table." where ".static::getCondition($key,$colkey);
			$cek = $conn->GetOne($sql);
			
			if(empty($cek))
				return false;
			else
				return true;
		}
		
		// mendapatkan data
		function getData($conn,$key,$table='',$colkey='',$sql='') {
			if(!empty($key)) {
				if (empty($sql))
					$sql = static::dataQuery($key,$table,$colkey);
					
				$row = $conn->GetRow($sql);
				$row = static::setExtraRow($row);
				
				return $row;
			}
			else
				return array();
		}
		
		// mendapatkan data tambahan
		function setExtraRow($row) {
			return $row;
		}
		
		// mendapatkan data beserta input
		function getDataEdit($conn,$kolom,$key,$post='',$table='',$colkey='',$sql='',$refpelamar='') {
			// untuk input
			require_once(Route::getUIPath('form'));
			
			$row = static::getData($conn,$key,$table,$colkey,$sql);
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
				$t_data['value'] = uForm::getLabel($datakolom,$row[$field],$refpelamar);
				$t_data['notnull'] = $datakolom['notnull'];
				$t_data['hidden'] = $datakolom['type'] == 'H' ? true : false;
				
				if($datakolom['readonly'])
					$t_data['input'] = $t_data['value'];
				else
					$t_data['input'] = uForm::getInput($datakolom,$row[$field],$refpelamar);
				
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
			
			if(!empty($label))
				$data[$label] = $data;
			
			return $data;
		}
		
		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			return array();
		}
		
		// mendapatkan kueri list
		function listQuery($table='') {
			if (empty($table))
				$table = static::table();
			else
				$table = static::schema.'.'.$table;
				
			$sql = "select * from ".$table;
			
			return $sql;
		}
		
		// mendapatkan kueri list dengan filter dan sort
		function getListQuery(&$sort,$filter='',$sql='',$table='', $row='') {
			// kueri
			if(empty($sql))
				$sql = static::listQuery($table);
			
			// filter
			$lfilter = static::listCondition();
			if(is_array($filter)) {
				if(!empty($lfilter))
					array_unshift($filter,$lfilter);
				
				$filter = implode(' and ',$filter);
			}
			else
				$filter = $lfilter.((!empty($lfilter) and !empty($filter)) ? ' and ' : '').$filter;
			
			if(!empty($filter)){
				if (strpos($sql, 'where') !== false){
					$sql .= ' and '.$filter;
				}else
					$sql .= ' where '.$filter;
			}
			
			// sort untuk yang tidak ada $row
			if($row == -1){
				if(empty($sort))
					$sort = static::order;
				if(!empty($sort))
					$sql .= ' order by '.$sort;
			}
			
			return $sql;
		}
		
		// mendapatkan kondisi kueri list
		function listCondition() {
			return "";
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col) {
			return "";
		}
		
		// mendapatkan data list
		function getListData($conn,$kolom,&$sort,$filter='',$sql='',$table='') {
			$page = 1; // karena by reference
			
			return static::getPagerData($conn,$kolom,-1,$page,$sort,$filter,$sql,$table);
		}
		
		// mendapatkan data pager
		function getPagerData($conn,$kolom,$row,&$page,&$sort,$filter='',$sql='',$table='') {
			if(empty($sort))
				$sort = static::order;
				
			// mengambil data
			$sql = static::getListQuery($sort,$filter,$sql,$table,$row);	
						
			if($row > -1) {
				//untuk set lastpage
				$select = strstr($sql,'from');
				$rownum = $conn->GetOne("select count(*) ".$select);
				$lastpage = ceil($rownum/$row);
				$lastpage = $lastpage == '0' ? '1' : $lastpage;
				
				// jika halaman terakhir
				if($page == -1) {
					$page = ceil($rownum/$row);
				}
				
				$offset = $row*($page);
				$pagenumber = ($page-1)*($row)+1;
				
				$rs = mModel::selectLimit($conn,$sort,$sql,$pagenumber,$offset);
			}
			else {
				$rs = $conn->Execute($sql);
				$p_row = $row;
				$lastpage = '1';
				$row = $rs->RecordCount();
			}
				
			Page::setTheLastPage($lastpage);
			
			// untuk input
			require_once(Route::getUIPath('form'));
			
			$i = 0;
			$a_data = array();
			while($i < $row and $rowdata = $rs->FetchRow()) {
				if(!empty($kolom)) {
					foreach($kolom as $datakolom) {
						if(empty($datakolom['alias']))
							$field = CStr::getLastPart($datakolom['kolom']);
						else
							$field = $datakolom['alias'];
						
						$value = $rowdata[$field];
						
						if($datakolom['type'] == 'D' or !empty($datakolom['option']) or !empty($datakolom['format']) or $datakolom['type'] == 'N')
							$rowdata['real_'.$field] = $value;
						
						$rowdata[$field] = uForm::getLabel($datakolom,$value);
					}
				}
				
				$a_data[$i++] = $rowdata;
			}
			
			if(empty($rowdata) or $p_row == '-1')
				$t_lastpage = true;
			else
				$t_lastpage = false;
						
			Page::setLastPage($t_lastpage);
			
			return $a_data;
		}
		
		//manual offset sql server
		function selectLimit($conn, &$sort, $sql='', $row, $offset=0) {
			// sort
			if(empty($sort))
				$sort = static::order;
						
			$p_strsql = "select * from (";
			$p_strsql .= "select tbl.* , Row_Number() OVER (order by $sort ) as RowNum  from ($sql) as tbl ";
			$p_strsql .= ") SOD ";
			$p_strsql .= "where SOD.RowNum between $row and $offset " ; 
			
			return $conn->Execute($p_strsql);
		}
		
		
		// menemukan data, untuk autocomplete
		function find($conn,$str,$col='',$key='',$table='',$order='') {
			global $conf;
			
			$str = strtolower($str);
			if(empty($col))
				$col = static::key;
			if(empty($key))
				$key = static::key;
				
			if(empty($table))
				$table = static::table();
			if(empty($order))
				$order = static::order;
			
			$sql = "select $key, $col as label from ".$table."
					where lower(cast($col as varchar(1000))) like '%$str%' order by ".$order;
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
		
		function getFileDown($conn,$p_dbtable='',$schema='',$filedbname='', $where='') {
			$p_dbtable = $schema.'.'.$p_dbtable;
				
			$sql = "select ".$filedbname." from ".$p_dbtable.' where '.$where;
			$filename = $conn->GetOne($sql);
							
			return $filename;
		}
	}
?>
