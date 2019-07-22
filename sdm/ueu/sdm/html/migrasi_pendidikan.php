<? 
	require_once(Route::getModelPath('model'));
	$connsync = Query::connect('sync');
	
	ini_set('max_execution_time',2000000);
	require_once($conf['includes_dir']."phpxbase/Column.class.php");
	require_once($conf['includes_dir']."phpxbase/Record.class.php");
	require_once($conf['includes_dir']."phpxbase/Table.class.php");
	
	$connsync->debug = true;
	
	$a_jenjang = array("A"=>"53", "B"=>"52", "C"=>"51", "D"=>"sp1", "E"=>"sp2", "F"=>"44", "G"=>"43", "H"=>"42", "I"=>"41", "J"=>"31", "K"=>"21", "L"=>"11");
	
	$table = new XBaseTable($conf['uploads_dir']."MSPDS.DBF",array(NMDOSMSPDS));
	$table->open;
	echo "version: ".$table->version."<br />";
    echo "foxpro: ".($table->foxpro?"yes":"no")."<br />";
    echo "modifyDate: ".date("r",$table->modifyDate)."<br />";
    echo "recordCount: ".$table->recordCount."<br />";
    echo "headerLength: ".$table->headerLength."<br />";
    echo "recordByteLength: ".$table->recordByteLength."<br />";
    echo "inTransaction: ".($table->inTransaction?"yes":"no")."<br />";
    echo "encrypted: ".($table->encrypted?"yes":"no")."<br />";
    echo "mdxFlag: ".ord($table->mdxFlag)."<br />";
    echo "languageCode: ".ord($table->languageCode)."<br />";
	
	$sql = "select nodosen,idpegawai from sdm.ms_pegawai where nodosen is not null";
	$a_idpegawai = Query::arrQuery($connsync, $sql);

	$i = 1;
	while ($row = $table->nextRecord()){
		$record = array();
		//if ($i < 20){
			foreach ($table->getColumns() as $n => $c){
				if ($n == 7) $record['nodosen'] = $row->getString($c);
				if ($n == 9) $record['idpendidikan'] = $a_jenjang[$row->getString($c)];
				if ($n == 10) $record['namainstitusi'] = $row->getString($c);
				if ($n == 11) $record['fakultas'] = $row->getString($c);
				if ($n == 12) $record['jurusan'] = $row->getString($c);
				if ($n == 13) $record['bidang'] = $row->getString($c);
				if ($n == 14) $record['alamat'] = $row->getString($c);
				if ($n == 17) $record['tahunlulus'] = CStr::cStrNull($row->getString($c));
				if ($n == 20) $record['tglijazah'] = $row->getString($c);
				if ($n == 21) $record['gelar'] = CStr::cStrNull($row->getString($c));
			}
			$recrod['t_userid'] = 'mig';
			$record['idpegawai'] = $a_idpegawai[$record['nodosen']];
						
			if (!empty($record['idpegawai'])){
				$isExist = $conn->GetOne("select top 1 1 from sdm.pe_rwtpendidikantemp where idpendidikan='$record[idpendidikan]' 
				and idpegawai=(select idpegawai from sdm.ms_pegawai where nodosen='$record[nodosen]')");
				
				if ($isExist)
					Query::recUpdate($connsync,$record,'sdm.pe_rwtpendidikantemp'," idpendidikan='$record[idpendidikan]' and idpegawai=(select idpegawai from sdm.ms_pegawai where nodosen='$record[nodosen]')");
				else
					Query::recInsert($connsync,$record,'sdm.pe_rwtpendidikantemp');
			}
	}
?>