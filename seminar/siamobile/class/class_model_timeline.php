<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	/**
	 * Model untuk interface mobile
	 * @author Sevima
	 * @version 1.0
	 */
	class mMobileTimeline {
		/**
		* Mendapatkan role dari userid
		* @param int $userid
		* @return mixed
		*/
		function getRole($userid){
			global $conn;
			
			$sql = "select * from gate.sc_userrole where userid = ".Query::escape($userid);			
			$row = $conn->GetRow($sql);
			
			if(empty($row))
				return false;
			else
				return $row;
		}
		
		/**
		* Mendapatkan list forum dari userid
		* @param int $userid
		* @param String keyword
		* @return mixed
		*/
		function getListForum($userid, $keyword = '', $lastId = ''){
			global $conn;
			$limit	= 20;
			
			$sql = "select m.group_id as id, m.member_role as role, g.group_name as name, 0 as rsc, g.group_description as desc, g.group_icon as image
					from mobile.ms_group_member m 
					left join mobile.ms_group g on m.group_id = g.group_id
					where m.user_id = '$userid'";
			
			if(!empty($lastId)){
				$sql .= " and g.group_id > '$lastId'";
			}			
			if(strlen($keyword)>0){
				$sql .= " and g.group_name ILIKE '%".$keyword."%'";
			}
					
			$sql .= " order by g.group_id asc LIMIT ".$limit;
			
			$row = $conn->GetArray($sql);
			
			if(empty($row))
				return false;
			else
				return $row;
		}
		/**
		* Mendapatkan list forum dari userid dengan role dosen
		* @param int $userid
		* @param String keyword
		* @return mixed
		*/
		function addForumDosen($userid){
			global $conn;
			
			$sql = "select distinct(a.periode||'-'||a.thnkurikulum||'-'||a.kodeunit||'-'||a.kodemk||'-'||a.kelasmk) as kodemk, 
					mk.namamk, k.nipdosen,a.periode,  akademik.f_namaperiode(a.periode) as periodename, 
					'Pengajar '||akademik.f_namalengkap(p.gelardepan::bpchar, p.namadepan::bpchar, p.namatengah::bpchar, p.namabelakang::bpchar, p.gelarbelakang::bpchar)::text||' . '||'Kelas '||a.kelasmk||' . '||akademik.f_namaperiode(a.periode) as namadosen
					from akademik.ak_mengajar a
					join akademik.ak_mengajar k on a.periode = k.periode and a.thnkurikulum = k.thnkurikulum and a.kodeunit = k.kodeunit and a.kodemk=k.kodemk and a.kelasmk = k.kelasmk and k.ispjmk = 1
					LEFT JOIN sdm.ms_pegawai p ON k.nipdosen::text = p.idpegawai::text
					join akademik.ak_matakuliah mk on mk.kodemk = a.kodemk
					join gate.sc_user u on u.username = a.nipdosen
					where u.userid = ".Query::escape($userid)." order by a.periode desc,mk.namamk";
			
			$row = $conn->GetArray($sql);
			
			foreach($row as $data){
				$dataInsert = array();
				$dataInsert['group_name'] = $data['namamk'] . " (".$data['periodename'].")";
				$dataInsert['group_icon'] = 'ic_class.png';
				$dataInsert['group_description'] = $data['namadosen'];
				$dataInsert['group_map_to_mk'] = $data['kodemk'];
				$sql = "select group_id from mobile.ms_group WHERE group_map_to_mk = ".Query::escape($data['kodemk']) ." LIMIT 1";
			
				$row2 = $conn->GetArray($sql);
				if(count($row2)==0){
					$err = Query::recInsert($conn,$record,'mobile.ms_group');
					$ok = Query::isOK($err);
				}
				if(isset($row2[0])){
					if(isset($row2[0]['group_id'])){
						$sql = "select user_id from mobile.ms_group_member WHERE user_id = ".Query::escape($userid) ." and group_id = ".Query::escape($row2[0]['group_id']);
						$row3 = $conn->GetArray($sql);
						if(count($row3)==0){
							$err = Query::recInsert($conn,array('member_role'=>"A", 'user_id'=>$userid, 'group_id'=>$row2[0]['group_id']),'mobile.ms_group_member');
							$ok = Query::isOK($err);
						}
					}
				}
			
			}
		}
		
		
		/**
		* Mendapatkan list forum dari userid dengan role mahasiswa
		* @param int $userid, 
		* @param String keyword
		* @return mixed
		*/
		function addForumMahasiswa($userid){
			global $conn;
			
			$sql = "select distinct(k.periode||'-'||k.thnkurikulum||'-'||k.kodeunit||'-'||k.kodemk||'-'||k.kelasmk) as kodemk, 
					mk.namamk, a.nipdosen,k.periode,  akademik.f_namaperiode(a.periode) as periodename, 			
					'Pengajar '||akademik.f_namalengkap(p.gelardepan::bpchar, p.namadepan::bpchar, p.namatengah::bpchar, p.namabelakang::bpchar, p.gelarbelakang::bpchar)::text||' . '||'Kelas '||a.kelasmk||' . '||akademik.f_namaperiode(a.periode) as namadosen
					from akademik.ak_krs k
					join akademik.ak_matakuliah mk on mk.kodemk = k.kodemk
					join gate.sc_user u on u.username = k.nim
					JOIN akademik.ak_mengajar a on a.periode = k.periode and a.thnkurikulum = k.thnkurikulum and a.kodeunit = k.kodeunit and a.kodemk=k.kodemk and a.kelasmk = k.kelasmk and a.ispjmk = 1
					LEFT JOIN sdm.ms_pegawai p ON a.nipdosen::text = p.idpegawai::text
					where u.userid = ".Query::escape($userid)." order by k.periode desc, mk.namamk";
			
			$row = $conn->GetArray($sql);
			foreach($row as $data){
				$record = array();
				$record['group_name'] = $data['namamk'] . " (".$data['periodename'].")";
				$record['group_icon'] = 'ic_class.png';
				$record['group_description'] = $data['namadosen'];
				$record['group_map_to_mk'] = $data['kodemk'];
				$sql = "select group_id from mobile.ms_group WHERE group_map_to_mk = ".Query::escape($data['kodemk']) ." LIMIT 1";
			
				$row2 = $conn->GetArray($sql);
				if(count($row2)==0){
					$err = Query::recInsert($conn,$record,'mobile.ms_group');
					$ok = Query::isOK($err);
				}
				if(isset($row2[0])){
					if(isset($row2[0]['group_id'])){
						$sql = "select user_id from mobile.ms_group_member WHERE user_id = ".Query::escape($userid) ." and group_id = ".Query::escape($row2[0]['group_id']);
						$row3 = $conn->GetArray($sql);
						if(count($row3)==0){
							$sql = "select user_id from mobile.ms_group_member WHERE member_role = 'A' and group_id = ".Query::escape($row2[0]['group_id']);
							$row4 = $conn->GetArray($sql);
							if(count($row4)>0){
								$err = Query::recInsert($conn,array('member_role'=>"M", 'user_id'=>$userid, 'group_id'=>$row2[0]['group_id']),'mobile.ms_group_member');
								$ok = Query::isOK($err);
							}
						}
					}
				}
			}
		}
		
		/**
		* Mendapatkan unit dari userid
		* @param int $userid
		* @return mixed
		*/
		function addForumUniversitas($userid){
			global $conn;
			
			$sql = "select namaunit as group_name, 'ic_universitas.png' as group_icon, 
			'Forum untuk civitas akademika ' || namaunit as group_description, kodeunit as group_map_to_unit
			from gate.ms_unit where level = 0";
			
			$row = $conn->GetArray($sql);
			if(count($row)>0){
				$sql = "select group_id from mobile.ms_group WHERE group_map_to_unit = ".Query::escape($row[0]['group_map_to_unit']) ." LIMIT 1";
				$row2 = $conn->GetArray($sql);
				
				if(count($row2)==0){
					$err = Query::recInsert($conn,$row[0],'mobile.ms_group');
					$ok = Query::isOK($err);
				}
				if(isset($row2[0])){
					if(isset($row2[0]['group_id'])){
						$sql = "select user_id from mobile.ms_group_member WHERE user_id = ".Query::escape($userid) ." and group_id = ".Query::escape($row2[0]['group_id']);
						$row3 = $conn->GetArray($sql);
						if(count($row3)==0){
							$err = Query::recInsert($conn,array('member_role'=>"M", 'user_id'=>$userid, 'group_id'=>$row2[0]['group_id']),'mobile.ms_group_member');
							$ok = Query::isOK($err);
						}
					}
				}
			}
		}
		
		/**
		* Mendapatkan parent unit dari kodeunit
		* @param int $kdoeunit
		* @return mixed
		*/
		function getUnit($kodeunit, $keyword = ''){
			global $conn;
			
			$sql = "select kodeunit, namaunit, kodeunitparent
					from gate.ms_unit 
					where kodeunit = ".Query::escape($kodeunit)."
					and lower(namaunit) like '%".$keyword."%'";
			$row = $conn->GetRow($sql);
			
			if(empty($row))
				return false;
			else
				return $row;
		}
		
		
		/**
		* Mendapatkan member dari groupmember
		* @param int $group_id
		* @return mixed
		*/
		function getListMember($group_id, $lastid = '') {
			
			global $conn;
			$limit	= 20;
			
			$sql = "select m.group_member_id as id, m.member_role as role, u.userid, u.userdesc
					from mobile.ms_group_member m 
					left join mobile.ms_group g on m.group_id = g.group_id
					left join gate.sc_user u on u.userid = m.user_id
					where m.group_id = '$group_id'";
			
			if(!empty($lastId)){
				$sql .= " and m.group_member_id > '$lastId'";
			}
					
			$sql .= " order by m.group_member_id asc LIMIT ".$limit;
			
			$row = $conn->GetArray($sql);
			
			if(empty($row))
				return false;
			else
				return $row;
		}
		
		/**
		* Mendapatkan member dari unit
		* @param int $kodeunit
		* @return mixed
		*/
		function getListMemberUnit($kodeunit, $lastid = '') {
			global $conn;
			
			$sql = "select * from gate.ms_unit where kodeunit = ".Query::escape($kodeunit);
			$row = $conn->GetRow($sql);
			
			$sql = "select ur.userid as id, u.userdesc as name, 
						case
							when ur.koderole = 'D' then 'A'
							else ur.koderole
						end as role,
						case
							when ur.koderole = 'D' then 'Admin'
							when ur.koderole = 'M' then 'Mahasiswa'
							else ur.koderole
						end as namarole,
						ur.kodeunit,
						case when n.level = 2 then n.namaunit else null end as department ,
						case 	when n.level = 1 then n.namaunit 
								when n.level = 2 then (select namaunit from gate.ms_unit where kodeunit = n.kodeunitparent)
								else null end as faculty,
						case 	when n.level = 0 then n.namaunit
								when n.level = 1 then (select namaunit from gate.ms_unit where kodeunit = n.kodeunitparent)
								when n.level = 2 then (select namaunit from gate.ms_unit where kodeunit = (select kodeunitparent from gate.ms_unit where kodeunit = n.kodeunitparent))
								else null end as university
					from gate.sc_userrole ur
					join gate.sc_user u on u.userid = ur.userid
					join gate.ms_unit n on n.kodeunit = ur.kodeunit
					where n.infoleft >= ".Query::escape($row['infoleft'])." and n.inforight <= ".Query::escape($row['inforight'])."
					and u.userdesc > '".$lastid."'
					order by ur.koderole, u.userdesc
					LIMIT 10";
			$row = $conn->GetArray($sql);
			
			if(empty($row))
				return false;
			else
				return $row;
		}
		
		/**
		* Mendapatkan member dari kelas
		* @param int $kodeunit
		* @return mixed
		*/
		function getListMemberForum($kodekelas, $lastid) {
			global $conn;
			
			$kodekelas 	= explode('-',$kodekelas);
			$periode	= $kodekelas[0];
			$tahun		= $kodekelas[1];
			$unit		= $kodekelas[2];
			$mk			= $kodekelas[3];
			$kelas		= $kodekelas[4];
			
			$sql = "SELECT * 
					FROM	(
							select k.nim as id, u.userdesc as name,'M' as role, 'Mahasiswa' as namarole,
								ur.kodeunit,
								case when n.level = 2 then n.namaunit else '' end as department ,
								case 	when n.level = 1 then n.namaunit 
									when n.level = 2 then (select namaunit from gate.ms_unit where kodeunit = n.kodeunitparent)
									else '' end as faculty,
								case 	when n.level = 0 then n.namaunit
									when n.level = 1 then (select namaunit from gate.ms_unit where kodeunit = n.kodeunitparent)
									when n.level = 2 then (select namaunit from gate.ms_unit where kodeunit = (select kodeunitparent from gate.ms_unit where kodeunit = n.kodeunitparent))
									else '' end as university
							from akademik.ak_krs k
							join gate.sc_user u on u.username = k.nim 
							join gate.sc_userrole ur on u.userid = ur.userid
							join gate.ms_unit n on n.kodeunit = ur.kodeunit
							where 	k.periode = ".Query::escape($periode)." 
							and k.thnkurikulum = ".Query::escape($tahun)." 
							and k.kodeunit = ".Query::escape($unit)."
							and k.kodemk = ".Query::escape($mk)." 
							and k.kelasmk = ".Query::escape($kelas)."
							union
							select k.nipdosen as id, u.userdesc as name,'A' as role, 'Admin' as namarole,
								ur.kodeunit,
								case when n.level = 2 then n.namaunit else '' end as department ,
								case 	when n.level = 1 then n.namaunit 
									when n.level = 2 then (select namaunit from gate.ms_unit where kodeunit = n.kodeunitparent)
									else '' end as faculty,
								case 	when n.level = 0 then n.namaunit
									when n.level = 1 then (select namaunit from gate.ms_unit where kodeunit = n.kodeunitparent)
									when n.level = 2 then (select namaunit from gate.ms_unit where kodeunit = (select kodeunitparent from gate.ms_unit where kodeunit = n.kodeunitparent))
									else '' end as university
							from akademik.ak_mengajar k
							join gate.sc_user u on u.username = k.nipdosen 
							join gate.sc_userrole ur on u.userid = ur.userid
							join gate.ms_unit n on n.kodeunit = ur.kodeunit
							where 	k.periode = ".Query::escape($periode)." 
							and k.thnkurikulum = ".Query::escape($tahun)." 
							and k.kodeunit = ".Query::escape($unit)."
							and k.kodemk = ".Query::escape($mk)." 
							and k.kelasmk = ".Query::escape($kelas)."
							) a
					where a.name > '".$lastid."'
					order by a.role,a.name
					LIMIT 10";
			$row = $conn->GetArray($sql);
			
			if(empty($row))
				return false;
			else
				return $row;
		}
		
		/**
		* Membuat isRead pada notification
		* @param int $userid, $lastid
		* @return mixed
		*/
		function uploadNotification($userid, $notigicationId) {
			global $conn;
			
			if(!empty($userid) && !empty($notigicationId)){
				
				$err = Query::recUpdate($conn,array('is_read'=>"2"),'mobile.ms_notification','id = '. $notigicationId);
				$ok = Query::isOK($err);
			}
		}
		
		/**
		* Mendapatkan list notification
		* @param int $userid, $lastid
		* @return mixed
		*/
		function getListNotification($userid, $lastId = '') {
			global $conn;
			$limit	= 20;
			
			if(!empty($userid)){
			
				$sql = "select n.id, u.userid, u.userdesc, n.description, n.tipe_launch as state, n.date_create as timestamp, n.is_read as isRead,
				n.launch_id as launchid
						from mobile.ms_notification n
						inner join gate.sc_user u on u.userid = n.user_notif_id
						where n.user_id = '$userid'";
			
				if(!empty($lastId)){
					$sql .= " and n.id < '$lastId'";
				}
				$sql .= " order by n.id desc LIMIT ".$limit;
				$row = $conn->GetArray($sql);
				
				if(empty($row))
					return false;
				else
					return $row;
			}else
				return false;
		}
		
		/**
		* set read notification
		* @param int $userid
		* @return mixed
		*/
		function setReadNotification($userid) {
			global $conn;
			
			if(!empty($userid)){
				$err = Query::recUpdate($conn,array('is_read'=>'1'),'mobile.ms_notification',"user_id = $userid and (is_read is null or is_read='0')");
				$ok = Query::isOK($err);
			}
		}
		
		
		/**
		* Mendapatkan jumlah notif yang sudah dibaca dan belum dibaca
		* @param int $userid
		* @return mixed
		*/
		function getCountNotif($userid){
			global $conn;
			
			if(!empty($userid)){
				$sql = "select count(id) from mobile.ms_notification where (is_read = '0' or is_read is null) and user_id = $userid";
				$row = $conn->GetRow($sql);
				$sql = "select count(id) from mobile.ms_notification where (is_read = '2' and is_read is not null) and user_id = $userid";
				$row2 = $conn->GetRow($sql);
				
				return array("read"=>$row2["count"], "notRead"=>$row["count"]);
			}else
				return array("read"=>0, "notRead"=>0);
		}
		
		/**
		* Mendapatkan list timeline dari (userid, state, groupId, lastId)
		* @param int $userid
		* @return mixed
		*/
		function getListTimeline($userid, $lastId = '',$groupId=null) {
			global $conn;
			$limit	= 20;
			
			if(!empty($userid)){
			$sql = "select *
					from mobile.ms_timeline t 
					left join gate.sc_user u on u.userid = t.user_id
					left join mobile.ms_group_member gm on gm.group_id::text = t.group_id
					left join mobile.ms_group g on g.group_id = gm.group_id
					where (t.group_id is null or gm.user_id = '$userid')";
			}else{
				$sql = 'select * from mobile.ms_timeline t 
						left join gate.sc_user u on u.userid = t.user_id
						left join mobile.ms_group_member gm on gm.group_id::text = t.group_id
						where t.group_id is null';
			}

			if(!empty($lastId)){
				//$sql .= " and t_updatetime > (select t_updatetime from mobile.ms_timeline where timeline_id = '".$lastId."')";
				$sql .= " and t.timeline_id < '$lastId'";
			}
			$sql .= " order by t.timeline_id desc LIMIT ".$limit;
			$row = $conn->GetArray($sql);

			if(!empty($userid)){
				foreach($row as $key=>$timelines){
					$sql = "select user_id as userid
					from mobile.ms_like WHERE is_like = 1 and timeline_id = ".$timelines['timeline_id'];
					$row2 = $conn->GetArray($sql);
					if(count($row2)>0){
						$row[$key]["isLike"] = "0";
						foreach($row2 as $key2=>$row3){
							$row[$key]["likes"][] = array("user"=>array("id"=>$row3["userid"]));
							if($row3["userid"]==$userid){
								$row[$key]["isLike"] = "1";
							}
						}
					}
					
					
					$sql = "select user_id as userid
						from mobile.ms_comment c
						where c.timeline_id = ".$timelines['timeline_id'];
					$row2 = $conn->GetArray($sql);
					if(count($row2)>0){
						$row[$key]["comments"] = $row2;
					}
					if($timelines['userid']==$userid){
						$row[$key]["isDelete"] = "1";
					}
				}
			}
			
			if(empty($row))
				return false;
			else
				return $row;
		}

		/**
		* Mendapatkan list timeline dari (userid, state, groupId, lastId)
		* @param int $userid
		* @return mixed
		*/
		function getListTimelineGroup($userid, $lastId = '',$groupId=null) {
			global $conn;
			$limit	= 20;
			
			if(!empty($userid) and !empty($groupId)){
			$sql = "select *
					from mobile.ms_timeline t 
					join gate.sc_user u on u.userid = t.user_id
					join mobile.ms_group_member gm on gm.group_id::text = t.group_id
					where (t.group_id = '$groupId' and gm.user_id = '$userid')";
			}

			if(!empty($lastId)){
				//$sql .= " and t_updatetime > (select t_updatetime from mobile.ms_timeline where timeline_id = '".$lastId."')";
				$sql .= " and t.timeline_id < '$lastId'";
			}
			$sql .= " order by t.timeline_id desc LIMIT ".$limit;
			$row = $conn->GetArray($sql);
			
			if(!empty($userid)){
				foreach($row as $key=>$timelines){
					$sql = "select user_id as userid
					from mobile.ms_like WHERE is_like = 1 and timeline_id = ".$timelines['timeline_id'];
					$row2 = $conn->GetArray($sql);
					if(count($row2)>0){
						$row[$key]["isLike"] = "0";
						foreach($row2 as $key2=>$row3){
							$row[$key]["likes"][] = array("user"=>array("id"=>$row3["userid"]));
							if($row3["userid"]==$userid){
								$row[$key]["isLike"] = "1";
							}
						}
					}
					
					$sql = "select user_id as userid
						from mobile.ms_comment c
						where c.timeline_id = ".$timelines['timeline_id'];
					$row2 = $conn->GetArray($sql);
					if(count($row2)>0){
						$row[$key]["comments"] = $row2;
					}
					if($timelines['userid']==$userid){
						$row[$key]["isDelete"] = "1";
					}
				}
			}
			
			if(empty($row))
				return false;
			else
				return $row;
		}

		/**
		* Update like timeline
		* @param int $userid, int timelineId
		* @return mixed
		*/
		function uploadLikeTimeline($userid, $timelineId = '', $isLike = 0) {
			global $conn;
			if(!empty($userid) and !empty($timelineId)){
				$sql = "select *
						from mobile.ms_like l
						where (l.timeline_id = $timelineId and l.user_id = $userid)";
				$row = $conn->GetArray($sql);
				$timestamp = date('Y-m-d G:i:s');
				
				if(count($row)==0){
					$conn->StartTrans();
					$err = Query::recInsert($conn,array('timeline_id'=>$timelineId, 'user_id'=>$userid, 'is_like'=>$isLike, 'like_date'=>$timestamp),'mobile.ms_like');
					$ok = Query::isOK($err);

					$group_id = $conn->GetOne('select group_id from mobile.ms_timeline where timeline_id ='.$timelineId);

					if($ok && !empty($group_id)){
						self::generateNotificationGroup($timelineId, $userid, $group_id, 'FORUM_TIMELINE_COMMENT', 'menyukai kiriman di');
					}elseif($ok && empty($group_id)){
						self::generateNotificationPublic($timelineId, $userid, 'HOME_TIMELINE_COMMENT', 'menyukai kiriman di timeline public yang anda ikuti');
					}
					$conn->CompleteTrans();

				}else{
					$err = Query::recUpdate($conn,array('timeline_id'=>$timelineId, 'user_id'=>$userid, 'is_like'=>$isLike, 'like_date'=>$timestamp),'mobile.ms_like','timeline_id = '. $timelineId .' and user_id = '. $userid);
					$ok = Query::isOK($err);
				}
			}
			
		}

		/**
		* Update Comment timeline
		* @param int $userid, int timelineId
		* @return mixed
		*/
		function uploadCommentTimeline($userid, $timelineId = '', $comment) {
			global $conn;
			$data = null;
			
			if(!empty($userid) and !empty($timelineId)){
				if(!empty($comment)){
					$timestamp = date('Y-m-d G:i:s');
				
					$err = Query::recInsert($conn,array('timeline_id'=>$timelineId, 'user_id'=>$userid, 'comment_text'=>$comment, 'comment_date'=>$timestamp),'mobile.ms_comment');
					$ok = Query::isOK($err);

				}
				
					
					$sql = "select *
					from mobile.ms_timeline t 
					join gate.sc_user u on u.userid = t.user_id
					where t.timeline_id = '$timelineId'";
					$row = $conn->GetRow($sql);
					if(count($row)>0){
						$data = $row;
					}

					if($ok && !empty($data['group_id'])){
						self::generateNotificationGroup($timelineId, $userid, $data['group_id'], 'FORUM_TIMELINE_COMMENT', 'mengomentari "'.$comment.'" di');
					}elseif($ok && empty($data['group_id'])){
						self::generateNotificationPublic($timelineId, $userid, 'HOME_TIMELINE_COMMENT', 'mengomentari "'.$comment.'" di timeline public yang anda ikuti');
					}

					$sql = "select c.comment_id, c.comment_date, c.comment_text, u.userid, u.userdesc
							from mobile.ms_comment c
							left join gate.sc_user u on c.user_id = u.userid
							where c.timeline_id = $timelineId";
					$row = $conn->GetArray($sql);
					$data["comments"] = $row;
					$sql = "select user_id as userid
					from mobile.ms_like WHERE is_like = 1 and timeline_id = ".$timelineId;
					$row2 = $conn->GetArray($sql);
					if(count($row2)>0){
						$data["isLike"] = "0";
						foreach($row2 as $key2=>$row3){
							$data["likes"][] = array("user"=>array("id"=>$row3["userid"]));
							if($row3["userid"]==$userid){
								$data["isLike"] = "1";
							}
						}
					}
					
					if($data['userid']==$userid){
						$data["isDelete"] = "1";
					}
			}
			
			
			if(empty($data))
				return false;
			else
				return $data;
			
		}
		
		
		function uploadTimeline($data){
			global $conn;

			$err = Query::recInsert($conn, $data, 'mobile.ms_timeline');
			$ok = Query::isOK($err);

			if($ok && !empty($data['group_id'])){
				$timelineId =self::getLastId('mobile.ms_timeline_timeline_id_seq');
				self::generateNotificationGroup($timelineId, $data['user_id'], $data['group_id'], 'FORUM_TIMELINE_VIEW', 'menambahkan kiriman pada group');
			}

			return  $ok;
		}


		function updateTimeline($data, $id){
			global $conn;

			$err = Query::recUpdate($conn, $data, 'mobile.ms_timeline', 'timeline_id = '.$id);
			return  Query::isOK($err);
		}

		function getLastId($sequence){
			global $conn;

			$sql = 'select last_value from '.$sequence;
			return $conn->GetOne($sql);
		}


		function getGroupById($id){
			global $conn;

			return $conn->GetRow('select * from mobile.ms_group where group_id = '.$id);
		}

		function getGroupMemberByGroupId($id){
			global $conn;

			return $conn->GetAll('select * from mobile.ms_group_member where group_id = '.$id);
		}

		function generateNotificationGroup($timeline_id, $user_id, $group_id, $type, $message){
			$user = mMobile::getDataUser($user_id);
			$group = self::getGroupById($group_id);
			$members = self::getGroupMemberByGroupId($group_id);
			$ids = array();
			foreach ($members as $key => $value) {
				if($value['user_id'] != $user_id){
					$ids[] = $value['user_id'];
				}
			}

			self::generateNotification($type, $message.' '. $group['group_name'], $ids, $timeline_id, $user_id);
		}

		function generateNotificationPublic($timeline_id, $user_id, $type, $message){
			global $conn;

			$user = $conn->GetAll('select user_id from mobile.ms_timeline where timeline_id = '.$timeline_id.' and user_id != '.$user_id.'
									union
									select user_id from mobile.ms_comment where timeline_id = '.$timeline_id.' and user_id != '.$user_id.'
									union 
									select user_id from mobile.ms_like where timeline_id = '.$timeline_id.'  and user_id != '.$user_id);
			
			$ids = array();
			foreach ($user as $value) {
				$ids[] = $value['user_id'];
			}

			self::generateNotification($type, $message, $ids, $timeline_id, $user_id);
		}
		
		function generateNotification($type, $message, $user, $timeline_id = NULL, $user_notif_id = NULL){
			if(empty($user)){
				return false;
			}

			require_once("GCMPushMessage.php");
			global $conn;
			$regId = $conn->GetAll('select regid from gate.sc_loginmobile where userid IN ('.implode(',',$user).')');
			foreach ($user as $user_id) {
				$data = array(
					'launch_id' => $timeline_id,
					'tipe_launch' => $type,
  					'date_create' => date('Y-m-d H:i:s'),
  					'user_id' => $user_id,
  					'user_notif_id' => $user_notif_id,
					'description' => $message,
					'is_read' => 0
				);
				
				Query::recInsert($conn, $data, 'mobile.ms_notification');
			}
			
			/*$gcm = new GCMPushMessage("AIzaSyDPCDd4mW2wPRQKKmOTt_MughtKl9vlfdM");
			$divice = array();
			foreach($regId as $row){
				$divice[] = $row['regid'];
			}
			$gcm->setDevices($divice);
			$gcm->send($message, array("state"=>$type));
			*/
			
			return true;
		}
		
	}

?>