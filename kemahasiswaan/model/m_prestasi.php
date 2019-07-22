<?php
	// model beasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPrestasi extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'ms_prestasimhs';
		const sequence = 'prestasi_mahasiswa_idprestasi_seq';
		const order = 'idprestasi desc,tglprestasi desc';
		const key = 'idprestasi';
		const label = 'prestasi';
		const uptype = 'prestasi';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select p.*, nama, namajenisprestasi, (case when isvalid='-1' then '<div align=center><img src=images/check.png></div>' end) as isvalid,
					(case when isvalid='-1' then coalesce(pp.poin,0) end) as poin
					from ".static::table()." p 
					join akademik.ms_mahasiswa m on p.nim = m.nim 
					join ".static::table('lv_jenisprestasi')." jp on p.kodejenisprestasi = jp.kodejenisprestasi
					left join ".static::table('ms_poinprestasi')." pp on p.kodejenisprestasi = pp.kodejenisprestasi and p.kodetingkatprestasi = pp.kodetingkatprestasi
					and p.kodekategoriprestasi = pp.kodekategoriprestasi and p.kodejenispeserta = pp.kodejenispeserta";
			
			return $sql;
		}
		
		// mendapatkan kolom filter list
		function getArrayListFilterCol() {
			return array('poin' => "(case when isvalid='-1' then coalesce(pp.poin,0) end)");
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			if($col == 'fromtanggal')
				return 'p.tglprestasi >= '.Query::escape(CStr::formatDate($key));
			else if($col == 'sdtanggal')
				return 'p.tglprestasi <= '.Query::escape(CStr::formatDate($key));
			else
				return parent::getListFilter($col,$key);
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select *, nama
					from ".static::table()." p
					join akademik.ms_mahasiswa m on p.nim = m.nim
					where ".static::getCondition($key);
			
			return $sql;
		}
		
		// mendapatkan nama mahasiswa
		function getNamaMahasiswa($conn,$nim) {
			$sql = "select nama from akademik.ms_mahasiswa where nim = '$nim'";
			
			$data = $conn->GetOne($sql);
			return $data;
		}
		/* @ mendapatkan data prestasi mahasiswa
		 * @ param nim string */
		function getPrestasi($conn,$key,$label='',$post='') {
			$sql = "select idprestasi, kodejenisprestasi, nim,tglprestasi, namaprestasi,kodetingkatprestasi,kodekategoriprestasi,
					isvalid,nipvalid,fileprestasi,kodejenispeserta,namaprestasien,lokasi
					from kemahasiswaan.ms_prestasimhs
					where nim = '$key' order by tglprestasi asc";
			
			return static::getDetail($conn,$sql,$label,$post);
		}	
			
		
	}
?>
