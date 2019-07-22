<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mHonor extends mModel {
		const schema = 'sdm';
		
		
		/**************************************************** HONOR ******************************************************/
		
		function listQueryHitHonor(){
			$sql = "select sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap,
					p.nik,p.nodosen,u.namaunit,pa.golongan,j.namapendidikan,t.nominal,g.nominal as tarif, p.idpegawai
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ga_ajardosen')." g on p.idpegawai=g.idpegawai
					left join ".static::table('lv_jenjangpendidikan')." j on j.idpendidikan=p.idpendidikan
					left join ".static::table('ms_pangkat')." pa on pa.idpangkat=p.idpangkat
					left join ".static::table('ga_tarifmengajar')." t on t.idpendidikan=p.idpendidikan and t.idpangkat=p.idpangkat
					where nodosen is not null and idstatusaktif in (select idstatusaktif 
					from ".static::table(lv_statusaktif)." where iskeluar='T')";
			
			return $sql;
			//left join ".static::table('ms_unit')." u on p.idunitbase=u.idunit
		}
		
		function getCPendidikan($conn){
			$sql = "select idpendidikan, namapendidikan from ".static::table('lv_jenjangpendidikan')." order by urutan desc";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getCPangkat($conn){
			$sql = "select idpangkat, golongan || ' - ' || namapangkat from ".static::table('ms_pangkat')." order by idpangkat";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getCAllJenisDosen($conn){
			$sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai as jenispegawai 
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
						return "idjenispegawai = '$key'";
					else
						return "(1=1)";
					
					break;	
			}
		}
		
		function saveHonor($conn, $connsia, $a_post){
			$a_pegawai = $a_post['id'];
			if (count($a_pegawai) > 0){
				$conn->StartTrans();
				foreach($a_pegawai as $idpegawai){
					$isExist = $conn->GetOne("select 1 from ".static::table('ga_ajardosen')." where idpegawai=$idpegawai");
					if (!empty($a_post['nominal_'.$idpegawai])){
						$record = array();
						$record['idpegawai'] = $idpegawai;
						$record['nominal'] = Cstr::cStrDec($a_post['nominal_'.$idpegawai]);
						
						$reclog = array();
						$reclog = $record;
						$reclog['tglubah'] = date("Y-m-d");
						
						if (!$isExist){
							$status = mHonor::insertRecord($conn,$record,true,'ga_ajardosen');
							$statuslog = mHonor::insertRecord($conn,$reclog,true,'ga_ajardosenlog');
						}else{
							$colkey = "idpegawai";
							$keylog = $idpegawai.'|'.$reclog['tglubah'];
							$status = mHonor::updateRecord($conn,$record,$idpegawai,true,'ga_ajardosen',$colkey);
							$statuslog = mHonor::updateRecord($conn,$reclog,$keylog,true,'ga_ajardosenlog',"idpegawai,tglubah");
						}
						
						$kodedosen = $conn->GetOne("select nodosen from ".static::table('ms_pegawai')." where idpegawai=$idpegawai");
						if (!empty($kodedosen)){
							$recrate = array();
							$recrate['BasicRate'] = $record['nominal'];
							$statushonor = mHonor::updateRecord($connsia,$recrate,$kodedosen,true,"tblLecture","Lect_ID","dbo.");
						}
					}	
				}
				$conn->CompleteTrans();				
			}
			
			return $status;
		}
		
		/**************************************************** E N D OF L A P O R A N ******************************************************/
	
	}
?>
