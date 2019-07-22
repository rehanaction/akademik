<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mAsistenAjar extends mModel {
		const schema = 'akademik';
		const table = 'ak_asistenajar';
		const order = 'nipasisten';
		const key = 'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskul,kelompok,nipasisten';
		const label = 'asisten';
		
		function listQuery() {
			$sql = "select a.thnkurikulum,a.periode,a.kodeunit,a.kodemk,a.kelasmk,a.jeniskul,a.kelompok,akademik.f_namahari(b.nohari) AS namahari,
					d.namamk,d.sks,d.semmk,b.jammulai,b.jamselesai,akademik.f_namahari(b.nohari2) AS namahari2,b.jammulai2,
					b.jamselesai2,b.koderuang,coalesce(b.jumlahpeserta,0) as jmlpeserta,b.sistemkuliah from ".static::table()." a
					join ".static::table('ak_kelas')." b on(a.periode=b.periode and a.kodeunit=b.kodeunit and a.thnkurikulum=b.thnkurikulum and a.kodemk=b.kodemk and a.kelasmk=b.kelasmk and a.jeniskul='P')
					join ".static::table('ak_kurikulum')." d on(a.thnkurikulum=d.thnkurikulum and a.kodeunit=d.kodeunit and a.kodemk=d.kodemk)";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'periode': return "a.periode = '$key'";
				case 'nipasisten': return "a.nipasisten = '$key'";
			}
		}
		
		function getAsistenPengajar($conn,$key) {
			require_once(Route::getModelPath('kelaspraktikum'));
			$sql = "select m.nipasisten, 
					p.namapegawai||' ('||m.nipasisten||')'  as namaasisten
					from ".static::table()." m
					join ".static::table('ms_pegawaipenunjang')." p on m.nipasisten = p.nopegawai
					where ".mKelasPraktikum::getCondition($key,null,'m')." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
