<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$p_detailpage = "data_tarif";
	// include
	require_once(Route::getModelPath('jenistagihan'));
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('tarif'));
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getModelPath('loggenerate'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
	$r_jalur = Modul::setRequest($_POST['jalurpenerimaan'],'JALUR');
	$r_jenis = $_POST['jenis'];
	if(!$r_jenis)
		$r_jenis = array();
	
	// combo
	$l_periode = uCombo::periode($conn,$r_periode,'periode','onchange="goSubmit()"',true);
	$l_jalur = uCombo::jalur($conn,$r_jalur,'jalurpenerimaan','onchange="goSubmit()"',true);
		
        
	// properti halaman
	$p_title = 'Generate Tagihan Rutin ( Non Formulir )';
	$p_tbwidth = '100%';
	$p_aktivitas = 'Master';
	
	$p_model = mJenistagihan;
	$p_key = $p_model::key;
	$p_colnum = count($p_kolom)+1;
	
	
	//daftar jenis tagihan
		$arr_jenistagihan = mJenistagihan::getArray($conn,$r_jenis);
		
		if($arr_jenistagihan)
			foreach($arr_jenistagihan as $i => $v){
					$jt[$v['frekuensitagihan']][] = $v['jenistagihan'];
					$arr_infojenistagihan[$v['jenistagihan']] = $v;
				}
	//hitung frekuensi yang tampil
	$col = 0;
	foreach($r_jenis as $i => $v){
		if($jt[$v]){
		if($v=='B')
			{
				//$frekuensi[$v] = array('1'=>'Jan','2'=>'Feb','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni');
				$frekuensi[$v] = mAkademik::getFrekuensibulanan($conn,$r_periode);//array('1'=>'Jan','2'=>'Feb','3'=>'Maret','4'=>'April','5'=>'Mei','6'=>'Juni');
				$col += (count($jt[$v])*(count($frekuensi[$v])));
			}
		elseif($v=='A'){
			$frekuensi[$v] = array($r_periode=>'Awal Tahun');
			$col += (count($jt[$v]));
		}elseif($v=='S'){
			$frekuensi[$v] = array($r_periode=>'Per Semester');
			$col += (count($jt[$v]));
		}elseif($v=='T'){
			$frekuensi[$v] = array($r_periode=>'Per Tahun');
			$col += (count($jt[$v]));
		}
		}
	}
		
	// daftar jurusan
		$arr_unit = mAkademik::getArrayunit($conn,false,'3');
	
	$r_act = $_POST['act'];
	if($r_act == 'void' and $c_delete){
		$conn->BeginTrans();
			$record = array();
			$record['kodeunit'] = $_POST['unitkey'];
			$record['jenistagihan'] = $_POST['jenistagihankey'];
			$record['bulantahun'] = $_POST['tahunbulankey'];
			$record['jalurpenerimaan'] = $r_jalur;
			$record['periodetagihan'] = $r_periode;
			$record['isgen'] = 'V';
			
			$infojt = $arr_infojenistagihan[$record['jenistagihan']];
			if($infojt['frekuensitagihan']=='A'){
			
			
			$err = mTagihan::deletetagihanawal($conn,$record);
			}
			else
			$err = mTagihan::delete($conn,$record);
			
			if($err == 0)
				$err = mLoggenerate::insertRecord($conn,$record);
			
		$conn->CommitTrans();	
		}
	else
	if($r_act == 'generate' and $c_edit){
			
			$record['jenistagihan'] = $_POST['jenistagihankey'];
			// informasi per jenis tagihan
			$infojt = $arr_infojenistagihan[$record['jenistagihan']];
			
			$datakrs = mAkademik::getDatakrsall($conn,$r_periode,$infojt['tipekuliah']);
			
			
			// tagihan pendaftar
			if($infojt['frekuensitagihan']=='A'){
					$record = array();
					$record['kodeunit'] = $_POST['unitkey'];
					$record['jenistagihan'] = $_POST['jenistagihankey'];
					$record['jalurpenerimaan'] = $r_jalur;
					$record['periodetagihan'] = $r_periode;
					$record['bulantahun'] = $_POST['tahunbulankey'];
					$record['isgen'] = 'G';
					//delete seluruh tagihan generated & belum bayar
					$err = mTagihan::delete($conn,$record);
					
					$arr_mhs = mAkademik::getListPendaftar($conn,$record);
					//cari tarif
					$arr_tarif = mTarif::getArraytarif($conn,$record['periodetagihan'],$r_jalur,$record['kodeunit'],$record['jenistagihan']);
					if($arr_tarif)
					foreach($arr_tarif as $i => $v)
					{
						$tarif[$v['periodetarif']][$v['jalurpenerimaan']][$v['sistemkuliah']][$v['kodeunit']] = $v;
						}
					$arr_tagihan = mTagihan::getTagihanmhs($conn,$record);
					if($arr_tagihan)
					foreach($arr_tagihan as $i => $val)
						$tagihan[$val['nopendaftar']] = $val;
					//mulai iterasi generate tagihan
					$jml = 0;
					if($arr_mhs)
						foreach($arr_mhs as $i => $mhs){
							
								if($tagihan[$mhs['nopendaftar']]){
									continue;
								}
								else
									{
										$rec = array();
										$panjang = 18-strlen($mhs['nopendaftar']);
										$rec['idtagihan'] = str_pad($infojt['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($record['bulantahun'],$panjang,'0',STR_PAD_LEFT).$mhs['nopendaftar'];
										$rec['jenistagihan'] = $record['jenistagihan'];
										$rec['nopendaftar'] = $mhs['nopendaftar'];
										$rec['tgltagihan'] = date('Y-m-d');
										$rec['periode'] = $record['periodetagihan'];
										$rec['bulantahun'] = $record['bulantahun'];
										$rec['isangsur'] = 0;
										$rec['isedit'] = 'G';
										$rec['flaglunas'] = 'BB';
										//nominal tarif sesuai dengan aturannya
										if($infojt['aturanperiode']=='A')	
											$rec['nominaltagihan'] = $tarif[$mhs['periodedaftar'].'1'][$mhs['jalurpenerimaan']][$mhs['sistemkuliah']][$mhs['pilihanditerima']]['nominaltarif'];
										else if($infojt['aturanperiode']=='S')
											$rec['nominaltagihan'] = $tarif[$record['periodetagihan']][$mhs['jalurpenerimaan']][$mhs['sistemkuliah']][$mhs['pilihanditerima']]['nominaltarif'];
										if($rec['nominaltagihan'] > 0)
											{
											$errt = mTagihan::insertRecord($conn,$rec);
											if($errt == 0)
												$jml++;
											}
									}
							}
							
				}
			else{
				
					$record = array();
					$record['kodeunit'] = $_POST['unitkey'];
					$record['jenistagihan'] = $_POST['jenistagihankey'];
					$record['bulantahun'] = $_POST['tahunbulankey'];
					$record['jalurpenerimaan'] = $r_jalur;
					$record['periodetagihan'] = $r_periode;
					$record['isgen'] = 'G';
					if(substr($r_periode,-1)=='2')
						$record['periodesebelumnya'] = substr($r_periode,0,4).'1';
					else
						{
						$tahunkini = substr($r_periode,0,4);
						$tahunsebelumnya = $tahunkini - 1; 
						$record['periodesebelumnya'] = $tahunsebelumnya.'2';
						}
					//delete seluruh tagihan generated & belum bayar
					$err = mTagihan::delete($conn,$record);
					
					//cari seluruh mhs + statusperwalian periode sebelumnya
					$arr_mhs = mAkademik::getArraymhsperwalian($conn,$record);
					
					//cari tarif
					$arr_tarif = mTarif::getArraytarif($conn,'',$r_jalur,$record['kodeunit'],$record['jenistagihan']);
					if($arr_tarif)
					foreach($arr_tarif as $i => $v)
					{
						$tarif[$v['periodetarif']][$v['jalurpenerimaan']][$v['sistemkuliah']][$v['kodeunit']] = $v;
						}
					
					$arr_tagihan = mTagihan::getTagihanmhs($conn,$record);
					if($arr_tagihan)
					foreach($arr_tagihan as $i => $val)
						$tagihan[$val['nim']] = $val;
					
					//mulai iterasi generate tagihan
					$jml = 0;
					if($arr_mhs)
						foreach($arr_mhs as $i => $mhs){
							if($infojt['issks'] == '1' and $infojt['tipekuliah'] == 'A')
								$pengali = $datakrs[$mhs['nim']];
							else 
								$pengali = '1';
							
								// bila tagihan di awal semester maka periode masuk harus sama dengan periode generate
								if($infojt['frekuensitagihan']=='A' and $mhs['periodemasuk'] <> $record['periodetagihan'])
									continue;
								
								// bila tagihan semester maka mhs lama harus statusnya aktif di periode perwalian sebelumnya
								if($infojt['frekuensitagihan']=='S' and $mhs['periodemasuk'] <> $record['periodetagihan'] and $mhs['statusperwalian'] <> 'A')
									continue;
									
								// bila tagihan bulanan maka mhs lama harus statusnya aktif di periode perwalian sebelumnya
								if($infojt['frekuensitagihan']=='B' and $mhs['periodemasuk'] <> $record['periodetagihan'] and $mhs['statusperwalian'] <> 'A')
									continue;
								// bila tagihan Tahunan maka mhs lama harus statusnya aktif di periode perwalian sebelumnya
								if($infojt['frekuensitagihan']=='T' and $mhs['periodemasuk'] <> $record['periodetagihan'] and $mhs['statusperwalian'] <> 'A')
									continue;
								
								if($tagihan[$mhs['nim']]){
									continue;
								}
								else
									{
										$rec = array();
										$panjang = 18-strlen($mhs['nim']);
										$rec['idtagihan'] = str_pad($infojt['kodetagihan'],2,'0',STR_PAD_LEFT).str_pad($record['bulantahun'],$panjang,'0',STR_PAD_LEFT).$mhs['nim'];
										$rec['jenistagihan'] = $record['jenistagihan'];
										$rec['nim'] = $mhs['nim'];
										$rec['tgltagihan'] = date('Y-m-d');
										$rec['periode'] = $record['periodetagihan'];
										$rec['bulantahun'] = ($record['periodetagihan']<>$record['bulantahun']?$record['bulantahun']:NULL);
										$rec['isangsur'] = 0;
										$rec['isedit'] = 'G';
										$rec['flaglunas'] = 'BB';
										if($infojt['issks'] == '1' and $infojt['tipekuliah'] == 'A')
											$rec['jumlahsks'] = $datakrs[$mhs['nim']];
										//nominal tarif sesuai dengan aturannya
										if($infojt['aturanperiode']=='A')	
											$rec['nominaltagihan'] = $pengali * $tarif[$mhs['periodemasuk']][$mhs['jalurpenerimaan']][$mhs['sistemkuliah']][$mhs['kodeunit']]['nominaltarif'];
										else if($infojt['aturanperiode']=='S')
											$rec['nominaltagihan'] = $pengali * $tarif[$record['periodetagihan']][$mhs['jalurpenerimaan']][$mhs['sistemkuliah']][$mhs['kodeunit']]['nominaltarif'];
										
										if($rec['nominaltagihan'] > 0)
											{
											$errt = mTagihan::insertRecord($conn,$rec);
											if($errt == 0)
												$jml++;
											}
									}
							}
					}
			
			$record['jml'] = $jml;
			//insert loggenerate
			$posterr = mLoggenerate::insertRecord($conn,$record);
			$p_postmsg = " Generate berhasil di lakukan sebanyak ".$jml." tagihan ";
			
		}
		
		
	//data log
	$f = array();
	$f['periodetagihan'] = $r_periode;
	$f['jalurpenerimaan'] = $r_jalur;
	$arr_data = mLoggenerate::getArray($conn,$f);
	foreach($arr_data as $i => $v){
			$v['bulantahun'] = $v['bulantahun'] ? $v['bulantahun'] : $v['periodetagihan'];
			$data[$v['kodeunit']][$v['jenistagihan']][$v['periodetagihan']][$v['bulantahun']] = $v['idloggen'];
			$jenisgen[$v['idloggen']]  = $v['isgen'];
		}
	
	$arr_jmltagihan = mTagihan::getCounttagihan($conn,$r_periode,$r_jalur);
	if($arr_jmltagihan)
		foreach($arr_jmltagihan as $i => $v){
				$jmltagihan[$v['kodeunit']][$v['jenistagihan']][$v['periode']][$v['bulantahun']][$v['isedit']] += $v['jml'];
			}
			
	$arr_jenis = mCombo::arrFlagtagihan();
	unset($arr_jenis['W']);
		
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Jalur Penerimaan', 'combo' => $l_jalur);
	
	require_once($conf['view_dir'].'v_list_gentagihan.php');
?>