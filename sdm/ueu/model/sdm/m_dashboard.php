<?php
	// model presensi
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mDashboard extends mModel {
		const schema = 'sdm';
		
		function graphJumlahKaryawan($conn){
			//tipe pegawai baru
			$sql = "select idtipepeg, tipepeg from ".static::table('ms_tipepegbaru')." order by tipepeg";
			$rs = $conn->Execute($sql);
			$a_tipepegawaibaru = array();
			while ($row = $rs->FetchRow())
				$a_tipepegawaibaru[] = $row;

			//berdasarkan jenis yang baru yang dosen
			$sql = "select idjenispegawai,tipepeg || ' - ' || jenispegawai as nama 
					from ".static::table('ms_jenispegbaru')." j
					left join ".static::table('ms_tipepegbaru')." t on t.idtipepeg=j.idtipepeg 
					where j.idtipepeg = 'D'
					order by j.idtipepeg";
			$rs = $conn->Execute($sql);
			$a_jenispegawaidosen = array();
			while ($row = $rs->FetchRow())
				$a_jenispegawaidosen[] = $row;

			//berdasarkan jenis yang baru yang kependidikan
			$sql = "select idjenispegawai,tipepeg || ' - ' || jenispegawai as nama 
					from ".static::table('ms_jenispegbaru')." j
					left join ".static::table('ms_tipepegbaru')." t on t.idtipepeg=j.idtipepeg 
					where j.idtipepeg = 'TK'
					order by j.idtipepeg";
			$rs = $conn->Execute($sql);
			$a_jenispegawaikependidikan = array();
			while ($row = $rs->FetchRow())
				$a_jenispegawaikependidikan[] = $row;

			//kelompok pegawai administrasi
			$sql = "select idkelompok, namakelompok from ".static::table('ms_kelompokpeg')." 
					where idjenispegawai = 'A' order by idkelompok";

			$rs = $conn->Execute($sql);
			$a_kelompokpegawaiadm = array();
			while ($row = $rs->FetchRow())
				$a_kelompokpegawaiadm[] = $row;

			//kelompok pegawai non administrasi
			$sql = "select idkelompok, namakelompok from ".static::table('ms_kelompokpeg')." 
					where idjenispegawai = 'NA' order by idkelompok";

			$rs = $conn->Execute($sql);
			$a_kelompokpegawainonadm = array();
			while ($row = $rs->FetchRow())
				$a_kelompokpegawainonadm[] = $row;
			
			$sql = "select idtipepegbaru,idjenispegbaru,idkelompok,idjfungsional
					from ".static::table('ms_pegawai')." p 
					where idstatusaktif in (select idstatusaktif 
					from ".static::table('lv_statusaktif')." where iskeluar<>'Y')";
			$rs = $conn->Execute($sql);
			$a_sum = array();
			while ($row = $rs->FetchRow()){
				$a_sum['tipebaru'][$row['idtipepegbaru']]++;

				if($row['idtipepegbaru'] == 'D')
					$a_sum['jenisdosen'][$row['idjenispegbaru']]++;
				else
					$a_sum['jeniskependidikan'][$row['idjenispegbaru']]++;

				if($row['idjenispegbaru'] == 'A')
					$a_sum['kelompokpegadm'][$row['idkelompok']]++;
				else if($row['idjenispegbaru'] == 'NA')
					$a_sum['kelompokpegnonadm'][$row['idkelompok']]++;
			}

			/*
			//berdasarkan jenis
			$sql = "select idjenispegawai,tipepeg + ' - ' + jenispegawai as nama 
					from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg 
					where idjenispegawai not in ('TT','AA')
					order by tipepeg";
			$rs = $conn->Execute($sql);
			$a_jenispegawai = array();
			while ($row = $rs->FetchRow())
				$a_jenispegawai[] = $row;
			
			//tipe pegawai 
			$sql = "select idtipepeg, tipepeg from ".static::table('ms_tipepeg')." order by tipepeg";
			$rs = $conn->Execute($sql);
			$a_tipepegawai = array();
			while ($row = $rs->FetchRow())
				$a_tipepegawai[] = $row;
			
			//Status aktif
			$sql = "select idstatusaktif, namastatusaktif from ".static::table('lv_statusaktif')." order by idstatusaktif";
			$rs = $conn->Execute($sql);
			$a_statusaktif = array();
			while ($row = $rs->FetchRow())
				$a_statusaktif[] = $row;
			
			//hubungan kerja
			$sql = "select idhubkerja, hubkerja from ".static::table('ms_hubkerja')." order by hubkerja";
			$rs = $conn->Execute($sql);
			$a_hubkerja = array();
			while ($row = $rs->FetchRow())
				$a_hubkerja[] = $row;
				
			//hubungan pangkat
			$sql = "select idpangkat, golongan from ".static::table('ms_pangkat')." order by idpangkat";
			$rs = $conn->Execute($sql);
			$a_pangkat = array();
			while ($row = $rs->FetchRow())
				$a_pangkat[] = $row;
			
			$sql = "select idjenispegawai,idstatusaktif,idhubkerja,idpangkat,idtipepeg
					from ".static::table('ms_pegawai')." p 
					where idstatusaktif in (select idstatusaktif 
					from ".static::table('lv_statusaktif')." where iskeluar<>'Y') and p.idjenispegawai not in ('TT','AA')";
			$rs = $conn->Execute($sql);
			$a_sum = array();
			while ($row = $rs->FetchRow()){
				$a_sum['tipe'][$row['idtipepeg']]++;
				$a_sum['jenis'][$row['idjenispegawai']]++;
				$a_sum['statusaktif'][$row['idstatusaktif']]++;
				$a_sum['hubkerja'][$row['idhubkerja']]++;
				$a_sum['pangkat'][$row['idpangkat']]++;
			}
			*/


			//Pembuatan graph				
			$a_graph = array();
			if (count($a_tipepegawaibaru) > 0){
				$a_tipebaru = array();
				foreach($a_tipepegawaibaru as $row){
					$a_tipebaru[$row['idtipepeg']] = ($a_sum['tipebaru'][$row['idtipepeg']] == '' ? '0' : $a_sum['tipebaru'][$row['idtipepeg']]);
					$a_graph['tipebaru'][] = "['".$row['idtipepeg']."', ".($a_sum['tipebaru'][$row['idtipepeg']] == '' ? '0' : $a_sum['tipebaru'][$row['idtipepeg']])."]";
				}
			}
			
			if (count($a_jenispegawaidosen) > 0){
				$a_jenisdosen = array();
				foreach($a_jenispegawaidosen as $row){
					$a_jenisdosen[$row['idjenispegawai']] = ($a_sum['jenisdosen'][$row['idjenispegawai']] == '' ? '0' : $a_sum['jenisdosen'][$row['idjenispegawai']]);
					$a_graph['jenisdosen'][] = "['".$row['idjenispegawai']."', ".($a_sum['jenisdosen'][$row['idjenispegawai']] == '' ? '0' : $a_sum['jenisdosen'][$row['idjenispegawai']])."]";
				}
			}
			
			if (count($a_jenispegawaikependidikan) > 0){
				$a_jeniskependidikan = array();
				foreach($a_jenispegawaikependidikan as $row){
					$a_jeniskependidikan[$row['idjenispegawai']] = ($a_sum['jeniskependidikan'][$row['idjenispegawai']] == '' ? '0' : $a_sum['jeniskependidikan'][$row['idjenispegawai']]);
					$a_graph['jeniskependidikan'][] = "['".$row['idjenispegawai']."', ".($a_sum['jeniskependidikan'][$row['idjenispegawai']] == '' ? '0' : $a_sum['jeniskependidikan'][$row['idjenispegawai']])."]";
				}
			}
			
			if (count($a_kelompokpegawaiadm) > 0){
				$a_kelompokpegadm = array();
				foreach($a_kelompokpegawaiadm as $row){
					$a_kelompokpegadm[$row['idkelompok']] = ($a_sum['kelompokpegadm'][$row['idkelompok']] == '' ? '0' : $a_sum['kelompokpegadm'][$row['idkelompok']]);
					$a_graph['kelompokpegadm'][] = "['".$row['idkelompok']."', ".($a_sum['kelompokpegadm'][$row['idkelompok']] == '' ? '0' : $a_sum['kelompokpegadm'][$row['idkelompok']])."]";
				}
			}
			
			if (count($a_kelompokpegawainonadm) > 0){
				$a_kelompokpegnonadm = array();
				foreach($a_kelompokpegawainonadm as $row){
					$a_kelompokpegnonadm[$row['idkelompok']] = ($a_sum['kelompokpegnonadm'][$row['idkelompok']] == '' ? '0' : $a_sum['kelompokpegnonadm'][$row['idkelompok']]);
					$a_graph['kelompokpegnonadm'][] = "['".$row['idkelompok']."', ".($a_sum['kelompokpegnonadm'][$row['idkelompok']] == '' ? '0' : $a_sum['kelompokpegnonadm'][$row['idkelompok']])."]";
				}
			}

			/*
			if (count($a_tipepegawai) > 0){
				$a_tipe = array();
				foreach($a_tipepegawai as $row){
					$a_tipe[$row['idtipepeg']] = ($a_sum['tipe'][$row['idtipepeg']] == '' ? '0' : $a_sum['tipe'][$row['idtipepeg']]);
					$a_graph['tipe'][] = "['".$row['idtipepeg']."', ".($a_sum['tipe'][$row['idtipepeg']] == '' ? '0' : $a_sum['tipe'][$row['idtipepeg']])."]";
				}
			}
			
			if (count($a_jenispegawai) > 0){
				$a_jenis = array();
				foreach($a_jenispegawai as $row){
					$a_jenis[$row['idjenispegawai']] = ($a_sum['jenis'][$row['idjenispegawai']] == '' ? '0' : $a_sum['jenis'][$row['idjenispegawai']]);
					$a_graph['jenis'][] = "['".$row['idjenispegawai']."', ".($a_sum['jenis'][$row['idjenispegawai']] == '' ? '0' : $a_sum['jenis'][$row['idjenispegawai']])."]";
				}
			}
			
			if (count($a_statusaktif) > 0){
				$a_stsaktif = array();
				foreach($a_statusaktif as $row){
					$a_stsaktif[$row['idstatusaktif']] = ($a_sum['statusaktif'][$row['idstatusaktif']] == '' ? '0' : $a_sum['statusaktif'][$row['idstatusaktif']]);
					$a_graph['statusaktif'][] = "['".$row['idstatusaktif']."', ".($a_sum['statusaktif'][$row['idstatusaktif']] == '' ? '0' : $a_sum['statusaktif'][$row['idstatusaktif']])."]";
				}
			}
			
			if (count($a_hubkerja) > 0){
				$a_hubungan = array();
				foreach($a_hubkerja as $row){
					$a_hubungan[$row['idhubkerja']] = ($a_sum['hubkerja'][$row['idhubkerja']] == '' ? '0' : $a_sum['hubkerja'][$row['idhubkerja']]);
					$a_graph['hubkerja'][] = "['".$row['idhubkerja']."', ".($a_sum['hubkerja'][$row['idhubkerja']] == '' ? '0' : $a_sum['hubkerja'][$row['idhubkerja']])."]";
				}
			}
			
			if (count($a_pangkat) > 0){
				$a_pangkatsum = array();
				foreach($a_pangkat as $row){
					$a_pangkatsum[$row['idpangkat']] = ($a_sum['pangkat'][$row['idpangkat']] == '' ? '0' : $a_sum['pangkat'][$row['idpangkat']]);
					$a_graph['pangkat'][] = "['".$row['idpangkat']."', ".($a_sum['pangkat'][$row['idpangkat']] == '' ? '0' : $a_sum['pangkat'][$row['idpangkat']])."]";
				}
			}
			*/

			$graph['tipebaru'] = implode(",",$a_graph['tipebaru']);
			$graph['jenisdosen'] = implode(",",$a_graph['jenisdosen']);
			$graph['jeniskependidikan'] = implode(",",$a_graph['jeniskependidikan']);
			$graph['kelompokpegadm'] = implode(",",$a_graph['kelompokpegadm']);
			$graph['kelompokpegnonadm'] = implode(",",$a_graph['kelompokpegnonadm']);

			/*
			$graph['tipe'] = implode(",",$a_graph['tipe']);
			$graph['jenis'] = implode(",",$a_graph['jenis']);
			$graph['statusaktif'] = implode(",",$a_graph['statusaktif']);
			$graph['hubungan'] = implode(",",$a_graph['hubkerja']);
			$graph['pangkat'] = implode(",",$a_graph['pangkat']);
			*/

			$a_return = array();
			$a_return['graph'] = $graph;
			
			$a_return['tipepegawaibaru'] = $a_tipepegawaibaru;
			$a_return['keterangantipebaru'] = $a_tipebaru;
			$a_return['jenispegawaidosen'] = $a_jenispegawaidosen;
			$a_return['keteranganjenisdosen'] = $a_jenisdosen;
			$a_return['jenispegawaikependidikan'] = $a_jenispegawaikependidikan;
			$a_return['keteranganjeniskependidikan'] = $a_jeniskependidikan;
			$a_return['kelompokpegawaiadm'] = $a_kelompokpegawaiadm;
			$a_return['keterangankelompokpegadm'] = $a_kelompokpegadm;
			$a_return['kelompokpegawainonadm'] = $a_kelompokpegawainonadm;
			$a_return['keterangankelompokpegnonadm'] = $a_kelompokpegnonadm;
			
			/*
			$a_return['tipepegawai'] = $a_tipepegawai;
			$a_return['keterangantipe'] = $a_tipe;
			$a_return['jenispegawai'] = $a_jenispegawai;
			$a_return['keteranganjenis'] = $a_jenis;
			$a_return['statusaktif'] = $a_statusaktif;
			$a_return['keteranganstatusaktif'] = $a_stsaktif;
			$a_return['hubungan'] = $a_hubkerja;
			$a_return['keteranganhubungan'] = $a_hubungan;
			$a_return['pangkat'] = $a_pangkat;
			$a_return['keteranganpangkat'] = $a_pangkatsum;
			*/

			return $a_return;
		}

		function graphJumlahDosenFung($conn,$idjenispegawai=''){				
			//hubungan fungsional
			$sql = "select idjfungsional, jabatanfungsional from ".static::table('ms_fungsional')." order by idjfungsional";
			$rs = $conn->Execute($sql);
			$a_fungsional = array();
			while ($row = $rs->FetchRow())
				$a_fungsional[] = $row;
			
			$sql = "select idjfungsional
					from ".static::table('ms_pegawai')." p 
					where idstatusaktif in (select idstatusaktif 
					from ".static::table('lv_statusaktif')." where iskeluar<>'Y') and p.idtipepegbaru = 'D'";
			
			if(!empty($idjenispegawai))
				$sql .= " and p.idjenispegbaru = '$idjenispegawai'";

			$rs = $conn->Execute($sql);
			$a_sum = array();
			while ($row = $rs->FetchRow()){
				$a_sum['fungsional'][$row['idjfungsional']]++;
			}
			
			if (count($a_fungsional) > 0){
				$a_fungsionalsum = array();
				foreach($a_fungsional as $row){
					$a_fungsionalsum[$row['idjfungsional']] = ($a_sum['fungsional'][$row['idjfungsional']] == '' ? '0' : $a_sum['fungsional'][$row['idjfungsional']]);
					$a_graph['fungsional'][] = "['".$row['idjfungsional']."', ".($a_sum['fungsional'][$row['idjfungsional']] == '' ? '0' : $a_sum['fungsional'][$row['idjfungsional']])."]";
				}
			}

			$graph['fungsional'] = implode(",",$a_graph['fungsional']);
			
			$a_return = array();
			$a_return['graph'] = $graph;
			$a_return['fungsional'] = $a_fungsional;
			$a_return['keteranganfungsional'] = $a_fungsionalsum;

			return $a_return;
		}	

		function graphJumlahDosenHB($conn,$idjenispegawai=''){	
			//jumlah pegawai berdasar homebase
			$sql = "select kodeunit, namaunit, level from ".static::table('ms_unit')." where isakademik = 'Y'  order by idunit";
			$rs = $conn->Execute($sql);
			$a_homebase = array();
			while ($row = $rs->FetchRow())
				$a_homebase[] = $row;
			
			$sql = "select u.kodeunit
					from ".static::table('ms_pegawai')." p 
					left join ".static::table('ms_unit')." u on u.idunit = p.idunitbase
					where idstatusaktif in (select idstatusaktif 
					from ".static::table('lv_statusaktif')." where iskeluar<>'Y') and p.idjenispegawai not in ('TT','AA')";
			
			if(!empty($idjenispegawai))
				$sql .= " and p.idjenispegawai = '$idjenispegawai'";

			$rs = $conn->Execute($sql);
			$a_sum = array();
			while ($row = $rs->FetchRow()){
				$a_sum['homebase'][$row['kodeunit']]++;
			}

			if (count($a_homebase) > 0){
				$a_homebasesum = array();
				foreach($a_homebase as $row){
					$a_homebasesum[$row['kodeunit']] = ($a_sum['homebase'][$row['kodeunit']] == '' ? '0' : $a_sum['homebase'][$row['kodeunit']]);
					$a_graph['homebase'][] = "['".$row['kodeunit']."', ".($a_sum['homebase'][$row['kodeunit']] == '' ? '0' : $a_sum['homebase'][$row['kodeunit']])."]";
				}
			}

			$graph['homebase'] = implode(",",$a_graph['homebase']);
			
			$a_return = array();
			$a_return['graph'] = $graph;
			$a_return['homebase'] = $a_homebase;
			$a_return['keteranganhomebase'] = $a_homebasesum;

			return $a_return;
		}
		
		function jenispegawai($conn){
			$sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai as jenis
					from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					where j.idtipepeg in ('D','AD') and j.idjenispegawai not in ('TT','AA')
					order by j.idtipepeg";
			$rs = $conn->Execute($sql);

			return Query::arrQuery($conn,$sql);
		}
		
		function jenispegawaibaru($conn){
			$sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai as jenis
					from ".static::table('ms_jenispegbaru')." j
					left join ".static::table('ms_tipepegbaru')." t on t.idtipepeg=j.idtipepeg
					where j.idtipepeg = 'D'
					order by j.idtipepeg";
			$rs = $conn->Execute($sql);

			return Query::arrQuery($conn,$sql);
		}
		
		function graphAbsenMinggu($conn, $r_date){
			$r_datestart = date("Y-m-d",strtotime($r_date) - (13*86400));
			$kodeabsensi = "D','C','T','PD','A','I','S','HL','H";
			
			$sql = "select kodeabsensi, absensi from ".static::table('ms_absensi')." where kodeabsensi in ('$kodeabsensi')";
			$rs = $conn->Execute($sql);
			$a_absen = array();
			while($row = $rs->FetchRow())
				$a_absen[] = $row;
			
			$sql = "select count(tglpresensi) as jumlah, tglpresensi, absensi, p.kodeabsensi
					from ".static::table('pe_presensidet')." p
					left join ".static::table('ms_absensi')." m on m.kodeabsensi=p.kodeabsensi
					where tglpresensi between '$r_datestart' and '$r_date' and p.kodeabsensi in ('$kodeabsensi')
					group by p.kodeabsensi,tglpresensi,absensi";
			$rs = $conn->Execute($sql);
			$a_date = array();
			$a_data = array();
			$a_categori = array();
			while($row = $rs->FetchRow()){
				$a_date[$row['tglpresensi']] = CStr::formatDateInd($row['tglpresensi']);
				$a_data[$row['kodeabsensi']][$row['tglpresensi']] = $row['jumlah'];
			}
			
			$r_mulai = strtotime($r_datestart);
			$r_selesai = strtotime($r_date);
			while($r_mulai <= $r_selesai){
				$r_tanggal = date("Y-m-d", $r_mulai);
				$a_categori[] = CStr::formatDateInd($r_tanggal,false);
				$r_mulai+=86400;
			}

			if (count($a_absen) > 0){
				foreach($a_absen as $row){
					$r_mulai = strtotime($r_datestart);
					$r_selesai = strtotime($r_date);
					$a_detailseries = array();
					while($r_mulai <= $r_selesai){
						$r_tanggal = date("Y-m-d", $r_mulai);
						$a_detailseries[] = empty($a_data[$row['kodeabsensi']][$r_tanggal]) ? 0 : $a_data[$row['kodeabsensi']][$r_tanggal];
						$r_mulai+=86400;
					}	
					$dataseri = implode(",", $a_detailseries);
					$a_series[] = "{name: '$row[absensi]',data : [".$dataseri."]}";
				}
			}
			
			$a_graph = array();
			$a_graph['categori'] = implode("','",$a_categori);
			$a_graph['series'] = implode(",",$a_series);
			
			return array("list" => $a_data, "absen" => $a_absen, 'graph' => $a_graph);
		}
	}
?>
