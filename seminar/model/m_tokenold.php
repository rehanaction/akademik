<?php
	// model pendaftar (terpakai)
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));

	class mToken extends mModel{
		const schema 	= 'h2h';
	    const table 	= 'ke_pembayaranfrm';
	    const order 	= 'idpembayaranfrm';
	    const key 		= 'idpembayaranfrm';
	    const label 	= 'Token Pendaftaran';
	    
			// mendapatkan potongan kueri filter list
			function getListFilter($col,$key) {
				switch($col) {
					case 'periodebayar': return "periodebayar = '$key'";
				}
			} 
			function getToken($nopendaftaran){
				global $conn;
				$sql="SELECT nopendaftar, tokenpendaftaran FROM pendaftaran.pd_pendaftar WHERE nopendaftar='$nopendaftaran'";
						$ok=Query::arrQuery($conn,$sql);
				return $ok[$nopendaftaran];
			}
				//cek token
			function cekToken($token) {
				global $conn;
				$sql="SELECT notoken FROM h2h.ke_pembayaranfrm WHERE notoken='$token'";
						$row = $conn->GetRow($sql);
					
				if(!empty($row)) {
					return  true;
				}else	return false;
			}
	    
			function isPassTrue($token, $pass){
				global $conn;
				$sql="SELECT transactionid,pin FROM h2h.ke_pembayaranfrm WHERE notoken='$token'";
						$ok=Query::arrQuery($conn,$sql);
					
				if($ok[$token]==$pass) {
					return  true;
				}else	return false;
			}
	    
			function isUsed($token){
				global $conn;
				$sql="SELECT tokenpendaftaran, nama FROM pendaftaran.pd_pendaftar WHERE tokenpendaftaran='$token'";
						$row = $conn->GetRow($sql);
					
				if(empty($row)) {
					return  false;
				}else	return true;
			}
	    
			function getJalurPenerimaan($token){
                global $conn;
				//$sql = "SELECT tokenpendaftaran FROM".self::schema.".".self::table." WHERE ".self::key."=' ".$token." ' ";
				$sql="SELECT tokenpendaftaran, jalurpenerimaan FROM pendaftaran.ls_tokendaftar WHERE tokenpendaftaran='$token'";
						$ok=Query::arrQuery($conn,$sql);
				return $ok[$token];
			}
			
            function getPeriodeDaftar($token){
                global $conn;
				//$sql = "SELECT tokenpendaftaran FROM".self::schema.".".self::table." WHERE ".self::key."=' ".$token." ' ";
				$sql="SELECT tokenpendaftaran, periodedaftar FROM pendaftaran.ls_tokendaftar WHERE tokenpendaftaran='$token'";
						$ok=Query::arrQuery($conn,$sql);
				return $ok[$token];
            }
            
            function getIdGelombang($token){
                global $conn;
				//$sql = "SELECT tokenpendaftaran FROM".self::schema.".".self::table." WHERE ".self::key."=' ".$token." ' ";
				$sql="SELECT tokenpendaftaran, idgelombang FROM pendaftaran.ls_tokendaftar WHERE tokenpendaftaran='$token'";
						$ok=Query::arrQuery($conn,$sql);
				return $ok[$token];
            }
            function getSistemKuliah($token){
                global $conn;
				//$sql = "SELECT tokenpendaftaran FROM".self::schema.".".self::table." WHERE ".self::key."=' ".$token." ' ";
				$sql="SELECT tokenpendaftaran, sistemkuliah FROM pendaftaran.pd_gelombangdaftar
							INNER JOIN pendaftaran.ls_tokendaftar ON 
							ls_tokendaftar.jalurpenerimaan=pd_gelombangdaftar.jalurpenerimaan
							AND ls_tokendaftar.periodedaftar=pd_gelombangdaftar.periodedaftar
							AND ls_tokendaftar.idgelombang=pd_gelombangdaftar.idgelombang
							WHERE pendaftaran.ls_tokendaftar.tokenpendaftaran='$token'";
						$ok=Query::arrQuery($conn,$sql);
				return $ok[$token];
			}
			
			function getTokenDetail($token){//replacing getJalurPenerimaan,getPeriodeDaftar, getIdGelombnag, getSistemKuliah
				global $conn;
				
				$result=array();
				$sql="SELECT idtariffrm from h2h.ke_pembayaranfrm where notoken='$token' ";
				$data = $conn->Execute($sql);
				$data = $data->FetchRow();
				$tar = $data['idtariffrm'];
				
				$sql2="SELECT jalurpenerimaan,periodedaftar,idgelombang,sistemkuliah FROM h2h.ke_tariffrm where idtariffrm = '$tar'";
				$data2 = $conn->Execute($sql2);
				$data2 = $data2->FetchRow();
				
				$result['nama']=$data['nama'];
				//$result['pin'] = $data['pin'];
				$result['jalurpenerimaan']=$data2['jalurpenerimaan'];
				$result['periodedaftar']=$data2['periodedaftar'];
				$result['idgelombang']=$data2['idgelombang'];
				$result['sistemkuliah']=$data2['sistemkuliah'];
							
				return $result;
			}
			
			function createToken($jalur, $periode, $gelombang){
				$data=array();
				/*
				$data['token']=date(His).strtoupper(substr($jalur,0,3)).substr($periode,0,-2).$gelombang."OR".date(jmy);
				$pass=date(s);
				for($i=0;$i<5;$i++){
					$pass.=substr("aAbBcCdDeEfFGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ1234567890",rand(1,61),1);
				}$pass.=date(Hiymj);
				$data['pass']=$pass;
				*/
				$token='0';
				for($i=0;$i<6;$i++){
					$token.=substr("1234567890",rand(1,9),1);
				}
				$data['token']=$token;
				$pass='2';
				
				for($i=0;$i<6;$i++){
					$pass.=substr("aAbBcCdDeEfFGhHiIjJkKlLmMnNoOpPqQrRsStTuUvVwWxXyYzZ1234567890",rand(1,61),1);
				}
				$data['pass']=$pass;
				return $data;
			}
	    
			function getOwner($token){
				global $conn;
				$sql="SELECT nama FROM pendaftaran.pd_pendaftar WHERE tokenpendaftaran = '$token'";
				$ok = $conn->GetRow($sql);
				return $ok['nama'];
			}
        }
?>
