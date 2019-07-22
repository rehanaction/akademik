<?php
	// cek akses halaman
	//defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//$a_auth = Modul::getFileAuth();
	$conn->debug=false;
	$sql="select nim,kodemk,count(kodemk) 
		from akademik.ak_krs 
		where nim not in(select nim from akademik.ms_mahasiswa where statusmhs in ('L','W','O','U'))
		group by kodemk,nim having count(kodemk)>1 order by nim";
	$data=$conn->GetArray($sql);
	
	foreach($data as $row){
		$sql2="select nim,kodemk,thnkurikulum from akademik.ak_krs 
				where nim='".$row['nim']."' and kodemk='".$row['kodemk']."' and 
				thnkurikulum::text in (select substr(periodemasuk,1,4) from akademik.ms_mahasiswa where nim='".$row['nim']."' and periodemasuk is not null and substr(periodemasuk,1,4)::int < 2013)
				order by periode";
		$data2=$conn->GetArray($sql2);
		foreach($data2 as $row2){
			echo $row2['nim'].'-'.$row2['kodemk'].'-'.$row2['thnkurikulum']."<br>";
		}
	}
?>
