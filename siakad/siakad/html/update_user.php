<?php

/*ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);*/
$conn->debug=true;
$conn->BeginTrans();
$row=$conn->GetArray("select nim,kodeunit,nama,tgllahir from akademik.ms_mahasiswa where substr(nim,1,4)='2010'");
$jum=0;
$kosong=0;
foreach($row as $data){
	$jum++;
	$nim=str_replace(" ","",$data['nim']);
	$nama=str_replace("'","''",$data['nama']);
	$tgllahir=$data['tgllahir'];
	$kodeunit=str_replace(" ","",$data['kodeunit']);
	$conn->debug=false;
	$cek=$conn->GetOne("select 1 from gate.sc_user where trim(username)='$nim'");
	$conn->debug=true;
	//echo $kodeunit."<br>";
	if($cek!=1){
		$kosong++;
		echo $nim."<br>";
		if(!empty($tgllahir)){
			$pass=substr($tgllahir,0,4).substr($tgllahir,5,2).substr($tgllahir,8,2);
		}else{
			$pass='19450817';
		}
		$ok=$conn->Execute("insert into gate.sc_user(username,password,hints, userdesc,t_updateact)
			values('$nim',md5('$pass'),'$pass', '$nama', 'db')");
		if($ok){
		$ok=$conn->Execute("insert into gate.sc_userrole(userid, koderole, kodeunit,t_updateact)
			select userid, 'M', '$kodeunit', 'db' from gate.sc_user where username = '$nim'");
		}
	}
}
$conn->CommitTrans($ok);
echo $kosong."<br>".$jum
?>
