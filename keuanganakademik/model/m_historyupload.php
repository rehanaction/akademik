<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mHistoryUpload extends mModel {
		const schema = 'h2h';
		const table = 'ke_historyupload';
		const order = 'uploadtime desc';
		const key = 'uploadtime';
		const label = 'uploadtime';
		const sequence = 'ke_historyupload_idhistoryupload_seq';
		
		function listQuery() {
			$sql = "select * from (select uploadtime, max(t_updateuser) as user,
					count(nim) as jumlahtransaksi, sum(jumlahbayar) as jumlahbayar,
					count(case when iserror = 0 then 1 else null end) as berhasil,
					count(case when iserror = 1 then 1 else null end) as gagal
					from ".static::table()."
					group by uploadtime) x";
			
			return $sql;
		}
		
		function getListTransaksi($conn,$uploadtime) {
			$sql = "select h.*, m.nama from ".static::table()." h
					left join akademik.ms_mahasiswa m on m.nim = h.nim
					where to_char(h.uploadtime,'YYYYMMDDHH24MISS') = ".Query::escape($uploadtime)."
					order by h.tglbayar, h.nim";

			return $conn->Execute($sql);
		}
	}
?>
