<? 
	require_once($conf['includes_dir']."phpxbase/Column.class.php");
	require_once($conf['includes_dir']."phpxbase/Record.class.php");
	require_once($conf['includes_dir']."phpxbase/Table.class.php");
	
	$conn->debug = true;
	
	$table = new XBaseTable($conf['uploads_dir']."MSDOS.DBF",array(NMDOSMSDOS));
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

	$i = 1;
	while ($row = $table->nextRecord()){
		$record = array();
		//if ($i < 2){
			foreach ($table->getColumns() as $n => $c){
				if ($n == 7) $record['nodosen'] = $row->getString($c);
				if ($n == 8) $record['namadepan'] = $row->getString($c);
				if ($n == 10) $record['gelarbelakang'] = $row->getString($c);
				if ($n == 24) $record['alamat1'] = $row->getString($c);
				if ($n == 25) $record['alamat2'] = $row->getString($c);
				if ($n == 27) $record['kodepos'] = CStr::cStrNull($row->getString($c));
				if ($n == 30) $record['tmplahir'] = CStr::cStrNull($row->getString($c));
				//if ($n == 29) $record['tgllahir'] = $row->getString($c);
				if ($n == 32) $record['jeniskelamin'] = trim($row->getString($c)) == 'W' ? 'P' : 'L';
				if ($n == 33) $record['idagama'] = CStr::cStrNull($row->getString($c));
				if ($n == 44) $record['nippns'] = CStr::cStrNull($row->getString($c));
				//if ($n == 49) $record['noktp'] = CStr::cStrNull($row->getString($c));
				if ($n == 53) $record['nidn'] = CStr::cStrNull($row->getString($c));
			}
			$record['alamat'] = CStr::cStrNull(trim($record['alamat1'].' '.$record['alamat']));
			$record['idtipepeg'] = 'PT';
			$record['idjenispegawai'] = 'D';
			Query::recInsert($conn,$record,'sdm.ms_pegawai');
			//print_r($record);
		//}
		//echo '<br />';
		$i++;
	}
?>