<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	require_once(Route::getModelPath('jawabankategorikuis'));
	
	class mPertanyaanKuisSeminar extends mModel {
		const schema = 'seminar';
		const table = 'ms_pertanyaankuisseminar';
		const order = 'idseminar';
		const key = 'idpertanyaankuisseminar';
		const label = 'Pertanyaan Kuisioner Seminar';
		const sequence = 'ms_pertanyaankuisseminar_idpertanyaankuisseminar_seq';

		// mendapatkan kueri list
		function listQuery() {
			$sql = "select p.nomor,p.idpertanyaankuisseminar,p.pertanyaan,p.idseminar,p.idkategori,p.isaktif, s.namaseminar, k.namakategori from ".static::table()." p 
					left join ".static::table('ms_seminar')." s using (idseminar)
					left join ".static::table('ms_kategorikuis')." k using (idkategori)
					";
			
			return $sql;
		}
		function getPertanyaan($conn,$a_idseminar,$aktif=false){
			$a_seminar = implode("','",$a_idseminar);
			$sql = "select * from ".static::table()." where idseminar in ('$a_seminar') ";
			
			if ($aktif)
			$sql.=" and coalesce(isaktif,'0') <> '0'";
			
			$sql.=" order by idseminar,nomor ";
			$rs =  $conn->getArray($sql);
			
			foreach ($rs as $row)
				$a_idkategori[$row['idkategori']] = $row['idkategori'];

			$a_kategori = implode("','",$a_idkategori);
			if (!empty ($a_kategori)){
				$sqlk = "select * from ".static::table('ms_jawabankategorikuis')." where idkategori in ('$a_kategori') order by kodejawaban";
				$rsk =  $conn->getArray($sqlk);
				foreach ($rsk as $row)
					$jawaban[$row['idkategori']][$row['kodejawaban']] =  $row;
				
				
				foreach ($rs as $row){
					$row['jawaban'] = $jawaban[$row['idkategori']];

					$data[$row['idseminar']][$row['idpertanyaankuisseminar']] = $row; 
				} 
			}
			return $data;
		}

		function copy($conn,$idfrom,$idto) {
			$sql = "select 1 from ".static::table()." where idseminar = ".Query::escape($idto);
			$cek = $conn->GetOne($sql);
			
			if(empty($cek)) {
				global $i_page;
				
				$sql = "insert into ".static::table()." (idseminar,pertanyaan,idkategori,isaktif,nomor,t_updateuser,t_updatetime,t_updateip,t_updateact)
						select ".Query::escape($idto).",pertanyaan,idkategori,isaktif,nomor,".Query::logInsert().",'".substr('c-'.$i_page,0,30)."'
						from ".static::table()."
						where idseminar = ".Query::escape($idfrom);
				$conn->Execute($sql);
				
				$err = $conn->ErrorNo();
			}
			else {
				$err = true;
				$msg = 'pertanyaan kuisioner seminar tujuan sudah ada';
			}
			
			return array($err,'Salin pertanyaan kuisioner '.(empty($err) ? 'berhasil' : 'gagal').(empty($msg) ? '' : ', '.$msg));
		}
	}
?>
