<?php
	include('../includes/adodb5/adodb.inc.php');
	
	$conn = ADONewConnection('postgres8');
	$conn->Connect('172.16.88.21:5432', 'esademo', 'esademo', 'akademik');
	$conn->SetFetchMode(ADODB_FETCH_ASSOC);
//	$conn->debug = true;
	//echo "via";die();
	// kalau kodeunit sudah mencerminkan urutan
	/* $tmp = 0;
	$unit = array();
	$rs = $conn->Execute("select kodeunit,kodeunitparent from gate.ms_unit order by kodeunit");
	
	while($row = $rs->FetchRow()) {
		$parent = $row['kodeunitparent'];
		$child = $row['kodeunit'];
		
		if($parent == '0'){
			$unit[$child]['l'] = 1;
			$unit[$child]['r'] = 2;
		}else{
			$tmp = $unit[$parent]['r'];
			
			foreach($unit as $k=>$v){
				if($unit[$k]['r'] >= $tmp)
					$unit[$k]['r'] += 2;
			}
		
			$unit[$child]['l'] = $tmp;
			$unit[$child]['r'] = $tmp+1;
		}
	}

	foreach($unit as $k => $v){
		// echo $v['l'].'-'.$k.'-'.$v['r'].'<br>';
		$conn->Execute("update gate.ms_unit set infoleft = {$v['l']}, inforight = {$v['r']} where kodeunit = '$k'");
	} */
	
	// menggunakan level dan kodeunit per children suatu parent
	function getListUnitUrut($conn,$mode=UNIT_SORT_REVERSE) {
		$sql = "select kodeunit, namaunit, kodeunitparent, level from gate.ms_unit
				order by level, kodeunit";
		$data = $conn->GetArray($sql);
		//print_r($data);
		// bentuk kode urutan
		$a_kode = array();
		$a_unit = array();
		$t_maxlevel = 0;
		foreach($data as $row) {
			$t_kodeunit = $row['kodeunit'];
			//echo "vvv".$t_kodeunit;die();
			$a_unit[$t_kodeunit] = $row;
			$a_kode[$t_kodeunit] = $a_kode[$row['kodeunitparent']].str_pad($row['kodeunit'],10,'0',STR_PAD_LEFT);
			
			if($row['level'] > $t_maxlevel)
				$t_maxlevel = $row['level'];
		}
		
		if($mode == UNIT_SORT_REVERSE) {
			$t_digit = ($t_maxlevel+1)*2;
			foreach($a_kode as $t_kodeunit => $t_kode)
				$a_kode[$t_kodeunit] = str_pad($t_kode,$t_digit,'x',STR_PAD_LEFT);
			
			asort($a_kode);
			
			foreach($a_kode as $t_kodeunit => $t_kode)
				$a_kode[$t_kodeunit] = ltrim($t_kode,'x');
		}
		else if($mode == UNIT_SORT_TREE)
			asort($a_kode,SORT_STRING);
		
		return array($a_kode,$a_unit);
	}
	
	function getListUnitTree($conn) {
		list($a_kode,$a_unit) = getListUnitUrut($conn);
		
		// cek unit cakupan
		$tree = array();
		foreach($a_kode as $t_kodeunit => $t_kode) {
			$t_unit = $a_unit[$t_kodeunit];
			$t_namaunit = $t_unit['namaunit'];
			$t_kodeparent = strval($t_unit['kodeunitparent']);
			
			if(empty($tree[$t_kodeunit]))
				$tree[$t_kodeunit] = $t_namaunit;
			else
				$tree[$t_kodeunit]['label'] = $t_namaunit;
			
			if(strcmp($t_kodeparent,'') != 0) {
				$tree[$t_kodeparent]['data'][$t_kodeunit] = $tree[$t_kodeunit];
				unset($tree[$t_kodeunit]);
			}
			else
				$tree[$t_kodeunit]['label'] = $t_namaunit;
		}
		
		return $tree;
	}
	
	$c = 0;
	function setLeftRight($kode,$unit,&$lr) {
		global $c;
		
		$lr[$kode]['l'] = ++$c;
		
		if(!empty($unit[$kode]['data'])) {
			foreach($unit[$kode]['data'] as $k => $v)
				setLeftRight($k,$unit,$lr);
		}
		
		$lr[$kode]['r'] = ++$c;
	}
	
	$c = 0;
	$a_lr = array();
	$a_unit = getListUnitTree($conn);
	
	setLeftRight(key($a_unit),$a_unit,$a_lr);
	
	foreach($a_lr as $k => $v) {
		// echo $v['l'].'-'.$k.'-'.$v['r'].'<br>';
		$conn->Execute("update gate.ms_unit set infoleft = {$v['l']}, inforight = {$v['r']} where kodeunit = '$k'");
	}echo "selesai";
?>
