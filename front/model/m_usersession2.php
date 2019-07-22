<?php
	// model user

	require_once('m_model2.php');
	
	class mUserSession2 extends mModel2 {
		const schema = 'gate';
		const table = 'sc_usersession';
		const sequence = 'sc_usersession_sessionid_seq';
		const order = 't_logintime desc';
		const key = 'sessionid';
		const label = 'user login';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select distinct s.* from ".static::table()." s
					left join gate.sc_user u on u.username = s.t_userid
					left join gate.sc_userrole ur on ur.userid = u.userid";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'role': return "ur.koderole = '$key'";
			}
		}
		
		// mendapatkan data untuk grafik home
		function getHomeGraphData($conn) {
			$hari = 14-1;
			$ivhari = 60*60*24;
			$time = time()-($ivhari*$hari);
			
			$a_browser = array('MSIE','Firefox','Chrome','Opera');
			
			$sql = "select t_logintime::timestamp as time, t_osname from ".static::table()."
					where t_logintime::date >= current_date - $hari";
			$rs = $conn->Execute($sql);
			
			$n_data = 0;
			$a_dlog = array();
			$a_dwaktu = array();
			$a_dweb = array();
			while($row = $rs->FetchRow()) {
				$n_data++;
				
				// hari login
				$t_idx = substr($row['time'],0,10);
				$a_dlog[$t_idx]++;
				
				// waktu login
				$t_idx = (int)substr($row['time'],11,2);
				$a_dwaktu[$t_idx]++;
				
				// browser login
				$found = false;
				foreach($a_browser as $t_browser) {
					if(($pos = strpos($row['t_osname'],$t_browser)) !== false) {
						$t_sub = substr($row['t_osname'],$pos+strlen($t_browser)+1);
						$t_dot = strpos($t_sub,'.');
						if($t_dot !== false)
							$t_sub = substr($t_sub,0,$t_dot);
						
						$a_dweb[$t_browser][$t_sub]++;
						$found = true;
						break;
					}
				}
				if(!$found)
					$a_dweb['Lainnya']['']++;
			}
			
			// grafik login
			$wtime = $time;
			$a_glog = array();
			for($i=0;$i<=$hari;$i++) {
				$wdate = date('Y-m-d',$wtime);
				$a_glog[] = (int)$a_dlog[$wdate];
				$wtime += $ivhari;
			}
			
			$time = date('Y',$time).', '.(date('m',$time)-1).', '.date('d',$time);
			$a_glog = implode(', ',$a_glog);
			
			// grafik jam
			$a_jam = array();
			$a_jam['06-10'] = array('mulai' => 6, 'selesai' => 10);
			$a_jam['10-14'] = array('mulai' => 10, 'selesai' => 14);
			$a_jam['14-18'] = array('mulai' => 14, 'selesai' => 18);
			$a_jam['18-22'] = array('mulai' => 18, 'selesai' => 22);
			
			$a_gwaktu = array();
			foreach($a_jam as $t_idx => $t_jamcek)
				$a_gwaktu[$t_idx] = 0;
			
			$a_lain = array();
			foreach($a_dwaktu as $t_jam => $t_jumlah) {
				$found = false;
				foreach($a_jam as $t_idx => $t_jamcek) {
					if($t_jam >= $t_jamcek['mulai'] and $t_jam < $t_jamcek['selesai']) {
						$a_gwaktu[$t_idx]++;
						$found = true;
						break;
					}
				}
				if(!$found)
					$a_gwaktu['Lainnya']++;
			}
			
			$a_gfwaktu = array();
			foreach($a_gwaktu as $t_idx => $t_jumlah)
				$a_gfwaktu[] = "['$t_idx',$t_jumlah]";
			$a_gwaktu = implode(', ',$a_gfwaktu);
			
			// grafik browser
			$a_gweb = array();
			foreach($a_dweb as $t_browser => $t_dweb) {
				$t_total = 0;
				$t_kategori = array();
				$t_data = array();
				foreach($t_dweb as $t_major => $t_jumlah) {
					$t_kategori[] = "'".$t_browser.' '.$t_major."'";
					$t_data[] = round($t_jumlah*100/$n_data,2);
					
					$t_total += $t_jumlah;
				}
				
				$a_gweb['browser'][] = "'".$t_browser."'";
				$a_gweb['detail'][] = array('y' => round($t_total*100/$n_data,2), 'browser' => $t_browser, 'kategori' => implode(', ',$t_kategori), 'data' => implode(', ',$t_data));
			}
			$a_gweb['browser'] = implode(', ',$a_gweb['browser']);
			
			return array('mulai' => $time, 'login' => $a_glog, 'waktu' => $a_gwaktu, 'web' => $a_gweb);
		}
	}
?>