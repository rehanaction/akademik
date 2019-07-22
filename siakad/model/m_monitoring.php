<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mMonitoring extends mModel {
		const schema = 'akademik';
		const table = 'ak_kelas';
		const order = 'periode,namamk,kelasmk';
		const key = 'thnkurikulum,kodemk,kodeunit,periode,kelasmk';
		const label = 'kelas';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select r.*,
			coalesce((select sum(case when nnumerik is not null then 1 end) from akademik.ak_krs k where k.thnkurikulum = r.thnkurikulum and k.kodemk = r.kodemk and k.kodeunit = r.kodeunit and k.periode = r.periode and k.kelasmk = r.kelasmk),0) as dinilai,
			coalesce((select sum(1) from akademik.ak_krs k where k.thnkurikulum = r.thnkurikulum and k.kodemk = r.kodemk and k.kodeunit = r.kodeunit and k.periode = r.periode and k.kelasmk = r.kelasmk),0) as total
			
			from ".self::table('v_kelas3')." r join gate.ms_unit u on r.kodeunit = u.kodeunit";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'periode': return "periode = '$key'";
				case 'unit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "infoleft >= ".(int)$row['infoleft']." and inforight <= ".(int)$row['inforight']." and tugasmengajar=-1" ;
			}
		}
		function getListMengajar($conn,$kolom,&$sort,$filter='') {
			$sql = "select * from (select u.infoleft,u.inforight,k.periode as periode,k.thnkurikulum, k.kodeunit, k.kodemk, m.namamk, k.kelasmk, m.sks, mj.nipdosen, mj.tugasmengajar, p.gelardepan, p.namadepan, p.namatengah, p.namabelakang, p.gelardepan,
					akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namadosen, kr.semmk
					from ".static::table('ak_kelas')." k 
					left join ".static::table('ak_matakuliah')." m using (thnkurikulum,kodemk)
					left join ".static::table('ak_mengajar')." mj using (periode, thnkurikulum, kodeunit, kodemk, kelasmk)
					left join ".static::table('ak_kurikulum')." kr using (thnkurikulum,kodemk,kodeunit)
					left join sdm.ms_pegawai p on  p.idpegawai::text=mj.nipdosen
					join gate.ms_unit u on k.kodeunit = u.kodeunit group by 
					u.infoleft,u.inforight,k.periode,k.thnkurikulum,k.kodeunit, k.kodemk, m.namamk, k.kelasmk, m.sks, mj.tugasmengajar, mj.nipdosen, p.gelardepan, p.namadepan, p.namatengah, p.namabelakang, p.gelarbelakang,
					akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang),kr.semmk) a";
			return static::getListData($conn,$kolom,$sort,$filter,$sql);
		}

		function getHasilQuiz($conn,$key,$npm){
			$arr=explode("|",$key);
			$condion=mKrs::getCondition($key);
			$sql= "select * from akademik.quiz_adji where thnkurikulum='$arr[0]' and kodemk ='$arr[1]' and kodeunit='$arr[2]' and periode='$arr[3]' and kelasmk='$arr[4]' and nipdosen='$arr[5]' and nim ='$arr[6]'";
			return $conn->GetArray($sql);

		}
		function getHasilQuizAll($conn,$key){
			$arr=explode("|",$key);
			//$condion=mKrs::getCondition($key);
			$sql= "select * from akademik.quiz_adji
					where thnkurikulum='$arr[0]' and kodemk ='$arr[1]' and kodeunit='$arr[2]' and periode='$arr[3]' and kelasmk='$arr[4]' and nipdosen='$arr[5]'
					";
			return $conn->GetArray($sql);
		}
		function arrPilihan($conn,$key){
			$arr=explode("|",$key);
			$periode=$arr[3];
			$sql="select pilihan as key,pilihan as value from akademik.ms_pilquiz where periode='$periode' order by pilihan asc";
			
			return Query::arrQuery($conn,$sql);
		} 

		
		function saranQuisioner($conn,$key) {
			list($thnkurikulum,$kodemk,$kodeunit,$periode,$kelasmk,$nipdosen)=explode('|',$key);

			$sql=  "select a.periode, 
					       a.kodeunit, 
					       a.thnkurikulum, 
					       a.kodemk, 
					       a.kelasmk, 
					       a.nipdosen, 
					       a.saran ,
					       a.nim ,
					       m.nama
					from   akademik.quiz_adji a
						   left join akademik.ms_mahasiswa m on m.nim = a.nim
						   where  a.periode = '$periode' and a.kodeunit = '$kodeunit' and a.thnkurikulum = '$thnkurikulum' 
						   and a.kelasmk = '$kelasmk' and a.nipdosen = '$nipdosen'	   
					group  by a.periode, 
					       a.kodeunit, 
					       a.thnkurikulum, 
					       a.kodemk, 
					       a.kelasmk, 
					       a.nipdosen, 
					       a.saran ,
					       a.nim ,
					       m.nama";

			return $conn->GetArray($sql);
		}
		
		function getJmlPes($conn,$key)
		{
			list($thnkurikulum,$kodemk,$kodeunit,$periode,$kelasmk)=explode('|',$key);
			$sql = "select count(k.nim) as jml
					from ".static::table('ak_krs')." k
					join ".static::table('ms_mahasiswa')." m on k.nim = m.nim
					left join ".static::table('ak_perwalian')." p on m.nim=p.nim and k.periode = k.periode and p.periode='$periode' 
					where ".static::getCondition($key,null,'k')." ";// and coalesce(p.frsdisetujui, 0) <>0 ";
	
			$jml = $conn->GetRow($sql);
			return $jml['jml'];
		
		}
		function getJmlRes($conn,$key)
		{
			list($thnkurikulum,$kodemk,$kodeunit,$periode,$kelasmk)=explode('|',$key);
			$sql = "select count(k.nim) as jml
					from ".static::table('quiz_adji')." k
					where ".static::getCondition($key,null,'k')." ";// and coalesce(p.frsdisetujui, 0) <>0 ";
	
			$jml = $conn->GetRow($sql);
			return $jml['jml'];
		
		}
		function getHeaderQuisioner($conn,$key)
		{
			list($thnkurikulum,$kodemk,$kodeunit,$periode,$kelasmk,$nipdosen)=explode('|',$key);
			$sql = "Select kodemk,nipdosen,kelasmk,periode,thnkurikulum,kodeunit from akademik.quiz_adji where nipdosen='$nipdosen' and thnkurikulum='$thnkurikulum' and kodeunit='$kodeunit' and periode='$periode'  group by kodemk,nipdosen,kelasmk,periode,thnkurikulum,kodeunit";
			return $conn->GetArray($sql);
		}
	}
?>
