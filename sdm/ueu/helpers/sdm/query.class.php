<?php
	// fungsi pembantu query
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	class Query {
		// Koneksi database
		function connect($pref='') {
			global $conf;
			require_once($conf['includes_dir'].'adodb5/adodb.inc.php');
						
			if($pref == 'akad' or $pref == 'keu' or $pref == 'mutu' or $pref == 'portal'){
				$pref = $pref.'_';
				
				$conn = ADONewConnection($conf[$pref.'db_driver']);
				
				$strconn = 'host=' . $conf[$pref.'db_host'] . ' dbname='  . $conf[$pref.'db_dbname'] . ' user=' . $conf[$pref.'db_username'] . ' password=' . $conf[$pref.'db_password'];			
				if($conf[$pref.'db_port'] != '')
					$strconn .= ' port=' . $conf[$pref.'db_port'];
				
				$conn->Connect($strconn);
			}
			else{
				
				if(!empty($pref))
					$pref = $pref.'_';
			
				$conn = ADONewConnection($conf[$pref.'db_driver']);
				$strconn = 'host=' . $conf[$pref.'db_host'] . ' dbname='  . $conf[$pref.'db_dbname'] . ' user=' . $conf[$pref.'db_username'] . ' password=' . $conf[$pref.'db_password'];
				$strconn .= ' port=' . $conf[$pref.'db_port'];
				$conn->Connect($strconn);
				//print_r($strconn);
				//$conn->SetFetchMode(ADODB_FETCH_ASSOC);
				
			}
			
			//$conn->Connect($conf[$pref.'db_dsn']);
			$conn->SetFetchMode(ADODB_FETCH_ASSOC);
			
			return $conn;
		}
		
		// Insert dari array
		function recInsert($conn,$record,$table) {
			Query::setLogColumn($record);
			
			$col = $conn->SelectLimit("select * from $table",1);
			$sql = $conn->GetInsertSQL($col,$record);
			$conn->Execute($sql);
			
			return $conn->ErrorNo();
		}
		
		// Update dari array
		function recUpdate($conn,$record,$table,$condition,$force=false) {
			Query::setLogColumn($record);
			
			$col = $conn->Execute("select * from $table where $condition");
			$sql = $conn->GetUpdateSQL($col,$record,$force);
			if($sql != '')
				$conn->Execute($sql);
			
			return $conn->ErrorNo();
		}
				
		// Hapus secara umum
		function qDelete($conn,$table,$condition) {
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
			$record['t_username'] = Modul::getUserName();
			$record['t_updatetime'] = date('Y-m-d H:i:s');
			$record['t_ipaddress'] = $_SERVER['REMOTE_ADDR'];
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
			return "lower(cast(".$col." as varchar(1000))) like '%".strtolower($str)."%'";
		}
		
		// tambahkan kondisi di query
		function addCondition(&$query,$arrcond) {
			$query .= ' and ('.implode(' and ',$arrcond).')';
		}
		
		// tambahan log untuk query insert (t_updateuser,t_updatetime,t_updateip)
		function logInsert() {
			return "'".Modul::getUserName()."','".date('Y-m-d H:i:s')."','".$_SERVER['REMOTE_ADDR']."'";
		}
		
		// tambahkan log untuk query update
		function logUpdate() {
			return "t_username = '".Modul::getUserName()."', t_updatetime = '".date('Y-m-d H:i:s')."', t_ipaddress = '".$_SERVER['REMOTE_ADDR']."'";
		}
	}
?>
