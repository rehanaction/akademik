<?php
	// model semua yang berhubungan riwayat
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKenaikan extends mModel {
		const schema = 'sdm';
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			if($col == 'unit') {
				global $conn, $conf;
				require_once($conf['gate_dir'].'model/m_unit.php');
				
				$row = mUnit::getData($conn,$key);
				
				return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
			if($col == 'bulan')
				return "substring(cast(cast(r.tglkpb as date) as varchar),6,2) = '$key'";
			if($col == 'tahun')
				return "substring(cast(cast(r.tglkpb as date) as varchar),1,4) = '$key'";
			if($col == 'bulankgb')
				return "substring(cast(cast(r.tglkgb as date) as varchar),6,2) = '$key'";
			if($col == 'tahunkgb')
				return "substring(cast(cast(r.tglkgb as date) as varchar),1,4) = '$key'";
		}
		
		// mendapatkan tahun rekap presensi pegawai ybs
		function getPeriodeKPB($conn){
			$sql = "select tglkpb from ".self::table('pe_kpb')." group by tglkpb order by tglkpb";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()){
				$data[$row['tglkpb']] = Cstr::formatDateInd($row['tglkpb']);
			}
			
			return $data;
		}
		
		// mendapatkan kueri list riwayat kpb
		function listQueryKPB() {
			$sql = "select p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					r.*,substring(right(replicate('0', 4) + cast(r.mkglama as varchar), 4),1,2)+' tahun '+substring(right(replicate('0', 4) + cast(r.mkglama as varchar), 4),3,2)+' bulan' as mklama,
					substring(right(replicate('0', 4) + cast(r.mkg as varchar), 4),1,2)+' tahun '+substring(right(replicate('0', 4) + cast(r.mkg as varchar), 4),3,2)+' bulan' as mkbaru,
					pl.golongan as pangkatlama, pb.golongan as pangkatbaru
					from ".self::table('pe_kpb')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					left join ".self::table('ms_pangkat')." pl on pl.idpangkat=r.idpangkatlama
					left join ".self::table('ms_pangkat')." pb on pb.idpangkat=r.idpangkat";
			
			if(Modul::getRole() == 'Jab'){ //bila atasan
				$sql .= " where p.emailatasan = '".Modul::getUserEmail()."'";
			}
			
			return $sql;
		}
		
		//mendapatkan data naik pangkat
		function getDataKPB($r_key){
			$sql = "select p.idpegawai,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as pegawai,
					r.*,substring(right(replicate('0', 4) + cast(r.mkglama as varchar), 4),1,2)+' tahun '+substring(right(replicate('0', 4) + cast(r.mkglama as varchar), 4),3,2)+' bulan' as mklama,
					substring(right(replicate('0', 4) + cast(r.mkg as varchar), 4),1,2) as mkthn, substring(right(replicate('0', 4) + cast(r.mkg as varchar), 4),3,2) as mkbln,
					pl.golongan as pangkatlama,u.kodeunit+' - '+u.namaunit as namaunit
					from ".self::table('pe_kpb')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					left join ".self::table('ms_pangkat')." pl on pl.idpangkat=r.idpangkatlama
					where r.nokpb = $r_key";
			
			return $sql;
		}
		
		// mendapatkan kueri list riwayat kgb
		function listQueryKGB() {
			$sql = "select p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					r.*,substring(right(replicate('0', 4) + cast(r.mkglama as varchar), 4),1,2)+' tahun '+substring(right(replicate('0', 4) + cast(r.mkglama as varchar), 4),3,2)+' bulan' as mklama,
					substring(right(replicate('0', 4) + cast(r.mkgbaru as varchar), 4),1,2)+' tahun '+substring(right(replicate('0', 4) + cast(r.mkgbaru as varchar), 4),3,2)+' bulan' as mkbaru,
					pl.golongan as golonganlama, pb.golongan as golonganbaru
					from ".self::table('pe_kgb')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					left join ".self::table('ms_pangkat')." pl on pl.idpangkat=r.pangkatlama
					left join ".self::table('ms_pangkat')." pb on pb.idpangkat=r.pangkatbaru";
			
			if(Modul::getRole() == 'Jab'){ //bila atasan
				$sql .= " where p.emailatasan = '".Modul::getUserEmail()."'";
			}
			
			return $sql;
		}
		
		//mendapatkan data kgb
		function getDataKGB($r_key){
			$sql = "select p.idpegawai,p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as pegawai,
					r.*,substring(right(replicate('0', 4) + cast(r.mkglama as varchar), 4),1,2)+' tahun '+substring(right(replicate('0', 4) + cast(r.mkglama as varchar), 4),3,2)+' bulan' as mklama,
					substring(right(replicate('0', 4) + cast(r.mkgbaru as varchar), 4),1,2) as mkthn, substring(right(replicate('0', 4) + cast(r.mkgbaru as varchar), 4),3,2) as mkbln,
					pl.golongan as golonganlama,u.kodeunit+' - '+u.namaunit as namaunit
					from ".self::table('pe_kgb')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					left join ".self::table('ms_pangkat')." pl on pl.idpangkat=r.pangkatlama
					where r.nokgb = $r_key";
			
			return $sql;
		}
		
		//cek kenaikan pangkat
		function isNaikPangkat($conn,$idpegawai,$tglkpb){
			$tglkpb = Cstr::formatDate($tglkpb);
			$bln = substr($tglkpb,5,2);
			$isnaik = true;
			//cek apakah pegawai sudah diproses naik pangkat
			$kpb = $conn->GetOne("select idpegawai from ".self::table('pe_kpb')." 
					where idpegawai = $idpegawai and datepart(month,tglkpb)='$bln'");
					
		
			if(!empty($kpb))
				$isnaik = false;
				
			//mendapatkan next pangkat
			$rown = mKenaikan::getNextPangkat($conn,$idpegawai,$tglkpb);
			
			//cek untuk status pegawai yang naik pangkat
			$sql = "select p.idpegawai,coalesce(p.nik+' - ','')+".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as pegawai,
					u.kodeunit+' - '+u.namaunit as namaunit,pl.golongan as pangkatlama,p.tmtpangkat as tmtpangkatlama,cast(p.masakerjathngol as varchar)+' tahun '+cast(p.masakerjablngol as varchar)+' bulan' as mklama,
					'".$rown['idpangkat']."' as idpangkat,'".$rown['tmtpangkat']."' as tmtpangkat,substring(right(replicate('0', 4) + cast(".$rown['mkg']." as varchar), 4),1,2) as mkthn, substring(right(replicate('0', 4) + cast(".$rown['mkg']." as varchar), 4),3,2) as mkbln
					from ".self::table('ms_pegawai')." p
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					left join ".self::table('ms_pangkat')." pl on pl.idpangkat=p.idpangkat
					left join ".self::table('pe_kpb')." k on k.idpegawai=p.idpegawai and k.tglkpb = '$tglkpb'
					where p.idpegawai = $idpegawai and ".static::schema.".get_nextpangkat(p.idpangkat) is not null
					and p.idstatusaktif = 'AA' and p.idtipepeg != 'D'";	
			$row = $conn->GetRow($sql);	
						
			if(empty($row))
				$isnaik = false;
				
			$a_data = array('kpb' => $kpb, 'row' => $row, 'isnaik' => $isnaik, 'sql' => $sql);
			
			return $a_data;
		}
		
		//cek kenaikan gaji berkala
		function isNaikGaji($conn,$idpegawai,$tglkgb){
			$tglkgb = Cstr::formatDate($tglkgb);
			$bln = substr($tglkgb,5,2);
			$isnaik = true;
			//cek apakah pegawai sudah diproses naik pangkat
			$kgb = $conn->GetOne("select idpegawai from ".self::table('pe_kgb')." 
					where idpegawai = $idpegawai and datepart(month,tglkgb)='$bln'");
					
		
			if(!empty($kgb))
				$isnaik = false;
				
			//mendapatkan next pangkat
			$rown = mKenaikan::getNaikGaji($conn,$idpegawai,$tglkgb);
			
			//cek untuk status pegawai yang naik pangkat
			$sql = "select p.idpegawai,coalesce(p.nik+' - ','')+".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as pegawai,
					u.kodeunit+' - '+u.namaunit as namaunit,pl.golongan as golonganlama,p.tmtpangkat as tmtpangkatlama,cast(p.masakerjathngol as varchar)+' tahun '+cast(p.masakerjablngol as varchar)+' bulan' as mklama,
					'".$rown['idpangkat']."' as pangkatbaru,'".$rown['tmtpangkat']."' as tmtpangkat,substring(right(replicate('0', 4) + cast(".$rown['mkg']." as varchar), 4),1,2) as mkthn, substring(right(replicate('0', 4) + cast(".$rown['mkg']." as varchar), 4),3,2) as mkbln
					from ".self::table('ms_pegawai')." p
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					left join ".self::table('ms_pangkat')." pl on pl.idpangkat=p.idpangkat
					left join ".self::table('pe_kgb')." k on k.idpegawai=p.idpegawai and k.tglkgb = '$tglkgb'
					where p.idpegawai = $idpegawai and p.idstatusaktif = 'AA'";
			$row = $conn->GetRow($sql);	
						
			if(empty($row))
				$isnaik = false;
				
			$a_data = array('kgb' => $kgb, 'row' => $rown, 'isnaik' => $isnaik, 'sql' => $sql);
			
			return $a_data;
		}
		
		function getPKTLama($conn,$idpegawai){
			$row = $conn->GetROw("select idpangkat as idpangkatlama,tmtpangkat as tmtpangkatlama,
					right(replicate('0', 2) + cast(masakerjathngol as varchar), 2)+right(replicate('0', 2) + cast(masakerjablngol as varchar), 2) as mkglama
					from ".self::table('ms_pegawai')." 
					where idpegawai = $idpegawai");
			
			return $row;
		}
		
		function getNextPangkat($conn,$idpegawai,$tglkpb){
			$pktlama = mKenaikan::getPKTLama($conn,$idpegawai);
			$thn = (int) substr($pktlama['mkglama'],0,2);
			$bln = (int) substr($pktlama['mkglama'],2,2);
			
			//$r_thn = $conn->GetOne("select convert(varchar,(select datediff(month,case when day('".$pktlama['tmtpangkatlama']."') > day('$tglkpb') then dateadd(month,1,'".$pktlama['tmtpangkatlama']."') else '".$pktlama['tmtpangkatlama']."' end,'$tglkpb') / 12 ))");
			//$r_bln = $conn->GetOne("select convert(varchar,(select datediff(month,case when day('".$pktlama['tmtpangkatlama']."') > day('$tglkpb') then dateadd(month,1,'".$pktlama['tmtpangkatlama']."') else '".$pktlama['tmtpangkatlama']."' end,'$tglkpb') % 12 ))");
			
			$r_thn = 4;
			$r_bln = 0;
			
			$blnbaru = $bln - (int) $r_bln;
			$thnbaru = $thn - (int) $r_thn;
			if($thnbaru < 1)
				$thnbaru = 0;
				
			if($blnbaru > 12){
				$thnbaru += 1;
				$blnbaru = $blnbaru % 12;
			}
			
			$row = array();
			$row['idpangkat'] = $conn->GetOne("select ".static::schema.".get_nextpangkat('".$pktlama['idpangkatlama']."')");
			$row['tmtpangkat'] = $tglkpb;
			$row['mkg'] = str_pad($thnbaru, 2, "0", STR_PAD_LEFT).str_pad($blnbaru, 2, "0", STR_PAD_LEFT);
			
			return $row;
		}
		
		function getNaikGaji($conn,$idpegawai,$tglkgb){
			require_once(Route::getModelPath('gaji'));
			
			$pktlama = mKenaikan::getPKTLama($conn,$idpegawai);
			$thn = (int) substr($pktlama['mkglama'],0,2);
			$bln = (int) substr($pktlama['mkglama'],2,2);
			
			$r_thn = $conn->GetOne("select convert(varchar,(select datediff(month,case when day('".$pktlama['tmtpangkatlama']."') > day('$tglkgb') then dateadd(month,1,'".$pktlama['tmtpangkatlama']."') else '".$pktlama['tmtpangkatlama']."' end,'$tglkgb') / 12 ))");
			$r_bln = $conn->GetOne("select convert(varchar,(select datediff(month,case when day('".$pktlama['tmtpangkatlama']."') > day('$tglkgb') then dateadd(month,1,'".$pktlama['tmtpangkatlama']."') else '".$pktlama['tmtpangkatlama']."' end,'$tglkgb') % 12 ))");
						
			$blnbaru = $bln + (int) $r_bln;
			$thnbaru = $thn + (int) $r_thn;
				
			if($blnbaru > 12){
				$thnbaru += 1;
				$blnbaru = $blnbaru % 12;
			}
			
			$gapoklama = mGaji::getGajiPokok($conn,$pktlama['idpangkatlama'],$thn);
			$gapokbaru = mGaji::getGajiPokok($conn,$pktlama['idpangkatlama'],$thnbaru);
			if($gapoklama < $gapokbaru){ //jika ada perubahan gaji pokok
				$row = array();
				$row['idpangkat'] = $pktlama['idpangkatlama'];
				$row['tmtpangkat'] = $tglkgb;
				$row['mkg'] = str_pad($thnbaru, 2, "0", STR_PAD_LEFT).str_pad($blnbaru, 2, "0", STR_PAD_LEFT);
			
				return $row;
			}
		}
		
		function isSetuju(){
			$a_data = array('Y' => 'Disetujui', 'T' => 'Ditangguhkan');
			
			return $a_data;
		}
		
		/***************************************************C R O N   K E N A I K A N******************************************/
		function getCekNaikPangkat($conn,$tglkpb){
			$bln = substr($tglkpb,5,2);
			$sql = "select idpegawai from ".self::table('pe_kpb')." where datepart(month,tglkpb)='$bln'";
			$rsc = $conn->Execute($sql);
			
			return $rsc;
		}
		
		function getCekNaikGaji($conn,$tglkgb){
			$bln = substr($tglkgb,5,2);
			$sql = "select idpegawai from ".self::table('pe_kgb')." where datepart(month,tglkgb)='$bln'";
			$rsc = $conn->Execute($sql);
			
			return $rsc;
		}
		
		function getPegNaikPangkat($conn,$tglkpb,$a_nkpb){
			$sql = "select idpegawai,idpangkat,tmtpangkat,masakerjathngol,masakerjablngol 
					from ".self::table('ms_pegawai')." 
					where ".static::schema.".get_nextpangkat(idpangkat) is not null and idstatusaktif = 'AA' and idtipepeg != 'D'
					and convert(varchar,(select datediff(month,case when day(tmtpangkat) > day('$tglkpb') then dateadd(month,1,tmtpangkat) else tmtpangkat end,'$tglkpb') / 12 )) = '4'";
			
			if(count($a_nkpb)>0){
				$anip = implode("','",$a_nkpb);
				$sql .= " and idpegawai not in ('$anip')";
			}
			
			$rs = $conn->Execute($sql);
			
			return $rs;
		}
				
		function getPegNaikGaji($conn,$a_nkgb){
			$sql = "select idpegawai,idpangkat as idpangkatlama,tmtpangkat as tmtpangkatlama,
					right(replicate('0', 2) + cast(masakerjathngol as varchar), 2)+right(replicate('0', 2) + cast(masakerjablngol as varchar), 2) as mkglama 
					from ".self::table('ms_pegawai')." 
					where idstatusaktif = 'AA'";
			
			if(count($a_nkgb)>0){
				$anip = implode("','",$a_nkgb);
				$sql .= " and idpegawai not in ('$anip')";
			}
			
			$rs = $conn->Execute($sql);
			
			return $rs;
		}
		
		/*********************************************************L A P O R A N************************************************/
		
		function getNaikPangkat($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select p.nik,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					r.*,substring(right(replicate('0', 4) + cast(r.mkglama as varchar), 4),1,2)+' tahun '+substring(right(replicate('0', 4) + cast(r.mkglama as varchar), 4),3,2)+' bulan' as mklama,
					substring(right(replicate('0', 4) + cast(r.mkg as varchar), 4),1,2)+' tahun '+substring(right(replicate('0', 4) + cast(r.mkg as varchar), 4),3,2)+' bulan' as mkbaru,
					pl.golongan as pangkatlama, pb.golongan as pangkatbaru,u.namaunit
					from ".self::table('pe_kpb')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					left join ".self::table('ms_pangkat')." pl on pl.idpangkat=r.idpangkatlama
					left join ".self::table('ms_pangkat')." pb on pb.idpangkat=r.idpangkat
					where u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." and r.tglkpb between '$r_tglmulai' and '$r_tglselesai'";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;	
		}		 
	}
?>
