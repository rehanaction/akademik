<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('presensi'));	
	$conn->debug = true;
	
	$p_dbtable = 'pe_presensidet';
	$p_key = 'tglpresensi,idpegawai';
	
	$p_model = mPresensi;

	$sql = "select idpegawai,tglpresensi from sdm.pe_presensidet where tglpresensi='2013-12-01'";
	$rs = $conn->Execute($sql);
	while ($row = $rs->FetchRow()){
		$conn->Execute("update sdm.pe_presensidet set t_username=t_username where idpegawai=$row[idpegawai] and tglpresensi='$row[tglpresensi]'");
	}
	
	/*$a_ahari = array();
	$a_ahari = $p_model::getDayPresensi();
	$a_hari = array();
	$a_hari = $a_ahari['aday'];
	
	$a_jamhadir = array();
	$a_jamhadir = $p_model::getJamHadir($conn);
	
	$mulai = strtotime('2013-09-16');
	$selesai = strtotime('2013-09-16');
	
	while($mulai<=$selesai){	
		$a_presensidate = array();
		$a_presensidate = $p_model::getPresensiDate($conn, date("Y-m-d", $mulai));
		
		$tglsekarang = date("Y-m-d", $mulai);
		$sql = "select r.idpegawai,cast('$tglsekarang' as date)  as tglsekarang, DATEPART(dw, cast('$tglsekarang' as date)) as nohari, k.* from sdm.pe_rwtharikerja r
					left join sdm.ms_kelkerja k on k.kodekelkerja=r.kodekelkerja 
					where cast('$tglsekarang' as date) BETWEEN tglawal and tglakhir";
		$rs = $conn->Execute($sql);
			
		$a_data = array();
		while ($row = $rs->FetchRow())
			$a_data[] = $row;
		
		if (count($a_data) > 0){
			foreach($a_data as $row){
				$record = array();
				$record['idpegawai'] = $row['idpegawai'];
				$record['tglpresensi'] = $row['tglsekarang'];
				$record['tglpemasukkan'] = $row['tglsekarang'];
				$record['sjamdatang'] = $a_jamhadir['jamdatang'][$row[$a_hari[$row['nohari']]]];
				$record['sjampulang'] = $a_jamhadir['jampulang'][$row[$a_hari[$row['nohari']]]];
				//$record['kodeabsensi'] = 'A';
				$r_key = $tglsekarang.'|'.$record['idpegawai'];
				
				$islibur = $p_model::isDataExist($conn, $tglsekarang, 'ms_liburdetail', 'tgllibur');
				if ($islibur)
					$record['kodeabsensi'] = 'L';
				else{
					$record['kodeabsensi'] = 'A';
					if (empty($record['sjamdatang']))
						$record['kodeabsensi'] = 'L';
				}
				
				if ($a_presensidate['idpegawai'][$row['idpegawai']] == $row['idpegawai']) //update
					list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_key,true,$p_dbtable,$p_key);		
				else //insert
					list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,$p_dbtable);		
			}
		}	
		
		$mulai+=86400;
	}*/
?>