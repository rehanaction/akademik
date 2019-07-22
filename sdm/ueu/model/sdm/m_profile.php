<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mProfile extends mModel {
		const schema = 'sdm';
		
		// mendapatkan kueri list
		function getPeriode($conn){
			$sql = " select periode, 
					case when substring(periode,5,1) = 1 then substring(periode,1,4) + ' Gasal' else substring(periode,1,4) + ' Genap' end as namaperiode 
					from ".static::table('pe_profildosen')." group by periode";
				
			return Query::arrQuery($conn, $sql);
		}
		
		function getPeriodeYear($conn){
			$sql = " select substring(periode,1,4) as periode, substring(periode,1,4) as namaperiode 
					from ".static::table('pe_profildosen')." group by substring(periode,1,4)";
				
			return Query::arrQuery($conn, $sql);
		}
		
		function getNamaPeriode($connsia,$r_periode){		
			$namaperiode = $connsia->GetOne("select akademik.f_namaperiode(periode) as namaperiode from akademik.ms_periode where periode='$r_periode'");
			
			return $namaperiode;
		}
		
		function getCPeriodeAkademik($connsia,$tahun){			
			//periode akademik
			$sql = "select periode,akademik.f_namaperiode(periode) as namaperiode from akademik.ms_periode where substring(periode,1,4)='$tahun' order by periode";
			
			return Query::arrQuery($connsia, $sql);
		}
		
		/************************************************************ L A P O R A N ***********************************************/
		
		function getLapProfileDosen($conn, $r_periode, $r_unit){
			$sql = "select namaunit, infoleft, inforight from ".static::table('ms_unit')." where idunit=$r_unit";
			$col = $conn->GetRow($sql);
			
			$sql = "select sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap,
					namapendidikan, jabatanfungsional, nodosen, u.namaunit, irpd, irkd
					from ".static::table('pe_profildosen')." pd
					left join ".static::table('ms_pegawai')." p on p.idpegawai=pd.idpegawai
					left join ".static::table('ms_unit')." u on pd.idunit=u.idunit
					left join ".static::table('lv_jenjangpendidikan')." j on j.idpendidikan=pd.idpendidikan
					left join ".static::table('ms_fungsional')." f on f.idjfungsional=pd.idjfungsional
					where periode='$r_periode' and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']."
					order by p.nodosen";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow()){
				if($temp != $row['nodosen'])
					$a_data[] = $row;
				
				$temp = $row['nodosen'];
			}
			
			return array('list' => $a_data, 'unit' => $col['namaunit']);
		}
		
		function getLapRekapILBD($conn, $r_periode, $r_unit){
			
			$sql = "select u.idunit, u.namaunit, u.level, u.parentunit from ".static::table('ms_unit')." u
					where u.isakademik='Y' order by u.infoleft";
			$rs = $conn->Execute($sql);
			
			$a_fakultas = array();
			$a_prodi = array();
			while($row = $rs->FetchRow()){
				if ($row['level'] == 4){
					$a_fakultas['idunit'][] = $row['idunit'];
					$a_fakultas['namaunit'][] = $row['namaunit'];
				}
				else if ($row['level'] == 5){
					$a_prodi['idunit'][$row['parentunit']][] = $row['idunit'];
					$a_prodi['namaunit'][$row['parentunit']][] = $row['namaunit'];
				}
				else if ($row['level'] == 6){
					$a_dekanat['idunit'][$row['parentunit']][] = $row['idunit'];
					$a_dekanat['namaunit'][$row['parentunit']][] = $row['namaunit'];
				}
			}
			
			//nilai ILBD prodi
			$sql = "select pd.idunit, u.level, u.parentunit,up.parentunit as parent,irpd, irkd,periode,pd.idpegawai,p.nodosen
					from ".static::table('pe_profildosen')." pd
					left join ".static::table('ms_pegawai')." p on p.idpegawai=pd.idpegawai
					left join ".static::table('ms_unit')." u on pd.idunit=u.idunit
					left join ".static::table('ms_unit')." up on up.idunit=u.parentunit
					left join ".static::table('lv_jenjangpendidikan')." j on j.idpendidikan=pd.idpendidikan
					left join ".static::table('ms_fungsional')." f on f.idjfungsional=pd.idjfungsional
					where substring(periode,1,4)='$r_periode'
					order by p.nodosen";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_count = array();
			
			$a_fak = array();
			$a_countfak = array();
			while ($row = $rs->FetchRow()){
				if($row['level'] == 4){
					//prodi
					$a_data['irpd'][substr($row['periode'],0,5)][$row['idunit']] += (int)$row['irpd'];
					$a_data['irkd'][substr($row['periode'],0,5)][$row['idunit']] += (int)$row['irkd'];
					$a_count[substr($row['periode'],0,5)][$row['idunit']]++;
					
					//fakultas
					$a_fak['irpd'][substr($row['periode'],0,5)][$row['idunit']] += (int)$row['irpd'];
					$a_fak['irkd'][substr($row['periode'],0,5)][$row['idunit']] += (int)$row['irkd'];
					$a_countfak[substr($row['periode'],0,5)][$row['idunit']]++;
				}
				else if($row['level'] == 5){
					//prodi
					$a_data['irpd'][substr($row['periode'],0,5)][$row['idunit']] += (int)$row['irpd'];
					$a_data['irkd'][substr($row['periode'],0,5)][$row['idunit']] += (int)$row['irkd'];
					$a_count[substr($row['periode'],0,5)][$row['idunit']]++;
					
					//fakultas
					$a_fak['irpd'][substr($row['periode'],0,5)][$row['parentunit']] += (int)$row['irpd'];
					$a_fak['irkd'][substr($row['periode'],0,5)][$row['parentunit']] += (int)$row['irkd'];
					$a_countfak[substr($row['periode'],0,5)][$row['parentunit']]++;
				}
				else if($row['level'] == 6){
					//prodi
					$a_data['irpd'][substr($row['periode'],0,5)][$row['idunit']] += (int)$row['irpd'];
					$a_data['irkd'][substr($row['periode'],0,5)][$row['idunit']] += (int)$row['irkd'];
					$a_count[substr($row['periode'],0,5)][$row['idunit']]++;
					
					//fakultas
					$a_fak['irpd'][substr($row['periode'],0,5)][$row['parent']] += (int)$row['irpd'];
					$a_fak['irkd'][substr($row['periode'],0,5)][$row['parent']] += (int)$row['irkd'];
					$a_countfak[substr($row['periode'],0,5)][$row['parent']]++;
				}
			}
			
			//nilai ILBD fakultas
			// $sql = "select pd.idpegawai,u.parentunit, u.level,irpd, irkd,periode,p.nodosen
					// from ".static::table('pe_profildosen')." pd
					// left join ".static::table('ms_pegawai')." p on p.idpegawai=pd.idpegawai
					// left join ".static::table('ms_unit')." u on pd.idunit=u.idunit
					// left join ".static::table('lv_jenjangpendidikan')." j on j.idpendidikan=pd.idpendidikan
					// left join ".static::table('ms_fungsional')." f on f.idjfungsional=pd.idjfungsional
					// where substring(periode,1,4)='$r_periode' 
					// order by p.nodosen";
			// $rs = $conn->Execute($sql);
			
			// $a_fak = array();
			// $a_countfak = array();
			// while ($row = $rs->FetchRow()){
				// $a_fak['irpd'][substr($row['periode'],0,5)][$row['parentunit']] += (int)$row['irpd'];
				// $a_fak['irkd'][substr($row['periode'],0,5)][$row['parentunit']] += (int)$row['irkd'];
				// $a_countfak[substr($row['periode'],0,5)][$row['parentunit']]++;
			// }
						
			return array('list' => $a_data, 'count' => $a_count, 'countfak' => $a_countfak, 'listfak' => $a_fak, 'fakultas' => $a_fakultas, 'prodi' => $a_prodi, 'dekanat' => $a_dekanat);
		}
		
		function getLapGrafik($conn, $r_periode, $r_unit){
			$sql = "select idpendidikan, namapendidikan from ".static::table('lv_jenjangpendidikan')." where urutan > 7 order by urutan";
			$a_pendidikan = Query::arrQuery($conn, $sql);
			
			$sql = "select idjfungsional, jabatanfungsional from ".static::table('ms_fungsional')." order by idjfungsional desc";
			$a_fungsional = Query::arrQuery($conn, $sql);
			
			$sql = "select idjenispegawai, jenispegawai from ".static::table('ms_jenispeg')." where idtipepeg='D' order by idjenispegawai desc";
			$a_jenispegawai = Query::arrQuery($conn, $sql);
			
			$sql = "select namaunit, infoleft, inforight from ".static::table('ms_unit')." where idunit=$r_unit";
			$col = $conn->GetRow($sql);
			
			$sql = "select p.idpendidikan,p.idjfungsional,p.idjenispegawai,p.nodosen
					from ".static::table('pe_profildosen')." pd
					left join ".static::table('ms_pegawai')." p on p.idpegawai=pd.idpegawai
					left join ".static::table('ms_unit')." u on p.idunit=u.idunit
					left join ".static::table('lv_jenjangpendidikan')." j on j.idpendidikan=pd.idpendidikan
					left join ".static::table('ms_fungsional')." f on f.idjfungsional=pd.idjfungsional
					where periode='$r_periode' and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']."
					order by p.nodosen";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow()){
					$a_data['pendidikan'][$row['idpendidikan']]++;
					$a_data['jenispegawai'][$row['idjenispegawai']]++;
					$a_data['fungsional'][$row['idjfungsional']]++;
			}
			
			$sql = "select p.idpendidikan,p.idjfungsional,p.idjenispegawai,p.nodosen
					from ".static::table('pe_profildosen')." pd
					left join ".static::table('ms_pegawai')." p on p.idpegawai=pd.idpegawai
					left join ".static::table('ms_unit')." u on pd.idunit=u.idunit
					left join ".static::table('lv_jenjangpendidikan')." j on j.idpendidikan=pd.idpendidikan
					left join ".static::table('ms_fungsional')." f on f.idjfungsional=pd.idjfungsional
					where periode='$r_periode' and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']."
					order by p.nodosen";
			$rs = $conn->Execute($sql);
			
			$a_datahb = array();
			while ($row = $rs->FetchRow()){
					$a_datahb['pendidikan'][$row['idpendidikan']]++;
					$a_datahb['jenispegawai'][$row['idjenispegawai']]++;
					$a_datahb['fungsional'][$row['idjfungsional']]++;
			}
			
			return array('count' => $a_data, 'counthb' => $a_datahb, 'unit' => $col['namaunit'], 'pendidikan' => $a_pendidikan, 'fungsional' => $a_fungsional, 'jenispegawai' => $a_jenispegawai);
		}
		/************************************************************ END OF L A P O R A N ***********************************************/
	}
?>
