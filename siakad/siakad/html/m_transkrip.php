<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTranskrip extends mModel {
		const schema = 'akademik';
		const table = 'ak_transkrip';
		const order = 'periode,kodemk';
		const key = 'thnkurikulum,kodemk,nim,kodeunit';
		const label = 'transkrip';
		
		// mendapatkan kueri list
		function listQuery() {
			global $r_key;
			
			$sql = "select t.periode, t.thnkurikulum,t.kodemk,t.namamk,t.kodeunit,t.nim,t.sks,t.nhuruf,t.nangka,(t.nangka*t.sks) as nk
					from ".static::table()." t
            
					where t.nim = '$r_key'";
					
			
			return $sql;
		}
		
		// mendapatkan data per kompetensi
		function getDataPerKompetensi($conn,$nim) {
			$sql = "select t.periode, t.kodemk,t.namamk,t.sks,t.nhuruf,t.nangka,(t.nangka*t.sks) as nk,m.kodejenis,j.namajenis
					from ".static::table()." t
				
					left join ".static::table('ak_matakuliah')." m using (thnkurikulum,kodemk)
					left join ".static::table('lv_jenismk')." j on m.kodejenis = j.kodejenis
					where t.nim = '$nim' order by j.urutan, t.namamk ";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[$row['kodejenis']][] = $row;
			
			return $a_data;
		}
		       

		
		// mendapatkan data sesuai kurikulum
		function getDataPerKurikulum($conn,$nim) {
			$sql = "select t.periode, t.kodemk,t.namamk,t.sks,t.nhuruf,t.nangka,(t.nangka*t.sks) as nk,k.thnkurikulum, k.kodeunit, k.semmk
      
					from ".static::table()." t
				
					left join ".static::table('ak_kurikulum')." k on k.kodemk = t.kodemk AND k.thnkurikulum='2012' AND k.kodeunit='$kodeunit'
			
					where t.nim = '$nim' order by k.semmk ";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[$row['semmk']][] = $row;
			
			return $a_data;
		}
		// mendapatkan mata kuliah tidak lulus
		function getDataTidakLulus($conn,$nim) {
			$sql = "select kodemk, nhuruf, lulus from ".static::table()."
					where nim = '$nim' and coalesce(lulus,0) = 0 and coalesce(nhuruf,'') <> ''";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[$row['kodemk']] = $row['nhuruf'];
			
			return $a_data;
		}
		
		/**
		 * mendapatkan mata kuliah yang sudah diambil, untuk dibandingkan dengan KRS
		 * @param object $conn 
		 * @param string $nim 
		 * @return array 
		 * @author dayat
		 */
		function getTrankskripMhs($conn,$nim,$transfer=0) {
			$sql = "select t.thnkurikulum,t.kodeunit,t.kodemk,t.nim,t.namamk, t.nhuruf,t.nangka, t.lulus,e.tahunkurikulumbaru,e.kodemkbaru 
          from ".static::table()." t
					left join ".static::table('ak_ekivaturan')." e on e.thnkurikulum = t.thnkurikulum and e.kodemklama = t.kodemk and e.kodeunitlama = t.kodeunit 
					where t.nim = '$nim' and istransfer=-1";
			$data = $conn->GetArray($sql);
			
			return $data;
		}
	}
?>
