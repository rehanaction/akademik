<?php
	class mAkademik {
		function getInfoMahasiswa($conn,$nim) {
			$sql = "select m.nama, coalesce(u.namasingkat,u.namaunit) as namaunit,
					m.jalurpenerimaan, m.gelombang
					from h2h.v_mhspendaftar m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					where m.nim = ?";
			
			return $conn->GetRow($sql,array($nim));
		}
	}
?>