<?php
	// model surat permintaan barang
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPo extends mModel {
		const schema = 'prcm';
		const table = 'pr_po';
		const order = 'idpo desc';
		const key = 'idpo';
		const label = 'purchase order';
				
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select idpo,tglpo,s.namasupplier as supplier,nopo,status
				from ".self::schema.".pr_po p
				left join aset.ms_supplier s on s.idsupplier = p.idsupplier ";
			
			return $sql;
		}

		function dataQuery($key){
		    $sql = "select p.idpo, p.tglpo, p.status, p.insertuser, p.inserttime, p.nopo, p.idsupplier, s.namasupplier, 
				sp.idspb , sp.nospb, sp.tglspb, sp.idunit, u.namaunit, sp.insertuser as userspb, sp.catatan as catatanspb
                from ".self::schema.".pr_po p
				left join prcm.pr_spb sp on sp.idspb = p.idspb
				left join aset.ms_supplier s on s.idsupplier = p.idsupplier
				left join aset.ms_unit u on u.idunit = sp.idunit
				where ".static::getCondition($key);
				
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key,$tahun='',$bulan='') {
			switch($col) {
				case 'periode': 
					return "datepart(year,tglspb) = '$tahun' and datepart(month,tglspb) = '$bulan' "; 
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
		    return $conn->GetRow("select idsupplier,tglpo,status from ".self::schema.".pr_po where idpo = '$key'");
		}
	}
?>
