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
		
		// mendapatkan kueri list pemberhentian
		function listQueryPemberhentian($r_key) {
			$sql = "select r.*, namastatusaktif as statusaktif,
					coalesce(cast(r.masakerjathn as varchar),'0')||' tahun '||coalesce(cast(r.masakerjabln as varchar),'0')||' bulan' as masakerja
					from ".self::table('pe_pensiun')." r
					left join ".self::table('lv_statusaktif')." l on r.idstatusaktif = l.idstatusaktif
					where r.idpegawai='$r_key'";
			
			return $sql;
		}
		
		function getDataEditPensiun($r_key) {
			$sql = "select r.*,substring(r.periodepensiun,1,4) as tahun,cast(substring(r.periodepensiun,5,2) as int) as bulan,
					coalesce(cast(r.masakerjathn as varchar),'0') as masakerjathn,coalesce(cast(r.masakerjabln as varchar),'0')as masakerjabln
					from ".self::table('pe_pensiun')." r 
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		function jenisPensiun($conn) {
			$sql = "select idstatusaktif, namastatusaktif from ".static::schema()."lv_statusaktif where iskeluar='Y' order by idstatusaktif";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function cekPensiun($conn,$r_key){
			$peg = $conn->GetOne("select idpegawai from ".self::table('pe_pensiun')." where idpegawai = $r_key");
			
			return $peg;
		}
		
		//cek data pemberhentian
		function cekDataPemberhentian($conn,$r_key){
			$peg = $conn->GetOne("select 1 from ".self::table('pe_pensiun')." where idpegawai = $r_key");
			
			return $peg;
		}
		
		function getPensiun($conn,$r_key){
			$a_data = array();
			
			$a_data = $conn->GetRow("select tglpensiun,idtipepeg from ".self::table('ms_pegawai')." where idpegawai = $r_key");
			if(empty($a_data['tglpensiun'])){
				$umurpensiun = $conn->GetOne("select umurpensiun from ".self::table('ms_tipepeg')." where idtipepeg = '".$a_data['idtipepeg']."'");
				$a_data['tglpensiun'] = $conn->GetOne("select tgllahir + cast('$umurpensiun years' as interval) as tglpensiun from ".self::table('ms_pegawai')." where idpegawai = $r_key");
			}
			
			$mk = $conn->GetOne("select ".static::schema()."get_mkpensiun($r_key)");
			list($a_data['masakerjathn'],$a_data['masakerjabln']) = explode(':',$mk);
			
			return $a_data;
		}
						
		// mendapatkan kueri list berobat
		function listQueryBerobat($r_key) {
			$sql = "select r.*,
					case when r.status = 'K' then 'Karyawan/ti' 
						when r.status = 'I' then 'Istri/Suami' 
						when r.status = 'A' then 'Anak' 
						else '' end as statusob
					from ".self::table('pe_rwtberobat')." r 
					where r.idpegawai='$r_key'";
			
			return $sql;
		}
				
		// mendapatkan kueri data berobat
		function getDataEditBerobat($r_key) {
			$sql = "select r.*,case when r.nosurat is null then '<i>Digenerate otomatis</i>' else r.nosurat end as nosurat,
					case when r.status = 'I' then t.namapasangan
						 when r.status = 'A' then a.namaanak
					end as nama
					from ".self::table('pe_rwtberobat')." r
					left join ".self::table('pe_istrisuami')." t on r.nourutkeluarga = t.nourutist
					left join ".self::table('pe_anak')." a on r.nourutkeluarga = a.nourutanak
					where nourutberobat='$r_key'";
			
			return $sql;
		}
		
		//untuk mendapatkan  permohonan berobat
		function getDataEditPermohonanBerobat($r_subkey) {
			$sql = "select r.*,p.nik, ".static::schema.".f_namalengkap(p.gelardepan, p.namadepan, p.namatengah, p.namabelakang, p.gelarbelakang) as namalengkap,
					case when r.status = 'I' then t.namapasangan
						 when r.status = 'A' then a.namaanak
					end as nama
					from ".self::table('pe_rwtberobat')." r
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".self::table('pe_istrisuami')." t on r.nourutkeluarga = t.nourutist
					left join ".self::table('pe_anak')." a on r.nourutkeluarga = a.nourutanak
					where nourutberobat ='$r_subkey'";
			
			return $sql;
		}
		
		//status persetujuan berobat
		function statusSetujuiBerobat(){
			$status = array( 'Y' => 'Disetujui', 'T' => 'Ditolak');
			
			return $status;
		}
		//status persetujuan klinik
		function statusSetujuiKlinik(){
			$status = array( 'S' => 'Selesai', 'R' => 'Rujuk');
			
			return $status;
		}
		
		//pop Istri Suami berobat
		function getIstriSuami($conn,$r_key){			
			$sql = "select r.*, namapasangan, jeniskelamin, tmplahir, tgllahir
					from ".self::table('pe_istrisuami')." r 
					where r.idpegawai='$r_key' and isvalid='Y' and istanggungberobat='Y'";
			$rs = $conn->Execute($sql);
			
			return $rs;
		}
		
		//pop Anak berobat
		function getAnak($conn,$r_key){			
			$sql = "select a.*, namaanak, anakke, tmplahir, tgllahir,jeniskelamin
					from ".self::table('pe_anak')." a 
					where a.idpegawai='$r_key' and isvalid='Y' and istanggungberobat='Y'";
			$rs = $conn->Execute($sql);
			
			return $rs;
		}
		
		function jenisStatus($conn){
			$a_tberobat = array('K'=>'Karyawan/ti','I'=>'Istri/Suami','A'=>'Anak');
			
			return $a_tberobat;
		}
		
		function listPersetujuanBerobat() {
			$sql = "select r.*,p.nik, sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					case when r.status = 'K' then 'Karyawan/ti' 
						when r.status = 'I' then 'Istri/Suami' 
						when r.status = 'A' then 'Anak' 
						else '' end as statusob
					from ".self::table('pe_rwtberobat')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai=r.idpegawai
					left join ".self::table('ms_unit')." u on u.idunit=p.idunit
					where (r.isvalid is not null or r.isvalid = 'T')";
			
			return $sql;
		}		
		
		//mendapatkan nourut berobat		
		function getNoSuratBerobat($conn,$r_subkey,$r_tglpengajuan){
			$thn = substr($r_tglpengajuan,0,4);
			$bln = substr($r_tglpengajuan,5,2);
			
			$a_romawi = SDM::aRomawiSurat();
			
			if(!empty($r_subkey))
				$isvalid = $conn->GetOne("select isvalid from ".self::table('pe_rwtberobat')." where nourutberobat = $r_subkey");
			
			if(empty($r_subkey) or $isvalid != 'Y'){
				$sql = "select max(coalesce(substring(nosurat,1,4)::int,0)::int)+1 as maks
						from ".self::table('pe_rwtberobat')." 
						where date_part('year',tglpengajuan)=date_part('year','$r_tglpengajuan'::timestamp)";
				
				$nourut =  $conn->GetOne($sql);
				
				if (empty($nourut))
					$kode ='0001/PK/P/'.$a_romawi[(int)$bln].'/'.$thn;
				else
					$kode = str_pad($nourut,'4','0', STR_PAD_LEFT).'/PK/P/'.$a_romawi[(int)$bln].'/'.$thn;
				
				return $kode;
			}else
				return 'null';
		}
		
		//cek pengobatan purna maks 2 thn
 		function cekPurna($conn,$r_key){
			$sql = "select date_part('year',age(now()::date, tmtpensiun::timestamp)) as tahun 
					from ".static::schema()."pe_pensiun where idpegawai=$r_key order by tmtpensiun limit 1";
			$rc = $conn->GetOne($sql);

			if (empty($rc)){
				$sql = "select date_part('year',age(now()::date, tglpensiun::timestamp)) as tahun 
					from ".static::schema()."ms_pegawai where idpegawai=$r_key";
				$rc = $conn->GetOne($sql);
			}
			
			if ($rc > 2)
				$cekpensiun = 0;
			else
				$cekpensiun = 1;
				
			
			return $cekpensiun;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			if($col == 'unit') {
				global $conn, $conf;
				require_once($conf['gate_dir'].'model/m_unit.php');
				
				$row = mUnit::getData($conn,$key);
				
				return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
			if($col == 'bulan')
				return "substring(cast(cast(r.tglpengajuan as date) as varchar),6,2) = '$key'";
			if($col == 'tahun')
				return "substring(cast(cast(r.tglpengajuan as date) as varchar),1,4) = '$key'";
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
