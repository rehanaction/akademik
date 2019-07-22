<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mAkJumpingClass extends mModel {
		const schema = 'akademik';
		const table = 'ak_jumpingclass';
		const order = 'nim,periode,kodemk,kodejumping';
		const key = 'thnkurikulum,kodemk,kodeunit,periode,kelasmk,nim,kodejumping';
		const label = 'Data Jumping Class';
		
		function deletejumping($conn,$key){
			$sql="select kodejumping from ".static::table()." where ".static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,nim');
			$data=$conn->GetArray($sql);
			foreach($data as $row)
				$kodejumping[]=$row['kodejumping'];
			$inkode=implode("','",$kodejumping);
			$sqldelete="delete from ".static::table()." where ".static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,nim')." and kodejumping in ('$inkode')";
			
			$del=$conn->Execute($sqldelete);
			//die('hentikan');
			return $del;
		}
	}
?>
