<?php
	// model combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once($conf['model_dir'].'m_model.php');
	
	class mPendaftar extends mModel{
		
		const schema 	= 'pendaftaran';
		const table 	= 'pd_pendaftar';
		const order 	= 'nopendaftar desc';
		const key 	= 'nopendaftar';
		const label 	= 'pendaftar';
		const uptype	='pendaftar';

		// mendapatkan kueri list
		function listQuery() {
			$sql = "select p.* from pendaftaran.pd_pendaftar p ";
			
			return $sql;
		}
				
		// mendapatkan kueri detail
		/*
		 * data Query untuk detail pendaftar
		 * variable
		 * 		m = pendaftar, p=jadwalujian,kt=kota,ktlhr=kotalahir,p_lhr=propinsilahir, u1 u2 u3=pilihan1 2 3, s = smu
		 * */
		/*function dataQuery($key) {
			$sql = "select m.*,p.kodekota as kotaujian, kt.namakota,ktlhr.namakota as namakotalahir, p_lhr.namapropinsi, u1.namaunit as pil1, u2.namaunit as pil2, u3.namaunit as pil3, s.namasmu,
					ks.namakota as namakotasmu, p_smu.namapropinsi as namapropinsismu
					from ".static::table()." m
					left join ".static::table('pd_jadwaldetail')." p using (idjadwaldetail)
					left join akademik.ms_kota kt on kt.kodekota = m.kodekota
					left join akademik.ms_kota ktlhr on ktlhr.kodekota = m.kodekotalahir
					left join akademik.ms_propinsi p_lhr on p_lhr.kodepropinsi = ktlhr.kodepropinsi
					left join gate.ms_unit u1 on u1.kodeunit = m.pilihan1
					left join gate.ms_unit u2 on u2.kodeunit = m.pilihan2
					left join gate.ms_unit u3 on u3.kodeunit = m.pilihan3
					left join pendaftaran.lv_smu s on s.idsmu = m.idsmu
					left join akademik.ms_kota ks on ks.kodekota = m.kodekotasmu
					left join akademik.ms_propinsi p_smu on p_smu.kodepropinsi = ks.kodepropinsi
					
					where ".static::getCondition($key);
			return $sql;
		}*/
		function dataQuery($key) {
			$sql = "select m.*, kt.namakota,ktlhr.namakota as namakotalahir, p_lhr.namapropinsi, u1.namaunit as pil1, u2.namaunit as pil2, u3.namaunit as pil3, s.namasmu,
					ks.namakota as namakotasmu, p_smu.namapropinsi as namapropinsismu
					from ".static::table()." m
					left join akademik.ms_kota kt on kt.kodekota = m.kodekota
					left join akademik.ms_kota ktlhr on ktlhr.kodekota = m.kodekotalahir
					left join akademik.ms_propinsi p_lhr on p_lhr.kodepropinsi = ktlhr.kodepropinsi
					left join gate.ms_unit u1 on u1.kodeunit = m.pilihan1
					left join gate.ms_unit u2 on u2.kodeunit = m.pilihan2
					left join gate.ms_unit u3 on u3.kodeunit = m.pilihan3
					left join pendaftaran.lv_smu s on s.idsmu = m.idsmu
					left join akademik.ms_kota ks on ks.kodekota = m.kodekotasmu
					left join akademik.ms_propinsi p_smu on p_smu.kodepropinsi = ks.kodepropinsi
					
					where ".static::getCondition($key);
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			global $conn;
			switch($col) {
				case 'periode': return "periodedaftar = '$key'";
				case 'jalur': return "p.jalurpenerimaan = '$key'";
				case 'gelombang':return "idgelombang = '$key'"; 
				case 'lulus':
					if($key=='l') return "pilihanditerima is not NULL";
					elseif ($key=='t') return "pilihanditerima is NULL";
				case 'jenis':
					if($key=='mhs') return "nimpendaftar is not NULL";
					elseif ($key=='pdf') return "nimpendaftar is NULL";
				case 'fakultas':
					return "pilihanditerima in (select kodeunit from gate.ms_unit where kodeunitparent ='$key') ";
					
			}
		}
	}	
?>
