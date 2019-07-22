<?php
	// model semua yang berhubungan riwayat
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPekerjaan extends mModel {
		const schema = 'sdm';
		
		// mendapatkan kueri list pengalaman kerja
		function listQueryPengalamanKerja($r_key) {
			$sql = "select r.*,coalesce(cast(r.masakerjathn as varchar),'0')||' tahun '||coalesce(cast(r.masakerjabln as varchar),'0')||' bulan' as masakerja
					from ".self::table('pe_pengalamankerja')." r 
					where r.idpegawai='$r_key'";
			
			return $sql;
		}
				
		// mendapatkan kueri data pengalaman kerja
		function getDataEditPengalamanKerja($r_key) {
			$sql = "select r.*,coalesce(cast(r.masakerjathn as varchar),'0') as masakerjathn,coalesce(cast(r.masakerjabln as varchar),'0')as masakerjabln
					from ".self::table('pe_pengalamankerja')." r 
					where nourutpk='$r_key'";
			
			return $sql;
		}
		
		function jenisPensiun($conn) {
			$sql = "select idstatusaktif, namastatusaktif from ".static::schema()."lv_statusaktif where iskeluar='Y' order by idstatusaktif";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getDataEditPensiun($r_key) {
			$sql = "select r.*,substring(r.periodepensiun,1,4) as tahun,cast(substring(r.periodepensiun,5,2) as int) as bulan,
					coalesce(cast(r.masakerjathn as varchar),'0') as masakerjathn,coalesce(cast(r.masakerjabln as varchar),'0')as masakerjabln
					from ".self::table('pe_pensiun')." r 
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		function cekPensiun($conn,$r_key){
			$peg = $conn->GetOne("select idpegawai from ".self::table('pe_pensiun')." where idpegawai = $r_key");
			
			return $peg;
		}
		
		function getPensiun($conn,$r_key){
			$a_data = array();
			
			$a_data = $conn->GetRow("select tglpensiun,idtipepeg from ".self::table('ms_pegawai')." where idpegawai = $r_key");
			if(empty($a_data['tglpensiun'])){
				$umurpensiun = $conn->GetOne("select umurpensiun from ".self::table('ms_tipepeg')." where idtipepeg = '".$a_data['idtipepeg']."'");
				$a_data['tglpensiun'] = $conn->GetOne("select dateadd(year,$umurpensiun,tgllahir) as tglpensiun from ".self::table('ms_pegawai')." where idpegawai = $r_key");
			}
			
			$mk = $conn->GetOne("select ".static::schema()."get_mkpensiun($r_key)");
			list($a_data['masakerjathn'],$a_data['masakerjabln']) = explode(':',$mk);
			
			return $a_data;
		}
		
		// mendapatkan kueri list homebase dosen
		function listQueryHomebase($r_key) {
			$sql = "select r.*,u.kodeunit||' - '||u.namaunit as namaunit
					from ".self::table('pe_rwtbasedosen')." r 
					left join ".self::table('ms_unit')." u on u.idunit = r.idunit
					where r.idpegawai='$r_key'";
			
			return $sql;
		}
		
		
		/******************************L A P O R A N***************************************/		
		
		function repPensiun($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$r_jenis){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,j.namastatusaktif
					from ".static::table('pe_pensiun')." r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."lv_statusaktif j on j.idstatusaktif=r.idstatusaktif
					where r.isvalid = 'Y' and r.tmtpensiun between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			
			if(count($r_jenis) > 0){
				$jenis = implode("','",$r_jenis);
				$sql .= " and r.idstatusaktif in ('$jenis')";
			}
			
			$sql .= "order by namapegawai,r.tmtpensiun desc";
			
			$rs = $conn->Execute($sql);
					
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;
		}
						
		function repRiwayatPengalamanKerja($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
						
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit, coalesce(cast(r.masakerjathn as varchar),'0')||' tahun '||coalesce(cast(r.masakerjabln as varchar),'0')||' bulan' as masakerja, t.tipepeg
					from ".static::schema()."pe_pengalamankerja r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg										
					where r.isvalid = 'Y' and r.tglmulai between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." 
					order by namapegawai,r.tglmulai desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}	
						
		function repRiwayatHomebase($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
						
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit, uh.namaunit as unithomebase, t.tipepeg
					from ".static::schema()."pe_rwtbasedosen r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_unit uh on uh.idunit=r.idunit
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg										
					where r.tglmulai between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." 
					order by namapegawai,r.tglmulai desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}	
		
		
		/******************************C R O N   P E N S I U N***************************************/
		//apakah sudah diproses pensiun
		function getCekPensiun($conn,$tmtpensiun){
			$thn = substr($tmtpensiun,0,4);
			$blnd = (int)substr($tmtpensiun,5,2) + 1;
			$bln = str_pad($blnd, 2, "0", STR_PAD_LEFT);
			
			$sql = "select idpegawai from ".self::table('pe_pensiun')." where datepart(year,tmtpensiun) = '$thn' and datepart(month,tmtpensiun) = '$bln'";
			$rsc = $conn->Execute($sql);
			
			return $rsc;
		}
		
		//mendapatkan daftar usulan pegawai pensiun
		function getPegPensiun($conn,$tmtpensiun,$a_nps){
			$thn = substr($tmtpensiun,0,4);
			$blnd = (int)substr($tmtpensiun,5,2) + 1;
			$bln = str_pad($blnd, 2, "0", STR_PAD_LEFT);
						
			$sql = "select idpegawai,tglpensiun as tmtpensiun,".static::schema.".get_mkpensiun(idpegawai) as masakerja
					from ".self::table('ms_pegawai')." 
					where datepart(year,tglpensiun) = '$thn' and datepart(month,tglpensiun) = '$bln'";
			
			if(count($a_nps)>0){
				$anip = implode("','",$a_nps);
				$sql .= " and idpegawai not in ('$anip')";
			}
			
			$rsc = $conn->Execute($sql);
			
			return $rsc;
		}
	}
?>
