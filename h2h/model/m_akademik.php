<?php
	class mAkademik {
		function getInfoMahasiswa($conn,$nim) {
			$sql = "select m.nama, coalesce(u.namasingkat,u.namaunit) as namaunit,
					m.jalurpenerimaan, m.gelombang, case when s.kodebasis = 'P' then 2 else 1 end as basis
					from h2h.v_mhspendaftar m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join akademik.ak_sistem s on m.sistemkuliah = s.sistemkuliah
					where m.nim = ?";
			
			return $conn->GetRow($sql,array($nim));
		}
	}
?>