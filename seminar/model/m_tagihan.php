<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTagihan extends mModel {
		const schema = 'seminar';
		const table = 'sm_tagihan';
		const key = 'idtagihan';
		const label = 'idtagihan';

		function listQuery() {
			$sql = "select  t.flaglunas,p.nopeserta,s.namaseminar,pe.nama,
					(case when p.islunas ='-1' then '<div align=center><img src=images/check.png></div>' end) as lunas		

					from ".static::table()." t
					left join seminar.sm_pembayaran b on b.idtagihan = t.idtagihan
					left join seminar.ms_peserta p on t.nopeserta = p.nopeserta
					left join seminar.ms_pendaftar pe on pe.nopendaftar = p.nopendaftar
					left join seminar.ms_seminar s on s.idseminar = p.idseminar";
			
			return $sql;
		}


		function getArrayListFilterCol() {
			$data['nopeserta'] = 'p.nopeserta';
			$data['periode'] = 's.periode';
			$data['idseminar'] = 's.idseminar';
			
			return $data;
		}

		function getListFilter($col,$key) {
			switch($col) {
				case 'namapeserta': 
					return " pe.nama  = '$key'";
				case 'idseminar': 
					return " s.idseminar  = '$key'";
				case 'periode': 
					return " s.periode  = '$key'";
			}
		}		
		
		
		function dataQuery($key) {
			$sql =" select t.*, p.nama, p.pilihanditerima, p.hp, p.email, u.namaunit, u.kodeunit from h2h.ke_tagihan t join pendaftaran.pd_pendaftar p on p.nopendaftar = t.nopendaftar join gate.ms_unit u on u.kodeunit = p.pilihanditerima ";
			$sql .= " where ".static::getCondition($key);
			
			return $sql;
		}
		
		function getTagihan($conn, $nopendaftar){
			
			$sql = "select * from ".static::table()." where nopendaftar = '$nopendaftar'";
			$rs = $conn->Execute($sql);
			$data = array();
			while ($row =  $rs->fetchRow()){
					$data[] = $row;
			}
		return $data;		
			
			
		}
		
		
		function getListTagihanGenerate($conn,$periode=''){
			$sql = "select p.idseminar, sum(nominaltagihan) as nominaltagihan 
					from seminar.sm_tagihan t
					join seminar.ms_peserta p on t.nopeserta = p.nopeserta
					join seminar.ms_seminar s on p.idseminar = s.idseminar
					group by p.idseminar";
			$rs = $conn->Execute($sql);	
			$data = array();
			
			while($row = $rs->FetchRow())
				$data[$row['idseminar']] = $row['nominaltagihan'];
			
			return $data;
		}
		

		function voidTagihan($conn,$filter,$periode,$jenis=null,$nonaktif=false) {		
			$idseminar = $filter['idseminar'];
			$sql = " t.flaglunas = 'BB' and nopeserta in (select nopeserta from seminar.ms_peserta where idseminar = $idseminar)  ";
			if(!empty($periode))
				$sql .= " and t.periode = $periode ";

			$jml = $conn->GetOne("select count(distinct(t.nopeserta)) from seminar.sm_tagihan t join seminar.ms_peserta p using (nopeserta) where ".$sql);
			
			// baru delete
			$err = Query::qDelete($conn,static::table().' t',$sql,false);
			
			$err = ($err ? true : false);
			$msg = 'Penghapusan tagihan '.($err ? 'gagal' : 'berhasil');
			
			return array($err,$msg,$jml);
		}
		
		function generateTagihan($conn,$filter,$periode,$jenis=null) {
			require_once(Route::getModelPath('seminar'));
			require_once(Route::getModelPath('pesertaseminar'));
			
			$info_seminar = mSeminar::getData($conn,$filter['idseminar']);
			
			//void
			list($err) = static::voidTagihan($conn,$filter,$periode,$jenis);
			
			//get peserta
			$arr_peserta = mPesertaSeminar::getPesertaSeminar($conn,$filter['idseminar']);
			
			$jml = 0;
			if(!$err){
				foreach($arr_peserta as $i => $mhs) {
					$record = array();
					$record['idtagihan'] = $filter['idseminar'].$mhs['nopeserta'].$info_seminar['periode'];
					$record['nopeserta'] = $mhs['nopeserta'];
					$record['tgltagihan'] = date('Y-m-d');
					$record['nominaltagihan'] = $info_seminar['tarifseminar'];
					$record['periode'] = $info_seminar['periode'];
					$record['flaglunas'] = 'BB';
					
					$err = static::insertRecord($conn,$record);
					if($err)
						break; 
				}
			}
		}
		
		function getInquiry($conn,$mhs,$kelompok=null,$periode=null){
			$emhs = Query::escape($mhs);
			$ekelompok = Query::escape($kelompok);
			$eperiode = Query::escape($periode);
			
			$sql = "select t.idtagihan, t.periode, t.flaglunas, t.isvalid, 
					t.nominaltagihan from seminar.sm_tagihan t
					where t.nopeserta = $emhs 
					and not(t.flaglunas = 'F' and t.isvalid <> 0) 
					and t.tgltagihan <= current_date 
					and t.flaglunas in ('BB','BL','S')
					order by case t.flaglunas when 'BB' then 0 when 'BL' then 0 when 'S' then 1 when 'L' then 2 else 3 end,
					case when t.isvalid = 0 then 1 else 0 end, t.periode";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if(!empty($row['isvalid']) and ($row['flaglunas'] == 'BB' or $row['flaglunas'] == 'BL'))
					$data[] = $row;
			}
			
			$arrid = array();
			foreach($data as $row)
				$arrid[] = $row['idtagihan'];
			/*
			// ambil deposit
			$sql = "select iddeposit, periode, jenisdeposit, novoucher, nominaldeposit-nominalpakai as nominaldeposit
					from h2h.ke_deposit
					where coalesce(nim,nopendaftar) = $emhs and status = '-1' and nominaldeposit > nominalpakai
					and (tglexpired is null or tglexpired > current_date) and tgldeposit <= current_date
					and (idtagihan is null or idtagihan in ('".implode("','",$arrid)."'))
					order by case when tglexpired is null then 1 else 0 end, tglexpired, nominaldeposit-nominalpakai";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->fetchRow()) {
				$rowt = array();
				$rowt['iddeposit'] = $row['iddeposit'];
				$rowt['periode'] = $row['periode'];
				$rowt['nominaltagihan'] = -1*$row['nominaldeposit'];
				
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
			*/
			return $data;
		}
		
		function getTagihanFromPembayaran($conn, $idpembayaran){
			$rs = $conn->getOne("select idtagihan from sem.sm_pembayarandetail where idpembayaran = '$idpembayaran'");
			return $rs;
		}

	}
?>
