<?php
	// cek akses halaman
	//defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//$a_auth = Modul::getFileAuth();
	/*$conn->debug=true;
	$sql="select idpegawai,idpegawailama from sdm.ms_pegawai";
	$data=$conn->GetArray($sql);
	$jum_data=0;
	$sukses=0;
	$gagal=0;
	$conn->BeginTrans();
	foreach($data as $row){
		echo $row['idpegawai'].'-'.$row['idpegawailama']."<br>";
		/*$up=$conn->Execute("update sdm.ms_pegawai set idpegawai='".$row['idpegawailama']."' where idpegawai='".$row['idpegawai']."'");
		if($up){
			$ok=true;
			$sukses++;
		}else{
			$ok=false;
			break;
		}*/
		/*
		$jum_data++;
	}
	echo $sukses;
	$conn->CommitTrans($ok);
	*/
	$conn->debug=true;
	$in_periode='20133';
	$sql="SELECT e.thnkurikulum, e.periode, e.kodeunit, e.kodemk, mk.namamk, e.kelasmk, e.nim, m.nama, m.semestermhs, m.kodeunit AS kodeunitmhs, count(k.perkuliahanke) AS totalabsenkelas, count(a.perkuliahanke) AS totalabsenmhs
		   FROM akademik.ak_krs e
		   JOIN akademik.ms_mahasiswa m ON m.nim::text = e.nim::text
		   JOIN akademik.ak_matakuliah mk ON mk.thnkurikulum::text = e.thnkurikulum::text AND mk.kodemk::text = e.kodemk::text
		   LEFT JOIN akademik.ak_kuliah k ON k.thnkurikulum::text = e.thnkurikulum::text AND k.periode::text = e.periode::text AND k.kodeunit::text = e.kodeunit::text AND k.kodemk::text = e.kodemk::text AND k.kelasmk::text = e.kelasmk::text and k.statusperkuliahan='S'
		   LEFT JOIN akademik.ak_absensikuliah a ON a.thnkurikulum::text = e.thnkurikulum::text AND a.periode::text = e.periode::text AND a.kodeunit::text = e.kodeunit::text AND a.kodemk::text = e.kodemk::text AND a.kelasmk::text = e.kelasmk::text AND a.nim::text = e.nim::text AND a.perkuliahanke = k.perkuliahanke AND a.tglkuliah = k.tglkuliah
		where k.periode='$in_periode'
		  GROUP BY e.thnkurikulum, e.periode, e.kodeunit, e.kodemk, mk.namamk, e.kelasmk, e.nim, m.nama, m.semestermhs, m.kodeunit;";
	$data=$conn->GetArray($sql);
	foreach($data as $row){
		list($periode , $thnkurikulum , $kodeunit , $kodemk , $kelasmk , $nim )=array($row['periode'],$row['thnkurikulum'],$row['kodeunit'],$row['kodemk'],$row['kelasmk'],$row['nim']);
		$t_absenkelas = $row['totalabsenkelas'];
		if(!empty($t_absenkelas)) {
			$t_absenmhs = round(($row['totalabsenmhs']*100)/$t_absenkelas);
			if($t_absenmhs < 75){
				$ikututs=0;
				$ikutuas=0;
			}else{
				$ikututs=-1;
				$ikutuas=-1;
			}
		}
		//$update=$conn->Execute("update akademik.ak_krs set isikututs='$ikututs',isikutuas='$ikutuas' where periode='$periode' and  thnkurikulum='$thnkurikulum' and kodeunit='$kodeunit' and  kodemk='$kodemk' and kelasmk='$kelasmk' and nim='$nim'");
		echo $row['nim'].'-'.$row['totalabsenkelas'].'-'.$row['totalabsenmhs']."<br>";
	}
?>
