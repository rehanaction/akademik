<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPoinkegiatan extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'ms_strukturkegiatanpoin';
		const order = 'kodekegiatan';
		const key = 'periode,kodekegiatan,kodejenjang';
		const label = 'setting poin kegiatan';
		
		function listQuery($conn) {
			$sql = "select *
					from ".static::table('ms_strukturkegiatan')." t 
					left join ".static::table()." p using (kodekegiatan)
					";
			
			return $sql;
		}
		
		function getListData($conn,$periode='',$kodejenjang=''){

			$sql = " select t.kodekegiatan as key, * from ".static::table('ms_strukturkegiatan')." t where 1=1 ";
			
			$sql .=	" order by infoleft  ";
			$data = array();
			$rs = $conn->GetArray($sql);
			$huruf ='a';
			$no ='1';
			foreach($rs as $v){
				$data[$v['key']] = $v;
				if($v['level'] == 0){
					$no ='1';
					$huruf ='a';
				}else if($v['level'] == 1){
					$data[$v['key']]['no'] = "<b>".$huruf."</b>";
					$huruf++;
				}else{
					$data[$v['key']]['no'] = $no;
					$no++;
				}
			}
			//var_dump($data);exit;	
			return $data;
		}
		
		function getListArrayPeriode($conn,$periode='',$kodejenjang=''){
			$sql = " select * from ".static::table()." t where 1=1 ";
			
			if(!empty($periode))
				$sql .= " and  periode = '$periode' ";
			if(!empty($kodejenjang))
				$sql .= " and kodejenjang = '$kodejenjang'  ";
			
			//$sql .=	" order by t.infoleft ";
			
			$rows = $conn->GetArray($sql);;
			
			$data = array();
			if(!empty($rows)){
				foreach($rows as $row){
					$data[$row['periode']][$row['kodekegiatan']] = $row['poinkegiatan'];
				}
			}
			
			return $data;
			//var_dump($data);exit;
		}
		
		// mendapatkan data untuk session
		function getDataSession($conn) {
			$sql = "select * from ".static::table()." where idsetting = 1";
			$row = $conn->GetRow($sql);
			
			$rows = array();
			$rows['KURIKULUM'] = $row['thnkurikulumsekarang'];
			$rows['PERIODE'] = $row['periodesekarang'];
			$rows['PERIODESPA'] = $row['periodespa'];
			$rows['TAHAP'] = $row['tahapfrs'];
			$rows['ISINILAI'] = $row['isinilai'];
			$rows['BIODATAMHS'] = $row['biodatamhs'];
			$rows['PERIODENILAI'] = $row['periodenilai'];
			$rows['PERIODENILAISPA'] = $row['periodenilaispa'];
			$rows['ISDEFAULTSKALANILAI'] = $row['isparameternilai'];
			$rows['DETIP'] = $row['detip'];
			$rows['PROSENTASEABSENSI'] = $row['pros_kehadiran'];
			
			return $rows;
		}
		
		function getPoin($conn,$key){
			$sql = "select poinkegiatan from ".static::table()." where ".static::getCondition($key);
			return $conn->GetOne($sql);
		}
		
		function copyPoinKegiatan($conn,$periodeawal,$periodetujuan,$jenjangawal,$jenjangtujuan){
			
			$conn->beginTrans();
			
				$sql = "select 1 from  ".static::Table()." where periode = '$periodeawal' and kodejenjang = '$jenjangawal' and coalesce(poinkegiatan,0) > 0 ";
				$poin = $conn->getOne($sql);
				
				if (empty ($poin))
					list($p_posterr,$p_postmsg) = array(true,'poin di periode asal tidak ada, proses salin gagal');
				else{
					$conn->Execute("delete from ".static::table()." where periode = '$periodetujuan' and kodejenjang = '$jenjangtujuan'");
					
					$errNo = $conn->ErrorNo();
					
					if ($errNo <> '0')
						list($p_posterr,$p_postmsg) = array(true,'Proses hapus data di periode dan jenjang tujuan gagal');
					else{
						$conn->Execute(" insert into ".static::table()." (periode,kodekegiatan,poinkegiatan,keterangan,idtahap,kodejenjang) 
										select '".$periodetujuan."'::text,kodekegiatan,poinkegiatan,keterangan,idtahap,'".$jenjangtujuan."'::text from ".static::table()." 
										where periode = '".$periodeawal."' and kodejenjang = '".$jenjangawal."' ");
										
						$errNo = $conn->ErrorNo();
						
						if ($errNo <> '0')
							list($p_posterr,$p_postmsg) = array(true,'Gagal melakukan penambahan data');
						else
							list($p_posterr,$p_postmsg) = array(false,'Berhasil melakukan salin poin kegiatan');	
					}
				}
				
				$ok = Query::isErr($p_posterr);
				
			$conn->commitTrans($ok);
			
		return array($p_posterr,$p_postmsg);	
		}
		
	}
?>
