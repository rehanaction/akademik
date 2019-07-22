<?php
	// fungsi pembantu modul akademik
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class Aset {
		// mengambil setting global
		function setGlobal() {
			$_SESSION[SITE_ID]['ASET']['PERIODE'] = self::getPeriode();
			$_SESSION[SITE_ID]['ASET']['BULAN'] = self::getMonth();
			$_SESSION[SITE_ID]['ASET']['TAHUN'] = self::getYear();
			//$_SESSION[SITE_ID]['ASET'] = mSetting::getDataSession($conn);
		}
		
		function setFormatNoSeri($noseri='0'){
		    return str_pad($noseri, 6, '0', STR_PAD_LEFT);
		}
		
		function formatNoSeri($noseri='0'){
		    return str_pad($noseri, 6, '0', STR_PAD_LEFT);
		}
		
		function setFormatLevelBarang($idbarang='0'){
		    return str_pad($idbarang, 10, '0', STR_PAD_RIGHT);
		}
		
		function formatLevelBarang($idbarang='0'){
		    return str_pad($idbarang, 10, '0', STR_PAD_RIGHT);
		}

	    function getLengthBrg($plevel){
		    $length = 16;
		    if($plevel == 1)
			    $length = 1;
		    else if($plevel == 2)
			    $length = 3;
		    else if($plevel == 3)
			    $length = 5;
		    else if($plevel == 4)
			    $length = 7;
		    else if($plevel == 5)
			    $length = 10;
		
		    return $length;
	    }
	    
	    function getPeriode(){
	        return date('Ym');
	    }
	    
	    function getMonth(){
	        return date('m');
	    }

	    function getYear(){
	        return date('Y');
	    }
	    
	    function getNamaRoleProc(){
	        return array('kaproc' => 'Kepala Bagian Pengadaan','pp' => 'Petugas Pengadaan');
	    }

	    function getRoleProc(){
	        return array('kaproc','pp');
	    }

	    function getNamaRoleRM(){
	        return array('karm' => 'Kepala Bagian Rumah Tangga','prm' => 'Petugas Rumah Tangga');
	    }

	    function getRoleRM(){
	        return array('karm','prm');
	    }

	    function getNamaRoleKeu(){
	        return array('kakeu' => 'Kepala Bagian Keuangan','pk' => 'Petugas Keuangan');
	    }
	    
	    function getRoleKeu(){
	        return array('kakeu','pk');
	    }
	    
	    function getRoleUnit(){
	        return array('pua' => 'Petugas Unit Aset');
	    }
		
		function getRoleSuperAset(){
			return array('admaset', 'karm');
		}
	    
	    function setInsert(&$record){
		    $record['insertuserid'] = Modul::getIDPegawai();
		    $record['insertuser'] = Modul::getUserDesc();
			$record['inserttime'] = date('Y-m-d H:i:s'); 
	    }

	    function setVerify(&$record,$old,$new){
            if(isset($new) and $old != $new){
		        if($new == '1'){
		            $record['verifyuser'] = Modul::getUserDesc();
			        $record['verifytime'] = date('Y-m-d H:i:s');
			        $record['status'] = 'V';
		        }else{
        	        $record['verifyuser'] = null;
		            $record['verifytime'] = null;
			        $record['status'] = 'A';

	                $record['isok1'] = null;
	                $record['memo1'] = null;
		            $record['isok1user'] = null;
		            $record['isok1time'] = null;
		        }
	        }
	    }

	    function setOk1(&$record,$old,$new){
            if(isset($new) and $old != $new){
		        if(in_array($new, array('0','1'))){
		            $record['isok1user'] = Modul::getUserDesc();
			        $record['isok1time'] = date('Y-m-d H:i:s');

			        if($new == '1'){
			            $record['status'] = 'S';
			        }else if($new == '0'){
			            $record['status'] = 'T';
		            }

		        }
	        }
	    }

		function getPrevPeriode($nprev=12){
		    $data = array();
		    
		    $tahun = (int)date('Y');
		    $bulan = (int)date('m');
		        
            $fmonth = ($bulan-$nprev)+1;
            
		    for($i=$fmonth; $i<=$bulan; $i++){
		        if($i <= 0)
		            $data[] = ($tahun-1).str_pad($i+12, 2, '0', STR_PAD_LEFT);
	            else if($i > 12)
		            $data[] = ($tahun+1).str_pad($i-12, 2, '0', STR_PAD_LEFT);
	            else
		            $data[] = $tahun.str_pad($i, 2, '0', STR_PAD_LEFT);
	        }
		        
	        return $data;
		}

		function getPrevPeriodeName($nprev=12){
		    $a_monthname = Date::arrayMonth(false);
		    $data = array();

		    $tahun = (int)date('Y');
		    $bulan = (int)date('m');
            
            $fmonth = ($bulan-$nprev)+1;
            
		    for($i=$fmonth; $i<=$bulan; $i++){
		        if($i <= 0)
		            $data[] = $a_monthname[$i+12].' '.($tahun-1);
	            else if($i > 12)
		            $data[] = $a_monthname[$i-12].' '.($tahun+1);
	            else
		            $data[] = $a_monthname[$i].' '.$tahun;
	        }
		        
	        return $data;
		}
		
		function getIsTutupBuku($conn,$periode) {
			// ambil model tutup buku
			require_once(Route::getModelPath('tutupbuku'));
			
			return mTutupBuku::isTutupBuku($conn,$periode);
		}

		function getIsOpname($conn) {
			// ambil model setting
			require_once(Route::getModelPath('setting'));
			
			return mSetting::isOpname($conn);
		}
		
		function isLock($conn,$periode){
		    $msg = '';
		    if(self::getIsTutupBuku($conn, $periode) == 1)
		        $msg = 'Tidak dapat menambah/merubah data pada periode ini, karena sudah dilakukan tutup buku !';

		    if(self::getIsOpname($conn) == 1)
		        $msg = 'Tidak dapat merubah data, karena sedang dilakukan proses opname !';
	        
	        return $msg;
		}

		function setTglToPeriode($tgl){
		    return substr($tgl,0,4).substr($tgl,5,2);
		}
		
		function unsetRecord($record,$a_except){
		    foreach($record as $key => $val){
		        if(!in_array($key,$a_except))
		            unset($record[$key]);
		    }
		}

	}
?>
