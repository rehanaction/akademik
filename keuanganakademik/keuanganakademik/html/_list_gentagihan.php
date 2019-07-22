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
	$conn->debug=true;
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
	$r_jalur = Modul::setRequest($_POST['jalurpenerimaan'],'JALUR');
	//$r_gelombang = Modul::setRequest($_POST['gelombang'],'GELOMBANG');
	
	if (empty ($r_periode))
		list($p_posterr, $p_postmsg) = array(true, 'silahkan Pilih Periode');
	if (empty ($r_jalur))
		list($p_posterr, $p_postmsg) = array(true, 'silahkan Pilih Jalur Penerimaan');
	/*if (empty ($r_gelombang))
		list($p_posterr, $p_postmsg) = array(true, 'silahkan Pilih Gelombang'); */
		
	
	$r_jenis = $_POST['jenis'];
	if(!$r_jenis)
		$r_jenis = array();
	
	// combo
	$l_periode = uCombo::periode($conn,$r_periode,'periode','onchange="goSubmit()"',true);
	$l_jalur = uCombo::jalur($conn,$r_jalur,'jalurpenerimaan','onchange="goSubmit()"',true);
	$l_gelombang = uCombo::gelombang($conn,$r_gelombang,'gelombang','onchange="goSubmit()"',true);
		
        
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
				$frekuensi[$v] = mAkademik::getFrekuensibulanan($conn,$r_periode);
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
		$arr_unit = mAkademik::getArrayunit($conn,false,'2');
	
	$r_act = $_POST['act'];
	if($r_act == 'void' and $c_delete){
		$conn->BeginTrans();
			$record = array();
			$record['kodeunit'] = $_POST['unitkey'];
			$record['jenistagihan'] = $_POST['jenistagihankey'];
			$record['bulantahun'] = $_POST['tahunbulankey'];
			$record['jalurpenerimaan'] = $r_jalur;
			$record['periodetagihan'] = $r_periode;
			//$record['gelombang'] = $r_gelombang;
			$record['isgen'] = 'V';
			
			$infojt = $arr_infojenistagihan[$record['jenistagihan']];
			if($infojt['frekuensitagihan']=='A'){
			
			
			$p_posterr = mTagihan::deletetagihanawal($conn,$record);
			}
			else
			$p_posterr = mTagihan::delete($conn,$record);
			
			if($p_posterr == 0)
				$p_posterr = mLoggenerate::insertRecord($conn,$record);
				
			if($p_posterr == 0)
				$p_postmsg='Berhasil Melakukan Pembatalan Tagihan';
			else	
				$p_postmsg='Gagal Melakukan Pembatalan Tagihan';
			
		$conn->CommitTrans();	
		}
	else if($r_act == 'generate' and $c_edit){
		 
			$record['jenistagihan'] = $_POST['jenistagihankey'];
			$infojt = $arr_infojenistagihan[$record['jenistagihan']];			
			$datakrs = mAkademik::getDatakrsall($conn,$r_periode,$infojt['tipekuliah']);
			 
			// tagihan pendaftar, jika tagihan awal, per tahun
			if($infojt['frekuensitagihan']=='A'){
					$record = array();
					$record['kodeunit'] = $_POST['unitkey'];
					$record['jenistagihan'] = $_POST['jenistagihankey'];
					$record['jalurpenerimaan'] = $r_jalur;
					$record['periodetagihan'] = $r_periode;
					//$record['gelombang'] = $r_gelombang;
					$record['bulantahun'] = $_POST['tahunbulankey'];
					$record['isgen'] = 'G';
					
					//delete seluruh tagihan generated & belum bayar
					$err = mTagihan::delete($conn,$record);
					
					//cari mahasiswa
					$arr_mhs = mAkademik::getListPendaftar($conn,$record);
					
					//cari tarif
					$arr_tarif = mTarif::getArraytarif($conn,$record['periodetagihan'],$r_jalur,$record['kodeunit'],$record['jenistagihan'], '', '');
					
					if($arr_tarif)
					foreach($arr_tarif as $i => $v)
					{
						$tarif[$v['periodetarif']][$v['jalurpenerimaan']][$v['gelombang']][$v['sistemkuliah']][$v['kodeunit']] = $v;						
						}
						
					$arr_tagihan = mTagihan::getTagihanmhs($conn,$record);
					
					if($arr_tagihan)
					foreach($arr_tagihan as $i => $val)
						$tagihan[$val['nopendaftar']] = $val;
						
					//mulai iterasi generate tagihan
					$a_mhs = array();
					$jml = $jmlerr =  0;
					if($arr_mhs)
					foreach($arr_mhs as $i => $mhs){
							
							if($tagihan[$mhs['nopendaftar']]){
								continue;
							}
							else{
										$rec = array();
										$rec['jenistagihan'] = $record['jenistagihan'];
										$rec['nopendaftar'] = $mhs['nopendaftar'];
										$rec['tgltagihan'] = date('Y-m-d');
										$rec['periode'] = $record['periodetagihan'];
										$rec['bulantahun'] = $record['bulantahun'];
										$rec['isangsur'] = 0;
										$rec['isedit'] = 'G';
										$rec['flaglunas'] = 'BB';
										
										// cek deadline
										if(!empty($infojt['nominaldenda']) and !empty($infojt['tgldeadline'])) {
												if($infojt['tgldeadline'] < date('j'))
														$rec['tgldeadline'] = date('Y-m-d',mktime(0,0,0,date('n')+1,$tgldeadline,date('Y')));
												else
														$rec['tgldeadline'] = date('Y-m-').str_pad($infojt['tgldeadline'],2,'0',STR_PAD_LEFT);
										}
										
										//nominal tarif sesuai dengan aturannya
										if($infojt['aturanperiode']=='A')	
											$rec['nominaltagihan'] = $tarif[$mhs['periodedaftar'].'1'][$mhs['jalurpenerimaan']][$mhs['gelombang']][$mhs['sistemkuliah']][$mhs['pilihanditerima']]['nominaltarif'];
										else if($infojt['aturanperiode']=='S')
											$rec['nominaltagihan'] = $tarif[$record['periodetagihan']][$mhs['jalurpenerimaan']][$mhs['gelombang']][$mhs['sistemkuliah']][$mhs['pilihanditerima']]['nominaltarif'];
										
							//Pecahkan tagihan
							if ($rec['nominaltagihan'] > 0){
								$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $infojt['jumlahangsur']);
								for ($a=1; $a<=$infojt['jumlahangsur']; $a++){
									
										$rec['idtagihan'] = str_pad($infojt['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($mhs['nopendaftar'],15,'0',STR_PAD_LEFT);

									if($rec['nominaltagihan'] > 0){
										$a_mhs[] = $mhs['nopendaftar'];
										
										$errt = mTagihan::insertRecord($conn,$rec);
											if(!$errt)
												$jml++;
											else
												$jmlerr++;
									}	
								}
							}
						}
					}
					
					// tambahkan potongan
					mTagihan::generatePotongan($conn,$r_periode,$_POST['tahunbulankey'],$_POST['jenistagihankey'],null,$a_mhs);
				}
			else{ 
					$record = array();
					$record['kodeunit'] = $_POST['unitkey'];
					$record['jenistagihan'] = $_POST['jenistagihankey'];
					$record['bulantahun'] = $_POST['tahunbulankey'];
					$record['jalurpenerimaan'] = $r_jalur;
					$record['periodetagihan'] = $r_periode;
					//$record['gelombang'] = $r_gelombang;
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
					$arr_tarif = mTarif::getArraytarif($conn,$r_periode,$r_jalur,$record['kodeunit'],$record['jenistagihan'],'','');
					
					//cari tarif jika UKT
					//if ($record['jenistagihan']=='UKT'){
										$rs_mhs=array();
										foreach($arr_mhs as $mhsrow){
										$rs_mhs[] = $mhsrow['nim'];
										}
					//}
					
					$datamhs="'".implode("','", $rs_mhs)."'";

					if ($record['jenistagihan']=='UKT'){
						$arr_tarif = mTarif::getArraytarifUkt($conn,'',$r_jalur,$record['kodeunit'],$record['jenistagihan'], $datamhs);
					}					
					
					if($arr_tarif  and $record['jenistagihan']<>'UKT'){
						foreach($arr_tarif as $i => $v)
						{
							$tarif[$v['periodetarif']][$v['jalurpenerimaan']][$v['gelombang']][$v['sistemkuliah']][$v['kodeunit']] = $v;
							
						}
					}else if($arr_tarif and $record['jenistagihan']=='UKT'){
						foreach($arr_tarif as $i => $v)
						{
							$tarif[$v['periodetarif']][$v['jalurpenerimaan']][$v['sistemkuliah']][$v['kodeunit']][$v['nim']] = $v;
						}
					}
					
					$arr_tagihan = mTagihan::getTagihanmhs($conn,$record);
					
					if($arr_tagihan)
					foreach($arr_tagihan as $i => $val)
						$tagihan[$val['nim']] = $val;
					
					//mulai iterasi generate tagihan
					$a_mhs = array();
					$jml =  $jmlerr = 0;
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
										
										// cek deadline
										if(!empty($infojt['nominaldenda']) and !empty($infojt['tgldeadline'])) {
												if($infojt['tgldeadline'] < date('j'))
														$rec['tgldeadline'] = date('Y-m-d',mktime(0,0,0,date('n')+1,$tgldeadline,date('Y')));
												else
														$rec['tgldeadline'] = date('Y-m-').str_pad($infojt['tgldeadline'],2,'0',STR_PAD_LEFT);
										}
										
										//nominal tarif sesuai dengan aturannya
										if($infojt['aturanperiode']=='A'){
											if ($record['jenistagihan'] <> 'UKT')
												$rec['nominaltagihan'] = $pengali * $tarif[$mhs['periodemasuk']][$mhs['jalurpenerimaan']][str_pad($mhs['gelombang'],2,'0',STR_PAD_LEFT)][$mhs['sistemkuliah']][$mhs['kodeunit']]['nominaltarif'];
											else if ($record['jenistagihan'] == 'UKT')
												$rec['nominaltagihan'] = $pengali * $tarif[$mhs['periodemasuk']][$mhs['jalurpenerimaan']][$mhs['sistemkuliah']][$mhs['kodeunit']][$mhs['nim']]['nominaltarif'];
												
										}else if($infojt['aturanperiode']=='S'){
											if ($record['jenistagihan'] <> 'UKT')
												$rec['nominaltagihan'] = $pengali * $tarif[$record['periodetagihan']][$mhs['jalurpenerimaan']][str_pad($mhs['gelombang'],2,'0',STR_PAD_LEFT)][$mhs['sistemkuliah']][$mhs['kodeunit']]['nominaltarif'];
											else if ($record['jenistagihan'] == 'UKT')
												$rec['nominaltagihan'] = $pengali * $tarif[$record['periodetagihan']][$mhs['jalurpenerimaan']][$mhs['sistemkuliah']][$mhs['kodeunit']][$mhs['nim']]['nominaltarif'];
										}
										
										if ($rec['nominaltagihan'] > 0){
											
											$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $infojt['jumlahangsur']);
											$rec['nominaltagihan'] = round($rec['nominaltagihan']);
											for ($b=1; $b<=$infojt['jumlahangsur']; $b++){
												if($rec['nominaltagihan'] > 0)
													{
														$idtagihan = str_pad($infojt['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.'0'.$b.str_pad($mhs['nim'],15,'0',STR_PAD_LEFT);
														$rec['idtagihan'] = $idtagihan;
													
													$a_mhs[] = $mhs['nim'];
													
													$errt = mTagihan::insertRecord($conn,$rec);
													if(!$errt)
														$jml++;
													else
														$jmlerr++;
													}
												
												}
										}
									}
							}
						
						// tambahkan potongan
						mTagihan::generatePotongan($conn,$r_periode,$_POST['tahunbulankey'],$_POST['jenistagihankey'],$a_mhs);
					}
			
			$record['jml'] = $jml;
			//insert loggenerate
			$posterr = mLoggenerate::insertRecord($conn,$record);
			
			if ($jmlerr <> '0')
				list($p_posterr,$p_postmsg) = array(true," Generate Gagal di lakukan sebanyak ".$jmlerr." tagihan ");
			else	
				list($p_posterr,$p_postmsg) = array(false," Generate berhasil di lakukan sebanyak ".$jml." tagihan ");
			
		}
		
	
	if (empty ($r_jalur)){
		$p_posterr=true;
		$p_postmsg='Silahkan Pilih Jalur Penerimaan';
	}else if (empty ($r_periode)){
		$p_posterr=true;
		$p_postmsg='Silahkan Pilih Periode';
	
	}
	//data log
	$f = array();
	$f['periodetagihan'] = $r_periode;
	$f['jalurpenerimaan'] = $r_jalur;
	//$f['gelombang'] = $r_gelombang;
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
	//$a_filtercombo[] = array('label' => 'Gelombang', 'combo' => $l_gelombang);
	
	require_once($conf['view_dir'].'v_list_gentagihan.php');
?>
