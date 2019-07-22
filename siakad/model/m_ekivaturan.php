<?php
	// model ekivalensi mata kuliah
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mEkivAturan extends mModel {
		const schema = 'akademik';
		const table = 'ak_ekivaturan';
		const order = 'kodemklama';
		const key = 'tahunkurikulumbaru,kodeunitbaru,kodemkbaru,thnkurikulum,kodeunitlama,kodemklama';
		const label = 'evaluasi';
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'thnkurikulumlama': return "e.thnkurikulum = '$key'";
				case 'thnkurikulumbaru': return "tahunkurikulumbaru = '$key'";
				case 'kodeunitlama': return "kodeunitlama = '$key'";
				case 'kodeunitbaru': return "kodeunitbaru = '$key'";
			}
		}
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select e.*, ml.namamk as namamklama, mb.namamk as namamkbaru, ml.sks as skslama, mb.sks as sksbaru
					from ".static::table()." e
					left join ".static::table('ak_matakuliah')." ml on ml.thnkurikulum = e.thnkurikulum and ml.kodemk = e.kodemklama
					left join ".static::table('ak_matakuliah')." mb on mb.thnkurikulum = e.tahunkurikulumbaru and mb.kodemk = e.kodemkbaru";
			
			return $sql;
		}
		
		// mendapatkan mata kuliah lama
		function getListLama($conn,$kurbaru,$mkbaru,$unitbaru) {
			$sql = "select e.thnkurikulum,e.kodemklama as kodemk,e.kodeunitlama as kodeunit,m.namamk,m.sks from ".static::table()." e
					join ".static::table('ak_matakuliah')." m on m.thnkurikulum = e.thnkurikulum and m.kodemk = e.kodemklama
					where e.tahunkurikulumbaru = '$kurbaru' and e.kodemkbaru = '$mkbaru' and e.kodeunitbaru = '$unitbaru'";
			
			return $conn->GetArray($sql);
		}
		
		// mendapatkan data sebelah kiri
		function getListKiri($conn,$nim,$kodeunit) {
			$sql = "select k.thnkurikulum, k.kodemk, k.namamk, k.sks, k.kodeunit, k.semmk as semester, t.nhuruf, t.nim, t.lulus
					from akademik.ak_kurikulum k
					join akademik.ak_transkrip t on t.thnkurikulum = k.thnkurikulum and t.kodemk = k.kodemk and t.nim = ".Query::escape($nim)."
					where k.kodeunit = ".Query::escape($kodeunit)." and t.lulus = -1 order by kodemk";
			
			return $conn->GetArray($sql);
		}
		
		// mendapatkan data sebelah kanan
		function getListKanan($conn,$nim,$kodeunit,$kurikulum) 
    {
			$sql = "select a.*, e.statusekivalen from akademik.ak_kurikulum a
					left join akademik.ak_ekivmhs e on e.nim = ".Query::escape($nim)." and e.thnkurikulum = a.thnkurikulum
					and e.kodemk = a.kodemk and e.kodeunit = a.kodeunit
					where a.kodeunit = ".Query::escape($kodeunit)." and a.thnkurikulum = ".Query::escape($kurikulum)." order by kodemk";
			
			return $conn->GetArray($sql);
		}
    
    
		// mendapatkan data sebelah kiri versi baru
		function getListKiriN($conn,$nim,$kodeunit) 
    {
			$sql = "select k.thnkurikulum, k.kodemk, k.namamk, k.sks, k.kodeunit, k.semmk as semester, t.nhuruf, t.nim, t.lulus
					from akademik.ak_kurikulum k
					join akademik.ak_transkrip t on t.thnkurikulum = k.thnkurikulum and t.kodemk = k.kodemk and t.nim = ".Query::escape($nim)."
					where k.kodeunit = ".Query::escape($kodeunit)." and t.lulus = -1 and t.thnkurikulum = '2012' order by kodemk";
			
			return $conn->GetArray($sql);
		}
		
		// mendapatkan data sebelah kanan versi baru (dgn nilai yg sudah masuk menggunakan K-2017)
		function getListKananN($conn,$nim,$kodeunit,$kurikulum) 
    {    
      
			$sql = "select a.*, e.statusekivalen, t.nim, t.nhuruf, t.lulus 
              from akademik.ak_kurikulum a
					    left join akademik.ak_ekivmhs e on e.nim = ".Query::escape($nim)." and e.thnkurikulum = a.thnkurikulum
					              and e.kodemk = a.kodemk and e.kodeunit = a.kodeunit
              left join akademik.ak_transkrip t on t.nim = ".Query::escape($nim)." and t.thnkurikulum = a.thnkurikulum
                        and t.kodemk = a.kodemk          
					    where a.kodeunit = ".Query::escape($kodeunit)." and a.thnkurikulum = ".Query::escape($kurikulum)." order by kodemk";
			
			return $conn->GetArray($sql);    
		} 
    
    
         
	}
?>