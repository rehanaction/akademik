<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mBahanajar extends mModel {
		const schema = 'akademik';
		const table = 'ak_bahanajar';
		const order = 'periode,namamk,kelasmk';
		const key = 'thnkurikulum,kodemk,periode';
        const label = 'bahanajar';


        	// mendapatkan kueri list
		function v_bahanajar($conn,$periode) {
            $sql = "select * from ".self::table('v_kelasonline')." where periode='$periode'";
			return $conn->GetArray($sql);
        }

        function v_detailbahanajar($conn,$periode,$kodemk) {
            $sql = "select * from ".self::table('v_bahanajaronline')." where periode='$periode' and kodemk='$kodemk'";
			return $conn->GetArray($sql);
		}
		function insertBahanajar($conn,$data){
			$kolom = implode(',',array_keys($data));
			$valuesArrays = array();
			$i = 0;
			foreach($data as $key=>$values)
			{
				if(is_int($values))
				{
					$valuesArrays[$i] = $values;
				}else{
					$valuesArrays[$i]= "'".$values."'";
				}
				$i++;
			}
			$values = implode(',',$valuesArrays);
			$sql = "insert into akademik.ak_bahanajar ($kolom) values($values)";
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
		}

		function DeleteBahanajar($conn,$pk){
			$sql = "delete from akademik.ak_bahanajar where id='$pk'";
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
		}
		function v_detailbahanajarbyid($conn,$pk) {
            $sql = "select * from ".self::table('v_bahanajaronline')." where id='$pk'";
			return $conn->GetRow($sql);
		}

		function updateBahanajar($conn,$data,$pk){
			$kolom = implode(',',array_keys($data));
			$valuesArrays = array();
			$i = 0;
			foreach($data as $key=>$values)
			{
				if(is_int($values))
				{
					$valuesArrays[$i] =$key.'='.$values;
				}else{
					$valuesArrays[$i]= $key."='".$values."'";
				}
				$i++;
			}
			$values = implode(',',$valuesArrays);
			$sql = "update akademik.ak_bahanajar set $values where id='$pk'";
			
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
		}
		
       


    }
