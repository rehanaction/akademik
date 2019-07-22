<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	//unsur penilaian
	$r_defaultunsur =  Akademik::getDefaultskalanilai(); 
	//$conn->debug = true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];	
	$c_tutup = $a_auth['canother']['C'];
	$c_buka = $a_auth['canother']['O'];
	// include
	require_once(Route::getModelPath('absensikuliah'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getModelPath('krs'));
	require_once(Route::getModelPath('setting'));
	require_once(Route::getModelPath('skalanilai'));
	require_once(Route::getModelPath('unsurnilai'));
	require_once(Route::getModelPath('matakuliah'));
	require_once(Route::getModelPath('laporanmhs'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Pengisian Nilai';
	$p_tbwidth = "100%";
	$p_aktivitas = 'NILAI';
	$p_listpage = 'list_nilai';
	$p_minriwayat = 12;
	
	
	// variabel request
	$r_key = CStr::removeSpecial(Akademik::base64url_decode($_REQUEST['key']));
	list($thnkurikulum,$kodemk,$kodeunit,$periode,$kelasmk)=explode('|',$r_key);
	$r_key=$thnkurikulum.'|'.$kodemk.'|'.$kodeunit.'|'.$periode.'|'.$kelasmk;
	$r_keyelearn = $kodeunit."".$periode."".$kodemk."".$kelasmk;
	if(empty($r_key))
		Route::navigate($p_listpage);
		
	//ambil informasi flag toefl
	$a_matakuliah=mMatakuliah::getData($conn,$thnkurikulum.'|'.$kodemk);
	$flagtoefl=$a_matakuliah['istoefl'];
	
	// mendapatkan data
	$a_infokelas = mKelas::getDataSingkat($conn,$r_key);
	$a_skalanilai = mSkalaNilai::getDataKurikulum($conn,$a_infokelas['thnkurikulum']);

	// cek periode nilai dan nilai masuk
	if($a_infokelas['isonline']==-1){

	}
	if(substr($a_infokelas['periode'],4,1)=='0')
	    $periodepenilaian=Akademik::getPeriodeNilaiSpa();
	else
	    $periodepenilaian=Akademik::getPeriodeNilai();
	
	if(!Akademik::isAdmin() and $a_infokelas['periode'] != $periodepenilaian) {
		$c_edit = false;
		$c_tutup = false;
	}
	else if(!Akademik::isAdmin() and (!empty($a_infokelas['nilaimasuk']) or Akademik::getIsiNilai() == 'DITUTUP'))
		$c_edit = true;
	

	$param = explode('|',$r_key);
	//pengecekan unsur nilai, apakah menggunakan format universitas atau membuat sendiri
	if ($r_defaultunsur){
	// cek unsur nilai
		 $a_unsurnilai = mUnsurNilaiKelas::getDataKelas($conn,$r_key);
		 if(empty($a_unsurnilai))
			$a_unsurnilai = mUnsurNilaiKelas::insertFromUnsurNilai($conn,$r_key);
	}
		$rs_param = $conn->Execute("select *,coalesce(prosentasenilai::int, 0) as persen from akademik.ak_unsurpenilaian where thnkurikulum='".$param[0]."' and periode='".$param[3]."' and 
					kodeunit='".$param[2]."' and kodemk='".$param[1]."' and kelasmk='".$param[4]."'");
		
		$a_unsurnilai = array();
		$a_unsurnilaiparameter = array();
		$a_unsurnilaikey = array();
		$a_unsurnilainama = array();
		while($row = $rs_param->FetchRow()){
			//$a_unsurnilai[] = $row['namaparameter'].'<br>('.$row['presentase'].'%)';
			$a_unsurnilai[$row['idunsurnilai']] = $row['namaunsurnilai'];
			$a_unsurnilaiparameter[$row['idunsurnilai']] = $row['prosentasenilai'];
			$a_unsurnilainama[$row['idunsurnilai']] = $row['namaunsurnilai'];
			$a_unsurnilaikey[] = $row['idunsurnilai'];
		}
	
	
	$n_unsurnilai = count($a_unsurnilai);
	$p_colnum = 9 + $n_unsurnilai;	
	
	// cek jurnal/riwayat perkuliahan
	$n_riwayat = mKuliah::getJumlahPerKelas($conn,$r_key,true);

	// ambil data
	$p_pesan = mSetting::getPesanPengesahan($conn);
	
	//yg bisa kunci nilai admin universitas dan dosen, tp ga bs batalkan
	if((Modul::getRole() == 'A' or Modul::getRole() == 'AKD')){
		$canKunciNilai = true;
		$canBatalKunciNilai = true;
	}else{
		$canKunciNilai = false;
		$canBatalKunciNilai = false;
	}
	
	//cek periode penilaian aktif
	$periodenilai = $conn->GetRow("select periodenilai,nangkatutup from akademik.ms_setting");
	//cari absensi
	$a_absensi = mAbsensiKuliah::getListPersenPerKelas($conn,$r_key);
	//===================================================
	
	$r_act = $_POST['act'];
	if($r_act == 'saveall' and $c_edit) {
		$ok = true;
		$conn->BeginTrans();
		
		if(!empty($_POST['npm'])) {
			foreach($_POST['npm'] as $t_idx => $t_npm) {
				$t_simpan = (int)$_POST['simpan'][$t_idx];			
				
				if(empty($t_simpan))
					continue;
			
				$t_npm = CStr::removeSpecial($t_npm);
				
				// masukkan unsur nilai mahasiswa
				$record = mKelas::getKeyRecord($r_key);
				$record['nim'] = $t_npm;
				
				$t_allnull = true;
				if(count($a_unsurnilaikey)>0){
					foreach($a_unsurnilaikey as $t_unsur) {
						// $t_idunsur = $t_unsur['idunsurnilai'];
						$t_idunsur = $t_unsur;
						
						$record['idunsurnilai'] = $t_idunsur;
						$record['nilaiunsur'] = CStr::cStrNull(CStr::cStrDec(str_replace('.',',',$_POST['n_'.$t_idunsur][$t_idx])));
						
						if($record['nilaiunsur'] != 'null') {
							$t_allnull = false;
							list($p_posterr,$p_postmsg) = mUnsurNilaiMhs::saveRecord($conn,$record,$r_key.'|'.$t_npm.'|'.$t_idunsur,true);
						}
						else
							list($p_posterr,$p_postmsg) = mUnsurNilaiMhs::delete($conn,$r_key.'|'.$t_npm.'|'.$t_idunsur);
						
						if($p_posterr) {
							$ok = false;
							break;
						}
					}
				}
				
				// masukkan krs
				if($ok) {
					//$t_dipakai = (int)$_POST['dipakai_'.$t_npm];
					//$t_dipakai = -1;
					
					$record = array();
					
					$record['nnumerik'] = CStr::cStrNull(CStr::cStrDec(str_replace('.',',',$_POST['nnumerik'][$t_idx])));
					if($_POST['nremidi'][$t_idx] != null){
						$record['nremidi'] = CStr::cStrNull(CStr::cStrDec(str_replace('.',',',$_POST['nremidi'][$t_idx])));
						//get nilai huruf
						$jenjang = $conn->GetRow("select kode_jenjang_studi from akademik.ak_prodi where kodeunit='".$param[2]."'"); //get kodejenjang
						$nilaihuruf = $conn->GetRow("select nangkasn, nhuruf from akademik.ak_skalanilai where thnkurikulum = '".$param[0]."' and programpend='".$jenjang['kode_jenjang_studi']."' and (".$record['nremidi']." between batasbawah and batasatas)");
						$record['nhurufremidi'] = $nilaihuruf['nhuruf'];
					}
					//$record['dipakai']=-1;
					
					if($t_allnull){
						$record['nangka'] = 'null';
					}
					list($p_posterr,$p_postmsg) = mKRS::updateRecord($conn,$record,$r_key.'|'.$t_npm,true);
					if($p_posterr) {
						$ok = false;
						break;
					}
				}
			}
		}
		
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'save' and $c_edit) {
		$ok = true;
		$conn->BeginTrans();
		
		$r_npm = CStr::removeSpecial($_POST['subkey']);
		
		if(!empty($_POST['npm'])) {
			foreach($_POST['npm'] as $t_idx => $t_npm) {
				$t_simpan = (int)$_POST['simpan'][$t_idx];
				if(empty($t_simpan))
					continue;
				
				$t_npm = CStr::removeSpecial($t_npm);
				if($t_npm == $r_npm) {
					// masukkan unsur nilai mahasiswa
					$record = mKelas::getKeyRecord($r_key);
					$record['nim'] = $t_npm;
					
					$t_allnull = true;
					foreach($a_unsurnilaikey as $t_unsur) {
						// $t_idunsur = $t_unsur['idunsurnilai'];
						$t_idunsur = $t_unsur;
						
						$record['idunsurnilai'] = $t_idunsur;
						$record['nilaiunsur'] = CStr::cStrNull(CStr::cStrDec(str_replace('.',',',$_POST['n_'.$t_idunsur][$t_idx])));
						if($record['nilaiunsur'] != 'null') {
							$t_allnull = false;
							list($p_posterr,$p_postmsg) = mUnsurNilaiMhs::saveRecord($conn,$record,$r_key.'|'.$t_npm.'|'.$t_idunsur,true);
						}
						else
							list($p_posterr,$p_postmsg) = mUnsurNilaiMhs::delete($conn,$r_key.'|'.$t_npm.'|'.$t_idunsur);
						
						if($p_posterr) {
							$ok = false;
							break;
						}
					}
					
					// masukkan krs
					if($ok) {
						$t_dipakai = (int)$_POST['dipakai_'.$t_npm];
						
						$record = array();
						$record['nnumerik'] = CStr::cStrNull(CStr::cStrDec(str_replace('.',',',$_POST['nnumerik'][$t_idx])));
						$record['dipakai']=-1;
						if($t_allnull)
							$record['nangka'] = 'null';
							
						if($_POST['nremidi'][$t_idx] != null){
						$record['nremidi'] = CStr::cStrNull(CStr::cStrDec(str_replace('.',',',$_POST['nremidi'][$t_idx])));
						//get nilai huruf
						$jenjang = $conn->GetRow("select kode_jenjang_studi from akademik.ak_prodi where kodeunit='".$param[2]."'"); //get kodejenjang
						$nilaihuruf = $conn->GetRow("select nangkasn, nhuruf from akademik.ak_skalanilai where thnkurikulum = '".$param[0]."' and programpend='".$jenjang['kode_jenjang_studi']."' and (".$record['nremidi']." between batasbawah and batasatas)");
						$record['nhurufremidi'] = $nilaihuruf['nhuruf'];
					}
					
						list($p_posterr,$p_postmsg) = mKRS::updateRecord($conn,$record,$r_key.'|'.$t_npm,true);
						if($p_posterr)
							$ok = false;
						
						break;
					}
				}
			}
		}
		
		$conn->CommitTrans($ok);
	}
	//else if($r_act == 'close' and $c_tutup)
	else if($r_act == 'close') {
		$record = array();
		$record['nilaimasuk']=-1;
		
		$record['usernilaimasuk'] = Modul::getUserName();
		$record['waktunilaimasuk'] = date('Y-m-d H:i:s');

		$record2 = array();
		$record2['dipakai']=-1;
		
		list($p_posterr,$p_postmsg) = mKelas::updateRecord($conn,$record,$r_key,true);
		foreach($_POST['npm'] as $t_idx => $t_npm) {
			list($p_posterr,$p_postmsg) = mKRS::updateRecord($conn,$record2,$r_key.'|'.$t_npm,true);
		}
		//print_r($r_key.'|'.$t_npm);
		if(!$p_posterr)
			$a_infokelas['nilaimasuk'] = $record['nilaimasuk'];
	}
	else if($r_act == 'deleteparameter') {
		$conn->Execute("delete from akademik.ak_unsurpenilaian where idunsurnilai='".$_POST['idparameter']."'");
	}
	else if($r_act == 'updateparameter') {
		//$val=$_POST['r_'.$_POST['idparameter']];
		//$conn->Execute("update akademik.ak_unsurpenilaian set prosentasenilai = '$val' where idunsurnilai='".$_POST['idparameter']."'");
		$record=array();
		$record['prosentasenilai']=$_POST['r_'.$_POST['idparameter']];
		list($p_posterr,$p_postmsg)=mUnsurNilaiKelas::updateRecord($conn,$record,$_POST['idparameter'],true);
		
	}
	else if($r_act == 'saveparameter' and $c_edit) {
		
		$record = array();
		$param = explode('|',$_POST['key']);
		$record['namaunsurnilai'] = $_POST['subjek'];
		$record['prosentasenilai'] = $_POST['bobot'];
		$record['thnkurikulum'] = $param[0];
		$record['periode'] = $param[3];
		$record['kodemk'] = $param[1];
		$record['kelasmk'] = $param[4];
		$record['kodeunit'] = $param[2];
		
		//cek apakah melebihi 100%
		/*$rs_persen = $conn->GetOne("select sum(prosentasenilai::numeric) as persen from akademik.ak_unsurpenilaian where thnkurikulum='".$param[0]."' and periode='".$param[3]."' and 
							kodeunit='".$param[2]."' and kodemk='".$param[1]."' and kelasmk='".$param[4]."'");
		$tot_persen = $rs_persen + $record['prosentasenilai'];
		if($tot_persen > 100 ){
			$p_posterr = true;
			$p_postmsg = 'Persentase melebihi 100%. Cek kembali persentase.';
		}else{
			Query::recInsert($conn,$record,'akademik.ak_unsurpenilaian');
		}*/
		list($p_posterr,$p_postmsg)=mUnsurNilaiKelas::insertRecord($conn,$record,true);
		
		// list($p_posterr,$p_postmsg) = mKelas::insertRecord($conn,$record,$r_key,true);
	}
	else if($r_act == 'kuncinilai' and $canKunciNilai) {
		
		$record = array();
		$record['kuncinilai'] = -1;
		$record['tglkuncinilai'] = date('Y-m-d');
		$record['userkuncinilai'] = Modul::getUserName();
		
		list($p_posterr,$p_postmsg) = mKelas::updateRecord($conn,$record,$r_key,true);
		
		if(!$p_posterr)
			$a_infokelas['kuncinilai'] = $record['kuncinilai'];
	}
	else if($r_act == 'batalkunci') {
		$record = array();
		$record['kuncinilai'] = 0;
		$record['tglkuncinilai'] = 'null';
		$record['userkuncinilai'] = 'null';
		
		list($p_posterr,$p_postmsg) = mKelas::updateRecord($conn,$record,$r_key,true);
		if(!$p_posterr)
			$a_infokelas['kuncinilai'] = $record['kuncinilai'];
	}
	else if($r_act == 'open' and $c_buka) {
		$record = array();
		$record['nilaimasuk'] = 0;
		$record['usernilaimasuk'] = 'null';
		$record['waktunilaimasuk'] = 'null';
		$record2 = array();
		$record2['dipakai']=0;
		list($p_posterr,$p_postmsg) = mKelas::updateRecord($conn,$record,$r_key,true);
		foreach($_POST['npm'] as $t_idx => $t_npm) {
			list($p_posterr,$p_postmsg) = mKRS::updateRecord($conn,$record2,$r_key.'|'.$t_npm,true);
		}
		if(!$p_posterr)
			$a_infokelas['nilaimasuk'] = $record['nilaimasuk'];
	}
	else if($r_act == 'downxls') {
		$a_header = array('NIM','NAMA');
		$a_text = array('NIM' => true);
		
		// header dari unsur nilai, mengambil persen juga
		$i = count($a_header);
		$a_persen = array();
		// foreach($a_unsurnilai as $t_data) {
			// $a_header[] = strtoupper($t_data['namaunsurnilai']);
			// $a_persen[$i++] = (float)$t_data['prosentasenilai'];
		// }
		foreach($a_unsurnilaikey as $t_data) {
			$a_header[] = strtoupper($a_unsurnilainama[$t_data]);
			$a_persen[$i++] = (float)$a_unsurnilaiparameter[$t_data];
		}
		
		// header khusus
		$a_header[] = 'NA';
		$a_header[] = 'NREMIDI';
		
		// data peserta
		$a_huruf = CStr::arrayHuruf();
		$a_data = mKelas::getDataPeserta2($conn,$r_key);
		$a_data_bb = mKelas::getDataPesertaBelumBayar($conn,$r_key);
		$a_unsurnilaimhs = mUnsurNilaiMhs::getDataKelas($conn,$r_key);
		
		$a_dataxls = array();
		foreach($a_data as $t_data) {
			$t_key = trim($t_data['nim']);
			$t_nilai = $a_unsurnilaimhs[$t_key];
			
			$t_dataxls = array();
			$t_dataxls['nim'] = $t_key;
			$t_dataxls['nama'] = trim($t_data['nama']);
			
			$t_dataxls['na'] = $t_data['nnumerik'];
			$t_dataxls['nremidi'] = $t_data['nremidi'];
			
			// dari unsur nilai
			foreach($a_unsurnilai as $keyunsurnilai=>$t_data){ 
			if ($t_data=='absensi')
				$t_dataxls[strtolower($t_data)] = $a_absensi[$t_key];
			else
				$t_dataxls[strtolower($t_data)] = $t_nilai[$keyunsurnilai];
			}
			$a_dataxls[] = $t_dataxls;
		}
		
		
		// menghilangkan segala echo
		ob_clean();
		
		header("Content-Type: application/msexcel");
		header('Content-Disposition: attachment; filename="template_nilai.xls"');
		
		// pakai phpexcel
		require_once($conf['includes_dir'].'phpexcel/PHPExcel.php');
		
		$xls = new PHPExcel();
		$xls->setActiveSheetIndex(0);
		$sheet = $xls->getActiveSheet();
		
		// header
		$r = 1;
		foreach($a_header as $i => $t_header)
			$sheet->setCellValue($a_huruf[$i].$r,$t_header);
		
		// data
		if(empty($a_unsurnilai))
			$t_hitungna = false;
		else
			$t_hitungna = true;
		//print_r($a_unsurnilai);die();
		foreach($a_dataxls as $row) {
			$r++;
			
			$i = -1;
			foreach($a_header as $k => $v) {
				$i++;
				
				// nilai angka pakai formula
				if($v == 'NA' and $t_hitungna) {
					$a_formula = array();
					foreach($a_persen as $j => $t_persen)
						$a_formula[] = '('.$a_huruf[$j].$r.'*'.$t_persen.')';
					
					$sheet->setCellValue($a_huruf[$i].$r,'=round(('.implode('+',$a_formula).')/100,2)');
				}
				else {
					$t_data = $row[strtolower($v)];
					
					if($a_text[$v])
						$sheet->getCell($a_huruf[$i].$r)->setValueExplicit($t_data,PHPExcel_Cell_DataType::TYPE_STRING);
					else
						$sheet->setCellValue($a_huruf[$i].$r,$t_data);
				}
			}
		}
		
		// paskan ukuran
		$n = count($a_header);
		for($i=0;$i<$n;$i++)
			$sheet->getColumnDimension($a_huruf[$i])->setAutoSize(true);
		
		$xlsfile = PHPExcel_IOFactory::createWriter($xls,'Excel5');
		$xlsfile->save('php://output');
		
		exit;
	}
	else if($r_act == 'upxls' and $c_edit) {
		$r_file = $_FILES['xls']['tmp_name'];
		
		// pakai excel reader
		require_once($conf['includes_dir'].'phpexcel/excel_reader2.php');
		$xls = new Spreadsheet_Excel_Reader($r_file);
		
		$cells = $xls->sheets[0]['cells'];
		$numrow = count($cells);
		
		// jika cells kosong (mungkin bukan merupakan format excel), baca secara csv
		if(empty($numrow)) {
			if(($handle = fopen($r_file, 'r')) !== false) {
				while (($data = fgetcsv($handle, 1000, "\t")) !== false) {
					$numrow++;
					foreach($data as $k => $v)
						$cells[$numrow][$k+1] = $v;
				}
				fclose($handle);
			}
		}
		
		// baris pertama adalah header
		$conn->BeginTrans();
		
		$ok = true;
		for($r=2;$r<=$numrow;$r++) {
			$data = $cells[$r];
			
			$rowxls = array();
			foreach($cells[1] as $k => $v) {
				$v = strtolower($v);
				$rowxls[$v] = trim($data[$k]);
			}
			
			$t_npm = $rowxls['nim'];
				
			// masukkan unsur nilai mahasiswa
			$record = mKelas::getKeyRecord($r_key);
			$record['nim'] = $t_npm;
				
			$t_allnull = true;
			$t_numerik = '';
			if(!empty($a_unsurnilaikey)) {
				foreach($a_unsurnilaikey as $t_unsur) {
					// $t_idunsur = $t_unsur['idunsurnilai'];
					$t_idunsur = $t_unsur;
					$t_namaunsur = strtolower($a_unsurnilainama[$t_unsur]);
					
					$record['idunsurnilai'] = $t_idunsur;
					$record['nilaiunsur'] = CStr::cNumNull($rowxls[$t_namaunsur]);
					
					if($record['nilaiunsur'] != 'null') {
						$record['nilaiunsur'] = round($record['nilaiunsur'],2);
						
						// untuk menghitung nilai numerik
						if(empty($t_numerik))
							$t_numerik = 0;
						
						// $t_numerik += ($record['nilaiunsur']*$t_unsur['prosentasenilai'])/100;
						$t_numerik += ($record['nilaiunsur']*$a_unsurnilaiparameter[$t_unsur])/100;
						
						$t_allnull = false;
						list($p_posterr,$p_postmsg) = mUnsurNilaiMhs::saveRecord($conn,$record,$r_key.'|'.$t_npm.'|'.$t_idunsur,true);
					}
					else
						list($p_posterr,$p_postmsg) = mUnsurNilaiMhs::delete($conn,$r_key.'|'.$t_npm.'|'.$t_idunsur);
					
					if($p_posterr) {
						$ok = false;
						break;
					}
				}
			}
			else
				$t_numerik = CStr::cNumNull($rowxls['na']);
			
			// masukkan krs
			if($ok) {
				$record = array();
				$record['nnumerik'] = CStr::cNumNull($t_numerik);
				$record['nremidi'] = CStr::cNumNull($rowxls['nremidi']);
				
				if($record['nremidi'] != 'null'){
					$jenjang = $conn->GetRow("select kode_jenjang_studi from akademik.ak_prodi where kodeunit='".$param[2]."'"); //get kodejenjang
					$nilaihuruf = $conn->GetRow("select nangkasn, nhuruf from akademik.ak_skalanilai where thnkurikulum = '".$param[0]."' and programpend='".$jenjang['kode_jenjang_studi']."' and (".$record['nremidi']." between batasbawah and batasatas)");
					$record['nhurufremidi'] = $nilaihuruf['nhuruf'];
				}
				
				//if($record['nnumerik'] != 'null')
					//$record['nnumerik'] = round($record['nnumerik']);
				
				if($t_allnull)
					$record['nangka'] = 'null';
				
				list($p_posterr,$p_postmsg) = mKRS::updateRecord($conn,$record,$r_key.'|'.$t_npm,true);
				if($p_posterr) {
					$ok = false;
					break;
				}
			}
		}
		
		if($ok)
			$p_postmsg = 'Impor data dari format excel berhasil';
		
		$conn->CommitTrans($ok);
	}
	
	//cek apakah sdh di kunci
	$kuncinilai = $conn->GetRow("select u.userdesc,tglkuncinilai,userkuncinilai, kuncinilai from akademik.ak_kelas ak left join gate.sc_user u on u.username=ak.userkuncinilai
				where thnkurikulum='".$param[0]."' and periode='".$param[3]."' and 
				kodeunit='".$param[2]."' and kodemk='".$param[1]."' and kelasmk='".$param[4]."'");
	if($kuncinilai['kuncinilai'] == '-1'){
		$showKunci=false;
		$c_kunci=true;
	}else{
		$showKunci=true;
		$c_kunci=false;
	}
	
	// cek ulang hak akses
	if(!Akademik::isAdmin()){
	if($a_infokelas['periode'] == $periodepenilaian) {
		if(!empty($a_infokelas['nilaimasuk']) and !$c_kunci) {
			$c_edit = false;
			$p_postmsg = 'Nilai perkuliahan kelas ini sudah disahkan';
		}
		else if(!empty($a_infokelas['nilaimasuk']) and $c_kunci) {
			$c_edit = false;
			$p_postmsg = "Nilai perkuliahan kelas ini sudah disahkan dan dikunci oleh <b>".$kuncinilai['userdesc']."</b> pada tanggal ".date('d-m-Y',strtotime($kuncinilai['tglkuncinilai']));
		}
		else if(!Akademik::isAdmin() and Akademik::getIsiNilai() == 'DITUTUP') {
			$c_edit = false;
			$p_postmsg = 'Periode penilaian sudah ditutup';
		}else if($c_kunci){
			// $p_posterr=true;
			$c_edit = false;
			$p_postmsg ="Nilai Sudah dikunci oleh <b>".$kuncinilai['userdesc']."</b> pada tanggal ".date('d-m-Y',strtotime($kuncinilai['tglkuncinilai']));
		}
		else
			$c_edit = $a_auth['canupdate'];
		if(Akademik::isNilai() OR Akademik::isMM()){
			$c_edit = $a_auth['canupdate'];
		}
	}
	else
		$p_postmsg = 'Periode penilaian untuk perkuliahan ini sudah berlalu';
	}
	// mendapatkan data
	$a_data = mKelas::getDataPeserta2($conn,$r_key); 
	$a_data_bb = mKelas::getDataPesertaBelumBayar($conn,$r_key);
	$a_absensi = mAbsensiKuliah::getListPersenPerKelas($conn,$r_key);
	$a_unsurnilaimhs = mUnsurNilaiMhs::getDataKelas($conn,$r_key);
	
	//cek apakah dosen Koordinator atau admin? jika ya bisa nambahkan parameter
	$ispjmk = false;
	if(Modul::getRole() == 'A'){
		$ispjmk = true;
	}else{
		$isdosenpjmk = $conn->GetRow("select ispjmk from akademik.ak_mengajar where jeniskul='K' and thnkurikulum='".$param[0]."' and periode='".$param[3]."' and 
				kodeunit='".$param[2]."' and kodemk='".$param[1]."' and kelasmk='".$param[4]."' and nipdosen='".Modul::getUserName()."'");
		if($isdosenpjmk['ispjmk']==1)
			$ispjmk=true;
	}
	
	$rs_param = $conn->Execute("select *,coalesce(prosentasenilai::int, 0) as persen from akademik.ak_unsurpenilaian where thnkurikulum='".$param[0]."' and periode='".$param[3]."' and 
				kodeunit='".$param[2]."' and kodemk='".$param[1]."' and kelasmk='".$param[4]."' order by idunsurnilai");
				
		$a_unsurnilai = array();		
		$a_unsurnilaiparameter = array();
		$a_unsurnilaikey = array();
		while($row = $rs_param->FetchRow()){
			$a_unsurnilai[$row['idunsurnilai']] = $row['namaunsurnilai'];		
			$a_unsurnilaiparameter[$row['idunsurnilai']] = $row['prosentasenilai'];
			$a_unsurnilaikey[] = $row['idunsurnilai'];
		}
		$rs_param->MoveFirst();
		
?>

<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">

			<div style="float:left; width:100%">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<center>
				<?php require_once('inc_headerkelas.php') ?>
				</center>
				<br>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<?	if(isset($p_posterr)) { ?>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				<?	} else { ?>
				<div style="width:<?= $p_tbwidth ?>px;font-size:14px">
					<strong><?= $p_postmsg ?></strong>
				</div>
				<?	} ?>
				</center>
				<div class="Break"></div>
				<?	}
					if($c_edit or !empty($a_infokelas['nilaimasuk'])) {
				?>
				<center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<?	if($c_edit) { ?>
						<?php /*<td align="center" colspan="2">Impor Data dari Format Excel</td>*/?>
						<?	}
							if(!empty($a_infokelas['nilaimasuk'])) { ?>
						<td align="center">Versi Cetak</td>
						<?	} ?>
					</tr>
					<tr class="NoHover NoGrid">
						<?	if($c_edit) { ?>
						<?php /*<td width="55"> &nbsp;
							<strong>Upload </strong>
						</td>
						 <td>
							<strong> : </strong> <input type="file" name="xls" id="xls" size="30" class="ControlStyle">
							<input type="button" value="Upload" onclick="goUpXLS()"> &nbsp; &nbsp;
							<u class="ULink" onclick="goDownXLS()">Download Template Excel...</u>
						</td>
						*/ ?>
						<?	} ?>
						<tr>
							<td align="center" colspan="2">Ekspor</td>
						</tr>
						<td>
							<u class="ULink" onclick="goPrint(false)">Cetak Nilai...</u>
							<u class="ULink" onclick="goPrint(true)">Download Versi Excel...</u>
						</td>
					</tr>
				</table>
				<? if($a_infokelas['isonline']==-1){ ?>
									<b><h3>Kolom Nilai Tugas Online Diambil Dari elearning.inaba.ac.id <h3></b>
									<b><h3>Untuk Memasukannya Ke Siakad Silahkan Ketik Ulang Nilai Pada kolom Tugas 30 %<h3></b>
				<? } ?>
				</center>
				<br>
				<table>
				<tr>
				    <td>
				<table width="250" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td align="center" colspan="3">Parameter Nilai</td>
					</tr>
					<tr class="DataBG">
						<td align="center">Subjek</td>
						<td align="center">Bobot(%)</td>
						<? if (!$c_kunci) {?>
						<td align="center" >Aksi</td>
						<? } ?>
					</tr>
					<? $i=0;while($row = $rs_param->FetchRow()){?>
							<tr class="NoHover NoGrid">
								<td align="center"><?= $row['namaunsurnilai']?></td>
								<td align="center"><input type="text" name="<?= "r_".$row['idunsurnilai']?>" id="<?= "r_".$row['idunsurnilai']?>" value="<?= $row['prosentasenilai']?>" style="width:30px" <?= $r_defaultunsur ? 'readonly' : '' ?>></td>
								<td align="center">
									<?if(($c_edit and $ispjmk and !$c_kunci) and (!$r_defaultunsur)){?>
										<img title="Update Prosentase Nilai" src="images/disk.png" onclick="goUpdateParameter(this,'<?= $row['idunsurnilai']?>')" style="cursor:pointer">
										<img title="Update Prosentase Nilai" src="images/delete.png" onclick="goDeleteParameter(this,'<?= $row['idunsurnilai']?>')" style="cursor:pointer">
									<?}else if(((Modul::getRole()=='A' or Modul::getRole()=='AKD') and (!$c_kunci)) AND (!$r_defaultunsur)){?>
										<img title="Update Prosentase nilai" src="images/disk.png" onclick="goUpdateParameter(this,'<?= $row['idunsurnilai']?>')" style="cursor:pointer">
										<img title="Update Prosentase nilai" src="images/delete.png" onclick="goDeleteParameter(this,'<?= $row['idunsurnilai']?>')" style="cursor:pointer">
									<?}?>
								</td>
							</tr>
					<?$i++;}
					if($i==0){?>
						<tr class="NoHover NoGrid"><td colspan=3 align="center">Data tidak ditemukan</td></tr>
					<?}?>
					<?  if((!$c_kunci) and !$r_defaultunsur){?>
					<tr class="NoHover NoGrid">
						<td align="center"><input type="text" name="subjek" maxlength="50" size="10"></td>
						<td align="center"><input type="text" name="bobot" maxlength="3" size="5"></td>
						<td align="center"><img id="<?= $t_key ?>" title="Simpan Parameter Nilai" src="images/disk.png" onclick="goSaveParameter(this)" style="cursor:pointer"></td>
					</tr>
					<?} ?>
					
				</table>
				    </td>
				</tr>
				</table>
				<div style="clear:both"></div>

				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
								<h1><?= $p_title ?></h1>
							</div>
							<? /* if(Modul::getRole() !='D'){?>
							<div class="left" style="padding-top:8px;padding-right:50px">
								<div><input id="<?= $r_key?>" type="button" value="Input Nilai Huruf" onclick="goNilaiHuruf(this)"></div>
							</div>
							<?} */?>
							<div class="right" style="padding-top:8px;padding-right:10px">
								
							</div>
							<? if(!empty($a_infokelas['nilaimasuk'])) { ?>
							<div class="right"> 
								<img title="Cetak Nilai" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">
							</div>
							<? } ?>
							
						</div>
					</header>
				</center>
				<?	/*************/
					/* LIST DATA */
					/*************/
				?>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
						<th rowspan="2" width="25">No.</th>
						<th rowspan="2" width="80">NIM</th>
						<th rowspan="2">Nama</th>
					<? if($a_infokelas['isonline']=='-1'){ ?>
						<th width="50">Tugas Online</th>
					<? } ?>
					<?	foreach($a_unsurnilai as $t_unsur) { ?>
						<th width="50"><?= $t_unsur ?></th>
					<?	} ?>
						<th rowspan="2" width="40">Nilai Angka Mutu</th>
						<th rowspan="2" width="40">Nilai Remidi</th>
						<th rowspan="2" width="30">Nilai Huruf Mutu</th>
						
					</tr>
					<tr>
					<? if($a_infokelas['isonline']=='-1'){ ?>
						<th width="50">Elearning</th>
					<? } ?>
					<?	foreach($a_unsurnilaikey as $t_unsur) {  ?>
						<th><?= $a_unsurnilaiparameter[$t_unsur] ?> %</th>
					<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							$t_key = trim($row['nim']);
							
							// cek absen
							//if($a_absensi[$t_key] < 75)
								//$rowstyle = 'YellowBG';
							//else if ($i % 2)
								//$rowstyle = 'NormalBG';
							//else
								//$rowstyle = 'AlternateBG';
							$i++;
							
							$t_nilai = $a_unsurnilaimhs[$t_key];
							$t_nremidi = $row['nremidi'];
							$t_nnumerik = $row['nnumerik'];
							/*if(strval($t_nnumerik) != '' or $t_nremidi !=''){
								$t_nnumerik = round($t_nnumerik);
								$t_nremidi = round($t_nremidi);
								}*/
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $i ?>.</td>
						<td align="center">
							<?= $row['nim'] ?>
							<input type="hidden" name="npm[]" value="<?= $t_key ?>">
							<input type="hidden" name="simpan[]" value="0">
							
							
						</td>
						<td><?= $row['nama'] ?></td>
						<? if($a_infokelas['isonline']=='-1'){ ?>
							<td align="center"><?= UI::createTextBox('n_'.$row['nim'].$r_key.'[]',str_replace('.',',',mKelas::getDataNilaiTugas($conn_moodle,$row['nim'],$r_keyelearn)),'ControlStyle XCell nunsur',5,3,$c_edit,' readonly') ?></td>
					<? } ?>
						
					<?	foreach($a_unsurnilaikey as $key => $t_unsur2) { 
						if (strtolower($a_unsurnilai[$t_unsur2])=='absensi'){
						?>
							<td align="center"><?= UI::createTextBox('n_'.$t_unsur2.'[]',str_replace('.',',',$a_absensi[$t_key]),'ControlStyle XCell nunsur',5,3,$c_edit,'onblur="hitungNilai(this)" readonly') ?></td>
						<?
						}else{
						?>
							<td align="center"><?= UI::createTextBox('n_'.$t_unsur2.'[]',str_replace('.',',',$t_nilai[$t_unsur2]),'ControlStyle XCell nunsur',5,3,$c_edit,'onkeydown="return onlyNumber(event,this,true)" onblur="hitungNilai(this)"') ?></td>
						<?	}
						}
						if(empty($n_unsurnilai)) {  ?>
						<td align="center"><?= UI::createTextBox('nnumerik[]',$t_nnumerik,'ControlStyle XCell nnumerik',5,3,$c_edit,'onkeydown="return onlyNumber(event,this)" onblur="setSimpan(this)"') ?></td>
					<?	}
						else { ?>
						<td align="center"><?= UI::createTextBox('nnumerik[]',$t_nnumerik,'ControlRead XCell nnumerik',5,3,$c_edit,'readonly') ?></td>
					<?	} ?>
						
						<td align="center"><?= UI::createTextBox('nremidi[]',$t_nremidi,'ControlStyle XCell nremidi',5,3,$c_edit,'onblur="setSimpan(this)"') ?></td>
						<td align="center"> <?php 
						$cek = mLaporanMhs::cekInputanNilai($conn,$periode,$a_infokelas['kodemk'],$row['nim']);
						$cek2 =  mLaporanMhs::cekInputanNilaiLengkap($conn,$periode,$a_infokelas['kodemk'],$row['nim']);
							if($cek==0 and $cek2==3){
								echo $row['nhuruf'];
							}else{
								echo "T";
							} 

						?>
						</td>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}
						if($c_edit) { ?>
					<tr class="LeftColumnBG">
						<td colspan="<?= $p_colnum ?>" align="center">
							<input type="button" value="Simpan" onclick="goSaveAll()" style="font-size:14px">
							<?if($canKunciNilai and $showKunci){?>
								<input type="button" value="Kunci Nilai" onclick="goKunciNilai(this)" style="font-size:14px">
							<?}if($canKunciNilai and !$showKunci){?>
								<input type="button" value="Batalkan Kunci Nilai" onclick="goBatalKunciNilai(this)" style="font-size:14px">
							<?} ?>
						</td>
					</tr>
					<?	}else{ ?>
					<tr class="LeftColumnBG">
						<td colspan="<?= $p_colnum ?>" align="center">
							<?if($canKunciNilai and $showKunci){?>
								<input type="button" value="Kunci Nilai" onclick="goKunciNilai(this)" style="font-size:14px">
							<?}if($canKunciNilai and !$showKunci){?>
								<input type="button" value="Batalkan Kunci Nilai" onclick="goBatalKunciNilai(this)" style="font-size:14px">
							<?}?>
						</td>
					</tr>
					<?	} ?>
				</table>
				<br>
				<br>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr>
						<th colspan="3"><center><h3>Mahasiswa Yang Belum Melunasi Tagihan Untuk UTS</h3></center></th>
					</tr>
					<tr>
						<th><center>No</center></th>
						<th><center>NIM</center></th>
						<th style="text-align: left !important;">Nama</th>
					</tr>
					
						<?php $no = 1; foreach ($a_data_bb as $huhu) { ?>
						<tr valign="top" class="<?= $rowstyle ?>">
							<td><center><?= $no; ?></center></td>
							<td><center><?php echo $huhu['nim']?></center></td>
							<td><?php echo $huhu['nama']?></td>
						</tr>
						<?php $no++;} ?>
					
					
				</table>
				
				<?if($a_infokelas['kuncinilai'] != -1){?>
					<?	if($c_tutup and empty($a_infokelas['nilaimasuk'])) { ?>
					<br>
					<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
						<? /*if($n_riwayat < $p_minriwayat) { ?>
						<tr>
							<td align="center" style="font-size:14px;padding:10px">
								Nilai tidak bisa disahkan karena jumlah jurnal perkuliahan yang berstatus Selesai <?= $n_riwayat ?>, kurang dari <?= $p_minriwayat ?>.<br>
								Untuk memasukkan jurnal perkuliahan, klik <u class="ULink" onclick="goSubmitBlank('<?= Route::navAddress('list_jurnal') ?>')">di sini</u>
							</td>
						</tr>
						<?	} else {*/ ?>
						<tr class="DataBG">
							<td>Pengesahan Nilai</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px;padding:10px">
								<?= $p_pesan ?>
							</td>
						</tr>
						<tr class="LeftColumnBG">
							<td align="center"><input type="button" value="Sahkan Nilai" onclick="goClose()" style="font-size:14px"></td>
						</tr>
						<?	//} ?>
					</table>
					<?	}
						else if($c_buka and !empty($a_infokelas['nilaimasuk'])) { ?>
					<br>
					<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
						<tr class="DataBG">
							<td>Pembatalan Pengesahan Nilai</td>
						</tr>
						<tr>
							<td align="center" style="font-size:14px;padding:10px">
								Lakukan pembatalan pengesahan nilai jika nilai terpaksa harus diubah.
							</td>
						</tr>
						<tr class="LeftColumnBG">
							<td align="center"><input type="button" value="Batalkan Pengesahan Nilai" onclick="goOpen()" style="font-size:14px"></td>
						</tr>
					</table>
					<?	} ?>
				<?	} ?>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= Akademik::base64url_encode($r_key) ?>">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="format" id="format">
				<input type="hidden" name="idparameter" id="idparameter">
			</form>
			</div>
		</div>
	</div>
</div>

	
	
<script type="text/javascript">
var flagtoefl='<?=$flagtoefl?>';

$(document).ready(function() {
	initXCell();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
});

function toggleDipakai() {
	var check = $("[name^='dipakai_']").attr("checked");
	$("[name='simpan[]']").val(1);
	if(check){
		$("[name^='dipakai_']").attr("checked",false);
	}else{
		$("[name^='dipakai_']").attr("checked",true);
		
	}
}

function hitungNilai(elem) {
	var tr = setSimpan(elem);
	
	var subnilai
	var nilai = "";
	<?	foreach($a_unsurnilaikey as $t_unsur) { ?>
	subnilai = jQuery.trim(tr.find("[name='n_<?= $t_unsur ?>[]']").val());
	if(subnilai != "") {
		if(nilai == "") nilai = 0;
		nilai += (formatNumber(subnilai) * <?= $a_unsurnilaiparameter[$t_unsur] ?>);
	}
	<?	} ?>
	
	if(nilai != "") {
		nilai = nilai/100;
		if((nilai > 100) && (flagtoefl=='0'))
			nilai = 100;
		else
			nilai = nilai.toFixed(2);
	}
		
	tr.find("[name='nnumerik[]']").val(nilai);
}

// function hitungNilai(elem) {
	// var tr = setSimpan(elem);
	
	// var subnilai
	// var nilai = "";
	// <?	foreach($a_unsurnilai as $t_unsur) { ?>
	// subnilai = jQuery.trim(tr.find("[name='n_<?= $t_unsur['idunsurnilai'] ?>[]']").val());
	// if(subnilai != "") {
		// if(nilai == "") nilai = 0;
		// nilai += (formatNumber(subnilai) * <?= $t_unsur['prosentasenilai'] ?>);
	// }
	// <?	} ?>
	
	// if(nilai != "") {
		// nilai = nilai/100;
		// if(nilai > 100)
			// nilai = 100;
		// else
			// nilai = Math.round(nilai);
	// }
		
	// tr.find("[name='nnumerik[]']").val(nilai);
// }

function setSimpan(elem) {
	var tr = $(elem).parents("tr:eq(0)");
	
	tr.find("[name='simpan[]']").val(1);
	
	return tr;
}

function goSave(elem) {
	// aktifkan npm
	$(elem).parents("tr:eq(0)").find("[name='simpan[]']").val(1);
	
	document.getElementById("act").value = "save";
	document.getElementById("subkey").value = elem.id;
	goSubmit();
}

function goSaveAll() {
	document.getElementById("act").value = "saveall";
	goSubmit();
}

function goClose() {
	var tutup = confirm("Apakah anda yakin akan mengesahkan nilai? Nilai tidak bisa diubah lagi.\nBila anda baru melakukan perubahan nilai, Simpan terlebih dahulu");
	if(tutup) {
		document.getElementById("act").value = "close";
		goSubmit();
	}
}

function goOpen() {
	var buka = confirm("Apakah anda yakin akan membatalkan pengesahan nilai?");
	if(buka) {
		document.getElementById("act").value = "open";
		goSubmit();
	}
}

function goDownXLS() {
	document.getElementById("act").value = "downxls";
	goSubmit();
}

function goUpXLS() {
	var upload = confirm("Apakah anda yakin akan mengupdate data dari format excel?");
	if(upload) {
		document.getElementById("act").value = "upxls";
		goSubmit();
	}
}

function goPrint(xls) {
	var form = document.getElementById("pageform");
	
	form.action = "<?= Route::navAddress('rep_nilai') ?>";
	form.target = "_blank";
	
	if(xls)
		document.getElementById("format").value = "xls";
	else
		document.getElementById("format").value = "html";
	
	goSubmit();
	
	form.action = "";
	form.target = "";
}

function goDeleteParameter(elem,idparam) {
	document.getElementById("act").value = "deleteparameter";
	document.getElementById("idparameter").value = idparam;
	document.getElementById("subkey").value = elem.id;
	goSubmit();
}
function goUpdateParameter(elem,idparam) {
	document.getElementById("act").value = "updateparameter";
	document.getElementById("idparameter").value = idparam;
	document.getElementById("subkey").value = elem.id;
	goSubmit();
}

function goSaveParameter(elem) {
	document.getElementById("act").value = "saveparameter";
	document.getElementById("subkey").value = elem.id;
	goSubmit();
}

function goKunciNilai(elem) {
	var kunci = confirm("Apakah Anda yakin akan mengunci nilai ? \nJika nilai peserta pada kelas ini ada yang kosong, akan secara otomatis nilai di update menggunakan nilai pemutihan !");
	if(kunci){
		document.getElementById("act").value = "kuncinilai";
		document.getElementById("subkey").value = elem.id;
		goSubmit();
	}
}

function goBatalKunciNilai(elem) {
	document.getElementById("act").value = "batalkunci";
	document.getElementById("subkey").value = elem.id;
	goSubmit();
}

function goNilaiHuruf(elem){
	var detailpage = "<?= Route::navAddress('set_nilaihuruf') ?>";
	location.href = detailpage + "&key=" + elem.id;
}

</script>
</body>
</html>
