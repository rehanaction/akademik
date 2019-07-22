<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('mahasiswa'));
	
	class mTransferNilai extends mMahasiswa {
		const schema = 'akademik';
		const table = 'ms_mahasiswa';
		const order = 'periodemasuk desc';
		const key = 'nim';
		const label = 'transfer nilai';
		
		const keynilai = 'thnkurikulum,kodemk,kodeunit,periode,kelasmk';
		
		// mendapatkan kueri list
		function listQuery() {
			
			$sql ="select m.*,".static::schema.".f_namaperiode(m.periodemasuk) as namaperiode from ".static::table()." m
					join gate.ms_unit u on u.kodeunit=m.kodeunit";
			return $sql;
		}
		
		function getPagerDataX($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter){
			$sql = "select r.*, r.nimlama as nimlama, ".static::schema.".f_namaperiode(r.periodemasuk) as namaperiode
					from ".self::table()." r join gate.ms_unit u on r.kodeunit = u.kodeunit";
			return static::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
		}
		// mendapatkan kondisi kueri list
		
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'mhstransfer':return "m.mhstransfer = '$key'";
				case 'unit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
				
			}
		}
		
		// transfer nilai
		function transferNilai($conn,$npmlama,$npm,$kodeunit,$krs) {
			// ambil kodemk
			$a_kodemk = array();
			foreach($krs as $t_idx => $t_krs) {
				list(,$t_kodemk,,,,$t_kurbaru) = explode('|',$t_krs);
				$a_kodemk[] = $t_kurbaru.'|'.$t_kodemk;
				
				$krs[$t_idx] = rtrim($t_krs,'|'.$t_kurbaru);
			}
			
			$ok = true;
			$conn->BeginTrans();
			
			// masukkan kelas
			$sql = "insert into ".self::table('ak_kelas')." (thnkurikulum,kodemk,kodeunit,periode,kelasmk,t_updateuser,t_updatetime,t_updateip)
					select k.thnkurikulum,k.kodemk,k.kodeunit,'00001','X',".Query::logInsert()." from ".self::table('ak_kurikulum')." k
					left join ".self::table('ak_kelas')." c on c.thnkurikulum = k.thnkurikulum and c.kodemk = k.kodemk and c.kodeunit = k.kodeunit
						and c.periode = '00001' and c.kelasmk = 'X'
					where k.kodeunit = '$kodeunit' and k.thnkurikulum||'|'||k.kodemk in ('".implode("','",$a_kodemk)."') and c.kodemk is null";
			$ok = $conn->Execute($sql);
			
			// masukkan krs
			if($ok) {
				$sql = "insert into ".self::table('ak_krs')." (thnkurikulum,kodemk,kodeunit,periode,kelasmk,nim,nnumerik,nangka,nhuruf,lulus,nilaimasuk,t_updateuser,t_updatetime,t_updateip)
						select c.thnkurikulum,k.kodemk,'$kodeunit','00001','X','$npm',k.nnumerik,k.nangka,k.nhuruf,-1,-1,".Query::logInsert()."
						from ".self::table('ak_krs')." k
						join ".self::table('ak_kurikulum')." c on c.kodemk = k.kodemk and c.kodeunit = '$kodeunit' and c.thnkurikulum||'|'||c.kodemk in ('".implode("','",$a_kodemk)."')
						where k.nim = '$npmlama' and ".static::getInCondition($krs,self::keynilai,'k');
				$ok = $conn->Execute($sql);
			}
			
			$err = $conn->ErrorNo();
			$msg = 'Transfer nilai '.($ok ? 'berhasil' : 'gagal');
			
			$conn->CommitTrans($ok);
			
			return array($err,$msg);
		}
		
		// hapus nilai
		function deleteNilai($conn,$npm,$krs) {
			Query::qDelete($conn,static::table('ak_krs'),"nim = '$npm' and ".static::getInCondition($krs,self::keynilai));
			
			return static::deleteStatus($conn);
		}
		
		// mendapatkan data mahasiswa transfer
		function getDataSingkat($conn,$npm) {
			$sql = "select b.nim, b.nama, b.kodeunit, b.periodemasuk, ub.namaunit, l.nim as nimlama, ul.namaunit as namaunitlama
					from ".self::table()." b join gate.ms_unit ub on b.kodeunit = ub.kodeunit
					left join (".self::table()." l join gate.ms_unit ul on l.kodeunit = ul.kodeunit)
					on b.nama = l.nama and b.nim <> l.nim and l.statusmhs = 'T'
					where b.nim = '$npm'";
			$row = $conn->GetRow($sql);
			
			$row['kurikulum'] = static::getKurikulum($conn,$row['periodemasuk'],$row['kodeunit']);
			
			return $row;
		}
		
		// mendapatkan data lama
		function getDataNilai($conn,$npm,$unitcek='') {
			if(!empty($unitcek)) {
				$sql = "select v.*, k.thnkurikulum as thnkurikulumbaru from ".self::table('v_nilai')." v
						left join
							(select kodemk, max(thnkurikulum) as thnkurikulum from ".self::table('ak_kurikulum')."
								where kodeunit = '$unitcek' group by kodemk) k
							on v.kodemk = k.kodemk
						where v.nim = '$npm' order by v.namamk";
			}
			else
				$sql = "select * from ".self::table('v_nilai')." where nim = '$npm' order by namamk";
			
			return $conn->GetArray($sql);
		}
		
		// mendapatkan mk nilai
		function mkNilai($conn,$npm) {
			$sql = "select kodemk, kodemk||' - '||namamk from ".self::table('v_nilai')." where nim = '$npm' order by namamk";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// mendapatkan mk kurikulum
		function mkKurikulum($conn,$kurikulum,$kodeunit) {
			$sql = "select kodemk, kodemk||' - '||namamk from ".self::table('ak_kurikulum')." where kodeunit='$kodeunit' and thnkurikulum='$kurikulum' order by namamk";
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>
