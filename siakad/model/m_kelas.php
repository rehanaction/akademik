<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKelas extends mModel {
		const schema = 'akademik';
		const table = 'ak_kelas';
		const order = 'periode,namamk,kelasmk';
		const key = 'thnkurikulum,kodemk,kodeunit,periode,kelasmk';
		const label = 'kelas';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select r.* from ".self::table('v_kelas3')." r join gate.ms_unit u on r.kodeunit = u.kodeunit";
			
			return $sql;
		}
		
		// Rehan - Buat Tampilan untuk print view jadwal ruangan kelas
		function listQueryBaru() {
			$sql = "select r.* from ".self::table('v_kelas3new')." r join gate.ms_unit u on r.kodeunit = u.kodeunit";
			
			return $sql;
		}
		// mendapatkan kolom filter list
		function getArrayListFilterCol() {
			$data['kodeunit'] = 'u.kodeunit';
			
			return $data;
		}
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'periode': return "periode = '$key'";
				case 'sistemkuliah': return "sistemkuliah = '$key'";
				case 'unit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
				case 'basiskampus':
					global $conn, $conf;
					require_once(Route::getModelPath('sistemkuliah'));
					$sistem = mSistemkuliah::getIdByBasisKampus($conn,modul::getBasis(),modul::getKampus());
					return "  sistemkuliah in ('".implode("','",$sistem)."') ";
					
			}
		}
		
		// mendapatkan data list
		function getListDataAbsensi($conn,$kolom,$row,$page,&$sort,$filter='') {
			$sql = "select r.* from ".self::table('v_kelasabsen')." r join gate.ms_unit u on r.kodeunit = u.kodeunit";
			
			
			return static::getPagerData($conn,$kolom,$row,$page,$sort,$filter,$sql);
		}

		// update pertemuan online
		function updatePertemuanOnline($conn,$key){
			$expKeyall = explode("|" ,$key);
			$sql = "update akademik.ak_kuliah set isonline=-1 where periode='$expKeyall[3]' and kodeunit='$expKeyall[2]' and kodemk='$expKeyall[1]' and kelasmk='$expKeyall[4]' and nipdosen='$expKeyall[10]' and perkuliahanke not in(1,7,8,15,16)";
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
		}
		// update pertemuan online
		function updateKelasPertemuanOnline($conn,$key){
			$expKeyall = explode("|" ,$key);
			$sql = "update akademik.ak_kelas set isonline=-1 where periode='$expKeyall[3]' and kodeunit='$expKeyall[2]' and kodemk='$expKeyall[1]' and kelasmk='$expKeyall[4]'";
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
		}
		
		// mendapatkan data untuk download xls
		function getListXLS($conn,$periode,$unit) {
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, k.dayatampung,
					k.nohari, k.jammulai, k.jamselesai, k.koderuang, k.nohari2, k.jammulai2, k.jamselesai2, k.koderuang2,
					akademik.f_namahari(nohari) as hari, akademik.f_namahari(nohari2) as hari2,
					substring(xmlagg((','||m.nipdosen)::xml)::character varying,2) as nipm, k.sistemkuliah
					from ".static::table()." k
					left join ".static::table('ak_mengajar')." m using (thnkurikulum,kodemk,kodeunit,periode,kelasmk)
					join gate.ms_unit u on k.kodeunit = u.kodeunit
					join gate.ms_unit a on u.infoleft >= a.infoleft and u.inforight <= a.inforight and a.kodeunit = '$unit'
					where k.periode = '$periode'
					group by k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, k.dayatampung,
					k.nohari, k.jammulai, k.jamselesai, k.koderuang, k.nohari2, k.jammulai2, k.jamselesai2, k.koderuang2
					order by k.kodeunit,k.kodemk,k.thnkurikulum,k.kelasmk";
			
			return $conn->GetArray($sql);
		}
		
		// mendapatkan data tambahan
		function getExtraRow($row) {
			$row['semester'] = substr($row['periode'],-1);
			$row['tahun'] = substr($row['periode'],0,4);
			
			return $row;
		}
		
		// mendapatkan data
		function getData($conn,$key) {
			if(!empty($key)) {
				$sql = static::dataQuery($key);
				$row = $conn->GetRow($sql);
				
				$row['semester'] = substr($row['periode'],-1);
				$row['tahun'] = substr($row['periode'],0,4);
				
				return $row;
			}
			else
				return array();
		}
		
		// salin kurikulum
		function copy($conn,$kodeunit,$periodeasal,$periodetujuan) {
			// masukkan kelas
			$sql = "insert into ".static::table()." (thnkurikulum,kodemk,kodeunit,periode,kelasmk,dayatampung,mengulang,
						nohari,jammulai,jamselesai,koderuang,nohari2,jammulai2,jamselesai2,koderuang2)
					select k.thnkurikulum,k.kodemk,k.kodeunit,'$periodetujuan',k.kelasmk,k.dayatampung,k.mengulang,
						k.nohari,k.jammulai,k.jamselesai,k.koderuang,k.nohari2,k.jammulai2,k.jamselesai2,k.koderuang2 from ".static::table()." k
					left join ".static::table()." kn on k.thnkurikulum = kn.thnkurikulum and k.kodemk = kn.kodemk
						and k.kodeunit = kn.kodeunit and k.kelasmk = kn.kelasmk and k.periode = '$periodetujuan'
					where kn.kodemk is null and k.kodeunit = '$kodeunit' and k.periode = '$periodeasal'";
			$ok = $conn->Execute($sql);
			
			$err = $conn->ErrorNo();
			if($ok)
				$msg = 'Salin '.self::label.' berhasil';
			else
				$msg = 'Salin '.self::label.' gagal';
			
			return array($err,$msg);
		}
		
		// hapus data
		function deleteMengajar($conn,$key,$nip=false) {
			$cond = static::getCondition($key);
			if($nip !== false)
				$cond .= " and nipdosen = '$nip'";
			
			Query::qDelete($conn,static::table('ak_mengajar'),$cond);
			
			return static::deleteStatus($conn,'dosen pengajar');
		}
		// hapus MKU
		function deleteMku($conn,$key,$unit=false) {
			$cond = static::getCondition($key);
			if($unit !== false)
				$cond .= " and unitmku = '$unit'";
			
			Query::qDelete($conn,static::table('ak_pesertamku'),$cond);
			
			return static::deleteStatus($conn,'Prodi Peserta MKU');
		}
		
		// insert record
		function insertRecordMengajar($conn,$record,$key) {
			$reckey = static::getKeyRecord($key);
			$record += $reckey;
			
			Query::recInsert($conn,$record,static::table('ak_mengajar'));
			
			return static::insertStatus($conn,$kosong,'dosen pengajar','ak_mengajar');
		}
		// insert record
		function insertRecordMku($conn,$record,$key) {
			$reckey = static::getKeyRecord($key);
			$record += $reckey;
			
			Query::recInsert($conn,$record,static::table('ak_pesertamku'));
			
			return static::insertStatus($conn,$kosong,'Prodi Peserta MKU','ak_pesertamku');
		}
		// udate record
		function updateRecordMengajar($conn,$record,$key) {
			
			Query::recUpdate($conn,$record,static::table('ak_mengajar'),static::getCondition($key)." and ispjmk=1");
			
			return static::updateStatus($conn,$kosong,'dosen pengajar','ak_mengajar');
		}
		
		// update record
		function updateRecordPeserta($conn,$record,$key,$status=false) {
			$err = Query::recUpdate($conn,$record,static::table('ak_krs'),static::getCondition($key));
		
			if($status)
				return static::updateStatus($conn);
			else
				return $err;
		}
		
		// mendapatkan informasi singkat KELAS TEORI
		function getDataSingkat($conn,$key,$pengajar=true,$nip='') {
			$sql = "select k.thnkurikulum, k.kodemk, c.namamk, k.kodeunit, k.periode, k.kelasmk, k.koderuang,
					k.nohari, k.jammulai, k.jamselesai, k.nohari2, k.jammulai2, k.jamselesai2, k.nilaimasuk, k.kuncinilai,
					k.dayatampung,k.jumlahpeserta,k.isonline
					from ".static::table()." k join ".static::table('ak_kurikulum')." c using (thnkurikulum,kodemk,kodeunit)
					where ".static::getCondition($key,null,'k');
			$row = $conn->GetRow($sql);
			
			$row['jadwal'] = Date::indoDay($row['nohari']).', '.CStr::formatjam($row['jammulai']).' - '.CStr::formatjam($row['jamselesai']);
			$row['jadwal2'] = Date::indoDay($row['nohari2']).', '.CStr::formatjam($row['jammulai2']).' - '.CStr::formatjam($row['jamselesai2']);
			
			// ambil data pengajar
			if($pengajar) {
				$sql = "select p.idpegawai::text as nipdosen, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as nama from ".static::table('ak_mengajar')." a
						join sdm.ms_pegawai p on a.nipdosen::text = p.idpegawai::text
						where ".static::getCondition($key,null,'a')." order by a.nipdosen";
				$rs = $conn->Execute($sql);
				
				$a_ajar = array();
				while($rowa = $rs->FetchRow())
					$a_ajar[] = $rowa['nama'];
				
				$row['pengajar'] = implode('<br>',$a_ajar);
			}
			if(!empty($nip)){
				$sql = "select idpegawai::text as nik, akademik.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as nama 
						from sdm.ms_pegawai where nik='$nip' or idpegawai::text='$nip'";
				$rs=$conn->GetRow($sql);
				$row['pengajar'] =$rs['nama'];
			}
			return $row;
		}
		
		
		// mendapatkan informasi singkat KELAS Prakt
		function getDataSingkatPrakt($conn,$key,$pengajar=true,$nip='') {
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, k.koderuang,
					k.nohari, k.jammulai, k.jamselesai, c.namamk
					from ".static::table('ak_kelaspraktikum')." k join ".static::table('ak_kurikulum')." c using (thnkurikulum,kodemk,kodeunit)
					where ".static::getCondition($key,null,'k');
			$row = $conn->GetRow($sql);
			
			$row['jadwal'] = Date::indoDay($row['nohari']).', '.CStr::formatjam($row['jammulai']).' - '.CStr::formatjam($row['jamselesai']);
			//$row['jadwal2'] = Date::indoDay($row['nohari2']).', '.CStr::formatjam($row['jammulai2']).' - '.CStr::formatjam($row['jamselesai2']);
				
			// ambil data pengajar
			if($pengajar) {
				$sql = "select p.idpegawai::text as nipdosen, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as nama from ".static::table('ak_mengajar')." a
						join sdm.ms_pegawai p on a.nipdosen::text = p.idpegawai::text
						where ".static::getCondition($key,null,'a')." order by a.nipdosen";
				$rs = $conn->Execute($sql);
				
				$a_ajar = array();
				while($rowa = $rs->FetchRow())
					$a_ajar[] = $rowa['nama'].' ('.$rowa['nipdosen'].')';
				
				$row['pengajar'] = implode('<br>',$a_ajar);
			}
			if(!empty($nip)){
				$sql = "select idpegawai::text as nik, akademik.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as nama 
						from sdm.ms_pegawai where nik='$nip' or idpegawai::text='$nip'";
				$rs=$conn->GetRow($sql);
				$row['pengajar'] =$rs['nama']." (".$rs['nik'].")";
			}
			return $row;
		}
		    

		// mendapatkan kelas yang ditawarkan
		function getDataPeriode($conn,$periode,$kurikulum,$kodeunit,$padanan=false,$kelasmk='',$infoMhs=array()) {
			$mhs = Akademik::isMhs();
			$dosen = Akademik::isDosen();
			$admin = Akademik::isAdmin();
			
			if($infoMhs) {
				if ($infoMhs['sistemkuliah'] == "R") {
					$basis = "and k.sistemkuliah = 'R'";
				}
				//$basis = $infoMhs['sistemkuliah'];
				$transfer = $infoMhs['mhstransfer'];
				$angkatan = $infoMhs['periodemasuk'];
				$semesterkrs = $infoMhs['semesterkrs'];
			}
			
			if($mhs and !$transfer) {
				$ceksmt = true;
				if($semesterkrs%2 == 0)
					$batasan = ' and c.semmk%2 = 0'; // untuk genap
				else
					$batasan = ' and c.semmk%2 <> 0'; // untuk ganjil
			}
			else {
				$ceksmt = false;
				$batasan = '';
			}
			
			/* $sql = "select distinct k.thnkurikulum, k.kodemk, k.kodeunit, k.kelasmk, c.namamk, c.sks, c.semmk,c.semmk_old, k.koderuang,
						k.nohari, k.jammulai, k.jamselesai, 
						k.nohari2, k.jammulai2, k.jamselesai2,
						k.nohari3, k.jammulai3, k.jamselesai3,
						k.nohari4, k.jammulai4, k.jamselesai4,
						k.dayatampung,k.jumlahpeserta
					from ".static::table()." k join ".static::table('ak_kurikulum')." c using (thnkurikulum,kodemk,kodeunit)
					left join ".static::table('ak_ekivaturan')." e on e.tahunkurikulumbaru = k.thnkurikulum and e.kodemkbaru = k.kodemk
						and e.kodeunitbaru = k.kodeunit and e.thnkurikulum = '$kurikulum'
					where  k.kodeunit = '$kodeunit' and 
						k.periode = '$periode'  and (k.thnkurikulum = '$kurikulum'".
						($padanan ? " or e.kodemkbaru is not null" : '').")".(!empty($kelasmk)?" and k.kelasmk='$kelasmk'":"").
						(($mhs or $dosen)?" and k.sistemkuliah='$basis'" :'').($ceksmt?" and c.semmk<=$semesterkrs" :'')." $batasan
					union
					select distinct k.thnkurikulum, k.kodemk, kl.kodeunit, k.kelasmk, c.namamk, c.sks, c.semmk,c.semmk_old, kl.koderuang, 
						kl.nohari, kl.jammulai,kl.jamselesai, 
						kl.nohari2, kl.jammulai2, kl.jamselesai2,
						kl.nohari3, kl.jammulai3, kl.jamselesai3,
						kl.nohari4, kl.jammulai4, kl.jamselesai4,
						kl.dayatampung,kl.jumlahpeserta  
					from ".static::table('ak_pesertamku')." k 
					join ".static::table()." kl using (kodeunit,periode,thnkurikulum,kodemk,kelasmk)
					join ".static::table('ak_kurikulum')." c on c.thnkurikulum=k.thnkurikulum and c.kodemk=k.kodemk and c.kodeunit=k.unitmku 
					where k.unitmku='$kodeunit' and k.periode = '$periode' and k.thnkurikulum = '$kurikulum' ".
					(($mhs or $dosen)?" and kl.sistemkuliah='$basis'" :'').($ceksmt?" and c.semmk<=$semesterkrs" :"").(!empty($kelasmk)?" and kl.kelasmk='$kelasmk'":"")." $batasan
					order by namamk, kelasmk"; */
			
			// cek: ekivalensi
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.kelasmk, c.namamk, c.sks, c.semmk, c.semmk_old, k.koderuang,
					k.nohari, k.jammulai, k.jamselesai, 
					k.nohari2, k.jammulai2, k.jamselesai2,
					k.nohari3, k.jammulai3, k.jamselesai3,
					k.nohari4, k.jammulai4, k.jamselesai4,
					k.dayatampung, k.jumlahpeserta
					from ".static::table()." k
					join ".static::table('ak_kurikulum')." c using (thnkurikulum,kodemk,kodeunit)
					left join ".static::table('ak_pesertamku')." p using (kodeunit,periode,thnkurikulum,kodemk,kelasmk)
					where (p.unitmku = ".Query::escape($kodeunit)." or p.unitmku is null) and (k.kodeunit = ".Query::escape($kodeunit)." or p.unitmku is not null)
					and k.periode = ".Query::escape($periode)." and k.thnkurikulum = ".Query::escape($kurikulum)."
					".(empty($kelasmk) ? '' : " and k.kelasmk = ".Query::escape($kelasmk))."			
					".(($mhs or $dosen) ? $basis : '')."

					order by c.namamk, k.kelasmk";
			
			return $conn->GetArray($sql);
		}
		
		// mendapatkan kelas yang ditawarkan untuk semester ganjil + genap
		function getDataPeriodeTh($conn,$periode,$kurikulum,$kodeunit,$padanan=false) {
			$sql_in="select distinct(kodemk) from ".static::table()." k join ".static::table('ak_kurikulum')." c using (thnkurikulum,kodemk,kodeunit) 
						where (k.periode = '".substr($periode,0,4)."1' or k.periode = '".substr($periode,0,4)."2') and k.kodeunit = '$kodeunit' and (k.thnkurikulum = '$kurikulum'".
						($padanan ? " or e.kodemkbaru is not null" : '').")";
			$sql = "select distinct k.thnkurikulum, k.kodemk, k.kodeunit, k.kelasmk, c.namamk, c.sks, c.semmk, k.koderuang,
						k.nohari, k.jammulai, k.jamselesai, k.nohari2, k.jammulai2, k.jamselesai2
					from ".static::table()." k join ".static::table('ak_kurikulum')." c using (thnkurikulum,kodemk,kodeunit)
					left join ".static::table('ak_ekivaturan')." e on e.tahunkurikulumbaru = k.thnkurikulum and e.kodemkbaru = k.kodemk
						and e.kodeunitbaru = k.kodeunit and e.thnkurikulum = '$kurikulum'
					where (k.periode = '".substr($periode,0,4)."1' or k.periode = '".substr($periode,0,4)."2') and k.kodemk in ($sql_in) and (k.thnkurikulum = '$kurikulum'".
						($padanan ? " or e.kodemkbaru is not null" : '').")
					order by c.namamk, k.kelasmk";
			
			return $conn->GetArray($sql);
		}
		
		// mendapatkan data peserta
		function getDataPeserta($conn,$key,$kelprak='') {
			list($thnkurikulum,$kodemk,$kodeunit,$periode,$kelasmk)=explode('|',$key);
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, k.nim,k.kelompok_tutor,k.kelompok_prak,p.isuts,p.isuas,
					m.nama, k.nnumerik, k.nangka, k.nhuruf, k.nilaimasuk, k.lulus, k.dipakai, k.nremidi, k.nhurufremidi,k.isikututs,k.isikutuas
					from ".static::table('ak_krs')." k
					join ".static::table('ms_mahasiswa')." m on k.nim = m.nim
					left join ".static::table('ak_perwalian')." p on m.nim=p.nim and k.periode = k.periode and p.periode='$periode' 
					where ".static::getCondition($key,null,'k')." ";// and coalesce(p.frsdisetujui, 0) <>0 ";
			if(!empty($kelprak))
				$sql.=" and k.kelompok_prak='$kelprak'";

			$sql.=" order by k.nim";
			
			return $conn->GetArray($sql);
		}

		function getDataPeserta2($conn,$key,$kelprak='') {
			list($thnkurikulum,$kodemk,$kodeunit,$periode,$kelasmk)=explode('|',$key);
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, k.nim,k.kelompok_tutor,k.kelompok_prak,p.isuts,p.isuas,
					m.nama, k.nnumerik, k.nangka, k.nhuruf, k.nilaimasuk, k.lulus, k.dipakai, k.nremidi, k.nhurufremidi,k.isikututs,k.isikutuas
					from ".static::table('ak_krs')." k
					join ".static::table('ms_mahasiswa')." m on k.nim = m.nim
					left join ".static::table('ak_perwalian')." p on m.nim=p.nim and k.periode = k.periode and p.periode='$periode' 
					where ".static::getCondition($key,null,'k')." ";// and coalesce(p.frsdisetujui, 0) <>0 ";
			if(!empty($kelprak))
				$sql.=" and k.kelompok_prak='$kelprak'";

			$sql.=" and p.isuts='-1' order by k.nim";
			
			return $conn->GetArray($sql);
		}

		function getDataPesertaBelumBayar($conn,$key,$kelprak='') {
			list($thnkurikulum,$kodemk,$kodeunit,$periode,$kelasmk)=explode('|',$key);
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, k.nim,k.kelompok_tutor,k.kelompok_prak,p.isuts,p.isuas,
					m.nama, k.nnumerik, k.nangka, k.nhuruf, k.nilaimasuk, k.lulus, k.dipakai, k.nremidi, k.nhurufremidi,k.isikututs,k.isikutuas
					from ".static::table('ak_krs')." k
					join ".static::table('ms_mahasiswa')." m on k.nim = m.nim
					left join ".static::table('ak_perwalian')." p on m.nim=p.nim and k.periode = k.periode and p.periode='$periode' 
					where ".static::getCondition($key,null,'k')." ";// and coalesce(p.frsdisetujui, 0) <>0 ";
			if(!empty($kelprak))
				$sql.=" and k.kelompok_prak='$kelprak'";

			$sql.="and p.isuts='0' order by k.nim";
			
			return $conn->GetArray($sql);
		}
		
		function getDataAbsenPeserta($conn,$key) {
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, k.nim,
					m.nama, k.nnumerik, k.nangka, k.nhuruf, k.lulus, k.dipakai,
					e.bulan1, e.bulan2, e.bulan3, e.bulan4, e.bulan5, e.bulan6, e.uts, e.uas
					from ".static::table('ak_krs')." k
					left join ".static::table('ak_evalmhs')." e using (thnkurikulum,kodemk,kodeunit,periode,kelasmk,nim)
					join ".static::table('ms_mahasiswa')." m on k.nim = m.nim
					where ".static::getCondition($key,null,'k')." order by k.nim";
			
			return $conn->GetArray($sql);
		}
		
		function getDataAbsenNilaiPeserta($conn,$key) {
			$sql = "select * from ".static::table('r_absennilaimhs')."
					where ".static::getCondition($key)." order by nim";
			
			return $conn->GetArray($sql);
		}
		
		// mendapatkan data kelas pararel
		function getDataPararel($conn,$key) {
			$sql = "select k.kelasmk, k.dayatampung, k.jumlahpeserta from ".static::table('ak_kelas')." k
					where ".static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode','k')."
					order by k.kelasmk";
			
			return $conn->GetArray($sql);
		}
		// mendapatkan data kelas pararel
		function getDataPararel2($conn,$key) {
			list($thnkurikulum,$kodemk,$kodeunit,$periode,$prodimhs)=explode('|',$key);
			$sql = "select k.kelasmk,k.dayatampung,k.jumlahpeserta from ".static::table('ak_kelas')." k
					join ".static::table('ak_pesertamku')." p using(periode, thnkurikulum, kodeunit, kodemk, kelasmk)
					where  k.thnkurikulum = '$thnkurikulum' and k.kodemk = '$kodemk' and k.periode = '$periode' and p.unitmku='$prodimhs'";
			$sql.=" order by k.kelasmk desc";
			
			return $conn->GetArray($sql);
		}
		
		// mendapatkan jadwal
		function getFormatJadwal($kelas) {
			$a_data = array();
			foreach($kelas as $row) {
				$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['kelasmk'];
				
				$t_data = array();
				for($i=1;$i<=4;$i++) {
					if($i == 1)
						$j = '';
					else
						$j = $i;
					
					if(!empty($row['nohari'.$j]))
						$t_data[] = Date::indoDay($row['nohari'.$j]).', '.CStr::formatJam($row['jammulai'.$j]).' - '.CStr::formatJam($row['jamselesai'.$j]);
				}
				
				if(!empty($t_data))
					$a_data[$t_key] = implode('<br>',$t_data);
			}
			
			return $a_data;
		}
		
		// mendapatkan kelas yang ditawarkan per semester mk
		function getFormatPerSemester($kelas) {
			$a_data = array();
			foreach($kelas as $row)
				//$a_data[$row['semmk']][] = $row;
				$a_data[$row['semmk_old']][] = $row;
			
			ksort($a_data);
			
			return $a_data;
		}
		
		// mendapatkan kelas yang ditawarkan per jadwal mk
		function getFormatPerJadwal($kelas) {
			//print_r($kelas);die();
			$a_data = array();
			foreach($kelas as $row) {
				
				for($i=1;$i<=4;$i++) {
					if($i == 1)
						$j = '';
					else
						$j = $i;
					
					if(!empty($row['nohari'.$j])) {
						$row['jammulai'] = CStr::formatJam($row['jammulai'.$j]);
						$row['jamselesai'] = CStr::formatJam($row['jamselesai'.$j]);
						
						$t_key = $row['nohari'.$j].'|'.$row['jammulai'].'|'.$row['jamselesai'];
						
						$a_data[$t_key][] = $row;
					}
				}
			}
			
			ksort($a_data);
			//print_r($a_data);
			$a_datac = array();
			foreach($a_data as $t_key => $t_data) {
				list($t_no) = explode('|',$t_key);
				
				foreach($t_data as $row)
					$a_datac[$t_no][] = $row;
			}
			
			return $a_datac;
		}
		
		// monitoring ruang
		function getMonitoringRuang($conn,$kodeunit, $periode,$hari,$waktumin,$waktumax) {
			// mendapatkan ruang
			$sql = "select koderuang from ".static::table('ms_ruang')." order by koderuang";
			$rs = $conn->Execute($sql);
			
			//info left dan right
			$sql_unit="select infoleft, inforight from gate.ms_unit where kodeunit='$kodeunit'";
			$unit=$conn->Execute($sql_unit);			
			$u_row = $unit->FetchRow();
			
			$a_jadwal = array();
			while($row = $rs->FetchRow())
				$a_jadwal[$row['koderuang']] = array();
			
			// mendapatkan jadwal
			$sql = "select p.namadepan,p.gelardepan,p.namatengah,p.namabelakang,p.gelarbelakang,mu.namaunit,k.kodemk,namamk,kelasmk,nohari,koderuang,jammulai,jamselesai,nohari2,koderuang2,jammulai2,jamselesai2 
					from ".static::table()." k join ".static::table('ak_matakuliah')." m using (thnkurikulum,kodemk)
					left join akademik.ak_mengajar am using (thnkurikulum,kodemk,kelasmk,periode,kodeunit)
					left join sdm.ms_pegawai p on p.idpegawai::text=am.nipdosen::text
					left join gate.ms_unit mu on mu.kodeunit = k.kodeunit
					left join gate.ms_unit u on u.kodeunit = k.kodeunit
					where periode = '$periode' and '$hari' in (nohari,nohari2) AND 
						  u.infoleft>='".$u_row['infoleft']."' AND u.inforight<='".$u_row['inforight']."'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()) {
				// select * from akademik.ak_mengajar where kodeunit='110420' and periode='20122' and kelasmk='B' and kodemk='AIK101' and ispjmk=1
				for($i=1;$i<=2;$i++) {
					if($i == 1) $j = '';
					else $j = $i;
					
					if($row['nohari'.$j] == $hari) {
						$t_jadwal = array();
						$t_jadwal['jammulai'] = trim($row['jammulai'.$j]);
						$t_jadwal['jamselesai'] = trim($row['jamselesai'.$j]);
						$t_jadwal['kodemk'] = $row['kodemk'];
						$t_jadwal['namamk'] = $row['namamk'];
						$t_jadwal['kelasmk'] = $row['kelasmk'];
						$t_jadwal['namaunit'] = $row['namaunit'];
						$t_jadwal['namadosen'] = $row['gelardepan'].' '.$row['namadepan'].' '.$row['namatengah'].' '.$row['namabelakang'].', '.$row['gelarbelakang'];
						
						// hanya yang lengkap
						if(empty($t_jadwal['jammulai']) or empty($t_jadwal['jamselesai']))
							continue;
						
						$a_jadwal[$row['koderuang'.$j]][$t_jadwal['jammulai']][] = $t_jadwal;
					}
				}
			}
			
			// diurutkan dulu
			foreach($a_jadwal as $t_ruang => $t_jadwal)
				ksort($a_jadwal[$t_ruang]);
			
			$a_bentrok = array();
			foreach($a_jadwal as $t_ruang => $t_mjadwal) {
				$t_lmulai = $waktumin;
				
				if(!empty($t_mjadwal)) {
					foreach($t_mjadwal as $t_ajadwal) {
						foreach($t_ajadwal as $t_jadwal) {
							$t_mulai = $t_jadwal['jammulai'];
							$t_selesai = $t_jadwal['jamselesai'];
							
							if($t_mulai > $t_selesai) {
								$t_temp = $t_mulai;
								$t_mulai = $t_selesai;
								$t_selesai = $t_temp;
							}
							
							// tertumpuk jadwal lain
							if($t_selesai <= $t_lmulai and !empty($t_truang['id'])) {
								$a_bentrok[$t_truang['id']] = true;
								continue;
							}
							
							// buat jadwal kosong
							if($t_mulai > $t_lmulai) {
								$t_truang = array();
								$t_truang['mulai'] = $t_lmulai;
								$t_truang['selesai'] = $t_mulai;
								$t_truang['lebar'] = Date::lamaMenit($t_lmulai,$t_mulai);
								$t_truang['status'] = false;
								
								$a_truang[$t_ruang][] = $t_truang;
								
								$t_lmulai = $t_mulai;
							}
							else if($t_mulai < $t_lmulai) {
								if($t_selesai < $t_lmulai)
									continue;
								else
									$t_mulai = $t_lmulai;
							}
							
							if($t_selesai > $waktumax)
								$t_selesai = $waktumax;
							
							$t_truang = array();
							$t_truang['mulai'] = $t_mulai;
							$t_truang['selesai'] = $t_selesai;
							$t_truang['lebar'] = Date::lamaMenit($t_mulai,$t_selesai);
							$t_truang['status'] = true;
							$t_truang['keterangan'] = $t_jadwal['kodemk'].' - '.$t_jadwal['namamk'].'<br>Kelas '.$t_jadwal['kelasmk'].': '.CStr::formatJam($t_mulai).' - '.CStr::formatJam($t_selesai).'<br>Prodi: '.$t_jadwal['namaunit'].'<br>Dosen: '.$t_jadwal['namadosen'];
							$t_truang['kodemk'] = $t_jadwal['kodemk'].' ('.$t_jadwal['kelasmk'].')';
							
							$a_truang[$t_ruang][] = $t_truang;
							
							$t_lmulai = $t_selesai;
						}
					}
				}
				
				// bila ada sisa
				if($t_lmulai < $waktumax) {
					$t_truang = array();
					$t_truang['mulai'] = $t_lmulai;
					$t_truang['selesai'] = $waktumax;
					$t_truang['lebar'] = Date::lamaMenit($t_lmulai,$waktumax);
					$t_truang['status'] = false;
					
					$a_truang[$t_ruang][] = $t_truang;
					
					$t_lmulai = $waktumax;
				}
			}
			
			return $a_truang;
		}
		
		// rekap ruang
		function getRekapRuang($conn,$periode) {
			// mendapatkan ruang
			$sql = "select koderuang from ".static::table('ms_ruang')." order by koderuang";
			$rs = $conn->Execute($sql);
			
			$a_jadwal = array();
			while($row = $rs->FetchRow())
				$a_jadwal[$row['koderuang']] = array();
			
			// mendapatkan jadwal
			$sql = "select nohari,koderuang,jammulai,jamselesai,nohari2,koderuang2,jammulai2,jamselesai2
					from ".static::table()." where periode = '$periode'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()) {
				for($i=1;$i<=2;$i++) {
					if($i == 1) $j = '';
					else $j = $i;
					
					$t_nohari = (int)$row['nohari'.$j];
					if(!empty($t_nohari)) {
						$t_mulai = trim($row['jammulai'.$j]);
						$t_selesai = trim($row['jamselesai'.$j]);
						
						// hanya yang lengkap
						if(empty($t_mulai) or empty($t_selesai))
							continue;
						
						$a_jadwal[$row['koderuang'.$j]][$t_nohari] += Date::lamaMenit($t_mulai,$t_selesai);
					}
				}
			}
			
			return $a_jadwal;
		}
		
		// dosen pengajar
		function getDosenPengajar($conn,$key) {
			$sql = "select m.ispjmk,m.tugasmengajar, p.idpegawai as nipdosen, p.idpegawai, p.username, 
					akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as nama 
					from ".static::table('ak_mengajar')." m
					join sdm.ms_pegawai p on m.nipdosen::text = p.idpegawai::text
					where ".static::getCondition($key,null,'m')." and m.jeniskul='K' and m.kelompok='1'";
			$sql.=" order by nama";
			
			return $conn->GetArray($sql);
		}
		// Peserta MKu
		function getPesertaMku($conn,$key) {
			$sql = "select p.unitmku,u.namaunit from ".static::table('ak_pesertamku')." p
					join gate.ms_unit u on p.unitmku = u.kodeunit
					where ".static::getCondition($key,null,'p')." order by u.namaunit";
			
			return $conn->GetArray($sql);
		}
		
		function dosenPengajar($conn,$key) {
			$sql = "select p.idpegawai, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as nama from ".static::table('ak_mengajar')." m
					join sdm.ms_pegawai p on m.nipdosen::text = p.idpegawai::text
					where ".static::getCondition($key,null,'m');
			if(Akademik::isDosen())
				$sql.=" and m.nipdosen='".Modul::getUserIDPegawai()."'";
			$sql.=" order by nama";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// mata kuliah kurikulum
		function mkKurikulum($conn,$kurikulum,$kodeunit) {
			$sql = "select kodemk, kodemk||' - '||namamk from ".static::table('ak_kurikulum')."
					where thnkurikulum = '$kurikulum' and kodeunit = '$kodeunit' order by kodemk";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// mengulang
		function mengulang() {
			$data = array('-1' => 'Ya', '0' => 'Tidak');
			
			return $data;
		}
		function isOnline() {
			$data = array('-1' => 'Ya', '0' => 'Tidak');
			
			return $data;
		}
		function isblock() {
			$data = array('-1' => 'Block', '0' => 'Bukan Block');
			
			return $data;
		}
		function ismkdu() {
			$data = array('-1' => 'Ya', '0' => 'Tidak');
			
			return $data;
		}
		function cekKresJadwal($conn,$periode,$tgl,$ruang,$start,$end){
			$str="select 1 
					from ".static::table()." where 
					periode='$periode' and tglpertemuan='$tgl' and koderuang='$ruang' and 
					((jammulai::integer between $start and $end-1) or (jamselesai::integer between $start+1 and $end))
					 limit 1";
			$data=$conn->GetOne($str);
			return $data;
		}
		function findMatkul($conn,$str,$periode,$kodeunit,$col='',$key='',$view=false) {
			global $conf;
			
			//info left dan right
			$sql_unit="select infoleft, inforight from gate.ms_unit where kodeunit='$kodeunit'";
			$unit=$conn->Execute($sql_unit);			
			$u_row = $unit->FetchRow();
			
			$str = strtolower($str);
			if(empty($col))
				$col = static::key;
			if(empty($key))
				$key = static::key;
			if($view)
				$sql="select $key, $col as label from ".static::schema.".v_kelasnamamk
				where periode='$periode' and lower(namamk) like '%".strtolower($str)."%'";
			else
				$sql = "select $key, $col as label from ".static::table()." k
					join ".static::table('ak_matakuliah')." m using (thnkurikulum, kodemk)
					where k.periode='$periode' and k.kodeunit='$kodeunit' and (lower(m.namamk) like '%".strtolower($str)."%' or lower(m.kodemk) like '%".strtolower($str)."%') order by m.namamk";
					
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($key == static::key)
					$t_key = static::getKeyRow($row);
				else
					$t_key = $row[$key];
				
				$data[] = array('key' => $t_key, 'label' => $row['label']);
			}
			
			return $data;
		}

		function findPeserta($conn,$str,$col='',$kelas,$key='',$view=false) {
			global $conf;
			
			list($thnkurikulum,$kodemk,$kodeunit,$periode,$kelasmk)=explode('|',$kelas);
			
			$str = strtolower($str);
			if(empty($col))
				$col = static::key;
			if(empty($key))
				$key = static::key;
			if($view)
				$sql="select $key as key, $col as label from ".static::schema.".ms_mahasiswa
				where lower(nama) like '%".strtolower($str)."%'";
			else
				$sql = "select $key as key, $col as label
					from ".static::table('ak_krs')." k
					join ".static::table('ms_mahasiswa')." m on k.nim = m.nim
					left join ".static::table('ak_perwalian')." p on m.nim=p.nim and k.periode = k.periode and p.periode='$periode' and $col ilike '%$str%'
					where ".static::getCondition($kelas,null,'k')." and coalesce(p.frsdisetujui, 0) <>0 ";
					
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				
				$data[] = array('key' =>  $row['key'], 'label' => $row['label']);
			}
			
			return $data;
		}
		function getMaxKelompok($conn){
			$kelompok=$conn->GetRow("select max(keltutorial) as tutorial,max(kelpraktikum) as praktikum from ".static::table());
			if($kelompok['tutorial']>$kelompok['praktikum'])
				$batas=$kelompok['tutorial'];
			else if($kelompok['tutorial']<=$kelompok['praktikum'])
				$batas=$kelompok['praktikum'];
			else
				$batas=1;
			
			$data=array();
			for($i=1;$i<=$batas;$i++)
				$data[$i]=$i;
				
			return $data;
		}
		// kelas mata kuliah
		function listKelasmk($conn,$periode,$kurikulum,$kodeunit){
			$sql="select kelasmk,kelasmk from ".static::table()." where periode='$periode' and thnkurikulum='$kurikulum' and kodeunit='$kodeunit'";
			return Query::arrQuery($conn,$sql);
		}
		function kelasPenuh($conn,$key){
			$data=$conn->GetRow("select dayatampung,jumlahpeserta from ".static::table()." where ".static::getCondition($key));
			if((int)$data['jumlahpeserta']>=(int)$data['dayatampung']+1)
			    return true;
			else
			    return false;
		}
		function saveMku($conn,$key,$arrmku,$record){
			$err = false;
			$cond = static::getCondition($key);
			$sql = "select unitmku from ".static::table('ak_pesertamku')." where ".$cond;
			$rs = $conn->Execute($sql);
			
			$n_masuk=0;
			$a_delete = array();
			while($row = $rs->FetchRow()) {
				if(!empty($arrmku[$row['unitmku']])) {
					$arrmku[$row['unitmku']] = false;
					$n_masuk++;
				}
				else
					$a_delete[] = $row['unitmku'];
			}
			// hapus dulu
			if(!$err and !empty($a_delete))
				$err = Query::qDelete($conn,static::table('ak_pesertamku'),$cond." and unitmku in ('".implode("','",$a_delete)."')");
			
			// masukkan yang baru
			if(!$err) {
				foreach($arrmku as $unitmku => $t_status) {
					if($t_status !== false) {
						$record['unitmku'] = $unitmku;
						
						$n_masuk++;
						$err = Query::recInsert($conn,$record,static::table('ak_pesertamku'));
					}
					
					if($err) break;
				}
			}
			if(!$err)
				return true;
			else
				return false;
		}
		function cekKapasitas($conn,$key,$record,$kapasitas){
			if(empty($key))
				$key=static::getKeyRow($record);
			$data=static::getData($conn,$key);
			$sql="select coalesce(sum(kapasitas),0) from ".static::table('ak_kelaspraktikum')." where ".static::getCondition($key);
			$kap_praktikum=$conn->GetOne($sql);
			$sisa=(int)$data['dayatampung']-(int)$kap_praktikum;
			
			//cek sisanya
			$new_capasity=(int)$data['dayatampung']-(int)($kap_praktikum+$kapasitas);
			if($new_capasity < 0)
				return array(true,'Kapasitas kelas kuliah tidak cukup, Tersisa '.$sisa);
			else
				return array(false,'');
		}
		
		function getReportKelas($conn,$kolom,$sort,$filter){
			return static::getListData($conn,$kolom,$sort,$filter);
		}

		function getReportKelasNew($conn,$sem){
			$sql = "select * from ".self::table('v_kelas3new')." where periode='$sem'";
	
			$row = $conn->GetArray($sql);
			$data = $row;
			return $data;
		}

		function getJumlahNilaiUTS($conn, $thnkurikulum, $kodemk, $kelasmk, $kodeunit, $periode){
			$sql ="
			select count(a.nim) as jumlahmahasiswa
			from akademik.ak_unsurnilaikelas a 
			join akademik.ak_unsurpenilaian b on a.idunsurnilai = b.idunsurnilai and a.periode = b.periode and a.thnkurikulum=b.thnkurikulum and a.kodemk=b.kodemk and a.kelasmk = b.kelasmk
			where a.kodemk='".$kodemk."' and a.kelasmk='".$kelasmk."' and b.namaunsurnilai='UTS' and a.thnkurikulum='".$thnkurikulum."' and a.kodeunit='".$kodeunit."' and a.periode='".$periode."'
			";
			return $conn->GetArray($sql);
		}

		function getJumlahNilaiTugas($conn, $thnkurikulum, $kodemk, $kelasmk, $kodeunit, $periode){
			$sql ="
			select count(a.nim) as jumlahmahasiswa
			from akademik.ak_unsurnilaikelas a 
			join akademik.ak_unsurpenilaian b on a.idunsurnilai = b.idunsurnilai and a.periode = b.periode and a.thnkurikulum=b.thnkurikulum and a.kodemk=b.kodemk and a.kelasmk = b.kelasmk
			where a.kodemk='".$kodemk."' and a.kelasmk='".$kelasmk."' and b.namaunsurnilai='TUGAS' and a.thnkurikulum='".$thnkurikulum."' and a.kodeunit='".$kodeunit."' and a.periode='".$periode."'
			";
			return $conn->GetArray($sql);
		}

		function getJumlahNilaiUAS($conn, $thnkurikulum, $kodemk, $kelasmk, $kodeunit, $periode){
			$sql ="
			select count(a.nim) as jumlahmahasiswa
			from akademik.ak_unsurnilaikelas a 
			join akademik.ak_unsurpenilaian b on a.idunsurnilai = b.idunsurnilai and a.periode = b.periode and a.thnkurikulum=b.thnkurikulum and a.kodemk=b.kodemk and a.kelasmk = b.kelasmk
			where a.kodemk='".$kodemk."' and a.kelasmk='".$kelasmk."' and b.namaunsurnilai='UAS' and a.thnkurikulum='".$thnkurikulum."' and a.kodeunit='".$kodeunit."' and a.periode='".$periode."'
			";
			return $conn->GetArray($sql);
		}
		// 27-03-2019 - Rehan Tambahan untuk moodle
		function getCourseByPass($conn_moodle,$key){
			$sql = "select id from mdl_course where idnumber='$key'";
			$data = $conn_moodle->GetRow($sql);
		
			return $data['id'];
			
		}
		function getNamamk($conn,$key){
			$sql = "select namamk from akademik.ak_matakuliah where kodemk='$key'";
			$data = $conn->GetRow($sql);
			return $data['namamk'];
			
		}

		function getUserMoodle($conn,$idnumber){
			
			$token = '847895ee848fdb5fb2d43b275705470c';
			$domainname = 'https://elearning.inaba.ac.id';
			$functionname = 'core_user_get_users';
			$restformat = 'json';
			$params = array('criteria'=>array(
					array(	
						'key'=>'idnumber',
						'value'=>$idnumber
						)
				)
			  );
			//header('Content-Type: text/plain');
			$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
			require_once($conf['model_dir'].'m_curl.php');
			$curl = new curl;
			//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
			$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
			$resp = $curl->post($serverurl . $restformat, $params);
			$data = json_decode($resp, true);
			return $data;
			//header("Location: http://localhost:8080/akademik2/front/admin/index.php?page=list_syncuser");
			//print_r($resp);
			//self::UpdateSyncMoodle($conn,$data['userid']);
		}


		function addCourseMoodle($key){
			
			$token = '847895ee848fdb5fb2d43b275705470c';
			$domainname = 'https://elearning.inaba.ac.id';
			$functionname = 'core_course_create_courses';
			$restformat = 'json';
			$data = explode('|', $key);
				$enddate = strtotime('+16 week',strtotime($data[9]));//menambahkan 3 minggu
				$course = new stdClass();

				$course->fullname=$data[1]."-".$data[8]."-".$data[4]."-".$data[10];	// string,    254, Obrigatorio,          Nome Completo do Curso
				$course->shortname=$data[1]."-".$data[8]."-".$data[4]."-".$data[10];					// string,    100, Obrigatorio,          Nome Curto, evite usar espaço, substitua os espaços por traço baixo (underscore)
				$course->categoryid=self::getCategory($data[2]."|".$data[3]);					// int, 	   10, Obrigatorio, 		 Id da categoria
				$course->idnumber=$data[2]."".$data[3]."".$data[1]."".$data[4];												// deve ser conhecido o id conforme já cadastrado no moodle 
				$course->summaryformat = 1;
				$course->showgrades = 1;
				$course->newsitems = 5;
				$course->maxbytes = 0;
				$course->showreports = 0;
				$course->groupmodeforce = 0;
				$course->defaultgroupingid = 0;
				$course->startdate = strtotime($data[9]);                
				$course->enddate =  $enddate ;
				$course->numsections=16;
				$course->maxbytes=5000;
				//$course->idnumber  = "axo.44d.1x";				// string,    100, Opcional,             Id universal do curso
				$course->summary  = "Mata Kuliah ".$data[8]." Kelas ".$data[4];
																// string,     1K, Obrigatorio, 			 Sumário
				$course->visible  = 1;						// int,         1, Obrigatorio,             1: Disponível para estudante, 0:Não disponível
				$course->groupmode  =  0;						// int,         1, Obrigatorio,             Padrão para "0" //no group, separate, visible
				$course->format  = "weeks";					// string,      1, Obrigatorio,				Padrão para "weeks" //Formato do curso: weeks, topics, social, site,..
				$courses = array( $course);
				$params = array('courses' => $courses);
				
				/// REST CALL
				//header('Content-Type: text/plain');
				$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
				require_once($conf['model_dir'].'m_curl.php');
				$curl = new curl;
				//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
				$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
				if(!empty(self::getCategory($data[3]))){
				$resp = $curl->post($serverurl . $restformat, $params);
				$data = json_decode($resp, true);
					
				return true;
				}else{
					return false;
				}
				
				
				//print_r($resp);
		

		}
		function enrolDosen($conn_moodle,$conn,$key)
		{
			$token = '847895ee848fdb5fb2d43b275705470c';
			$domainname = 'https://elearning.inaba.ac.id';
			$functionname = 'enrol_manual_enrol_users';
			$restformat = 'json';
			$data = explode('|', $key);
			$idnumber = $data[2]."".$data[3]."".$data[1]."".$data[4];
			$uid = self::getUserMoodle($conn,$data[10]);
			$enrolment = new stdClass();
			$enrolment->roleid=3;
			$enrolment->userid =$uid['users'][0]['id'];
			$enrolment->courseid=self::getCourseByPass($conn_moodle,$idnumber);
			$enrolments = array($enrolment);
			$params = array('enrolments' => $enrolments);
			//header('Content-Type: text/plain');
			$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
			require_once($conf['model_dir'].'m_curl.php');
			$curl = new curl;
			//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
			$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
			$resp = $curl->post($serverurl . $restformat, $params);
			//print_r($resp);
		}
		function inquiryByuserid($conn,$uid){
			$unit = Modul::getLeftRight();
			$left=$unit['LEFT'];
			$right = $unit['RIGHT'];
			
			$sql = "select distinct u.*, ur.koderole
				from gate.sc_user u 
				left join gate.sc_userrole ur on ur.userid = u.userid 
				left join gate.ms_unit un on ur.kodeunit = un.kodeunit 
				and un.infoleft >= '$left' and un.inforight <= '$right' where u.idpegawai='$uid'
				";
			return $conn->GetRow($sql);
		}
		function syncUserToElearning($conn,$data){
		
			$token = '847895ee848fdb5fb2d43b275705470c';
			$domainname = 'https://elearning.inaba.ac.id';
			$functionname = 'core_user_create_users';
			$restformat = 'json';
			$names = explode(' ', $data['userdesc']);
			$ft = explode('.',$data['username']);
			$lastname='';
			for($i=1;$i<=count($names)-1;$i++){
				$lastname = $lastname.' '.$names[$i];
			}
			$firstname = $names[0];
			$user2 = new stdClass();
			$user2->username = $data['username'];
			if($data['koderole']=='D'){
				$user2->idnumber = $data['idpegawai'];
				$user2->password = '@DsnInaba1984';
			}else{
				$user2->idnumber = $data['username'];
				$user2->password = '@Inaba448';
			}
			$user2->firstname = $firstname;
			$user2->lastname = $lastname;
			$user2->email = $data['username'].'@moodle.com';
			$user2->timezone = 'Asia/Jakarta';
			$user2->city = 'Bandung';
			$user2->country = 'ID';
			$users = array($user2);
			$params = array('users' => $users);

			//header('Content-Type: text/plain');
			$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
			require_once($conf['model_dir'].'m_curl.php');
			$curl = new curl;
			//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
			$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
			$resp = $curl->post($serverurl . $restformat, $params);
			//header("Location: http://localhost:8080/akademik2/front/admin/index.php?page=list_syncuser");
			//print_r($resp);
			//self::UpdateSyncMoodle($conn,$data['userid']);
		}
		function getCategory($periode){
			
			$token = '847895ee848fdb5fb2d43b275705470c';
			$domainname = 'https://elearning.inaba.ac.id';
			$functionname = 'core_course_get_categories';
			$restformat = 'json';
			$params = array('criteria'=>array(
					array(	
						'key'=>'idnumber',
						'value'=>$periode
						)
				)
			  );
			//header('Content-Type: text/plain');
			$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
			require_once($conf['model_dir'].'m_curl.php');
			$curl = new curl;
			//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
			$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
			$resp = $curl->post($serverurl . $restformat, $params);
			$data = json_decode($resp, true);
			return $data[0]['id'];
			//header("Location: http://localhost:8080/akademik2/front/admin/index.php?page=list_syncuser");
			//print_r($resp);
			//self::UpdateSyncMoodle($conn,$data['userid']);
	}
	function UnEnrolUser($conn_moodle,$conn,$key){
		$token = '847895ee848fdb5fb2d43b275705470c';
		$domainname = 'https://elearning.inaba.ac.id';
		$functionname = 'enrol_manual_unenrol_users';
		$restformat = 'json';
		$data = explode('|', $key);
		$enrolment = new stdClass();
		$enrolment->roleid=3;
		$enrolment->userid =$data[1];
		$enrolment->courseid=$data[0];
		$enrolments = array($enrolment);
		$params = array('enrolments' => $enrolments);
		//header('Content-Type: text/plain');
		$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
		require_once($conf['model_dir'].'m_curl.php');
		$curl = new curl;
		//if rest format == 'xml', then we do not add the param for backward compatibility with Moodle < 2.2
		$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
		$resp = $curl->post($serverurl . $restformat, $params);
	}


		//

	function getDataNilaiTugas($conn_moodle,$nim,$kodeunit){
		$sql ="select round(nilai,2) from moodle.v_nilaitugas where nim='$nim' and idnumber='$kodeunit'";
	
		return $conn_moodle->getOne($sql);

	}
	}
?>
