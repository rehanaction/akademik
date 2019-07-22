<?php
	// model periode wisuda
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mThnkurikulum extends mModel {
		const schema = 'akademik';
		const table = 'ms_thnkurikulum';
		const order = 'thnkurikulum';
		const key = 'thnkurikulum';
		const label = 'tahun kurikulum';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select thnkurikulum from ".static::table()." order by thnkurikulum desc";
			
			return Query::arrQuery($conn,$sql);
		}
		function allData($conn, $thn){
		$sql="select t.thnkurikulum, m.mk_totalkurikulum, p.kode_jenjang_studi, 
				sn.set_skalanilai, 
				un.set_tipekuliah, un.set_unsurnilai,  
				sp.set_skalapredikat,
				bs.set_batassks,
				ev.set_evaluasi,
				j.kodeunit, u.namaunit, j.mk_jurusan, j.mk_wajib, j.mk_pilihan, j.mk_paket
				from akademik.ms_thnkurikulum t 
				left join (	select thnkurikulum, count(kodemk) as mk_totalkurikulum from akademik.ak_matakuliah group by thnkurikulum ) m on t.thnkurikulum = m.thnkurikulum 
				left join (
						select thnkurikulum, kodeunit, 
						count(kodemk) as mk_jurusan,
						sum(case when wajibpilihan = 'W' then 1 else 0 end) as mk_wajib, 
						sum(case when wajibpilihan <> 'W' then 1 else 0 end) as mk_pilihan, 
						sum(case when paket = 1 then 1 else 0 end) as mk_paket 
						from akademik.ak_kurikulum group by thnkurikulum, kodeunit
				) j on t.thnkurikulum = j.thnkurikulum
				left join gate.ms_unit u on j.kodeunit = u.kodeunit
				left join akademik.ak_prodi p on j.kodeunit = p.kodeunit
				left join (	
						select thnkurikulum, programpend, count(*) as set_skalanilai 
						from akademik.ak_skalanilai 
						group by thnkurikulum, programpend
				) sn on t.thnkurikulum = sn.thnkurikulum and p.kode_jenjang_studi = sn.programpend
				left join (	
						select thnkurikulum, programpend, count(distinct(tipekuliah)) as set_tipekuliah, count(*) as set_unsurnilai 
						from akademik.ak_unsurnilai 
						group by thnkurikulum, programpend
				) un on t.thnkurikulum = un.thnkurikulum and p.kode_jenjang_studi = un.programpend
				left join (	
						select thnkurikulum, programpend, count(*) as set_skalapredikat 
						from akademik.ak_predikat 
						group by thnkurikulum, programpend
				) sp on t.thnkurikulum = sp.thnkurikulum and p.kode_jenjang_studi = sp.programpend
				left join (	
						select thnkurikulum, programpend, count(*) as set_batassks 
						from akademik.ak_batassks 
						group by thnkurikulum, programpend
				) bs on t.thnkurikulum = bs.thnkurikulum and p.kode_jenjang_studi = bs.programpend
				left join (	
						select thnkurikulum, programpend, count(*) as set_evaluasi 
						from akademik.ak_predikat 
						group by thnkurikulum, programpend
				) ev on t.thnkurikulum = ev.thnkurikulum and p.kode_jenjang_studi = ev.programpend
				where t.thnkurikulum='$thn'
				order by t.thnkurikulum, p.programpend, j.kodeunit";
				$rsc = $conn->Execute($sql);
				
				return $rsc;
		
		}
	}
?>