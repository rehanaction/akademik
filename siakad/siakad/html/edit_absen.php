<?php
$conn->debug=true;
	require_once(Route::getModelPath('setting'));
	require_once(Route::getModelPath('absensikuliah'));
	$min_absen=mSetting::minAbsen($conn);
	$arr_absen=array();
	$periode='20133';
	$sql="select nim from akademik.ak_perwalian where periode='$periode' and prasyaratspp=-1 and nim='201181106'";
	$data=$conn->GetArray($sql);
	foreach($data as $row) { 
		$t_nim=$row['nim'];
		$krs=$conn->GetArray("select kodemk from akademik.ak_krs where nim='$t_nim' and periode='$periode' and isikutuas=0");
		foreach($krs as $rowk){
			$data_absen = mAbsensiKuliah::getAbsensi($conn,$t_nim,$periode,$rowk['kodemk']);
			if(!empty($data_absen['totalabsenkelas'])) {
				$t_absenmhs = round(($data_absen['totalabsenmhs']*100)/$data_absen['totalabsenkelas']);
			}else{
				$t_absenmhs=0;
			}
			//mulai update
			$rec=array();
			$rec['manualuas']=-1;
			if($t_absenmhs < $min_absen){
				$rec['isikutuas']=0;
			}else{
				$rec['isikutuas']=-1;
			}
			$err = Query::recUpdate($conn,$rec,'akademik.ak_krs',"nim='$t_nim' and periode='$periode' and kodemk='".$rowk['kodemk']."'");
			$arr_absen[$t_nim][$rowk['kodemk']]=$t_absenmhs;
		}
	}
	/*print_r($arr_absen);die();
	$conn->BeginTrans();
	foreach($arr_absen as $nim => $arr_krs){
		foreach($arr_krs as $kodemk=>$pros_absen){
			$rec=array();
			$rec['isikututs']=-1;
			if($pros_absen < $min_absen){
				$rec['isikutuas']=0;
			}else{
				$rec['isikutuas']=-1;
			}
			$err = Query::recUpdate($conn,$rec,'akademik.ak_krs',"nim='$nim' and periode='$periode' and kodemk='$kodemk'");
		}
	}
	$ok = Query::isOK($err);	
	$conn->CommitTrans($ok);
	*/
?>
