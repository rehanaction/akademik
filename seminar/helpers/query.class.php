<?php
	// fungsi pembantu query
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	class Query {
		// Koneksi database
		function connect($pref='') {
			global $conf;
			require_once($conf['includes_dir'].'adodb5/adodb.inc.php');
			
			if(!empty($pref))
				$pref = $pref.'_';
			
			$conn = ADONewConnection($conf[$pref.'db_driver']);
			$conn->Connect('host=' . $conf[$pref.'db_host'] . ' port=' . $conf[$pref.'db_port'] . ' dbname='  . $conf[$pref.'db_dbname'] . ' user=' . $conf[$pref.'db_username'] . ' password=' . $conf[$pref.'db_password']);
			$conn->SetFetchMode(ADODB_FETCH_ASSOC);
			
			return $conn;
		}
		
		function xssFilter(&$record) {
			if($record) {
				$unset = array();
				foreach($record as $col => $val) {
					if(substr($col,-5) !== ':skip') {
						if(empty($record[$col.':skip']))
							$record[$col] = htmlentities($val);
					}
					else
						$unset[] = $col;
				}
				foreach($unset as $col)
					unset($record[$col]);
			}
			
			return $record;
		}
		
		// Insert dari array
		function recInsert($conn,$record,$table,$key=null) {
			Query::xssFilter($record);
			Query::setLogColumn($record);
			Query::setAct($record,'i');
			
			$col = $conn->SelectLimit("select * from $table",1);
			$sql = $conn->GetInsertSQL($col,$record);
			
			if(!empty($key))
				$sql .= " returning $key";
			
			$rs = $conn->Execute($sql);
			$err = $conn->ErrorNo();
			
			if(!empty($key)) {
				if(empty($err))
					$row = $rs->FetchRow();
				
				return array($err,$row);
			}
			else
				return $err;
		}
		
		// Update dari array
		function recUpdate($conn,$record,$table,$condition,$force=false,$key=null) {
			Query::xssFilter($record);
			Query::setLogColumn($record);
			Query::setAct($record,'u');
			
			$col = $conn->Execute("select * from $table where $condition");
			$sql = $conn->GetUpdateSQL($col,$record,$force);
			
			if($sql != '') {
				if(!empty($key))
					$sql .= " returning $key";
				
				$rs = $conn->Execute($sql);
			}
			
			$err = $conn->ErrorNo();
			
			if(!empty($key)) {
				if(empty($err))
					$row = $rs->FetchRow();
				
				return array($err,$row);
			}
			else
				return $err;
		}
		
		// Simpan dari array
		function recSave($conn,$record,$table,$condition,$force=false,$key=null) {
			Query::xssFilter($record);
			Query::setLogColumn($record);
			Query::setAct($record,'s');
			
			$col = $conn->Execute("select * from $table where $condition");
			if(!$col->EOF)
				$sql = $conn->GetUpdateSQL($col,$record,$force);
			else
				$sql = $conn->GetInsertSQL($col,$record);
			
			if($sql != '') {
				if(!empty($key))
					$sql .= " returning $key";
				
				$rs = $conn->Execute($sql);
			}
			
			if(!empty($key)) {
				if(empty($err))
					$row = $rs->FetchRow();
				
				return array($err,$row);
			}
			else
				return $err;
		}
		
		// Hapus secara umum
		function qDelete($conn,$table,$condition,$updfirst=true) {
			if($updfirst) {
				global $i_page;
				
				// update log dulu, sebenarnya hanya untuk pencatatan log
				$record['t_updateact'] = substr('d-'.$i_page,0,30);
				
				Query::recUpdate($conn,$record,$table,$condition);
			}
			
			$sql = "delete from $table where $condition";
			$conn->Execute($sql);
			
			return $conn->ErrorNo();
		}
		
		// Membuat array dari hasil select
		function arrQuery($conn,$query,$emptyrow=false) {
			$rsc = $conn->Execute($query);
			
			if($emptyrow === false)
				$a_return = array();
			else
				$a_return = array('' => $emptyrow);
				
			while($rowc = $rsc->FetchRow()) {
				list(,$t_col) = each($rowc);
				if(count($rowc) > 1)
					list(,$t_val) = each($rowc);
				else
					$t_val = $t_col;
				
				$a_return[$t_col] = $t_val;
			}
			
			return $a_return;
		}
		
		// membentuk array option untuk combo dsb
		function setOption($option,$empty=false,$labelempty='') {
			global $conn;
			
			if(substr($option,0,6) == 'select')
				$sql = $option;
			else
				$sql = 'select * from '.$option;
			
			if($empty)
				$option = array('' => $labelempty);
			else
				$option = array();
			
			$option += Query::arrQuery($conn,$sql);
			ksort($option);
			
			return $option;
		}
		
		// menambahkan pengisian kolom log
		function setLogColumn(&$record) {
			$record['t_userid'] = Modul::getUserName();
			$record['t_updatetime'] = date('Y-m-d H:i:s');
			$record['t_ipaddress'] = $_SERVER['REMOTE_ADDR'];
		}
		
		// menambahkan pengisian kolom act
		function setAct(&$record,$act) {
			if(empty($record['t_act'])) {
				global $i_page;
				
				$act = substr($act.'-'.$i_page,0,30);
				$record['t_act'] = $act;
			}
		}
		
		// cek error database
		function cekError($conn) {
			$no = $conn->ErrorNo();
			$msg = $conn->ErrorMsg();
			
			$info = '';
			if($no == -5) {
				$code = 'DUPLICATE'; // duplikasi
			}
			else if(strpos($msg,'referenced')) {
				$code = 'REFERENCED'; // masih direferensi
			}
			else if(strpos($msg,'not present')) {
				$code = 'NOT PRESENT'; // parent belum ada
				$info = $msg;
			}
			else if(strpos($msg,'not-null')) {
				$code = 'NOT NULL';
				
				// mengambil nama kolom
				$strapp = strpos($msg,' "')+2;
				$endapp = strpos($msg,'" ');
				$info = substr($msg,$strapp,$endapp-$strapp);
			}
			else if(strpos($msg,'too long')) {
				$code = 'TOO LONG';
				
				// mengambil tipe data
				$strapp = strpos($msg,'type ')+5;
				list($type,$len) = explode('(',substr($msg,$strapp));
				$len = substr($len,0,strlen($len)-1);
				$info = $type.':'.$len;
			}
			else if(!empty($no)) {
				
				$code = 'ERROR';
				$info = $msg;
			}
			else {
				$code = false;
			}
			
			return array($code,$info);
		}
		
		// ubah ok
		function isErr($ok) {
			return ($ok ? false : true);
		}
		
		// ubah error
		function isOK($err) {
			return (empty($err) ? true : false);
		}
		
		function boolErr($err) {
			return (empty($err) ? false : true);
		}
		
		// mengambil properti kolom tabel
		function metaColumn($conn,$table,$schema='') {
			$sql = "select c.oid from pg_class c";
			if(!empty($schema))
				$sql .= " join pg_namespace n on n.oid = c.relnamespace and n.nspname = '$schema'";
			$sql .= " where c.relname = '$table'";
			$relid = $conn->GetOne($sql);
			
			if(empty($relid))
				return false;
			
			$sql = "select a.attname,a.atttypmod,t.typname from pg_attribute a
					join pg_type t on t.typelem = a.atttypid where a.attrelid = '$relid'
					and a.attnum > 0 order by a.attnum";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			if($rs) {
				while($row = $rs->FetchRow()) {
					$t_type = $row['typname'];
					$t_len = $row['atttypmod'];
					if($t_len == -1)
						$t_len = 0;
					else
						$t_len -= 4;
					
					$t_data = array();
					$t_data['tipemeta'] = $t_type;
					
					switch($t_type) {
						case '_bpchar': $t_ftype = 'character'; break;
						case '_varchar': $t_ftype = 'character varying'; break;
						default: $t_ftype = substr($t_type,1);
					}
					
					$t_data['tipe'] = $t_ftype;
					
					if($t_type == '_numeric') {
						$t_bin = decbin($t_len);
						$t_lbin = strlen($t_bin);
						$t_tlen = substr($t_bin,0,$t_lbin-16);
						$t_tprc = substr($t_bin,-16);
						
						$t_len = bindec($t_tlen).','.bindec($t_tprc);
					}
					
					$t_data['panjang'] = $t_len;
					
					$a_data[$row['attname']] = $t_data;
				}
				
				return $a_data;
			}
			else
				return false;
		}
		
		// mengambil kolom dari select
		function selectCols($query) {
			$spos = stripos($query,'select ')+7;
			$fpos = stripos($query,' from ');
			
			$cek = substr($query,$spos,$fpos-$spos);
			return explode(',',$cek);
		}
		
		// mengambil kolom dari kolom select
		function colSelect($col,$acek) {
			$idx = -1*strlen($col);
			
			$found = false;
			foreach($acek as $tcek) {
				$tcek = trim($tcek);
				if(substr($tcek,$idx) == $col or $tcek == '*') {
					if($tcek != '*')
						$col = $tcek;
					$found = true;
					break;
				}
			}
			
			if($found)
				return $col;
			else
				return false;
		}
		
		// potongan kueri filter
		function colFilter($col,$str) {
			return "lower((".$col.")::varchar) like '%".strtolower($str)."%'";
		}
		
		// tambahkan kondisi di query
		function addCondition(&$query,$arrcond) {
			$query .= ' and ('.implode(' and ',$arrcond).')';
		}
		
		// kondisi untuk join
		function onJoin($colsv,$alias1,$alias2) {
			$a_col = explode(',',$colsv);
			
			$a_on = array();
			foreach($a_col as $t_col)
				$a_on[] = $alias1.'.'.$t_col.' = '.$alias2.'.'.$t_col;
			
			return implode(' and ',$a_on);
		}
		
		// tambahan log untuk query insert (t_updateuser,t_updatetime,t_updateip)
		function logInsert() {
			return "'".Modul::getUserName()."','".date('Y-m-d H:i:s')."','".$_SERVER['REMOTE_ADDR']."'";
		}
		
		// tambahkan log untuk query update
		function logUpdate() {
			return "t_updateuser = '".Modul::getUserName()."', t_updatetime = '".date('Y-m-d H:i:s')."', t_updateip = '".$_SERVER['REMOTE_ADDR']."'";
		}
		
		// escape string
		function escape($str) {
			if(function_exists('pg_escape_literal'))
				return pg_escape_literal($str);
			else
				return "'".pg_escape_string($str)."'";
		}
	}
?>
