<?php
	// model tagihan mhs
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mTagihanMhs extends mModel {
		const schema = 'h2h';
		
		function isShowTagihan($conn,$nim,$periode=null) {
			if(empty($periode))
				$periode = Akademik::getPeriode();
			
			$sql = "select b.namajenistagihan, a.nim, a.nominaltagihan, a.periode from h2h.ke_tagihan a join h2h.lv_jenistagihan b on a.jenistagihan=b.jenistagihan where a.nim=".Query::escape($nim)." and a.periode=".Query::escape($periode)."" ;

			// mengambil status dan prasyarat spp
			//$sql = "select 1 from akademik.ak_perwalian
			//		where nim = ".Query::escape($nim)."
			//		and periode = ".Query::escape($periode)."
			//		and prasyaratspp = -1 and statusmhs = 'A'";
			$cek = $conn->GetOne($sql);
			
			if(empty($cek))
				return false;
			else
				return true;
		}
		
		function getStatusPeriode($conn,$nim,$periode=null) {
			if(empty($periode))
				$periode = Akademik::getPeriode();
			
			// data kembalian
			$data = array('tagihan' => 0, 'tunggakan' => 0, 'potongan' => 0, 'deposit' => 0, 'pembayaran' => 0, 'sisa' => 0);
			
			// pembayaran
			$sql = "select max(tglbayar) from ".static::table('ke_pembayaran')."
					where nim = ".Query::escape($nim);
			$data['tglbayarmax'] = $conn->GetOne($sql);
			
			// tagihan
			$sql = "select periode, nominaltagihan, potongan, nominalbayar, flaglunas
					from ".static::table('ke_tagihan')."
					where nim = ".Query::escape($nim)." and
					(periode = ".Query::escape($periode)." and flaglunas <> 'L')
					order by periode";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()) {
				if($row['periode'] == $periode) {
					$data['tagihan'] = (float)$row['nominaltagihan'];
					$data['potongan'] = (float)$row['potongan'];
					$data['pembayaran'] = (float)$row['nominalbayar'];
				}
				else
					$data['tunggakan'] += (float)$row['nominaltagihan']-(float)$row['potongan']-(float)$row['nominalbayar'];
			}
			
			// deposit, belum dipakai
			$sql = "select sum(nominaldeposit-nominalpakai) from ".static::table('ke_deposit')."
					where nim = ".Query::escape($nim)." and status = '-1' and
					(tglexpired is null or tglexpired >= '".date('Y-m-d')."')";
			$data['deposit'] = $conn->GetOne($sql);
			
			// bersifat negatif
			/* if(!empty($data['potongan'])) $data['potongan'] = -1*$data['potongan'];
			if(!empty($data['pembayaran'])) $data['pembayaran'] = -1*$data['pembayaran'];
			if(!empty($data['deposit'])) $data['deposit'] = -1*$data['deposit'];
			
			$data['sisa'] = $data['tagihan']+$data['tunggakan']+$data['potongan']+$data['pembayaran']+$data['deposit']; */
			$data['sisa'] = $data['nominaltagihan	']+$data['tunggakan']-$data['potongan']-$data['pembayaran']-$data['deposit'];
			
			return $data;
		}

		function getUts($conn, $nim,$periode) {
			$periode = Akademik::getPeriode();
			$sql = "select isuts, isuas from akademik.ak_perwalian where nim=".Query::escape($nim)." and statusmhs='A' and periode='$periode'";
			$data = $conn->GetRow($sql);
			return $data;
		}

		
	}
?>