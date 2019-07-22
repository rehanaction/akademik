<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mGaji extends mModel {
		const schema = 'sdm';
				
		/**************************************************** PERIODE GAJI ******************************************************/
		// mendapatkan kueri list untuk periode gaji
		function listQueryPeriodeGA() {
			$sql = "select * from ".static::table('ga_periodegaji');
			
			return $sql;
		}
		
		function getCPeriodeGaji($conn){
			$sql = "select periodegaji, namaperiode from ".static::table('ga_periodegaji')." where refperiodegaji is null order by tglakhirhitung desc";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getLastPeriodeGaji($conn){
			$r_periodegaji = $conn->GetOne("select periodegaji from ".static::table('ga_periodegaji')." where refperiodegaji is null order by tglakhirhitung desc limit 1");
			
			return $r_periodegaji;
		}
		
		function getDataPeriodeGaji($conn,$r_periode){
			$row = $conn->GetRow("select * from ".static::table('ga_periodegaji')." where periodegaji = '$r_periode'");
			
			return $row;
		}
		
		/**************************************************** PERIODE TARIF GAJI ******************************************************/
		
		function getLastDataPeriodeTarif($conn){
			$r_periodetarif = $conn->GetOne("select periodetarif from ".static::table('ga_periodetarif')." order by tglmulai desc limit 1");
			
			return $r_periodetarif;
		}
		
		function getLastDataPeriodeGaji($conn){
			$periode = self::getLastPeriodeGaji($conn);
			$row = $conn->GetRow("select * from ".static::table('ga_periodegaji')." where periodegaji = '$periode'");
			
			return $row;
		}
		
		function listQueryPeriodeTarif(){
			$sql = "select * from ".static::table('ga_periodetarif')."";
			
			return $sql;
		}
		
		function getCPeriodeTarif($conn){
			$sql = "select periodetarif, namaperiode from ".static::table('ga_periodetarif')." order by tglmulai desc";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getLastPeriodeTarif($conn){
			$r_periodetarif = $conn->GetOne("select periodetarif from ".static::table('ga_periodetarif')." order by tglmulai desc limit 1");
			
			return $r_periodetarif;
		}
		
		function periodeTarifSalin($conn,$r_key=''){
			$sql = "select periodetarif, namaperiode from ".static::table('ga_periodetarif')." where periodetarif <> '$r_key' order by tglmulai desc";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function saveSalinTarif($conn,$r_key,$p_dbtable,$f_key){
			//bersihkan dulu
			list($err,$msg) = self::delete($conn,$r_key,'ga_tarifgapok','periodetarif');
			if(!$err){
				$sql = "insert into ".static::table('ga_tarifgapok')." 
						select '$r_key',idpangkat,masakerja,tarifgapok,'".Modul::getUserName()."','".date('Y-m-d H:i:s')."','".$_SERVER['REMOTE_ADDR']."' 
						from ".static::table('ga_tarifgapok')."
						where periodetarif = '$f_key'";
				
				$conn->Execute($sql);
			}
			
			list($err,$msg) = self::delete($conn,$r_key,'ga_tariftunjangan','periodetarif');
			if(!$err){
				$sql = "insert into ".static::table('ga_tariftunjangan')." 
						select '$r_key',kodetunjangan,variabel1,variabel2,nominal,'".Modul::getUserName()."','".date('Y-m-d H:i:s')."','".$_SERVER['REMOTE_ADDR']."' 
						from ".static::table('ga_tariftunjangan')."
						where periodetarif = '$f_key'";
				
				$conn->Execute($sql);
			}
			
			return $err;
		}
		
		//********************************G A J I   P O K O K*************************************
		
		function listQueryGapok(){
			$sql = "select g.*,p.namapendidikan from ".static::table('ga_tarifgapok')." g
					left join ".static::table('lv_jenjangpendidikan')." p on p.idpendidikan = g.idpendidikan";
			
			return $sql;
		}
		
		function getGajiPokok($conn,$pendidikan,$masakerjapend){
			$r_periodetarif = self::getLastPeriodeTarif($conn);
			
			$gapok = $conn->GetOne("select tarifgapok from ".static::table('ga_tarifgapok')." 
					where periodetarif = '$r_periodetarif' and idpendidikan = '$pendidikan' and masakerjapend = '$masakerjapend'");
					
			return $gapok;
		}
			
		function getCPendidikan($conn){
			$sql = "select idpendidikan, namapendidikan from ".static::table('lv_jenjangpendidikan')." order by urutan desc";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function listQueryPeriodeUMR(){
			$sql = "select * from ".static::table('ga_periodegapokumr')."";
			
			return $sql;
		}
		
		function getLastUMR($conn){
			$r_umr = $conn->GetOne("select umr from ".static::table('ga_periodegapokumr')." order by tglmulai desc limit 1");
			
			return $r_umr;
		}
		
		//daftar gaji pokok pegawai
		function listQueryGajiPokok($r_periode) {
			$sql = "select g.*,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,substring(g.masakerjapend,1,2)||' tahun ' as masakerja, gp.idpegawai, gp.periodegaji, gp.gapok,
					t.tipepeg||' - '||j.jenispegawai as namajenispegawai
					from ".static::table('ga_historydatagaji')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpeg
					left join ".static::table('ga_gajipeg')." gp on g.idpeg = gp.idpegawai and g.gajiperiode = '$r_periode'
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = g.idtipepeg
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = g.idjenispegawai
					left join ".static::table('ms_unit')." u on u.idunit = g.idunit
					left join ".static::table('lv_jenjangpendidikan')." pd on pd.idpendidikan = g.pendidikan";
			
			return $sql;
		}
		
		//perhitungan gaji pokok
		function hitGajiPokok($conn,$r_periode,$r_sql=''){	
			//filter dari daftar
			if(!empty($r_sql)){
				$a_peg = self::pegFilter($conn,$r_sql);
			}
			
			$sql = "select * from ".static::table('ga_historydatagaji')."
					where gajiperiode = '$r_periode'";
			if(!empty($a_peg))
				$sql .= " and idpeg in ($a_peg)";
				
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$gajipokok = '';
				if(!empty($row['pendidikan']) and !empty($row['masakerjapend']))
					$gajipokok = self::getGajiPokok($conn,$row['pendidikan'],$row['masakerjapend']);
								
				$record = array();
				$record['periodegaji'] = $r_periode;
				$record['idpegawai'] = $row['idpeg'];
				$record['gapok'] = $gajipokok;
				
				list($err,$msg) = self::saveGaji($conn,$record,$r_periode,$row['idpeg']);
			}
			
			return array($err,$msg);
		}
		
		/***********************SALIN TARIF GAJI POKOK***************************/
		
		function getCSalinPeriodeTarif($conn,$r_periodetarif){	
			$sql = "select periodetarif, namaperiode from ".static::table('ga_periodetarif')."
					where periodetarif <>'$r_periodetarif'
					order by periodetarif";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['periodetarif']] = $row['namaperiode'];
			}
			
			return $a_data;
		}
		
		function getCSalinPendidikan($conn){
			$sql = "select idpendidikan, namapendidikan from ".static::table('lv_jenjangpendidikan')." order by urutan desc";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_add = array('all' => '-- Semua Pendidikan --');
			$a_data = array_merge($a_data,$a_add);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['idpendidikan']] = $row['namapendidikan'];
			}
			
			return $a_data;
		}
		
		function saveSalinGapok($conn,$r_periode,$r_pendidikan,$f_keyPeriode,$f_keyPendidikan,$prosentase){
			$err = 0;
		
			$r_key = $r_periode."|".$f_keyPendidikan;
			list($err,$msg) = self::delete($conn,$r_key,'ga_tarifgapok','periodetarif,idpendidikan');
			
			if(!$err){
				if ($f_keyPendidikan !='all')
					$pendidikan= "'".$f_keyPendidikan."'";
				else
					$pendidikan="idpendidikan";
				
				$sql = "insert into ".static::table('ga_tarifgapok')." 
						select $pendidikan,'$r_periode',masakerjapend,tarifgapok + (tarifgapok * $prosentase) ,'".Modul::getUserName()."','".date('Y-m-d H:i:s')."','".$_SERVER['REMOTE_ADDR']."' 
						from ".static::table('ga_tarifgapok')."
						where periodetarif='$f_keyPeriode' ";
				
				if ($f_keyPendidikan !='all')
					$sql .= "and idpendidikan='$f_keyPendidikan'";
				
				$conn->Execute($sql);
			}
			if($err)
				$msg = 'Salin tarif gaji pokok gagal, data masih digunakan';
			else
				$msg = 'Salin tarif gaji pokok berhasil';
			
			return array($err,$msg);
		}
		
		//*************************************T U N J A N G A N************************************
		
		function listQueryTunjangan() {
			$sql = "select * from ".static::table('ga_tunjangan');
			
			return $sql;
		}
				
		function getCTunjTarif($conn){
			$sql = "select kodetunjangan,namatunjangan from ".static::table('ga_tunjangan')." where carahitung in ('T') order by kodetunjangan";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getNamaTunj($conn,$r_tunjangan){
			$sql = "select namatunjangan from ".static::table('ga_tunjangan')." where kodetunjangan = '$r_tunjangan'";
			$nama = $conn->GetOne($sql);
			
			return $nama;
		}
		
		function getHubunganPeg($conn){
			$sql = "select idhubkerja,hubkerja from ".static::table('ms_hubkerja')." order by idhubkerja";
			$rs = $conn->Execute($sql);
			
			$a_hubungan = array();
			while($row = $rs->FetchRow()){
				$a_hubungan[$row['idhubkerja']] = $row['hubkerja'];
			}
			
			return $a_hubungan;
		}
		
		function getInfoTunjanganPeg($conn,$r_tunjangan){
			if($r_tunjangan == 'T00001'){//T. Struktural
				$sql = "select idjstruktural,jabatanstruktural,level from ".static::table('ms_struktural')." order by idjstruktural";
			}
			
			else if($r_tunjangan == 'T00002'){//T. Fungsional
				$sql = "select idjfungsional,jabatanfungsional from ".static::table('ms_fungsional')." order by idjfungsional";
			}
			
			else if($r_tunjangan == 'T00003'){//T. Transport
				$sql = "select idhubkerja,hubkerja from ".static::table('ms_hubkerja')." order by idhubkerja";
			}
			
			else{//Selain di atas
				$sql = "select idhubkerja,hubkerja from ".static::table('ms_hubkerja')." order by idhubkerja";
			}
			
				return Query::arrQuery($conn, $sql);
		}
		
		function getInfoLevel($conn){
			$sql = "select idjstruktural,level from ".static::table('ms_struktural')." order by idjstruktural";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function listQueryTarifTunjangan($r_tunjangan) {
			if($r_tunjangan == 'T00001'){//T. Struktural
				$select = ",s.jabatanstruktural as namavariabel1,h.hubkerja as namavariabel2,s.level,s.infoleft";
				$leftjoin = "left join ".static::table('ms_struktural')." s on s.idjstruktural = g.variabel1";
				$leftjoin .= " left join ".static::table('ms_hubkerja')." h on h.idhubkerja = g.variabel2";
			}
			
			else if($r_tunjangan == 'T00002'){//T. Fungsional
				$select = ",f.jabatanfungsional as namavariabel1";
				$leftjoin = "left join ".static::table('ms_fungsional')." f on f.idjfungsional = g.variabel1";
			}
			
			else if($r_tunjangan == 'T00003'){//T. Transport
				$select = ",h.hubkerja as namavariabel1";
				$leftjoin = "left join ".static::table('ms_hubkerja')." h on h.idhubkerja = g.variabel1";
			}
			
			else{//Selain di atas
				$select = ",h.hubkerja as namavariabel1";
				$leftjoin = "left join ".static::table('ms_hubkerja')." h on h.idhubkerja = g.variabel1";
			}
			
			$sql = "select g.*{$select} from ".static::table('ga_tariftunjangan')." g {$leftjoin}";
						
			return $sql;
		}
		
		function getTunjangan($conn,$r_key){
			$tunj = $conn->GetOne("select kodetunjangan from ".static::table('ga_tariftunjangan')." where notariftunjangan = $r_key");
			
			return $tunj;
		}
		
		function getLastTunjangan($conn){
			$sql = "select kodetunjangan || '|' || carahitung as kode from ".static::table('ga_tunjangan')." order by kodetunjangan";
			
			return $conn->GetOne($sql);
		}		
		
		function getCTunjangan($conn){
			$sql = "select kodetunjangan || '|' || coalesce(carahitung,'') as kodetunjangan, namatunjangan from ".static::table('ga_tunjangan')." order by kodetunjangan";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function listQueryHitTunjangan($r_periode,$r_tunjangan){
			$sqladd = "";
			if (!empty($r_periode))
				$sqladd = " and pt.periodegaji='$r_periode'";
				
			if (!empty($r_tunjangan))
				$sqladd .= " and pt.kodetunjangan='$r_tunjangan'";
				
			$sql = "select g.*,p.idpegawai,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,js.jabatanstruktural,jf.jabatanfungsional,hk.hubkerja,
					t.tipepeg||' - '||j.jenispegawai as namajenispegawai,pt.nominal
					from ".static::table('ga_historydatagaji')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpeg
					left join ".static::table('ga_tunjanganpeg')." pt on pt.idpegawai = g.idpeg {$sqladd}
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = g.idtipepeg
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = g.idjenispegawai
					left join ".static::table('ms_unit')." u on u.idunit = g.idunit
					left join ".static::table('ms_struktural')." js on js.idjstruktural = g.struktural
					left join ".static::table('ms_fungsional')." jf on jf.idjfungsional = g.fungsional
					left join ".static::table('ms_hubkerja')." hk on hk.idhubkerja = g.idhubkerja";
			
			return $sql;
		}
		
		function infoTunjangan($conn,$r_tunjangan){
			if($r_tunjangan == '')
				$r_tunjangan = $conn->GetOne("select kodetunjangan from ".static::table('ga_tunjangan')." order by kodetunjangan");
			
			$tunj = $conn->GetOne("select namatunjangan from ".static::table('ga_tunjangan')." where kodetunjangan = '$r_tunjangan'");
			
			if($r_tunjangan == 'T00001'){//T. Struktural
				$info1 = 'Jabatan';
				$info2 = 'Hubungan Kerja';
				$filter = 's.jabatanstruktural';
			}else if($r_tunjangan == 'T00002'){//T. Fungsional
				$info1 = 'Jab. Fungsional';
				$filter = 'f.jabatanfungsional';
			}else if($r_tunjangan == 'T00003'){//T. Transport
				$info1 = 'Hubungan Kerja';
				$filter = 'h.hubkerja';
			}else{//Selain di atas
				$info1 = 'Hubungan Kerja';
				$filter = 'h.hubkerja';
			}
			
			$rtunj['namatunjangan'] = $tunj;
			$rtunj['info1'] = $info1;
			$rtunj['info2'] = $info2;
			$rtunj['filter'] = $filter;
						
			return $rtunj;
		}
		
		function aCaraHitungTunj(){
			return array("M" => "Manual","O" => "Otomatis", "T" => "Tarif Parameter");
		}
		
		function getCJenisPegawai($conn){
			$sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai as jenispegawai 
					from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg 
					order by tipepeg";
			
			return Query::arrQuery($conn, $sql);
		}
		
		//Jenis tunjangan dengan jenis pegawai
		function jenisPegTunj($conn){			
			$sql = "select d.* from ".static::table('ga_tunjangandet')." d
					left join ".static::table('ga_tunjangan')." t on t.kodetunjangan = d.kodetunjangan
					where t.isgajitetap = 'Y'";
			$rsd = $conn->Execute($sql);
			
			while($rowd = $rsd->FetchRow()){
				$a_tunjdet[$rowd['kodetunjangan']][$rowd['idjenispegawai']] = $rowd['idjenispegawai'];
			}
			
			return $a_tunjdet;
		}
		
		function getCAllJenisPegawai($conn){
			$sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai as jenispegawai 
					from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg 
					order by tipepeg";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_add = array('all' => '-- Semua Jenis Pegawai --');
			$a_data = array_merge($a_data,$a_add);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['idjenispegawai']] = $row['jenispegawai'];
			}
			
			return $a_data;
		}
		
		function aTunjHak($conn, $r_key){
			$sql = "select j.idjenispegawai, tipepeg || ' - ' || jenispegawai as jenispegawai, kodetunjangan
					from ".static::table('ms_jenispeg')." j
					left join ".static::table('ga_tunjangandet')." t on j.idjenispegawai=t.idjenispegawai and  kodetunjangan='$r_key'
					left join ".static::table('ms_tipepeg')." tp on tp.idtipepeg=j.idtipepeg
					order by tipepeg";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
				
			return $a_data;
		}
		
		function saveTunjHak($conn, $r_key, $a_jenis){
			$sql = "delete from ".static::table('ga_tunjangandet')." where kodetunjangan='$r_key'";
			$conn->Execute($sql);
			
			$recdetail = array();
			$recdetail['kodetunjangan'] = $r_key;
			
			if(count($a_jenis)){
				foreach($a_jenis as $col){
					unset($recdetail['idjenispegawai']);
					$recdetail['idjenispegawai'] = $col;
					
					mGaji::insertRecord($conn, $recdetail, false, 'ga_tunjangandet');
				}
			}
			
			return static::updateStatus($conn);
		}
		
		function getTarifTunj($conn){
			$r_periodetarif = self::getLastPeriodeTarif($conn);
			
			$sql = "select * from ".static::table('ga_tariftunjangan')." where periodetarif = '$r_periodetarif'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				if(!empty($row['variabel2']))
					$a_tariftunj[$row['kodetunjangan']][$row['variabel1']][$row['variabel2']] = $row['nominal'];
				else
					$a_tariftunj[$row['kodetunjangan']][$row['variabel1']] = $row['nominal'];
			}
			
			return $a_tariftunj;
		}
		
		//Simpan gaji tunjangan
		function saveTunjangan($conn,$record){
			$err = 0;
			if(!empty($record['nominal']))
				$err = self::insertRecord($conn,$record,false,'ga_tunjanganpeg');
			
			return $err;
		}
		
		function getTunjTetapSlip($conn,$key){
			list($periode,$idpegawai) = explode('|',$key);
			
			$sql = "select g.* from ".static::table('ga_tunjanganpeg')." g
					left join ".static::table('ga_tunjangan')." t on t.kodetunjangan=g.kodetunjangan
					where g.periodegaji = '$periode' and g.idpegawai = $idpegawai and t.isgajitetap = 'Y'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_tunj[$row['kodetunjangan']] = $row['nominal'];
			}
			
			return $a_tunj;
		}
		
		function getTunjPendapatanLainSlip($conn,$key){
			list($periode,$idpegawai) = explode('|',$key);
			
			$sql = "select g.* from ".static::table('ga_tunjanganpeg')." g
					left join ".static::table('ga_tunjangan')." t on t.kodetunjangan=g.kodetunjangan
					where g.periodegaji = '$periode' and g.idpegawai = $idpegawai and t.isbayargaji = 'Y' and (t.isgajitetap = 'N' or t.isgajitetap is null)";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_tunj[$row['kodetunjangan']] = $row['nominal'];
			}
			
			return $a_tunj;
		}		
		
		//Jenis Tunjangan Tetap bersama gaji
		function getTunjTetapGaji($conn,$r_tipepeg){
			$sql = "select g.kodetunjangan,namatunjangan from ".static::table('ga_tunjangan')." g
					left join ".static::table('ga_tunjangandet')." dt on dt.kodetunjangan = g.kodetunjangan
					where isbayargaji = 'Y' and isgajitetap = 'Y' and idjenispegawai like '$r_tipepeg%' group by g.kodetunjangan order by kodetunjangan";
			$a_jtunj = Query::arrQuery($conn, $sql);
			
			return $a_jtunj;
		}
		
		//Jenis Tunjangan lain
		function getTunjPendapatanLain($conn,$r_tipepeg){
			$sql = "select g.kodetunjangan,namatunjangan from ".static::table('ga_tunjangan')." g
					left join ".static::table('ga_tunjangandet')." dt on dt.kodetunjangan = g.kodetunjangan
					where isbayargaji = 'Y' and (isgajitetap = 'N' or isgajitetap is null) and 
					idjenispegawai like '$r_tipepeg%' group by g.kodetunjangan order by kodetunjangan";
			$a_jttunj = Query::arrQuery($conn, $sql);
			
			return $a_jttunj;
		}
		
		//info gaji pegawai
		function getGajiPegawai($conn,$r_periode){
			$sql = "select * from ".static::table('ga_gajipeg')."
					where periodegaji = '$r_periode' and istunda is null and isfinish is null";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_gapok[$row['idpegawai']] = $row;
			}
			
			return $a_gapok;
		}
		
		//mendapatkan info prosentase kehadiran
		function getProcKehadiran($conn,$periode){
			$sql = "select idpegawai,proctransport,prockesra from ".static::table('pe_presensi')." 
					where periode = '$periode'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_prockehadiran[$row['idpegawai']] = $row;
			}
			
			return $a_prockehadiran;
		}
		
		function hitungTunjangan($conn,$r_periode,$r_tunjangan,$r_sql){		
			if(!empty($r_sql)){
				$a_peg = self::pegFilter($conn,$r_sql);
			}
			
			//pegawai yang gajinya sudah dibayar
			$b_peg = self::sudahBayar($conn,$r_periode);
			
			$sql = "select * from ".static::table('ga_historydatagaji')."
					where gajiperiode = '$r_periode'";
			if(!empty($a_peg))
				$sql .= " and idpeg in ($a_peg)";
			if(!empty($b_peg))
				$sql .= " and idpeg not in ($b_peg)";
				
			$rs = $conn->Execute($sql);
			
			//jenis pegawai yang mendapatkan tunjangan
			$a_jnsPeg = self::jenisPegTunj($conn);
			
			//tarif tunjangan
			$a_tarifTunj = self::getTarifTunj($conn);
			
			//periode tarif sekarang
			$r_periodetarif = self::getLastPeriodeTarif($conn);
			
			//gaji pokok pegawai
			$a_gajipeg = self::getGajiPegawai($conn,$r_periode);
			
			//proposional prosentase kehadiran
			$a_proposionalkehadiran = self::getProcKehadiran($conn,$r_periode);
			
			while($row = $rs->FetchRow()){
				//hapus dulu tunjangan pegawai, agar bersih
				$key = $r_periode.'|'.$row['idpeg'].'|'.$r_tunjangan;
				$colkey = 'periodegaji,idpegawai,kodetunjangan';
				
				list($err,$msg) = self::delete($conn,$key,'ga_tunjanganpeg',$colkey);
				
				if(!$err){
					//T. Struktural
					if($r_tunjangan == 'T00001' and in_array($row['idjenispegawai'],$a_jnsPeg[$r_tunjangan]) and !empty($row['struktural']) and !empty($row['idhubkerja'])){					
						$record = array();
						$record['periodegaji'] = $r_periode;
						$record['periodetarif'] = $r_periodetarif;
						$record['kodetunjangan'] = $r_tunjangan;
						$record['idpegawai'] = $row['idpeg'];
						
						$nominal = $a_tarifTunj[$r_tunjangan][$row['struktural']][$row['idhubkerja']];
						$record['nominal'] = $nominal;
						
						$err = self::saveTunjangan($conn,$record);
					}
					//T. Fungsional
					else if($r_tunjangan == 'T00002' and in_array($row['idjenispegawai'],$a_jnsPeg[$r_tunjangan]) and !empty($row['fungsional'])){
						$record = array();
						$record['periodegaji'] = $r_periode;
						$record['periodetarif'] = $r_periodetarif;
						$record['kodetunjangan'] = $r_tunjangan;
						$record['idpegawai'] = $row['idpeg'];
						
						$nominal = $a_tarifTunj[$r_tunjangan][$row['fungsional']];
						$record['nominal'] = $nominal;
						
						$err = self::saveTunjangan($conn,$record);
					}
					//T. Kesejahteraan
					else if($r_tunjangan == 'T00003' and in_array($row['idjenispegawai'],$a_jnsPeg[$r_tunjangan])){
						$procKesra = $conn->GetOne("select prosentasekali from ".static::table('ga_tunjangan')." where kodetunjangan='T00003'");
						
						$record = array();
						$record['periodegaji'] = $r_periode;
						$record['periodetarif'] = $r_periodetarif;
						$record['kodetunjangan'] = $r_tunjangan;
						$record['idpegawai'] = $row['idpeg'];
						
						$nominal = $a_gajipeg[$row['idpeg']]['gapok'] * ($procKesra/100) * ($a_proposionalkehadiran[$row['idpeg']]['prockesra']/100);
						$record['nominal'] = $nominal;
						
						$err = self::saveTunjangan($conn,$record);
					}
					//T. Transport
					else if($r_tunjangan == 'T00004' and in_array($row['idjenispegawai'],$a_jnsPeg[$r_tunjangan]) and !empty($row['idhubkerja'])){
						$record = array();
						$record['periodegaji'] = $r_periode;
						$record['periodetarif'] = $r_periodetarif;
						$record['kodetunjangan'] = $r_tunjangan;
						$record['idpegawai'] = $row['idpeg'];
						
						$nominal = $a_tarifTunj[$r_tunjangan][$row['idhubkerja']]* ($a_proposionalkehadiran[$row['idpeg']]['proctransport']/100);
						$record['nominal'] = $nominal;
						
						$err = self::saveTunjangan($conn,$record);
					}
				}
			}
			
			if($err)
				$msg = 'Penyimpanan tunjangan gagal';
			else
				$msg = 'Penyimpanan tunjangan berhasil';
			
			return array($err,$msg);
		}
		
		function saveHitTunjangan($conn,$r_periode,$r_tunjangan,$a_post){
			$a_pegawai = $a_post['id'];
			
			//periode tarif sekarang
			$r_periodetarif = self::getLastPeriodeTarif($conn);
			
			if (count($a_pegawai) > 0){
				foreach($a_pegawai as $idpegawai){
					//hapus dulu tunjangan pegawai, agar bersih
					$key = $r_periode.'|'.$idpegawai.'|'.$r_tunjangan;
					$colkey = 'periodegaji,idpegawai,kodetunjangan';
					
					list($err,$msg) = self::delete($conn,$key,'ga_tunjanganpeg',$colkey);					

					if (!$err){
						$record = array();
						$record['periodegaji'] = $r_periode;
						$record['periodetarif'] = $r_periodetarif;
						$record['kodetunjangan'] = $r_tunjangan;
						$record['idpegawai'] = $idpegawai;

						
						$nominal = $a_post['nominal_'.$idpegawai];
						$record['nominal'] = Cstr::cStrDec($nominal);
						
						$err = self::saveTunjangan($conn,$record);
					}	
				}
			}
			
			if($err)
				$msg = 'Penyimpanan tunjangan gagal';
			else
				$msg = 'Penyimpanan tunjangan berhasil';
			
			return array($err,$msg);
		}
		
		//skala penilaian
		function getCSkala($conn, $r_periode){
			$r_periodebobot = $conn->GetOne("select kodeperiodebobot from ".static::table('pa_periodepa')." where kodeperiodepa = '$r_periode'");
			$sql = "select kodeskala, nilaihuruf from ".static::table('pa_skala')." where kodeperiodebobot = '$r_periodebobot' order by kodeskala";
			
			return Query::arrQuery($conn, $sql);
		}
		
		/***********************SALIN TARIF TUNJANGAN***************************/
		function getCSalinTunjangan($conn){
			$sql = "select kodetunjangan, namatunjangan from ".static::table('ga_tunjangan')." 
					where carahitung='T' order by kodetunjangan";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_add = array('all' => '-- Semua Tunjangan --');
			$a_data = array_merge($a_data,$a_add);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['kodetunjangan']] = $row['namatunjangan'];
			}
			
			return $a_data;
		}
		
		function saveSalinTarifTunj($conn,$r_periode,$r_tunjangan,$f_keyPeriode,$f_keyTunjangan,$prosentase){
			$err = 0;
		
			$r_key = $r_periode."|".$f_keyTunjangan;
			list($err,$msg) = self::delete($conn,$r_key,'ga_tariftunjangan','periodetarif,kodetunjangan');
			
			if(!$err){
				if ($f_keyTunjangan !='all')
					$tunjangan= "'".$f_keyTunjangan."'";
				else
					$tunjangan="t.kodetunjangan";
				
				$sql = "insert into ".static::table('ga_tariftunjangan')." (periodetarif,kodetunjangan,variabel1,variabel2,nominal,t_username,t_ipaddress,t_updatetime)
						select '$r_periode',$tunjangan,variabel1,variabel2,nominal+(nominal * $prosentase),'".Modul::getUserName()."','".date('Y-m-d H:i:s')."','".$_SERVER['REMOTE_ADDR']."' 
						from ".static::table('ga_tariftunjangan')." t
						left join ".static::table('ga_tunjangan')." g on g.kodetunjangan=t.kodetunjangan
						where t.periodetarif='$f_keyPeriode' and g.carahitung='T' ";
				
				if ($f_keyTunjangan !='all')
					$sql .= "and t.kodetunjangan='$f_keyTunjangan'";
				
				$conn->Execute($sql);
			}
			if($err)
				$msg = 'Salin tarif tunjangan gagal, data masih digunakan';
			else
				$msg = 'Salin tarif tunjangan berhasil';
			
			return array($err,$msg);
		}
		
		
		//********************************************P A J A K****************************************************
		
		function listQueryPajak(){
			$sql = "select * from ".static::table('ga_pajak');
			
			return $sql;
		}
		
		function getListPajakDet($conn,$r_key){
			$sql = "select * from ".static::table('ga_pajakdet')." where idpajak = '$r_key'";
			$rs = $conn->Execute($sql);
			
			return $rs;
		}
		
		function getPajak($conn){
			$sql = "select * from ".static::table('ga_pajak')." where isaktif = 'Y'";
			$row = $conn->GetRow($sql);
			
			if(!empty($row))
				return array(true,$row);
			else
				return array(false,$row);
		}
		
		function getPajakDet($conn,$r_key){
			$sql = "select * from ".static::table('ga_pajakdet')." where idpajak = '$r_key' order by batasbawah";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$b_atas = empty($row['batasatas']) ? '-' : $row['batasatas'];
				$a_bts[$b_atas] = $row['prosentase']/100;
			}
						
			if(!empty($a_bts))
				return array(true,$a_bts);
			else
				return array(false,$a_bts);
		}
		
		function hitPajak($conn,$r_periode,$r_sql=''){		
			if(!empty($r_sql)){
				$a_peg = self::pegFilter($conn,$r_sql);
			}
			
			//pegawai yang gajinya sudah dibayar
			$b_peg = self::sudahBayar($conn,$r_periode);
			
			$sql = "select g.*,gh.*
					from ".static::table('ga_gajipeg')." g
					left join ".static::table('ga_historydatagaji')." gh on gh.idpeg = g.idpegawai and gh.gajiperiode = g.periodegaji
					where (g.istunda = 'T' or g.istunda is null) and g.periodegaji = '$r_periode'";
			if(!empty($a_peg))
				$sql .= " and g.idpegawai in ($a_peg)";
			if(!empty($b_peg))
				$sql .= " and g.idpegawai not in ($b_peg)";
				
			$rs = $conn->Execute($sql);
			
			list($isset,$pjk) = self::getPajak($conn);
			if($isset)
				list($isset,$pjkd) = self::getPajakDet($conn,$pjk['idpajak']);
			
			if($isset){
				while($row = $rs->FetchRow()){
					$g_bruto = $row['gajibruto'];
										
					//mendapatkan biaya jabatan
					$biaya_jbt = $pjk['prosentasepotongan']/100 * $g_bruto;
					if($biaya_jbt > $pjk['maxpotongan'])
						$biaya_jbt = $pjk['maxpotongan'];
					
					//mendapatkan premi pensiun
					$p_pensiun = 0;
					/*$p_pensiun = $row['premipensiun'];
					if(!empty($pen[$row['nip']]))
						$p_pensiun = $p_pensiun + $pen[$row['nip']];*/
						
					$pengurangan = $biaya_jbt + $p_pensiun;
					
					$g_netto = $g_bruto - $pengurangan;
					
					$pengali = 12;
					$g_netto_th = $g_netto * $pengali;//gaji netto disetahunkan jika bukan akhir dan masa kerja pegawai sudah lebih dari setahun
					
					//perhitungan penghasilan tidak kena pajak (ptkp)
					$sendiri = $pjk['ptkppribadi'];
					if($row['statusnipah'] == 'N')
						$menipah = $pjk['ptkpkawin'];
					else
						$menipah = 0;
					
					if($row['jmlanak'] > $pjk['maxanak'])
						$anak = $pjk['maxanak'] * $pjk['ptkpanak'];
					else
						$anak = $row['jmlanak'] * $pjk['ptkpanak'];
					
					if($row['jeniskelamin'] == 'P' and $row['statusnipah'] == 'N' and $row['ispasangankerja'] == 'Y')//wanita, yang punya suami bekerja dihitung seperti sendiri
						$ptkp = $sendiri + $anak;
					else
						$ptkp = $sendiri + $menipah + $anak;
					
					//pengurangan netto setahun dengan total PTKP
					$pkp_th = $g_netto_th - $ptkp;
					
					$pph_th = 0;
					$tpkp_th = $pkp_th; // menyimpan pkp pada proses perpajakan
					
					//menghitung pph
					foreach($pjkd as $t_limit => $t_persen) {
						if($t_limit != '-' and $tpkp_th > $t_limit) {
							$pph_th += ($t_persen * $t_limit);
							$tpkp_th -= $t_limit;
						}
						else {
							$pph_th += ($t_persen * $tpkp_th);
							break;
						}
					}
					
					$pph = $pph_th/$pengali;
					if($pph <= 0)
						$pph = 0;
					
					$record = array();
					$record['pph'] = $pph;
					
					list($err,$msg) = self::saveGaji($conn,$record,$r_periode,$row['idpegawai']);
					if($err)
						$msg = 'Perhitungan pajak gagal';
					else
						$msg = 'Perhitungan pajak berhasil';
							
				}
			}else{
				$err = true;
				$msg = 'Silahkan setting Pajak terlebih dahulu';
			}
			
			return array($err,$msg);
		}
		
		//***************************************** GAJI PEGAWAI ***********************************************
		//Bayar gaji
		function bayarGaji($conn,$r_periode,$r_sql=''){		
			if(!empty($r_sql)){
				$a_peg = self::pegFilter($conn,$r_sql);
			}
			
			//pegawai yang gajinya sudah dibayar
			$b_peg = self::sudahBayar($conn,$r_periode);
			
			$sql = "select g.idpegawai,g.periodegaji,gh.norekening from ".static::table('ga_gajipeg')." g
					left join ".static::table('ga_historydatagaji')." gh on gh.gajiperiode = g.periodegaji and gh.idpeg = g.idpegawai
					where g.periodegaji = '$r_periode' and (g.istunda = 'T' or g.istunda is null)";
			if(!empty($a_peg))
				$sql .= " and g.idpegawai in ($a_peg)";
			if(!empty($b_peg))
				$sql .= " and g.idpegawai not in ($b_peg)";
			
			$rs = $conn->Execute($sql);
			
			$record = array();
			$record['isfinish'] = 'Y';
			$record['tgldibayarkan'] = date('Y-m-d');
			
			while($row = $rs->FetchRow()){
				if(!empty($row['norekening']))
					$record['istransfer'] = 'Y';
				else
					$record['istransfer'] = 'T';
				
				$key = $row['idpegawai'].'|'.$row['periodegaji'];
				$colkey = 'idpegawai,periodegaji';
				list($err,$msg) = self::updateRecord($conn,$record,$key,true,'ga_gajipeg',$colkey);
			}
			
			list($err,$msg) = self::updateStatus($conn);
			
			if(!$err){
				self::bayarPinjaman($conn,$r_periode);
			}
			
			return array($err,$msg);
		}
		
		//daftar gaji pegawai
		function listQueryCekalGajiPegawai() {
			$sql = "select g.*,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,substring(gh.masakerjapend,1,2)||' tahun ' as masakerja,
					t.tipepeg||' - '||j.jenispegawai as namajenispegawai
					from ".static::table('ga_gajipeg')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpegawai
					left join ".static::table('ga_historydatagaji')." gh on gh.idpeg = g.idpegawai and gh.gajiperiode = g.periodegaji
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = gh.idtipepeg
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = gh.idjenispegawai
					left join ".static::table('ms_unit')." u on u.idunit = gh.idunit
					left join ".static::table('lv_jenjangpendidikan')." pd on pd.idpendidikan = gh.pendidikan";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {			
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once($conf['gate_dir'].'model/m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
					break;				
				case 'tahun':
					if($key != 'all')
						return "date_part('year', tmtmulai) = '$key'";
					else
						return "(1=1)";
					
					break;				
				case 'tunjangan':
					if($key != 'all')
						return "g.kodetunjangan = '$key'";
					else
						return "(1=1)";
					
					break;			
				case 'tahunpot':
					if($key != 'all')
						return "substring(g.periodetarif,1,4) = '$key'";
					else
						return "(1=1)";
					
					break;				
				case 'potonganpegawai':
					if($key != 'all')
						return "g.kodepotongan = '$key'";
					else
						return "(1=1)";
					
					break;				
				case 'jenispegawai':
					if($key != 'all')
						return "p.idjenispegawai = '$key'";
					else
						return "(1=1)";
					
					break;					
				case 'periodegaji':
					return "g.periodegaji = '$key'";
					break;					
				case 'periodetarif':
					return "periodetarif = '$key'";
					break;
				case 'pendidikan':
					return "g.idpendidikan = '$key'";
					break;
				case 'jnstunjangan':
					return "g.kodetunjangan = '$key'";
					break;
				case 'jnshonor':
					return "g.kodehonor = '$key'";
					break;
				case 'periodehist':
					return "g.gajiperiode = '$key'";
					break;
				case 'tunda':
					if(!empty($key)){
						if($key == 'Y')
							return "g.istunda = 'Y'";
						else
							return "(g.istunda = 'T' or g.istunda is null)";
					}
					break;
				case 'bayar':
					if(!empty($key)){
						if($key == 'Y')
							return "g.isfinish = 'Y'";
						else
							return "(g.isfinish = 'T' or g.isfinish is null)";
					}
					break;
				case 'bayarlembur':
					if(!empty($key)){
						if($key == 'Y')
							return "g.isbayar = 'Y'";
						else
							return "(g.isbayar = 'T' or g.isbayar is null)";
					}
					break;
			}
		}	
		
		function filterJenis($conn){
			$sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					order by j.idtipepeg";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function filterJenisPeg($conn,$jenis){
			$sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					where t.idtipepeg in ('$jenis')
					order by j.idtipepeg";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function isBayarGaji($conn,$r_periode){	
			$sql = "select idpegawai from ".static::table('ga_gajipeg')." where periodegaji = '$r_periode' and isfinish = 'Y'";
			$rs = $conn->Execute($sql);
			
			$a_peg = array();
			while($row = $rs->FetchRow()){
				$a_peg[$row['idpegawai']] = $row['idpegawai'];
			}
			
			return $a_peg;
		}
		
		//************************************** PENARIKAN DATA PEGAWAI ****************************************
		
		//Daftar penarikan data
		function listQueryHistoryGaji() {
			$sql = "select g.*,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,s.jabatanstruktural,t.tipepeg||' - '||j.jenispegawai as namajenispegawai,
					".static::schema.".get_mkacuangaji(g.idpeg)||' tahun ' as mkgaji
					from ".static::table('ga_historydatagaji')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpeg
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = g.idtipepeg
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = g.idjenispegawai
					left join ".static::table('ms_unit')." u on u.idunit = g.idunit
					left join ".static::table('lv_jenjangpendidikan')." pd on pd.idpendidikan = g.pendidikan
					left join ".static::table('ms_struktural')." s on s.idjstruktural = g.struktural";
			
			return $sql;
		}
		
		function listQueryHistoryGajiPerPegawai() {
			$a_peg = self::getPegawaiGaji('TARIKDATA');
			
			$sql = "select g.*,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,s.jabatanstruktural,t.tipepeg||' - '||j.jenispegawai as namajenispegawai,
					".static::schema.".get_mkacuangaji(g.idpeg)||' tahun ' as mkgaji
					from ".static::table('ga_historydatagaji')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpeg
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = g.idtipepeg
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = g.idjenispegawai
					left join ".static::table('ms_unit')." u on u.idunit = g.idunit
					left join ".static::table('lv_jenjangpendidikan')." pd on pd.idpendidikan = g.pendidikan
					left join ".static::table('ms_struktural')." s on s.idjstruktural = g.struktural";
			
			if(!empty($a_peg))
				$sql .= " where g.idpeg in ($a_peg)";
			else
				$sql .= " where 1=0";
				
			return $sql;
		}
		
		function getDataHistoryGaji($conn,$r_key){
			list($idpegawai,$periodegaji) = explode('|',$r_key);
			
			$sql = "select g.*,pg.namaperiode as namaperiodegaji,pt.namaperiode as namaperiodetarif,
					nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pk.golongan,js.jabatanstruktural,ms.jenispejabat,pd.namapendidikan,t.tipepeg||' - '||j.jenispegawai as namajenispegawai,
					h.hubkerja,s.namastatusaktif,case when g.statusnipah = 'S' then 'Single' when g.statusnipah = 'N' then 'nipah' 
					when g.statusnipah = 'D' then 'Duda' when g.statusnipah = 'J' then 'Janda' end as statusnipah,
					case when g.ispasangankerja = 'Y' then 'Ya' else 'Tidak' end as pasangankerja,
					substring(g.masakerja,1,2)||' tahun ' || substring(g.masakerja,3,2)||' bulan' as masakerja,
					case when g.jeniskelamin = 'L' then 'Laki-laki' when g.jeniskelamin = 'P' then 'Perempuan' else '' end  as jnskelamin
					from ".static::table('ga_historydatagaji')." g
					left join ".static::table('ga_periodegaji')." pg on pg.periodegaji = g.gajiperiode
					left join ".static::table('ga_periodetarif')." pt on pt.periodetarif = g.tarifperiode
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpeg
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = g.idtipepeg
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = g.idjenispegawai
					left join ".static::table('ms_hubkerja')." h on h.idhubkerja = g.idhubkerja
					left join ".static::table('lv_statusaktif')." s on s.idstatusaktif = g.idstatusaktif
					left join ".static::table('ms_unit')." u on u.idunit = g.idunit
					left join ".static::table('ms_pangkat')." pk on pk.idpangkat = g.pangkatpeg
					left join ".static::table('lv_jenjangpendidikan')." pd on pd.idpendidikan = g.pendidikan
					left join ".static::table('ms_struktural')." js on js.idjstruktural = g.struktural
					left join ".static::table('ms_jenispejabat')." ms on ms.idjnspejabat = g.jnspejabat
					where idpeg = $idpegawai and gajiperiode = '$periodegaji'";
			
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		//mendapatkan data presensi pegawai
		function getDataPresensi($conn,$r_periodegaji){
			$rowg = self::getDataPeriodeGaji($conn,$r_periodegaji);
			$thngaji = substr($rowg['tglawalhitung'],0,4);
			$blngaji = substr($rowg['tglawalhitung'],5,2);
			if($blngaji == '01')//jika bulan januari
				$periodeabs = ($thngaji-1).'12';
			else
				$periodeabs = $thngaji.(str_pad(($blngaji-1), 2, "0", STR_PAD_LEFT));
			
			$rs = $conn->Execute("select idpegawai,hadir,sakit,izin,alpa,cuti from ".static::table('pe_presensi')." where periode = '$periodeabs' order by idpegawai");
			
			while($row = $rs->FetchRow()){
				$rowa[$row['idpegawai']]['H'] = $row['hadir'];
				$rowa[$row['idpegawai']]['S'] = $row['sakit'];
				$rowa[$row['idpegawai']]['I'] = $row['izin'];
				$rowa[$row['idpegawai']]['A'] = $row['alpa'];
				$rowa[$row['idpegawai']]['C'] = $row['cuti'];
			}
			
			return $rowa;
		}
		
		//proses penarikan/ penguncian data pegawai
		function tarikData($conn,$r_periode,$r_unit='',$r_idpegawai=''){
			$r_periodetarif = self::getLastPeriodeTarif($conn);			
			
			if(!empty($r_unit)){
				global $conn, $conf;
				require_once($conf['gate_dir'].'model/m_unit.php');
				
				$row = mUnit::getData($conn,$r_unit);
				
				$sqladd = " and u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
			else if(!empty($r_idpegawai))
				$sqladd = " and p.idpegawai = $r_idpegawai";
			
			//mendapatkan data kehadiran pegawai
			$abs = self::getDataPresensi($conn,$r_periode);
			
			$sql = "select p.idpegawai,p.jeniskelamin,p.statusnipah,p.jmlanak,p.npwp,js.idjstruktural,js.idjnspejabat,p.idpendidikan,p.idpangkat,
					p.idtipepeg,p.idjenispegawai,p.idhubkerja,jk.jamkerja,p.ispasangankerja,p.idstatusaktif,p.idjfungsional,
					".static::schema.".get_mkpendidikan(p.idpegawai) as masakerjapend, 
					".static::schema.".get_mkpengabdian(p.idpegawai) as masakerja,
					p.idunit,p.norekening,p.anrekening
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_unit')." u on u.idunit = p.idunit
					left join ".static::table('lv_statusaktif')." a on a.idstatusaktif = p.idstatusaktif
					left join ".static::table('ms_kelompokdosen')." jk on jk.kodekeldosen = p.kodekeldosen
					left join ".static::table('pe_rwtstruktural')." js on js.nourutjs = (select jss.nourutjs from ".static::table('pe_rwtstruktural')." jss
						where jss.idpegawai = p.idpegawai and jss.isvalid = 'Y' and jss.isaktif = 'Y' order by coalesce(isutama,'T') desc,tmtmulai desc limit 1)
					where a.iskeluar = 'T' and p.idhubkerja not in ('HR','HP') {$sqladd}";
			
			$rs = $conn->Execute($sql);

			print_r($rs);
			die();
			
			$i=0;
			while($row = $rs->FetchRow()){
				$i++;
				
				$record = array();
				$record['gajiperiode'] = $r_periode;
				$record['tarifperiode'] = $r_periodetarif;
				$record['idpeg'] = $row['idpegawai'];
				$record['jeniskelamin'] = $row['jeniskelamin'];
				$record['statusnipah'] = $row['statusnipah'];
				$record['ispasangankerja'] = $row['ispasangankerja'];
				$record['jmlanak'] = $row['jmlanak'];
				$record['npwp'] = $row['npwp'];
				$record['idtipepeg'] = $row['idtipepeg'];
				$record['idjenispegawai'] = $row['idjenispegawai'];
				$record['idhubkerja'] = $row['idhubkerja'];
				$record['idstatusaktif'] = $row['idstatusaktif'];
				$record['jamkerja'] = $row['jamkerja'];
				$record['struktural'] = $row['idjstruktural'];
				$record['jnspejabat'] = $row['idjnspejabat'];
				$record['pendidikan'] = $row['idpendidikan'];
				$record['fungsional'] = $row['idjfungsional'];
				$record['pangkatpeg'] = $row['idpangkat'];
				$record['idunit'] = $row['idunit'];
				$record['masakerjapend'] = $row['masakerjapend'];
				$record['masakerja'] = $row['masakerja'];
				$record['norekening'] = $row['norekening'];
				$record['anrekening'] = $row['anrekening'];
				$record['tgltarik'] = date('Y-m-d');
				$record['hadir'] = $abs[$row['idpegawai']]['H'];
				$record['sakit'] = $abs[$row['idpegawai']]['C'];
				$record['izin'] = $abs[$row['idpegawai']]['S'];
				$record['alpa'] = $abs[$row['idpegawai']]['A'];
				$record['cuti'] = $abs[$row['idpegawai']]['C'];
				
				$isexist = $conn->GetOne("select 1 from ".static::table('ga_historydatagaji')." where idpeg = ".$record['idpeg']." and gajiperiode = '".$record['gajiperiode']."'");
				if(empty($isexist))
					list($err,$msg) = self::insertRecord($conn,$record,true,'ga_historydatagaji');
				else{
					$key = $record['idpeg'].'|'.$record['gajiperiode'];
					$colkey = 'idpeg,gajiperiode';
					list($err,$msg) = self::updateRecord($conn,$record,$key,true,'ga_historydatagaji',$colkey);
				}
			}
			
			if($i == 0){
				$err = 1;
				$msg = 'Tidak ada data pegawai yang ditarik';
			}
			
			return array($err,$msg);
		}	
		
		//cek apakah sudah ditarik data pegawai
		function isTarikData($conn,$r_unit,$r_periode){
			global $conn, $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
			
			$row = mUnit::getData($conn,$r_unit);
			
			$sql = "select count(*) from ".static::table('ga_historydatagaji')." g
					left join ".static::table('ms_unit')." u on u.idunit = g.idunit
					where gajiperiode = '$r_periode' and u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			$ntarik = $conn->GetOne($sql);
			
			$istarik = !empty($ntarik) ? true : false;
			return $istarik;
		}
		
		function deleteTarikData($conn,$r_key){
			list($r_pegawai,$r_periode) = explode('|',$r_key);
			$r_periodetarif = self::getLastPeriodeTarif($conn);	
			
			$conn->Execute("delete from ".static::table('ga_tunjanganpeg')." where idpegawai = $r_pegawai and periodegaji = '$r_periode' and periodetarif = '$r_periodetarif'");
			$conn->Execute("delete from ".static::table('ga_potongan')." where idpegawai = $r_pegawai and periodegaji = '$r_periode'");
			$conn->Execute("delete from ".static::table('ga_gajipeg')." where idpegawai = $r_pegawai and periodegaji = '$r_periode'");
			
			$conn->Execute("delete from ".static::table('ga_historydatagaji')." where idpeg = $r_pegawai and gajiperiode = '$r_periode'");
			
			return self::deleteStatus($conn);
		}
		
		function cekTarikPegawai($conn,$r_pegawai){
			$sql = "select p.idpegawai from ".static::table('ms_pegawai')." p
					left join ".static::table('lv_statusaktif')." a on a.idstatusaktif = p.idstatusaktif
					where a.iskeluar = 'T' and p.idhubkerja <> 'HR' and idpegawai = $r_pegawai";
					
			$idpegawai = $conn->GetOne($sql);
			
			return $idpegawai;
		}
		
		function setPegawaiGajiTetap($idpegawai,$key){
			$isEx = $_SESSION[SITE_ID]['VAR'][$key];
			if(!empty($isEx)){
				$a_id = array();
				$a_id = explode(',',$_SESSION[SITE_ID]['VAR'][$key]);
				if(!in_array($idpegawai,$a_id))
					$_SESSION[SITE_ID]['VAR'][$key] .= ','.$idpegawai;
			}else
				$_SESSION[SITE_ID]['VAR'][$key] = $idpegawai;
		}
		
		function getPegawaiGaji($key){
			return $_SESSION[SITE_ID]['VAR'][$key];
		}
		
		function unsetPegawaiGaji($key,$idpegawai){
			$isEx = $_SESSION[SITE_ID]['VAR'][$key];
			if(!empty($isEx)){
				$a_id = array();
				$a_id = explode(',',$isEx);
				array_splice($a_id,array_search($idpegawai,$a_id),1);
				$_SESSION[SITE_ID]['VAR'][$key] = implode(',',$a_id);
			}
		}
		
		function pegFilter($conn,$r_sql){
			$rs = $conn->Execute($r_sql);
			
			$a_pegawai = array();
			while($row = $rs->FetchRow()){
				$a_pegawai[$row['idpegawai']] = $row['idpegawai'];
			}
			
			if(count($a_pegawai)>0){
				$i_peg = implode(',',$a_pegawai);
			}
			
			return $i_peg;
		}
		
		function getWajibHadir($conn){
			$last = self::getLastDataPeriodeGaji($conn);
			
			$sql = "select coalesce(count(*),0) as wajibhadir,idpegawai from ".static::table('pe_presensidet')."
					where tglpresensi between '".$last['tglawalhitung']."' and '".$last['tglakhirhitung']."'
					group by idpegawai";	
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_wajibhadir[$row['idpegawai']] = $row['wajibhadir'];
			}
			
			return $a_wajibhadir;			
		}
		
		//hasil PA
		function getHasilPA($conn){
			//periode gaji terakhir
			$periodegaji = self::getLastPeriodeGaji($conn);
			$tahunperiode = substr($periodegaji,0,4);
			
			//periode penilaian tahun sebelumnya
			$periodepa = $conn->GetOne("select kodeperiode from ".static::table('pa_periode')." 
						where substring(kodeperiode,1,4) = '".($tahunperiode-1)."' order by kodeperiode desc limit 1");
			
			//hasil penilaian
			if(!empty($periodepa)){
				$sql = "select idpegawai,kategorinilai from ".static::table('pa_nilaiakhir')."
						where kodeperiode = '$periodepa'";
				$rsh = $conn->Execute($sql);
				
				while($rowh = $rsh->FetchRow()){
					$a_hasil[$rowh['idpegawai']] = $rowh['kategorinilai'];
				}
			}
			return $a_hasil;
		}
		
		//prosentase tunjangan prestasi
		function getProcPrestasi($conn){
			//periode gaji terakhir
			$periodegaji = self::getLastPeriodeGaji($conn);
			$tahunperiode = substr($periodegaji,0,4);
			
			//periode penilaian tahun sebelumnya
			$periodepa = $conn->GetOne("select kodeperiode from ".static::table('pa_periode')." 
						where substring(kodeperiode,1,4) = '".($tahunperiode-1)."' order by kodeperiode desc limit 1");
			
			//acuan periodegaji
			if(!empty($periodepa)){
				$periodeacuan = $conn->GetOne("select periodegaji from ".static::table('ga_patunjprestasi')." 
							where kodeperiode = '$periodepa' order by kodeperiode desc limit 1");
				
				//select prosentase tunjangan prestasi
				if(!empty($periodeacuan)){
					$sql = "select kategorinilai,procnilai from ".static::table('ga_patunjprestasi')."
							where kodeperiode = '$periodepa' and periodegaji = '$periodeacuan'";
					$rsp = $conn->Execute($sql);
					
					while($rowp = $rsp->FetchRow()){
						$a_proc[$rowp['kategorinilai']] = $rowp['procnilai'];
					}
				}
			}
			return $a_proc;
		}
		
		//Simpan gaji
		function saveGaji($conn,$record,$r_periode,$idpegawai){
			$isexist = $conn->GetOne("select 1 from ".static::table('ga_gajipeg')." where periodegaji = '$r_periode' and idpegawai = $idpegawai");
			
			if(empty($isexist))
				$err = self::insertRecord($conn,$record,false,'ga_gajipeg');
			else{
				$key = $r_periode.'|'.$idpegawai;
				$colkey = 'periodegaji,idpegawai';
				$err = self::updateRecord($conn,$record,$key,false,'ga_gajipeg',$colkey);
			}
			
			if($err)
				$msg = 'Penyimpanan gaji gagal';
			else
				$msg = 'Penyimpanan gaji berhasil';
			
			return array($err,$msg);
		}
		
		//Informasi gaji
		function getInfoGaji($conn,$key){
			list($periode,$idpegawai) = explode('|',$key);
			
			$sql = "select g.*,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					p.idjenispegawai, p.idtipepeg, js.jabatanstruktural,pd.namapendidikan,	substring(gh.masakerja,1,2)||' thn. ' || substring(gh.masakerja,3,2)||' bln.' as mkgaji,pk.golongan,
					gp.namaperiode
					from ".static::table('ga_gajipeg')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai=g.idpegawai
					left join ".static::table('ga_historydatagaji')." gh on gh.gajiperiode=g.periodegaji and gh.idpeg = g.idpegawai
					left join ".static::table('ms_struktural')." js on js.idjstruktural=gh.struktural
					left join ".static::table('lv_jenjangpendidikan')." pd on pd.idpendidikan=gh.pendidikan
					left join ".static::table('ms_pangkat')." pk on pk.idpangkat = gh.pangkatpeg
					left join ".static::table('ga_periodegaji')." gp on gp.periodegaji = g.periodegaji
					where g.periodegaji = '$periode' and g.idpegawai = $idpegawai";
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		function iyaTidak(){
			$a_konf = array('Y' => 'Iya', 'T' => 'Tidak');
			
			return $a_konf;
		}
		
		function getCCekal(){
			$a_cekal = array('' => '-- Semua --', 'Y' => 'Ditunda', 'T' => 'Tidak Ditunda');
			
			return $a_cekal;
		}
		
		function getCBayar(){
			$a_bayar = array('' => '-- Semua --', 'Y' => 'Sudah Dibayar', 'T' => 'Belum Dibayar');
			
			return $a_bayar;
		}
		
		function listQueryGajiBayar() {
			$sql = "select g.*,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,substring(gh.masakerjapend,1,2)||' tahun ' as masakerja,
					t.tipepeg||' - '||j.jenispegawai as namajenispegawai
					from ".static::table('ga_gajipeg')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpegawai
					left join ".static::table('ga_historydatagaji')." gh on gh.idpeg = g.idpegawai and gh.gajiperiode = g.periodegaji
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = gh.idtipepeg
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = gh.idjenispegawai
					left join ".static::table('ms_unit')." u on u.idunit = gh.idunit
					left join ".static::table('lv_jenjangpendidikan')." pd on pd.idpendidikan = gh.pendidikan
					where (g.istunda = 'T' or g.istunda is null)";
			
			return $sql;
		}
		
		//pegawai yang sudah dibayarkan gajinya
		function sudahBayar($conn,$r_periode){
			$sql = "select idpegawai from ".static::table('ga_gajipeg')." where periodegaji = '$r_periode' and isfinish = 'Y'";
			$rs = $conn->Execute($sql);
			
			$a_peg = array();
			while($row = $rs->FetchRow()){
				$a_peg[$row['idpegawai']] = $row['idpegawai'];
			}
			
			if(count($a_peg)>0)
				$i_peg = implode(",",$a_peg);
				
			return $i_peg;
		}
		
		/**************************************************** HONOR ******************************************************/
		
		//Daftar penarikan data mengajar
		function listQueryHistoryMengajar() {
			$sql = "select g.*,g.waktumulai||' - '||g.waktuselesai as waktumengajar,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,t.tipepeg||' - '||j.jenispegawai as namajenispegawai,mk.namamk,
					case 
						when g.jeniskuliah = 'K' then 'Kuliah' 
						when g.jeniskuliah = 'P' then 'Praktikum'
						when g.jeniskuliah = 'R' then 'Tutorial'
						when g.jeniskuliah = 'Q' then 'Quiz'
						when g.jeniskuliah = 'T' then 'UTS'
						when g.jeniskuliah = 'U' then 'UAS'
						when g.jeniskuliah = 'H' then 'HER'
					end as jeniskul
					from ".static::table('ga_mengajarlog')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpegawai
					left join akademik.ak_matakuliah mk on mk.kodemk = g.kodemk and mk.thnkurikulum = g.thnkurikulum
					left join ".static::table('ga_historydatagaji')." gh on gh.idpeg = g.idpegawai and gh.gajiperiode = g.periodegaji
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = gh.idtipepeg
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = gh.idjenispegawai
					left join ".static::table('ms_unit')." u on u.kodeunit = g.kodeunit";
			
			return $sql;
		}		
		
		function getDataHistoryMengajar($conn,$r_key){
			list($tglkuliah,$perkuliahanke,$periode,$thnkurikulum,$kodeunit,$kodemk,$kelasmk) = explode('|',$r_key);
			
			$sql = "select g.*,pg.namaperiode as namaperiodegaji,mk.namamk,
					p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,t.tipepeg||' - '||j.jenispegawai as namajenispegawai
					from ".static::table('ga_mengajarlog')." g
					left join ".static::table('ga_periodegaji')." pg on pg.periodegaji = g.periodegaji
					left join akademik.ak_matakuliah mk on mk.kodemk = g.kodemk
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpegawai
					left join ".static::table('ga_historydatagaji')." gh on gh.idpeg = g.idpegawai and gh.gajiperiode = g.periodegaji
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = gh.idtipepeg
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = gh.idjenispegawai
					left join ".static::table('ms_unit')." u on u.kodeunit = g.kodeunit
					left join ".static::table('lv_jenjangpendidikan')." pd on pd.idpendidikan = gh.pendidikan
					where g.tglkuliah = '$tglkuliah' and g.perkuliahanke = $perkuliahanke and g.periode = '$periode' and g.thnkurikulum = '$thnkurikulum' and 
					g.kodeunit = '$kodeunit' and g.kodemk = '$kodemk' and g.kelasmk = '$kelasmk'";
			
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		// jenis kuliah
		function jenisKuliah() {
			$data = array('K' => 'Kuliah','P'=>'Praktikum','R'=>'Tutorial','Q' => 'Quiz','T' => 'UTS','U' => 'UAS','H' => 'HER');
			
			return $data;
		}
		
		//proses penarikan/ penguncian data mengajar
		function tarikDataMengajar($conn,$r_periode){
			//data periode gaji
			$pg = self::getDataPeriodeGaji($conn,$r_periode);
			
			$sql = "select a.*,p.idpegawai,sdm.f_diffmenit(lpad(a.waktumulai::character varying,4,'0'),lpad(a.waktuselesai::character varying,4,'0')) as jmljam,up.kodeunit as fakultas
					from ".self::schema('akademik')."ak_kuliah a
					left join ".static::table('ms_pegawai')." p on p.nip = a.nipdosen
					left join ".static::table('ms_unit')." u on u.kodeunit = a.kodeunit
					left join ".static::table('ms_unit')." up on up.idunit = u.parentunit
					left join ".static::table('ga_historydatagaji')." h on h.idpeg = p.idpegawai and h.gajiperiode = '$r_periode'
					where a.tglkuliah between '".$pg['tglawalhitung']."' and '".$pg['tglakhirhitung']."' and a.statusperkuliahan = 'S' and a.isvalid = -1";
			
			$rs = $conn->Execute($sql);
			
			$i=0;
			while($row = $rs->FetchRow()){
				if(!empty($row['idpegawai'])){
					$i++;
					
					$record = array();
					$record = $row;
					$record['periodegaji'] = $r_periode;
					$record['jmljam'] = (int)$row['jmljam']/60;
					
					$isexist = $conn->GetOne("select 1 from ".static::table('ga_mengajarlog')." where tglkuliah = '".$record['tglkuliah']."' and
								perkuliahanke = ".$record['perkuliahanke']." and periode = '".$record['periode']."' and thnkurikulum = '".$record['thnkurikulum']."' and
								kodeunit = '".$record['kodeunit']."' and kodemk = '".$record['kodemk']."' and kelasmk = '".$record['kelasmk']."'");
					if(empty($isexist))
						list($err,$msg) = self::insertRecord($conn,$record,true,'ga_mengajarlog');
					else{
						$key = $record['tglkuliah'].'|'.$record['perkuliahanke'].'|'.$record['periode'].'|'.$record['thnkurikulum'].'|'.$record['kodeunit'].'|'.$record['kodemk'].'|'.$record['kelasmk'];
						$colkey = 'tglkuliah,perkuliahanke,periode,thnkurikulum,kodeunit,kodemk,kelasmk';
						list($err,$msg) = self::updateRecord($conn,$record,$key,true,'ga_mengajarlog',$colkey);
					}
				}
			}
			
			if($i == 0){
				$err = 1;
				$msg = 'Tidak ada data mengajar dosen yang ditarik';
			}
			
			return array($err,$msg);
		}
		
		//Daftar penarikan data bimbingan
		function listQueryHistoryBimbingan() {
			$sql = "select g.*,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,u.namaunit
					from ".static::table('ga_bimbinganlog')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpegawai
					left join ".static::table('ga_historydatagaji')." gh on gh.idpeg = g.idpegawai and gh.gajiperiode = g.periodegaji
					left join ".static::table('ms_unit')." u on u.idunit = gh.idunit";
			
			return $sql;
		}
		
		//penarikan data bimbingan mengajar mahasiswa
		function tarikDataBimbingan($conn,$r_periode){
			//data periode gaji
			$pg = self::getDataPeriodeGaji($conn,$r_periode);
			
			$sql = "select p.idpegawai from akademik.ak_bimbingan b
					left join ".static::table('ms_pegawai')." p on p.nip = b.nip
					where disetujui=1 and tglbimbingan between '".$pg['tglawalhitung']."' and '".$pg['tglakhirhitung']."' 
					group by idta,idpegawai";
			
			$rs = $conn->Execute($sql);
			
			$i=0;
			while($row = $rs->FetchRow()){
				if(!empty($row['idpegawai'])){
					$i++;					
					$a_data[$row['idpegawai']]++;
				}
			}
			
			if($i == 0){
				$err = 1;
				$msg = 'Tidak ada data bimbingan mahasiswa yang ditarik';
			}else{
				if(count($a_data)>0){
					foreach($a_data as $idpegawai => $jml){
						$record = array();
						$record['periodegaji'] = $r_periode;
						$record['idpegawai'] = $idpegawai;
						$record['jmlbimbingan'] = $jml;
						
						$isexist = $conn->GetOne("select 1 from ".static::table('ga_bimbinganlog')." where periodegaji = '$r_periode' and idpegawai = ".$record['idpegawai']."");
						
						if(empty($isexist))
							list($err,$msg) = self::insertRecord($conn,$record,true,'ga_bimbinganlog');
						else{
							$key = $record['periodegaji'].'|'.$record['idpegawai'];
							$colkey = 'periodegaji,idpegawai';
							list($err,$msg) = self::updateRecord($conn,$record,$key,true,'ga_bimbinganlog',$colkey);
						}
					}
				}
			}
			
			return array($err,$msg);
		}
		
		//Daftar penarikan data bimbingan
		function listQueryHistoryUjianTA() {
			$sql = "select g.*,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,u.namaunit
					from ".static::table('ga_ketuapengujilog')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpegawai
					left join ".static::table('ga_historydatagaji')." gh on gh.idpeg = g.idpegawai and gh.gajiperiode = g.periodegaji
					left join ".static::table('ms_unit')." u on u.idunit = gh.idunit";
			
			return $sql;
		}
		
		//penarikan data ujian TA mengajar mahasiswa
		function tarikDataUjianTA($conn,$r_periode){
			//data periode gaji
			$pg = self::getDataPeriodeGaji($conn,$r_periode);
			
			$sql = "select p.idpegawai from akademik.ak_pembimbing b
					left join ".static::table('ms_pegawai')." p on p.nip = b.nip
					left join akademik.ak_ujianta u on u.idta = b.idta
					where b.tipepembimbing = 'U' and u.tglujian between '".$pg['tglawalhitung']."' and '".$pg['tglakhirhitung']."' 
					group by b.idta,p.idpegawai";
			
			$rs = $conn->Execute($sql);
			
			$i=0;
			while($row = $rs->FetchRow()){
				if(!empty($row['idpegawai'])){
					$i++;					
					$a_data[$row['idpegawai']]++;
				}
			}
			
			if($i == 0){
				$err = 1;
				$msg = 'Tidak ada data ujian TA mahasiswa yang ditarik';
			}else{
				if(count($a_data)>0){
					foreach($a_data as $idpegawai => $jml){
						$record = array();
						$record['periodegaji'] = $r_periode;
						$record['idpegawai'] = $idpegawai;
						$record['jmlmenguji'] = $jml;
						
						$isexist = $conn->GetOne("select 1 from ".static::table('ga_ketuapengujilog')." where periodegaji = '$r_periode' and idpegawai = ".$record['idpegawai']."");
						
						if(empty($isexist))
							list($err,$msg) = self::insertRecord($conn,$record,true,'ga_ketuapengujilog');
						else{
							$key = $record['periodegaji'].'|'.$record['idpegawai'];
							$colkey = 'periodegaji,idpegawai';
							list($err,$msg) = self::updateRecord($conn,$record,$key,true,'ga_ketuapengujilog',$colkey);
						}
					}
				}
			}
			
			return array($err,$msg);
		}
		
		//daftar honor pegawai
		function listQueryHonor($r_periode) {
			$sql = "select p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,substring(g.masakerjapend,1,2)||' tahun ' as masakerja, gp.idpegawai, gp.periodegaji, gp.tothonor,
					t.tipepeg||' - '||j.jenispegawai as namajenispegawai,sum(jmljam) as totjam
					from ".static::table('ga_historydatagaji')." g
					left join ".static::table('ga_mengajarlog')." m on m.idpegawai = g.idpeg and m.periodegaji = '$r_periode'
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpeg
					left join ".static::table('ga_gajipeg')." gp on g.idpeg = gp.idpegawai and g.gajiperiode = '$r_periode'
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = g.idtipepeg
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = g.idjenispegawai
					left join ".static::table('ms_unit')." u on u.idunit = g.idunit
					left join ".static::table('lv_jenjangpendidikan')." pd on pd.idpendidikan = g.pendidikan
					group by p.nip,p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang,u.namaunit,pd.namapendidikan,
					g.masakerjapend, gp.idpegawai, gp.periodegaji, gp.tothonor,t.tipepeg,j.jenispegawai";
			
			return $sql;
		}
		
		function infoHonor($conn,$r_honor){
			if($r_honor == '')
				$r_honor = $conn->GetOne("select kodehonor from ".static::table('ga_honor')." order by kodehonor");
			
			$honor = $conn->GetOne("select namahonor from ".static::table('ga_honor')." where kodehonor = '$r_honor'");
			
			if($r_honor == 'H0001'){//H. Prodi
				$info = 'Prodi';
				$filter = 'u.namaunit';
			}else if($r_honor == 'H0002'){//H. Jenis Matakuliah
				$info = 'Jenis Mata Kuliah';
				$filter = "case when mk.ismkdu = -1 then 'MKDU' else 'Non MKDU' end";
			}else if($r_honor == 'H0003'){//H. Transport
				$info = 'Pendidikan';
				$filter = 'pd.namapendidikan';
			}else if($r_honor == 'H0005'){//H. Ujian TA
				$info = 'Jenis';
				$filter = 'g.variabel1';
			}else if($r_honor == 'H0007'){//H. Ketua Penguji
				$info = 'Jenis';
				$filter = 'g.variabel1';
			}else{//Selain di atas
				$info = 'Prodi';
				$filter = 'u.namaunit';
			}
			
			$rhonor['namahonor'] = $honor;
			$rhonor['info'] = $info;
			$rhonor['filter'] = $filter;
						
			return $rhonor;
		}
						
		function getCHonorTarif($conn){
			$sql = "select kodehonor,namahonor from ".static::table('ga_honor')." where tarifnominal is null order by kodehonor";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getHonor($conn,$r_key){
			$tunj = $conn->GetOne("select kodehonor from ".static::table('ga_tarifhonor')." where notarifhonor = $r_key");
			
			return $tunj;
		}
		
		function getJnsSkripsi(){
			$a_data = array('P' => 'Proposal', 'S' => 'Skripsi');
			
			return $a_data;
		}
		
		function listQueryTarifHonor($r_honor) {
			if($r_honor == 'H0001'){//H. Prodi
				$select = ",u.namaunit as namavariabel,u.level,u.infoleft";
				$leftjoin = "left join ".static::table('ms_unit')." u on u.kodeunit = g.variabel1";
			}
			
			else if($r_honor == 'H0002'){//H. Jenis Matakuliah
				$select = ",mk.jnsmk as namavariabel";
				$leftjoin = "left join (select t.ismkdu,case when t.ismkdu = -1 then 'MKDU' else 'Non MKDU' end as jnsmk
					from akademik.ak_kelas k
					left join akademik.ak_kurikulum mk on k.kodemk = mk.kodemk and k.thnkurikulum = mk.thnkurikulum
					left join akademik.ak_matakuliah t on t.kodemk = mk.kodemk and t.thnkurikulum = mk.thnkurikulum
					where k.periode = (select periodesekarang from akademik.ms_setting) and t.isaktif = 1
					group by t.ismkdu) mk on mk.ismkdu = g.variabel1::numeric
					where (1=1)";
			}
			
			else if($r_honor == 'H0003'){//H. Transport
				$select = ",pd.namapendidikan as namavariabel";
				$leftjoin = "left join ".static::table('lv_jenjangpendidikan')." pd on pd.idpendidikan = g.variabel1";
			}
			
			else if($r_honor == 'H0005'){//H. Ujian TA
				$select = ",case when g.variabel1 = 'P' then 'Proposal' when g.variabel1 = 'S' then 'Skripsi' end as namavariabel";
			}
			
			else if($r_honor == 'H0007'){//H. Ketua Penguji
				$select = ",case when g.variabel1 = 'P' then 'Proposal' when g.variabel1 = 'S' then 'Skripsi' end as namavariabel";
			}
			
			else{//Selain di atas
				$select = ",u.namaunit as namavariabel,u.level,u.infoleft";
				$leftjoin = "left join ".static::table('ms_unit')." u on u.idunit::character varying = g.variabel1";
			}
			
			$sql = "select g.*{$select} from ".static::table('ga_tarifhonor')." g {$leftjoin}";
						
			return $sql;
		}
		
		function getInfoHonor($conn,$r_honor,$r_periode) {
			if($r_honor == 'H0001'){//H. Prodi
				$sql = "select kodeunit,namaunit from ".static::table('ms_unit')." where level=2 and isakademik='Y' order by kodeunit";
			}
			
			else if($r_honor == 'H0002'){//H. Jenis Matakuliah
				$sql = "select mk.kodemk,namamk from ".static::table('ga_tarifhonor')." g 
						left join akademik.ak_matakuliah mk on mk.kodemk = g.variabel1
						where periodetarif='$r_periode' and kodehonor='$r_honor'";
			}
			
			else if($r_honor == 'H0003'){//T. Transport
				$sql = "select idpendidikan,namapendidikan from ".static::table('lv_jenjangpendidikan')." order by idpendidikan";
			}
			
			else{//Selain di atas
				$sql = "select idpendidikan,namapendidikan from ".static::table('lv_jenjangpendidikan')." order by idpendidikan";
			}
						
			return Query::arrQuery($conn, $sql);
		}
		
		function unitFakultas($conn) {	
			$sql = "select kodeunit, nama_program_studi from akademik.ak_prodi
					where kode_jenjang_studi = 'Fak'
					order by kodeunit";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function jenisMK($conn) {	
			$sql = "select t.ismkdu,case when t.ismkdu = -1 then 'MKDU' else 'Non MKDU' end as jnsmk
					from akademik.ak_kelas k
					left join akademik.ak_kurikulum mk on k.kodemk = mk.kodemk and k.thnkurikulum = mk.thnkurikulum
					left join akademik.ak_matakuliah t on t.kodemk = mk.kodemk and t.thnkurikulum = mk.thnkurikulum
					where k.periode = (select periodesekarang from akademik.ms_setting) and t.isaktif = 1
					group by t.ismkdu";
			
			return Query::arrQuery($conn, $sql);
		}
		
		//mendapatkan tarif honor mengajar		
		function getTarifHonor($conn){
			$r_periodetarif = self::getLastPeriodeTarif($conn);
			
			$sql = "select * from ".static::table('ga_tarifhonor')." where periodetarif = '$r_periodetarif'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				if(!empty($row['variabel2']))
					$a_tarifhonor[$row['kodehonor']][$row['variabel1']][$row['variabel2']] = $row['nominal'];
				else
					$a_tarifhonor[$row['kodehonor']][$row['variabel1']] = $row['nominal'];
			}
			
			return $a_tarifhonor;
		}
		
		//mendapatkan variabel honor
		function getVariabelHonor($conn){
			$r_periodetarif = self::getLastPeriodeTarif($conn);
			
			$sql = "select * from ".static::table('ga_tarifhonor')." where periodetarif = '$r_periodetarif'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_varhonor[$row['kodehonor']][] = $row['variabel1'];
			}
			
			return $a_varhonor;
		}
		
		//mendapatkan data mengajar dosen
		function getHistroyMengajar($conn,$r_periode){
			$sql = "select * from ".static::table('ga_mengajarlog')." where periodegaji = '$r_periode'";
			$rs = $conn->Execute($sql);
			
			//variabel honor
			$a_varhonor = self::getVariabelHonor($conn);
			
			while($row = $rs->FetchRow()){
				//honor berdasarkan MKDU
				if(in_array($row['kodemk'],$a_varhonor['H0002'])){
					$a_histajar[$row['idpegawai']]['H0002'][$row['kodemk']] += $row['jmljam'];
				}
				//honor berdasarkan Prodi
				else if(in_array($row['kodeunit'],$a_varhonor['H0001'])){
					$a_histajar[$row['idpegawai']]['H0001'][$row['kodeunit']] += $row['jmljam'];
				}
			}
			
			return $a_histajar;
		}
		
		//perhitungan honor
		function hitHonor($conn,$r_periode,$r_sql=''){	
			//filter dari daftar
			if(!empty($r_sql)){
				$a_peg = self::pegFilter($conn,$r_sql);
			}
			
			//pegawai yang gajinya sudah dibayar
			$b_peg = self::sudahBayar($conn,$r_periode);
			
			$sql = "select * from ".static::table('ga_historydatagaji')."
					where gajiperiode = '$r_periode'";
			if(!empty($a_peg))
				$sql .= " and idpeg in ($a_peg)";
			if(!empty($b_peg))
				$sql .= " and idpeg not in ($b_peg)";
				
			$rs = $conn->Execute($sql);
			
			//tarif honor
			$a_tarifhonor = self::getTarifHonor($conn);
			
			//history mengajar
			$a_histajar = self::getHistroyMengajar($conn,$r_periode);
			
			//variabel honor
			$a_varhonor = self::getVariabelHonor($conn);
			
			while($row = $rs->FetchRow()){
				//hapus dulu tunjangan pegawai, agar bersih
				$key = $r_periode.'|'.$row['idpeg'];
				$colkey = 'periodegaji,idpegawai';
				
				list($err,$msg) = self::delete($conn,$key,'ga_honorpeg',$colkey);
				
				if(!$err){
					if(count($a_histajar[$row['idpeg']])>0){
						foreach($a_histajar[$row['idpeg']] as $key => $val){
							foreach($val as $keys => $jmljam){
								//honor berdasarkan MKDU
								if($key == 'H0002'){
									$record = array();
									$record['periodegaji'] = $r_periode;
									$record['kodehonor'] = $key;
									$record['idpegawai'] = $row['idpeg'];
									
									$nominal = $a_tarifhonor[$key][$keys];
									$record['nominal'] = $jmljam * $nominal;
									
									$err = self::saveHonor($conn,$record);						
								}
								//honor berdasarkan Fakultas
								else if($key == 'H0001'){
									$record = array();
									$record['periodegaji'] = $r_periode;
									$record['kodehonor'] = $key;
									$record['idpegawai'] = $row['idpeg'];
									
									$nominal = $a_tarifhonor[$key][$keys];
									$record['nominal'] = $jmljam * $nominal;
									
									$err = self::saveHonor($conn,$record);
								}
							}
						}
					}
					
					//honor berdasarkan Dosen LB
					if(in_array($row['pendidikan'],$a_varhonor['H0003'])){
						$record = array();
						$record['periodegaji'] = $r_periode;
						$record['kodehonor'] = 'H0003';
						$record['idpegawai'] = $row['idpeg'];
						
						$nominal = $a_tarifhonor[$key][$row['pendidikan']];
						$record['nominal'] = $nominal;
						
						$err = self::saveHonor($conn,$record);
					}
				}
			}
			
			if($err)
				$msg = 'Penyimpanan honor gagal';
			else
				$msg = 'Penyimpanan honor berhasil';
			
			return array($err,$msg);
		}
				
		//Simpan gaji honor
		function saveHonor($conn,$record){
			$err = 0;
			if(!empty($record['nominal']))
				$err = self::insertRecord($conn,$record,false,'ga_honorpeg');
			
			return $err;
		}
		
		/***********************SALIN TARIF HONOR***************************/
		function getCSalinHonor($conn,$r_honor){
			$sql = "select kodehonor, namahonor from ".static::table('ga_honor')." where kodehonor='$r_honor'";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_add = array('all' => '-- Semua Honor --');
			$a_data = array_merge($a_data,$a_add);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['kodehonor']] = $row['namahonor'];
			}
			
			return $a_data;
		}
		
		function saveSalinTarifHonor($conn,$r_periode,$r_honor,$f_keyPeriode,$f_keyHonor){
			$err = 0;
		
			$r_key = $r_periode."|".$f_keyHonor;
			list($err,$msg) = self::delete($conn,$r_key,'ga_tarifhonor','periodetarif,kodehonor');
			
			if(!$err){
				if ($f_keyHonor !='all')
					$honor= "'".$f_keyHonor."'";
				else
					$honor="kodehonor";
				
				$sql = "insert into ".static::table('ga_tarifhonor')." (periodetarif,kodehonor,variabel1,variabel2,nominal,t_username,t_ipaddress,t_updatetime)
						select '$r_periode',$honor,variabel1,variabel2,nominal+(nominal * $prosentase),'".Modul::getUserName()."','".date('Y-m-d H:i:s')."','".$_SERVER['REMOTE_ADDR']."' 
						from ".static::table('ga_tarifhonor')."
						where periodetarif='$f_keyPeriode' ";
				
				if ($f_keyHonor !='all')
					$sql .= "and kodehonor='$f_keyHonor'";
				
				$conn->Execute($sql);
			}
			if($err)
				$msg = 'Salin tarif honor gagal, data masih digunakan';
			else
				$msg = 'Salin tarif honor berhasil';
			
			return array($err,$msg);
		}
		
		/**************************************************** POTONGAN ******************************************************/
		
		function listQueryPotongan() {
			$sql = "select * from ".static::table('ga_potongan');
			
			return $sql;
		}
		
		function getLastPotongan($conn){
			$sql = "select kodepotongan || '|' || ismanual as kode from ".static::table('ga_potongan')." where isaktif='Y' order by kodepotongan";
			
			return $conn->GetOne($sql);
		}		
		
		function getCPotongan($conn){
			$sql = "select kodepotongan || '|' || coalesce(ismanual,'') as kodepotongan, namapotongan from ".static::table('ga_potongan')." 
					where isaktif='Y'";
			
			return Query::arrQuery($conn, $sql);
		}	
		
		//List Combo Potongan Otomatis
		function getCPotOtomatis($conn,$empty=false){
			$sql = "select kodepotongan, namapotongan from ".static::table('ga_potongan')." where ismanual = 'T' and isaktif='Y'";
			
			return Query::arrQuery($conn, $sql);
		}
		
		//jenis potongan parameter
		function getCPotParam($conn){
			$sql = "select kodepotongan, namapotongan from ".static::table('ga_potongan')." where ismanual = 'P' and isaktif='Y' order by kodepotongan";
			
			return Query::arrQuery($conn, $sql);
		}
		
		//Jenis Potongan
		function getJnsPotongan($conn){
			$sql = "select kodepotongan,namapotongan from ".static::table('ga_potongan')." where isaktif = 'Y' order by kodepotongan";
			$a_jpot = Query::arrQuery($conn, $sql);
			
			return $a_jpot;
		}
		
		//mendapatkan satu potongan parameter
		function getOnePotParam($conn){
			$potparam = $conn->GetOne("select kodepotongan from ".static::table('ga_potongan')." where ismanual = 'P' and isaktif='Y' order by kodepotongan limit 1");
			
			return $potparam;
		}
		
		//mendapatkan potongan pegawai
		function getPotPeg($conn){
			$potparam = $conn->GetOne("select kodepotongan from ".static::table('ga_potongan')." where ismanual = 'T' and isaktif='Y' order by kodepotongan limit 1");
			
			return $potparam;
		}
		
		//List Master Tarif Potongan per pegawai
		function listTarifPotonganPeg(){
			$sql = "select g.*, p.idpegawai, sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					pt.namapotongan
					from ".static::table('ga_tarifpotonganpeg')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai=g.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					left join ".static::table('ga_potongan')." pt on pt.kodepotongan=g.kodepotongan
					where pt.ismanual = 'T'";
			
			return $sql;
		}
		
		function getDataTarifPotonganPeg($r_key){
			list($periodetarif,$idpegawai,$kodepotongan) = explode('|',$r_key);
			
			$sql = "select g.*, sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap 
					from ".static::table('ga_tarifpotonganpeg')." g
					left join ".static::table('ms_pegawai')." m on m.idpegawai=g.idpegawai
					where g.periodetarif = '$periodetarif' and g.idpegawai= $idpegawai and kodepotongan='$kodepotongan' ";
			
			return $sql;
		}
		
		//List Master Tarif Potongan seluruh pegawai
		function listTarifPotongan($r_potongan){
			$sql = "select g.*,h.hubkerja
					from ".static::table('ga_tarifpotongan')." g
					left join ".static::table('ms_hubkerja')." h on h.idhubkerja=g.variabel1
					where g.kodepotongan = '$r_potongan'";
			
			return $sql;
		}
		
		function getDataTarifPotongan($r_key){
			$sql = "select * from ".static::table('ga_tarifpotongan')." where notarifpotongan = $r_key ";
		
			return $sql;
		}
		
		function getCHubkerja($conn){
			$sql = "select idhubkerja, hubkerja from ".static::table('ms_hubkerja')." order by idhubkerja";

			return Query::arrQuery($conn, $sql);
		}
		
		//mendapatkan data hubungan kerja pegawai
		function getHubKerja($conn,$r_idpegawai){
			$hubkerja = $conn->GetOne("select idhubkerja from ".static::table('ms_pegawai')." where idpegawai = $r_idpegawai");
			
			return $hubkerja;
		}
		
		function caraHitungPot(){
			$a_cara = array('Y' => 'Manual', 'T' => 'Otomatis', 'P' => 'Parameter');
			
			return $a_cara;
		}
		
		function listQueryHitPotongan($r_periode,$r_potongan){
			$sqladd = "";
			if (!empty($r_periode))
				$sqladd = " and pt.periodegaji='$r_periode'";
				
			if (!empty($r_potongan))
				$sqladd .= " and pt.kodepotongan='$r_potongan'";
				
			$sql = "select g.*,p.idpegawai,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,hk.hubkerja,t.tipepeg||' - '||j.jenispegawai as namajenispegawai,pt.nominal
					from ".static::table('ga_historydatagaji')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpeg
					left join ".static::table('ga_potonganpeg')." pt on pt.idpegawai = g.idpeg {$sqladd}
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = g.idtipepeg
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = g.idjenispegawai
					left join ".static::table('ms_unit')." u on u.idunit = g.idunit
					left join ".static::table('ms_hubkerja')." hk on hk.idhubkerja = g.idhubkerja";
			
			return $sql;
		}
		
		//simpan potongan manual
		function savePotongan($conn,$r_periode,$r_potongan,$a_post){
			$a_pegawai = $a_post['id'];
			
			if (count($a_pegawai) > 0){
				foreach($a_pegawai as $idpegawai){
					$key = $r_periode.'|'.$r_potongan.'|'.$idpegawai;
					$colkey = 'periodegaji,kodepotongan,idpegawai';
					
					list($err,$msg) = self::delete($conn,$key,'ga_potonganpeg',$colkey);
					

					if (!$err){
						$record = array();
						$record['periodegaji'] = $r_periode;
						$record['kodepotongan'] = $r_potongan;
						$record['idpegawai'] = $idpegawai;

						
						$nominal = $a_post['nominal_'.$idpegawai];
						$record['nominal'] = Cstr::cStrDec($nominal);
						
						$err = self::simpanPotongan($conn,$record);
					}	
				}
			}
			
			if($err)
				$msg = 'Penyimpanan potongan gagal';
			else
				$msg = 'Penyimpanan potongan berhasil';
			
			return array($err,$msg);
		}
		
		//hitung potongan otomatis dan parameter
		function hitungPotongan($conn,$r_periode,$r_potongan,$r_sql){		
			if(!empty($r_sql)){
				$a_peg = self::pegFilter($conn,$r_sql);
			}
			
			//pegawai yang gajinya sudah dibayar
			$b_peg = self::sudahBayar($conn,$r_periode);
			
			$sql = "select * from ".static::table('ga_historydatagaji')."
					where gajiperiode = '$r_periode'";
			
			if(!empty($a_peg))
				$sql .= " and idpeg in ($a_peg)";
			if(!empty($b_peg))
				$sql .= " and idpeg not in ($b_peg)";
				
			$rs = $conn->Execute($sql);
			
			//cek apakah potongan termasuk potongan parameter
			$isparam = $conn->GetOne("select ismanual from ".static::table('ga_potongan')." where kodepotongan = '$r_potongan'");
			
			//mendapatkan tarif parameter
			if($isparam == 'P')
				$a_tarifPotParam = self::getTarifPotParam($conn);
			else
				$a_tarifPotPeg = self::getTarifPotPegawai($conn);
			

			while($row = $rs->FetchRow()){
				$key = $r_periode.'|'.$r_potongan.'|'.$row['idpeg'];
				$colkey = 'periodegaji,kodepotongan,idpegawai';
			
				list($err,$msg) = self::delete($conn,$key,'ga_potonganpeg',$colkey);
				
				$record = array();
				$record['periodegaji'] = $r_periode;
				$record['kodepotongan'] = $r_potongan;
				$record['idpegawai'] = $row['idpeg'];
				
				if($isparam == 'P'){
					$nominal = $a_tarifPotParam[$r_potongan][$row['idhubkerja']];		
					$record['nominal'] = $nominal;					
				}
				else {
					$nominal = $a_tarifPotPeg[$r_potongan][$row['idpeg']];						
					$record['nominal'] = $nominal;
				}
			
				$err = self::simpanPotongan($conn,$record);		
			}
			
			if($err)
				$msg = 'Penyimpanan potongan gagal';
			else
				$msg = 'Penyimpanan potongan berhasil';
			
			return array($err,$msg);
		}
		
		//Simpan potongan
		function simpanPotongan($conn,$record){
			if(!empty($record['nominal']))
				$err = self::insertRecord($conn,$record,false,'ga_potonganpeg');
			
			return $err;
		}
				
		function getPotonganSlip($conn,$key){
			list($periode,$idpegawai) = explode('|',$key);
			
			$sql = "select g.* from ".static::table('ga_potonganpeg')." g
					where g.periodegaji = '$periode' and g.idpegawai = $idpegawai";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_tunj[$row['kodepotongan']] = $row['nominal'];
			}
			
			return $a_tunj;
		}
		
		function getTarifPotPegawai($conn,$r_potongan,$r_idpegawai){
			$last = self::getLastDataPeriodeTarif($conn);
			
			$sql = "select coalesce(nominal,0) as potpegawai,idpegawai
					from ".static::table('ga_tarifpotonganpeg')."
					where kodepotongan = '$r_potongan' and periodetarif = '".$last."' and idpegawai = '$r_idpegawai'";	
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_potpegawai['potpegawai'] = $row['potpegawai'];
			}
			
			return $a_potpegawai;
		}
		
		function getTarifPotParam($conn){
			$r_periodetarif = self::getLastPeriodeTarif($conn);
			
			$sql = "select * from ".static::table('ga_tarifpotongan')." where periodetarif = '$r_periodetarif'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				if(!empty($row['variabel2']))
					$a_tarifpotparam[$row['kodepotongan']][$row['variabel1']][$row['variabel2']] = $row['nominal'];
				else
					$a_tarifpotparam[$row['kodepotongan']][$row['variabel1']] = $row['nominal'];
			}
			
			return $a_tarifpotparam;	
		}
		
		/***********************SALIN TARIF POTONGAN***************************/
		function getCSalinPotongan($conn){
			$sql = "select kodepotongan, namapotongan from ".static::table('ga_potongan')." 
					where ismanual='P' order by kodepotongan";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_add = array('all' => '-- Semua Potongan --');
			$a_data = array_merge($a_data,$a_add);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['kodepotongan']] = $row['namapotongan'];
			}
			
			return $a_data;
		}
		
		function saveSalinTarifPot($conn,$r_periode,$r_potongan,$f_keyPeriode,$f_keyPotongan,$prosentase){
			$err = 0;
		
			$r_key = $r_periode."|".$f_keyPotongan;
			list($err,$msg) = self::delete($conn,$r_key,'ga_tarifpotongan','periodetarif,kodepotongan');
			
			if(!$err){
				if ($f_keyPotongan !='all')
					$potongan= "'".$f_keyPotongan."'";
				else
					$potongan="t.kodepotongan";
				
				$sql = "insert into ".static::table('ga_tarifpotongan')." (periodetarif,kodepotongan,variabel1,variabel2,nominal,t_username,t_ipaddress,t_updatetime)
						select '$r_periode',$potongan,variabel1,variabel2,nominal+(nominal * $prosentase),'".Modul::getUserName()."','".date('Y-m-d H:i:s')."','".$_SERVER['REMOTE_ADDR']."' 
						from ".static::table('ga_tarifpotongan')." t
						left join ".static::table('ga_potongan')." g on g.kodepotongan=t.kodepotongan
						where t.periodetarif='$f_keyPeriode' and g.ismanual='P' ";
				
				if ($f_keyPotongan !='all')
					$sql .= "and t.kodepotongan='$f_keyPotongan'";
				
				$conn->Execute($sql);
			}
			if($err)
				$msg = 'Salin tarif potongan gagal, data masih digunakan';
			else
				$msg = 'Salin tarif potongan berhasil';
			
			return array($err,$msg);
		}
		
		/*******************SALIN TARIF POTONGAN PEGAWAI*********************/
		function getCSalinPotonganPeg($conn){
			$sql = "select kodepotongan, namapotongan from ".static::table('ga_potongan')." 
					where ismanual='T' order by kodepotongan";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_add = array('all' => '-- Semua Potongan Pegawai--');
			$a_data = array_merge($a_data,$a_add);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['kodepotongan']] = $row['namapotongan'];
			}
			
			return $a_data;
		}
		
		function saveSalinTarifPotPeg($conn,$r_periode,$r_potongan,$f_keyPeriode,$f_keyPotonganPeg,$prosentase){
			$err = 0;
		
			$r_key = $r_periode."|".$f_keyPotonganPeg;
			list($err,$msg) = self::delete($conn,$r_key,'ga_tarifpotongan','periodetarif,kodepotongan');
			
			if(!$err){
				if ($f_keyPotonganPeg !='all')
					$potongan= "'".$f_keyPotonganPeg."'";
				else
					$potongan="t.kodepotongan";
				
				$sql = "insert into ".static::table('ga_tarifpotongan')." (periodetarif,kodepotongan,variabel1,variabel2,nominal,t_username,t_ipaddress,t_updatetime)
						select '$r_periode',$potongan,variabel1,variabel2,nominal+(nominal * $prosentase),'".Modul::getUserName()."','".date('Y-m-d H:i:s')."','".$_SERVER['REMOTE_ADDR']."' 
						from ".static::table('ga_tarifpotongan')." t
						left join ".static::table('ga_potongan')." g on g.kodepotongan=t.kodepotongan
						where t.periodetarif='$f_keyPeriode' and g.ismanual='T' ";
				
				if ($f_keyPotonganPeg !='all')
					$sql .= "and t.kodepotongan='$f_keyPotonganPeg'";
				
				$conn->Execute($sql);
			}
			if($err)
				$msg = 'Salin tarif potongan pegawai gagal, data masih digunakan';
			else
				$msg = 'Salin tarif potongan pegawai berhasil';
			
			return array($err,$msg);
		}
		
		/**************************************************** PINJAMAN ******************************************************/
				
		//sudah bayar pinjaman utk periode hitung gaji
		function isbyrpjm($conn,$r_periode){
			$sql = "select p.idpeminjam
					from ".static::table('pe_angsuran')." a
					left join ".static::table('pe_pinjaman')." p on p.idpinjaman = a.idpinjaman
					where a.periodegaji = '$r_periode' and a.isdibayar = 'Y' and p.isfixpinjam = 'Y' and (p.islunas is null or p.islunas = 'N')";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_sdhbyr[$row['idpeminjam']] = $row['idpeminjam'];
			}
			
			return $a_sdhbyr;
		}
		
		function bayarPinjaman($conn,$r_periode){			
			$sql = "select * from ".static::table('pe_angsuran')." where periodegaji = '$r_periode' and (isdibayar is null or isdibayar = 'N')";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$recangs = array();
				$recangs['isdibayar'] = 'Y';
				
				$akey = $row['idpinjaman'].'|'.$row['noangsuran'];
				$acolkey = 'idpinjaman,noangsuran';
				$err = self::updateRecord($conn,$recangs,$akey,false,'pe_angsuran',$acolkey);
				
				//insert ke bayar angsuran
				if(!$err){
					$recbyr = array();
					$recbyr['idpinjaman'] = $row['idpinjaman'];
					$recbyr['noangsuran'] = $row['noangsuran'];
					$recbyr['jmlbayar'] = $row['jmlangsuran'];
					$recbyr['tglbayar'] = date('Y-m-d');
					$recbyr['periodegaji'] = $r_periode;
				
					$err = self::insertRecord($conn,$recbyr,false,'pe_bayarpinjaman');
				}
			}
		}
		
		/**************************************************** T H R ***********************************************************/
					
		function getCPeriodeGajiTHR($conn){
			$sql = "select periodegaji, namaperiode from ".static::table('ga_periodegaji')." where refperiodegaji is not null order by tglakhirhitung desc";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getLastPeriodeGajiTHR($conn){
			$r_periodegaji = $conn->GetOne("select periodegaji from ".static::table('ga_periodegaji')." where refperiodegaji is not null order by tglakhirhitung desc limit 1");
			
			return $r_periodegaji;
		}
		
		function getLastDataPeriodeGajiTHR($conn){
			$periode = self::getLastPeriodeGaji($conn);
			$row = $conn->GetRow("select * from ".static::table('ga_periodegaji')." where periodegaji = '$periode'");
			
			return $row;
		}	
		
		function listQueryKomposisiTHR(){
			$sql = "select g.*,h.hubkerja,cast(mkmin as varchar) || ' - '|| cast(mkmax as varchar)||' bulan' as masakerja
					from ".static::table('ga_komposisithr')." g
					left join ".static::table('ms_hubkerja')." h on h.idhubkerja = g.idhubkerja";
			
			return $sql;
		}
		
		function getDataEditKomposisiTHR($r_key){
			list($r_periode,$r_hubkerja) = explode('|',$r_key);
			$sql = "select * from ".static::table('ga_komposisithr')." 
					where periodegaji = '$r_periode' and idhubkerja = '$r_hubkerja'";
			
			return $sql;
		}
		
		function getTHRDetail($conn,$key,$label='',$post='') {
			list($r_periode,$r_jenis,$r_aktif) = explode('|',$key);
			$sql = "select * from ".static::table('ga_komposisithrdet')." 
					where periodegaji = '$r_periode' and idjenispegawai = '$r_jenis' and idstatusaktif = '$r_aktif' 
					order by kodetunjangan";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'detailthr':
					$info['table'] = 'ga_komposisithrdet';
					$info['key'] = 'periodegaji,idjenispegawai,idstatusaktif,kodetunjangan';
					$info['label'] = 'Komposisi THR Tunjangan';
					break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}
		
		function getDataPeriodeGajiTHR($conn,$r_periode){
			$row = $conn->GetRow("select * from ".static::table('ga_periodegaji')." where periodegaji = '$r_periode'");
			
			return $row;
		}
		
		function getCSettingTHR(){
			$a_setting = array('O' => 'Otomatis', 'M' => 'Manual');
			
			return $a_setting;
		}
		
		function listQueryHitungTHR($r_periode,$r_refperiode,$r_hubkerja,$r_jenispeg){
			$sql = "select g.gajiditerima,g.pph,g.isfinish,g.idpegawai,g.periodegaji,gh.*,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pk.golongan,gh.masakerja as mkerja,
					t.tipepeg||' - '||j.jenispegawai as namajenispegawai
					from ".static::table('ga_historydatagaji')." gh
					left join ".static::table('ms_pegawai')." p on p.idpegawai = gh.idpeg
					left join ".static::table('ga_gajipeg')." g on g.idpegawai = gh.idpeg and g.periodegaji = '$r_periode'
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = p.idtipepeg
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = p.idjenispegawai
					left join ".static::table('ms_unit')." u on u.idunit = p.idunit
					left join ".static::table('ms_pangkat')." pk on pk.idpangkat = p.idpangkat
					where gh.gajiperiode = '$r_refperiode' and g.idpegawai is not null";
					
			if(count($r_hubkerja)==0 and count($r_jenispeg)==0)
				$sql .= ' and 1=0';
			if(count($r_hubkerja)>0){
				$i_hubkerja = implode("','",$r_hubkerja);
				$sql .= " and p.idhubkerja in ('$i_hubkerja')";
			}
			if(count($r_jenispeg)>0){
				$i_jenispeg = implode("','",$r_jenispeg);
				$sql .= " and p.idjenispegawai in ('$i_jenispeg')";
			}
			
			return $sql;
		}
				
		function hitGajiTHR($conn,$r_periode,$r_sql='',$r_perioderef){		
			if(!empty($r_sql)){
				$a_peg = self::pegFilter($conn,$r_sql);
			}
			
			//pegawai yang gajinya sudah dibayar
			$b_peg = self::sudahBayar($conn,$r_periode);
			
			//gaji periode referensi
			$g_ref = self::getGajiRef($conn,$r_perioderef);
			
			$sql = "select * from ".static::table('ga_historydatagaji')."
					where gajiperiode = '$r_perioderef'";
			if(!empty($a_peg))
				$sql .= " and idpeg in ($a_peg)";
			if(!empty($b_peg))
				$sql .= " and idpeg not in ($b_peg)";
				
			$rs = $conn->Execute($sql);
				
			//masa kerja proporsional
			$prop = self::getProporsional($conn, $r_perioderef);
						
			while($row = $rs->FetchRow()){				
				$record = array();
				$record['periodegaji'] = $r_periode;
				
				if(!empty($g_ref[$row['idpeg']]) and $prop[$row['idpeg']] > 0){
					$record['idpegawai'] = $row['idpeg'];
					$record['gapok'] = $prop[$row['idpeg']] * $g_ref[$row['idpeg']];
					echo $record['gapok'];
					list($err,$msg) = self::saveGaji($conn,$record,$r_periode,$row['idpeg']);
				}
			}
			
			if($err)
				$msg = 'Penyimpanan gaji THR gagal';
			else
				$msg = 'Penyimpanan gaji THR berhasil';
			
			return array($err,$msg);
		}
		
		function getGajiRef($conn,$r_perioderef){
			$sql = "select idpegawai,cast(gajiditerima as varchar) as gajiditerima from ".static::table('ga_gajipeg')."
					where periodegaji = '$r_perioderef'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_gaji[$row['idpegawai']] = $row['gajiditerima'];
			}
			
			return $a_gaji;
		}
		
		function saveGajiTHR($conn,$r_periode,$r_perioderef,$a_post){
			$a_pegawai = $a_post['id'];
			
			if (count($a_pegawai) > 0){
				//masa kerja proporsional
				$prop = self::getProporsional($conn,$r_perioderef);
				
				foreach($a_pegawai as $idpegawai){					
					if (!empty($a_post['gajiditerima_'.$idpegawai])){
						$record = array();
						$record['periodegaji'] = $r_periode;
						$record['idpegawai'] = $idpegawai;
						$record['gapok'] = Cstr::cStrDec($a_post['gajiditerima_'.$idpegawai]);
						
						list($err,$msg) = self::saveGaji($conn,$record,$r_periode,$idpegawai);
					}	
				}
			}
			
			if($err)
				$msg = 'Penyimpanan gaji THR gagal';
			else
				$msg = 'Penyimpanan gaji THR berhasil';
			
			return array($err,$msg);
		}
		
		function isDihitungTHR($conn,$r_periode){
			$isExist = $conn->GetOne("select idpegawai from ".static::table('ga_gajipeg')." where periodegaji = '$r_periode' limit 1");
			
			return $isExist;
		}
		
		function getProporsional($conn, $r_periode){
			$sql = "select idpeg,".static::schema.".get_mkpengabdian(idpeg) as masakerja 
					from ".static::table('ga_historydatagaji')." 
					where gajiperiode = '$r_periode'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				//masa kerja
				$prop = 12/12;
				if((int)substr($row['masakerja'],0,2) <= 0){
					$prop = (int)substr($row['masakerja'],2,2)/12;
				}
				
				if($prop > 0)
					$a_prop[$row['idpeg']] = $prop;
			}
			
			return $a_prop;
		}
		
		/********************************************** LEMBUR ***************************************************/
		
		function listQueryHitLembur($r_periode) {
			$sql = "select g.*,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,substring(h.masakerjapend,1,2)||' tahun ' as masakerja,
					t.tipepeg||' - '||j.jenispegawai as namajenispegawai 
					from ".static::table('ga_upahlemburpeg')." g
					left join ".static::table('ga_historydatagaji')." h on h.idpeg = g.idpegawai and h.gajiperiode = g.periodegaji
					left join ".static::table('ms_pegawai')." p on p.idpegawai = h.idpeg
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = p.idtipepeg
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = p.idjenispegawai
					left join ".static::table('ms_unit')." u on u.idunit = p.idunit
					left join ".static::table('lv_jenjangpendidikan')." pd on pd.idpendidikan = h.pendidikan";
			
			return $sql;
		}
		
		function hitungLembur($conn, $r_periode, $r_sql=''){
			if(!empty($r_sql)){
				$a_peg = self::pegFilter($conn,$r_sql);
			}
			
			//pegawai yang gajinya sudah dibayar
			$b_peg = self::sudahBayar($conn,$r_periode);
			
			//lembur yang sudah dibayarkan
			$l_peg = self::isLemburBayar($conn,$r_periode);
					
			$periode = $conn->GetRow("select tglawallembur, tglakhirlembur from ".static::table('ga_periodegaji')." where periodegaji='$r_periode'");
			$sql = "select p.totlembur,p.idpegawai,p.tglpresensi,g.gajitetap,p.sjamdatang,p.kodeabsensi 
					from ".static::table('pe_presensidet')." p
					left join ".static::table('ga_gajipeg')." g on g.idpegawai=p.idpegawai and g.periodegaji='$r_periode'
					left join ".static::table('ms_pegawai')." m on m.idpegawai=p.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=m.idunit
					where totlembur is not null and issetujuatasan='Y' and isvalid='Y'
					and tglpresensi between '$periode[tglawallembur]' and '$periode[tglakhirlembur]'";
					
			if(!empty($a_peg))
				$sql .= " and p.idpegawai in ($a_peg)";
			if(!empty($b_peg))
				$sql .= " and p.idpegawai not in ($b_peg)";	
			if(!empty($l_peg))
				$sql .= " and p.idpegawai not in ($l_peg)";
			$rs = $conn->Execute($sql);
			
			$a_pegawai = array();
			$a_lembur = array();
			
			//perhitungan lembur 8 jam/hari dan 5 hari kerja/minggu
			while ($row = $rs->FetchRow()){
				$jumjam = $jumjamlibur = $l1 = $l2 = $l3 = 0;
				
				//karyawan yang lembur
				$a_pegawai[$row['idpegawai']] = $row['idpegawai'];
				if ($row['kodeabsensi']  == 'H'){
					$jumjam = $row['totlembur']/60;
					if ($jumjam >= 1)
						$l1 = 1 * 1.5 * 1/173 * $row['gajitetap'];
					if ($jumjam >= 2)
						$l2 = ($jumjam - 1) * 2 * 1/173 *  $row['gajitetap'];
				}else if ($row['kodeabsensi'] == 'HL'){
					$jumjamlibur = $row['totlembur']/60;
					if ($jumjamlibur >= 1)
						$l1 = ($jumjamlibur - 8) * 2 * 1/173 *  $row['gajitetap'];
					if ($jumjamlibur >= 9)
						$l2 = ($jumjamlibur - 9) * 3 * 1/173 *  $row['gajitetap'];
					if ($jumjamlibur >= 10)
						$l3 = ($jumjamlibur - 9) * 4 * 1/173 *  $row['gajitetap'];
				}
				
				$a_lembur[$row['idpegawai']] += round(($l1 + $l2 + $l3),3);
				$a_jamkerja[$row['idpegawai']] += $jumjam;
				$a_jumjamlibur[$row['idpegawai']] += $jumjamlibur;
			}	
			
			if (count($a_pegawai) > 0){
				$conn->StartTrans();
				foreach($a_pegawai as $id){
					$record = array();
					$record['periodegaji'] = $r_periode;
					$record['idpegawai'] = $id;
					$record['upahlembur'] = $a_lembur[$id];
					$record['jamkerja'] = $a_jamkerja[$id];
					$record['jamkerjalibur'] = $a_jumjamlibur[$id];
					
					$isExist = false;
					$key = $r_periode.'|'.$id;
					$colkey = 'periodegaji,idpegawai';
					$isExist = mGaji::isDataExist($conn,$key,'ga_upahlembur',$colkey);
					
					if ($isExist)
						list($err,$msg) = mGaji::updateRecord($conn,$record,$key,true,'ga_upahlembur',$colkey);
					else
						list($err,$msg) = mGaji::insertRecord($conn,$record,true,'ga_upahlembur');
					
					if($err)
						$msg = 'Perhitungan lembur gagal';
					else
						$msg = 'Perhitungan lembur berhasil';
				}
				
				$conn->CompleteTrans();	
			}
			
			return array($err,$msg);
		}
		
		function bayarLembur($conn,$r_periode,$r_sql=''){
			if(!empty($r_sql)){
				$a_peg = self::pegFilter($conn,$r_sql);
			}
			
			//pegawai yang gajinya sudah dibayar
			$b_peg = self::sudahBayar($conn,$r_periode);
						
			$sql = "select idpegawai from ".static::table('ga_upahlembur')."
					where periodegaji = '$r_periode' and (isbayar = 'T' or isbayar is null)";
			if(!empty($a_peg))
				$sql .= " and idpegawai in ($a_peg)";	
			if(!empty($b_peg))
				$sql .= " and idpegawai not in ($b_peg)";	
			
			$rs = $conn->Execute($sql);
			
			$record = array();
			$record['isbayar'] = 'Y';
			$record['tglbayar'] = date('Y-m-d');
			
			while($row = $rs->FetchRow()){				
				$key = $row['idpegawai'].'|'.$r_periode;
				$colkey = 'idpegawai,periodegaji';
				
				list($err,$msg) = self::updateRecord($conn,$record,$key,true,'ga_upahlembur',$colkey);
			}
			
			list($err,$msg) = self::updateStatus($conn);
			
			return array($err,$msg);
		}
		
		function isLemburBayar($conn,$r_periode){
			$sql = "select idpegawai from ".static::table('ga_upahlembur')."
					where periodegaji = '$r_periode' and isbayar = 'Y'";
			$rs = $conn->Execute($sql);
			
			$a_peg = array();
			while($row = $rs->FetchRow()){
				$a_peg[$row['idpegawai']] = $row['idpegawai'];
			}
			
			if(count($a_peg) > 0)
				$i_peg = implode(',',$a_peg);
				
			return $i_peg;
		}
		
		//List Query Tarif Lembur
		function listQueryTarifLembur(){
			$sql = "select l.*, j.namapendidikan as pendidikan,h.namajenishari as jenishari,
					case when l.isharilibur = 'Y' then 'Ya' when l.isharilibur = 'T' then 'Tidak' else '' end as harilibur
					from ".static::table('ga_tariflembur')." l
					left join ".static::table('lv_jenjangpendidikan')." j on j.idpendidikan = l.idpendidikan
					left join ".static::table('lv_jenishari')." h on h.idjenishari = l.idjenishari";
			
			return $sql;
		}
		
		//mendapatkan jenis hari
		function getJenisHari($conn) {
			$sql = "select idjenishari, namajenishari from ".static::schema()."lv_jenishari order by idjenishari";
			
			return Query::arrQuery($conn,$sql);
		}
		
		//is libur
		function isLibur($conn) {
			return array('Y'=>'Ya','T'=>'Tidak');
		}
		
		//cek list daftar hari libur
		function getlistLibur($conn) {
			$sql = "select * from ".static::schema()."ms_libur where substring(tglmulai::text,1,4) = substring(now()::text,1,4)";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data['idliburan'] = $row['idliburan'];
				$t_data['namaliburan'] = $row['namaliburan'];
				$t_data['tglmulai'] = $row['tglmulai'];
				$t_data['tglselesai'] = $row['tglselesai'];
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		
		//cek hari libur pada tarif lembur detail 
		function cekstatuslibur($conn, $r_key){
			list($r_kodetariflembur,$r_idpendidikan,$r_periodetarif) = explode('|',$r_key);
			$sql = "select idliburan from ".static::table('ga_tariflemburdetail')." 
					where kodetariflembur = '$r_kodetariflembur' and
					idpendidikan = '$r_idpendidikan' and
					periodetarif = '$r_periodetarif'";
								 
			return Query::arrQuery($conn,$sql);
		}
		
		//simpan hari libur pada tarif lembur detail
		function saveTarifLemburDet($conn,$post,$r_key){
			list($r_kodetariflembur,$r_idpendidikan,$r_periodetarif) = explode('|',$r_key);
			
			$record = array();
			$record['kodetariflembur'] = $post['kodetariflembur'];
			$record['idpendidikan'] = $post['idpendidikan'];
			$record['periodetarif'] = $post['periodetarif'];
			$record['isharilibur'] = $post['isharilibur'];
			
			if ($record['isharilibur'] == 'Y'){
				if(count($post['ceklist']) > 0){
					//hapus dulu di tarif lembur detail
					$conn->Execute("delete from ".static::schema()."ga_tariflemburdetail where kodetariflembur = '$r_kodetariflembur'
									and idpendidikan = '$r_idpendidikan' and periodetarif = '$r_periodetarif' ");
					
					foreach($post['ceklist'] as $val){
						if(!empty($post['ceklist_'.$val])){
							$record['idliburan'] = $val;
							self::insertRecord($conn,$record,false,'ga_tariflemburdetail');
						}
					}
				}
			} else {
				$conn->Execute("delete from ".static::schema()."ga_tariflemburdetail where kodetariflembur = '$r_kodetariflembur'
									and idpendidikan = '$r_idpendidikan' and periodetarif = '$r_periodetarif' ");
			}
			
			return self::SaveStatus($conn);
		}
		
		/***********************SALIN TARIF LEMBUR***************************/
		function saveSalinTarifLembur($conn,$r_periode,$f_keyPeriode,$prosentase){
			$err = 0;
		
			list($err,$msg) = self::delete($conn,$r_periode,'ga_tariflembur','periodetarif');
			
			if(!$err){
				$sql = "insert into ".static::table('ga_tariflembur')." 
						select kodetariflembur,idpendidikan,$r_periode,idjenishari,isharilibur,tariflembur + (tariflembur * $),'".Modul::getUserName()."','".date('Y-m-d H:i:s')."','".$_SERVER['REMOTE_ADDR']."' 
						from ".static::table('ga_tariflembur')."
						where periodetarif='$f_keyPeriode' ";
				
				$conn->Execute($sql);
			}
			if($err)
				$msg = 'Salin tarif lembur gagal, data masih digunakan';
			else
				$msg = 'Salin tarif lembur berhasil';
			
			return array($err,$msg);
		}
		
		/**************************************************** L A P O R A N ******************************************************/
		
		function getInfoLembur($conn, $key){
			list($r_periode,$r_pegawai) = explode("|", $key);
			$periode = $conn->GetRow("select tglawallembur, tglakhirlembur from ".static::table('ga_periodegaji')." where periodegaji='$r_periode'");
			$sql = "select p.totlembur,p.idpegawai,p.tglpresensi,p.jamdatang,p.jampulang,p.kodeabsensi,g.*,
					sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap, u.namaunit
					from ".static::table('pe_presensidet')." p
					left join ".static::table('ga_upahlembur')." g on g.idpegawai=p.idpegawai and g.periodegaji='$r_periode'
					left join ".static::table('ms_pegawai')." m on m.idpegawai=p.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=m.idunit
					where totlembur is not null and issetujuatasan='Y' and isvalid='Y'
					and tglpresensi between '$periode[tglawallembur]' and '$periode[tglakhirlembur]'
					and p.idpegawai='$r_pegawai'";
			$rs = $conn->Execute($sql);
			$a_data = array();
			$a_info = array();
			while ($row = $rs->FetchRow()){
				$a_info['namalengkap'] = $row['namalengkap'];
				$a_info['namaunit'] = $row['namaunit'];
				if ($row['kodeabsensi'] == 'H'){
					$a_data['H']['totlembur'][] = $row['totlembur']/60;
					$a_data['H']['jamdatang'][] = $row['jamdatang'];
					$a_data['H']['jampulang'][] = $row['jampulang'];
					$a_data['H']['tanggal'][] = $row['tglpresensi'];
				}else if ($row['kodeabsensi'] == 'HL'){
					$a_data['HL']['totlembur'][] = $row['totlembur']/60;
					$a_data['HL']['jamdatang'][] = $row['jamdatang'];
					$a_data['HL']['jampulang'][] = $row['jampulang'];
					$a_data['HL']['tanggal'][]= $row['tglpresensi'];
				}
				
				$a_info['upahlembur']= $row['upahlembur'];
			}
			
			return array("info" => $a_info, "data" => $a_data);
		}
		
		function repSlipGaji($conn,$r_periode,$r_kodeunit,$r_idpegawai,$r_tipepeg){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select g.*,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					p.idjenispegawai, p.idtipepeg, js.jabatanstruktural,pd.namapendidikan,	substring(gh.masakerja,1,2)||' thn. ' || substring(gh.masakerja,3,2)||' bln.' as mkgaji,pk.golongan
					from ".static::table('ga_gajipeg')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai=g.idpegawai
					left join ".static::table('ga_historydatagaji')." gh on gh.gajiperiode=g.periodegaji and gh.idpeg = g.idpegawai
					left join ".static::table('ms_struktural')." js on js.idjstruktural=gh.struktural
					left join ".static::table('lv_jenjangpendidikan')." pd on pd.idpendidikan=gh.pendidikan
					left join ".static::table('ms_unit')." u on u.idunit=gh.idunit
					left join ".static::table('ms_pangkat')." pk on pk.idpangkat = gh.pangkatpeg
					where g.periodegaji = '$r_periode'";
			
			if(!empty($r_idpegawai))
				$sql .= " and g.idpegawai = $r_idpegawai";
			if(!empty($r_kodeunit))
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			if(!empty($r_tipepeg))
				$sql .= " and p.idtipepeg = '$r_tipepeg'";
				
			$rs = $conn->Execute($sql);
			
			// Data Tunjangan tetap
			$sql = "select g.* from ".static::table('ga_tunjanganpeg')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai=g.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					left join ".static::table('ga_tunjangan')." t on t.kodetunjangan=g.kodetunjangan
					where g.periodegaji = '$r_periode' and t.isgajitetap = 'Y'";			
			
			if(!empty($r_idpegawai))
				$sql .= " and g.idpegawai = $r_idpegawai";
			if(!empty($r_kodeunit))
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
				
			$rst = $conn->Execute($sql);
			
			while($rowt = $rst->FetchRow()){
				$a_tunj[$rowt['idpegawai']][$rowt['kodetunjangan']] = $rowt['nominal'];
			}
			
			// Data Tunjangan tidak tetap
			$sql = "select g.* from ".static::table('ga_tunjanganpeg')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai=g.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					left join ".static::table('ga_tunjangan')." t on t.kodetunjangan=g.kodetunjangan
					where g.periodegaji = '$r_periode' and (t.isgajitetap = 'N' or t.isgajitetap is null)";
			
			if(!empty($r_idpegawai))
				$sql .= " and g.idpegawai = $r_idpegawai";
			if(!empty($r_kodeunit))
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
				
			$rstt = $conn->Execute($sql);
			
			while($rowtt = $rstt->FetchRow()){
				$a_ttunj[$rowtt['idpegawai']][$rowtt['kodetunjangan']] = $rowtt['nominal'];
			}
			
			// Data Tunjangan awal
			$sql = "select g.*,cast(g.nominal as varchar) as nominal from ".static::table('ga_tunjanganpeg')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai=g.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					left join ".static::table('ga_tunjangan')." t on t.kodetunjangan=g.kodetunjangan
					where g.periodegaji = '$r_periode' and t.isbayargaji = 'T'";
			
			if(!empty($r_idpegawai))
				$sql .= " and g.idpegawai = $r_idpegawai";
			if(!empty($r_kodeunit))
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
				
			$rsa = $conn->Execute($sql);
			
			while($rowa = $rsa->FetchRow()){
				$a_tunja[$rowa['idpegawai']][$rowa['kodetunjangan']] = $rowa['nominal'];
			}
						
			//Data Potongan			
			$sql = "select g.* from ".static::table('ga_potonganpeg')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai=g.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where g.periodegaji = '$r_periode'";			
			
			if(!empty($r_idpegawai))
				$sql .= " and g.idpegawai = $r_idpegawai";
			if(!empty($r_kodeunit))
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
				
			$rsp = $conn->Execute($sql);
			
			while($rowp = $rsp->FetchRow()){
				$a_pot[$rowp['idpegawai']][$rowp['kodepotongan']] = $rowp['nominal'];
			}
			
			//nama periode gaji
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('ga_periodegaji')." where periodegaji = '$r_periode'");
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit'], 'namaperiode' => $namaperiode, 'tunjangan' => $a_tunj, 'tunjanganp' => $a_ttunj, 'tunjangana' => $a_tunja, 'potongan' => $a_pot);
			
			return $a_data;
		}
		
		function repLapGapok($conn,$r_periode){
			$sql = "select idpendidikan,cast(masakerjapend as varchar) as masakerjapend,tarifgapok from ".static::table('ga_tarifgapok')." 
					where periodetarif = '$r_periode' order by masakerjapend,idpendidikan";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_masakerjapend = array();
			while($row = $rs->FetchRow()){
				$a_data[$row['idpendidikan']][$row['masakerjapend']] = $row['tarifgapok'];
				$a_masakerjapend[$row['masakerjapend']] = $row['masakerjapend'];
			}
			
			$a_masakerjapend = array_unique($a_masakerjapend);
			
			//nama periode gaji
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('ga_periodetarif')." where periodetarif = '$r_periode'");
			
			//pendidikan
			$sql = "select idpendidikan,namapendidikan from ".static::table('lv_jenjangpendidikan')." order by idpendidikan";
			$rsp = $conn->Execute($sql);
			
			$a_pendidikan = array();
			while($rowp = $rsp->FetchRow()){
				$a_pendidikan[$rowp['idpendidikan']] = $rowp['namapendidikan'];
			}
			
			return array('data' => $a_data, 'pendidikan' => $a_pendidikan, 'masakerjapend' => $a_masakerjapend, 'namaperiode' => $namaperiode);
		}
		
		function repLapTarifTunjangan($conn,$r_periode,$r_tunjangan){
			$sql = "select variabel1,variabel2,nominal from ".static::table('ga_tariftunjangan')." 
					where periodetarif = '$r_periode' and kodetunjangan='$r_tunjangan' order by variabel1,variabel2";
			$rss = $conn->Execute($sql);
			
			$a_datastruktural = array();
			while($rows = $rss->FetchRow()){
				$a_datastruktural[$rows['variabel1']][$rows['variabel2']] = $rows['nominal'];
			}
			
			$sql = "select variabel1,nominal from ".static::table('ga_tariftunjangan')." 
					where periodetarif = '$r_periode' and kodetunjangan='$r_tunjangan' order by variabel1,variabel2";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()){
				$a_data[$row['variabel1']] = $row['nominal'];
			}
			
			//nama periode gaji
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('ga_periodetarif')." where periodetarif = '$r_periode'");
			
			//nama tunjangan
			$namatunjangan = $conn->GetOne("select namatunjangan from ".static::table('ga_tunjangan')." where kodetunjangan = '$r_tunjangan'");
			
			return array('datastruktural' => $a_datastruktural, 'data' => $a_data, 'namaperiode' => $namaperiode, 'namatunjangan' => $namatunjangan);
		}
		
		function repLapTarifHonor($conn,$r_periode,$r_honor){	
			$sql = "select variabel1,nominal from ".static::table('ga_tarifhonor')." 
					where periodetarif = '$r_periode' and kodehonor='$r_honor' order by variabel1";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()){
				$a_data[$row['variabel1']] = $row['nominal'];
			}
			
			//nama periode gaji
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('ga_periodetarif')." where periodetarif = '$r_periode'");
			
			return array('data' => $a_data, 'namaperiode' => $namaperiode);
		}
		
		function repLapTarifPotongan($conn,$r_periode,$r_potongan){	
			$sql = "select variabel1,nominal from ".static::table('ga_tarifpotongan')." 
					where periodetarif = '$r_periode' and kodepotongan='$r_potongan' order by variabel1";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()){
				$a_data[$row['variabel1']] = $row['nominal'];
			}
			
			//nama potongan
			$namapotongan = $conn->GetOne("select namapotongan from ".static::table('ga_potongan')." where kodepotongan = '$r_potongan'");
			
			//nama periode potongan
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('ga_periodetarif')." where periodetarif = '$r_periode'");
			
			//info potongan (hub kerja)
			$sql = "select idhubkerja, hubkerja from ".static::table('ms_hubkerja')." order by idhubkerja";
			
			$infopotongan = Query::arrQuery($conn, $sql);
			
			return array('data' => $a_data, 'namapotongan' => $namapotongan, 'namaperiode' => $namaperiode, 'infopotongan' => $infopotongan );
		}
		
		function repLapTarifPotonganPeg($conn,$r_periode,$r_potongan){	
			$sql = "select idpegawai,nominal from ".static::table('ga_tarifpotonganpeg')." 
					where periodetarif = '$r_periode' and kodepotongan='$r_potongan' order by idpegawai";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()){
				$a_data[$row['idpegawai']] = $row['nominal'];
			}
			
			//nama potongan
			$namapotongan = $conn->GetOne("select namapotongan from ".static::table('ga_potongan')." where kodepotongan = '$r_potongan'");
			
			//nama periode potongan
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('ga_periodetarif')." where periodetarif = '$r_periode'");
			
			//info potongan (hub kerja)
			$sql = "select t.idpegawai,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai 
					from ".static::table('ga_tarifpotonganpeg')." t
					left join ".static::table('ms_pegawai')." p on p.idpegawai=t.idpegawai
					where kodepotongan = '$r_potongan' and periodetarif='$r_periode'
					order by idpegawai";
			
			$infopotongan = Query::arrQuery($conn, $sql);
			
			return array('data' => $a_data, 'namapotongan' => $namapotongan, 'namaperiode' => $namaperiode, 'infopotongan' => $infopotongan );
		}
		
		function repLapPindahBuku($conn, $r_periode, $r_unit, $sqljenis){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_unit);
			
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('ga_periodegaji')." where periodegaji='$r_periode'");
			
			//pendatanganan
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					p.idjstruktural,s.jabatanstruktural
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_struktural')." s on s.idjstruktural=p.idjstruktural
					where p.idjstruktural in ('10000','22100','22200')";
			$rs = $conn->Execute($sql);
			
			$a_ttd = array();
			while ($row = $rs->FetchRow()){
				if ($row['idjstruktural'] == '10000'){
					$a_ttd['yayasan'] = $row['namalengkap'];
					$a_ttd['jabyayasan'] = $row['jabatanstruktural'];
				}else if ($row['idjstruktural'] == '22100'){
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				}else if ($row['idjstruktural'] == '22200'){
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}
			
			//jenis potongan
			$sql = "select kodepotongan,namapotongan from ".static::table('ga_potongan')." order by kodepotongan";
			$jns = Query::arrQuery($conn, $sql);
			
			//data potongan
			$sql = "select * from ".static::table('ga_potongan')." where periodegaji = '$r_periode' order by idpegawai";
			$rsp = $conn->Execute($sql);
			
			while($rowp = $rsp->FetchRow()){
				$a_pot[$rowp['idpegawai']][$rowp['kodepotongan']] = $rowp['nominal'];
			}
			
			//data gaji
			$sql = "select g.*,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					case when h.struktural is not null then s.jabatanstruktural else 'Staff' end as jabatanstruktural,j.jenispegawai,h.idjenispegawai
					from ".static::table('ga_gajipeg')." g 
					left join ".static::table('ga_historydatagaji')." h on h.idpeg=g.idpegawai and h.gajiperiode = g.periodegaji
					left join ".static::table('ms_pegawai')." p on p.idpegawai=h.idpeg
					left join ".static::table('ms_unit')." u on u.idunit=h.idunit
					left join ".static::table('ms_struktural ')." s on s.idjstruktural=h.struktural
					left join ".static::table('ms_jenispeg  ')." j on j.idjenispegawai=h.idjenispegawai
					where g.periodegaji='$r_periode' {$sqljenis} and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']."
					order by h.idjenispegawai";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return array("ttd" => $a_ttd, "data" => $a_data, "namaperiode" => $namaperiode, "jenis" => $jns, "potongan" => $a_pot);
		}
		
		function repLapSerahBank($conn, $r_periode, $r_unit, $sqljenis){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_unit);
			
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('ga_periodegaji')." where periodegaji='$r_periode'");
			
			//pendatanganan
			$sql = "select sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap, idjstruktural 
					from ".static::table('ms_pegawai')." where idjstruktural in ('10000','20000')";
			$rs = $conn->Execute($sql);
			$a_ttd = array();
			while ($row = $rs->FetchRow()){
				if ($row['idjstruktural'] == '10000')
					$a_ttd['yayasan'] = $row['namalengkap'];
				else if ($row['idjstruktural'] == '20000')
					$a_ttd['rektor'] = $row['namalengkap'];
			}
			
			$sql = "select g.*, anrekening, p.norekening,p.alamat,p.nip
					from ".static::table('ga_gajipeg')." g 
					left join ".static::table('ms_pegawai')." p on p.idpegawai=g.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where g.periodegaji='$r_periode' {$sqljenis} and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return array("ttd" => $a_ttd, "data" => $a_data, "namaperiode" => $namaperiode);
		}
		
		function repLapPindahBukuStruk($conn, $r_periode, $r_unit, $sqljenis){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_unit);
			
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('ga_periodegaji')." where periodegaji='$r_periode'");
			
			//pendatanganan
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					p.idjstruktural,s.jabatanstruktural
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_struktural')." s on s.idjstruktural=p.idjstruktural
					where p.idjstruktural in ('10000','22100','22200')";
			$rs = $conn->Execute($sql);
			
			$a_ttd = array();
			while ($row = $rs->FetchRow()){
				if ($row['idjstruktural'] == '10000'){
					$a_ttd['yayasan'] = $row['namalengkap'];
					$a_ttd['jabyayasan'] = $row['jabatanstruktural'];
				}else if ($row['idjstruktural'] == '22100'){
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				}else if ($row['idjstruktural'] == '22200'){
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}
			
			//jenis potongan
			$sql = "select kodepotongan,namapotongan from ".static::table('ga_potongan')." order by kodepotongan";
			$jns = Query::arrQuery($conn, $sql);
			
			//data potongan
			$sql = "select * from ".static::table('ga_potongan')." where periodegaji = '$r_periode' order by idpegawai";
			$rsp = $conn->Execute($sql);
			
			while($rowp = $rsp->FetchRow()){
				$a_pot[$rowp['idpegawai']][$rowp['kodepotongan']] = $rowp['nominal'];
			}
			
			//Jabatan struktural
			$sql = "select s.idjstruktural,s.jabatanstruktural,s.idunit,u.namaunit,h.idpeg,g.*,
					p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap
					from ".static::table('ms_struktural')." s
					left join ".static::table('ms_unit')." u on u.idunit=s.idunit
					left join ".static::table('ga_historydatagaji')." h on h.struktural=s.idjstruktural and h.gajiperiode = '$r_periode'
					left join ".static::table('ms_pegawai')." p on p.idpegawai=h.idpeg
					left join ".static::table('ga_gajipeg')." g on g.idpegawai=h.idpeg and g.periodegaji = h.gajiperiode
					where u.inforight - u.infoleft > 1 and h.idpeg is not null {$sqljenis}
					order by u.infoleft,s.kodeurutan";
			$rss = $conn->Execute($sql);
			
			$a_data = array();
			while ($rows = $rss->FetchRow())
				$a_data[] = $rows;
				
			return array("ttd" => $a_ttd, "data" => $a_data, "namaperiode" => $namaperiode, "jenis" => $jns, "potongan" => $a_pot);
		}
		
		function repSlipLembur($conn,$r_periode,$r_kodeunit,$r_idpegawai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$periode = $conn->GetRow("select tglawalhitung, tglakhirhitung from ".static::table('ga_periodegaji')." where periodegaji='$r_periode'");
			
			//data header
			$sql = "select g.*,p.nip,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,u.namaunit
					from ".static::table('ga_gajipeg')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai=g.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where g.periodegaji = '$r_periode'";
			
			if(!empty($r_idpegawai))
				$sql .= " and g.idpegawai = $r_idpegawai";
			if(!empty($r_kodeunit))
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
						
			$rs = $conn->Execute($sql);
					
			$sql = "select p.totlembur,p.idpegawai,p.tglpresensi,p.jamdatang,p.jampulang,p.kodeabsensi,g.*
					from ".static::table('pe_presensidet')." p
					left join ".static::table('ga_upahlemburpeg')." g on g.idpegawai=p.idpegawai and g.periodegaji='$r_periode'
					left join ".static::table('ms_pegawai')." m on m.idpegawai=p.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=m.idunit
					where totlembur is not null and issetujuatasan='Y' and isvalid='Y'
					and tglpresensi between '$periode[tglawallembur]' and '$periode[tglakhirlembur]'";
			
			if(!empty($r_idpegawai))
				$sql .= " and p.idpegawai = $r_idpegawai";
			if(!empty($r_kodeunit))
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
				
			$rsl = $conn->Execute($sql);
		
			$a_data = array();
			while ($row = $rsl->FetchRow()){
				if ($row['kodeabsensi'] == 'H'){
					$a_data[$row['idpegawai']]['H']['totlembur'][] = $row['totlembur']/60;
					$a_data[$row['idpegawai']]['H']['jamdatang'][] = $row['jamdatang'];
					$a_data[$row['idpegawai']]['H']['jampulang'][] = $row['jampulang'];
					$a_data[$row['idpegawai']]['H']['tanggal'][] = $row['tglpresensi'];
				}else if ($row['kodeabsensi'] == 'HL'){
					$a_data[$row['idpegawai']]['HL']['totlembur'][] = $row['totlembur']/60;
					$a_data[$row['idpegawai']]['HL']['jamdatang'][] = $row['jamdatang'];
					$a_data[$row['idpegawai']]['HL']['jampulang'][] = $row['jampulang'];
					$a_data[$row['idpegawai']]['HL']['tanggal'][]= $row['tglpresensi'];
				}
				
				$a_data[$row['idpegawai']]['upahlembur']= $row['upahlembur'];
			}
			
			//nama periode gaji
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('ga_periodegaji')." where periodegaji = '$r_periode'");
			
			return array("list" => $rs, "namaunit" => $col['namaunit'], "namaperiode" => $namaperiode,"data" => $a_data);
		}
		
		function repLapRekapGaji($conn, $r_periode, $r_unit, $sqljenis){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_unit);
			
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('ga_periodegaji')." where periodegaji='$r_periode'");
			
			//pendatanganan
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					p.idjstruktural,s.jabatanstruktural
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_struktural')." s on s.idjstruktural=p.idjstruktural
					where p.idjstruktural in ('10000','22100','22200')";
			$rs = $conn->Execute($sql);
			
			$a_ttd = array();
			while ($row = $rs->FetchRow()){
				if ($row['idjstruktural'] == '10000'){
					$a_ttd['yayasan'] = $row['namalengkap'];
					$a_ttd['jabyayasan'] = $row['jabatanstruktural'];
				}else if ($row['idjstruktural'] == '22100'){
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				}else if ($row['idjstruktural'] == '22200'){
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}
			
			//jenis tunjangan
			$sql = "select kodetunjangan,namatunjangan from ".static::table('ga_tunjangan')." order by urutan";
			$jns = Query::arrQuery($conn, $sql);
			
			//data tunjangan
			$sql = "select kodetunjangan,nominal,idpegawai from ".static::table('ga_tunjanganpeg')." where periodegaji = '$r_periode' order by idpegawai";
			$rsp = $conn->Execute($sql);
			
			//jenis potongan
			$sql = "select kodepotongan,namapotongan from ".static::table('ga_potongan')." order by kodepotongan";
			$jnspot = Query::arrQuery($conn, $sql);
			
			//data potongan
			$sql = "select kodepotongan,nominal,idpegawai from ".static::table('ga_potonganpeg')." where periodegaji = '$r_periode' order by idpegawai";
			$rspot = $conn->Execute($sql);
			
			while($rowp = $rsp->FetchRow()){
				$a_tunj[$rowp['idpegawai']][$rowp['kodetunjangan']] = $rowp['nominal'];
			}
			
			while($rowpot = $rspot->FetchRow()){
				$a_pot[$rowpot['idpegawai']][$rowpot['kodepotongan']] = $rowpot['nominal'];
			}
						
			$sql = "select g.*, h.idpeg, p.nip,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					g.gajiditerima, g.gapok, g.pph
					from ".static::table('ga_historydatagaji')." h
					left join ".static::table('ga_gajipeg')." g on g.idpegawai=h.idpeg and h.gajiperiode=g.periodegaji
					left join ".static::table('ms_pegawai')." p on p.idpegawai=h.idpeg
					left join ".static::table('ms_unit')." u on u.idunit=h.idunit
					where h.gajiperiode='$r_periode' {$sqljenis} 
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			$rs = $conn->Execute($sql);			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
				
			return array("ttd" => $a_ttd, "data" => $a_data, "namaperiode" => $namaperiode, "jenis" => $jns, "tunjangan" => $a_tunj, "jenispotongan" => $jnspot, "potongan" => $a_pot);
		}
		
		function repLapRekapGajiHonorer($conn, $r_periode, $r_unit){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_unit);
			
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('ga_periodegaji')." where periodegaji='$r_periode'");
			
			//pendatanganan
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					p.idjstruktural,s.jabatanstruktural
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_struktural')." s on s.idjstruktural=p.idjstruktural
					where p.idjstruktural in ('10000','22100','22200')";
			$rs = $conn->Execute($sql);
			
			$a_ttd = array();
			while ($row = $rs->FetchRow()){
				if ($row['idjstruktural'] == '10000'){
					$a_ttd['yayasan'] = $row['namalengkap'];
					$a_ttd['jabyayasan'] = $row['jabatanstruktural'];
				}else if ($row['idjstruktural'] == '22100'){
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				}else if ($row['idjstruktural'] == '22200'){
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}
			
			$sql = "select g.idpegawai,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					coalesce(g.gajiditerima,0) as gajiditerima, j.namapendidikan, count(tglpresensi) as jum
					from ".static::table('ga_gajipeg')." g
					left join ".static::table('ms_pegawai')." p on g.idpegawai=p.idpegawai
					left JOIN ".static::table('pe_rwtpendidikan')." r on r.nourutrpen=(select ".static::table('pe_rwtpendidikan').".nourutrpen
					from ".static::table('pe_rwtpendidikan')." where ".static::table('pe_rwtpendidikan').".idpegawai=p.idpegawai and isvalid='Y' 
					and isdiakuiuniv='Y' order by ".static::table('pe_rwtpendidikan').".tglijazah desc limit 1)
					left join ".static::table('lv_jenjangpendidikan')." j on j.idpendidikan=r.idpendidikan
					left join ".static::table('pe_presensidet')." t on g.idpegawai=t.idpegawai and 
					date_part('month',tglpresensi)=(select date_part('month',tglawalhitung) from ".static::table('ga_periodegaji')." 
					where periodegaji='$r_periode') and (jamdatang is not null or jampulang is not null)
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where periodegaji='$r_periode' and idhubkerja='HP'
					group by g.idpegawai,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang),gajiditerima,namapendidikan";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
				
			return array('list' => $a_data, 'namaperiode' => $namaperiode, "ttd" => $a_ttd);
		}
	
	
		//laporan rekapitulasi jurnal dosen
		function getRekapJurnalDosen($conn,$r_kodeunit,$r_periodegaji,$r_idpegawai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select g.idpegawai, v.nip, v.namalengkap, v.namaunit
					from ".static::table('ga_mengajarlog')." g
					left join ".static::schema()."v_pegawai v on v.idpegawai=g.idpegawai
					where periodegaji = '$r_periodegaji'";
			
			if(!empty($r_kodeunit))
				$sql .= " and v.infoleft >= ".(int)$col['infoleft']." and v.inforight <= ".(int)$col['inforight']."";
			if(!empty($r_idpegawai))
				$sql .= " and t.idpegawai = $r_idpegawai";
			
			$sql .= " group by g.idpegawai, v.nip, v.namalengkap, v.namaunit";
			$rs1 = $conn->Execute($sql);
			
			$a_row = array();
			while ($row = $rs1->FetchRow())
				$a_row[] = $row;
					
			$sql = "select g.*,mk.namamk,
					case 
						when g.jeniskuliah = 'K' then 'Kuliah' 
						when g.jeniskuliah = 'P' then 'Praktikum'
						when g.jeniskuliah = 'R' then 'Tutorial'
						when g.jeniskuliah = 'Q' then 'Quiz'
						when g.jeniskuliah = 'T' then 'UTS'
						when g.jeniskuliah = 'U' then 'UAS'
						when g.jeniskuliah = 'H' then 'HER'
					end as jeniskul
					from ".static::schema()."ga_mengajarlog g 
					left join ".static::schema()."ms_pegawai p on p.idpegawai = g.idpegawai 
					left join ".static::schema()."ms_unit u on u.idunit = p.idunit 
					left join akademik.ak_matakuliah mk on mk.kodemk = g.kodemk and mk.thnkurikulum = g.thnkurikulum 
					left join ".static::schema()."ga_historydatagaji gh on gh.idpeg = g.idpegawai and gh.gajiperiode = g.periodegaji
					where periodegaji = '$r_periodegaji'";
					
			if(!empty($r_kodeunit))
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			if(!empty($r_idpegawai))
				$sql .= " and g.idpegawai = $r_idpegawai";
			$sql .= " order by p.nip";
			
			$rs = $conn->Execute($sql);
			
			$a_row1 = array();
			while ($row = $rs->FetchRow())
				$a_row1[$row[idpegawai]][] = $row;
				
			$periodegaji = $conn->GetOne("select namaperiode from ".static::schema()."ga_periodegaji where periodegaji = '$r_periodegaji'");
			
			$a_data = array('list' => $rs, 'data' => $a_row,'detail' => $a_row1,  'namaunit' => $col['namaunit'], $periodegaji => 'periodegaji');
			
			return $a_data;			
		}
	
	}	
?>
