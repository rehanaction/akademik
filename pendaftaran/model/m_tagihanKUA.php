<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTagihanKUA extends mModel {
		const schema = 'h2h';
		const table = 'ke_tagihan';
		const order = 'idtagihan';
		const key = 'idtagihan';
		const label = 'idtagihan';
		const dendaDefault = 'DENDA'; // denda terlambat bayar, bukan merupakan jenis tagihan

		function getArrayTagRutin($conn,$frekuensitagihan='',$sks=''){
			$sql = "select * from ".static::table('lv_jenistagihan')." where 1=1 AND jenistagihan<>'FRM' and jenistagihan<>'UKT'";
			if($frekuensitagihan)
			    if(is_array($frekuensitagihan))
				$sql .= " and frekuensitagihan in ('".implode("','",$frekuensitagihan)."')";
			    else
				$sql .= " and frekuensitagihan ='".$frekuensitagihan."'";
			if($sks <>'' and $sks<>'0')
			    $sql .= " and issks = '".$sks."'";

			$sql .= " order by ".static::order;
			return $conn->GetArray($sql);
		    
		}


		function getListJenisTagihanGenerate($conn,$periode,$sistem,$isawal=null) {
			$semester = substr($periode,-1);
			if($semester == '0')
				return array();
			else if($semester == '3')
				$ispendek = true;
			else
				$ispendek = false;
			
			if($sistem == 'P')
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
					$datakrs = mAkademik::getDatakrsall($conn,$periode,'A',$nim); // tidak ada data tipekuliah
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
										$tarif[$v['periodetarif']][$v['jalurpenerimaan']][$v['gelombang']]['R'][$v['kodeunit']][$v['angsuranke']] = array('nominaltarif' => $v['nominaltarif'], 'tgldeadline' => $v['tgldeadline']); // hanya untuk reguler				
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
										$jumlahangsur = (int)$infojt['jumlahangsur'];
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
								}
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
			
			return array($err,$msg,$jml);
		}
		
	function getTagihanPendaftar($conn, $nopendaftar){
		$sql = "select t.*, k.namakelompok from ".static::table()." t
				left join h2h.lv_jenistagihan j using (jenistagihan)
				left join h2h.lv_kelompoktagihan k using (kodekelompok)
				where t.nopendaftar = ".Query::escape($nopendaftar)."
				order by t.angsuranke";
		$rs = $conn->Execute($sql);
		
		$data = array();
		while ($row = $rs->fetchRow()){
			$data[] = $row;			
			}
		return $data;
		
		}
	
		
	}
?>
