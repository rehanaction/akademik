<?php
	// model pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKuisionerFront extends mModel {
		const schema = 'seminar';
		const table = 'ms_jawabanpeserta';
		const order = 'idjawabanpeserta';
		const key = 'idjawabanpeserta';
		const label = 'Kuisioner Seminar';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;			
			return Query::arrQuery($conn,$sql);
		}

		// mendapatkan array data
		function getKuisioner($conn,$idseminar,$nopendaftar) {
			$sql = "select 1 as isfilled  from ".static::table()." 
					where idseminar = '$idseminar' and nopendaftar = '$nopendaftar' 
					order by ".static::order;			
			
			return $conn->GetArray($sql);
		}

		// mendapatkan array data
		function getRekapKuisioner($conn,$idseminar) {
			$sql = "select j.* , s.namaseminar , s.tglkegiatan , p.nama , pe.nopeserta ,

					   (select count(jawaban_1) from seminar.ms_jawabanpeserta where jawaban_1 ='Sangat Menarik' and idseminar = j.idseminar) as sm1,
				       (select count(jawaban_1) from seminar.ms_jawabanpeserta where jawaban_1 ='Menarik' and idseminar = j.idseminar) as m1,
				       (select count(jawaban_1) from seminar.ms_jawabanpeserta where jawaban_1 ='Biasa Saja' and idseminar = j.idseminar) as bs1,
				       (select count(jawaban_1) from seminar.ms_jawabanpeserta where jawaban_1 ='Cukup' and idseminar = j.idseminar) as c1,
				       (select count(jawaban_2) from seminar.ms_jawabanpeserta where jawaban_2 ='Sangat Menarik' and idseminar = j.idseminar) as sm2,
				       (select count(jawaban_2) from seminar.ms_jawabanpeserta where jawaban_2 ='Menarik' and idseminar = j.idseminar) as m2,
				       (select count(jawaban_2) from seminar.ms_jawabanpeserta where jawaban_2 ='Biasa Saja' and idseminar = j.idseminar) as bs2,
				       (select count(jawaban_2) from seminar.ms_jawabanpeserta where jawaban_2 ='Cukup' and idseminar = j.idseminar) as c2,
				       (select count(jawaban_3) from seminar.ms_jawabanpeserta where jawaban_3 ='Ya' and idseminar = j.idseminar) as y,
				       (select count(jawaban_3) from seminar.ms_jawabanpeserta where jawaban_3 ='Tidak' and idseminar = j.idseminar) as t

					from ".static::table()."  j
					left join seminar.ms_seminar s
						on j.idseminar = s.idseminar
					left join seminar.ms_pendaftar p 
						on p.nopendaftar = j.nopendaftar
					left join seminar.ms_peserta pe 
						on pe.nopendaftar = j.nopendaftar and pe.idseminar = j.idseminar
					where j.idseminar = '$idseminar' and pe.nopeserta is not null order by pe.nopeserta";
					
			
			return $conn->GetArray($sql);
		}
 
	}
?>