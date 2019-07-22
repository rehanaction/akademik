<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mHonor extends mModel {
		const schema = 'sdm';
		
		
		/**************************************************** HONOR ******************************************************/
		
		function listQueryHitHonor(){
			$sql = "select g.*,p.idpegawai as pidpegawai,sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap,
					p.nip,p.nodosen,u.namaunit,j.namapendidikan,p.idjenispegawai,p.idpendidikan,p.idjfungsional,f.jabatanfungsional
					from ".static::table('ga_ajardosen')." g
					left join ".static::table('ms_pegawai')." p on p.idpegawai=g.idpegawai
					left join ".static::table('lv_jenjangpendidikan')." j on j.idpendidikan=p.idpendidikan
					left join ".static::table('ms_fungsional')." f on f.idjfungsional=p.idjfungsional
					left join ".static::table('ms_unit')." u on p.idunitbase=u.idunit
					where (p.idtipepeg = 'D' or p.idtipepeg = 'AD' or p.nodosen is not null) /*and p.isoffmengajar is null*/";
			
			return $sql;
		}
		
		//mendapatkan prosentase honor
		function getLastProcHonor($conn){
			$sql = "select top 1 prosentase from ".static::table('ms_prochonor')." where isaktif = 'Y' order by tglberlaku desc";
			
			return $conn->GetOne($sql);
		}
		
		function getTunjanganKehadiran($conn,$r_periodetarif){
			$kode = 'T00019';
			$sql = "select nominal from ".static::table('ms_tariftunjangan')." where periodetarif = '$r_periodetarif' and kodetunjangan = '$kode' and variabel1 = 'PT'";
			
			return $conn->GetOne($sql);
		}
		
		function getTunjanganHomebase($conn,$r_periodetarif){
			$kode = 'T00013';
			$sql = "select nominal,variabel1 from ".static::table('ms_tariftunjangan')." where periodetarif = '$r_periodetarif' and kodetunjangan = '$kode'";
			
			$rs = $conn->Execute($sql);
			while($row = $rs->FetchRow()){
				$a_data[$row['variabel1']] = $row['nominal'];
			}
			return $a_data;
		}
		
		function getCPendidikan($conn){
			$sql = "select idpendidikan, namapendidikan from ".static::table('lv_jenjangpendidikan')." order by urutan desc";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getCPangkat($conn){
			$sql = "select idpangkat, golongan || '-' || namapangkat from ".static::table('ms_pangkat')." order by idpangkat";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getCAllJenisDosen($conn){
			$sql = "select idjenispegawai, tipepeg || '-' || jenispegawai as jenispegawai 
					from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg 
					where j.idtipepeg in ('D','AD')
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
		
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once($conf['gate_dir'].'model/m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
					break;
				case 'jenispegawai':
					if($key != 'all')
						return "p.idjenispegawai = '$key'";
					else
						return "(1=1)";
					
					break;
				case 'status':
					if($key == 'all')
						return "(1=1)";
					else if($key == 'null')
						return "g.isvalid = $key";
					else	
						return "g.isvalid = '$key'";
					break;	
			}
		}
		
		function saveHonor($conn,$a_post){
			$a_pegawai = $a_post['id'];
			$a_kodejnsrate = $a_post['kodejnsrate'];
			
			if (count($a_pegawai) > 0){
				$conn->StartTrans();
				foreach($a_pegawai as $idpegawai){							
					$record = array();
					$record['idpegawai'] = $idpegawai;
					
					if(Modul::getRole() == 'A' or Modul::getRole() == 'admga'){
						if(!empty($a_post['check'.$idpegawai]))
							$record['isvalid'] = 'Y';
						else
							$record['isvalid'] = 'null';
					}else
						unset($a_kodejnsrate);
					
					if(Modul::getRole() == 'dppu'){
						$record['procpphmanual'] = CStr::cStrNull($a_post['procpphmanual_'.$idpegawai]);
						$record['biatrans'] = CStr::cStrDec($a_post['biatrans_'.$idpegawai]);
						$record['transport'] = CStr::cStrDec($a_post['transport_'.$idpegawai]);
					}
					
					$isExist = $conn->GetOne("select 1 from ".static::table('ga_ajardosen')." where idpegawai=$idpegawai");
					if (!$isExist)
						list($p_posterr,$p_postmsg) = mHonor::insertRecord($conn,$record,true,'ga_ajardosen');
					else{
						$colkey = "idpegawai";
						list($p_posterr,$p_postmsg) = mHonor::updateRecord($conn,$record,$idpegawai,true,'ga_ajardosen',$colkey);
					}
					
					if (count($a_kodejnsrate) > 0){
						foreach($a_kodejnsrate as $kkodejnsrate => $kodejnsrate){
							$nominal = $a_post['nominal_'.$idpegawai.'_'.$kodejnsrate];
							
							if($nominal != ''){
								$rec['idpegawai'] = $idpegawai;
								$rec['kodejnsrate'] = $kodejnsrate;
								$rec['nominal'] = CStr::cStrDec($nominal);
								
								$isExistRate = $conn->GetOne("select 1 from ".static::table('ga_ratehonor')." where idpegawai=$idpegawai and kodejnsrate='$kodejnsrate'");								
								if (!$isExistRate)
									list($p_posterr,$p_postmsg) = mHonor::insertRecord($conn,$rec,true,'ga_ratehonor');
								else{
									$key = $idpegawai.'|'.$kodejnsrate;
									$colkey = "idpegawai,kodejnsrate";
									list($p_posterr,$p_postmsg) = mHonor::updateRecord($conn,$rec,$key,true,'ga_ratehonor',$colkey);
								}
							}
						}
					}
					
				}
					
				$conn->CompleteTrans();				
			}
			
			return array($p_posterr,$p_postmsg);
		}
		
		function setPPHHonor($conn,$idpegawai='',$idhubkerja='',$isnpwp=''){
			if(!empty($idpegawai))
				$widpegawai = "and idpegawai = '$idpegawai'";
			
			if(!empty($idhubkerja))
				$widhubkerja = "and idhubkerja = '$idhubkerja'";
				
			if(!empty($isnpwp)){
				if($isnpwp == 'Y'){
					$wnpwp = "and npwp is not null";
					$wisnpwp = "and isnpwp = 'Y'";
				}else{
					$wnpwp = "and npwp is null";
					$wisnpwp = "and isnpwp = 'T'";
				}
			}
			
			//prosentase
			$sql = "select * from ".static::table('ms_procpphhonor')." where isaktif='Y' {$widhubkerja} {$wisnpwp}";
			$rsp = $conn->Execute($sql);
			while($rowp = $rsp->FetchRow()){
				$a_proc[$rowp['idhubkerja']][$rowp['isnpwp']] = $rowp['prosentase'];
			}
				
			$sql = "select idpegawai,idhubkerja,npwp from ".static::table('ms_pegawai')." 
					where (idtipepeg = 'D' or idtipepeg = 'AD' or nodosen is not null) and idhubkerja is not null {$widhubkerja} {$widpegawai} {$wnpwp}";			
			$rs = $conn->Execute($sql);
			while($row = $rs->FetchRow()){
				if(count($a_proc[$row['idhubkerja']]) > 0){
					foreach($a_proc[$row['idhubkerja']] as $isnpwp => $prosentase){						
						$record = array();
						$record['idpegawai'] = $row['idpegawai'];
						
						//proc pph
						if($row['npwp'] != '')
							$procpph = $a_proc[$row['idhubkerja']]['Y'];
						else
							$procpph = $a_proc[$row['idhubkerja']]['T'];
						
						$record['procpph'] = CStr::cStrNull($procpph);					
						
						$isExist = $conn->GetOne("select 1 from ".static::table('ga_ajardosen')." where idpegawai=".$row['idpegawai']."");
						if (!$isExist)
							list($p_posterr,$p_postmsg) = mHonor::insertRecord($conn,$record,true,'ga_ajardosen');
						else{
							$colkey = "idpegawai";
							list($p_posterr,$p_postmsg) = mHonor::updateRecord($conn,$record,$idpegawai,true,'ga_ajardosen',$colkey);
						}
					}
				}
			}

        	return array($p_posterr, $p_postmsg);
		}
		
		function getMsJnsRate($conn){
			$sql = "select kodejnsrate, namajnsrate, ismanual from ".static::table('ms_jnsrate')." 
					where isaktif='Y' order by kodejnsrate";
			
			$rs = $conn->Execute($sql);
			while($row = $rs->FetchRow()){
				$a_data[$row['kodejnsrate']]['namajnsrate'] = $row['namajnsrate'];
				$a_data[$row['kodejnsrate']]['ismanual'] = $row['ismanual'];
			}
			return $a_data;
		}
		
		function getRateHonor($conn){
			$sql = "select * from ".static::table('ga_ratehonor')." ";
			
			$rs = $conn->Execute($sql);
			while($row = $rs->FetchRow()){
				$a_data[$row['idpegawai']][$row['kodejnsrate']]  = $row['nominal'];
			}
			return $a_data;
		}
		
		
		/**************************************************** PROC PPH HONOR ***************************************************************/
		function getCHubKerja($conn){
			$sql = "select idhubkerja, hubkerja from ".static::table('ms_hubkerja')." 
					where isaktif='Y' order by hubkerja";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getCNPWP(){
			return array("Y" => "Ada", "T" => "Kosong");
		}
		
		function getSistemKuliah($connsia){
			$sql = "select sistemkuliah, namasistem ||' - '|| tipeprogram as nama from akademik.ak_sistem 
					order by sistemkuliah";
			
			return Query::arrQuery($connsia, $sql);
		}
	
		function getHari(){
			return array("1" => "Senin", "2" => "Selasa","3" => "Rabu", "4" => "Kamis","5" => "Jum'at", "6" => "Sabtu", "7" => "Minggu");
		}
		
		function getJnsPerkuliahan($connsia){
			$sql = "select idjeniskuliah,namajeniskuliah from akademik.lv_jeniskuliah 
					order by idjeniskuliah";
			
			return Query::arrQuery($connsia, $sql);
		}
		
		function getIsOnline(){
			return array("0" => "Tatap Muka", "-1" => "Online");
		}
		
		function getRateJenisRate($conn){
			$sql = "select kodejnsrate,namajnsrate from ".static::table('ms_jnsrate')."
					where isaktif='Y' and ismanual='T' order by kodejnsrate";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getStatusRateHonor(){
			return array('all'=>'Semua','Y'=>'Valid','null'=>'Belum Valid');
		}
		
	}
?>
