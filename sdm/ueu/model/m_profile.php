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
		
		function getNamaPeriode($r_periode){
			if (substr($r_periode,4,1) == '0'){
				$addperiode = (int)substr($r_periode,0,4)+1;
				$r_periode = 'Pendek Awal '.substr($r_periode,0,4).'/'.(string)$addperiode;
			}else if (substr($r_periode,4,1) == '1'){
				$addperiode = (int)substr($r_periode,0,4)+1;
				$r_periode = 'Ganjil '.substr($r_periode,0,4).'/'.(string)$addperiode;
			}else if (substr($r_periode,4,1) == '2'){
				$addperiode = (int)substr($r_periode,0,4)+1;
				$r_periode = 'Genap '.substr($r_periode,0,4).'/'.(string)$addperiode;
			}else if (substr($r_periode,4,1) == '4'){
				$addperiode = (int)substr($r_periode,0,4)+1;
				$r_periode = 'Pendek '.substr($r_periode,0,4).'/'.(string)$addperiode;
			}
			
			return $r_periode;
		}
		
		/************************************************************ L A P O R A N ***********************************************/
		
		function getLapProfileDosen($conn, $r_periode, $r_unit){
			$sql = "select namaunit, infoleft, inforight from ".static::table('ms_unit')." where idunit=$r_unit";
			$col = $conn->GetRow($sql);
			
			$namaperiode = mProfile::getNamaPeriode($r_periode);
			
			$sql = "select sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap,
					namapendidikan, jabatanfungsional, nodosen, u.namaunit, irpd, irkd
					from ".static::table('pe_profildosen')." pd
					left join ".static::table('ms_pegawai')." p on p.idpegawai=pd.idpegawai
					left join ".static::table('ms_unit')." u on p.idunit=u.idunit
					left join ".static::table('lv_jenjangpendidikan')." j on j.idpendidikan=pd.idpendidikan
					left join ".static::table('ms_fungsional')." f on f.idjfungsional=pd.idjfungsional
					where periode='$r_periode' and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']."
					order by p.nodosen";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return array('list' => $a_data, 'unit' => $col['namaunit'], 'namaperiode' => $namaperiode);
		}
		
		function getLapRekapILBD($conn, $r_periode, $r_unit){
			
			$sql = "select idunit, namaunit, level, parentunit from ".static::table('ms_unit')." where isakademik='Y' order by infoleft";
			$rs = $conn->Execute($sql);
			
			$a_fakultas = array();
			$a_prodi = array();
			while($row = $rs->FetchRow()){
				if ($row['level'] == 4){
					$a_fakultas['idunit'][] = $row['idunit'];
					$a_fakultas['namaunit'][] = $row['namaunit'];
				}else if ($row['level'] == 5){
					$a_prodi['idunit'][$row['parentunit']][] = $row['idunit'];
					$a_prodi['namaunit'][$row['parentunit']][] = $row['namaunit'];
				}
			}
			
			$sql = "select pd.idunit, u.parentunit, irpd, irkd,periode,pd.idpegawai
					from ".static::table('pe_profildosen')." pd
					left join ".static::table('ms_pegawai')." p on p.idpegawai=pd.idpegawai
					left join ".static::table('ms_unit')." u on pd.idunitsia=u.idunit
					left join ".static::table('lv_jenjangpendidikan')." j on j.idpendidikan=pd.idpendidikan
					left join ".static::table('ms_fungsional')." f on f.idjfungsional=pd.idjfungsional
					where substring(periode,1,4)='$r_periode'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_count = array();
			while ($row = $rs->FetchRow()){
				$a_data['irpd'][substr($row['periode'],0,5)][$row['idunit']] += (int)$row['irpd'];
				$a_data['irkd'][substr($row['periode'],0,5)][$row['idunit']] += (int)$row['irkd'];
				$a_count[substr($row['periode'],0,5)][$row['idunit']]++;
			}
			
			$sql = "select pd.idpegawai,u.parentunit, irpd, irkd,periode
					from ".static::table('pe_profildosen')." pd
					left join ".static::table('ms_pegawai')." p on p.idpegawai=pd.idpegawai
					left join ".static::table('ms_unit')." u on pd.idunitsia=u.idunit
					left join ".static::table('lv_jenjangpendidikan')." j on j.idpendidikan=pd.idpendidikan
					left join ".static::table('ms_fungsional')." f on f.idjfungsional=pd.idjfungsional
					where substring(periode,1,4)='$r_periode' 
					group by pd.idpegawai,u.parentunit, irpd, irkd,periode";
			$rs = $conn->Execute($sql);
			
			$a_fak = array();
			$a_countfak = array();
			while ($row = $rs->FetchRow()){
				$a_fak['irpd'][substr($row['periode'],0,5)][$row['parentunit']] += (int)$row['irpd'];
				$a_fak['irkd'][substr($row['periode'],0,5)][$row['parentunit']] += (int)$row['irkd'];
				$a_countfak[substr($row['periode'],0,5)][$row['parentunit']]++;
			}
			
			return array('list' => $a_data, 'count' => $a_count, 'countfak' => $a_countfak, 'listfak' => $a_fak, 'fakultas' => $a_fakultas, 'prodi' => $a_prodi);
		}
		
		function getLapGrafik($conn, $connsia, $r_periode, $r_unit){
			$sql = "select idpendidikan, namapendidikan from ".static::table('lv_jenjangpendidikan')." where urutan > 7 order by urutan";
			$a_pendidikan = Query::arrQuery($conn, $sql);
			
			$sql = "select idjfungsional, jabatanfungsional from ".static::table('ms_fungsional')." order by idjfungsional desc";
			$a_fungsional = Query::arrQuery($conn, $sql);
			
			$sql = "select idjenispegawai, jenispegawai from ".static::table('ms_jenispeg')." where idtipepeg='D' order by idjenispegawai desc";
			$a_jenispegawai = Query::arrQuery($conn, $sql);
			
			$sql = "select namaunit, infoleft, inforight from ".static::table('ms_unit')." where idunit=$r_unit";
			$col = $conn->GetRow($sql);
			
			$namaperiode = $connsia->GetOne("select Course_Desc as namaperiode from dbo.tblCourse where rtrim(Course_ID)='$r_periode'");
			
			$sql = "select p.idpendidikan,p.idjfungsional,p.idjenispegawai
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
			
			$sql = "select p.idpendidikan,p.idjfungsional,p.idjenispegawai
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
			
			return array('count' => $a_data, 'counthb' => $a_datahb, 'unit' => $col['namaunit'], 'namaperiode' => $namaperiode, 'pendidikan' => $a_pendidikan, 'fungsional' => $a_fungsional, 'jenispegawai' => $a_jenispegawai);
		}
		/************************************************************ END OF L A P O R A N ***********************************************/
	}
?>
