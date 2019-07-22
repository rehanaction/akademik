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
		
			$sql = " delete from ".static::table()." where ( isedit = 'G' and flaglunas = 'BB') and idtagihan = '$id' ";
	
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
	
	function getCounttagihan($conn,$periode,$jalur){
		
		$sql = "select count(*) as jml,
				m.jalurpenerimaan,
				m.sistemkuliah,
				t.periode,
				t.jenistagihan,
				coalesce(t.bulantahun,t.periode) as bulantahun,
				isedit,kodeunit
				 from ".static::table()." t
				join akademik.ms_mahasiswa m on m.nim = t.nim  
				where t. periode = '".$periode."' and m.jalurpenerimaan = '$jalur'
				group by m.jalurpenerimaan,
				m.sistemkuliah,
				t.periode,
				t.jenistagihan,
				coalesce(t.bulantahun,t.periode),
				isedit,kodeunit";
		return $conn->GetArray($sql);
			
		}
		
		function listQuery() { 
			$sql = "select * from (select 
					p.idtagihan,p.jenistagihan,p.tgltagihan, 
					p.tgldeadline,p.nominaltagihan,p.keterangan, p.periode,
					p.bulantahun, p.isangsur, p.isedit,p.flaglunas,p.jumlahsks,
					coalesce(p.nim,p.nopendaftar) as nim,
					coalesce(t.nama,td.nama) as nama,
					coalesce(t.jalurpenerimaan,td.jalurpenerimaan) as jalurpenerimaan,
					coalesce(t.sistemkuliah,t.sistemkuliah) as sistemkuliah,
					coalesce(t.kodeunit,td.pilihanditerima) as kodeunit,
					coalesce(u.namaunit,ud.namaunit) as namaunit 
					from h2h.ke_tagihan p 
					left JOIN akademik.ms_mahasiswa t ON p.nim=t.nim 
					left JOIN gate.ms_unit u ON u.kodeunit = t.kodeunit 
					left JOIN pendaftaran.pd_pendaftar td ON p.nopendaftar=td.nopendaftar 
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
					p.tgldeadline,p.nominaltagihan,p.keterangan, p.periode,
					p.bulantahun, p.isangsur, p.isedit,p.flaglunas,p.jumlahsks,
					p.nim,p.nopendaftar,
					coalesce(t.nama,td.nama) as nama,
					coalesce(t.jalurpenerimaan,td.jalurpenerimaan) as jalurpenerimaan,
					coalesce(t.sistemkuliah,t.sistemkuliah) as sistemkuliah,
					coalesce(t.kodeunit,td.pilihanditerima) as kodeunit,
					coalesce(u.namaunit,ud.namaunit) as namaunit 
					from h2h.ke_tagihan p 
					left JOIN akademik.ms_mahasiswa t ON p.nim=t.nim 
					left JOIN gate.ms_unit u ON u.kodeunit = t.kodeunit 
					left JOIN pendaftaran.pd_pendaftar td ON p.nopendaftar=td.nopendaftar 
					left JOIN gate.ms_unit ud ON ud.kodeunit = td.pilihanditerima where idtagihan = '$key' 
					";
			return $conn->getRow($sql);
		}
		
		function getInquiry($conn,$mhs='',$jenisperiode='',$jenistagihan=''){
			
			if($jenisperiode == 'setting')
			{
				require_once(Route::getModelPath('settingh2hdetail'));
				$settingdetail = mSettingh2hdetail::getDataSetting($conn);	
				
				$sql = "select t.*,j.* from ".static::table()." t
						join h2h.lv_jenistagihan j on j.jenistagihan = t.jenistagihan 
						 where (1=1)
						and (nim = '$mhs' or nopendaftar = '$mhs') and flaglunas <> 'L'"; 
				
				if($jenistagihan<>'')
					$sql .= " and t.jenistagihan = '".$jenistagihan."'";
					
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
				return $data;
			}
			else {
				$sql = "select t.*,j.* from ".static::table()." t
						join h2h.lv_jenistagihan j on j.jenistagihan = t.jenistagihan 
						 where (1=1)
						and (nim = '$mhs' or nopendaftar = '$mhs') and flaglunas <> 'L'"; 
				
				if($jenistagihan<>'')
					$sql .= " and t.jenistagihan = '".$jenistagihan."'";
					
				return $conn->getArray($sql);
			}
			
			
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
			
				if($cek == '1'){
					$record = array();
					$record['nim'] = $nim;
					$record['periode'] = $periodeyudisium;
					$record['tgltagihan'] = date('Y-m-d');
					$record['isedit'] = 'G';
					if($jenistagihan <> ''){
						$infojt = $arr_infojenistagihan[$jenistagihan];
						$panjang = 18-strlen($nim);
						$record['idtagihan'] = str_pad($infojt['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($periodeyudisium,$panjang,'0',STR_PAD_LEFT).$nim;
						$record['jenistagihan'] = $jenistagihan;
						$record['nominaltagihan'] = $tarif[$periodeyudisium][$kodeunit][$jenistagihan]['nominaltarif'];
						$err = static::insertRecord($conn,$record);
						}
					else{
							if($arr_tagihan)
							foreach($arr_tagihan as $i => $v){
								$record['jenistagihan'] = $v['jenistagihan'];
								$panjang = 18-strlen($nim);
								$record['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($periodeyudisium,$panjang,'0',STR_PAD_LEFT).$nim;
								$record['nominaltagihan'] = $tarif[$periodeyudisium][$kodeunit][$v['jenistagihan']]['nominaltarif'];
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
									$record['nim'] = $nim;
									$record['periode'] = $periodeyudisium;
									$record['tgltagihan'] = date('Y-m-d');
									$record['isedit'] = 'G';
									$record['jenistagihan'] = $jenistagihan;
									$record['nominaltagihan'] = $tarif[$periodeyudisium][$kodeunit][$jenistagihan]['nominaltarif'];
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
							if($jenistagihan <> ''){
								
								$infojt = $arr_infojenistagihan[$jenistagihan];
								$panjang = 18-strlen($nim);
								$record['idtagihan'] = str_pad($infojt['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($periodeyudisium,$panjang,'0',STR_PAD_LEFT).$nim;
								$record['jenistagihan'] = $jenistagihan;
								$record['nominaltagihan'] = $tarif[$periodeyudisium][$kodeunit][$jenistagihan]['nominaltarif'];
								$err = static::insertRecord($conn,$record);
								}
							else{
									if($arr_tagihan)
									foreach($arr_tagihan as $i => $v){
										$panjang = 18-strlen($nim);
										$record['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($periodeyudisium,$panjang,'0',STR_PAD_LEFT).$nim;
										$record['jenistagihan'] = $v['jenistagihan'];
										$record['nominaltagihan'] = $tarif[$periodeyudisium][$kodeunit][$v['jenistagihan']]['nominaltarif'];
										$err = static::insertRecord($conn,$record);
										}
								}
						}
				}
			}
			
			// generate awal Perkuliahan + semester 1 untuk pendaftar
		function generateTagihanpendaftar($conn, $r_nopendaftar, $datapendaftar){
			require_once(Route::getModelPath('akademik'));
			require_once(Route::getModelPath('tarif'));
			require_once(Route::getModelPath('jenistagihan'));
			$arr_tagihan = mJenistagihan::getArray($conn,array('A','S','T','B'),'0');
			
			if($arr_tagihan)
				foreach($arr_tagihan as $i => $v){
					//cari tarif
					$arr_tarif = mTarif::getRowtarif($conn,$datapendaftar['periodedaftar'].'1',$datapendaftar['jalurpenerimaan'],$datapendaftar['pilihanditerima'],$v['jenistagihan'],$datapendaftar['sistemkuliah']);
					if($v['frekuensitagihan']<>'B')
						{
						$cek = $conn->getRow("select * from h2h.ke_tagihan where nopendaftar = '$r_nopendaftar' 
											and jenistagihan = '".$v['jenistagihan']."' and periode = '".$datapendaftar['periodedaftar'].'1'."'");
							if($cek){
								if($cek['isedit'] == 'G')
									{
										if($cek['flaglunas']=='BB'){
										static::deleteperid($conn,$cek['idtagihan']);
										$rec = array();
										$panjang = 18-strlen($r_nopendaftar);
										$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($datapendaftar['periodedaftar'].'1',$panjang,'0',STR_PAD_LEFT).$r_nopendaftar;
										$rec['jenistagihan'] = $v['jenistagihan'];
										$rec['nopendaftar'] = $r_nopendaftar;
										$rec['tgltagihan'] = date('Y-m-d');
										$rec['periode'] = $datapendaftar['periodedaftar'].'1';
										$rec['isangsur'] = 0;
										$rec['isedit'] = 'G';
										$rec['flaglunas'] = 'BB';
										$rec['nominaltagihan'] = $arr_tarif['nominaltarif'];
										static::insertRecord($conn,$rec);
										}
										}
								}
							else{
									$rec = array();
									$panjang = 18-strlen($r_nopendaftar);
									$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($datapendaftar['periodedaftar'].'1',$panjang,'0',STR_PAD_LEFT).$r_nopendaftar;
									$rec['jenistagihan'] = $v['jenistagihan'];
									$rec['nopendaftar'] = $r_nopendaftar;
									$rec['tgltagihan'] = date('Y-m-d');
									$rec['periode'] = $datapendaftar['periodedaftar'].'1';
									$rec['isangsur'] = 0;
									$rec['isedit'] = 'G';
									$rec['flaglunas'] = 'BB';
									$rec['nominaltagihan'] = $arr_tarif['nominaltarif'];
									static::insertRecord($conn,$rec);
								}
						}
					else{
						$frekuensi = mAkademik::getFrekuensibulanan($conn,$datapendaftar['periodedaftar'].'1');//array('1'=>'Jan','2'=>'Feb','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni');
						foreach($frekuensi as $i => $val){
								$cek = $conn->getRow("select * from h2h.ke_tagihan where nopendaftar = '$r_nopendaftar' 
											and jenistagihan = '".$v['jenistagihan']."' and periode = '".$datapendaftar['periodedaftar'].'1'."'
											and bulantahun = '$i'");
							if($cek){
								if($cek['isedit'] == 'G')
									{
										if($cek['flaglunas']=='BB'){
										static::deleteperid($conn,$cek['idtagihan']);
										$rec = array();
										$panjang = 18-strlen($r_nopendaftar);
										$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($i,$panjang,'0',STR_PAD_LEFT).$r_nopendaftar;
										$rec['jenistagihan'] = $v['jenistagihan'];
										$rec['nopendaftar'] = $r_nopendaftar;
										$rec['tgltagihan'] = date('Y-m-d');
										$rec['periode'] = $datapendaftar['periodedaftar'].'1';
										$rec['bulantahun'] = $i;
										$rec['isangsur'] = 0;
										$rec['isedit'] = 'G';
										$rec['flaglunas'] = 'BB';
										$rec['nominaltagihan'] = $arr_tarif['nominaltarif'];
										static::insertRecord($conn,$rec);
										}
										}
								}
							else{
									$rec = array();
									$panjang = 18-strlen($r_nopendaftar);
									$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($i,$panjang,'0',STR_PAD_LEFT).$r_nopendaftar;
									$rec['jenistagihan'] = $v['jenistagihan'];
									$rec['nopendaftar'] = $r_nopendaftar;
									$rec['tgltagihan'] = date('Y-m-d');
									$rec['periode'] = $datapendaftar['periodedaftar'].'1';
										$rec['bulantahun'] = $i;
									$rec['isangsur'] = 0;
									$rec['isedit'] = 'G';
									$rec['flaglunas'] = 'BB';
									$rec['nominaltagihan'] = $arr_tarif['nominaltarif'];
									static::insertRecord($conn,$rec);
								}
							}
						
						}
					}
			
			}
			
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
					//cari tarif
					$arr_tarif = mTarif::getRowtarif($conn,'',$datamhs['jalurpenerimaan'],$datamhs['kodeunit'],$v['jenistagihan'],$datamhs['sistemkuliah']);
					if($arr_tarif)
					foreach($arr_tarif as $i => $v)
					{
						$tarif[$v['periodetarif']][$v['jalurpenerimaan']][$v['sistemkuliah']][$v['kodeunit']] = $v;
						}
						
					if($v['frekuensitagihan']<>'B')
						{
						$cek = $conn->getRow("select * from h2h.ke_tagihan where nim = '$r_nim' 
											and jenistagihan = '".$v['jenistagihan']."' and periode = '".$r_periode."'");
							if($cek){
								if($cek['isedit'] == 'G')
									{
										if($cek['flaglunas']=='BB'){
											static::deleteperid($conn,$cek['idtagihan']);
											$rec = array();
											$panjang = 18-strlen($r_nim);
											$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($r_periode,$panjang,'0',STR_PAD_LEFT).$r_nim;
											$rec['jenistagihan'] = $v['jenistagihan'];
											$rec['nim'] = $r_nim;
											$rec['tgltagihan'] = date('Y-m-d');
											$rec['periode'] = $r_periode;
											$rec['isangsur'] = 0;
											$rec['isedit'] = 'G';
											$rec['flaglunas'] = 'BB';
											if($v['aturanperiode']=='A')
												$rec['nominaltagihan'] = $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
											else
												$rec['nominaltagihan'] = $tarif[$r_periode][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
											static::insertRecord($conn,$rec);
											}
										}
								}
							else{
									$rec = array();
									$panjang = 18-strlen($r_nim);
									$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($r_periode,$panjang,'0',STR_PAD_LEFT).$r_nim;
									$rec['jenistagihan'] = $v['jenistagihan'];
									$rec['nim'] = $r_nim;
									$rec['tgltagihan'] = date('Y-m-d');
									$rec['periode'] = $r_periode;
									$rec['isangsur'] = 0;
									$rec['isedit'] = 'G';
									$rec['flaglunas'] = 'BB';
									if($v['aturanperiode']=='A')
												$rec['nominaltagihan'] = $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
											else
												$rec['nominaltagihan'] = $tarif[$r_periode][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
									static::insertRecord($conn,$rec);
								}
						}
					else{
						$frekuensi = mAkademik::getFrekuensibulanan($conn,$r_periode);//array('1'=>'Jan','2'=>'Feb','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni');
						foreach($frekuensi as $i => $val){
								$cek = $conn->getRow("select * from h2h.ke_tagihan where nim = '$r_nim' 
											and jenistagihan = '".$v['jenistagihan']."'
											and bulantahun = '$i'");
							if($cek){
								if($cek['isedit'] == 'G')
									{
										if($cek['flaglunas']=='BB'){
										static::deleteperid($conn,$cek['idtagihan']);
										$rec = array();
										$panjang = 18-strlen($r_nim);
										$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($i,$panjang,'0',STR_PAD_LEFT).$r_nim;
										$rec['jenistagihan'] = $v['jenistagihan'];
										$rec['nim'] = $r_nim;
										$rec['tgltagihan'] = date('Y-m-d');
										$rec['periode'] = $r_periode;
										$rec['bulantahun'] = $i;
										$rec['isangsur'] = 0;
										$rec['isedit'] = 'G';
										$rec['flaglunas'] = 'BB';
										if($v['aturanperiode']=='A')
											$rec['nominaltagihan'] = $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
										else
											$rec['nominaltagihan'] = $tarif[$r_periode][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
										static::insertRecord($conn,$rec);
										}
										}
								}
							else{
									$rec = array();
									$panjang = 18-strlen($r_nim);
									$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($i,$panjang,'0',STR_PAD_LEFT).$r_nim;
									$rec['jenistagihan'] = $v['jenistagihan'];
									$rec['nim'] = $r_nim;
									$rec['tgltagihan'] = date('Y-m-d');
									$rec['periode'] = $r_periode;
									$rec['bulantahun'] = $i;
									$rec['isangsur'] = 0;
									$rec['isedit'] = 'G';
									$rec['flaglunas'] = 'BB';
									if($v['aturanperiode']=='A')
										$rec['nominaltagihan'] = $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
									else
										$rec['nominaltagihan'] = $tarif[$r_periode][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
									static::insertRecord($conn,$rec);
								}
							}
						
						}
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
				
			$datakrs = mAkademik::getDatakrs($conn,$r_nim,$r_periode);
				
			if($arr_tagihan)
				foreach($arr_tagihan as $i => $v){
					if($datakrs[$v['tipekuliah']]){
					//cari tarif
					$arr_tarif = mTarif::getArraytarif($conn,'',$datamhs['jalurpenerimaan'],$datamhs['kodeunit'],$v['jenistagihan'],$datamhs['sistemkuliah']);
					if($arr_tarif)
					foreach($arr_tarif as $t => $vt)
					{
						$tarif[$vt['periodetarif']][$vt['jalurpenerimaan']][$vt['sistemkuliah']][$vt['kodeunit']] = $vt;
						}
						
					if($v['frekuensitagihan']<>'B')
						{
						$cek = $conn->getRow("select * from h2h.ke_tagihan where nim = '$r_nim' 
											and jenistagihan = '".$v['jenistagihan']."' and periode = '".$r_periode."'");
							if($cek){
								if($cek['isedit'] == 'G')
									{
										if($cek['flaglunas']=='BB'){
											static::deleteperid($conn,$cek['idtagihan']);
											$rec = array();
											$panjang = 18-strlen($r_nim);
											$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($r_periode,$panjang,'0',STR_PAD_LEFT).$r_nim;
											$rec['jenistagihan'] = $v['jenistagihan'];
											$rec['nim'] = $r_nim;
											$rec['tgltagihan'] = date('Y-m-d');
											$rec['periode'] = $r_periode;
											$rec['isangsur'] = 0;
											$rec['isedit'] = 'G';
											$rec['flaglunas'] = 'BB';
											if($v['tipekuliah'] == 'A'){
												$pengali = $datakrs[$v['tipekuliah']];
											$rec['jumlahsks'] = $datakrs[$v['tipekuliah']];
											}
											else 
												$pengali = 1;
											
											if($v['aturanperiode']=='A')
												$rec['nominaltagihan'] = $pengali * $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
											else
												$rec['nominaltagihan'] = $pengali * $tarif[$r_periode][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
											static::insertRecord($conn,$rec);
											}
										}
								}
							else{
									$rec = array();
									$panjang = 18-strlen($r_nim);
									$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($r_periode,$panjang,'0',STR_PAD_LEFT).$r_nim;
									$rec['jenistagihan'] = $v['jenistagihan'];
									$rec['nim'] = $r_nim;
									$rec['tgltagihan'] = date('Y-m-d');
									$rec['periode'] = $r_periode;
									$rec['isangsur'] = 0;
									$rec['isedit'] = 'G';
									$rec['flaglunas'] = 'BB';
									if($v['tipekuliah'] == 'A'){
												$pengali = $datakrs[$v['tipekuliah']];
											$rec['jumlahsks'] = $datakrs[$v['tipekuliah']];
											}
									else 
										$pengali = 1;
									if($v['aturanperiode']=='A')
										$rec['nominaltagihan'] = $pengali * $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
									else
										$rec['nominaltagihan'] = $pengali * $tarif[$r_periode][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
												
									static::insertRecord($conn,$rec);
								}
						}
					else{
						$frekuensi = mAkademik::getFrekuensibulanan($conn,$r_periode);//array('1'=>'Jan','2'=>'Feb','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni');
						foreach($frekuensi as $i => $val){
								$cek = $conn->getRow("select * from h2h.ke_tagihan where nim = '$r_nim' 
											and jenistagihan = '".$v['jenistagihan']."'
											and bulantahun = '$i'");
							if($cek){
								if($cek['isedit'] == 'G')
									{
										if($cek['flaglunas']=='BB'){
										static::deleteperid($conn,$cek['idtagihan']);
										$rec = array();
										$panjang = 18-strlen($r_nim);
										$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($i,$panjang,'0',STR_PAD_LEFT).$r_nim;
										$rec['jenistagihan'] = $v['jenistagihan'];
										$rec['nim'] = $r_nim;
										$rec['tgltagihan'] = date('Y-m-d');
										$rec['periode'] = $r_periode;
										$rec['bulantahun'] = $i;
										$rec['isangsur'] = 0;
										$rec['isedit'] = 'G';
										$rec['flaglunas'] = 'BB';
										if($v['tipekuliah'] == 'A'){
												$pengali = $datakrs[$v['tipekuliah']];
											$rec['jumlahsks'] = $datakrs[$v['tipekuliah']];
											}
										else 
											$pengali = 1;
										if($v['aturanperiode']=='A')
											$rec['nominaltagihan'] = $pengali * $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
										else
											$rec['nominaltagihan'] = $pengali * $tarif[$r_periode][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
										static::insertRecord($conn,$rec);
										}
										}
								}
							else{
									$rec = array();
									$panjang = 18-strlen($r_nim);
									$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($i,$panjang,'0',STR_PAD_LEFT).$r_nim;
									$rec['jenistagihan'] = $v['jenistagihan'];
									$rec['nim'] = $r_nim;
									$rec['tgltagihan'] = date('Y-m-d');
									$rec['periode'] = $r_periode;
									$rec['bulantahun'] = $i;
									$rec['isangsur'] = 0;
									$rec['isedit'] = 'G';
									$rec['flaglunas'] = 'BB';
									if($v['tipekuliah'] == 'A'){
												$pengali = $datakrs[$v['tipekuliah']];
											$rec['jumlahsks'] = $datakrs[$v['tipekuliah']];
											}
									else 
										$pengali = 1;
									if($v['aturanperiode']=='A')
										$rec['nominaltagihan'] = $pengali * $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
									else
										$rec['nominaltagihan'] = $pengali * $tarif[$r_periode][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
									static::insertRecord($conn,$rec);
								}
							}
						
						}
					}
				}
			}
	}
?>