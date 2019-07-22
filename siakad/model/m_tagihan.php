<?php
	// model tagihan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mTagihan extends mModel {
		const label = 'tagihan';
		const schema = 'h2h';
		const table = 'ke_tagihan';
		
		const billfrm = 'frm';
		const tablefrm = 'bill_frm';
		const keyfrm = 'transactionid';
		
		const kodereg = '001';
		const billreg = 'reg';
		const tablereg = 'bill_reg';
		const keyreg = 'billcode,periode,notest';
		
		const kodeher = '002';
		const billher = 'her';
		const tableher = 'bill_her';
		const keyher = 'billcode,periode,nim';
		
		// mendapatkan field recordset key
		function getKeyRow($row) {
			return $row['key'];
		}
		
		// kondisi satu record
		function getCondition($key,$colkey) {
			if(strpos($colkey,',') === false)
				return $colkey." = '$key'";
			else {
				if(!is_array($key))
					$key = explode('|',$key);
				$pk = explode(',',$colkey);
				
				$cond = array();
				foreach($pk as $i => $t_pk)
					$cond[] = $t_pk." = '".$key[$i]."'";
				
				return implode(' and ',$cond);
			}
		}
		
		// mendapatkan data pager
		function getPagerDataFrm($conn,$kolom,$row,&$page,&$sort,$filter='') {
			$t_key = self::keyfrm;
			
			// mengambil data
			$sql = "select *, $t_key as key from ".self::tablefrm;
			$defsort = "transactionid desc";
			
			return self::getPagerData($conn,$kolom,$row,$page,$sort,$filter,$sql,$defsort);
		}
		
		function getPagerDataReg($conn,$kolom,$row,&$page,&$sort,$filter='') {
			$a_key = explode(',',self::keyreg);
			$t_key = implode("||'|'||",$a_key);
			
			// mengambil data
			$sql = "select *, $t_key as key from ".self::tablereg;
			$defsort = "periode desc,notest";
			
			return self::getPagerData($conn,$kolom,$row,$page,$sort,$filter,$sql,$defsort);
		}
		
		function getPagerDataHer($conn,$kolom,$row,&$page,&$sort,$filter='') {
			$a_key = explode(',',self::keyher);
			$t_key = 'b.'.implode("||'|'||b.",$a_key);
			
			// mengambil data
			$sql = "select b.*, m.nama, $t_key as key
					from ".self::tableher." b join ms_mahasiswa m on b.nim = m.nim";
			$defsort = "b.periode desc,b.nim";
			
			return self::getPagerData($conn,$kolom,$row,$page,$sort,$filter,$sql,$defsort);
		}
		
		function getPagerDataHerWithkey($conn,$kolom,$row,&$page,&$sort,$key) {
			$a_key =  explode(',',self::keyher);
			$t_key = 'b.'.implode("||'|'||b.",$a_key);
			
			// mengambil data
			$sql = "select b.*, m.nama
					from ".self::tableher." b join ms_mahasiswa m on b.nim = m.nim and b.nim='$key'";
			$defsort = "b.periode desc,b.nim";
			
			return self::getPagerData($conn,$kolom,$row,$page,$sort,$filter,$sql,$defsort);
		}
		
		function getPagerData($conn,$kolom,$row,&$page,&$sort,$filter,$sql,$defsort) {
			// getListQuery
			
			// filter
			if(is_array($filter)) {
				if(!empty($lfilter))
					array_unshift($filter,$lfilter);
				
				$filter = implode(' and ',$filter);
			}
			else
				$filter = $lfilter.((!empty($lfilter) and !empty($filter)) ? ' and ' : '').$filter;
			
			if(!empty($filter))
				$sql .= ' where '.$filter;
			
			// sort
			if(empty($sort))
				$sort = $defsort;
			if(!empty($sort))
				$sql .= ' order by '.$sort;
			
			// getPagerData
			
			$start = microtime(true);
			
			if($row > -1) {
				// jika halaman terakhir
				// if($page == -1) {
					$sqlc = "select count(*) from (".$sql.") a";
					$rownum = $conn->GetOne($sqlc);
				// }
				
				if($page == -1)	
					$page = ceil($rownum/$row);
				
				$offset = $row*($page-1);
				$rs = $conn->SelectLimit($sql,$row+1,$offset);
			}
			else {
				$rs = $conn->Execute($sql);
				$row = $rs->RecordCount();
			}
			
			// untuk input
			require_once(Route::getUIPath('form'));
			
			$i = 0;
			$a_data = array();
			while($rowdata = $rs->FetchRow() and $i < $row) {
				if(!empty($kolom)) {
					foreach($kolom as $datakolom) {
						if(empty($datakolom['alias']))
							$field = CStr::getLastPart($datakolom['kolom']);
						else
							$field = $datakolom['alias'];
						
						$value = $rowdata[$field];
						
						if($datakolom['type'] == 'D' or !empty($datakolom['option']) or !empty($datakolom['format']))
							$rowdata['real_'.$field] = $value;
						
						$rowdata[$field] = uForm::getLabel($datakolom,$value);
					}
				}
				
				$a_data[$i++] = $rowdata;
			}
			
			if(empty($rowdata))
				$t_lastpage = true;
			else
				$t_lastpage = false;
			
			$end = microtime(true);
			$time = $end-$start;
			
			Page::setLastPage($t_lastpage);
			Page::setListTime($time);
			Page::setRowNum($rownum);
			
			Page::setLastPage($t_lastpage);
			
			return $a_data;
		}
		function getDataTagihan($conn,$r_nim,$r_periode,$jenis)
		{
			$sql = "select jenistagihan,nopendaftar,nim,tgltagihan,tgldeadline,sum(nominaltagihan) as nominaltagihan,keterangan,periode,bulantahun,isangsur,isedit,sum(potongan) as potongan,flaglunas,tgllunas,jumlahsks,t_updateuser,t_updatetime,t_updateip,isvalid,sum(nominalbayar) as nominalbayar,sum(denda) as denda,angsuranke,isfollowup,keteranganpendaftar from h2h.ke_tagihan where nim='$r_nim' and periode='$r_periode' and jenistagihan='$jenis' GROUP BY jenistagihan,nopendaftar,nim,tgltagihan,tgldeadline,keterangan,periode,bulantahun,isangsur,isedit,flaglunas,tgllunas,t_updateuser,t_updatetime,t_updateip,isvalid,angsuranke,isfollowup,keteranganpendaftar,jumlahsks";
			return $conn->GetArray($sql);
			
		}

		
		//delete data tagihan
		function deleteDataTagihan($conn,$r_nim,$r_periode,$jenis)
		{
			$sql = "delete from h2h.ke_tagihan where nim='$r_nim' and periode='$r_periode' and jenistagihan='$jenis'";
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
		}
		function getInquiry($conn,$mhs,$kelompok=null,$periode=null){
			$emhs = Query::escape($mhs);
			$ekelompok = Query::escape($kelompok);
			$eperiode = Query::escape($periode);
			
			$sql = "select t.idtagihan, t.periode, t.flaglunas, t.isvalid, j.jenistagihan, j.namajenistagihan,
					k.namakelompok, t.nominaltagihan, t.nominalbayar, t.potongan, t.denda, t.bulantahun, t.isangsur from h2h.ke_tagihan t
					join h2h.lv_jenistagihan j on t.jenistagihan = j.jenistagihan
					join h2h.lv_kelompoktagihan k on j.kodekelompok = k.kodekelompok
					where coalesce(t.nim,t.nopendaftar) = $emhs and not(t.flaglunas = 'F' and t.isvalid <> 0) ".
					(empty($kelompok) ? '' : " and j.kodekelompok = $ekelompok").
					(empty($periode) ? '' : " and (t.periode = $eperiode or ((t.periode is null or t.periode < $eperiode) and t.flaglunas in ('BB','BL','S')))").
					" order by t.idtagihan,case t.flaglunas when 'BB' then 0 when 'BL' then 0 when 'S' then 1 when 'L' then 2 else 3 end,
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
			
			return $data;
		}
		function getInquirySebelumnya($conn,$mhs,$kelompok=null,$periode=null){
			$emhs = Query::escape($mhs);
			$ekelompok = Query::escape($kelompok);
			$eperiode = Query::escape($periode);
			
			$sql = "select t.idtagihan, t.periode, t.flaglunas, t.isvalid, j.jenistagihan, j.namajenistagihan,
					k.namakelompok, t.nominaltagihan, t.nominalbayar, t.potongan, t.denda, t.bulantahun, t.isangsur from h2h.ke_tagihan t
					join h2h.lv_jenistagihan j on t.jenistagihan = j.jenistagihan
					join h2h.lv_kelompoktagihan k on j.kodekelompok = k.kodekelompok
					where coalesce(t.nim,t.nopendaftar) = $emhs and not(t.flaglunas = 'F' and t.isvalid <> 0) ".
					(empty($kelompok) ? '' : " and j.kodekelompok = $ekelompok").
					(empty($periode) ? '' : " and (t.periode < $eperiode or ((t.periode is null or t.periode < $eperiode) and t.flaglunas in ('BB','BL','S')))").
					" order by t.idtagihan,case t.flaglunas when 'BB' then 0 when 'BL' then 0 when 'S' then 1 when 'L' then 2 else 3 end,
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
			
			return $data;
		}
		function deleteTmpPembayaran($conn,$kode)
		{
			$sql = "delete from h2h.tmp_pembayaran where kodepembayaran='$kode'";
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
			
		}
		function deleteTmpPembayaranManual($conn,$kode)
		{
			list($kp,$nim) = explode('|',$kode);
			$sql = "delete from h2h.tmp_pembayaran where kodepembayaran='$kp' and nim='$nim'";
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
			
		}

		function updateTmpPembayaran($conn,$kode){
			$sql = "update h2h.tmp_pembayaran set status='P' where kodepembayaran='$kode'";
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
		}
		function updateTmpPembayaranCancel($conn,$kode){
			$sql = "update h2h.tmp_pembayaran set status='C' where kodepembayaran='$kode' and status is null";
			print_r($sql);
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
		}
		function updateTmpPembayaranCancelManual($conn,$kode){
			list($kp,$nim) = explode('|',$kode);
			$sql = "update h2h.tmp_pembayaran set status='C' where kodepembayaran='$kp' and nim='$nim' ";
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
		}
		function generateRandomString($length = 10) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			$randomString = '';
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			return $randomString;
		}
		function InsertDeposit($conn,$data)
		{
			$kolom = implode(',',array_keys($data));
			$valuesArrays = array();
			$i = 0;
			foreach($data as $key=>$values)
			{
				if(is_int($values))
				{
					$valuesArrays[$i] = $values;
				}else{
					$valuesArrays[$i]= "'".$values."'";
				}
				$i++;
			}
			$values = implode(',',$valuesArrays);
			$sql = "insert into h2h.ke_deposit ($kolom) values($values)";
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
		}

		
		// log tagihan tambahan rehan
		function insertInputLog($conn,$data) {
			$record = array();
			$record['idtagihan'] = $data['idtagihan'];
			$record['jenistagihan'] = $data['jenistagihan'];
			$record['nim'] = $data['nim'];
			$record['jumlahsks'] = $data['jumlahsks'];
			$record['periode'] = $data['periode'];
			$record['nominaltagihan'] = $data['nominaltagihan'];
			$record['tglinput'] = date('Y-m-d');
			$record['t_userupdate']=Modul::getUserName();
			return $conn->AutoExecute('h2h.log_ke_tagihan',$record,'INSERT');
		}
		//generate KRS
		function generateTagihankrs($conn, $r_nim, $r_periode, $datamhs){
			// tinggal tambah query insert ke tabel deposit
			require_once(Route::getModelPath('akademikkeu'));
			require_once(Route::getModelPath('tarif'));
			require_once(Route::getModelPath('jenistagihan'));
			if(substr($r_periode,-1)=='1')
				$arr_tagihan = mJenistagihan::getArray($conn,array('S','T','B'),'1');
			else
				$arr_tagihan = mJenistagihan::getArray($conn,array('S','B'),'1');
			
			//$datakrs = mAkademikkeu::getDatakrs($conn,$r_nim,$r_periode);
			if($datamhs['jalurpenerimaan']=="YIM"){
				$datakrs = mAkademikkeu::getDatakrsYIM($conn,$r_nim,$r_periode);
			}else{
				$datakrs = mAkademikkeu::getDatakrs($conn,$r_nim,$r_periode);
			}
			//mengambil data tagihan krs
			$dataTagihan = mTagihan::getDataTagihan($conn,$r_nim,$r_periode);
			$err=0;
			if($arr_tagihan)
				foreach($arr_tagihan as $i => $v){
					$jumlahangsur = ($datamhs['periodemasuk'] >= '20171' ? $v['jumlahangsur2017'] : $v['jumlahangsur']);
					if($datakrs[$v['aturanperiode']]){
					//cari tarif
					$arr_tarif = mTarif::getArraytarif($conn,'',$datamhs['jalurpenerimaan'],$datamhs['kodeunit'],$v['jenistagihan'],$datamhs['sistemkuliah']);
					if($arr_tarif)
					foreach($arr_tarif as $t => $vt)
					{
						$tarif[$vt['periodetarif']][$vt['jalurpenerimaan']][$vt['gelombang']][$vt['sistemkuliah']][$vt['kodeunit']] = $vt;
						}
					if($v['frekuensitagihan']<>'B') {
						$dataTagihan = array();
						$dataTagihan = mTagihan::getDataTagihan($conn,$r_nim,$r_periode,$v['jenistagihan']);
						
						$cek = $conn->GetArray("select * from h2h.ke_tagihan where nim = '$r_nim' 
											and jenistagihan = '".$v['jenistagihan']."' and periode = '".$r_periode."'");
						//var_dump($cek);
						//die();
							if($cek){
								if($cek['isedit'] == 'G')
									{
										if($cek['flaglunas']=='BB'){
											static::deleteperid($conn,$cek['idtagihan']);
											}
										}
									}		
									//custom koding krs
											$dep = array();
											$rec = array();
											$pengali=0;
											foreach ($datakrs as $key => $value) {
												$pengali += $datakrs[$key];
												$rec['jumlahsks'] += $datakrs[$key];
											}
											$rec['jenistagihan'] = $v['jenistagihan'];
											$rec['nim'] = $r_nim;
											$rec['tgltagihan'] = date('Y-m-d');
											$rec['periode'] = $r_periode;
											$rec['isangsur'] = 0;
											$rec['isedit'] = 'G';
											$rec['flaglunas'] = 'BB';
											$rec['nominaltagihan'] = $pengali * $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['gelombang']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
											
										if(empty($dataTagihan)){
											if ($jumlahangsur > 1){
														$rec['nominaltagihan'] = $rec['nominaltagihan']/$jumlahangsur ;
														$rec['nominalbayar'] = ($rec['nominalbayar']/$jumlahangsur);
														$a=1;
														for ($a=1; $a<=$jumlahangsur; $a++){
															$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
															$err = static::insertRecord($conn,$rec);
															self::insertInputLog($conn,$rec);
													}
												}
										}else{
											$rec['nominalbayar']=0;
											$jmlsks=0;
										
											foreach($dataTagihan as $tagihan){
												
												   $rec['nominalbayar'] = $rec['nominalbayar']+$tagihan['nominalbayar'];
												   $jmlsks=$tagihan['jumlahsks'];
											}
										
										
											if($pengali==$jmlsks){
												//delete insert ulang
												$rec['nominalbayar'] = $rec['nominalbayar'];
												$del = mTagihan::deleteDataTagihan($conn,$r_nim,$r_periode,$v['jenistagihan']);
												 if($del==true){
												 	//insert data
												 	/*if ($jumlahangsur > 1){
														$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);
														for ($a=1; $a<=$jumlahangsur; $a++){
															$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
															$err = static::insertRecord($conn,$rec);
															self::insertInputLog($conn,$rec);
														}
													}*/
															$rec['nominaltagihan'] = $rec['nominaltagihan'];
															if($rec['nominaltagihan']==$dataTagihan['nominalbayar'] and $dataTagihan['nominalbayar']>0){
																$rec['flaglunas'] = 'L';
															}elseif($rec['nominaltagihan']>$dataTagihan['nominalbayar'] && $dataTagihan['nominalbayar']>0){
																$rec['flaglunas'] = 'BL';
															}else{
																$rec['flaglunas'] = 'BB';
															}
														if ($jumlahangsur > 1){
															$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);
															$rec['nominalbayar'] = ($rec['nominalbayar']/$jumlahangsur);
															for ($a=1; $a<=$jumlahangsur; $a++){
																$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
																$err = static::insertRecord($conn,$rec);
																self::insertInputLog($conn,$rec);
															}
														}

														

												 }

											}elseif($pengali>$jmlsks){
												//$rec['tgltagihan']=$dataTagihan['tgltagihan'];
												$rec['nominalbayar'] = $rec['nominalbayar'];
												$del = mTagihan::deleteDataTagihan($conn,$r_nim,$r_periode,$v['jenistagihan']);
												 if($del==true){
												 	//insert data
												 	/*if ($jumlahangsur > 1){
														$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);
														for ($a=1; $a<=$jumlahangsur; $a++){
															$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
															$err = static::insertRecord($conn,$rec);
															self::insertInputLog($conn,$rec);
														}
													}*/

															$rec['nominaltagihan'] = $rec['nominaltagihan'];
															if($rec['nominaltagihan']==$dataTagihan['nominalbayar'] and $dataTagihan['nominalbayar']>0){
																$rec['flaglunas'] = 'L';
															}elseif($rec['nominaltagihan']>$dataTagihan['nominalbayar'] && $dataTagihan['nominalbayar']>0){
																$rec['flaglunas'] = 'BL';
															}else{
																$rec['flaglunas'] = 'BB';
															}
														if ($jumlahangsur > 1){
															$a=1;
															$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);
															$rec['nominalbayar'] = ($rec['nominalbayar']/$jumlahangsur);
															for ($a=1; $a<=$jumlahangsur; $a++){
																$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
																$err = static::insertRecord($conn,$rec);
																self::insertInputLog($conn,$rec);
															}
														}

												 }
											
											}elseif($pengali<$jmlsks){
												$rec['nominalbayar'] = $rec['nominalbayar'];
												$del = mTagihan::deleteDataTagihan($conn,$r_nim,$r_periode,$v['jenistagihan']);
												 if($del==true){
												 	//insert data
												 	/*if ($jumlahangsur > 1){
														$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);
														for ($a=1; $a<=$jumlahangsur; $a++){
															$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
															$err = static::insertRecord($conn,$rec);
															self::insertInputLog($conn,$rec);
														}
													}*/
															$rec['nominaltagihan'] = $rec['nominaltagihan'];
															if($rec['nominaltagihan']==$rec['nominalbayar'] and $dataTagihan['nominalbayar']>0){
																$rec['flaglunas'] = 'L';
															}elseif($rec['nominaltagihan']>$rec['nominalbayar'] && $rec['nominalbayar']>0){
																$rec['flaglunas'] = 'BL';
															}else{
																$rec['flaglunas'] = 'BB';
															}
													if ($jumlahangsur > 1){
															//$a=1;
															//$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
															//$err = static::insertRecord($conn,$rec);
															//self::insertInputLog($conn,$rec);
															$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);
															$rec['nominalbayar'] = ($rec['nominalbayar']/$jumlahangsur);
														for ($a=1; $a<=$jumlahangsur; $a++){
															$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);
															$err = static::insertRecord($conn,$rec);
															self::insertInputLog($conn,$rec);
														}
													}
													    if($rec['nominalbayar']>$rec['nominaltagihan']){
															$dep['nim']=$r_nim;
															$dep['tgldeposit'] = date('Y-m-d');
															$dep['periode'] = $r_periode;
															$dep['nominaldeposit'] = $rec['nominalbayar']-$rec['nominaltagihan'];
															$dep['keterangan'] = "Lebih Bayar Pengurangan SKS";
															$dep['status'] = -1;
															mTagihan::InsertDeposit($conn,$dep);
														}

												 }
												
											}
										
											
										}
											/*
												source code bawaan 
											if($v['aturanperiode'] == 'A'){
												$pengali =$datakrs[$v['aturanperiode']];
												$rec['jumlahsks'] = $datakrs[$v['aturanperiode']];
											}else{ 
												$pengali = 1;
											}
											
											if($v['aturanperiode']=='A')
												
											else
												$rec['nominaltagihan'] = $pengali * $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['gelombang']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
											*/
											//var_dump($rec);
											//var_dump($tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['gelombang']]);
											//die();
											
										/*$kolom = implode(',',array_keys($rec));
										$nilai = implode(',', array_values($rec));
										var_dump($kolom);
										var_dump($nilai);
										die();*/
										//var_dump($rec);
										//die();
					}else{ 	

						$frekuensi = mAkademik::getFrekuensibulanan($conn,$r_periode);
						foreach($frekuensi as $i => $val){
								$cek = $conn->getRow("select * from h2h.ke_tagihan where nim = '$r_nim' 
											and jenistagihan = '".$v['jenistagihan']."'
											and bulantahun = '$i'");
							if($cek){
								if($cek['isedit'] == 'G')
									{
										if($cek['flaglunas']=='BB'){
										static::deleteperid($conn,$cek['idtagihan']);
										}
									}
								}	
										$rec = array();
										$rec['jenistagihan'] = $v['jenistagihan'];
										$rec['nim'] = $r_nim;
										$rec['tgltagihan'] = date('Y-m-d');
										$rec['periode'] = $r_periode;
										$rec['bulantahun'] = $i;
										$rec['isangsur'] = 0;
										$rec['isedit'] = 'G';
										$rec['flaglunas'] = 'BB';
										/* source code bawaan
										if($v['aturanperiode'] == 'A'){
												$pengali = $datakrs[$v['aturanperiode']];
											$rec['jumlahsks'] =  $datakrs[$v['aturanperiode']];
											}
										else 
											$pengali = 1;
										if($v['aturanperiode']=='A')
											$rec['nominaltagihan'] = $pengali * $tarif[$datamhs['periodemasuk']][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
										else
											$rec['nominaltagihan'] = $pengali * $tarif[$r_periode][$datamhs['jalurpenerimaan']][$datamhs['sistemkuliah']][$datamhs['kodeunit']]['nominaltarif'];
										*/
											if ($jumlahangsur > 1)
											$rec['nominaltagihan'] = ($rec['nominaltagihan'] / $jumlahangsur);
							
											for ($a=1; $a<=$jumlahangsur; $a++){
												$rec['idtagihan'] = str_pad($v['kodetagihan'],2,'0',STR_PAD_LEFT).$r_periode.str_pad($a,2,'0',STR_PAD_LEFT).str_pad($r_nim,15,'0',STR_PAD_LEFT);

												//$err = static::insertRecord($conn,$rec);
												
												}
						}
					}
				}
			}
			
			return $err;
		}
		function generateTagihan($conn,$filter,$periode,$jenis=null) {
			if(empty($jenis))
				$jenis = static::getListJenisTagihanGenerate($conn,$periode,$filter['sistemkuliah'],$filter['ispendaftar']);
			
			// include
			require_once(Route::getModelPath('akademik'));
			require_once(Route::getModelPath('deposit'));
			require_once(Route::getModelPath('jenistagihan'));
			require_once(Route::getModelPath('loggenerate'));
			require_once(Route::getModelPath('tarif'));
			require_once(Route::getModelPath('tarifreg'));
			
			
			// ambil jenis tagihan, index 0 awal, index 1 rutin
			$rows = mJenistagihan::getArrayTagRutin($conn);
			
			$datakrs = array();
			$datajenis = array();
			foreach($rows as $row) {
				$t_jenis = $row['jenistagihan'];
				if(empty($jenis[$t_jenis]))
					continue;
				
				// cek tagihan yang termasuk awal dan per semester
				/* if($row['frekuensitagihan'] == 'A')
					$datajenis[0][$t_jenis] = $row;
				else
					$datajenis[1][$t_jenis] = $row; */
				
				if(!empty($row['ismaba']))
					$datajenis[0][$t_jenis] = $row;
				if(!empty($row['ismala']))
					$datajenis[1][$t_jenis] = $row;
				
				// cek apa ada tagihan per sks
				if($row['issks'] == '1')
					$datakrs = mAkademik::getDatakrsall($conn,$periode,$nim); // tidak ada data tipekuliah
			}
		
			
			// cek pendaftar, ambil index ke 0 saja
			if(isset($filter['ispendaftar'])) {
				if(empty($filter['ispendaftar']))
					unset($datajenis[0]);
				else
					unset($datajenis[1]);
			}
			
			// di-void dulu
			list($err) = static::voidTagihan($conn,$filter,$periode,$jenis);
			// generate tagihan
			$jml = 0;
			if(!$err) {
				// tambah filter
				$filter['periodetagihan'] = $periode;
				$filter['periodesebelumnya'] = Akademik::getPeriodeSebelumnya($periode);
				
				
				// ambil tagihan yang tidak ter-void
				$tagihan = static::getListTagihanMhsGenerate($conn,$filter,$periode,$jenis);
				
				
				// ambil data periode
				$infoperiode = mAkademik::getDataPeriode($conn,$periode);
				if(empty($infoperiode['bulanawal']))
					$infoperiode['bulanawal'] = Akademik::getBulanAwalPeriode($periode);
			
				foreach($datajenis as $idx => $infojenis) {
					if(!empty($infojenis)) {
					
										
							$arr_mhs = mAkademik::getArraymhsperwalian($conn,$filter); // mahasiswa lama
					

						$jml += count($arr_mhs);
						
						foreach($infojenis as $t_jenis => $infojt) {
							// untuk tagihan tahunan hanya pada semester gasal
							if($infojt['frekuensitagihan'] == 'T' and substr($periode,-1) != '1')
								continue;
							
							// ambil tarif
							$tarif = array();
							if($t_jenis == mTarifReg::jenisTagihan) {
								$arr_tarif = mTarifReg::getArraytarif($conn,$periode,'',$filter['kodeunit'],'');
								if($arr_tarif) {
									foreach($arr_tarif as $i => $v)
										$tarif[$v['periodetarif']][$v['jalurpenerimaan']][$v['gelombang']][$v['sistemkuliah']][$v['kodeunit']][$v['angsuranke']] = array('nominaltarif' => $v['nominaltarif'], 'tgldeadline' => $v['tgldeadline']); // hanya untuk reguler				
								}
							}
							else {
								$arr_tarif = mTarif::getArraytarif($conn,'','',$filter['kodeunit'],$t_jenis,$filter['sistemkuliah'],'');
								if($arr_tarif) {
									foreach($arr_tarif as $i => $v)
										$tarif[$v['periodetarif']][$v['jalurpenerimaan']][$v['gelombang']][$v['sistemkuliah']][$v['kodeunit']] = $v['nominaltarif'];					
								}
							}
							foreach($arr_mhs as $i => $mhs) {
								// per semester tidak termasuk mahasiswa baru
								if($idx == 1 and $mhs['periodemasuk'] == $periode)
									continue;
								
								// cek untuk lulusan smu atau d3
								if(
								   (empty($mhs['mhstransfer']) and empty($infojt['issmu'])) or
								   (!empty($mhs['mhstransfer']) and empty($infojt['isd3']))
								)
									continue;
								
								$record = array();
								$record['jenistagihan'] = $t_jenis;
								$record['periode'] = $periode;
								$record['isangsur'] = 0;
								$record['isedit'] = 'G';
								$record['flaglunas'] = 'BB';
								
								if($mhs['jenisdata'] == 'pendaftar')
									$record['nopendaftar'] = $mhs['nim'];
								else
									$record['nim'] = $mhs['nim'];
								
								// cek sks
								if($infojt['issks'] == '1') {
									$pengali = $datakrs[$mhs['nim']];
									$record['jumlahsks'] = $pengali;
								}
								else
									$pengali = 1;
								
								// cek dengan tagihan existing
								$tertagih = 0;
								if(!empty($tagihan[$mhs['nim']][$t_jenis])) {
									foreach($tagihan[$mhs['nim']][$t_jenis] as $k => $v)
										$tertagih += $v;
								}
								
								// ambil tarif
								$datatarif = $tarif[$mhs['periodemasuk']][$mhs['jalurpenerimaan']][$mhs['gelombang']][$mhs['sistemkuliah']][$mhs['kodeunit']];
								
								if(!empty($datatarif) and !is_array($datatarif)) {
									$datatarif *= $pengali;
									if($datatarif < 0)
										$datatarif = 0;
								}
						
								if(!empty($datatarif)) {
									// jika bukan array, cek jumlahangsur
									if(!is_array($datatarif)) {
										$jumlahangsur = ($mhs['periodemasuk'] >= '20171' ? (int)$infojt['jumlahangsur2017'] : (int)$infojt['jumlahangsur']) ;
										if(empty($jumlahangsur))
											$jumlahangsur = 1;
										
										$nominal = $datatarif/$jumlahangsur;
										
										$datatarif = array();
										for ($a=1; $a<=$jumlahangsur; $a++)
											$datatarif[$a] = array('nominaltarif' => $nominal);
									}
									else
										$jumlahangsur = count($datatarif);
									
									foreach($datatarif as $a => $infotarif) {
										// cek dengan tagihan existing
										$nominal = $infotarif['nominaltarif'];
										if($nominal <= $tertagih) {
											$tertagih -= $nominal;
											$nominal = 0;
										}
										else {
											$nominal -= $tertagih;
											$tertagih = 0;
										}
										
										if(empty($nominal))
											continue;
										
										$record['nominaltagihan'] = $nominal;
										
										// set tgl
										if(empty($infotarif['tgldeadline'])) {
											if($filter['sistemkuliah'] == 'R')
												$record['tgldeadline'] = static::getTglDeadlineReguler($infoperiode,$a);
											else
												$record['tgldeadline'] = static::getTglDeadlineBulanan($infoperiode['bulanawal'],$a);
										}
										else
											$record['tgldeadline'] = $infotarif['tgldeadline'];
											
										$record['tgltagihan'] = static::getTglTagihanByDeadline($record['tgldeadline']);
										$record['bulantahun'] = substr($record['tgltagihan'],0,4).substr($record['tgltagihan'],5,2);
										
										// cek id tagihan
										$b = $a-1;
										do {
											$idtagihan = static::getIDTagihan($infojt['kodetagihan'],$record['periode'],$mhs['nim'],++$b);
										}
										while(isset($tagihan[$mhs['nim']][$t_jenis][$idtagihan]));
										
										$record['idtagihan'] = $idtagihan;
										$record['angsuranke'] = $b;
										
										$tagihan[$mhs['nim']][$t_jenis][$idtagihan] = $nominal;
									
										$err = static::insertRecord($conn,$record);
										if($err)
											break 4; // keluar 4 foreach :D
									}
								} //else
									// list($err,$msginfo) = array(true,'Tarif tidak ditemukan');
							}
						}
					}
				}
			}
			
			// buat voucher beasiswa
			if(!$err)
				$err = mDeposit::generateVoucher($conn,$filter,$periode,$jenis);
			
			$err = ($err ? true : false);
			$msg = 'Generate tagihan '.($err ? 'gagal' : 'berhasil');
			if ($msginfo)
			{
				$msg.= '<br>'.$msginfo;
			}
			return array($err,$msg,$jml);
		}
		function getTglDeadlineReguler($infoperiode,$ke=1) {
			if($ke == 2 and !empty($infoperiode['tgluts']))
				return static::getTglDepan($infoperiode['tgluts'],-14);
			else if($ke == 3 and !empty($infoperiode['tgluas']))
				return static::getTglDepan($infoperiode['tgluas'],-14);
			else
				return static::getTglDeadlineBulanan($infoperiode['bulanawal'],$ke);
		}
		function voidTagihan($conn,$filter,$periode,$jenis=null,$nonaktif=false) {
			if(empty($jenis))
				$jenis = static::getListJenisTagihanGenerate($conn,$periode,$filter['sistemkuliah'],$filter['ispendaftar']);
				
			$injenis = array();
			foreach($jenis as $k => $v)
				$injenis[] = $k;
			$injenis = "'".implode("','",$injenis)."'";
			
			// tambah filter
			if($nonaktif) {
				$filter['ispendaftar'] = false;
				$filter['periode'] = $periode;
				$filter['isnonaktif'] = true;
			}
			
			
			
			// include
			require_once(Route::getModelPath('deposit'));
			
			
			$eperiode = Query::escape($periode);
			
			$sql = "t.flaglunas = 'BB' and t.periode = $eperiode and t.jenistagihan in ($injenis) and exists
					(select 1 from (".mAkademik::sqlMhsPendaftar($filter).") m where coalesce(t.nim,t.nopendaftar) = m.nim)";
			/* if(empty($nonaktif))
				$sql .= " and t.isedit = 'G'"; */
			
			// ambil jumlahnya dulu
			$jml = $conn->GetOne("select count(distinct(coalesce(t.nim,t.nopendaftar))) from h2h.ke_tagihan t where ".$sql);
			
			// baru delete
			$err = Query::qDelete($conn,static::table().' t',$sql,false);
		
			// untuk void tagihan non aktif
			if($nonaktif) {
				// membuat deposit dari tagihan terbayar mhs
				if(!$err) {
					$sql = "insert into ".static::table('ke_deposit')." (nim,tgldeposit,nominaldeposit,periode,status,jenisdeposit)
							select t.nim, current_date, sum(d.nominalbayar), $eperiode, '-1', 'D'
							from ".static::table()." t
							join ".static::table('ke_pembayarandetail')." d on d.idtagihan = t.idtagihan and d.iddeposit is null
							join ".static::table('ke_pembayaran')." b on d.idpembayaran = b.idpembayaran and b.flagbatal = '0'
							join (".mAkademik::sqlMhsPendaftar($filter).") m on coalesce(t.nim,t.nopendaftar) = m.nim
							where t.isvalid = -1 and t.flaglunas = 'L' and t.periode = $eperiode and t.jenistagihan in ($injenis)
							group by t.nim";
					$conn->Execute($sql);
					$err = $conn->ErrorNo();
				}
				
				// set tagihan invalid
				if(!$err) {
					$sql = "update ".static::table()." t set isvalid = 0
							from (".mAkademik::sqlMhsPendaftar($filter).") m
							where coalesce(t.nim,t.nopendaftar) = m.nim and t.isvalid = -1 and
							t.flaglunas = 'L' and t.periode = $eperiode and t.jenistagihan in ($injenis)";
					$conn->Execute($sql);
					$err = $conn->ErrorNo();
				}
			}
			
			
			// void voucher beasiswa
			if(!$err){
				$err = mDeposit::voidVoucher($conn,$filter,$periode,$jenis);
			}
			
			//$err = ($err ? true : false);
	
			$msg = 'Penghapusan tagihan '.($err ? 'gagal' : 'berhasil');
		
			return array($err,$msg,$jml);
		}
		function getTglDeadlineBulanan($bulanawal,$ke=1,$tgl=10) {
			$tahun = (int)substr($bulanawal,0,4);
			$bulan = (int)substr($bulanawal,4,2);
			
			for($i=1;$i<$ke;$i++) {
				if($bulan == 12) {
					$bulan = 0;
					$tahun++;
				}
				
				$bulan++;
			}
			
			return $tahun.'-'.str_pad($bulan,2,'0',STR_PAD_LEFT).'-'.$tgl;
		}
		function getTglTagihanByDeadline($tgldeadline) {
			$tahun = substr($tgldeadline,0,4);
			$bulan = substr($tgldeadline,5,2);
			$tgl = substr($tgldeadline,8,2);
			
			// selisih sebulan
			/* $bulan--;
			if(empty($bulan)) {
				$bulan = 12;
				$tahun--;
			} */
			
			// ambil tanggal 1
			$tgl = '01';
			
			return $tahun.'-'.str_pad($bulan,2,'0',STR_PAD_LEFT).'-'.$tgl;
		}
		function getIDTagihan($kodetagihan,$periode,$id,$angsuranke=1) {
			return str_pad($kodetagihan,2,'0',STR_PAD_LEFT).$periode.str_pad($angsuranke,2,'0',STR_PAD_LEFT).str_pad($id,15,'0',STR_PAD_LEFT);
		}
		function getListTagihanMhsGenerate($conn,$filter,$periode,$jenis=null) {
			if(empty($jenis))
				$jenis = static::getListJenisTagihanGenerate($conn,$periode,$filter['sistemkuliah'],$filter['ispendaftar']);
			
			$injenis = array();
			foreach($jenis as $k => $v)
				$injenis[] = $k;
			$injenis = "'".implode("','",$injenis)."'";
			
			$sql = "select coalesce(t.nim,t.nopendaftar) as nim, t.jenistagihan, t.idtagihan, t.nominaltagihan
					from ".static::table()." t
					join (".mAkademik::sqlMhsPendaftar($filter).") m on coalesce(t.nim,t.nopendaftar) = m.nim
					where t.periode = ".Query::escape($periode)." and t.jenistagihan in ($injenis)";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['nim']][$row['jenistagihan']][$row['idtagihan']] = (float)$row['nominaltagihan'];
			
			return $data;
		}
		
		
		
		// mendapatkan data beserta input
		function getDataEditFrm($conn,$kolom,$key,$post='') {
			$sql = "select * from ".self::tablefrm." where ".self::getCondition($key,self::keyfrm);
			
			return self::getDataEdit($conn,$kolom,$sql,$post);
		}
		
		function getDataEditReg($conn,$kolom,$key,$post='') {
			$sql = "select * from ".self::tablereg." where ".self::getCondition($key,self::keyreg);
			
			return self::getDataEdit($conn,$kolom,$sql,$post);
		}
		
		function getDataEditHer($conn,$kolom,$key,$post='') {
			list($billcode,$periode,$nim) = explode('|',$key);
			
			$sql = "select *, substring(periode,1,4) as tahun, substring(periode,5) as semester
					from ".self::tableher." where ".self::getCondition($key,self::keyher);
			
			return self::getDataEdit($conn,$kolom,$sql,$post);
		}
		
		function getDataEdit($conn,$kolom,$sql,$post) {
			// untuk input
			require_once(Route::getUIPath('form'));
			
			// getData
			
			$row = $conn->GetRow($sql);
			
			if(!empty($post)) {
				foreach($post as $k => $v)
					$row[$k] = $v;
			}
			
			$data = array();
			foreach($kolom as $datakolom) {
				$field = $datakolom['kolom'];
				
				$t_data = array();
				$t_data['id'] = empty($datakolom['nameid']) ? $datakolom['kolom'] : $datakolom['nameid'];
				$t_data['label'] = $datakolom['label'];
				$t_data['realvalue'] = $row[$field];
				$t_data['value'] = uForm::getLabel($datakolom,$row[$field]);
				$t_data['notnull'] = $datakolom['notnull'];
				
				if($datakolom['readonly'])
					$t_data['input'] = $t_data['value'];
				else
					$t_data['input'] = uForm::getInput($datakolom,$row[$field]);
				
				$data[] = $t_data;
			}
			
			return $data;
		}
		
		// insert data
		function insertCRecordFrm($conn,$kolom,$record) {
			return self::insertCRecord($conn,$kolom,$record,self::tablefrm);
		}
		
		function insertCRecordHer($conn,$kolom,$record) {
			return self::insertCRecord($conn,$kolom,$record,self::tableher);
		}
		
		function insertCRecordReg($conn,$kolom,$record) {
			return self::insertCRecord($conn,$kolom,$record,self::tablereg);
		}
		
		function insertCRecord($conn,$kolom,$record,$table) {
			// unset record
			if(!empty($kolom)) {
				foreach($kolom as $datakolom) {
					if($datakolom['readonly'])
						unset($record[$datakolom['kolom']]);
				}
			}
			
			$err = Query::recInsert($conn,$record,$table);
			
			return $err;
		}
		
		// update data
		function updateCRecordFrm($conn,$kolom,$record,$key) {
			return self::updateCRecord($conn,$kolom,$record,self::tablefrm,self::keyfrm,$key);
		}
		
		function updateCRecordHer($conn,$kolom,$record,$key) {
			return self::updateCRecord($conn,$kolom,$record,self::tableher,self::keyher,$key);
		}
		
		function updateCRecordReg($conn,$kolom,$record,$key) {
			return self::updateCRecord($conn,$kolom,$record,self::tablereg,self::keyreg,$key);
		}
		
		function updateCRecord($conn,$kolom,$record,$table,$colkey,$key) {
			global $conf;
			
			// unset record
			if(!empty($kolom)) {
				foreach($kolom as $datakolom) {
					if($datakolom['readonly'])
						unset($record[$datakolom['kolom']]);
				}
			}
			
			$err = Query::recUpdate($conn,$record,$table,self::getCondition($key,$colkey));
			
			return $err;
		}
		
		// delete data
		function deleteFrm($conn,$key) {
			return self::delete($conn,self::tablefrm,self::keyfrm,$key);
		}
		
		function deleteHer($conn,$key) {
			return self::delete($conn,self::tableher,self::keyher,$key);
		}
		
		function deleteReg($conn,$key) {
			return self::delete($conn,self::tablereg,self::keyreg,$key);
		}
		
		function delete($conn,$table,$colkey,$key) {
			$err = Query::qDelete($conn,$table,self::getCondition($key,$colkey));
			
			return $err;
		}
		
		// mendapatkan data lunas
		function getLunasFrm($conn,$key) {
			$sql = "select lunas from ".self::tablefrm." where ".self::getCondition($key,self::keyfrm);
			$lunas = $conn->GetOne($sql);
			
			return (empty($lunas) ? false : true);
		}
		
		function getLunasHer($conn,$key) {
			$sql = "select lunas from ".self::tableher." where ".self::getCondition($key,self::keyher);
			$lunas = $conn->GetOne($sql);
			
			return (empty($lunas) ? false : true);
		}
		
		function getLunasReg($conn,$key) {
			$sql = "select lunas from ".self::tablereg." where ".self::getCondition($key,self::keyreg);
			$lunas = $conn->GetOne($sql);
			
			return (empty($lunas) ? false : true);
		}
		
		// mendapatkan nama mahasiswa
		function getNamaMahasiswa($conn,$nim) {
			$sql = "select nama from ms_mahasiswa where nim = '$nim'";
			
			return $conn->GetOne($sql);
		}
		
		// generate tagihan
		function generateTagihanReg($conn,$periode,$kodeunit,$jalur) {
			$connh = Query::connect('h2h');
			$connh->debug = $conn->debug;
			
			$connh->BeginTrans();
			
			// mengambil tarif
			$sql = "select t.jumlahtotal, td.nourut, td.jumlahtarif
					from akademik.ke_tarif t
					left join akademik.ke_tarifdetail td on td.idtarif = t.idtarif
					where t.jenistarif = '".self::kodereg."' and substring(t.periodemasuk,1,4) = '$periode'
					and t.kodeunit = '$kodeunit' and t.jalurpenerimaan = '$jalur'";
			$rs = $conn->Execute($sql);
			
			$a_tarif = array('billamount' => 0);
			while($row = $rs->FetchRow()) {
				if(isset($row['nourut'])) {
					$a_tarif['bil'.$row['nourut']] = (float)$row['jumlahtarif'];
					$a_tarif['billamount'] += (float)$row['jumlahtarif'];
				}
				else
					$a_tarif = array('billamount' => (float)$row['jumlahtotal']);
			}
			
			// mengambil unit
			$sql = "select * from ms_unit where jurusan = '$kodeunit'";
			$a_unit = $connh->GetRow($sql);
			
			// mengambil pendaftar
			$sql = "select p.nopendaftar, p.nama, p.pilihanditerima
					from pendaftaran.pd_pendaftar p
					where p.periodedaftar = '$periode' and p.jalurpenerimaan = '$jalur'
					and p.nopendaftar is not null and p.pilihanditerima = '$kodeunit'";
			$rs = $conn->Execute($sql);
			
			// di-uppercase
			$jalur = strtoupper($jalur);
			
			// field yang diinsert
			$a_field = array('billcode','periode','notest','nama','fakultas','jurusan',
							'billamount','jalur','namajurusan','time_modified','ip_client',
							'bil1','bil2','bil3','bil4','bil5','bil6','bil7','bil8','bil9','bil10','bil11','bil12','bil13');
			
			$t_data = array();
			$t_data['billcode'] = 'reg';
			$t_data['periode'] = $periode;
			$t_data['jalur'] = strtoupper($jalur);
			$t_data['time_modified'] = date('Y-m-d H:i:s');
			$t_data['ip_client'] = $_SERVER['REMOTE_ADDR'];
			
			$t_data += $a_tarif;
			
			$a_data = array();
			while($row = $rs->FetchRow()) {
				$t_data['notest'] = $row['nopendaftar'];
				$t_data['nama'] = strtoupper($row['nama']);
				
				$t_data = array_merge($t_data,$a_unit);
				
				$t_ins = array();
				foreach($a_field as $t_field)
					$t_ins[] = CStr::cStrNullS($t_data[$t_field],false);
				
				$a_data[] = '('.implode(',',$t_ins).')';
			}
			
			// hapus tagihan
			$sql = "delete from bill_reg where periode = '$periode' and jalur = '$jalur'
					and jurusan = '$kodeunit' and coalesce(lunas,0) <> 1";
			$ok = $connh->Execute($sql);
			
			// masukkan tagihan
			if($ok and !empty($a_data)) {
				$sql = "select ".implode(',',$a_field)." into temp bill_reg_temp from bill_reg limit 0;
						insert into bill_reg_temp (".implode(',',$a_field).") values ".implode(',',$a_data).";
						insert into bill_reg (".implode(',',$a_field).")
							select t.".str_replace(',',',t.',implode(',',$a_field))." from bill_reg_temp t
							left join bill_reg b on b.billcode = t.billcode and b.periode = t.periode and b.notest = t.notest
							where b.billcode is null";
				$ok = $connh->Execute($sql);
			}
			
			$connh->CommitTrans($ok);
			
			$err = Query::isErr($ok);
			
			return $err;
		}
		
		function generateTagihanHer($conn,$periode,$kodeunit,$jalur) {
			$connh = Query::connect('h2h');
			$connh->debug = $conn->debug;
			
			$connh->BeginTrans();
			
			// mengambil tarif
			$sql = "select t.periodemasuk, t.jumlahtotal, td.nourut, td.jumlahtarif
					from akademik.ke_tarif t
					left join akademik.ke_tarifdetail td on td.idtarif = t.idtarif
					where t.jenistarif = '".self::kodeher."'
					and t.kodeunit = '$kodeunit' and t.jalurpenerimaan = '$jalur'
					order by t.periodemasuk desc";
			$rs = $conn->Execute($sql);
			
			$a_tarif = array();
			while($row = $rs->FetchRow()) {
				$t_periode = $row['periodemasuk'];
				
				if(isset($row['nourut'])) {
					$a_tarif[$t_periode]['bil'.$row['nourut']] = (float)$row['jumlahtarif'];
					$a_tarif[$t_periode]['billamount'] += (float)$row['jumlahtarif'];
				}
				else
					$a_tarif[$t_periode] = array('billamount' => (float)$row['jumlahtotal']);
			}
			
			// mengambil pendaftar
			$sql = "select m.nim, m.periodemasuk, m.semestermhs, m.statusmhs
					from akademik.ms_mahasiswa m where m.statusmhs in ('A','C')
					and m.jalurpenerimaan = '$jalur' and m.kodeunit = '$kodeunit'";
			$rs = $conn->Execute($sql);
			
			// field yang diinsert
			$a_field = array('billcode','periode','nim','semester','keuangan','time_created','ip_client',
							'billamount','bil1','bil2','bil3','bil4','bil5','bil6','bil7');
			
			$t_data = array();
			$t_data['billcode'] = 'her';
			$t_data['periode'] = $periode;
			$t_data['keuangan'] = 0;
			$t_data['time_created'] = date('Y-m-d H:i:s');
			$t_data['ip_client'] = $_SERVER['REMOTE_ADDR'];
			
			$a_data = array();
			while($row = $rs->FetchRow()) {
				$t_data['nim'] = substr($row['nim'],0,9);
				$t_data['semester'] = (int)$row['semestermhs'];
				
				// mencari tarif yang tepat
				unset($t_tarif);
				foreach($a_tarif as $t_periode => $t_tarifperiode) {
					if($row['periodemasuk'] >= $t_periode) {
						$t_tarif = $t_tarifperiode[$row['jalurpenerimaan']];
						if(!empty($t_tarif))
							break;
					}
				}
				
				if(empty($t_tarif))
					$t_tarif = array('billamount' => 0);
				
				$t_data += $t_tarif;
				
				$t_ins = array();
				foreach($a_field as $t_field)
					$t_ins[] = CStr::cStrNullS($t_data[$t_field],false);
				
				$a_data[] = '('.implode(',',$t_ins).')';
			}
			
			// hapus tagihan
			if(!empty($a_data)) {
				$sql = "select ".implode(',',$a_field)." into temp bill_her_temp from bill_her limit 0;
						insert into bill_her_temp (".implode(',',$a_field).") values ".implode(',',$a_data).";
						delete from bill_her where periode = '$periode' and coalesce(lunas,0) <> 1 and nim in
							(select nim from bill_her_temp);
						insert into bill_her (".implode(',',$a_field).")
							select t.".str_replace(',',',t.',implode(',',$a_field))." from bill_her_temp t
							join ms_mahasiswa m on m.nim = t.nim
							left join bill_her b on b.billcode = t.billcode and b.periode = t.periode and b.nim = t.nim
								where b.billcode is null";
				$ok = $connh->Execute($sql);
			}
			
			$connh->CommitTrans($ok);
			
			$err = Query::isErr($ok);
			
			return $err;
		}
		
		// rekap tagihan
		function getRekapTagihanReg($conn,$periode, $unit, $jalur) {
			$connh = Query::connect('h2h');
			$connh->debug = $conn->debug;
			
			$t_lain = 'Lain-lain';
			
			// data unit
			$sql = "select u.kodeunit, u.namaunit as jurusan, up.namaunit as fakultas, j.jalurpenerimaan
					from gate.ms_unit u
					join gate.ms_unit up on up.kodeunit = u.kodeunitparent
					cross join akademik.lv_jalurpenerimaan j
					order by u.infoleft, j.jalurpenerimaan";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()) {
				if($t_kodeunit != $row['kodeunit'])
					$t_kodeunit = $row['kodeunit'];
				
				$a_data[$t_kodeunit][$row['jalurpenerimaan']] = $row;
				
				if($rs->EOF or $rs->fields['kodeunit'] != $t_kodeunit) {
					$row['jalurpenerimaan'] = $t_lain;
					
					$a_data[$t_kodeunit][$row['jalurpenerimaan']] = $row;
				}
			}
			
			$row = array();
			$row['kodeunit'] = '-';
			$row['jurusan'] = $t_lain;
			$row['fakultas'] = $t_lain;
			$row['jalurpenerimaan'] = $t_lain;
			
			$a_data[$row['kodeunit']][$row['jalurpenerimaan']] = $row;
			
			// data pendaftar
			$sql = "select pilihanditerima, jalurpenerimaan,
					count(*) as jmlpendaftar, count(case when coalesce(isasing,0) = 0 then null else 1 end) as jmlasing
					from pendaftaran.pd_pendaftar
					where periodedaftar = '$periode' and pilihanditerima ='$unit' and jalurpenerimaan = '$jalur'
					group by pilihanditerima, jalurpenerimaan";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()) {
				$t_kodeunit = $row['pilihanditerima'];
				$t_jalurpenerimaan = $row['jalurpenerimaan'];
				
				if(empty($t_jalurpenerimaan))
					$t_jalurpenerimaan = $t_lain;
				
				unset($row['pilihanditerima'],$row['jalurpenerimaan']);
				
				$a_data[$t_kodeunit][$t_jalurpenerimaan] += $row;
			}
			
			// data tagihan
			$sql = "select jurusan, jalur, count(*) as jmltagihan from ".self::tablereg."
					where periode = '$periode' and jalur = upper('$jalur') and jurusan = '$unit' group by jurusan, jalur";
			$rs = $connh->Execute($sql);
			
			while($row = $rs->FetchRow()) {
				$t_kodeunit = $row['jurusan'];
				$t_jalurpenerimaan = ucfirst(strtolower($row['jalur']));
				
				if(empty($a_data[$t_kodeunit])) {
					$t_kodeunit = '-';
					$t_jalurpenerimaan = $t_lain;
				}
				else if(empty($a_data[$t_kodeunit][$t_jalurpenerimaan]))
					$t_jalurpenerimaan = $t_lain;
				
				unset($row['jurusan'],$row['jalur']);
				
				$a_data[$t_kodeunit][$t_jalurpenerimaan] += $row;
			}
			
			// bersih bersih
			foreach($a_data as $t_unit => $t_dataunit) {
				foreach($t_dataunit as $t_jalur => $t_data) {
					if(!(isset($t_data['jmlpendaftar']) or isset($t_data['jmltagihan'])))
						unset($a_data[$t_unit][$t_jalur]);
				}
			}
			
			return $a_data;
		}
		
		function getRekapTagihanHer($conn,$periode) {
			$connh = Query::connect('h2h');
			$connh->debug = $conn->debug;
			
			$t_lain = 'Lain-lain';
			
			// data unit
			$sql = "select u.kodeunit, u.namaunit as jurusan, up.namaunit as fakultas, j.jalurpenerimaan
					from gate.ms_unit u
					join gate.ms_unit up on up.kodeunit = u.kodeunitparent
					cross join akademik.lv_jalurpenerimaan j
					order by u.infoleft, j.jalurpenerimaan";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()) {
				if($t_kodeunit != $row['kodeunit'])
					$t_kodeunit = $row['kodeunit'];
				
				$a_data[$t_kodeunit][$row['jalurpenerimaan']] = $row;
				
				if($rs->EOF or $rs->fields['kodeunit'] != $t_kodeunit) {
					$row['jalurpenerimaan'] = $t_lain;
					
					$a_data[$t_kodeunit][$row['jalurpenerimaan']] = $row;
				}
			}
			
			$row = array();
			$row['kodeunit'] = '-';
			$row['jurusan'] = $t_lain;
			$row['fakultas'] = $t_lain;
			$row['jalurpenerimaan'] = $t_lain;
			
			$a_data[$row['kodeunit']][$row['jalurpenerimaan']] = $row;
			
			// data mahasiswa
			$sql = "select nim, kodeunit, jalurpenerimaan, isasing
					from akademik.ms_mahasiswa
					where statusmhs in ('A','C')";
			$rs = $conn->Execute($sql);
			
			$a_unitmhs = array();
			$a_jalurmhs = array();
			$a_prefnim = array();
			while($row = $rs->FetchRow()) {
				$t_nim = $row['nim'];
				$t_kodeunit = $row['kodeunit'];
				$t_jalurpenerimaan = $row['jalurpenerimaan'];
				
				if(empty($t_jalurpenerimaan))
					$t_jalurpenerimaan = $t_lain;
				
				$a_unitmhs[$t_nim] = $t_kodeunit;
				$a_jalurmhs[$t_nim] = $t_jalurpenerimaan;
				
				$a_data[$t_kodeunit][$t_jalurpenerimaan]['jmlmahasiswa']++;
				if(!empty($row['isasing']))
					$a_data[$t_kodeunit][$t_jalurpenerimaan]['jmlasing']++;
				
				// prefix tahun
				$t_prefnim = substr($t_nim,4,2);
				$a_prefnim[$t_prefnim] = $t_prefnim;
			}
			
			// data mahasiswa h2h
			$sql = "select nim from ms_mahasiswa where substring(nim,5,2)
					in ('".implode("','",$a_prefnim)."')";
			$rs = $connh->Execute($sql);
			
			while($row = $rs->FetchRow()) {
				$t_nim = $row['nim'];
				$t_kodeunit = $a_unitmhs[$t_nim];
				
				if(!empty($t_kodeunit)) {
					$t_jalurpenerimaan = $a_jalurmhs[$t_nim];
					
					$a_data[$t_kodeunit][$t_jalurpenerimaan]['jmlmhsh2h']++;
				}
			}
			
			// data tagihan, manual
			$sql = "select nim from ".self::tableher." where periode = '$periode'";
			$rs = $connh->Execute($sql);
			
			while($row = $rs->FetchRow()) {
				$t_kodeunit = $a_unitmhs[$row['nim']];
				$t_jalurpenerimaan = $a_jalurmhs[$row['nim']];
				
				if(empty($a_data[$t_kodeunit])) {
					$t_kodeunit = '-';
					$t_jalurpenerimaan = $t_lain;
				}
				else if(empty($a_data[$t_kodeunit][$t_jalurpenerimaan]))
					$t_jalurpenerimaan = $t_lain;
				
				$a_data[$t_kodeunit][$t_jalurpenerimaan]['jmltagihan']++;
			}
			
			// bersih bersih
			foreach($a_data as $t_unit => $t_dataunit) {
				foreach($t_dataunit as $t_jalur => $t_data) {
					if(!(isset($t_data['jmlmahasiswa']) or isset($t_data['jmltagihan'])))
						unset($a_data[$t_unit][$t_jalur]);
				}
			}
			
			return $a_data;
		}
		
		function getRekapHerUnit($conn,$periode) {
			$sql = "select m.jurusan, coalesce(b.lunas,0) as lunas, sum(case when b.billamount = '' then 0 else b.billamount::numeric end) as billamount
					from bill_her b
					join ms_mahasiswa m on b.nim = m.nim where b.periode = '$periode'
					group by m.jurusan,coalesce(b.lunas,0)";
					
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()){
				$a_data[$row['jurusan']]['total'] = (float)$row['billamount'];

				if ($row['lunas']==1)
					$a_data[$row['jurusan']]['bayar'] = (float)$row['billamount'];
				else
					$a_data[$row['jurusan']]['belum'] = (float)$row['billamount'];	
			 
				}
				
			return $a_data;
		}
		
		// status mahasiswa
		function status($conn) {
			$sql = "select status, nama_status from ms_status order by status";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function statusHer($conn) {
			$sql = "select status, nama_status from ms_status where her = '1' order by status";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// periode
		function periode($conn) {
			$sql = "select periode from ms_periode order by periode desc";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function periodeDaftar($conn) {
			$sql = "select periodedaftar from pendaftaran.ms_periodedaftar order by periodedaftar desc";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// unit
		function unit($conn) {
			$sql = "select fakultas, jurusan, namajurusan from ms_unit order by fakultas,jurusan";
			$rs = $conn->Execute($sql);
			
			$i = 0;
			$a_unit = array();
			while($row = $rs->FetchRow()) {
				if($row['fakultas'] != $t_fakultas) {
					$t_fakultas = $row['fakultas'];
					$a_unit[str_repeat(' ',$i)] = 'FAKULTAS '.$t_fakultas;
					
					$i++;
				}
				
				$a_unit[$row['jurusan']] = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$row['namajurusan'];
			}
			
			return $a_unit;
		}
		
		// billcode
		function billCodeFrm($conn) {
			$sql = "select billcode, billname from ms_bill where formulir = 1";
			
			return Query::arrQuery($conn,$sql);
		}

		function sendMail($tujuan,$subject,$body){
			global $conf;
			include($conf['includes_dir'].'PHPMailer2/PHPMailerAutoload.php');
			
			if(!empty($tujuan)){
				$mail = new PHPMailer;
				//$mail->SMTPDebug = 2;
				$mail->SMTPOptions = array(
				    'ssl' => array(
				        'verify_peer' => false,
				        'verify_peer_name' => false,
				        'allow_self_signed' => true
				    )
				);
				// Konfigurasi SMTP
				$mail->isSMTP();
				
				try {
					$mail->IsHTML();
					$mail->SMTPAuth = true;
					$mail->SMTPSecure = 'tls';
					$mail->Host     = $conf['smtp_host']; // SMTP servers
					$mail->SMTPAuth = true;
					$mail->Username = $conf['smtp_user'];
					$mail->Password = $conf['smtp_pass'];
					$mail->Port       = 587;//$conf['smtp_port'];
					$mail->ClearAddresses();
					$mail->AddAddress($tujuan);
					$mail->From = $conf['smtp_email'];
					$mail->FromName ='IT-INABA';
					$mail->Subject = $subject;
					$mail->Body = $body;			
					$ok = $mail->Send();
					if ($ok){
						return array(false,'Pengiriman Email Berhasil');
					}
					else{
						return array(false,'Pengiriman Email Berhasil');
					}


					
				} catch (phpmailerException $e) {
					return array(true,'Pengiriman Email Gagal');
				} catch (Exception $e) {
					return array(true,'Pengiriman Email Gagal');
				}
			}
		}
		
		function getTmpPembayaran($conn,$nim){
			$sql = "select kodepembayaran,url,noinvoice,datecreate,expireddate,sof_id,sum(nominaltagihan) as total from h2h.tmp_pembayaran where nim='$nim' and status is null group by kodepembayaran,datecreate,expireddate,noinvoice,sof_id,url";
			return $conn->GetRow($sql);
		}
		function getTmpPembayaranAll($conn){
			$sql = "select kodepembayaran,nim,url,noinvoice,datecreate,expireddate,sof_id,sum(nominaltagihan) as total 
			from h2h.tmp_pembayaran where status is null group by kodepembayaran,datecreate,expireddate,noinvoice,sof_id,url,nim";
			return $conn->GetArray($sql);
		}
		function metodePembayaran($jenis){
			if($jenis=='vabni'){
				return "Virtual Account BNI";
			}elseif($jenis=='vamandiri'){
				return "Virtual Account Bank Mandiri";
			}elseif($jenis=='vapermata'){
				return "Virtual Account Bank Permata";
			}elseif($jenis=='cc'){
				return "Kartu Kredit";
			}else{
				return "Finpay Telkom - Pembayaran Melalui Channel Telkom Alfamart/Pengadaian/Kantor POS";
			}
		}

		function getAllTmpPembayaran($conn,$kode){
			$sql = "select * from h2h.tmp_pembayaran where kodepembayaran='$kode' and status is null";
			return $conn->GetArray($sql);
		}
		function insertTmpPembayaran($conn,$data){
			$kolom = implode(',',array_keys($data));
			$valuesArrays = array();
			$i = 0;
			foreach($data as $key=>$values)
			{
				if(is_int($values))
				{
					$valuesArrays[$i] = $values;
				}else{
					$valuesArrays[$i]= "'".$values."'";
				}
				$i++;
			}
			$values = implode(',',$valuesArrays);
			$sql = "insert into h2h.tmp_pembayaran ($kolom) values($values)";
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
		}

		function cekdataTagihan($conn){
			$sql = "select t.nim,periode,count(idtagihan) from h2h.ke_tagihan t join akademik.ms_mahasiswa m on m.nim=t.nim  where periode='20182' and m.statusmhs='A' group by t.nim,periode HAVING count(idtagihan) = 3";
			return $conn->getArray($sql);

		}
	}
?>