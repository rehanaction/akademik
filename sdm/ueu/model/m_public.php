<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPublic extends mModel {
		const schema = 'sdm';
		
		// mendapatkan kueri list
		function getPengumuman($conn) {
			$sql = "select * from ".static::table('pe_pengumuman')." 
					where case when tglselesai is not null then now() between tglmulai and tglselesai else now() >= tglmulai end
					order by tglmulai desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
				
			return $a_data;
		}	
		
		function getNotice($conn) {
			$sql = "select * from ".static::table('pe_notifikasi')." where isread=0";
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
			$sql = "select 1 from ".static::schema()."ms_pegawai
					where date_part('year', age(tglpensiun::date,now()::date)) between 0 and 1
					and tglpensiun is not null";
			if(!empty($idlogpegawai))
				$sql.= " and idpegawai=$idlogpegawai";
			
			$sql.= " limit 1";
			
			$ceNotePensiun = $conn->GetOne($sql);
			
			return $ceNotePensiun;
		}
		
		function listQueryPensiun(){
			$sql = "select p.idpegawai,p.nik, ".static::schema()."f_namalengkap(p.gelardepan, p.namadepan, p.namatengah, p.namabelakang, p.gelarbelakang) AS namalengkap, 
					u.namaunit,tglpensiun
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_unit')." u on u.idunit = p.idunit
					where date_part('year', age(tglpensiun::date,now()::date)) between 0 and 1 and tglpensiun is not null";
			
			return $sql;
		}
		
		/*************************************** NOTE KONTRAK ******************************************************/
		function getNotifikasiHubKerja($conn,$idlogpegawai){
			$sql = "select 1 from ".static::schema()."ms_pegawai p
					left join ".static::table('pe_rwthubungankerja')." k on k.nourutrwthub = (select kk.nourutrwthub from ".static::table('pe_rwthubungankerja')." kk
						where kk.idpegawai = p.idpegawai and kk.isvalid = 'Y' and kk.idhubkerja = 'H2' order by kk.tglefektif desc limit 1)
					where k.tglberakhir is not null and sdm.f_diffbulan(now()::date,k.tglberakhir) between 0 and 3 
					and (sdm.f_diffbulan(now()::date,p.tglpensiun) > 3 or p.tglpensiun is null)";
			if(!empty($idlogpegawai))
				$sql.= " and p.idpegawai=$idlogpegawai";
			
			$sql.=" limit 1";
			$cekNoteHubKerja = $conn->GetOne($sql);
			
			return $cekNoteHubKerja;
		}
		
		function listQueryKontrak(){
			$sql = "select p.idpegawai,p.nik, ".static::schema()."f_namalengkap(p.gelardepan, p.namadepan, p.namatengah, p.namabelakang, p.gelarbelakang) AS namalengkap, 
					u.namaunit,h.hubkerja,g.idtipepeg,tglkontrak,tglberakhir,
					substring(".static::schema()."get_mkpengabdian(p.idpegawai),1,2)::int ||' Tahun ' || substring(".static::schema()."get_mkpengabdian(p.idpegawai),3,2)::int ||' Bulan' as masakerja
					from ".static::table('ms_pegawai')." p
					left join ".static::table('pe_rwthubungankerja')." k on k.nourutrwthub = (select kk.nourutrwthub from ".static::table('pe_rwthubungankerja')." kk
						where kk.idpegawai = p.idpegawai and kk.isvalid = 'Y' and kk.idhubkerja = 'H2' order by kk.tglefektif desc limit 1)
					left join ".static::table('ms_tipepeg')." g on g.idtipepeg = p.idtipepeg
					left join ".static::table('ms_unit')." u on u.idunit = p.idunit
					left join ".static::table('ms_hubkerja')." h on h.idhubkerja = k.idhubkerja
					where k.tglberakhir is not null and sdm.f_diffbulan(now()::date,tglberakhir) between 0 and 3
					and (sdm.f_diffbulan(now()::date,p.tglpensiun) > 3 or p.tglpensiun is null)";
			
			return $sql;
		}
		
		/*************************************** NOTE KENAIKAN PANGKAT ******************************************************/
		
		function getNotifikasiPangkat($conn,$idlogpegawai){
			$sql = "select 1 from ".static::schema()."pe_kpb
					where DATE_PART('year',tglkpb::date) = DATE_PART('year',now()::date) and DATE_PART('month',tglkpb::date) = DATE_PART('month',now()::date) 
					and (isvalid is null or issetuju is null)";
			
			if(!empty($idlogpegawai))
				$sql.= " and idpegawai=$idlogpegawai";
			
			$sql.=" limit 1";
			
			$cekNotePangkat = $conn->GetOne($sql);
			
			return $cekNotePangkat;
		}
		
		/*************************************** NOTE KENAIKAN GAJI ******************************************************/
		
		function getNotifikasiGaji($conn,$idlogpegawai){
			$sql = "select 1 from ".static::schema()."pe_kgb
					where DATE_PART('year',tglkgb::date) = DATE_PART('year',now()::date) and DATE_PART('month',tglkgb::date) = DATE_PART('month',now()::date) 
					and (isvalid is null or issetuju is null)";
			
			if(!empty($idlogpegawai))
				$sql.= " and idpegawai=$idlogpegawai";
			
			$sql.=" limit 1";
			
			$cekNoteGaji = $conn->GetOne($sql);
			
			return $cekNoteGaji;
		}
		
		/*************************************** NOTE PENGAJUAN CUTI ******************************************************/
		
		function getNotifikasiCuti($conn){
			$sql = "select 1 from ".static::schema()."pe_rwtcuti r
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					where (r.statususulan = 'A' or r.statususulan is null) and substring(cast(cast(r.tglpengajuan as date) as varchar),6,2) = '".date('m')."'
					and substring(cast(cast(r.tglpengajuan as date) as varchar),1,4) = '".date('Y')."'";

			if(Modul::getRole() == 'PS'){ //bila atasan
				$sql .= " and p.emailatasan = '".Modul::getUserEmail()."'";
			}
			
			$sql.=" limit 1";
			
			$cekNoteCuti = $conn->GetOne($sql);
			
			return $cekNoteCuti;
		}
	}
?>
