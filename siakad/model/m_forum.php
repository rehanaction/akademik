<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	require_once(Route::getModelPath('kelas'));
	
	class mForum extends mModel {
		function getKelas($conn,$periode,$kodeunit) {
			$hal = substr(static::key,2);
			
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, m.sks,
					k.jumlahpeserta, count(f.".static::key.") as jumlah,
					max(f.waktuposting::text||'|'||f.id{$hal}||'|'||f.judul{$hal}) as label
					from akademik.ak_kelas k
					join akademik.ak_matakuliah m using (thnkurikulum,kodemk)
					join gate.ms_unit u on u.kodeunit = k.kodeunit
					join gate.ms_unit p on u.infoleft >= p.infoleft and u.inforight <= p.inforight and p.kodeunit = '$kodeunit'
					left join ".static::table()." f on f.thnkurikulum = k.thnkurikulum and f.kodemk = k.kodemk and
						f.kodeunit = k.kodeunit and f.periode = k.periode and f.kelasmk = k.kelasmk
					where k.periode = '$periode'
					group by k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, m.sks
					order by m.namamk, k.kodemk, k.kelasmk";
			
			return $conn->GetArray($sql);
		}
		
		function getKelasMhs($conn,$periode,$nim) {
			$hal = substr(static::key,2);
			
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, m.sks,
					k.jumlahpeserta, count(f.".static::key.") as jumlah,
					max(f.waktuposting::text||'|'||f.id{$hal}||'|'||f.judul{$hal}) as label
					from akademik.ak_kelas k
					join akademik.ak_krs s using (thnkurikulum,kodemk,kodeunit,periode,kelasmk)
					join akademik.ak_matakuliah m on m.thnkurikulum = k.thnkurikulum and m.kodemk = k.kodemk
					left join ".static::table()." f on f.thnkurikulum = k.thnkurikulum and f.kodemk = k.kodemk and
						f.kodeunit = k.kodeunit and f.periode = k.periode and f.kelasmk = k.kelasmk
					where k.periode = '$periode' and s.nim = '$nim'
					group by k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, m.sks
					order by m.namamk, k.kodemk, k.kelasmk";
			
			return $conn->GetArray($sql);
		}
		
		function getKelasDosen($conn,$periode,$nip) {
			$hal = substr(static::key,2);
			
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, m.sks,
					k.jumlahpeserta, count(f.".static::key.") as jumlah,
					max(f.waktuposting::text||'|'||f.id{$hal}||'|'||f.judul{$hal}) as label
					from akademik.ak_kelas k
					join akademik.ak_mengajar a using (thnkurikulum,kodemk,kodeunit,periode,kelasmk)
					join akademik.ak_matakuliah m on m.thnkurikulum = k.thnkurikulum and m.kodemk = k.kodemk
					left join ".static::table()." f on f.thnkurikulum = k.thnkurikulum and f.kodemk = k.kodemk and
						f.kodeunit = k.kodeunit and f.periode = k.periode and f.kelasmk = k.kelasmk
					where k.periode = '$periode' and a.nipdosen = '$nip'
					group by k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, m.sks
					order by m.namamk, k.kodemk, k.kelasmk";
			
			return $conn->GetArray($sql);
		}
		
		function getKeyKelas($conn,$key) {
			$sql = "select thnkurikulum, kodemk, kodeunit, periode, kelasmk
					from ".static::table()." where ".static::key." = '$key'";
			$row = $conn->GetRow($sql);
			
			return mKelas::getKeyRow($row);
		}
		
		function getListForumKelas($conn,$key) {
			$sql = "select * from ".static::table()." where ".mKelas::getCondition($key)." order by t_updatetime desc";
			
			return $conn->GetArray($sql);
		}
	}
?>