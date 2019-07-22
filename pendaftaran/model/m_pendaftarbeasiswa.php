<?php
	// model combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once($conf['model_dir'].'m_model.php');
	require_once($conf['akademikmodel_dir'].'m_mahasiswa.php');
	
	class mPendaftarBeasiswa extends mModel{
		
		const schema 	= 'pendaftaran';
		const table 	= 'pd_pendaftar';
		const order 	= 'nopendaftar desc';
		const key 	= 'nopendaftar';
		const label 	= 'pendaftar';
		const uptype	='pendaftar';

		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			global $conn;
			switch($col) {
				case 'periode': return "periodedaftar = '$key'";
				case 'pilihan1':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "u1.infoleft >= ".(int)$row['infoleft']." and u1.inforight <= ".(int)$row['inforight'];
				case 'pilihanditerima':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "up.infoleft >= ".(int)$row['infoleft']." and up.inforight <= ".(int)$row['inforight'];
					
				case 'periodeformulir': return "t.periodedaftar = '$key'";
				case 'jalur': return "p.jalurpenerimaan = '$key'";
				case 'gelombang':return "idgelombang = '$key'";
				case 'tgltes':return "tgltes = '$key'";
				case 'isadministrasi':return "isadministrasi = '-1'";
				case 'isdaftarulang':return "coalesce(isdaftarulang,0) <> '$key'";
				case 'lulus':
					if($key=='l') return "pilihanditerima is not NULL";
					elseif ($key=='t') return "pilihanditerima is NULL";
				case 'jenis':
					if($key=='mhs') return "nimpendaftar is not NULL";
					elseif ($key=='pdf') return "nimpendaftar is NULL";
				case 'fakultas':
					return "pilihanditerima in (select kodeunit from gate.ms_unit where kodeunitparent ='$key') ";
				case 'basiskampus':
					global $conn, $conf;
					require_once(Route::getModelPath('sistemkuliah'));
					$sistem = mSistemkuliah::getIdByBasisKampus($conn,modul::getBasis(),modul::getKampus());
					return "  p.sistemkuliah in ('".implode("','",$sistem)."') ";
					
			}
		}



		// mendapatkan kueri list
		function listQuery() {
			$sql = "select p.* from pendaftaran.pd_pendaftar p left join gate.ms_unit u1 on u1.kodeunit=p.pilihan1 left join gate.ms_unit up on up.kodeunit = p.pilihanditerima";
			
			return $sql;
		}
				
		// mendapatkan kueri detail
		/*
		 * data Query untuk detail pendaftar
		 * variable
		 * 		m = pendaftar, kt=kota,ktlhr=kotalahir,p_lhr=propinsilahir, u1 u2 u3=pilihan1 2 3, s = smu
		 * */
		function dataQuery($key) {
			$sql = "select m.*, up.namaunit, upf.namaunit as fakultas, s.namasistem||' '||s.tipeprogram as namasistem, 
					k1.namakota as kodekota_text, 
					k2.namakota as kodekotalahir_text, 
					k3.namakota as kodekotasmu_text, 
					k4.namakota as kodekotapt_text,
					k5.namakota as kodekotaayah_text,
					k6.namakota as kodekotaibu_text,
					k7.namakota as kodekotakantor_text,
					k8.namakota as kodekotapt_text,
					smu.namasmu,
					pr1.namapropinsi as propinsismu_text,
					pr2.namapropinsi as propinsiptasal_text,
					pr3.namapropinsi as kodepropinsilahir_text,
					pr4.namapropinsi as kodepropinsikantor_text
					
					from ".static::table()." m
					left join gate.ms_unit up on up.kodeunit=m.pilihanditerima					
					left join gate.ms_unit upf on upf.kodeunit = up.kodeunitparent
					left join akademik.ak_sistem s on s.sistemkuliah=m.sistemkuliah
					left join akademik.ms_kota k1 on k1.kodekota = m.kodekota
					left join akademik.ms_kota k2 on k2.kodekota = m.kodekotalahir
					left join akademik.ms_kota k3 on k3.kodekota = m.kodekotasmu
					left join akademik.ms_kota k4 on k4.kodekota = m.kodekotapt
					left join akademik.ms_kota k5 on k5.kodekota = m.kodekotaayah
					left join akademik.ms_kota k6 on k6.kodekota = m.kodekotaibu
					left join akademik.ms_kota k7 on k7.kodekota = m.kodekotakantor
					left join akademik.ms_kota k8 on k8.kodekota = m.kodekotapt
					left join pendaftaran.lv_smu smu on smu.idsmu=m.asalsmu
					left join akademik.ms_propinsi pr1 on pr1.kodepropinsi=m.propinsismu
					left join akademik.ms_propinsi pr2 on pr2.kodepropinsi=m.propinsiptasal
					left join akademik.ms_propinsi pr3 on pr3.kodepropinsi=m.kodepropinsilahir
					left join akademik.ms_propinsi pr4 on pr4.kodepropinsi=m.kodepropinsikantor
					where ".static::getCondition($key);
			return $sql;
		}
		
		/*
		 * sql for list_tagihan pendaftar
		 * 
		 * */
		 
		function getSQLTagihan($conn, $periode, $kodeunit){

		
		if (!empty($kodeunit))
		$u = $conn->getRow("select infoleft, inforight from gate.ms_unit where kodeunit = '$kodeunit'");
		
			// get data pendaftar;
			$sql = "select nopendaftar from pendaftaran.pd_pendaftar p left join gate.ms_unit u on u.kodeunit=p.pilihanditerima where 1=1";
			if (!empty($periode))
				$sql.=" and periodedaftar = '$periode'";
			
			if (!empty($kodeunit)){
				$sql.=" and u.infoleft >= '".$u['infoleft']."' and u.inforight <= '".$u['inforight']."' ";
				}
			
			//create temp_table with data pendaftar on condition (flaglunas(L) > 0 && flaglunas(<>L) > 0) 
			$sql_tagihan = " DROP TABLE IF EXISTS temp_pendaftar;
							
							 select nopendaftar into temp temp_pendaftar
							 from h2h.ke_tagihan where nopendaftar in ($sql) ";							
			$sql_tagihan .=" group by nopendaftar
							 having count(case when flaglunas ='L' then 1 else null end ) > 0 and count(case when flaglunas <> 'L' then 1 else null end ) > 0 ";
			$rs = $conn->execute($sql_tagihan);
			
			//get data pendaftar, join with table pd_pendaftar n unit
			$sql_data = "select p.nopendaftar, p.nimpendaftar, p.pilihanditerima, p.pilihan1, p.nama, p.hp, p.email, p.sistemkuliah, u.namaunit,tg.isfollowup, tg.keteranganpendaftar,tg.tgltagihan, tg.nominaltagihan, tg.idtagihan from temp_pendaftar t 
							join pendaftaran.pd_pendaftar p on p.nopendaftar=t.nopendaftar 
							join h2h.ke_tagihan tg on tg.nopendaftar = p.nopendaftar and tg.flaglunas <> 'L'
							join gate.ms_unit u on u.kodeunit=p.pilihanditerima ";
			
			return $sql_data;
			
			}
		function sqlPembeliantoken(){
			return "select f.ish2h, f.notoken,f.flagbatal, p.nama, p.nopendaftar, p.lulusujian,p.pilihanditerima,p.isdaftarulang,m.nim, p.nimpendaftar,p.ukuranalmamater  from h2h.ke_pembayaranfrm f 
					join h2h.ke_tariffrm t on t.idtariffrm = f.idtariffrm
					left join pendaftaran.pd_pendaftar p on p.tokenpendaftaran=f.notoken
					left join gate.ms_unit u on u.kodeunit = p.pilihanditerima
					left join akademik.ms_mahasiswa m on m.nim = p.nimpendaftar
					";
			}
		function data($conn, $fperiode, $fjalur, $fgelombang, $cond){
			$sql="SELECT *
				FROM pendaftaran.pd_pendaftar";
				
			if(empty($fperiode) && empty($fjalur) && empty($fgelombang) ){
				
			}else{
				$sql.=" WHERE ";
				
				if(!empty($fperiode)){
					$sql.=" periodedaftar='$fperiode'";
				}
				if(!empty($fjalur)){
					$sql.=" AND jalurpenerimaan='$fjalur'";
				}
				if(!empty($fgelombang)){
					$sql.=" AND idgelombang='$fgelombang'";
				}
			}
			if(!empty($cond)){
				$sql.=" AND".$cond;
			}
			return $conn->SelectLimit($sql);
		}
		

		function getDatas($conn, $filter, $valfilter,$tarif='1'){
			$status['lunas'] = 1;
			if($status['lunas']==1 || $tarif!='1'){
				$sql="
					SELECT p.isdaftarulang,p.nopendaftar, nama, sex, jalurpenerimaan, periodedaftar,idgelombang, pilihanditerima, s.nourutreg, p.lulusujian, p.sistemkuliah, u,namaunit
					FROM pendaftaran.pd_pendaftar p
					LEFT JOIN pendaftaran.pd_syaratdaftarulang s on p.nopendaftar = s.nopendaftar
					left join gate.ms_unit u on u.kodeunit = p.pilihanditerima
					WHERE p.$filter='$valfilter' ";
				return $conn->SelectLimit($sql, 1);
			}else return false;
		}
		function getDataLokasi($conn, $kodelokasi){
			$sql="
				SELECT nopendaftar, gelardepan, nama, gelarbelakang, jalurpenerimaan
				FROM pendaftaran.pd_pendaftar
				WHERE lokasiujian='$kodelokasi' ";
			return $conn->SelectLimit($sql);
		}
		function getTotalNilai($conn,$nopendaftar){
			$conn->debug=false;
			$nilai=$conn->GetOne("select sum(nilai) from pendaftaran.pd_nilaipesertamateri where nopendaftar='$nopendaftar'");
			$count = $conn->GetOne("select count(*) from pendaftaran.pd_nilaipesertamateri where nopendaftar='$nopendaftar'");
			if($count<>0)
			$total = $nilai/$count;
			else
			$total = 0;
			return $total;
		}
		function getSyarat($conn, $filter, $valfilter){
			$sql="
				SELECT DISTINCT spj.idsyaratjalur, spj.namasyarat FROM pendaftaran.lv_syaratperjalur spj
				INNER JOIN pendaftaran.pd_pendaftar p ON spj.jalurpenerimaan=p.jalurpenerimaan
				WHERE p.$filter = '$valfilter' ORDER BY spj.idsyaratjalur ";
			return $conn->SelectLimit($sql);
		}
		function getStatusPesertaSyarat($conn, $filter, $valfilter, $syarat){
			$sql="
				SELECT idsyaratjalur FROM pendaftaran.pd_syaratpendaftar sp
				INNER JOIN pendaftaran.pd_pendaftar p ON sp.nopendaftar=p.nopendaftar
				WHERE p.$filter='$valfilter' AND sp.idsyaratjalur='$syarat'	
				";
			return $conn->Execute($sql);
		}
		function insertSyaratPendaftar($conn, $filter, $filval, $idsyaratjalur){
			
			$exist=self::getStatusPesertaSyarat($conn, $filter, $filval, $idsyaratjalur) ;
			$exist=$exist-> GetRows();
			
			if(empty($exist)){
				
				$sql="SELECT nopendaftar, jalurpenerimaan FROM pendaftaran.pd_pendaftar WHERE $filter = '$filval' ";
				$result=$conn->Execute($sql);
				$result=$result->FetchRow();
				
				$record=array();
				$record['nopendaftar']		= $result['nopendaftar'];
				$record['idsyaratjalur']	= $idsyaratjalur;
				$record['jalurpenerimaan']	= $result['jalurpenerimaan'];
				
				$col = "select * from pendaftaran.pd_syaratpendaftar where nopendaftar='-1'";	     
				$col=$conn->Execute($col);
				$insertSQL = $conn->GetInsertSQL($col,$record);
				$sql = $conn->Execute($insertSQL);
				
				if($sql==true){
					return true;
					
				}else{
					return false;
					
				}
			}
		}
		function deleteSyaratPendaftar($conn, $filter, $filval, $idsyaratjalur){
			
			$exist=self::getStatusPesertaSyarat($conn, $filter, $filval, $idsyaratjalur) ;
			$exist=$exist-> GetRows();
			
			if(!empty($exist)){
				
				$sql="SELECT nopendaftar, jalurpenerimaan FROM pendaftaran.pd_pendaftar WHERE $filter = '$filval' ";
				$result=$conn->Execute($sql);
				$result=$result->FetchRow();
				
				$nopendaftar		= $result['nopendaftar'];
				$idsyaratjalur		= $idsyaratjalur;
				//$record['jalurpenerimaan']	= $result['jalurpenerimaan'];
				
				$col = "DELETE FROM pendaftaran.pd_syaratpendaftar WHERE nopendaftar='$nopendaftar' AND idsyaratjalur='$idsyaratjalur'";	     
				$col=$conn->Execute($col);
				
				if($col==true){
					return true;
					
				}else{
					return false;
				}
			}
		}
		function getMateri($conn){
			$conn->debug=false;
			$sql="
				SELECT DISTINCT mu.kodemateri, mu.namamateri, mu.nillaiminimum FROM pendaftaran.lv_materiujian mu ORDER BY kodemateri
			";
			return $conn->SelectLimit($sql);
		}
		function getNilaiPesertaMateri($conn, $filter, $valfilter, $materi){
			$sql="
				SELECT kodemateri, nilai FROM pendaftaran.pd_nilaipesertamateri npm
				INNER JOIN pendaftaran.pd_pendaftar p ON npm.nopendaftar=p.nopendaftar
				WHERE p.$filter='$valfilter' AND npm.kodemateri='$materi'	
				";
			return $conn->Execute($sql);
		}
		function insertMateriPendaftar($conn, $filter, $filval, $materi, $nilai){
			
			$exist=self::getNilaiPesertaMateri($conn, $filter, $filval, $materi) ;
			$exist=$exist-> GetRows();
			
			if(empty($exist)){
				
				$sql="SELECT nopendaftar FROM pendaftaran.pd_pendaftar WHERE $filter = '$filval' ";
				$result=$conn->Execute($sql);
				$result=$result->FetchRow();
				
				$record=array();
				$record['nopendaftar']	= $result['nopendaftar'];
				$record['kodemateri']	= $materi;
				$record['nilai']	= $nilai;
				
				$col = "select * from pendaftaran.pd_nilaipesertamateri where nopendaftar='-1'";	     
				$col=$conn->Execute($col);
				$insertSQL = $conn->GetInsertSQL($col,$record);
				$sql = $conn->Execute($insertSQL);
				
				if($sql==true){
					return true;
					
				}else{
					return false;
					
				}
			}else if(!empty($exist)){
							
				$sql="SELECT nopendaftar FROM pendaftaran.pd_pendaftar WHERE $filter = '$filval' ";
				$result=$conn->Execute($sql);
				$result=$result->FetchRow();
				
				$record=array();
				$nopendaftar=$result['nopendaftar'];
				//$record['nopendaftar']	= $result['nopendaftar'];
				//$record['kodemateri']	= $materi;
				$record['nilai']	= $nilai;
				
				//$col = "select * from pendaftaran.pd_nilaipesertamateri where nopendaftar='-1'";	     
				//$col=$conn->Execute($col);
				$table="pendaftaran.pd_nilaipesertamateri";
				$updateSQL = $conn->AutoExecute($table,$record,'UPDATE', "nopendaftar='$nopendaftar' AND kodemateri='$materi'");
				//$sql = $conn->Execute($insertSQL);
				
				if($updateSQL==true){
					return true;
					
				}else{
					return false;
					
				}
			}
		}
		function deleteMateriPendaftar($conn, $filter, $filval, $materi){
			
			$exist=self::getNilaiPesertaMateri($conn, $filter, $filval, $materi) ;
			$exist=$exist-> GetRows();
			
			if(!empty($exist)){
				
				$sql="SELECT nopendaftar, jalurpenerimaan FROM pendaftaran.pd_pendaftar WHERE $filter = '$filval' ";
				$result=$conn->Execute($sql);
				$result=$result->FetchRow();
				
				$nopendaftar	= $result['nopendaftar'];
				$materi		= $materi;
				//$record['jalurpenerimaan']	= $result['jalurpenerimaan'];
				
				$col = "DELETE FROM pendaftaran.pd_nilaipesertamateri WHERE nopendaftar='$nopendaftar' AND kodemateri='$materi'";	     
				$col=$conn->Execute($col);
				
				if($col==true){
					return true;
					
				}else{
					return false;
				}
			}
		}
		
		function getSQLnim() {
			$sql = "select p.*, u.namaunit, m.nim from pendaftaran.pd_pendaftar p left join akademik.ms_mahasiswa m on m.nim = p.nimpendaftar
					join gate.ms_unit u on u.kodeunit = p.pilihanditerima
					";
			
			return $sql;
		}
		
		
		// mendapatkan info mahasiswa
		function getDataSingkat($conn,$key) {
			$sql = "select m.npm, m.nama, m.periodedaftar, m.sex, m.semmhs, m.nip, m.kodeunit,
					p.nama as dosenwali, up.namaunit as fakultas, u.namaunit as jurusan
					from ".static::table()." m left join ms_pegawai p on p.nip = m.nip
					left join gate.ms_unit u on u.kodeunit = m.kodeunit
					left join gate.ms_unit up on u.parentunit = up.kodeunit
					where ".static::getCondition($key);
			$row = $conn->GetRow($sql);
			
			// ambil model combo
			require_once(Route::getUIPath('combo'));
			
			$a_semester = mCombo::semester(true);
			$a_sex = static::jenisKelamin();
			
			$row['namasex'] = $row['sex'].' ('.$a_sex[$row['sex']].')';
			$row['namaperiodedaftar'] = Akademik::getNamaPeriode($row['periodedaftar']);
			$row['kurikulum'] = static::getKurikulum($conn,$row['periodedaftar'],$row['kodeunit']);
			
			return $row;
		}
		
		// mendapatkan nama mahasiswa
		function getNama($conn,$npm) {
			$sql = "select nama from ".static::table()." where ".static::getCondition($npm);
			
			return $conn->GetOne($sql);
		}
		
		// mendapatkan jalur penerimaan 
		function cekJalurPenerimaan($conn, $jalur) {
			$sql = "select * from akademik.lv_jalurpenerimaan where jalurpenerimaan='$jalur'";
			
			return $conn->GetRow($sql);
		}
		
		// mendapatkan indeks nilai mahasiswa
		function getIndeksNilai($conn,$npm) {
			$sql = "select ".static::schema.".f_ipk(npm) as ipk, ".static::schema.".f_ipslalu(npm) as ipslalu,
					".static::schema.".f_skslalu(npm) as skslalu, tipemhs from ".static::table()."
					where ".static::getCondition($npm);
			$data = $conn->GetRow($sql);
			
			if(empty($data['tipemhs']))
				$data['tipemhs'] = 'S1';
			
			$sql = "select f_batassks('".$data['ipslalu']."','".$data['skslalu']."','".$data['tipemhs']."') as batasbaru";
			$data['batasbaru'] = $conn->GetOne($sql);
			
			return $data;
		}
		
		// menghitung kurikulum mahasiswa
		function getKurikulum($conn,$periodedaftar,$kodeunit='') {
			if(empty($kodeunit)) {
				$sql = "select kurikulum from ak_thnkurikulum where kurikulum <= '".substr($periodedaftar,0,4)."'
						order by kurikulum desc limit 1";
			}
			else {
				$sql = "select kurikulum from ak_kurikulum where kurikulum <= '".substr($periodedaftar,0,4)."'
						and kodeunit = '$kodeunit' order by kurikulum desc limit 1";
			}
			
			return $conn->GetOne($sql);
		}
		
		// golongan
		function golongan() {
			$data = array('1' => 'PNS/ABRI/TNI', '2' => 'Anak PNS/ABRI/TNI', '3' => 'Umum');
			
			return $data;
		}
		
		/*
		function getDataPendaftarLulus($conn, $periode, $jalur, $gelombang){
			
			$sql="
				SELECT *
				FROM pendaftaran.pd_pendaftar
				WHERE periodedaftar='$periode'
				AND jalurpenerimaan='$jalur'
				AND idgelombang='$gelombang'
				";
			return $conn->SelectLimit($sql);
		}
		*/
		
		function getDataPendaftar($conn, $periode, $jalur, $gelombang, $tgltes){
			if(!empty($tgltes)){
				$tgltes=date('Y-m-d',strtotime($tgltes));
				$tgltes="'".$tgltes."'";
			}
			$sql="
				SELECT *
				FROM ".static::table('pd_pendaftar')." p 
				WHERE p.periodedaftar='$periode'
				AND p.jalurpenerimaan='$jalur'
				AND p.idgelombang='$gelombang' ";
			$sql.="order by nopendaftar ASC";
			return $conn->SelectLimit($sql);
		}
		
		function getDataPendaftarLulus($conn, $periode, $jalur, $gelombang){

			$sql="
				SELECT *
				FROM ".static::table('pd_pendaftar')." p 
				WHERE p.periodedaftar='$periode'
				AND p.jalurpenerimaan='$jalur'
				AND p.idgelombang='$gelombang'
				AND p.isadministrasi=-1
				";
			$sql.="order by nopendaftar ASC";
			return $conn->SelectLimit($sql);
		}
		
		function getNamaPilihan($conn, $kodeunit){
			$sql = "select kodeunit, namaunit from gate.ms_unit where kodeunit='$kodeunit'";
			$sql = Query::arrQuery($conn,$sql);
			return $sql[$kodeunit];
		}
		function getFakultas($conn, $kodeunit){
			$sql="SELECT kodeunit,kodeunitparent FROM gate.ms_unit WHERE kodeunit='$kodeunit'";
			$sql = Query::arrQuery($conn,$sql);
			return $sql[$kodeunit];
		}
		function updateKelulusanAll($conn, $record, $nopendaftar){
			$tgllulusujian=$conn->GEtOne("select tgllulusujian from ".static::table()." where nopendaftar='$nopendaftar'");
			$altjalur=$conn->GEtOne("select altjalur from ".static::table()." where nopendaftar='$nopendaftar'");
			if($record['lulusujian']==-1){
				$sql="update ".static::table()." set lulusujian=-1 ,pilihanditerima='".$record['pilihanditerima']."'";
				if(empty($tgllulusujian))
					$sql.=",tgllulusujian=now()";
				$sql.=" where nopendaftar='$nopendaftar'";
				$updateSQL=$conn->Execute($sql);
			}else if($record['lulusujian']==-2){
				$updateSQL=$conn->Execute("update ".static::table()." 
							set lulusujian=-2 ,pilihanditerima=null,tgllulusujian=null
							 where nopendaftar='$nopendaftar'");
				if(!empty($record['altjalur']) and empty($altjalur)){
					$password_acak = Date::RandomCode(6);
					$pswd = md5($password_acak);
					$password = $password_acak;
					$new_jalur=$record['altjalur'];
					$new_pilditerima=$record['altpilihanditerima'];
					$new_nopeserta=mPendaftar::nopeserta($record['periodedaftar'], $record['idgelombang'], $record['altjalur']);
					$new_nopendaftar =mPendaftar::nopendaftar($conn,$record['periodedaftar'], $record['idgelombang'], $record['altjalur']);
					
					$sql="insert into ".static::table()."
						(nopesertaspmb,nopendaftar,jalurpenerimaan,pilihanditerima,lulusujian,idgelombang,periodedaftar,gelardepan,nama,gelarbelakang,sex,kodepropinsilahir,
						kodekotalahir,tgllahir,goldarah,statusnikah,jalan,rt,rw,kel,kec,kodepos,kodekota,
						kodepropinsi,kodeagama,kodewn,telp,telp2,hp,hp2,email,email2,nomorktp,nomorkk,nis,namaayah,
						namaibu,kodeposortu,telportu,jalanortu,rtortu,rwortu,kelortu,kecortu,kodekotaortu,
						kodepekerjaanayah,kodepekerjaanibu,kodependidikanayah,kodependidikanibu,propinsismu,
						kodekotasmu,asalsmu,jurusansmaasal,thnlulussmaasal,alamatsmu,telpsmu,nemsmu,noijasahsmu,
						pernahponpes,namaponpes,alamatponpes,propinsiponpes,kodekotaponpes,lamaponpes,mhstransfer,
						ptasal,propinsiptasal,kodekotapt,ptjurusan,ptipk,ptthnlulus,sksasal,bhsarab,bhsinggris,pengkomp,
						pilihan1,pilihan2,pilihan3,kodepropinsiortu,kotaujian,kontaknama,kontaktelp,jalankontak,rtkontak,
						rwkontak,kelkontak,keckontak,kodekotakotak,kodepropinsikontak,isasing,iskartanu,facebook,idjadwaldetail,
						namapemilikkartanu,nopemilikkartanu,hubungankartanu,pendapatanayah,pendapatanibu,pswd,password)
						
						select '$new_nopeserta','$new_nopendaftar','$new_jalur','$new_pilditerima','-1',idgelombang,periodedaftar,gelardepan,nama,gelarbelakang,sex,kodepropinsilahir,
						kodekotalahir,tgllahir,goldarah,statusnikah,jalan,rt,rw,kel,kec,kodepos,kodekota,
						kodepropinsi,kodeagama,kodewn,telp,telp2,hp,hp2,email,email2,nomorktp,nomorkk,nis,namaayah,
						namaibu,kodeposortu,telportu,jalanortu,rtortu,rwortu,kelortu,kecortu,kodekotaortu,
						kodepekerjaanayah,kodepekerjaanibu,kodependidikanayah,kodependidikanibu,propinsismu,
						kodekotasmu,asalsmu,jurusansmaasal,thnlulussmaasal,alamatsmu,telpsmu,nemsmu,noijasahsmu,
						pernahponpes,namaponpes,alamatponpes,propinsiponpes,kodekotaponpes,lamaponpes,mhstransfer,
						ptasal,propinsiptasal,kodekotapt,ptjurusan,ptipk,ptthnlulus,sksasal,bhsarab,bhsinggris,pengkomp,
						pilihan1,pilihan2,pilihan3,kodepropinsiortu,kotaujian,kontaknama,kontaktelp,jalankontak,rtkontak,
						rwkontak,kelkontak,keckontak,kodekotakotak,kodepropinsikontak,isasing,iskartanu,facebook,idjadwaldetail,
						namapemilikkartanu,nopemilikkartanu,hubungankartanu,pendapatanayah,pendapatanibu,'$pswd','$password'
						from ".static::table()."
						where nopendaftar='".$nopendaftar."'";
					$copy=$conn->Execute($sql);
					if($copy)
						$update=$conn->Execute("update ".static::table()." set altjalur='$new_jalur',nopendaftarbaru='$new_nopendaftar' where nopendaftar='$nopendaftar'");
				}
			}
			//if($updateSQL)
				return true;
			//else 
				//return false;
		}
		function updateKelulusan($conn, $record, $nopendaftar){
			$updateSQL=$conn->AutoExecute(static::table(),$record,'UPDATE',"nopendaftar='$nopendaftar'");
			if($updateSQL)
				return true;
			else 
				return false;
		}
		function getSyaratDaftarUlang($conn){
			$sql="
				SELECT kodesyarat, namasyarat FROM pendaftaran.lv_syaratdaftarulang WHERE aktif='true'";
			return $conn->SelectLimit($sql);
		}
		function getStatusDaftarUlang($conn, $filter, $valfilter, $syarat){
			$sql="
				SELECT kodesyarat FROM pendaftaran.pd_syaratdaftarulang sp
				INNER JOIN pendaftaran.pd_pendaftar p ON sp.nopendaftar=p.nopendaftar
				WHERE p.$filter='$valfilter' AND sp.kodesyarat='$syarat'	
				";
			return $conn->Execute($sql);
		}
		function cekSyaratPendaftar($conn,$nopendaftar){
			$sql="select count(*) as nilai from pendaftaran.pd_syaratpendaftar 
                  where nopendaftar='$nopendaftar'";
			$data = $conn->GetOne($sql);
			return $data;
		}
		function cekSyaratJalur($conn,$nopendaftar){
			$sql="select count(*) as nilai from pendaftaran.lv_syaratperjalur d 
			      join pendaftaran.pd_pendaftar p on d.jalurpenerimaan=p.jalurpenerimaan
                  where p.nopendaftar='$nopendaftar'";
			$data = $conn->GetOne($sql);
			return $data;
		}
		function cekSyaratDftrUlangPendaftar($conn,$nopendaftar){
			$sql="select count(*) as nilai from pendaftaran.pd_syaratdaftarulang d 			      
                  where d.nopendaftar='$nopendaftar'";
			$data = $conn->GetOne($sql);
			return $data;
		}
		function cekSyaratDftrUlangJalur($conn,$nopendaftar){
			$sql="select count(*) as nilai from pendaftaran.lv_syaratdaftarulang where aktif = 'TRUE'";
			$data = $conn->GetOne($sql);
			return $data;
		}
		function setNoUrut($conn, $nopendaftar){
			$sql2="SELECT DISTINCT nourutreg FROM pendaftaran.pd_syaratdaftarulang WHERE nopendaftar='$nopendaftar'";
			$ok=$conn->Execute($sql2);
			$result=$ok->RecordCount();
			if($result==0){
				$sql2="SELECT max(nourutreg) as nourutreg FROM pendaftaran.pd_syaratdaftarulang";
				return $conn->Execute($sql2);
			}
			else{
				return $ok;
			}
		}
		function getNoUrut($conn, $nopendaftar){
			$sql="SELECT DISTINCT nopendaftar, nourutreg FROM pendaftaran.pd_syaratdaftarulang WHERE nopendaftar='$nopendaftar'";
			$ok=Query::arrQuery($conn, $sql);
			return $ok[$nopendaftar];
		}
		function insertSyaratDaftarUlang($conn, $filter, $filval, $kodesyarat){
			
			$exist=self::getStatusDaftarUlang($conn, $filter, $filval, $kodesyarat) ;
			$exist=$exist-> GetRows();
			
			if(empty($exist)){
				
				$sql="SELECT nopendaftar FROM pendaftaran.pd_pendaftar WHERE $filter = '$filval' ";
				$result=$conn->Execute($sql);
				$result=$result->FetchRow();
				
				$urut=self::setNoUrut($conn,$result['nopendaftar']);
				$urut=$urut+1;				
				
				$record=array();
				$record['nopendaftar']	= $result['nopendaftar'];
				$record['kodesyarat']	= $kodesyarat;
				$record['nourutreg']	= $urut;
				
				
				$col = "select * from pendaftaran.pd_syaratdaftarulang where nopendaftar='-1'";	     
				$col=$conn->Execute($col);
				$insertSQL = $conn->GetInsertSQL($col,$record);
				$sql = $conn->Execute($insertSQL);
				
				if($sql==true){
					return true;
					
				}else{
					return false;
					
				}
			}
		}
		function deleteSyaratDaftarUlang($conn, $filter, $filval, $kodesyarat){
			
			$exist=self::getStatusDaftarUlang($conn, $filter, $filval, $kodesyarat) ;
			$exist=$exist-> GetRows();
			
			if(!empty($exist)){
				
				$sql="SELECT nopendaftar FROM pendaftaran.pd_pendaftar WHERE $filter = '$filval' ";
				$result=$conn->Execute($sql);
				$result=$result->FetchRow();
				
				$nopendaftar		= $result['nopendaftar'];
				$kodesyarat		= $kodesyarat;
				//$record['jalurpenerimaan']	= $result['jalurpenerimaan'];
				
				$col = "DELETE FROM pendaftaran.pd_syaratdaftarulang WHERE nopendaftar='$nopendaftar' AND kodesyarat='$kodesyarat'";	     
				$col=$conn->Execute($col);
				
				if($col==true){
					return true;
					
				}else{
					return false;
				}
			}
		}
		//-------------------------------------generate NIM
		
		function setGenLog($conn, $periode){
			$record=array();
			$record['periodedaftar']	= $periode;
			$record['t_updatetime']		= date('Y-m-d H:m:s');
			$record['t_updateuser']		= Modul::getUserID();
				
			$col = "select * from pendaftaran.pd_genlog where idlog='-1'";	     
			$col=$conn->Execute($col);
			$insertSQL = $conn->GetInsertSQL($col,$record);
			$sql = $conn->Execute($insertSQL);
				
			if($sql==true){
				return true;
				
			}else{
				return false;
				
			}
		}
		
		function getKodeProdi($conn, $prodi){
			return $conn->getOne("select kodenim from akademik.ak_prodi where kodeunit = '$prodi'");
		}
		function exportPendaftar($conn, $periode, $fakultas, $nim=''){
			$sql= "select p.* from pendaftaran.pd_pendaftar p left join akademik.ms_mahasiswa m on p.nimpendaftar = m.nim 
					where m.nim is null and p.nimpendaftar is not null and periodedaftar = '$periode'";
			if (!empty ($nim))
					$sql.=" and nimpendaftar = '$nim' ";
			
			$rs = $conn->Execute($sql);
			$a=0;
			$gagal=0;
			$berhasil = 0;
			
			while ($row = $rs->fetchRow()){
				$thnkurikulum = $conn->getOne('select thnkurikulumsekarang from akademik.ms_setting where idsetting = 1');
					$record = array();
					$record['nim'] = $row['nimpendaftar'];
					$record['nama'] = $row['nama'];
					$record['tgllahir'] = $row['tgllahir'];
					$record['kodeunit'] = $row['pilihanditerima'];
					$record['jalurpenerimaan'] = $row['jalurpenerimaan'];
					$record['periodemasuk'] = $row['periodedaftar'];
					$record['gelombang'] = $row['idgelombang'];
					$record['sex'] = $row['sex'];
					$record['thnkurikulum'] = $thnkurikulum;
					$record['sistemkuliah'] = trim($row['sistemkuliah']);
					$record['potongan'] = ($row['isvalidbeasiswa'] == -1) ? $row['potonganbeasiswa'] : 0 ;
					$record['potongansp'] = ($row['isvalidsemesterpendek'] == -1) ? $row['potongansemesterpendek'] : 0 ;					
					$record['potsmtawal'] = 1;
					$record['potsmtakhir'] = 8;
					$record['tmplahir']=$conn->GetOne("select namakota from akademik.ms_kota where kodekota='".$row['tmplahir']."'");
					$record['alamat']=$row['jalan'];
					$record['kodekota']=$row['kodekota'];
					$record['kodepos']=$row['kodepos'];
					$record['telp']=$row['telp'];
					$record['hp']=$row['hp'];
					$record['email']=$row['email'];
					$record['statusnikah']=$row['statusnikah'];
					$record['kodeagama']=$row['kodeagama'];
					$record['kodewn']=$row['kodewn'];
					$record['namaayah']=$row['namaayah'];
					$record['namaibu']=$row['namaibu'];
					$record['alamatortu']=$row['alamatayah'];
					$record['kodepekerjaanayah']=$row['kodepekerjaanayah'];
					$record['kodepekerjaanibu']=$row['kodepekerjaanibu'];
					$record['kodependidikanayah']=$row['kodependidikanayah'];
					$record['kodependidikanibu']=$row['kodependidikanibu'];
					$record['alamatsmu']=$row['alamatsmu'];
					$record['kodekotasmu']=$row['kodekotasmu'];
					$record['telpsmu']=$row['telpsmu'];
					$record['ptasal']=$row['ptasal'];
					$record['kodekotapt']=$row['kodekotapt'];
					$record['ptjurusan']=$row['ptjurusan'];
					$record['ptipk']=$row['ptipk'];
					$record['ptthnlulus numeric ']=$row['ptthnlulus'];
					$record['rt numeric']=$row['rt'];
					$record['rw numeric']=$row['rw'];
					$record['kelurahan']=$row['kel'];
					$record['kecamatan']=$row['kec'];
					$record['rtortu']=$row['rtayah'];
					$record['rwortu']=$row['rwayah'];
					$record['kelurahanortu']=$row['kelayah'];
					$record['kecamatanortu']=$row['kecayah'];
					$record['kodepropinsi']=$row['kodepropinsi'];
					$record['kodepropinsiortu']=$row['kodepropinsiayah'];
					$record['kodepropinsipt']=$row['propinsiptasal'];
					$p_posterr = Query::recInsert($conn,$record,'akademik.ms_mahasiswa');
						if ($p_posterr == 0 )
							$berhasil++;
						else
							$gagal++;
				$a++;
				}
				
				$jenis = ($gagal > 0) ? true : false;
				
				
			if ($a == 0)
				return list($p_posterr, $p_postmsg) = array(true, 'Tidak ada pendaftar untuk di export ke mahasiswa');
			else
				return list($p_posterr, $p_postmsg) = array($jenis, 'Hasil Export Pendaftar ke Mahasiswa : '.$berhasil.' data Berhasil export <br>'.$gagal.' data Gagal Export');
				
			}			
		
		function setNIM($conn, $periode, $fakultas){
			$sql_fakultas= "select kodeunit from gate.ms_unit where kodeunitparent ='$fakultas'"; // list kodeunit
			$rs_fakultas = $conn->Execute($sql_fakultas);

			$kurang = 0;
			$berhasil = 0;
			$gagal = 0;
			while ($row = $rs_fakultas->fetchRow()){				

			$prodi = $row['kodeunit'];
			$kodenimprodi = self::getKodeProdi($conn, $prodi);

			$data=mPendaftar::data($conn, $periode, '', ''," lulusujian=-1 AND (pilihanditerima ='$prodi')
																		   AND coalesce(isadministrasi,'0') <> '0'
																		   AND nimpendaftar is null
																		   AND periodedaftar is not null
																		   AND idgelombang is not null
																		   AND jalurpenerimaan is not null
																		   AND coalesce(isdaftarulang,'0') <> '0'");
			foreach ($data as $pendaftar){
				$ok = true;
				$conn->beginTrans();

					$periodenim = substr($periode, 0, 4);
					$maxnim = self::getMaxnim($conn, $periode, $pendaftar['pilihanditerima']);
					$nim = $periodenim.$kodenimprodi.$maxnim;
					if (strlen($nim)==11 ){
						
						$record = array();
						$record['nimpendaftar'] = $nim;
						
						list($p_posterr, $p_postmsg) = mPendaftar::updateRecord($conn, $record, $pendaftar['nopendaftar'], true);						
						//$maxnim++;
						
						if ($p_posterr){
							$ok = false;
							$gagal++;
						}
						else
							$berhasil++;
					}else{
						
					$ok = false;
					$kurang++;
					}
				$conn->commitTrans($ok);

				}
				
				
			}
				if ($gagal > 0 or $kurang >0)
					$err = true;
				else
					$err = false;
				return array($err, 'HASIL GENERATE NIM : <br>Gagal generate karena nim  tidak 11 digit : '.$kurang.'
				<br>Berhasil Generate nim : '.$berhasil.'<br>Gagal generate nim : '.$gagal);
			
		}
		function setSingleNIM($nopendaftar, $nim){
			global $conn;
			if(strlen($nim)==9){
				$sql="UPDATE pendaftaran.pd_pendaftar SET nimpendaftar = '$nim' WHERE nopendaftar = '$nopendaftar'";
				return $conn->Execute($sql);
			}else return false;
		}
		function generateNIM($conn, $jalur, $jurusan, $periode, $urutreg, $asing=''){
			
			$fakultas	= self::getFakultas($conn, $jurusan);
			$fakultas	= $fakultas['kodeunitparent'];
			if($asing==1) $kodejalur='4';
			else $kodejalur	= self::getkodenim($conn, "akademik.lv_jalurpenerimaan", "jalurpenerimaan='$jalur'","");
			$kodejurusan	= self::getkodenim($conn,"akademik.ak_prodi","kodeunit='$jurusan'","");
			$kodejenjang	= self::getkodenim($conn,"akademik.ms_programpend pp INNER JOIN akademik.ak_prodi u ON u.kode_jenjang_studi=pp.programpend","u.kodeunit='$jurusan'"," pp.kodenim ");
			$kodeangkatan	= substr($periode, 2, 2);
			
			$nim=$fakultas.$kodejalur.$kodejurusan.$kodejenjang.$kodeangkatan.$urutreg;
			
			return $nim;
		}
		
		function getkodenim($conn,$table,$condition,$case){
			$sql="SELECT";
			if(empty($case)){
				$sql.=" kodenim ";
			}elseif(!empty($case)){
				$sql.=$case;
			}
			$sql.=" FROM ".$table." WHERE ".$condition;
			$ok=$conn->Execute($sql);
			$ok=$ok->FetchRow();
			return $ok['kodenim'];
		}
		//generate nopendaftaran
	    function nopeserta($periode, $gelombang, $jalur){
			global $conn;
			$sql = "
				SELECT max(substring (nopendaftar,4,5)) FROM pendaftaran.pd_pendaftar
				WHERE periodedaftar='$periode'
				/*AND idgelombang='$gelombang'
				AND jalurpenerimaan='$jalur'*/
				";
			$ok =$conn->getOne($sql);
			$ok= $ok+1;
			return $ok;
	    }
		
	    function nopendaftar($conn,$periode, $gelombang, $jalur){
			$nopeserta=self::nopeserta($periode,$gelombang, $jalur);
			$nopeserta=str_pad($nopeserta, 5, "0", STR_PAD_LEFT); 
			$noujian=substr($periode, 2,3).$nopeserta;		
			return $noujian;
	    } 
		// function nopendaftar($conn,$periode, $gelombang, $jalur){
			// $nopeserta=self::nopeserta($periode,$gelombang, $jalur);
			// $nopeserta=str_pad($nopeserta, 5, "0", STR_PAD_LEFT); 
			// $noujian=
				// substr($periode, 2,2).
				// $gelombang.
				// self::getKode($conn,$jalur).
				// $nopeserta;		
			// return $noujian;
	    // }
	    function getKode($conn, $jalur){
			$sql="SELECT jalurpenerimaan, kodejalur FROM akademik.lv_jalurpenerimaan WHERE jalurpenerimaan='$jalur'";
			$ok=Query::arrQuery($conn, $sql);
			return $ok[$jalur];
			}
			function getStatus($idstat){
			global $conn;
			$sql = "select statusnikah,namastatus from akademik.lv_statusnikah where statusnikah='$idstat'";
			$ok=Query::arrQuery($conn,$sql);
			return $ok[$idstat];
	    }
	    
	    function getKota($kodekota){
			global $conn;
			$sql = "select kodekota,namakota from akademik.ms_kota where kodekota='$kodekota'";
			$ok=$conn->GetRow($sql);
			return $ok['namakota'];
	    }
		
	    function getAgama($kodeAgama){
			global $conn;
			$sql = "select kodeagama, namaagama from akademik.lv_agama where kodeagama='$kodeAgama'";
			$ok=Query::arrQuery($conn,$sql);
			return $ok[$kodeAgama];
	    }
		
	    function getPendapatan($kodependapatan){
			global $conn;
			$sql = "select kodependapatan, namapendapatan from akademik.lv_pendapatan where kodependapatan='$kodependapatan'";
			$ok=Query::arrQuery($conn,$sql);
			return $ok[$kodependapatan];
	    }
		
	    function getPendidikan($kodependidikan){
			global $conn;
			$sql = "select kodependidikan, namapendidikan from akademik.lv_pendidikan where kodependidikan='$kodependidikan'";
			$ok=Query::arrQuery($conn,$sql);
			return $ok[$kodependidikan];
	    }
		
	    function getPekerjaan($kodepekerjaan){
			global $conn;
			$sql = "select kodepekerjaan, namapekerjaan from akademik.lv_pekerjaan where kodepekerjaan='$kodepekerjaan'";
			$ok=Query::arrQuery($conn,$sql);
			return $ok[$kodepekerjaan];
	    }
		
	    function getPilihan($idUnit){
			global $conn;
			$sql = "select kodeunit, namaunit from gate.ms_unit where kodeunit='$idUnit'";
			$ok=Query::arrQuery($conn,$sql);
			return $ok[$idUnit];
	    }
		
	    //insert data
	    function insertPeserta($record){
			global $conn;
				 
			$col = "select * from pendaftaran.pd_pendaftar where nopendaftar='-1'";	     
			$col=$conn->Execute($col);
			$insertSQL = $conn->GetInsertSQL($col,$record);
			$sql = $conn->Execute($insertSQL);
			if($sql==true){
				return true;   
			}else return false;
	    }
	    function getWaktuUjian($nomoropendaftar){
		global $conn;
		$sql = "select pd_pendaftar.nopendaftar, pd_gelombangdaftar.tglujian
			from pendaftaran.pd_gelombangdaftar
			INNER JOIN pendaftaran.pd_pendaftar 
			ON pd_pendaftar.jalurpenerimaan=pd_gelombangdaftar.jalurpenerimaan
			AND pd_pendaftar.periodedaftar=pd_gelombangdaftar.periodedaftar
			AND pd_pendaftar.idgelombang=pd_gelombangdaftar.idgelombang
			WHERE pd_pendaftar.nopendaftar='$nomoropendaftar'
			";
		$ok=Query::arrQuery($conn,$sql);
		return $ok[$nomoropendaftar];
	    }
	    function pendaftarlulus($conn, $no, $page){
		$sql = "SELECT 	nopendaftar, 
					nama, 
					u.namaunit as jurusan, 
					(SELECT mu.namaunit as fakultas 
					FROM gate.ms_unit mu
					WHERE mu.kodeunit=u.kodeunitparent
					) as fakultas
				FROM pendaftaran.pd_pendaftar p INNER JOIN gate.ms_unit u ON p.pilihanditerima=u.kodeunit
				WHERE p.lulusujian=true
				";
		if(!empty($no)){
			$sql .= "AND p.nopendaftar='$no'";
		}
		$sql.="LIMIT 25 OFFSET $page*25-25";
		return $conn->SelectLimit($sql);
	    }
	    
	    function dataPendaftarlulus($conn, $nama, $page){
		$sql = "SELECT 	p.nopendaftar,p.nama,p.asalsmu,p.jalan,p.rt,p.rw,p.kel,p.kec,trim(p.kodekota) as kodekota,trim(p.kodepropinsi) as kodepropinsi,p.isdaftarulang, 
					u.namaunit as jurusan,uf.namaunit as fakultas 
				FROM pendaftaran.pd_pendaftar p 
				INNER JOIN gate.ms_unit u ON p.pilihanditerima=u.kodeunit
				INNER JOIN gate.ms_unit uf on uf.kodeunit=u.kodeunitparent
				WHERE lower(p.nama) like '%".strtolower($nama)."%'
				";
		
		return $conn->GetArray($sql);
	    }
	    
	    function getMaxpage($conn){
		$sql="SELECT * FROM pendaftaran.pd_pendaftar WHERE lulusujian=true";
		    $ok = $conn->SelectLimit($sql);
		    $ok=$ok->RecordCount();
		    $ok=($ok/25)+1;
		    
		    return (int)$ok;
	    }
		
		function cekPassword($nopendaftar,$email){
			global $conn;
			$cek = $conn->GetOne("SELECT count(*) FROM pendaftaran.pd_pendaftar WHERE nopendaftar='$nopendaftar' and email='$email'");
			if($cek > 0) return true;
			else return false;
		}
		
		function updatePassword($nopendaftar, $rand){
			global $conn;
			$sql="UPDATE pendaftaran.pd_pendaftar SET pswd = '$rand' WHERE nopendaftar = '$nopendaftar'";
			$conn->Execute($sql);
		}
		function getFakJenjang($conn, $kodeunit){
			
			$sql="SELECT f.kodenim as fakultas,j.kodenim as jurusan,p.kodenim as jenjang FROM gate.ms_unit j
					JOIN gate.ms_unit f on j.kodeunitparent=f.kodeunit
					LEFT JOIN akademik.ms_programpend p ON p.programpend=j.programpend
					WHERE j.kodeunit='$kodeunit'";
			
			/*$sql="select f.kodenim as fakultas,j.kodenim as jurusan,p.kodenim as jenjang from akademik.ak_prodi j
				JOIN akademik.ms_programpend p ON p.programpend=j.kode_jenjang_studi
				JOIN gate.ms_unit u on u.kodeunit=j.kodeunit
				JOIN gate.ms_unit uf on uf.kodeunit=u.kodeunitparent
				JOIN akademik.ak_prodi f on f.kodeunit=uf.kodeunit 
				WHERE j.kodeunit='$kodeunit'";*/
			//$sql = Query::arrQuery($conn,$sql);
			
			return $conn->GetRow($sql);
		}
		function getAbsen($conn,$periode,$jalur,$tgltes){
			$sql="SELECT p.nama
				FROM ".static::table('pd_pendaftar')." p 
				WHERE isadministrasi=-1";
				if(!empty($periode))
				$sql.=" AND p.periodedaftar='$periode'";
				if(!empty($jalur))
				$sql.=" AND p.jalurpenerimaan='$jalur'";
				
				$sql.=" order by nopendaftar ASC";
			
			return $conn->Execute($sql);
		}
		function getDataKtm($conn,$nopendaftar){
			$sql="select p.nopendaftar,p.nama,p.periodedaftar,p.jalurpenerimaan,p.ukuranalmamater,p.tgldaftarulang,p.periodedaftar,g.namagelombang,u.namaunit as jurusan,uf.namaunit as fakultas
			from ".static::table('pd_pendaftar')." p 
			LEFT JOIN ".static::table('lv_gelombang')." g on p.idgelombang=g.idgelombang
			LEFT JOIN gate.ms_unit u on u.kodeunit=p.pilihanditerima
			LEFT JOIN gate.ms_unit uf on uf.kodeunit=u.kodeunitparent
			WHERE p.nopendaftar='$nopendaftar' and isdaftarulang='-1'";
			
			return $conn->GetRow($sql);
		}
		function getBiodata($conn,$nopendaftar){
			$sql="
				SELECT p.*,coalesce(p.gelardepan,'')||coalesce(p.nama,'')||coalesce(p.gelarbelakang,'') as namalengkap,
				
				g.namagelombang,jur.namaunit as jurusan,fak.namaunit as fakultas,pp.namaunit as prodipindahan,pf.namaunit as fakpindahan
				FROM pendaftaran.pd_pendaftar p
				INNER JOIN pendaftaran.pd_gelombangdaftar gd
					ON p.jalurpenerimaan=gd.jalurpenerimaan
					AND p.periodedaftar=gd.periodedaftar
					AND p.idgelombang=gd.idgelombang
				left join pendaftaran.lv_gelombang g on gd.idgelombang=g.idgelombang
				left join pendaftaran.pd_jadwaldetail jd on jd.idjadwaldetail=p.idjadwaldetail
				left join pendaftaran.pd_jadwal j on j.idjadwal=jd.idjadwal
				left join akademik.ms_ruang r on r.koderuang=jd.koderuang
				left join gate.ms_unit jur on jur.kodeunit=p.pilihanditerima
				left join gate.ms_unit fak on jur.kodeunitparent=fak.kodeunit
				left join gate.ms_unit pp on pp.kodeunit=p.pindahprodi
				left join gate.ms_unit pf on pp.kodeunitparent=pf.kodeunit
				WHERE p.nopendaftar='$nopendaftar'
				
			";
			return $conn->GetRow($sql);
		}
		function saudara($conn,$nopendaftar){
			$sql="select * from ".static::table('pd_saudarakandung')." where nopendaftar='$nopendaftar'";
			return $conn->GetArray($sql);
		}
		function pendFormal($conn,$nopendaftar){
			$sql="select * from ".static::table('pd_pendformal')." where nopendaftar='$nopendaftar'";
			return $conn->GetArray($sql);
		}
		function pendNonFormal($conn,$nopendaftar){
			$sql="select * from ".static::table('pd_pendnonformal')." where nopendaftar='$nopendaftar'";
			return $conn->GetArray($sql);
		}
		function organisasi($conn,$nopendaftar){
			$sql="select * from ".static::table('pd_organisasi')." where nopendaftar='$nopendaftar'";
			return $conn->GetArray($sql);
		}
		function prestasiAkad($conn,$nopendaftar){
			$sql="select * from ".static::table('pd_prestasiakad')." where nopendaftar='$nopendaftar'";
			return $conn->GetArray($sql);
		}
		function prestasiNonAkad($conn,$nopendaftar){
			$sql="select * from ".static::table('pd_prestasinonakad')." where nopendaftar='$nopendaftar'";
			return $conn->GetArray($sql);
		}
		function getReportPendaftar($conn,$periode,$jalur,$pilihan1='',$pilihanditerima='',$daftarulang='',$idsmu='',$lulusujian=''){
			$sql="select p.tglregistrasi,p.jalurpenerimaan,p.idgelombang,p.nopendaftar,p.nama as namalengkap,p.sex,p.kodekotalahir,
					p.jalan,p.rt,p.rw,p.kel,p.kec,trim(p.kodekota) as kodekota,trim(p.kodepropinsi) as kodepropinsi,p.telp,p.hp,p.asalsmu,p.periodedaftar,p.pilihan1,p.pilihan2,p.pilihan3,p.pilihanditerima,
					p.lulusujian,p.isdaftarulang,p.tgllahir,p.tgldaftarulang,p.ukuranalmamater,p.nimpendaftar, s.namasistem||' '||s.tipeprogram as namasistem, k1.namakota, mu.namasmu
					from ".static::table('pd_pendaftar')." p 
					left join akademik.ak_sistem s on s.sistemkuliah=p.sistemkuliah
					left join akademik.ms_kota k1 on k1.kodekota = p.kodekotalahir
					left join pendaftaran.lv_smu mu on mu.idsmu=p.asalsmu
					where 1=1";
			if(!empty($periode))
				$sql.=" AND p.periodedaftar='$periode'";
			if(!empty($pilihan1))
				$sql.=" AND p.pilihan1='$pilihan1'";
			if(!empty($pilihanditerima))
				$sql.=" AND p.pilihanditerima='$pilihanditerima'";
			if(!empty($jalur))
				$sql.=" AND p.jalurpenerimaan='$jalur'";
			if($daftarulang!='')
				$sql.=" AND p.isdaftarulang=$daftarulang";
			if($lulusujian!='')
				$sql.=" AND p.lulusujian=$lulusujian";
			if(!empty($idsmu))
				$sql.=" AND p.asalsmu='$idsmu'";
				
			$sql.= " order by nopendaftar ASC";
			return $conn->Execute($sql);
		}
		function getReportLulus($conn,$periode,$jalur,$pilihan1,$tahapujian){
			$sql="select p.nopendaftar,p.nama as namalengkap,p.sex,p.kodekotalahir,
					p.jalan,p.rt,p.rw,p.kel,p.kec,trim(p.kodekota) as kodekota,trim(p.kodepropinsi) as kodepropinsi,p.asalsmu,p.periodedaftar,p.pilihan1,p.pilihan2,p.pilihan3,p.pilihanditerima,
					p.lulusujian,p.isdaftarulang,p.tgllahir, k1.namakota
					from ".static::table('pd_pendaftar')." p
					left join akademik.ms_kota k1 on k1.kodekota = p.kodekotalahir 
					where 1=1 AND p.$tahapujian=-1";
			if(!empty($periode))
				$sql.=" AND p.periodedaftar='$periode'";
			if(!empty($pilihan1))
				$sql.=" AND p.pilihan1='$pilihan1'";
			if(!empty($jalur))
				$sql.=" AND p.jalurpenerimaan='$jalur'";
			$sql.= " order by nopendaftar ASC";
			return $conn->Execute($sql);
		}
		function getStatistikProdi($conn,$periode,$jalur,$prodi){
			
			$unit = $conn->getRow("select infoleft, inforight from gate.ms_unit where kodeunit = '$prodi'");
			
			$left=$unit['infoleft'];
			$right=$unit['inforight'];
			
			$sql="select p.nopendaftar,p.sex,p.isdaftarulang,p.pilihan1,p.pilihan2,p.pilihan3,p.pilihanditerima,
					p.lulustpa,p.luluswawancara,p.lulusteskesehatan,p.lulusnilairaport,p.lulustespelajaran,p.luluskompetensi
					from ".static::table('pd_pendaftar')." p left join gate.ms_unit u1 on p.pilihan1=u1.kodeunit 
					left join gate.ms_unit u2 on p.pilihan2=u2.kodeunit 
					left join gate.ms_unit u3 on p.pilihan3=u3.kodeunit 
					left join gate.ms_unit uterima on p.pilihanditerima=uterima.kodeunit 
					where ((u1.infoleft >= ".(int)$left." and u1.inforight <= ".(int)$right.") or
							(u2.infoleft >= ".(int)$left." and u2.inforight <= ".(int)$right.") or 
							(u3.infoleft >= ".(int)$left." and u3.inforight <= ".(int)$right.") or 
							(uterima.infoleft >= ".(int)$left." and uterima.inforight <= ".(int)$right.")
						 )";
			if(!empty($periode))
				$sql.=" AND periodedaftar='$periode'";
			if(!empty($jalur))
				$sql.=" AND jalurpenerimaan='$jalur'";
			return $conn->Execute($sql);
		}
		function getStatistikProdi2($conn,$periode,$jalur,$prodi){
			require_once(Route::getModelPath('unit'));
			$unit=mUnit::getData($conn,$prodi);
			$left=$unit['infoleft'];
			$right=$unit['inforight'];
			$sql="select u1.namaunit as pil1,sum(case when p.pilihan1 is not null then 1 end) as jum_pil1,
					u2.namaunit as pil2,sum(case when p.pilihan2 is not null then 1 end) as jum_pil2,
					u3.namaunit as pil3,sum(case when p.pilihan3 is not null then 1 end) as jum_pil3, 
					uterima.namaunit pildeterima,sum(case when p.pilihanditerima is not null then 1 end) as jum_pilditerima
					from pendaftaran.pd_pendaftar p
					left join gate.ms_unit u1 on p.pilihan1=u1.kodeunit 
					left join gate.ms_unit u2 on p.pilihan2=u2.kodeunit 
					left join gate.ms_unit u3 on p.pilihan3=u3.kodeunit 
					left join gate.ms_unit uterima on p.pilihanditerima=uterima.kodeunit 
					where ((u1.infoleft >= ".(int)$left." and u1.inforight <= ".(int)$right.") or
							(u2.infoleft >= ".(int)$left." and u2.inforight <= ".(int)$right.") or 
							(u3.infoleft >= ".(int)$left." and u3.inforight <= ".(int)$right.") or 
							(uterima.infoleft >= ".(int)$left." and uterima.inforight <= ".(int)$right.")
						 )";
			if(!empty($periode))
				$sql.=" AND p.periodedaftar='$periode'";
			if(!empty($jalur))
				$sql.=" AND p.jalurpenerimaan='$jalur'";
			
				$sql.=" group by u1.namaunit,u2.namaunit,u3.namaunit,uterima.namaunit";
			return $conn->GetArray($sql);
		}
		function getLevelUnit($conn,$kodeunit){
			return $conn->GetOne("select level from gate.ms_unit where kodeunit='$kodeunit'");
		}
		
		function getSaudara($conn,$nopendaftar){
			$rs_saudara = $conn->Execute("select s.*,p.namapendidikan,pr.namapropinsi,k.namakota from pendaftaran.pd_saudarakandung s left join akademik.lv_pendidikan p on p.kodependidikan=s.kodependidikan
								left join akademik.ms_propinsi pr on pr.kodepropinsi=s.kodepropinsisaudara left join akademik.ms_kota k on k.kodekota=s.kodekotasaudara
								where nopendaftar='$nopendaftar'");
			return $rs_saudara;
		}
		function deleteFile($conn,$key){
			$cond=static::getCondition($key);
			$file=$conn->GetOne("select filepindahprodi from pendaftaran.pd_pendaftar where $cond");
			$del=unlink("uploads/".static::uptype.'/'.$file);
			if($del)
				$update=$conn->Execute("update pendaftaran.pd_pendaftar set filepindahprodi='' where $cond");
			return static::updateStatus($conn,"filepindahprodi");
		}
		function infoLulus($conn,$nopendaftar){
			$sql = "SELECT 	p.nopendaftar,p.nama,p.asalsmu,p.jalan,p.rt,p.rw,p.kel,p.kec,trim(p.kodekota) as kodekota,trim(p.kodepropinsi) as kodepropinsi,p.isdaftarulang, 
					p.isadministrasi,p.lulusujian,u.namaunit as jurusan,uf.namaunit as fakultas 
				FROM pendaftaran.pd_pendaftar p 
				left JOIN gate.ms_unit u ON p.pilihanditerima=u.kodeunit
				left JOIN gate.ms_unit uf on uf.kodeunit=u.kodeunitparent
				WHERE p.nopendaftar='$nopendaftar'
				";
		
		return $conn->GetRow($sql);
		}
		
		function insertSaudara($record,$nopendaftar){
			global $conn;
			
			$col = "select * from pendaftaran.pd_saudarakandung where idsaudara='-1'";	     
			$col=$conn->Execute($col);
			$insertSQL = $conn->GetInsertSQL($col,$record);
			$sql = $conn->Execute($insertSQL);
			if($sql==true){
				return true;   
			}else return false;
	    }
		
		function insertPendformal($record,$nopendaftar){
			global $conn;
			
			$col = "select * from pendaftaran.pd_pendformal where idpendformal='-1'";	     
			$col=$conn->Execute($col);
			$insertSQL = $conn->GetInsertSQL($col,$record);
			$sql = $conn->Execute($insertSQL);
			if($sql==true){
				return true;   
			}else return false;
	    }
		
		function insertPendnonformal($record,$nopendaftar){
			global $conn;
			
			$col = "select * from pendaftaran.pd_pendnonformal where idpendnonformal='-1'";	     
			$col=$conn->Execute($col);
			$insertSQL = $conn->GetInsertSQL($col,$record);
			$sql = $conn->Execute($insertSQL);
			if($sql==true){
				return true;   
			}else return false;
	    }
		
		function insertOrganisasi($record,$nopendaftar){
			global $conn;
			
			$col = "select * from pendaftaran.pd_organisasi where idorganisasi='-1'";	     
			$col=$conn->Execute($col);
			$insertSQL = $conn->GetInsertSQL($col,$record);
			$sql = $conn->Execute($insertSQL);
			if($sql==true){
				return true;   
			}else return false;
	    }
		
		function insertPrestasiAkad($record,$nopendaftar){
			global $conn;
			
			$col = "select * from pendaftaran.pd_prestasiakad where idprestasiakad='-1'";	     
			$col=$conn->Execute($col);
			$insertSQL = $conn->GetInsertSQL($col,$record);
			$sql = $conn->Execute($insertSQL);
			if($sql==true){
				return true;   
			}else return false;
	    }
		
		function insertPrestasinonAkad($record,$nopendaftar){
			global $conn;
			
			$col = "select * from pendaftaran.pd_prestasinonakad where idprestasinonakad='-1'";	     
			$col=$conn->Execute($col);
			$insertSQL = $conn->GetInsertSQL($col,$record);
			$sql = $conn->Execute($insertSQL);
			if($sql==true){
				return true;   
			}else return false;
	    }
	    function uploadTPA($conn,$file) {
			//$r_file = $_FILES['xls']['tmp_name'];
		
			// pakai excel reader
			require_once('../includes/phpexcel/excel_reader2.php');
			$xls = new Spreadsheet_Excel_Reader($file);
			
			$cells = $xls->sheets[0]['cells'];
			$numrow = count($cells);
			
			// jika cells kosong (mungkin bukan merupakan format excel), baca secara csv
			if(empty($numrow)) {
				if(($handle = fopen($r_file, 'r')) !== false) {
					while (($data = fgetcsv($handle, 1000, "\t")) !== false) {
						$numrow++;
						foreach($data as $k => $v)
							$cells[$numrow][$k+1] = $v;
					}
					fclose($handle);
				}
			}
			//print_r($cells);die();
			// baris pertama adalah header
			$conn->BeginTrans();
			
			$ok = true;
			for($r=2;$r<=$numrow;$r++) {
				$data = $cells[$r];
				$record=array();
				//$record['nopendaftar']=$data[1];
				$record['lulustpa']=$data[2];
				$record['nilaitpa']=$data[3];
				
				
				list($p_posterr,$p_postmsg)=self::updateRecord($conn,$record,$data[1]);
				if($p_posterr) {
					$ok = false;
					break;
				}
			}
			$conn->CommitTrans($ok);
			return $ok;
		}
		function cekLulus($conn,$nopendaftar){
			$cek=$conn->GetOne("select 1 from ".static::table()." where nopendaftar='$nopendaftar' and pilihanditerima is not null");
			
			if($cek==1)
				return true;
			else
				return false;
		}
		
		function getBatasDu($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter){
			$sql="select p.nopendaftar,p.nama,p.periodedaftar,p.pilihanditerima,p.isadministrasi,
					p.jalurpenerimaan,p.tgllulusujian,p.isdaftarulang,l.lamadaftarulang 
					from pendaftaran.pd_pendaftar p 
					left join akademik.lv_jalurpenerimaan l on l.jalurpenerimaan=p.jalurpenerimaan
					LEFT JOIN pendaftaran.pd_jadwaldetail jd on p.idjadwaldetail=jd.idjadwaldetail 
					LEFT JOIN pendaftaran.pd_jadwal j on j.idjadwal=jd.idjadwal";
			$a_data = static::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
			return $a_data;
		}
		function rekapPerSma($conn,$periode,$jalur){
			$sql="select s.idsmu,s.namasmu,sum(1) as jumdaftar,sum(case when p.isdaftarulang=-1 then 1 end) as jumdaftarulang 
				from pendaftaran.pd_pendaftar p
				left join pendaftaran.lv_smu s on s.idsmu=p.asalsmu where 1=1";
				
			if(!empty($periode))
				$sql.=" AND p.periodedaftar='$periode'";
			if(!empty($jalur))
				$sql.=" AND p.jalurpenerimaan='$jalur'";
			$sql.=" group by s.idsmu,s.namasmu";
			
			return $conn->GetArray($sql);
		}
		function getMaxNim($conn,$periode,$kodeunit){
			$sql="select coalesce(max(right(nimpendaftar,3)),'0') from pendaftaran.pd_pendaftar where pilihanditerima = '$kodeunit' and periodedaftar = '$periode'";
			$nim=$conn->GetOne($sql);
			$nourut=substr($nim,-3);
			$nourut++;
			return str_pad($nourut,3,"0",STR_PAD_LEFT);
		}
		function getNim($conn,$nopendaftar){
			$sql="select pr.kodenim, p.nopendaftar, p.jalurpenerimaan, p.idgelombang, p.periodedaftar,p.pilihanditerima from pendaftaran.pd_pendaftar p
					join gate.ms_unit j on j.kodeunit=p.pilihanditerima
					join gate.ms_unit f on f.kodeunit=j.kodeunitparent
					join akademik.ak_prodi pr on pr.kodeunit = p.pilihanditerima
					where nopendaftar='$nopendaftar'";
			$data=$conn->GetRow($sql);
			$urutreg=self::getMaxNim($conn,$data['periodedaftar'],$data['pilihanditerima']);
			$nim=substr($data['periodedaftar'],0,4).str_pad($data['kodenim'],2,"0",STR_PAD_LEFT).$urutreg;
			return $nim;
		}
		function generateOneNim($conn,$nopendaftar){
			$nim=self::getNim($conn,$nopendaftar);
			if(strlen($nim)==11)
			{
				$record=array();
				$record['nimpendaftar']	= $nim;
				$record['tglgeneratenim'] = date('Y-m-d');
				$cek = $conn->getOne("select nimpendaftar from pendaftaran.pd_pendaftar where nopendaftar = '$nopendaftar'");
				if (!$cek)
					list($p_posterr, $p_postmsg) = mPendaftar::updateRecord($conn, $record, $nopendaftar);
				else
					list($p_posterr, $p_postmsg) = array(true, 'Pendaftar telah memiliki NIM');
			}else{
				list($p_posterr,$p_postmsg)=array(true,'Nim Yang Dihasilkan Kurang Dari 11 digit');
			}
			//berikan nilai balik
			if(!$p_posterr)
				return array(false,'Generate NIM Berhasil, Nim Baru :'.$nim);
			else
				return array($p_posterr,$p_postmsg);
		}
		function resetPassword($conn,$nopendaftar){

				$sql = "select tgllahir from pendaftaran.pd_pendaftar where nopendaftar = '$nopendaftar'";
				$tgllahir = $conn->getOne($sql);
				
				$tgllahir = md5(str_replace('-','',$tgllahir));
				
				$rs = $conn->Execute("update pendaftaran.pd_pendaftar set password = '$tgllahir' where nopendaftar = '$nopendaftar'");
						
				if (!$rs)
					return list($p_posterr,$p_postmsg) = array(true,'Reset Password Gagal');
				else
					return list($p_posterr,$p_postmsg) = array(false,'Reset Password Berhasil');
			}
		function getValidasipotongan($conn,$nopendaftar){
			$sql = "select isvalidbeasiswa,isvalidregistrasi,isvalidsemesterpendek from pendaftaran.pd_pendaftar where nopendaftar = '$nopendaftar'";
			$rs = $conn->getRow($sql);
			return array($rs['isvalidbeasiswa'],$rs['isvalidregistrasi'],$rs['isvalidsemesterpendek']);
			}
		function getTarifregistrasi($conn,$nopendaftar){
			$re = $conn->getRow(" select pilihanditerima, jalurpenerimaan, periodedaftar, idgelombang,sistemkuliah, mhstransfer from pendaftaran.pd_pendaftar where nopendaftar = '$nopendaftar'");
			
			$data1 =  $conn->getRow( "select t.gelombang, t.kodeunit, count(t.idtarifreg) as angsuran, sum(t.nominaltarif) as total 
					from h2h.ke_tarifreg t where t.jalurpenerimaan = '".$re['jalurpenerimaan']."' and t.periodetarif = '".$re['periodedaftar']."' and t.gelombang = '".$re['idgelombang']."' and t.kodeunit = '".$re['pilihanditerima']."' group by t.gelombang, t.kodeunit  ");
			
			$sql2 = "select sum(coalesce(nominaltarif,0)) as totalbiaya from h2h.ke_tarif k 
					left join h2h.lv_jenistagihan t on k.jenistagihan = t.jenistagihan
					where (1=1)";
					
					if ($re['sistemkuliah']=='R')
						$sql2.=" and t.isreguler = '-1'";
					if ($re['sistemkuliah']=='P')
						$sql2.=" and t.isparalel = '-1'";
						
					if ($re['mhstransfer']=='-1')
						$sql2.=" and t.isd3 = '-1'";
					else if (empty($re['mhstransfer']))
						$sql2.=" and t.issmu = '-1'";					
						
						$sql2.=" and k.sistemkuliah = '".$re['sistemkuliah']."' ";
						$sql2.=" and t.frekuensitagihan = 'S'";
					
					$re2 = $conn->getOne($sql2);
			
			return array($data1,$re2);	
			
					
					
			}
		function find($conn,$str,$col='',$key='') {
			global $conf;
			
			$str = strtolower($str);
			if(empty($col))
				$col = static::key;
			if(empty($key))
				$key = static::key;
			
			$sql = "select $key, $col as label from ".static::table()." 
					where 1=1 and lower($col::varchar) like '%$str%' order by ".static::order;
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($key == static::key)
					$t_key = static::getKeyRow($row);
				else
					$t_key = $row[$key];
				
				$data[] = array('key' => $t_key, 'label' => $row['label']);
			}
			if (empty($data))
				$data[] = array('key' => $str, 'label' => $str);
				
			return $data;
		}
	function inputColumn($conn,$key='',$r_jalur='', $r_periode='', $r_gel='',$beasiswa='',$registrasi='',$semesterpendek=''){
		require_once(Route::getModelPath('combo'));
		
		$readonly = $key ? true : false;

		$arrJurusan = mCombo::jurusan_spmb($conn,$r_jalur, $r_periode, $r_gel);

		$arrPropinsi = mCombo::propinsi($conn);
		$arrTingkatpelatihan = mCombo::tkpelatihan();
		$arrPekerjaan = mCombo::pekerjaan($conn);
		$arrPendidikan = mCombo::pendidikan($conn);
		$arrBekerja = array(0=>'Tidak', -1=>'Ya');
		$arrJenissekolah = array(1=>'Swasta',2=>'Negeri');
		$arrAgama = mCombo::agama($conn);
		$arrSistemkuliah = mCombo::sistemKuliah($conn);
		$arrPendapatan = mCombo::pendapatan($conn);
		$a_input = array();
		$a_input[] = array('kolom' => 'nama',	'label' => 'Nama Pendaftar', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
		$a_input[] = array('kolom' => 'sistemkuliah', 'label' => 'Sistem Kuliah', 'type' => 'S','option' => $arrSistemkuliah,'empty'=>false,'readonly'=>$readonly,'add'=>'onChange="loadProdiBuka()"');
		$a_input[] = array('kolom' => 'jalan', 	'label' => 'Jalan', 'maxlength' => 150, 'size' => 50, 'notnull' => true);
		$a_input[] = array('kolom' => 'rt', 	'label' => 'RT', 'maxlength' => 5, 'size' => 5,'notnull' => true);
		$a_input[] = array('kolom' => 'rw', 	'label' => 'RW', 'maxlength' => 5, 'size' => 5,'notnull' => true);
		$a_input[] = array('kolom' => 'kel', 	'label' => 'Kelurahan', 'maxlength' => 20, 'size' => 50,'notnull' => true);
		$a_input[] = array('kolom' => 'kec', 	'label' => 'Kecamatan', 'maxlength' => 20, 'size' => 50,'notnull' => true);
		$a_input[] = array('kolom' => 'kodepos','label' => 'Kode Pos', 'maxlength' => 150, 'size' => 50,'notnull' => true);
		$a_input[] = array('kolom' => 'kodekota','label' => 'Kota', 'type' => 'S', 'option' => "", 'notnull' => true, 'empty'=>true,'text'=>'kodekota_text');        
		$a_input[] = array('kolom' => 'kodepropinsi', 'label' => 'Propinsi', 'type' => 'S', 'notnull' => true, 'option' => $arrPropinsi,  'add' => 'onchange="loadKota()"','empty'=>true);
		$a_input[] = array('kolom' => 'nomorrumah', 'label' => 'Nomor Rumah', 'maxlength' => 4, 'size' => 4,  'add'=>'onkeypress="return numberOnly(event)"');
		$a_input[] = array('kolom' => 'isbekerja', 'label' => 'Bekerja ?', 'type' => 'S', 'notnull' => true, 'option' => $arrBekerja);
		$a_input[] = array('kolom' => 'negara', 'label' => 'Negara', 'maxlength' => 20, 'size' => 15, 'notnull' => true);
		$a_input[] = array('kolom' => 'iskelainanfisik', 'label' => 'Kelainan Fisik', 'type' => 'S', 'notnull' => false, 'option' => $arrBekerja);
		$a_input[] = array('kolom' => 'keteranganfisik', 'label' => 'Keterangan Kelainan Fisik', 'maxlength' => 255, 'size' => 50, 'notnull' => false, 'type'=>'A');
		
		$a_input[] = array('kolom' => 'telp', 	'label' => 'Telp', 'maxlength' => 15, 'size' => 15, 'notnull' => false);
		$a_input[] = array('kolom' => 'hp', 	'label' => 'Hp', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
		$a_input[] = array('kolom' => 'hp2', 	'label' => 'Hp2', 'maxlength' => 15, 'size' => 15);
		$a_input[] = array('kolom' => 'email', 	'label' => 'Email', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
		$a_input[] = array('kolom' => 'kodewn', 'label' => 'Kewarganegaraan', 'type' => 'S', 'notnull' => true, 'option' => mCombo::wargaNegara(),'empty'=>false);
		$a_input[] = array('kolom' => 'sex', 	'label' => 'Jenis Kelamin', 'type' => 'S', 'option' => mCombo::jenisKelamin(),'empty'=>false, 'notnull' => true);
		$a_input[] = array('kolom' => 'kodeagama', 'label' => 'Agama', 'type' => 'S', 'notnull' => true, 'option' => $arrAgama,'empty'=>false);
		$a_input[] = array('kolom' => 'statusnikah', 'label' => 'Status Nikah', 'type' => 'S', 'option' => mCombo::statusNikah(),'empty'=>false,'notnull' => true);
		$a_input[] = array('kolom' => 'tgllahir','label' => 'Tgl Lahir', 'type' => 'D', 'notnull' => true,'add'=>'readonly="readonly" class="readonly"');
		$a_input[] = array('kolom' => 'kodepropinsilahir', 'label' => 'TTL Propinsi, kota, tanggal lahir', 'type' => 'S', 'option' => $arrPropinsi, 'empty' => true, 'add' => 'onchange="loadKotaLahir()"', 'notnull' => true);
		$a_input[] = array('kolom' => 'kodekotalahir', 'label' => 'Kota Lahir', 'type' => 'S', 'option' => "", 'notnull' => true, 'empty'=>true,'text'=>'kodekotalahir_text');

		$a_input[] = array('kolom' => 'pilihan1', 'label' => 'Pilihan 1', 'type' => 'S', 'notnull' => true, 'option' => $arrJurusan, 'empty' => true,'add' => 'onchange="loadProdiBuka2()"','readonly'=>$readonly);
		$a_input[] = array('kolom' => 'pilihan2', 'label' => 'Pilihan 2', 'type' => 'S', 'option' => $arrJurusan, 'empty' => true,'readonly'=>$readonly);
		$a_input[] = array('kolom' => 'pilihan3', 'label' => 'Pilihan 3', 'type' => 'S', 'option' => $arrJurusan, 'empty' => true,'readonly'=>$readonly);
		$a_input[] = array('kolom' => 'alasan1', 'label' => 'Alasan 1','type'=>'A');
		$a_input[] = array('kolom' => 'alasan2', 'label' => 'Alasan 2','type'=>'A');

		//data sekolah
		$a_input[] = array('kolom' => 'xasalsmu', 'label' => 'Nama Sekolah','type' => 'S', 'notnull' => true,'option'=>'');
		$a_input[] = array('kolom' => 'asalsmu', 'label' => 'Nama Sekolah','type' => 'S','option'=>'');
		$a_input[] = array('kolom' => 'jurusansmaasal', 'label' => 'Jurusan', 'maxlength' => 30, 'size' => 50, 'notnull' => true);
		$a_input[] = array('kolom' => 'alamatsmu', 'label' => 'Alamat Sekolah', 'maxlength' => 60, 'size' => 50, 'notnull' => true);
		$a_input[] = array('kolom' => 'kodekotasmu', 'label' => 'Kota Sekolah', 'type' => 'S', 'notnull' => true, 'option' =>"",'add'=>'onChange="loadSMU()"','maxlength' => 20,'text'=>'kodekotasmu_text');
		$a_input[] = array('kolom' => 'propinsismu', 'label' => 'Propinsi Sekolah ', 'type' => 'S', 'notnull' => true, 'option' => $arrPropinsi, 'add' => 'onchange="loadKotaSMU()"','empty'=>true);
		$a_input[] = array('kolom' => 'telpsmu', 'label' => 'Telp Sekolah', 'maxlength' => 15, 'notnull' => false, 'size' => 15);
		$a_input[] = array('kolom' => 'thnlulussmaasal', 'label' => 'Tahun Lulus', 'maxlength' => 4, 'size' => 5, 'notnull' => true,'add'=>'onkeypress="return numberOnly(event)"');

		$a_input[] = array('kolom' => 'thnmasuksmaasal', 'label' => 'Tahun Masuk', 'maxlength' => 4, 'size' => 4,'notnull' => true,'add'=>'onkeypress="return numberOnly(event)"');
		$a_input[] = array('kolom' => 'nemsmu',	'label' => 'Nilai UN', 'type' => 'N,2', 'notnull' => true, 'maxlength' => 6, 'size' => 6);
		$a_input[] = array('kolom' => 'noijasahsmu', 'label' => 'No Ijasah SMU', 'maxlength' => 20, 'notnull' => true, 'size' => 20);
		$a_input[] = array('kolom' => 'kodesekolah', 'label' => 'Kode Sekolah', 'maxlength' => 10, 'notnull' => true, 'size' => 10);
		$a_input[] = array('kolom' => 'kodepossekolah','label' => 'Kode Pos Sekolah', 'maxlength' => 6, 'size' => 6,'notnull' => false);
		$a_input[] = array('kolom' => 'jenissekolah', 'label' => 'Jenis Sekolah', 'type' => 'S', 'option' => $arrJenissekolah, 'empty' => false);
		$a_input[] = array('kolom' => 'kodeagamasekolah', 'label' => 'Basis Agama Sekolah', 'type' => 'S', 'notnull' => true, 'option' => $arrAgama,'empty'=>false);
		$a_input[] = array('kolom' => 'negarasekolah', 'label' => 'Negara Sekolah', 'maxlength' => 20, 'size' => 15, 'notnull' => true);

		//pekerjaan
		$a_input[] = array('kolom' => 'namaperusahaan', 	'label' => 'nama tempat bekerja', 'maxlength' => 50, 'size' => 15,'notnull' => false);
		$a_input[] = array('kolom' => 'alamatperusahaan', 'label' => 'Alamat Kantor', 'maxlength' => 200, 'size' => 50, 'notnull' => false);
		$a_input[] = array('kolom' => 'nomorkantor', 'label' => 'Nomor Kantor', 'maxlength' => 4, 'size' => 4,  'add'=>'onkeypress="return numberOnly(event)"');
		$a_input[] = array('kolom' => 'rtkantor', 	'label' => 'RT Kantor', 'maxlength' => 5, 'size' => 5,'notnull' => false);
		$a_input[] = array('kolom' => 'rwkantor', 	'label' => 'RW Kantor', 'maxlength' => 5, 'size' => 5,'notnull' => false);
		$a_input[] = array('kolom' => 'kelkantor', 	'label' => 'Kelurahan Kantor', 'maxlength' => 20, 'size' =>20,'notnull' => false);
		$a_input[] = array('kolom' => 'kodekotakantor', 'label' => 'Kota', 'type' => 'S', 'notnull' => false, 'option' =>"",'text'=>'kodekotakantor_text');
		$a_input[] = array('kolom' => 'kodepropinsikantor', 'label' => 'Propinsi ', 'type' => 'S', 'notnull' => false, 'option' => $arrPropinsi, 'empty'=>true,'add' => 'onchange="loadKotaKantor()"');
		$a_input[] = array('kolom' => 'jabatankerja', 	'label' => 'Jabatan', 'maxlength' => 50, 'size' => 15,'notnull' => false);
		$a_input[] = array('kolom' => 'bagian', 	'label' => 'bagian', 'maxlength' => 50, 'size' => 15,'notnull' => false);
		$a_input[] = array('kolom' => 'telpkantor', 	'label' => 'Telp', 'maxlength' => 15, 'size' => 15, 'notnull' => false);
		$a_input[] = array('kolom' => 'hpkantor', 	'label' => 'Hp', 'maxlength' => 15, 'size' => 15, 'notnull' => false);
		$a_input[] = array('kolom' => 'thnmasuk', 'label' => 'Bekerja sejak tahun?', 'maxlength' => 4, 'size' => 4, 'notnull' => false,'add'=>'onkeypress="return numberOnly(event)"');
		
		//orang tua wali
		$a_input[] = array('kolom' => 'namaayah', 'label' => 'Nama', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
		$a_input[] = array('kolom' => 'kodepekerjaanayah', 'label' => 'Pekerjaan', 'type' => 'S', 'notnull' => true, 'option' => $arrPekerjaan, 'empty' => true);
		$a_input[] = array('kolom' => 'kodependidikanayah', 'label' => 'Pendidikan', 'type' => 'S', 'notnull' => true, 'option' => $arrPendidikan, 'empty' => true);
		$a_input[] = array('kolom' => 'jeniswali', 'label' => 'Jenis', 'type' => 'S', 'option' => array(1=>'Ayah',2=>'Wali'), 'empty' => false);
		$a_input[] = array('kolom' => 'statuswali', 'label' => 'Status', 'type' => 'S', 'option' => array(1=>'Masih Hidup',2=>'Meninggal'), 'empty' => false);
		$a_input[] = array('kolom' => 'alamatayah', 'label' => 'Alamat', 'maxlength' => 200, 'size' => 50, 'notnull' => true);
		$a_input[] = array('kolom' => 'nomorrumahayah', 'label' => 'Nomor Rumah', 'maxlength' => 4, 'size' => 4,  'add'=>'onkeypress="return numberOnly(event)"');
		$a_input[] = array('kolom' => 'rtayah', 	'label' => 'RT', 'maxlength' => 5, 'size' => 5,'notnull' => true);
		$a_input[] = array('kolom' => 'rwayah', 	'label' => 'RW', 'maxlength' => 5, 'size' => 5,'notnull' => true);
		$a_input[] = array('kolom' => 'kelayah', 	'label' => 'Kelurahan', 'maxlength' => 20, 'size' =>20,'notnull' => true);
		$a_input[] = array('kolom' => 'kecayah', 	'label' => 'Kecamatan', 'maxlength' => 20, 'size' =>20,'notnull' => true);
		$a_input[] = array('kolom' => 'kodekotaayah', 'label' => 'Kota', 'type' => 'S', 'notnull' => true, 'option' =>"",'text'=>'kodekotaayah_text');
		$a_input[] = array('kolom' => 'kodepropinsiayah', 'label' => 'Propinsi ', 'type' => 'S', 'notnull' => true, 'option' => $arrPropinsi, 'empty'=>true,'add' => 'onchange="loadKotaayah()"');
		$a_input[] = array('kolom' => 'telpayah', 	'label' => 'Telp', 'maxlength' => 15, 'size' => 15, 'notnull' => false);
		$a_input[] = array('kolom' => 'hpayah', 	'label' => 'Hp', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
		$a_input[] = array('kolom' => 'emailayah', 	'label' => 'Email', 'maxlength' => 50, 'size' => 30, 'notnull' => false);
		$a_input[] = array('kolom' => 'jabatankerjaayah', 	'label' => 'Jabatan', 'maxlength' => 50, 'size' => 15,'notnull' => false);
		$a_input[] = array('kolom' => 'namaperusahaanayah', 	'label' => 'nama tempat bekerja', 'maxlength' => 50, 'size' => 15,'notnull' => false);

		//nama ibu
		$a_input[] = array('kolom' => 'namaibu', 'label' => 'Nama Ibu', 'maxlength' => 50, 'size' => 30, 'notnull' => true);
		$a_input[] = array('kolom' => 'kodepekerjaanibu', 'label' => 'Pekerjaan Ibu', 'type' => 'S', 'notnull' => true, 'option' => $arrPekerjaan, 'empty' => true);
		$a_input[] = array('kolom' => 'kodependidikanibu', 'label' => 'Pendidikan Ibu', 'type' => 'S', 'notnull' => true, 'option' => $arrPendidikan, 'empty' => true);
		$a_input[] = array('kolom' => 'statusibu', 'label' => 'Status', 'type' => 'S', 'option' => array(1=>'Masih Hidup',2=>'Meninggal'), 'empty' => false);
		$a_input[] = array('kolom' => 'alamatibu', 'label' => 'Alamat', 'maxlength' => 200, 'size' => 50, 'notnull' => true);
		$a_input[] = array('kolom' => 'nomorrumahibu', 'label' => 'Nomor Rumah', 'maxlength' => 4, 'size' => 4, 'add'=>'onkeypress="return numberOnly(event)"');
		$a_input[] = array('kolom' => 'rtibu', 	'label' => 'RT', 'maxlength' => 5, 'size' => 5,'notnull' => true);
		$a_input[] = array('kolom' => 'rwibu', 	'label' => 'RW', 'maxlength' => 5, 'size' => 5,'notnull' => true);
		$a_input[] = array('kolom' => 'kelibu', 	'label' => 'Kelurahan', 'maxlength' => 20, 'size' =>20,'notnull' => true);
		$a_input[] = array('kolom' => 'kecibu', 	'label' => 'Kecamatan', 'maxlength' => 20, 'size' =>20,'notnull' => true);
		$a_input[] = array('kolom' => 'kodekotaibu', 'label' => 'Kota', 'type' => 'S', 'notnull' => true, 'option' =>"",'text'=>'kodekotaibu_text');
		$a_input[] = array('kolom' => 'kodepropinsiibu', 'label' => 'Propinsi ', 'type' => 'S', 'notnull' => true, 'option' => $arrPropinsi, 'empty'=>true,'add' => 'onchange="loadKotaibu()"');
		$a_input[] = array('kolom' => 'telpibu', 	'label' => 'Telp', 'maxlength' => 15, 'size' => 15, 'notnull' => false);
		$a_input[] = array('kolom' => 'hpibu', 	'label' => 'Hp', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
		$a_input[] = array('kolom' => 'emailibu', 	'label' => 'Email', 'maxlength' => 50, 'size' => 30, 'notnull' => false);
		$a_input[] = array('kolom' => 'jabatankerjaibu', 	'label' => 'Jabatan', 'maxlength' => 50, 'size' => 15,'notnull' => false);
		$a_input[] = array('kolom' => 'namaperusahaanibu', 	'label' => 'Perusahaan', 'maxlength' => 50, 'size' => 15,'notnull' => false);

		$a_input[] = array('kolom' => 'kodependapatanortu', 'label' => 'Pendapatan ortu ', 'type' => 'S', 'notnull' => true, 'option' => $arrPendapatan, 'empty' => true);

		//perguruan tinggi
		$a_input[] = array('kolom' => 'mhstransfer', 'label' => 'Status Asal ?', 'type' => 'R', 'option' => array('0' => 'SMU/SMK dan sejenisnya','-1' => 'D3/Pindahan'), 'add' => 'onChange="disabledMhst();"', 'default'=>'1','notnull'=>true);
		$a_input[] = array('kolom' => 'ptasal', 'label' => 'Universitas Asal', 'maxlength' => 50, 'size' => 40);
		$a_input[] = array('kolom' => 'propinsiptasal', 'label' => 'Propinsi universitas ', 'type' => 'S', 'option' => $arrPropinsi, 'add' => 'onchange="loadKotaPTAsal()"', 'empty'=>true);
		$a_input[] = array('kolom' => 'kodekotapt', 'label' => 'Kota Universitas', 'type' => 'S', 'option' =>"",'empty'=>true,'text'=>'kodekotapt_text');
		$a_input[] = array('kolom' => 'ptipk', 'label' => 'IPK', 'maxlength' => 4, 'size' => 4);
		$a_input[] = array('kolom' => 'ptthnlulus', 'label' => 'Tahun Lulus', 'maxlength' => 4, 'size' => 4,'add'=>'onkeypress="return numberOnly(event)"');
		$a_input[] = array('kolom' => 'ptthnmasuk', 'label' => 'Tahun Masuk', 'maxlength' => 4, 'size' => 4,'add'=>'onkeypress="return numberOnly(event)"');
		$a_input[] = array('kolom' => 'sksasal', 'label' => 'SKS', 'maxlength' => 3, 'size' => 4);
		$a_input[] = array('kolom' => 'semesterkeluar', 'label' => 'Semester Lulus', 'maxlength' => 3, 'size' => 4);
		$a_input[] = array('kolom' => 'negaraptasal', 'label' => 'Negara', 'maxlength' => 20, 'size' => 20);
		$a_input[] = array('kolom' => 'ptfakultas', 'label' => 'Fakultas', 'maxlength' => 20, 'size' => 20);
		$a_input[] = array('kolom' => 'ptjurusan', 'label' => 'Jurusan', 'maxlength' => 50, 'size' => 40);


		//informasi beasiswa
		$a_input[] = array('kolom' => 'potonganbeasiswa', 'label' => 'Potongan Beasiswa', 'maxlength' => 12, 'size' => 10, 'type'=>'N','readonly'=>$beasiswa == '-1' ? true : false);
		$a_input[] = array('kolom' => 'potonganregistrasi', 'label' => 'Potongan Registrasi', 'maxlength' => 12, 'size' => 10, 'type'=>'N','readonly'=>$registrasi == '-1' ? true : false);
		$a_input[] = array('kolom' => 'potongansemesterpendek', 'label' => 'Potongan Semester Pendek', 'maxlength' => 12, 'size' => 10, 'type'=>'N','readonly'=>$semesterpendek == '-1' ? true : false);

		$a_input[] = array('kolom' => 'keteranganpotonganbeasiswa', 'label' => 'Keterangan Potongan Beasiswa', 'maxlength' => 200, 'type'=>'A','readonly'=>$beasiswa == '-1' ? true : false);
		$a_input[] = array('kolom' => 'keteranganpotonganregistrasi', 'label' => 'Keterangan Potongan Registrasi', 'maxlength' => 200, 'type'=>'A','readonly'=>$registrasi == '-1' ? true : false);
		$a_input[] = array('kolom' => 'keteranganpotongansemesterpendek', 'label' => 'Keterangan Potongan Semester Pendek', 'maxlength' => 200, 'type'=>'A','readonly'=>$semesterpendek == '-1' ? true : false);
		$a_input[] = array('kolom' => 'isvalidbeasiswa', 'label' => 'Validasi Potongan Beasiswa', 'type'=>'S', 'option'=>array(0=>'Tidak disetujui', -1=>'Disetujui'),'readonly'=>true);
		$a_input[] = array('kolom' => 'isvalidregistrasi', 'label' => 'Validasi Potongan Registrasi', 'type'=>'S', 'option'=>array(0=>'Tidak disetujui', -1=>'Disetujui'),'readonly'=>true);
		$a_input[] = array('kolom' => 'isvalidsemesterpendek', 'label' => 'Validasi Potongan Semester Pendek', 'type'=>'S', 'option'=>array(0=>'Tidak disetujui', -1=>'Disetujui'),'readonly'=>true);

		//alasan memilih UEU			
		$a_input[] = array('kolom'=>'raport_10_1', 'label'=>'Raport 10_1','maxlength' => 5, 'size' => 5,'add'=>'style="width:50px"');
		$a_input[] = array('kolom'=>'raport_10_2', 'label'=>'Raport 10_2','maxlength' => 5, 'size' => 5,'add'=>'style="width:50px"');
		$a_input[] = array('kolom'=>'raport_11_1', 'label'=>'Raport 11_1','maxlength' => 5, 'size' => 5,'add'=>'style="width:50px"');
		$a_input[] = array('kolom'=>'raport_11_2', 'label'=>'Raport 11_2','maxlength' => 5, 'size' => 5,'add'=>'style="width:50px"');
		$a_input[] = array('kolom'=>'raport_12_1', 'label'=>'Raport 12_1','maxlength' => 5, 'size' => 5,'add'=>'style="width:50px"');

		$a_input[] = array('kolom' => 'filektp', 'label' => 'Scan KTP (JPG) ', 'type' => 'U', 'uptype' => 'ktp', 'size' => 100,'arrtype'=>array('image/jpeg'));
		$a_input[] = array('kolom' => 'fileraport', 'label' => 'Scan Raport smt 1-4 (PDF) ', 'type' => 'U','uptype' => 'raport', 'size' => 100,'arrtype'=>array('application/pdf'));
		
		$a_input[] = array('kolom' => 'filekk', 'label' => 'Scan Kartu Keluarga (PDF) ', 'type' => 'U','uptype' => 'kk', 'size' => 100,'arrtype'=>array('application/pdf'));
		$a_input[] = array('kolom' => 'filektpibu', 'label' => 'Scan KTP Ibu (PDF) ', 'type' => 'U','uptype' => 'ktpibu', 'size' => 100,'arrtype'=>array('application/pdf'));
		$a_input[] = array('kolom' => 'filektpayah', 'label' => 'Scan KTP Ayah (PDF) ', 'type' => 'U','uptype' => 'ktpayah', 'size' => 100,'arrtype'=>array('application/pdf'));
		$a_input[] = array('kolom' => 'fileijazah', 'label' => 'Scan Ijazah (PDF) ', 'type' => 'U','uptype' => 'ijazah', 'size' => 100,'arrtype'=>array('application/pdf'));

		
	return $a_input; 
		
	}	
	
	function getUrl($url,$post_data){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		//curl_setopt($ch, CURLOPT_POST,1);
		$rs = curl_exec($ch);
		
		curl_close($ch);
		list($p_posterrtagihan,$p_postmsgtagihan) = explode('|',$rs);
		return array($p_posterrtagihan,$p_postmsgtagihan);
	}
		function insertSMU($conn,$namasmu,$propinsi,$kodekota){
			require_once(Route::getModelPath('smu'));
			
			$rec['namasmu'] = $namasmu;
			$rec['kodekota'] = $kodekota;
			
			list($p_posterr,$p_postmsg) = mSmu::insertRecord($conn,$rec,true);
			
			$id = mSmu::getLastValue($conn);
			
			return array($p_posterr,$id);
		}
	
	function getDataAlasan($conn,$key){
		$sql = "select m.*, pil1.namaunit as pilihan1,pil2.namaunit as pilihan2,pil3.namaunit as pilihan3,alasan1,alasan2
				from pendaftaran.pd_pendaftar m 
				left join gate.ms_unit pil1 on pil1.kodeunit=m.pilihan1
				left join gate.ms_unit pil2 on pil2.kodeunit = m.pilihan2 
				left join gate.ms_unit pil3 on pil3.kodeunit = m.pilihan3 
				left join kemahasiswaan.mw_pengajuanbeasiswapendaftar bs using (nopendaftar)
				where m.nopendaftar = '$key'   ";
		return $conn->getRow($sql);
	}
	
	function getDataAnak($conn,$key){
		$sql = "select *
				from  kemahasiswaan.mw_pengajuanbeasiswapendaftar bs 
				where nopendaftar = '$key'   ";
		return $conn->getRow($sql);
	}
					
	    
	}	
?>
