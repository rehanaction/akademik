<?php
	// model unsur nilai
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mUnsurNilai extends mModel {
		const schema = 'akademik';
		const table = 'ak_unsurnilai';
		const order = 'nounsurnilai';
		const key = 'thnkurikulum,programpend,nounsurnilai';
		const label = 'unsur nilai';
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'thnkurikulum': return "thnkurikulum = '$key'";
				case 'progpend': return "programpend = '$key'";
				case 'tipekuliah': return "tipekuliah = '$key'";
			}
		}
		
		// salin data
		function copy($conn,$kurasal,$kurtujuan) {
			$sql = "delete from ".static::table()." where thnkurikulum = '$kurtujuan';
					insert into ".static::table()." (thnkurikulum,programpend,nounsurnilai,namaunsurnilai,prosentasenilai, tipekuliah)
					select '$kurtujuan'::numeric,programpend,nounsurnilai,namaunsurnilai,prosentasenilai, tipekuliah from ".static::table()."
					where thnkurikulum = '$kurasal'";
			$ok = $conn->Execute($sql);
			
			if($ok)
				$msg = 'Salin '.self::label.' berhasil';
			else
				$msg = 'Salin '.self::label.' gagal';
			
			return array($err,$msg);
		}
		
		// tipe kuliah
		function tipeKuliah() {
			require_once(Route::getModelPath('matakuliah'));
			
			return mMataKuliah::tipeKuliah();
		}
	}
	
	class mUnsurNilaiKelas extends mModel {
		const schema = 'akademik';
		const table = 'ak_unsurpenilaian';
		const order = 'idunsurnilai';
		const key = 'idunsurnilai';
		const label = 'unsur nilai kelas';
		
		// mendapatkan data
		function getDataKelas($conn,$key) {
			require_once(Route::getModelPath('kelas'));
			
			$sql = "select idunsurnilai, namaunsurnilai, prosentasenilai
					from ".static::table()." where ".mKelas::getCondition($key)."
					order by ".static::order;
			
			return $conn->GetArray($sql);
		}
		
		// insert dari unsur nilai
		function insertFromUnsurNilai($conn,$key) {
			require_once(Route::getModelPath('kelas'));
			require_once(Route::getModelPath('matakuliah'));
			require_once(Route::getModelPath('unit'));
			
			$a_kelas = mKelas::getKeyRecord($key);
			$keymk = mMataKuliah::getKeyRow($a_kelas);
			
			$t_progpend = mUnit::getProgramPendidikan($conn,$a_kelas['kodeunit']);
			$t_tipekuliah = mMatakuliah::getTipeKuliah($conn,$keymk);
			
			$sql = "insert into ".static::table()." (thnkurikulum,kodemk,kodeunit,periode,kelasmk,namaunsurnilai,prosentasenilai)
					select '".$a_kelas['thnkurikulum']."','".$a_kelas['kodemk']."','".$a_kelas['kodeunit']."','".$a_kelas['periode']."',
						'".$a_kelas['kelasmk']."',namasingkat,prosentasenilai from ".static::table('ak_unsurnilai')."
					where thnkurikulum = '".$a_kelas['thnkurikulum']."' and programpend = '$t_progpend' and tipekuliah = '$t_tipekuliah'
					order by nounsurnilai";
			$ok = $conn->Execute($sql);
			
			if($ok)
				return self::getDataKelas($conn,$key);
			else
				return false;
		}
	}
	
	class mUnsurNilaiMhs extends mModel {
		const schema = 'akademik';
		const table = 'ak_unsurnilaikelas';
		const order = 'nim';
		const key = 'thnkurikulum,kodemk,kodeunit,periode,kelasmk,nim,idunsurnilai';
		const label = 'unsur nilai mahasiswa';
		
		// mendapatkan data satu kelas
		function getDataKelas($conn,$key) {
			require_once(Route::getModelPath('kelas'));
			
			$sql = "select nim, idunsurnilai, nilaiunsur from ".static::table()." where ".mKelas::getCondition($key);
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['nim']][$row['idunsurnilai']] = $row['nilaiunsur'];
			
			return $data;
		}
		
		// mendapatkan data satu mahasiswa
		function getDataKelasMhs($conn,$key) {
			require_once(Route::getModelPath('krs'));
			
			$sql = "select k.idunsurnilai, k.nilaiunsur, p.namaunsurnilai from ".static::table()." k
					join ".static::table('ak_unsurpenilaian')." p on k.idunsurnilai = p.idunsurnilai
					where ".mKRS::getCondition($key,'','k');
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[] = $row;
			
			return $data;
		}
	}
?>