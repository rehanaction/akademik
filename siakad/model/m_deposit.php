<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mDeposit extends mModel {
		const schema = 'h2h';
		const table = 'ke_deposit';
		const order = 'm.nim, d.tgldeposit';
		const key = 'iddeposit';
		const label = 'Deposit';
		const sequence = 'ke_deposit_iddeposit_seq';
		
		function dataQuery($key) {
			$sql = "select d.*, m.nim||' - '||m.nama as nama,
					case when d.nim is null then 'p' else 'm' end||':'||coalesce(d.nim,d.nopendaftar) as nimpendaftar
					from ".static::table()." d
					join ".static::table('v_mhspendaftarall')." m on m.nim = coalesce(d.nim,d.nopendaftar)
					where ".static::getCondition($key);
			
			return $sql;
		}
		
		function listQuery() {
			$sql = "select d.*, m.nama from ".static::table()." d
					left join akademik.ms_mahasiswa m on m.nim = d.nim";
			
			return $sql;
		}
		
		function listQueryVoucher() {
			if(mAkademik::isRolePMB())
				$join = "join ".static::table('v_pendaftar')." m on m.nim = d.nopendaftar";
			else
				$join = "left join ".static::table('v_mhspendaftarall')." m on m.nim = coalesce(d.nim,d.nopendaftar)";
			
			$sql = "select d.*, m.nim as nimpendaftar, m.nama
					from ".static::table()." d $join";
			
			return $sql;
		}
		
		function listCondition() {
			$sql = "d.jenisdeposit = 'D'";
			
			return $sql;
		}
		
		function listConditionVoucher() {
			$sql = "d.jenisdeposit = 'V'";
			
			return $sql;
		}
		
		function listQueryMhsDeposit() {
			$sql = "select y.* from
					(select x.*, coalesce(sum(t.nominaltagihan),0) as nominaltagihan from
					(select d.nim, m.nama, u.namaunit,
					sum(case when d.jenisdeposit = 'D' then d.nominaldeposit-d.nominalpakai else 0 end) as nominaldeposit,
					sum(case when d.jenisdeposit = 'V' then d.nominaldeposit-d.nominalpakai else 0 end) as nominalvoucher
					from ".static::table()." d
					join akademik.ms_mahasiswa m on m.nim = d.nim
					join gate.ms_unit u on m.kodeunit = u.kodeunit
					where d.status = '-1' and d.nominaldeposit > d.nominalpakai
					and (d.tglexpired is null or d.tglexpired > current_date)
					group by d.nim, m.nama, u.namaunit) x
					left join h2h.ke_tagihan t on t.nim = x.nim and t.flaglunas in ('BB','BL') and t.tgltagihan <= current_date
					group by x.nim, x.nama, x.namaunit, x.nominaldeposit, x.nominalvoucher) y";
			
			return $sql;
		}
		
		function listConditionMhsDeposit() {
			$sql = "y.nominaltagihan > 0 and (y.nominaldeposit+y.nominalvoucher) >= y.nominaltagihan";
			
			return $sql;
		}
		
		function getArrayListFilterCol() {
			$data['nimpendaftar'] = 'm.nim';
			
			return $data;
		}
		
		function getListFilter($col,$key) {
			switch($col) {
				case 'nimpendaftar': return 'm.nim = '.Query::escape($key);
				case 'periode': return 'd.periode = '.Query::escape($key);
			}
		}
		
		function getJenisDeposit($conn,$id) {
			$sql = "select jenisdeposit from ".static::table()." where iddeposit = ".Query::escape($id);
			
			return $conn->GetOne($sql);
		}
		
		function voidVoucher($conn,$filter,$periode,$jenis=null) {
			if(!empty($jenis)) {
				$injenis = array();
				foreach($jenis as $k => $v)
					$injenis[] = $k;
				$injenis = "'".implode("','",$injenis)."'";
			}
			
			// pakai kolom beasiswa mahasiswa
			$sql = "delete from ".static::table()." v
					where v.isfromtagihan = -1 and v.nominalpakai = 0 and v.periode = ".Query::escape($periode)."
					and exists
					(
						select 1 from (".mAkademik::sqlMhsPendaftar($filter).") m where coalesce(v.nim,v.nopendaftar) = m.nim
					)";
			
			$conn->Execute($sql);
			$err = $conn->ErrorNo();
			
			return $err;
		}
		
		function generateVoucher($conn,$filter,$periode,$jenis=null) {
			if(!empty($jenis)) {
				$injenis = array();
				foreach($jenis as $k => $v)
					$injenis[] = $k;
				$injenis = "'".implode("','",$injenis)."'";
			}
			
			// di semester pendek awal tidak ada generate voucher
			$semester = substr($periode,-1);
			if($semester == '0')
				return 0;
			
			$eperiode = Query::escape($periode);
			
			// dibedakan antara mahasiswa dan pendaftar
			if(isset($filter['ispendaftar']))
				$all = false;
			else
				$all = true;
			
			// untuk mahasiswa lama atau kelas karyawan
			/* $sql = "insert into ".static::table()." (nim,nopendaftar,tgldeposit,nominaldeposit,periode,status,jenisdeposit,isfromtagihan,t_updateuser,t_updatetime,t_updateip)
					select case when m.jenisdata = 'mahasiswa' then m.nim else null end, case when m.jenisdata = 'pendaftar' then m.nim else null end,
					current_date, ".($semester == '3' ? 'm.potongansp' : 'm.potongan').", $eperiode, '-1', 'V', '-1', ".Query::logInsert()."
					from (".mAkademik::sqlMhsPendaftar($filter).") m
					left join akademik.ak_perwalian w on w.nim = m.nim and w.periode = $eperiode
					left join ".static::table()." d on coalesce(d.nim,d.nopendaftar) = m.nim and d.periode = $eperiode and d.isfromtagihan = '-1'
					where d.iddeposit is null and (m.sistemkuliah = 'P' or m.periodemasuk <> $eperiode)";
			
			if($semester == '3')
				$sql .= " and coalesce(m.potongansp,0) > 0";
			else
				$sql .= " and coalesce(m.potongan,0) > 0 and (m.potsmtawal is null or m.potsmtawal <= coalesce(w.semmhs,1))
						and (m.potsmtakhir is null or m.potsmtakhir >= coalesce(w.semmhs,1))";
			
			$conn->Execute($sql);
			$err = $conn->ErrorNo(); */
			
			$sql = "select m.nim, m.jenisdata, ".($semester == '3' ? 'm.potongansp' : 'm.potongan')." as potongan from (".mAkademik::sqlMhsPendaftar($filter).") m
					left join akademik.ak_perwalian w on w.nim = m.nim and w.periode = $eperiode
					left join ".static::table()." d on coalesce(d.nim,d.nopendaftar) = m.nim and d.periode = $eperiode and d.isfromtagihan = '-1'
					where d.iddeposit is null and (m.sistemkuliah = 'P' or m.periodemasuk <> $eperiode)";
			
			if($semester == '3')
				$sql .= " and coalesce(m.potongansp,0) > 0";
			else
				$sql .= " and coalesce(m.potongan,0) > 0 and (m.potsmtawal is null or m.potsmtawal <= coalesce(w.semmhs,1))
						and (m.potsmtakhir is null or m.potsmtakhir >= coalesce(w.semmhs,1))";
			
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()) {
				$enim = Query::escape($row['nim']);
				
				$sql = "insert into ".static::table()." (nim,nopendaftar,tgldeposit,periode,status,jenisdeposit,isfromtagihan,idtagihan,nominaldeposit,t_updateuser,t_updatetime,t_updateip)
						select ".($row['jenisdata'] == 'mahasiswa' ? $enim : 'null').", ".($row['jenisdata'] == 'pendaftar' ? $enim : 'null').",
						current_date, $eperiode, '-1', 'V', '-1', tidtagihan, tpotongan, ".Query::logInsert()."
						from ".static::table("f_recpotongan($enim,$eperiode,".(float)$row['potongan'].")");
				
				$conn->Execute($sql);
				$err = $conn->ErrorNo();
				
				if($err) break;
			}
			
			// untuk pendaftar kelas reguler
			if(!$err) {
				$sql = "select m.nim, m.potongan from (".mAkademik::sqlMhsPendaftar($filter).") m
						left join ".static::table()." d on d.nopendaftar = m.nim and d.periode = $eperiode and d.isfromtagihan = '-1'
						where d.iddeposit is null and (m.sistemkuliah = 'R' and m.jenisdata = 'pendaftar' and m.periodemasuk = $eperiode)
						and coalesce(m.potongan,0) > 0";
				$rs = $conn->Execute($sql);
				
				while($row = $rs->FetchRow()) {
					$enim = Query::escape($row['nim']);
					
					$sql = "insert into ".static::table()." (nopendaftar,tgldeposit,periode,status,jenisdeposit,isfromtagihan,idtagihan,nominaldeposit,t_updateuser,t_updatetime,t_updateip)
							select $enim, current_date, $eperiode, '-1', 'V', '-1', tidtagihan, tpotongan, ".Query::logInsert()."
							from ".static::table("f_recpotonganreg($enim,$eperiode,".(float)$row['potongan'].")");
					
					$conn->Execute($sql);
					$err = $conn->ErrorNo();
					
					if($err) break;
				}
			}
			
			return $err;
		}
	}
?>
