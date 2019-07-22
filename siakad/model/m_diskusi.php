<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('forum'));
	
	class mDiskusi extends mForum {
		const schema = 'elearning';
		const table = 'el_forum';
		const sequence = 'el_forum_idforum_seq';
		const order = 'idforum';
		const key = 'idforum';
		const label = 'diskusi';
		const uptype = 'diskusi';
		
		// masukkan detail
		function insertRecordDetail($conn,$record,$status=false) {
			$err = Query::recInsert($conn,$record,static::table('el_forumdetail'));
			
			if($status)
				return static::insertStatus($conn);
			else
				return $err;
		}
		
		// mendapatkan kueri data
		function dataQuery($key) {
			$sql = "select f.*, u.userdesc from ".static::table()." f
					left join gate.sc_user u on f.creator = u.username
					where ".static::getCondition($key);
			
			return $sql;
		}
		
		// daftar diskusi terbaru
		function getListTerbaru($conn,$periode='',$row=5) {
			$user = Modul::getUserName();
			if(empty($periode))
				$periode = Akademik::getPeriode();
			
			if(Akademik::isMhs()) {
				$sql = "select f.idforum, f.judulforum, max(d.waktuposting::text||'|'||d.creator) from ".static::table()." f
						join akademik.ak_krs k using (thnkurikulum,kodemk,kodeunit,periode,kelasmk)
						join ".static::table('el_forumdetail')." d on d.idforum = f.idforum
						where f.periode = '$periode' and k.nim = '$user' group by f.idforum, f.judulforum";
			}
			else if(Akademik::isDosen()) {
				$sql = "select f.idforum, f.judulforum, max(d.waktuposting::text||'|'||d.creator) from ".static::table()." f
						join akademik.ak_mengajar a using (thnkurikulum,kodemk,kodeunit,periode,kelasmk)
						join ".static::table('el_forumdetail')." d on d.idforum = f.idforum
						where f.periode = '$periode' and a.nipdosen = '$user' group by f.idforum, f.judulforum";
			}
			else {
				$sql = "select f.idforum, f.judulforum, max(d.waktuposting::text||'|'||d.creator) from ".static::table()." f
						join ".static::table('el_forumdetail')." d on d.idforum = f.idforum
						where f.periode = '$periode' group by f.idforum, f.judulforum";
			}
			$rs = $conn->SelectLimit($sql,$row);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[] = $row;
			
			return $data;
		}
		
		// daftar topik
		function getListTopik($conn,$jumlah=true,$kelas='') {
			if($jumlah) {
				$sql = "select t.idtopik, t.parenttopik, t.topik, count(f.idforum) as jumlah,
						max(f.waktuposting::text||'|'||f.idforum||'|'||f.judulforum) as label
						from ".static::table('lv_topikforum')." t
						left join ".static::table()." f on f.idtopik = t.idtopik";
				
				if($kelas != '')
					$sql .= " and ".mKelas::getCondition($kelas);
				
				$sql .= " group by t.idtopik, t.parenttopik, t.topik
						order by coalesce(t.parenttopik,''), t.idtopik";
			}
			else {
				$sql = "select idtopik, parenttopik, topik from ".static::table('lv_topikforum')."
						order by coalesce(parenttopik,''), idtopik";
			}
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()) {
				if(empty($row['parenttopik'])) {
					$row['child'] = array();
					
					$a_data[$row['idtopik']] = $row;
				}
				else {
					if(empty($a_data[$row['parenttopik']]))
						$row['parenttopik'] = '';
					
					$a_data[$row['parenttopik']]['child'][] = $row;
				}
			}
			
			foreach($a_data as $t_id => $t_data) {
				if(empty($t_data['child'])) {
					array_unshift($a_data['']['child'],$t_data);
					unset($a_data[$t_id]);
				}
			}
			
			return $a_data;
		}
		
		// mendapatkan daftar diskusi
		function getListForumKelas($conn,$kelas,$topik='') {
			$sql = "select f.idforum, f.judulforum, f.creator, count(d.idforumdetail) as jumlah,
					max(d.waktuposting::text||'|'||d.idforumdetail||'|'||d.t_updateuser) as label
					from ".static::table()." f
					left join ".static::table('el_forumdetail')." d on d.idforum = f.idforum
					where ".mKelas::getCondition($kelas);
			if(!empty($topik))
				$sql .= " and f.idtopik = '$topik'";
			$sql .= " group by f.idforum, f.judulforum, f.creator, f.t_updatetime order by f.t_updatetime desc";
			
			return $conn->GetArray($sql);
		}
		
		// mendapatkan daftar detail diskusi
		function getListDetail($conn,$key,&$page,&$lastpage) {
			global $conf;
			
			$row = $conf['row_diskusi'];
			
			$sql = "select f.*, u.userdesc from ".static::table('el_forumdetail')." f
					left join gate.sc_user u on f.t_updateuser = u.username
					where ".self::getCondition($key)." order by f.waktuposting, f.idforumdetail";
			
			// halaman terakhir
			// if($page == -1) {
				$sqlc = "select count(*) from (".$sql.") a";
				$rownum = $conn->GetOne($sqlc);
				
				// $page = ceil($rownum/$row);
				$lastpage = ceil($rownum/$row);
				if(empty($lastpage))
					$lastpage = 1;
			// }
			
			if($page > $lastpage)
				$page = $lastpage;
			
			$offset = $row*($page-1);
			
			// $sql .= " limit ".($row+1)." offset $offset";
			$sql .= " limit $row offset $offset";
			$data = $conn->GetArray($sql);
			
			// if(empty($data[$row]))
			if($page == $lastpage)
				$t_lastpage = true;
			else
				$t_lastpage = false;
			
			Page::setLastPage($t_lastpage);
			
			// return array_slice($data,0,$row);
			return $data;
		}
		
		// mendapatkan nama topik
		function getNamaTopik($conn,$idtopik) {
			$sql = "select topik from ".static::table('lv_topikforum')." where idtopik = '$idtopik'";
			$topik = $conn->GetOne($sql);
			
			return empty($topik) ? '(Semua)' : $topik;
		}
		
		// topik
		function topik($conn,$emparent=false) {
			$a_data = self::getListTopik($conn,false);
			
			// membentuk combo
			$combo = array();
			foreach($a_data as $t_topik => $t_parent) {
				$combo[($emparent ? '|' : '').$t_topik] = $t_parent['topik'];
				foreach($t_parent['child'] as $t_child)
					$combo[$t_child['idtopik']] = '&nbsp; &nbsp; '.$t_child['topik'];
			}
			
			return $combo;
		}
	}
?>