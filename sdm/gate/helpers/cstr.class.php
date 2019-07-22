<?php
	// fungsi format string
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	class CStr {
		// strip selain alfanumerik
		function cAlphaNum($item,$allow='') {
			return preg_replace('/[^a-zA-Z0-9'.$allow.']/', '$1', $item);
		}
		
		// strip selain numerik
		function cNum($item) {
			return (float)preg_replace('/[^0-9]/', '$1', $item);
		}
		
		// melakukan string stripping (untuk masalah sekuritas)
		function removeSpecial($mystr,$stripapp=true) {
			$mystr = trim($mystr);
			
			$pattern = '/[%&;\"';
			if($stripapp === false) // tidak stripping ', tapi direplace jadi '', biasanya dipakai di nama
				$mystr = str_replace("'","''",$mystr);
			else
				$pattern .= "\'";
			$pattern .= ']|--/';
			
			return preg_replace($pattern, '$1', $mystr);
		}
		
		// melakukan removespecial dengan mengecek array
		function removeSpecialAll($item) {
			if(is_array($item)) {
				foreach($item as $idx => $val)
					$item[$idx] = CStr::removeSpecial($val);
				return $item;
			}
			else
				return CStr::removeSpecial($item);
		}
		
		// bila $item kosong, set null, bila tidak, strip
		function cStrNull($item) {
			if($item == '')
				return 'null';
			else
				return CStr::removeSpecial($item);
		}
		
		// bila $item kosong, set null, tanpa petik, bila tidak, pakai petik
		function cStrNullS($item) {
			if($item == '')
				return 'null';
			else
				return "'".CStr::removeSpecial($item)."'";
		}
		
		// format menjadi bentuk bilangan
		function cStrDec($str,$ndec='',$convpoint=true) {
			if($str == '' or $str == 'null')
				return $str;
			
			if($convpoint)
				$str = str_replace('.','',$str);
			$str = str_replace(',','.',$str);
			
			if($ndec == '')
				return (float)$str;
			else
				return round($str,$ndec);
		}
		
		// untuk elemen array, lakukan cStrNull
		function cStrFill($src,$elem='',$idx=false) {
			if(is_array($elem)) {
				$retarr = array();
				for($i=0;$i<count($elem);$i++) {
					if(is_array($src[$elem[$i]])) {
						if($idx === false)
							$retarr[$elem[$i]] = CStr::cStrFill($src[$elem[$i]]);
						else
							$retarr[$elem[$i]] = CStr::cStrNull($src[$elem[$i]][$idx]);
					}
					else
						$retarr[$elem[$i]] = CStr::cStrNull($src[$elem[$i]]);
				}
			}
			else {
				$retarr = array();
				foreach($src as $key => $val) {
					if(is_array($val)) {
						if($idx === false) 
							$retarr[$key] = CStr::cStrFill($val);
						else 
							$retarr[$key] = CStr::cStrNull($val[$idx]);
					}
					else
						$retarr[$key] = CStr::cStrNull($val);
				}
			}
			
			return $retarr;
		}
		
		// lakukan cstrnull pada array, sesuai tipe data
		function cStrDB($conn,$table,&$record) {
			$meta = Query::metaColumn($conn,$table);
			
			foreach($record as $t_col => $t_val) {
				$t_meta = $meta[$t_col];
				if(empty($t_meta)) {
					unset($record[$t_col]);
					continue;
				}
				
				$t_type = $t_meta['tipe'];
				$t_val = CStr::cStrNull($t_val);
				
				if($t_type == 'numeric' or substr($t_type,0,3) == 'int') {
					list($len,$prc) = explode(',',$t_meta['panjang']);
					$t_val = CStr::cStrDec($t_val,$prc);
				}
				else if($t_type == 'date' or $t_type == 'timestamp')
					$t_val = CStr::formatDateTime($t_val);
				
				$record[$t_col] = $t_val;
			}
		}
		
		// cStrNull dengan beberapa aturan
		function cStrManage($post,$ctrans='') {
			$row = array();
			$record = array();
			
			if(!empty($ctrans)) {
				foreach($ctrans as $coldb => $kodecon) {
					list($kode,$jenis) = explode(':',$kodecon);
					$row[$coldb] = $post[$kode];
					$record[$coldb] = CStr::cStrNull($row[$coldb]);
					
					// untuk tanggal
					if($jenis == 'D')
						$record[$coldb] = CStr::formatDate($record[$coldb]);
				}
			}
			else {
				foreach($post as $coldb => $value) {
					$row[$coldb] = $value;
					$record[$coldb] = CStr::cStrNull($value);
				}
			}
			
			return array($row,$record);
		}
		
		// bila $item kosong, set &nbsp;
		function cStrNBSP($item) {
			$item = CStr::removeSpecial($item);
			if($item == '')
				return '&nbsp;';
			else
				return $item;
		}
		
		// jika parameter kosong, digantikan sebelahnya
		function cEmChg() {
			$n = func_num_args();
			for($i=0;$i<$n;$i++)
				if(func_get_arg($i) != '')
					return func_get_arg($i);
		}
		
		// jika parameter tidak masuk key, digantikan sebelahnya
		function cKeyChg($data,$key,$col,&$post,$default) {
			if(empty($key))
				$value = $post[$col];
			else
				$value = $default[$col];
			if(!isset($data[$value]))
				$value = key($data);
			
			if($value != $default[$col])
				$post[$col] = $value;
			else
				unset($post[$col]);
			
			return $value;
		}
		
		// jika parameter !set, digantikan sebelahnya
		function cNotSetChg() {
			$n = func_num_args();
			for($i=0;$i<$n;$i++) {
				$arg = func_get_arg($i);
				if(isset($arg))
					return $arg;
			}
		}
		
		// menggabungkan parameter
		function joinParam($glue) {
			$n = func_num_args();
			
			$arr = array();
			for($i=1;$i<$n;$i++)
				if(func_get_arg($i) != '')
					$arr[] = func_get_arg($i);
			
			return implode($glue,$arr);
		}
		
		// menggabungkan parameter dengan koma ","
		function joinComma() {
			return CStr::joinParam(', ');
		}
		
		// mengubah format tanggal dari yyyy-mm-dd menjadi array d m y
		function splitDate($ymd,$dmy=false,$delim='-') {
			if($dmy)
				list($d,$m,$y) = explode($delim,substr($ymd,0,10));
			else
				list($y,$m,$d) = explode($delim,substr($ymd,0,10));
			
			return array($d,$m,$y);
		}
		
		// mengubah format tanggal dari yyyy-mm-dd menjadi format indonesia
		function formatDateInd($ymd,$full=true,$dmy=false,$delim='-',$intd=true) {
			if($ymd == '')
				return '';
			if($ymd == 'null')
				return 'null';
			
			list($d,$m,$y) = CStr::splitDate($ymd,$dmy,$delim);
			if($intd)
				$d = (int)$d;
			
			return $d.' '.Date::indoMonth($m,$full).' '.$y;
		}
		
		// mengubah format tanggal dari yyyy-mm-dd menjadi format inggris
		function formatDateEng($ymd,$comma=true,$dmy=false,$delim='-') {
			if($ymd == '')
				return '';
			if($ymd == 'null')
				return 'null';
			
			list($d,$m,$y) = CStr::splitDate($ymd,$dmy,$delim);
			
			$abb = 'th';
			if($d == 1)
				$abb = 'st';
			else if($d == 2)
				$abb = 'nd';
			else if($d == 3)
				$abb = 'rd';
			
			$time = mktime(0,0,0,$m,$d,$y);
			$date = date('F j, Y',$time);
			
			if($comma)
				return str_replace(',',$abb.',',$date);
			else
				return str_replace(',',$abb,$date);
		}
		
		// mengubah format tanggal dari dd-mm-yyyy menjadi yyyy-mm-dd dan sebaliknya
		function formatDate($dmy,$idelim='-',$xdelim='-') {
			if($dmy == '')
				return '';
			if($dmy == 'null')
				return 'null';
			
			list($y,$m,$d) = explode($xdelim,substr($dmy,0,10));
			
			return $d.$idelim.$m.$idelim.$y;
		}
		
		// mengubah format tanggal dari yyyy-mm-dd menjadi format indonesia, plus waktu
		function formatDateTimeInd($ymd,$full=true,$hari=false,$delim='-') {
			if($ymd == '')
				return '';
			if($ymd == 'null')
				return 'null';
			
			list($d,$m,$y) = CStr::splitDate($ymd,$dmy,$delim);
			
			$return = (int)$d.' '.Date::indoMonth($m,$full).' '.$y.', '.substr($ymd,11);
			if($hari)
				$return = Date::indoDay(date('N',mktime(0,0,0,$m,$d,$y))).', '.$return;
			
			return $return;
		}
		
		// mengubah format tanggal dari dd-mm-yyyy menjadi yyyy-mm-dd dan sebaliknya, plus waktu
		function formatDateTime($dmy,$idelim='-',$xdelim='-') {
			if($dmy == '')
				return '';
			if($dmy == 'null')
				return 'null';
			
			return CStr::formatDate($dmy,$idelim,$xdelim).' '.substr($dmy,11);
		}
		
		// mengubah format waktu (di kelas)
		function formatJam($jam,$separator=':') {
			if(empty($jam))
				return '';
			
			$str = str_pad($jam,4,'0',STR_PAD_LEFT);
			return substr($str,0,2).$separator.substr($str,-2);
		}
		
		// mengubah format bilangan
		function formatNumber($num,$dec=0) {
			return number_format($num,$dec,',','.');
		}
		
		// mengubah array true-indexed menjadi in untuk where
		function arrayToIn($array) {
			$f = true;
			
			$in = "('";
			foreach($array as $idx => $true) {
				if(!$f) $in .= "','";
				$in .= $idx;
				$f = false;
			}
			$in .= "')";
			
			return $in;
		}
		
		// mendapatkan bagian terakhir data
		function getLastPart($str,$separator='.') {
			$rchr = strrchr($str,$separator);
			
			if($rchr === false)
				return $str;
			else
				return substr($rchr,1);
		}
		
		// join key array
		function getJoinKey($row,$key) {
			if(is_array($key)) {
				$a_key = array();
				foreach($key as $t_key)
					$a_key[] = $row[CStr::getLastPart($t_key)];
				return implode(':',$a_key);
			}
			else
				return $row[CStr::getLastPart($key)];
		}
		
		// mengubah array berindex menjadi xml response
		function createResponseXML($array) {
			$xml = '<response>';
			
			$current = current($array);
			if(is_array($current)) {
				foreach($array as $sub) {
					$xml .= '<item>';
					foreach($sub as $idx => $value)
						$xml .= '<'.$idx.'>'.$value.'</'.$idx.'>';
					$xml .= '</item>';
				}
			}
			else {
				foreach($array as $idx => $value)
					$xml .= '<'.$idx.'>'.$value.'</'.$idx.'>';
			}
			
			$xml .= '</response>';
			
			return $xml;
		}
		
		// format string sesuai jenis
		function formatData($str,$type) {
			if($type == 'D')
				return CStr::formatDate($str);
			else if($type == 'N')
				return CStr::formatNumber($str);
			else
				return $str;
		}
		
		// kembalikan format string
		function formatBack($str,$type) {
			if($type == 'D')
				return CStr::formatDate($str);
			else if($type == 'N')
				return CStr::cStrDec($str);
			else
				return $str;
		}
		
		// cek elemen array
		function arrElem() {
			$n = func_num_args();
			$arr = func_get_arg(0);
			for($i=1;$i<$n;$i++)
				if($arr[func_get_arg($i)])
					return true;
			return false;
		}
	}
?>