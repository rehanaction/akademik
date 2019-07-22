<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTagihan extends mModel {
		const schema = 'h2h';
		const table = 'ke_tagihan';
		const order = 'idtagihan';
		const key = 'idtagihan';
		const label = 'idtagihan';
		
		const dendaDefault = 'DENDA'; // denda terlambat bayar, bukan merupakan jenis tagihan
		
	function getListFilter($col,$key) {
			switch($col) {
				case 'p.kodeunit': return "p.kodeunit = '$key'";
				case 'periode': return "periode = '$key'";
				case 'jalurpenerimaan': return "jalurpenerimaan = '$key'";
				case 'jenistagihan': return "jenistagihan = '$key'";
				case 'isedit': return "isedit = '$key'";
				case 'sistemkuliah': return "p.sistemkuliah = '$key'";
				case 'flaglunas': 
				$key = implode("','", $key);
				return "flaglunas in ('$key')";
				case 'unitdesc':
					global $conn;
					$sql = "select infoleft,inforight from gate.ms_unit where kodeunit = '$key'";
					$row = $conn->GetRow($sql);
					return "infoleft >= ".(int)$row['infoleft']." and inforight <= ".(int)$row['inforight'];
				case 'basiskampus':
					global $conn, $conf;
					require_once(Route::getModelPath('sistemkuliah'));
					$sistem = mSistemkuliah::getIdByBasisKampus($conn,modul::getBasis(),modul::getKampus());
					return "  p.sistemkuliah in ('".implode("','",$sistem)."') ";
					
			}
		}

	
	// delete
	function delete($conn,$data){
		
			require_once(Route::getModelPath('akademik'));
			
			$sqlmhs = mAkademik::sqlMhs($conn,$data);
			
			$sql = " delete from ".static::table()." where ( isedit = 'G' and flaglunas = 'BB') ";
			
			if($data['jenistagihan']<>'')
				$sql .= " and jenistagihan = '".$data['jenistagihan']."'";
			if($data['periodetagihan']<>'')
				$sql .= " and periode = '".$data['periodetagihan']."'";
			if($data['bulantahun']<>'' and $data['bulantahun']<>$data['periodetagihan'])
				$sql .= " and bulantahun = '".$data['bulantahun']."'";
			  $sql .= " and nim in (".$sqlmhs.")";
			 
			$conn->Execute($sql);
			
			return $err->errorNo;
			}
		
	function deletetagihanawal($conn,$data){
		
			require_once(Route::getModelPath('akademik'));
			
			$sqlmhs = mAkademik::sqlpendaftar($conn,$data);
			
			$sql = " delete from ".static::table()." where ( isedit = 'G' and flaglunas = 'BB') ";
			
			if($data['jenistagihan']<>'')
				$sql .= " and jenistagihan = '".$data['jenistagihan']."'";
			if($data['periodetagihan']<>'')
				$sql .= " and periode = '".$data['periodetagihan']."'";
			  $sql .= " and nopendaftar in (".$sqlmhs.")";
			 
			$conn->Execute($sql);
			
			return $err->errorNo;
			}
			
	function deleteperid($conn,$id){
		
			$sql = " delete from ".static::table()." where ( /*isedit = 'G' and*/ flaglunas = 'BB') and idtagihan = '$id' ";
	
			$conn->Execute($sql);
			
			return $err->errorNo;
			}
	
	function getTagihanmhs($conn,$data){
			require_once(Route::getModelPath('akademik'));
			
			$sqlmhs = mAkademik::sqlMhs($conn,$data);
			
			$sql = "select * from ".static::table()." where (1=1)";
			if($data['jenistagihan']<>'')
				$sql .= " and jenistagihan = '".$data['jenistagihan']."'";
			if($data['periodetagihan']<>'')
				$sql .= " and periode = '".$data['periodetagihan']."'";
			if($data['bulantahun']<>'' and $data['bulantahun']<>$data['periodetagihan'])
				$sql .= " and bulantahun = '".$data['bulantahun']."'";
			$sql .= " and nim in (".$sqlmhs.")";
				
			return $conn->getArray($sql);
			
		}
	
	function getCounttagihan($conn,$periode,$jalur, $gelombang=''){
		
		$sql = "select count(*) as jml,
				m.jalurpenerimaan,
				m.sistemkuliah,
				t.periode,
				t.jenistagihan,
				coalesce(t.bulantahun,t.periode) as bulantahun,
				isedit,kodeunit
				 from ".static::table()." t
				join akademik.ms_mahasiswa m on m.nim = t.nim  
				where t. periode = '$periode' and m.jalurpenerimaan = '$jalur' ";
				
				if (!empty($gelombang))
					$sql.=" AND m.gelombang = '$gelombang' ";
					
				$sql.=" group by m.jalurpenerimaan,
				m.sistemkuliah,
				t.periode,
				t.jenistagihan,
				coalesce(t.bulantahun,t.periode),
				isedit,kodeunit";
		return $conn->GetArray($sql);
			
		}
		
		function listQuery() {
			$ispmb = mAkademik::isRolePMB();
			
			$sql = "select * from (select 
					p.idtagihan,p.jenistagihan,p.tgltagihan, p.isvalid,j.kodekelompok,
					p.tgldeadline,p.nominaltagihan, p.nominalbayar, p.potongan,p.denda,p.keterangan, p.periode,
					p.bulantahun, p.isangsur, p.isedit,p.flaglunas,p.jumlahsks,
					coalesce(sp.namasistem||' '||coalesce(sp.tipeprogram,''),s.namasistem||' '||coalesce(s.tipeprogram,'')) as namasistem,
					coalesce(p.nim,p.nopendaftar) as nim,
					coalesce(t.nama,td.nama) as nama,
					coalesce(t.jalurpenerimaan,td.jalurpenerimaan) as jalurpenerimaan,
					coalesce(t.sistemkuliah,td.sistemkuliah) as sistemkuliah,
					coalesce(t.kodeunit,td.pilihanditerima) as kodeunit,
					coalesce(u.namaunit,ud.namaunit) as namaunit,
					coalesce(u.infoleft,ud.infoleft) as infoleft,
					coalesce(u.inforight,ud.inforight) as inforight
					from h2h.ke_tagihan p
					JOIN h2h.lv_jenistagihan j ON p.jenistagihan=j.jenistagihan
					".($ispmb ? '' : 'left ')."JOIN pendaftaran.pd_pendaftar td ON p.nopendaftar=td.nopendaftar
					left JOIN akademik.ms_mahasiswa t ON p.nim=t.nim 
					left JOIN akademik.ak_sistem s on t.sistemkuliah=s.sistemkuliah 
					left JOIN akademik.ak_sistem sp on td.sistemkuliah=sp.sistemkuliah 
					left JOIN gate.ms_unit u ON u.kodeunit = t.kodeunit
					left JOIN gate.ms_unit ud ON ud.kodeunit = td.pilihanditerima) p
				";
			/*$sql = "select * from ((select 
					p.idtagihan,p.jenistagihan,p.nim,p.tgltagihan, 
					p.tgldeadline,p.nominaltagihan,p.keterangan, p.periode,
					p.bulantahun, p.isangsur, p.isedit,p.flaglunas,p.jumlahsks,
					t.nama,t.jalurpenerimaan,t.sistemkuliah,t.kodeunit,u.namaunit from h2h.ke_tagihan p 
					JOIN akademik.ms_mahasiswa t ON p.nim=t.nim 
					JOIN gate.ms_unit u ON u.kodeunit = t.kodeunit order by p.nim desc) 
					union ( 
					select p.idtagihan,p.jenistagihan,p.nopendaftar as nim,p.tgltagihan, 
					p.tgldeadline,p.nominaltagihan,p.keterangan, p.periode,
					p.bulantahun, p.isangsur, p.isedit,p.flaglunas,p.jumlahsks,
					 t.nama,t.jalurpenerimaan,t.sistemkuliah,t.pilihanditerima as kodeunit,u.namaunit from h2h.ke_tagihan p 
					JOIN pendaftaran.pd_pendaftar t ON p.nopendaftar=t.nopendaftar 
					JOIN gate.ms_unit u ON u.kodeunit = t.pilihanditerima order by p.nopendaftar desc ))
					 p 
					";*/
			return $sql;
		}
		
		function getDatadetail($conn,$key) { 
			$sql = "select 
					p.idtagihan,p.jenistagihan,p.tgltagihan, 
					p.tgldeadline,p.nominaltagihan,p.potongan,p.keterangan, p.periode,
					p.bulantahun, p.isangsur, p.isedit,p.flaglunas,p.jumlahsks,
					p.nim,p.nopendaftar,
					coalesce(t.nama,td.nama) as nama,
					coalesce(t.jalurpenerimaan,td.jalurpenerimaan) as jalurpenerimaan,
					coalesce(t.sistemkuliah,td.sistemkuliah) as sistemkuliah,
					coalesce(t.kodeunit,td.pilihanditerima) as kodeunit,
					coalesce(u.namaunit,ud.namaunit) as namaunit,
					coalesce(s.namasistem,'')||' '||coalesce(s.tipeprogram,'') as namasistem
					from h2h.ke_tagihan p 
					left JOIN akademik.ms_mahasiswa t ON p.nim=t.nim 
					left JOIN gate.ms_unit u ON u.kodeunit = t.kodeunit 
					left JOIN pendaftaran.pd_pendaftar td ON p.nopendaftar=td.nopendaftar 
					left JOIN gate.ms_unit ud ON ud.kodeunit = td.pilihanditerima
					left JOIN akademik.ak_sistem s ON s.sistemkuliah=coalesce(t.sistemkuliah,td.sistemkuliah)
					where idtagihan = '$key' 
					";
			return $conn->getRow($sql);
		}
		
		/* function getInquiry($conn,$mhs='',$jenisperiode='',$jenistagihan=''){
			$mhs = Query::escape($mhs);
			
			$sql = "select t.*,j.* from ".static::table()." t
					join h2h.lv_jenistagihan j on j.jenistagihan = t.jenistagihan 
					where (nim = $mhs or nopendaftar = $mhs) and flaglunas <> 'L'"; 
			
			if($jenistagihan<>'')
				$sql .= " and t.jenistagihan = ".Query::escape($jenistagihan);
			
			if($jenisperiode == 'setting')
			{
				require_once(Route::getModelPath('settingh2hdetail'));
				$settingdetail = mSettingh2hdetail::getDataSetting($conn);	
				
				$rs = $conn->Execute($sql);
				while($row = $rs->fetchRow()){
						if($row['frekuensitagihan']=='B')
							{
								if($row['bulantahun'] <= $settingdetail[$row['jenistagihan']]['bulantahunsekarang'])
									$data[]  = $row;
							}
						else
							{
								if($row['periode'] <= $settingdetail[$row['jenistagihan']]['periodesekarang'])
									$data[]  = $row;
							}
					}
			}
			else {
				$data = $conn->getArray($sql);
			}
			
			$arrid = array();
			foreach($data as $row)
				$arrid[] = $row['idtagihan'];
			
			// ambil deposit
			$sql = "select iddeposit, periode, jenisdeposit, novoucher, nominaldeposit-nominalpakai as nominaldeposit
					from h2h.ke_deposit
					where coalesce(nim,nopendaftar) = $mhs and status = '-1' and nominaldeposit > nominalpakai
					and (tglexpired is null or tglexpired > current_date) and tgldeposit <= current_date
					and (idtagihan is null or idtagihan in ('".implode("','",$arrid)."'))
					order by case when tglexpired is null then 1 else 0 end, tglexpired, nominaldeposit-nominalpakai";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->fetchRow()) {
				$rowt = array();
				$rowt['iddeposit'] = $row['iddeposit'];
				$rowt['periode'] = $row['periode'];
				$rowt['nominaltagihan'] = -1*$row['nominaldeposit'];
				
				if($row['jenisdeposit'] == 'V') {
					$rowt['idtagihan'] = $row['novoucher'];
					$rowt['jenistagihan'] = 'VOU';
					$rowt['namajenistagihan'] = 'VOUCHER';
				}
				else {
					$rowt['idtagihan'] = 'DEP'.str_pad($row['iddeposit'],21,'0',STR_PAD_LEFT);
					$rowt['jenistagihan'] = 'DEP';
					$rowt['namajenistagihan'] = 'DEPOSIT';
				}
				
				$data[] = $rowt;
			}
			
			return $data;
		} */
		function getInquiry2($conn,$mhs,$kelompok=null,$periode=null){
			$emhs = Query::escape($mhs);
			$ekelompok = Query::escape($kelompok);
			$eperiode = Query::escape($periode);
			
			$sql = "select t.idtagihan, t.periode, t.flaglunas, t.isvalid, j.jenistagihan, j.namajenistagihan,
					k.namakelompok, t.nominaltagihan, t.nominalbayar, t.potongan, t.denda, t.bulantahun, t.isangsur from h2h.ke_tagihan t
					join h2h.lv_jenistagihan j on t.jenistagihan = j.jenistagihan
					join h2h.lv_kelompoktagihan k on j.kodekelompok = k.kodekelompok
					where coalesce(t.nopendaftar,t.nim) = $emhs and not(t.flaglunas = 'F' and t.isvalid <> 0) ".
					(empty($kelompok) ? '' : " and j.kodekelompok = $ekelompok").
					(empty($periode) ? '' : " and (t.periode = $eperiode or ((t.periode is null or t.periode < $eperiode) and t.flaglunas in ('BB','BL','S')))").
					" order by case t.flaglunas when 'BB' then 0 when 'BL' then 0 when 'S' then 1 when 'L' then 2 else 3 end,
					case when t.isvalid = 0 then 1 else 0 end, t.periode";
					
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if(!empty($row['isvalid']) and ($row['flaglunas'] == 'BB' or $row['flaglunas'] == 'BL'))
					$data[] = $row;
			}
			
			$arrid = array();
			foreach($data as $row)
				$arrid[] = $row['idtagihan'];
			
			// ambil deposit
			$sql = "select iddeposit, periode, jenisdeposit, novoucher, nominaldeposit-nominalpakai as nominaldeposit
					from h2h.ke_deposit
					where coalesce(nim,nopendaftar) = $emhs and status = '-1' and nominaldeposit > nominalpakai
					and (tglexpired is null or tglexpired > current_date) and tgldeposit <= current_date
					and (idtagihan is null or idtagihan in ('".implode("','",$arrid)."'))
					order by case when tglexpired is null then 1 else 0 end, tglexpired, nominaldeposit-nominalpakai";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->fetchRow()) {
				$rowt = array();
				$rowt['iddeposit'] = $row['iddeposit'];
				$rowt['periode'] = $row['periode'];
				$rowt['nominaltagihan'] = -1*$row['nominaldeposit'];
				
				if($row['jenisdeposit'] == 'V') {
					$rowt['idtagihan'] = $row['novoucher'];
					$rowt['jenistagihan'] = 'VOU';
					$rowt['namajenistagihan'] = 'VOUCHER';
				}
				else {
					$rowt['idtagihan'] = 'DEP'.str_pad($row['iddeposit'],21,'0',STR_PAD_LEFT);
					$rowt['jenistagihan'] = 'DEP';
					$rowt['namajenistagihan'] = 'DEPOSIT';
				}
				
				$data[] = $rowt;
			}
			
			return $data;
		}
			
		
		function getInquiry($conn,$mhs,$kelompok=null,$periode=null){
			$emhs = Query::escape($mhs);
			$ekelompok = Query::escape($kelompok);
			$eperiode = Query::escape($periode);
			
			$sql = "select t.idtagihan, t.periode, t.flaglunas, t.isvalid, j.jenistagihan, j.namajenistagihan,
					k.namakelompok, t.nominaltagihan, t.nominalbayar, t.potongan, t.denda, t.bulantahun, t.isangsur from h2h.ke_tagihan t
					join h2h.lv_jenistagihan j on t.jenistagihan = j.jenistagihan
					join h2h.lv_kelompoktagihan k on j.kodekelompok = k.kodekelompok
					where coalesce(t.nim,t.nopendaftar) = $emhs and not(t.flaglunas = 'F' and t.isvalid <> 0) ".
					(empty($kelompok) ? '' : " and j.kodekelompok = $ekelompok").
					(empty($periode) ? '' : " and (t.periode = $eperiode or ((t.periode is null or t.periode < $eperiode) and t.flaglunas in ('BB','BL','S')))").
					" order by case t.flaglunas when 'BB' then 0 when 'BL' then 0 when 'S' then 1 when 'L' then 2 else 3 end,
					case when t.isvalid = 0 then 1 else 0 end, t.periode";
					
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if(!empty($row['isvalid']) and ($row['flaglunas'] == 'BB' or $row['flaglunas'] == 'BL'))
					$data[] = $row;
			}
			
			$arrid = array();
			foreach($data as $row)
				$arrid[] = $row['idtagihan'];
			
			// ambil deposit
			$sql = "select iddeposit, periode, jenisdeposit, novoucher, nominaldeposit-nominalpakai as nominaldeposit
					from h2h.ke_deposit
					where coalesce(nim,nopendaftar) = $emhs and status = '-1' and nominaldeposit > nominalpakai
					and (tglexpired is null or tglexpired > current_date) and tgldeposit <= current_date
					and (idtagihan is null or idtagihan in ('".implode("','",$arrid)."'))
					order by case when tglexpired is null then 1 else 0 end, tglexpired, nominaldeposit-nominalpakai";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->fetchRow()) {
				$rowt = array();
				$rowt['iddeposit'] = $row['iddeposit'];
				$rowt['periode'] = $row['periode'];
				$rowt['nominaltagihan'] = -1*$row['nominaldeposit'];
				
				if($row['jenisdeposit'] == 'V') {
					$rowt['idtagihan'] = $row['novoucher'];
					$rowt['jenistagihan'] = 'VOU';
					$rowt['namajenistagihan'] = 'VOUCHER';
				}
				else {
					$rowt['idtagihan'] = 'DEP'.str_pad($row['iddeposit'],21,'0',STR_PAD_LEFT);
					$rowt['jenistagihan'] = 'DEP';
					$rowt['namajenistagihan'] = 'DEPOSIT';
				}
				
				$data[] = $rowt;
			}
			
			return $data;
		}
			
		function dataTagihanwisuda($conn,$periode = '',$unit='',$nim = ''){
			$sql = "select * from h2h.ke_tagihan where
			 jenistagihan in ( select jenistagihan from h2h.lv_jenistagihan where frekuensitagihan = 'W')";
			if($periode)
				$sql .= " and periode = '$periode'";
			if($nim)
				$sql .= " and nim = '$nim'";
				
			$rs = $conn->Execute($sql);
			while($row = $rs->fetchRow()){
					$data[$row['nim']][$row['jenistagihan']][$row['periode']] = $row;
				}
			return $data;
			}
			
		function updateReversal($conn,$id){
				$sql = "update h2h.ke_tagihan set flaglunas = 'BL' where 
						idtagihan in (select idtagihan from h2h.ke_pembayarandetail where idpembayaran = '$id')";
				$conn->Execute($sql);
				
				return $conn->errorNo();
			}
			
		// generate wisuda .. 
		function generateTagihanwisuda($conn, $nim, $kodeunit, $periodeyudisium, $jenistagihan = '',$cek = '1'){
				require_once(Route::getModelPath('tarif'));
				require_once(Route::getModelPath('jenistagihan'));
				$arr_data = mTarif::getTarifwisuda($conn,$periodeyudisium);
				if($arr_data)
					foreach($arr_data as $i => $v){
							$tarif[$v['periodetarif']][$v['kodeunit']][$v['jenistagihan']] = $v;
						}
				
				$arr_tagihan = mJenistagihan::getArray($conn,'W');
				if($arr_tagihan)
				foreach($arr_tagihan as $i => $v){
					$arr_infojenistagihan[$v['jenistagihan']] = $v;
				}
				//cek jika tagihan hanya 1
				if($cek == '1'){
					 
					$record = array();
					$record['nim'] = $nim;
					$record['periode'] = $periodeyudisium;
					$record['tgltagihan'] = date('Y-m-d');
					$record['isedit'] = 'G';
					$record['flaglunas'] = 'BB';
					$periode = $conn->getOne("select periode from akademik.ak_periodeyudisium where idyudisium = '$periodeyudisium'");
					if($jenistagihan <> ''){
						
						$infojt = $arr_infojenistagihan[$jenistagihan];
						
						$kodetagihan = str_pad($infojt['kodetagihan'],2,'0',STR_PAD_LEFT);
						$idtagihan = $kodetagihan.$periode.'01'.str_pad($nim,15,'0',STR_PAD_LEFT);
						
						$record['idtagihan'] = $idtagihan;  // generate idtagihan
						$record['jenistagihan'] = $infojt['jenistagihan'];
						$record['nominaltagihan'] = $tarif[$periodeyudisium][$kodeunit][$jenistagihan]['nominaltarif'];
						
						if(!empty($record['nominaltagihan'])) //jika tidak ditemukan nominal tarif maka batalkan proses insert tagihan 
							$err = static::insertRecord($conn,$record);

						}
					else{
							if($arr_tagihan)
							foreach($arr_tagihan as $i => $v){
								$record['jenistagihan'] = $v['jenistagihan'];
								$jenistagihan = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT);
								$idtagihan = $jenistagihan.$periode.'01'.str_pad($nim,15,'0',STR_PAD_LEFT);
								$record['idtagihan'] = $idtagihan; //generate idtagihan
								$record['nominaltagihan'] = $tarif[$periodeyudisium][$kodeunit][$v['jenistagihan']]['nominaltarif'];
								if(!empty($record['nominaltagihan'])) //jika tidak ditemukan nominal tarif maka batalkan proses insert tagihan 
									$err = static::insertRecord($conn,$record);
								}
						}
					
					}
				else{
					$data = static::dataTagihanwisuda($conn,$periodeyudisium,$kodeunit,$nim);
					if($data){
						if($jenistagihan <> '')
						{
							if($data[$nim][$jenistagihan][$periodeyudisium]['isedit']=='E')
								{
									$err = 1;
									$msg = "Telah ada tagihan dan edited";
									}
								else{
									$infojt = $arr_infojenistagihan[$jenistagihan];
									$record['nim'] = $nim;
									$record['periode'] = $periodeyudisium;
									$record['tgltagihan'] = date('Y-m-d');
									$record['isedit'] = 'G';
									$record['jenistagihan'] = $infojt['jenistagihan'];
									$record['nominaltagihan'] = $tarif[$periodeyudisium][$kodeunit][$jenistagihan]['nominaltarif'];
									if(!empty($record['nominaltagihan'])) //jika tidak ditemukan nominal tarif maka batalkan proses update tagihan 
										$err = static::updateRecord($conn,$record,$data[$nim][$jenistagihan][$periodeyudisium]['idtagihan']);
									}
							}
						else{
								if($arr_tagihan)
								foreach($arr_tagihan as $i => $v){
								if($data[$nim][$v['jenistagihan']][$periodeyudisium]['isedit']=='E')
								{
									$err = 1;
									$msg = "Telah ada tagihan dan edited";
									}
								else{
									$record['nim'] = $nim;
									$record['periode'] = $periodeyudisium;
									$record['tgltagihan'] = date('Y-m-d');
									$record['isedit'] = 'G';
									$record['jenistagihan'] = $v['jenistagihan'];
									$record['nominaltagihan'] = $tarif[$periodeyudisium][$kodeunit][$v['jenistagihan']]['nominaltarif'];
									if(!empty($record['nominaltagihan'])) //jika tidak ditemukan nominal tarif maka batalkan proses update tagihan 
										$err = static::updateRecord($conn,$record,$data[$nim][$v['jenistagihan']][$periodeyudisium]['idtagihan']);
									}
								}
							}
						}
					else{
							$record = array();
							$record['nim'] = $nim;
							$record['periode'] = $periodeyudisium;
							$record['tgltagihan'] = date('Y-m-d');
							$record['isedit'] = 'G';
							$periode = $conn->getOne("select periode from akademik.ak_periodeyudisium where idyudisium = '$periodeyudisium'");
							
							if($jenistagihan <> ''){
								
								$infojt = $arr_infojenistagihan[$jenistagihan];
								$jenistagihan = str_pad($infojt['kodetagihan'],2,'0',STR_PAD_LEFT);
								$idtagihan = $jenistagihan.$periode.'01'.str_pad($nim,15,'0',STR_PAD_LEFT);
								$record['idtagihan'] = $idtagihan; //Generate idtagihan
								$record['jenistagihan'] = $infojt['jenistagihan'];
								$record['nominaltagihan'] = $tarif[$periodeyudisium][$kodeunit][$jenistagihan]['nominaltarif'];
								if(!empty($record['nominaltagihan'])) //jika tidak ditemukan nominal tarif maka batalkan proses insert tagihan 
									$err = static::insertRecord($conn,$record);
								}
							else{
									if($arr_tagihan)
									foreach($arr_tagihan as $i => $v){
										$jenistagihan = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT);
										$idtagihan = $jenistagihan.$periode.'01'.str_pad($nim,15,'0',STR_PAD_LEFT);
										$record['idtagihan'] = $idtagihan; // Generate idtagihan;
										$record['jenistagihan'] = $v['jenistagihan'];
										$record['nominaltagihan'] = $tarif[$periodeyudisium][$kodeunit][$v['jenistagihan']]['nominaltarif'];
										if(!empty($record['nominaltagihan'])) //jika tidak ditemukan nominal tarif maka batalkan proses insert tagihan 
											$err = static::insertRecord($conn,$record);
										}
								}
						}
				}
				return $err;
			}
			
			// generate awal Perkuliahan + semester 1 untuk pendaftar
		function generateTagihanpendaftar($conn, $r_nopendaftar, $datapendaftar){
			require_once(Route::getModelPath('akademik'));
			require_once(Route::getModelPath('tarif'));
			require_once(Route::getModelPath('jenistagihan'));
			$arr_tagihan = mJenistagihan::getArray($conn,array('A','S','T','B'),'0'); # mendapatkan jenis tagihan.
			
			if (empty ($datapendaftar['jalurpenerimaan']))
				return list($p_posterr, $p_postmsg) = array(true, 'Proses Generate Gagal, Silahkan cek jalur penerimaan pendaftar');
			else if (empty ($datapendaftar['periodedaftar']))
				return list($p_posterr, $p_postmsg) = array(true, 'Proses Generate Gagal, Silahkan cek Periode Daftar');
			else if (empty ($datapendaftar['sistemkuliah']))
				return list($p_posterr, $p_postmsg) = array(true, 'Proses Generate Gagal, Silahkan cek Sistem Kuliah pendaftar');
			else if (empty ($datapendaftar['gelombang']))
				return list($p_posterr, $p_postmsg) = array(true, 'Proses Generate Gagal, Silahkan cek Gelombang daftar pendaftar');
				
			if($arr_tagihan)
				$g_tagihan = array();
				foreach($arr_tagihan as $i => $v){
					$jumlahangsur = ($datapendaftar['periodemasuk'] >= '20171' ? $v['jumlahangsur2017'] : $v['jumlahangsur']);
					//cari tarif
					$arr_tarif = mTarif::getRowtarif($conn,$datapendaftar['periodedaftar'].'1',$datapendaftar['jalurpenerimaan'],$datapendaftar['pilihanditerima'],$v['jenistagihan'],$datapendaftar['sistemkuliah'], $datapendaftar['gelombang']);
					
					if ($v['jenistagihan'] == 'UKT')
					$arr_tarif = mTarif::getTarifuktpendaftar($conn, $datapendaftar['nopendaftar']);
					 
					if($v['frekuensitagihan']<>'B')
						{  
						$cek = $conn->getRow("select * from h2h.ke_tagihan where nopendaftar = '$r_nopendaftar' 
											and jenistagihan = '".$v['jenistagihan']."' and periode = '".$datapendaftar['periodedaftar'].'1'."'");
							if($cek){
								if($cek['isedit'] == 'G')
									{
										if($cek['flaglunas']=='BB'){
											static::deleteperid($conn,$cek['idtagihan']);
										}
									}
							}
							
							$nominaltagihan = ($arr_tarif[0]['nominaltarif'] / $jumlahangsur); //bagi jika ada split tagihan
							if ($nominaltagihan > 0){


										$rec = array();
										$rec['jenistagihan'] = $v['jenistagihan'];
										$rec['nopendaftar'] = $r_nopendaftar;
										$rec['tgltagihan'] = date('Y-m-d');

										$rec['periode'] = $datapendaftar['periodedaftar'].'1';
										$rec['isangsur'] = 0;
										$rec['isedit'] = 'G';
										$rec['flaglunas'] = 'BB';
										$rec['nominaltagihan'] = $nominaltagihan;
										
									for ($a=1; $a<=$jumlahangsur; $a++){
										if ($rec['nominaltagihan'] > 0) {
										$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$datapendaftar['periodedaftar'].'1'.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nopendaftar,15,'0',STR_PAD_LEFT);

										$err = static::insertRecord($conn,$rec);
										if ($err){
											$jumlah_err++;
										}else{
											if (!empty ($rec['nominaltagihan']))
											$g_tagihan[]=$v['namajenistagihan'];	
											}										
										}
									}

							}
					
						}
					else{  
						$frekuensi = mAkademik::getFrekuensibulanan($conn,$datapendaftar['periodedaftar'].'1');
						foreach($frekuensi as $i => $val){
								$cek = $conn->getRow("select * from h2h.ke_tagihan where nopendaftar = '$r_nopendaftar' 
											and jenistagihan = '".$v['jenistagihan']."' and periode = '".$datapendaftar['periodedaftar'].'1'."'
											and bulantahun = '$i'");
							if($cek){
								if($cek['isedit'] == 'G')
									{
										if($cek['flaglunas']=='BB'){
										static::deleteperid($conn,$cek['idtagihan']);
										}
									}
								}
										$rec = array();

										$rec['jenistagihan'] = $v['jenistagihan'];
										$rec['nopendaftar'] = $r_nopendaftar;
										$rec['tgltagihan'] = date('Y-m-d');
										$rec['periode'] = $datapendaftar['periodedaftar'].'1';
										$rec['bulantahun'] = $i;
										$rec['isangsur'] = 0;
										$rec['isedit'] = 'G';
										$rec['flaglunas'] = 'BB';
										
										$rec['nominaltagihan'] = ($arr_tarif[0]['nominaltarif'] / $jumlahangsur);
										if ($rec['nominaltagihan'] > 0){
											for ($a=1; $a<=$jumlahangsur; $a++){
												if ($rec['nominaltagihan'] > 0) {
													$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$datapendaftar['periodedaftar'].'1'.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nopendaftar,15,'0',STR_PAD_LEFT);
													
												$err = static::insertRecord($conn,$rec);
												
												if ($err){
													$jumlah_err++;
												}else{
													if (!empty ($arr_tarif['nominaltarif']))
													$g_tagihan[]=$v['namajenistagihan'];	
													}
												}
											}
										}
					 
						}
						
					}
				}
					if ($err <> 0)
						return array(true, 'Penyimpanan Data Gagal <br>'.$conn->ErrorMsg());
					else{
						
						if (count($g_tagihan) > 0 ){
						$pesan='Proses Generate Tagihan Berhasil';
						}else
						$pesan='Proses Generate Tagihan, Namun Tidak ada Tagihan untuk ';
						
						for($a=0; $a< count($g_tagihan); $a++){
							$pesan.= $g_tagihan[$a].', ';
							}
						$pesan .=' <br> No Pendaftar: <b>'.$r_nopendaftar.'</b>';
						
						if (count($g_tagihan) > 0 )
						$pesan .=' Berhasil';
						
						return array(false, $pesan);
					}
		}
			
			
			//"1130013008"
				// generate awal Perkuliahan + semester 1 untuk pendaftar
		function generateTagihansemester($conn, $r_nim, $r_periode, $datamhs){
			require_once(Route::getModelPath('tarif'));
			require_once(Route::getModelPath('jenistagihan'));
			if(substr($r_periode,-1)=='1')
				$arr_tagihan = mJenistagihan::getArray($conn,array('S','T','B'),'0');
			else
				$arr_tagihan = mJenistagihan::getArray($conn,array('S','B'),'0');
			if($arr_tagihan)
				foreach($arr_tagihan as $i => $v){
					$jumlahangsur = ($datamhs['periodemasuk'] >= '20171' ? $v['jumlahangsur2017'] : $v['jumlahangsur']  );
					//cari tarif
					$arr_tarif = mTarif::getRowtarif($conn,'',$datamhs['jalurpenerimaan'],$datamhs['kodeunit'],$v['jenistagihan'],$datamhs['sistemkuliah']);
					if($arr_tarif)
					foreach($arr_tarif as $it => $vt)
					{
						$tarif[$vt['periodetarif']][$vt['jalurpenerimaan']][$vt['gelombang']][$vt['sistemkuliah']][$vt['kodeunit']] = $vt;
					}
					
					if($v['frekuensitagihan']<>'B')
						{
						$cek = $conn->Execute("select * from h2h.ke_tagihan where nim = '$r_nim' 
											and jenistagihan = '".$v['jenistagihan']."' and periode = '".$r_periode."'");
											
						while ($row = $cek->FetchRow()){							
							if($row['isedit'] == 'G')
								{ 
									if($row['flaglunas']=='BB'){ 
										static::deleteperid($conn,$row['idtagihan']);
										$rec = array();
									}
								}
							}
									

								$rec = array();
								$rec['jenistagihan'] = $v['jenistagihan'];
								$rec['nim'] = $r_nim;
								$rec['tgltagihan'] = date('Y-m-d');
								$rec['periode'] = $r_periode;
								$rec['isangsur'] = 0;
								$rec['isedit'] = 'G';
								$rec['flaglunas'] = 'BB';
								if($v['aturanperiode']=='A'){
									$rec['nominaltagihan'] = $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][str_pad($datamhs['gelombang'],2,'0',STR_PAD_LEFT)][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
								}else
									$rec['nominaltagihan'] = $tarif[$r_periode][$datamhs['jalurpenerimaan']][str_pad($datamhs['gelombang'],2,'0',STR_PAD_LEFT)][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
									
								if ($jumlahangsur > 1)
								$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);
								
								for ($a=1; $a<=$jumlahangsur; $a++){
									
								if (!empty($rec['nominaltagihan']) or $rec['nominaltagihan'] > 0){
									$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
									$err = static::insertRecord($conn,$rec);
									if (!$err)
										$daftartagihan[] = $v['namajenistagihan'];
								}
									
							}

										
					}else{ 
						$frekuensi = mAkademik::getFrekuensibulanan($conn,$r_periode);
						foreach($frekuensi as $i => $val){
								$cek = $conn->getRow("select * from h2h.ke_tagihan where nim = '$r_nim' 
											and jenistagihan = '".$v['jenistagihan']."'
											and bulantahun = '$i'");
							if($cek){
								if($cek['isedit'] == 'G')
									{
										if($cek['flaglunas']=='BB'){
										static::deleteperid($conn,$cek['idtagihan']);
										}
									}
							}
									$rec = array();
									$rec['jenistagihan'] = $v['jenistagihan'];
									$rec['nim'] = $r_nim;
									$rec['tgltagihan'] = date('Y-m-d');
									$rec['periode'] = $r_periode;
									$rec['bulantahun'] = $i;
									$rec['isangsur'] = 0;
									$rec['isedit'] = 'G';
									$rec['flaglunas'] = 'BB';
									if($v['aturanperiode']=='A')
										$rec['nominaltagihan'] = $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][str_pad($datamhs['gelombang'],2,'0',STR_PAD_LEFT)][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
									else
										$rec['nominaltagihan'] = $tarif[$r_periode][$datamhs['jalurpenerimaan']][str_pad($datamhs['gelombang'],2,'0',STR_PAD_LEFT)][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
									
									if ($jumlahangsur > 1)
									$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);

									for ($a=1; $a<=$jumlahangsur; $a++){								
										if (!empty($rec['nominaltagihan']) or $rec['nominaltagihan'] > 0){
											$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
											$err = static::insertRecord($conn,$rec);
											
											if (!empty ($rec['nominaltagihan']))
												$daftartagihan[] = $v['namajenistagihan'];
												
										}
									}
								}
							}
						}
				return array($err, $daftartagihan);
			}
			function getDataTagihan($conn,$r_nim,$r_periode,$jenis)
		{
			$sql = "select jenistagihan,nopendaftar,nim,tgltagihan,tgldeadline,sum(nominaltagihan) as nominaltagihan,keterangan,periode,bulantahun,isangsur,isedit,sum(potongan) as potongan,flaglunas,tgllunas,jumlahsks,t_updateuser,t_updatetime,t_updateip,isvalid,sum(nominalbayar) as nominalbayar,sum(denda) as denda,angsuranke,isfollowup,keteranganpendaftar from h2h.ke_tagihan where nim='$r_nim' and periode='$r_periode' and jenistagihan='$jenis' GROUP BY jenistagihan,nopendaftar,nim,tgltagihan,tgldeadline,keterangan,periode,bulantahun,isangsur,isedit,flaglunas,tgllunas,t_updateuser,t_updatetime,t_updateip,isvalid,angsuranke,isfollowup,keteranganpendaftar,jumlahsks";
			return $conn->getRow($sql);
			
		}
		//delete data tagihan
		function deleteDataTagihan($conn,$r_nim,$r_periode,$jenis)
		{
			$sql = "delete from h2h.ke_tagihan where nim='$r_nim' and periode='$r_periode' and jenistagihan='$jenis'";
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
		}
		function InsertDeposit($conn,$data)
		{
			$kolom = implode(',',array_keys($data));
			$valuesArrays = array();
			$i = 0;
			foreach($data as $key=>$values)
			{
				if(is_int($values))
				{
					$valuesArrays[$i] = $values;
				}else{
					$valuesArrays[$i]= "'".$values."'";
				}
				$i++;
			}
			$values = implode(',',$valuesArrays);
			$sql = "insert into h2h.ke_deposit ($kolom) values($values)";
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
		}
			
		// generate KRS
		function generateTagihankrs($conn, $r_nim, $r_periode, $datamhs){
			require_once(Route::getModelPath('akademik'));
			require_once(Route::getModelPath('tarif'));
			require_once(Route::getModelPath('jenistagihan'));
			if(substr($r_periode,-1)=='1')
				$arr_tagihan = mJenistagihan::getArray($conn,array('S','T','B'),'1');
			else
				$arr_tagihan = mJenistagihan::getArray($conn,array('S','B'),'1');
			
			$datakrs = mAkademikkeu::getDatakrs($conn,$r_nim,$r_periode);
			//mengambil data tagihan krs
			$dataTagihan = mTagihan::getDataTagihan($conn,$r_nim,$r_periode);
			
			$err=0;
			if($arr_tagihan)
				foreach($arr_tagihan as $i => $v){
					$jumlahangsur = ($datamhs['periodemasuk'] >= '20171' ? $v['jumlahangsur2017'] : $v['jumlahangsur']);
					if($datakrs[$v['aturanperiode']]){
					//cari tarif
					$arr_tarif = mTarif::getArraytarif($conn,'',$datamhs['jalurpenerimaan'],$datamhs['kodeunit'],$v['jenistagihan'],$datamhs['sistemkuliah']);
					if($arr_tarif)
					foreach($arr_tarif as $t => $vt)
					{
						$tarif[$vt['periodetarif']][$vt['jalurpenerimaan']][$vt['gelombang']][$vt['sistemkuliah']][$vt['kodeunit']] = $vt;
						}
					if($v['frekuensitagihan']<>'B') {
						$dataTagihan = mTagihan::getDataTagihan($conn,$r_nim,$r_periode,$v['jenistagihan']);
						$cek = $conn->GetArray("select * from h2h.ke_tagihan where nim = '$r_nim' 
											and jenistagihan = '".$v['jenistagihan']."' and periode = '".$r_periode."'");
						//var_dump($cek);
						//die();
							if($cek){
								if($cek['isedit'] == 'G')
									{
										if($cek['flaglunas']=='BB'){
											static::deleteperid($conn,$cek['idtagihan']);
											}
										}
									}		
									//custom koding krs
											$dep = array();
											$rec = array();
											$pengali=0;
											foreach ($datakrs as $key => $value) {
												$pengali += $datakrs[$key];
												$rec['jumlahsks'] += $datakrs[$key];
											}
											$rec['jenistagihan'] = $v['jenistagihan'];
											$rec['nim'] = $r_nim;
											$rec['tgltagihan'] = date('Y-m-d');
											$rec['periode'] = $r_periode;
											$rec['isangsur'] = 0;
											$rec['isedit'] = 'G';
											$rec['flaglunas'] = 'BB';
											$rec['nominaltagihan'] = $pengali * $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['gelombang']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
											
										if(empty($dataTagihan)){
											if ($jumlahangsur > 1){
														$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);
														for ($a=1; $a<=$jumlahangsur; $a++){
															$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
															$err = static::insertRecord($conn,$rec);
												
													}
												}
										}else{

											if($pengali==$dataTagihan['jumlahsks']){
												//delete insert ulang
												
												$del = mTagihan::deleteDataTagihan($conn,$r_nim,$r_periode,$v['jenistagihan']);
												 if($del==true){
												 	//insert data
												 	if ($jumlahangsur > 1){
														$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);
														for ($a=1; $a<=$jumlahangsur; $a++){
															$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
															$err = static::insertRecord($conn,$rec);
												
														}
													}

												 }

											}elseif($pengali>$dataTagihan['jumlahsks'] && $dataTagihan['flaglunas']=='BB'){
												$rec['tgltagihan']=$dataTagihan['tgltagihan'];
												$del = mTagihan::deleteDataTagihan($conn,$r_nim,$r_periode,$v['jenistagihan']);
												 if($del==true){
												 	//insert data
												 	if ($jumlahangsur > 1){
														$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);
														for ($a=1; $a<=$jumlahangsur; $a++){
															$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
															$err = static::insertRecord($conn,$rec);
												
														}
													}

												 }
											}elseif($pengali>$dataTagihan['jumlahsks'] && $dataTagihan['flaglunas']=='L'){
												$rec['tgltagihan']=$dataTagihan['tgltagihan'];
												$rec['nominalbayar'] = $dataTagihan['nominalbayar'];
												$del = mTagihan::deleteDataTagihan($conn,$r_nim,$r_periode,$v['jenistagihan']);
												 if($del==true){
												 	//insert data
												 	if ($jumlahangsur > 1){
														$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);
														for ($a=1; $a<=$jumlahangsur; $a++){
															$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
															$err = static::insertRecord($conn,$rec);
												
														}
													}

												 }
											}elseif($pengali<$dataTagihan['jumlahsks'] && $dataTagihan['flaglunas']=='BB'){
												$del = mTagihan::deleteDataTagihan($conn,$r_nim,$r_periode,$v['jenistagihan']);
												 if($del==true){
												 	//insert data
												 	if ($jumlahangsur > 1){
														$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);
														for ($a=1; $a<=$jumlahangsur; $a++){
															$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
															$err = static::insertRecord($conn,$rec);
												
														}
													}

												 }
												
											}elseif($pengali<$dataTagihan['jumlahsks'] && $dataTagihan['flaglunas']=='L'){
												//deposit lebih bayar
												$rec['flaglunas'] = 'L';
												$rec['nominalbayar'] = $dataTagihan['nominalbayar'];
												$deposit = $dataTagihan['nominalbayar'] - $rec['nominaltagihan'];
												$del = mTagihan::deleteDataTagihan($conn,$r_nim,$r_periode,$v['jenistagihan']);
												 if($del==true){
												 	//insert data
												 	if ($jumlahangsur > 1){
														$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);
														for ($a=1; $a<=$jumlahangsur; $a++){
															$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
															$err = static::insertRecord($conn,$rec);
												
														}
														$dep['nim']=$r_nim;
														$dep['tgldeposit'] = date('Y-m-d');
														$dep['periode'] = $r_periode;
														$dep['nominaldeposit'] = $deposit;
														$dep['keterangan'] = "Lebih Bayar Pengurangan SKS";
														$dep['status'] = -1;
														mTagihan::InsertDeposit($conn,$dep);
													}

													

												 }

											}elseif($pengali<=$dataTagihan['jumlahsks'] && $dataTagihan['flaglunas']=='BL'){
												$rec['nominalbayar'] = $dataTagihan['nominalbayar'];
												$rec['flaglunas'] = 'BL';
												$del = mTagihan::deleteDataTagihan($conn,$r_nim,$r_periode,$v['jenistagihan']);
												 if($del==true){
												 	//insert data
												 	if ($jumlahangsur > 1){
														$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);
														for ($a=1; $a<=$jumlahangsur; $a++){
															$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
															$err = static::insertRecord($conn,$rec);
												
														}
													}
												}

											}
											
										}
											/*
												source code bawaan 
											if($v['aturanperiode'] == 'A'){
												$pengali =$datakrs[$v['aturanperiode']];
												$rec['jumlahsks'] = $datakrs[$v['aturanperiode']];
											}else{ 
												$pengali = 1;
											}
											
											if($v['aturanperiode']=='A')
												
											else
												$rec['nominaltagihan'] = $pengali * $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['gelombang']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
											*/
											//var_dump($rec);
											//var_dump($tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['gelombang']]);
											//die();
											
										/*$kolom = implode(',',array_keys($rec));
										$nilai = implode(',', array_values($rec));
										var_dump($kolom);
										var_dump($nilai);
										die();*/
										//var_dump($rec);
										//die();
					}else{ 	

						$frekuensi = mAkademik::getFrekuensibulanan($conn,$r_periode);
						foreach($frekuensi as $i => $val){
								$cek = $conn->getRow("select * from h2h.ke_tagihan where nim = '$r_nim' 
											and jenistagihan = '".$v['jenistagihan']."'
											and bulantahun = '$i'");
							if($cek){
								if($cek['isedit'] == 'G')
									{
										if($cek['flaglunas']=='BB'){
										static::deleteperid($conn,$cek['idtagihan']);
										}
									}
								}	
										$rec = array();
										$rec['jenistagihan'] = $v['jenistagihan'];
										$rec['nim'] = $r_nim;
										$rec['tgltagihan'] = date('Y-m-d');
										$rec['periode'] = $r_periode;
										$rec['bulantahun'] = $i;
										$rec['isangsur'] = 0;
										$rec['isedit'] = 'G';
										$rec['flaglunas'] = 'BB';
										if($v['aturanperiode'] == 'A'){
												$pengali = $datakrs[$v['aturanperiode']];
											$rec['jumlahsks'] =  $datakrs[$v['aturanperiode']];
											}
										else 
											$pengali = 1;
										if($v['aturanperiode']=='A')
											$rec['nominaltagihan'] = $pengali * $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
										else
											$rec['nominaltagihan'] = $pengali * $tarif[$r_periode][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];

											if ($jumlahangsur > 1)
											$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);

											for ($a=1; $a<=$jumlahangsur; $a++){
												$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);

												$err = static::insertRecord($conn,$rec);
												
												}
						}
					}
				}
			}
		
			return $err;
		}

		function generateTagihankrs2($conn, $r_nim, $r_periode, $datamhs){
			require_once(Route::getModelPath('akademik'));
			require_once(Route::getModelPath('tarif'));
			require_once(Route::getModelPath('jenistagihan'));
			if(substr($r_periode,-1)=='1')
				$arr_tagihan = mJenistagihan::getArray($conn,array('S','T','B'),'1');
			else
				$arr_tagihan = mJenistagihan::getArray($conn,array('S','B'),'1');
				
			$datakrs = mAkademik::getDatakrs($conn,$r_nim,$r_periode);
			$err=0;
			if($arr_tagihan)
				foreach($arr_tagihan as $i => $v){
					$jumlahangsur = ($datamhs['periodemasuk'] >= '20171' ? $v['jumlahangsur2017'] : $v['jumlahangsur']);
					if($datakrs){
					//cari tarif
					$arr_tarif = mTarif::getArraytarif($conn,'',$datamhs['jalurpenerimaan'],$datamhs['kodeunit'],$v['jenistagihan'],$datamhs['sistemkuliah']);
					if($arr_tarif)
					foreach($arr_tarif as $t => $vt)
					{
						$tarif[$vt['periodetarif']][$vt['jalurpenerimaan']][$vt['gelombang']][$vt['sistemkuliah']][$vt['kodeunit']] = $vt;
					}
					if($v['frekuensitagihan']<>'B') {
						
						$cek = $conn->getRow("select * from h2h.ke_tagihan where nim = '$r_nim' 
											and jenistagihan = '".$v['jenistagihan']."' and periode = '".$r_periode."'");
							if($cek){
								if($cek['isedit'] == 'G')
									{
										if($cek['flaglunas']=='BB'){
											static::deleteperid($conn,$cek['idtagihan']);
											}
										}
									}		
											$rec = array();
											
											$rec['jenistagihan'] = $v['jenistagihan'];
											$rec['nim'] = $r_nim;
											$rec['tgltagihan'] = date('Y-m-d');
											$rec['periode'] = $r_periode;
											$rec['isangsur'] = 0;
											$rec['isedit'] = 'G';
											$rec['flaglunas'] = 'BB';
											$pengali = 1;
											
											if($v['aturanperiode']=='A')
												$rec['nominaltagihan'] = $pengali * $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
											else
												$rec['nominaltagihan'] = $pengali * $tarif[$r_periode][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
												
											if ($jumlahangsur > 1)
											$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);

											for ($a=1; $a<=$jumlahangsur; $a++){
												$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);

												$err = static::insertRecord($conn,$rec);
												
												}
					}else{ 	

						$frekuensi = mAkademik::getFrekuensibulanan($conn,$r_periode);
						foreach($frekuensi as $i => $val){
								$cek = $conn->getRow("select * from h2h.ke_tagihan where nim = '$r_nim' 
											and jenistagihan = '".$v['jenistagihan']."'
											and bulantahun = '$i'");
							if($cek){
								if($cek['isedit'] == 'G')
									{
										if($cek['flaglunas']=='BB'){
										static::deleteperid($conn,$cek['idtagihan']);
										}
									}
								}	
										$rec = array();
										$rec['jenistagihan'] = $v['jenistagihan'];
										$rec['nim'] = $r_nim;
										$rec['tgltagihan'] = date('Y-m-d');
										$rec['periode'] = $r_periode;
										$rec['bulantahun'] = $i;
										$rec['isangsur'] = 0;
										$rec['isedit'] = 'G';
										$rec['flaglunas'] = 'BB';
										$pengali = 1;
										
										if($v['aturanperiode']=='A')
											$rec['nominaltagihan'] = $pengali * $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
										else
											$rec['nominaltagihan'] = $pengali * $tarif[$r_periode][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];

											if ($jumlahangsur > 1)
											$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);

											for ($a=1; $a<=$jumlahangsur; $a++){
												$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);

												$err = static::insertRecord($conn,$rec);
												
												}
						}
					}
				}
			}
			return $err;
		}

		function getTagihanFromPembayaran($conn, $idpembayaran){
			$rs = $conn->getOne("select idtagihan from h2h.ke_pembayarandetail where idpembayaran = '$idpembayaran'");
			return $rs;
		}
		function cekTagihan($conn, $jenistagihan,$jenis, $nomor, $periode){
			$sql = "select idtagihan from h2h.ke_tagihan where 1=1";
			
			if ($jenis == 'nim')
				$sql.=" AND nim  = '$nomor' ";
			elseif ($jenis == 'nopendaftar')
				$sql.=" AND nopendaftar = '$nomor' ";
				
			$sql.=" AND jenistagihan = '$jenistagihan'";
			$sql.=" AND periode = '$periode'";
			
			$rs = $conn->Execute($sql);
				$count=array();
				while($row = $rs->fetchRow()){
					$count[]=1;
					}
			return count($count);
		}
		
		// manajemen tagihan
		
		function getListTagihanPeriode($conn,$nim,$periode,$jenis=null) {
			if(empty($nim) or empty($periode))
				return array();
			
			$sql = "select t.*, j.namajenistagihan, v.nominaldeposit, v.nominalpakai from ".static::table()." t
					join ".static::table('lv_jenistagihan')." j on t.jenistagihan = j.jenistagihan
					left join ".static::table('ke_deposit')." v on t.idtagihan = v.idtagihan
					where coalesce(t.nim,t.nopendaftar) = ".Query::escape($nim)."
					and t.periode = ".Query::escape($periode);
			
			if(!empty($jenis))
				$sql .= " and t.jenistagihan = ".Query::escape($jenis);
			
			$sql .= " order by t.jenistagihan, t.angsuranke";
			
			return $conn->GetArray($sql);
		}
		
		function joinTagihan($conn,$arrid) {
			if(empty($arrid))
				return array(true,'Pilih tagihan yang akan digabungkan terlebih dahulu');
			
			$inid = "'".implode("','",$arrid)."'";
			
			// pakai tabel temporari deh
			$sql = "select min(idtagihan) as idtagihan, max(jenistagihan) as jenistagihan, max(nopendaftar) as nopendaftar,
					max(nim) as nim, min(tgltagihan) as tgltagihan, max(tgldeadline) as tgldeadline,
					sum(nominaltagihan) as nominaltagihan, min(periode) as periode, min(bulantahun) as bulantahun,
					sum(potongan) as potongan, sum(denda) as denda, min(angsuranke) as angsuranke into temp temp_jointagihan
					from ".static::table()." where idtagihan in ($inid) and flaglunas = 'BB' and nominalbayar = 0";
			$conn->Execute($sql);
			$err = $conn->ErrorNo();
			
			// hapus tagihan
			if(!$err)
				$err = Query::qDelete($conn,static::table(),"idtagihan in ($inid) and flaglunas = 'BB' and nominalbayar = 0",false);
			
			// baru insert
			if(!$err) {
				$sql = "insert into ".static::table()." (idtagihan,jenistagihan,nopendaftar,nim,tgltagihan,tgldeadline,
						nominaltagihan,periode,bulantahun,potongan,denda,angsuranke,isangsur,isedit,flaglunas,t_updateuser,t_updatetime,t_updateip)
						select idtagihan,jenistagihan,nopendaftar,nim,tgltagihan,tgldeadline,
						nominaltagihan,periode,bulantahun,potongan,denda,angsuranke,0,'E','BB',".Query::logInsert()."
						from temp_jointagihan";
				$conn->Execute($sql);
				$err = $conn->ErrorNo();
			}
			
			$err = ($err ? true : false);
			$msg = 'Penggabungan tagihan '.($err ? 'gagal' : 'berhasil');
			
			return array($err,$msg);
		}
		
		function splitTagihan($conn,$id,$nominal,$tgl=null,$tgldl=null) {
			// cek tagihan
			$row = static::getData($conn,$id);
			if($row['flaglunas'] != 'BB' or $row['nominalbayar'] > 0 or $row['nominaltagihan'] < $nominal)
				return array(true,'Tagihan tidak bisa dibelah');
			
			// cari angsuran terakhir
			$nim = (empty($row['nim']) ? $row['nopendaftar'] : $row['nim']);
			
			$sql = "select max(angsuranke) from ".static::table()."
					where coalesce(nim,nopendaftar) = '".$nim."'
					and jenistagihan = '".$row['jenistagihan']."' and periode = '".$row['periode']."'";
			$angsuranke = (int)$conn->GetOne($sql) + 1;
			
			// dapatkan kode tagihan
			require_once(Route::getModelPath('jenistagihan'));
			
			$kodetagihan = mJenistagihan::getKodeTagihan($conn,$row['jenistagihan']);
			$idtagihan = static::getIDTagihan($kodetagihan,$row['periode'],$nim,$angsuranke);
			
			// insert data
			if(!empty($tgl)) {
				list($y,$m,$d) = explode('-',$tgl);
				if(checkdate((int)$m,(int)$d,(int)$y))
					$tgl = "to_date('$y$m$d','YYYYMMDD')";
				else
					unset($tgl);
			}
			if(!empty($tgldl)) {
				list($y,$m,$d) = explode('-',$tgldl);
				if(checkdate((int)$m,(int)$d,(int)$y))
					$tgldl = "to_date('$y$m$d','YYYYMMDD')";
				else
					unset($tgldl);
			}
			
			if(empty($tgl) and !empty($tgldl))
				$tgl = $tgldl.'-10';
			else if(!empty($tgl) and empty($tgldl))
				$tgldl = $tgl.'+10';
			
			if(empty($tgl))
				$tgl = 'tgltagihan';
			if(empty($tgldl))
				$tgldl = 'tgldeadline';
			
			$sql = "insert into ".static::table()." (idtagihan,jenistagihan,nopendaftar,nim,tgltagihan,tgldeadline,nominaltagihan,
					periode,bulantahun,angsuranke,isangsur,isedit,flaglunas,t_updateuser,t_updatetime,t_updateip)
					select '$idtagihan',jenistagihan,nopendaftar,nim,$tgl,$tgldl,nominaltagihan-$nominal,
					periode,bulantahun,$angsuranke,0,'E',flaglunas,".Query::logInsert()."
					from ".static::table()." where idtagihan = ".Query::escape($id);
			$conn->Execute($sql);
			$err = $conn->ErrorNo();
			
			// dan update
			if(!$err) {
				$record = array();
				$record['nominaltagihan'] = $nominal;
				$record['isedit'] = 'E';
				
				$err = static::updateRecord($conn,$record,$id);
			}
			
			$err = ($err ? true : false);
			$msg = 'Pembelahan tagihan '.($err ? 'gagal' : 'berhasil');
			
			return array($err,$msg);
		}
		
		// potongan
		
		function wherePotongan($periode,$tahunbulan=null,$jenistagihan=null,$arrnim=null,$arrnop=null) {
			$sql = "t.periode = ".Query::escape($periode)." and t.flaglunas = 'BB'";
			if(!empty($tahunbulan))
				$sql .= " and t.bulantahun = ".Query::escape($tahunbulan);
			if(!empty($jenistagihan))
				$sql .= " and t.jenistagihan = ".Query::escape($jenistagihan);
			if(isset($arrnim)) {
				if(!empty($arrnim)) {
					$arresc = array();
					foreach($arrnim as $nim)
						$arresc[] = Query::escape($nim);
					
					$sql .= " and t.nim in (".implode(',',$arresc).")";
				}
				else
					$sql .= " and t.nim = ''";
			}
			if(isset($arrnop)) {
				if(!empty($arrnop)) {
					$arresc = array();
					foreach($arrnop as $nim)
						$arresc[] = Query::escape($nim);
					
					$sql .= " and t.nopendaftar in (".implode(',',$arresc).")";
				}
				else
					$sql .= " and t.nopendaftar = ''";
			}
			
			return $sql;
		}
		
		function deletePotongan($conn,$periode,$tahunbulan=null,$jenistagihan=null,$arrnim=null,$arrnop=null) {
			$sql = "update ".static::table()." t set potongan = 0, ".Query::logUpdate()."
					where ".static::wherePotongan($periode,$tahunbulan,$jenistagihan,$arrnim,$arrnop);
			$conn->Execute($sql);
			
			return $conn->ErrorNo();
		}
		
		function generatePotongan($conn,$periode,$tahunbulan=null,$jenistagihan=null,$arrnim=null,$arrnop=null) {
			$err = static::deletePotongan($conn,$periode,$tahunbulan,$jenistagihan,$arrnim,$arrnop);
			
			if(!$err) {
				$sql = "update ".static::table()." t set potongan =
							case when p.ispersen = 1 then ((p.jumlahpotongan/100) * (t.nominaltagihan-t.potongan))
							else p.jumlahpotongan end
						from
							akademik.ak_beasiswa b,
							akademik.ak_penerimabeasiswa m,
							".static::table('ke_potonganbeasiswa')." p
						where
							t.nim = m.nim and t.jenistagihan = p.jenistagihan
							and m.idbeasiswa = b.idbeasiswa and b.kodekategori = p.kodekategori
							and (b.periodeawal is null or b.periodeawal <= ".Query::escape($periode).")
							and (b.periodeakhir is null or b.periodeakhir >= ".Query::escape($periode).")
							and ".static::wherePotongan($periode,$tahunbulan,$jenistagihan,$arrnim,$arrnop);
				$conn->Execute($sql);
				
				$err = $conn->ErrorNo();
			}
			
			return $err;
		}
		
		// denda
		
		function getListJenisDenda($conn) {
			$data = array('AKTIF', 'TELAT');
			
			// diubah menjadi key-value
			$sql = "select jenistagihan, namajenistagihan from ".static::table('lv_jenistagihan').
					" where jenistagihan in ('".implode("','",$data)."') order by jenistagihan";
			$rs = $conn->Execute($sql);
			
			$data = array(self::dendaDefault => 'Terlambat Bayar');
			while($row = $rs->FetchRow())
				$data[$row['jenistagihan']] = $row['namajenistagihan'];
			
			return $data;
		}
		
		function getListDenda($conn,$periode) {
			$a_denda = self::getListJenisDenda($conn);
			$default = self::dendaDefault;
			
			$a_jenis = array();
			foreach($a_denda as $k => $v) {
				if($k == $default)
					continue;
				
				$a_jenis[$k] = $k;
			}
			
			$sql = "select u.kodeunit, t.jenistagihan, t.nominaltagihan-t.potongan as jumlah, t.denda, t.flaglunas
					from ".static::table()." t
					left join akademik.ms_mahasiswa m on t.nim = m.nim
					left join pendaftaran.pd_pendaftar p on t.nopendaftar = p.nopendaftar
					join gate.ms_unit u on u.kodeunit = coalesce(m.kodeunit,p.pilihanditerima)
					where t.periode = ".Query::escape($periode)."
					and (t.jenistagihan in ('".implode("','",$a_jenis)."') or t.denda > 0)";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				$kodeunit = $row['kodeunit'];
				$jenis = $row['jenistagihan'];
				
				$jumlah = (float)$row['jumlah'];
				if($a_jenis[$jenis]) {
					$data['cek'][$kodeunit][$jenis] = true;
					
					$data['tagihan'][$kodeunit][$jenis] += $jumlah;
					if($row['flaglunas'] == 'L')
						$data['bayar'][$kodeunit][$jenis] += $jumlah;
				}
				
				$denda = (float)$row['denda'];
				if(!empty($denda)) {
					$data['cek'][$kodeunit][$default] = true;
					
					$data['tagihan'][$kodeunit][$default] += $denda;
					if($row['flaglunas'] == 'L')
						$data['bayar'][$kodeunit][$default] += $denda;
				}
			}
			
			return $data;
		}
		
		function updateDenda($conn,$where) {
			$sqld = "update ".static::table()." t set denda = (j.nominaldenda/100) * t.nominaltagihan
					from ".static::table('lv_jenistagihan')." j
					where t.jenistagihan = j.jenistagihan and t.flaglunas = 'BB'
					and j.nominaldenda is not null";
					
			if(!empty($where['kodeunit'])) {
				$sqlmhs = mAkademik::sqlMhs($conn,$where);
				$sqlpen = mAkademik::sqlpendaftar($conn,$where);
				
				$sqld .= " and (t.nim in (".$sqlmhs.") or t.nopendaftar in (".$sqlpen."))";
			}
			
			// denda dengan tanggal deadline
			$sql = $sqld;
			$sql .= " and t.tgldeadline is not null and t.tgldeadline < current_date";
			
			if(!empty($where['periodetagihan']))
				$sql .= " and t.periode = ".Query::escape($where['periodetagihan']);
			
			$conn->Execute($sql);
			$err = $conn->ErrorNo();
			
			// denda dengan periode, tidak jadi
			
			return $err;
		}
		
		function deleteDenda($conn,$where) {
			$sql = "update ".static::table()." set denda = 0 where flaglunas = 'BB'";
			if(!empty($where['kodeunit'])) {
				$sqlmhs = mAkademik::sqlMhs($conn,$where);
				$sqlpen = mAkademik::sqlpendaftar($conn,$where);
				
				$sql .= " and (nim in (".$sqlmhs.") or nopendaftar in (".$sqlpen."))";
			}
			if(!empty($where['periodetagihan']))
				$sql .= " and periode = ".Query::escape($where['periodetagihan']);
			
			$conn->Execute($sql);
			
			return $conn->ErrorNo();
		}
		
		function generateTagihanDenda($conn,$where) {
			// include
			require_once(Route::getModelPath('tarif'));
			
			// filter wajib
			$periode = $where['periodetagihan'];
			
			// filter opsional
			$jenis = $where['jenistagihan'];
			$kodeunit = $where['kodeunit'];
			
			// cek jenis tagihan
			$a_denda = self::getListJenisDenda($conn);
			$default = self::dendaDefault;
			
			// cek data tidak termasuk tagihan denda
			if(!empty($jenis) and (empty($a_denda[$jenis]) or $jenis == $default))
				return true;
			
			if(empty($jenis)) {
				$a_jenis = array();
				foreach($a_denda as $t_denda) {
					if($t_denda != $default)
						$a_jenis[] = $t_denda;
				}
			}
			else
				$a_jenis = array($jenis);
			
			$indenda = array();
			$a_denda = $a_jenis;
			foreach($a_denda as $t_denda)
				$indenda[] = Query::escape($t_denda);
			$indenda = implode(',',$indenda);
			
			// ambil info jenis tagihan
			$sql = "select jenistagihan,kodetagihan,tgldeadline from ".static::table('lv_jenistagihan')."
					where jenistagihan in ($indenda)";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()) {
				$jenis = $row['jenistagihan'];
				$kodetagihan = $row['kodetagihan'];
				$tgldeadline = $row['tgldeadline'];
			
				// ambil tarif, $kodeunit bisa kosong
				$tarif = mTarif::getArraytarif($conn,null,null,$kodeunit,$jenis);
				if($tarif) {
					foreach($tarif as $k => $v)
						$tarif[trim($v['periodetarif'])][trim($v['jalurpenerimaan'])][trim($v['gelombang'])][trim($v['sistemkuliah'])] = $v['nominaltarif'];						
				}
			
				// cek jenis tagihan
				if($jenis == 'AKTIF') {
					$tahun = substr($periode,0,4);
					$semester = substr($periode,-1);
					
					if($semester == '1') {
						$tahun--;
						$semester = '2';
					}
					else
						$semester = '1';
					
					$periodesebelum = $tahun.$semester;
					
					$sql = "select m.nim, m.periodemasuk, m.jalurpenerimaan, m.gelombang, m.sistemkuliah
							from akademik.ak_perwalian w
							join akademik.ms_mahasiswa m on m.nim = w.nim
							where w.periode = ".Query::escape($periodesebelum)." and w.statusmhs = 'T'";
					if(!empty($kodeunit))
						$sql .= " and m.kodeunit = ".Query::escape($kodeunit);
				}
				else if($jenis == 'TELAT') {
					$sql = "select m.nim, m.periodemasuk, m.jalurpenerimaan, m.gelombang, m.sistemkuliah
							from akademik.ak_perwalian w
							join akademik.ms_mahasiswa m on m.nim = w.nim
							where w.periode = ".Query::escape($periode)." and w.statusmhs = 'T'";
					if(!empty($kodeunit))
						$sql .= " and m.kodeunit = ".Query::escape($kodeunit);
				}
				else
					continue;
				
				$rs = $conn->Execute($sql);
				
				$err = 0;
				while($row = $rs->FetchRow()) {
					// cek tagihan
					$sql = "select 1 from ".static::table()."
							where periode = ".Query::escape($periode)."
							and jenistagihan = ".Query::escape($jenis);
					
					if(!empty($row['nopendaftar'])) {
						$id = $row['nopendaftar'];
						$sql .= " and nopendaftar = ".Query::escape($row['nopendaftar']);
					}
					else if(!empty($row['nim'])) {
						$id = $row['nim'];
						$sql .= " and nim = ".Query::escape($row['nim']);
					}
					else
						continue;
					
					$cek = $conn->GetOne($sql);
					if(!empty($cek))
						continue;
					
					// cek tarif
					$jmltarif = (float)$tarif[$row['periodemasuk']][$row['jalurpenerimaan']][$row['gelombang']][$row['sistemkuliah']];
					if(empty($jmltarif))
						continue;
					
					$record = array();
					$record['idtagihan'] = self::getIDTagihan($kodetagihan,$periode,$id);
					$record['jenistagihan'] = $jenis;
					$record['nopendaftar'] = CStr::cStrNull($row['nopendaftar']);
					$record['nim'] = CStr::cStrNull($row['nim']);
					$record['tgltagihan'] = date('Y-m-d');
					$record['nominaltagihan'] = $jmltarif;
					$record['periode'] = $periode;
					$record['bulantahun'] = date('Ym');
					$record['isangsur'] = 0;
					$record['isedit'] = 'G';
					$record['flaglunas'] = 'BB';
					
					if(!empty($tgldeadline)) {
						if($tgldeadline < date('j'))
							$record['tgldeadline'] = date('Y-m-d',mktime(0,0,0,date('n')+1,$tgldeadline,date('Y')));
						else
							$record['tgldeadline'] = date('Y-m-').str_pad($tgldeadline,2,'0',STR_PAD_LEFT);
					}
					
					$err = mTagihan::insertRecord($conn,$record);
					if($err)
						break 2; // break dari while luarnya juga
				}
			}
			
			return $err;
		}
		
		// generate tagihan baru
		
		function getListJenisTagihanGenerate($conn,$periode,$sistem,$isawal=null) {
			$semester = substr($periode,-1);
			if($semester == '0')
				return array();
			else if($semester == '3')
				$ispendek = true;
			else
				$ispendek = false;
			
			if($sistem == 'P' || $sistem == 'C' || $sistem == 'H')
				$col = 'isparalel';
			else
				$col = 'isreguler';
			
			$sql = "select jenistagihan, namajenistagihan, frekuensitagihan, ismaba, ismala
					from ".static::table('lv_jenistagihan')."
					where coalesce($col,0) <> 0 and frekuensitagihan in ('A','S','P')
					order by kodetagihan";
			$rows = $conn->GetArray($sql);
			
			$data = array();
			foreach($rows as $row) {
				$cek1 = (!isset($isawal) and !$ispendek and $row['frekuensitagihan'] != 'P');
				$cek2 = (!isset($isawal) and $ispendek and $row['frekuensitagihan'] == 'P');
				$cek3 = ($isawal and !empty($row['ismaba']) and $row['frekuensitagihan'] == 'A');
				$cek4 = (!$isawal and !empty($row['ismala']) and !$ispendek and $row['frekuensitagihan'] == 'S');
				$cek5 = (!$isawal and !empty($row['ismala']) and $ispendek and $row['frekuensitagihan'] == 'P');
				
				if($cek1 or $cek2 or $cek3 or $cek4 or $cek5)
					$data[$row['jenistagihan']] = $row['namajenistagihan'];
			}
			
			// jika tagihan pendaftar tidak menemukan jenisnya
			if($isawal and empty($data)) {
				foreach($rows as $row) {
					$cek1 = (!empty($row['ismaba']) and !$ispendek and $row['frekuensitagihan'] == 'S');
					$cek2 = (!empty($row['ismaba']) and $ispendek and $row['frekuensitagihan'] == 'P');
					
					if($cek1 or $cek2)
						$data[$row['jenistagihan']] = $row['namajenistagihan'];
				}
			}
			
			return $data;
		}
		
		function getListTagihanGenerate($conn,$filter,$periode,$jenis=null) {
			// include
			require_once(Route::getModelPath('akademik'));
			
			if(empty($jenis))
				$jenis = static::getListJenisTagihanGenerate($conn,$periode,$filter['sistemkuliah'],$filter['ispendaftar']);
			
			$injenis = array();
			foreach($jenis as $k => $v)
				$injenis[] = $k;
			$injenis = "'".implode("','",$injenis)."'";
			
			$sql = "select m.kodeunit, t.jenistagihan, sum(t.nominaltagihan) as nominaltagihan, sum(t.potongan) as potongan,
						sum(t.nominalbayar) as nominalbayar, sum(coalesce(v.nominaldeposit,0)) as nominaldeposit
					from ".static::table()." t
					join (".mAkademik::sqlMhsPendaftar($filter).") m on coalesce(t.nim,t.nopendaftar) = m.nim
					left join ".static::table('ke_deposit')." v on t.idtagihan = v.idtagihan
					where t.periode = ".Query::escape($periode)." and t.jenistagihan in ($injenis)
					group by m.kodeunit, t.jenistagihan";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['kodeunit']][$row['jenistagihan']] = array('tagihan' => $row['nominaltagihan'], 'bayar' => $row['nominalbayar'], 'potongan' => $row['potongan']+$row['nominaldeposit']);
			
			return $data;
		}
		
		function getListTagihanMhsGenerate($conn,$filter,$periode,$jenis=null) {
			if(empty($jenis))
				$jenis = static::getListJenisTagihanGenerate($conn,$periode,$filter['sistemkuliah'],$filter['ispendaftar']);
			
			$injenis = array();
			foreach($jenis as $k => $v)
				$injenis[] = $k;
			$injenis = "'".implode("','",$injenis)."'";
			
			$sql = "select coalesce(t.nim,t.nopendaftar) as nim, t.jenistagihan, t.idtagihan, t.nominaltagihan
					from ".static::table()." t
					join (".mAkademik::sqlMhsPendaftar($filter).") m on coalesce(t.nim,t.nopendaftar) = m.nim
					where t.periode = ".Query::escape($periode)." and t.jenistagihan in ($injenis)";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['nim']][$row['jenistagihan']][$row['idtagihan']] = (float)$row['nominaltagihan'];
			
			return $data;
		}
		
		function voidTagihan($conn,$filter,$periode,$jenis=null,$nonaktif=false) {
			if(empty($jenis))
				$jenis = static::getListJenisTagihanGenerate($conn,$periode,$filter['sistemkuliah'],$filter['ispendaftar']);
			
			$injenis = array();
			foreach($jenis as $k => $v)
				$injenis[] = $k;
			$injenis = "'".implode("','",$injenis)."'";
			
			// tambah filter
			if($nonaktif) {
				$filter['ispendaftar'] = false;
				$filter['periode'] = $periode;
				$filter['isnonaktif'] = true;
			}
			
			// include
			require_once(Route::getModelPath('deposit'));
			
			$eperiode = Query::escape($periode);
			
			$sql = "t.flaglunas = 'BB' and t.periode = $eperiode and t.jenistagihan in ($injenis) and exists
					(select 1 from (".mAkademik::sqlMhsPendaftar($filter).") m where coalesce(t.nim,t.nopendaftar) = m.nim)";
			/* if(empty($nonaktif))
				$sql .= " and t.isedit = 'G'"; */
			
			// ambil jumlahnya dulu
			$jml = $conn->GetOne("select count(distinct(coalesce(t.nim,t.nopendaftar))) from h2h.ke_tagihan t where ".$sql);
			
			// baru delete
			$err = Query::qDelete($conn,static::table().' t',$sql,false);
			
			// untuk void tagihan non aktif
			if($nonaktif) {
				// membuat deposit dari tagihan terbayar mhs
				if(!$err) {
					$sql = "insert into ".static::table('ke_deposit')." (nim,tgldeposit,nominaldeposit,periode,status,jenisdeposit)
							select t.nim, current_date, sum(d.nominalbayar), $eperiode, '-1', 'D'
							from ".static::table()." t
							join ".static::table('ke_pembayarandetail')." d on d.idtagihan = t.idtagihan and d.iddeposit is null
							join ".static::table('ke_pembayaran')." b on d.idpembayaran = b.idpembayaran and b.flagbatal = '0'
							join (".mAkademik::sqlMhsPendaftar($filter).") m on coalesce(t.nim,t.nopendaftar) = m.nim
							where t.isvalid = -1 and t.flaglunas = 'L' and t.periode = $eperiode and t.jenistagihan in ($injenis)
							group by t.nim";
					$conn->Execute($sql);
					$err = $conn->ErrorNo();
				}
				
				// set tagihan invalid
				if(!$err) {
					$sql = "update ".static::table()." t set isvalid = 0
							from (".mAkademik::sqlMhsPendaftar($filter).") m
							where coalesce(t.nim,t.nopendaftar) = m.nim and t.isvalid = -1 and
							t.flaglunas = 'L' and t.periode = $eperiode and t.jenistagihan in ($injenis)";
					$conn->Execute($sql);
					$err = $conn->ErrorNo();
				}
			}
			
			// void voucher beasiswa
			if(!$err)
				$err = mDeposit::voidVoucher($conn,$filter,$periode,$jenis);
			
			$err = ($err ? true : false);
			$msg = 'Penghapusan tagihan '.($err ? 'gagal' : 'berhasil');
			
			return array($err,$msg,$jml);
		}
		
		function voidTagihanNA($conn,$filter,$periode,$jenis=null) {
			return static::voidTagihan($conn,$filter,$periode,$jenis,true);
		}
		
		function generateTagihan($conn,$filter,$periode,$jenis=null) {
			if(empty($jenis))
				$jenis = static::getListJenisTagihanGenerate($conn,$periode,$filter['sistemkuliah'],$filter['ispendaftar']);
			// include
			require_once(Route::getModelPath('akademik'));
			require_once(Route::getModelPath('deposit'));
			require_once(Route::getModelPath('jenistagihan'));
			require_once(Route::getModelPath('loggenerate'));
			require_once(Route::getModelPath('tarif'));
			require_once(Route::getModelPath('tarifreg'));
			
			// ambil jenis tagihan, index 0 awal, index 1 rutin
			$rows = mJenistagihan::getArrayTagRutin($conn);
			
			$datakrs = array();
			$datajenis = array();
			foreach($rows as $row) {
				$t_jenis = $row['jenistagihan'];
				if(empty($jenis[$t_jenis]))
					continue;
				
				// cek tagihan yang termasuk awal dan per semester
				/* if($row['frekuensitagihan'] == 'A')
					$datajenis[0][$t_jenis] = $row;
				else
					$datajenis[1][$t_jenis] = $row; */
				
				if(!empty($row['ismaba']))
					$datajenis[0][$t_jenis] = $row;
				if(!empty($row['ismala']))
					$datajenis[1][$t_jenis] = $row;
				
				// cek apa ada tagihan per sks
				if($row['issks'] == '1')
					$datakrs = mAkademik::getDatakrsall($conn,$periode,$nim); // tidak ada data tipekuliah
			}
			
			// cek pendaftar, ambil index ke 0 saja
			if(isset($filter['ispendaftar'])) {
				if(empty($filter['ispendaftar']))
					unset($datajenis[0]);
				else
					unset($datajenis[1]);
			}
			
			// di-void dulu
			list($err) = static::voidTagihan($conn,$filter,$periode,$jenis);
			
			// generate tagihan
			$jml = 0;
			if(!$err) {
				// tambah filter
				$filter['periodetagihan'] = $periode;
				$filter['periodesebelumnya'] = Akademik::getPeriodeSebelumnya($periode);
				
				// ambil tagihan yang tidak ter-void
				$tagihan = static::getListTagihanMhsGenerate($conn,$filter,$periode,$jenis);
				
				// ambil data periode
				$infoperiode = mAkademik::getDataPeriode($conn,$periode);
				if(empty($infoperiode['bulanawal']))
					$infoperiode['bulanawal'] = Akademik::getBulanAwalPeriode($periode);
				
				foreach($datajenis as $idx => $infojenis) {
					if(!empty($infojenis)) {
						if($idx == 0) {
							$arr_mhs = mAkademik::getListMahasiswaBaru($conn,$filter); // pendaftar dan mahasiswa baru
						}
						else {
							// sisipkan proses pembuatan perwalian
							mAkademik::generatePerwalian($conn,$periode,$filter);
							
							$arr_mhs = mAkademik::getArraymhsperwalian($conn,$filter); // mahasiswa lama
						}

						$jml += count($arr_mhs);
						
						foreach($infojenis as $t_jenis => $infojt) {
							// untuk tagihan tahunan hanya pada semester gasal
							if($infojt['frekuensitagihan'] == 'T' and substr($periode,-1) != '1')
								continue;
							
							// ambil tarif
							$tarif = array();
							if($t_jenis == mTarifReg::jenisTagihan) {
								$arr_tarif = mTarifReg::getArraytarif($conn,$periode,'',$filter['kodeunit'],'');
								if($arr_tarif) {
									foreach($arr_tarif as $i => $v)
										$tarif[$v['periodetarif']][$v['jalurpenerimaan']][$v['gelombang']][$v['sistemkuliah']][$v['kodeunit']][$v['angsuranke']] = array('nominaltarif' => $v['nominaltarif'], 'tgldeadline' => $v['tgldeadline']); // hanya untuk reguler				
								}
							}
							else {
								$arr_tarif = mTarif::getArraytarif($conn,'','',$filter['kodeunit'],$t_jenis,$filter['sistemkuliah'],'');
								if($arr_tarif) {
									foreach($arr_tarif as $i => $v)
										$tarif[$v['periodetarif']][$v['jalurpenerimaan']][$v['gelombang']][$v['sistemkuliah']][$v['kodeunit']] = $v['nominaltarif'];					
								}
							}
							foreach($arr_mhs as $i => $mhs) {
								// per semester tidak termasuk mahasiswa baru
								if($idx == 1 and $mhs['periodemasuk'] == $periode)
									continue;
								
								// cek untuk lulusan smu atau d3
								if(
								   (empty($mhs['mhstransfer']) and empty($infojt['issmu'])) or
								   (!empty($mhs['mhstransfer']) and empty($infojt['isd3']))
								)
									continue;
								
								$record = array();
								$record['jenistagihan'] = $t_jenis;
								$record['periode'] = $periode;
								$record['isangsur'] = 0;
								$record['isedit'] = 'G';
								$record['flaglunas'] = 'BB';
								
								if($mhs['jenisdata'] == 'pendaftar')
									$record['nopendaftar'] = $mhs['nim'];
								else
									$record['nim'] = $mhs['nim'];
								
								// cek sks
								if($infojt['issks'] == '1') {
									$pengali = $datakrs[$mhs['nim']];
									$record['jumlahsks'] = $pengali;
								}
								else
									$pengali = 1;
								
								// cek dengan tagihan existing
								$tertagih = 0;
								if(!empty($tagihan[$mhs['nim']][$t_jenis])) {
									foreach($tagihan[$mhs['nim']][$t_jenis] as $k => $v)
										$tertagih += $v;
								}
								
								// ambil tarif
								$datatarif = $tarif[$mhs['periodemasuk']][$mhs['jalurpenerimaan']][$mhs['gelombang']][$mhs['sistemkuliah']][$mhs['kodeunit']];
								
								if(!empty($datatarif) and !is_array($datatarif)) {
									$datatarif *= $pengali;
									if($datatarif < 0)
										$datatarif = 0;
								}

								if(!empty($datatarif)) {
									// jika bukan array, cek jumlahangsur
									if(!is_array($datatarif)) {
										$jumlahangsur = ($mhs['periodemasuk'] >= '20171' ? (int)$infojt['jumlahangsur2017'] : (int)$infojt['jumlahangsur']) ;
										if(empty($jumlahangsur))
											$jumlahangsur = 1;
										
										$nominal = $datatarif/$jumlahangsur;
										
										$datatarif = array();
										for ($a=1; $a<=$jumlahangsur; $a++)
											$datatarif[$a] = array('nominaltarif' => $nominal);
									}
									else
										$jumlahangsur = count($datatarif);
									
									foreach($datatarif as $a => $infotarif) {
										// cek dengan tagihan existing
										$nominal = $infotarif['nominaltarif'];
										if($nominal <= $tertagih) {
											$tertagih -= $nominal;
											$nominal = 0;
										}
										else {
											$nominal -= $tertagih;
											$tertagih = 0;
										}
										
										if(empty($nominal))
											continue;
										
										$record['nominaltagihan'] = $nominal;
										
										// set tgl
										if(empty($infotarif['tgldeadline'])) {
											if($filter['sistemkuliah'] == 'R')
												$record['tgldeadline'] = static::getTglDeadlineReguler($infoperiode,$a);
											else
												$record['tgldeadline'] = static::getTglDeadlineBulanan($infoperiode['bulanawal'],$a);
										}
										else
											$record['tgldeadline'] = $infotarif['tgldeadline'];
											
										$record['tgltagihan'] = static::getTglTagihanByDeadline($record['tgldeadline']);
										$record['bulantahun'] = substr($record['tgltagihan'],0,4).substr($record['tgltagihan'],5,2);
										
										// cek id tagihan
										$b = $a-1;
										do {
											$idtagihan = static::getIDTagihan($infojt['kodetagihan'],$record['periode'],$mhs['nim'],++$b);
										}
										while(isset($tagihan[$mhs['nim']][$t_jenis][$idtagihan]));
										
										$record['idtagihan'] = $idtagihan;
										$record['angsuranke'] = $b;
										
										$tagihan[$mhs['nim']][$t_jenis][$idtagihan] = $nominal;
										$err = static::insertRecord($conn,$record);
										if($err)
											break 4; // keluar 4 foreach :D
									}
								} //else
									// list($err,$msginfo) = array(true,'Tarif tidak ditemukan');
							}
						}
					}
				}
			}
			
			// buat voucher beasiswa
			if(!$err)
				$err = mDeposit::generateVoucher($conn,$filter,$periode,$jenis);
			
			$err = ($err ? true : false);
			$msg = 'Generate tagihan '.($err ? 'gagal' : 'berhasil');
			if ($msginfo)
			$msg.= '<br>'.$msginfo;
			return array($err,$msg,$jml);
		}
		
		// pelengkap tagihan
		
		function getIDTagihan($kodetagihan,$periode,$id,$angsuranke=1) {
			return str_pad($kodetagihan,2,'0',STR_PAD_LEFT).$periode.str_pad($angsuranke,2,'0',STR_PAD_LEFT).str_pad($id,15,'0',STR_PAD_LEFT);
		}
		
		function getTglDepan($ymd,$hari=0) {
			list($y,$m,$d) = explode('-',$ymd);
			
			$time = mktime(0,0,0,(int)$m,(int)$d+$hari,(int)$y);
			
			return date('Y-m-d',$time);
		}
		
		function getTglDeadlineBulanan($bulanawal,$ke=1,$tgl=10) {
			$tahun = (int)substr($bulanawal,0,4);
			$bulan = (int)substr($bulanawal,4,2);
			
			for($i=1;$i<$ke;$i++) {
				if($bulan == 12) {
					$bulan = 0;
					$tahun++;
				}
				
				$bulan++;
			}
			
			return $tahun.'-'.str_pad($bulan,2,'0',STR_PAD_LEFT).'-'.$tgl;
		}
		
		function getTglDeadlineReguler($infoperiode,$ke=1) {
			if($ke == 2 and !empty($infoperiode['tgluts']))
				return static::getTglDepan($infoperiode['tgluts'],-14);
			else if($ke == 3 and !empty($infoperiode['tgluas']))
				return static::getTglDepan($infoperiode['tgluas'],-14);
			else
				return static::getTglDeadlineBulanan($infoperiode['bulanawal'],$ke);
		}
		
		function getTglTagihanByDeadline($tgldeadline) {
			$tahun = substr($tgldeadline,0,4);
			$bulan = substr($tgldeadline,5,2);
			$tgl = substr($tgldeadline,8,2);
			
			// selisih sebulan
			/* $bulan--;
			if(empty($bulan)) {
				$bulan = 12;
				$tahun--;
			} */
			
			// ambil tanggal 1
			$tgl = '01';
			
			return $tahun.'-'.str_pad($bulan,2,'0',STR_PAD_LEFT).'-'.$tgl;
		}

		function getDataPiutang($conn,$periode){
			$sql = "select * from h2h.v_tagihanperiode where periode='$periode'";
			return $conn->getArray($sql);
		}
		function getStatusMhs($conn,$key)
		{
			$r_periode = Akademik::getPeriode();
			$sql = "select statusmhs from akademik.ak_perwalian where nim='$key' and periode='$r_periode'";
			return $conn->getOne($sql);
			
		}

		function updateStatusMhs($conn,$key){
			$r_periode = Akademik::getPeriode();
			$sql = "update akademik.ak_perwalian set statusmhs='K' where nim='$key' and periode='$r_periode'";
			$conn->Execute($sql);
			return $err->errorNo;
		}
	}
?>
