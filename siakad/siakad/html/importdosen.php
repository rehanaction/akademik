<?php
	$file='uploads/dosen.xls';
	// pakai excel reader
	require_once('../includes/phpexcel/excel_reader2.php');
	$xls = new Spreadsheet_Excel_Reader($file);
	
	$cells = $xls->sheets[0]['cells'];
	$numrow = count($cells);
	
	// jika cells kosong (mungkin bukan merupakan format excel), baca secara csv
	if(empty($numrow)) {
		if(($handle = fopen($file, 'r')) !== false) {
			while (($data = fgetcsv($handle, 1000, "\t")) !== false) {
				$numrow++;
				foreach($data as $k => $v)
					$cells[$numrow][$k+1] = $v;
			}
			fclose($handle);
		}
	}
	//print_r($cells);die();
	// baris pertama adalah header
	$conn->BeginTrans();
	
	$ok = true;
	for($r=2;$r<=$numrow;$r++) {
		$data = $cells[$r];
		if(empty($data[3]))
			$pass='19450817';
		else
			$pass=date('Ymd',strtotime($data[3]));
			
		$nama=str_replace("'","''",$data[2]);
		$user=$conn->GetOne("select userdesc from gate.sc_user where username='".$data[1]."'");
		if(empty($user)){
			$del=$conn->Execute("delete from gate.sc_userrole where userid in (select userid from gate.sc_user where username = '".$data[1]."')");
			if(!$del){
				$ok=false;break;
			}
			$del2=$conn->Execute("delete from gate.sc_user where username = '".$data[1]."'");
			if(!$del2){
				$ok=false;break;
			}
			$in=$conn->Execute("insert into gate.sc_user(username,password,hints, userdesc, t_updateact)
								values('".$data[1]."',md5('".$pass."'),'".$pass."','".$nama."', 'migrasi')");
			if(!$in){
				$ok=false;break;
			}
			$in2=$conn->Execute("insert into gate.sc_userrole(userid, koderole, kodeunit, t_updateact)
								select userid, 'D', '20000000', 'migrasi' 
								from gate.sc_user where username = '".$data[1]."'");
			if(!$in2){
				$ok=false;break;
			}
		}else{
			$up1=$conn->Execute("update gate.sc_user set username = '".$data[1]."', userdesc = '".$nama."',password=md5('".$pass."'),hints='".$pass."', t_updateact = 'migrasi'
								where username = '".$data[1]."'");
			if(!$up1){
				$ok=false;break;
			}
		}
		
	}
	$conn->CommitTrans($ok);
			
		
?>
