<?php
	// model pendaftar (terpakai)
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once ($conf['helpers_dir'].'query.class.php');
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
				case 'periodebayar': return "tglbayar BETWEEN '$key-01-01' AND '$key-12-31'";
			}
		}
		
		function cekPeriode($conn,$token,$periode,$gelombang,$jalur){
			$periodetoken = $conn->getRow("select f.periodedaftar,f.jalurpenerimaan,f.idgelombang from h2h.ke_pembayaranfrm p 
											join h2h.ke_tariffrm f on f.idtariffrm = p.idtariffrm 
											where p.notoken = '$token'");
			if ($periodetoken['periodedaftar']==$periode and $periodetoken['idgelombang']==$gelombang and $periodetoken['jalurpenerimaan']==$jalur)
				$rs = false;
			else
				$rs = true;
				
				return $rs;		
		}
		
	    function getToken($nopendaftaran){
		global $conn;
		$sql="SELECT nopendaftar, tokenpendaftaran FROM pendaftaran.pd_pendaftar WHERE nopendaftar='$nopendaftaran'";
                $ok=Query::arrQuery($conn,$sql);
		return $ok[$nopendaftaran];
	    }
            //cek token
	    function cekToken($conn, $token,$periode,$jalurpenerimaan,$gelombang){
			$sql="SELECT 1 FROM h2h.ke_pembayaranfrm p 
					join h2h.ke_tariffrm t using(idtariffrm)
					WHERE p.notoken='$token' and t.periodedaftar='$periode' and t.jalurpenerimaan='$jalurpenerimaan' and t.idgelombang='$gelombang'";
					$rs =  $conn->GetOne($sql);
					
					//dibalik biar pengecekan sama dengan yang lain :D 
					if ($rs)
						return false;
					else 
						return true;
		}
		function isPassTrue($token){
			global $conn;
			$sql="SELECT count(*) as jml from h2h.ke_pembayaranfrm where notoken='$token'";
			$ok = $conn->GetRow($sql);
				
			if($ok['jml']>0) {
				return  true;
			}else	return false;
	    }
	    
		function cekBatalToken($token,$periode,$jalurpenerimaan,$gelombang) {
			global $conn;
			$sql="SELECT flagbatal FROM h2h.ke_pembayaranfrm p 
				join h2h.ke_tariffrm t using(idtariffrm)
				WHERE p.notoken='$token' and t.periodedaftar='$periode' and t.jalurpenerimaan='$jalurpenerimaan' and t.idgelombang='$gelombang'";
					$row = $conn->GetOne($sql);
				
			if($row==1) {
				
				return  true;
			}else	return false;
		}
	    function isUsed($conn, $token){
			$sql ="SELECT tokenpendaftaran FROM pendaftaran.pd_pendaftar WHERE tokenpendaftaran='$token'";
			$row = $conn->getOne($sql);
					
			return $row;
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
		$sql="select t.*, f.notoken from h2h.ke_pembayaranfrm f join h2h.ke_tariffrm t using (idtariffrm) where t.notoken = '$token'";
		$data = $conn->getRow($sql);
			
			$result['jalurpenerimaan']=$data['jalurpenerimaan'];
			$result['periodedaftar']=$data['periodedaftar'];
			$result['idgelombang']=$data['idgelombang'];
			$result['sistemkuliah']=$data['sistemkuliah'];
			
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
