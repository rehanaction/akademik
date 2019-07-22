<?php
	// model semua yang berhubungan riwayat
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mValidasi extends mModel {
		const schema = 'sdm';
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once($conf['gate_dir'].'model/m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
		}
		
		// mendapatkan kueri list riwayat pendidikan
		function listQueryValidasiPendidikan() {
			$sql = "select p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					r.*,case when r.kodept is not null then t.namapt else r.namainstitusi end as namainstitusipend, j.namapendidikan
					from ".self::table('pe_rwtpendidikan')." r 
					left join ".self::table('lv_jenjangpendidikan')." j on j.idpendidikan=r.idpendidikan
					left join ".self::table('ms_pt')." t on t.kodept=r.kodept
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					where (r.isvalid is null or r.isvalid = 'T')";
			
			return $sql;
		}
		
		function listQueryValidasiIS() {
			$sql = "select p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					r.*, case when r.jeniskelamin = 'P' then 'Perempuan' when r.jeniskelamin = 'P' then 'Perempuan' else '' end as jnskelamin,
					case when r.statuspasangan = 'W' then 'Wafat' when r.statuspasangan = 'H' then 'Hidup' else '' end as stspasangan
					from ".self::table('pe_istrisuami')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					where (r.isvalid is null or r.isvalid = 'T')";
			
			return $sql;
		}
		
		function listQueryValidasiAnak() {
			$sql = "select p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					r.*, case when r.jeniskelamin = 'P' then 'Perempuan' when r.jeniskelamin = 'P' then 'Perempuan' else '' end as jnskelamin
					from ".self::table('pe_anak')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					where (r.isvalid is null or r.isvalid = 'T')";
			
			return $sql;
		}
		
		//list validasi pengalaman kerja		
		function listQueryValidasiPengKerja() {
			$sql = "select r.*,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap
					from ".self::table('pe_pengalamankerja')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					where (r.isvalid is null or r.isvalid = 'T')";
			
			return $sql;
		}
				
		//list validasi organisasi
		function listQueryValidasiOrganisasi() {
			$sql = "select r.*,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap
					from ".self::table('pe_organisasi')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					where (r.isvalid is null or r.isvalid = 'T')";
			
			return $sql;
		}
		
		//list validasi penelitian
		function listQueryValidasiPenelitian() {
			$sql = "select r.*,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					o.outputpenelitian
					from ".self::table('pe_penelitian')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".self::table('lv_outputpenelitian')." o on o.kodeoutput=r.kodeoutput
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					where (r.isvalid is null or r.isvalid = 'T') and r.refpenelitian is null";
			
			return $sql;
		}
		
		//list validasi pkm
		function listQueryValidasiPKM() {
			$sql = "select r.*,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					k.namapkm
					from ".self::table('pe_pkm')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".self::table('lv_jenispkm')." k on k.kodepkm=r.kodepkm
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					where (r.isvalid is null or r.isvalid = 'T') and r.refpkm is null";
			
			return $sql;
		}
		
		//list validasi cuti
		function listQueryValidasiCuti() {
			$sql = "select r.*,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					j.jeniscuti,cast(r.lamacuti as varchar)||' hari' as lama
					from ".self::table('pe_rwtcuti')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".self::table('ms_cuti')." j on j.idjeniscuti=r.idjeniscuti
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					where (r.isvalid is null or r.isvalid = 'T')";
			
			return $sql;
		}
		
		function isDosen($conn){
			$rs = $conn->Execute("select idpegawai,idtipepeg from ".self::table('ms_pegawai')." order by idpegawai");
			
			while($row = $rs->FetchRow()){
				$a_nip[$row['idpegawai']] = ($row['idtipepeg'] == 'D' or $row['idtipepeg'] == 'AD') ? true : false;
			}
			
			return $a_nip;
		}
	}
?>
