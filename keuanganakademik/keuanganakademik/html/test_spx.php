<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// direktori spx
	$dir = '/home/bri/spx/';
	
	if($handle = opendir($dir)) {
		$sql = "truncate table h2h.temp_spx";
		$conn->Execute($sql);
		
		$sql = "select setval('h2h.temp_spx_idtemp_seq',1,false)";
		$conn->Execute($sql);
		
		while (false !== ($entry = readdir($handle))) {
			if ($entry != "." && $entry != "..") {
				$str = file_get_contents($dir.$entry);
				$rows = explode(PHP_EOL,$str);
				
				foreach($rows as $row) {
					$row = trim($row);
					$cols = explode('|',$row);
					
					if($cols[0] == '0')
						continue;
					
					$sql = "insert into h2h.temp_spx (flag,transtime,bankcode,nim,kodekelompok,periode,refno,notoken,jumlahbayar) values ('".implode("','",$cols)."')";
					$conn->Execute($sql);
				}
			}
		}
		
		$sql = "update h2h.temp_spx set nim = null where nim = ''";
		$conn->Execute($sql);
		
		$sql = "update h2h.temp_spx set kodekelompok = null where kodekelompok = ''";
		$conn->Execute($sql);
		
		$sql = "update h2h.temp_spx set periode = null where periode = ''";
		$conn->Execute($sql);
		
		$sql = "update h2h.temp_spx set refno = null where refno = ''";
		$conn->Execute($sql);
		
		closedir($handle);
	}
?>
