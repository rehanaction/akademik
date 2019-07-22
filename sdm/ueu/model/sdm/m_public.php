<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPublic extends mModel {
		const schema = 'sdm';
		
		// mendapatkan kueri list
		function getPengumuman($conn) {
			$sql = "select * from ".static::table('pe_pengumuman')." 
					where current_date >= tglmulai order by tglmulai desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
				
			return $a_data;
		}	
		
		function getNotice($conn) {
			$sql = "select * from ".static::table('pe_notifikasi')." where isread='0'";
			$sql .= " and jenis = '".Modul::getRole()."'";
			$sql .= " order by tglpesan desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
				
			return $a_data;
		}	

		function getDetailPengumuman($conn, $r_key) {
			$sql = "select * from ".static::table('pe_pengumuman')." where idpengumuman=$r_key";
			
			$a_data = array();
			$a_data = $conn->GetRow($sql);
			
			return $a_data;
		}	
		
		function getDetailNotice($conn, $r_key) {
			$sql = "select * from ".static::table('pe_notifikasi')." where idpesan=$r_key";
			
			$a_data = array();
			$a_data = $conn->GetRow($sql);
			
			return $a_data;
		}	

		function listQueryPengumuman(){
			$sql = "select * from ".static::table('pe_pengumuman');
			
			return $sql;
		}
		
		/**************************************** NOTE PENSIUN *****************************************************/
		function getNotifikasiPensiun($conn,$idlogpegawai){
			$sql = "select 1 from ".static::schema()."ms_pegawai p 
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = p.idtipepeg
					where date_part('year', age(current_date,tgllahir)) > umurpensiun and tgllahir is not null";
			if(!empty($idlogpegawai))
				$sql.= " and idpegawai=$idlogpegawai";
			
			$ceNotePensiun = $conn->GetOne($sql);
			$sql ="limit 1";
			return $ceNotePensiun;
		}
		
		function listQueryPensiun(){
			$sql = "select p.idpegawai,p.nik, ".static::schema()."f_namalengkap(p.gelardepan, p.namadepan, p.namatengah, p.namabelakang, p.gelarbelakang) AS namalengkap, 
					u.namaunit,tgllahir
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_unit')." u on u.idunit = p.idunit
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = p.idtipepeg
					where DATEDIFF(YEAR,tgllahir,GETDATE()) > umurpensiun and tgllahir is not null";
			
			return $sql;
		}
		
		/*************************************** NOTE PENGAJUAN CUTI DAN DINAS ******************************************************/
		
		function getNotifikasiCuti($conn){
			$sql = "select  1 from ".static::schema()."pe_rwtcuti r
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					where r.statususulan = 'A' and substring(cast(cast(r.tglpengajuan as date) as varchar),6,2) = '".date('m')."'
					and substring(cast(cast(r.tglpengajuan as date) as varchar),1,4) = '".date('Y')."'";

			if(Modul::getRole() == 'Jab'){ //bila atasan
				$sql .= " and p.emailatasan = '".Modul::getUserEmail()."'";
			}
			$sql .= " limit 1";
			
			$cekNoteCuti = $conn->GetOne($sql);
			
			return $cekNoteCuti;
		}

		function getNotifikasiDinas($conn, $role=''){
			$sql = "select 1 from ".static::schema()."pe_rwtdinas r
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.pegditunjuk
					where substring(cast(cast(r.tglusulan as date) as varchar),6,2) = '".date('m')."'
					and substring(cast(cast(r.tglusulan as date) as varchar),1,4) = '".date('Y')."'
					and issetujukasdm is null and issetujukabagkeu is null";

			if(Modul::getRole() == 'Jab'){ //bila atasan
				$sql .= " and p.emailatasan = '".Modul::getUserEmail()."' and issetujuatasan is null";
			}else{
				$sql .= " and issetujuatasan is not null";				
			}
			$sql .= " limit 1";
			
			$cekNoteCuti = $conn->GetOne($sql);
			
			return $cekNoteCuti;
		}
		
		/*************************************** NOTE STATUS RATE HONOR ******************************************************/
		
		function getNotifikasiHonor($conn){
			$sql = "select 1 from ".static::schema()."ga_ajardosen 
					where isvalid is null";
			
			$cekNoteHonor = $conn->GetOne($sql);
			$sql .=" limit 1";
			return $cekNoteHonor;
		}

		function getKuesioner($connmutu){
			$sql = "select kodequisioner,namaquisioner from mutu.qu_quisioner where viewsim = 'HRM' and isaktif = 'Y'";

			return Query::arrQuery($connmutu,$sql);
		}
	}
?>
