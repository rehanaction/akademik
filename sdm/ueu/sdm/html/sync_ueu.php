<? 
	
	require_once(Route::getModelPath('model'));
	
	$connsync = Query::connect('sync');
	
	$connsync->debug = true;
	
	$sql = "select * from sdm.ms_unit order by infoleft";
	$rs = $conn->Execute($sql);
	
	$sql = "select * from sdm.ms_unit order by infoleft";
	$rss = $connsync->Execute($sql);
?>
<table>
	<tr>
		<td valign="top">
			<table width="800px" border="1" style="border-collapse:collapse 1px">
				<tr>
					<td>IDUNIT</td>
					<td>Kodeunit</td>
					<td>Nama Unit</td>
					<td>Infoleft</td>
					<td>Inforight</td>
				</tr>
				<? 
				while ($row = $rs->FetchRow()){?>
				<tr>
					<td><?= $row['idunit']; ?></td>
					<td><?= $row['kodeunit']; ?></td>
					<td><?= $row['namaunit']; ?></td>
					<td><?= $row['infoleft']; ?></td>
					<td><?= $row['inforight']; ?></td>
				</tr>
				<? }  ?>
			</table>
		</td>
		<td valign="top">
			<table width="800px" border="1" style="border-collapse:collapse 1px">
				<tr>
					<td>IDUNIT</td>
					<td>Kodeunit</td>
					<td>Nama Unit</td>
					<td>Infoleft</td>
					<td>Inforight</td>
				</tr>
				<? while ($row = $rss->FetchRow()){?>
				<tr>
					<td><?= $row['idunit']; ?></td>
					<td><?= $row['kodeunit']; ?></td>
					<td><?= $row['namaunit']; ?></td>
					<td><?= $row['infoleft']; ?></td>
					<td><?= $row['inforight']; ?></td>
				</tr>
				<? }  ?>
			</table>
		</td>
	</tr>
</table>
<? 
	/*$sql = "select * from sdm.ms_unit order by infoleft";
	$rs = $conn->Execute($sql);
	
	while ($row = $rs->FetchRow())
		$a_in[] = $row['idunit'];
	
	$sql_in = implode("','", $a_in);
	
	$sql = "select * from sdm.ms_unit order by infoleft";
	$rss = $conn->Execute($sql);
	while ($row = $rss->FetchRow()){
		$record = array();
		$record = $row;
		$isExist = $connsync->GetRow("select 1 from sdm.ms_unit where idunit=$row[idunit]");
		
		if ($isExist){
			mModel::updateRecord($connsync,$record,$row['idunit'],false,'ms_unit','idunit','aset.');
			mModel::updateRecord($connsync,$record,$row['idunit'],false,'ms_unit','idunit','sdm.');
		}else{
			$connsync->Execute("SET IDENTITY_INSERT [sdm].[ms_unit] ON");
			mModel::insertRecord($connsync,$record,false,'ms_unit','sdm.');
			$connsync->Execute("SET IDENTITY_INSERT [sdm].[ms_unit] OFF");
			
			//$connsync->Execute("SET IDENTITY_INSERT [aset].[ms_unit] ON");
			mModel::insertRecord($connsync,$record,false,'ms_unit','aset.');
			//$connsync->Execute("SET IDENTITY_INSERT [aset].[ms_unit] OFF");
		}
	}
	
	
	$sql = "select * from gate.ms_unit order by infoleft";
	$rs = $conn->Execute($sql);
	
	while ($row = $rs->FetchRow())
		$a_in[] = $row['idunit'];
	
	$sql_in = implode("','", $a_in);
	
	$sql = "select * from gate.ms_unit order by infoleft";
	$rss = $conn->Execute($sql);
	while ($row = $rss->FetchRow()){
		$record = array();
		$record = $row;
		$isExist = $connsync->GetRow("select 1 from gate.ms_unit where idunit=$row[idunit]");
		
		if ($isExist){
			mModel::updateRecord($connsync,$record,$row['idunit'],false,'ms_unit','idunit','gate.');
		}else{
			mModel::insertRecord($connsync,$record,false,'ms_unit','gate.');
			
		}
	}
	
	$sql = "select * from gate.ms_unit where idunit not in ('$sql_in') order by infoleft";
	$rss = $connsync->Execute($sql);
	while ($row = $rss->FetchRow()){
		$record = array();
		$record = $conn->GetRow("select * from gate.ms_unit where idunit=$row[idunit]");
		
		mModel::delete($connsync,$row['idunit'],'ms_unit','idunit','gate.');
	}
	
	$sql = "select * from sdm.ms_unit where idunit not in ('$sql_in') order by infoleft";
	$rss = $connsync->Execute($sql);
	while ($row = $rss->FetchRow()){
		$record = array();
		$record = $conn->GetRow("select * from sdm.ms_unit where idunit=$row[idunit]");
		
		mModel::delete($connsync,$row['idunit'],'ms_unit','idunit','sdm.');
	}
	
	$sql = "select * from aset.ms_unit where idunit not in ('$sql_in') order by infoleft";
	$rss = $connsync->Execute($sql);
	while ($row = $rss->FetchRow()){
		$record = array();
		$record = $conn->GetRow("select * from aset.ms_unit where idunit=$row[idunit]");
		
		mModel::delete($connsync,$row['idunit'],'ms_unit','idunit','aset.');
	}*/
	
?>
