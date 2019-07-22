<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mBebanDosen extends mModel {
		const schema = 'sdm';
						
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once($conf['gate_dir'].'model/m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
					break;
				case 'kategori' :
					return "idkategori='$key'";
					break;
				case 'periodebkd' :
					return "b.kodeperiodebd='$key'";
					break;
			}
		}
		
		function getCKategoriRubrik($conn, $isall=true){
			$sql = "select idkategori, kategori from ".static::table('lv_kategori')."";
			if($isall)
				$sql .= " where isall = 'Y'";
				
			$sql .= " order by idkategori";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function aSemester(){
			return array('1' => 'Semester Gasal', '2' => 'Semester Genap');
		}
		
		function getDataPeriodeBKD($r_key){
			$sql = "select substring(kodeperiodebd,1,4) as tahun,substring(kodeperiodebd,5,1) as semester,*
					from ".static::table('ms_periodebd')."
					where kodeperiodebd = '$r_key'";
			
			return $sql;
		}
		
		//menyimpan dosen monev
		function saveMonev($conn,$r_key,$r_pegawai){
			$isExist = $conn->GetOne("select 1 from  ".static::table('bd_monev')." where kodeperiodebd = '$r_key' and idpegawaimonev = '$r_pegawai'");
			
			$record = array();
			$record['kodeperiodebd'] = $r_key;
			$record['idpegawaimonev'] = $r_pegawai;
			
			if(empty($isExist))
				list($p_posterr,$p_postmsg) = self::insertRecord($conn,$record,true,'bd_monev');
			else
				list($p_posterr,$p_postmsg) = self::updateRecord($conn,$record,$r_key.'|'.$r_pegawai,true,'bd_monev','kodeperiodebd,idpegawaimonev');
				
			return array($p_posterr,$p_postmsg);
		}
		
		
		//menyimpan dosen monev detail
		function saveMonevDet($conn,$r_key,$r_pegawai){
			list($periode,$idpegawaimonev) = explode("|",$r_key);
			
			$isExist = $conn->GetOne("select 1 from  ".static::table('bd_monevdet')." where kodeperiodebd = '$periode' and idpegawaimonev = '$idpegawaimonev' and idpegawai = '$r_pegawai'");
			
			$record = array();
			$record['kodeperiodebd'] = $periode;
			$record['idpegawaimonev'] = $idpegawaimonev;
			$record['idpegawai'] = $r_pegawai;
			
			if(empty($isExist))
				list($p_posterr,$p_postmsg) = self::insertRecord($conn,$record,true,'bd_monevdet');
			else
				list($p_posterr,$p_postmsg) = self::updateRecord($conn,$record,$r_key.'|'.$r_pegawai,true,'bd_monevdet','kodeperiodebd,idpegawaimonev,idpegawai');
				
			return array($p_posterr,$p_postmsg);
		}
		
		//mendapatkan daftar dosen monev
		function getDataMonev($conn,$r_key){
			$sql = "select b.*,p.nik||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap
					from ".static::table('bd_monev')." b
					left join ".static::table('ms_pegawai')." p on p.idpegawai = b.idpegawaimonev
					where b.kodeperiodebd = '$r_key'";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()){
				$t_data['namalengkap'] = $row['namalengkap'];
				$t_data['kodeperiodebd'] = $row['kodeperiodebd'];
				$t_data['idpegawaimonev'] = $row['idpegawaimonev'];
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		
		//info monev
		function getInfoMonev($conn,$r_key){
			list($periode,$idpegawai) = explode("|",$r_key);
			
			$sql = "select b.*,d.*,p.nik||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap
					from ".static::table('bd_monev')." b
					left join ".static::table('ms_pegawai')." p on p.idpegawai = b.idpegawaimonev
					left join ".static::table('ms_periodebd')." d on d.kodeperiodebd = b.kodeperiodebd
					where b.kodeperiodebd = '$periode' and b.idpegawaimonev = '$idpegawai'";
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		//pegawa yang dinilai
		function getDosenDinilai($conn,$r_key){
			list($periode,$idpegawai) = explode("|",$r_key);
			
			$sql = "select b.*,p.nik||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap
					from ".static::table('bd_monevdet')." b
					left join ".static::table('ms_pegawai')." p on p.idpegawai = b.idpegawai
					where b.kodeperiodebd = '$periode' and b.idpegawaimonev = '$idpegawai'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()){
				$t_data['namalengkap'] = $row['namalengkap'];
				$t_data['kodeperiodebd'] = $row['kodeperiodebd'];
				$t_data['idpegawai'] = $row['idpegawai'];
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		
		
		//list pegawai dinilai
		function listBDPegawaiDinilai($r_key){
			list($periode,$idpegawai) = explode("|",$r_key);
			
			$sql = "select b.*,p.nik||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					u.namaunit
					from ".static::table('bd_monevdet')." b
					left join ".static::table('ms_pegawai')." p on p.idpegawai = b.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit = p.idunit
					where b.kodeperiodebd = '$periode' and b.idpegawaimonev = '$idpegawai'";
			
			return $sql;
		}
		
		//last periode
		function getLastPeriodeBKD($conn){
			$lastperiode = $conn->GetOne("select kodeperiodebd from ".static::table('ms_periodebd')." order by tglawal desc");
			
			return $lastperiode;
		}
		
		//periode BKD
		function getCPeriodeBKD($conn){
			$rs = $conn->Execute("select * from ".static::table('ms_periodebd')." order by tglawal desc");
			
			while($row = $rs->FetchRow()){
				$a_data[$row['kodeperiodebd']] = $row['periodebd'];
			}
			
			return $a_data;
		}
		
		//list beban dosen
		function listBebanDosen(){
			$sql = "select b.*,m.periodebd,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pm.nik||' - '||".static::schema.".f_namalengkap(pm.gelardepan,pm.namadepan,pm.namatengah,pm.namabelakang,pm.gelarbelakang) as namamonev
					from ".static::table('bd_bebandosen')." b
					left join ".static::table('ms_periodebd')." m on m.kodeperiodebd = b.kodeperiodebd
					left join ".static::table('ms_pegawai')." p on p.idpegawai = b.idpegawai
					left join ".static::table('ms_pegawai')." pm on pm.idpegawai = b.idpegawaimonev
					left join ".static::table('ms_unit')." u on u.idunit = p.idunit";

			if(Modul::getRole() == 'P'){ //bila pegawai
				$sql .= " where b.idpegawai = ".Modul::getIDPegawai()."";
			}
			
			return $sql;
		}
		
		//cek apakah dosen
		function isDosen($conn,$r_key){
			$isdosen = $conn->GetOne("select isdosen from ".static::table('ms_pegawai')." where idpegawai = $r_key");
			
			return $isdosen;
		}
				
		//list nilai beban dosen
		function listNilaiBebanDosen(){
			$sql = "select b.*,m.periodebd,p.nik||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pm.nik||' - '||".static::schema.".f_namalengkap(pm.gelardepan,pm.namadepan,pm.namatengah,pm.namabelakang,pm.gelarbelakang) as namamonev
					from ".static::table('bd_bebandosen')." b
					left join ".static::table('ms_periodebd')." m on m.kodeperiodebd = b.kodeperiodebd
					left join ".static::table('ms_pegawai')." p on p.idpegawai = b.idpegawai
					left join ".static::table('ms_pegawai')." pm on pm.idpegawai = b.idpegawaimonev
					left join ".static::table('ms_unit')." u on u.idunit = p.idunit
					where b.isfinal = 'Y'";
			
			return $sql;
		}
		
		//set load kategori
		function setKategori($conn){
			$sql = "select idkategori from ".static::table('lv_kategori')." order by idkategori limit 1";
			
			return $conn->GetOne($sql);			
		}
		
		//kategori
		function getKategori($conn){
			$sql = "select idkategori, kategori from ".static::table('lv_kategori')." order by idkategori";
			
			return Query::arrQuery($conn, $sql);			
		}
		
		//periode
		function getPeriodeBD($conn){
			$sql = "select kodeperiodebd, periodebd from ".static::table('ms_periodebd')." order by tglawal desc";
			
			return Query::arrQuery($conn, $sql);			
		}
		
		//status dosen
		function getStatusDosen($conn){
			$sql = "select kodestatusdosen, statusdosen from ".static::table('lv_statusdosen')." order by kodestatusdosen";
			
			return Query::arrQuery($conn, $sql);			
		}
		
		//query mendapatkan data pegawai bkd
		function sQLDataBKD($conn,$r_periode,$r_key){
			$sql = "select v.*,v.nik||' - '||v.namalengkap as pegawai,
					p.nik||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as monev
					from ".self::table('v_databebandosen')." v
					left join ".static::table('ms_pegawai')." p on p.idpegawai = (select d.idpegawaimonev from ".self::table('bd_monevdet')." d where d.idpegawai = '$r_key' and d.kodeperiodebd = '$r_periode')
					where v.idpegawai = '$r_key'";
			
			//cek apakah pegawai sudah punya monev
			$monev = $conn->GetOne("select idpegawaimonev from ".self::table('bd_monevdet')." where idpegawai = '$r_key' and kodeperiodebd = '$r_periode'");
			if(!empty($monev))
				$cek = true;
				
			return array('sql' => $sql, 'cek' => $cek);
		}
		
		//mendapatkan data pegawai bkd
		function getDataBKD($conn,$r_key){
			$sql = "select v.*,v.nik||' - '||v.namalengkap as pegawai 
					from ".self::table('v_databebandosen')." v
					where idpegawai = '$r_key'";
			
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		//mendapatkan data input pegawai bkd
		function getDataInputBKD($r_key){
			list($periode,$idpegawaimonev,$idpegawai) = explode("|",$r_key);
			$sql = "select b.*,p.nik||' - '||b.namalengkap as pegawai,p.nohp,p.tmplahir,p.tgllahir,
					pm.nik||' - '||".static::schema.".f_namalengkap(pm.gelardepan,pm.namadepan,pm.namatengah,pm.namabelakang,pm.gelarbelakang) as monev,
					case when b.isfinal = 'Y' then 'Sudah diajukan' else 'Belum diajukan' end as isfinal,
					case when b.isfinalreal = 'Y' then 'Sudah dinilai' else 'Belum dinilai' end as isfinalreal, jabatanfungsional as fungsional
					from ".self::table('bd_bebandosen')." b
					left join ".static::table('ms_pegawai')." p on p.idpegawai = b.idpegawai
					left join ".static::table('ms_fungsional')." f on f.idjfungsional=p.idjfungsional 
					left join ".static::table('ms_pegawai')." pm on pm.idpegawai = b.idpegawaimonev
					where b.kodeperiodebd = '$periode' and b.idpegawaimonev = '$idpegawaimonev' and b.idpegawai = '$idpegawai'";
			
			return $sql;
		}
		
		//cek apakah sudah diajukan
		function isDiajukan($conn,$r_key){
			list($periode,$idpegawaimonev,$idpegawai) = explode("|",$r_key);
			$isdiajukan = $conn->GetOne("select isfinal from ".self::table('bd_bebandosen')." where kodeperiodebd = '$periode' and idpegawaimonev = '$idpegawaimonev' and idpegawai = '$idpegawai'");
			
			if($isdiajukan == 'Y')
				return true;
			else
				return false;
		}
		
		//cek apakah sudah final
		function isFinal($conn,$r_key){
			list($periode,$idpegawaimonev,$idpegawai) = explode("|",$r_key);
			$isfinal = $conn->GetOne("select isfinalreal from ".self::table('bd_bebandosen')." where kodeperiodebd = '$periode' and idpegawaimonev = '$idpegawaimonev' and idpegawai = '$idpegawai'");
			
			if($isfinal == 'Y')
				return true;
			else
				return false;
		}
		
		//monev
		function getMonev($conn,$r_periode,$r_key){
			$monev = $conn->GetOne("select idpegawaimonev from ".self::table('bd_monevdet')." where idpegawai = '$r_key' and kodeperiodebd = '$r_periode'");
			
			return $monev;
		}
		
		//bidang-bidang BKD
		function getBidangBKD($conn){
			$sql = "select idkategori, kategori from ".static::table('lv_kategori')." where isall = 'Y' order by idkategori";
			
			return Query::arrQuery($conn, $sql);			
		}
		
		//pop up rubrik
		function getRubrik($conn,$kat=''){
			if(!empty($kat))
				$kat = "and idkategori = '$kat'";
			
			$sql = "select * from ".self::table('ms_kegiatandosen')." where isaktif = 'Y' {$kat} order by idjeniskegiatan";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data['idjeniskegiatan'] = $row['idjeniskegiatan'];
				$t_data['namakegiatan'] = $row['namakegiatan'];
				$t_data['kodekegiatan'] = $row['kodekegiatan'];
				$t_data['sksmax'] = $row['sksmax'];
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
			
		}
		
		//mendapatkan data riwayat
		function getDataRwtBKD($conn,$r_key){
			list($periode,$idpegawaimonev,$idpegawai) = explode("|",$r_key);
			$sql = "select d.*,r.idkategori 
					from ".self::table('bd_bebandosenadet')." d
					left join ".static::table('ms_kegiatandosen')." r on r.idjeniskegiatan = d.idjeniskegiatan
					where d.kodeperiodebd = '$periode' and d.idpegawaimonev = '$idpegawaimonev' and d.idpegawai = '$idpegawai'
					order by r.idkategori";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()){
				$a_data[$row['idkategori']][] = $row;
			}
			
			return $a_data;
		}
		
		//mendapatkan detail bkd
		function getDataBKDDet($r_key){
			list($periode,$idpegawaimonev,$idpegawai,$nobd) = explode("|",$r_key);
			$sql = "select d.*, m.kodekegiatan||' - '||m.namakegiatan as namakegiatan,m.idkategori as kategori
					from ".self::table('bd_bebandosenadet')." d
					left join ".static::table('ms_kegiatandosen')." m on m.idjeniskegiatan = d.idjeniskegiatan
					where d.kodeperiodebd = '$periode' and d.idpegawaimonev = '$idpegawaimonev' and d.idpegawai = '$idpegawai' and d.nobd = '$nobd'";
			
			return $sql;
		}
		
		
		/*************************************L A P O R A N   B K D*************************************/
		
		function getLapBebanDosen($conn,$r_periode,$r_unit='',$r_pegawai=''){
			$namaperiode = $conn->GetOne("select periodebd from ".static::table('ms_periodebd')." where kodeperiodebd='$r_periode'");
			
			if(!empty($r_unit))
				$col = $conn->GetRow("select namaunit, infoleft, inforight from ".static::table('ms_unit')." where idunit='$r_unit'");
			
			$sql = "select b.*,sd.statusdosen,p.tmplahir,p.tgllahir,p.nohp,g.golongan
					from ".static::table('bd_bebandosen')." b
					left join ".static::table('ms_pegawai')." p on p.idpegawai=b.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit					
					left join ".static::table('lv_statusdosen')." sd on sd.kodestatusdosen=b.status					
					left join ".static::table('ms_pangkat')." g on g.idpangkat=p.idpangkat					
					where b.kodeperiodebd='$r_periode'";
					
			if(!empty($r_unit)) 
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			if(!empty($r_pegawai))
				$sql .= " and b.idpegawai = '$r_pegawai'";
				
			$rs = $conn->Execute($sql);
			
			$sql = "select d.*,r.idkategori 
					from ".self::table('bd_bebandosenadet')." d
					left join ".static::table('ms_pegawai')." p on p.idpegawai=d.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					left join ".static::table('ms_kegiatandosen')." r on r.idjeniskegiatan = d.idjeniskegiatan
					where d.kodeperiodebd='$r_periode'";
					
			if(!empty($r_unit)) 
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			if(!empty($r_pegawai))
				$sql .= " and d.idpegawai = '$r_pegawai'";
			
			$sql .= " order by r.idkategori";
			
			$rsd = $conn->Execute($sql);
			
			$a_bkddet = array();
			while($rowd = $rsd->FetchRow()){
				$a_bkddet[$rowd['idpegawai']][$rowd['idkategori']][] = $rowd;
			}
			
			return array("list" => $rs, "listdet" => $a_bkddet, "unit" => $col['namaunit'], "periode" => $namaperiode);			
		}
	}
?>
