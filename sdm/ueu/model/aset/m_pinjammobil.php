<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPinjamMobil extends mModel {
		const schema = 'aset';
		const table = 'as_pinjam';
		const order = 'idpinjam desc';
		const key = 'idpinjam';
		const label = 'peminjaman mobil';
		
        // mendapatkan kueri list
		function listQuery() {
			$sql = "select idpinjam,tglpengajuan,tglpinjam,tgltenggat,status,isverify,
			    u.kodeunit+' - '+u.namaunit as unitpeminjam,g.namalengkap 
                from ".self::table()." p 
                left join aset.ms_unit u on u.idunit = p.idunitpeminjam 
                left join sdm.v_biodatapegawai g on g.idpegawai = p.idpeminjam
				where idbarang1 like '302010%' ";
			
			return $sql;
		}

		function dataQuery($key){
		    $sql = "select p.*,g.namalengkap as peminjam 
		        from ".static::table()." p 
		        left join sdm.v_biodatapegawai g on g.idpegawai = p.idpeminjam 
		        where  idbarang1 like '302010%'
				and ".static::getCondition($key);
	        return $sql;
		}
				
		// mendapatkan potongan kueri filter list
		
		function getListFilter($col,$key,$tahun='',$bulan='') {
			switch($col) {
				case 'periode': 
					return "datepart(year,tglpengajuan) = '$tahun' and datepart(month,tglpengajuan) = '$bulan' "; 
				break;
				case 'unit':
					global $conn, $conf;
					require_once('m_unit.php');

					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
				break;
			}
		}

		function getMData($conn, $key){
		    return $conn->GetRow("select isok1,isverify,idunitasal,tglpinjam,tglkembali from ".self::table()." where idpinjam = '$key'");
		}
		
		function setPinjam($conn, $key, $old, $new){
		    $ok = true;
            if(isset($new) and $old != $new){
                if(empty($new) or $new == 'null') $ok = self::setSeriKembali($conn, $key);
                else $ok = self::setSeriPinjam($conn, $key);
            }
            return $ok;
		}
		
		function setKembali($conn, $key, $old, $new){
		    $ok = true;
            if(isset($new) and $old != $new){
                if(empty($new) or $new == 'null') $ok = self::setSeriPinjam($conn, $key);
                else $ok = self::setSeriKembali($conn, $key);
            }
            return $ok;
		}
		
		function setSeriPinjam($conn, $key){
	        $sql = "update aset.as_seri set idstatus = 'P' 
	            where idseri in (select idseri from aset.as_pinjamdetail where idpinjam = '$key')";
            return $conn->Execute($sql);
		}

		function setSeriKembali($conn, $key){
		    $sql = "update aset.as_seri set idstatus = 'A' 
		        where idseri in (select idseri from aset.as_pinjamdetail where idpinjam = '$key')";
	        return $conn->Execute($sql);
		}
		
	}
?>
