<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mStatistik extends mModel {
		const schema = 'sdm';
		
		/*************************************************** START OF REPORT ****************************************************/
		function getSLapJA($conn, $r_unit, $sqljenis){			
			$col = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unit");
			
			$sql = "select idunit,namaunit,level from ".static::table('ms_unit')." 
					where infoleft >= ".(int)$col['infoleft']." and inforight <= ".(int)$col['inforight']."
					order by infoleft";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			
			$sql = "select p.idjfungsional,p.idunit,u.namaunit from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_fungsional')." f on f.idjfungsional=p.idjfungsional
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." {$sqljenis}";
			$rs = $conn->Execute($sql);
			
			$a_stsjabatan = array();
			while($row = $rs->FetchRow())
				$a_stsjabatan[$row['idunit']][$row['idjfungsional']]++;
			
			$sql = "select idjfungsional, jabatanfungsional from ".static::table('ms_fungsional')."";
			$rs = $conn->Execute($sql);
			
			$a_jabatan = array();
			while ($row = $rs->FetchRow())
				$a_jabatan[] = $row;
			
			$a_return = array("list" => $a_data, "namaunit" => $col['namaunit'], "sts" => $a_stsjabatan, "jabatan" => $a_jabatan);
			return $a_return;
		}
		
		function getSLapGol($conn, $r_unit, $sqljenis){			
			$col = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unit");
			
			$sql = "select idunit,namaunit,level from ".static::table('ms_unit')." 
					where infoleft >= ".(int)$col['infoleft']." and inforight <= ".(int)$col['inforight']."
					order by infoleft";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			
			$sql = "select p.idpangkat,p.idunit,u.namaunit from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_pangkat')." m on m.idpangkat=p.idpangkat
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." {$sqljenis}";
			$rs = $conn->Execute($sql);
			
			$a_stspangkat = array();
			while($row = $rs->FetchRow())
				$a_stspangkat[$row['idunit']][$row['idpangkat']]++;
			
			$sql = "select idpangkat, golongan from ".static::table('ms_pangkat')."";
			$rs = $conn->Execute($sql);
			
			$a_pangkat = array();
			while ($row = $rs->FetchRow())
				$a_pangkat[] = $row;
				
			$a_return = array("list" => $a_data, "namaunit" => $col['namaunit'], "sts" => $a_stspangkat, "pangkat" => $a_pangkat);
			return $a_return;
		}
		
		function getSLapHub($conn, $r_unit, $sqljenis){			
			$col = $conn->GetRow("select infoleft,inforight,namaunit from ".static::table('ms_unit')." where idunit=$r_unit");
			
			$sql = "select idunit,namaunit,level from ".static::table('ms_unit')." 
					where infoleft >= ".(int)$col['infoleft']." and inforight <= ".(int)$col['inforight']."
					order by infoleft";
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			
			$sql = "select p.idhubkerja,p.idunit,u.namaunit from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_hubkerja')." m on m.idhubkerja=p.idhubkerja
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." {$sqljenis}";
			$rs = $conn->Execute($sql);
			
			$a_stshubungan = array();
			while($row = $rs->FetchRow())
				$a_stshubungan[$row['idunit']][$row['idhubkerja']]++;
			
			$sql = "select idhubkerja, hubkerja from ".static::table('ms_hubkerja')."";
			$rs = $conn->Execute($sql);
			
			$a_hubungan = array();
			while ($row = $rs->FetchRow())
				$a_hubungan[] = $row;
				
			$a_return = array("list" => $a_data, "namaunit" => $col['namaunit'], "sts" => $a_stshubungan, "hubungan" => $a_hubungan);
			return $a_return;
		}
		
		function getSLapJenis($conn, $r_unit, $sqlaktif){			
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
					where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." {$sqlaktif}";
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
		
		function getSLapAktif($conn, $r_unit, $sqljenis){			
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
					where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." {$sqljenis}";
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
		
		function getSLapHomebase($conn, $r_unit, $sqlaktif){			
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
					where t.idtipepeg in ('P') and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." {$sqlaktif}";
			$rs = $conn->Execute($sql);
			
			$a_stsjenis = array();
			while($row = $rs->FetchRow())
				$a_stsjenis[$row['idunitbase']][$row['idjenispegawai']]++;
			
			$sql = "select j.*,t.tipepeg from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					where t.idtipepeg in ('P')
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
				
		function filterJenisDosen($conn){
			$sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					where j.idtipepeg in ('P') order by j.idtipepeg";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function filterJenis($conn){
			$sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					order by j.idtipepeg";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function filterAktif($conn){
			$sql = "select idstatusaktif, namastatusaktif from ".static::table('lv_statusaktif')." order by idstatusaktif";
			
			return Query::arrQuery($conn, $sql);
		}
		/*************************************************** START OF REPORT ****************************************************/
	}
