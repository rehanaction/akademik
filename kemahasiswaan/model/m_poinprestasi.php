<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPoinprestasi extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'ms_poinprestasi';
		const order = 'kodejenisprestasi';
		const key = 'kodejenisprestasi,kodetingkatprestasi,kodekategoriprestasi,kodejenispeserta';
		const label = 'setting poin prestasi';
		
		function listQuery($conn) {
			$sql = " select  * from ".static::table('ms_poinprestasi')." t 
					 join ".static::table('lv_tingkatprestasi')." using (kodetingkatprestasi)
					 join ".static::table('lv_jenisprestasi')." using (kodejenisprestasi)
					 join ".static::table('lv_kategoriprestasi')." using (kodekategoriprestasi)
					 join ".static::table('lv_jenispeserta')." using (kodejenispeserta) ";			
			return $sql;
		}
		
		/* function getListData($conn,$periode='',$kodejenjang=''){

			$sql = " select  * from ".static::table('ms_poinprestasi')." t 
					 join ".static::table('lv_tingkatprestasi')." using (kodetingkatprestasi)
					 join ".static::table('lv_jenisprestasi')." using (kodejenisprestasi)
					 join ".static::table('lv_kategoriprestasi')." using (kodekategoriprestasi)
					 join ".static::table('lv_jenispeserta')." using (kodejenispeserta)
					 order by kodejenisprestasi,kodetingkatprestasi,kodejenispeserta,kodekategoriprestasi ";
					
			return $conn->GetArray($sql);
		} */
		
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
					$data[$row['periode']][$row['kodeprestasi']] = $row['poinprestasi'];
				}
			}
			
			return $data;
			//var_dump($data);exit;
		}
		
		function getPoin($conn,$key){
			$sql = "select poin from ".static::table()." where ".static::getCondition($key);
			return $conn->GetOne($sql);
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
		function copyPoinPrestasi($conn,$jenisawal,$jenistujuan,$tingkatawal,$tingkattujuan){
			
			$conn->beginTrans();
			
				$sql = "select 1 from  ".static::Table()." where kodejenisprestasi = '$jenisawal' and kodetingkatprestasi = '$tingkatawal' and coalesce(poin,0) > 0 ";
				$poin = $conn->getOne($sql);
				
				if (empty ($poin))
					list($p_posterr,$p_postmsg) = array(true,'poin di periode asal tidak ada, proses salin gagal');
				else{
					$conn->Execute("delete from ".static::table()." where kodejenisprestasi = '$jenistujuan' and kodetingkatprestasi = '$tingkattujuan'");
					
					$errNo = $conn->ErrorNo();
					
					if ($errNo <> '0')
						list($p_posterr,$p_postmsg) = array(true,'Proses hapus data di Jenis dan tingkat tujuan gagal');
					else{
						$conn->Execute(" insert into ".static::table()." (kodejenisprestasi,kodetingkatprestasi,kodekategoriprestasi,kodejenispeserta,poin) 
										select ".$jenistujuan.",'".$tingkattujuan."'::text,kodekategoriprestasi,kodejenispeserta,poin from ".static::table()." 
										where kodejenisprestasi = '$jenisawal' and kodetingkatprestasi = '$tingkatawal' ");
										
						$errNo = $conn->ErrorNo();
						
						if ($errNo <> '0')
							list($p_posterr,$p_postmsg) = array(true,'Gagal melakukan penambahan data');
						else
							list($p_posterr,$p_postmsg) = array(false,'Berhasil melakukan salin poin prestasi');	
					}
				}
				
				$ok = Query::isErr($p_posterr);
				
			$conn->commitTrans($ok);
			
		return array($p_posterr,$p_postmsg);	
		}
 

		
		
	}
?>
