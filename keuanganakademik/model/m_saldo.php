<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mSaldo extends mModel {
		const schema = 'h2h';
		const order = 'm.nim';
		
		function listQuery() {
			$periode = Modul::getRequest('PERIODE'); // Akademik::getPeriode();
			
			$sql = "select t.*, m.nama, m.statusmhs, u.namaunit
					from (
						select z.nim, z.totaltagihan, z.totalbayar, z.totaldenda, z.totalhutang,
							sum(coalesce(a.nominaldeposit,0)-coalesce(a.nominalpakai,0)) as totaldeposit,
							z.totaltagihan+z.totaldenda+z.totalhutang-sum(coalesce(a.nominaldeposit,0)-coalesce(a.nominalpakai,0)) as saldo
						from (
							select x.nim, x.totaltagihan, x.totalbayar, x.totaldenda,
								sum(coalesce(y.nominaltagihan,0)+coalesce(y.denda,0)-coalesce(y.potongan,0)-coalesce(y.nominalbayar,0)) as totalhutang
							from (
								select nim,
									sum(nominaltagihan+denda-potongan-nominalbayar) as totaltagihan,
									sum(nominalbayar) as totalbayar, sum(denda) as totaldenda
								from ".static::table('ke_tagihan')."
								where periode = '$periode' and tgltagihan <= current_date and flaglunas <> 'L'
								group by nim
							) x
							left join ".static::table('ke_tagihan')." y on x.nim = y.nim and y.flaglunas <> 'L' and y.periode < '$periode'
							group by x.nim, x.totaltagihan, x.totalbayar, x.totaldenda
						) z
						left join ".static::table('ke_deposit')." a on z.nim = a.nim and a.status = '-1' and a.tgldeposit <= current_date and a.nominaldeposit-a.nominalpakai > 0
						group by z.nim, z.totaltagihan, z.totalbayar, z.totaldenda, z.totalhutang
					) t
					join akademik.ms_mahasiswa m on t.nim = m.nim
					left join gate.ms_unit u on m.kodeunit = u.kodeunit";
			
			return $sql;
		}
		
		function getListFilter($col,$key) {
			switch($col) {
				case 'kodeunit': return "m.kodeunit = '$key'";
				case 'jalurpenerimaan': return "m.jalurpenerimaan = '$key'";
				case 'sistemkuliah': return "m.sistemkuliah = '$key'";
			}
		}
	}
?>
