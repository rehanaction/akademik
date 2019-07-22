<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKRS extends mModel {
		const schema = 'akademik';
		const table = 'ak_krs';
		const order = 'nim,periode asc,kodemk';
		const key = 'thnkurikulum,kodemk,kodeunit,periode,kelasmk,nim';
		const label = 'KRS';
		
		// hapus data
		function deleteByMhs($conn,$kurikulum,$periode,$kodeunit,$kodemk,$kelasmk,$nim) {
			$sql = "select ".static::schema.".f_dropkrs('$kurikulum','$periode','$kodeunit','$kodemk','$kelasmk','$nim')";
			$conn->Execute($sql);
			
			if($conn->ErrorNo() == 0) {
				$err = false;
				$msg = 'Penghapusan data '.self::label.' berhasil';
			}
			else {
				$err = true;
				$msg = $conn->ErrorMsg();
			}
			
			return array($err,$msg);
		}
		
		function cekKresKrs($conn,$kurikulum,$periode,$kodeunit,$kodemk,$kelasmk,$nim,$kelompok){
			//ambil mk yg sudah diambil untuk bahan cek kres
			$a_mk=self::getMkDetail($conn,$nim,$periode);
			$arr_rowmk=array();
			foreach($a_mk as $val){
				foreach($val as $key=>$val2){
					$arr_rowmk[$key]=array('tglpertemuan'=>$val2['tglpertemuan'],'jammulai'=>$val2['jammulai'],'jamselesai'=>$val2['jamselesai']);
				}
			}
			
			//mulai cek kres
			require_once(Route::getModelPath('detailkelas'));
			$keyjadwal=$kurikulum.'|'.$kodemk.'|'.$kodeunit.'|'.$periode.'|'.$kelasmk;
			$jadwal=mDetailKelas::getArrCekKres($conn,$keyjadwal,'thnkurikulum,kodemk,kodeunit,periode,kelasmk',$kelompok);
			
			$kres=false;
			foreach($jadwal as $key => $row_jadwal){
				if($arr_rowmk[$row_jadwal['tglpertemuan']]){
					$tgl=$row_jadwal['tglpertemuan'];
					$start=$row_jadwal['jammulai'];
					$end=$row_jadwal['jamselesai'];
					$cek=mDetailKelas::cekKresJadwalMhs($conn,$periode,$kodeunit,$tgl,$nim,$start,$end);
					if(!empty($cek['namamk'])){
						$kres=true;
						$msg=$nim.'=> Kres dengan '.$cek['kodemk'].' '.$cek['namamk'].'('.$cek['kelasmk'].') tgl '.Date::indoDate($cek['tglpertemuan'],false).' '.CStr::formatJam($cek['jammulai']).'-'.CStr::formatJam($cek['jamselesai']).' Ruang '.$cek['koderuang'];
						break;
					}
				}
			}
			
			return array($kres,$msg);
		}

		// tambahan rehan krs log
		function insertLogKrs($conn,$nim,$kodemk,$kelasmk,$stat,$user)
		{
			$tgl = date("Y-m-d");
			$sql = "insert into akademik.krs_log(nim,kodemk,kelasmk,status,tgl,t_updateuser) values('$nim','$kodemk','$kelasmk','$stat','$tgl','$user')";
			//var_dump($sql);
					//die();
			return $conn->Execute($sql);
		}
				
		// insert data
		function insertByMhs($conn,$kurikulum,$periode,$kodeunit,$kodemk,$kelasmk,$nim,$kelompok) {
			
			//list($err,$msg)=self::cekKresKrs($conn,$kurikulum,$periode,$kodeunit,$kodemk,$kelasmk,$nim,$kelompok);
			
			//if(!$err){
				/*
				di-remarks tgl 1-mar-2018, berdasarkan https://esaunggul.facebook.com/groups/1245940182165463/permalink/1704875256271951/
				
				if(Akademik::isMhs())
					$sql = "select ".static::schema.".f_addkrs('$kurikulum','$periode','$kodeunit','$kodemk','$kelasmk','$nim','$kelompok')";
				else
					$sql = "select ".static::schema.".f_addkrsadm('$kurikulum','$periode','$kodeunit','$kodemk','$kelasmk','$nim','$kelompok')";
				*/
				/* begin tambah tgl 1-mar-2018, berdasarkan https://esaunggul.facebook.com/groups/1245940182165463/permalink/1704875256271951/
				*/
				if( Akademik::isPerwalianProdi() || Akademik::isAdminDAA() )
					$sql = "select ".static::schema.".f_addkrsadm('$kurikulum','$periode','$kodeunit','$kodemk','$kelasmk','$nim','$kelompok')";
				else
					$sql = "select ".static::schema.".f_addkrs('$kurikulum','$periode','$kodeunit','$kodemk','$kelasmk','$nim','$kelompok')";
				/* end */
				
				$insert=$conn->Execute($sql);
			//}
			
			if($conn->ErrorNo() == 0) {
				$err = false;
				$msg = 'Penambahan data '.self::label.' berhasil';
			}
			else {
				$err = true;
				$msg = str_replace('ERROR:','',$conn->ErrorMsg());
			}
			
			return array($err,$msg);
		}
		
		// mendapatkan data untuk download xls
		function getListXLS($conn,$kelas) {
			require_once(Route::getModelPath('kelas'));
			
			$sql = "select k.nim, m.nama
					from ".static::table()." k
					left join ".static::table('ms_mahasiswa')." m on k.nim = m.nim
					where ".mKelas::getCondition($kelas,'','k')."
					order by k.nim";
			
			return $conn->GetArray($sql);
		}

		//cek nilai uas
		function ceknilaiUas($conn,$periode,$kodemk,$nim,$kelasmk){
			$sql = "select nilaiunsur from akademik.v_ceknilai where periode='$periode' and namaunsurnilai='UAS' and kodemk='$kodemk' and nim='$nim'";
			return $conn->GetOne($sql);
		}
		
		// mendapatkan kueri data
		function dataQuery($key) {
			$sql = "select *, substr(periode,1,4) as tahun, substr(periode,5,1) as semester
					from ".static::table()." where ".static::getCondition($key);
			
			return $sql;
		}
		
		// cek ambil krs
		function isAmbil($conn,$kelas,$nim='') {
			require_once(Route::getModelPath('kelas'));
			
			if(empty($nim))
				$nim = Modul::getUserName();
			
			$sql = "select 1 from ".static::table()." where nim = '$nim' and ".mKelas::getCondition($kelas);
			$cek = $conn->GetOne($sql);
			
			if(empty($cek))
				return false;
			else
				return true;
		}
		
		// mendapatkan data per semester
		function getDataPerSemester($conn,$nim,$periodeawal='',$mundur=false,$incbelum=false,$jumping=false) {
			$sql = "select c.thnkurikulum, coalesce(k.periode,'') as periode, c.kodemk, c.namamk, c.kodeunit, c.semmk, k.kelasmk, k.nim, c.sks, k.nhuruf, k.nangka, k.dipakai, k.nilaimasuk
					from ".static::table('ak_kurikulum')." c left join ".static::table()." k on k.nim = '$nim' /*and k.nhuruf is not null
*/
						and k.thnkurikulum = c.thnkurikulum and k.kodemk = c.kodemk and k.kodeunit = c.kodeunit
						where k.nim is not null";
			if($incbelum) {
				require_once(Route::getModelPath('mahasiswa'));
				
				$sqlm = "select kodeunit, thnkurikulum as kurikulum, periodemasuk from ".static::table('ms_mahasiswa')." where ".mMahasiswa::getCondition($nim);
				$a_mhs = $conn->GetRow($sqlm);
				
				if(empty($a_mhs['periodemasuk']))
					$a_mhs['periodemasuk'] = mMahasiswa::getPeriodeMasukNIM($nim);
				
				 
				$sql .= " or (c.thnkurikulum = '".$a_mhs['kurikulum']."' and c.kodeunit = '".$a_mhs['kodeunit']."')";
			}
			if($jumping)
				$sql .= " and isjumping=-1";
			if($mundur)
				$sql .= " order by k.periode desc, k.kodemk";
			else
				$sql .= " order by k.periode desc, k.kodemk";
			$rs = $conn->Execute($sql);
			
			if(empty($periodeawal))
				$periodeawal = $rs->fields['periode'];
			
			$a_data = array();
			while($row = $rs->FetchRow()) {
				if($row['periode'] != $t_periode) {
					$t_periode = $row['periode'];
					if(empty($t_periode))
						$t_semmk = '';
					else
						$t_semmk = self::getSemester($periodeawal,$t_periode);
				}
				
				$a_data[$t_semmk][] = $row;
			}
			
			return $a_data;
		}
		
		// mendapatkan data per periode 
		function getDataPeriode($conn,$nim,$periode) {
			$sql = "select k.thnkurikulum, k.kodeunit, k.kodemk, m.namamk, k.kelasmk, m.sks, k.nhuruf, k.nangka, k.dipakai, k.nilaimasuk, k.lulus,k.kelompok_prak, CASE kl.isonline when -1 then 'Online' else 'Tatap muka' end as isonline
					from ".static::table()." k 
					join ".static::table('ak_matakuliah')." m using (thnkurikulum,kodemk) 
					join akademik.ak_kelas kl using(periode,thnkurikulum,kodeunit,kodemk,kelasmk)
					where k.nim = '$nim' and k.periode = '$periode' order by m.namamk, k.kelasmk";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}

					
		function getNilaiMasuk($conn,$nim,$periode) {
			$sql = "select nilaimasuk
					from ".static::table()."
					where nim = '$nim' and periode = '$periode' and nilaimasuk='-1' limit 1";
			$rs = $conn->Execute($sql);
			
			return true;
		}

		//mendapatkan data detail jadwal yg diambil
		function getMkDetail($conn,$nim,$periode) {
			$sql = "select j.tglpertemuan, j.jammulai, j.jamselesai
					from ".static::table()." k join ".static::table('ak_detailkelas')." j using (kodemk,kelasmk)
					where k.nim = '$nim' and k.periode = '$periode' order by j.tglpertemuan";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[][$row['tglpertemuan']] = $row;
			
			return $a_data;
		}
		// cek quisioner
		function cekQuisioner($conn,$nim,$periode,$kodemk='') {

			$sql = "select s.*, substr(periode,1,4) as tahun, substr(periode,5,1) as semester
						from ".static::table('quiz_adji')." s where periode = '$periode' ";
			$rsquiz=$conn->Execute($sql);
			$jumquiz=$rsquiz->RecordCount();
			if ( $jumquiz==0 ){
				return true;
			}

			$sqlquiz="select distinct periode, thnkurikulum, kodeunit, kodemk, kelasmk, nim, nipdosen from ".static::table('quiz_adji')." 
						where nim = '$nim' and periode = '$periode' and kodemk != 'INA028'";
			if(!empty($kodemk))
				$sqlquiz.=" and kodemk='$kodemk'";
					$rsquiz=$conn->Execute($sqlquiz);
					$jumquiz=$rsquiz->RecordCount();
			
			$sql = "select distinct k.periode, k.thnkurikulum, k.kodeunit, k.kodemk, k.kelasmk, k.nim, mj.nipdosen from ".static::table('ak_krs')." k
					join ".static::table('ak_mengajar')." mj using (periode, thnkurikulum, kodeunit, kodemk, kelasmk)
					where k.nim = '$nim' and k.periode = '$periode' and mj.tugasmengajar='-1' " ;
			if(!empty($kodemk))
				$sql.=" and k.kodemk='$kodemk'";
					$rskrs=$conn->Execute($sql);
					$jumkrs=$rskrs->RecordCount();

			if($jumquiz==$jumkrs)
				return false;
			else
				return true; //false//
		}
		
		// mendapatkan data mengulang
		function getDataMengulang($conn,$key) {
			$sql = "select k.periode, k.kodemk, c.namamk, c.sks, c.semmk, k.nhuruf
					from ".static::table()." k join ".static::table('ak_kurikulum')." c using (thnkurikulum,kodemk,kodeunit)
					where k.nim = '$key' order by k.periode";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()) {
				$t_key = $row['kodemk'];
				
				if(!empty($a_ulang[$t_key]))
					$a_data[$t_key] = $row;
				else
					$a_ulang[$t_key] = array();
				
				$a_ulang[$t_key][] = $row['periode'].' ('.$row['nhuruf'].')';
			}
			
			foreach($a_data as $t_key => $t_data)
				$a_data[$t_key]['ulang'] = $a_ulang[$t_key];
			
			return $a_data;
		}
		
		// mendapatkan data jadwal
		function getDataJadwal($conn,$hari,$nim='') {
			$periode = Akademik::getPeriode();
			if(empty($nim))
				$nim = Modul::getUserName();
			
			$sql = "select k.kodemk, m.namamk, k.nohari, k.koderuang, k.jammulai, k.jamselesai,
					k.nohari2, k.koderuang2, k.jammulai2, k.jamselesai2 from ".static::table('ak_kelas')." k
					join ".static::table('ak_matakuliah')." m using (thnkurikulum,kodemk)
					join ".static::table('ak_krs')." c using (thnkurikulum,kodemk,kodeunit,periode,kelasmk)
					where k.periode = '$periode' and c.nim = '$nim'
					and (k.nohari = '$hari' or k.nohari2 = '$hari') order by k.kodemk";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				for($i=1;$i<=2;$i++) {
					if($i == 1) $j = '';
					else $j = $i;
					
					if($row['nohari'.$j] == $hari) {
						$key = CStr::formatJam($row['jammulai'.$j],'').CStr::formatJam($row['jamselesai'.$j],'');
						
						$rowk = array();
						$rowk['jam'] = CStr::formatJam($row['jammulai'.$j]).' - '.CStr::formatJam($row['jamselesai'.$j]);
						$rowk['mk'] = $row['kodemk'].' - '.$row['namamk'];
						$rowk['koderuang'] = $row['koderuang'];
						
						$data[$key][] = $rowk;
					}
				}
			}
			
			ksort($data);
			
			return $data;
		}
		
		function getDataJadwalMingguan($conn,$nim,$a_infomhs) {
			require_once(Route::getModelPath('kelas'));
			$r_periode = Akademik::getPeriode();
			if(substr($a_infomhs['periodemasuk'],0,4)==substr($r_periodespa,0,4))
				$r_periode=Akademik::getPeriodeSpa();
			
			$sql="SELECT f.nim, k.periode, k.thnkurikulum, k.kodeunit, k.isblock, k.kodemk, m.namamk, k.kelasmk, m.sks, 
					k.nohari, akademik.f_namahari(k.nohari) AS namahari, k.jammulai, k.jamselesai, k.koderuang,
					k.nohari2, akademik.f_namahari(k.nohari2) AS namahari2, k.jammulai2, k.jamselesai2, k.koderuang2,
					k.nohari3, akademik.f_namahari(k.nohari3) AS namahari3, k.jammulai3, k.jamselesai3, k.koderuang3,
					k.nohari4, akademik.f_namahari(k.nohari4) AS namahari4, k.jammulai4, k.jamselesai4, k.koderuang4,
					case k.isonline when -1 then 'Online' else 'Tatap Muka' end as isonline
					   FROM akademik.ak_kelas k
					   JOIN akademik.ak_matakuliah m ON k.thnkurikulum::text = m.thnkurikulum::text AND k.kodemk::text = m.kodemk::text
					   JOIN akademik.ak_krs f ON f.thnkurikulum::text = k.thnkurikulum::text AND f.periode::text = k.periode::text AND f.kodeunit::text = k.kodeunit::text AND f.kodemk::text = k.kodemk::text AND f.kelasmk::text = k.kelasmk::text
					   where f.nim = '$nim' and k.periode='$r_periode'";
			$data=$conn->GetArray($sql);
			$jadwal=mKelas::getFormatPerJadwal($data);
			return $jadwal;
		}
		
		function getDataJadwalHarian($conn,$nim,$a_infomhs) {
			
			$r_periode = Akademik::getPeriode();
			$r_periodespa=Akademik::getPeriodeSpa();
			if(substr($a_infomhs['periodemasuk'],0,4)==substr($r_periodespa,0,4))
				$r_periode=Akademik::getPeriodeSpa();
			$sql = "select distinct f.nim, k.thnkurikulum, k.periode, k.kodeunit, k.kodemk, m.namamk, k.kelasmk, k.perkuliahanke, k.jeniskuliah,
			 k.statusperkuliahan, k.tglkuliah, k.nohari, k.namahari, k.koderuang, k.waktumulai, k.waktuselesai, k.nipdosen, k.nama AS namadosen,
			  k.topikkuliah, k.jumlahpeserta, k.jadwalganti, k.alasanpergantian,k.tglkuliahrealisasi,k.waktumulairealisasi,k.waktuselesairealisasi,
			  k.koderuangrealisasi,k.noharirealisasi,k.nipdosenrealisasi,
			  case k.isonline when -1 then 'Online' else 'Tatap Muka' end as isonline
				   FROM akademik.v_perkuliahan k
				   JOIN akademik.ak_krs f ON f.thnkurikulum::text = k.thnkurikulum::text AND f.periode::text = k.periode::text AND f.kodeunit::text = k.kodeunit::text AND f.kodemk::text = k.kodemk::text AND f.kelasmk::text = k.kelasmk::text
				   JOIN akademik.ak_matakuliah m ON m.thnkurikulum::text = k.thnkurikulum::text AND m.kodemk::text = k.kodemk::text
				  WHERE k.tglkuliah >= 'now'::text::date and f.nim = '$nim' and k.periode='$r_periode' order by k.tglkuliahrealisasi";
			return $conn->GetArray($sql);
		}
		function getDataJadwalUjian($conn,$nim,$a_infomhs,$jenis){
			
			$r_periode = Akademik::getPeriode();
			$r_periodespa=Akademik::getPeriodeSpa();
			//if(substr($a_infomhs['periodemasuk'],0,4)==substr($r_periodespa,0,4))
			$r_periode=Akademik::getPeriode();
			if($jenis=="uts"){
				$sql="select case when jenisujian='T' then 'UTS' else 'UAS' end as jenis_ujian,j.tglujian,j.waktumulai,j.waktuselesai,j.koderuang,j.kelompok,kr.kodemk,kr.namamk,k.kelasmk,k.kodeunit 
				from akademik.ak_jadwalujian j 
				join akademik.ak_kurikulum kr on j.kodeunit=kr.kodeunit and j.thnkurikulum=kr.thnkurikulum and j.kodemk=kr.kodemk 
				join akademik.ak_krs k on k.periode=j.periode and k.thnkurikulum=j.thnkurikulum and k.kodeunit=j.kodeunit and k.kodemk=j.kodemk and k.kelasmk=j.kelasmk 
				join akademik.ak_perwalian pw on k.nim=pw.nim and k.periode=pw.periode 
				join akademik.ak_pesertaujian pu on j.idjadwalujian=pu.idjadwalujian and k.nim=pu.nim
				where  k.periode='$r_periode' and pu.nim='$nim' and j.jenisujian='T' 
				order by j.tglujian";
			}else{
				$sql="select case when jenisujian='T' then 'UTS' else 'UAS' end as jenis_ujian,j.tglujian,j.waktumulai,j.waktuselesai,j.koderuang,j.kelompok,kr.kodemk,kr.namamk,k.kelasmk,k.kodeunit 
				from akademik.ak_jadwalujian j 
				join akademik.ak_kurikulum kr on j.kodeunit=kr.kodeunit and j.thnkurikulum=kr.thnkurikulum and j.kodemk=kr.kodemk 
				join akademik.ak_krs k on k.periode=j.periode and k.thnkurikulum=j.thnkurikulum and k.kodeunit=j.kodeunit and k.kodemk=j.kodemk and k.kelasmk=j.kelasmk 
				join akademik.ak_perwalian pw on k.nim=pw.nim and k.periode=pw.periode 
				join akademik.ak_pesertaujian pu on j.idjadwalujian=pu.idjadwalujian and k.nim=pu.nim
				where  k.periode='$r_periode' and pu.nim='$nim' and j.jenisujian='A' 
				order by j.tglujian";
			}
		
				return $conn->GetArray($sql);
		}
		function getDetailJadwal($conn,$key) {
		list($periode,$kodeunit, $thnkurikulum, $kodemk, $kelasmk )=explode('|',$key);
			$sql = "select d.periode, d.thnkurikulum, d.kodeunit, d.tglpertemuan, d.kodemk, d.kelasmk, d.pertemuan, d.nohari, d.tglpertemuan, d.jammulai, d.jamselesai, d.koderuang, d.jeniskul, k.namamk, k.namamken, k.sks 
					from ".static::table('ak_detailkelas')." d
					join akademik.ak_kurikulum k on k.kodemk=d.kodemk and k.thnkurikulum=d.thnkurikulum
					where d.kodeunit='$kodeunit' and d.kodemk='$kodemk' and d.kelasmk='$kelasmk' and d.periode='$periode' and d.thnkurikulum='$thnkurikulum'
					order by d.pertemuan";

			return $conn->GetArray($sql);
		}
		
		// mata kuliah periode
		function mkPeriode($conn,$kurikulum,$kodeunit,$periode) {
			$sql = "select k.kodemk, k.namamk||' ('||k.kodemk||') - '||k.sks||' sks' from ".static::table('ak_kurikulum')." k
					left join ".static::table('ak_kelas')." c using (thnkurikulum,kodemk)
					where k.thnkurikulum = '$kurikulum' and k.kodeunit = '$kodeunit' and c.periode = '$periode' order by k.namamk";
			return Query::arrQuery($conn,$sql);
		}
		
		// kelas mata kuliah periode
		function kelasMkPeriode($conn,$kurikulum,$kodeunit,$periode,$kodemk) {
			$sql
			 = "select kelasmk from ".static::table('ak_kelas')."
					where thnkurikulum = '$kurikulum' and kodeunit = '$kodeunit' and periode = '$periode' and kodemk = '$kodemk'
					order by kelasmk";
			if(empty(Query::arrQuery($conn,$sql))){
				$sql
				= "select kelasmk from ".static::table('ak_kelas')."
					   where thnkurikulum = '$kurikulum' and periode = '$periode' and kodemk = '$kodemk'
					   order by kelasmk";
			}
			
			return Query::arrQuery($conn,$sql);
		}
		
		// menghitung semester pengambilan mk
		function getSemester($periodedaftar,$periodeambil) {
			return ((substr($periodeambil,0,4)-substr($periodedaftar,0,4))*2) + (substr($periodeambil,-1)-substr($periodedaftar,-1)) + 1;
		}
		function getStatusKrs($conn,$unit){
			$data=$conn->GetRow("select namaunit,iskrs from gate.ms_unit where kodeunit='$unit'");
			if($data['iskrs']==1)
				return '';
			else
				return $data['namaunit'];
		}
		function krsJumpingClass($conn,$a_input,$key,$post){
			if(!empty($key)){
			$sql = "select k.*,j.kodejumping,j.nilai, substr(k.periode,1,4) as tahun, substr(k.periode,5,1) as semester
					from ".static::table()." k 
					join ".static::table('ak_jumpingclass')." j using (periode, thnkurikulum, kodeunit, kodemk, kelasmk, nim)
					where ".static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,nim','k');
			}	
			return static::getDataEdit($conn,$a_input,$r_key,$post,$sql);
		}
		function updateKelompok($conn,$keykrs,$kelompok){
			$sqlu="update ".static::table()." set kelompok_prak='$kelompok' where ".static::getCondition($keykrs);
			return $conn->Execute($sqlu);
		}
		
		function getKrsPeriodeUnit($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter){
			$sql="select m.nim,m.nama,k.kodemk,k.namamk,kl.kelasmk,kr.isikutuas from ".static::table()." kr
				join ".static::table('ak_kurikulum')." k using (thnkurikulum,kodeunit,kodemk)
				join ".static::table('ak_kelas')." kl using (thnkurikulum,kodeunit,kodemk,periode,kelasmk)
				join ".static::table('ms_mahasiswa')." m using (nim)";
				
			return static::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
		}
		function genAbsensi($conn,$r_unit,$r_periode,$r_basis){
			require_once(Route::getModelPath('setting'));
			$min_absen=mSetting::minAbsen($conn);
			$r_kurikulum=Akademik::getKurikulum();
			
			$sql="select r.thnkurikulum,r.periode,r.kodeunit,r.kodemk,r.kelasmk,r.nim,r.kelompok_prak,r.kelompok_tutor
				from ".static::table()." r";
			if(!empty($r_basis)){
				$sql.=" join ".static::table('ak_kelas')." k on k.thnkurikulum=r.thnkurikulum and k.kodeunit=r.kodeunit and k.kodemk=r.kodemk
						and k.periode=r.periode and k.kelasmk=r.kelasmk and k.sistemkuliah='$r_basis'";
			}
			$sql.=" where r.thnkurikulum='$r_kurikulum' and r.kodeunit='$r_unit' and r.periode='$r_periode'";
			$rs=$conn->Execute($sql);
			$a_false=array();
			$a_true=array();
			while($row=$rs->fetchRow()){
				$t_absenmhs = $conn->GetOne("select akademik.f_absensi(".$row['thnkurikulum'].",'".$row['periode']."','".$row['kodeunit']."','".$row['kodemk']."','".$row['kelasmk']."','".$row['nim']."','".$row['kelompok_prak']."','".$row['kelompok_tutor']."')");
				$kodemk=$row['kodemk'];
				$nim=$row['nim'];
				if($t_absenmhs < $min_absen){
					$a_false[$kodemk][]=$nim;
				}else{
					$a_true[$kodemk][]=$nim;
				}
			}
			$conn->BeginTrans();
			//ijinkan UAS
			foreach($a_true as $kodemk=>$a_nim){
				$sql="update ".static::table()." set isikutuas=-1 where isikutuas=0 and periode='$r_periode' and thnkurikulum='$r_kurikulum' and kodeunit='$r_unit'
					and kodemk='$kodemk' and nim in ('".implode("','",$a_nim)."')";
				$ok=$conn->Execute($sql);
				if(!$ok)
					break;
			}
			
			//blokir UAS
			foreach($a_false as $kodemk=>$a_nim){
				$sql="update ".static::table()." set isikutuas=0 where isikutuas=-1 and periode='$r_periode' and thnkurikulum='$r_kurikulum' and kodeunit='$r_unit'
					and kodemk='$kodemk' and nim in ('".implode("','",$a_nim)."')";
				$ok=$conn->Execute($sql);
				if(!$ok)
					break;
			}
			$conn->CommitTrans($ok);
			
			return array(!$ok,'Generate Data '.($ok?'Berhasil':'Gagal'));
		}
		function getNilaiUnsur($conn,$nim,$periode){
			$sql ="select k.kodemk, m.namamk,k.kelasmk,u.idunsurnilai,u.namaunsurnilai,u.prosentasenilai,un.nilaiunsur,k.nnumerik
					from akademik.ak_unsurpenilaian u 
					join akademik.ak_krs k using(periode, thnkurikulum, kodeunit, kodemk, kelasmk)
					join akademik.ak_unsurnilaikelas un using (periode, thnkurikulum, kodeunit, kodemk, kelasmk, nim, idunsurnilai)
					left join akademik.ak_kurikulum m using (thnkurikulum, kodeunit, kodemk)
					where k.nim = '$nim' and k.periode = '$periode' order by k.kodemk";
			$rsp=$conn->Execute($sql);
			$a_unsur = array();
			while($row = $rsp->FetchRow()){
				$idx=$row['namamk'].'('.$row['kodemk'].')';
				$a_unsur[$idx][$row['idunsurnilai']] = $row;
			}
			
			return $a_unsur;
		}
		//cek matakuliah pasar modal
		function CekMatakuliahPasarmodal($conn,$nim,$periode)
		{
			$sql = "select kodemk from akademik.ak_krs where nim='$nim' and periode='$periode' and kodemk in('AKC016','INA027')";
			return $conn->GetOne($sql);
		}
		function CekMatakuliahYIMSkripsi($conn,$nim,$periode)
		{
			$sql = "select kodemk from akademik.ak_krs where nim='$nim' and periode='$periode' and kodemk='INA029'";
			return $conn->GetOne($sql);
		}
		function CekMatakuliahYIMToefl($conn,$nim,$periode)
		{
			$sql = "select kodemk from akademik.ak_krs where nim='$nim' and periode='$periode' and kodemk='LU25'";
			return $conn->GetOne($sql);
		}
		function CekMatakuliahYIMSup($conn,$nim,$periode)
		{
			$sql = "select kodemk from akademik.ak_krs where nim='$nim' and periode='$periode' and kodemk='INA028'";
			return $conn->GetOne($sql);
		}


	}
?>
