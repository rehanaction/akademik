<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPesan extends mModel {
		const schema = 'elearning';
		const table = 'el_mail';
		const sequence = 'el_mail_idmail_seq';
		const order = 'waktukirim desc';
		const key = 'idmail';
		const label = 'pesan';
		
		// baca data
		function setBaca($conn,$key) {
			$record = array();
			$record['flagbaca'] = -1;
			$record['t_updateact'] = 'baca';
			
			if(is_array($key)) {
				foreach($key as $tkey) {
					list($err,$msg) = static::updateRecord($conn,$record,$tkey,true);
					if($err) break;
				}
				
				return array($err,$msg);
			}
			else
				return static::updateRecord($conn,$record,$key,true);
		}
		
		// belum baca data
		function setBelumBaca($conn,$key) {
			$record = array();
			$record['flagbaca'] = 0;
			$record['t_updateact'] = 'belumbaca';
			
			if(is_array($key)) {
				foreach($key as $tkey) {
					list($err,$msg) = static::updateRecord($conn,$record,$tkey,true);
					if($err) break;
				}
				
				return array($err,$msg);
			}
			else
				return static::updateRecord($conn,$record,$key,true);
		}
		
		// hapus data
		function delete($conn,$key,$from) {
			$record = array();
			if($from == 'inbox') {
				$record['flaghapuspenerima'] = -1;
				$record['t_updateact'] = 'hapusinbox';
			}
			else {
				$record['flaghapuspengirim'] = -1;
				$record['t_updateact'] = 'hapusoutbox';
			}
			
			if(is_array($key)) {
				foreach($key as $tkey) {
					list($err,$msg) = static::updateRecord($conn,$record,$tkey,true);
					if($err) break;
				}
				
				return array($err,$msg);
			}
			else
				return static::updateRecord($conn,$record,$key,true);
		}
		
		// mendapatkan kueri data
		function dataQuery($key) {
			$sql = "select p.*, uk.userdesc as namapengirim, ut.userdesc as namapenerima
					from ".static::table()." p
					left join gate.sc_user uk on p.idpengirim = uk.username
					left join gate.sc_user ut on p.idpenerima = ut.username
					where ".static::getCondition($key);
			
			return $sql;
		}
		
		// mendapatkan jumlah pesan belum dibaca
		function getNumUnread($conn,$user='') {
			if(empty($user))
				$user = Modul::getUserName();
			
			$sql = "select count(*) from ".static::table()."
					where idpenerima = '$user' and coalesce(flagbaca,0) = 0";
			
			return $conn->GetOne($sql);
		}
		
		// mendapatkan data inbox
		function getListInbox($conn,&$page) {
			global $conf;
			
			$row = $conf['row_pesan'];
			
			$sql = "select p.*, u.userdesc from ".static::table()." p
					left join gate.sc_user u on p.idpengirim = u.username
					where idpenerima = '".Modul::getUserName()."' and coalesce(flaghapuspenerima,0) = 0
					order by ".static::order;
			
			// halaman terakhir
			if($page == -1) {
				$sqlc = "select count(*) from (".$sql.") a";
				$rownum = $conn->GetOne($sqlc);
				
				$page = ceil($rownum/$row);
			}
			
			$offset = $row*($page-1);
			
			$sql .= " limit ".($row+1)." offset $offset";
			$data = $conn->GetArray($sql);
			
			if(empty($data[$row]))
				$t_lastpage = true;
			else
				$t_lastpage = false;
			
			Page::setLastPage($t_lastpage);
			
			return array_slice($data,0,$row);
		}
		
		// mendapatkan data outbox
		function getListOutbox($conn,&$page) {
			global $conf;
			
			$row = $conf['row_pesan'];
			
			$sql = "select p.*, u.userdesc from ".static::table()." p
					left join gate.sc_user u on p.idpenerima = u.username
					where idpengirim = '".Modul::getUserName()."' and coalesce(flaghapuspengirim,0) = 0
					order by ".static::order;
			
			// halaman terakhir
			if($page == -1) {
				$sqlc = "select count(*) from (".$sql.") a";
				$rownum = $conn->GetOne($sqlc);
				
				$page = ceil($rownum/$row);
			}
			
			$offset = $row*($page-1);
			
			$sql .= " limit ".($row+1)." offset $offset";
			$data = $conn->GetArray($sql);
			
			if(empty($data[$row]))
				$t_lastpage = true;
			else
				$t_lastpage = false;
			
			Page::setLastPage($t_lastpage);
			
			return array_slice($data,0,$row);
		}
	}
?>