<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mStatistik extends mModel {
		const schema = 'sdm';
		
		/*************************************************** START OF REPORT ****************************************************/
		function getSLapJA($conn, $r_unit, $sqljenis,$sqlaktif,$sqlaktifhb){			
			$col = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unit");			
			
			$sql = "select p.idjfungsional,p.idunitbase,u.namaunit from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_fungsional')." f on f.idjfungsional=p.idjfungsional
					left join ".static::table('ms_unit')." u on u.idunit=p.idunitbase
					where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." {$sqljenis} {$sqlaktif} {$sqlaktifhb}
					order by infoleft";
			$rs = $conn->Execute($sql);
			
			$a_stsjabatan = array();
			while($row = $rs->FetchRow()){
				$a_stsjabatan[$row['idunitbase']][$row['idjfungsional']]++;
				$a_data[$row['idunitbase']] = $row;
			}
			
			$sql = "select idjfungsional, jabatanfungsional from ".static::table('ms_fungsional')."";
			$rs = $conn->Execute($sql);
			
			$a_jabatan = array();
			while ($row = $rs->FetchRow())
				$a_jabatan[] = $row;
			
			$a_return = array("list" => $a_data, "namaunit" => $col['namaunit'], "sts" => $a_stsjabatan, "jabatan" => $a_jabatan);
			return $a_return;
		}
		
		function getSLapGol($conn, $r_unit,$r_unithb, $sqljenis,$sqlaktif){
			if(!empty($r_unit)){	
				$col = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unit");
				$unitinfo = " and infoleft >= ".(int)$col['infoleft']." and inforight <= ".(int)$col['inforight'];
			}

			if(!empty($r_unithb)){	
				$colhb = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unithb");
				$unitinfohb = " and infoleft >= ".(int)$colhb['infoleft']." and inforight <= ".(int)$colhb['inforight'];
			}

			$namaunit = !empty($col['namaunit']) ? $col['namaunit'] : (!empty($colhb['namaunit']) ? $colhb['namaunit'] : '');

			$sql = "select idunit,namaunit,level from ".static::table('ms_unit')." 
					where 1=1 {$unitinfo} {$unitinfohb}
					order by infoleft";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			
			$sql = "select p.idpangkat,p.idunit,u.namaunit from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_pangkat')." m on m.idpangkat=p.idpangkat
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where 1=1 {$unitinfo} {$unitinfohb} {$sqljenis} {$sqlaktif}";
			$rs = $conn->Execute($sql);
			
			$a_stspangkat = array();
			while($row = $rs->FetchRow())
				$a_stspangkat[$row['idunit']][$row['idpangkat']]++;
			
			$sql = "select idpangkat, golongan from ".static::table('ms_pangkat')."";
			$rs = $conn->Execute($sql);
			
			$a_pangkat = array();
			while ($row = $rs->FetchRow())
				$a_pangkat[] = $row;
				
			$a_return = array("list" => $a_data, "namaunit" => $namaunit, "sts" => $a_stspangkat, "pangkat" => $a_pangkat);
			return $a_return;
		}
		
		function getSLapHub($conn, $r_unit,$r_unithb, $sqlhubungan,$sqljenis,$sqlaktif){	
			if(!empty($r_unit)){	
				$col = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unit");
				$unitinfo = " and infoleft >= ".(int)$col['infoleft']." and inforight <= ".(int)$col['inforight'];
			}

			if(!empty($r_unithb)){	
				$colhb = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unithb");
				$unitinfohb = " and infoleft >= ".(int)$colhb['infoleft']." and inforight <= ".(int)$colhb['inforight'];
			}

			$namaunit = !empty($col['namaunit']) ? $col['namaunit'] : (!empty($colhb['namaunit']) ? $colhb['namaunit'] : '');
			
			$sql = "select idunit,namaunit,level from ".static::table('ms_unit')." 
					where 1=1 {$unitinfo} {$unitinfohb}
					order by infoleft";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			
			$sql = "select p.idhubkerja,p.idunit,u.namaunit from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_hubkerja')." m on m.idhubkerja=p.idhubkerja
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where 1=1 {$unitinfo} {$unitinfohb} {$sqlhubungan} {$sqljenis} {$sqlaktif}";
			$rs = $conn->Execute($sql);
			
			$a_stshubungan = array();
			while($row = $rs->FetchRow())
				$a_stshubungan[$row['idunit']][$row['idhubkerja']]++;
			
			$sql = "select idhubkerja, hubkerja from ".static::table('ms_hubkerja')."";
			$rs = $conn->Execute($sql);
			
			$a_hubungan = array();
			while ($row = $rs->FetchRow())
				$a_hubungan[] = $row;
				
			$a_return = array("list" => $a_data, "namaunit" => $namaunit, "sts" => $a_stshubungan, "hubungan" => $a_hubungan);
			return $a_return;
		}
		
		function getSLapJenis($conn, $r_unit,$sqljenis, $sqlaktif){			
			$col = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unit");
			
			$sql = "select idunit,namaunit,level from ".static::table('ms_unit')." 
					where infoleft >= ".(int)$col['infoleft']." and inforight <= ".(int)$col['inforight']."
					order by infoleft";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			
			$sql = "select p.idjenispegawai,p.idunit,u.namaunit from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_jenispeg')." m on m.idjenispegawai=p.idjenispegawai
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." {$sqljenis} {$sqlaktif}";
			$rs = $conn->Execute($sql);
			
			$a_stsjenis = array();
			while($row = $rs->FetchRow())
				$a_stsjenis[$row['idunit']][$row['idjenispegawai']]++;
			
			$sql = "select j.*,t.tipepeg from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					order by j.idtipepeg";
			$rs = $conn->Execute($sql);
			
			$a_jenis = array();
			while ($row = $rs->FetchRow()){
				$a_jenis[] = $row;
				$c_jenis[$row['idtipepeg']]++;
			}
				
			$a_return = array("list" => $a_data, "namaunit" => $col['namaunit'], "sts" => $a_stsjenis, "jenis" => $a_jenis, "coljenis" => $c_jenis);
			return $a_return;
		}
		
		function getSLapAktif($conn, $r_unit, $sqlaktif){			
			$col = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unit");
			
			$sql = "select idunit,namaunit,level from ".static::table('ms_unit')." 
					where infoleft >= ".(int)$col['infoleft']." and inforight <= ".(int)$col['inforight']."
					order by infoleft";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			
			$sql = "select p.idstatusaktif,p.idunit,u.namaunit from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." {$sqlaktif}";
			$rs = $conn->Execute($sql);
			
			$a_stsaktif = array();
			while($row = $rs->FetchRow())
				$a_stsaktif[$row['idunit']][$row['idstatusaktif']]++;
			
			$sql = "select idstatusaktif, namastatusaktif from ".static::table('lv_statusaktif')."";
			$rs = $conn->Execute($sql);
			
			$a_aktif = array();
			while ($row = $rs->FetchRow())
				$a_aktif[] = $row;
				
			$a_return = array("list" => $a_data, "namaunit" => $col['namaunit'], "sts" => $a_stsaktif, "aktif" => $a_aktif);
			return $a_return;
		}
		
		function getSLapHomebase($conn, $r_unit,$sqljenis,$sqlaktif,$sqlaktifhb){			
			$col = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unit");
			
			$sql = "select idunit,namaunit,level from ".static::table('ms_unit')." 
					where isakademik = 'Y' and infoleft >= ".(int)$col['infoleft']." and inforight <= ".(int)$col['inforight']."
					order by infoleft";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;			
			
			$sql = "select p.idjenispegawai,p.idunitbase,u.namaunit from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_unit')." u on u.idunit=p.idunitbase
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=p.idtipepeg
					where t.idtipepeg in ('D','AD') and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." {$sqljenis} {$sqlaktif} {$sqlaktifhb}";
			$rs = $conn->Execute($sql);
			
			$a_stsjenis = array();
			while($row = $rs->FetchRow())
				$a_stsjenis[$row['idunitbase']][$row['idjenispegawai']]++;
			
			$sql = "select j.*,t.tipepeg from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					where t.idtipepeg in ('D','AD')
					order by j.idtipepeg";
			$rs = $conn->Execute($sql);
			
			$a_jenis = array();
			while ($row = $rs->FetchRow()){
				$a_jenis[] = $row;
				$c_jenis[$row['idtipepeg']]++;
			}
				
			$a_return = array("list" => $a_data, "namaunit" => $col['namaunit'], "sts" => $a_stsjenis, "jenis" => $a_jenis, "coljenis" => $c_jenis);
			return $a_return;
		}
		
		function getSLapPendidikanDosen($conn, $r_unit,$sqljenis,$sqlaktif,$sqlpendidikan){			
			$col = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unit");
			
			$sql = "select idjenispegawai,tipepeg + ' - ' + jenispegawai as jenispegawai 
					from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					where j.idtipepeg in ('D','AD')
					order by j.idtipepeg,idjenispegawai";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;			
			
			$sql = "select p.idjenispegawai,p.idpendidikan from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_unit')." u on u.idunit=p.idunitbase
					where p.idtipepeg in ('D','AD') and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." {$sqljenis} {$sqlaktif} {$sqlpendidikan}";
			$rs = $conn->Execute($sql);
			
			$a_stspendidikan = array();
			while($row = $rs->FetchRow())
				$a_stspendidikan[$row['idjenispegawai']][$row['idpendidikan']]++;

			$sql = "select idpendidikan,namasingkat from ".static::table('lv_jenjangpendidikan')." order by urutan";
			$rs = $conn->Execute($sql);
			
			$a_pendidikan = array();
			while ($row = $rs->FetchRow()){
				$a_pendidikan[] = $row;
			}
				
			$a_return = array("list" => $a_data, "namaunit" => $col['namaunit'], "sts" => $a_stspendidikan, "pendidikan" => $a_pendidikan);
			return $a_return;
		}
		
		function getSLapDosenSertifikasi($conn, $r_unit,$sqljenis,$sqlaktifhb,$r_serdos){			
			$col = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unit");
			
			$sql = "select idjenispegawai,tipepeg + ' - ' + jenispegawai as jenispegawai 
					from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					where j.idtipepeg in ('D','AD')
					order by j.idtipepeg,idjenispegawai";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;			
			
			$sql = "select p.idjenispegawai,p.idjfungsional from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_unit')." u on u.idunit=p.idunitbase
					where p.idtipepeg in ('D','AD') and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." {$sqljenis} {$sqlaktifhb}";

			if(in_array('Y', $r_serdos) and in_array('T', $r_serdos)){
				$sql .= " and (p.nosertifikasi is not null or p.nosertifikasi is null)";
			}
			else if(in_array('Y', $r_serdos)){
				$sql .= " and p.nosertifikasi is not null";
			}
			else if(in_array('T', $r_serdos)){
				$sql .= " and p.nosertifikasi is null";
			}

			$rs = $conn->Execute($sql);
			
			$a_stsfungsional = array();
			while($row = $rs->FetchRow())
				$a_stsfungsional[$row['idjenispegawai']][$row['idjfungsional']]++;

			$sql = "select idjfungsional,jabatanfungsional from ".static::table('ms_fungsional')." order by idjfungsional";
			$rs = $conn->Execute($sql);
			
			$a_fungsional = array();
			while ($row = $rs->FetchRow()){
				$a_fungsional[] = $row;
			}
				
			$a_return = array("list" => $a_data, "namaunit" => $col['namaunit'], "sts" => $a_stsfungsional, "fungsional" => $a_fungsional);
			return $a_return;
		}
		
		function getSLapBidangIlmu($conn, $r_unit,$sqlfungsional,$sqlpendidikan,$r_sesuai){			
			$col = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unit");
			
			$sql = "select idpendidikan,namapendidikan from ".static::table('lv_jenjangpendidikan')." order by urutan";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;			
			
			$sqlssesuai = $r_sesuai == 'Y' ? " and p.issesuaibidang = 'Y'" : " and p.issesuaibidang is null";

			$sql = "select p.idpendidikan,p.idjfungsional from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_unit')." u on u.idunit=p.idunitbase
					where p.idtipepeg in ('D','AD') and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." {$sqlssesuai} {$sqlpendidikan} {$sqlfungsional}";
			$rs = $conn->Execute($sql);
			
			$a_stssesuai = array();
			while($row = $rs->FetchRow())
				$a_stssesuai[$row['idpendidikan']][$row['idjfungsional']]++;

			$sql = "select idjfungsional,jabatanfungsional from ".static::table('ms_fungsional')." order by idjfungsional";
			$rs = $conn->Execute($sql);
			
			$a_fungsional = array();
			while ($row = $rs->FetchRow()){
				$a_fungsional[] = $row;
			}
				
			$a_return = array("list" => $a_data, "namaunit" => $col['namaunit'], "sts" => $a_stssesuai, "fungsional" => $a_fungsional);
			return $a_return;
		}
				
		function filterJenisDosen($conn){
			$sql = "select idjenispegawai, tipepeg + ' - ' + jenispegawai from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					where j.idtipepeg in ('D','AD') order by j.idtipepeg";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function filterJenis($conn){
			$sql = "select idjenispegawai, tipepeg + ' - ' + jenispegawai from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					order by j.idtipepeg";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function filterAktif($conn){
			$sql = "select idstatusaktif, namastatusaktif from ".static::table('lv_statusaktif')." order by idstatusaktif";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function filterAktifHB($conn){
			$sql = "select idstatusaktifhomebase, namastatusaktifhomebase from ".static::table('lv_statusaktifhomebase')." order by idstatusaktifhomebase";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function filterHubungan($conn){
			$sql = "select idhubkerja, hubkerja from ".static::table('ms_hubkerja')." order by idhubkerja";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function filterPendidikan($conn){
			$sql = "select idpendidikan, namapendidikan from ".static::table('lv_jenjangpendidikan')." order by urutan";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function filterFungsional($conn){
			$sql = "select idjfungsional, jabatanfungsional from ".static::table('ms_fungsional')." order by idjfungsional";
			
			return Query::arrQuery($conn, $sql);
		}
		/*************************************************** START OF REPORT ****************************************************/
	}