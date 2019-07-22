<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mMastKepegawaian extends mModel {
		const schema = 'sdm';
		
		//detail unit
		function getDataUnit($r_key){
			$sql = "select u.*, case when u.nippimpinan is not null then 
					coalesce(p.nip||' - ','')||".self::schema('sdm')."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) end as namapimpinan,
					substring(blnthnmulai,1,4) as tahun,cast(substring(blnthnmulai,5,2) as int) as bulan
					from ".self::table('ms_unit')." u
					left join ".self::table('ms_pegawai')." p on u.nippimpinan = p.idpegawai
					where u.idunit = $r_key";
					
			return $sql;
		}
		
		//is akademik unit
		function isAkademik() {
			$data = array( 'Y' => 'Akademik', 'T' => 'Non Akademik');
			
			return $data;
		}
		
		//Program Studi
		function listProgramPendidikan($conn) {
			$sql = "select kodeprogram,namaprogram from ".self::table('ms_programpend');
			
			return Query::arrQuery($conn,$sql);
		}
		
		//parent unit
		function listUnit() {
			$sql = "select u.*,pu.namaunit as namaparentunit from ".self::table('ms_unit')." u
					left join ".self::table('ms_unit')." pu on pu.idunit = u.parentunit";
			
			return $sql;
		}
		
		//level dan kategori parent
		function pUnit($conn,$p_key) {
			$sql = "select isakademik,isaktif from ".self::table('ms_unit')." where idunit = $p_key";
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		//inforigth ketika insert tanpa parent
		function saveInfoUnit($conn,$parent,$r_key){
			$record = array();
			$parent = $parent == 'null' ? '' : $parent;
			
			if(empty($r_key)){//insert
				if(empty($parent)){
					$maxright = $conn->GetOne("select max(inforight) from ".self::table('ms_unit')."");
					
					$record['infoleft'] = $maxright + 1;
					$record['inforight'] = $maxright + 2;
					$record['level'] = '0';
				}else{
					$parentright = $conn->GetOne("select inforight from ".self::table('ms_unit')." where idunit = $parent");
				
					if(!empty($parentright)){
						$ok = $conn->Execute("update ".self::table('ms_unit')." set inforight = inforight+2 where inforight >= $parentright");
						if($ok)
							$ok = $conn->Execute("update ".self::table('ms_unit')." set infoleft = infoleft+2 where infoleft > $parentright");
						else
							break;
							
						if($ok) {
							$record['infoleft'] = $parentright;
							$record['inforight'] = $parentright + 1;
							$record['level'] = $conn->GetOne("select coalesce(level,0)+1 as level from ".self::table('ms_unit')." where idunit = $parent");
						}else
							break;
					}
				}
			}else{//update
				if(!empty($parent)) {
					$p_cek = $conn->GetOne("select parentunit from ".self::table('ms_unit')." where idunit = $r_key");
					if($p_cek != $parent){
						// cek unit
						$sql = "select infoleft, inforight from ".self::table('ms_unit')." where idunit = $r_key";
						$rowc = $conn->GetRow($sql);
						
						$sql = "select infoleft, inforight,level from ".self::table('ms_unit')." where idunit = '$parent'";
						$rowp = $conn->GetRow($sql);
						
						//if($rowp['infoleft'] > $rowc['infoleft'] and $rowp['inforight'] < $rowc['inforight'])
						//	die('Pemindahan tidak bisa dilakukan, unit parent baru merupakan child unit');
						
						$a = '('.((int)$rowc['inforight']-(int)$rowc['infoleft']+1).')';
						
						// siapkan tempat
						$sql = "update ".self::table('ms_unit')." set infoleft = infoleft+$a where infoleft > '".($rowp['inforight'])."'";
						$ok = $conn->Execute($sql);
							
						if($ok) {
							$sql = "update ".self::table('ms_unit')." set inforight = inforight+$a where inforight >= '".($rowp['inforight'])."'";
							$ok = $conn->Execute($sql);
						}
						
						// ubah unit
						if($ok) {
							// ambil lagi ah :D
							$sql = "select infoleft, inforight,level from ".self::table('ms_unit')." where idunit = $r_key";
							$rowc = $conn->GetRow($sql);
		
							$b = '('.((int)$rowp['inforight']-(int)$rowc['infoleft']).')';
							
							$sql = "update ".self::table('ms_unit')." set infoleft = infoleft+$b, inforight = inforight+$b,level = (level-".($rowc['level']-1).")+".$rowp['level']." where infoleft between '".($rowc['infoleft'])."' and '".($rowc['inforight'])."'";
							$ok = $conn->Execute($sql);
						}
						
						// tutup lubang
						if($ok) {
							$sql = "update ".self::table('ms_unit')." set infoleft = infoleft-$a where infoleft > '".($rowc['inforight'])."'";
							$ok = $conn->Execute($sql);
						}
						
						if($ok) {
							$sql = "update ".self::table('ms_unit')." set inforight = inforight-$a where inforight > '".($rowc['inforight'])."'";
							$ok = $conn->Execute($sql);
						}
					}
				}
			}
			
			return $record;
		}
		
		//hapus info unit
		function deleteInfoUnit($conn,$r_key){
			$right = $conn->GetOne("select inforight from ".self::table('ms_unit')." where idunit = $r_key");		
			if(!empty($right)) {
				$ok = $conn->Execute("update ".self::table('ms_unit')." set inforight = inforight - 2 where inforight >= $right");
				if($ok) 
				$ok = $conn->Execute("update ".self::table('ms_unit')." set infoleft = infoleft - 2 where infoleft > $right");
				else
					break;				
				if(!$ok)
					break;
			}	
			$err = $conn->ErrorNo();	
			return $err;
		}

		function deleteUnit($conn,$r_key)
		{
			$ok = $conn->Execute("delete from ".self::table('ms_unit')." where idunit = $r_key");
			$err = $conn->ErrorNo();	
			return $err;
		}
		
		//inforigth ketika insert tanpa parent
		function saveInfo($conn,$p_tabel,$p_parent,$p_key,$r_parent,$r_key){
			$r_parent = $r_parent == 'null' ? '' : $r_parent;
			
			if(empty($r_key)){//insert
				if(empty($r_parent)){
					$maxright = $conn->GetOne("select max(inforight) from ".self::table("$p_tabel")."");
					
					$record['infoleft'] = $maxright + 1;
					$record['inforight'] = $maxright + 2;
					$record['level'] = '0';
				}else{
					$r_parentright = $conn->GetOne("select inforight from ".self::table("$p_tabel")." where $p_key = '$r_parent'");
				
					if(!empty($r_parentright)){
						$ok = $conn->Execute("update ".self::table("$p_tabel")." set inforight = inforight+2 where inforight >= $r_parentright");
						if($ok)
							$ok = $conn->Execute("update ".self::table("$p_tabel")." set infoleft = infoleft+2 where infoleft > $r_parentright");
						else
							break;
							
						if($ok) {
							$record['infoleft'] = $r_parentright;
							$record['inforight'] = $r_parentright + 1;
							$record['level'] = $conn->GetOne("select coalesce(level,0)+1 as level from ".self::table("$p_tabel")." where $p_key = '$r_parent'");
						}else
							break;
					}
				}
			}else{//update
				if(!empty($r_parent)) {
					$p_cek = $conn->GetOne("select $p_parent from ".self::table("$p_tabel")." where $p_key = $r_key");
					if($p_cek != $r_parent){
						// cek unit
						$sql = "select infoleft, inforight from ".self::table("$p_tabel")." where $p_key = $r_key";
						$rowc = $conn->GetRow($sql);
						
						$sql = "select infoleft, inforight from ".self::table("$p_tabel")." where $p_key = '$r_parent'";
						$rowp = $conn->GetRow($sql);
						
						//if($rowp['infoleft'] > $rowc['infoleft'] and $rowp['inforight'] < $rowc['inforight'])
						//	die('Pemindahan tidak bisa dilakukan, unit parent baru merupakan child unit');
						
						$a = '('.((int)$rowc['inforight']-(int)$rowc['infoleft']+1).')';
						
						// siapkan tempat
						$sql = "update ".self::table("$p_tabel")." set infoleft = infoleft+$a where infoleft > '".($rowp['inforight'])."'";
						$ok = $conn->Execute($sql);
							
						if($ok) {
							$sql = "update ".self::table("$p_tabel")." set inforight = inforight+$a where inforight >= '".($rowp['inforight'])."'";
							$ok = $conn->Execute($sql);
						}
						
						// ubah unit
						if($ok) {
							// ambil lagi ah :D
							$sql = "select infoleft, inforight from ".self::table("$p_tabel")." where $p_key = $r_key";
							$rowc = $conn->GetRow($sql);
		
							$b = '('.((int)$rowp['inforight']-(int)$rowc['infoleft']).')';
							
							$sql = "update ".self::table("$p_tabel")." set infoleft = infoleft+$b, inforight = inforight+$b where infoleft between '".($rowc['infoleft'])."' and '".($rowc['inforight'])."'";
							$ok = $conn->Execute($sql);
						}
						
						// tutup lubang
						if($ok) {
							$sql = "update ".self::table("$p_tabel")." set infoleft = infoleft-$a where infoleft > '".($rowc['inforight'])."'";
							$ok = $conn->Execute($sql);
						}
						
						if($ok) {
							$sql = "update ".self::table("$p_tabel")." set inforight = inforight-$a where inforight > '".($rowc['inforight'])."'";
							$ok = $conn->Execute($sql);
						}
					}
				}
			}
			
			$err = $conn->ErrorNo();
			
			return array($record,$err);
		}
		
		//hapus info unit
		function deleteInfo($conn,$p_tabel,$p_key,$r_key){
			$right = $conn->GetOne("select inforight from ".self::table("$p_tabel")." where $p_key = '$r_key'");
			
			if(!empty($right)) {
				$ok = $conn->Execute("update ".self::table("$p_tabel")." set inforight = inforight - 2 where inforight >= $right");
				if($ok) 
					$ok = $conn->Execute("update ".self::table("$p_tabel")." set infoleft = infoleft - 2 where infoleft > $right");
				else
					break;
								
				if(!$ok)
					break;
			}		
				
			$err = $conn->ErrorNo();
				
			return $err;
		}

		
		//inforigth ketika insert tanpa parent
		function saveInfoLain($conn,$skema,$p_tabel,$p_key,$p_kodekey,$r_key,$p_parent,$p_kodeparent,$r_parent){
			$r_parent = $r_parent == 'null' ? '' : $r_parent;
			
			if(empty($r_key)){//insert
				if(empty($r_parent)){
					$maxright = $conn->GetOne("select max(inforight) from $skema"."."."$p_tabel");
					
					$record['infoleft'] = $maxright + 1;
					$record['inforight'] = $maxright + 2;
					$record['level'] = '0';
				}else{
					$r_parentright = $conn->GetRow("select inforight,$p_kodekey from $skema"."."."$p_tabel where $p_key = '$r_parent'");
				
					if(!empty($r_parentright)){
						$ok = $conn->Execute("update $skema"."."."$p_tabel set inforight = inforight+2 where inforight >= ".$r_parentright['inforight']);
						if($ok)
							$ok = $conn->Execute("update $skema"."."."$p_tabel set infoleft = infoleft+2 where infoleft > ".$r_parentright['inforight']);
						else
							break;
							
						if($ok) {
							$record['infoleft'] = $r_parentright['inforight'];
							$record['inforight'] = $r_parentright['inforight'] + 1;
							$record[$p_kodeparent] = $r_parentright[$p_kodekey];
							$record['level'] = $conn->GetOne("select coalesce(level,0)+1 as level from $skema"."."."$p_tabel where $p_key = '$r_parent'");
						}else
							break;
					}
				}
			}else{//update
				$record['level'] = $conn->GetOne("select level from $skema"."."."$p_tabel where $p_key = '$r_key'");
				if(!empty($r_parent)) {
					$p_cek = $conn->GetOne("select $p_parent from $skema"."."."$p_tabel where $p_key = '$r_key'");
					if($p_cek != $r_parent){
						// cek unit
						$sql = "select infoleft, inforight from $skema"."."."$p_tabel where $p_key = '$r_key'";
						$rowc = $conn->GetRow($sql);
						
						$sql = "select infoleft, inforight,level,$p_kodekey from $skema"."."."$p_tabel where $p_key = '$r_parent'";
						$rowp = $conn->GetRow($sql);

						//update kode parent
						$sql = "update $skema"."."."$p_tabel set $p_kodeparent = '".$rowp[$p_kodekey]."' where $p_key = '$r_key'";
						$ok = $conn->Execute($sql);

						$a = '('.((int)$rowc['inforight']-(int)$rowc['infoleft']+1).')';
						
						// siapkan tempat
						if($ok){
							$sql = "update $skema"."."."$p_tabel set infoleft = infoleft+$a where infoleft > '".($rowp['inforight'])."'";
							$ok = $conn->Execute($sql);
						}

						if($ok) {
							$sql = "update $skema"."."."$p_tabel set inforight = inforight+$a where inforight >= '".($rowp['inforight'])."'";
							$ok = $conn->Execute($sql);
						}
						
						// ubah unit
						if($ok) {
							// ambil lagi ah :D
							$sql = "select infoleft, inforight,level from $skema"."."."$p_tabel where $p_key = '$r_key'";
							$rowc = $conn->GetRow($sql);
		
							$b = '('.((int)$rowp['inforight']-(int)$rowc['infoleft']).')';
							
							$sql = "update $skema"."."."$p_tabel set infoleft = infoleft+$b, inforight = inforight+$b,level = (level-(".($rowc['level']-1)."))+".$rowp['level']." where infoleft between '".($rowc['infoleft'])."' and '".($rowc['inforight'])."'";
							$ok = $conn->Execute($sql);
						}
						
						// tutup lubang
						if($ok) {
							$sql = "update $skema"."."."$p_tabel set infoleft = infoleft-$a where infoleft > '".($rowc['inforight'])."'";
							$ok = $conn->Execute($sql);
						}
						
						if($ok) {
							$sql = "update $skema"."."."$p_tabel set inforight = inforight-$a where inforight > '".($rowc['inforight'])."'";
							$ok = $conn->Execute($sql);
						}
					}
				}
			}
			
			$err = $conn->ErrorNo();
			
			return array($record,$err);
		}
		
		//hapus info unit
		function deleteInfoLain($conn,$skema,$p_tabel,$p_key,$r_key){
			$right = $conn->GetOne("select inforight from $skema"."."."$p_tabel where $p_key = '$r_key'");
			
			if(!empty($right)) {
				$ok = $conn->Execute("update $skema"."."."$p_tabel set inforight = inforight - 2 where inforight >= $right");
				if($ok) 
					$ok = $conn->Execute("update $skema"."."."$p_tabel set infoleft = infoleft - 2 where infoleft > $right");
				else
					break;
								
				if(!$ok)
					break;
			}		
				
			$err = $conn->ErrorNo();
				
			return $err;
		}
		
		// pindah menu ke atas
		function moveUp($p_id,$r_id,$r_table) {
			global $conn;
			
			// cek elemen sebelumnya
			$sql = "select m.$p_id as fromid, m.infoleft as fromleft, m.inforight as fromright,
					s.$p_id as toid, s.infoleft as toleft, s.inforight as toright
					from ".static::schema.".$r_table m 
					join ".static::schema.".$r_table s on s.inforight = m.infoleft-1
					where m.$p_id = '".$r_id."'";
			$row = $conn->GetRow($sql);
			
			if(empty($row)) {
				$err = 'NOPREV';
				$msg = 'Item sudah berada paling atas';
			}
			
			if(!$err) {
				$t_selisih = $row['fromleft'] - $row['toleft'];
				$t_length = $row['fromright'] - $row['fromleft'] + 1;
				
				$sql = "update ".static::schema.".$r_table set infoleft = infoleft - $t_selisih, inforight = inforight - $t_selisih, t_updateact = 'wait'
						where infoleft >= '".$row['fromleft']."' and inforight <= '".$row['fromright']."';
						update ".static::schema.".$r_table set infoleft = infoleft + $t_length, inforight = inforight + $t_length
						where infoleft >= '".$row['toleft']."' and inforight <= '".$row['toright']."' and coalesce(t_updateact,'') <> 'wait';
						update ".static::schema.".$r_table set ".Query::logUpdate().", t_updateact = 'moveup'
						where infoleft >= '".$row['toleft']."' and inforight <= '".$row['fromright']."'";
				$conn->Execute($sql);
				$err = $conn->ErrorNo();
				
				if($err)
					$msg = 'Pemindahan item gagal';
				else
					$msg = 'Pemindahan item berhasil';
			}
			
			return array($err,$msg);
		}
		
		// pindah menu ke atas
		function moveUpLain($conn,$p_id,$r_id,$skema,$r_table) {			
			// cek elemen sebelumnya
			$sql = "select m.$p_id as fromid, m.infoleft as fromleft, m.inforight as fromright,
					s.$p_id as toid, s.infoleft as toleft, s.inforight as toright
					from $skema"."."."$r_table m 
					join $skema"."."."$r_table s on s.inforight = m.infoleft-1
					where m.$p_id = '".$r_id."'";
			$row = $conn->GetRow($sql);
			
			if(empty($row)) {
				$err = 'NOPREV';
				$msg = 'Item sudah berada paling atas';
			}
			
			if(!$err) {
				$t_selisih = $row['fromleft'] - $row['toleft'];
				$t_length = $row['fromright'] - $row['fromleft'] + 1;
				
				$sql = "update $skema"."."."$r_table set infoleft = infoleft - $t_selisih, inforight = inforight - $t_selisih, t_updateact = 'wait'
						where infoleft >= '".$row['fromleft']."' and inforight <= '".$row['fromright']."';
						update $skema"."."."$r_table set infoleft = infoleft + $t_length, inforight = inforight + $t_length
						where infoleft >= '".$row['toleft']."' and inforight <= '".$row['toright']."' and coalesce(t_updateact,'') <> 'wait';
						update $skema"."."."$r_table set ".Query::logUpdate().", t_updateact = 'moveup'
						where infoleft >= '".$row['toleft']."' and inforight <= '".$row['fromright']."'";
				$conn->Execute($sql);
				$err = $conn->ErrorNo();
				
				if($err)
					$msg = 'Pemindahan item gagal';
				else
					$msg = 'Pemindahan item berhasil';
			}
			
			return array($err,$msg);
		}
		
		// pindah menu ke atas
		function moveDown($p_id,$r_id,$r_table) {
			global $conn;
			
			// cek elemen sebelumnya
			$sql = "select m.$p_id as fromid, m.infoleft as fromleft, m.inforight as fromright,
					s.$p_id as toid, s.infoleft as toleft, s.inforight as toright
					from ".static::schema.".$r_table m 
					join ".static::schema.".$r_table s on s.infoleft = m.inforight+1
					where m.$p_id = '".$r_id."'";
			$row = $conn->GetRow($sql);
			
			if(empty($row)) {
				$err = 'NONEXT';
				$msg = 'Item sudah berada paling bawah';
			}
			
			if(!$err) {
				$t_selisih = $row['toleft'] - $row['fromleft'];
				$t_length = $row['toright'] - $row['toleft'] + 1;
				
				$sql = "update ".static::schema.".$r_table set infoleft = infoleft - $t_selisih, inforight = inforight - $t_selisih, t_updateact = 'wait'
						where infoleft >= '".$row['toleft']."' and inforight <= '".$row['toright']."';
						update ".static::schema.".$r_table set infoleft = infoleft + $t_length, inforight = inforight + $t_length
						where infoleft >= '".$row['fromleft']."' and inforight <= '".$row['fromright']."' and coalesce(t_updateact,'') <> 'wait';
						update ".static::schema.".$r_table set ".Query::logUpdate().", t_updateact = 'movedown'
						where infoleft >= '".$row['fromleft']."' and inforight <= '".$row['toright']."'";
				$conn->Execute($sql);
				$err = $conn->ErrorNo();
				
				if($err)
					$msg = 'Pemindahan item gagal';
				else
					$msg = 'Pemindahan item berhasil';
			}
			
			return array($err,$msg);
		}
		
		// pindah menu ke atas
		function moveDownLain($conn,$p_id,$r_id,$skema,$r_table) {			
			// cek elemen sebelumnya
			$sql = "select m.$p_id as fromid, m.infoleft as fromleft, m.inforight as fromright,
					s.$p_id as toid, s.infoleft as toleft, s.inforight as toright
					from $skema"."."."$r_table m 
					join $skema"."."."$r_table s on s.infoleft = m.inforight+1
					where m.$p_id = '".$r_id."'";
			$row = $conn->GetRow($sql);
			
			if(empty($row)) {
				$err = 'NONEXT';
				$msg = 'Item sudah berada paling bawah';
			}
			
			if(!$err) {
				$t_selisih = $row['toleft'] - $row['fromleft'];
				$t_length = $row['toright'] - $row['toleft'] + 1;
				
				$sql = "update $skema"."."."$r_table set infoleft = infoleft - $t_selisih, inforight = inforight - $t_selisih, t_updateact = 'wait'
						where infoleft >= '".$row['toleft']."' and inforight <= '".$row['toright']."';
						update $skema"."."."$r_table set infoleft = infoleft + $t_length, inforight = inforight + $t_length
						where infoleft >= '".$row['fromleft']."' and inforight <= '".$row['fromright']."' and coalesce(t_updateact,'') <> 'wait';
						update $skema"."."."$r_table set ".Query::logUpdate().", t_updateact = 'movedown'
						where infoleft >= '".$row['fromleft']."' and inforight <= '".$row['toright']."'";
				$conn->Execute($sql);
				$err = $conn->ErrorNo();
				
				if($err)
					$msg = 'Pemindahan item gagal';
				else
					$msg = 'Pemindahan item berhasil';
			}
			
			return array($err,$msg);
		}
		
		//mendapatkan into right
		function infoRight($conn,$kodeunit,$koderole,$userid){
			$sql = "select u.inforight from gate.sc_userrole ur
					left join gate.ms_unit u on u.kodeunit = ur.kodeunit
					where ur.kodeunit = '$kodeunit' and ur.koderole = '$koderole' and ur.userid = '$userid'";
			
			return $conn->GetOne($sql);
		}
		
		//list struktural
		function listStruktural() {
			$sql = "select m.*,mp.jabatanstruktural as jabatanparent,u.namaunit,e.namaeselon 
					from ".static::schema.".ms_struktural m
					left join ".static::schema.".ms_struktural mp on mp.idjstruktural=m.parentjstruktural
					left join ".static::schema.".ms_unit u on u.idunit=m.idunit
					left join ".static::schema.".ms_eselon e on e.kodeeselon=m.kodeeselon";
			
			return $sql;
		}
				
		function aEselon($conn) {
			$sql = "select kodeeselon, namaeselon from ".self::table('ms_eselon')." order by kodeeselon";
			
			return Query::arrQuery($conn,$sql);
		}
				
		function aJabatan($conn) {
			$sql = "select idjabatan, namajabatan from ".self::table('ms_jabatan')." order by idjabatan";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function aPangkat($conn) {
			$sql = "select idpangkat, '('||golongan||') ' || namapangkat from ".self::table('ms_pangkat')." order by idpangkat";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function isPimpinan() {
			$data = array('T' => 'Tidak', 'Y' => 'Ya');
			
			return $data;
		}
		
		//kategori parent
		function pStruktural($conn,$p_key) {
			$sql = "select idunit,isaktif from ".self::table('ms_struktural')." where idjstruktural = '$p_key'";
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		//passing level dan kode urutan struktural
		function levelStruktural($conn,$parent,$r_key=''){
			if($parent == 'null') {
				$parent = '';
				$record['level'] = 0;
							
				$g_max = $conn->GetOne("select max(cast(kodeurutan as int)) from ".self::table('ms_struktural')." where level = 0 or level is null");
				if($g_max == '')
					$kodeurutan = '1';
				else
					$kodeurutan = $g_max+1;
			}
			else {
				$g_parent = $conn->GetRow("select coalesce(level,0)+1 as nlevel, kodeurutan from ".self::table('ms_struktural')." where idjstruktural = '".$parent."'");
				$record['level'] = $g_parent['nlevel'];
				
				$g_max = $conn->GetOne("select max(cast(kodeurutan as int)) from ".self::table('ms_struktural')." where parentjstruktural = '".$parent."'");
				if($g_max == '')
					$kodeurutan = $g_parent['kodeurutan'].'01';
				else {
					$lnum = substr($g_max,-2);
					$lnum++;
					if(strlen($lnum) == 1)
						$lnum = str_pad($lnum,2,'0',STR_PAD_LEFT);
					$kodeurutan = substr($g_max,0,strlen($g_max)-2).$lnum;
				}
			}
							
			if(!empty($r_key)){
				$g_struktural = $conn->GetRow("select kodeurutan,parentjstruktural from ".self::table('ms_struktural')." where idjstruktural = '$r_key'");
				if(empty($g_struktural['kodeurutan']) or $parent != $g_struktural['parentjstruktural'])
					$record['kodeurutan'] = $kodeurutan;
			}else{
				$record['kodeurutan'] = $kodeurutan;
			}
			
			return $record;
		}
		
		//list jabatan
		function listJabatan() {
			$sql = "select m.*,e.namaeselon,'('||p.golongan||') ' || p.namapangkat as namapangkat
					from ".static::schema.".ms_jabatan m
					left join ".static::schema.".ms_eselon e on e.kodeeselon=m.kodeeselon
					left join ".static::schema.".ms_pangkat p on p.idpangkat=m.pangkatmin";
			
			return $sql;
		}
		
		function Jabatan($conn) {
			$sql = "select idjabatan, namajabatan from sdm.ms_jabatan order by idjabatan";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once($conf['gate_dir'].'model/m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
					
					break;
				case 'idtipepeg': 
					return "idtipepeg = '$key'";
					break;
					
				case 'idjenispegawai': 
					return "idjenispegawai = '$key'";
					
					break;
			}
		}
		
		// status keluar
		function statusKeluar() {
			$data = array('T' => 'Aktif', 'Y' => 'Keluar');
			
			return $data;
		}
		
		//list rumpun bidang dosen
		function listBidang() {
			$sql = "select m.*,mp.namabidang as parent
					from ".static::schema.".ms_bidang m
					left join ".static::schema.".ms_bidang mp on mp.kodebidang=m.parentbidang";
			
			return $sql;
		}
		
		//passing level dan kode urutan bidang
		function levelBidang($conn,$parent,$r_key=''){
			if($parent == 'null') {
				$parent = '';
				$record['level'] = 0;
							
				$g_max = $conn->GetOne("select max(cast(kodeurutan as int)) from ".self::table('ms_bidang')." where level = 0 or level is null");
				if($g_max == '')
					$kodeurutan = '1';
				else
					$kodeurutan = $g_max+1;
			}
			else {
				$g_parent = $conn->GetRow("select coalesce(level,0)+1 as nlevel, kodeurutan from ".self::table('ms_bidang')." where kodebidang = '".$parent."'");
				$record['level'] = $g_parent['nlevel'];
				
				$g_max = $conn->GetOne("select max(cast(kodeurutan as int)) from ".self::table('ms_bidang')." where parentbidang = '".$parent."'");
				if($g_max == '')
					$kodeurutan = $g_parent['kodeurutan'].'01';
				else {
					$lnum = substr($g_max,-2);
					$lnum++;
					if(strlen($lnum) == 1)
						$lnum = str_pad($lnum,2,'0',STR_PAD_LEFT);
					$kodeurutan = substr($g_max,0,strlen($g_max)-2).$lnum;
				}
			}
							
			if(!empty($r_key)){
				$g_struktural = $conn->GetRow("select kodeurutan,parentbidang from ".self::table('ms_bidang')." where kodebidang = '$r_key'");
				if(empty($g_struktural['kodeurutan']) or $parent != $g_struktural['parentbidang'])
					$record['kodeurutan'] = $kodeurutan;
			}else{
				$record['kodeurutan'] = $kodeurutan;
			}
			
			return $record;
		}
		
		/***********************************************L A P O R A N******************************************/
		function repUnitKerja($conn){
			$sql = "select u.*,u.namaunit as parentunit
					from ".self::table('ms_unit')." u
					left join ".self::table('ms_unit')." pu on pu.idunit = u.parentunit
					order by u.infoleft";
			$rs = $conn->Execute($sql);
			
			return $rs;
		}
		
		function repStruktural($conn){
			$sql = "select s.*,ps.jabatanstruktural as parentjabatan,u.namaunit,e.namaeselon,g1.golongan as golonganmin,g2.golongan as golonganmax
					from ".self::table('ms_struktural')." s
					left join ".self::table('ms_struktural')." ps on ps.idjstruktural = s.parentjstruktural
					left join ".self::table('ms_unit')." u on u.idunit = s.idunit
					left join ".self::table('ms_eselon')." e on e.kodeeselon = s.kodeeselon
					left join ".self::table('ms_pangkat')." g1 on g1.idpangkat = s.pangkatmin
					left join ".self::table('ms_pangkat')." g2 on g2.idpangkat = s.pangkatmax
					order by s.kodeurutan";
			$rs = $conn->Execute($sql);
			
			return $rs;
		}
		
		function repJabatanStruktural($conn){
			$sql = "select m.*,s.namajabatan+' '+u.namaunit as jabatanstruktural,sp.namajabatan+' '+up.namaunit as jabatanstrukturalparent,u.namaunit,e.namaeselon,s.level,
					g1.golongan as golonganmin,g2.golongan as golonganmax
					from ".static::schema.".ms_jabatanstruktural m
					left join ".static::schema.".ms_jabatan s on s.idjabatan=m.idjabatan
					left join ".static::schema.".ms_eselon e on e.kodeeselon=s.kodeeselon
					left join ".self::table('ms_pangkat')." g1 on g1.idpangkat = s.pangkatmin
					left join ".self::table('ms_pangkat')." g2 on g2.idpangkat = s.pangkatmax
					left join ".static::schema.".ms_unit u on u.idunit=m.idunit
					left join ".static::schema.".ms_jabatan sp on sp.idjabatan=(select idjabatan from ".static::schema.".ms_jabatanstruktural where idjabatanstruktural=m.parentidjabatanstruktural)
					left join ".static::schema.".ms_unit up on up.idunit=(select idunit from ".static::schema.".ms_jabatanstruktural where idjabatanstruktural=m.parentidjabatanstruktural)
					order by m.idjabatan";
			$rs = $conn->Execute($sql);
			
			return $rs;
		}
	}
?>
