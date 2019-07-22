<?php
	// model pendapatan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once(Route::getModelPath('model'));

	class mPrestasiBeasiswaMaba extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_prestasibeasiswamaba';
		const key = 'idprestasibeasiswa';
		const sequence = 'mw_prestasibeasiswamaba_idprestasibeasiswa_seq';
		const label = 'penghargaan';

		function getByPd($conn,$key){
			$sql = "select pb.*,namajenisprestasi,namatingkatprestasi,namakategoriprestasi,namaprestasi,tempat,tahun
					from kemahasiswaan.mw_prestasibeasiswamaba pb
					left join ".static::table('lv_jenisprestasi')." jp on pb.kodejenisprestasi = jp.kodejenisprestasi
					left join ".static::table('lv_tingkatprestasi')." tp on pb.kodetingkatprestasi = tp.kodetingkatprestasi
					left join ".static::table('lv_kategoriprestasi')." kp on pb.kodekategoriprestasi = kp.kodekategoriprestasi
					where pb.idpengajuanbeasiswa = $key ";
			return static::getDetail($conn,$sql,$label,$post);
		}


	}
?>
