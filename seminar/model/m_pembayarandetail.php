<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPembayarandetail extends mModel {
		const schema = 'seminar';
		const table = 'sm_pembayarandetail';
		const order = 'idpembayaran,idtagihan';
		const key = 'idpembayaran,idtagihan';
		const label = 'idpembayaran,idtagihan';
	
		function getPembayaranDetail($conn,$key,$label='',$post='') {
			$sql = "select t.idtagihan,t.periode,t.bulantahun,t.jenistagihan,p.nominalbayar from ".static::table()." p
					join ".static::table('ke_tagihan')." t on(p.idtagihan = t.idtagihan)
					where idpembayaran=$key";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
	
		function getDatapembayaran($conn,$id) {
			$sql = "select b.*,t.*,j.* from ".static::table()." b 
					left join h2h.ke_tagihan t on t.idtagihan = b.idtagihan 
					left join h2h.lv_jenistagihan j on j.jenistagihan = t.jenistagihan
					where b.idpembayaran = '$id'";
		
			return $conn->getArray($sql);		
		}
		
		function getDataPembayaranDeposit($conn,$key) {
			// detail pembayaran
			$sql = "select idtagihan, iddeposit, nominalbayar from ".static::table()." where idpembayaran = ".Query::escape($key);
			$rs = $conn->Execute($sql);
			
			$tagihan = array();
			$deposit = array();
			$nominaltag = array();
			$nominaldep = array();
			while($row = $rs->FetchRow()) {
				$tagihan[$row['idtagihan']] = $row['idtagihan'];
				$nominaltag[$row['idtagihan']] += (float)$row['nominalbayar'];
				
				if(!empty($row['iddeposit'])) {
					$deposit[$row['iddeposit']] = $row['iddeposit'];
					$nominaldep[$row['iddeposit']] += (float)$row['nominalbayar'];
				}
			}
			
			// tagihan
			$sql = "select t.*, j.namajenistagihan from ".static::table('ke_tagihan')." t
					join ".static::table('lv_jenistagihan')." j on t.jenistagihan = j.jenistagihan
					where t.idtagihan in ('".implode("','",$tagihan)."')";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->fetchRow()) {
				$row['nominalbayar'] = $nominaltag[$row['idtagihan']];
				
				$data[] = $row;
			}
			
			// deposit
			if(!empty($deposit)) {
				$sql = "select * from ".static::table('ke_deposit')." where iddeposit in ('".implode("','",$deposit)."')";
				$rs = $conn->Execute($sql);
				
				while($row = $rs->fetchRow()) {
					$rowt = array();
					$rowt['iddeposit'] = $row['iddeposit'];
					$rowt['periode'] = $row['periode'];
					$rowt['nominalbayar'] = -1*$nominaldep[$row['iddeposit']];
					
					if($row['jenisdeposit'] == 'V') {
						$rowt['idtagihan'] = $row['novoucher'];
						$rowt['jenistagihan'] = 'VOU';
						$rowt['namajenistagihan'] = 'VOUCHER';
					}
					else {
						$rowt['idtagihan'] = 'DEP'.str_pad($row['iddeposit'],21,'0',STR_PAD_LEFT);
						$rowt['jenistagihan'] = 'DEP';
						$rowt['namajenistagihan'] = 'DEPOSIT';
					}
					
					$data[] = $rowt;
				}
			}
			
			return $data;
		}
	}
?>
