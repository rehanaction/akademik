<?php
	// model presensi
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mDashboard extends mModel {
		const schema = 'sdm';
		
		function graphJumlahKaryawan($conn){
			//berdasarkan jenis
			$sql = "select idjenispegawai,tipepeg || ' - ' || jenispegawai as nama 
					from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					where idjenispegawai <> 'P2'
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
				
			//hubungan fungsional
			$sql = "select idjfungsional, jabatanfungsional from ".static::table('ms_fungsional')." order by idjfungsional";
			$rs = $conn->Execute($sql);
			$a_fungsional = array();
			while ($row = $rs->FetchRow())
				$a_fungsional[] = $row;
			
			//status aktif
			$sql = "select idstatusaktif, namastatusaktif from ".static::table('lv_statusaktif')." 
					order by idstatusaktif";
			$rs = $conn->Execute($sql);
			$a_statusaktif = array();
			while ($row = $rs->FetchRow())
				$a_statusaktif[] = $row;
				
			$sql = "select idjenispegawai,idhubkerja,idpangkat,idjfungsional,idtipepeg,p.idstatusaktif
					from ".static::table('ms_pegawai')." p
					left join ".static::table('lv_statusaktif')." a on a.idstatusaktif=p.idstatusaktif
					where iskeluar='T' and idjenispegawai <> 'P2'";
			$rs = $conn->Execute($sql);
			$a_sum = array();
			$a_kosong = array();
			while ($row = $rs->FetchRow()){
				$a_sum['tipe'][$row['idtipepeg']]++;
				$a_sum['jenis'][$row['idjenispegawai']]++;
				$a_sum['hubkerja'][$row['idhubkerja']]++;
				$a_sum['pangkat'][$row['idpangkat']]++;
				$a_sum['fungsional'][$row['idjfungsional']]++;
				
				if($row['idtipepeg'] =='')
					$a_kosong['tipekosong']++;
				if($row['idjenispegawai']=='')
					$a_kosong['jeniskosong']++;
				if($row['idhubkerja']=='')
					$a_kosong['hubkerjakosong']++;
				if($row['idpangkat']=='')
					$a_kosong['pangkatkosong']++;
				if($row['idjfungsional']=='' and $row['idtipepeg']=='P')
					$a_kosong['fungsionalkosong']++;
			}
			
			$sqlpendidik = "select idpendidikan 
					from ".static::table('ms_pegawai')." p
					left join ".static::table('lv_statusaktif')." a on a.idstatusaktif=p.idstatusaktif
					where iskeluar='T' and idjenispegawai <> 'P2' and idtipepeg ='P'";
			$rspendidik = $conn->Execute($sqlpendidik);
			$a_sumpendidik = array();
			while ($row = $rspendidik->FetchRow()){
				$a_sumpendidik['jenjangpendidikanpendidik'][$row['idpendidikan']]++;
				if($row['idpendidikan']=='')
					$a_kosong['pendpendidikankosong']++;
			}
			
			$sqlkependidikan = "select idpendidikan 
					from ".static::table('ms_pegawai')." p
					left join ".static::table('lv_statusaktif')." a on a.idstatusaktif=p.idstatusaktif
					where iskeluar='T' and idjenispegawai <> 'P2' and idtipepeg ='K'";
			$rskependidikan = $conn->Execute($sqlkependidikan);
			$a_sumkependidikan = array();
			while ($row = $rskependidikan->FetchRow()){
				$a_sumkependidikan['jenjangpendidikankependidikan'][$row['idpendidikan']]++;
				if($row['idpendidikan']=='')
					$a_kosong['pendkependidikankosong']++;
			}
			
			$sqlmasakerja = "select cast(substring(sdm.get_mkpengabdian(idpegawai),1,2) as integer) as masakerja
					from ".static::table('ms_pegawai')." p
					left join ".static::table('lv_statusaktif')." a on a.idstatusaktif=p.idstatusaktif
					where iskeluar='T' and idjenispegawai <> 'P2'";
			$rsmasakerja = $conn->Execute($sqlmasakerja);
			$a_summasakerja = array();
			while ($row = $rsmasakerja->FetchRow()){
				$a_summasakerja['masakerja'][$row['masakerja']]++;
				if($row['masakerja']=='')
					$a_kosong['mkkosong']++;
			}
			
			//masa kerja
			$a_masakerja = array();
			for( $i =0; $i<=30;$i++){
				$a_masakerja[$i] = $i;
			}
			
			//jenjang pendidikan
			$sql = "select idpendidikan,namapendidikan 
					from ".static::table('lv_jenjangpendidikan')." j
					order by urutan";
			$rs = $conn->Execute($sql);
			$a_jenjangpendidikan = array();
			while ($row = $rs->FetchRow())
				$a_jenjangpendidikan[] = $row;
				
			//jumlah anak
			$sql = "Select coalesce(jmlanak,0) as jmlanak,count(*) as keteranganjumlahanak
					from ".static::table('ms_pegawai')." p
					left join ".static::table('lv_statusaktif')." a on a.idstatusaktif=p.idstatusaktif
					where iskeluar='T' and idjenispegawai <> 'P2'
					GROUP BY coalesce(jmlanak,0) order by coalesce(jmlanak,0)";
			$rs = $conn->Execute($sql);
			$a_jumlahanak = array();
			while ($row = $rs->FetchRow())
			$a_jumlahanak[] = $row;

			//status aktif
			$sqlstatusaktif = "select idstatusaktif
					from ".static::table('ms_pegawai')."
					where idjenispegawai <> 'P2'";
			$rssa = $conn->Execute($sqlstatusaktif);
			while ($rowsa = $rssa->FetchRow()){
				$a_sum['statusaktif'][$rowsa['idstatusaktif']]++;
				
				if($rowsa['idstatusaktif']=='')
					$a_kosong['statusaktifkosong']++;
			}
			
			$a_graph = array();
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
			
			if (count($a_fungsional) > 0){
				$a_fungsionalsum = array();
				foreach($a_fungsional as $row){
					$a_fungsionalsum[$row['idjfungsional']] = ($a_sum['fungsional'][$row['idjfungsional']] == '' ? '0' : $a_sum['fungsional'][$row['idjfungsional']]);
					$a_graph['fungsional'][] = "['".$row['idjfungsional']."', ".($a_sum['fungsional'][$row['idjfungsional']] == '' ? '0' : $a_sum['fungsional'][$row['idjfungsional']])."]";
				}
			}
			if (count($a_jenjangpendidikan) > 0){
				$a_jenjangpendidikanpendidiksum = array();
				foreach($a_jenjangpendidikan as $row){
					$a_jenjangpendidikanpendidiksum[$row['idpendidikan']] = ($a_sumpendidik['jenjangpendidikanpendidik'][$row['idpendidikan']] == '' ? '0' : $a_sumpendidik['jenjangpendidikanpendidik'][$row['idpendidikan']]);
					$a_graph['jenjangpendidikanpendidik'][] = ($a_sumpendidik['jenjangpendidikanpendidik'][$row['idpendidikan']] == '' ? '0' : $a_sumpendidik['jenjangpendidikanpendidik'][$row['idpendidikan']]);
				}
				$a_jenjangpendidikankependidikansum = array();
				foreach($a_jenjangpendidikan as $row){
					 $a_jenjangpendidikankependidikansum[$row['idpendidikan']] = ($a_sumkependidikan['jenjangpendidikankependidikan'][$row['idpendidikan']] == '' ? '0' : $a_sumkependidikan['jenjangpendidikankependidikan'][$row['idpendidikan']]);
					 $a_graph['jenjangpendidikankependidikan'][] = ($a_sumkependidikan['jenjangpendidikankependidikan'][$row['idpendidikan']] == '' ? '0' : $a_sumkependidikan['jenjangpendidikankependidikan'][$row['idpendidikan']]);
				}
			}
			
			if (count($a_jumlahanak) > 0){
	
				foreach($a_jumlahanak as $row){
					$a_graph['jumlahanak'][] = "['".$row['jmlanak']."', ".($row['keteranganjumlahanak'] == '' ? '0' : $row['keteranganjumlahanak'])."]";
				}
			}
			
			if (count($a_masakerja) > 0){
				$a_masakerjasum = array();
				foreach($a_masakerja as $row){
					$a_masakerjasum[$row] = ($a_summasakerja['masakerja'][$row] == '' ? '0' : $a_summasakerja['masakerja'][$row]);
					$a_graph['masakerja'][] = "['".$row."', ".($a_summasakerja['masakerja'][$row] == '' ? '0' : $a_summasakerja['masakerja'][$row])."]";
				}
			}
			
			if (count($a_statusaktif) > 0){
				$a_statusaktifsum = array();
				foreach($a_statusaktif as $row){
					$a_statusaktifsum[$row['idstatusaktif']] = ($a_sum['statusaktif'][$row['idstatusaktif']] == '' ? '0' : $a_sum['statusaktif'][$row['idstatusaktif']]);
					$a_graph['statusaktif'][] = "['".$row['idstatusaktif']."', ".($a_sum['statusaktif'][$row['idstatusaktif']] == '' ? '0' : $a_sum['statusaktif'][$row['idstatusaktif']])."]";
				}
			}
			
			$graph['tipe'] = implode(",",$a_graph['tipe']);
			$graph['jenis'] = implode(",",$a_graph['jenis']);
			$graph['hubungan'] = implode(",",$a_graph['hubkerja']);
			$graph['pangkat'] = implode(",",$a_graph['pangkat']);
			$graph['fungsional'] = implode(",",$a_graph['fungsional']);
			$graph['jenjangpendidikanpendidik'] = implode(",",$a_graph['jenjangpendidikanpendidik']);
			$graph['jenjangpendidikankependidikan'] = implode(",",$a_graph['jenjangpendidikankependidikan']);
			$graph['jumlahanak'] = implode(",",$a_graph['jumlahanak']);
			$graph['masakerja'] = implode(",",$a_graph['masakerja']);
			$graph['statusaktif'] = implode(",",$a_graph['statusaktif']);
			
			return array("kosong" => $a_kosong,"graph" => $graph, "tipepegawai" => $a_tipepegawai, "keterangantipe" => $a_tipe, "jenispegawai" => $a_jenispegawai, "keteranganjenis" => $a_jenis,  "hubungan" => $a_hubkerja, "keteranganhubungan" => $a_hubungan,  "pangkat" => $a_pangkat, "keteranganpangkat" => $a_pangkatsum,  "fungsional" => $a_fungsional, "keteranganfungsional" => $a_fungsionalsum,  "jenjangpendidikan" => $a_jenjangpendidikan, "keteranganjenjangpendidikanpendidik" => $a_jenjangpendidikanpendidiksum, "keteranganjenjangpendidikankependidikan" => $a_jenjangpendidikankependidikansum, "jumlahanak" => $a_jumlahanak, "masakerja" => $a_masakerja, "keteranganmasakerja" => $a_masakerjasum, "statusaktif" => $a_statusaktif, "keteranganstatusaktif" => $a_statusaktifsum);
		}	
		
		function graphAbsenMinggu($conn, $r_date,$r_tipe){
			$r_datestart = date("Y-m-d",strtotime($r_date) - (13*86400));
			
			$sql = "select kodeabsensi, absensi from ".static::table('ms_absensi')." where kodeabsensi in ('D','C','T','A','I','S','HL','PD','H')";
			$rs = $conn->Execute($sql);
			$a_absen = array();
			while($row = $rs->FetchRow())
				$a_absen[] = $row;
			
			$sql = "select count(tglpresensi) as jumlah, tglpresensi, absensi, p.kodeabsensi
					from ".static::table('pe_presensidet')." p
					left join ".static::table('ms_absensi')." m on m.kodeabsensi=p.kodeabsensi
					left join ".static::table('ms_pegawai')." g on g.idpegawai=p.idpegawai
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=g.idtipepeg
					where tglpresensi between '$r_datestart' and '$r_date' and p.kodeabsensi in ('D','C','T','A','I','S','HL','PD','H')
					and g.idtipepeg='$r_tipe'
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