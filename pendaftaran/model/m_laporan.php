<?php
	// model laporan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
class mLaporan {

		function makeRowData($conn, $sql){
			$rs_data = $conn->Execute($sql);			
			
			$data = array();
			while ($row = $rs_data->fetchRow()){
				$data[] = $row;
				}
			return $data;
		}
	
		
		function getABHPruang($conn, $periode, $jalur, $gelombang, $ruang){
			$sql="SELECT * FROM pendaftaran.pd_pendaftar WHERE periodedaftar='$periode' AND jalurpenerimaan='$jalur' AND idgelombang='$gelombang' AND lokasiujian='$ruang'";
			
			return $conn->Execute($sql);
		}
		function getDataPeserta($conn,$kodeunit,$periode,$jalur,$gelombang,$nopendaftar,$lulusujian = false,$tagihan=true){
			$sql = "select p.*,u.namaunit, f.namaunit as namaunitfakultas, k.namakota from pendaftaran.pd_pendaftar p 
						join gate.ms_unit u on u.kodeunit = p.pilihanditerima 
						join gate.ms_unit f on f.kodeunit = u.kodeunitparent 
						join akademik.ms_kota k on k.kodekota = p.kodekota						
						where 1=1 and lulusujian = -1 ";
			if (!empty($kodeunit))
				$sql.=" AND pilihanditerima = '$kodeunit'";
			if (!empty($periode))
				$sql.=" AND periodedaftar = '$periode'";
			if (!empty($jalur))
				$sql.=" AND jalurpenerimaan = '$jalur'";
			if (!empty($gelombang))
				$sql.=" AND idgelombang = '$gelombang'";
			if (!empty($nopendaftar))
				$sql.=" AND nopendaftar = '$nopendaftar'";
			if ($lulusujian)
				$sql.=" AND lulusujian = -1";
				
				$sql.=" order by nopendaftar";
			$rs =  $conn->Execute($sql);
			
			if ($tagihan){
			$sqltagihan = "select * from h2h.ke_tagihan where periode = '$periode'  and jenistagihan = 'REG'";
			$rs1 = $conn->Execute($sqltagihan);
			$datakeuangan = array();

		}
			if ($tagihan)
			while ($row1 = $rs1->fetchRow()){				
				$datakeuangan[$row1['nopendaftar']][] = array('nopendaftar'=>$row1['nopendaftar'],'jenistagihan'=>$row1['jenistagihan'],'angsuranke'=>$row1['angsuranke'], 
																			  'nominaltagihan'=>$row1['nominaltagihan'],
																			  'tanggaldeadline'=>$row1['tgldeadline']
																			  );		
			}
			$data = array();
			while($row = $rs->fetchRow()){
				if ($tagihan)
				$row['keuangan'] = $datakeuangan[$row['nopendaftar']];
				
				$data[] = $row;
			} 
			return $data;
			
		}
		
	function getPendaftarbelumdaftarulang($conn, $periode, $kodeunit){
		
		if (!empty($kodeunit))
		$u = $conn->getRow("select infoleft, inforight from gate.ms_unit where kodeunit = '$kodeunit'");
		
			// get data pendaftar;
			$sql = "select p.nopendaftar, p.nama, p.hp, p.email, u.namaunit, u1.namaunit as namaunitpilihan1, p.isfollowup,p.keterangan, s.namasistem from pendaftaran.pd_pendaftar p 
					left join gate.ms_unit u on u.kodeunit=p.pilihanditerima 
					left join gate.ms_unit u1 on u1.kodeunit=p.pilihan1 
					left join akademik.ak_sistem s on s.sistemkuliah=p.sistemkuliah
					where 1=1";
			if (!empty($periode))
				$sql.=" and periodedaftar = '$periode'";
			
			if (!empty($kodeunit)){
				$sql.=" and u.infoleft >= '".$u['infoleft']."' and u.inforight <= '".$u['inforight']."' ";
				}
			$sql.=" and coalesce(isdaftarulang,0) <> -1 and nimpendaftar is null ";
			
			$rs_data = $conn->Execute($sql);			
			
			$data = array();
			while ($row = $rs_data->fetchRow()){
				$data[] = $row;
				}
				
			return $data;
			
			}
	function getPendaftarsudahdaftarulang($conn, $periode, $kodeunit,$tglawal,$tglakhir){
		
		if (!empty($kodeunit))
		$u = $conn->getRow("select infoleft, inforight from gate.ms_unit where kodeunit = '$kodeunit'");
		
			// get data pendaftar;
			$sql = "select nopendaftar from pendaftaran.pd_pendaftar p left join gate.ms_unit u on u.kodeunit=p.pilihanditerima where 1=1";
			if (!empty($periode))
				$sql.=" and periodedaftar = '$periode'";
			
			if (!empty($kodeunit)){
				$sql.=" and u.infoleft >= '".$u['infoleft']."' and u.inforight <= '".$u['inforight']."' ";
				}
			
			//create temp_table with data pendaftar on condition (flaglunas(L) > 0 && flaglunas(<>L) > 0) 
			$sql_tagihan = "
							DROP TABLE IF EXISTS temp_pendaftar;
							
							select nopendaftar into temp temp_pendaftar
							from h2h.ke_tagihan where nopendaftar in ($sql) ";
			if (!empty($tglawal) and !empty($tglakhir)){
				$tglawal = date::indoDateYmd($tglawal);
				$tglakhir = date::indoDateYmd($tglakhir);
				
				$sql_tagihan .=" and tgltagihan between '$tglawal' and '$tglakhir'"; 
				
			}
			else if (empty($tglakhir) and !empty($tglawal)){
				$tglawal = date::indoDateYmd($tglawal);
				
				$sql_tagihan .=" and tgltagihan = '$tglawal'"; 
				
			}
					
			$sql_tagihan .=" group by nopendaftar
							 having count(case when flaglunas ='L' then 1 else null end ) > 0 and count(case when flaglunas <> 'L' then 1 else null end ) > 0 ";
			$rs = $conn->execute($sql_tagihan);
			
			//get data pendaftar, join with table pd_pendaftar n unit n tagihan
			$sql_data = "select p.nopendaftar, p.nimpendaftar, p.nama, p.hp, p.email,tg.isfollowup, tg.keterangan, u.namaunit, u1.namaunit as namaunitpilihan1, tg.jenistagihan, tg.tgltagihan, tg.nominaltagihan, s.namasistem from temp_pendaftar t 
							join pendaftaran.pd_pendaftar p on p.nopendaftar=t.nopendaftar 
							left join h2h.ke_tagihan tg on tg.nopendaftar=p.nopendaftar 
							left join akademik.ak_sistem s on s.sistemkuliah=p.sistemkuliah
							join gate.ms_unit u on u.kodeunit=p.pilihanditerima 
							join gate.ms_unit u1 on u1.kodeunit=p.pilihan1 
							where tg.flaglunas <> 'L' ";
			if (!empty($tglawal) and !empty($tglakhir)){				
				$sql_data .=" and tg.tgltagihan between '$tglawal' and '$tglakhir'"; 
				
			}
			else if (empty($tglakhir) and !empty($tglawal)){
				
				$sql_data .=" and tg.tgltagihan = '$tglawal'"; 
				
			}else if (!empty($tglakhir) and empty($tglawal))
				$sql_data .=" and tg.tgltagihan <= '$tglakhir'"; 
			
			$sql_data.="  order by p.nimpendaftar, tg.tgltagihan ";
			
			$rs_data = $conn->Execute($sql_data);
			
			
			$data = array();
			while ($row = $rs_data->fetchRow()){
				$data[] = $row;
				}
				
			return $data;
			
			}
	function getDataPerbandinganMahasiswa($conn,$r_tglawal,$r_tglakhir){
		$sql = "select kodeunit, substring (periodemasuk,1,4) as periodemasuk, count(*) as jumlah from akademik.ms_mahasiswa m left join pendaftaran.pd_pendaftar p on p.nimpendaftar = m.nim where 1=1 ";
		if (!empty($r_tglawal) and empty ($r_tglakhir)){
			$r_tglawal = date::indoDateYmd($r_tglawal);	
			$sql.=" and tgldaftarulang = '$r_tglawal'";
		}	
		if (!empty($r_tglawal) and !empty ($r_tglakhir)){
			$r_tglawal = date::indoDateYmd($r_tglawal);	
			$r_tglakhir = date::indoDateYmd($r_tglakhir);	
			$inDate = date::getInRange($r_tglawal,$r_tglakhir);
			
			$sql.=" and tgldaftarulang in $inDate";
			
		}
			
			$sql.=" group by kodeunit, substring (periodemasuk,1,4)";
			
		return  self::makeRowData($conn, $sql);
		
	}
		
	function getDataPerbandinganPendaftar($conn,$periode,$tglawal,$tglakhir){
		$sql = "select p.pilihanditerima, substring (p.periodedaftar,1,4) as periodedaftar, count(*) as jumlah 
				from pendaftaran.pd_pendaftar p 
				join h2h.ke_pembayaranfrm f on p.tokenpendaftaran=f.notoken
				join h2h.ke_tariffrm t on t.idtariffrm = f.idtariffrm where 1=1 ";
		if (!empty ($periode))
		$sql.=" and p.periodedaftar = '$periode'";

		if (!empty($tglawal) and !empty($tglakhir)){
			$r_tglawal = date::indoDateYmd($tglawal);
			$r_tglakhir = date::indoDateYmd($tglakhir);
			$inDate = date::getInRange($r_tglawal,$r_tglakhir);
			 
			$sql.=" and p.tglregistrasi in $inDate";
		}
		
		$sql.=" group by p.pilihanditerima, substring (p.periodedaftar,1,4)";
		
		return  self::makeRowData($conn, $sql);
		
	}
	function getDataPerbandinganPesertaUSM($conn,$periode,$tglawal,$tglakhir){
		$sql = "select p.pilihan1, substring (p.periodedaftar,1,4) as periodedaftar, count(*) as jumlah 
				from pendaftaran.pd_pendaftar p 
				join h2h.ke_pembayaranfrm f on p.tokenpendaftaran=f.notoken
				join h2h.ke_tariffrm t on t.idtariffrm = f.idtariffrm where 1=1 ";
		if (!empty ($periode))
		$sql.=" and p.periodedaftar = '$periode'";

		if (!empty($tglawal) and !empty($tglakhir)){
			$r_tglawal = date::indoDateYmd($tglawal);
			$r_tglakhir = date::indoDateYmd($tglakhir);
			$inDate = date::getInRange($r_tglawal,$r_tglakhir);
			 
			$sql.=" and p.tglregistrasi in $inDate";
		}
		
		$sql.=" group by p.pilihan1, substring (p.periodedaftar,1,4)";
		
		return  self::makeRowData($conn, $sql);
		
	}
	function getDataPenjualannopendaftar($conn,$periode='',$sistemkuliah='',$tglawal,$tglakhir){
		
		$sql=" select p.nopendaftar, p.nama, p.sistemkuliah, t.nominaltarif from pendaftaran.pd_pendaftar p 
			   join h2h.ke_pembayaranfrm f on f.notoken=p.tokenpendaftaran 
			   join h2h.ke_tariffrm t on t.idtariffrm =f.idtariffrm where 1=1";
		if (!empty ($periode))
		$sql.=" and p.periodedaftar = '$periode'";
		if (!empty ($sistemkuliah))
		$sql.=" and p.sistemkuliah = '$sistemkuliah'";
		if (!empty ($tglawal) and !empty($tglakhir)){
		$tglawal = date::indoDateYmd($tglawal);			
		$tglakhir = date::indoDateYmd($tglakhir);			
		$sql.=" and p.tglregistrasi between '$tglawal' and '$tglakhir' ";

		}
		
		return  self::makeRowData($conn, $sql);
		
	}
	
	function getDataSPDU($conn, $nopendaftarmulai,$nopendaftarakhir){
		$sql = "select p.nopendaftar, p.nama, p.nimpendaftar, p.sistemkuliah,p.periodedaftar,  u1.namaunit, u2.namaunit as fakultas, s.namasistem 
					from pendaftaran.pd_pendaftar p 
					join gate.ms_unit u1 on u1.kodeunit=p.pilihanditerima
					join gate.ms_unit u2 on u2.kodeunit = u1.kodeunitparent 
					join akademik.ak_sistem s on s.sistemkuliah=p.sistemkuliah
					where 1=1";
		if (!empty ($nopendaftarmulai) and empty ($nopendaftarakhir))
		$sql .=" and nopendaftar = '$nopendaftarmulai'";
		if (!empty ($nopendaftarmulai) and !empty($nopendaftarakhir))
		$sql .=" and nopendaftar between '$nopendaftarmulai' and '$nopendaftarakhir'";
		if (!empty ($nopendaftarakhir) and empty ($nopendaftarmulai))
		$sql .=" and nopendaftar = '$nopendaftarakhir'";
		
		$sql.=" order by nopendaftar ";
		
		
		return  self::makeRowData($conn, $sql);
	}
	
	function getTagihanPendaftar($conn, $nopendaftarmulai,$nopendaftarakhir){
		
		$sql = "select * from h2h.ke_tagihan where 1=1";
		if (!empty ($nopendaftarmulai) and empty ($nopendaftarakhir))
		$sql .=" and nopendaftar = '$nopendaftarmulai'";
		if (!empty ($nopendaftarmulai) and !empty($nopendaftarakhir))
		$sql .=" and nopendaftar between '$nopendaftarmulai' and '$nopendaftarakhir'";
		if (!empty ($nopendaftarakhir) and empty ($nopendaftarmulai))
		$sql .=" and nopendaftar = '$nopendaftarakhir'";
		
			$rs_data = $conn->Execute($sql);			
			
			$data = array();
			while ($row = $rs_data->fetchRow()){
				$data[$row['nopendaftar']][] = $row;
				}
			return $data;
		
	}
	function getRekapmahasiswabaru($conn, $tglawal,$tglakhir,$sistemkuliah = ''){

		if (!empty ($tglawal))		
				$tglawal = date::indoDateYmd($tglawal);			
		if (!empty ($tglakhir))		
				$tglakhir = date::indoDateYmd($tglakhir);			
		
		
		$sql = "select count(*) as jumlah, pilihanditerima from pendaftaran.pd_pendaftar where 1=1 and tgldaftarulang is not null and nimpendaftar is not null and isdaftarulang is not null";
		if (!empty ($tglawal) and empty ($tglakhir))
		$sql .=" and tgldaftarulang = '$tglawal'";
		if (!empty ($tglawal) and !empty($tglakhir))
		$sql .=" and tgldaftarulang between '$tglawal' and '$tglakhir'";
		if (!empty ($tglakhir) and empty ($tglawal))
		$sql .=" and tgldaftarulang = '$tglakhir'";
		if (!empty ($sistemkuliah))
		$sql.=" and sistemkuliah = '$sistemkuliah'";
		$sql.=" group by pilihanditerima";
		return  self::makeRowData($conn, $sql);
		
		
		
	}
	function getAsalsekolahPendaftar($conn,$kodeunit,$periode,$sistemkuliah){
		//start ambil data smu
		$sql_smu = "select p.asalsmu, p.kodekotasmu, s.namasmu,  k.namakota, count(*) as jumlah from pendaftaran.pd_pendaftar p 
					left join gate.ms_unit u on u.kodeunit = p.pilihanditerima 
					left join pendaftaran.lv_smu s on s.idsmu = p.asalsmu
					left join akademik.ms_kota k on k.kodekota = p.kodekotasmu
					where 1=1 "; 
		if (!empty ($kodeunit)){
			$unit = $conn->getRow("select infoleft, inforight from gate.ms_unit where kodeunit = '$kodeunit'");
			$sql_smu .=" and u.infoleft >= '".$unit['infoleft']."' and u.inforight <= '".$unit['inforight']."'";
		}
		if (!empty ($periode))
			$sql_smu .=" and p.periodedaftar= '$periode'";
		if (!empty ($sistemkuliah))
			$sql_smu .=" and p.sistemkuliah= '$sistemkuliah'";
			
		$sql_smu .= " group by namasmu,namakota,asalsmu,kodekotasmu order by namasmu";
		
		$data_smu = self::makeRowData($conn, $sql_smu);
		
		//start ambil data smu pendaftar;
		$sql_data = "select kodekotasmu ,kodesekolah, nopendaftar, nama, asalsmu,tglregistrasi, rt,rw, nomorrumah,kel, kec, nimpendaftar, hp, telp, email,
						'jalan '||coalesce(jalan,'')||' Rt '||coalesce(rt,'-')||' Rw '||coalesce(rw,'-')||' Nomor '||coalesce(nomorrumah,0)||' Kelurahan '||coalesce(kel,'-')||' Kecamatan '||coalesce(kec,'-')||' Kota '||namakota||' Propinsi '||namapropinsi as alamat
						from pendaftaran.pd_pendaftar p 					
						left join gate.ms_unit u on u.kodeunit = p.pilihanditerima 
						left join akademik.ms_kota k on k.kodekota=p.kodekota
						left join akademik.ms_propinsi pr on pr.kodepropinsi=p.kodepropinsi
						where 1=1 "; 
		if (!empty ($kodeunit)){
			$unit = $conn->getRow("select infoleft, inforight from gate.ms_unit where kodeunit = '$kodeunit'");
			$sql_data .=" and u.infoleft >= '".$unit['infoleft']."' and u.inforight <= '".$unit['inforight']."'";
		}
		if (!empty ($periode))
			$sql_data .=" and p.periodedaftar= '$periode'";
		if (!empty ($sistemkuliah))
			$sql_data .=" and p.sistemkuliah= '$sistemkuliah'";
						
		$sql_data .=" order by tglregistrasi desc";
		
		$rowdata = self::makeRowData($conn,$sql_data);
		
		$data = array();
		foreach ($rowdata as $val){
			$data[$val['asalsmu']][trim($val['kodekotasmu'])][] = $val;
		}
		
		return array($data_smu,$data);
		
	}
	function getGeneratenopendaftar($conn, $tglawal,$tglakhir,$periode,$kodeunit,$sistemkuliah){
		$sql=" 	select nopendaftar, nama, idgelombang, telp, hp, email, nimpendaftar,
				'jalan '||coalesce(jalan,'')||' Rt '||coalesce(rt,'-')||' Rw '||coalesce(rw,'-')||' Nomor '||coalesce(nomorrumah,0)||' Kelurahan '||coalesce(kel,'-')||' Kecamatan '||coalesce(kec,'-')||' Kota '||namakota||' Propinsi '||namapropinsi as alamat
				
				from pendaftaran.pd_pendaftar p 
				join gate.ms_unit u on u.kodeunit = p.pilihan1 
				left join akademik.ms_kota k on k.kodekota=p.kodekota
				left join akademik.ms_propinsi pr on pr.kodepropinsi=p.kodepropinsi
				
				where 1=1";
		if (!empty ($periode))
		$sql.=" and p.periodedaftar = '$periode'";
		if (!empty ($sistemkuliah))
		$sql.=" and p.sistemkuliah = '$sistemkuliah'";
		if (!empty($kodeunit)){
			$u = $conn->getRow("select infoleft, inforight from gate.ms_unit where kodeunit = '$kodeunit'");
		$sql.=" and u.infoleft >= '".$u['infoleft']."' and u.inforight <= '".$u['inforight']."' ";
		}

		if (!empty ($tglawal) or !empty($tglakhir)){
			if (!empty ($tglawal) and !empty($tglakhir)){
				$tglawal = date::indoDateYmd($tglawal);			
				$tglakhir = date::indoDateYmd($tglakhir);	
				$sql.=" and p.tglregistrasi between '$tglawal' and '$tglakhir' ";
			}else if (!empty ($tglawal) and empty($tglakhir)){
				$tglawal = date::indoDateYmd($tglawal);			
				$sql.=" and p.tglregistrasi = '$tglawal' ";
			}
			else if (empty ($tglawal) and !empty($tglakhir)){
				$tglawal = date::indoDateYmd($tglakhir);			
				$sql.=" and p.tglregistrasi = '$tglakhir' ";
			}
			$sql.=" order by nopendaftar";
		}
		return  self::makeRowData($conn, $sql);
		
	}
	function getGeneratenim($conn, $tglawal,$tglakhir,$periode='',$kodeunit='',$sistemkuliah=''){
		$sql=" 	select p.nopendaftar, p.nimpendaftar, nama, p.idgelombang, telp, hp, email, p.sistemkuliah, u.namaunit, p.tglgeneratenim, v.jumlahbayar,
				'jalan '||coalesce(jalan,'')||' Rt '||coalesce(rt,'-')||' Rw '||coalesce(rw,'-')||' Nomor '||coalesce(nomorrumah,0)||' Kelurahan '||coalesce(kel,'-')||' Kecamatan '||coalesce(kec,'-')||' Kota '||namakota||' Propinsi '||namapropinsi as alamat
				
				from pendaftaran.pd_pendaftar p 
				join gate.ms_unit u on u.kodeunit = p.pilihan1 
				left join akademik.ms_kota k on k.kodekota=p.kodekota
				left join akademik.ms_propinsi pr on pr.kodepropinsi=p.kodepropinsi
				left join h2h.ke_pembayaranfrm f on f.notoken = p.tokenpendaftaran
				left join h2h.v_tagbayarawalpendaftar v on v.nopendaftar = p.nopendaftar
				where 1=1 and p.nimpendaftar is not null";
		if (!empty ($periode))
		$sql.=" and p.periodedaftar = '$periode'";
		if (!empty ($sistemkuliah))
		$sql.=" and p.sistemkuliah = '$sistemkuliah'";
		if (!empty($kodeunit)){
			$u = $conn->getRow("select infoleft, inforight from gate.ms_unit where kodeunit = '$kodeunit'");
		$sql.=" and u.infoleft >= '".$u['infoleft']."' and u.inforight <= '".$u['inforight']."' ";
		}

		if (!empty ($tglawal) or !empty($tglakhir)){
			if (!empty ($tglawal) and !empty($tglakhir)){
				$tglawal = date::indoDateYmd($tglawal);			
				$tglakhir = date::indoDateYmd($tglakhir);	
				$sql.=" and p.tglgeneratenim between '$tglawal' and '$tglakhir' ";
			}else if (!empty ($tglawal) and empty($tglakhir)){
				$tglawal = date::indoDateYmd($tglawal);
				$sql.=" and p.tglgeneratenim = '$tglawal' ";
			}
			else if (empty ($tglawal) and !empty($tglakhir)){
				$tglawal = date::indoDateYmd($tglakhir);			
				$sql.=" and p.tglgeneratenim = '$tglakhir' ";
			}
		}
		$sql.=" order by nopendaftar";

		return  self::makeRowData($conn, $sql);
		
	}
	function getRekapgeneratenopendaftar($conn, $tglawal,$tglakhir,$periode,$sistemkuliah){

		if (!empty ($tglawal))		
				$tglawal = date::indoDateYmd($tglawal);			
		if (!empty ($tglakhir))		
				$tglakhir = date::indoDateYmd($tglakhir);			
		
		
		$sql = "select count(*) as jumlah, p.pilihanditerima from pendaftaran.pd_pendaftar p left join gate.ms_unit u on u.kodeunit = p.pilihan1 where 1=1 ";
		if (!empty ($tglawal) and empty ($tglakhir))
		$sql .=" and tglregistrasi = '$tglawal'";
		if (!empty ($tglawal) and !empty($tglakhir))
		$sql .=" and tglregistrasi between '$tglawal' and '$tglakhir'";
		if (!empty ($tglakhir) and empty ($tglawal))
		$sql .=" and tglregistrasi = '$tglakhir'";

		if (!empty ($periode))
		$sql.=" and p.periodedaftar = '$periode'";
		
		if (!empty ($sistemkuliah))
		$sql.=" and p.sistemkuliah = '$sistemkuliah'";


		$sql.=" group by pilihanditerima";
		return  self::makeRowData($conn, $sql);		
	}
	function getPesertausm($conn,$tglawal,$tglakhir,$periode='',$kodeunit='',$sistemkuliah=''){
		$sql=" 	select p.nopendaftar, p.nimpendaftar, nama, p.idgelombang, telp, hp, email, p.sistemkuliah, u.namaunit, p.tglregistrasi,
				'jalan '||coalesce(jalan,'')||' Rt '||coalesce(rt,'-')||' Rw '||coalesce(rw,'-')||' Nomor '||coalesce(nomorrumah,0)||' Kelurahan '||coalesce(kel,'-')||' Kecamatan '||coalesce(kec,'-')||' Kota '||namakota||' Propinsi '||namapropinsi as alamat
				
				from pendaftaran.pd_pendaftar p 
				join gate.ms_unit u on u.kodeunit = p.pilihan1 
				left join akademik.ms_kota k on k.kodekota=p.kodekota
				left join akademik.ms_propinsi pr on pr.kodepropinsi=p.kodepropinsi
				where 1=1 ";
		if (!empty ($periode))
		$sql.=" and p.periodedaftar = '$periode'";
		if (!empty ($sistemkuliah))
		$sql.=" and p.sistemkuliah = '$sistemkuliah'";
		if (!empty($kodeunit)){
			$u = $conn->getRow("select infoleft, inforight from gate.ms_unit where kodeunit = '$kodeunit'");
		$sql.=" and u.infoleft >= '".$u['infoleft']."' and u.inforight <= '".$u['inforight']."' ";
		}

		if (!empty ($tglawal) or !empty($tglakhir)){
			if (!empty ($tglawal) and !empty($tglakhir)){
				$tglawal = date::indoDateYmd($tglawal);			
				$tglakhir = date::indoDateYmd($tglakhir);	
				$sql.=" and p.tglregistrasi between '$tglawal' and '$tglakhir' ";
			}else if (!empty ($tglawal) and empty($tglakhir)){
				$tglawal = date::indoDateYmd($tglawal);
				$sql.=" and p.tglregistrasi = '$tglawal' ";
			}
			else if (empty ($tglawal) and !empty($tglakhir)){
				$tglawal = date::indoDateYmd($tglakhir);			
				$sql.=" and p.tglregistrasi = '$tglakhir' ";
			}
		}
		$sql.=" order by nopendaftar";

		return  self::makeRowData($conn, $sql);
		
	}	
	function getRekappesertausm($conn,$periode,$sistemkuliah,$tglawal,$tglakhir){

		if (!empty ($tglawal))		
				$tglawal = date::indoDateYmd($tglawal);			
		if (!empty ($tglakhir))		
				$tglakhir = date::indoDateYmd($tglakhir);			
		
		
		$sql = "select count(*) as jumlah, pilihan1 from pendaftaran.pd_pendaftar where 1=1 ";
		if (!empty ($tglawal) and empty ($tglakhir))
		$sql .=" and tglregistrasi = '$tglawal'";
		if (!empty ($tglawal) and !empty($tglakhir))
		$sql .=" and tglregistrasi between '$tglawal' and '$tglakhir'";
		if (!empty ($tglakhir) and empty ($tglawal))
		$sql .=" and tglregistrasi = '$tglakhir'";
		
		if (!empty($periode))
		$sql.=" and periodedaftar = '$periode'";
		if (!empty($sistemkuliah))
		$sql.=" and sistemkuliah = '$sistemkuliah'";
		
		$sql.=" group by pilihan1";
		return  self::makeRowData($conn, $sql);
		
		
		
	}

	function InquiryPendaftarPeriode($conn)
	{
		$sql = "select * from pendaftaran.v_chartpendaftarperiode order by periodedaftar desc";
		return $conn->getArray($sql);
	}
	
	
}
?>
