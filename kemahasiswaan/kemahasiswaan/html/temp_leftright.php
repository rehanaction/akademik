<?php
	// cek akses halaman
	defined('__VALID_ENTRANCE') or die('Akses terbatas');
	
	$sql = "select kodekegiatan as id, parentkodekegiatan as parent, level
			from kemahasiswaan.ms_strukturkegiatan
			order by level, namakegiatan";
	$data = $conn->GetArray($sql);
	
	$child = array();
	foreach($data as $row)
		$child[strval($row['parent'])][] = strval($row['id']);
	
	$i = 0;
	$lr = array();
	function setLeftRight($idx) {
		global $i, $lr, $child;
		
		if(!empty($child[$idx])) {
			foreach($child[$idx] as $v) {
				$lr[$v]['l'] = ++$i;
				setLeftRight($v);
			}
		}
		
		if(!empty($idx))
			$lr[$idx]['r'] = ++$i;
	}
	
	setLeftRight('');
	
	$sql = "update kemahasiswaan.ms_strukturkegiatan set infoleft = -1, inforight = -1";
	$conn->Execute($sql);
	// echo $sql.';<br>';
	
	foreach($lr as $k => $v) {
		$cols = array();
		if(!empty($v['l']))
			$cols[] = 'infoleft = '.$v['l'];
		if(!empty($v['r']))
			$cols[] = 'inforight = '.$v['r'];
		
		if(!empty($cols)) {
			$sql = "update kemahasiswaan.ms_strukturkegiatan set ".implode(', ',$cols)." where kodekegiatan = '$k'";
			$conn->Execute($sql);
			// echo $sql.';<br>';
		}
	}
?>