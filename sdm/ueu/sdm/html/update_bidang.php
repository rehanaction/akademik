<? 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );	
	ini_set('max_execution_time',50000000000);
	
	require_once(Route::getModelPath('presensi'));	
	
	$p_dbtable = 'pe_presensidet';
	$p_key = 'tglpresensi,idpegawai';
	
	$p_model = mPresensi;	
	
	$var = strtotime(date("Y-m-d"));
	$tglkemarin = date("Y-m-d",($var - 86400));
	
	$mulai=strtotime('2014-05-01');
	$selesai=strtotime($tglkemarin);
	
	while($mulai<=$selesai){
		$tgl = date("Y-m-d",$mulai);
				
		//cek bukan hari libur
		$isLibur = $p_model::isLibur($conn,$tgl);
		$a_peg = array();
		$a_lkpd = array();
		$a_lkpp = array();
		if(empty($isLibur)){
			$a_peg = $p_model::isAlpaKemarin($conn,$tgl);
			
			//insert alpa
			if(count($a_peg['insert']) > 0){
				foreach($a_peg['insert'] as $key => $val){
					$record = array();
					$record['kodeabsensi'] = "A";
					$record['idpegawai'] = $key;
					$record['tglpresensi'] = $tgl;
					$record['tglpemasukan'] = date("Y-m-d");
					$record['sjamdatang'] = $val['jamdatang'];
					$record['sjampulang'] = $val['jampulang'];
					$record['procpotkehadirantelat'] = 'null';
					$record['procpotkehadiranpd'] = 'null';
					$record['procpottransporttelat'] = 'null';
					$record['procpottransportpd'] = 'null';
					
					list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,$p_dbtable);
				}
			}
			
			//cek apakah sudah lengkap presensi
			$a_lkpd = $p_model::isDatangKosong($conn,$tgl);
			
			//update alpa
			if(count($a_lkpd) > 0){
				foreach($a_lkpd as $key => $val){
					$record = array();
					$record['kodeabsensi'] = "DK";
					$record['procpotkehadirantelat'] = 'null';
					$record['procpotkehadiranpd'] = 'null';
					$record['procpottransporttelat'] = 'null';
					$record['procpottransportpd'] = 'null';
					
					$r_key = $tgl.'|'.$key;
					
					list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_key,true,$p_dbtable,$p_key);
				}		
			}
			
			//cek apakah sudah lengkap presensi
			$a_lkpp = $p_model::isPulangKosong($conn,$tgl);
			
			//update alpa
			if(count($a_lkpp) > 0){
				foreach($a_lkpp as $key => $val){
					$record = array();
					$record['kodeabsensi'] = "PK";
					$record['procpotkehadirantelat'] = 'null';
					$record['procpotkehadiranpd'] = 'null';
					$record['procpottransporttelat'] = 'null';
					$record['procpottransportpd'] = 'null';
					
					$r_key = $tgl.'|'.$key;
					
					list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_key,true,$p_dbtable,$p_key);
				}		
			}
		}	
		
		$mulai+=86400;
	}
?>