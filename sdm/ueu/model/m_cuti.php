<?php
	// model semua yang berhubungan riwayat
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mCuti extends mModel {
		const schema = 'sdm';
		
		// mendapatkan kueri list permohonan cuti
		function listQueryPermohonanCuti($r_key) {
			$sql = "select r.*, r.lamacuti::text || ' hari' as lama,c.jeniscuti,
					case when r.statususulan = 'A' then 'Diajukan' when r.statususulan = 'S' then 'Disetujui' when r.statususulan = 'T' then 'Ditolak' else 'Belum Diajukan' end as status
					from ".self::table('pe_rwtcuti')." r
					left join ".self::table('ms_cuti')." c on c.idjeniscuti=r.idjeniscuti
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		//untuk mendapatkan data permohonan cuti
		function getDataEditPermohonanCuti($r_subkey) {
			$sql = "select r.*,c.jeniscuti,case when r.nosurat is null then '<i>Digenerate otomatis</i>' else r.nosurat end as nourutsurat
					from ".self::table('pe_rwtcuti')." r 
					left join ".self::table('ms_cuti')." c on c.idjeniscuti=r.idjeniscuti
					where nourutcuti='$r_subkey'";
			
			return $sql;
		}
		
		//mendapatkan nourut cuti		
		function getNoSuratCuti($conn,$r_subkey,$r_tglpengajuan){
			$thn = substr($r_tglpengajuan,0,4);
			$bln = substr($r_tglpengajuan,5,2);
			
			$a_romawi = SDM::aRomawiSurat();
			
			if(!empty($r_subkey))
				$isvalid = $conn->GetOne("select isvalid from ".self::table('pe_rwtcuti')." where nourutcuti = $r_subkey");
			
			if(empty($r_subkey) or $isvalid != 'Y'){
				$sql = "select max(coalesce(substring(nosurat,1,4)::int,0)::int)+1 as maks
						from ".self::table('pe_rwtcuti')." 
						where date_part('year',tglpengajuan)=date_part('year','$r_tglpengajuan'::timestamp)";
				
				$nourut =  $conn->GetOne($sql);
				
				if (empty($nourut))
					$kode ='0001/'.$a_romawi[(int)$bln].'/'.$thn;
				else
					$kode = str_pad($nourut,'4','0', STR_PAD_LEFT).'/'.$a_romawi[(int)$bln].'/'.$thn;
				
				return $kode;
			}else
				return 'null';
		}
		
		function jenisCuti($conn) {
			$sql = "select idjeniscuti, jeniscuti from ".static::schema()."ms_cuti order by jeniscuti";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getCutiDetail($conn,$r_subkey){
			$sql = "select * from ".self::table('pe_rwtcutidet')." where nourutcuti = '$r_subkey' and ischange is null order by tglmulai";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data['nocutidet'] = $row['nocutidet'];
				$t_data['tglmulai'] = $row['tglmulai'];
				$t_data['tglselesai'] = $row['tglselesai'];
				$t_data['lamacuti'] = $row['lamacuti'];
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		
		function getUnitJabAtas($conn,$r_key){
			$row = $conn->GetRow("select idunit,idjstruktural,idjstrukturalatasan from ".self::table('ms_pegawai')." where idpegawai = '$r_key'");
			
			//atasan
			if(!empty($row['idjstruktural'])){
				$jabatasan = $conn->GetOne("select parentjstruktural from ".self::table('ms_struktural')." 
						where idjstruktural = '".$row['idjstruktural']."'");
			}
			
			if(empty($jabatasan))
				$jabatasan = $row['idjstrukturalatasan'];
				
			if(empty($jabatasan)){
				$jabatasan = $conn->GetOne("select idjstruktural from ".self::table('ms_struktural')." 
						where idunit = '".$row['idunit']."'
						order by kodeeselon desc limit 1");
			}
						
			$nippejabat = $conn->GetOne("select idpegawai from ".self::table('pe_rwtstruktural')." 
						where idjstruktural = '$jabatasan' and isvalid = '1' and tmtmulai <= now() and tmtselesai >= now()
						order by coalesce(cast(isutama as int),0) desc,tmtmulai desc limit 1");
			
			if(!empty($nippejabat)){
				$rw['nippejabat'] = $nippejabat;
				$row = array_merge($row,$rw);
			}
			
			return $row;
		}
		
		//pengecekan status pegawai iskotrak
		function isKontrak($conn,$r_key){
			$istetap = $conn->GetOne("select idhubkerja from ".self::table('ms_pegawai')." where idpegawai = $r_key");
			$return = $istetap == 'H1' ? '1' : '0';
			
			return $return;
		}
		
		//list persetujuan cuti
		function listPersetujuanCuti(){
			$sql = "select r.*, p.nik, sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					c.jeniscuti,r.lamacuti::text || ' hari' as lama
					from ".self::table('pe_rwtcuti')." r 
					left join ".self::table('ms_cuti')." c on c.idjeniscuti=r.idjeniscuti
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					where r.statususulan is not null";

			if(Modul::getRole() == 'PS'){ //bila atasan
				$sql .= " and p.emailatasan = '".Modul::getUserEmail()."'";
			}
			
			return $sql;
		}
		
		function getDataEditPersetujuanCuti($r_subkey) {
			$sql = "select r.*, p.idpegawai,p.nik, sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					c.jeniscuti,r.tglpengajuan as tglp,case when r.nosurat is null then '<i>Digenerate otomatis</i>' else r.nosurat end as nourutsurat
					from ".self::table('pe_rwtcuti')." r 
					left join ".self::table('ms_cuti')." c on c.idjeniscuti=r.idjeniscuti
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					where nourutcuti='$r_subkey'";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			if($col == 'unit') {
				global $conn, $conf;
				require_once($conf['gate_dir'].'model/m_unit.php');
				
				$row = mUnit::getData($conn,$key);
				
				return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
			if($col == 'bulan')
				return "substring(cast(cast(r.tglpengajuan as date) as varchar),6,2) = '$key'";
			if($col == 'tahun')
				return "substring(cast(cast(r.tglpengajuan as date) as varchar),1,4) = '$key'";
		}
		
		//status mengajuan cuti
		function statusAjukanCuti(){
			$status = array('' => 'Belum Diajukan', 'A' => 'Diajukan', 'S' => 'Disetujui', 'T' => 'Ditolak');
			
			return $status;
		}
		
		//status persetujuan cuti
		function statusSetujuiCuti(){
			$status = array('A' => 'Diajukan', 'S' => 'Disetujui', 'T' => 'Ditolak');
			
			return $status;
		}
		
		//function lamacuti
		function getLamaCuti($conn,$tglm,$tgls,$idpegawai){
			$mulai=strtotime($tglm);
			$selesai=strtotime($tgls);
			$jnefektif=0;
			$jlibur=0;
			$liburan=0;
			$lama=0;
			$arlama=array();
			$i=0;
							
			//cek apakah ada shift presensi
			$rss = $conn->Execute("select tglpresensi from ".self::table('pe_presensidet')." 
					where tglpresensi between '$tglm' and '$tgls' 
					and sjamdatang is not null and sjampulang is not null and idpegawai = $idpegawai");
			
			$a_shiftnum = array();
			while($rows = $rss->FetchRow()){
				$a_shiftnum[] = strtotime($rows['tglpresensi']);
			}
			
			//cek selain satpam (K2)
			$jnspeg = $conn->GetOne("select idjenispegawai from ".self::table('ms_pegawai')." where idpegawai = $idpegawai");
			
			while($mulai<=$selesai){
				$nefektif=date("w",$mulai);
				if(($nefektif == 0 or $nefektif == 6) and !in_array($mulai,$a_shiftnum) and $jnspeg != 'K2')
					$jnefektif+=1;	
				$arlama[$i++]=$mulai;		
				$mulai+=86400;
			}		
			
			//Cek apakah hari cuti dalam hari liburan	
			if(count($arlama)>0){
				$libur = $conn->Execute("select tgllibur from ".self::table('ms_liburdetail')." 
						where tgllibur between '$tglm' and '$tgls' and 
						extract(dow from tgllibur) <> 0 and extract(dow from tgllibur) <> 6");

				while($rowl = $libur->FetchRow()){
					if(in_array(strtotime($rowl['tgllibur']),$arlama) and !in_array(strtotime($rowl['tgllibur']),$a_shiftnum))
						$jlibur+=1;
				}
			}
			
			$liburan=$jnefektif+$jlibur; //Mendapatkan hari libur dan non efektif
			$lama=count($arlama)-$liburan; //lama hari cuti setelah dicek hari non efektif dan hari libur
			
			return $lama;
		}
		
		//cek apakah sudah mengajukan cuti
		function cekPengajuanTgl($conn,$idpegawai,$tglm,$tgls){
			$sql = "select 1 from ".self::table('pe_rwtcutidet')." d
					left join ".self::table('pe_rwtcuti')." c on c.nourutcuti = d.nourutcuti
					where c.idpegawai = $idpegawai and (('$tglm' between d.tglmulai and d.tglselesai) or ('$tgls' between d.tglmulai and d.tglselesai)
					or ('$tglm' < d.tglmulai and '$tgls' > d.tglselesai))
					limit 1";
			
			return $conn->GetOne($sql);
		}
		
		function saveCutiDetail($conn,$record,$idpegawai){
			$thnm = substr($record['tglmulai'],0,4);
			$thns = substr($record['tglselesai'],0,4);
			
			//bila beda periode, maka simpannya dipecah
			if($thnm < $thns){
				$reca['nourutcuti'] = $record['nourutcuti'];
				$reca['tglmulai'] = $record['tglmulai'];
				$reca['tglselesai'] = $thnm.'-12-31';
				$lama = mCuti::getLamaCuti($conn,$reca['tglmulai'],$reca['tglselesai'],$idpegawai);
				$reca['lamacuti'] = $lama;
				
				Query::recInsert($conn,$reca,self::table('pe_rwtcutidet'));
				
				//lamabaru
				$recb['nourutcuti'] = $record['nourutcuti'];
				$recb['tglmulai'] = $thns.'-01-01';
				$recb['tglselesai'] = $record['tglselesai'];
				$lamabaru = mCuti::getLamaCuti($conn,$recb['tglmulai'],$recb['tglselesai'],$idpegawai);
				$recb['lamacuti'] = $lamabaru;			
				
				Query::recInsert($conn,$recb,self::table('pe_rwtcutidet'));
			}else{
				$lama = mCuti::getLamaCuti($conn,$record['tglmulai'],$record['tglselesai'],$idpegawai);
				$record['lamacuti'] = $lama;	
			
				Query::recInsert($conn,$record,self::table('pe_rwtcutidet'));
			}
			
			return self::saveStatus($conn);
		}
		
		function deleteCutiDetail($conn,$r_subkey,$r_subkeyx){
			$status = $conn->GetOne("select statususulan from ".self::table('pe_rwtcuti')." where nourutcuti = $r_subkey");
			
			if(!empty($status))
				$conn->Execute("update ".self::table('pe_rwtcutidet')." set ischange = '1' where nourutcuti = $r_subkey and nocutidet = $r_subkeyx");
			else
				$conn->Execute("delete from ".self::table('pe_rwtcutidet')." where nourutcuti = $r_subkey and nocutidet = $r_subkeyx");			
						
			return self::deleteStatus($conn);
		}
		
		function getKetCuti($conn,$r_key){
			$row = $conn->GetRow("select idpegawai,idjeniscuti,tglpengajuan from ".self::table('pe_rwtcuti')." where nourutcuti = $r_key");
			
			return $row;
		}
		
		function getAmbilCuti($conn,$idpegawai,$jenis,$tgl){
			$thn = substr($tgl,0,4);
			$sql = "select coalesce(sum(d.lamacuti),0) from ".self::table('pe_rwtcutidet')." d
					left join ".self::table('pe_rwtcuti')." c on c.nourutcuti = d.nourutcuti
					where c.idpegawai = '$idpegawai' and c.idjeniscuti = '$jenis' and (c.statususulan <> 'T' or c.statususulan is null)
					and substring(cast(cast(d.tglmulai as date) as varchar),1,4) = '$thn' and d.ischange is null";
			
			return $conn->GetOne($sql);
		}
		
		function getSisaCuti($conn,$idpegawai,$jenis,$tgl){
			$sisa = $conn->GetOne("select ".self::table("get_sisacuti('$idpegawai','$jenis','$tgl')")."");
			
			return $sisa;
		}
		
		//pegawai yang mendapatkan cuti besar
		function cutiBesarPeg($conn){
			$sql = "select idpegawai from ".self::table('pe_cutibesardet')." d
					left join ".self::table('pe_cutibesar')." m on m.kodeperiode = d.kodeperiode
					where now()::date between m.tglmulai and m.tglselesai";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['idpegawai']] = $row['idpegawai'];
			}
			
			return $a_data;
		}
		
		/***************************************************** CUTI BESAR **********************************************/
		
		function listQueryForm() {
			$sql = "select f.* from ".static::table('pe_cutibesar')." f";
			
			return $sql;
		}
		
		function getListDetail($conn,$key){			
			$sql = "select pe.*,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap
					from ".static::table('pe_cutibesardet')." pe 
					left join ".static::table('ms_pegawai')." p on p.idpegawai=pe.idpegawai
					where pe.kodeperiode='$key'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function getMasaKerja($conn,$r_subkey){
			$sql = "select ".static::schema()."get_mkgolnow('$r_subkey')";
			
			return $conn->GetOne($sql);
		}
		
		/******************************************************************************************************************/
		/***************************************************** L A P O R A N **********************************************/
		/******************************************************************************************************************/
		
		function getLapCuti($conn,$r_kodeunit,$r_tahun,$r_bulan,$r_jeniscuti){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select count(p.idjeniscuti) as jumlah,p.idjeniscuti 
					from ".static::table('pe_rwtcuti')." p
					left join ".static::schema()."ms_cuti r on r.idjeniscuti=p.idjeniscuti 
					left join ".static::schema()."ms_pegawai s on s.idpegawai=p.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=s.idunit 
					where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']."
					group by p.idjeniscuti";
			$rs = $conn->Execute($sql);
			
			$a_row = array();
			while ($row = $rs->FetchRow())
				$a_row[$row['idjeniscuti']] = $row['jumlah'];
			
			$sqladd = "";
			if ($r_jeniscuti != '')
				$sqladd = " and r.idjeniscuti = '$r_jeniscuti'";
			
			$sql = "select r.*,cast(r.nourutcuti as varchar) as nourutcuti, p.nik, u.namaunit,
					p.namadepan, p.namatengah, p.namabelakang, c.jeniscuti,cast(r.lamacuti as varchar)||' hari' as lama,cast(r.sisacuti as varchar)||' hari' as sisacuti,
					case r.statususulan 
					when 'A' then 'Diajukan' 
					when 'S' then 'Disetujui' 
					when 'T' then 'Ditolak' 
					else '' 
					end as status
					from ".static::schema()."pe_rwtcuti r 
					left join ".static::schema()."ms_cuti c on c.idjeniscuti=r.idjeniscuti
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					where date_part('year',tglpengajuan) = '$r_tahun' and date_part('month',tglpengajuan) = '$r_bulan'
					{$sqladd}
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			$rs = $conn->Execute($sql);
			
			//detail cuti
			$sql = "select d.*,cast(d.nourutcuti as varchar) as nourutcuti,cast(d.nocutidet as varchar) as nocutidet 
					from ".static::schema()."pe_rwtcutidet d
					left join ".static::schema()."pe_rwtcuti r on r.nourutcuti=d.nourutcuti
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					where date_part('year',r.tglpengajuan) = '$r_tahun' and date_part('month',r.tglpengajuan) = '$r_bulan'
					{$sqladd} and d.ischange is null
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			$rsd = $conn->Execute($sql);
			
			$a_det = array();
			while ($rowd = $rsd->FetchRow()){
				$a_det[$rowd['nourutcuti']][$rowd['nocutidet']]['tglmulai'] = $rowd['tglmulai'];
				$a_det[$rowd['nourutcuti']][$rowd['nocutidet']]['tglselesai'] = $rowd['tglselesai'];
			}
			
			$a_data = array('list' => $rs, 'terima' => $a_row, 'namaunit' => $col['namaunit'], 'det' => $a_det);
			
			return $a_data;		
		}
		
		function getLapCutiPeg($conn,$r_idpegawai,$r_tahun1,$r_bulan1,$r_tahun2,$r_bulan2,$r_jeniscuti){
			$tglmulai = $r_tahun1.'-'.str_pad($r_bulan1,2,'0',STR_PAD_LEFT).'-01';
			$tgl2 = $r_tahun2.'-'.str_pad($r_bulan2,2,'0',STR_PAD_LEFT).'-01';
			$stgl = strtotime($tgl2);
			$tglselesai = date('Y-m-t',$stgl);
			
			require_once(Route::getModelPath('pegawai'));
			
			$namapegawai = mPegawai::getNamaPegawai($conn,$r_idpegawai);
			
			$sql = "select r.*,cast(r.nourutcuti as varchar) as nourutcuti,
					c.jeniscuti,cast(r.lamacuti as varchar)||' hari' as lama,cast(r.sisacuti as varchar)||' hari' as sisacuti,
					case r.statususulan 
					when 'A' then 'Diajukan' 
					when 'S' then 'Disetujui' 
					when 'T' then 'Ditolak' 
					else '' 
					end as status
					from ".static::schema()."pe_rwtcuti r 
					left join ".static::schema()."ms_cuti c on c.idjeniscuti=r.idjeniscuti
					where r.idpegawai = $r_idpegawai and r.tglpengajuan between '$tglmulai' and '$tglselesai'";
			if(!empty($r_jeniscuti))
				$sql .= " and r.idjeniscuti = '$r_jeniscuti'";
				
			$sql .= " order by r.tglpengajuan desc";	
			$rs = $conn->Execute($sql);
			
			//detail cuti
			$sql = "select d.*,cast(d.nourutcuti as varchar) as nourutcuti,cast(d.nocutidet as varchar) as nocutidet 
					from ".static::schema()."pe_rwtcutidet d
					left join ".static::schema()."pe_rwtcuti c on c.nourutcuti=d.nourutcuti
					left join ".static::schema()."ms_pegawai p on p.idpegawai=c.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					where c.idpegawai = $r_idpegawai and c.tglpengajuan between '$tglmulai' and '$tglselesai' and d.ischange is null";
			if(!empty($r_jeniscuti))
				$sql .= " and c.idjeniscuti = '$r_jeniscuti'";
				
			$rsd = $conn->Execute($sql);
			
			$a_det = array();
			while ($rowd = $rsd->FetchRow()){
				$a_det[$rowd['nourutcuti']][$rowd['nocutidet']]['tglmulai'] = $rowd['tglmulai'];
				$a_det[$rowd['nourutcuti']][$rowd['nocutidet']]['tglselesai'] = $rowd['tglselesai'];
			}
			
			$a_data = array('list' => $rs, 'namapegawai' => $namapegawai, 'det' => $a_det);
			
			return $a_data;		
		}	
		
		function repRekapCuti($conn, $r_unit,$r_tahun,$r_bulan,$sqljenis){			
			$col = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unit");
			
			$sql = "select idunit,namaunit,level from ".static::table('ms_unit')." 
					where infoleft >= ".(int)$col['infoleft']." and inforight <= ".(int)$col['inforight']."
					order by infoleft";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			
			$sql = "select c.idjeniscuti,p.idunit,u.namaunit 
					from ".static::table('ms_pegawai')." p
					left join (select idpegawai,idjeniscuti from ".static::table('pe_rwtcuti')." where date_part('year',tglpengajuan) = '$r_tahun' and date_part('month',tglpengajuan) = '$r_bulan' and statususulan = 'S' group by idpegawai,idjeniscuti) c on c.idpegawai=p.idpegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." {$sqljenis}";
			$rs = $conn->Execute($sql);
			
			$a_stscuti = array();
			while($row = $rs->FetchRow())
				$a_stscuti[$row['idunit']][$row['idjeniscuti']]++;
			
			$sql = "select idjeniscuti, jeniscuti from ".static::table('ms_cuti')."";
			$rs = $conn->Execute($sql);
			
			$a_cuti = array();
			while ($row = $rs->FetchRow())
				$a_cuti[] = $row;
				
			$a_return = array("list" => $a_data, "namaunit" => $col['namaunit'], "sts" => $a_stscuti, "cuti" => $a_cuti);
			return $a_return;
		}
		
		function filterJenis($conn){
			$sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					order by j.idtipepeg";
			
			return Query::arrQuery($conn, $sql);
		}
				
		/********************************** C R O N *****************************************/
		
		function getCutiShift($conn,$tglsekarang){
			$sql = "select d.*,r.idpegawai from ".static::table('pe_rwtcutidet')." d
					left join ".static::table('pe_rwtcuti')." r on r.nourutcuti = d.nourutcuti
					where '$tglsekarang' between d.tglmulai and d.tglselesai and (r.statususulan <> 'T' or r.statususulan is null) and d.ischange is null";
			$rs = $conn->Execute($sql);
			
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;		
		}
	}
?>
