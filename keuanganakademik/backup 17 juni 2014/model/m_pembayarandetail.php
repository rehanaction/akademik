<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPembayarandetail extends mModel {
		const schema = 'h2h';
		const table = 'ke_pembayarandetail';
		const order = 'idpembayaran,idtagihan';
		const key = 'idpembayaran,idtagihan';
		const label = 'idpembayaran,idtagihan';
	
	
	function getPembayaranDetail($conn,$key,$label='',$post='') {
			$sql = "select p.idtagihan,t.periode,t.bulantahun,jenistagihan, nominalbayar, denda, cicilanke from ".static::table()." p
					join ".static::table('ke_tagihan')." t on(p.idtagihan = t.idtagihan)
					where idpembayaran=$key";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
	
	function getDatapembayaran($conn,$id){
		
			$sql = "select b.*,t.*,j.* from ".static::table()." b 
					left join h2h.ke_tagihan t on t.idtagihan = b.idtagihan 
					left join h2h.lv_jenistagihan j on j.jenistagihan = t.jenistagihan
					where b.idpembayaran = '$id'";
		
		return $conn->getArray($sql);		
		}
		
	}
?>