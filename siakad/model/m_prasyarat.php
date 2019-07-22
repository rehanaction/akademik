<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPrasyarat extends mModel {
		const schema = 'akademik';
		const table = 'ak_prasyarat';
		const order = 'p.kodeunit,k1.namamk';
		const key = 'kodeunit,thnkurikulum,kodemk1,kodemk2';
		const label = 'prasyarat matakuliah';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select p.*, k1.namamk as namamk1, k2.namamk as namamk2 from ".static::table()." p
					left join ".static::table('ak_kurikulum')." k1 on k1.thnkurikulum = p.thnkurikulum and k1.kodemk = p.kodemk1 and k1.kodeunit = p.kodeunit
					left join ".static::table('ak_kurikulum')." k2 on k2.thnkurikulum = p.thnkurikulum and k2.kodemk = p.kodemk2 and k2.kodeunit = p.kodeunit
					
					";
					
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'thnkurikulum': return "p.thnkurikulum = '$key'";
				case 'unit': return "p.kodeunit = '$key'";
				case 'matkul': return "p.kodemk1 = '$key'";
				case 'matkul': return "p.kodemk2 = '$key'";
				
			}
		}
		
		// salin prasyarat
		function copy($conn,$kodeunit,$kurasal,$kurtujuan) {
			$sql = "insert into ".static::table()." (thnkurikulum,kodeunit,kodemk1,kodemk2,nilaimin,relasi)
					select distinct '$kurtujuan'::numeric,p.kodeunit,p.kodemk1,p.kodemk2,p.nilaimin,p.relasi from ".static::table()." p
					join ".static::table('ak_kurikulum')." k on k.thnkurikulum = '$kurtujuan' and k.kodemk in (p.kodemk1,p.kodemk2) and k.kodeunit = p.kodeunit
					where p.kodeunit = '$kodeunit' and p.thnkurikulum = '$kurasal'";
			$ok = $conn->Execute($sql);
			
			if($ok)
				$msg = 'Salin '.self::label.' berhasil';
			else
				$msg = 'Salin '.self::label.' gagal';
			
			return array($err,$msg);
		}
		
		// mata kuliah kurikulum
		function mkKurikulum($conn,$kurikulum,$kodeunit) {
			$sql = "select kodemk, kodemk||' - '||namamk||' - '||sks||' sks' from ".static::table('ak_kurikulum')."
					where thnkurikulum = '$kurikulum' and kodeunit = '$kodeunit' order by kodemk";
			
			return Query::arrQuery($conn,$sql);
		}

		function mkKurikulum2($conn,$kurikulum) {
			$sql = "select kodemk, kodemk||' - '||namamk||' - '||sks||' sks' from ".static::table('ak_kurikulum')."
					where thnkurikulum = '$kurikulum' order by kodemk";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// nilai angka kurikulum
		function nAngkaKurikulum($conn,$kurikulum) {
			$sql = "select nangkasn from ".static::table('ak_skalanilai')."
					where thnkurikulum = '$kurikulum' order by nangkasn";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// relasi
		function relasi() {
			$data = array('A' => 'AND', 'O' => 'OR');
			
			return $data;
		}
		
		// syarat
		function syarat() {
			$data = array('A' => 'Lulus', 'O' => 'Ambil');
			
			return $data;
		}
	}
?>