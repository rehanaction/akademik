<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mGajit extends mModel {
		const schema = 'sdm';
		
		
		/**************************************************** GAJI ******************************************************/
		// mendapatkan kueri list untuk setting kehadiran
		function listQueryPeriodeGA() {
			$sql = "select * from ".static::table('ga_periodegaji');
			
			return $sql;
		}
		
		function getCPeriodeGaji($conn){
			$sql = "select periodegaji, namaperiode from ".static::table('ga_periodegaji')." where refperiodegaji is null  order by tglakhirhit desc";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getLastPeriodeGaji($conn){
			$sql = "select top 1 periodegaji from ".static::table('ga_periodegaji')." where refperiodegaji is null  order by tglakhirhit desc";
			
			return $conn->GetOne($sql);
		}
		
		function listQueryPeriodeTarif(){
			$sql = "select * from ".static::table('ms_periodetarif')."";
			
			return $sql;
		}
				
		function getCPeriodeTarif($conn){
			$sql = "select periodetarif, namaperiode from ".static::table('ms_periodetarif')." order by periodetarif desc";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function listQueryGapok(){
			$sql = "select g.*,p.golongan from ".static::table('ms_tarifgapok')." g
					left join ".static::table('ms_pangkat')." p on p.idpangkat = g.idpangkat";
			
			return $sql;
		}
		
		function getCPendidikan($conn){
			$sql = "select idpendidikan, namapendidikan from ".static::table('lv_jenjangpendidikan')." order by urutan desc";
			
			return Query::arrQuery($conn, $sql);
		}
			
		function getCPangkat($conn){
			$sql = "select idpangkat, golongan from ".static::table('ms_pangkat')." order by idpangkat";
			
			return Query::arrQuery($conn, $sql);
		}
				
		function getCBayar(){
			$a_bayar = array('' => '-- Semua --', 'Y' => 'Sudah Dibayar', 'T' => 'Belum Dibayar');
			
			return $a_bayar;
		}
		
		function listQueryPotongan() {
			$sql = "select * from ".static::table('ms_potongan');
			
			return $sql;
		}
		
		function listQueryTunjangan() {
			$sql = "select * from ".static::table('ms_tunjangan');
			
			return $sql;
		}
				
		function getCTunjangan($conn){
			$sql = "select kodetunjangan,namatunjangan from ".static::table('ms_tunjangan')." order by kodetunjangan";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function listQueryTarifTunjangan($r_tunjangan) {
			if($r_tunjangan == 'T00001' or $r_tunjangan == 'T00005'){//T. Struktural dan T. Fasilitas
				$select = ",s.jabatanstruktural as namavariabel";
				$leftjoin = "left join ".static::table('ms_struktural')." s on s.idjstruktural = g.variabel1";
			}
			
			else if($r_tunjangan == 'T00002'){//T. Pendidikan
				$select = ",p.namapendidikan as namavariabel";
				$leftjoin = "left join ".static::table('lv_jenjangpendidikan')." p on p.idpendidikan = g.variabel1";
			}
			
			else if($r_tunjangan == 'T00003'){//T. Transport
				$select = ",gl.golongan as namavariabel";
				$leftjoin = "left join ".static::table('ms_pangkat')." gl on gl.idpangkat = g.variabel1";
			}
			
			else{//Selain di atas
				$select = ",j.jenispegawai as namavariabel";
				$leftjoin = "left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = g.variabel1";
			}
			
			$sql = "select g.*{$select} from ".static::table('ms_tariftunjangan')." g {$leftjoin}";
						
			return $sql;
		}
		
		function getTunjangan($conn,$r_key){
			$tunj = $conn->GetOne("select kodetunjangan from ".static::table('ms_tariftunjangan')." where idtarif = $r_key");
			
			return $tunj;
		}
		
		function infoTunjangan($conn,$r_tunjangan){
			if($r_tunjangan == '')
				$r_tunjangan = $conn->GetOne("select top 1 kodetunjangan from ".static::table('ms_tunjangan')." order by kodetunjangan");
			
			$tunj = $conn->GetOne("select namatunjangan from ".static::table('ms_tunjangan')." where kodetunjangan = '$r_tunjangan'");
			
			if($r_tunjangan == 'T00001' or $r_tunjangan == 'T00005')//T. Struktural dan T. Fasilitas
				$info = 'Jabatan';
			else if($r_tunjangan == 'T00002')//T. Pendidikan
				$info = 'Pendidikan';
			else if($r_tunjangan == 'T00003')//T. Transport
				$info = 'Golongan';
			else//Selain di atas
				$info = 'Jenis Pegawai';
			
			$rtunj['namatunjangan'] = $tunj;
			$rtunj['info'] = $info;
						
			return $rtunj;
		}
		
		function listQueryPajak(){
			$sql = "select * from ".static::table('ms_pajak');
			
			return $sql;
		}
		
		function getListPajakDet($conn,$r_key){
			$sql = "select * from ".static::table('ms_pajakdet')." where idpajak = '$r_key'";
			$rs = $conn->Execute($sql);
			
			return $rs;
		}
		
		function aCaraHitungTunj(){
			return array("M" => "Manual", "T" => "Tarif", "P" => "Parameter");
		}
		
		function getCJenisPegawai($conn){
			$sql = "select idjenispegawai, tipepeg + ' - ' + jenispegawai as jenispegawai 
					from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg 
					order by tipepeg";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getCAllJenisPegawai($conn){
			$sql = "select idjenispegawai, tipepeg + ' - ' + jenispegawai as jenispegawai 
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
						return "datepart(year, tmtmulai) = '$key'";
					else
						return "(1=1)";
					
					break;				
				case 'tunjangan':
					if($key != 'all')
						return "g.kodetunjangan = '$key'";
					else
						return "(1=1)";
					
					break;				
				case 'jenispegawai':
					if($key != 'all')
						return "idjenispegawai = '$key'";
					else
						return "(1=1)";
					
					break;					
				case 'periodegaji':
					return "g.periodegaji = '$key'";
					break;					
				case 'periodetarif':
					return "g.periodetarif = '$key'";
					break;
				case 'golongan':
					return "g.idpangkat = '$key'";
					break;
				case 'tunjangan':
					return "g.kodetunjangan = '$key'";
					break;
				case 'periodehist':
					return "g.gajiperiode = '$key'";
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
		
		function aTunjHak($conn, $r_key){
			$sql = "select j.idjenispegawai, tipepeg + ' - ' + jenispegawai as jenispegawai, kodetunjangan
					from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tunjangandet')." t on j.idjenispegawai=t.idjenispegawai and  kodetunjangan='$r_key'
					left join ".static::table('ms_tipepeg')." tp on tp.idtipepeg=j.idtipepeg
					order by tipepeg";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
				
			return $a_data;
		}
		
		function saveTunjHak($conn, $r_key, $a_jenis){
			$sql = "delete from ".static::table('ms_tunjangandet')." where kodetunjangan='$r_key'";
			$conn->Execute($sql);
			
			$recdetail = array();
			$recdetail['kodetunjangan'] = $r_key;
			foreach($a_jenis as $col){
				unset($recdetail['idjenispegawai']);
				$recdetail['idjenispegawai'] = $col;
				
				mGaji::insertRecord($conn, $recdetail, false, 'ms_tunjangandet');
			}
			return static::updateStatus($conn);
		}		
		
		function listQueryHistoryGaji() {
			$sql = "select g.*,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pk.namapangkat,p.masakerjathngol,p.masakerjablngol,s.jabatanstruktural
					from ".static::table('ga_historydatagaji')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpeg
					left join ".static::table('ms_unit')." u on u.idunit = p.idunit
					left join ".static::table('ms_pangkat')." pk on pk.idpangkat = p.idpangkat
					left join ".static::table('ms_struktural')." s on s.idjstruktural = p.idjstruktural";
			
			return $sql;
		}
		
		function getDataHistoryGaji($r_key){
			$sql = "select g.*,pg.namaperiode as namaperiodegaji,pt.namaperiode as namaperiodetarif,
					nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pk.namapangkat,js.jabatanstruktural,pd.namapendidikan
					from ".static::table('ga_historydatagaji')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpeg
					left join ".static::table('ms_unit')." u on u.idunit = p.idunit
					left join ".static::table('ms_pangkat')." pk on pk.idpangkat = p.idpangkat
					left join ".static::table('lv_jenjangpendidikan')." pd on pd.idpendidikan = p.idpendidikan
					left join ".static::table('ms_struktural')." js on js.idjstruktural = p.idjstruktural";
			
			return $sql;
		}
		
		function tarikData($conn,$r_unit,$r_periode){
			$r_periodetarif = $conn->GetOne("select top 1 periodetarif from ".static::table('ms_periodetarif')." order by tglmulai desc");
			
			global $conn, $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
			
			$row = mUnit::getData($conn,$r_unit);
			
			$sql = "select p.idpegawai,p.statusnikah,p.jmlanak,p.npwp,p.idjstruktural,p.idpendidikan,p.idpangkat,
					cast(substring(".static::schema.".get_mkgolnow(p.idpegawai), 1, 2) as int) as masakerjathngol,cast(substring(".static::schema.".get_mkgolnow(p.idpegawai), 3, 2) as int) as masakerjablngol
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_unit')." u on u.idunit = p.idunit
					where u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			
			$rs = $conn->Execute($sql);
			while($row = $rs->FetchRow()){
				$record = array();
				$record['gajiperiode'] = $r_periode;
				$record['tarifperiode'] = $r_periodetarif;
				$record['idpeg'] = $row['idpegawai'];
				$record['statusnikah'] = $row['statusnikah'];
				$record['jmlanak'] = $row['jmlanak'];
				$record['npwp'] = $row['npwp'];
				$record['struktural'] = $row['idjstruktural'];
				$record['pendidikan'] = $row['idpendidikan'];
				$record['pangkatpeg'] = $row['idpangkat'];
				$record['masakerja'] = str_pad($row['masakerjathngol'], 2, "0", STR_PAD_LEFT).str_pad($row['masakerjablngol'], 2, "0", STR_PAD_LEFT);
				
				$isexist = $conn->GetOne("select 1 from ".static::table('ga_historydatagaji')." where idpeg = ".$record['idpeg']." and gajiperiode = '".$record['gajiperiode']."' and tarifperiode = '".$record['tarifperiode']."'");
				if(empty($isexist))
					$err = self::insertRecord($conn,$record,true,'ga_historydatagaji');
				else{
					$key = $record['idpeg'].'|'.$record['gajiperiode'].'|'.$record['tarifperiode'];
					$colkey = 'idpeg,gajiperiode,tarifperiode';
					$err = self::updateRecord($conn,$record,$key,true,'ga_historydatagaji',$colkey);
				}
			}
			
			$err = $conn->ErrorNo();
			
			return $err;
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
		
		function listQueryGajiTetap() {
			$sql = "select g.*,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pk.namapangkat,p.masakerjathngol,p.masakerjablngol,s.jabatanstruktural
					from ".static::table('ga_historydatagaji')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpeg
					left join ".static::table('ms_unit')." u on u.idunit = p.idunit
					left join ".static::table('ms_pangkat')." pk on pk.idpangkat = p.idpangkat
					left join ".static::table('ms_struktural')." s on s.idjstruktural = p.idjstruktural";
			
			return $sql;
		}
		
		function listQueryTunjLain(){
			$sql = "select g.*, p.nik, sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					t.namatunjangan
					from ".static::table('ga_pegawaitunjangan')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai=g.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					left join ".static::table('ms_tunjangan')." t on t.kodetunjangan=g.kodetunjangan";
			
			return $sql;
		}
		
		function getCTunjLainFilter($conn){
			$sql = "select kodetunjangan, namatunjangan from ".static::table('ms_tunjangan')." 
					where kodetunjangan in ('T00007','T00008','T00009','T00010')";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_add = array('all' => '-- Semua Tunjangan Lain --');
			$a_data = array_merge($a_data,$a_add);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['kodetunjangan']] = $row['namatunjangan'];
			}
			
			return $a_data;
		}
		
		function getCTunjLain($conn){
			$sql = "select kodetunjangan, namatunjangan from ".static::table('ms_tunjangan')." 
					where kodetunjangan in ('T00007','T00008','T00009','T00010')";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getTahunTunjLain($conn){
			$sql = "select  DATEPART(YEAR, tmtmulai) as tahun 
					from ".static::table('ga_pegawaitunjangan')."
					group by DATEPART(YEAR, tmtmulai) order by DATEPART(YEAR, tmtmulai) desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_add = array('all' => '-- Semua Tahun --');
			$a_data = array_merge($a_data,$a_add);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['tahun']] = $row['tahun'];
			}
			
			
			return $a_data;
		}
		
		function getDataEditTunjLain($r_key){
			list( $r_pegawai,$r_key) = explode('|',$r_key);
			$sql = "select g.*, sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap 
					from ".static::table('ga_pegawaitunjangan')." g
					left join ".static::table('ms_pegawai')." m on m.idpegawai=g.idpegawai
					where kodetunjangan='$r_key' and g.idpegawai=$r_pegawai";
			
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
		
		/**************************************************** POTONGAN ******************************************************/
		function getLastPotongan($conn){
			$sql = "select kodepotongan + '|' + ismanual as kode from ".static::table('ms_potongan')." where isaktif='Y' order by kodepotongan";
			
			return $conn->GetOne($sql);
		}		
		
		function getCPotongan($conn){
			$sql = "select kodepotongan + '|' + ismanual, namapotongan from ".static::table('ms_potongan')." 
					where isaktif='Y'";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function listQueryHitPotongan($r_periode='', $r_potongan=''){
			$sqladd = "";
			if (!empty($r_periode))
				$sqladd = " and g.periodegaji='$r_periode'";
				
			if (!empty($r_potongan))
				$sqladd .= " and g.kodepotongan='$r_potongan'";
			
			$sql = "select g.nominal, p.nik, sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					t.namapotongan, u.namaunit, p.idpegawai
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ga_potongan')." g on p.idpegawai=g.idpegawai {$sqladd}
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					left join ".static::table('ms_potongan')." t on t.kodepotongan=g.kodepotongan
					where idstatusaktif in (select idstatusaktif from ".static::table('lv_statusaktif')." where iskeluar='T')";
			
			return $sql;
		}
		
		function savePotongan($conn, $r_periode, $r_potongan, $a_post){
			$a_pegawai = $a_post['id'];
			if (count($a_pegawai) > 0){
				$conn->StartTrans();
				foreach($a_pegawai as $idpegawai){
					$sql = "delete from ".static::table('ga_potongan')." 
							where periodegaji='$r_periode' and kodepotongan='$r_potongan' and idpegawai='$r_pegawai'";
					$conn->Execute($sql);
					if (!empty($a_post['nominal_'.$idpegawai])){
						$record = array();
						$record['periodegaji'] = $r_periode;
						$record['kodepotongan'] = $r_potongan;
						$record['idpegawai'] = $idpegawai;
						$record['nominal'] = $a_post['nominal_'.$idpegawai];
						
						$status = mGaji::insertRecord($conn,$record,true,'ga_potongan');
					}	
				}
				$conn->CompleteTrans();				
			}
			
			return $status;
		}
		
		function hitungPotongan($conn, $r_periode, $r_potongan, $a_post){
			$a_pegawai = $a_post['id'];
			if (count($a_pegawai) > 0){
				$conn->StartTrans();
				foreach($a_pegawai as $idpegawai){
					$sql = "delete from ".static::table('ga_potongan')." 
							where periodegaji='$r_periode' and kodepotongan='$r_potongan' and idpegawai='$r_pegawai'";
					$conn->Execute($sql);
					
					$record = array();
					$record['periodegaji'] = $r_periode;
					$record['kodepotongan'] = $r_potongan;
					$record['idpegawai'] = $idpegawai;
					
					/*if ($r_potongan == 'P00001'){ //potongan transport
						
						$record['nominal'] = $a_post['nominal_'.$idpegawai];
					}	
						
					$status = mGaji::insertRecord($conn,$record,true,'ga_potongan');*/
				}
				$conn->CompleteTrans();				
			}
			
			return $status;
		}
		/**************************************************** END OF POTONGAN ******************************************************/
		
		/**************************************************** START LEMBUR ******************************************************/
		
		function listQueryHitLembur($r_periode) {
			$sql = "select g.*,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pk.golongan,cast(coalesce(p.masakerjathngol,0) as varchar)+' tahun ' + cast(coalesce(p.masakerjablngol,0) as varchar)+' bulan' as mkkerja,
					t.tipepeg+' - '+j.jenispegawai as namajenispegawai 
					from ".static::table('ga_upahlembur')." g
					left join ".static::table('ga_historydatagaji')." h on h.idpeg = g.idpegawai and h.gajiperiode = g.periodegaji
					left join ".static::table('ms_pegawai')." p on p.idpegawai = h.idpeg
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg = p.idtipepeg
					left join ".static::table('ms_jenispeg')." j on j.idjenispegawai = p.idjenispegawai
					left join ".static::table('ms_unit')." u on u.idunit = p.idunit
					left join ".static::table('ms_pangkat')." pk on pk.idpangkat = p.idpangkat";
			
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
					$isExist = mGajit::isDataExist($conn,$key,'ga_upahlembur',$colkey);
					
					if ($isExist)
						list($err,$msg) = mGajit::updateRecord($conn,$record,$key,true,'ga_upahlembur',$colkey);
					else
						list($err,$msg) = mGajit::insertRecord($conn,$record,true,'ga_upahlembur');
					
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
		/**************************************************** END OF LEMBUR ******************************************************/
		
		/**************************************************** END OF GAJI ******************************************************/
		
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
		
		function repSlipLembur($conn,$r_periode,$r_kodeunit,$r_idpegawai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$periode = $conn->GetRow("select tglawallembur, tglakhirlembur from ".static::table('ga_periodegaji')." where periodegaji='$r_periode'");
			
			//data header
			$sql = "select g.*,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,u.namaunit
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
					left join ".static::table('ga_upahlembur')." g on g.idpegawai=p.idpegawai and g.periodegaji='$r_periode'
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
			
			$sql = "select g.*, anrekening, p.norekening,p.alamat,p.nik
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
			$sql = "select kodepotongan,namapotongan from ".static::table('ms_potongan')." order by kodepotongan";
			$jns = Query::arrQuery($conn, $sql);
			
			//data potongan
			$sql = "select * from ".static::table('ga_potongan')." where periodegaji = '$r_periode' order by idpegawai";
			$rsp = $conn->Execute($sql);
			
			while($rowp = $rsp->FetchRow()){
				$a_pot[$rowp['idpegawai']][$rowp['kodepotongan']] = $rowp['nominal'];
			}
			
			//data gaji
			$sql = "select g.*,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
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
			$sql = "select kodepotongan,namapotongan from ".static::table('ms_potongan')." order by kodepotongan";
			$jns = Query::arrQuery($conn, $sql);
			
			//data potongan
			$sql = "select * from ".static::table('ga_potongan')." where periodegaji = '$r_periode' order by idpegawai";
			$rsp = $conn->Execute($sql);
			
			while($rowp = $rsp->FetchRow()){
				$a_pot[$rowp['idpegawai']][$rowp['kodepotongan']] = $rowp['nominal'];
			}
			
			//Jabatan struktural
			$sql = "select s.idjstruktural,s.jabatanstruktural,s.idunit,u.namaunit,h.idpeg,g.*,
					p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap
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
		
		//detail gaji pemindahan buku
		function getDetailPindahBukuStruk($conn,$r_unit,$a_pegawai,$r_periode, $sqljenis){
			if(count($a_pegawai) > 0)
				$r_pegawai = implode(",",$a_pegawai);
			
			//data gaji
			$sql = "select g.*,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					case when h.struktural is not null then s.jabatanstruktural else 'Staff' end as jabatanstruktural
					from ".static::table('ga_gajipeg')." g 
					left join ".static::table('ga_historydatagaji')." h on h.idpeg=g.idpegawai and h.gajiperiode = g.periodegaji
					left join ".static::table('ms_pegawai')." p on p.idpegawai=g.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=h.idunit
					left join ".static::table('ms_struktural ')." s on s.idjstruktural=h.struktural
					where g.periodegaji='$r_periode' {$sqljenis} and u.parentunit = $r_unit";
			if(!empty($r_pegawai))
				$sql .= " and g.idpegawai not in ($r_pegawai)";
				
			$sql .=" order by s.kodeurutan";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function filterJenisDosen($conn){
			$sql = "select idjenispegawai, tipepeg + ' - ' + jenispegawai from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					order by j.idtipepeg";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function filterJenisPeg($conn,$jenis){
			$sql = "select idjenispegawai, tipepeg + ' - ' + jenispegawai from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					where t.idtipepeg in ('$jenis')
					order by j.idtipepeg";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function listQueryGajiHonorer(){
			$sql = "select p.idpegawai,".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap,
					namapendidikan,namaunit,g.gajiditerima as nominal
					from ".static::table('ga_gajipeg')." g 
					left join ".static::table('ms_pegawai')." p on p.idpegawai=g.idpegawai
					left join ".static::table('lv_jenjangpendidikan')." j on j.idpendidikan=p.idpendidikan
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where idhubkerja in ('HP','HR')";
			return $sql;
		}
		
		
		function hitGajiHonorer($conn,$r_periode,$r_sql=''){
			$sql = "select idpendidikan,nominal from ".static::table('ga_tarifhonorer');
			$a_tarif = Query::arrQuery($conn, $sql);
			
			$sql = "select count(tglpresensi) as jum,t.idpegawai,p.idpendidikan,g.idpegawai as idpeg,g.isfinish from ".static::table('pe_presensidet')." t
					left join ".static::table('ms_pegawai')." p on p.idpegawai=t.idpegawai
					left join ".static::table('ga_gajipeg')." g on g.idpegawai=t.idpegawai and periodegaji='$r_periode'
					where datepart(m,tglpresensi)=(select datepart(m,tglawalhit) from ".static::table('ga_periodegaji')." 
					where periodegaji='$r_periode') and p.idstatusaktif in (select idstatusaktif 
					from ".static::table('lv_statusaktif')." where iskeluar='T') and idhubkerja in ('HP','HR')
					group by t.idpegawai,p.idpendidikan,g.idpegawai,g.isfinish";
			$rs = $conn->Execute($sql);
			$a_pegawai = array();
			while ($row = $rs->FetchRow()){						
				$record = array();
				$record['periodegaji'] = $r_periode;
				$record['idpegawai'] = $row['idpegawai'];
				$record['gapok'] = $row['jum'] * $a_tarif[$row['idpendidikan']];
				
				if ($row['idpeg'] <> ''){					
					if ($row['isfinish'] <> 'Y'){
						$key = $r_periode.'|'.$idpegawai;
						$colkey = 'periodegaji,idpegawai';
						$err = self::updateRecord($conn,$record,$key,false,'ga_gajipeg',$colkey);
					}
				}else
					$err = self::insertRecord($conn,$record,false,'ga_gajipeg');
			}
						
			return array($err,$msg);
		}
		
		/**************************************************** E N D OF L A P O R A N ******************************************************/
	
	}
?>
