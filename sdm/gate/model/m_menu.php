<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once($conf['model_dir'].'m_model.php');
	
	class mMenu extends mModel {
		const schema = 'gate';
		const table = 'sc_menu';
		const sequence = 'sc_menu_idmenu_seq';
		const order = 'kodeurutan';
		const key = 'idmenu';
		const label = 'menu';
		
		// hapus data
		function delete($conn,$key) {
			$conn->BeginTrans();
			
			// atur left right dan urutan
			$row = self::getData($conn,$key);
			
			$err = static::deleteLeaf($conn,$row['infoleft'],$row['kodeurutan'],$row['levelmenu']);
			if(!$err)
				$err = Query::qDelete($conn,static::table('sc_menurole'),static::getCondition($key));
			if(!$err)
				$err = Query::qDelete($conn,static::table('sc_menufile'),static::getCondition($key));
			if(!$err)
				$err = Query::qDelete($conn,static::table('sc_menuakses'),static::getCondition($key));
			if(!$err)
				$err = Query::qDelete($conn,static::table(),static::getCondition($key));
			
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
			
			return static::deleteStatus($conn);
		}
		
		// delete leaf ($left dari yang didelete)
		function deleteLeaf($conn,$left,$urutan,$level) {
			$sql = "update ".static::table()." set infoleft = infoleft-2 where infoleft > '$left';
					update ".static::table()." set inforight = inforight-2 where inforight > '$left'";
			$ok = $conn->Execute($sql);
			
			if($ok) {
				$step = $level*2;
				$like = substr($urutan,0,$step).'%';
				
				$sql = "update ".static::table()." set kodeurutan = substring(kodeurutan,1,$step) + 
						right(replicate('0',2) + cast((cast(substring(kodeurutan,$step+1,2) as int)-1) as varchar(10)),2) +
						substring(kodeurutan,$step+3,2)
						where kodeurutan like '$like' and kodeurutan > '$urutan'";
				$ok = $conn->Execute($sql);
			}
			
			return $conn->ErrorNo();
		}
		
		// insert record
		function insertRecord($conn,$record,$status=false) {
			if($record['parentmenu'] != 'null') {
				$a_parent = $conn->GetRow("select levelmenu, inforight, kodeurutan from ".static::table()." where idmenu = '".$record['parentmenu']."'");
				$t_urutan = $conn->GetOne("select max(kodeurutan) from ".static::table()." where parentmenu = '".$record['parentmenu']."'");
				
				$t_urutan = str_pad(substr($a_parent['kodeurutan'],0,($a_parent['levelmenu']+1)*2).str_pad((int)substr($t_urutan,($a_parent['levelmenu']+1)*2,2)+1,2,'0',STR_PAD_LEFT),6,'0',STR_PAD_RIGHT);
			}
			else {
				$a_parent = $conn->GetRow("select -1 as levelmenu, coalesce(max(inforight),0)+1 as inforight from ".static::table());
				$t_urutan = $conn->GetOne("select max(kodeurutan) from ".static::table());
				
				$t_urutan = str_pad((int)substr($t_urutan,0,2)+1,2,'0',STR_PAD_LEFT).'0000';
			}
			
			$record['levelmenu'] = $a_parent['levelmenu'] + 1;
			$record['infoleft'] = $a_parent['inforight'];
			$record['inforight'] = $record['infoleft']+1;
			$record['kodeurutan'] = $t_urutan;
			
			$err = static::insertLeaf($conn,$a_parent['inforight']);
			if(!$err) {
				$err = Query::recInsert($conn,$record,static::table());
				if(!$err) {
					$seq = static::sequence;
					if(empty($seq))
						$key = static::getRecordKey($key,$record);
					else
						$key = static::getLastValue($conn);
				}
			}
			
			if($status)
				return static::insertStatus($conn);
			else
				return $err;
		}
		
		// insert child leaf ($right dari parent)
		function insertLeaf($conn,$right) {
			$sql = "update ".static::table()." set infoleft = infoleft+2 where infoleft > '$right';
					update ".static::table()." set inforight = inforight+2 where inforight >= '$right'";
			$conn->Execute($sql);
			
			return $conn->ErrorNo();
		}
		
		// pindah menu ke atas
		function moveUp($idmenu) {
			global $conn;
			
			// cek elemen sebelumnya
			$sql = "select m.idmenu as fromid, m.infoleft as fromleft, m.inforight as fromright,
					s.idmenu as toid, s.infoleft as toleft, s.inforight as toright
					from ".static::table()." m join ".static::table()." s on s.inforight = m.infoleft-1
					where m.idmenu = '".(int)$idmenu."'";
			$row = $conn->GetRow($sql);
			
			if(empty($row)) {
				$err = 'NOPREV';
				$msg = 'Item menu sudah berada paling atas';
			}
			
			if(!$err) {
				$t_selisih = $row['fromleft'] - $row['toleft'];
				$t_length = $row['fromright'] - $row['fromleft'] + 1;
				
				$sql = "update ".static::table()." set infoleft = infoleft - $t_selisih, inforight = inforight - $t_selisih, t_updateact = 'wait'
						where infoleft >= '".$row['fromleft']."' and inforight <= '".$row['fromright']."';
						update ".static::table()." set infoleft = infoleft + $t_length, inforight = inforight + $t_length
						where infoleft >= '".$row['toleft']."' and inforight <= '".$row['toright']."' and coalesce(t_updateact,'') <> 'wait';
						update ".static::table()." set ".Query::logUpdate().", t_updateact = 'moveup'
						where infoleft >= '".$row['toleft']."' and inforight <= '".$row['fromright']."'";
				$conn->Execute($sql);
				$err = $conn->ErrorNo();
				
				if($err)
					$msg = 'Pemindahan item menu gagal';
				else
					$msg = 'Pemindahan item menu berhasil';
			}
			
			return array($err,$msg);
		}
		
		// pindah menu ke atas
		function moveDown($idmenu) {
			global $conn;
			
			// cek elemen sebelumnya
			$sql = "select m.idmenu as fromid, m.infoleft as fromleft, m.inforight as fromright,
					s.idmenu as toid, s.infoleft as toleft, s.inforight as toright
					from ".static::table()." m join ".static::table()." s on s.infoleft = m.inforight+1
					where m.idmenu = '".(int)$idmenu."'";
			$row = $conn->GetRow($sql);
			
			if(empty($row)) {
				$err = 'NONEXT';
				$msg = 'Item menu sudah berada paling bawah';
			}
			
			if(!$err) {
				$t_selisih = $row['toleft'] - $row['fromleft'];
				$t_length = $row['toright'] - $row['toleft'] + 1;
				
				$sql = "update ".static::table()." set infoleft = infoleft - $t_selisih, inforight = inforight - $t_selisih, t_updateact = 'wait'
						where infoleft >= '".$row['toleft']."' and inforight <= '".$row['toright']."';
						update ".static::table()." set infoleft = infoleft + $t_length, inforight = inforight + $t_length
						where infoleft >= '".$row['fromleft']."' and inforight <= '".$row['fromright']."' and coalesce(t_updateact,'') <> 'wait';
						update ".static::table()." set ".Query::logUpdate().", t_updateact = 'movedown'
						where infoleft >= '".$row['fromleft']."' and inforight <= '".$row['toright']."'";
				$conn->Execute($sql);
				$err = $conn->ErrorNo();
				
				if($err)
					$msg = 'Pemindahan item menu gagal';
				else
					$msg = 'Pemindahan item menu berhasil';
			}
			
			return array($err,$msg);
		}
		
		// mendapatkan array menu
		function getArrMenu($conn,$modul,$role='') {
			if(empty($role)) {
				$sql = "select * from ".self::table()." where kodemodul = '$modul' order by infoleft";
				$rs = $conn->Execute($sql);
			
				$a_menu = array();
				while($row = $rs->FetchRow()) {
					if(!empty($row['urladd'])) {
						$row['namafile'] .= '&'.$row['urladd'];
						unset($row['urladd']);
					}
					
					$a_menu[] = $row;
				}
			}
			else {
				$sql = "select m.namamenu, m.namafile, m.urladd, m.levelmenu, m.infoleft, m.inforight,
						r.idmenu, r.caninsert, r.canupdate, r.candelete, r.aksesmenu from ".self::table()." m
						left join gate.sc_menurole r on r.idmenu = m.idmenu and r.koderole = '$role'
						where m.kodemodul = '$modul' order by m.infoleft";
				$rs = $conn->Execute($sql);
//print_r($conn->getArray($sql));
//echo $conn->errormsg();
				
				$a_range = array();
				$a_tempmenu = array();
				$a_akses = array();
				while($row = $rs->FetchRow()) {
					if(!empty($row['idmenu']))
						$row['akses'] = (int)$row['caninsert'].(int)$row['canupdate'].(int)$row['candelete'].$row['aksesmenu'];
					unset($row['caninsert'],$row['canupdate'],$row['candelete'],$row['aksesmenu']);
					
					if(!empty($row['urladd'])) {
						$row['namafile'] .= '&'.$row['urladd'];
						unset($row['urladd']);
					}
					
					$a_tempmenu[] = $row;
					
					if(!empty($row['idmenu']))
						$a_range[] = array($row['infoleft'],$row['inforight']);
				}

				$a_menu = array();
				foreach($a_tempmenu as $row) {
					if(empty($row['idmenu'])) {
						$t_inc = false;
						foreach($a_range as $t_range) {
							if($row['infoleft'] < $t_range[0] and $row['inforight'] > $t_range[1]) {
								$t_inc = true;
								break;
							}
						}
					}
					else
						$t_inc = true;
					
					if($t_inc) {
						unset($row['infoleft'],$row['inforight'],$row['idmenu']);
						
						$a_menu[] = $row;
					}
				}
			}
			
			return $a_menu;
		}
		
		// akses role
		function getAksesRole($conn,$modul,$role) {
			$sql = "select m.idmenu, mr.caninsert, mr.canupdate, mr.candelete, mr.aksesmenu
					from ".static::table('sc_menurole')." mr
					join ".static::table()." m on mr.idmenu = m.idmenu and m.kodemodul = '$modul'
					where mr.koderole = '$role'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()) {
				// asal ada datanya, read
				$t_data = array('read' => true);
				
				if(!empty($row['caninsert'])) $t_data['insert'] = true;
				if(!empty($row['canupdate'])) $t_data['update'] = true;
				if(!empty($row['candelete'])) $t_data['delete'] = true;
				
				$a_akses = str_split($row['aksesmenu']);
				foreach($a_akses as $t_akses) {
					if(!empty($t_akses))
						$t_data[$t_akses] = true;
				}
				
				$a_data[$row['idmenu']] = $t_data;
			}
			
			return $a_data;
		}
		
		// simpan akses role
		function saveAksesRole($conn,$role,$akses) {
			$table = 'sc_menurole';
			
			$conn->BeginTrans();
			
			// proses internal
			$err = 0;
			$a_hapus = array();
			foreach($akses as $idmenu => $aksesid) {
				$record = array();
				if(!empty($aksesid['I']))
					foreach($aksesid['I'] as $t_akses => $t_val)
						$record['can'.$t_akses] = (int)$t_val;
				
				if(!empty($record) or !empty($aksesid['E'])) {
					// ambil data dulu
					Query::setLogColumn($record);
					
					$col = $conn->Execute("select * from ".static::table($table)." where idmenu = '$idmenu' and koderole = '$role'");
					if(!empty($aksesid['E'])) {
						if(empty($col->fields['aksesmenu']))
							$a_hakcek = array();
						else
							$a_hakcek = str_split($col->fields['aksesmenu']);
						
						foreach($aksesid['E'] as $t_akses => $t_val) {
							if(empty($t_val)) {
								$t_idx = array_search($t_akses,$a_hakcek);
								if($t_idx !== false)
									unset($a_hakcek[$t_idx]);
							}
							else
								$a_hakcek[] = $t_akses;
						}
						
						$record['aksesmenu'] = CStr::cStrNull(implode('',$a_hakcek));
					}
					
					if($col->EOF) {
						$record['koderole'] = $role;
						$record['idmenu'] = $idmenu;
						
						$sql = $conn->GetInsertSQL($col,$record);
						$conn->Execute($sql);
						
						list($err,$msg) = static::insertStatus($conn,'','hak akses menu','sc_menurole');
					}
					else {
						$sql = $conn->GetUpdateSQL($col,$record);
						if($sql != '')
							$conn->Execute($sql);
						
						list($err,$msg) = static::updateStatus($conn,'','hak akses menu','sc_menurole');
						
						if(!$err and $record['canread'] == 'null')
							$a_hapus[] = $idmenu;
					}
					if($err) break;
				}
			}
			
			// hapus akses role
			if(!$err and !empty($a_hapus)) {
				$cond = "koderole = '$role' and idmenu in (".implode(',',$a_hapus).")
						and coalesce(caninsert,0) = 0 and coalesce(canupdate,0) = 0
						and coalesce(candelete,0) = 0 and coalesce(aksesmenu,'') = ''";
				
				Query::qDelete($conn,static::table($table),$cond);
				list($err,$msg) = static::deleteStatus($conn);
			}
			
			$ok = ($err ? false : true);
			$conn->CommitTrans($ok);
						
			if($err)
				$msg = 'Penyimpanan hak akses menu gagal';
			else
				$msg = 'Penyimpanan hak akses menu berhasil';
			
			return array($err,$msg);
		}
		
		// akses kode
		function getAksesKode($conn,$modul) {
			$sql = "select ma.idmenu, ma.kodeakses, ma.namaakses
					from ".static::table('sc_menuakses')." ma
					join ".static::table()." m on ma.idmenu = m.idmenu and m.kodemodul = '$modul'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[$row['idmenu']][$row['kodeakses']] = $row['namaakses'];
			
			return $a_data;
		}
		
		function getAksesKodeMenu($conn,$idmenu) {
			$sql = "select kodeakses, namaakses from ".static::table('sc_menuakses')." where idmenu = '$idmenu'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[$row['kodeakses']] = $row['namaakses'];
			
			return $a_data;
		}
		
		// simpan akses tambahan menu
		function saveAksesKode($conn,$idmenu,$akses) {
			$table = 'sc_menuakses';
			
			Query::qDelete($conn,static::table($table),static::getCondition($idmenu));
			list($err,$msg) = static::deleteStatus($conn);
			
			if(!$err) {
				$record = array();
				$record['idmenu'] = $idmenu;
				
				foreach($akses as $kode => $nama) {
					$record['kodeakses'] = $kode;
					$record['namaakses'] = CStr::cStrNull($nama);
					
					Query::recInsert($conn,$record,static::table($table));
					list($err,$msg) = static::insertStatus($conn,'','akses tambahan',$table);
					
					if($err) break;
				}
			}
			
			return array($err,$msg);
		}
		
		// simpan file menu
		function saveFile($conn,$idmenu,$files) {
			$table = 'sc_menufile';
			
			Query::qDelete($conn,static::table($table),static::getCondition($idmenu));
			list($err,$msg) = static::deleteStatus($conn);
			
			if(!$err) {
				$record = array();
				$record['idmenu'] = $idmenu;
				
				foreach($files as $file) {
					$record['filemenu'] = $file;
					
					Query::recInsert($conn,$record,static::table($table));
					list($err,$msg) = static::insertStatus($conn,'','file menu',$table);
					
					if($err) break;
				}
			}
			
			return array($err,$msg);
		}
		
		// mendapatkan file menu
		function getFile($conn,$idmenu) {
			$sql = "select filemenu from ".static::table('sc_menufile')." where idmenu = '$idmenu' order by filemenu";
			
			return $conn->GetArray($sql);
		}
		
		// hak akses dasar
		function aksesMenu() {
			$data = array('read' => 'Read', 'insert' => 'Insert', 'update' => 'Update', 'delete' => 'Delete');
			
			return $data;
		}
	}
?>
