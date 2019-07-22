<?php
	class Helper {
		// tambah log
		function addLog($record) {
			$record['t_updateuser'] = 'h2h';
			$record['t_updatetime'] = date('Y-m-d H:i:s');
			$record['t_updateip'] = $_SERVER['REMOTE_ADDR'];
			
			return $record;
		}
		
		// random string
		function randomString($len=10,$isnum=false) {
			$pool = '0123456789';
			if(!$isnum)
				$pool .= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			
			$str = '';
			for ($i=0; $i < $len; $i++)
				$str .= substr($pool,mt_rand(0,strlen($pool)-1),1);
			
			return $str;
		}
		
		// escape string
		function escape($str) {
			return "'".pg_escape_string($str)."'";
		}
	}
?>